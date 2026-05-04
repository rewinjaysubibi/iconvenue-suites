@extends('layouts.admin')

@section('page-title', 'Manage Venue Availability')

@section('main-content')
<div class="mb-6">
    <p class="text-gray-600">Manage the availability status of venues and their packages for customer bookings.</p>
</div>

@if(session('success'))
<div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
    {{ session('success') }}
</div>
@endif

@if($venues->count() > 0)
<div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
    @foreach($venues as $venue)
    <div class="bg-white rounded-lg shadow-lg overflow-hidden {{ $venue->is_active ? 'border-l-4 border-green-500' : 'border-l-4 border-red-500' }}">
        <!-- Image -->
        <div class="relative h-48 bg-gray-200">
            @if($venue->images && count($venue->images) > 0)
            <img src="{{ \App\Helpers\ImageHelper::getImageUrl($venue->images[0]) }}" 
                 alt="{{ $venue->name }}" 
                 class="w-full h-full object-cover {{ !$venue->is_active ? 'opacity-50 grayscale' : '' }}">
            @else
            <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-purple-100 to-pink-100 {{ !$venue->is_active ? 'opacity-50 grayscale' : '' }}">
                <i class="fas fa-building text-6xl text-purple-300"></i>
            </div>
            @endif
            
            <!-- Status Badge -->
            <div class="absolute top-3 right-3">
                <span class="px-3 py-1 {{ $venue->is_active ? 'bg-green-600' : 'bg-red-600' }} text-white rounded-full text-xs font-semibold shadow-lg">
                    <i class="fas fa-{{ $venue->is_active ? 'check-circle' : 'times-circle' }} mr-1"></i>
                    {{ $venue->is_active ? 'Available' : 'Not Available' }}
                </span>
            </div>
            
            <!-- Venue Type Badge -->
            <div class="absolute top-3 left-3">
                <span class="px-3 py-1 bg-purple-600 text-white rounded-full text-xs font-semibold shadow-lg">
                    <i class="fas fa-building mr-1"></i>Venue
                </span>
            </div>
        </div>

        <!-- Content -->
        <div class="p-5">
            <div class="flex items-center justify-between mb-2">
                <h3 class="text-xl font-bold text-gray-800">{{ $venue->name }}</h3>
                
                <div class="flex items-center space-x-2">
                    <!-- Book Button -->
                    <a href="{{ route('admin.bookings.create', ['venue_id' => $venue->id]) }}" 
                       class="px-3 py-1 bg-purple-100 text-purple-700 hover:bg-purple-200 rounded-full text-xs font-semibold transition-all duration-200">
                        <i class="fas fa-calendar-plus mr-1"></i>
                        Book
                    </a>
                    
                    <!-- Venue Availability Toggle -->
                    <form action="{{ route('admin.venues.toggle-availability', $venue) }}" method="POST" class="inline">
                        @csrf
                        @method('PATCH')
                        <button type="submit" 
                                class="px-3 py-1 rounded-full text-xs font-semibold transition-all duration-200 {{ $venue->is_active ? 'bg-red-100 text-red-700 hover:bg-red-200' : 'bg-green-100 text-green-700 hover:bg-green-200' }}"
                                onclick="return confirm('{{ $venue->is_active ? 'Make this venue unavailable?' : 'Make this venue available?' }}')">
                            <i class="fas fa-{{ $venue->is_active ? 'times' : 'check' }} mr-1"></i>
                            {{ $venue->is_active ? 'Set Unavailable' : 'Set Available' }}
                        </button>
                    </form>
                </div>
            </div>
            
            <p class="text-gray-600 text-sm mb-4 line-clamp-2">{{ $venue->description }}</p>

            <!-- Details -->
            <div class="space-y-2 mb-4">
                <div class="flex items-center text-sm text-gray-600">
                    <i class="fas fa-users w-5 text-purple-600"></i>
                    <span>Capacity: {{ $venue->capacity }} people</span>
                </div>
                <div class="flex items-center text-sm text-gray-600">
                    <i class="fas fa-tag w-5 text-purple-600"></i>
                    <span>₱{{ number_format($venue->price_per_day, 2) }}/day</span>
                </div>
            </div>

            <!-- Time-based Pricing -->
            @if($venue->price_morning || $venue->price_afternoon || $venue->price_evening)
            <div class="border-t pt-3 mb-4">
                <p class="text-xs font-semibold text-gray-700 mb-2">Time Slot Pricing:</p>
                <div class="grid grid-cols-3 gap-2 text-xs">
                    @if($venue->price_morning)
                    <div class="text-center">
                        <p class="text-gray-600">Morning</p>
                        <p class="font-semibold text-purple-600">₱{{ number_format($venue->price_morning, 0) }}</p>
                    </div>
                    @endif
                    @if($venue->price_afternoon)
                    <div class="text-center">
                        <p class="text-gray-600">Afternoon</p>
                        <p class="font-semibold text-purple-600">₱{{ number_format($venue->price_afternoon, 0) }}</p>
                    </div>
                    @endif
                    @if($venue->price_evening)
                    <div class="text-center">
                        <p class="text-gray-600">Evening</p>
                        <p class="font-semibold text-purple-600">₱{{ number_format($venue->price_evening, 0) }}</p>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            <!-- Packages with Availability Toggle -->
            @if($venue->packages->count() > 0)
            <div class="border-t pt-3 mb-4">
                <p class="text-xs font-semibold text-gray-700 mb-3">Event Packages:</p>
                <div class="space-y-2">
                    @foreach($venue->packages as $package)
                    <div class="flex items-center justify-between p-2 rounded {{ $package->is_active ? 'bg-green-50 border border-green-200' : 'bg-red-50 border border-red-200' }}">
                        <div class="flex-1">
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-medium {{ $package->is_active ? 'text-green-800' : 'text-red-800' }}">
                                    {{ $package->name }}
                                </span>
                                <span class="text-sm font-semibold {{ $package->is_active ? 'text-green-600' : 'text-red-600' }}">
                                    ₱{{ number_format($package->price, 0) }}
                                </span>
                            </div>
                            <div class="flex items-center mt-1">
                                <span class="text-xs px-2 py-1 rounded {{ $package->is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                    <i class="fas fa-{{ $package->is_active ? 'check-circle' : 'times-circle' }} mr-1"></i>
                                    {{ $package->is_active ? 'Available' : 'Not Available' }}
                                </span>
                            </div>
                        </div>
                        
                        <!-- Package Toggle Button -->
                        <form action="{{ route('admin.venues.packages.toggle-availability', [$venue->id, $package->id]) }}" method="POST" class="ml-2">
                            @csrf
                            @method('PATCH')
                            <button type="submit" 
                                    class="px-2 py-1 rounded text-xs font-semibold transition-all duration-200 {{ $package->is_active ? 'bg-red-100 text-red-700 hover:bg-red-200' : 'bg-green-100 text-green-700 hover:bg-green-200' }}"
                                    onclick="return confirm('{{ $package->is_active ? 'Make this package unavailable?' : 'Make this package available?' }}')">
                                <i class="fas fa-{{ $package->is_active ? 'times' : 'check' }}"></i>
                            </button>
                        </form>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Amenities -->
            @if($venue->amenities && count($venue->amenities) > 0)
            <div class="border-t pt-3">
                <p class="text-xs font-semibold text-gray-700 mb-2">Amenities:</p>
                <div class="flex flex-wrap gap-1">
                    @foreach($venue->amenities as $amenity)
                    <span class="px-2 py-1 bg-purple-50 text-purple-700 rounded text-xs">
                        {{ $amenity }}
                    </span>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>
    @endforeach
</div>
@else
<div class="bg-white rounded-lg shadow p-12 text-center">
    <i class="fas fa-building text-6xl text-gray-300 mb-4"></i>
    <p class="text-gray-600 text-lg">No venues found.</p>
</div>
@endif
@endsection
