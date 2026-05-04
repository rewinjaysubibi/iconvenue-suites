@extends('layouts.public')

@section('main-content')
<div class="container mx-auto px-4 py-12">
    <!-- Header Section -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden mb-8">
        <div class="bg-gradient-to-r from-purple-600 to-pink-600 text-white p-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-4xl font-bold mb-2">{{ $venue->name }} - Add-ons</h1>
                    <p class="text-purple-100">
                        <i class="fas fa-{{ $venue->type == 'venue' ? 'building' : 'bed' }} mr-2"></i>
                        Enhance your {{ $venue->type }} booking with premium add-on services
                    </p>
                </div>
                <div class="text-right">
                    <p class="text-purple-100 text-sm">Base Price</p>
                    <p class="text-3xl font-bold">₱{{ number_format($venue->price_per_day, 2) }}</p>
                </div>
            </div>
        </div>
        
        <!-- Navigation -->
        <div class="p-6 bg-gray-50 border-b">
            <nav class="flex items-center space-x-2 text-sm">
                <a href="{{ route('venue.details', $venue->id) }}" class="text-purple-600 hover:text-purple-800 transition-colors">
                    <i class="fas fa-arrow-left mr-1"></i>
                    Back to {{ $venue->name }}
                </a>
                <span class="text-gray-400">/</span>
                <span class="text-gray-600">Add-ons & Services</span>
            </nav>
        </div>
    </div>

    <div class="grid lg:grid-cols-3 gap-8">
        <!-- Add-ons Selection -->
        <div class="lg:col-span-2">
            @if($addons->count() > 0)
            <div class="bg-white rounded-xl shadow-lg p-8">
                <h2 class="text-3xl font-bold mb-6 text-gray-800">
                    <i class="fas fa-plus-circle text-purple-600 mr-3"></i>
                    Available Add-ons
                </h2>
                <p class="text-gray-600 mb-8">Select a category to view available add-on services. You can adjust quantities and see the total cost update in real-time.</p>
                
                <!-- Category Selector -->
                <div class="mb-8">
                    <label class="block text-lg font-semibold text-gray-800 mb-4">
                        <i class="fas fa-filter text-purple-600 mr-2"></i>
                        Select Category:
                    </label>
                    <select id="category-selector" class="w-full md:w-1/2 px-4 py-3 border-2 border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-600 focus:border-purple-600 text-lg font-medium">
                        <option value="">Choose a category...</option>
                        @foreach($addons as $category => $categoryAddons)
                        <option value="{{ $category }}">
                            {{ ucfirst($category) }} ({{ count($categoryAddons) }} items)
                        </option>
                        @endforeach
                    </select>
                </div>
                
                <div id="addons-container">
                    <!-- Default message when no category is selected -->
                    <div id="no-category-message" class="text-center py-12">
                        <i class="fas fa-arrow-up text-gray-300 text-6xl mb-4"></i>
                        <h3 class="text-2xl font-semibold text-gray-600 mb-2">Select a Category</h3>
                        <p class="text-gray-500">Choose a category from the dropdown above to view available add-ons.</p>
                    </div>

                    @foreach($addons as $category => $categoryAddons)
                    <div class="category-section hidden" data-category="{{ $category }}">
                        <div class="mb-6">
                            <h3 class="text-2xl font-semibold text-gray-800 capitalize border-b-2 border-purple-200 pb-3 mb-6">
                                <i class="fas fa-{{ $category == 'catering' ? 'utensils' : ($category == 'decoration' ? 'palette' : ($category == 'equipment' ? 'tools' : 'concierge-bell')) }} text-purple-600 mr-3"></i>
                                {{ ucfirst($category) }}
                                <span class="text-sm text-gray-500 font-normal ml-2">({{ count($categoryAddons) }} items available)</span>
                            </h3>
                            <div class="grid md:grid-cols-2 gap-6">
                                @foreach($categoryAddons as $addon)
                                <div class="bg-gray-50 border border-gray-200 rounded-xl p-6 hover:shadow-lg transition-all duration-300 addon-item {{ $addon->isOutOfStock() ? 'opacity-60' : '' }}" data-addon-id="{{ $addon->id }}">
                                    <div class="flex items-start justify-between mb-4">
                                        <div class="flex-1">
                                            <h4 class="text-xl font-semibold text-gray-800 mb-2">{{ $addon->name }}</h4>
                                            <p class="text-gray-600 mb-3 leading-relaxed">{{ $addon->description }}</p>
                                            <p class="text-2xl font-bold text-purple-600">₱{{ number_format($addon->price, 2) }}</p>
                                            
                                            <!-- Stock Information -->
                                            @if($addon->track_stock)
                                                <div class="mt-2">
                                                    @if($addon->isOutOfStock())
                                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                            <i class="fas fa-times-circle mr-1"></i>
                                                            Out of Stock
                                                        </span>
                                                    @elseif($addon->isLowStock())
                                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                            <i class="fas fa-exclamation-triangle mr-1"></i>
                                                            Only {{ $addon->stock_quantity }} left
                                                        </span>
                                                    @else
                                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                            <i class="fas fa-check-circle mr-1"></i>
                                                            {{ $addon->stock_quantity }} available
                                                        </span>
                                                    @endif
                                                </div>
                                            @endif
                                            
                                            @if(!$addon->isOutOfStock())
                                                <p class="text-xs text-gray-500 mt-2">
                                                    <i class="fas fa-hand-pointer mr-1"></i>
                                                    Click to select this add-on
                                                </p>
                                            @endif
                                        </div>
                                        <div class="ml-4">
                                            @if(!$addon->isOutOfStock())
                                                <label class="flex items-center cursor-pointer">
                                                    <input type="checkbox" 
                                                           class="addon-checkbox sr-only" 
                                                           data-addon-id="{{ $addon->id }}" 
                                                           data-addon-price="{{ $addon->price }}"
                                                           data-addon-name="{{ $addon->name }}"
                                                           data-track-stock="{{ $addon->track_stock ? 'true' : 'false' }}"
                                                           data-stock-quantity="{{ $addon->stock_quantity ?? 999 }}">
                                                    <div class="addon-checkbox-custom w-8 h-8 border-3 border-gray-300 rounded-lg flex items-center justify-center transition-all duration-200">
                                                        <i class="fas fa-check text-white text-lg opacity-0 transition-opacity duration-200"></i>
                                                    </div>
                                                </label>
                                            @else
                                                <div class="w-8 h-8 border-3 border-gray-300 rounded-lg flex items-center justify-center bg-gray-200">
                                                    <i class="fas fa-ban text-gray-400"></i>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    
                                    <!-- Quantity selector (hidden by default) -->
                                    @if(!$addon->isOutOfStock())
                                        <div class="addon-quantity hidden pt-4 border-t border-gray-300">
                                            <label class="block text-sm font-semibold text-gray-700 mb-3">Quantity:</label>
                                            <div class="flex items-center space-x-4">
                                                <button type="button" class="quantity-btn quantity-minus w-10 h-10 bg-gray-200 hover:bg-gray-300 rounded-full flex items-center justify-center transition-all duration-200 hover:scale-105">
                                                    <i class="fas fa-minus"></i>
                                                </button>
                                                <input type="number" 
                                                       class="addon-quantity-input w-20 text-center border-2 border-gray-300 rounded-lg px-3 py-2 font-semibold" 
                                                       value="1" 
                                                       min="1" 
                                                       max="{{ $addon->track_stock ? $addon->stock_quantity : 99 }}">
                                                <button type="button" class="quantity-btn quantity-plus w-10 h-10 bg-gray-200 hover:bg-gray-300 rounded-full flex items-center justify-center transition-all duration-200 hover:scale-105">
                                                    <i class="fas fa-plus"></i>
                                                </button>
                                            </div>
                                            @if($addon->track_stock && $addon->stock_quantity <= 5)
                                                <p class="text-xs text-orange-600 mt-2">
                                                    <i class="fas fa-info-circle mr-1"></i>
                                                    Limited stock: Maximum {{ $addon->stock_quantity }} available
                                                </p>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @else
            <div class="bg-white rounded-xl shadow-lg p-12 text-center">
                <i class="fas fa-plus-circle text-gray-300 text-6xl mb-4"></i>
                <h3 class="text-2xl font-semibold text-gray-600 mb-2">No Add-ons Available</h3>
                <p class="text-gray-500">Add-on services are not currently available for this venue.</p>
            </div>
            @endif
        </div>

        <!-- Pricing Summary Sidebar -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-xl shadow-lg overflow-hidden sticky top-8">
                <div class="bg-gradient-to-r from-purple-600 to-pink-600 text-white p-6">
                    <h3 class="text-2xl font-bold">
                        <i class="fas fa-calculator mr-2"></i>
                        Pricing Summary
                    </h3>
                </div>
                
                <div class="p-6">
                    <div class="space-y-4">
                        <div class="flex justify-between items-center pb-3 border-b border-gray-200">
                            <span class="text-gray-700 font-medium">Base Venue Price:</span>
                            <span class="font-bold text-gray-800" id="base-price">₱{{ number_format($venue->price_per_day, 2) }}</span>
                        </div>
                        
                        <div id="package-price-row" class="flex justify-between items-center pb-3 border-b border-gray-200 hidden">
                            <span class="text-gray-700 font-medium">Package Price:</span>
                            <span class="font-bold text-gray-800" id="package-price">₱0.00</span>
                        </div>
                        
                        <div id="addons-summary" class="hidden">
                            <div class="mb-4">
                                <h4 class="font-semibold text-gray-800 mb-3">Selected Add-ons:</h4>
                                <div id="selected-addons-list" class="space-y-2 text-sm"></div>
                            </div>
                            <div class="flex justify-between items-center pb-3 border-b border-gray-200">
                                <span class="text-gray-700 font-medium">Add-ons Subtotal:</span>
                                <span class="font-bold text-gray-800" id="addons-total">₱0.00</span>
                            </div>
                        </div>
                        
                        <div class="pt-4 border-t-2 border-purple-200">
                            <div class="flex justify-between items-center">
                                <span class="text-xl font-bold text-gray-800">Total Amount:</span>
                                <span class="text-3xl font-bold text-purple-600" id="total-amount">₱{{ number_format($venue->price_per_day, 2) }}</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Action Buttons -->
                    <div class="mt-8 space-y-3">
                        <button id="save-selection" class="w-full bg-gradient-to-r from-purple-600 to-pink-600 text-white py-3 px-6 rounded-lg font-semibold hover:from-purple-700 hover:to-pink-700 transition-all duration-200 transform hover:scale-105 shadow-lg">
                            <i class="fas fa-save mr-2"></i>
                            Save Selection
                        </button>
                        <a href="{{ route('venue.details', $venue->id) }}" class="block w-full bg-gray-100 text-gray-700 py-3 px-6 rounded-lg font-semibold hover:bg-gray-200 transition-all duration-200 text-center">
                            <i class="fas fa-arrow-left mr-2"></i>
                            Back to Venue
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // Add-ons functionality
    let selectedAddons = {};
    const basePrice = {{ $venue->price_per_day }};
    let packagePrice = 0;

    // Handle category selection
    $('#category-selector').on('change', function() {
        const selectedCategory = $(this).val();
        
        // Hide all category sections and the no-category message
        $('.category-section').addClass('hidden');
        $('#no-category-message').addClass('hidden');
        
        if (selectedCategory) {
            // Show selected category section
            $(`.category-section[data-category="${selectedCategory}"]`).removeClass('hidden');
        } else {
            // Show no-category message if nothing is selected
            $('#no-category-message').removeClass('hidden');
        }
    });

    // Handle addon item clicks (make entire card clickable)
    $(document).on('click', '.addon-item', function(e) {
        // Don't trigger if clicking on quantity controls or if out of stock
        if ($(e.target).closest('.addon-quantity, .quantity-btn, .addon-quantity-input').length > 0 || $(this).hasClass('opacity-60')) {
            return;
        }
        
        const checkbox = $(this).find('.addon-checkbox');
        if (checkbox.length > 0) {
            checkbox.prop('checked', !checkbox.prop('checked')).trigger('change');
        }
    });

    // Handle addon checkbox changes
    $(document).on('change', '.addon-checkbox', function(e) {
        e.stopPropagation(); // Prevent event bubbling
        
        const addonId = $(this).data('addon-id');
        const addonPrice = parseFloat($(this).data('addon-price'));
        const addonName = $(this).data('addon-name');
        const trackStock = $(this).data('track-stock') === 'true';
        const stockQuantity = parseInt($(this).data('stock-quantity'));
        const addonItem = $(this).closest('.addon-item');
        const quantitySection = addonItem.find('.addon-quantity');
        const checkboxCustom = $(this).siblings('.addon-checkbox-custom');
        const quantityInput = addonItem.find('.addon-quantity-input');
        
        if ($(this).is(':checked')) {
            // Add addon to selection
            selectedAddons[addonId] = {
                name: addonName,
                price: addonPrice,
                quantity: 1,
                trackStock: trackStock,
                maxQuantity: trackStock ? stockQuantity : 99
            };
            
            // Update UI
            checkboxCustom.addClass('bg-purple-600 border-purple-600');
            checkboxCustom.find('i').removeClass('opacity-0').addClass('opacity-100');
            quantitySection.removeClass('hidden');
            addonItem.addClass('ring-2 ring-purple-300 bg-purple-50 transform scale-105');
            
            // Set max quantity for input
            quantityInput.attr('max', trackStock ? stockQuantity : 99);
            
            // If stock is 1, disable quantity controls
            if (trackStock && stockQuantity === 1) {
                quantityInput.val(1).prop('readonly', true);
                addonItem.find('.quantity-btn').prop('disabled', true).addClass('opacity-50 cursor-not-allowed');
            }
        } else {
            // Remove addon from selection
            delete selectedAddons[addonId];
            
            // Update UI
            checkboxCustom.removeClass('bg-purple-600 border-purple-600');
            checkboxCustom.find('i').removeClass('opacity-100').addClass('opacity-0');
            quantitySection.addClass('hidden');
            addonItem.removeClass('ring-2 ring-purple-300 bg-purple-50 transform scale-105');
            
            // Reset quantity controls
            quantityInput.prop('readonly', false);
            addonItem.find('.quantity-btn').prop('disabled', false).removeClass('opacity-50 cursor-not-allowed');
        }
        
        updatePricingSummary();
    });

    // Handle custom checkbox clicks
    $(document).on('click', '.addon-checkbox-custom', function(e) {
        e.stopPropagation();
        const checkbox = $(this).siblings('.addon-checkbox');
        checkbox.prop('checked', !checkbox.prop('checked')).trigger('change');
    });

    // Handle quantity changes
    $(document).on('click', '.quantity-plus', function(e) {
        e.stopPropagation();
        
        // Don't allow if button is disabled
        if ($(this).prop('disabled')) {
            return;
        }
        
        const input = $(this).siblings('.addon-quantity-input');
        const currentValue = parseInt(input.val());
        const maxValue = parseInt(input.attr('max'));
        const newValue = Math.min(currentValue + 1, maxValue);
        
        // Don't increase if already at max
        if (currentValue >= maxValue) {
            return;
        }
        
        input.val(newValue);
        
        const addonId = $(this).closest('.addon-item').find('.addon-checkbox').data('addon-id');
        if (selectedAddons[addonId]) {
            selectedAddons[addonId].quantity = newValue;
            updatePricingSummary();
        }
        
        // Disable plus button if at max
        if (newValue >= maxValue) {
            $(this).addClass('opacity-50 cursor-not-allowed').prop('disabled', true);
        }
        
        // Enable minus button
        $(this).siblings('.quantity-minus').removeClass('opacity-50 cursor-not-allowed').prop('disabled', false);
    });

    $(document).on('click', '.quantity-minus', function(e) {
        e.stopPropagation();
        
        // Don't allow if button is disabled
        if ($(this).prop('disabled')) {
            return;
        }
        
        const input = $(this).siblings('.addon-quantity-input');
        const currentValue = parseInt(input.val());
        const newValue = Math.max(currentValue - 1, 1);
        
        // Don't decrease below 1
        if (currentValue <= 1) {
            return;
        }
        
        input.val(newValue);
        
        const addonId = $(this).closest('.addon-item').find('.addon-checkbox').data('addon-id');
        if (selectedAddons[addonId]) {
            selectedAddons[addonId].quantity = newValue;
            updatePricingSummary();
        }
        
        // Disable minus button if at minimum
        if (newValue <= 1) {
            $(this).addClass('opacity-50 cursor-not-allowed').prop('disabled', true);
        }
        
        // Enable plus button
        const maxValue = parseInt(input.attr('max'));
        if (newValue < maxValue) {
            $(this).siblings('.quantity-plus').removeClass('opacity-50 cursor-not-allowed').prop('disabled', false);
        }
    });

    $(document).on('change', '.addon-quantity-input', function(e) {
        e.stopPropagation();
        
        // Don't allow if readonly
        if ($(this).prop('readonly')) {
            return;
        }
        
        const minValue = parseInt($(this).attr('min')) || 1;
        const maxValue = parseInt($(this).attr('max')) || 99;
        const newValue = Math.max(Math.min(parseInt($(this).val()) || 1, maxValue), minValue);
        $(this).val(newValue);
        
        const addonId = $(this).closest('.addon-item').find('.addon-checkbox').data('addon-id');
        if (selectedAddons[addonId]) {
            selectedAddons[addonId].quantity = newValue;
            updatePricingSummary();
        }
        
        // Update button states
        const plusBtn = $(this).siblings('.quantity-plus');
        const minusBtn = $(this).siblings('.quantity-minus');
        
        if (newValue >= maxValue) {
            plusBtn.addClass('opacity-50 cursor-not-allowed').prop('disabled', true);
        } else {
            plusBtn.removeClass('opacity-50 cursor-not-allowed').prop('disabled', false);
        }
        
        if (newValue <= minValue) {
            minusBtn.addClass('opacity-50 cursor-not-allowed').prop('disabled', true);
        } else {
            minusBtn.removeClass('opacity-50 cursor-not-allowed').prop('disabled', false);
        }
    });

    // Update pricing summary
    function updatePricingSummary() {
        let addonsTotal = 0;
        let addonsList = '';
        
        Object.values(selectedAddons).forEach(addon => {
            const subtotal = addon.price * addon.quantity;
            addonsTotal += subtotal;
            addonsList += `
                <div class="flex justify-between text-gray-600 bg-gray-50 p-2 rounded">
                    <span class="font-medium">${addon.name} (${addon.quantity}x)</span>
                    <span class="font-semibold">₱${subtotal.toLocaleString('en-PH', {minimumFractionDigits: 2})}</span>
                </div>
            `;
        });
        
        const totalAmount = basePrice + packagePrice + addonsTotal;
        
        // Update display
        $('#addons-total').text('₱' + addonsTotal.toLocaleString('en-PH', {minimumFractionDigits: 2}));
        $('#total-amount').text('₱' + totalAmount.toLocaleString('en-PH', {minimumFractionDigits: 2}));
        
        if (Object.keys(selectedAddons).length > 0) {
            $('#addons-summary').removeClass('hidden');
            $('#selected-addons-list').html(addonsList);
        } else {
            $('#addons-summary').addClass('hidden');
        }
    }

    // Save selection (placeholder for future booking integration)
    $('#save-selection').on('click', function() {
        if (Object.keys(selectedAddons).length === 0) {
            alert('Please select at least one add-on to save your selection.');
            return;
        }
        
        // Store selection in localStorage for now
        localStorage.setItem('venue_addons_' + {{ $venue->id }}, JSON.stringify(selectedAddons));
        
        // Show success message
        $(this).html('<i class="fas fa-check mr-2"></i>Selection Saved!').addClass('bg-green-600').removeClass('from-purple-600 to-pink-600');
        
        setTimeout(() => {
            $(this).html('<i class="fas fa-save mr-2"></i>Save Selection').removeClass('bg-green-600').addClass('from-purple-600 to-pink-600');
        }, 2000);
    });

    // Load saved selection if exists
    const savedSelection = localStorage.getItem('venue_addons_' + {{ $venue->id }});
    if (savedSelection) {
        selectedAddons = JSON.parse(savedSelection);
        
        // Restore UI state for all selected addons (across all categories)
        Object.keys(selectedAddons).forEach(addonId => {
            const checkbox = $(`.addon-checkbox[data-addon-id="${addonId}"]`);
            if (checkbox.length > 0) {
                const addonItem = checkbox.closest('.addon-item');
                const quantityInput = addonItem.find('.addon-quantity-input');
                const checkboxCustom = checkbox.siblings('.addon-checkbox-custom');
                const trackStock = checkbox.data('track-stock') === 'true';
                const stockQuantity = parseInt(checkbox.data('stock-quantity'));
                
                // Check if addon is still available and not out of stock
                if (!addonItem.hasClass('opacity-60')) {
                    checkbox.prop('checked', true);
                    checkboxCustom.addClass('bg-purple-600 border-purple-600');
                    checkboxCustom.find('i').removeClass('opacity-0').addClass('opacity-100');
                    addonItem.find('.addon-quantity').removeClass('hidden');
                    addonItem.addClass('ring-2 ring-purple-300 bg-purple-50 transform scale-105');
                    
                    // Validate saved quantity against current stock
                    let savedQuantity = selectedAddons[addonId].quantity;
                    if (trackStock && savedQuantity > stockQuantity) {
                        savedQuantity = stockQuantity;
                        selectedAddons[addonId].quantity = savedQuantity;
                    }
                    
                    quantityInput.val(savedQuantity);
                    quantityInput.attr('max', trackStock ? stockQuantity : 99);
                    
                    // Update addon data with current stock info
                    selectedAddons[addonId].trackStock = trackStock;
                    selectedAddons[addonId].maxQuantity = trackStock ? stockQuantity : 99;
                    
                    // Handle stock-limited items
                    if (trackStock && stockQuantity === 1) {
                        quantityInput.prop('readonly', true);
                        addonItem.find('.quantity-btn').prop('disabled', true).addClass('opacity-50 cursor-not-allowed');
                    } else {
                        // Update button states based on current quantity
                        const plusBtn = addonItem.find('.quantity-plus');
                        const minusBtn = addonItem.find('.quantity-minus');
                        
                        if (savedQuantity >= (trackStock ? stockQuantity : 99)) {
                            plusBtn.addClass('opacity-50 cursor-not-allowed').prop('disabled', true);
                        }
                        
                        if (savedQuantity <= 1) {
                            minusBtn.addClass('opacity-50 cursor-not-allowed').prop('disabled', true);
                        }
                    }
                } else {
                    // Remove out of stock items from saved selection
                    delete selectedAddons[addonId];
                }
            } else {
                // Remove items that no longer exist
                delete selectedAddons[addonId];
            }
        });
        
        updatePricingSummary();
    }
});
</script>
@endpush

