<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Scopes\ActiveScope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Review extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];

    protected $fillable = [
        'customer_id',
        'product_id',
        'shop_id',
        'order_id',
        'rating',
        'description',
        'photos',
        'reply',
        'replied_at',
        'is_active',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'rating' => 'float',
            'photos' => 'array',
            'replied_at' => 'datetime',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get the customer associated with this model.
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    /**
     * Get the product from this model.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    /**
     * Get the shop from this model.
     */
    public function shop(): BelongsTo
    {
        return $this->belongsTo(Shop::class, 'shop_id');
    }

    /**
     * Get the order associated with this review.
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    /**
     * Scope to pending reviews awaiting admin approval.
     */
    public function scopePending(Builder $query): Builder
    {
        return $query->withoutGlobalScopes()->where('is_active', false);
    }

    /**
     * Scope to approved active reviews.
     */
    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Boot method for the Review model.
     */
    protected static function booted(): void
    {
        static::addGlobalScope(new ActiveScope);
    }
}
