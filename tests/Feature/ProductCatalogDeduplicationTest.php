<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\GeneraleSetting;
use App\Models\Product;
use App\Models\Shop;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductCatalogDeduplicationTest extends TestCase
{
    use RefreshDatabase;

    public function test_global_products_catalog_deduplicates_products_with_same_name(): void
    {
        GeneraleSetting::create(['shop_type' => 'multi', 'name' => 'Janmitram']);

        $user1 = User::factory()->create(['is_active' => true]);
        $shop1 = Shop::factory()->create(['user_id' => $user1->id, 'status' => true]);

        $user2 = User::factory()->create(['is_active' => true]);
        $shop2 = Shop::factory()->create(['user_id' => $user2->id, 'status' => true]);

        $brand = Brand::create(['name' => 'Test Brand', 'slug' => 'test-brand']);
        $unit = Unit::create(['name' => 'kg', 'shop_id' => $shop1->id, 'is_active' => true]);

        Product::factory()->create([
            'name' => 'Premium Rice 5kg',
            'shop_id' => $shop1->id,
            'unit_id' => $unit->id,
            'is_active' => true,
            'is_approve' => true,
        ]);

        Product::factory()->create([
            'name' => 'Premium Rice 5kg',
            'shop_id' => $shop2->id,
            'unit_id' => $unit->id,
            'is_active' => true,
            'is_approve' => true,
        ]);

        Product::factory()->create([
            'name' => 'Wheat Flour 10kg',
            'shop_id' => $shop1->id,
            'unit_id' => $unit->id,
            'is_active' => true,
            'is_approve' => true,
        ]);

        $response = $this->getJson('/api/products');

        $response->assertStatus(200);

        $response->assertJsonPath('data.total', 2);

        $productNames = collect($response->json('data.products'))->pluck('name');
        $this->assertCount(2, $productNames->unique());
        $this->assertContains('Premium Rice 5kg', $productNames);
        $this->assertContains('Wheat Flour 10kg', $productNames);
    }

    public function test_shop_specific_product_catalog_returns_all_shop_products(): void
    {
        $user1 = User::factory()->create(['is_active' => true]);
        $shop1 = Shop::factory()->create(['user_id' => $user1->id, 'status' => true]);

        $brand = Brand::create(['name' => 'Test Brand', 'slug' => 'test-brand']);
        $unit = Unit::create(['name' => 'kg', 'shop_id' => $shop1->id, 'is_active' => true]);

        Product::factory()->create([
            'name' => 'Premium Rice 5kg',
            'shop_id' => $shop1->id,
            'unit_id' => $unit->id,
            'is_active' => true,
            'is_approve' => true,
        ]);

        Product::factory()->create([
            'name' => 'Wheat Flour 10kg',
            'shop_id' => $shop1->id,
            'unit_id' => $unit->id,
            'is_active' => true,
            'is_approve' => true,
        ]);

        $response = $this->getJson('/api/products?shop_id='.$shop1->id);

        $response->assertStatus(200);
        $response->assertJsonPath('data.total', 2);
    }
}
