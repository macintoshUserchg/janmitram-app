<?php

namespace Tests\Browser\AdminExtended;

use App\Models\GeneraleSetting;
use App\Models\ThemeColor;
use App\Models\VerifyManage;
use Laravel\Dusk\Browser;
use Tests\Browser\Helpers;
use Tests\DuskTestCase;

class AdminSettingsExtendedTest extends DuskTestCase
{
    use Helpers;

    /**
     * General settings page loads and form can be saved.
     */
    public function test_general_settings_save(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->admin())
                ->visit('/admin/generale-setting')
                ->assertSee('Admin Settings')
                ->assertSee('Website Name')
                ->assertSee('Email Address')
                ->assertSee('Address');

            // Change a few fields
            $browser->type('input[name="name"]', 'Ext Test Site')
                ->type('input[name="email"]', 'ext-test@example.com')
                ->type('input[name="address"]', 'Ext Test Address')
                ->press('Update')
                ->waitForText('Generale settings updated successfully', 10);
        });

        $setting = GeneraleSetting::first();
        $this->assertNotNull($setting);
        $this->assertEquals('Ext Test Site', $setting->name);
        $this->assertEquals('ext-test@example.com', $setting->email);
    }

    /**
     * Business setup shop configuration: toggle business model and commission.
     */
    public function test_business_setup_shop_config(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->admin())
                ->visit('/admin/business-setting')
                ->assertSee('Business Settings')
                ->assertSee('Business Information')
                ->assertSee('Business Model');

            // Toggle business model to subscription and back
            $browser->script([
                "document.querySelector('input[name=\"business_based_on\"][value=\"subscription\"]').click();",
            ])->pause(500);

            $browser->press('Save And Update')
                ->waitForText('Business settings updated successfully', 10);
        });

        $setting = GeneraleSetting::first();
        $this->assertNotNull($setting);
        $this->assertEquals('subscription', $setting->business_based_on);

        // Reset to commission
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->admin())
                ->visit('/admin/business-setting')
                ->script([
                    "document.querySelector('input[name=\"business_based_on\"][value=\"commission\"]').click();",
                ])->pause(500)
                ->press('Save And Update')
                ->waitForText('Business settings updated successfully', 10);
        });

        $setting->refresh();
        $this->assertEquals('commission', $setting->business_based_on);
    }

    /**
     * Verification settings page loads and toggles persist.
     */
    public function test_verification_settings(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->admin())
                ->visit('/admin/verification')
                ->assertSee('Verification OTP Settings')
                ->assertSee('Customer Registration OTP Verify')
                ->assertSee('Phone')
                ->assertSee('Email');

            // Toggle register OTP on/off
            $initial = $browser->script('return document.querySelector("input[name=\"register_otp\"]").checked')[0] ?? false;
            $browser->script([
                'document.querySelector("input[name=\"register_otp\"]").click();',
            ])->pause(300)
                ->press('Save And Update')
                ->waitForText('Updated successfully', 10);
        });

        $verify = VerifyManage::latest('id')->first();
        $this->assertNotNull($verify);
        $this->assertNotEquals($initial, (bool) $verify->register_otp);

        // Reset to original
        $this->browse(function (Browser $browser) use ($initial) {
            $browser->loginAs($this->admin())
                ->visit('/admin/verification')
                ->script([
                    'document.querySelector("input[name=\"register_otp\"]").checked = '.($initial ? 'true' : 'false').';',
                ])->pause(300)
                ->press('Save And Update')
                ->waitForText('Updated successfully', 10);
        });

        $verify->refresh();
        $this->assertEquals($initial, (bool) $verify->register_otp);
    }

    /**
     * Extended theme color palette page tests - verify color palettes render
     * and a custom color can be saved.
     */
    public function test_theme_color_palette_extended(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->admin())
                ->visit('/admin/theme-color')
                ->assertSee('Theme Colors Settings')
                ->assertSee('Current Color')
                ->assertSee('Save And Update');

            $palettes = $browser->elements('.color-panel');
            $this->assertGreaterThan(0, count($palettes), 'At least one color palette should be shown');

            $initialPrimary = $browser->value('input[name="primary_color"]');
            $initialSecondary = $browser->value('input[name="secondary_color"]');

            // Click a non-active palette's primary color button if available
            $clicked = $browser->elements('.color-panel:not(.active) .primary-color');
            if (count($clicked) > 0) {
                $clicked[0]->click();
                $browser->pause(300);

                $newPrimary = $browser->value('input[name="primary_color"]');
                $this->assertNotEquals($initialPrimary, $newPrimary,
                    'Primary color should change after clicking a different palette');
            }
        });

        // Save custom colors via JS injection and form submit
        $testPrimary = '#8B5CF6';
        $testSecondary = '#EDE9FE';

        $this->browse(function (Browser $browser) use ($testPrimary, $testSecondary) {
            $browser->loginAs($this->admin())
                ->visit('/admin/theme-color')
                ->click('.color-panel .primary-color')
                ->pause(300);

            $browser->script([
                "document.getElementById('primary_color').value = '{$testPrimary}';",
                "document.getElementById('secondary_color').value = '{$testSecondary}';",
            ]);

            $browser->press('Save And Update')
                ->waitForText('Theme color updated successfully', 10)
                ->assertSee('Theme color updated successfully');
        });

        $defaultTheme = ThemeColor::where('is_default', true)->first();
        $this->assertNotNull($defaultTheme);
        $this->assertEquals(strtolower($testPrimary), strtolower($defaultTheme->primary));
        $this->assertEquals(strtolower($testSecondary), strtolower($defaultTheme->secondary));
    }

    /**
     * Negative test: submitting empty required fields on general settings fails.
     */
    public function test_general_settings_empty_required_fails(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->admin())
                ->visit('/admin/generale-setting')
                ->type('input[name="name"]', '')
                ->type('input[name="email"]', '')
                ->press('Update')
                ->pause(2000)
                ->assertSee('The website name field is required');
        });
    }
}
