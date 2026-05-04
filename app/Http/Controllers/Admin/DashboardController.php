<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Venue;
use App\Models\Booking;
use App\Models\User;
use App\Models\Payment;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_venues' => Venue::count(),
            'active_venues' => Venue::where('is_active', true)->count(),
            'total_bookings' => Booking::count(),
            'pending_bookings' => Booking::where('status', 'pending')->count(),
            'confirmed_bookings' => Booking::where('status', 'confirmed')->count(),
            'total_staff' => User::whereHas('role', fn($q) => $q->where('name', 'staff'))->count(),
            'pending_payments' => Payment::where('status', 'pending')->count(),
            'total_revenue' => Payment::where('status', 'verified')->sum('amount')
        ];

        $recentBookings = Booking::with(['venue', 'staff'])
            ->latest()
            ->take(10)
            ->get();

        $upcomingBookings = Booking::with(['venue', 'staff'])
            ->whereBetween('booking_date', [Carbon::today(), Carbon::today()->addDays(3)])
            ->whereIn('status', ['confirmed', 'completed'])
            ->orderBy('booking_date')
            ->take(5)
            ->get();

        return view('admin.dashboard', compact('stats', 'recentBookings', 'upcomingBookings'));
    }
}
