@extends('layouts.admin')

@section('page-title', 'Bookings Management')

@section('main-content')
<div class="mb-6 flex justify-end items-center">
    <div class="flex space-x-4 items-center">
        <!-- Search Box -->
        <form method="GET" class="flex">
            <input type="text" 
                   name="search" 
                   value="{{ request('search') }}" 
                   placeholder="Search by reference, name, email..." 
                   class="px-4 py-2 border border-gray-300 rounded-l-lg focus:outline-none focus:ring-2 focus:ring-purple-600 w-64">
            <button type="submit" class="bg-gray-600 text-white px-4 py-2 rounded-r-lg hover:bg-gray-700 transition">
                <i class="fas fa-search"></i>
            </button>
            @if(request('search'))
            <a href="{{ route('admin.bookings.index') }}" class="ml-2 bg-gray-300 text-gray-700 px-3 py-2 rounded-lg hover:bg-gray-400 transition">
                <i class="fas fa-times"></i>
            </a>
            @endif
        </form>
        
        <!-- Status Filters -->
        <div class="flex space-x-2">
            <a href="{{ route('admin.bookings.index') }}" class="px-4 py-2 rounded-lg {{ !request('status') ? 'bg-purple-600 text-white' : 'bg-gray-200' }}">All</a>
            <a href="{{ route('admin.bookings.index', ['status' => 'confirmed']) }}" class="px-4 py-2 rounded-lg {{ request('status') == 'confirmed' ? 'bg-green-600 text-white' : 'bg-gray-200' }}">Confirmed</a>
            <a href="{{ route('admin.bookings.index', ['status' => 'completed']) }}" class="px-4 py-2 rounded-lg {{ request('status') == 'completed' ? 'bg-purple-600 text-white' : 'bg-gray-200' }}">Done</a>
            <a href="{{ route('admin.bookings.index', ['status' => 'cancelled']) }}" class="px-4 py-2 rounded-lg {{ request('status') == 'cancelled' ? 'bg-red-600 text-white' : 'bg-gray-200' }}">Cancelled</a>
        </div>
    </div>
</div>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="min-w-full">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Reference</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Client</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Venue</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Amount</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Payment</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
            @forelse($bookings as $booking)
            <tr>
                <td class="px-6 py-4">
                    <div class="font-mono text-sm font-semibold text-purple-600 bg-purple-50 px-2 py-1 rounded">
                        {{ $booking->booking_reference ?? 'IVS-' . date('Y') . '-' . str_pad($booking->id, 4, '0', STR_PAD_LEFT) }}
                    </div>
                </td>
                <td class="px-6 py-4">
                    <div class="font-semibold text-gray-800">{{ $booking->client_name }}</div>
                    <div class="text-sm text-gray-600">{{ $booking->client_phone }}</div>
                </td>
                <td class="px-6 py-4 text-gray-600">
                    <div>{{ $booking->venue->name }}</div>
                    @if($booking->package)
                    <div class="text-xs text-purple-600 mt-1">
                        <i class="fas fa-box mr-1"></i>{{ $booking->package->name }}
                    </div>
                    @endif
                </td>
                <td class="px-6 py-4 text-gray-600">{{ $booking->booking_date->format('M d, Y') }}</td>
                <td class="px-6 py-4 text-gray-600">
                    <div>₱{{ number_format($booking->total_amount, 2) }}</div>
                    @if($booking->hasDiscount())
                    <div class="text-xs text-red-600 mt-1">
                        <i class="fas fa-percent mr-1"></i>Discount applied
                    </div>
                    @endif
                </td>
                <td class="px-6 py-4">
                    <span class="px-3 py-1 rounded-full text-xs font-semibold
                        @if($booking->status == 'confirmed') bg-green-100 text-green-800
                        @elseif($booking->status == 'pending') bg-yellow-100 text-yellow-800
                        @elseif($booking->status == 'cancelled') bg-red-100 text-red-800
                        @elseif($booking->status == 'completed') bg-purple-100 text-purple-800
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
                <td class="px-6 py-4">
                    <div class="flex flex-wrap gap-2">
                        <!-- View Button -->
                        <a href="{{ route('admin.bookings.show', $booking) }}" 
                           class="inline-flex items-center px-3 py-2 bg-gradient-to-r from-blue-500 to-blue-600 text-white text-xs font-semibold rounded-lg hover:shadow-lg transition-all duration-200" 
                           title="View Details">
                            <i class="fas fa-eye mr-1"></i>View
                        </a>
                        
                        <!-- Edit Button -->
                        <a href="{{ route('admin.bookings.edit', $booking) }}" 
                           class="inline-flex items-center px-3 py-2 bg-gradient-to-r from-gray-600 to-gray-700 text-white text-xs font-semibold rounded-lg hover:shadow-lg transition-all duration-200" 
                           title="Edit Booking">
                            <i class="fas fa-edit mr-1"></i>Edit
                        </a>
                        
                        <!-- Status Action Buttons -->
                        @if($booking->status == 'confirmed')
                        <form action="{{ route('admin.bookings.complete', $booking) }}" method="POST" class="inline">
                            @csrf
                            @method('PATCH')
                            <button type="submit" 
                                    class="inline-flex items-center px-3 py-2 bg-gradient-to-r from-purple-500 to-purple-600 text-white text-xs font-semibold rounded-lg hover:shadow-lg transition-all duration-200" 
                                    title="Mark as Done" 
                                    onclick="return confirm('Mark this booking as done?')">
                                <i class="fas fa-check-double mr-1"></i>Done
                            </button>
                        </form>
                        @endif
                        
                        @if($booking->status != 'cancelled' && $booking->status != 'completed')
                        <form action="{{ route('admin.bookings.cancel', $booking) }}" method="POST" class="inline">
                            @csrf
                            @method('PATCH')
                            <button type="submit" 
                                    class="inline-flex items-center px-3 py-2 bg-gradient-to-r from-orange-500 to-orange-600 text-white text-xs font-semibold rounded-lg hover:shadow-lg transition-all duration-200" 
                                    title="Cancel Booking" 
                                    onclick="return confirm('Cancel this booking?')">
                                <i class="fas fa-ban mr-1"></i>Cancel
                            </button>
                        </form>
                        @endif
                        
                        <!-- Delete Button (For Completed or Cancelled Bookings) -->
                        @if($booking->status == 'cancelled' || $booking->status == 'completed')
                        <form action="{{ route('admin.bookings.destroy', $booking) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    class="inline-flex items-center px-3 py-2 bg-gradient-to-r from-red-600 to-red-700 text-white text-xs font-semibold rounded-lg hover:shadow-lg transition-all duration-200" 
                                    title="Delete Booking" 
                                    onclick="return confirm('Permanently delete this booking? This action cannot be undone.')">
                                <i class="fas fa-trash-alt mr-1"></i>Delete
                            </button>
                        </form>
                        @endif
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="px-6 py-4 text-center text-gray-600">No bookings found</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-6">
    {{ $bookings->links() }}
</div>
@endsection
