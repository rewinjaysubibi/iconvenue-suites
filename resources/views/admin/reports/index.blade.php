@extends('layouts.admin')

@section('page-title', 'Reports & Analytics')

@section('main-content')
<!-- Header -->
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-900">Reports & Analytics</h1>
    <p class="text-gray-500 text-sm mt-1">Overview of bookings and revenue</p>
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
                <span class="text-gray-600 font-medium">Total Verified Revenue</span>
                <span class="text-2xl font-bold text-green-600">₱{{ number_format($revenueStats['total'], 2) }}</span>
            </div>
            <div class="flex justify-between items-center border-b pb-3">
                <span class="text-gray-600 font-medium">Pending Payments</span>
                <span class="text-xl font-bold text-yellow-600">₱{{ number_format($revenueStats['pending'], 2) }}</span>
            </div>

            {{-- Breakdown by payment method --}}
            @if($revenueStats['by_method']->count() > 0)
            <div class="pt-1">
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">Breakdown by Payment Method</p>
                <div class="space-y-2">
                    @foreach($revenueStats['by_method'] as $item)
                    @php
                        $icons = [
                            'gcash'        => ['icon' => 'fas fa-mobile-alt',   'color' => 'text-blue-500',   'bg' => 'bg-blue-50'],
                            'maya'         => ['icon' => 'fas fa-wallet',        'color' => 'text-green-500',  'bg' => 'bg-green-50'],
                            'cash'         => ['icon' => 'fas fa-money-bill',    'color' => 'text-emerald-600','bg' => 'bg-emerald-50'],
                            'bank transfer'=> ['icon' => 'fas fa-university',    'color' => 'text-indigo-500', 'bg' => 'bg-indigo-50'],
                            'credit card'  => ['icon' => 'fas fa-credit-card',   'color' => 'text-purple-500', 'bg' => 'bg-purple-50'],
                            'check'        => ['icon' => 'fas fa-money-check',   'color' => 'text-orange-500', 'bg' => 'bg-orange-50'],
                            'unspecified'  => ['icon' => 'fas fa-question-circle','color'=> 'text-gray-400',   'bg' => 'bg-gray-50'],
                        ];
                        $key = strtolower(trim($item->method));
                        $style = $icons[$key] ?? ['icon' => 'fas fa-receipt', 'color' => 'text-gray-500', 'bg' => 'bg-gray-50'];
                    @endphp
                    <div class="flex items-center justify-between {{ $style['bg'] }} rounded-lg px-3 py-2">
                        <div class="flex items-center gap-2">
                            <i class="{{ $style['icon'] }} {{ $style['color'] }} w-4 text-center"></i>
                            <div>
                                <span class="text-sm font-semibold text-gray-700">{{ ucwords($item->method) }}</span>
                                <span class="text-xs text-gray-400 ml-1">({{ $item->count }} payment{{ $item->count > 1 ? 's' : '' }})</span>
                            </div>
                        </div>
                        <span class="text-sm font-bold text-gray-800">₱{{ number_format($item->total, 2) }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
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
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Time Slot</th>
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
                    <td class="px-6 py-4 text-gray-600">{{ $booking->getReportTimeSlotDisplay() }}</td>
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
                    <td colspan="9" class="px-6 py-4 text-center text-gray-600">No bookings found for this period</td>
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
