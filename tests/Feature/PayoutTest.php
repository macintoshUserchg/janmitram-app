<?php

namespace Tests\Feature;

use App\Enums\OrderStatus;
use App\Models\Address;
use App\Models\Area;
use App\Models\Coupon;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Shop;
use App\Models\ShopMonthlyPayout;
use App\Models\User;
use App\Models\Wallet;
use App\Services\PayoutService;
use Carbon\Carbon;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PayoutTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // CustomerFactory assigns the Spatie `customer` role, so seed roles first.
        $this->seed(RoleSeeder::class);

        // OrderFactory randomly picks customer/coupon/address/area from the DB,
        // so those tables must be non-empty before any order is created.
        // CouponFactory randomly picks a shop too, so seed one reference shop.
        // Its user is inactive so the payout run never processes it (and it
        // doesn't pollute shop counts in assertions).
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
     * Create a delivered order for a shop in a specific month.
     */
    private function deliveredOrder(Shop $shop, float $amount, int $year, int $month): Order
    {
        $createdAt = Carbon::create($year, $month, 15, 12, 0, 0);

        return Order::factory()->create([
            'shop_id' => $shop->id,
            'customer_id' => $this->customer->id,
            'coupon_id' => $this->coupon->id,
            'address_id' => $this->address->id,
            'total_amount' => $amount,
            'order_status' => OrderStatus::DELIVERED->value,
            'created_at' => $createdAt,
            'updated_at' => $createdAt,
        ]);
    }

    /**
     * Create a shop with a fresh user, optionally under a parent.
     */
    private function shop(?Shop $parent = null): Shop
    {
        return Shop::create([
            'name' => 'Shop '.fake()->unique()->word(),
            'user_id' => User::factory()->create(['is_active' => true])->id,
            'parent_shop_id' => $parent?->id,
        ]);
    }

    public function test_phase1_only_shop_gets_10_percent(): void
    {
        $shop = $this->shop();
        $this->deliveredOrder($shop, 20000, 2026, 7);
        $this->deliveredOrder($shop, 20000, 2026, 7);
        $this->deliveredOrder($shop, 10000, 2026, 7);

        PayoutService::payoutMonth(2026, 7);

        $this->assertDatabaseHas('shop_monthly_payouts', [
            'shop_id' => $shop->id,
            'year' => 2026,
            'month' => 7,
            'personal_sales' => 50000,
            'group_sales' => 50000,
            'group_size' => 1,
            'level' => null,
            'phase1_amount' => 5000,
            'phase2_amount' => 0,
            'total_payout' => 5000,
        ]);

        $wallet = Wallet::where('user_id', $shop->user_id)->first();
        $this->assertNotNull($wallet);
        $this->assertEquals(5000, $wallet->balance);
        $this->assertDatabaseHas('transactions', [
            'wallet_id' => $wallet->id,
            'amount' => 5000,
            'type' => 'credit',
            'is_commission' => false,
            'purpose' => 'mlm_payout',
        ]);
    }

    public function test_level0_flat_3000_at_33000(): void
    {
        $root = $this->shop();
        for ($i = 0; $i < 9; $i++) {
            $this->shop($root);
        }
        $this->deliveredOrder($root, 33000, 2026, 7);

        PayoutService::payoutMonth(2026, 7);

        $payout = ShopMonthlyPayout::where('shop_id', $root->id)->where('year', 2026)->where('month', 7)->first();
        $this->assertNotNull($payout);
        $this->assertSame(0, $payout->level);
        $this->assertEquals(3000, $payout->phase2_amount);
        $this->assertEquals(6300, $payout->total_payout);
    }

    public function test_level0_flat_not_percent_at_40000(): void
    {
        $root = $this->shop();
        for ($i = 0; $i < 9; $i++) {
            $this->shop($root);
        }
        $this->deliveredOrder($root, 40000, 2026, 7);

        PayoutService::payoutMonth(2026, 7);

        $payout = ShopMonthlyPayout::where('shop_id', $root->id)->where('year', 2026)->where('month', 7)->first();
        $this->assertSame(0, $payout->level);
        $this->assertEquals(3000, $payout->phase2_amount);
    }

    public function test_level1_4_percent_at_100000(): void
    {
        $root = $this->shop();
        for ($i = 0; $i < 9; $i++) {
            $this->shop($root);
        }
        $this->deliveredOrder($root, 100000, 2026, 7);

        PayoutService::payoutMonth(2026, 7);

        $payout = ShopMonthlyPayout::where('shop_id', $root->id)->where('year', 2026)->where('month', 7)->first();
        $this->assertSame(1, $payout->level);
        $this->assertEquals(4000, $payout->phase2_amount);
    }

    public function test_group_sales_includes_descendants(): void
    {
        $a = $this->shop();
        $b = $this->shop($a);
        $this->deliveredOrder($a, 30000, 2026, 7);
        $this->deliveredOrder($b, 50000, 2026, 7);

        PayoutService::payoutMonth(2026, 7);

        // Aggregation is correct (group = own + child), but a 2-member tree
        // meets no tier's group_size gate (all tiers need >= 10), so no phase 2.
        $payoutA = ShopMonthlyPayout::where('shop_id', $a->id)->where('year', 2026)->where('month', 7)->first();
        $this->assertNotNull($payoutA);
        $this->assertEquals(30000, $payoutA->personal_sales);
        $this->assertEquals(80000, $payoutA->group_sales);
        $this->assertSame(2, $payoutA->group_size);
        $this->assertNull($payoutA->level);
        $this->assertEquals(0, $payoutA->phase2_amount);
        $this->assertEquals(3000, $payoutA->total_payout); // phase 1 only

        $payoutB = ShopMonthlyPayout::where('shop_id', $b->id)->where('year', 2026)->where('month', 7)->first();
        $this->assertNotNull($payoutB);
        $this->assertEquals(50000, $payoutB->group_sales);
        $this->assertNull($payoutB->level);
        $this->assertEquals(0, $payoutB->phase2_amount);
    }

    public function test_multi_level_tree_aggregates(): void
    {
        $a = $this->shop();
        $b = $this->shop($a);
        $c = $this->shop($b);
        $this->deliveredOrder($b, 10000, 2026, 7);
        $this->deliveredOrder($c, 5000, 2026, 7);

        PayoutService::payoutMonth(2026, 7);

        // Grandchild sales roll up through the chain, but 15,000 group sales is
        // below the 33,000 minimum and 3 members is below the 10-member gate.
        $payoutA = ShopMonthlyPayout::where('shop_id', $a->id)->where('year', 2026)->where('month', 7)->first();
        $this->assertNotNull($payoutA);
        $this->assertEquals(15000, $payoutA->group_sales);
        $this->assertSame(3, $payoutA->group_size);
        $this->assertNull($payoutA->level);
        $this->assertEquals(0, $payoutA->phase1_amount);
        $this->assertEquals(0, $payoutA->phase2_amount);
    }

    public function test_level4_cap_via_resolver(): void
    {
        [$level, $amount] = PayoutService::phase2(400_000_000, 100_000);
        $this->assertSame(4, $level);
        $this->assertEquals(150000, $amount);

        [$level, $amount] = PayoutService::phase2(400_000_000, 10);
        $this->assertNull($level);
        $this->assertEquals(0, $amount);
    }

    public function test_size_gate_excludes_small_trees(): void
    {
        $root = $this->shop();
        for ($i = 0; $i < 9; $i++) {
            $this->shop($root);
        }
        $this->deliveredOrder($root, 400000, 2026, 7);

        PayoutService::payoutMonth(2026, 7);

        // 400,000 sales would place the group above level 1 (which tops out at
        // 300,000), but level 2 needs 100 members — a 10-member tree falls
        // through every tier, so phase 2 is 0.
        $payout = ShopMonthlyPayout::where('shop_id', $root->id)->where('year', 2026)->where('month', 7)->first();
        $this->assertNotNull($payout);
        $this->assertNull($payout->level);
        $this->assertEquals(0, $payout->phase2_amount);
    }

    public function test_skips_when_snapshot_exists(): void
    {
        $shop = $this->shop();
        $this->deliveredOrder($shop, 50000, 2026, 7);

        PayoutService::payoutMonth(2026, 7);
        $result = PayoutService::payoutMonth(2026, 7);

        $this->assertSame(0, $result['credited']);
        $this->assertSame(1, $result['skipped']);
        $this->assertDatabaseCount('shop_monthly_payouts', 1);
        $this->assertDatabaseCount('transactions', 1);

        $wallet = Wallet::where('user_id', $shop->user_id)->first();
        $this->assertEquals(5000, $wallet->balance);
    }

    public function test_zero_sales_creates_snapshot_without_credit(): void
    {
        $shop = $this->shop();

        PayoutService::payoutMonth(2026, 7);

        $this->assertDatabaseHas('shop_monthly_payouts', [
            'shop_id' => $shop->id,
            'year' => 2026,
            'month' => 7,
            'personal_sales' => 0,
            'group_sales' => 0,
            'group_size' => 1,
            'level' => null,
            'phase1_amount' => 0,
            'phase2_amount' => 0,
            'total_payout' => 0,
        ]);
        $this->assertDatabaseCount('transactions', 0);
    }

    public function test_no_admin_wallet_credit(): void
    {
        $shop = $this->shop();
        $this->deliveredOrder($shop, 50000, 2026, 7);

        $admin = User::factory()->create(['is_active' => true]);
        $adminWallet = Wallet::create(['user_id' => $admin->id, 'balance' => 100]);

        PayoutService::payoutMonth(2026, 7);

        $this->assertEquals(100, $adminWallet->fresh()->balance);
    }

    public function test_inactive_user_shop_excluded_from_tree(): void
    {
        $a = $this->shop();
        $bUser = User::factory()->create(['is_active' => false]);
        $b = Shop::create(['name' => 'Inactive Shop', 'user_id' => $bUser->id, 'parent_shop_id' => $a->id]);
        $this->deliveredOrder($a, 30000, 2026, 7);
        $this->deliveredOrder($b, 50000, 2026, 7);

        PayoutService::payoutMonth(2026, 7);

        $payoutA = ShopMonthlyPayout::where('shop_id', $a->id)->where('year', 2026)->where('month', 7)->first();
        $this->assertNotNull($payoutA);
        $this->assertEquals(30000, $payoutA->group_sales);
        $this->assertSame(1, $payoutA->group_size);

        $this->assertDatabaseMissing('shop_monthly_payouts', ['shop_id' => $b->id]);
    }

    public function test_command_runs_clean(): void
    {
        $shop = $this->shop();
        $this->deliveredOrder($shop, 50000, 2026, 7);

        $exitCode = $this->artisan('mlm:calculate-payouts', ['--month' => 7, '--year' => 2026])->run();

        $this->assertSame(0, $exitCode);
        $this->assertDatabaseHas('shop_monthly_payouts', [
            'shop_id' => $shop->id,
            'year' => 2026,
            'month' => 7,
            'total_payout' => 5000,
        ]);
    }

    public function test_invalid_month_fails(): void
    {
        $exitCode = $this->artisan('mlm:calculate-payouts', ['--month' => 13, '--year' => 2026])->run();

        $this->assertSame(1, $exitCode);
    }

    public function test_main_shop_has_unlimited_direct_downline_capacity(): void
    {
        // Main shop (id = 1)
        $mainShop = Shop::find(1) ?? Shop::create([
            'id' => 1,
            'name' => 'Main Janmitram Shop',
            'user_id' => User::factory()->create(['is_active' => true])->id,
            'parent_shop_id' => null,
        ]);

        $this->assertTrue($mainShop->isMainShop());
        $this->assertTrue($mainShop->canAcceptDirectDownline());
        $this->assertNull($mainShop->availableDirectDownlineSlots());

        // Add 12 direct downlines to Main Shop
        for ($i = 0; $i < 12; $i++) {
            $this->shop($mainShop);
        }

        $this->assertTrue($mainShop->canAcceptDirectDownline());
    }

    public function test_standard_shop_enforces_10_direct_downline_limit(): void
    {
        $partnerShop = $this->shop();
        $this->assertFalse($partnerShop->isMainShop());
        $this->assertTrue($partnerShop->canAcceptDirectDownline());
        $this->assertSame(10, $partnerShop->availableDirectDownlineSlots());

        // Add 10 direct children
        for ($i = 0; $i < 10; $i++) {
            $this->shop($partnerShop);
        }

        $this->assertSame(10, $partnerShop->directDownlinesCount());
        $this->assertSame(0, $partnerShop->availableDirectDownlineSlots());
        $this->assertFalse($partnerShop->canAcceptDirectDownline());
    }
}
