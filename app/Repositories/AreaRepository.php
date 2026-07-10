<?php

namespace App\Repositories;

use App\Models\Area;
use App\Support\Repositories\Repository;

class AreaRepository extends Repository
{
    public static function model()
    {
        return Area::class;
    }

    public static function storeByRequest($request)
    {
        return self::model()::create([
            'name' => $request->name,
            'delivery_amount' => $request->delivery_amount,
        ]);
    }

    public static function updateByRequest($request, Area $area)
    {
        return $area->update([
            'name' => $request->name,
            'delivery_amount' => $request->delivery_amount,
            'is_active' => $area->is_active,
        ]);
    }

    public static function destroyByRequest(Area $area)
    {
        return $area->delete();
    }
}
