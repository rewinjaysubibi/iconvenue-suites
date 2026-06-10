@extends('layouts.app')

@section('content')
<nav style="background: linear-gradient(135deg, #1a0e05 0%, #2d1a08 100%); border-bottom: 1px solid rgba(201,168,76,0.4); position: relative;">
    <!-- Top accent line -->
    <div style="position:absolute;top:0;left:0;right:0;height:2px;background:linear-gradient(90deg,#8b6914,#c9a84c,#f0d080,#c9a84c,#8b6914);"></div>
    <div class="container mx-auto px-4">
        <div class="flex justify-between items-center py-4">
            <a href="{{ route('home') }}" class="flex items-center space-x-3">
                <div style="background:linear-gradient(135deg,#c9a84c,#8b6914);padding:3px;border-radius:10px;">
                    <img src="{{ asset('images/logo.jpg') }}" alt="Icon Venue & Suites" class="h-10 w-10 rounded-lg object-cover">
                </div>
                <span class="text-2xl font-bold tracking-wider" style="color:#c9a84c;">Icon Venue & Suites</span>
            </a>
            <div class="space-x-6">
                <a href="{{ route('home') }}" class="text-sm font-semibold tracking-widest uppercase transition-colors" style="color:#c9a84c;" onmouseover="this.style.color='#f0d080'" onmouseout="this.style.color='#c9a84c'">Home</a>
                <a href="{{ route('contact') }}" class="text-sm font-semibold tracking-widest uppercase transition-colors" style="color:#c9a84c;" onmouseover="this.style.color='#f0d080'" onmouseout="this.style.color='#c9a84c'">Contact</a>
            </div>
        </div>
    </div>
</nav>

<main>
    @yield('main-content')
</main>

<!-- Toast Notifications Container -->
<div id="toast-container" class="fixed top-4 right-4 z-50 space-y-3" style="max-width: 400px;">
    @if(session('success'))
    <div class="toast-notification bg-white rounded-xl shadow-2xl overflow-hidden transform transition-all duration-500 ease-out"
         x-data="{ show: true }"
         x-show="show"
         x-init="setTimeout(() => show = false, 5000)"
         x-transition:enter="translate-x-full opacity-0"
         x-transition:enter-end="translate-x-0 opacity-100"
         x-transition:leave="translate-x-full opacity-0">
        <div class="flex items-start p-4">
            <div class="flex-shrink-0">
                <div class="w-12 h-12 bg-gradient-to-br from-green-400 to-green-600 rounded-full flex items-center justify-center shadow-lg">
                    <i class="fas fa-check text-white text-xl"></i>
                </div>
            </div>
            <div class="ml-4 flex-1">
                <p class="text-sm text-gray-800 font-medium">{{ session('success') }}</p>
            </div>
            <button @click="show = false" class="ml-4 text-gray-400 hover:text-gray-600 transition">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="h-1 bg-gradient-to-r from-green-400 to-green-600 toast-progress"></div>
    </div>
    @endif

    @if($errors->any())
    <div class="toast-notification bg-white rounded-xl shadow-2xl overflow-hidden transform transition-all duration-500 ease-out"
         x-data="{ show: true }"
         x-show="show"
         x-init="setTimeout(() => show = false, 5000)"
         x-transition:enter="translate-x-full opacity-0"
         x-transition:enter-end="translate-x-0 opacity-100"
         x-transition:leave="translate-x-full opacity-0">
        <div class="flex items-start p-4">
            <div class="flex-shrink-0">
                <div class="w-12 h-12 bg-gradient-to-br from-red-400 to-red-600 rounded-full flex items-center justify-center shadow-lg">
                    <i class="fas fa-exclamation-triangle text-white text-xl"></i>
                </div>
            </div>
            <div class="ml-4 flex-1">
                <div class="text-sm text-gray-800 space-y-1">
                    @foreach($errors->all() as $error)
                    <p class="font-medium">{{ $error }}</p>
                    @endforeach
                </div>
            </div>
            <button @click="show = false" class="ml-4 text-gray-400 hover:text-gray-600 transition">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="h-1 bg-gradient-to-r from-red-400 to-red-600 toast-progress"></div>
    </div>
    @endif
</div>

<style>
@keyframes toast-progress {
    from { width: 100%; }
    to { width: 0%; }
}

.toast-progress {
    animation: toast-progress 5s linear forwards;
}

.toast-notification {
    animation: slideInRight 0.5s ease-out;
}

@keyframes slideInRight {
    from {
        transform: translateX(100%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}
</style>

@endsection
