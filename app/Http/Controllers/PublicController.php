<?php

namespace App\Http\Controllers;

use App\Models\Venue;
use App\Models\ContactSetting;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Booking;

class PublicController extends Controller
{
    public function index()
    {
        $carouselImages = \App\Models\CarouselImage::where('is_active', true)
            ->orderBy('order')
            ->get();
        $contact = ContactSetting::first();
        return view('public.index', compact('carouselImages', 'contact'));
    }

    public function venues()
    {
        $venues = Venue::where('is_active', true)->where('type', 'venue')->get();
        $contact = ContactSetting::first();
        return view('public.venues', compact('venues', 'contact'));
    }

    public function suites()
    {
        $suites = Venue::where('is_active', true)->where('type', 'suite')->get();
        $contact = ContactSetting::first();
        return view('public.suites', compact('suites', 'contact'));
    }

    public function venueDetails($id)
    {
        $venue = Venue::where('is_active', true)->with('activePackages')->findOrFail($id);
        $contact = ContactSetting::first();
        return view('public.venue-details', compact('venue', 'contact'));
    }

    public function venueAddons($id)
    {
        $venue = Venue::where('is_active', true)->with('activePackages')->findOrFail($id);
        $addons = \App\Models\VenueAddon::active()->orderBy('category')->orderBy('sort_order')->get()->groupBy('category');
        $contact = ContactSetting::first();
        return view('public.venue-addons', compact('venue', 'addons', 'contact'));
    }

