<?php

namespace App\Console\Commands;

use App\Models\Booking;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CompleteExpiredBookings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bookings:complete-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically complete bookings that have passed their date and time';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking for expired bookings...');

        // Get all confirmed bookings that haven't been completed yet
        $bookings = Booking::with('venue')
            ->where('status', 'confirmed')
            ->get();

        $completedCount = 0;

        foreach ($bookings as $booking) {
            if ($this->shouldCompleteBooking($booking)) {
                $booking->status = 'completed';
                $booking->save();
                
                $completedCount++;
                $venueType = $booking->venue ? $booking->venue->type : 'unknown';
                $this->info("Completed {$venueType} booking #{$booking->id} - {$booking->booking_reference}");
            }
        }

        if ($completedCount > 0) {
            $this->info("Successfully completed {$completedCount} booking(s).");
        } else {
            $this->info('No bookings to complete at this time.');
        }

        return Command::SUCCESS;
    }

    /**
     * Determine if a booking should be completed based on date and time
     */
    private function shouldCompleteBooking(Booking $booking): bool
    {
        $now = Carbon::now();
        $venue = $booking->venue;
        
        // If booking has an end_date, use that
        if ($booking->end_date) {
            $endDate = Carbon::parse($booking->end_date);
            
            // For suites, check-out time is 12:00 PM (noon) on the end date
            if ($venue && $venue->type === 'suite') {
                $endDateTime = $endDate->setTime(12, 0, 0); // 12:00 PM check-out
            }
            // For venues with specific time slots
            elseif ($booking->time_slots && !empty($booking->time_slots)) {
                $latestTimeSlot = $this->getLatestTimeSlotEnd($booking->time_slots);
                $endDateTime = $endDate->setTimeFromTimeString($latestTimeSlot);
            }
            // Full day venue booking - complete at end of day (10 PM)
            else {
                $endDateTime = $endDate->setTime(22, 0, 0);
            }
            
            return $now->greaterThan($endDateTime);
        }
        
        // Fallback to booking_date if no end_date
        $bookingDate = Carbon::parse($booking->booking_date);
        
        // For suites without end_date (shouldn't happen, but handle it)
        if ($venue && $venue->type === 'suite') {
            // Assume 22-hour booking (2 PM check-in, 12 PM next day check-out)
            $endDateTime = $bookingDate->copy()->addDay()->setTime(12, 0, 0);
        }
        // For venues with time slots
        elseif ($booking->time_slots && !empty($booking->time_slots)) {
            $latestTimeSlot = $this->getLatestTimeSlotEnd($booking->time_slots);
            $endDateTime = $bookingDate->setTimeFromTimeString($latestTimeSlot);
        }
        // Full day venue booking
        else {
            $endDateTime = $bookingDate->setTime(22, 0, 0);
        }
        
        return $now->greaterThan($endDateTime);
    }

    /**
     * Get the end time of the latest time slot
     */
    private function getLatestTimeSlotEnd(array $timeSlots): string
    {
        // Time slot end times
        $timeSlotEnds = [
            'morning' => '12:00:00',    // 8AM-12PM
            'afternoon' => '17:00:00',  // 1PM-5PM
            'evening' => '22:00:00',    // 6PM-10PM
        ];

        $latestEnd = '12:00:00';
        
        foreach ($timeSlots as $slot) {
            if (isset($timeSlotEnds[$slot])) {
                $latestEnd = max($latestEnd, $timeSlotEnds[$slot]);
            }
        }

        return $latestEnd;
    }
}
