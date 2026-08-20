<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\GeneraleSetting;
use App\Models\Product;
use App\Models\Shop;
use App\Models\ShopInventory;
use App\Models\Unit;
use App\Models\User;
use App\Models\Warehouse;
use App\Models\WarehouseStock;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class StockAssignmentFeatureTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $shopUser;

    protected Shop $shop;

    protected Warehouse $warehouse;

    protected Product $product;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['name' => 'root', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'customer', 'guard_name' => 'web']);

        GeneraleSetting::create([
            'title' => 'Janmitram',
            'shop_type' => 'multi',
            'currency' => '₹',
            'currency_position' => 'prefix',
        ]);

        $this->admin = User::factory()->create(['email' => 'admin_stock_test@janmitram.com']);
        $this->admin->assignRole('root');

        $this->shopUser = User::factory()->create(['is_active' => true]);
        $this->shop = Shop::factory()->create([
            'user_id' => $this->shopUser->id,
            'name' => 'Jaipur Branch Shop',
            'status' => true,
        ]);

        $this->warehouse = Warehouse::create([
            'name' => 'Central Hub',
            'address' => 'Jaipur Road',
            'is_default' => true,
        ]);

        $brand = Brand::create(['name' => 'Organic Brand', 'slug' => 'organic-brand']);
        $unit = Unit::create(['name' => 'kg', 'shop_id' => $this->shop->id, 'is_active' => true]);

        $this->product = Product::create([
            'name' => 'Turmeric Powder 500g',
            'price' => 200,
            'discount_price' => 180,
            'unit_id' => $unit->id,
            'brand_id' => $brand->id,
            'is_digital' => false,
            'is_active' => true,
            'is_approve' => true,
            'quantity' => 100,
            'code' => 'TURM-500',
        ]);

        // Add 100 units to warehouse
        WarehouseStock::create([
            'warehouse_id' => $this->warehouse->id,
            'product_id' => $this->product->id,
            'quantity' => 100,
        ]);
    }

    public function test_admin_can_assign_inventory_from_warehouse_to_shop(): void
    {
        $this->actingAs($this->admin);

        // Shop's first transfer requires at least 3000 value (e.g. 20 units x 180 = 3600)
        $response = $this->post(route('admin.inventory-assignment.store'), [
            'from_warehouse_id' => $this->warehouse->id,
            'shop_id' => $this->shop->id,
            'notes' => 'Initial branch kit',
            'items' => [
                [
                    'product_id' => $this->product->id,
                    'quantity' => 20,
                ],
            ],
        ]);

        $response->assertRedirect(route('admin.inventory-assignment.index'));
        $response->assertSessionHas('success');

        // Warehouse stock reduced from 100 to 80
        $this->assertDatabaseHas('warehouse_stock', [
            'warehouse_id' => $this->warehouse->id,
            'product_id' => $this->product->id,
            'quantity' => 80,
        ]);

        // Shop inventory increased to 20
        $this->assertDatabaseHas('shop_inventories', [
            'shop_id' => $this->shop->id,
            'product_id' => $this->product->id,
            'quantity' => 20,
        ]);

        // Completed stock request created
        $this->assertDatabaseHas('stock_requests', [
            'shop_id' => $this->shop->id,
            'warehouse_id' => $this->warehouse->id,
            'status' => 'completed',
        ]);

        // Ledger entry recorded
        $this->assertDatabaseHas('stock_ledgers', [
            'from_warehouse_id' => $this->warehouse->id,
            'product_id' => $this->product->id,
            'quantity' => 20,
            'reference_type' => 'shop_request',
        ]);
    }

    public function test_admin_dashboard_renders_without_errors(): void
    {
        $this->actingAs($this->admin);

        $response = $this->get(route('admin.dashboard.index'));
        $response->assertOk();
    }

    public function test_shop_dashboard_renders_without_errors(): void
    {
        $this->actingAs($this->shopUser);

        $response = $this->get(route('shop.dashboard.index'));
        $response->assertOk();
    }

    public function test_shop_can_view_its_allocated_inventory(): void
    {
        ShopInventory::create([
            'shop_id' => $this->shop->id,
            'product_id' => $this->product->id,
            'quantity' => 25,
            'is_active' => true,
        ]);

        $this->actingAs($this->shopUser);

        $response = $this->get(route('shop.shop-inventory.index'));
        $response->assertOk();
        $response->assertSee('Turmeric Powder 500g');
        $response->assertSee('25');
    }
}
