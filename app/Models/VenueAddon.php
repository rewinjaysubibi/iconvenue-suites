<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VenueAddon extends Model
{
    protected $fillable = [
        'name',
        'description',
        'price',
        'category',
        'is_active',
        'sort_order',
        'stock_quantity',
        'track_stock',
        'low_stock_threshold',
        'notes'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_active' => 'boolean',
        'track_stock' => 'boolean'
    ];

    public function bookings()
    {
        return $this->belongsToMany(Booking::class, 'booking_addons')
                    ->withPivot('quantity', 'price_at_booking')
                    ->withTimestamps();
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public function scopeLowStock($query)
    {
        return $query->where('track_stock', true)
                    ->whereColumn('stock_quantity', '<=', 'low_stock_threshold');
    }

    public function scopeOutOfStock($query)
    {
        return $query->where('track_stock', true)
                    ->where('stock_quantity', '<=', 0);
    }

    public function isLowStock()
    {
        return $this->track_stock && 
               $this->low_stock_threshold && 
               $this->stock_quantity <= $this->low_stock_threshold;
    }

    public function isOutOfStock()
    {
        return $this->track_stock && $this->stock_quantity <= 0;
    }

    public function getStockStatusAttribute()
    {
        if (!$this->track_stock) {
            return 'unlimited';
        }
        
        if ($this->stock_quantity <= 0) {
            return 'out_of_stock';
        }
        
        if ($this->isLowStock()) {
            return 'low_stock';
        }
        
        return 'in_stock';
    }
}
