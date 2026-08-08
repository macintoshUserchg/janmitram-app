<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\Shop;
use App\Models\User;
use App\Models\VatTax;
use App\Repositories\ProductRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ProductImportExportTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Import/export spreadsheet headers, in IMPORT_COLUMNS order.
     */
    private const HEADERS = [
        'id',
        'name',
        'short_description',
        'description',
        'brand',
        'unit',
        'category',
        'sub_category',
        'colors',
        'sizes',
        'price',
        'discount_price',
        'buy_price',
        'sku',
        'quantity',
        'min_order_quantity',
        'is_digital',
        'vat_rate',
        'meta_title',
        'meta_description',
        'meta_keywords',
    ];

    /**
     * Column name → 0-based index, mirroring ProductRepository::IMPORT_COLUMNS.
     */
    private const COLUMN_INDEX = [
        'id' => 0,
        'name' => 1,
        'short_description' => 2,
        'description' => 3,
        'brand' => 4,
        'unit' => 5,
        'category' => 6,
        'sub_category' => 7,
        'colors' => 8,
        'sizes' => 9,
        'price' => 10,
        'discount_price' => 11,
        'buy_price' => 12,
        'sku' => 13,
        'quantity' => 14,
        'min_order_quantity' => 15,
        'is_digital' => 16,
        'vat_rate' => 17,
        'meta_title' => 18,
        'meta_description' => 19,
        'meta_keywords' => 20,
    ];

    private Shop $rootShop;

    private Category $category;

    private VatTax $vat;

    protected function setUp(): void
    {
        parent::setUp();

        Cache::forget('admin_shop');
        Cache::forget('generale_setting');
    }

    /**
     * Create the root role + a root user owning a shop, and act as them.
     * The root shop is the first root user's shop (generaleSetting('rootShop')).
     */
    private function createRootContext(): void
    {
        Role::findOrCreate('root');

        $user = User::factory()->create();
        $user->assignRole('root');

        $this->rootShop = Shop::factory()->create(['user_id' => $user->id]);
        $user->shop_id = $this->rootShop->id;
        $user->save();

        $this->actingAs($user);

        $this->category = Category::create(['name' => 'Electronics', 'status' => 1]);
        $this->rootShop->categories()->attach($this->category->id);

        $this->vat = VatTax::create(['name' => 'GST 12%', 'percentage' => 12, 'is_active' => 1]);
    }

    /**
     * Create a non-root shop user owning a different shop and act as them.
     */
    private function createNonRootUser(): User
    {
        $user = User::factory()->create();
        Shop::factory()->create(['user_id' => $user->id]);
        $this->actingAs($user);

        return $user;
    }

    /**
     * Build a 21-column import row (index-keyed in IMPORT_COLUMNS order).
     * Overrides are keyed by column name.
     */
    private function row(array $overrides = []): array
    {
        $indexed = [];
        foreach ($overrides as $column => $value) {
            $indexed[self::COLUMN_INDEX[$column]] = $value;
        }

        return array_replace([
            null,               // 0  id
            'Imported Product', // 1  name
            null,               // 2  short_description
            null,               // 3  description
            null,               // 4  brand
            null,               // 5  unit
            'Electronics',      // 6  category
            null,               // 7  sub_category
            null,               // 8  colors
            null,               // 9  sizes
            100,                // 10 price
            null,               // 11 discount_price
            null,               // 12 buy_price
            'SKU-IMPORT-1',     // 13 sku
            5,                  // 14 quantity
            1,                  // 15 min_order_quantity
            0,                  // 16 is_digital
            null,               // 17 vat_rate
            null,               // 18 meta_title
            null,               // 19 meta_description
            null,               // 20 meta_keywords
        ], $indexed);
    }

    /**
     * Build a real minimal xlsx upload containing the given rows.
     */
    private function xlsxUpload(array $rows): UploadedFile
    {
        $spreadsheet = new Spreadsheet;
        $spreadsheet->getActiveSheet()->fromArray(array_merge([self::HEADERS], $rows));

        $writer = new Xlsx($spreadsheet);
        $stream = fopen('php://temp', 'r+');
        $writer->save($stream);
        rewind($stream);
        $bytes = stream_get_contents($stream);
        fclose($stream);

        return UploadedFile::fake()->createWithContent('products.xlsx', $bytes);
    }

    private function makeProduct(string $code): Product
    {
        return Product::create([
            'shop_id' => $this->rootShop->id,
            'name' => 'Existing Product',
            'price' => 10,
            'discount_price' => null,
            'quantity' => 2,
            'min_order_quantity' => 1,
            'is_active' => true,
            'is_digital' => false,
            'is_stock_managed' => true,
            'is_new' => false,
            'is_approve' => true,
            'code' => $code,
        ]);
    }

    public function test_import_creates_product_by_sku(): void
    {
        $this->createRootContext();

        $result = ProductRepository::importRows([$this->row(['sku' => 'SKU-A1'])]);

        $this->assertSame(['imported' => 1, 'updated' => 0, 'failed' => 0, 'errors' => []], $result);

        $product = Product::where('code', 'SKU-A1')->first();
        $this->assertNotNull($product);
        $this->assertSame($this->rootShop->id, $product->shop_id);
        $this->assertSame('Imported Product', $product->name);
        $this->assertSame(100.0, (float) $product->price);
        $this->assertSame(5, $product->quantity);
        $this->assertTrue($product->categories()->get()->contains('id', $this->category->id));
    }

    public function test_import_updates_existing_product_by_sku(): void
    {
        $this->createRootContext();
        $existing = $this->makeProduct('SKU-A1');

        $result = ProductRepository::importRows([
            $this->row(['sku' => 'SKU-A1', 'name' => 'Renamed', 'price' => 250, 'quantity' => 7]),
        ]);

        $this->assertSame(['imported' => 0, 'updated' => 1, 'failed' => 0, 'errors' => []], $result);
        $this->assertDatabaseCount('products', 1);

        $existing->refresh();
        $this->assertSame('Renamed', $existing->name);
        $this->assertSame(250.0, (float) $existing->price);
        $this->assertSame(7, $existing->quantity);
        $this->assertTrue($existing->categories()->get()->contains('id', $this->category->id));
    }

    public function test_import_rejects_missing_sku(): void
    {
        $this->createRootContext();

        $result = ProductRepository::importRows([$this->row(['sku' => ''])]);

        $this->assertSame(0, $result['imported']);
        $this->assertSame(1, $result['failed']);
        $this->assertSame('Missing or empty SKU', $result['errors'][1]['reason']);
        $this->assertDatabaseCount('products', 0);
    }

    public function test_import_maps_vat_rate_name_to_vat_tax(): void
    {
        $this->createRootContext();

        $result = ProductRepository::importRows([$this->row(['sku' => 'SKU-VAT', 'vat_rate' => 'GST 12%'])]);

        $this->assertSame(1, $result['imported']);

        $product = Product::where('code', 'SKU-VAT')->first();
        $this->assertNotNull($product);
        $this->assertTrue($product->vatTaxes()->pluck('vat_taxes.id')->contains($this->vat->id));
    }

    public function test_import_records_per_row_error_reasons(): void
    {
        $this->createRootContext();

        $result = ProductRepository::importRows([
            $this->row(['sku' => 'SKU-NO-NAME', 'name' => '']),
            $this->row(['sku' => 'SKU-BAD-PRICE', 'price' => 'abc']),
            $this->row(['sku' => 'SKU-NO-CATEGORY', 'category' => 'Does Not Exist']),
            $this->row(['sku' => 'SKU-BAD-VAT', 'vat_rate' => 'VAT 999%']),
            $this->row(['sku' => 'SKU-GOOD']),
        ]);

        $this->assertSame(1, $result['imported']);
        $this->assertSame(4, $result['failed']);
        $this->assertCount(4, $result['errors']);

        $this->assertSame('Missing product name', $result['errors'][1]['reason']);
        $this->assertSame('Price must be numeric', $result['errors'][2]['reason']);
        $this->assertSame('No valid category', $result['errors'][3]['reason']);
        $this->assertSame('Unknown VAT rate', $result['errors'][4]['reason']);
        $this->assertArrayNotHasKey(5, $result['errors']);

        $this->assertDatabaseHas('products', ['code' => 'SKU-GOOD']);
        $this->assertDatabaseMissing('products', ['code' => 'SKU-NO-NAME']);
        $this->assertDatabaseMissing('products', ['code' => 'SKU-BAD-PRICE']);
        $this->assertDatabaseMissing('products', ['code' => 'SKU-NO-CATEGORY']);
        $this->assertDatabaseMissing('products', ['code' => 'SKU-BAD-VAT']);
    }

    public function test_import_store_creates_product_via_xlsx(): void
    {
        $this->createRootContext();

        $response = $this->post(route('shop.bulk-product-import.store'), [
            'file' => $this->xlsxUpload([$this->row(['sku' => 'SKU-HTTP'])]),
        ]);

        $response->assertStatus(302);
        $response->assertSessionHas('success', 'Imported 1, updated 0, failed 0');
        $this->assertDatabaseHas('products', ['code' => 'SKU-HTTP', 'name' => 'Imported Product']);
    }

    public function test_import_store_writes_error_file(): void
    {
        $this->createRootContext();
        Storage::fake('local');

        $response = $this->post(route('shop.bulk-product-import.store'), [
            'file' => $this->xlsxUpload([$this->row(['sku' => ''])]),
        ]);

        $response->assertStatus(302);
        $response->assertSessionHas('errors_file');

        $filename = session('errors_file');
        Storage::disk('local')->assertExists('import-errors/'.$filename);
    }

    public function test_export_returns_products_xlsx(): void
    {
        $this->createRootContext();
        $this->makeProduct('SKU-EXPORT');

        $response = $this->post(route('shop.bulk-product-export.export'));

        $response->assertStatus(200);
        $this->assertStringContainsString('products.xlsx', $response->headers->get('content-disposition'));
    }

    public function test_demo_export_returns_demo_template(): void
    {
        $this->createRootContext();

        $response = $this->get(route('shop.bulk-product-export.demo'));

        $response->assertStatus(200);
        $this->assertStringContainsString('demo-template.xlsx', $response->headers->get('content-disposition'));
    }

    public function test_non_root_shop_user_is_blocked(): void
    {
        $this->createRootContext();
        $this->createNonRootUser();

        $this->get(route('shop.bulk-product-import.index'))->assertForbidden();
        $this->post(route('shop.bulk-product-import.store'), [
            'file' => $this->xlsxUpload([$this->row([])]),
        ])->assertForbidden();
        $this->get(route('shop.bulk-product-import.errors', 'products.xlsx'))->assertForbidden();

        $this->get(route('shop.bulk-product-export.index'))->assertForbidden();
        $this->post(route('shop.bulk-product-export.export'))->assertForbidden();
        $this->get(route('shop.bulk-product-export.demo'))->assertForbidden();
    }
}
