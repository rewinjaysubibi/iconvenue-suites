@extends('layouts.admin')

@section('page-title', 'Reports & Analytics')

@section('main-content')
<!-- Header with Calendar Button -->
<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Reports & Analytics</h1>
        <p class="text-gray-500 text-sm mt-1">Overview of bookings and revenue</p>
    </div>
    <a href="{{ route('admin.bookings.calendar') }}" 
       class="inline-flex items-center px-5 py-2.5 bg-gradient-to-r from-purple-600 to-pink-600 text-white rounded-lg font-semibold shadow hover:shadow-lg transition-all duration-200 hover:scale-105">
        <i class="fas fa-calendar-alt mr-2"></i>
        Booking Calendar
    </a>
</div>

<!-- Date Filter -->
<div class="bg-white rounded-lg shadow p-6 mb-6">
    <form action="{{ route('admin.reports.index') }}" method="GET" class="flex flex-wrap items-end gap-4">
        <div>
            <label class="block text-gray-700 font-semibold mb-2">Start Date</label>
            <input type="date" name="start_date" value="{{ $startDate->format('Y-m-d') }}" 
                class="px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-600">
        </div>
        <div>
            <label class="block text-gray-700 font-semibold mb-2">End Date</label>
            <input type="date" name="end_date" value="{{ $endDate->format('Y-m-d') }}" 
                class="px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-600">
        </div>
        <button type="submit" class="bg-purple-600 text-white px-6 py-2 rounded-lg hover:bg-purple-700 transition">
            <i class="fas fa-filter mr-2"></i>Filter
        </button>
        <a href="{{ route('admin.reports.export', ['start_date' => $startDate->format('Y-m-d'), 'end_date' => $endDate->format('Y-m-d')]) }}" 
           class="bg-gradient-to-r from-green-500 to-green-600 text-white px-6 py-2 rounded-lg hover:shadow-lg transition-all duration-200 font-semibold">
            <i class="fas fa-file-excel mr-2"></i>Export to Excel
        </a>
    </form>
</div>

<!-- Statistics Cards -->
<div class="grid md:grid-cols-4 gap-6 mb-8">
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-600 text-sm">Total Bookings</p>
                <p class="text-3xl font-bold text-blue-600">{{ $bookingStats['total'] }}</p>
            </div>
            <i class="fas fa-calendar-check text-blue-600 text-4xl"></i>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-600 text-sm">Confirmed</p>
                <p class="text-3xl font-bold text-green-600">{{ $bookingStats['confirmed'] }}</p>
            </div>
            <i class="fas fa-check-circle text-green-600 text-4xl"></i>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-600 text-sm">Pending</p>
                <p class="text-3xl font-bold text-yellow-600">{{ $bookingStats['pending'] }}</p>
            </div>
            <i class="fas fa-clock text-yellow-600 text-4xl"></i>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-600 text-sm">Cancelled</p>
                <p class="text-3xl font-bold text-red-600">{{ $bookingStats['cancelled'] }}</p>
            </div>
            <i class="fas fa-times-circle text-red-600 text-4xl"></i>
        </div>
    </div>
</div>

<!-- Revenue Statistics -->
<div class="grid md:grid-cols-2 gap-6 mb-8">
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-xl font-bold mb-4 text-gray-800">Revenue Summary</h3>
        <div class="space-y-4">
            <div class="flex justify-between items-center border-b pb-3">
                <span class="text-gray-600">Total Revenue</span>
                <span class="text-2xl font-bold text-green-600">₱{{ number_format($revenueStats['total'], 2) }}</span>
            </div>
            <div class="flex justify-between items-center">
                <span class="text-gray-600">Pending Payments</span>
                <span class="text-xl font-bold text-yellow-600">₱{{ number_format($revenueStats['pending'], 2) }}</span>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-xl font-bold mb-4 text-gray-800 flex items-center">
            <i class="fas fa-building mr-2 text-purple-600"></i>
            Top Performing Venues
        </h3>
        <div class="space-y-3">
            @forelse($topVenues as $venue)
            <div class="flex justify-between items-center border-b pb-2">
                <div>
                    <p class="font-semibold text-gray-800">{{ $venue->name }}</p>
                    <p class="text-sm text-gray-600">{{ $venue->bookings_count }} booking(s)</p>
                </div>
                <span class="px-3 py-1 bg-purple-100 text-purple-800 rounded-full text-sm font-semibold">
                    #{{ $loop->iteration }}
                </span>
            </div>
            @empty
            <p class="text-gray-600 text-center py-4">No venue bookings in this period</p>
            @endforelse
        </div>
    </div>
