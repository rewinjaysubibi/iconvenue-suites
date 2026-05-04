@extends('layouts.admin')

@section('page-title', 'Add-ons Management')

@section('main-content')
<div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Add-ons Management</h1>
        <p class="text-gray-600">Manage venue add-ons, categories, pricing, and stock levels</p>
    </div>
    <a href="{{ route('admin.addons.create') }}" class="bg-gradient-to-r from-purple-600 to-pink-600 text-white px-6 py-3 rounded-lg hover:from-purple-700 hover:to-pink-700 transition-all duration-200 shadow-lg">
        <i class="fas fa-plus mr-2"></i>Add New Add-on
    </a>
</div>

<!-- Filters -->
<div class="bg-white rounded-lg shadow-md p-6 mb-6">
    <form method="GET" id="filter-form" class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
            <input type="text" name="search" value="{{ request('search') }}" 
                   placeholder="Search add-ons..." 
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-600">
        </div>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Category</label>
            <select name="category" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-600 auto-filter">
                <option value="">All Categories</option>
                @foreach($categories as $category)
                <option value="{{ $category }}" {{ request('category') == $category ? 'selected' : '' }}>
                    {{ ucfirst($category) }}
                </option>
                @endforeach
            </select>
        </div>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
            <select name="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-600 auto-filter">
                <option value="">All Status</option>
                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                <option value="low_stock" {{ request('status') == 'low_stock' ? 'selected' : '' }}>Low Stock</option>
                <option value="out_of_stock" {{ request('status') == 'out_of_stock' ? 'selected' : '' }}>Out of Stock</option>
            </select>
        </div>
    </form>
</div>

<!-- Active Filters Indicator -->
@if(request('search') || request('category') || request('status'))
<div class="bg-purple-50 border border-purple-200 rounded-lg p-4 mb-6">
    <div class="flex items-center justify-between">
        <div class="flex items-center space-x-4">
            <div class="flex items-center text-purple-700">
                <i class="fas fa-filter mr-2"></i>
                <span class="font-medium">Active Filters:</span>
            </div>
            <div class="flex items-center space-x-2">
                @if(request('search'))
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                    Search: "{{ request('search') }}"
                    <a href="{{ request()->fullUrlWithQuery(['search' => null]) }}" class="ml-2 text-purple-600 hover:text-purple-800">
                        <i class="fas fa-times"></i>
                    </a>
                </span>
                @endif
                @if(request('category'))
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                    Category: {{ ucfirst(request('category')) }}
                    <a href="{{ request()->fullUrlWithQuery(['category' => null]) }}" class="ml-2 text-purple-600 hover:text-purple-800">
                        <i class="fas fa-times"></i>
                    </a>
                </span>
                @endif
                @if(request('status'))
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                    Status: {{ ucfirst(str_replace('_', ' ', request('status'))) }}
                    <a href="{{ request()->fullUrlWithQuery(['status' => null]) }}" class="ml-2 text-purple-600 hover:text-purple-800">
                        <i class="fas fa-times"></i>
                    </a>
                </span>
                @endif
            </div>
        </div>
        <a href="{{ route('admin.addons.index') }}" class="text-purple-600 hover:text-purple-800 text-sm font-medium">
            <i class="fas fa-times mr-1"></i>Clear All Filters
        </a>
    </div>
</div>
@endif

