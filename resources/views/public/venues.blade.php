@extends('layouts.public')

@section('main-content')
<!-- Enhanced Venues Header with Parallax Effect -->
<section class="relative overflow-hidden">
    <!-- Background with Parallax -->
    <div class="absolute inset-0 bg-gradient-to-br from-purple-900 via-purple-800 to-indigo-900"></div>
    <div class="absolute inset-0 bg-black/20"></div>
    
    <!-- Animated Background Elements -->
    <div class="absolute inset-0 overflow-hidden">
        <div class="absolute -top-40 -right-40 w-80 h-80 bg-white/5 rounded-full blur-3xl animate-pulse"></div>
        <div class="absolute -bottom-40 -left-40 w-96 h-96 bg-purple-400/10 rounded-full blur-3xl animate-pulse delay-1000"></div>
    </div>
    
    <div class="relative container mx-auto px-4 py-20 text-center text-white">
        <!-- Icon with Glow Effect -->
        <div class="inline-block relative mb-8">
            <div class="absolute inset-0 bg-white/20 rounded-full blur-xl scale-110"></div>
            <div class="relative bg-white/10 backdrop-blur-sm rounded-full p-8 border border-white/20">
                <i class="fas fa-building text-6xl text-white drop-shadow-lg"></i>
            </div>
        </div>
        
        <!-- Enhanced Typography -->
        <h1 class="text-6xl md:text-7xl font-black mb-6 bg-gradient-to-r from-white to-purple-200 bg-clip-text text-transparent drop-shadow-2xl">
            Premium Venues
        </h1>
        <p class="text-xl md:text-2xl mb-4 text-purple-100 font-light max-w-3xl mx-auto leading-relaxed">
            Discover exceptional spaces designed for unforgettable events, meetings, and celebrations
        </p>
        
        <!-- Enhanced Info Cards -->
        <div class="flex flex-wrap justify-center gap-6 mt-8">
            <div class="bg-white/10 backdrop-blur-sm rounded-2xl px-6 py-3 border border-white/20">
                <i class="fas fa-clock mr-2 text-purple-300"></i>
                <span class="text-sm font-medium">Morning: 8AM - 12PM</span>
            </div>
            <div class="bg-white/10 backdrop-blur-sm rounded-2xl px-6 py-3 border border-white/20">
                <i class="fas fa-sun mr-2 text-yellow-300"></i>
                <span class="text-sm font-medium">Afternoon: 1PM - 5PM</span>
            </div>
            <div class="bg-white/10 backdrop-blur-sm rounded-2xl px-6 py-3 border border-white/20">
                <i class="fas fa-moon mr-2 text-blue-300"></i>
                <span class="text-sm font-medium">Evening: 6PM - 10PM</span>
            </div>
        </div>
    </div>
</section>

<!-- Enhanced Filter & Search Section -->
<section class="bg-gray-50 py-8 border-b">
    <div class="container mx-auto px-4">
        <div class="flex flex-col md:flex-row gap-4 items-center justify-between">
            <div class="flex items-center space-x-4">
                <h2 class="text-2xl font-bold text-gray-800">Available Venues</h2>
                <span class="bg-purple-100 text-purple-800 px-3 py-1 rounded-full text-sm font-semibold">
                    {{ $venues->count() }} {{ Str::plural('venue', $venues->count()) }}
                </span>
            </div>
            
            <!-- Search and Filter Controls -->
            <div class="flex flex-col sm:flex-row gap-3 w-full md:w-auto">
                <div class="relative">
                    <input type="text" id="venueSearch" placeholder="Search venues..." 
                           class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent w-full sm:w-64">
                    <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                </div>
                <select id="capacityFilter" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                    <option value="">All Capacities</option>
                    <option value="0-50">Up to 50 guests</option>
                    <option value="51-100">51-100 guests</option>
                    <option value="101-200">101-200 guests</option>
                    <option value="200+">200+ guests</option>
                </select>
            </div>
        </div>
    </div>
</section>

