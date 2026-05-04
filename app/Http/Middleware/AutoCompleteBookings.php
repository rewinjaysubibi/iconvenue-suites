<?php

namespace App\Http\Middleware;

use App\Models\Booking;
use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class AutoCompleteBookings
{
    public function handle(Request $request, Closure $next)
    {
        // Only run once per minute max using cache lock to avoid overhead on every request
        if (!Cache::has('bookings_auto_complete_last_run')) {
            Cache::put('bookings_auto_complete_last_run', true, now()->addMinute());
            $this->completeExpiredBookings();
        }

        return $next($request);
    }

    private function completeExpiredBookings(): void
    {
        $now = Carbon::now();

        $bookings = Booking::with('venue')
            ->where('status', 'confirmed')
            ->get();

        foreach ($bookings as $booking) {
            if ($this->shouldComplete($booking, $now)) {
                $booking->update(['status' => 'completed']);
            }
        }
    }

    private function shouldComplete(Booking $booking, Carbon $now): bool
    {
        $venue = $booking->venue;
        $endDate = $booking->end_date
            ? Carbon::parse($booking->end_date)
            : Carbon::parse($booking->booking_date);

        if ($venue && $venue->type === 'suite') {
            // Suites check out at 12:00 PM on end_date
            return $now->greaterThan($endDate->copy()->setTime(12, 0, 0));
        }

        if ($booking->time_slots && !empty($booking->time_slots)) {
            // Use the latest time slot end time
            $ends = ['morning' => '12:00', 'afternoon' => '17:00', 'evening' => '22:00'];
            $latest = '12:00';
            foreach ($booking->time_slots as $slot) {
                if (isset($ends[$slot]) && $ends[$slot] > $latest) {
                    $latest = $ends[$slot];
                }
            }
            [$h, $m] = explode(':', $latest);
            return $now->greaterThan($endDate->copy()->setTime((int)$h, (int)$m, 0));
        }

        // Full day venue — complete after 10:00 PM
        return $now->greaterThan($endDate->copy()->setTime(22, 0, 0));
    }
}
