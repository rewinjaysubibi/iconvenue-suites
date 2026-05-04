<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Venue;
use App\Mail\BookingStatusNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class BookingController extends Controller
{
    public function index(Request $request)
    {
        $query = Booking::with(['venue', 'package', 'staff']);

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('booking_reference', 'like', "%{$search}%")
                  ->orWhere('client_name', 'like', "%{$search}%")
                  ->orWhere('client_email', 'like', "%{$search}%")
                  ->orWhere('client_phone', 'like', "%{$search}%");
            });
        }

        $bookings = $query->latest()->paginate(15);
        return view('admin.bookings.index', compact('bookings'));
    }

    public function create(Request $request)
    {
        $venues = Venue::where('is_active', true)->with('activePackages')->get();
        $addons = \App\Models\VenueAddon::active()->orderBy('category')->orderBy('sort_order')->get()->groupBy('category');
        $selectedVenueId = $request->get('venue_id');
        
        // Prepare venue packages data for JavaScript
        $venuePackages = $venues->mapWithKeys(function($venue) {
            return [$venue->id => $venue->activePackages->map(function($package) {
                return [
                    'id' => $package->id,
                    'name' => $package->name,
                    'price' => $package->price,
                    'price_morning' => $package->price_morning,
                    'price_afternoon' => $package->price_afternoon,
                    'price_evening' => $package->price_evening,
                    'has_time_based_pricing' => $package->has_time_based_pricing
                ];
            })];
        });
        
        return view('admin.bookings.create', compact('venues', 'addons', 'selectedVenueId', 'venuePackages'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'venue_id' => 'required|exists:venues_and_suites,id',
            'client_name' => 'required|string|max:255',
            'client_email' => 'required|email',
            'client_phone' => 'required|string',
            'booking_date' => 'required|date|after_or_equal:today',
            'number_of_days' => 'nullable|integer|min:1|max:365',
            'time_slot_type' => 'nullable|in:full_day,multiple',
            'time_slots' => 'nullable|array',
            'time_slots.*' => 'in:morning,afternoon,evening',
            'notes' => 'nullable|string',
            'addons' => 'nullable|array',
            'addons.*' => 'exists:venue_addons,id',
            'addon_quantities' => 'nullable|array',
            'addon_quantities.*' => 'integer|min:1|max:99',
            // Discount fields
            'discount_type' => 'nullable|in:amount,percentage',
            'discount_value' => 'nullable|numeric|min:0',
            'discount_reason' => 'nullable|string|max:255'
        ], [
            'booking_date.after_or_equal' => 'Cannot book past dates. Please select today or a future date.'
        ]);

        // Additional check for past dates
        $bookingDate = \Carbon\Carbon::parse($validated['booking_date']);
        if ($bookingDate->isPast() && !$bookingDate->isToday()) {
            return back()->withErrors([
                'booking_date' => 'Cannot book past dates. The selected date has already passed. Please choose today or a future date.'
            ])->withInput();
        }

        // If booking is for TODAY, check that selected time slots haven't already passed
        if ($bookingDate->isToday()) {
            $now = \Carbon\Carbon::now();
            $currentHour = $now->hour;

            // Time slot end hours
            $slotEndHours = [
                'morning'   => 12, // ends 12:00 PM
                'afternoon' => 17, // ends  5:00 PM
                'evening'   => 22, // ends 10:00 PM
            ];

            $isFullDay = empty($validated['time_slots']) || ($request->time_slot_type === 'full_day');

            if ($isFullDay) {
                // Full day: all slots must not have passed — block if past 10 PM
                if ($currentHour >= 22) {
                    return back()->withErrors([
                        'booking_date' => 'Cannot create a full-day booking for today — all time slots have already passed.'
                    ])->withInput();
                }
            } else {
                // Check each selected slot
                $expiredSlots = [];
                foreach ($validated['time_slots'] as $slot) {
                    if (isset($slotEndHours[$slot]) && $currentHour >= $slotEndHours[$slot]) {
                        $expiredSlots[] = ucfirst($slot);
                    }
                }

                if (!empty($expiredSlots)) {
                    $slotLabels = [
                        'Morning'   => '8:00 AM – 12:00 PM',
                        'Afternoon' => '1:00 PM – 5:00 PM',
                        'Evening'   => '6:00 PM – 10:00 PM',
                    ];
                    $details = implode(', ', array_map(
                        fn($s) => "{$s} ({$slotLabels[$s]})",
                        $expiredSlots
                    ));
                    return back()->withErrors([
                        'time_slots' => "The following time slot(s) have already passed for today: {$details}. Please select a future time slot or choose a different date."
                    ])->withInput();
                }
            }
        }

        // Validate time slot combination — morning+evening without afternoon is not allowed
        if (!empty($validated['time_slots']) && count($validated['time_slots']) === 2) {
            $slots = $validated['time_slots'];
            if (in_array('morning', $slots) && in_array('evening', $slots)) {
                return back()->withErrors([
                    'time_slots' => 'Invalid time slot combination. You can only select Morning + Afternoon or Afternoon + Evening.'
                ])->withInput();
            }
        }

        $venue = Venue::findOrFail($validated['venue_id']);
        
        // Calculate number of days and end_date
        $days = $request->number_of_days ?? 1;
        $validated['number_of_days'] = $days;
        
        // Calculate end_date based on number of days
        $bookingDate = \Carbon\Carbon::parse($validated['booking_date']);
        if ($venue->type === 'suite' && $days > 1) {
            // For multi-day suite bookings, end_date is booking_date + (days - 1)
            $validated['end_date'] = $bookingDate->copy()->addDays($days - 1)->format('Y-m-d');
        } else {
            // For single-day bookings or venues, end_date is same as booking_date
            $validated['end_date'] = $validated['booking_date'];
        }

        if (!$venue->isAvailable($validated['booking_date'], $validated['end_date'])) {
            return back()->withErrors(['booking_date' => 'Venue is not available for selected date range.'])->withInput();
        }
        
        // Calculate venue/package amount
        if ($request->package_id) {
            // Package booking
            $package = \App\Models\VenuePackage::findOrFail($request->package_id);
            
            // Check if multiple time slots are selected
            if ($request->time_slot_type === 'multiple' && $validated['time_slots']) {
                $venueAmount = 0;
                foreach ($validated['time_slots'] as $slot) {
                    if ($package->hasTimeBasedPricing()) {
                        $venueAmount += $package->getPriceForTimeSlot($slot) * $days;
                    } else {
                        $venueAmount += $package->price * $days;
                    }
                }
            } elseif ($package->hasTimeBasedPricing() && $validated['time_slots']) {
                // Single time slot with package time-based pricing
                $venueAmount = 0;
                foreach ($validated['time_slots'] as $slot) {
                    $venueAmount += $package->getPriceForTimeSlot($slot) * $days;
                }
            } else {
                // Full day package booking
                if ($package->hasTimeBasedPricing()) {
                    // Calculate full day as sum of all 3 time slots
                    $venueAmount = ($package->getPriceForTimeSlot('morning') + 
                                   $package->getPriceForTimeSlot('afternoon') + 
                                   $package->getPriceForTimeSlot('evening')) * $days;
                } else {
                    // Package without time-based pricing
                    $venueAmount = $package->price * $days;
                }
            }
            
            $validated['package_id'] = $request->package_id;
        } elseif ($request->time_slot_type === 'multiple' && $validated['time_slots']) {
            // Multiple time slots booking
            $venueAmount = 0;
            foreach ($validated['time_slots'] as $slot) {
                $pricePerSlot = match($slot) {
                    'morning' => $venue->price_morning ?? $venue->price_per_day,
                    'afternoon' => $venue->price_afternoon ?? $venue->price_per_day,
                    'evening' => $venue->price_evening ?? $venue->price_per_day,
                    default => $venue->price_per_day
                };
                $venueAmount += $pricePerSlot * $days;
            }
        } else {
            // Full day booking - add all time slot prices if available
            if ($venue->price_morning && $venue->price_afternoon && $venue->price_evening) {
                // If venue has time-based pricing, full day = sum of all slots
                $fullDayPrice = $venue->price_morning + $venue->price_afternoon + $venue->price_evening;
            } else {
                // Otherwise use standard price per day
                $fullDayPrice = $venue->price_per_day;
            }
            $venueAmount = $fullDayPrice * $days;
        }

        // Calculate add-ons amount
        $addonsAmount = 0;
        $selectedAddons = [];
        if ($request->addons) {
            $addons = \App\Models\VenueAddon::whereIn('id', $request->addons)->get();
            foreach ($addons as $addon) {
                $quantity = $request->addon_quantities[$addon->id] ?? 1;
                
                // Check stock availability
                if ($addon->track_stock && $addon->stock_quantity < $quantity) {
                    return back()->withErrors(['addons' => "Insufficient stock for {$addon->name}. Only {$addon->stock_quantity} available."])->withInput();
                }
                
                $subtotal = $addon->price * $quantity;
                $addonsAmount += $subtotal;
                $selectedAddons[$addon->id] = [
                    'quantity' => $quantity,
                    'price_at_booking' => $addon->price
                ];
            }
        }

        $originalAmount = $venueAmount + $addonsAmount;
        
        // Apply discount if provided
        $discountAmount = 0;
        $discountPercentage = null;
        
        if ($request->discount_type && $request->discount_value > 0) {
            if ($request->discount_type === 'amount') {
                $discountAmount = min($request->discount_value, $originalAmount);
                $discountPercentage = ($discountAmount / $originalAmount) * 100;
            } elseif ($request->discount_type === 'percentage') {
                $discountPercentage = min($request->discount_value, 100);
                $discountAmount = ($originalAmount * $discountPercentage) / 100;
            }
        }
        
        $finalAmount = $originalAmount - $discountAmount;

        $validated['staff_id'] = auth()->id();
        $validated['original_amount'] = $originalAmount;
        $validated['discount_amount'] = $discountAmount;
        $validated['discount_percentage'] = $discountPercentage;
        $validated['total_amount'] = $finalAmount;
        $validated['status'] = 'pending'; // Stays pending until payment is verified

        // Wrap booking creation, addon attachment, and stock decrement in a transaction
        $booking = DB::transaction(function () use ($validated, $selectedAddons) {
            $booking = Booking::create($validated);

            if (!empty($selectedAddons)) {
                $booking->addons()->attach($selectedAddons);

                foreach ($selectedAddons as $addonId => $data) {
                    $addon = \App\Models\VenueAddon::find($addonId);
                    if ($addon && $addon->track_stock) {
                        $addon->decrement('stock_quantity', $data['quantity']);
                    }
                }
            }

            return $booking;
        });

        // Load relationships for email (outside transaction)
        $booking->load(['venue', 'package', 'addons']);

        // Send email notification after successful transaction
        try {
            Mail::to($booking->client_email)->send(new BookingStatusNotification($booking));
        } catch (\Exception $e) {
            \Log::error('Failed to send new booking email: ' . $e->getMessage());
        }

        return redirect()->route('admin.bookings.show', $booking)
            ->with('success', 'Booking created successfully! Status is pending until payment is verified.');
    }

    public function show(Booking $booking)
    {
        $booking->load(['venue', 'package', 'staff', 'payments.verifiedBy']);
        return view('admin.bookings.show', compact('booking'));
    }

    public function edit(Booking $booking)
    {
        $venues = Venue::with('activePackages')->where('is_active', true)->get();

        $venuePackages = $venues->mapWithKeys(function ($venue) {
            return [$venue->id => $venue->activePackages->map(function ($package) {
                return [
                    'id'                   => $package->id,
                    'name'                 => $package->name,
                    'price'                => $package->price,
                    'price_morning'        => $package->price_morning,
                    'price_afternoon'      => $package->price_afternoon,
                    'price_evening'        => $package->price_evening,
                    'has_time_based_pricing' => $package->has_time_based_pricing,
                ];
            })];
        });

        return view('admin.bookings.edit', compact('booking', 'venues', 'venuePackages'));
    }

    public function update(Request $request, Booking $booking)
    {
        $validated = $request->validate([
            'venue_id' => 'required|exists:venues_and_suites,id',
            'package_id' => 'nullable|exists:venue_packages,id',
            'client_name' => 'required|string|max:255',
            'client_email' => 'required|email',
            'client_phone' => 'required|string',
            'booking_date' => 'required|date|after_or_equal:today',
            'number_of_days' => 'nullable|integer|min:1|max:365',
            'time_slot_type' => 'nullable|in:full_day,multiple',
            'time_slots' => 'nullable|array',
            'time_slots.*' => 'in:morning,afternoon,evening',
            'status' => 'required|in:confirmed,cancelled,completed',
            'notes' => 'nullable|string'
        ], [
            'booking_date.after_or_equal' => 'Cannot book past dates. Please select today or a future date.'
        ]);

        // Additional check for past dates
        $bookingDate = \Carbon\Carbon::parse($validated['booking_date']);
        if ($bookingDate->isPast() && !$bookingDate->isToday()) {
            return back()->withErrors([
                'booking_date' => 'Cannot update to a past date. The selected date has already passed. Please choose today or a future date.'
            ])->withInput();
        }

        // Validate time slot combination
        if (!empty($validated['time_slots']) && count($validated['time_slots']) === 2) {
            $slots = $validated['time_slots'];
            if (in_array('morning', $slots) && in_array('evening', $slots)) {
                return back()->withErrors([
                    'time_slots' => 'Invalid time slot combination. You can only select Morning + Afternoon or Afternoon + Evening.'
                ])->withInput();
            }
        }

        $venue = Venue::findOrFail($validated['venue_id']);

        $days = $request->number_of_days ?? 1;
        $validated['number_of_days'] = $days;

        $bookingDate = \Carbon\Carbon::parse($validated['booking_date']);
        if ($venue->type === 'suite' && $days > 1) {
            $validated['end_date'] = $bookingDate->copy()->addDays($days - 1)->format('Y-m-d');
        } else {
            $validated['end_date'] = $validated['booking_date'];
        }

        if (!$venue->isAvailable($validated['booking_date'], $validated['end_date'], $booking->id)) {
            return back()->withErrors(['booking_date' => 'Venue is not available for selected date range.'])->withInput();
        }

        // Recalculate amount — mirrors store() logic
        if ($request->package_id) {
            $package = \App\Models\VenuePackage::findOrFail($request->package_id);

            if ($request->time_slot_type === 'multiple' && !empty($validated['time_slots'])) {
                $venueAmount = 0;
                foreach ($validated['time_slots'] as $slot) {
                    $venueAmount += $package->hasTimeBasedPricing()
                        ? $package->getPriceForTimeSlot($slot) * $days
                        : $package->price * $days;
                }
            } elseif ($package->hasTimeBasedPricing() && !empty($validated['time_slots'])) {
                $venueAmount = 0;
                foreach ($validated['time_slots'] as $slot) {
                    $venueAmount += $package->getPriceForTimeSlot($slot) * $days;
                }
            } else {
                $venueAmount = $package->hasTimeBasedPricing()
                    ? ($package->getPriceForTimeSlot('morning') + $package->getPriceForTimeSlot('afternoon') + $package->getPriceForTimeSlot('evening')) * $days
                    : $package->price * $days;
            }

            $validated['package_id'] = $request->package_id;
        } elseif ($request->time_slot_type === 'multiple' && !empty($validated['time_slots'])) {
            $venueAmount = 0;
            foreach ($validated['time_slots'] as $slot) {
                $pricePerSlot = match($slot) {
                    'morning'   => $venue->price_morning ?? $venue->price_per_day,
                    'afternoon' => $venue->price_afternoon ?? $venue->price_per_day,
                    'evening'   => $venue->price_evening ?? $venue->price_per_day,
                    default     => $venue->price_per_day
                };
                $venueAmount += $pricePerSlot * $days;
            }
            $validated['package_id'] = null;
        } else {
            $validated['package_id'] = null;
            $fullDayPrice = ($venue->price_morning && $venue->price_afternoon && $venue->price_evening)
                ? $venue->price_morning + $venue->price_afternoon + $venue->price_evening
                : $venue->price_per_day;
            $venueAmount = $fullDayPrice * $days;
        }

        $validated['total_amount'] = $venueAmount;

        $booking->update($validated);

        return redirect()->route('admin.bookings.show', $booking)
            ->with('success', 'Booking updated successfully!');
    }

    public function destroy(Booking $booking)
    {
        $booking->delete();

        return redirect()->route('admin.bookings.index')
            ->with('success', 'Booking deleted successfully!');
    }

    public function confirm(Booking $booking)
    {
        $previousStatus = $booking->status;
        $booking->update(['status' => 'confirmed']);

        // Load package relationship for email
        $booking->load(['venue', 'package']);

        // Send email notification
        try {
            Mail::to($booking->client_email)->send(new BookingStatusNotification($booking, $previousStatus));
        } catch (\Exception $e) {
            // Log error but don't fail the status update
            \Log::error('Failed to send booking confirmation email: ' . $e->getMessage());
        }

        return redirect()->route('admin.bookings.index')
            ->with('success', 'Booking confirmed successfully! Email notification sent to client.');
    }

    public function cancel(Booking $booking)
    {
        $previousStatus = $booking->status;

        // Restore addon stock before cancelling
        $booking->load('addons');
        foreach ($booking->addons as $addon) {
            if ($addon->track_stock) {
                $quantity = $addon->pivot->quantity ?? 1;
                $addon->increment('stock_quantity', $quantity);
            }
        }

        $booking->update(['status' => 'cancelled']);

        $booking->load(['venue', 'package']);

        try {
            Mail::to($booking->client_email)->send(new BookingStatusNotification($booking, $previousStatus));
        } catch (\Exception $e) {
            \Log::error('Failed to send booking cancellation email: ' . $e->getMessage());
        }

        return redirect()->route('admin.bookings.index')
            ->with('success', 'Booking cancelled successfully! Email notification sent to client.');
    }

    public function complete(Booking $booking)
    {
        $previousStatus = $booking->status;
        $booking->update(['status' => 'completed']);

        // Load package relationship for email
        $booking->load(['venue', 'package']);

        // Send email notification
        try {
            Mail::to($booking->client_email)->send(new BookingStatusNotification($booking, $previousStatus));
        } catch (\Exception $e) {
            // Log error but don't fail the status update
            \Log::error('Failed to send booking completion email: ' . $e->getMessage());
        }

        return redirect()->route('admin.bookings.index')
            ->with('success', 'Booking marked as completed! Email notification sent to client.');
    }
    public function calendar()
        {
            $venues = Venue::where('is_active', true)->orderBy('type')->orderBy('name')->get();
            return view('admin.bookings.calendar', compact('venues'));
        }

        public function calendarData(Request $request)
        {
            $year  = (int) ($request->year  ?? date('Y'));
            $month = (int) ($request->month ?? date('n'));
            $month = max(1, min(12, $month));
            $year  = max(2020, min(2030, $year));

            $start = \Carbon\Carbon::createFromDate($year, $month, 1)->startOfMonth();
            $end   = $start->copy()->endOfMonth();

            $venues = Venue::where('is_active', true)->orderBy('type')->orderBy('name')->get();

            $bookings = Booking::with(['venue', 'package'])
                ->whereIn('venue_id', $venues->pluck('id'))
                ->where('status', '!=', 'cancelled')
                ->where(function ($q) use ($start, $end) {
                    $q->whereBetween('booking_date', [$start, $end])
                      ->orWhereBetween('end_date', [$start, $end]);
                })
                ->get();

            // Build calendar grid: days × venues
            $days = [];
            $current = $start->copy();
            while ($current <= $end) {
                $dateStr = $current->format('Y-m-d');
                $dayData = [
                    'date'       => $dateStr,
                    'day'        => $current->day,
                    'day_name'   => $current->format('D'),
                    'is_today'   => $current->isToday(),
                    'is_past'    => $current->isPast() && !$current->isToday(),
                    'is_weekend' => $current->isWeekend(),
                    'venues'     => [],
                ];

                foreach ($venues as $venue) {
                    $dayBookings = $bookings->filter(function ($b) use ($venue, $dateStr) {
                        return $b->venue_id === $venue->id
                            && $b->booking_date->format('Y-m-d') <= $dateStr
                            && $b->end_date->format('Y-m-d') >= $dateStr;
                    });

                    $dayData['venues'][$venue->id] = $dayBookings->map(function ($b) {
                        return [
                            'id'              => $b->id,
                            'reference'       => $b->booking_reference,
                            'client_name'     => $b->client_name,
                            'client_email'    => $b->client_email,
                            'client_phone'    => $b->client_phone,
                            'status'          => $b->status,
                            'payment_status'  => $b->payment_status,
                            'total_amount'    => $b->total_amount,
                            'time_slots'      => $b->getTimeSlotsDisplay(),
                            'package'         => $b->package?->name,
                            'booking_date'    => $b->booking_date->format('M d, Y'),
                            'end_date'        => $b->end_date->format('M d, Y'),
                            'number_of_days'  => $b->number_of_days ?? 1,
                        ];
                    })->values();
                }

                $days[] = $dayData;
                $current->addDay();
            }

            // Occupancy stats
            $totalVenues   = $venues->count();
            $occupiedDays  = 0;
            $totalDays     = count($days) * $totalVenues;
            foreach ($days as $day) {
                foreach ($day['venues'] as $vBookings) {
                    if (count($vBookings) > 0) $occupiedDays++;
                }
            }

            return response()->json([
                'year'       => $year,
                'month'      => $month,
                'month_name' => $start->format('F Y'),
                'prev'       => ['year' => $start->copy()->subMonth()->year, 'month' => $start->copy()->subMonth()->month],
                'next'       => ['year' => $start->copy()->addMonth()->year, 'month' => $start->copy()->addMonth()->month],
                'venues'     => $venues->map(fn($v) => ['id' => $v->id, 'name' => $v->name, 'type' => $v->type, 'capacity' => $v->capacity]),
                'days'       => $days,
                'stats'      => [
                    'total_venues'    => $totalVenues,
                    'occupied'        => $occupiedDays,
                    'available'       => $totalDays - $occupiedDays,
                    'occupancy_rate'  => $totalDays > 0 ? round(($occupiedDays / $totalDays) * 100, 1) : 0,
                ],
            ]);
        }
}