<!-- Enhanced Venues Grid -->
<section class="container mx-auto px-4 py-16">
    @if($venues->count() > 0)
    <div class="grid md:grid-cols-2 xl:grid-cols-3 gap-8" id="venuesGrid">
        @foreach($venues as $venue)
        <div class="venue-card bg-white rounded-2xl shadow-lg overflow-hidden hover:shadow-2xl transition-all duration-500 transform hover:-translate-y-2 group" 
             data-name="{{ strtolower($venue->name) }}" 
             data-capacity="{{ $venue->capacity }}">
            
            @if($venue->images && count($venue->images) > 0)
                <!-- Enhanced Image Carousel -->
                <div class="relative h-64 overflow-hidden">
                    @foreach($venue->images as $index => $image)
                    <div class="carousel-item absolute inset-0 transition-all duration-700 {{ $index === 0 ? 'opacity-100 scale-100' : 'opacity-0 scale-105' }}" data-carousel="venue-{{ $venue->id }}">
                        <img src="{{ \App\Helpers\ImageHelper::getImageUrl($image) }}" alt="{{ $venue->name }}" 
                             class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/50 via-transparent to-transparent"></div>
                    </div>
                    @endforeach
                    
                    <!-- Enhanced Navigation -->
                    @if(count($venue->images) > 1)
                    <button onclick="prevSlide('venue-{{ $venue->id }}')" 
                            class="absolute left-3 top-1/2 -translate-y-1/2 bg-white/20 backdrop-blur-sm text-white p-3 rounded-full opacity-0 group-hover:opacity-100 transition-all duration-300 hover:bg-white/30 hover:scale-110">
                        <i class="fas fa-chevron-left"></i>
                    </button>
                    <button onclick="nextSlide('venue-{{ $venue->id }}')" 
                            class="absolute right-3 top-1/2 -translate-y-1/2 bg-white/20 backdrop-blur-sm text-white p-3 rounded-full opacity-0 group-hover:opacity-100 transition-all duration-300 hover:bg-white/30 hover:scale-110">
                        <i class="fas fa-chevron-right"></i>
                    </button>
                    
                    <!-- Enhanced Dots -->
                    <div class="absolute bottom-4 left-1/2 -translate-x-1/2 flex space-x-2">
                        @foreach($venue->images as $index => $image)
                        <button onclick="goToSlide('venue-{{ $venue->id }}', {{ $index }})" 
                                class="carousel-dot w-3 h-3 rounded-full bg-white/50 hover:bg-white transition-all duration-300 {{ $index === 0 ? 'bg-white scale-125' : '' }}" 
                                data-carousel="venue-{{ $venue->id }}" data-index="{{ $index }}"></button>
                        @endforeach
                    </div>
                    @endif
                    
                    <!-- Venue Type Badge -->
                    <div class="absolute top-4 right-4">
                        <span class="bg-purple-600/90 backdrop-blur-sm text-white px-4 py-2 rounded-full text-xs font-bold tracking-wide shadow-lg">
                            <i class="fas fa-building mr-1"></i>VENUE
                        </span>
                    </div>
                </div>
            @else
                <div class="w-full h-64 bg-gradient-to-br from-purple-500 to-indigo-600 flex items-center justify-center relative overflow-hidden">
                    <div class="absolute inset-0 bg-black/10"></div>
                    <i class="fas fa-building text-white text-8xl opacity-80 relative z-10"></i>
                    <div class="absolute top-4 right-4">
                        <span class="bg-white/20 backdrop-blur-sm text-white px-4 py-2 rounded-full text-xs font-bold tracking-wide">
                            <i class="fas fa-building mr-1"></i>VENUE
                        </span>
                    </div>
                </div>
            @endif
            
            <!-- Enhanced Content -->
            <div class="p-6">
                <div class="flex items-start justify-between mb-3">
                    <h3 class="text-2xl font-bold text-gray-900 group-hover:text-purple-600 transition-colors duration-300">
                        {{ $venue->name }}
                    </h3>
                    <div class="flex items-center space-x-1 text-yellow-400">
                        @for($i = 1; $i <= 5; $i++)
                        <i class="fas fa-star text-sm"></i>
                        @endfor
                    </div>
                </div>
                
                <p class="text-gray-600 mb-4 leading-relaxed">{{ Str::limit($venue->description, 120) }}</p>
                
                <!-- Enhanced Features -->
                <div class="space-y-4 mb-6">
                    <div class="flex items-center text-gray-700 bg-gray-50 rounded-lg p-3">
                        <div class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center mr-3">
                            <i class="fas fa-users text-purple-600"></i>
                        </div>
                        <div>
                            <span class="font-semibold text-gray-900">Capacity</span>
                            <p class="text-sm text-gray-600">Up to {{ $venue->capacity }} guests</p>
                        </div>
                    </div>
                    
                    <!-- Enhanced Pricing Display -->
                    @if($venue->price_morning || $venue->price_afternoon || $venue->price_evening)
                    <div class="bg-gradient-to-r from-purple-50 to-indigo-50 p-4 rounded-xl border border-purple-100">
                        <div class="flex items-center justify-between mb-3">
                            <span class="font-bold text-purple-800 flex items-center">
                                <i class="fas fa-clock mr-2"></i>Flexible Pricing
                            </span>
                            <span class="text-xs text-purple-600 bg-purple-100 px-2 py-1 rounded-full">Time-based</span>
                        </div>
                        <div class="grid grid-cols-3 gap-2 text-sm">
                            @if($venue->price_morning)
                            <div class="text-center p-2 bg-white rounded-lg">
                                <div class="text-xs text-gray-500 mb-1">Morning</div>
                                <div class="font-bold text-purple-600">₱{{ number_format($venue->price_morning, 0) }}</div>
                            </div>
                            @endif
                            @if($venue->price_afternoon)
                            <div class="text-center p-2 bg-white rounded-lg">
                                <div class="text-xs text-gray-500 mb-1">Afternoon</div>
                                <div class="font-bold text-purple-600">₱{{ number_format($venue->price_afternoon, 0) }}</div>
                            </div>
                            @endif
                            @if($venue->price_evening)
                            <div class="text-center p-2 bg-white rounded-lg">
                                <div class="text-xs text-gray-500 mb-1">Evening</div>
                                <div class="font-bold text-purple-600">₱{{ number_format($venue->price_evening, 0) }}</div>
                            </div>
                            @endif
                        </div>
                    </div>
                    @else
                    <div class="bg-gradient-to-r from-purple-50 to-indigo-50 p-4 rounded-xl border border-purple-100 text-center">
                        <div class="text-sm text-purple-600 mb-1">Full Day Rate</div>
                        <div class="text-3xl font-bold text-purple-700">₱{{ number_format($venue->price_per_day, 0) }}</div>
                        <div class="text-xs text-purple-500">per day</div>
                    </div>
                    @endif
                </div>
                
                <!-- Enhanced CTA Button -->
                <a href="{{ route('venue.details', $venue->id) }}" 
                   class="block w-full text-center bg-gradient-to-r from-purple-600 to-indigo-600 text-white py-4 rounded-xl hover:from-purple-700 hover:to-indigo-700 transition-all duration-300 font-semibold shadow-lg hover:shadow-xl transform hover:scale-105 group">
                    <i class="fas fa-eye mr-2 group-hover:mr-3 transition-all duration-300"></i>
                    <span>Explore Details</span>
                    <i class="fas fa-arrow-right ml-2 opacity-0 group-hover:opacity-100 transition-all duration-300"></i>
                </a>
            </div>
        </div>
        @endforeach
    </div>
    
    <!-- No Results Message -->
    <div id="noResults" class="hidden text-center py-20">
        <div class="inline-block bg-gray-100 rounded-full p-8 mb-6">
            <i class="fas fa-search text-gray-400 text-7xl"></i>
        </div>
        <h3 class="text-2xl font-bold text-gray-700 mb-2">No Venues Found</h3>
        <p class="text-gray-600 text-lg mb-8">Try adjusting your search criteria</p>
        <button onclick="clearFilters()" class="inline-block bg-purple-600 text-white px-8 py-3 rounded-full font-semibold hover:bg-purple-700 transition">
            <i class="fas fa-refresh mr-2"></i>Clear Filters
        </button>
    </div>
    
    @else
    <div class="text-center py-20">
        <div class="inline-block bg-gradient-to-br from-purple-100 to-indigo-100 rounded-full p-12 mb-8">
            <i class="fas fa-building text-purple-400 text-8xl"></i>
        </div>
        <h3 class="text-3xl font-bold text-gray-800 mb-4">No Venues Available</h3>
        <p class="text-gray-600 text-xl mb-8 max-w-md mx-auto">We're preparing amazing venues for you. Check back soon!</p>
        <a href="{{ route('home') }}" class="inline-block bg-gradient-to-r from-purple-600 to-indigo-600 text-white px-10 py-4 rounded-full font-bold hover:from-purple-700 hover:to-indigo-700 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105">
            <i class="fas fa-home mr-2"></i>Back to Home
        </a>
    </div>
    @endif
