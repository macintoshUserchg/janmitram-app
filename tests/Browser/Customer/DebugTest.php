<?php

namespace Tests\Browser\Customer;

use Laravel\Dusk\Browser;
use Tests\Browser\Helpers;
use Tests\DuskTestCase;

class DebugTest extends DuskTestCase
{
    use Helpers;

    public function test_debug_home(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->waitForText('Buy Now', 15)
                ->pause(2000);
            // Check what the page source looks like
            $html = $browser->driver->getPageSource();
            echo 'PAGE LENGTH: '.strlen($html)."\n";
            // Look for login button
            $hasLogin = strpos($html, 'Login') !== false;
            echo 'HAS LOGIN TEXT: '.($hasLogin ? 'yes' : 'no')."\n";
            // Try clicking Buy Now and check modal
            $browser->press('Buy Now')->pause(2000);
            $html2 = $browser->driver->getPageSource();
            $hasModal = strpos($html2, 'Please Login to continue') !== false;
            $hasEmail = strpos($html2, 'Enter email or phone number') !== false;
            echo 'MODAL AFTER BUY NOW: '.($hasModal ? 'yes' : 'no')."\n";
            echo 'EMAIL FIELD: '.($hasEmail ? 'yes' : 'no')."\n";
        });
    }
}
