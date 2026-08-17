<?php

namespace Tests\Feature;

use App\Models\Cart;
use App\Models\Product;
use App\Models\Shop;
use App\Models\Size;
use App\Models\User;
use App\Models\VatTax;
use App\Repositories\CartRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ProductVatTaxTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Set a bare global request so the checkout math path
     * (cartAccessToken / getDeliveryAmount) sees no auth, no address.
     */
    private function bindBareRequest(): void
    {
        $this->app->instance('request', Request::create('/'));
    }

    private function makeProduct(Shop $shop, array $overrides = []): Product
    {
        return Product::create(array_merge([
            'shop_id' => $shop->id,
            'name' => 'Test Product',
            'price' => 100,
            'discount_price' => null,
            'quantity' => 10,
            'min_order_quantity' => 1,
            'is_active' => true,
            'is_digital' => false,
            'is_stock_managed' => false,
            'is_new' => false,
            'is_featured' => false,
            'is_approve' => true,
        ], $overrides));
    }

    public function test_default_rate_applies_to_unassigned_and_override_to_assigned(): void
    {
        $this->bindBareRequest();

        // VAT 5% is the platform default; VAT 12% is a per-product override.
        $vat5 = VatTax::create(['name' => 'VAT 5%', 'percentage' => 5, 'is_active' => 1, 'is_default' => 1]);
        $vat12 = VatTax::create(['name' => 'VAT 12%', 'percentage' => 12, 'is_active' => 1]);

        $shop = Shop::factory()->create();

        $p1 = $this->makeProduct($shop, ['name' => 'P1 Override', 'price' => 100]);
        $p1->vatTaxes()->attach($vat12->id);

        $p2 = $this->makeProduct($shop, ['name' => 'P2 Global', 'price' => 100]);

        $cart1 = Cart::create([
            'product_id' => $p1->id,
            'shop_id' => $shop->id,
            'quantity' => 1,
        ]);
        $cart2 = Cart::create([
            'product_id' => $p2->id,
            'shop_id' => $shop->id,
            'quantity' => 1,
        ]);

        $result = CartRepository::checkoutByRequest(Request::create('/'), collect([$cart1, $cart2]));

        $this->assertEquals(200.0, $result['total_amount']);
        $this->assertEquals(0.0, $result['delivery_charge']);
        $this->assertEquals(0.0, $result['coupon_discount']);

        $taxByName = collect($result['all_vat_taxes'])->keyBy('name');

        // Only the default rate is applied to the unassigned product's subtotal.
        $this->assertEquals(5.00, $taxByName['VAT 5%']['amount']);
        // The overridden product pays exactly its assigned rate, not the default stack.
        $this->assertEquals(12.00, $taxByName['VAT 12%']['amount']);
        $this->assertEquals(17.00, $result['order_tax_amount']);
        $this->assertEquals(217.00, $result['payable_amount']);
    }

    public function test_discount_and_variant_pricing_are_taxed(): void
    {
        $this->bindBareRequest();

        $vat12 = VatTax::create(['name' => 'VAT 12%', 'percentage' => 12, 'is_active' => 1]);

        $shop = Shop::factory()->create();

        $size = Size::create(['name' => 'L', 'shop_id' => $shop->id]);

        $p1 = $this->makeProduct($shop, [
            'name' => 'Discounted Variant',
            'price' => 100,
            'discount_price' => 80,
        ]);
        $p1->sizes()->attach($size->id, ['price' => 10]);
        $p1->vatTaxes()->attach($vat12->id);

        $cart = Cart::create([
            'product_id' => $p1->id,
            'shop_id' => $shop->id,
            'quantity' => 1,
            'size' => $size->id,
        ]);

        $result = CartRepository::checkoutByRequest(Request::create('/'), collect([$cart]));

        $this->assertEquals(90.0, $result['total_amount']);

        $taxByName = collect($result['all_vat_taxes'])->keyBy('name');

        $this->assertEquals(10.80, $taxByName['VAT 12%']['amount']);
        $this->assertEquals(10.80, $result['order_tax_amount']);
        $this->assertEquals(100.80, $result['payable_amount']);
    }

    public function test_zero_rate_override_excludes_product_and_skips_zero_row(): void
    {
        $this->bindBareRequest();

        $vat0 = VatTax::create(['name' => 'VAT 0%', 'percentage' => 0, 'is_active' => 1]);
        $vat5 = VatTax::create(['name' => 'VAT 5%', 'percentage' => 5, 'is_active' => 1, 'is_default' => 1]);

        $shop = Shop::factory()->create();

        $p1 = $this->makeProduct($shop, ['name' => 'P1 Zero-Rate', 'price' => 100]);
        $p1->vatTaxes()->attach($vat0->id);

        $p2 = $this->makeProduct($shop, ['name' => 'P2 Global', 'price' => 100]);

        $cart1 = Cart::create([
            'product_id' => $p1->id,
            'shop_id' => $shop->id,
            'quantity' => 1,
        ]);
        $cart2 = Cart::create([
            'product_id' => $p2->id,
            'shop_id' => $shop->id,
            'quantity' => 1,
        ]);

        $result = CartRepository::checkoutByRequest(Request::create('/'), collect([$cart1, $cart2]));

        $taxByName = collect($result['all_vat_taxes'])->keyBy('name');

        // P1 (zero-rate override) is excluded from the global base and
        // contributes no tax; the zero-rate row itself is skipped.
        $this->assertFalse($taxByName->has('VAT 0%'));
        $this->assertEquals(5.00, $taxByName['VAT 5%']['amount']);
        $this->assertEquals(5.00, $result['order_tax_amount']);
        $this->assertEquals(200.0, $result['total_amount']);
        $this->assertEquals(205.00, $result['payable_amount']);
    }

    public function test_admin_endpoint_assigns_per_product_tax(): void
    {
        Role::create(['name' => 'root']);

        $user = User::factory()->create();
        $user->assignRole('root');
        $this->actingAs($user);

        $shop = Shop::factory()->create();
        $product = $this->makeProduct($shop, ['name' => 'Taxable Product']);
        $tax = VatTax::create(['name' => 'VAT 12%', 'percentage' => 12, 'is_active' => 1]);

        $response = $this->post(route('admin.product.tax.update', $product), [
            'vat_tax_id' => $tax->id,
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasNoErrors();

        $this->assertDatabaseHas('product_vat_taxes', [
            'product_id' => $product->id,
            'vat_tax_id' => $tax->id,
        ]);

        $this->assertTrue($product->vatTaxes()->pluck('vat_taxes.id')->contains($tax->id));
    }

    public function test_product_creation_requires_unit_and_vat_tax(): void
    {
        $user = User::factory()->create();
        $shop = Shop::factory()->create(['user_id' => $user->id]);
        $this->actingAs($user);

        $response = $this->post(route('shop.product.store'), [
            'name' => 'Sample Product',
            'short_description' => 'Short desc',
            'description' => 'Full desc',
            'category' => 1,
            'code' => '123456',
            'price' => 150,
            // omitting 'unit' and 'vat_tax_id'
        ]);

        $response->assertSessionHasErrors(['unit', 'vat_tax_id']);
    }
}
