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
        
        // Generate calendar data
        $calendarData = [];
        $currentDate = $startDate->copy();
        
        while ($currentDate <= $endDate) {
            $dateStr = $currentDate->format('Y-m-d');
            $dayOfWeek = $currentDate->dayOfWeek; // 0 = Sunday, 6 = Saturday
            
            $dayData = [
                'date' => $dateStr,
                'day' => $currentDate->day,
                'day_of_week' => $dayOfWeek,
                'is_past' => $currentDate->isPast(),
                'is_today' => $currentDate->isToday(),
                'bookings' => $bookingsByDate[$dateStr] ?? [],
                'availability' => $this->calculateDayAvailability($venue, $bookingsByDate[$dateStr] ?? [])
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
    
    private function calculateDayAvailability($venue, $bookings)
    {
        if (empty($bookings)) {
            return [
                'status' => 'available',
                'available_slots' => $venue->type === 'suite' ? ['suite'] : ['morning', 'afternoon', 'evening', 'full-day', 'package']
            ];
        }
        
        $bookedSlots = array_column($bookings, 'time_slot');
        
        // Check for full-day/suite bookings (these block everything)
        if (in_array('full-day', $bookedSlots) || in_array('suite', $bookedSlots)) {
            return [
                'status' => 'fully-booked',
                'available_slots' => []
            ];
        }
        
        if ($venue->type === 'suite') {
            return [
                'status' => 'fully-booked',
                'available_slots' => []
            ];
        }
        
        // For venues, check individual time slots.
        $allSlots = ['morning', 'afternoon', 'evening'];
        $availableSlots = array_diff($allSlots, $bookedSlots);

        if (empty($availableSlots)) {
            return [
                'status' => 'fully-booked',
                'available_slots' => []
            ];
        }

        // Full-day/package require a totally free day (no other bookings).
        $bookedTimeSlots = array_intersect($bookedSlots, $allSlots);
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
                'venue_id' => 'required|exists:venues,id',
                'event_date' => 'required|date|after_or_equal:today',
                'pricing_type' => 'required|string',
                'pricing_name' => 'required|string',
                'package_id' => 'nullable|exists:venue_packages,id'
            ]);

            $venue = Venue::findOrFail($request->venue_id);
            $eventDate = $request->event_date;
            $pricingType = $request->pricing_type;
            
            // Get existing bookings for this venue on the requested date
            $existingBookings = Booking::where('venue_id', $venue->id)
                ->where('status', '!=', 'cancelled')
                ->where('booking_date', $eventDate)
                ->get();
            
            // Define time slots
            $timeSlots = [
                'morning' => 'Morning (8:00 AM - 12:00 PM)',
                'afternoon' => 'Afternoon (1:00 PM - 5:00 PM)', 
                'evening' => 'Evening (6:00 PM - 10:00 PM)',
                'full-day' => 'Full Day',
                'suite' => 'Suite Booking',
                'package' => 'Event Package'
            ];
            
            // Check availability based on booking logic
            $isAvailable = true;
            $conflictingBookings = [];
            $availableSlots = [];
            $existingBookedSlots = [];
            $hasAllDayBlock = false;

            foreach ($existingBookings as $booking) {
                if ($venue->type === 'suite') {
                    $hasAllDayBlock = true;
                    $existingBookedSlots[] = 'suite';
                    continue;
                }

                $slots = $booking->getTimeSlots();
                if (empty($slots)) {
                    $hasAllDayBlock = true;
                    $existingBookedSlots[] = 'full-day';
                    continue;
                }

                foreach ($slots as $slot) {
                    if (in_array($slot, ['morning', 'afternoon', 'evening'], true)) {
                        $existingBookedSlots[] = $slot;
                    }
                }
            }
            $existingBookedSlots = array_values(array_unique($existingBookedSlots));
            
            // If requesting full day, check if ANY booking exists for that date
            if ($pricingType === 'full-day') {
                if ($existingBookings->count() > 0) {
                    $isAvailable = false;
                    foreach ($existingBookings as $booking) {
                        $conflictingBookings[] = $booking->getTimeSlotsDisplay() . ' - ' . $booking->client_name;
                    }
                } else {
                    $availableSlots = ['Full Day Available'];
                }
            }
            // If requesting suite booking, check if ANY booking exists for that date
            elseif ($pricingType === 'suite') {
                if ($existingBookings->count() > 0) {
                    $isAvailable = false;
                    foreach ($existingBookings as $booking) {
                        $conflictingBookings[] = $booking->getTimeSlotsDisplay() . ' - ' . $booking->client_name;
                    }
                } else {
                    $availableSlots = ['Suite Available'];
                }
            }
            // If requesting package, treat like full day
            elseif ($pricingType === 'package') {
                if ($existingBookings->count() > 0) {
                    $isAvailable = false;
                    foreach ($existingBookings as $booking) {
                        $conflictingBookings[] = $booking->getTimeSlotsDisplay() . ' - ' . $booking->client_name;
                    }
                } else {
                    $availableSlots = ['Package Booking Available'];
                }
            }
            // If requesting specific time slot (morning/afternoon/evening)
            else {
                if ($hasAllDayBlock) {
                    $isAvailable = false;
                    foreach ($existingBookings as $booking) {
                        $conflictingBookings[] = $booking->getTimeSlotsDisplay() . ' - ' . $booking->client_name;
                    }
                } else {
                    if (in_array($pricingType, $existingBookedSlots, true)) {
                        $isAvailable = false;
                        $slotName = $timeSlots[$pricingType] ?? $pricingType;
                        $conflictingBookings[] = $slotName . ' - Already booked';
                    }
                    
                    // Show available time slots
                    $allTimeSlots = ['morning', 'afternoon', 'evening'];
                    foreach ($allTimeSlots as $slot) {
                        if (!in_array($slot, $existingBookedSlots, true)) {
                            $availableSlots[] = $timeSlots[$slot];
                        }
                    }
                }
            }
            
            // Calculate estimated cost (this would be more complex in real implementation)
            $estimatedCost = $this->calculateEstimatedCost($venue, $pricingType, $request->package_id);

            return response()->json([
                'available' => $isAvailable,
                'event_date' => $eventDate,
                'pricing_type' => $pricingType,
                'estimated_cost' => $estimatedCost,
                'conflicting_bookings' => $conflictingBookings,
                'available_slots' => $availableSlots,
                'message' => $isAvailable 
                    ? "Great! {$request->pricing_name} is available for your selected date." 
                    : "Sorry, {$request->pricing_name} is not available for the selected date."
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'available' => false,
                'message' => 'Error checking availability: ' . $e->getMessage()
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
