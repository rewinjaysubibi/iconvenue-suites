<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VenueAddon;
use Illuminate\Http\Request;

class VenueAddonController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = VenueAddon::query();

        // Filter by category
        if ($request->category) {
            $query->where('category', $request->category);
        }

        // Filter by status
        if ($request->status === 'active') {
            $query->where('is_active', true);
        } elseif ($request->status === 'inactive') {
            $query->where('is_active', false);
        } elseif ($request->status === 'low_stock') {
            $query->lowStock();
        } elseif ($request->status === 'out_of_stock') {
            $query->outOfStock();
        }

        // Search
        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        $addons = $query->orderBy('category')->orderBy('sort_order')->paginate(15);
        
        $categories = VenueAddon::distinct()->pluck('category')->filter()->sort();
        
        return view('admin.addons.index', compact('addons', 'categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = VenueAddon::distinct()->pluck('category')->filter()->sort();
        return view('admin.addons.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'category' => 'required|string|max:100',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer',
            'track_stock' => 'boolean',
            'stock_quantity' => 'nullable|integer|min:0',
            'low_stock_threshold' => 'nullable|integer|min:0',
            'notes' => 'nullable|string'
        ]);

        $validated['is_active'] = $request->has('is_active');
        $validated['track_stock'] = $request->has('track_stock');
        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        VenueAddon::create($validated);

        return redirect()->route('admin.addons.index')
            ->with('success', 'Add-on created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(VenueAddon $addon)
    {
        return view('admin.addons.show', compact('addon'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(VenueAddon $addon)
    {
        $categories = VenueAddon::distinct()->pluck('category')->filter()->sort();
        return view('admin.addons.edit', compact('addon', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, VenueAddon $addon)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'category' => 'required|string|max:100',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer',
            'track_stock' => 'boolean',
            'stock_quantity' => 'nullable|integer|min:0',
            'low_stock_threshold' => 'nullable|integer|min:0',
            'notes' => 'nullable|string'
        ]);

        $validated['is_active'] = $request->has('is_active');
        $validated['track_stock'] = $request->has('track_stock');
        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        $addon->update($validated);

        return redirect()->route('admin.addons.index')
            ->with('success', 'Add-on updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(VenueAddon $addon)
    {
        $addon->delete();

        return redirect()->route('admin.addons.index')
            ->with('success', 'Add-on deleted successfully!');
    }

    /**
     * Toggle addon availability
     */
    public function toggleActive(VenueAddon $addon)
    {
        $addon->update(['is_active' => !$addon->is_active]);
        
        $status = $addon->is_active ? 'activated' : 'deactivated';
        return back()->with('success', "Add-on {$status} successfully!");
    }

    /**
     * Bulk actions
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:activate,deactivate,delete',
            'selected_addons' => 'required|array',
            'selected_addons.*' => 'exists:venue_addons,id'
        ]);

        $addons = VenueAddon::whereIn('id', $request->selected_addons);

        switch ($request->action) {
            case 'activate':
                $addons->update(['is_active' => true]);
                $message = 'Selected add-ons activated successfully!';
                break;
            case 'deactivate':
                $addons->update(['is_active' => false]);
                $message = 'Selected add-ons deactivated successfully!';
                break;
            case 'delete':
                $addons->delete();
                $message = 'Selected add-ons deleted successfully!';
                break;
        }

        return back()->with('success', $message);
    }
}