</section>

<!-- Enhanced CTA Section -->
<section class="relative overflow-hidden">
    <div class="absolute inset-0 bg-gradient-to-r from-purple-900 via-indigo-900 to-purple-900"></div>
    <div class="absolute inset-0 bg-black/20"></div>
    
    <!-- Animated Background -->
    <div class="absolute inset-0 overflow-hidden">
        <div class="absolute top-1/4 left-1/4 w-64 h-64 bg-white/5 rounded-full blur-3xl animate-pulse"></div>
        <div class="absolute bottom-1/4 right-1/4 w-80 h-80 bg-purple-400/10 rounded-full blur-3xl animate-pulse delay-1000"></div>
    </div>
    
    <div class="relative container mx-auto px-4 py-20 text-center text-white">
        <div class="max-w-4xl mx-auto">
            <h2 class="text-4xl md:text-5xl font-bold mb-6 bg-gradient-to-r from-white to-purple-200 bg-clip-text text-transparent">
                Found Your Perfect Venue?
            </h2>
            <p class="text-xl md:text-2xl mb-10 text-purple-100 font-light leading-relaxed">
                Let's bring your vision to life. Contact our expert team to check availability and create an unforgettable experience.
            </p>
            
            <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
                <a href="{{ route('contact') }}" 
                   class="group bg-white text-purple-900 px-10 py-4 rounded-full font-bold hover:bg-gray-100 transition-all duration-300 transform hover:scale-105 shadow-2xl flex items-center">
                    <i class="fas fa-phone mr-3 group-hover:animate-pulse"></i>
                    <span>Contact Us Now</span>
                    <i class="fas fa-arrow-right ml-3 opacity-0 group-hover:opacity-100 transition-all duration-300"></i>
                </a>
                
                <div class="flex items-center space-x-4 text-purple-200">
                    <div class="flex items-center">
                        <i class="fas fa-clock mr-2"></i>
                        <span class="text-sm">Quick Response</span>
                    </div>
                    <div class="w-1 h-1 bg-purple-300 rounded-full"></div>
                    <div class="flex items-center">
                        <i class="fas fa-shield-alt mr-2"></i>
                        <span class="text-sm">Secure Booking</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
