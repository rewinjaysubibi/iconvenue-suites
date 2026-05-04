<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->search;

        $query = Booking::with(['venue', 'payments'])
            ->select('client_name', 'client_email', 'client_phone')
            ->selectRaw('COUNT(*) as total_bookings')
            ->selectRaw('SUM(total_amount) as total_spent')
            ->selectRaw('MAX(booking_date) as last_booking_date')
            ->groupBy('client_email', 'client_name', 'client_phone');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('client_name', 'like', "%{$search}%")
                  ->orWhere('client_email', 'like', "%{$search}%")
                  ->orWhere('client_phone', 'like', "%{$search}%");
            });
        }

        $clients = $query->orderByDesc('last_booking_date')->paginate(15);

        return view('admin.clients.index', compact('clients', 'search'));
    }

    public function show(string $email)
    {
        $bookings = Booking::with(['venue', 'package', 'payments'])
            ->where('client_email', $email)
            ->latest()
            ->get();

        abort_if($bookings->isEmpty(), 404);

        $client = [
            'name'  => $bookings->first()->client_name,
            'email' => $email,
            'phone' => $bookings->first()->client_phone,
        ];

        return view('admin.clients.show', compact('client', 'bookings'));
    }

    public function update(Request $request, string $email)
    {
        $validated = $request->validate([
            'client_name'  => 'required|string|max:255',
            'client_email' => 'required|email|max:255',
            'client_phone' => 'required|string|max:50',
        ]);

        // Update all bookings that belong to this client (matched by original email)
        Booking::where('client_email', $email)->update([
            'client_name'  => $validated['client_name'],
            'client_email' => $validated['client_email'],
            'client_phone' => $validated['client_phone'],
        ]);

        return redirect()
            ->route('admin.clients.show', urlencode($validated['client_email']))
            ->with('success', 'Client information updated successfully.');
    }
}
