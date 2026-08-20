<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShopInventory extends Model
{
    use HasFactory;

    protected $table = 'shop_inventories';

    protected $fillable = [
        'shop_id',
        'product_id',
        'quantity',
        'is_active',
    ];

    protected $casts = [
        'shop_id' => 'integer',
        'product_id' => 'integer',
        'quantity' => 'integer',
        'is_active' => 'boolean',
    ];

    public function shop(): BelongsTo
    {
        return $this->belongsTo(Shop::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
