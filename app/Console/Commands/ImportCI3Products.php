<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ImportCI3Products extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:import-ci3-products 
                            {--db-host=127.0.0.1 : CodeIgniter database host} 
                            {--db-name=u939461333_janmitra : CodeIgniter database name} 
                            {--db-user=root : CodeIgniter database username} 
                            {--db-password=root : CodeIgniter database password}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import categories, subcategories, products, and variants from the CodeIgniter 3 database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $host = $this->option('db-host');
        $dbName = $this->option('db-name');
        $username = $this->option('db-user');
        $password = $this->option('db-password');

        $this->info("Establishing connection to CodeIgniter 3 database: {$dbName} on {$host}...");

        // Configure a runtime database connection to CI3 DB
        Config::set('database.connections.old_ci3', [
            'driver' => 'mysql',
            'host' => $host,
            'port' => '3306',
            'database' => $dbName,
            'username' => $username,
            'password' => $password,
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
        ]);

        try {
            $db = DB::connection('old_ci3');
            // Ping to verify connection
            $db->getPdo();
        } catch (\Exception $e) {
            $this->error('Failed to connect to CodeIgniter database: '.$e->getMessage());

            return Command::FAILURE;
        }

        // Check required old tables
        $requiredTables = ['tbl_categories', 'tbl_subcategory', 'jm_productlist', 'jm_multipriceproduct'];
        foreach ($requiredTables as $table) {
            if (! $db->getSchemaBuilder()->hasTable($table)) {
                $this->error("Required table '{$table}' is missing in the source database.");

                return Command::FAILURE;
            }
        }

        $this->info('Migration started...');

        DB::beginTransaction();

        try {
            // 1. Migrate Categories
            $oldCategories = $db->table('tbl_categories')->get();
            $categoryMap = [];
            $this->info('Migrating '.count($oldCategories).' categories...');

            foreach ($oldCategories as $oldCat) {
                // Check if already exists in Laravel categories table (by name)
                $newCatId = DB::table('categories')->where('name', $oldCat->category)->value('id');

                if (! $newCatId) {
                    $newCatId = DB::table('categories')->insertGetId([
                        'name' => $oldCat->category,
                        'name_ar' => null,
                        'type' => 'image',
                        'media_id' => null,
                        'description' => null,
                        'status' => $oldCat->active_status === 'Active' ? 1 : 0,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }

                // Link category to Shop ID 1 (Root Shop) in shop_categories pivot
                $hasShopCat = DB::table('shop_categories')
                    ->where('shop_id', 1)
                    ->where('category_id', $newCatId)
                    ->exists();

                if (! $hasShopCat) {
                    DB::table('shop_categories')->insert([
                        'shop_id' => 1,
                        'category_id' => $newCatId,
                    ]);
                }

                $categoryMap[$oldCat->Id] = $newCatId;
            }

            // 2. Migrate Subcategories
            $oldSubcategories = $db->table('tbl_subcategory')->get();
            $subcategoryMap = [];
            $this->info('Migrating '.count($oldSubcategories).' subcategories...');

            foreach ($oldSubcategories as $oldSub) {
                // Check if already exists (by name and shop_id)
                $newSubId = DB::table('sub_categories')
                    ->where('name', $oldSub->subcategory)
                    ->where('shop_id', 1)
                    ->value('id');

                if (! $newSubId) {
                    $newSubId = DB::table('sub_categories')->insertGetId([
                        'shop_id' => 1,
                        'name' => $oldSub->subcategory,
                        'name_ar' => null,
                        'slug' => Str::slug($oldSub->subcategory),
                        'is_active' => $oldSub->active_status === 'Active' ? 1 : 0,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }

                $subcategoryMap[$oldSub->Id] = $newSubId;

                // Pivot association: Category-Subcategory
                if (isset($categoryMap[$oldSub->category_id])) {
                    $parentId = $categoryMap[$oldSub->category_id];
                    $pivotExists = DB::table('category_subcategories')
                        ->where('category_id', $parentId)
                        ->where('sub_category_id', $newSubId)
                        ->exists();

                    if (! $pivotExists) {
                        DB::table('category_subcategories')->insert([
                            'category_id' => $parentId,
                            'sub_category_id' => $newSubId,
                        ]);
                    }
                }
            }

            // Helper function to find or create a Media row
            $mediaHelper = function ($filename) {
                if (empty($filename)) {
                    return null;
                }

                $mediaId = DB::table('media')->where('name', $filename)->value('id');
                if (! $mediaId) {
                    $ext = pathinfo($filename, PATHINFO_EXTENSION);
                    $mediaId = DB::table('media')->insertGetId([
                        'type' => 'image',
                        'name' => $filename,
                        'original_name' => $filename,
                        'src' => 'products/'.$filename,
                        'extention' => $ext ?: 'jpg',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }

                return $mediaId;
            };

            // 3. Migrate Products & Variants
            $oldProducts = $db->table('jm_productlist')->get();
            $this->info('Migrating '.count($oldProducts).' products...');

            foreach ($oldProducts as $oldProduct) {
                // Get variants for this product
                $variants = $db->table('jm_multipriceproduct')
                    ->where('product_id', $oldProduct->Id)
                    ->get();

                if ($variants->isEmpty()) {
                    $this->warn("Skipping product: '{$oldProduct->product_name}' (ID: {$oldProduct->Id}) - No pricing variants found.");

                    continue;
                }

                // Use the first variant as the base product pricing/inventory values
                $firstVariant = $variants->first();

                // Migrate Main Image
                $mediaId = $mediaHelper($oldProduct->main_image);

                // Check if product already exists (by name and shop_id = 1)
                $productId = DB::table('products')
                    ->where('name', $oldProduct->product_name)
                    ->where('shop_id', 1)
                    ->value('id');

                if (! $productId) {
                    $productId = DB::table('products')->insertGetId([
                        'name' => $oldProduct->product_name,
                        'name_ar' => null,
                        'slug' => Str::slug($oldProduct->product_name),
                        'code' => 'JM-'.str_pad($oldProduct->Id, 5, '0', STR_PAD_LEFT),
                        'shop_id' => 1,
                        'media_id' => $mediaId,
                        'brand_id' => null,
                        'price' => $firstVariant->ProductPrice,
                        'buy_price' => 0,
                        'quantity' => $firstVariant->quantity,
                        'min_order_quantity' => 1,
                        'discount_price' => $firstVariant->discount_price > 0 ? $firstVariant->discount_price : null,
                        'short_description' => null,
                        'short_description_ar' => null,
                        'description' => $oldProduct->product_description,
                        'description_ar' => null,
                        'is_active' => $oldProduct->status === 'Active' ? 1 : 0,
                        'is_digital' => 0,
                        'is_new' => 1,
                        'is_featured' => 0,
                        'is_approve' => 1,
                        'unit_id' => null,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                } else {
                    // Update main image and details just in case
                    DB::table('products')->where('id', $productId)->update([
                        'media_id' => $mediaId,
                        'price' => $firstVariant->ProductPrice,
                        'quantity' => $firstVariant->quantity,
                    ]);
                }

                // Pivot association: Product-Category
                if (isset($categoryMap[$oldProduct->category])) {
                    $catId = $categoryMap[$oldProduct->category];
                    $hasCat = DB::table('product_categories')
                        ->where('product_id', $productId)
                        ->where('category_id', $catId)
                        ->exists();

                    if (! $hasCat) {
                        DB::table('product_categories')->insert([
                            'product_id' => $productId,
                            'category_id' => $catId,
                        ]);
                    }
                }

                // Pivot association: Product-Subcategory
                if (isset($subcategoryMap[$oldProduct->subcategory])) {
                    $subId = $subcategoryMap[$oldProduct->subcategory];
                    $hasSub = DB::table('product_subcategories')
                        ->where('product_id', $productId)
                        ->where('sub_category_id', $subId)
                        ->exists();

                    if (! $hasSub) {
                        DB::table('product_subcategories')->insert([
                            'product_id' => $productId,
                            'sub_category_id' => $subId,
                        ]);
                    }
                }

                // Pivot association: Product-Gallery (Thumbnails)
                $extraImages = [
                    $oldProduct->first_image,
                    $oldProduct->second_image,
                    $oldProduct->third_image,
                    $oldProduct->fourth_image,
                ];

                foreach ($extraImages as $img) {
                    $extraMediaId = $mediaHelper($img);
                    if ($extraMediaId) {
                        $hasThumb = DB::table('product_thumbnails')
                            ->where('product_id', $productId)
                            ->where('media_id', $extraMediaId)
                            ->exists();

                        if (! $hasThumb) {
                            DB::table('product_thumbnails')->insert([
                                'product_id' => $productId,
                                'media_id' => $extraMediaId,
                            ]);
                        }
                    }
                }

                // Migrate Multi-price Variants (Sizes)
                foreach ($variants as $variant) {
                    if (empty($variant->ProductUnit)) {
                        continue;
                    }

                    // Find or create size record
                    $sizeId = DB::table('sizes')
                        ->where('name', $variant->ProductUnit)
                        ->where('shop_id', 1)
                        ->value('id');

                    if (! $sizeId) {
                        $sizeId = DB::table('sizes')->insertGetId([
                            'name' => $variant->ProductUnit,
                            'name_ar' => null,
                            'shop_id' => 1,
                            'is_active' => 1,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }

                    // Link variant in product_sizes pivot table
                    $hasSizePivot = DB::table('product_sizes')
                        ->where('product_id', $productId)
                        ->where('size_id', $sizeId)
                        ->exists();

                    if (! $hasSizePivot) {
                        DB::table('product_sizes')->insert([
                            'product_id' => $productId,
                            'size_id' => $sizeId,
                            'price' => $variant->ProductPrice,
                        ]);
                    }
                }
            }

            DB::commit();
            $this->info('SUCCESS: Product migration completed successfully!');

            return Command::SUCCESS;

        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('ERROR: Product migration failed: '.$e->getMessage());

            return Command::FAILURE;
        }
    }
}
