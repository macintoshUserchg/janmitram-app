<?php

namespace Tests\Browser\Customer;

use App\Models\Favorite;
use App\Models\Product;
use App\Models\Shop;
use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\Browser\Helpers;
use Tests\DuskTestCase;

class WishlistTest extends DuskTestCase
{
    use Helpers;

    protected array $cleanupIds = [];

    private function resetState(Browser $browser): void
    {
        $browser->visit('/');
        $browser->pause(300);
        $browser->script(['localStorage.clear(); sessionStorage.clear();']);
        $browser->visit('/');
        $browser->pause(800);
        $browser->waitForText('Buy Now', 15);
    }

    private function login(Browser $browser, string $email, string $password): void
    {
        $browser->press('Buy Now')
            ->waitForText('Please login first!', 5)
            ->type('input[placeholder*="email"]', $email)
            ->type('input[placeholder*="Password"]', $password)
            ->press('Log in')
            ->waitForText('Login Successful', 10);
    }

    public function test_add_to_wishlist(): void
    {
        $customer = User::factory()->create([
            'password' => bcrypt('secret'),
            'phone' => 'wish_'.time().'@test.com',
        ]);
        $customer->assignRole('customer');
        $customer->customer()->create(['user_id' => $customer->id]);
        $this->cleanupIds[] = [User::class, $customer->id];

        $shop = Shop::factory()->create(['status' => true]);
        $shop->user()->update(['is_active' => true]);
        $this->cleanupIds[] = [Shop::class, $shop->id];

        $product = Product::factory()->create([
            'shop_id' => $shop->id,
            'is_active' => true,
            'is_approve' => true,
        ]);
        $this->cleanupIds[] = [Product::class, $product->id];

        $this->browse(function (Browser $browser) use ($product, $customer) {
            $this->resetState($browser);
            $this->login($browser, $customer->email, 'secret');

            // Find heart icon on product card and click it
            $browser->script([
                "document.querySelectorAll('.w-full button svg').forEach(svg => { if (svg.closest('button') && svg.closest('button').textContent.includes('') && svg.closest('div').textContent.includes('".addslashes($product->name)."')) svg.click(); })",
            ]);
            $browser->pause(1000);

            // Check for success toast
            $browser->waitForText('Product added to favorite', 5)
                ->assertSee('Product added to favorite');
        });

        $this->assertDatabaseHas('favorites', [
            'customer_id' => $customer->customer->id,
            'product_id' => $product->id,
        ]);
    }

    public function test_remove_from_wishlist(): void
    {
        $customer = User::factory()->create([
            'password' => bcrypt('secret'),
            'phone' => 'rem_wish_'.time().'@test.com',
        ]);
        $customer->assignRole('customer');
        $customer->customer()->create(['user_id' => $customer->id]);
        $this->cleanupIds[] = [User::class, $customer->id];

        $shop = Shop::factory()->create(['status' => true]);
        $shop->user()->update(['is_active' => true]);
        $this->cleanupIds[] = [Shop::class, $shop->id];

        $product = Product::factory()->create([
            'shop_id' => $shop->id,
            'is_active' => true,
            'is_approve' => true,
        ]);
        $this->cleanupIds[] = [Product::class, $product->id];

        Favorite::create([
            'customer_id' => $customer->customer->id,
            'product_id' => $product->id,
        ]);

        $this->browse(function (Browser $browser) use ($product, $customer) {
            $this->resetState($browser);
            $this->login($browser, $customer->email, 'secret');

            $browser->visit('/')
                ->waitForText('Buy Now', 15)
                ->pause(1000);

            $browser->script([
                "document.querySelectorAll('.w-full button svg').forEach(svg => { if (svg.closest('button') && svg.closest('div').textContent.includes('".addslashes($product->name)."')) svg.click(); })",
            ]);
            $browser->pause(1000);

            $browser->waitForText('Product removed from favorite', 5)
                ->assertSee('Product removed from favorite');
        });

        $this->assertDatabaseMissing('favorites', [
            'customer_id' => $customer->customer->id,
            'product_id' => $product->id,
        ]);
    }

    public function test_wishlist_page_loads(): void
    {
        $customer = User::factory()->create([
            'password' => bcrypt('secret'),
            'phone' => 'wishlist_'.time().'@test.com',
        ]);
        $customer->assignRole('customer');
        $customer->customer()->create(['user_id' => $customer->id]);
        $this->cleanupIds[] = [User::class, $customer->id];

        $shop = Shop::factory()->create(['status' => true]);
        $shop->user()->update(['is_active' => true]);
        $this->cleanupIds[] = [Shop::class, $shop->id];

        $product = Product::factory()->create([
            'shop_id' => $shop->id,
            'is_active' => true,
            'is_approve' => true,
        ]);
        $this->cleanupIds[] = [Product::class, $product->id];

        Favorite::create([
            'customer_id' => $customer->customer->id,
            'product_id' => $product->id,
        ]);

        $this->browse(function (Browser $browser) use ($product, $customer) {
            $this->resetState($browser);
            $this->login($browser, $customer->email, 'secret');

            $browser->visit('/wishlist')
                ->waitForText('Wishlist', 8)
                ->assertSee($product->name);
        });
    }

    public function test_move_wishlist_to_cart(): void
    {
        $customer = User::factory()->create([
            'password' => bcrypt('secret'),
            'phone' => 'move_wish_'.time().'@test.com',
        ]);
        $customer->assignRole('customer');
        $customer->customer()->create(['user_id' => $customer->id]);
        $this->cleanupIds[] = [User::class, $customer->id];

        $shop = Shop::factory()->create(['status' => true]);
        $shop->user()->update(['is_active' => true]);
        $this->cleanupIds[] = [Shop::class, $shop->id];

        $product = Product::factory()->create([
            'shop_id' => $shop->id,
            'is_active' => true,
            'is_approve' => true,
            'quantity' => 10,
        ]);
        $this->cleanupIds[] = [Product::class, $product->id];

        Favorite::create([
            'customer_id' => $customer->customer->id,
            'product_id' => $product->id,
        ]);

        $this->browse(function (Browser $browser) use ($product, $customer) {
            $this->resetState($browser);
            $this->login($browser, $customer->email, 'secret');

            $browser->visit('/wishlist')
                ->waitForText('Wishlist', 8)
                ->press('Add to Cart')
                ->waitForText('product added to cart', 5)
                ->visit('/cart')
                ->waitForText($product->name, 8)
                ->assertSee($product->name);
        });

        $this->assertDatabaseHas('carts', ['product_id' => $product->id]);
    }

    public function test_guest_cannot_access_wishlist(): void
    {
        $this->browse(function (Browser $browser) {
            $this->resetState($browser);
            $browser->visit('/wishlist')
                ->waitForText('Login', 10)
                ->assertSee('Login');
        });
    }
}
