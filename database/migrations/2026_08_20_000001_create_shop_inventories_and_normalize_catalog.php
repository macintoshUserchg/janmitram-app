<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Create shop_inventories table if not exists
        if (! Schema::hasTable('shop_inventories')) {
            Schema::create('shop_inventories', function (Blueprint $table) {
                $table->id();
                $table->foreignId('shop_id')->constrained('shops')->cascadeOnDelete();
                $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
                $table->integer('quantity')->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamps();

                $table->unique(['shop_id', 'product_id'], 'shop_product_unique');
                $table->index(['product_id', 'quantity', 'is_active'], 'prod_qty_active_idx');
            });
        }

        // 2. Backfill shop_inventories from existing products table
        $allProducts = DB::table('products')->get();
        $copyMapping = []; // child_id => master_id

        foreach ($allProducts as $product) {
            if ($product->master_product_id) {
                $copyMapping[$product->id] = (int) $product->master_product_id;

                // Insert branch stock record for this shop
                if ($product->shop_id) {
                    DB::table('shop_inventories')->updateOrInsert(
                        [
                            'shop_id' => $product->shop_id,
                            'product_id' => $product->master_product_id,
                        ],
                        [
                            'quantity' => max(0, (int) $product->quantity),
                            'is_active' => (bool) $product->is_active,
                            'updated_at' => now(),
                            'created_at' => now(),
                        ]
                    );
                }
            } else {
                // Master product in primary shop (shop_id or default 1)
                $shopId = $product->shop_id ?: 1;
                DB::table('shop_inventories')->updateOrInsert(
                    [
                        'shop_id' => $shopId,
                        'product_id' => $product->id,
                    ],
                    [
                        'quantity' => max(0, (int) $product->quantity),
                        'is_active' => (bool) $product->is_active,
                        'updated_at' => now(),
                        'created_at' => now(),
                    ]
                );
            }
        }

        // 3. Remap all foreign keys referencing child copies to point directly to master_product_id
        if (! empty($copyMapping)) {
            foreach ($copyMapping as $childId => $masterId) {
                // order_products
                DB::table('order_products')
                    ->where('product_id', $childId)
                    ->update(['product_id' => $masterId]);

                // carts
                DB::table('carts')
                    ->where('product_id', $childId)
                    ->update(['product_id' => $masterId]);

                // pos_cart_products
                if (Schema::hasTable('pos_cart_products')) {
                    DB::table('pos_cart_products')
                        ->where('product_id', $childId)
                        ->update(['product_id' => $masterId]);
                }

                // reviews
                if (Schema::hasTable('reviews')) {
                    DB::table('reviews')
                        ->where('product_id', $childId)
                        ->update(['product_id' => $masterId]);
                }

                // recent_views
                if (Schema::hasTable('recent_views')) {
                    DB::table('recent_views')
                        ->where('product_id', $childId)
                        ->update(['product_id' => $masterId]);
                }

                // favorites
                if (Schema::hasTable('favorites')) {
                    DB::table('favorites')
                        ->where('product_id', $childId)
                        ->update(['product_id' => $masterId]);
                }

                // shop_user_chats
                if (Schema::hasTable('shop_user_chats')) {
                    DB::table('shop_user_chats')
                        ->where('product_id', $childId)
                        ->update(['product_id' => $masterId]);
                }

                // stock_request_items
                if (Schema::hasTable('stock_request_items')) {
                    DB::table('stock_request_items')
                        ->where('product_id', $childId)
                        ->update(['product_id' => $masterId]);
                }

                // stock_ledgers
                if (Schema::hasTable('stock_ledgers')) {
                    DB::table('stock_ledgers')
                        ->where('product_id', $childId)
                        ->update(['product_id' => $masterId]);
                }

                // warehouse_stock
                if (Schema::hasTable('warehouse_stock')) {
                    DB::table('warehouse_stock')
                        ->where('product_id', $childId)
                        ->update(['product_id' => $masterId]);
                }
            }

            // 4. Clean up child copy rows and their pivot records from database
            $childIds = array_keys($copyMapping);

            $pivotTables = [
                'product_categories',
                'product_subcategories',
                'product_colors',
                'product_sizes',
                'product_vat_taxes',
                'product_thumbnails',
                'product_attachments',
                'product_translations',
                'product_licenses',
            ];

            foreach ($pivotTables as $pivot) {
                if (Schema::hasTable($pivot)) {
                    DB::table($pivot)->whereIn('product_id', $childIds)->delete();
                }
            }

            // Delete child copy products from products table
            DB::table('products')->whereIn('id', $childIds)->delete();
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shop_inventories');
    }
};
