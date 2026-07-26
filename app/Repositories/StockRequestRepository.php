<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\StockRequest;
use App\Support\Repositories\Repository;

class StockRequestRepository extends Repository
{
    public static function model()
    {
        return StockRequest::class;
    }
}
