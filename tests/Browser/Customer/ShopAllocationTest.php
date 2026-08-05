<?php

namespace Tests\Browser\Customer;

use App\Models\Address;
use App\Models\Area;
use App\Models\Cart;
use App\Models\Customer;
use App\Models\GeneraleSetting;
use App\Models\Product;
use App\Models\Shop;
use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class ShopAllocationTest extends DuskTestCase
{
    protected array $cleanupIds = [];

    private ?float $originalRadiusKm = null;

    private ?string $originalShopType = null;

    /**
     * Reset persisted Pinia state between tests.
     */
    private function resetState(Browser $browser): void
    {
        $browser->visit('/');
        $browser->pause(300);
        $browser->script(['localStorage.clear(); sessionStorage.clear();']);
        $browser->visit('/');
        $browser->pause(800);
        $browser->waitForText('Buy Now', 15);
    }

    /**
     * Click an element whose text matches the given string.
     */
    private function clickText(Browser $browser, string $text): void
    {
        $text = addslashes($text);
        $browser->script([
            "(function() {
                var nodes = document.querySelectorAll('button, a, span, div');
                for (var i = 0; i < nodes.length; i++) {
                    if (nodes[i].textContent.trim().toLowerCase() === '{$text}'.toLowerCase()) {
                        nodes[i].click(); return true;
                    }
                }
                for (var i = 0; i < nodes.length; i++) {
                    if (nodes[i].textContent.trim().toLowerCase().includes('{$text}'.toLowerCase())) {
                        nodes[i].click(); return true;
                    }
                }
                return false;
            })();",
        ]);
    }

    public function test_checkout_shows_shop_picker_when_line_unfulfillable(): void
    {
        // A single shop ~1.5 km away from the delivery address (out of radius).
        $shop = Shop::factory()->create([
            'name' => 'Test Shop',
            'status' => true,
            'latitude' => 26.91,
            'longitude' => 75.79,
        ]);
        $shop->user()->update(['is_active' => true]);
        $this->cleanupIds[] = [Shop::class, $shop->id];

        $product = Product::factory()->create([
            'name' => 'Test Product',
            'shop_id' => $shop->id,
            'quantity' => 5,
            'is_active' => true,
            'is_approve' => true,
        ]);
        $this->cleanupIds[] = [Product::class, $product->id];

        $user = User::factory()->create([
            'password' => bcrypt('secret'),
            'is_active' => true,
        ]);
        $user->assignRole('customer');
        $this->cleanupIds[] = [User::class, $user->id];

        $customer = Customer::factory()->create(['user_id' => $user->id]);
        $this->cleanupIds[] = [Customer::class, $customer->id];

        // AddressFactory defaults area_id from the areas table.
        $area = Area::factory()->create();
        $this->cleanupIds[] = [Area::class, $area->id];

        $address = Address::factory()->create([
            'customer_id' => $customer->id,
            'name' => 'Test Address',
            'latitude' => 26.9,
            'longitude' => 75.8,
            'is_default' => true,
        ]);
        $this->cleanupIds[] = [Address::class, $address->id];

        Cart::create([
            'customer_id' => $customer->id,
            'shop_id' => $shop->id,
            'product_id' => $product->id,
            'quantity' => 2,
        ]);

        // Force the single shop out of the allocation radius -> unfulfillable.
        $setting = GeneraleSetting::first();
        $this->originalRadiusKm = $setting?->shop_allocation_radius_km;
        $this->originalShopType = $setting?->shop_type;
        $setting->update([
            'shop_allocation_radius_km' => 1,
            'shop_type' => 'multi',
        ]);

        $this->browse(function (Browser $browser) use ($user, $shop, $product, $address) {
            $this->resetState($browser);

            // Login through the SPA auth modal.
            $browser->press('Buy Now')
                ->waitForText('Please login first!', 5)
                ->type('input[placeholder*="email"]', $user->email)
                ->type('input[placeholder*="Password"]', 'secret')
                ->press('Log in')
                ->waitForText('Login Successful', 10);

            // The cart row was seeded directly; select the shop for checkout.
            $browser->visit('/cart')->pause(4000);
            $bodyText = $browser->script("return document.body.innerText;")[0] ?? '';
            fwrite(STDERR, "\n=== CART PAGE TEXT ===\n".substr($bodyText, 0, 2000)."\n=== END ===\n");
            $browser->waitForText($shop->name, 8);
            $this->clickText($browser, $shop->name);
            $browser->pause(500);

            // Checkout auto-selects the default address, then place the order.
            // The 422 unfulfillable response renders the shop picker.
            $browser->visit('/checkout')
                ->waitForText($address->name, 8)
                ->waitForText($product->name, 8)
                ->pause(500)
                ->press('Place Order')
                ->waitForText('Choose delivery shop', 10)
                ->assertSee('km');
        });
    }

    protected function tearDown(): void
    {
        $setting = GeneraleSetting::first();
        if ($setting && $this->originalRadiusKm !== null) {
            $setting->update([
                'shop_allocation_radius_km' => $this->originalRadiusKm,
                'shop_type' => $this->originalShopType,
            ]);
        }

        foreach ($this->cleanupIds as [$model, $ids]) {
            $ids = (array) $ids;
            if (! empty($ids)) {
                $model::whereIn('id', $ids)->forceDelete();
            }
        }

        parent::tearDown();
    }
}
