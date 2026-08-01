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

class DeactivationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // CustomerFactory assigns the Spatie `customer` role, so seed roles first.
        $this->seed(RoleSeeder::class);

        // CouponFactory randomly picks a shop, so seed one reference shop.
        // Its user is inactive so it is never a deactivation candidate (and it
        // doesn't pollute counts in assertions).
        $this->customer = Customer::factory()->create();
        $this->coupon = Coupon::factory()->create(['shop_id' => Shop::create([
            'name' => 'Reference Shop',
            'user_id' => User::factory()->create(['is_active' => false])->id,
        ])->id]);
        $this->area = Area::factory()->create();
        $this->address = Address::create([
            'customer_id' => $this->customer->id,
            'area_id' => $this->area->id,
            'address_type' => 'Home',
            'name' => 'Test Address',
            'phone' => '1234567890',
        ]);
    }

    /**
     * Create an order (any status) for a shop at a given point in time.
     */
    private function orderAt(Shop $shop, Carbon $createdAt): Order
    {
        return Order::factory()->create([
            'shop_id' => $shop->id,
            'customer_id' => $this->customer->id,
            'coupon_id' => $this->coupon->id,
            'address_id' => $this->address->id,
            'total_amount' => 100,
            'order_status' => OrderStatus::PENDING->value,
            'created_at' => $createdAt,
            'updated_at' => $createdAt,
        ]);
    }

    /**
     * Create a shop with a fresh active user, optionally under a parent.
     */
    private function shop(?Shop $parent = null): Shop
    {
        return Shop::create([
            'name' => 'Shop '.fake()->unique()->word(),
            'user_id' => User::factory()->create(['is_active' => true])->id,
            'parent_shop_id' => $parent?->id,
        ]);
    }

    public function test_deactivates_user_with_no_recent_orders(): void
    {
        $shop = $this->shop();
        $this->orderAt($shop, now()->subDays(100));

        PayoutService::deactivateInactiveMembers();

        $this->assertSame(0, (int) $shop->user()->first()->is_active);
        $this->assertNull($shop->fresh()->parent_shop_id);
    }

    public function test_any_order_status_counts_as_activity(): void
    {
        $shop = $this->shop();
        $this->orderAt($shop, now()->subDays(5));

        $result = PayoutService::deactivateInactiveMembers();

        $this->assertSame(0, $result['deactivated']);
        $this->assertSame(1, (int) $shop->user()->first()->is_active);
    }

    public function test_children_become_roots(): void
    {
        $parent = $this->shop();
        $c1 = $this->shop($parent);
        $c2 = $this->shop($parent);
        $this->orderAt($parent, now()->subDays(100));

        PayoutService::deactivateInactiveMembers();

        $this->assertNull($c1->fresh()->parent_shop_id);
        $this->assertNull($c2->fresh()->parent_shop_id);
        $this->assertNull($parent->fresh()->parent_shop_id);
        $this->assertSame(0, (int) $parent->user()->first()->is_active);
    }

    public function test_skips_already_inactive_users(): void
    {
        $user = User::factory()->create(['is_active' => false]);
        $shop = Shop::create(['name' => 'Stale Shop', 'user_id' => $user->id]);
        $this->orderAt($shop, now()->subDays(200));

        $result = PayoutService::deactivateInactiveMembers();

        $this->assertSame(0, $result['deactivated']);
        $this->assertSame(0, (int) $user->fresh()->is_active);
    }

    public function test_never_deactivates_root_admin_shops(): void
    {
        $user = User::factory()->create(['is_active' => true]);
        $user->assignRole(Role::findOrCreate('root', 'web'));

        $shop = Shop::create(['name' => 'Root Shop', 'user_id' => $user->id]);
        $this->orderAt($shop, now()->subDays(200));

        $result = PayoutService::deactivateInactiveMembers();

        $this->assertSame(0, $result['deactivated']);
        $this->assertSame(1, (int) $user->fresh()->is_active);
    }

    public function test_rerun_is_noop(): void
    {
        $shop = $this->shop();
        $this->orderAt($shop, now()->subDays(100));

        PayoutService::deactivateInactiveMembers();
        $second = PayoutService::deactivateInactiveMembers();

        $this->assertSame(0, $second['deactivated']);
        $this->assertSame(0, (int) $shop->user()->first()->is_active);
    }

    public function test_command_runs_clean(): void
    {
        $shop = $this->shop();
        $this->orderAt($shop, now()->subDays(100));

        $exitCode = $this->artisan('mlm:deactivate-inactive')->run();

        $this->assertSame(0, $exitCode);
        $this->assertSame(0, (int) $shop->user()->first()->is_active);
    }
}
