<?php

namespace App\Repositories;

use App\Http\Requests\VatTaxRequest;
use App\Models\VatTax;
use App\Support\Repositories\Repository;

class VatTaxRepository extends Repository
{
    public static function model()
    {
        return VatTax::class;
    }

    public static function getActiveVatTaxes()
    {
        return self::query()->where('is_active', true)->get();
    }

    public static function getDefaultVatTax(): ?VatTax
    {
        return self::query()->where('is_active', true)->where('is_default', true)->first();
    }

    public static function setDefault(VatTax $vatTax): void
    {
        self::query()->where('is_default', true)->update(['is_default' => false]);
        $vatTax->update(['is_default' => true]);
    }

    public static function storeByRequest(VatTaxRequest $request)
    {
        return self::create([
            'name' => $request->name,
            'percentage' => $request->percentage,
        ]);
    }

    public static function updateByRequest(VatTax $vatTax, VatTaxRequest $request)
    {
        return $vatTax->update([
            'name' => $request->name,
            'percentage' => $request->percentage,
        ]);
    }

    public static function toggle(VatTax $vatTax)
    {
        return $vatTax->update([
            'is_active' => ! $vatTax->is_active,
        ]);
    }
}
