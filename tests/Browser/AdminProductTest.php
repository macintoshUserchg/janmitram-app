<?php

namespace Tests\Browser;

use App\Models\Product;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class AdminProductTest extends DuskTestCase
{
    use Helpers;

    public function test_product_list_loads(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->admin())
                ->visit('/admin/product')
                ->assertSee('Product List');
        });
    }

    public function test_product_detail_loads(): void
    {
        $product = Product::first();
        $this->assertNotNull($product);

        $this->browse(function (Browser $browser) use ($product) {
            $browser->loginAs($this->admin())
                ->visit("/admin/product/{$product->id}")
                ->assertSee($product->name);
        });
    }

    public function test_product_approve_toggle(): void
    {
        // Create an unapproved product so the approve action changes it
        $product = Product::factory()->create(['is_approve' => false]);
        $this->cleanupIds[] = [Product::class, $product->id];
        $originalApprove = $product->is_approve;

        $this->browse(function (Browser $browser) use ($product) {
            $browser->loginAs($this->admin())
                ->visit('/admin/product')
                ->visit("/admin/product/{$product->id}/approve")
                ->waitForText('successfully', 10);
        });

        $product->refresh();
        $this->assertNotEquals($originalApprove, $product->is_approve);
    }
}
