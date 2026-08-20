<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('order_products')) {
            try {
                Schema::table('order_products', function (Blueprint $table) {
                    $table->index(['order_id', 'product_id'], 'order_product_composite_idx');
                });
            } catch (Throwable $e) {
            }
        }

        if (Schema::hasTable('shop_inventories')) {
            try {
                Schema::table('shop_inventories', function (Blueprint $table) {
                    $table->index(['shop_id', 'is_active'], 'shop_inv_active_idx');
                });
            } catch (Throwable $e) {
            }
        }

        if (Schema::hasTable('products') && Schema::hasColumn('products', 'master_product_id')) {
            try {
                Schema::table('products', function (Blueprint $table) {
                    $table->dropForeign(['master_product_id']);
                });
            } catch (Throwable $e) {
            }

            try {
                Schema::table('products', function (Blueprint $table) {
                    $table->dropColumn('master_product_id');
                });
            } catch (Throwable $e) {
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('products') && ! Schema::hasColumn('products', 'master_product_id')) {
            Schema::table('products', function (Blueprint $table) {
                $table->unsignedBigInteger('master_product_id')->nullable()->after('is_stock_managed');
            });
        }

        if (Schema::hasTable('shop_inventories')) {
            try {
                Schema::table('shop_inventories', function (Blueprint $table) {
                    $table->dropIndex('shop_inv_active_idx');
                });
            } catch (Throwable $e) {
            }
        }

        if (Schema::hasTable('order_products')) {
            try {
                Schema::table('order_products', function (Blueprint $table) {
                    $table->dropIndex('order_product_composite_idx');
                });
            } catch (Throwable $e) {
            }
        }
    }
};