// Enhanced Image Carousel Functions
let carouselIntervals = {};

function nextSlide(carouselId) {
    const items = document.querySelectorAll(`[data-carousel="${carouselId}"].carousel-item`);
    const dots = document.querySelectorAll(`[data-carousel="${carouselId}"].carousel-dot`);
    let currentIndex = Array.from(items).findIndex(item => item.classList.contains('opacity-100'));
    
    items[currentIndex].classList.remove('opacity-100', 'scale-100');
    items[currentIndex].classList.add('opacity-0', 'scale-105');
    dots[currentIndex].classList.remove('bg-white', 'scale-125');
    dots[currentIndex].classList.add('bg-white/50');
    
    currentIndex = (currentIndex + 1) % items.length;
    
    items[currentIndex].classList.remove('opacity-0', 'scale-105');
    items[currentIndex].classList.add('opacity-100', 'scale-100');
    dots[currentIndex].classList.remove('bg-white/50');
    dots[currentIndex].classList.add('bg-white', 'scale-125');
}

function prevSlide(carouselId) {
    const items = document.querySelectorAll(`[data-carousel="${carouselId}"].carousel-item`);
    const dots = document.querySelectorAll(`[data-carousel="${carouselId}"].carousel-dot`);
    let currentIndex = Array.from(items).findIndex(item => item.classList.contains('opacity-100'));
    
    items[currentIndex].classList.remove('opacity-100', 'scale-100');
    items[currentIndex].classList.add('opacity-0', 'scale-105');
    dots[currentIndex].classList.remove('bg-white', 'scale-125');
    dots[currentIndex].classList.add('bg-white/50');
    
    currentIndex = (currentIndex - 1 + items.length) % items.length;
    
    items[currentIndex].classList.remove('opacity-0', 'scale-105');
    items[currentIndex].classList.add('opacity-100', 'scale-100');
    dots[currentIndex].classList.remove('bg-white/50');
    dots[currentIndex].classList.add('bg-white', 'scale-125');
}

function goToSlide(carouselId, index) {
    const items = document.querySelectorAll(`[data-carousel="${carouselId}"].carousel-item`);
    const dots = document.querySelectorAll(`[data-carousel="${carouselId}"].carousel-dot`);
    
    items.forEach(item => {
        item.classList.remove('opacity-100', 'scale-100');
        item.classList.add('opacity-0', 'scale-105');
    });
    dots.forEach(dot => {
        dot.classList.remove('bg-white', 'scale-125');
        dot.classList.add('bg-white/50');
    });
    
    items[index].classList.remove('opacity-0', 'scale-105');
    items[index].classList.add('opacity-100', 'scale-100');
    dots[index].classList.remove('bg-white/50');
    dots[index].classList.add('bg-white', 'scale-125');
}

