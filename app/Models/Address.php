<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Address extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    /**
     * Get the customer that owns the address.
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Get all of the orders.
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    // public function area(): BelongsTo
    // {
    //     return $this->belongsTo(Area::class, 'area_id');
    // }

    public function getArea()
    {
        return $this->hasOne(Area::class, 'id', 'area_id');
    }

    public function deliveryAmount(): float
    {
        // 1. Try resolving delivery fee by City
        $cityName = trim($this->city ?? $this->area ?? '');
        if (! empty($cityName)) {
            $cityRate = Area::where('is_active', true)
                ->where(function ($q) use ($cityName) {
                    $q->whereRaw('LOWER(name) = ?', [strtolower($cityName)])
                        ->orWhereRaw('LOWER(name) LIKE ?', ['%'.strtolower($cityName).'%'])
                        ->orWhereRaw('? LIKE CONCAT("%", LOWER(name), "%")', [strtolower($cityName)]);
                })
                ->first();

            if ($cityRate) {
                return (float) $cityRate->delivery_amount;
            }
        }

        // 2. Legacy fallback: area_id
        if ($this->area_id && $this->getArea) {
            return (float) ($this->getArea->delivery_amount ?? 0);
        }

        return 0.0;
    }
}
