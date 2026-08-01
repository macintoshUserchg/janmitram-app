<?php

namespace Tests\Feature;

use App\Enums\OrderStatus;
use App\Models\Address;
use App\Models\Area;
use App\Models\Coupon;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Shop;
use App\Models\User;
use App\Services\PayoutService;
use Carbon\Carbon;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class PayoutNetworkTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);

        // Reference shop (inactive user) so CouponFactory/OrderFactory random-picks work.
        $this->customer = Customer::factory()->create();
        $this->coupon = Coupon::factory()->create(['shop_id' => Shop::create([
            'name' => 'Ref Shop',
            'user_id' => User::factory()->create(['is_active' => false])->id,
        ])->id]);
        $this->area = Area::factory()->create();
        $this->address = Address::create([
            'customer_id' => $this->customer->id,
            'area_id' => $this->area->id,
            'address_type' => 'Home', 'name' => 'Addr', 'phone' => '1',
        ]);
    }

    private function shop(?Shop $parent = null): Shop
    {
        return Shop::create([
            'name' => 'Shop '.fake()->unique()->word(),
            'user_id' => User::factory()->create(['is_active' => true])->id,
            'parent_shop_id' => $parent?->id,
        ]);
    }

    private function rootUser(): User
    {
        $user = User::factory()->create(['is_active' => true]);
        $user->assignRole(Role::findOrCreate('root', 'web'));

        return $user;
    }

    private function deliveredOrder(Shop $shop, float $amount, int $year, int $month): Order
    {
        $at = Carbon::create($year, $month, 15, 12, 0, 0);

        return Order::factory()->create([
            'shop_id' => $shop->id,
            'customer_id' => $this->customer->id,
            'coupon_id' => $this->coupon->id,
            'address_id' => $this->address->id,
            'total_amount' => $amount,
            'order_status' => OrderStatus::DELIVERED->value,
            'created_at' => $at, 'updated_at' => $at,
        ]);
    }

    public function test_network_returns_root_nodes_with_children(): void
    {
        $root = $this->shop();
        $this->shop($root);
        $this->deliveredOrder($root, 50000, 2026, 7);

        $nodes = PayoutService::networkForMonth(2026, 7);

        $this->assertCount(1, $nodes); // only the root (ref shop is inactive)
        $this->assertSame($root->id, $nodes[0]['shop_id']);
        $this->assertTrue($nodes[0]['has_children']);
        $this->assertSame(50000.0, $nodes[0]['personal_sales']);
        $this->assertSame(5000.0, $nodes[0]['phase1_amount']);
        $this->assertNull($nodes[0]['level']); // group size 2 < 10
    }

    public function test_children_of_returns_direct_children(): void
    {
        $root = $this->shop();
        $child = $this->shop($root);
        $this->deliveredOrder($root, 50000, 2026, 7);

        $nodes = PayoutService::childrenOf($root->id, 2026, 7);

        $this->assertCount(1, $nodes);
        $this->assertSame($child->id, $nodes[0]['shop_id']);
        $this->assertFalse($nodes[0]['has_children']);
    }

    public function test_paid_month_uses_snapshot_values(): void
    {
        $root = $this->shop();
        $this->deliveredOrder($root, 50000, 2026, 7);
        PayoutService::payoutMonth(2026, 7);

        $nodes = PayoutService::networkForMonth(2026, 7);

        $this->assertSame(5000.0, $nodes[0]['total_payout']);
        // Snapshot values win even though sales are still in the DB:
        $this->assertSame(5000.0, $nodes[0]['phase1_amount']);
    }

    public function test_unpaid_month_computes_preview(): void
    {
        $root = $this->shop();
        $this->deliveredOrder($root, 50000, 2026, 7);

        $nodes = PayoutService::networkForMonth(2026, 7);

        $this->assertSame(5000.0, $nodes[0]['phase1_amount']);
        $this->assertSame(0.0, $nodes[0]['phase2_amount']);
    }

    public function test_network_route_renders_tree_page(): void
    {
        $root = $this->shop();
        $this->deliveredOrder($root, 50000, 2026, 7);

        $response = $this->actingAs($this->rootUser())
            ->get(route('admin.payout.network', ['year' => 2026, 'month' => 7]));

        $response->assertOk();
        $response->assertSee('Payout Network');
    }

    public function test_children_route_returns_json(): void
    {
        $root = $this->shop();
        $child = $this->shop($root);

        $response = $this->actingAs($this->rootUser())
            ->getJson(route('admin.payout.network.children', ['shop' => $root->id, 'year' => 2026, 'month' => 7]));

        $response->assertOk();
        $response->assertJsonCount(1);
        $response->assertJsonPath('0.shop_id', $child->id);
    }
}
