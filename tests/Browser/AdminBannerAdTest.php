<?php

namespace Tests\Browser;

use App\Models\Ad;
use App\Models\Banner;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class AdminBannerAdTest extends DuskTestCase
{
    use Helpers;

    public function test_create_banner(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->admin())
                ->visit('/admin/banner/create')
                ->assertSee('Add New Banner')
                ->type('title', 'Test Banner Dusk')
                ->attach('banner', $this->fakeImage())
                ->press('Submit')
                ->waitForText('Banner created successfully', 10);

            $record = Banner::where('title', 'Test Banner Dusk')->first();
            $this->assertNotNull($record);
        });
    }

    public function test_banner_toggle(): void
    {
        $record = Banner::where('title', 'Test Banner Dusk')->first();
        $this->assertNotNull($record);
        $originalStatus = (bool) $record->status;

        $this->browse(function (Browser $browser) use ($record) {
            $browser->loginAs($this->admin())
                ->visit('/admin/banner')
                ->visit("/admin/banner/{$record->id}/toggle")
                ->waitForText('Banner status updated', 10);
        });

        $record->refresh();
        $this->assertNotEquals($originalStatus, (bool) $record->status);
    }

    public function test_create_ad(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->admin())
                ->visit('/admin/ad/create')
                ->assertSee('Add Ad')
                ->type('title', 'Test Ad Dusk')
                ->attach('banner', $this->fakeImage())
                ->press('Submit')
                ->waitForText('Ad created successfully', 10);

            $record = Ad::where('title', 'Test Ad Dusk')->first();
            $this->assertNotNull($record);
        });
    }

    public function test_ad_toggle(): void
    {
        $record = Ad::where('title', 'Test Ad Dusk')->first();
        $this->assertNotNull($record);
        $originalStatus = (bool) $record->status;

        $this->browse(function (Browser $browser) use ($record) {
            $browser->loginAs($this->admin())
                ->visit('/admin/ad')
                ->visit("/admin/ad/{$record->id}/toggle")
                ->waitForText('Ad status updated', 10);
        });

        $record->refresh();
        $this->assertNotEquals($originalStatus, (bool) $record->status);
    }

    public function test_cleanup(): void
    {
        Banner::where('title', 'Test Banner Dusk')->forceDelete();
        Ad::where('title', 'Test Ad Dusk')->forceDelete();
        $this->assertNull(Banner::where('title', 'Test Banner Dusk')->first());
        $this->assertNull(Ad::where('title', 'Test Ad Dusk')->first());
    }
}
