<?php

namespace Tests\Feature;

use App\Http\Controllers\Admin\InvoiceController;
use App\Http\Controllers\Shop\StockRequestController;
use App\Models\Brand;
use App\Models\Media;
use App\Models\Product;
use App\Models\Shop;
use App\Models\ShopInventory;
use App\Models\StockRequest;
use App\Models\User;
use App\Models\Warehouse;
use App\Services\WarehouseService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Spatie\Permission\Models\Permission;
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

        // Assert shop received branch inventory with quantity 10
        $this->assertDatabaseHas('shop_inventories', [
            'shop_id' => $requestShop->id,
            'product_id' => $masterProduct->id,
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

    public function test_shop_inventory_calculates_approved_requested_stock_and_sold_units(): void
    {
        $shop = Shop::factory()->create();
        $brand = Brand::create(['name' => 'Brand']);
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
            'name' => 'Catalog Item',
            'price' => 50.00,
            'quantity' => 0,
            'is_stock_managed' => true,
        ]);

        WarehouseService::addStock($warehouse, $masterProduct, 100);

        $user = User::factory()->create();
        $permission = Permission::firstOrCreate(['name' => 'shop.stock-request.index']);
        $user->givePermissionTo($permission);

        $requestShop = Shop::factory()->create([
            'user_id' => $user->id,
            'warehouse_id' => $warehouse->id,
        ]);
        $user->update(['shop_id' => $requestShop->id]);

        $stockRequest = StockRequest::create([
            'shop_id' => $requestShop->id,
            'warehouse_id' => $warehouse->id,
            'status' => 'pending',
        ]);

        $stockRequest->items()->create([
            'product_id' => $masterProduct->id,
            'quantity' => 25,
        ]);

        // Fulfill request (Approved 25 units for requestShop)
        WarehouseService::fulfillStockRequest($stockRequest);

        // Shop receives 25 units in branch inventory
        $shopInv = ShopInventory::where('shop_id', $requestShop->id)->where('product_id', $masterProduct->id)->first();
        $this->assertEquals(25, $shopInv->quantity);

        // Simulate customer order / POS sale of 5 units (shop stock decrements to 20)
        $shopInv->update(['quantity' => 20]);

        auth()->login($user);
        $controller = new StockRequestController;
        $view = $controller->inventory();

        $products = $view->getData()['products'];
        $item = $products->first();

        $this->assertEquals(25, $item->total_requested_qty);
        $this->assertEquals(5, $item->sold_qty);
        $this->assertEquals(20, $item->quantity);
    }

    public function test_can_generate_and_download_stock_request_invoice_when_completed(): void
    {
        $shop = Shop::factory()->create();
        $brand = Brand::create(['name' => 'Brand']);
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
            'name' => 'Invoice Test Item',
            'price' => 150.00,
            'quantity' => 0,
            'is_stock_managed' => true,
        ]);

        WarehouseService::addStock($warehouse, $masterProduct, 50);

        $user = User::factory()->create();
        $requestShop = Shop::factory()->create([
            'user_id' => $user->id,
            'warehouse_id' => $warehouse->id,
        ]);
        $user->update(['shop_id' => $requestShop->id]);

        $stockRequest = StockRequest::create([
            'shop_id' => $requestShop->id,
            'warehouse_id' => $warehouse->id,
            'status' => 'pending',
        ]);

        $stockRequest->items()->create([
            'product_id' => $masterProduct->id,
            'quantity' => 10,
        ]);

        // Non-completed request returns redirect/error
        auth()->login($user);
        $shopController = new StockRequestController;
        $response = $shopController->invoice($stockRequest);
        $this->assertInstanceOf(RedirectResponse::class, $response);

        // Complete request
        WarehouseService::fulfillStockRequest($stockRequest);

        // Completed request returns invoice view
        $viewResponse = $shopController->invoice($stockRequest);
        $this->assertInstanceOf(View::class, $viewResponse);
        $this->assertEquals('PDF.stock-request-invoice', $viewResponse->name());
    }

    public function test_admin_can_access_invoice_management_page(): void
    {
        $admin = User::factory()->create();

        $adminController = new InvoiceController;
        auth()->login($admin);

        $request = Request::create(route('admin.invoice.index'), 'GET');
        $viewResponse = $adminController->index($request);

        $this->assertInstanceOf(View::class, $viewResponse);
        $this->assertEquals('admin.invoice.index', $viewResponse->name());
    }
}
