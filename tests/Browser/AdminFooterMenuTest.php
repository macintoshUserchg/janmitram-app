<?php

namespace Tests\Browser;

use App\Models\Menu;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class AdminFooterMenuTest extends DuskTestCase
{
    use Helpers;

    public function test_footer_page_renders(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->admin())
                ->visit('/admin/footer')
                ->assertSee('Footer');
        });
    }

    public function test_menu_page_renders(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->admin())
                ->visit('/admin/menu')
                ->assertSee('Menus');
        });
    }

    public function test_create_menu_item(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->admin())
                ->visit('/admin/menu')
                ->assertSee('Menus')
                ->press('Add Menu')
                ->waitFor('#addMenu')
                ->type('input[name="name"]', 'Test Menu Dusk')
                ->click('#customUrlBtn')
                ->type('input[name="custom_url"]', 'https://example.com/dusk-test')
                ->press('Submit')
                ->waitForText('created successfully', 10);

            $record = Menu::where('name', 'Test Menu Dusk')->first();
            $this->assertNotNull($record);
        });
    }

    public function test_cleanup(): void
    {
        Menu::where('name', 'Test Menu Dusk')->forceDelete();
        $this->assertNull(Menu::where('name', 'Test Menu Dusk')->first());
    }
}
