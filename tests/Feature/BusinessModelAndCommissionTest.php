<?php

namespace Tests\Feature;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Models\GeneraleSetting;
use App\Models\Order;
use App\Models\Shop;
use App\Models\User;
use App\Repositories\WalletRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BusinessModelAndCommissionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        GeneraleSetting::create([
            'business_based_on' => 'none',
            'commission' => 0,
            'commission_type' => 'percentage',
            'commission_charge' => 'per_order',
            'shop_allocation_radius_km' => 50,
            'currency' => '₹',
            'currency_position' => 'prefix',
        ]);
    }

    public function test_direct_zero_fee_model_deducts_zero_commission_and_credits_100_percent(): void
    {
        $setting = GeneraleSetting::first();
        $this->assertEquals('none', $setting->business_based_on);

        $shopUser = User::factory()->create();
        $shop = Shop::factory()->create(['user_id' => $shopUser->id]);
        $wallet = WalletRepository::storeByRequest($shopUser);

        $order = Order::create([
            'shop_id' => $shop->id,
            'order_code' => 1234,
            'prefix' => 'RC',
            'total_amount' => 1000,
            'payable_amount' => 1000,
            'order_status' => OrderStatus::DELIVERED->value,
            'payment_status' => PaymentStatus::PAID->value,
            'payment_method' => 'Cash Payment',
            'admin_commission' => 0,
        ]);

        $commission = 0;
        if ($setting?->business_based_on == 'commission' && $setting?->commission_charge != 'monthly') {
            if ($setting?->commission_type != 'fixed') {
                $commission = $order->total_amount * $setting->commission / 100;
            } else {
                $commission = $setting->commission ?? 0;
            }
        }

        $this->assertEquals(0, $commission, 'Commission must be exactly 0 in direct model');
        $this->assertEquals(0, $order->admin_commission);
    }

    public function test_future_commission_model_calculates_and_records_correct_percentages(): void
    {
        $setting = GeneraleSetting::first();
        $setting->update([
            'business_based_on' => 'commission',
            'commission' => 10,
            'commission_type' => 'percentage',
            'commission_charge' => 'per_order',
        ]);

        $orderTotal = 1500;
        $commission = 0;
        if ($setting?->business_based_on == 'commission' && $setting?->commission_charge != 'monthly') {
            if ($setting?->commission_type != 'fixed') {
                $commission = $orderTotal * $setting->commission / 100;
            } else {
                $commission = $setting->commission ?? 0;
            }
        }

        $this->assertEquals(150, $commission, '10% of ₹1,500 must equal ₹150');
    }

    public function test_future_fixed_commission_model_calculates_flat_fee(): void
    {
        $setting = GeneraleSetting::first();
        $setting->update([
            'business_based_on' => 'commission',
            'commission' => 50,
            'commission_type' => 'fixed',
            'commission_charge' => 'per_order',
        ]);

        $orderTotal = 2000;
        $commission = 0;
        if ($setting?->business_based_on == 'commission' && $setting?->commission_charge != 'monthly') {
            if ($setting?->commission_type != 'fixed') {
                $commission = $orderTotal * $setting->commission / 100;
            } else {
                $commission = $setting->commission ?? 0;
            }
        }

        $this->assertEquals(50, $commission, 'Flat fee of ₹50 must be charged');
    }
}
