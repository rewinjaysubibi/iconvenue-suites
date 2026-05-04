@extends('layouts.public')

@section('main-content')
<section class="relative h-screen flex flex-col overflow-hidden">

    <!-- Image Slideshow Background -->
    <div class="absolute inset-0 z-0">
        <div class="slideshow-container h-full w-full">
            @if($carouselImages->count() > 0)
                @foreach($carouselImages as $index => $image)
                <div class="slide {{ $index === 0 ? 'active' : '' }}">
                    <img src="{{ asset('storage/' . $image->image_path) }}" alt="{{ $image->title }}" class="slide-image">
                </div>
                @endforeach
            @else
                <div class="slide active">
                    <img src="https://images.unsplash.com/photo-1519167758481-83f29da8c2b0?w=1920&q=80" alt="Elegant Venue" class="slide-image">
                </div>
                <div class="slide">
                    <img src="https://images.unsplash.com/photo-1540541338287-41700207dee6?w=1920&q=80" alt="Luxury Suite" class="slide-image">
                </div>
                <div class="slide">
                    <img src="https://images.unsplash.com/photo-1464366400600-7168b8af9bc3?w=1920&q=80" alt="Event Space" class="slide-image">
                </div>
                <div class="slide">
                    <img src="https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?w=1920&q=80" alt="Beautiful Interior" class="slide-image">
                </div>
            @endif
        </div>
    </div>

    <!-- Overlay -->
    <div class="absolute inset-0 bg-gradient-to-br from-purple-900/85 via-pink-800/75 to-purple-900/85 z-10"></div>

    <!-- Pattern -->
    <div class="absolute inset-0 opacity-10 z-10" style="background-image: url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'none\' fill-rule=\'evenodd\'%3E%3Cg fill=\'%23ffffff\' fill-opacity=\'1\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');"></div>

    <!-- Main Content — vertically centered, fills screen -->
    <div class="relative z-20 flex flex-col h-full">

        <!-- Hero Center -->
        <div class="flex-1 flex flex-col items-center justify-center text-center px-4 animate-fade-in">
            <!-- Logo -->
            <div class="inline-block bg-white/10 backdrop-blur-sm rounded-2xl p-4 mb-4 shadow-2xl">
                <img src="{{ asset('images/logo.jpg') }}" alt="Icon Venue & Suites" class="h-20 w-auto">
            </div>

            <h1 class="text-5xl md:text-6xl font-bold text-white mb-2 tracking-tight drop-shadow-2xl">
                Icon Venue & Suites
            </h1>
            <p class="text-lg md:text-xl text-white/90 mb-8 max-w-xl mx-auto drop-shadow-lg">
                Where Elegance Meets Excellence
            </p>

            <!-- CTA Buttons -->
            <div class="flex flex-col md:flex-row gap-4 justify-center items-center mb-8">
                <a href="{{ route('venues') }}" class="group relative overflow-hidden bg-white text-purple-900 px-8 py-4 rounded-full font-bold text-base shadow-2xl hover:shadow-purple-500/50 transition-all duration-300 transform hover:scale-105 w-56">
                    <div class="absolute inset-0 bg-gradient-to-r from-purple-400 to-pink-400 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    <span class="relative flex items-center justify-center group-hover:text-white transition-colors">
                        <i class="fas fa-building mr-2 text-xl"></i>Explore Venues
                    </span>
                </a>
                <a href="{{ route('suites') }}" class="group relative overflow-hidden bg-white text-blue-900 px-8 py-4 rounded-full font-bold text-base shadow-2xl hover:shadow-blue-500/50 transition-all duration-300 transform hover:scale-105 w-56">
                    <div class="absolute inset-0 bg-gradient-to-r from-blue-400 to-cyan-400 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    <span class="relative flex items-center justify-center group-hover:text-white transition-colors">
                        <i class="fas fa-bed mr-2 text-xl"></i>Explore Suites
                    </span>
                </a>
                <a href="{{ route('contact') }}" class="group relative overflow-hidden bg-transparent border-2 border-white text-white px-8 py-4 rounded-full font-bold text-base hover:bg-white hover:text-purple-900 transition-all duration-300 transform hover:scale-105 w-56">
                    <span class="relative flex items-center justify-center">
                        <i class="fas fa-phone mr-2 text-xl"></i>Contact Us
                    </span>
                </a>
            </div>

            <!-- Feature Pills -->
            <div class="flex flex-wrap justify-center gap-3">
                <span class="bg-white/15 backdrop-blur-sm text-white px-4 py-2 rounded-full text-sm font-medium border border-white/20">
                    <i class="fas fa-star mr-1 text-yellow-300"></i> Premium Quality
                </span>
                <span class="bg-white/15 backdrop-blur-sm text-white px-4 py-2 rounded-full text-sm font-medium border border-white/20">
                    <i class="fas fa-calendar-check mr-1 text-green-300"></i> Easy Booking
                </span>
                <span class="bg-white/15 backdrop-blur-sm text-white px-4 py-2 rounded-full text-sm font-medium border border-white/20">
                    <i class="fas fa-headset mr-1 text-blue-300"></i> 24/7 Support
                </span>
            </div>
        </div>

        <!-- Slideshow Dots — pinned to bottom -->
        <div class="flex justify-center pb-6 z-20">
            @if($carouselImages->count() > 0)
                @foreach($carouselImages as $index => $image)
                <button class="dot {{ $index === 0 ? 'active' : '' }} mx-1" onclick="currentSlide({{ $index + 1 }})"></button>
                @endforeach
            @else
                <button class="dot active mx-1" onclick="currentSlide(1)"></button>
                <button class="dot mx-1" onclick="currentSlide(2)"></button>
                <button class="dot mx-1" onclick="currentSlide(3)"></button>
                <button class="dot mx-1" onclick="currentSlide(4)"></button>
            @endif
        </div>
    </div>
</section>

<style>
html, body { overflow: hidden; height: 100%; }

.slideshow-container { position: relative; }
.slide {
    position: absolute; top: 0; left: 0;
    width: 100%; height: 100%;
    opacity: 0; transition: opacity 1.5s ease-in-out;
}
.slide.active { opacity: 1; }
.slide-image { width: 100%; height: 100%; object-fit: cover; object-position: center; }

.dot {
    width: 12px; height: 12px;
    border-radius: 50%;
    background-color: rgba(255,255,255,0.5);
    border: 2px solid rgba(255,255,255,0.8);
    cursor: pointer;
    transition: all 0.3s ease;
    display: inline-block;
}
.dot:hover { background-color: rgba(255,255,255,0.8); transform: scale(1.2); }
.dot.active { background-color: white; width: 40px; border-radius: 6px; }

@keyframes fade-in {
    from { opacity: 0; transform: translateY(20px); }
    to   { opacity: 1; transform: translateY(0); }
}
.animate-fade-in { animation: fade-in 1s ease-out; }
</style>

<script>
let slideIndex = 0;
let slideTimer;

function showSlides() {
    const slides = document.querySelectorAll('.slide');
    const dots   = document.querySelectorAll('.dot');
    slides.forEach(s => s.classList.remove('active'));
    dots.forEach(d => d.classList.remove('active'));
    slideIndex++;
    if (slideIndex > slides.length) slideIndex = 1;
    slides[slideIndex - 1].classList.add('active');
    dots[slideIndex - 1].classList.add('active');
    slideTimer = setTimeout(showSlides, 5000);
}

function currentSlide(n) {
    clearTimeout(slideTimer);
    slideIndex = n - 1;
    showSlides();
}

document.addEventListener('DOMContentLoaded', showSlides);
</script>
@endsection
