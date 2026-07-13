<?php

namespace Tests\Browser;

use App\Models\VatTax;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class AdminVatTaxTest extends DuskTestCase
{
    use Helpers;

    public function test_vat_tax_list_loads(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->admin())
                ->visit('/admin/vat-tax')
                ->assertSee('All Taxes');
        });
    }

    public function test_create_vat_tax(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->admin())
                ->visit('/admin/vat-tax')
                ->assertSee('All Taxes')
                ->press('Add Tax')
                ->waitFor('#createBrand')
                ->type('input[name="name"]', 'Test Vat Tax Dusk')
                ->type('input[name="percentage"]', '5')
                ->press('Submit')
                ->waitForText('Vat tax created successfully', 10);

            $record = VatTax::where('name', 'Test Vat Tax Dusk')->first();
            $this->assertNotNull($record);
        });
    }

    public function test_toggle_vat_tax(): void
    {
        $record = VatTax::where('name', 'Test Vat Tax Dusk')->first();
        $this->assertNotNull($record);
        $originalStatus = (bool) $record->is_active;

        $this->browse(function (Browser $browser) use ($record) {
            $browser->loginAs($this->admin())
                ->visit('/admin/vat-tax')
                ->visit("/admin/vat-tax/{$record->id}/toggle")
                ->waitForText('Status Updated Successfully', 10);
        });

        $record->refresh();
        $this->assertNotEquals($originalStatus, (bool) $record->is_active);
    }

    public function test_cleanup_vat_tax(): void
    {
        VatTax::where('name', 'Test Vat Tax Dusk')->forceDelete();
        $this->assertNull(VatTax::where('name', 'Test Vat Tax Dusk')->first());
    }
}