<style>
.addon-checkbox-custom {
    transition: all 0.3s ease;
}

.addon-checkbox:checked + .addon-checkbox-custom {
    background-color: #8B5CF6;
    border-color: #8B5CF6;
}

.addon-item {
    transition: all 0.3s ease;
    cursor: pointer;
}

.addon-item:hover:not(.opacity-60) {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
    border-color: #8B5CF6;
}

.addon-item:hover:not(.opacity-60) .addon-checkbox-custom {
    border-color: #8B5CF6;
    transform: scale(1.05);
}

.addon-item.opacity-60 {
    cursor: not-allowed;
}

.quantity-btn {
    transition: all 0.2s ease;
    cursor: pointer;
}

.quantity-btn:hover:not(:disabled) {
    transform: scale(1.1);
    background-color: #8B5CF6;
    color: white;
}

.quantity-btn:disabled {
    cursor: not-allowed;
}

.addon-quantity-input:focus {
    outline: none;
    border-color: #8B5CF6;
    box-shadow: 0 0 0 3px rgba(139, 92, 246, 0.1);
}

.addon-quantity-input:read-only {
    background-color: #f3f4f6;
    cursor: not-allowed;
}

.addon-checkbox-custom {
    cursor: pointer;
}

.addon-checkbox-custom:hover {
    border-color: #8B5CF6;
    transform: scale(1.1);
}

/* Add visual feedback for clickable areas */
.addon-item:active:not(.opacity-60) {
    transform: translateY(0) scale(0.98);
}

/* Improve quantity controls visibility */
.addon-quantity {
    background-color: rgba(139, 92, 246, 0.05);
    border-radius: 8px;
    padding: 12px;
}

/* Stock status badges */
.addon-item .bg-red-100 {
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0%, 100% {
        opacity: 1;
    }
    50% {
        opacity: 0.7;
    }
}
</style>
@endsection