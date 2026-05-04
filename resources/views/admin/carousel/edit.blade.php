@extends('layouts.admin')

@section('page-title', 'Edit Carousel Image')

@section('main-content')
<div class="bg-white rounded-lg shadow p-6">
    <h2 class="text-2xl font-bold text-gray-800 mb-6">Edit Carousel Image</h2>
    
    <form action="{{ route('admin.carousel.update', $carousel) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        
        <div class="space-y-6">
            <div>
                <label class="block text-gray-700 font-semibold mb-2">Current Image</label>
                <img src="{{ asset('storage/' . $carousel->image_path) }}" alt="{{ $carousel->title }}" 
                    class="max-w-full h-64 object-cover rounded-lg shadow mb-4">
            </div>

            <div>
                <label class="block text-gray-700 font-semibold mb-2">Replace Image (Optional)</label>
                <input type="file" name="image" accept="image/*" 
                    class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-600"
                    onchange="previewImage(event)">
                @error('image')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
                <p class="text-sm text-gray-500 mt-1">Leave empty to keep current image. Max: 5MB</p>
                
                <!-- Image Preview -->
                <div id="imagePreview" class="mt-4 hidden">
                    <p class="text-sm text-gray-600 mb-2">New Preview:</p>
                    <img id="preview" class="max-w-full h-64 object-cover rounded-lg shadow">
                </div>
            </div>

            <div>
                <label class="block text-gray-700 font-semibold mb-2">Title (Optional)</label>
                <input type="text" name="title" value="{{ old('title', $carousel->title) }}" 
                    class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-600"
                    placeholder="e.g., Grand Ballroom">
                @error('title')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-gray-700 font-semibold mb-2">Display Order</label>
                <input type="number" name="order" value="{{ old('order', $carousel->order) }}" min="0"
                    class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-600">
                @error('order')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
                <p class="text-sm text-gray-500 mt-1">Lower numbers appear first (0 = first)</p>
            </div>
        </div>

        <div class="mt-8 flex space-x-4">
            <button type="submit" class="bg-purple-600 text-white px-6 py-3 rounded-lg hover:bg-purple-700 transition">
                <i class="fas fa-save mr-2"></i>Update Image
            </button>
            <a href="{{ route('admin.carousel.index') }}" class="bg-gray-300 text-gray-700 px-6 py-3 rounded-lg hover:bg-gray-400 transition">
                Cancel
            </a>
        </div>
    </form>
</div>

<script>
function previewImage(event) {
    const file = event.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('preview').src = e.target.result;
            document.getElementById('imagePreview').classList.remove('hidden');
        }
        reader.readAsDataURL(file);
    }
}
</script>
@endsection
