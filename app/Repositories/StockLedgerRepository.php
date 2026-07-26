<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\StockLedger;
use App\Support\Repositories\Repository;

class StockLedgerRepository extends Repository
{
    public static function model()
    {
        return StockLedger::class;
    }
}
