@extends('layouts.admin')

@section('page-title', 'Edit Add-on')

@section('main-content')
<div class="mb-6">
    <nav class="flex items-center space-x-2 text-sm text-gray-600">
        <a href="{{ route('admin.addons.index') }}" class="hover:text-purple-600 transition-colors">Add-ons</a>
        <i class="fas fa-chevron-right text-gray-400"></i>
        <span>Edit {{ $addon->name }}</span>
    </nav>
</div>

<div class="bg-white rounded-lg shadow-md">
    <div class="p-6 border-b border-gray-200">
        <h2 class="text-2xl font-bold text-gray-800">Edit Add-on</h2>
        <p class="text-gray-600 mt-1">Update the details of {{ $addon->name }}</p>
    </div>
    
    <form action="{{ route('admin.addons.update', $addon) }}" method="POST" class="p-6">
        @csrf
        @method('PUT')
        
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Basic Information -->
            <div class="space-y-6">
                <h3 class="text-lg font-semibold text-gray-800 border-b border-gray-200 pb-2">Basic Information</h3>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Category *</label>
                    <div class="flex space-x-2">
                        <select name="category" required
                                class="flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-600 @error('category') border-red-500 @enderror">
                            <option value="">Select Category</option>
                            @foreach(['catering', 'decoration', 'equipment', 'service'] as $cat)
                            <option value="{{ $cat }}" {{ old('category', $addon->category) == $cat ? 'selected' : '' }}>
                                {{ ucfirst($cat) }}
                            </option>
                            @endforeach
                            @foreach($categories as $category)
                            @if(!in_array($category, ['catering', 'decoration', 'equipment', 'service']))
                            <option value="{{ $category }}" {{ old('category', $addon->category) == $category ? 'selected' : '' }}>
                                {{ ucfirst($category) }}
                            </option>
                            @endif
                            @endforeach
                        </select>
                        <input type="text" id="new-category" placeholder="Or type new category"
                               class="flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-600">
                    </div>
                    @error('category')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Name *</label>
                    <input type="text" name="name" value="{{ old('name', $addon->name) }}" required
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-600 @error('name') border-red-500 @enderror"
                           placeholder="e.g., Basic Catering Package">
                    @error('name')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                    <textarea name="description" rows="4"
                              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-600 @error('description') border-red-500 @enderror"
                              placeholder="Detailed description of the add-on service...">{{ old('description', $addon->description) }}</textarea>
                    @error('description')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Price *</label>
                        <div class="relative">
                            <span class="absolute left-3 top-3 text-gray-500">₱</span>
                            <input type="number" name="price" value="{{ old('price', $addon->price) }}" step="0.01" min="0" required
                                   class="w-full pl-8 pr-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-600 @error('price') border-red-500 @enderror"
                                   placeholder="0.00">
                        </div>
                        @error('price')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Sort Order</label>
                        <input type="number" name="sort_order" value="{{ old('sort_order', $addon->sort_order) }}" min="0"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-600 @error('sort_order') border-red-500 @enderror"
                               placeholder="0">
                        @error('sort_order')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
            
            <!-- Stock Management -->
            <div class="space-y-6">
                <h3 class="text-lg font-semibold text-gray-800 border-b border-gray-200 pb-2">Stock Management</h3>
                
                <div>
                    <label class="flex items-center">
                        <input type="checkbox" name="track_stock" value="1" {{ old('track_stock', $addon->track_stock) ? 'checked' : '' }}
                               class="rounded border-gray-300 text-purple-600 focus:ring-purple-500" id="track-stock">
                        <span class="ml-2 text-sm font-medium text-gray-700">Track Stock Quantity</span>
                    </label>
                    <p class="text-xs text-gray-500 mt-1">Enable this if the add-on has limited availability</p>
                </div>
                
                <div id="stock-fields" class="space-y-4" style="display: {{ old('track_stock', $addon->track_stock) ? 'block' : 'none' }};">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Stock Quantity</label>
                        <input type="number" name="stock_quantity" value="{{ old('stock_quantity', $addon->stock_quantity) }}" min="0"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-600 @error('stock_quantity') border-red-500 @enderror"
                               placeholder="Available quantity">
                        @error('stock_quantity')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Low Stock Threshold</label>
                        <input type="number" name="low_stock_threshold" value="{{ old('low_stock_threshold', $addon->low_stock_threshold) }}" min="0"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-600 @error('low_stock_threshold') border-red-500 @enderror"
                               placeholder="Alert when stock reaches this level">
                        @error('low_stock_threshold')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                    <textarea name="notes" rows="3"
                              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-600 @error('notes') border-red-500 @enderror"
                              placeholder="Internal notes about this add-on...">{{ old('notes', $addon->notes) }}</textarea>
                    @error('notes')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label class="flex items-center">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $addon->is_active) ? 'checked' : '' }}
                               class="rounded border-gray-300 text-purple-600 focus:ring-purple-500">
                        <span class="ml-2 text-sm font-medium text-gray-700">Active</span>
                    </label>
                    <p class="text-xs text-gray-500 mt-1">Only active add-ons will be available for booking</p>
                </div>
            </div>
        </div>
        
        <!-- Action Buttons -->
        <div class="flex items-center justify-end space-x-4 mt-8 pt-6 border-t border-gray-200">
            <a href="{{ route('admin.addons.index') }}" 
               class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                Cancel
            </a>
            <button type="submit" 
                    class="px-6 py-3 bg-gradient-to-r from-purple-600 to-pink-600 text-white rounded-lg hover:from-purple-700 hover:to-pink-700 transition-all duration-200 shadow-lg">
                <i class="fas fa-save mr-2"></i>Update Add-on
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // Toggle stock fields
    $('#track-stock').on('change', function() {
        if ($(this).is(':checked')) {
            $('#stock-fields').slideDown();
        } else {
            $('#stock-fields').slideUp();
        }
    });
    
    // New category input
    $('#new-category').on('input', function() {
        const newCategory = $(this).val().trim();
        if (newCategory) {
            $('select[name="category"]').val('');
            // Create a temporary option
            if ($('select[name="category"] option[value="' + newCategory + '"]').length === 0) {
                $('select[name="category"]').append('<option value="' + newCategory + '" selected>' + newCategory.charAt(0).toUpperCase() + newCategory.slice(1) + '</option>');
            }
            $('select[name="category"]').val(newCategory);
        }
    });
    
    $('select[name="category"]').on('change', function() {
        if ($(this).val()) {
            $('#new-category').val('');
        }
    });
});
</script>
@endpush
@endsection