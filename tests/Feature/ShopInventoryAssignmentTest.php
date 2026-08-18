<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Shop;
use App\Models\StockLedger;
use App\Models\StockRequest;
use App\Models\User;
use App\Models\Warehouse;
use App\Models\WarehouseStock;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ShopInventoryAssignmentTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
        $this->seed(PermissionSeeder::class);
    }

    private function rootUser(): User
    {
        $admin = User::factory()->create(['is_active' => true]);
        $admin->assignRole(Role::findOrCreate('root', 'web'));

        return $admin;
    }

    private function warehouse(string $name = 'Central', bool $default = true): Warehouse
    {
        return Warehouse::create(['name' => $name, 'is_default' => $default]);
    }

    private function masterProduct(int $qty, Shop $shop): Product
    {
        $product = Product::create([
            'name' => 'Assignment Product',
            'shop_id' => $shop->id,
            'quantity' => $qty,
            'is_digital' => false,
            'is_stock_managed' => true,
            'is_approve' => true,
            'price' => 600,
        ]);

        return $product;
    }

    public function test_admin_can_assign_stock_to_shop_and_moves_it(): void
    {
        $shop = Shop::factory()->create();
        $warehouse = $this->warehouse('Central', true);
        $product = $this->masterProduct(50, $shop);

        WarehouseStock::create([
            'warehouse_id' => $warehouse->id,
            'product_id' => $product->id,
            'quantity' => 20,
        ]);

        $response = $this->actingAs($this->rootUser())
            ->post(route('admin.inventory-assignment.store'), [
                'from_warehouse_id' => $warehouse->id,
                'shop_id' => $shop->id,
                'notes' => 'X',
                'items' => [
                    ['product_id' => $product->id, 'quantity' => 5],
                ],
            ]);

        $response->assertRedirect(route('admin.inventory-assignment.index'));

        // Movement assertions
        $this->assertSame(45, $product->refresh()->quantity); // master decremented
        $this->assertSame(15, WarehouseStock::where('warehouse_id', $warehouse->id)->where('product_id', $product->id)->first()->quantity);

        $shopCopy = Product::where('master_product_id', $product->id)->where('shop_id', $shop->id)->first();
        $this->assertNotNull($shopCopy);
        $this->assertSame(5, $shopCopy->quantity); // shop copy incremented

        $request = StockRequest::where('shop_id', $shop->id)->latest()->first();
        $this->assertNotNull($request);
        $this->assertSame('completed', $request->status);

        $ledger = StockLedger::where('reference_type', 'shop_request')->where('reference_id', $request->id)->first();
        $this->assertNotNull($ledger);
        $this->assertSame(5, $ledger->quantity);
    }

    public function test_admin_cannot_assign_beyond_available_stock(): void
    {
        $shop = Shop::factory()->create();
        $source = $this->warehouse('Central', true);
        $product = $this->masterProduct(50, $shop);

        WarehouseStock::create([
            'warehouse_id' => $source->id,
            'product_id' => $product->id,
            'quantity' => 10,
        ]);

        $this->actingAs($this->rootUser())
            ->post(route('admin.inventory-assignment.store'), [
                'from_warehouse_id' => $source->id,
                'shop_id' => $shop->id,
                'items' => [
                    ['product_id' => $product->id, 'quantity' => 999],
                ],
            ]);

        // Nothing persisted on rejection
        $this->assertSame(0, StockRequest::count());
        $this->assertSame(50, $product->refresh()->quantity);
        $this->assertTrue(Product::where('master_product_id', $product->id)->where('shop_id', $shop->id)->doesntExist());
    }
}
