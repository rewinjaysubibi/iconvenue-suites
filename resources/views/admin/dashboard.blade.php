@extends('layouts.admin')

@section('page-title', 'Dashboard')

@section('main-content')
<!-- Stats Cards -->
<div class="grid md:grid-cols-4 gap-6 mb-8">
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-600 text-sm">Total Venues</p>
                <p class="text-3xl font-bold text-purple-600">{{ $stats['total_venues'] }}</p>
            </div>
            <i class="fas fa-building text-purple-600 text-4xl"></i>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-600 text-sm">Total Bookings</p>
                <p class="text-3xl font-bold text-blue-600">{{ $stats['total_bookings'] }}</p>
            </div>
            <i class="fas fa-calendar-check text-blue-600 text-4xl"></i>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-600 text-sm">Pending Bookings</p>
                <p class="text-3xl font-bold text-yellow-600">{{ $stats['pending_bookings'] }}</p>
            </div>
            <i class="fas fa-clock text-yellow-600 text-4xl"></i>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-600 text-sm">Total Revenue</p>
                <p class="text-3xl font-bold text-green-600">₱{{ number_format($stats['total_revenue'], 2) }}</p>
            </div>
            <i class="fas fa-money-bill-wave text-green-600 text-4xl"></i>
        </div>
    </div>
</div>

<!-- Recent Bookings & Upcoming -->
<div class="grid md:grid-cols-2 gap-6">
    <!-- Recent Bookings -->
    <div class="bg-white rounded-lg shadow">
        <div class="p-6 border-b">
            <h3 class="text-xl font-bold text-gray-800">Recent Bookings</h3>
        </div>
        <div class="p-6">
            @if($recentBookings->count() > 0)
            <div class="space-y-4">
                @foreach($recentBookings->take(5) as $booking)
                <a href="{{ route('admin.bookings.show', $booking->id) }}" class="flex justify-between items-center border-b pb-3 hover:bg-gray-50 transition-colors duration-150 rounded px-2 py-1 -mx-2 -my-1 cursor-pointer border-l-4
                    @if($booking->status == 'confirmed') border-l-green-500
                    @elseif($booking->status == 'pending') border-l-yellow-500
                    @elseif($booking->status == 'cancelled') border-l-red-500
                    @else border-l-blue-500
                    @endif">
                    <div class="flex-1">
                        <div class="flex items-center gap-2 mb-1">
                            <p class="font-semibold text-gray-800">{{ $booking->client_name }}</p>
                            @if($booking->venue->type === 'suite')
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                    <i class="fas fa-bed mr-1 text-xs"></i>
                                    Suite
                                </span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-purple-100 text-purple-800">
                                    <i class="fas fa-building mr-1 text-xs"></i>
                                    Venue
                                </span>
                            @endif
                        </div>
                        <p class="text-sm text-gray-600">{{ $booking->venue->name }}</p>
                        <p class="text-xs text-gray-500">{{ $booking->booking_date->format('M d, Y') }}</p>
                    </div>
                    <span class="px-3 py-1 rounded-full text-xs font-semibold
                        @if($booking->status == 'confirmed') bg-green-100 text-green-800
                        @elseif($booking->status == 'pending') bg-yellow-100 text-yellow-800
                        @elseif($booking->status == 'cancelled') bg-red-100 text-red-800
                        @else bg-blue-100 text-blue-800
                        @endif">
                        {{ ucfirst($booking->status) }}
                    </span>
                </a>
                @endforeach
            </div>
            @else
            <p class="text-gray-600 text-center">No recent bookings</p>
            @endif
        </div>
    </div>

    <!-- Upcoming Bookings -->
    <div class="bg-white rounded-lg shadow">
        <div class="p-6 border-b">
            <h3 class="text-xl font-bold text-gray-800">Upcoming Bookings</h3>
        </div>
        <div class="p-6">
            @if($upcomingBookings->count() > 0)
            <div class="space-y-4">
                @foreach($upcomingBookings as $booking)
                <a href="{{ route('admin.bookings.show', $booking->id) }}" class="flex justify-between items-center border-b pb-3 hover:bg-gray-50 transition-colors duration-150 rounded px-2 py-1 -mx-2 -my-1 cursor-pointer border-l-4 border-l-purple-500">
                    <div class="flex-1">
                        <div class="flex items-center gap-2 mb-1">
                            <p class="font-semibold text-gray-800">{{ $booking->client_name }}</p>
                            @if($booking->venue->type === 'suite')
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                    <i class="fas fa-bed mr-1 text-xs"></i>
                                    Suite
                                </span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-purple-100 text-purple-800">
                                    <i class="fas fa-building mr-1 text-xs"></i>
                                    Venue
                                </span>
                            @endif
                        </div>
                        <p class="text-sm text-gray-600">{{ $booking->venue->name }}</p>
                        <p class="text-xs text-gray-500">{{ $booking->booking_date->format('M d, Y') }}</p>
                    </div>
                    <span class="px-3 py-1 rounded-full text-xs font-semibold bg-purple-100 text-purple-800">
                        {{ $booking->booking_date->diffForHumans() }}
                    </span>
                </a>
                @endforeach
            </div>
            @else
            <p class="text-gray-600 text-center">No upcoming bookings</p>
            @endif
        </div>
    </div>
</div>
@endsection
