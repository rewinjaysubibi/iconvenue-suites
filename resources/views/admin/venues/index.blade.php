@extends('layouts.admin')

@section('page-title', 'Venues Management')

@section('main-content')
<div class="mb-6 flex justify-between items-center">
    <div class="flex space-x-4">
        <a href="{{ route('admin.venues.create') }}" class="bg-purple-600 text-white px-6 py-3 rounded-lg hover:bg-purple-700 transition">
            <i class="fas fa-plus mr-2"></i>Add New Venue
        </a>
    </div>
</div>

<!-- Info Box -->
<div class="bg-gradient-to-r from-purple-50 to-pink-50 border-l-4 border-purple-600 rounded-lg p-4 mb-6">
    <div class="flex items-start">
        <i class="fas fa-info-circle text-purple-600 text-xl mr-3 mt-1"></i>
        <div>
            <h3 class="font-semibold text-purple-900 mb-1">Venues Management</h3>
            <p class="text-sm text-purple-800">
                Manage all <strong>Venues</strong> here. You can create event packages (Birthday, Wedding, Corporate, etc.) with custom pricing per venue.
            </p>
        </div>
    </div>
</div>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="min-w-full">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Capacity</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Price/Day</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
            @forelse($venues as $venue)
            <tr>
                <td class="px-6 py-4">
                    <div class="flex items-center">
                        <i class="fas fa-building text-purple-600 text-xl mr-3"></i>
                        <div class="font-semibold text-gray-800">{{ $venue->name }}</div>
                    </div>
                </td>
                <td class="px-6 py-4 text-gray-600">{{ $venue->capacity }} guests</td>
                <td class="px-6 py-4 text-gray-600">₱{{ number_format($venue->price_per_day, 2) }}</td>
                <td class="px-6 py-4">
                    <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $venue->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                        {{ $venue->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </td>
                <td class="px-6 py-4">
                    <div class="flex flex-wrap gap-2">
                        <!-- Book Button -->
                        <a href="{{ route('admin.bookings.create', ['venue_id' => $venue->id]) }}" 
                           class="inline-flex items-center px-3 py-2 bg-gradient-to-r from-purple-500 to-purple-600 text-white text-xs font-semibold rounded-lg hover:shadow-lg transition-all duration-200" 
                           title="Create Booking">
                            <i class="fas fa-calendar-plus mr-1"></i>Book
                        </a>
                        
                        <!-- Availability Toggle -->
                        <form action="{{ route('admin.venues.toggle-availability', $venue) }}" method="POST" class="inline">
                            @csrf
                            @method('PATCH')
                            <button type="submit" 
                                    class="inline-flex items-center px-3 py-2 text-xs font-semibold rounded-lg transition-all duration-200 {{ $venue->is_active ? 'bg-gradient-to-r from-red-500 to-red-600 text-white hover:shadow-lg' : 'bg-gradient-to-r from-green-500 to-green-600 text-white hover:shadow-lg' }}"
                                    title="{{ $venue->is_active ? 'Set Unavailable' : 'Set Available' }}"
                                    onclick="return confirm('{{ $venue->is_active ? 'Make this venue unavailable for bookings?' : 'Make this venue available for bookings?' }}')">
                                <i class="fas fa-{{ $venue->is_active ? 'times' : 'check' }} mr-1"></i>
                                {{ $venue->is_active ? 'Set Unavailable' : 'Set Available' }}
                            </button>
                        </form>
                        
                        <!-- Edit Button -->
                        <a href="{{ route('admin.venues.edit', $venue) }}" 
                           class="inline-flex items-center px-3 py-2 bg-gradient-to-r from-blue-500 to-blue-600 text-white text-xs font-semibold rounded-lg hover:shadow-lg transition-all duration-200" 
                           title="Edit Venue">
                            <i class="fas fa-edit mr-1"></i>Edit
                        </a>
                        
                        <!-- Manage Packages Button -->
                        <a href="{{ route('admin.venues.packages', $venue) }}" 
                           class="inline-flex items-center px-3 py-2 bg-gradient-to-r from-purple-500 to-pink-500 text-white text-xs font-semibold rounded-lg hover:shadow-lg transition-all duration-200" 
                           title="Manage Event Packages">
                            <i class="fas fa-box-open mr-1"></i>Packages
                        </a>
                        
                        <!-- Delete Button -->
                        <form action="{{ route('admin.venues.destroy', $venue) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    class="inline-flex items-center px-3 py-2 bg-gradient-to-r from-red-600 to-red-700 text-white text-xs font-semibold rounded-lg hover:shadow-lg transition-all duration-200" 
                                    title="Delete Venue"
                                    onclick="return confirm('Permanently delete this venue? This will also delete all associated bookings and packages. This action cannot be undone.')">
                                <i class="fas fa-trash-alt mr-1"></i>Delete
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="px-6 py-12 text-center">
                    <i class="fas fa-building text-gray-300 text-6xl mb-4"></i>
                    <p class="text-gray-600 text-xl">No venues found</p>
                    <a href="{{ route('admin.venues.create') }}" class="inline-block mt-4 bg-purple-600 text-white px-6 py-3 rounded-lg hover:bg-purple-700 transition">
                        <i class="fas fa-plus mr-2"></i>Add Your First Venue
                    </a>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-6">
    {{ $venues->links() }}
</div>
@endsection
