<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\Media;
use App\Models\Product;
use App\Models\Shop;
use App\Models\StockRequest;
use App\Models\Warehouse;
use App\Services\WarehouseService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WarehouseTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_central_warehouse(): void
    {
        $shop = Shop::factory()->create();

        $warehouse = Warehouse::create([
            'shop_id' => $shop->id,
            'name' => 'Central Logistics Hub',
            'is_default' => true,
        ]);

        $this->assertDatabaseHas('warehouses', [
            'name' => 'Central Logistics Hub',
            'is_default' => 1,
        ]);
    }

    public function test_can_add_and_fulfill_stock_request(): void
    {
        $shop = Shop::factory()->create();
        $brand = Brand::create(['name' => 'Test Brand']);
        $media = Media::factory()->create();

        $warehouse = Warehouse::create([
            'shop_id' => $shop->id,
            'name' => 'Central Warehouse',
            'is_default' => true,
        ]);

        $masterProduct = Product::create([
            'shop_id' => $shop->id,
            'brand_id' => $brand->id,
            'media_id' => $media->id,
            'name' => 'Master Electronics Product',
            'price' => 100.00,
            'quantity' => 0,
            'is_stock_managed' => true,
            'is_active' => true,
            'is_approve' => true,
        ]);

        // Add 50 stock to central warehouse
        WarehouseService::addStock($warehouse, $masterProduct, 50);

        $this->assertDatabaseHas('warehouse_stock', [
            'warehouse_id' => $warehouse->id,
            'product_id' => $masterProduct->id,
            'quantity' => 50,
        ]);

        // Shop requests 10 stock
        $requestShop = Shop::factory()->create(['warehouse_id' => $warehouse->id]);
        $stockRequest = StockRequest::create([
            'shop_id' => $requestShop->id,
            'warehouse_id' => $warehouse->id,
            'status' => 'pending',
        ]);

        $stockRequest->items()->create([
            'product_id' => $masterProduct->id,
            'quantity' => 10,
        ]);

        // Fulfill stock request
        WarehouseService::fulfillStockRequest($stockRequest);

        // Assert warehouse stock decreased by 10 (remaining: 40)
        $this->assertDatabaseHas('warehouse_stock', [
            'warehouse_id' => $warehouse->id,
            'product_id' => $masterProduct->id,
            'quantity' => 40,
        ]);

        // Assert shop received local cloned product with quantity 10
        $this->assertDatabaseHas('products', [
            'shop_id' => $requestShop->id,
            'master_product_id' => $masterProduct->id,
            'quantity' => 10,
        ]);

        // Assert ledger entry created
        $this->assertDatabaseHas('stock_ledgers', [
            'from_warehouse_id' => $warehouse->id,
            'product_id' => $masterProduct->id,
            'quantity' => 10,
            'reference_type' => 'shop_request',
        ]);
    }

    public function test_deleting_product_removes_warehouse_stock(): void
    {
        $shop = Shop::factory()->create();
        $brand = Brand::create(['name' => 'Brand']);
        $media = Media::factory()->create();

        $warehouse = Warehouse::create([
            'shop_id' => $shop->id,
            'name' => 'Warehouse',
            'is_default' => true,
        ]);

        $product = Product::create([
            'shop_id' => $shop->id,
            'brand_id' => $brand->id,
            'media_id' => $media->id,
            'name' => 'Item To Delete',
            'price' => 50.00,
            'quantity' => 0,
            'is_stock_managed' => true,
        ]);

        WarehouseService::addStock($warehouse, $product, 15);

        $this->assertDatabaseHas('warehouse_stock', [
            'product_id' => $product->id,
            'quantity' => 15,
        ]);

        // Delete product
        $product->delete();

        $this->assertDatabaseMissing('warehouse_stock', [
            'product_id' => $product->id,
        ]);
    }

    public function test_product_creation_does_not_double_count_stock_quantity(): void
    {
        $shop = Shop::factory()->create();
        $brand = Brand::create(['name' => 'Brand']);
        $media = Media::factory()->create();

        $warehouse = Warehouse::create([
            'shop_id' => $shop->id,
            'name' => 'Central Warehouse',
            'is_default' => true,
        ]);

        $product = Product::create([
            'shop_id' => $shop->id,
            'brand_id' => $brand->id,
            'media_id' => $media->id,
            'name' => 'Single Addition Item',
            'price' => 50.00,
            'quantity' => 0,
            'is_stock_managed' => true,
        ]);

        WarehouseService::addStock($warehouse, $product, 100, null, null, 'initial_product_create');

        $this->assertEquals(100, $product->fresh()->quantity);
        $this->assertDatabaseHas('warehouse_stock', [
            'product_id' => $product->id,
            'quantity' => 100,
        ]);
    }
}
