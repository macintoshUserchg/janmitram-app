<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Warehouse;
use App\Support\Repositories\Repository;

class WarehouseRepository extends Repository
{
    public static function model()
    {
        return Warehouse::class;
    }

    public static function getCentralWarehouse(): ?Warehouse
    {
        return Warehouse::where('is_default', true)->first();
    }
}
