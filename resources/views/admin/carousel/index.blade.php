@extends('layouts.admin')

@section('page-title', 'Carousel Images')

@section('main-content')
<div class="mb-6 flex justify-between items-center">
    <h2 class="text-2xl font-bold text-gray-800">Manage Carousel Images</h2>
    <a href="{{ route('admin.carousel.create') }}" class="bg-purple-600 text-white px-6 py-3 rounded-lg hover:bg-purple-700 transition">
        <i class="fas fa-plus mr-2"></i>Add New Image
    </a>
</div>

<div class="bg-white rounded-lg shadow overflow-hidden">
    @if($images->count() > 0)
    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6 p-6">
        @foreach($images as $image)
        <div class="bg-gray-50 rounded-lg overflow-hidden shadow-md">
            <div class="relative h-48">
                <img src="{{ asset('storage/' . $image->image_path) }}" alt="{{ $image->title }}" class="w-full h-full object-cover">
                <div class="absolute top-2 right-2 flex space-x-2">
                    @if($image->is_active)
                    <span class="bg-green-500 text-white px-3 py-1 rounded-full text-xs font-semibold">Active</span>
                    @else
                    <span class="bg-gray-500 text-white px-3 py-1 rounded-full text-xs font-semibold">Inactive</span>
                    @endif
                </div>
                <div class="absolute top-2 left-2">
                    <span class="bg-purple-600 text-white px-3 py-1 rounded-full text-xs font-semibold">Order: {{ $image->order }}</span>
                </div>
            </div>
            
            <div class="p-4">
                <h3 class="font-semibold text-gray-800 mb-2">{{ $image->title ?: 'Untitled' }}</h3>
                <p class="text-sm text-gray-500 mb-4">Added {{ $image->created_at->diffForHumans() }}</p>
                
                <div class="flex flex-wrap gap-2">
                    <!-- Edit Button -->
                    <a href="{{ route('admin.carousel.edit', $image) }}" 
                       class="inline-flex items-center px-3 py-2 bg-gradient-to-r from-blue-500 to-blue-600 text-white text-xs font-semibold rounded-lg hover:shadow-lg transition-all duration-200" 
                       title="Edit Image">
                        <i class="fas fa-edit mr-1"></i>Edit
                    </a>
                    
                    <!-- Toggle Active Button -->
                    <form action="{{ route('admin.carousel.toggle', $image) }}" method="POST" class="inline">
                        @csrf
                        @method('PATCH')
                        <button type="submit" 
                                class="inline-flex items-center px-3 py-2 {{ $image->is_active ? 'bg-gradient-to-r from-orange-500 to-orange-600' : 'bg-gradient-to-r from-green-500 to-green-600' }} text-white text-xs font-semibold rounded-lg hover:shadow-lg transition-all duration-200" 
                                title="{{ $image->is_active ? 'Hide from Homepage' : 'Show on Homepage' }}">
                            <i class="fas fa-{{ $image->is_active ? 'eye-slash' : 'eye' }} mr-1"></i>{{ $image->is_active ? 'Hide' : 'Show' }}
                        </button>
                    </form>
                    
                    <!-- Delete Button -->
                    <form action="{{ route('admin.carousel.destroy', $image) }}" method="POST" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                class="inline-flex items-center px-3 py-2 bg-gradient-to-r from-red-600 to-red-700 text-white text-xs font-semibold rounded-lg hover:shadow-lg transition-all duration-200" 
                                title="Delete Image"
                                onclick="return confirm('Permanently delete this carousel image? This action cannot be undone.')">
                            <i class="fas fa-trash-alt mr-1"></i>Delete
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @else
    <div class="text-center py-12">
        <i class="fas fa-images text-gray-300 text-6xl mb-4"></i>
        <p class="text-gray-600 text-xl mb-4">No carousel images yet</p>
        <a href="{{ route('admin.carousel.create') }}" class="inline-block bg-purple-600 text-white px-6 py-3 rounded-lg hover:bg-purple-700 transition">
            <i class="fas fa-plus mr-2"></i>Add Your First Image
        </a>
    </div>
    @endif
</div>

<div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
    <h3 class="font-semibold text-blue-800 mb-2"><i class="fas fa-info-circle mr-2"></i>Tips:</h3>
    <ul class="text-sm text-blue-700 space-y-1">
        <li>• Images will be displayed in order (lowest number first)</li>
        <li>• Only active images will appear on the homepage carousel</li>
        <li>• Recommended image size: 1920x1080 pixels for best quality</li>
        <li>• Maximum file size: 5MB</li>
    </ul>
</div>
@endsection
