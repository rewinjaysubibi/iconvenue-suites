<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\Venue;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->start_date ? Carbon::parse($request->start_date) : Carbon::now()->startOfMonth();
        $endDate = $request->end_date ? Carbon::parse($request->end_date) : Carbon::now()->endOfMonth();

        $bookingStats = [
            'total' => Booking::whereBetween('created_at', [$startDate, $endDate])->count(),
            'confirmed' => Booking::whereBetween('created_at', [$startDate, $endDate])->where('status', 'confirmed')->count(),
            'pending' => Booking::whereBetween('created_at', [$startDate, $endDate])->where('status', 'pending')->count(),
            'cancelled' => Booking::whereBetween('created_at', [$startDate, $endDate])->where('status', 'cancelled')->count(),
        ];

        $revenueStats = [
            'total' => Payment::whereBetween('created_at', [$startDate, $endDate])->where('status', 'verified')->sum('amount'),
            'pending' => Payment::whereBetween('created_at', [$startDate, $endDate])->where('status', 'pending')->sum('amount'),
            'by_method' => Payment::whereBetween('created_at', [$startDate, $endDate])
                ->where('status', 'verified')
                ->selectRaw('COALESCE(NULLIF(payment_method, ""), "Unspecified") as method, SUM(amount) as total, COUNT(*) as count')
                ->groupBy('method')
                ->orderByDesc('total')
                ->get(),
        ];

        // Separate top venues and suites
        $topVenues = Venue::where('type', 'venue')
            ->withCount(['bookings' => function($q) use ($startDate, $endDate) {
                $q->whereBetween('created_at', [$startDate, $endDate]);
            }])
            ->orderBy('bookings_count', 'desc')
            ->take(5)
            ->get();

        $topSuites = Venue::where('type', 'suite')
            ->withCount(['bookings' => function($q) use ($startDate, $endDate) {
                $q->whereBetween('created_at', [$startDate, $endDate]);
            }])
            ->orderBy('bookings_count', 'desc')
            ->take(5)
            ->get();

        $recentBookings = Booking::with(['venue', 'staff'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->latest()
            ->paginate(20);

        return view('admin.reports.index', compact('bookingStats', 'revenueStats', 'topVenues', 'topSuites', 'recentBookings', 'startDate', 'endDate'));
    }

    public function export(Request $request)
    {
        $startDate = $request->start_date ? Carbon::parse($request->start_date) : Carbon::now()->startOfMonth();
        $endDate = $request->end_date ? Carbon::parse($request->end_date) : Carbon::now()->endOfMonth();

        $bookings = Booking::with(['venue', 'staff', 'payments'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->orderBy('created_at', 'desc')
            ->get();

        $filename = 'bookings_report_' . $startDate->format('Y-m-d') . '_to_' . $endDate->format('Y-m-d') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($bookings) {
            $file = fopen('php://output', 'w');
            
            // Add UTF-8 BOM for Excel compatibility
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Header row
            fputcsv($file, [
                'Booking ID',
                'Booking Date',
                'Client Name',
                'Client Email',
                'Client Phone',
                'Venue/Suite',
                'Type',
                'Check-in Date',
                'Check-out Date',
                'Duration (Days)',
                'Time Slot',
                'Total Amount',
                'Paid Amount',
                'Balance',
                'Payment Status',
                'Booking Status',
                'Handled By',
                'Created At',
                'Notes'
            ]);

            // Data rows
            foreach ($bookings as $booking) {
                $paidAmount = $booking->payments->where('status', 'verified')->sum('amount');
                $balance = $booking->total_amount - $paidAmount;
                $duration = $booking->booking_date->diffInDays($booking->end_date) + 1;
                
                $timeSlot = $booking->getReportTimeSlotDisplay();

                fputcsv($file, [
                    $booking->id,
                    $booking->booking_date->format('Y-m-d'),
                    $booking->client_name,
                    $booking->client_email,
                    $booking->client_phone,
                    $booking->venue->name,
                    ucfirst($booking->venue->type),
                    $booking->booking_date->format('Y-m-d'),
                    $booking->end_date->format('Y-m-d'),
                    $duration,
                    $timeSlot,
                    number_format($booking->total_amount, 2, '.', ''),
                    number_format($paidAmount, 2, '.', ''),
                    number_format($balance, 2, '.', ''),
                    ucfirst($booking->payment_status),
                    ucfirst($booking->status),
                    $booking->staff->name ?? 'N/A',
                    $booking->created_at->format('Y-m-d H:i:s'),
                    $booking->notes ?? ''
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
