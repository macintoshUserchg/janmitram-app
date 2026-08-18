<?php

namespace Tests\Feature;

use App\Enums\PaymentMethod;
use App\Http\Requests\OrderRequest;
use App\Models\Address;
use App\Models\Area;
use App\Models\Brand;
use App\Models\Card;
use App\Models\Cart;
use App\Models\Customer;
use App\Models\Order;
use App\Models\PosCart;
use App\Models\Product;
use App\Models\Shop;
use App\Models\Unit;
use App\Models\User;
use App\Repositories\CardRepository;
use App\Repositories\OrderRepository;
use App\Repositories\PosCartRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class CardTest extends TestCase
{
    use RefreshDatabase;

    public function test_card_gives_10_percent_discount_online(): void
    {
        [$shop, $product, $customerUser, $customer, $address, $card] = $this->makeOrderFixture($price = 500);

        $this->placeOrderWithCard($customerUser, $address, $shop->id, $card->card_number);

        $order = Order::latest('id')->first();
        $this->assertSame($card->id, $order->card_id);
        $this->assertSame(50.0, (float) $order->card_discount); // 10% of 500
        $this->assertSame(0.0, (float) $order->coupon_discount);
    }

    public function test_card_below_minimum_order_gets_no_discount(): void
    {
        [$shop, $product, $customerUser, $customer, $address, $card] = $this->makeOrderFixture($price = 400);

        $this->placeOrderWithCard($customerUser, $address, $shop->id, $card->card_number);

        $order = Order::latest('id')->first();
        $this->assertSame(0.0, (float) $order->card_discount); // 400 < min 500
    }

    public function test_card_not_owned_by_customer_gets_no_discount_online(): void
    {
        [$shop, $product, $customerUser, $customer, $address] = $this->makeOrderFixture($price = 500);

        // a card owned by a different customer
        $otherUser = User::factory()->create();
        $otherCustomer = Customer::factory()->create(['user_id' => $otherUser->id]);
        $otherCard = CardRepository::createForCustomer($otherCustomer->id);

        $this->placeOrderWithCard($customerUser, $address, $shop->id, $otherCard->card_number);

        $order = Order::latest('id')->first();
        $this->assertSame(0.0, (float) $order->card_discount);
        $this->assertNull($order->card_id);
    }

    public function test_inactive_card_gets_no_discount(): void
    {
        [$shop, $product, $customerUser, $customer, $address, $card] = $this->makeOrderFixture($price = 500);

        $card->update(['is_active' => false]);

        $this->placeOrderWithCard($customerUser, $address, $shop->id, $card->card_number);

        $order = Order::latest('id')->first();
        $this->assertSame(0.0, (float) $order->card_discount);
    }

    public function test_one_active_card_per_customer(): void
    {
        [$shop, $product, $customerUser, $customer, $address] = $this->makeOrderFixture($price = 500);

        $first = CardRepository::createForCustomer($customer->id);
        $second = CardRepository::createForCustomer($customer->id);

        $this->assertFalse($first->fresh()->is_active);
        $this->assertTrue($second->fresh()->is_active);
        $this->assertSame(1, Card::where('customer_id', $customer->id)->where('is_active', true)->count());
    }

    public function test_pos_card_applies_discount(): void
    {
        [$shop, $product, $customerUser, $customer, $address, $card] = $this->makeOrderFixture($price = 600);

        $posCart = PosCart::create([
            'name' => 'cart-1',
            'shop_id' => $shop->id,
            'subtotal' => 600,
            'total' => 600,
            'discount' => 0,
        ]);

        $posCart = PosCartRepository::applyCard($posCart, $card);

        $this->assertSame($card->id, $posCart->card_id);
        $this->assertSame(60.0, (float) $posCart->discount); // 10% of 600
        $this->assertSame(540.0, (float) $posCart->total);
    }

    public function test_admin_cards_index_renders_with_metrics_and_sorting(): void
    {
        Role::firstOrCreate(['name' => 'root']);
        Role::firstOrCreate(['name' => 'customer']);
        $rootUser = User::factory()->create();
        $rootUser->assignRole('root');
        $this->actingAs($rootUser);

        $customerUser = User::factory()->create(['name' => 'John Doe']);
        $customer = Customer::factory()->create(['user_id' => $customerUser->id]);
        $card = CardRepository::createForCustomer($customer->id);

        $response = $this->get(route('admin.cards.index', [
            'search' => 'John',
            'status' => 'active',
            'assignment' => 'assigned',
            'sort' => 'id',
            'direction' => 'desc',
        ]));

        $response->assertStatus(200);
        $response->assertSee('John Doe');
        $response->assertSee($card->card_number);
        $response->assertSee(route('admin.cards.download', $card->id));
    }

    public function test_admin_can_download_card_pdf(): void
    {
        Role::firstOrCreate(['name' => 'root']);
        $rootUser = User::factory()->create();
        $rootUser->assignRole('root');
        $this->actingAs($rootUser);

        $card = CardRepository::create([
            'card_number' => CardRepository::generateUniqueNumber(),
            'is_active' => true,
        ]);

        $response = $this->get(route('admin.cards.download', $card->id));

        $response->assertStatus(200);
        $this->assertSame('application/pdf', $response->headers->get('Content-Type'));
        $this->assertStringContainsString('attachment', (string) $response->headers->get('Content-Disposition'));
    }

    public function test_admin_can_preview_card_pdf(): void
    {
        Role::firstOrCreate(['name' => 'root']);
        $rootUser = User::factory()->create();
        $rootUser->assignRole('root');
        $this->actingAs($rootUser);

        $card = CardRepository::create([
            'card_number' => CardRepository::generateUniqueNumber(),
            'is_active' => true,
        ]);

        $response = $this->get(route('admin.cards.download', [$card->id, 'preview' => 1]));

        $response->assertStatus(200);
        $this->assertSame('application/pdf', $response->headers->get('Content-Type'));
        $this->assertStringContainsString('inline', (string) $response->headers->get('Content-Disposition'));
    }

    private function makeOrderFixture(int $price): array
    {
        Cache::forget('generale_setting');
        Role::create(['name' => 'customer']);
        Area::factory()->create();
        $shop = Shop::factory()->create(['delivery_charge' => 0]);
        $shop->user->update(['is_active' => true]);
        Brand::create(['name' => 'Test Brand', 'slug' => 'test-brand']);
        $unit = Unit::create(['name' => 'kg', 'shop_id' => $shop->id, 'is_active' => true]);
        $product = Product::factory()->create([
            'shop_id' => $shop->id, 'unit_id' => $unit->id,
            'price' => $price, 'discount_price' => 0, 'quantity' => 10, 'is_active' => true, 'is_approve' => true,
        ]);

        $customerUser = User::factory()->create();
        $customer = Customer::factory()->create(['user_id' => $customerUser->id]);
        $address = Address::factory()->create(['customer_id' => $customer->id, 'latitude' => 26.9, 'longitude' => 75.8]);
        Cart::create(['customer_id' => $customer->id, 'shop_id' => $shop->id, 'product_id' => $product->id, 'quantity' => 1]);

        $card = CardRepository::createForCustomer($customer->id);

        return [$shop, $product, $customerUser, $customer, $address, $card];
    }

    private function placeOrderWithCard(User $customerUser, Address $address, int $shopId, string $cardNumber): void
    {
        $request = Request::create('/api/place-order', 'POST', [
            'shop_ids' => [$shopId],
            'address_id' => $address->id,
            'payment_method' => 'cash',
            'card_number' => $cardNumber,
        ]);

        $this->actingAs($customerUser);
        $orderRequest = OrderRequest::createFromBase($request);
        $this->app->instance('request', $orderRequest);

        $carts = userCart($orderRequest)->get();

        OrderRepository::storeByRequestFromCart($orderRequest, PaymentMethod::CASH, $carts);
    }
}
