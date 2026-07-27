<?php

namespace Tests\Feature;

use App\Http\Requests\ProductRequest;
use App\Models\Brand;
use App\Models\Media;
use App\Models\Product;
use App\Models\Shop;
use App\Models\User;
use App\Models\Warehouse;
use App\Repositories\ProductRepository;
use App\Services\WarehouseService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProductWarehouseSyncTest extends TestCase
{
    use RefreshDatabase;

    public function test_product_creation_with_warehouse_stock(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $this->actingAs($user);

        $shop = Shop::factory()->create();
        $brand = Brand::create(['name' => 'Brand']);
        $media = Media::factory()->create();

        $warehouse = Warehouse::create([
            'shop_id' => $shop->id,
            'name' => 'Central Warehouse',
            'is_default' => true,
        ]);

        $request = new ProductRequest([
            'name' => 'Stock Managed Phone',
            'description' => 'Great phone',
            'short_description' => 'Short desc',
            'brand' => $brand->id,
            'code' => '9988776655',
            'price' => 200.00,
            'quantity' => 25,
            'is_stock_managed' => 1,
            'warehouse_id' => $warehouse->id,
            'initial_warehouse_stock' => 25,
            'thumbnail' => UploadedFile::fake()->image('thumb.jpg'),
        ]);

        $product = ProductRepository::storeByRequest($request);

        $this->assertTrue((bool) $product->is_stock_managed);
        $this->assertEquals(0, $product->fresh()->quantity);

        // Add stock later via warehouse refill interface
        WarehouseService::addStock($warehouse, $product, 25);

        $this->assertDatabaseHas('warehouse_stock', [
            'warehouse_id' => $warehouse->id,
            'product_id' => $product->id,
            'quantity' => 25,
        ]);
    }

    public function test_order_deducts_warehouse_stock(): void
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
            'name' => 'Stock Laptop',
            'price' => 500.00,
            'quantity' => 10,
            'is_stock_managed' => true,
        ]);

        WarehouseService::addStock($warehouse, $product, 10);

        WarehouseService::deductStock($warehouse, $product, 3, null, null, 'order_sale', 1);

        $this->assertDatabaseHas('warehouse_stock', [
            'warehouse_id' => $warehouse->id,
            'product_id' => $product->id,
            'quantity' => 7,
        ]);
    }
}
