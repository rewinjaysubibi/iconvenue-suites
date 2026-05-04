<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Venue extends Model
{
    protected $table = 'venues_and_suites';
    
    protected $fillable = [
        'name', 'type', 'description', 'capacity', 
        'price_per_day', 'price_morning', 'price_afternoon', 'price_evening',
        'amenities', 'images', 'is_active'
    ];

    protected $casts = [
        'amenities' => 'array',
        'images' => 'array',
        'is_active' => 'boolean',
        'price_per_day' => 'decimal:2',
        'price_morning' => 'decimal:2',
        'price_afternoon' => 'decimal:2',
        'price_evening' => 'decimal:2',
    ];

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function packages(): HasMany
    {
        return $this->hasMany(VenuePackage::class);
    }

    public function activePackages(): HasMany
    {
        return $this->hasMany(VenuePackage::class)->where('is_active', true);
    }

    public function isAvailable($startDate, $endDate, $excludeBookingId = null)
    {
        $query = $this->bookings()
            ->where('status', '!=', 'cancelled')
            ->where(function($q) use ($startDate, $endDate) {
                $q->whereBetween('booking_date', [$startDate, $endDate])
                  ->orWhereBetween('end_date', [$startDate, $endDate])
                  ->orWhere(function($q2) use ($startDate, $endDate) {
                      $q2->where('booking_date', '<=', $startDate)
                         ->where('end_date', '>=', $endDate);
                  });
            });

        if ($excludeBookingId) {
            $query->where('id', '!=', $excludeBookingId);
        }

        return $query->count() === 0;
    }
}
