@extends('layouts.admin')

@section('page-title', 'Manage Suite Availability')

@section('main-content')
<div class="mb-6">
    <p class="text-gray-600">Manage the availability status of suites for customer bookings.</p>
</div>

@if(session('success'))
<div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
    {{ session('success') }}
</div>
@endif

@if($suites->count() > 0)
<div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
    @foreach($suites as $suite)
    <div class="bg-white rounded-lg shadow-lg overflow-hidden {{ $suite->is_active ? 'border-l-4 border-green-500' : 'border-l-4 border-red-500' }}">
        <!-- Image -->
        <div class="relative h-48 bg-gray-200">
            @if($suite->images && count($suite->images) > 0)
            <img src="{{ \App\Helpers\ImageHelper::getImageUrl($suite->images[0]) }}" 
                 alt="{{ $suite->name }}" 
                 class="w-full h-full object-cover {{ !$suite->is_active ? 'opacity-50 grayscale' : '' }}">
            @else
            <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-blue-100 to-cyan-100 {{ !$suite->is_active ? 'opacity-50 grayscale' : '' }}">
                <i class="fas fa-bed text-6xl text-blue-300"></i>
            </div>
            @endif
            
            <!-- Status Badge -->
            <div class="absolute top-3 right-3">
                <span class="px-3 py-1 {{ $suite->is_active ? 'bg-green-600' : 'bg-red-600' }} text-white rounded-full text-xs font-semibold shadow-lg">
                    <i class="fas fa-{{ $suite->is_active ? 'check-circle' : 'times-circle' }} mr-1"></i>
                    {{ $suite->is_active ? 'Available' : 'Not Available' }}
                </span>
            </div>
            
            <!-- Suite Type Badge -->
            <div class="absolute top-3 left-3">
                <span class="px-3 py-1 bg-blue-600 text-white rounded-full text-xs font-semibold shadow-lg">
                    <i class="fas fa-bed mr-1"></i>Suite
                </span>
            </div>
        </div>

        <!-- Content -->
        <div class="p-5">
            <div class="flex items-center justify-between mb-2">
                <h3 class="text-xl font-bold text-gray-800">{{ $suite->name }}</h3>
                
                <div class="flex items-center space-x-2">
                    <!-- Book Button -->
                    <a href="{{ route('admin.bookings.create', ['venue_id' => $suite->id]) }}" 
                       class="px-3 py-1 bg-blue-100 text-blue-700 hover:bg-blue-200 rounded-full text-xs font-semibold transition-all duration-200">
                        <i class="fas fa-calendar-plus mr-1"></i>
                        Book
                    </a>
                    
                    <!-- Suite Availability Toggle -->
                    <form action="{{ route('admin.venues.toggle-availability', $suite) }}" method="POST" class="inline">
                        @csrf
                        @method('PATCH')
                        <button type="submit" 
                                class="px-3 py-1 rounded-full text-xs font-semibold transition-all duration-200 {{ $suite->is_active ? 'bg-red-100 text-red-700 hover:bg-red-200' : 'bg-green-100 text-green-700 hover:bg-green-200' }}"
                                onclick="return confirm('{{ $suite->is_active ? 'Make this suite unavailable?' : 'Make this suite available?' }}')">
                            <i class="fas fa-{{ $suite->is_active ? 'times' : 'check' }} mr-1"></i>
                            {{ $suite->is_active ? 'Set Unavailable' : 'Set Available' }}
                        </button>
                    </form>
                </div>
            </div>
            
            <p class="text-gray-600 text-sm mb-4 line-clamp-2">{{ $suite->description }}</p>

            <!-- Details -->
            <div class="space-y-2 mb-4">
                <div class="flex items-center text-sm text-gray-600">
                    <i class="fas fa-users w-5 text-blue-600"></i>
                    <span>Capacity: {{ $suite->capacity }} people</span>
                </div>
                <div class="flex items-center text-sm text-gray-600">
                    <i class="fas fa-tag w-5 text-blue-600"></i>
                    <span>₱{{ number_format($suite->price_per_day, 2) }}/22 hours</span>
                </div>
                <div class="flex items-center text-sm text-gray-600">
                    <i class="fas fa-clock w-5 text-blue-600"></i>
                    <span>Check-in: 2PM | Check-out: 12PM</span>
                </div>
            </div>

            <!-- Amenities -->
            @if($suite->amenities && count($suite->amenities) > 0)
            <div class="border-t pt-3">
                <p class="text-xs font-semibold text-gray-700 mb-2">Amenities:</p>
                <div class="flex flex-wrap gap-1">
                    @foreach($suite->amenities as $amenity)
                    <span class="px-2 py-1 bg-blue-50 text-blue-700 rounded text-xs">
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
    <i class="fas fa-bed text-6xl text-gray-300 mb-4"></i>
    <p class="text-gray-600 text-lg">No suites found.</p>
</div>
@endif
@endsection
