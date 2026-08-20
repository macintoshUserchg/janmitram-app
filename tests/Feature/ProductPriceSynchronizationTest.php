<?php

namespace Tests\Feature;

use App\Http\Requests\ProductRequest;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Color;
use App\Models\GeneraleSetting;
use App\Models\Product;
use App\Models\Shop;
use App\Models\ShopInventory;
use App\Models\Size;
use App\Models\StockRequest;
use App\Models\Unit;
use App\Models\User;
use App\Models\Warehouse;
use App\Models\WarehouseStock;
use App\Repositories\CardRepository;
use App\Repositories\ProductRepository;
use App\Services\WarehouseService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ProductPriceSynchronizationTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;

    protected Shop $rootShop;

    protected Shop $branchShopA;

    protected Shop $branchShopB;

    protected Warehouse $warehouse;

    protected Unit $unit;

    protected Brand $brand;

    protected Color $color;

    protected Size $size;

    protected function setUp(): void
    {
        parent::setUp();

        $role = Role::firstOrCreate(['name' => 'root', 'guard_name' => 'web']);
        $this->adminUser = User::factory()->create(['email' => 'admin_pricing_test@janmitram.com']);
        $this->adminUser->assignRole($role);
        $this->actingAs($this->adminUser);

        GeneraleSetting::create([
            'title' => 'Janmitram Test',
            'shop_type' => 'multi',
            'currency' => '₹',
            'currency_position' => 'prefix',
        ]);

        $this->rootShop = Shop::create([
            'name' => 'Main Janmitram Shop',
            'user_id' => $this->adminUser->id,
            'status' => 1,
            'city' => 'Jaipur',
        ]);

        $branchUserA = User::factory()->create();
        $this->branchShopA = Shop::create([
            'name' => 'Jaipur Branch Shop',
            'user_id' => $branchUserA->id,
            'status' => 1,
            'city' => 'Jaipur',
        ]);

        $branchUserB = User::factory()->create();
        $this->branchShopB = Shop::create([
            'name' => 'Mumbai Branch Shop',
            'user_id' => $branchUserB->id,
            'status' => 1,
            'city' => 'Mumbai',
        ]);

        $this->warehouse = Warehouse::create([
            'name' => 'Central Warehouse Jaipur',
            'is_default' => true,
        ]);

        $this->unit = Unit::create([
            'name' => '500g',
            'shop_id' => $this->rootShop->id,
            'is_active' => true,
        ]);

        $this->brand = Brand::create([
            'name' => 'Janmitram Organic',
            'slug' => 'janmitram-organic',
            'shop_id' => $this->rootShop->id,
            'is_active' => true,
        ]);

        $this->color = Color::create([
            'name' => 'Golden',
            'color_code' => '#FFD700',
            'shop_id' => $this->rootShop->id,
        ]);

        $this->size = Size::create([
            'name' => 'Standard',
            'shop_id' => $this->rootShop->id,
        ]);
    }

    public function test_single_canonical_product_price_and_discount_update_reflects_globally(): void
    {
        // 1. Create Canonical Master Product with initial pricing
        $product = Product::create([
            'name' => 'Organic Honey',
            'shop_id' => $this->rootShop->id,
            'price' => 100.00,
            'discount_price' => 90.00,
            'buy_price' => 70.00,
            'quantity' => 50,
            'min_order_quantity' => 1,
            'unit_id' => $this->unit->id,
            'brand_id' => $this->brand->id,
            'is_active' => true,
            'is_approve' => true,
            'is_stock_managed' => true,
            'code' => 'HONEY-500G',
        ]);

        // 2. Allocate inventory to Branch Shop A and Branch Shop B
        ShopInventory::create([
            'shop_id' => $this->branchShopA->id,
            'product_id' => $product->id,
            'quantity' => 10,
            'is_active' => true,
        ]);
        ShopInventory::create([
            'shop_id' => $this->branchShopB->id,
            'product_id' => $product->id,
            'quantity' => 15,
            'is_active' => true,
        ]);

        $this->assertEquals(100.00, $product->price);
        $this->assertEquals(90.00, $product->discount_price);
        $this->assertEquals(70.00, $product->buy_price);

        // 3. Admin updates Product price: MRP 150, Discount 130, Buy Price 110
        $request = new ProductRequest([
            'name' => 'Organic Honey Super',
            'price' => 150.00,
            'discount_price' => 130.00,
            'buy_price' => 110.00,
            'min_order_quantity' => 2,
            'unit' => $this->unit->id,
            'brand' => $this->brand->id,
            'code' => 'HONEY-500G',
            'category' => [],
            'sub_category' => [],
            'color' => [],
            'size' => [],
        ]);

        ProductRepository::updateByRequest($request, $product);

        // 4. Assert Product was updated in a single query
        $product->refresh();
        $this->assertEquals(150.00, $product->price);
        $this->assertEquals(130.00, $product->discount_price);
        $this->assertEquals(110.00, $product->buy_price);
        $this->assertEquals(2, $product->min_order_quantity);
        $this->assertEquals('Organic Honey Super', $product->name);

        // 5. Branch shops query the same canonical product and immediately see updated pricing
        $shopAProduct = $this->branchShopA->products()->find($product->id);
        $this->assertNotNull($shopAProduct);
        $this->assertEquals(150.00, $shopAProduct->price);
        $this->assertEquals(130.00, $shopAProduct->discount_price);
        $this->assertEquals(10, $shopAProduct->pivot->quantity);
    }

    public function test_variant_extra_pricing_on_canonical_product(): void
    {
        $product = Product::create([
            'name' => 'Organic Ghee',
            'shop_id' => $this->rootShop->id,
            'price' => 500.00,
            'discount_price' => 450.00,
            'buy_price' => 380.00,
            'quantity' => 50,
            'is_active' => true,
            'is_approve' => true,
            'is_stock_managed' => true,
            'code' => 'GHEE-1KG',
        ]);

        $product->colors()->attach($this->color->id, ['price' => 10.00]);
        $product->sizes()->attach($this->size->id, ['price' => 50.00]);

        $this->assertEquals(10.00, $product->colors()->first()->pivot->price);
        $this->assertEquals(50.00, $product->sizes()->first()->pivot->price);

        // Admin updates variant extra prices on Product
        $request = new ProductRequest([
            'name' => 'Organic Ghee',
            'price' => 550.00,
            'discount_price' => 500.00,
            'buy_price' => 420.00,
            'code' => 'GHEE-1KG',
            'category' => [],
            'sub_category' => [],
            'color' => [['id' => $this->color->id, 'price' => 20.00]],
            'size' => [['id' => $this->size->id, 'price' => 80.00]],
        ]);

        ProductRepository::updateByRequest($request, $product);

        $product->refresh();
        $this->assertEquals(550.00, $product->price);
        $this->assertEquals(500.00, $product->discount_price);
        $this->assertEquals(420.00, $product->buy_price);

        // Verify variant pivots updated
        $this->assertEquals(20.00, $product->colors()->first()->pivot->price);
        $this->assertEquals(80.00, $product->sizes()->first()->pivot->price);
    }

    public function test_stock_request_fulfillment_maintains_canonical_pricing(): void
    {
        $product = Product::create([
            'name' => 'Wheat Flour',
            'shop_id' => $this->rootShop->id,
            'price' => 40.00,
            'discount_price' => 35.00,
            'buy_price' => 28.00,
            'quantity' => 100,
            'is_active' => true,
            'is_approve' => true,
            'is_stock_managed' => true,
            'code' => 'ATTA-1KG',
        ]);

        WarehouseStock::create([
            'warehouse_id' => $this->warehouse->id,
            'product_id' => $product->id,
            'quantity' => 100,
        ]);

        $stockRequest = StockRequest::create([
            'shop_id' => $this->branchShopA->id,
            'warehouse_id' => $this->warehouse->id,
            'status' => 'pending',
        ]);
        $stockRequest->items()->create([
            'product_id' => $product->id,
            'quantity' => 20,
        ]);

        WarehouseService::fulfillStockRequest($stockRequest);

        // Branch inventory incremented to 20 without altering canonical prices
        $inv = ShopInventory::where('shop_id', $this->branchShopA->id)->where('product_id', $product->id)->first();
        $this->assertNotNull($inv);
        $this->assertEquals(20, $inv->quantity);

        $product->refresh();
        $this->assertEquals(40.00, $product->price);
        $this->assertEquals(35.00, $product->discount_price);
        $this->assertEquals(28.00, $product->buy_price);
    }

    public function test_bulk_excel_import_updates_canonical_pricing(): void
    {
        $product = Product::create([
            'name' => 'Basmati Rice',
            'shop_id' => $this->rootShop->id,
            'price' => 120.00,
            'discount_price' => 110.00,
            'buy_price' => 85.00,
            'quantity' => 0,
            'is_active' => true,
            'is_approve' => true,
            'is_stock_managed' => true,
            'code' => 'RICE-5KG',
        ]);

        $category = Category::create(['name' => 'Grocery', 'status' => 1]);
        $this->rootShop->categories()->attach($category->id);

        $row = [
            0 => 1,
            1 => 'Basmati Rice Premium',
            2 => 'Premium rice',
            3 => 'Fine grain basmati',
            4 => $this->brand->name,
            5 => $this->unit->name,
            6 => $category->name,
            7 => null,
            8 => null,
            9 => null,
            10 => 160.00,
            11 => 140.00,
            12 => 105.00,
            13 => 'RICE-5KG',
            14 => 50,
            15 => 1,
            16 => 'no',
            17 => null,
            18 => null,
            19 => null,
            20 => null,
        ];

        $result = ProductRepository::importRows([$row]);
        $this->assertSame('', $result['errors'][1]['reason'] ?? '');
        $this->assertEquals(1, $result['updated']);

        $product->refresh();
        $this->assertEquals(160.00, $product->price);
        $this->assertEquals(140.00, $product->discount_price);
        $this->assertEquals(105.00, $product->buy_price);
        $this->assertEquals('Basmati Rice Premium', $product->name);
    }

    public function test_discount_and_card_calculations_use_canonical_prices(): void
    {
        // 1. Create Product with initial price ₹200, discount ₹180
        $product = Product::create([
            'name' => 'Cold Pressed Mustard Oil',
            'shop_id' => $this->rootShop->id,
            'price' => 200.00,
            'discount_price' => 180.00,
            'buy_price' => 140.00,
            'quantity' => 20,
            'is_active' => true,
            'is_approve' => true,
            'is_stock_managed' => true,
            'code' => 'OIL-1LTR',
        ]);

        // 2. Admin updates Product: MRP ₹300, Special Discount ₹250
        $request = new ProductRequest([
            'name' => 'Cold Pressed Mustard Oil Pure',
            'price' => 300.00,
            'discount_price' => 250.00,
            'buy_price' => 190.00,
            'code' => 'OIL-1LTR',
            'category' => [],
            'sub_category' => [],
            'color' => [],
            'size' => [],
        ]);

        ProductRepository::updateByRequest($request, $product);

        $product->refresh();
        $this->assertEquals(300.00, $product->price);
        $this->assertEquals(250.00, $product->discount_price);

        // 3. Verify discount percentage calculation
        $discountPct = Product::getDiscountPercentage($product->price, $product->discount_price);
        $this->assertEquals(16.666666666666668, $discountPct);

        // 4. Verify Card Discount calculation on 2 items (Subtotal: 2 * 250 = ₹500)
        // 10% on ₹500 = ₹50.00
        $subtotal = $product->discount_price * 2;
        $cardDiscount = CardRepository::discountFor($subtotal);
        $this->assertEquals(50.00, $cardDiscount);
    }
}
