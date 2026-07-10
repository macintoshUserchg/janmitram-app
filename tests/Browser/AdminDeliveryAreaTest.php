<?php

namespace Tests\Browser;

use App\Models\Area;
use App\Models\DeliveryCharge;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class AdminDeliveryAreaTest extends DuskTestCase
{
    use Helpers;

    public function test_delivery_charge_create(): void
    {
        $this->browse(function (Browser $browser) {
            $min = mt_rand(100000, 999999);
            $max = mt_rand(1000000, 9999999);
            $browser->loginAs($this->admin())
                ->visit('/admin/delivery-charge/create')
                ->assertSee('Add New Delivery Charge')
                ->type('input[name="min_order_qty"]', $min)
                ->type('input[name="max_order_qty"]', $max)
                ->type('input[name="delivery_charge"]', '25.50')
                ->press('Submit');

            $record = DeliveryCharge::where('min_qty', $min)->first();
            $this->assertNotNull($record);
            $this->cleanupIds[] = [DeliveryCharge::class, $record->id];
        });
    }

    public function test_area_create(): void
    {
        $this->browse(function (Browser $browser) {
            $name = 'Area Dusk '.time();
            $browser->loginAs($this->admin())
                ->visit('/admin/area')
                ->assertSee('Area List')
                ->press('Add Area')
                ->waitFor('#createArea')
                ->type('input[name="name"]', $name)
                ->type('input[name="delivery_amount"]', '50')
                ->press('Submit')
                ->waitForText('Area created successfully', 10);

            $record = Area::where('name', $name)->first();
            $this->assertNotNull($record);
            $this->cleanupIds[] = [Area::class, $record->id];
        });
    }

    public function test_country_page_loads(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->admin())
                ->visit('/admin/country')
                ->assertSee('Country List');
        });
    }
}
