<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\Shop;
use App\Models\StockRequest;
use App\Models\Unit;
use App\Models\User;
use App\Models\Warehouse;
use App\Services\WarehouseService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class FirstStockTransferThresholdTest extends TestCase
{
    use RefreshDatabase;

    private User $rootUser;

    private Warehouse $warehouse;

    private Shop $shop;

    private Product $productLowPrice;

    private Product $productHighPrice;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['name' => 'root']);
        Role::firstOrCreate(['name' => 'shop']);

        $this->rootUser = User::factory()->create();
        $this->rootUser->assignRole('root');

        $shopOwner = User::factory()->create();
        $shopOwner->assignRole('shop');

        $this->shop = Shop::factory()->create([
            'user_id' => $shopOwner->id,
            'status' => true,
        ]);

        $this->warehouse = Warehouse::create([
            'name' => 'Main Central Warehouse',
            'code' => 'CWH-001',
            'is_default' => true,
            'is_active' => true,
        ]);

        Category::create(['name' => 'Groceries']);
        Brand::create(['name' => 'Janmitram Brand', 'slug' => 'janmitram-brand']);
        $unit = Unit::create(['name' => 'pcs', 'shop_id' => $this->shop->id, 'is_active' => true]);

        $this->productLowPrice = Product::factory()->create([
            'name' => 'Low Price Item',
            'price' => 100.0,
            'discount_price' => 0,
            'unit_id' => $unit->id,
            'is_active' => true,
            'is_approve' => true,
            'is_digital' => false,
        ]);

        $this->productHighPrice = Product::factory()->create([
            'name' => 'High Price Item',
            'price' => 1500.0,
            'discount_price' => 0,
            'unit_id' => $unit->id,
            'is_active' => true,
            'is_approve' => true,
            'is_digital' => false,
        ]);

        WarehouseService::addStock($this->warehouse, $this->productLowPrice, 100);
        WarehouseService::addStock($this->warehouse, $this->productHighPrice, 50);
    }

    public function test_shop_identifies_first_stock_transfer_correctly(): void
    {
        $this->assertTrue($this->shop->isFirstStockTransfer());
        $this->assertFalse($this->shop->hasReceivedStock());

        StockRequest::create([
            'shop_id' => $this->shop->id,
            'warehouse_id' => $this->warehouse->id,
            'status' => 'completed',
        ]);

        $this->assertFalse($this->shop->fresh()->isFirstStockTransfer());
        $this->assertTrue($this->shop->fresh()->hasReceivedStock());
    }

    public function test_admin_stock_assignment_rejects_first_transfer_below_3000(): void
    {
        $this->actingAs($this->rootUser);

        // 5 units x ₹100 = ₹500 (below ₹3,000)
        $response = $this->post(route('admin.inventory-assignment.store'), [
            'from_warehouse_id' => $this->warehouse->id,
            'shop_id' => $this->shop->id,
            'items' => [
                [
                    'product_id' => $this->productLowPrice->id,
                    'quantity' => 5,
                ],
            ],
        ]);

        $response->assertSessionHasErrors(['items']);
        $this->assertDatabaseMissing('stock_requests', [
            'shop_id' => $this->shop->id,
        ]);
    }

    public function test_admin_stock_assignment_allows_first_transfer_at_or_above_3000(): void
    {
        $this->actingAs($this->rootUser);

        // 2 units x ₹1,500 = ₹3,000 (meets ₹3,000 threshold)
        $response = $this->post(route('admin.inventory-assignment.store'), [
            'from_warehouse_id' => $this->warehouse->id,
            'shop_id' => $this->shop->id,
            'items' => [
                [
                    'product_id' => $this->productHighPrice->id,
                    'quantity' => 2,
                ],
            ],
        ]);

        $response->assertRedirect(route('admin.inventory-assignment.index'));
        $this->assertDatabaseHas('stock_requests', [
            'shop_id' => $this->shop->id,
            'status' => 'completed',
        ]);
    }

    public function test_admin_stock_assignment_allows_subsequent_transfer_any_amount(): void
    {
        $this->actingAs($this->rootUser);

        // First transfer of ₹3,000
        StockRequest::create([
            'shop_id' => $this->shop->id,
            'warehouse_id' => $this->warehouse->id,
            'status' => 'completed',
        ]);

        // Second restocking transfer of only 2 units x ₹100 = ₹200 (< ₹3,000)
        $response = $this->post(route('admin.inventory-assignment.store'), [
            'from_warehouse_id' => $this->warehouse->id,
            'shop_id' => $this->shop->id,
            'items' => [
                [
                    'product_id' => $this->productLowPrice->id,
                    'quantity' => 2,
                ],
            ],
        ]);

        $response->assertRedirect(route('admin.inventory-assignment.index'));
    }

    public function test_shop_stock_request_rejects_first_request_below_3000(): void
    {
        $this->actingAs($this->shop->user);

        // 10 units x ₹100 = ₹1,000 (< ₹3,000)
        $response = $this->post(route('shop.stock-request.store'), [
            'items' => [
                [
                    'product_id' => $this->productLowPrice->id,
                    'quantity' => 10,
                ],
            ],
        ]);

        $response->assertSessionHasErrors(['items']);
        $this->assertDatabaseMissing('stock_requests', [
            'shop_id' => $this->shop->id,
        ]);
    }

    public function test_shop_stock_request_allows_first_request_at_or_above_3000(): void
    {
        $this->actingAs($this->shop->user);

        // 2 units x ₹1,500 = ₹3,000 (meets threshold)
        $response = $this->post(route('shop.stock-request.store'), [
            'items' => [
                [
                    'product_id' => $this->productHighPrice->id,
                    'quantity' => 2,
                ],
            ],
        ]);

        $response->assertRedirect(route('shop.stock-request.index'));
        $this->assertDatabaseHas('stock_requests', [
            'shop_id' => $this->shop->id,
            'status' => 'pending',
        ]);
    }
}
