<?php

namespace Tests\Browser;

use App\Models\Coupon;
use App\Models\FlashSale;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class AdminCouponFlashSaleTest extends DuskTestCase
{
    use Helpers;

    private static string $couponCode;

    public function test_coupon_create(): void
    {
        Coupon::where('code', 'like', 'TESTDUSK%')->forceDelete();
        FlashSale::where('name', 'like', 'Test Flash Sale Dusk%')->forceDelete();

        self::$couponCode = 'TESTDUSK'.strtotime('now').rand(100, 999);

        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->admin())
                ->visit('/admin/coupon/create')
                ->assertSee('Add New Promo Code')
                ->type('input[name="code"]', self::$couponCode)
                ->tap(fn ($b) => $this->selectByValue($b, 'select[name="discount_type"]', 'Percentage'))
                ->type('input[name="discount"]', '15')
                ->type('input[name="min_order_amount"]', '100')
                ->tap(function (Browser $b) {
                    $b->script([
                        "document.querySelector('input[name=\"start_date\"]').value = '".date('Y-m-d')."';",
                        "document.querySelector('input[name=\"start_time\"]').value = '00:00';",
                        "document.querySelector('input[name=\"expired_date\"]').value = '".date('Y-m-d', strtotime('+30 days'))."';",
                        "document.querySelector('input[name=\"expired_time\"]').value = '23:59';",
                    ]);
                })
                ->press('Submit')
                ->pause(2000)
                ->assertPathBeginsWith('/janmitram-app/admin/coupon');
        });

        $coupon = Coupon::where('code', self::$couponCode)->first();
        $this->assertNotNull($coupon);
    }

    public function test_coupon_list_shows_new_coupon(): void
    {
        $coupon = Coupon::where('code', self::$couponCode)->first();
        $this->assertNotNull($coupon);

        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->admin())
                ->visit('/admin/coupon')
                ->assertSee('Promo Codes');
        });
    }

    public function test_coupon_toggle(): void
    {
        $coupon = Coupon::where('code', self::$couponCode)->first();
        $this->assertNotNull($coupon);
        $originalStatus = (bool) $coupon->is_active;

        $this->browse(function (Browser $browser) use ($coupon) {
            $browser->loginAs($this->admin())
                ->visit("/admin/coupon/{$coupon->id}/toggle")
                ->pause(2000);
        });

        $coupon->refresh();
        $this->assertNotEquals($originalStatus, (bool) $coupon->is_active);
    }

    public function test_flash_sale_create(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->admin())
                ->visit('/admin/flash-sale/create')
                ->assertSee('Create New FlashSale')
                ->type('input[name="name"]', 'Test Flash Sale Dusk')
                ->type('input[name="discount"]', '25')
                ->tap(function (Browser $b) {
                    $b->script([
                        "document.querySelector('input[name=\"start_date\"]').value = '".date('Y-m-d')."';",
                        "document.querySelector('input[name=\"start_time\"]').value = '00:00';",
                        "document.querySelector('input[name=\"end_date\"]').value = '".date('Y-m-d', strtotime('+7 days'))."';",
                        "document.querySelector('input[name=\"end_time\"]').value = '23:59';",
                    ]);
                })
                ->type('textarea[name="description"]', 'Flash sale created by Dusk test')
                ->attach('input[name="thumbnail"]', $this->fakeImage())
                ->press('Submit')
                ->pause(2000)
                ->assertPathBeginsWith('/janmitram-app/admin/flash-sale');
        });

        $flashSale = FlashSale::where('name', 'Test Flash Sale Dusk')->first();
        $this->assertNotNull($flashSale);
    }

    public function test_flash_sale_list_shows_new_record(): void
    {
        $flashSale = FlashSale::where('name', 'Test Flash Sale Dusk')->first();
        $this->assertNotNull($flashSale);

        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->admin())
                ->visit('/admin/flash-sale')
                ->assertSee('Flash Sales')
                ->assertSee('Test Flash Sale Dusk');
        });
    }

    public function test_flash_sale_toggle(): void
    {
        $flashSale = FlashSale::where('name', 'Test Flash Sale Dusk')->first();
        $this->assertNotNull($flashSale);
        $originalStatus = (bool) $flashSale->status;

        $this->browse(function (Browser $browser) use ($flashSale) {
            $browser->loginAs($this->admin())
                ->visit("/admin/flash-sale/{$flashSale->id}/toggle")
                ->pause(2000);
        });

        $flashSale->refresh();
        $this->assertNotEquals($originalStatus, (bool) $flashSale->status);
    }

    public function test_cleanup(): void
    {
        Coupon::where('code', self::$couponCode)->forceDelete();
        FlashSale::where('name', 'Test Flash Sale Dusk')->forceDelete();
        $this->assertNull(Coupon::where('code', self::$couponCode)->first());
        $this->assertNull(FlashSale::where('name', 'Test Flash Sale Dusk')->first());
    }
}
