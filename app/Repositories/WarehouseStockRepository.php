<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\WarehouseStock;
use App\Support\Repositories\Repository;

class WarehouseStockRepository extends Repository
{
    public static function model()
    {
        return WarehouseStock::class;
    }
}
