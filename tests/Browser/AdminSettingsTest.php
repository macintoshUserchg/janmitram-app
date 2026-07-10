<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class AdminSettingsTest extends DuskTestCase
{
    use Helpers;

    /**
     * General setting page loads with form fields.
     */
    public function test_general_settings_page_loads(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->admin())
                ->visit('/admin/generale-setting')
                ->assertSee('Admin Settings')
                ->assertSee('Website Name')
                ->assertSee('Email Address')
                ->assertSee('Address');
        });
    }

    /**
     * Business setup page loads with commission/shop settings.
     */
    public function test_business_setup_page_loads(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->admin())
                ->visit('/admin/business-setting')
                ->assertSee('Business Settings')
                ->assertSee('Business Information')
                ->assertSee('Business Model');
        });
    }

    /**
     * Verification/OTP settings page loads with toggles.
     */
    public function test_verification_settings_page_loads(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->admin())
                ->visit('/admin/verification')
                ->assertSee('Verification OTP Settings')
                ->assertSee('Customer Registration OTP Verify')
                ->assertSee('Phone')
                ->assertSee('Email');
        });
    }
}
