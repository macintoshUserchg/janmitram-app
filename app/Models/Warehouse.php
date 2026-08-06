<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Warehouse extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'is_default' => 'boolean',
    ];

    /**
     * Whether this warehouse is the single Central hub.
     *
     * Resolved from one stable source (the default warehouse, else the earliest
     * created) and memoized per request so view + controller guards never drift.
     */
    public function isCentralHub(): bool
    {
        static $centralId = null;

        if ($centralId === null) {
            $centralId = static::where('is_default', true)->value('id')
                ?? static::query()->min('id');
        }

        return $this->id === $centralId;
    }

    public function shops(): HasMany
    {
        return $this->hasMany(Shop::class);
    }

    public function stocks(): HasMany
    {
        return $this->hasMany(WarehouseStock::class);
    }

    public function stockRequests(): HasMany
    {
        return $this->hasMany(StockRequest::class);
    }

    public function outgoingTransfers(): HasMany
    {
        return $this->hasMany(WarehouseTransfer::class, 'from_warehouse_id');
    }

    public function incomingTransfers(): HasMany
    {
        return $this->hasMany(WarehouseTransfer::class, 'to_warehouse_id');
    }
}
