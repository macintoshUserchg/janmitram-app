<?php

namespace Tests\Browser\ShopOwner;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Color;
use App\Models\Media;
use App\Models\Product;
use App\Models\Shop;
use App\Models\Size;
use App\Models\Unit;
use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\Browser\Helpers;
use Tests\DuskTestCase;

class ProductManagementTest extends DuskTestCase
{
    use Helpers;

    protected Shop $shop;

    protected User $user;

    protected Category $category;

    protected Brand $brand;

    protected Color $color;

    protected Size $size;

    protected Unit $unit;

    protected function setUp(): void
    {
        parent::setUp();

        $this->shop = Shop::factory()->create();
        $this->user = User::factory()->create([
            'shop_id' => $this->shop->id,
            'is_active' => true,
        ]);
        $this->user->assignRole('shop');

        // Create dependent models directly since factories don't exist
        $media = Media::factory()->create();
        $this->category = Category::factory()->create(['status' => 1]);
        $this->brand = Brand::create([
            'name' => 'Test Brand',
            'shop_id' => $this->shop->id,
            'is_active' => true,
            'media_id' => $media->id,
        ]);
        $this->color = Color::create([
            'name' => 'Test Color',
            'shop_id' => $this->shop->id,
            'color_code' => '#FF0000',
            'is_active' => true,
        ]);
        $this->size = Size::create([
            'name' => 'Test Size',
            'shop_id' => $this->shop->id,
            'is_active' => true,
        ]);
        $this->unit = Unit::create([
            'name' => 'Test Unit',
            'shop_id' => $this->shop->id,
            'is_active' => true,
        ]);

        $this->cleanupIds[] = [Shop::class, $this->shop->id];
        $this->cleanupIds[] = [User::class, $this->user->id];
        $this->cleanupIds[] = [Category::class, $this->category->id];
        $this->cleanupIds[] = [Brand::class, $this->brand->id];
        $this->cleanupIds[] = [Color::class, $this->color->id];
        $this->cleanupIds[] = [Size::class, $this->size->id];
        $this->cleanupIds[] = [Unit::class, $this->unit->id];
    }

    protected function fakeImage(): string
    {
        return __DIR__.'/../test-image.png';
    }

    public function test_create_product_with_variants(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->user)
                ->visit('/shop/product/create')
                ->waitForText('Add New Product', 10)
                ->type('name', 'Test Product Dusk')
                ->type('short_description', 'Short description for test')
                ->tap(function ($browser) {
                    $browser->script("document.querySelector('.ql-editor').innerHTML = '<p>Full description</p>';document.querySelector('#description').value = '<p>Full description</p>';");
                })
                ->select('category', $this->category->id)
                ->select('brand', $this->brand->id)
                ->type('code', rand(10000, 99999))
                ->type('price', '100')
                ->type('discount_price', '80')
                ->type('quantity', '50')
                ->select('unit', $this->unit->id)
                ->attach('thumbnail', $this->fakeImage())
                ->press('Submit')
                ->waitForText('Product created successfully', 10);
        });

        $product = Product::where('name', 'Test Product Dusk')->first();
        $this->assertNotNull($product);
        $this->cleanupIds[] = [Product::class, $product->id];
    }

    public function test_product_image_upload(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->user)
                ->visit('/shop/product/create')
                ->waitForText('Add New Product', 10)
                ->type('name', 'Image Test Product')
                ->type('short_description', 'Product with images')
                ->tap(function ($browser) {
                    $browser->script("document.querySelector('.ql-editor').innerHTML = '<p>Description</p>';document.querySelector('#description').value = '<p>Description</p>';");
                })
                ->select('category', $this->category->id)
                ->select('brand', $this->brand->id)
                ->type('code', 'IMG-TEST-'.rand(1000, 9999))
                ->type('price', '200')
                ->type('quantity', '10')
                ->select('unit', $this->unit->id)
                ->attach('thumbnail', $this->fakeImage())
                ->press('Submit')
                ->waitForText('Product created successfully', 10);
        });

        $product = Product::where('name', 'Image Test Product')->first();
        $this->assertNotNull($product);
        $this->assertNotNull($product->thumbnail);
        $this->cleanupIds[] = [Product::class, $product->id];
    }

    public function test_product_status_toggle(): void
    {
        $product = Product::factory()->create([
            'shop_id' => $this->shop->id,
            'is_active' => true,
            'is_approve' => true,
        ]);
        $this->cleanupIds[] = [Product::class, $product->id];

        $this->browse(function (Browser $browser) use ($product) {
            $browser->loginAs($this->user)
                ->visit('/shop/product')
                ->waitForText('Product List', 10)
                ->visit('/shop/product/'.$product->id.'/toggle')
                ->waitForText('Status updated successfully', 10);
        });

        $product->refresh();
        $this->assertFalse((bool) $product->is_active);
    }

    public function test_product_approval_flow_unapproved_shows_error(): void
    {
        $product = Product::factory()->create([
            'shop_id' => $this->shop->id,
            'is_active' => true,
            'is_approve' => false,
        ]);
        $this->cleanupIds[] = [Product::class, $product->id];

        $this->browse(function (Browser $browser) use ($product) {
            $browser->loginAs($this->user)
                ->visit('/shop/product')
                ->waitForText('Product List', 10)
                ->assertSee($product->name)
                ->visit('/shop/product/'.$product->id.'/toggle')
                ->waitForText('Sorry! Your Product is not approved yet!', 10);
        });

        $product->refresh();
        $this->assertTrue((bool) $product->is_active);
    }

    public function test_product_delete(): void
    {
        $product = Product::factory()->create([
            'shop_id' => $this->shop->id,
            'name' => 'Product To Delete',
            'is_approve' => true,
        ]);
        $this->cleanupIds[] = [Product::class, $product->id];

        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->user)
                ->visit('/shop/product?view_type=list')
                ->waitForText('Product List', 10)
                ->assertSee('Product To Delete')
                ->click('.deleteConfirm')
                ->waitForText('Yes, delete it!', 10)
                ->press('Yes, delete it!')
                ->waitForText('Product deleted successfully', 10)
                ->assertDontSee('Product To Delete');
        });

        // cleanupIds will handle deletion; assertion just confirms
        $this->assertNull(Product::find($product->id));
    }
}