</div>

<!-- Top Suites Section -->
<div class="grid md:grid-cols-1 gap-6 mb-8">
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-xl font-bold mb-4 text-gray-800 flex items-center">
            <i class="fas fa-bed mr-2 text-blue-600"></i>
            Top Performing Suites
        </h3>
        <div class="grid md:grid-cols-2 gap-4">
            @forelse($topSuites as $suite)
            <div class="flex justify-between items-center border-b pb-2">
                <div>
                    <p class="font-semibold text-gray-800">{{ $suite->name }}</p>
                    <p class="text-sm text-gray-600">{{ $suite->bookings_count }} booking(s)</p>
                </div>
                <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm font-semibold">
                    #{{ $loop->iteration }}
                </span>
            </div>
            @empty
            <p class="text-gray-600 text-center py-4 col-span-2">No suite bookings in this period</p>
            @endforelse
        </div>
    </div>
</div>

<!-- Recent Bookings Table -->
<div class="bg-white rounded-lg shadow">
    <div class="p-6 border-b">
        <h3 class="text-xl font-bold text-gray-800">Booking Details</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Client</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Venue/Suite</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Amount</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Payment</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($recentBookings as $booking)
                <tr>
                    <td class="px-6 py-4 text-gray-600">#{{ $booking->id }}</td>
                    <td class="px-6 py-4">
                        <div class="font-semibold text-gray-800">{{ $booking->client_name }}</div>
                        <div class="text-sm text-gray-600">{{ $booking->client_phone }}</div>
                    </td>
                    <td class="px-6 py-4 text-gray-600">{{ $booking->venue->name }}</td>
                    <td class="px-6 py-4">
                        <span class="px-3 py-1 rounded-full text-xs font-semibold
                            @if($booking->venue->type == 'venue') bg-purple-100 text-purple-800
                            @else bg-blue-100 text-blue-800
                            @endif">
                            <i class="fas fa-{{ $booking->venue->type == 'venue' ? 'building' : 'bed' }} mr-1"></i>
                            {{ ucfirst($booking->venue->type) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-gray-600">{{ $booking->booking_date->format('M d, Y') }}</td>
                    <td class="px-6 py-4 text-gray-600">₱{{ number_format($booking->total_amount, 2) }}</td>
                    <td class="px-6 py-4">
                        <span class="px-3 py-1 rounded-full text-xs font-semibold
                            @if($booking->status == 'confirmed') bg-green-100 text-green-800
                            @elseif($booking->status == 'pending') bg-yellow-100 text-yellow-800
                            @elseif($booking->status == 'cancelled') bg-red-100 text-red-800
                            @else bg-blue-100 text-blue-800
                            @endif">
                            {{ ucfirst($booking->status) }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <span class="px-3 py-1 rounded-full text-xs font-semibold
                            @if($booking->payment_status == 'paid') bg-green-100 text-green-800
                            @elseif($booking->payment_status == 'partial') bg-yellow-100 text-yellow-800
                            @else bg-red-100 text-red-800
                            @endif">
                            {{ ucfirst($booking->payment_status) }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-6 py-4 text-center text-gray-600">No bookings found for this period</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-6">
    {{ $recentBookings->links() }}
</div>
@endsection
