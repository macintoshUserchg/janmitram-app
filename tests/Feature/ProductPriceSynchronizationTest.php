<?php

namespace Tests\Feature;

use App\Http\Requests\ProductRequest;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Color;
use App\Models\GeneraleSetting;
use App\Models\Product;
use App\Models\Shop;
use App\Models\Size;
use App\Models\Unit;
use App\Models\User;
use App\Models\Warehouse;
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
            'latitude' => 26.9124,
            'longitude' => 75.7873,
        ]);

        $branchUserB = User::factory()->create();
        $this->branchShopB = Shop::create([
            'name' => 'Mansarovar Branch Shop',
            'user_id' => $branchUserB->id,
            'status' => 1,
            'city' => 'Jaipur',
            'latitude' => 26.8500,
            'longitude' => 75.7600,
        ]);

        $this->warehouse = Warehouse::create([
            'name' => 'Central Hub Warehouse',
            'code' => 'WH-TEST-SYNC',
            'is_default' => 1,
            'status' => 1,
        ]);

        $this->unit = Unit::create(['name' => '500 GM', 'is_active' => true, 'shop_id' => $this->rootShop->id]);
        $this->brand = Brand::create(['name' => 'Janmitram Pure', 'is_active' => true, 'shop_id' => $this->rootShop->id]);
        $this->color = Color::create(['name' => 'Standard', 'color_code' => '#ffffff', 'is_active' => true, 'shop_id' => $this->rootShop->id]);
        $this->size = Size::create(['name' => 'Standard Pack', 'is_active' => true, 'shop_id' => $this->rootShop->id]);
    }

    public function test_master_product_price_and_discount_update_cascades_to_all_shop_copies(): void
    {
        // 1. Create Master Product with initial pricing
        $master = Product::create([
            'name' => 'Organic Honey',
            'shop_id' => $this->rootShop->id,
            'master_product_id' => null,
            'price' => 100.00,
            'discount_price' => 90.00,
            'buy_price' => 70.00,
            'quantity' => 0,
            'min_order_quantity' => 1,
            'unit_id' => $this->unit->id,
            'brand_id' => $this->brand->id,
            'is_active' => true,
            'is_approve' => true,
            'is_stock_managed' => true,
            'code' => 'HONEY-500G',
        ]);

        // 2. Clone product to Branch Shop A and Branch Shop B
        $copyA = WarehouseService::cloneMasterToShop($master, $this->branchShopA);
        $copyB = WarehouseService::cloneMasterToShop($master, $this->branchShopB);

        $this->assertEquals(100.00, $copyA->price);
        $this->assertEquals(90.00, $copyA->discount_price);
        $this->assertEquals(70.00, $copyA->buy_price);

        $this->assertEquals(100.00, $copyB->price);
        $this->assertEquals(90.00, $copyB->discount_price);
        $this->assertEquals(70.00, $copyB->buy_price);

        // 3. Admin updates Master Product price: MRP 150, Discount 130, Buy Price 110
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

        ProductRepository::updateByRequest($request, $master);

        // 4. Assert Master Product was updated
        $master->refresh();
        $this->assertEquals(150.00, $master->price);
        $this->assertEquals(130.00, $master->discount_price);
        $this->assertEquals(110.00, $master->buy_price);
        $this->assertEquals(2, $master->min_order_quantity);

        // 5. Assert Shop A and Shop B copies received the cascade update immediately!
        $copyA->refresh();
        $copyB->refresh();

        $this->assertEquals(150.00, $copyA->price);
        $this->assertEquals(130.00, $copyA->discount_price);
        $this->assertEquals(110.00, $copyA->buy_price);
        $this->assertEquals(2, $copyA->min_order_quantity);
        $this->assertEquals('Organic Honey Super', $copyA->name);

        $this->assertEquals(150.00, $copyB->price);
        $this->assertEquals(130.00, $copyB->discount_price);
        $this->assertEquals(110.00, $copyB->buy_price);
        $this->assertEquals(2, $copyB->min_order_quantity);
        $this->assertEquals('Organic Honey Super', $copyB->name);
    }

    public function test_variant_extra_pricing_cascades_to_shop_copies(): void
    {
        $master = Product::create([
            'name' => 'Organic Ghee',
            'shop_id' => $this->rootShop->id,
            'master_product_id' => null,
            'price' => 500.00,
            'discount_price' => 450.00,
            'buy_price' => 380.00,
            'quantity' => 0,
            'is_active' => true,
            'is_approve' => true,
            'is_stock_managed' => true,
            'code' => 'GHEE-1KG',
        ]);

        $master->colors()->attach($this->color->id, ['price' => 10.00]);
        $master->sizes()->attach($this->size->id, ['price' => 50.00]);

        $copyA = WarehouseService::cloneMasterToShop($master, $this->branchShopA);

        $this->assertEquals(10.00, $copyA->colors()->first()->pivot->price);
        $this->assertEquals(50.00, $copyA->sizes()->first()->pivot->price);

        // Admin updates variant extra prices on Master
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

        ProductRepository::updateByRequest($request, $master);

        $copyA->refresh();
        $this->assertEquals(550.00, $copyA->price);
        $this->assertEquals(500.00, $copyA->discount_price);
        $this->assertEquals(420.00, $copyA->buy_price);

        // Verify variant pivots synced to shop copy
        $this->assertEquals(20.00, $copyA->colors()->first()->pivot->price);
        $this->assertEquals(80.00, $copyA->sizes()->first()->pivot->price);
    }

    public function test_clone_master_to_shop_refreshes_existing_copy_pricing_on_subsequent_fulfillment(): void
    {
        $master = Product::create([
            'name' => 'Wheat Flour',
            'shop_id' => $this->rootShop->id,
            'master_product_id' => null,
            'price' => 40.00,
            'discount_price' => 35.00,
            'buy_price' => 28.00,
            'quantity' => 0,
            'is_active' => true,
            'is_approve' => true,
            'is_stock_managed' => true,
            'code' => 'ATTA-1KG',
        ]);

        // First stocking creates the copy
        $copyA = WarehouseService::cloneMasterToShop($master, $this->branchShopA);
        $this->assertEquals(40.00, $copyA->price);

        // Directly change master price in database (e.g. external sync / direct update)
        $master->update([
            'price' => 48.00,
            'discount_price' => 42.00,
            'buy_price' => 34.00,
        ]);

        // Second stocking dispatch runs cloneMasterToShop
        $refreshedCopy = WarehouseService::cloneMasterToShop($master, $this->branchShopA);

        $this->assertEquals($copyA->id, $refreshedCopy->id);
        $this->assertEquals(48.00, $refreshedCopy->price);
        $this->assertEquals(42.00, $refreshedCopy->discount_price);
        $this->assertEquals(34.00, $refreshedCopy->buy_price);
    }

    public function test_bulk_excel_import_price_update_cascades_to_shop_copies(): void
    {
        $master = Product::create([
            'name' => 'Basmati Rice',
            'shop_id' => $this->rootShop->id,
            'master_product_id' => null,
            'price' => 120.00,
            'discount_price' => 110.00,
            'buy_price' => 85.00,
            'quantity' => 0,
            'is_active' => true,
            'is_approve' => true,
            'is_stock_managed' => true,
            'code' => 'RICE-5KG',
        ]);

        $copyA = WarehouseService::cloneMasterToShop($master, $this->branchShopA);
        $this->assertEquals(120.00, $copyA->price);
        $this->assertEquals(110.00, $copyA->discount_price);

        $category = Category::create(['name' => 'Grocery', 'status' => 1]);
        $this->rootShop->categories()->attach($category->id);

        // Match IMPORT_COLUMNS exact 0-based indices:
        // 0: si_no, 1: name, 2: short_description, 3: description, 4: brand, 5: unit, 6: category, 7: sub_category,
        // 8: colors, 9: sizes, 10: price, 11: discount_price, 12: buy_price, 13: sku, 14: quantity, 15: min_order_quantity,
        // 16: is_digital, 17: vat_rate, 18: meta_title, 19: meta_description, 20: meta_keywords
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

        // Assert shop copy received the bulk import update!
        $copyA->refresh();
        $this->assertEquals(160.00, $copyA->price);
        $this->assertEquals(140.00, $copyA->discount_price);
        $this->assertEquals(105.00, $copyA->buy_price);
        $this->assertEquals('Basmati Rice Premium', $copyA->name);
    }

    public function test_discount_and_card_calculations_use_synchronized_shop_prices(): void
    {
        // 1. Create Master Product with initial price ₹200, discount ₹180
        $master = Product::create([
            'name' => 'Cold Pressed Mustard Oil',
            'shop_id' => $this->rootShop->id,
            'master_product_id' => null,
            'price' => 200.00,
            'discount_price' => 180.00,
            'buy_price' => 140.00,
            'quantity' => 0,
            'is_active' => true,
            'is_approve' => true,
            'is_stock_managed' => true,
            'code' => 'OIL-1LTR',
        ]);

        $copyA = WarehouseService::cloneMasterToShop($master, $this->branchShopA);
        $copyA->update(['quantity' => 20]);

        // 2. Admin updates Master Product: MRP ₹300, Special Discount ₹250
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

        ProductRepository::updateByRequest($request, $master);

        $copyA->refresh();
        $this->assertEquals(300.00, $copyA->price);
        $this->assertEquals(250.00, $copyA->discount_price);

        // 3. Verify discount percentage calculation on shop copy
        $discountPct = Product::getDiscountPercentage($copyA->price, $copyA->discount_price);
        $this->assertEquals(16.666666666666668, $discountPct);

        // 4. Verify Card Discount calculation on 2 items (Subtotal: 2 * 250 = ₹500)
        // 10% on ₹500 = ₹50.00
        $subtotal = $copyA->discount_price * 2;
        $cardDiscount = CardRepository::discountFor($subtotal);
        $this->assertEquals(50.00, $cardDiscount);
    }
}