    public function venueCalendarData($id, Request $request)
    {
        $venue = Venue::where('is_active', true)->findOrFail($id);
        
        // Get the requested month and year, default to current month
        $year = $request->get('year', date('Y'));
        $month = $request->get('month', date('n'));
        
        // Validate month and year
        $month = max(1, min(12, (int)$month));
        $year = max(2020, min(2030, (int)$year));
        
        // Get the first and last day of the month
        $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();
        
        // Get all bookings for this venue in the requested month
        $bookings = Booking::where('venue_id', $venue->id)
            ->where('status', '!=', 'cancelled')
            ->whereBetween('booking_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->with('package')
            ->get();
        
        // Group bookings by date
        $bookingsByDate = [];
        foreach ($bookings as $booking) {
            $date = $booking->booking_date->format('Y-m-d');
            if (!isset($bookingsByDate[$date])) {
                $bookingsByDate[$date] = [];
            }

            $slotsForCalendar = $this->getBookingSlotsForCalendar($venue, $booking);
            foreach ($slotsForCalendar as $slot) {
                $bookingsByDate[$date][] = [
                    'time_slot' => $slot,
                    'client_name' => $booking->client_name,
                    'booking_reference' => $booking->booking_reference,
                    'status' => $booking->status,
                    'package_name' => $booking->package ? $booking->package->name : null
                ];
            }
        }
        
        // For the public calendar, we do NOT block slots based on time-of-day.
        // The calendar is purely for visualization — clients can see which slots
        // are already booked, but past-time slots are not artificially blocked.
        $todayPassedSlots = []; // intentionally empty for public view

        // Generate calendar data
        $calendarData = [];
        $currentDate = $startDate->copy();
        
        while ($currentDate <= $endDate) {
            $dateStr = $currentDate->format('Y-m-d');
            $dayOfWeek = $currentDate->dayOfWeek; // 0 = Sunday, 6 = Saturday
            $isToday = $currentDate->isToday();
            
            $dayData = [
                'date' => $dateStr,
                'day' => $currentDate->day,
                'day_of_week' => $dayOfWeek,
                'is_past' => $currentDate->isPast(),
                'is_today' => $isToday,
                'past_slots' => $isToday ? $todayPassedSlots : [],
                'bookings' => $bookingsByDate[$dateStr] ?? [],
                'availability' => $this->calculateDayAvailability($venue, $bookingsByDate[$dateStr] ?? [], $isToday ? $todayPassedSlots : [])
            ];
            
            $calendarData[] = $dayData;
            $currentDate->addDay();
        }
        
        return response()->json([
            'venue' => [
                'id' => $venue->id,
                'name' => $venue->name,
                'type' => $venue->type
            ],
            'calendar' => [
                'year' => $year,
                'month' => $month,
                'month_name' => $startDate->format('F'),
                'days' => $calendarData,
                'prev_month' => [
                    'year' => $startDate->copy()->subMonth()->year,
                    'month' => $startDate->copy()->subMonth()->month
                ],
                'next_month' => [
                    'year' => $startDate->copy()->addMonth()->year,
                    'month' => $startDate->copy()->addMonth()->month
                ]
            ]
        ]);
    }
    
    private function calculateDayAvailability($venue, $bookings, array $passedSlots = [])
    {
        if ($venue->type === 'suite') {
            // Suites support same-day walk-in booking when the suite is not already booked.
            if (empty($bookings)) {
                return [
                    'status' => 'available',
                    'available_slots' => ['suite']
                ];
            }

            return [
                'status' => 'fully-booked',
                'available_slots' => []
            ];
        }

        $bookedSlots = array_column($bookings, 'time_slot');

        // Merge booked + time-passed slots — both make a slot unavailable
        $unavailableSlots = array_unique(array_merge($bookedSlots, $passedSlots));

        // Check for full-day/suite bookings (these block everything)
        if (in_array('full-day', $unavailableSlots) || in_array('suite', $unavailableSlots)) {
            return [
                'status' => 'fully-booked',
                'available_slots' => []
            ];
        }
        
        // For venues, check individual time slots.
        $allSlots = ['morning', 'afternoon', 'evening'];
        $availableSlots = array_values(array_diff($allSlots, $unavailableSlots));

        if (empty($availableSlots)) {
            return [
                'status' => 'fully-booked',
                'available_slots' => []
            ];
        }

        // Full-day/package require ALL slots to be free (no bookings AND no passed slots)
        $bookedTimeSlots = array_intersect($unavailableSlots, $allSlots);
        if (empty($bookedTimeSlots)) {
            $availableSlots[] = 'full-day';
            $availableSlots[] = 'package';
            return [
                'status' => 'available',
                'available_slots' => array_values($availableSlots)
            ];
        }

        return [
            'status' => 'partially-booked',
            'available_slots' => array_values($availableSlots)
        ];
    }

    private function getBookingSlotsForCalendar(Venue $venue, Booking $booking): array
    {
        if ($venue->type === 'suite') {
            return ['suite'];
        }

        // Venues: empty time_slots means full-day booking.
        $timeSlots = $booking->getTimeSlots();
        if (empty($timeSlots)) {
            return ['full-day'];
        }

        // Ensure stable order for display.
        $order = ['morning' => 1, 'afternoon' => 2, 'evening' => 3];
        $timeSlots = array_values(array_unique(array_filter($timeSlots, fn ($s) => isset($order[$s]))));
        usort($timeSlots, fn ($a, $b) => $order[$a] <=> $order[$b]);
        return $timeSlots;
    }

    public function venueAddonsData($id)
    {
        $venue = Venue::where('is_active', true)->findOrFail($id);
        $addons = \App\Models\VenueAddon::active()->orderBy('category')->orderBy('sort_order')->get()->groupBy('category');
        
        // Transform addons for JSON response
        $transformedAddons = [];
        foreach ($addons as $category => $categoryAddons) {
            $transformedAddons[$category] = $categoryAddons->map(function ($addon) {
                return [
                    'id' => $addon->id,
                    'name' => $addon->name,
                    'description' => $addon->description,
                    'price' => $addon->price,
                    'track_stock' => $addon->track_stock,
                    'stock_quantity' => $addon->stock_quantity,
                    'isOutOfStock' => $addon->isOutOfStock(),
                    'isLowStock' => $addon->isLowStock(),
                ];
            });
        }
        
        return response()->json([
            'venue' => $venue,
            'addons' => $transformedAddons
        ]);
    }

    public function checkAvailability(Request $request)
    {
        try {
            $request->validate([
                'venue_id'     => 'required|exists:venues_and_suites,id',
                'event_date'   => 'required|date|after_or_equal:today',
                'pricing_type' => 'required|string',
                'pricing_name' => 'required|string',
                'package_id'   => 'nullable|exists:venue_packages,id',
            ]);

            $venue      = Venue::findOrFail($request->venue_id);
            $eventDate  = $request->event_date;
            $pricingType = $request->pricing_type;

            // Label map for display
            $slotLabels = [
                'morning'   => 'Morning (8:00 AM – 12:00 PM)',
                'afternoon' => 'Afternoon (1:00 PM – 5:00 PM)',
                'evening'   => 'Evening (6:00 PM – 10:00 PM)',
                'full-day'  => 'Full Day',
                'suite'     => 'Suite Booking',
                'package'   => 'Event Package',
            ];

            // ── Collect already-booked slots for this date ────────────────────
            $existingBookings = Booking::where('venue_id', $venue->id)
                ->where('status', '!=', 'cancelled')
                ->where('booking_date', $eventDate)
                ->get();

            $bookedSlots   = [];   // specific slots that are taken
            $hasFullDayBlock = false; // true when a full-day / suite booking exists

            foreach ($existingBookings as $booking) {
                if ($venue->type === 'suite') {
                    $hasFullDayBlock = true;
                    continue;
                }

                $slots = $booking->getTimeSlots();
                if (empty($slots)) {
                    // Empty time_slots means full-day — blocks everything
                    $hasFullDayBlock = true;
                } else {
                    foreach ($slots as $slot) {
                        if (in_array($slot, ['morning', 'afternoon', 'evening'], true)) {
                            $bookedSlots[] = $slot;
                        }
                    }
                }
            }
            $bookedSlots = array_values(array_unique($bookedSlots));

            // All three specific slots booked also constitutes a full-day block for venues
            $allTimeSlots = ['morning', 'afternoon', 'evening'];
            if (!$hasFullDayBlock && count(array_intersect($allTimeSlots, $bookedSlots)) === 3) {
                $hasFullDayBlock = true;
            }

            // ── Remaining available specific slots ────────────────────────────
            $remainingSlots = array_values(array_diff($allTimeSlots, $bookedSlots));

            // ── Determine availability for the requested pricing type ─────────
            $isAvailable       = true;
            $conflictingBookings = [];
            $availableSlots    = [];

            if ($pricingType === 'suite') {
                // Suites: any existing booking blocks the whole day
                if ($existingBookings->isNotEmpty()) {
                    $isAvailable = false;
                    foreach ($existingBookings as $b) {
                        $conflictingBookings[] = $b->getTimeSlotsDisplay() . ' – ' . $b->client_name;
                    }
                } else {
                    $availableSlots = ['Suite Available'];
                }

            } elseif (in_array($pricingType, ['full-day', 'package'], true)) {
                // Full-day / package: requires ALL three slots to be free
                if ($hasFullDayBlock || !empty($bookedSlots)) {
                    $isAvailable = false;
                    foreach ($existingBookings as $b) {
                        $conflictingBookings[] = $b->getTimeSlotsDisplay() . ' – ' . $b->client_name;
                    }
                    // Let the user know which slots are still open
                    foreach ($remainingSlots as $slot) {
                        $availableSlots[] = $slotLabels[$slot] ?? ucfirst($slot);
                    }
                } else {
                    $availableSlots = [$pricingType === 'package' ? 'Package Booking Available' : 'Full Day Available'];
                }

            } else {
                // Specific time slot (morning / afternoon / evening)
                if ($hasFullDayBlock) {
                    // A full-day booking blocks every individual slot
                    $isAvailable = false;
                    foreach ($existingBookings as $b) {
                        $conflictingBookings[] = $b->getTimeSlotsDisplay() . ' – ' . $b->client_name;
                    }
                } elseif (in_array($pricingType, $bookedSlots, true)) {
                    // The exact requested slot is already taken
                    $isAvailable = false;
                    $conflictingBookings[] = ($slotLabels[$pricingType] ?? ucfirst($pricingType)) . ' – Already booked';
                }

                // Always show the remaining open slots
                foreach ($remainingSlots as $slot) {
                    if ($slot !== $pricingType) { // don't double-list the requested slot
                        $availableSlots[] = $slotLabels[$slot] ?? ucfirst($slot);
                    }
                }
                if ($isAvailable) {
                    // Confirm the requested slot is free
                    array_unshift($availableSlots, ($slotLabels[$pricingType] ?? ucfirst($pricingType)) . ' – Available');
                }
            }

            $estimatedCost = $this->calculateEstimatedCost($venue, $pricingType, $request->package_id);

            return response()->json([
                'available'           => $isAvailable,
                'event_date'          => $eventDate,
                'pricing_type'        => $pricingType,
                'estimated_cost'      => $estimatedCost,
                'conflicting_bookings' => $conflictingBookings,
                'available_slots'     => $availableSlots,
                'message'             => $isAvailable
                    ? "Great! {$request->pricing_name} is available for your selected date."
                    : "Sorry, {$request->pricing_name} is not available for the selected date.",
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'available' => false,
                'message'   => 'Validation error: ' . implode(' ', array_merge(...array_values($e->errors()))),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'available' => false,
                'message'   => 'Error checking availability: ' . $e->getMessage(),
            ], 500);
        }
    }
    
    private function calculateEstimatedCost($venue, $pricingType, $packageId = null)
    {
        switch ($pricingType) {
            case 'full-day':
            case 'suite':
                return $venue->price_per_day;
            case 'morning':
                return $venue->price_morning ?? $venue->price_per_day;
            case 'afternoon':
                return $venue->price_afternoon ?? $venue->price_per_day;
            case 'evening':
                return $venue->price_evening ?? $venue->price_per_day;
            case 'package':
                if ($packageId) {
                    $package = \App\Models\VenuePackage::find($packageId);
                    return $package ? $package->price : $venue->price_per_day;
                }
                return $venue->price_per_day;
            default:
                return $venue->price_per_day;
        }
    }

    public function contact()
    {
        $contact = ContactSetting::first();
        return view('public.contact', compact('contact'));
    }
}
