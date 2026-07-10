<?php

namespace Tests\Browser;

use App\Models\Brand;
use App\Models\Color;
use App\Models\Size;
use App\Models\Unit;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class AdminBrandColorSizeUnitTest extends DuskTestCase
{
    use Helpers;

    public function test_create_brand(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->admin())
                ->visit('/admin/brand')
                ->assertSee('Brand List')
                ->press('Add Brand')
                ->waitFor('#createBrand')
                ->type('input[name="name"]', 'Test Brand Dusk')
                ->press('Submit')
                ->waitForText('Brand created successfully', 10);

            $record = Brand::where('name', 'Test Brand Dusk')->first();
            $this->assertNotNull($record);
        });
    }

    public function test_create_color(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->admin())
                ->visit('/admin/color')
                ->assertSee('Color List')
                ->press('Add Color')
                ->waitFor('#createBrand')
                ->type('input[name="name"]', 'Test Color Dusk')
                ->type('input[name="color_code"]', '#FF5733')
                ->press('Submit')
                ->waitForText('Color created successfully', 10);

            $record = Color::where('name', 'Test Color Dusk')->first();
            $this->assertNotNull($record);
        });
    }

    public function test_create_size(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->admin())
                ->visit('/admin/size')
                ->assertSee('Size List')
                ->press('Add Size')
                ->waitFor('#createBrand')
                ->type('input[name="name"]', 'Test Size Dusk')
                ->press('Submit')
                ->waitForText('Size created successfully', 10);

            $record = Size::where('name', 'Test Size Dusk')->first();
            $this->assertNotNull($record);
        });
    }

    public function test_create_unit(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->admin())
                ->visit('/admin/unit')
                ->assertSee('Unit List')
                ->press('Add Unit')
                ->waitFor('#createBrand')
                ->type('input[name="name"]', 'Test Unit Dusk')
                ->press('Submit')
                ->waitForText('Unit created successfully', 10);

            $record = Unit::where('name', 'Test Unit Dusk')->first();
            $this->assertNotNull($record);
        });
    }

    public function test_toggle_brand(): void
    {
        $record = Brand::where('name', 'Test Brand Dusk')->first();
        $this->assertNotNull($record);
        $originalStatus = (bool) $record->is_active;

        $this->browse(function (Browser $browser) use ($record) {
            $browser->loginAs($this->admin())
                ->visit('/admin/brand')
                ->visit("/admin/brand/{$record->id}/toggle")
                ->waitForText('Brand status updated', 10);
        });

        $record->refresh();
        $this->assertNotEquals($originalStatus, (bool) $record->is_active);
    }

    public function test_toggle_color(): void
    {
        $record = Color::where('name', 'Test Color Dusk')->first();
        $this->assertNotNull($record);
        $originalStatus = (bool) $record->is_active;

        $this->browse(function (Browser $browser) use ($record) {
            $browser->loginAs($this->admin())
                ->visit('/admin/color')
                ->visit("/admin/color/{$record->id}/toggle")
                ->waitForText('Status updated successfully', 10);
        });

        $record->refresh();
        $this->assertNotEquals($originalStatus, (bool) $record->is_active);
    }

    public function test_toggle_size(): void
    {
        $record = Size::where('name', 'Test Size Dusk')->first();
        $this->assertNotNull($record);
        $originalStatus = (bool) $record->is_active;

        $this->browse(function (Browser $browser) use ($record) {
            $browser->loginAs($this->admin())
                ->visit('/admin/size')
                ->visit("/admin/size/{$record->id}/toggle")
                ->waitForText('Status updated successfully', 10);
        });

        $record->refresh();
        $this->assertNotEquals($originalStatus, (bool) $record->is_active);
    }

    public function test_toggle_unit(): void
    {
        $record = Unit::where('name', 'Test Unit Dusk')->first();
        $this->assertNotNull($record);
        $originalStatus = (bool) $record->is_active;

        $this->browse(function (Browser $browser) use ($record) {
            $browser->loginAs($this->admin())
                ->visit('/admin/unit')
                ->visit("/admin/unit/{$record->id}/toggle")
                ->waitForText('Status updated successfully', 10);
        });

        $record->refresh();
        $this->assertNotEquals($originalStatus, (bool) $record->is_active);
    }

    public function test_cleanup_all(): void
    {
        Brand::where('name', 'Test Brand Dusk')->forceDelete();
        Color::where('name', 'Test Color Dusk')->forceDelete();
        Size::where('name', 'Test Size Dusk')->forceDelete();
        Unit::where('name', 'Test Unit Dusk')->forceDelete();
        $this->assertNull(Brand::where('name', 'Test Brand Dusk')->first());
    }
}