// Enhanced Search and Filter Functions
function filterVenues() {
    const searchTerm = document.getElementById('venueSearch').value.toLowerCase();
    const capacityFilter = document.getElementById('capacityFilter').value;
    const venueCards = document.querySelectorAll('.venue-card');
    const noResults = document.getElementById('noResults');
    let visibleCount = 0;
    
    venueCards.forEach(card => {
        const name = card.dataset.name;
        const capacity = parseInt(card.dataset.capacity);
        let showCard = true;
        
        // Search filter
        if (searchTerm && !name.includes(searchTerm)) {
            showCard = false;
        }
        
        // Capacity filter
        if (capacityFilter) {
            const [min, max] = capacityFilter.split('-').map(v => v.replace('+', ''));
            if (capacityFilter.includes('+')) {
                if (capacity < parseInt(min)) showCard = false;
            } else {
                if (capacity < parseInt(min) || capacity > parseInt(max)) showCard = false;
            }
        }
        
        if (showCard) {
            card.style.display = 'block';
            visibleCount++;
        } else {
            card.style.display = 'none';
        }
    });
    
    // Show/hide no results message
    if (visibleCount === 0) {
        noResults.classList.remove('hidden');
    } else {
        noResults.classList.add('hidden');
    }
}

function clearFilters() {
    document.getElementById('venueSearch').value = '';
    document.getElementById('capacityFilter').value = '';
    filterVenues();
}

// Auto-slide functionality
function startAutoSlide(carouselId) {
    const items = document.querySelectorAll(`[data-carousel="${carouselId}"].carousel-item`);
    if (items.length <= 1) return;
    
    carouselIntervals[carouselId] = setInterval(() => {
        nextSlide(carouselId);
    }, 4000); // Slower auto-slide for better UX
}

function stopAutoSlide(carouselId) {
    if (carouselIntervals[carouselId]) {
        clearInterval(carouselIntervals[carouselId]);
        delete carouselIntervals[carouselId];
    }
}

// Initialize everything when page loads
document.addEventListener('DOMContentLoaded', function() {
    // Initialize search and filters
    document.getElementById('venueSearch').addEventListener('input', filterVenues);
    document.getElementById('capacityFilter').addEventListener('change', filterVenues);
    
    // Initialize carousels
    const carouselIds = new Set();
    document.querySelectorAll('[data-carousel]').forEach(element => {
        const carouselId = element.getAttribute('data-carousel');
        carouselIds.add(carouselId);
    });
    
    carouselIds.forEach(carouselId => {
        startAutoSlide(carouselId);
        
        const carouselContainer = document.querySelector(`[data-carousel="${carouselId}"]`).closest('.relative');
        if (carouselContainer) {
            carouselContainer.addEventListener('mouseenter', () => stopAutoSlide(carouselId));
            carouselContainer.addEventListener('mouseleave', () => startAutoSlide(carouselId));
        }
    });
    
    // Add scroll animations
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, observerOptions);
    
    document.querySelectorAll('.venue-card').forEach(card => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        card.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
        observer.observe(card);
    });
});

// Override manual navigation to restart auto-slide
const originalNextSlide = nextSlide;
const originalPrevSlide = prevSlide;
const originalGoToSlide = goToSlide;

nextSlide = function(carouselId) {
    stopAutoSlide(carouselId);
    originalNextSlide(carouselId);
    setTimeout(() => startAutoSlide(carouselId), 5000);
};

prevSlide = function(carouselId) {
    stopAutoSlide(carouselId);
    originalPrevSlide(carouselId);
    setTimeout(() => startAutoSlide(carouselId), 5000);
};

goToSlide = function(carouselId, index) {
    stopAutoSlide(carouselId);
    originalGoToSlide(carouselId, index);
    setTimeout(() => startAutoSlide(carouselId), 5000);
};
</script>

<style>
/* Enhanced Animations and Effects */
@keyframes float {
    0%, 100% { transform: translateY(0px); }
    50% { transform: translateY(-10px); }
}

.card-hover {
    transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
}

.card-hover:hover {
    transform: translateY(-8px) scale(1.02);
}

/* Smooth scrolling */
html {
    scroll-behavior: smooth;
}

/* Custom scrollbar */
::-webkit-scrollbar {
    width: 8px;
}

::-webkit-scrollbar-track {
    background: #f1f1f1;
}

::-webkit-scrollbar-thumb {
    background: linear-gradient(to bottom, #8B5CF6, #6366F1);
    border-radius: 4px;
}

::-webkit-scrollbar-thumb:hover {
    background: linear-gradient(to bottom, #7C3AED, #4F46E5);
}

/* Enhanced button effects */
.group:hover .fas {
    animation: float 2s ease-in-out infinite;
}

/* Loading animation for images */
img {
    transition: opacity 0.3s ease;
}

img:not([src]) {
    opacity: 0;
}
</style>
@endsection
