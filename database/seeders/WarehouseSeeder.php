<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Shop;
use App\Models\StockLedger;
use App\Models\User;
use App\Models\Warehouse;
use App\Models\WarehouseStock;
use Illuminate\Database\Seeder;

class WarehouseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $rootShop = null;
        try {
            $rootUser = User::role('root')->whereHas('shop')->first();
            $rootShop = $rootUser?->shop;
        } catch (\Exception $e) {
            $rootShop = Shop::first();
        }

        if (! $rootShop) {
            $rootShop = Shop::first();
        }

        $centralWarehouse = Warehouse::firstOrCreate(
            ['is_default' => true],
            [
                'shop_id' => $rootShop?->id,
                'name' => 'Central Warehouse',
                'address' => 'Main Logistics Hub',
                'is_default' => true,
            ]
        );

        // Link all shops to central warehouse if not set
        Shop::whereNull('warehouse_id')->update(['warehouse_id' => $centralWarehouse->id]);

        // Migrate non-digital products to central warehouse stock
        Product::where('is_digital', false)->chunk(100, function ($products) use ($centralWarehouse) {
            foreach ($products as $product) {
                $product->update(['is_stock_managed' => true]);

                $stock = WarehouseStock::firstOrCreate(
                    [
                        'warehouse_id' => $centralWarehouse->id,
                        'product_id' => $product->id,
                        'color_id' => null,
                        'size_id' => null,
                    ],
                    [
                        'quantity' => $product->quantity ?? 0,
                    ]
                );

                StockLedger::firstOrCreate(
                    [
                        'to_warehouse_id' => $centralWarehouse->id,
                        'product_id' => $product->id,
                        'reference_type' => 'migration',
                    ],
                    [
                        'from_warehouse_id' => null,
                        'quantity' => $product->quantity ?? 0,
                        'notes' => 'Initial migration seed',
                    ]
                );
            }
        });
    }
}
