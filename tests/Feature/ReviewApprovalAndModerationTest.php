<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Models\Address;
use App\Models\Area;
use App\Models\Brand;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Models\Review;
use App\Models\Shop;
use App\Models\ShopInventory;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ReviewApprovalAndModerationTest extends TestCase
{
    use RefreshDatabase;

    private User $customerUser;

    private Customer $customer;

    private User $adminUser;

    private Shop $shop1;

    private Shop $shop2;

    private Product $masterProduct;

    private Product $productCopy1;

    private Product $productCopy2;

    private Order $order1;

    private Order $order2;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['name' => 'root']);
        Role::firstOrCreate(['name' => 'customer']);
        Role::firstOrCreate(['name' => 'shop']);

        $this->adminUser = User::factory()->create(['is_active' => true]);
        $this->adminUser->assignRole('root');

        $this->customerUser = User::factory()->create(['is_active' => true]);
        $this->customerUser->assignRole('customer');
        $this->customer = Customer::factory()->create(['user_id' => $this->customerUser->id]);

        $shopOwner1 = User::factory()->create(['is_active' => true]);
        $shopOwner1->assignRole('shop');
        $this->shop1 = Shop::factory()->create(['user_id' => $shopOwner1->id, 'name' => 'Shop Alpha']);

        $shopOwner2 = User::factory()->create(['is_active' => true]);
        $shopOwner2->assignRole('shop');
        $this->shop2 = Shop::factory()->create(['user_id' => $shopOwner2->id, 'name' => 'Shop Beta']);

        $brand = Brand::create(['name' => 'Organic Brand', 'slug' => 'organic-brand']);
        $unit = Unit::create(['name' => 'pack', 'shop_id' => $this->shop1->id, 'is_active' => true]);

        $this->masterProduct = Product::create([
            'name' => 'Organic Green Tea',
            'brand_id' => $brand->id,
            'unit_id' => $unit->id,
            'price' => 300.0,
            'quantity' => 50,
            'is_stock_managed' => true,
            'is_active' => true,
            'is_approve' => true,
            'is_digital' => false,
        ]);

        ShopInventory::create([
            'shop_id' => $this->shop1->id,
            'product_id' => $this->masterProduct->id,
            'quantity' => 20,
            'is_active' => true,
        ]);

        ShopInventory::create([
            'shop_id' => $this->shop2->id,
            'product_id' => $this->masterProduct->id,
            'quantity' => 20,
            'is_active' => true,
        ]);

        $this->productCopy1 = $this->masterProduct;
        $this->productCopy2 = $this->masterProduct;

        $area = Area::create(['name' => 'Jaipur Central', 'delivery_amount' => 20]);
        $address = Address::create([
            'customer_id' => $this->customer->id,
            'name' => 'Buyer',
            'phone' => '9999999999',
            'area_id' => $area->id,
            'address_line' => 'Main Road',
            'address_type' => 'home',
            'latitude' => 26.9,
            'longitude' => 75.8,
        ]);

        $this->order1 = Order::create([
            'order_code' => 'ORD-1001',
            'customer_id' => $this->customer->id,
            'shop_id' => $this->shop1->id,
            'address_id' => $address->id,
            'order_status' => OrderStatus::DELIVERED,
            'payment_status' => PaymentStatus::PAID,
            'payable_amount' => 300.0,
            'total_amount' => 300.0,
        ]);

        $this->order2 = Order::create([
            'order_code' => 'ORD-1002',
            'customer_id' => $this->customer->id,
            'shop_id' => $this->shop2->id,
            'address_id' => $address->id,
            'order_status' => OrderStatus::DELIVERED,
            'payment_status' => PaymentStatus::PAID,
            'payable_amount' => 300.0,
            'total_amount' => 300.0,
        ]);
    }

    public function test_new_review_is_pending_by_default_and_hidden_from_public_api(): void
    {
        $response = $this->actingAs($this->customerUser, 'sanctum')->postJson('/api/product-review', [
            'product_id' => $this->productCopy1->id,
            'order_id' => $this->order1->id,
            'rating' => 5,
            'description' => 'Superb quality green tea, fresh aroma!',
        ]);

        $response->assertStatus(200);
        $response->assertJsonPath('message', 'Review submitted successfully. It will be published after admin approval.');

        // Verify review exists in DB with is_active = false
        $review = Review::withoutGlobalScopes()->where('product_id', $this->productCopy1->id)->first();
        $this->assertNotNull($review);
        $this->assertFalse((bool) $review->is_active);

        // Public API must NOT return unapproved review
        $publicResponse = $this->getJson('/api/reviews?product_id='.$this->productCopy1->id);
        $publicResponse->assertStatus(200);
        $publicResponse->assertJsonPath('data.total', 0);
        $this->assertEmpty($publicResponse->json('data.reviews'));
    }

    public function test_admin_can_approve_review_and_it_becomes_visible_publicly(): void
    {
        $review = Review::withoutGlobalScopes()->create([
            'customer_id' => $this->customer->id,
            'product_id' => $this->productCopy1->id,
            'shop_id' => $this->shop1->id,
            'order_id' => $this->order1->id,
            'rating' => 5.0,
            'description' => 'Best green tea available.',
            'is_active' => false,
        ]);

        // Admin approves the review
        $response = $this->actingAs($this->adminUser)->post('/admin/review/'.$review->id.'/approve');
        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertTrue((bool) $review->fresh()->is_active);

        // Public API now returns the approved review
        $publicResponse = $this->getJson('/api/reviews?product_id='.$this->productCopy1->id);
        $publicResponse->assertStatus(200);
        $publicResponse->assertJsonPath('data.total', 1);
        $this->assertSame(5.0, (float) $publicResponse->json('data.reviews.0.rating'));
        $this->assertSame('Best green tea available.', $publicResponse->json('data.reviews.0.description'));
    }

    public function test_admin_can_reject_review(): void
    {
        $review = Review::withoutGlobalScopes()->create([
            'customer_id' => $this->customer->id,
            'product_id' => $this->productCopy1->id,
            'shop_id' => $this->shop1->id,
            'order_id' => $this->order1->id,
            'rating' => 1.0,
            'description' => 'Inappropriate content',
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->adminUser)->post('/admin/review/'.$review->id.'/reject');
        $response->assertRedirect();

        $this->assertFalse((bool) $review->fresh()->is_active);
    }

    public function test_admin_can_reply_to_review_and_reply_appears_in_api(): void
    {
        $review = Review::withoutGlobalScopes()->create([
            'customer_id' => $this->customer->id,
            'product_id' => $this->productCopy1->id,
            'shop_id' => $this->shop1->id,
            'order_id' => $this->order1->id,
            'rating' => 5.0,
            'description' => 'Great packaging.',
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->adminUser)->post('/admin/review/'.$review->id.'/reply', [
            'reply' => 'Thank you for choosing Janmitram! We appreciate your feedback.',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertSame('Thank you for choosing Janmitram! We appreciate your feedback.', $review->fresh()->reply);
        $this->assertNotNull($review->fresh()->replied_at);

        // Public API includes official response
        $publicResponse = $this->getJson('/api/reviews?product_id='.$this->productCopy1->id);
        $publicResponse->assertStatus(200);
        $this->assertSame('Thank you for choosing Janmitram! We appreciate your feedback.', $publicResponse->json('data.reviews.0.reply'));
    }

    public function test_master_product_aggregates_approved_reviews_from_all_cloned_shop_copies(): void
    {
        // Approved review on Shop 1 copy (5 stars)
        Review::withoutGlobalScopes()->create([
            'customer_id' => $this->customer->id,
            'product_id' => $this->productCopy1->id,
            'shop_id' => $this->shop1->id,
            'order_id' => $this->order1->id,
            'rating' => 5.0,
            'description' => 'Review for Alpha shop copy.',
            'is_active' => true,
        ]);

        // Approved review on Shop 2 copy (4 stars)
        Review::withoutGlobalScopes()->create([
            'customer_id' => $this->customer->id,
            'product_id' => $this->productCopy2->id,
            'shop_id' => $this->shop2->id,
            'order_id' => $this->order2->id,
            'rating' => 4.0,
            'description' => 'Review for Beta shop copy.',
            'is_active' => true,
        ]);

        // Pending review on Shop 2 copy (should NOT be included)
        Review::withoutGlobalScopes()->create([
            'customer_id' => $this->customer->id,
            'product_id' => $this->productCopy2->id,
            'shop_id' => $this->shop2->id,
            'order_id' => $this->order2->id,
            'rating' => 1.0,
            'description' => 'Pending review.',
            'is_active' => false,
        ]);

        // Query master product reviews
        $response = $this->getJson('/api/reviews?product_id='.$this->masterProduct->id);
        $response->assertStatus(200);
        $response->assertJsonPath('data.total', 2);
        $response->assertJsonPath('data.average_rating_percentage.rating', 4.5);
        $response->assertJsonPath('data.average_rating_percentage.total_review', 2);
    }

    public function test_duplicate_review_on_same_order_item_is_rejected(): void
    {
        Review::withoutGlobalScopes()->create([
            'customer_id' => $this->customer->id,
            'product_id' => $this->productCopy1->id,
            'shop_id' => $this->shop1->id,
            'order_id' => $this->order1->id,
            'rating' => 5.0,
            'description' => 'First review.',
            'is_active' => false,
        ]);

        $response = $this->actingAs($this->customerUser, 'sanctum')->postJson('/api/product-review', [
            'product_id' => $this->productCopy1->id,
            'order_id' => $this->order1->id,
            'rating' => 4,
            'description' => 'Second attempt review.',
        ]);

        $response->assertStatus(200);
        $response->assertJsonPath('message', 'review already exists');
    }
}
