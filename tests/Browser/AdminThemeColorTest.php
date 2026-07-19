<?php

namespace Tests\Browser;

use App\Models\ThemeColor;
use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class AdminThemeColorTest extends DuskTestCase
{
    use Helpers;

    /**
     * Admin can log in and see the theme-color page.
     */
    public function test_admin_can_see_theme_color_page(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(User::role('root')->first())
                ->visit('/admin/theme-color')
                ->assertSee('Theme Colors Settings')
                ->assertSee('Current Color')
                ->assertSee('Save And Update');
        });
    }

    /**
     * Clicking an available color palette updates the form fields.
     */
    public function test_clicking_color_palette_updates_form(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(User::role('root')->first())
                ->visit('/admin/theme-color');

            // The page lists available palettes; grab the first non-active one
            $initialPrimary = $browser->value('input[name="primary_color"]');
            $initialSecondary = $browser->value('input[name="secondary_color"]');

            // Click a different palette's primary-color button
            $palette = $browser->elements('.color-panel:not(.active) .primary-color');
            if (count($palette) > 0) {
                $palette[0]->click();
                $browser->pause(300);

                $newPrimary = $browser->value('input[name="primary_color"]');
                $this->assertNotEquals($initialPrimary, $newPrimary,
                    'Primary color should change after clicking a different palette');
            }
        });
    }

    /**
     * Save color update changes the database record.
     */
    public function test_saving_color_update_succeeds(): void
    {
        $this->browse(function (Browser $browser) {
            // Pick distinct test colors so the test is idempotent
            $testPrimary = '#8B5CF6';
            $testSecondary = '#EDE9FE';

            $browser->loginAs(User::role('root')->first())
                ->visit('/admin/theme-color')
                ->click('.color-panel .primary-color')
                ->pause(300);

            // Override hidden inputs with our test values via JS
            $browser->script([
                "document.getElementById('primary_color').value = '{$testPrimary}';",
                "document.getElementById('secondary_color').value = '{$testSecondary}';",
            ]);

            $browser->press('Save And Update')
                ->waitForText('Theme color updated successfully', 10)
                ->assertSee('Theme color updated successfully');

            // Verify DB was updated
            $defaultTheme = ThemeColor::where('is_default', true)->first();
            $this->assertNotNull($defaultTheme);
            $this->assertEquals(strtolower($testPrimary), strtolower($defaultTheme->primary));
            $this->assertEquals(strtolower($testSecondary), strtolower($defaultTheme->secondary));
        });
    }

    /**
     * Available color palettes are displayed on the page.
     */
    public function test_color_palettes_are_displayed(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(User::role('root')->first())
                ->visit('/admin/theme-color');

            $palettes = $browser->elements('.color-panel');
            $this->assertGreaterThan(0, count($palettes), 'At least one color palette should be shown');

            foreach ($palettes as $i => $palette) {
                $colorSpan = $palette->getText();
                $this->assertNotEmpty($colorSpan, "Palette $i should show a color hex value");
            }
        });
    }
}