<!-- Bulk Actions -->
@if($addons->count() > 0)
<form id="bulk-form" method="POST" action="{{ route('admin.addons.bulk-action') }}">
    @csrf
    <div class="bg-white rounded-lg shadow-md mb-6">
        <div class="p-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <label class="flex items-center">
                        <input type="checkbox" id="select-all" class="rounded border-gray-300 text-purple-600 focus:ring-purple-500">
                        <span class="ml-2 text-sm font-medium text-gray-700">Select All</span>
                    </label>
                    <span id="selected-count" class="text-sm text-gray-500">0 selected</span>
                </div>
                
                <div class="flex items-center space-x-2" id="bulk-actions" style="display: none;">
                    <select name="action" class="px-3 py-2 border border-gray-300 rounded-lg text-sm">
                        <option value="">Choose Action</option>
                        <option value="activate">Activate</option>
                        <option value="deactivate">Deactivate</option>
                        <option value="delete">Delete</option>
                    </select>
                    <button type="submit" class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition-colors text-sm">
                        Apply
                    </button>
                </div>
            </div>
        </div>

        <!-- Add-ons Table -->
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <input type="checkbox" class="rounded border-gray-300 text-purple-600 focus:ring-purple-500">
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Add-on</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stock</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($addons as $addon)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <input type="checkbox" name="selected_addons[]" value="{{ $addon->id }}" 
                                   class="addon-checkbox rounded border-gray-300 text-purple-600 focus:ring-purple-500">
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    <div class="h-10 w-10 rounded-full bg-gradient-to-br from-purple-500 to-pink-500 flex items-center justify-center">
                                        <i class="fas fa-{{ $addon->category == 'catering' ? 'utensils' : ($addon->category == 'decoration' ? 'palette' : ($addon->category == 'equipment' ? 'tools' : 'concierge-bell')) }} text-white"></i>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $addon->name }}</div>
                                    <div class="text-sm text-gray-500">{{ Str::limit($addon->description, 50) }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                {{ ucfirst($addon->category) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            ₱{{ number_format($addon->price, 2) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($addon->track_stock)
                                @if($addon->stock_status == 'out_of_stock')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        <i class="fas fa-times-circle mr-1"></i>Out of Stock
                                    </span>
                                @elseif($addon->stock_status == 'low_stock')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                        <i class="fas fa-exclamation-triangle mr-1"></i>Low Stock ({{ $addon->stock_quantity }})
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <i class="fas fa-check-circle mr-1"></i>In Stock ({{ $addon->stock_quantity }})
                                    </span>
                                @endif
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    <i class="fas fa-infinity mr-1"></i>Unlimited
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $addon->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                <i class="fas fa-{{ $addon->is_active ? 'check' : 'times' }} mr-1"></i>
                                {{ $addon->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex items-center space-x-2">
                                <a href="{{ route('admin.addons.edit', $addon) }}" 
                                   class="text-blue-600 hover:text-blue-900 transition-colors" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                
                                <form action="{{ route('admin.addons.toggle', $addon) }}" method="POST" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" 
                                            class="text-{{ $addon->is_active ? 'red' : 'green' }}-600 hover:text-{{ $addon->is_active ? 'red' : 'green' }}-900 transition-colors" 
                                            title="{{ $addon->is_active ? 'Deactivate' : 'Activate' }}">
                                        <i class="fas fa-{{ $addon->is_active ? 'times' : 'check' }}"></i>
                                    </button>
                                </form>
                                
                                <form action="{{ route('admin.addons.destroy', $addon) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="text-red-600 hover:text-red-900 transition-colors" 
                                            title="Delete"
                                            onclick="return confirm('Are you sure you want to delete this add-on?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</form>

<!-- Pagination -->
<div class="mt-6">
    {{ $addons->appends(request()->query())->links() }}
</div>

@else
<div class="bg-white rounded-lg shadow-md p-12 text-center">
    <i class="fas fa-plus-circle text-gray-300 text-6xl mb-4"></i>
    <h3 class="text-xl font-semibold text-gray-600 mb-2">No Add-ons Found</h3>
    <p class="text-gray-500 mb-6">Get started by creating your first add-on service.</p>
    <a href="{{ route('admin.addons.create') }}" class="bg-gradient-to-r from-purple-600 to-pink-600 text-white px-6 py-3 rounded-lg hover:from-purple-700 hover:to-pink-700 transition-all duration-200 shadow-lg">
        <i class="fas fa-plus mr-2"></i>Create First Add-on
    </a>
</div>
@endif

@push('scripts')
<script>
$(document).ready(function() {
    // Auto-filter functionality for dropdowns
    $('.auto-filter').on('change', function() {
        $('#filter-form').submit();
    });
    
    // Debounced search functionality
    let searchTimeout;
    $('input[name="search"]').on('input', function() {
        clearTimeout(searchTimeout);
        const searchValue = $(this).val();
        
        searchTimeout = setTimeout(function() {
            $('#filter-form').submit();
        }, 500); // 500ms delay after user stops typing
    });
    
    // Select all functionality
    $('#select-all').on('change', function() {
        $('.addon-checkbox').prop('checked', $(this).is(':checked'));
        updateBulkActions();
    });
    
    $('.addon-checkbox').on('change', function() {
        updateBulkActions();
        
        // Update select all checkbox
        const totalCheckboxes = $('.addon-checkbox').length;
        const checkedCheckboxes = $('.addon-checkbox:checked').length;
        $('#select-all').prop('checked', totalCheckboxes === checkedCheckboxes);
    });
    
    function updateBulkActions() {
        const checkedCount = $('.addon-checkbox:checked').length;
        $('#selected-count').text(checkedCount + ' selected');
        
        if (checkedCount > 0) {
            $('#bulk-actions').show();
        } else {
            $('#bulk-actions').hide();
        }
    }
    
    // Bulk form submission
    $('#bulk-form').on('submit', function(e) {
        const action = $('select[name="action"]').val();
        const checkedCount = $('.addon-checkbox:checked').length;
        
        if (!action) {
            e.preventDefault();
            alert('Please select an action.');
            return;
        }
        
        if (checkedCount === 0) {
            e.preventDefault();
            alert('Please select at least one add-on.');
            return;
        }
        
        if (action === 'delete') {
            if (!confirm(`Are you sure you want to delete ${checkedCount} add-on(s)? This action cannot be undone.`)) {
                e.preventDefault();
                return;
            }
        }
    });
});
</script>
@endpush
@endsection