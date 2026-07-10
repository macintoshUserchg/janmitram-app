<?php

namespace Tests\Browser;

use App\Models\Shop;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class AdminShopTest extends DuskTestCase
{
    use Helpers;

    public function test_shop_list_loads(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->admin())
                ->visit('/admin/shop')
                ->assertSee('Shops');
        });
    }

    public function test_shop_detail_loads(): void
    {
        $shop = Shop::first();
        $this->assertNotNull($shop);

        $this->browse(function (Browser $browser) use ($shop) {
            $browser->loginAs($this->admin())
                ->visit("/admin/shop/{$shop->id}")
                ->assertSee($shop->name);
        });
    }

    public function test_shop_orders_subview_loads(): void
    {
        $shop = Shop::first();
        $this->assertNotNull($shop);

        $this->browse(function (Browser $browser) use ($shop) {
            $browser->loginAs($this->admin())
                ->visit("/admin/shop/{$shop->id}/orders")
                ->assertSee('Order');
        });
    }

    public function test_shop_products_subview_loads(): void
    {
        $shop = Shop::first();
        $this->assertNotNull($shop);

        $this->browse(function (Browser $browser) use ($shop) {
            $browser->loginAs($this->admin())
                ->visit("/admin/shop/{$shop->id}/products")
                ->assertSee('Product');
        });
    }
}
