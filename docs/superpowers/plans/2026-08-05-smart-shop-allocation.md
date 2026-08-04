# Smart Shop Allocation for Orders — Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** When a customer places an order (cart checkout, Buy-Now, or re-order), allocate each product line to the **nearest shop** (haversine distance from the customer's address to the shop's lat/lng, within a configurable radius) whose copy of the product has enough stock — creating one order per allocated shop, with delivery charge from the allocated shop.

**Architecture:** Master products are copied per shop (`Product.master_product_id`, created by `WarehouseService::cloneMasterToShop`); each shop copy's `quantity` column is its available stock. A new allocation step in `OrderRepository` replaces the cart's pinned `shop_id` grouping: per line, it finds all copies of the same master product with `quantity >= line.quantity`, ranks them by haversine distance, applies the radius cap, and picks the nearest (or the customer's explicit pick). Lines with no in-radius shop return a **candidate list** the customer picks from. Order creation/payment/one-order-per-shop mechanics are unchanged; the only other backend change is an atomic stock-decrement guard. Addresses must carry lat/lng (map-picked); otherwise checkout is rejected with `422`.

**Tech Stack:** Laravel 11 (PHP 8.2), MySQL, Vue 3 SPA (Vite), PHPUnit (feature tests), Laravel Dusk (browser tests), `vendor/bin/pint` for formatting.

## Global Constraints

- PHP 8.2; use typed signatures, `readonly` only where existing code does, follow the existing Repository/controller style.
- Do NOT touch the POS flow (`Shop\POSController` / `POSController::index`) — POS stays shop-local, no allocation.
- Do NOT add new composer/npm dependencies. Haversine is implemented inline (PHP), no geo library.
- Follow existing naming: helpers in `app/helpers.php` as `if (! function_exists(...))`; methods on `OrderRepository` follow the static-method style.
- Single-vendor mode (`generaleSetting('setting')->shop_type === 'single'`) MUST skip allocation and keep current behavior.
- Every PHP change: run `vendor/bin/pint --dirty --format agent` before committing.
- Every task commits independently with a `feat:`/`fix:` message ending in the Co-Authored-By trailer.

---

### Task 1: Haversine distance helper

**Files:**
- Modify: `app/helpers.php` (append a new helper)
- Test: `tests/Feature/ShopAllocationTest.php` (create)

**Interfaces:**
- Produces: `function haversineKm($lat1, $lng1, $lat2, $lng2): float` — straight-line great-circle distance in km.

- [ ] **Step 1: Create the test file with a failing haversine test**

Create `tests/Feature/ShopAllocationTest.php`:

```php
<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShopAllocationTest extends TestCase
{
    use RefreshDatabase;

    public function test_haversine_km_returns_expected_distance(): void
    {
        $this->assertSame(0.0, haversineKm(26.9, 75.8, 26.9, 75.8));

        // Delhi (28.6139, 77.2090) -> Jaipur (26.9124, 75.7873) ≈ 239 km
        $km = haversineKm(28.6139, 77.2090, 26.9124, 75.7873);
        $this->assertEqualsWithDelta(239.0, $km, 5.0);
    }
}
```

- [ ] **Step 2: Run the test to verify it fails**

Run: `php artisan test --compact tests/Feature/ShopAllocationTest.php`
Expected: FAIL — "Call to undefined function haversineKm()".

- [ ] **Step 3: Add the helper to `app/helpers.php`**

Append at the end of the file:

```php
if (! function_exists('haversineKm')) {
    /**
     * Great-circle distance between two coordinates, in kilometres.
     */
    function haversineKm($lat1, $lng1, $lat2, $lng2): float
    {
        $earthRadiusKm = 6371.0;
        $dLat = deg2rad((float) $lat2 - (float) $lat1);
        $dLng = deg2rad((float) $lng2 - (float) $lng1);
        $a = sin($dLat / 2) ** 2
            + cos(deg2rad((float) $lat1)) * cos(deg2rad((float) $lat2)) * sin($dLng / 2) ** 2;

        return $earthRadiusKm * 2 * atan2(sqrt($a), sqrt(1 - $a));
    }
}
```

- [ ] **Step 4: Run the test to verify it passes**

Run: `php artisan test --compact tests/Feature/ShopAllocationTest.php`
Expected: PASS (1 passed).

- [ ] **Step 5: Commit**

```bash
git add app/helpers.php tests/Feature/ShopAllocationTest.php
git commit -m "feat: add haversineKm distance helper

Co-Authored-By: Claude Fable 5 <noreply@anthropic.com>"
```

---

### Task 2: Candidate-shop lookup + nearest-shop allocation in OrderRepository

**Files:**
- Modify: `app/Repositories/OrderRepository.php`
- Test: `tests/Feature/ShopAllocationTest.php`

**Interfaces:**
- Consumes: `haversineKm()` (Task 1).
- Produces:
  - `public static function candidateShopsForLine(Product $product, int $qty, Address $address): Collection` — returns a collection of stdClass objects sorted by `distance_km` (ascending), each: `{ product_id:int, shop_id:int, name:string, logo:string, distance_km:float, available_quantity:int, price:float, delivery_charge:float, radius_eligible:bool }`.
  - `private static function allocateNearestShop(Product $product, int $qty, Address $address, ?int $overrideShopId = null): ?Product` — nearest in-radius copy, or the override shop's copy (override bypasses the radius cap but must still pass the stock filter), or `null` when nothing matches.

- [ ] **Step 1: Add imports and the two methods to `OrderRepository`**

Add to the `use` block of `app/Repositories/OrderRepository.php`:

```php
use App\Models\Address;
use App\Models\GeneraleSetting;
use App\Models\Product;
use Illuminate\Support\Collection;
```

(`Address` and `GeneraleSetting` are already imported — keep single occurrences; `Product` and `Collection` are new.)

Add these methods (e.g. right after `storeByRequestFromCart`):

```php
    /**
     * Eligible shops for a product line, ranked by haversine distance from the
     * delivery address. Only shop copies of the same master product with enough
     * stock are considered.
     *
     * @return Collection<int, object> candidate objects (see candidateForCopy)
     */
    public static function candidateShopsForLine(Product $product, int $qty, Address $address): Collection
    {
        $masterId = $product->master_product_id ?? $product->id;
        $radius = (float) (GeneraleSetting::first()?->shop_allocation_radius_km ?? 50.0);

        return Product::query()
            ->where(fn ($q) => $q->where('id', $masterId)->orWhere('master_product_id', $masterId))
            ->isActive()
            ->where('quantity', '>=', $qty)
            ->with('shop')
            ->get()
            ->map(fn (Product $copy) => self::candidateForCopy($copy, $address, $radius))
            ->filter()
            ->sortBy('distance_km')
            ->values();
    }

    private static function candidateForCopy(Product $copy, Address $address, float $radius): ?object
    {
        $shop = $copy->shop;

        if (! $shop || ! $shop->latitude || ! $shop->longitude) {
            return null;
        }

        $distance = haversineKm($address->latitude, $address->longitude, $shop->latitude, $shop->longitude);

        return (object) [
            'product_id' => (int) $copy->id,
            'shop_id' => (int) $shop->id,
            'name' => $shop->name,
            'logo' => $shop->logo,
            'distance_km' => round($distance, 2),
            'available_quantity' => (int) $copy->quantity,
            'price' => (float) ($copy->discount_price > 0 ? $copy->discount_price : $copy->price),
            'delivery_charge' => (float) ($shop->delivery_charge ?? 0),
            'radius_eligible' => $distance <= $radius,
        ];
    }

    private static function allocateNearestShop(Product $product, int $qty, Address $address, ?int $overrideShopId = null): ?Product
    {
        $candidates = self::candidateShopsForLine($product, $qty, $address);

        if ($overrideShopId) {
            $pick = $candidates->first(fn ($c) => (int) $c->shop_id === (int) $overrideShopId);

            return $pick ? Product::find($pick->product_id) : null;
        }

        $nearest = $candidates->firstWhere('radius_eligible', true);

        return $nearest ? Product::find($nearest->product_id) : null;
    }
```

- [ ] **Step 2: Write the failing tests for candidate ranking and allocation**

Add to `tests/Feature/ShopAllocationTest.php` a private setup helper and three tests:

```php
    private function masterWithTwoCopies(): array
    {
        $nearShop = \App\Models\Shop::factory()->create(['latitude' => 26.91, 'longitude' => 75.79, 'delivery_charge' => 20]);
        $farShop = \App\Models\Shop::factory()->create(['latitude' => 28.61, 'longitude' => 77.21, 'delivery_charge' => 80]);
        // shops factory must have an active user; ensure is_active on both users
        $nearShop->user->update(['is_active' => true]);
        $farShop->user->update(['is_active' => true]);

        $master = \App\Models\Product::factory()->create([
            'shop_id' => $nearShop->id,
            'quantity' => 10,
            'is_active' => true,
            'is_approve' => true,
        ]);
        $copy = \App\Models\Product::factory()->create([
            'shop_id' => $farShop->id,
            'master_product_id' => $master->id,
            'quantity' => 10,
            'is_active' => true,
            'is_approve' => true,
        ]);

        return [$master, $copy, $nearShop, $farShop];
    }

    public function test_candidate_shops_are_ranked_by_distance(): void
    {
        [$master, $copy, $nearShop, $farShop] = $this->masterWithTwoCopies();
        $address = \App\Models\Address::factory()->create(['latitude' => 26.9, 'longitude' => 75.8]);

        $candidates = \App\Repositories\OrderRepository::candidateShopsForLine($master, 2, $address);

        $this->assertCount(2, $candidates);
        $this->assertSame($nearShop->id, $candidates[0]->shop_id);
        $this->assertSame($farShop->id, $candidates[1]->shop_id);
        $this->assertTrue($candidates[0]->radius_eligible);
        $this->assertFalse($candidates[1]->radius_eligible);
        $this->assertSame(20.0, $candidates[0]->delivery_charge);
    }

    public function test_allocate_nearest_shop_picks_in_radius_copy(): void
    {
        [$master, $copy, $nearShop, $farShop] = $this->masterWithTwoCopies();
        $address = \App\Models\Address::factory()->create(['latitude' => 26.9, 'longitude' => 75.8]);

        $allocated = $this->invokePrivate('allocateNearestShop', [$master, 2, $address]);

        $this->assertNotNull($allocated);
        $this->assertSame($nearShop->id, $allocated->shop_id);
    }

    public function test_allocate_honours_override_pick(): void
    {
        [$master, $copy, $nearShop, $farShop] = $this->masterWithTwoCopies();
        $address = \App\Models\Address::factory()->create(['latitude' => 26.9, 'longitude' => 75.8]);

        $allocated = $this->invokePrivate('allocateNearestShop', [$master, 2, $address, $farShop->id]);

        $this->assertNotNull($allocated);
        $this->assertSame($farShop->id, $allocated->shop_id);
    }

    private function invokePrivate(string $method, array $args)
    {
        $ref = new \ReflectionMethod(\App\Repositories\OrderRepository::class, $method);
        $ref->setAccessible(true);

        return $ref->invokeArgs(null, $args);
    }
```

- [ ] **Step 3: Run the tests to verify they fail**

Run: `php artisan test --compact tests/Feature/ShopAllocationTest.php`
Expected: FAIL — the three new tests error ("Call to undefined method OrderRepository::candidateShopsForLine()") until Step 1's methods exist; if you wrote methods first, they pass — run TDD order (test → fail → implement → pass) by writing the tests before the methods.

- [ ] **Step 4: Implement the methods (Step 1) and run tests**

Run: `php artisan test --compact tests/Feature/ShopAllocationTest.php`
Expected: PASS (4 passed).

- [ ] **Step 5: Commit**

```bash
git add app/Repositories/OrderRepository.php tests/Feature/ShopAllocationTest.php
git commit -m "feat: rank shop copies by distance for order allocation

Co-Authored-By: Claude Fable 5 <noreply@anthropic.com>"
```

---

### Task 3: Wire allocation into order creation + atomic stock guard

**Files:**
- Create: `app/Exceptions/UnfulfillableOrderException.php`
- Modify: `app/Repositories/OrderRepository.php` (`storeByRequestFromCart`, `getCartWiseAmounts`)
- Test: `tests/Feature/ShopAllocationTest.php`

**Interfaces:**
- Consumes: `allocateNearestShop`, `candidateShopsForLine` (Task 2); `haversineKm` (Task 1).
- Produces: `class UnfulfillableOrderException extends \Exception` with `public array $unfulfillable` — thrown when at least one line has no allocatable shop (caught by `OrderController@store` in Task 4).

- [ ] **Step 1: Create the exception**

Create `app/Exceptions/UnfulfillableOrderException.php`:

```php
<?php

namespace App\Exceptions;

use Exception;

class UnfulfillableOrderException extends Exception
{
    public array $unfulfillable;

    public function __construct(array $unfulfillable)
    {
        parent::__construct(__('Some items cannot be delivered to your area'));
        $this->unfulfillable = $unfulfillable;
    }
}
```

- [ ] **Step 2: Refactor `storeByRequestFromCart` to allocate before grouping**

Replace the grouping preamble in `storeByRequestFromCart` (currently `$shopProducts = $carts->groupBy('shop_id');`):

```php
        $tokens = cartAccessToken(request());
        $customer = Customer::firstWhere('id', $tokens['customer_id']);

        $isMultiVendor = generaleSetting('setting')?->shop_type === 'multi';
        $overrides = collect($request->allocations ?? [])->keyBy('product_id');
        $address = Address::find($request->address_id);

        $shopLines = [];
        $unfulfillable = [];

        foreach ($carts as $cart) {
            if (! $isMultiVendor) {
                $shopLines[$cart->shop_id][] = ['cart' => $cart, 'copy' => $cart->product];
                continue;
            }

            $allocated = self::allocateNearestShop(
                $cart->product,
                (int) $cart->quantity,
                $address,
                $overrides->get($cart->product_id)?->shop_id,
            );

            if (! $allocated) {
                $unfulfillable[$cart->product_id] = self::candidateShopsForLine($cart->product, (int) $cart->quantity, $address);
                continue;
            }

            $shopLines[$allocated->shop_id][] = ['cart' => $cart, 'copy' => $allocated];
        }

        if (! empty($unfulfillable)) {
            throw new UnfulfillableOrderException($unfulfillable);
        }

        $shopProducts = $shopLines;
```

Then inside the `foreach ($shopProducts as $shopId => $cartProducts)` loop, change the per-line body so it unpacks the line and prices from the allocated copy:

Replace the first two lines of the inner `foreach ($cartProducts as $cart)` with:

```php
            foreach ($cartProducts as $line) {
                $cart = $line['cart'];
                $product = $line['copy'];
```

(Delete the old `$product = $cart->product;` line — `$product` now comes from the line.)

Replace the stock decrement line `$cart->product->decrement('quantity', $cart->quantity);` with the **atomic guard**:

```php
                $decremented = Product::query()
                    ->whereKey($product->id)
                    ->where('quantity', '>=', $cart->quantity)
                    ->decrement('quantity', $cart->quantity);

                if (! $decremented) {
                    throw new \RuntimeException(__('Sorry, this product is no longer available in the required quantity'));
                }
```

(All subsequent references to `$product->...` in that loop already operate on the allocated copy — sizes/colors/flash-sale/license/warehouse logic now price and deplete the allocated shop's copy. The `attach` writes `$product->id` — the allocated copy id — which is correct.)

- [ ] **Step 3: Make `getCartWiseAmounts` price from the allocated copies and use the shop's delivery charge**

In `getCartWiseAmounts`, replace the delivery-charge line `$deliveryCharge = self::getDeliveryAmount();` with:

```php
        $deliveryCharge = $shop->delivery_charge > 0 ? (float) $shop->delivery_charge : self::getDeliveryAmount();
```

Then change the loop preamble from `$product = $cart->product;` to unpack the line (the method now receives `$carts` as a collection of `['cart', 'copy']` lines):

```php
        foreach ($carts ?? [] as $line) {
            $cart = $line['cart'];
            $product = $line['copy'];

            if (! $cart) {
                continue;
            }
```

(Keep the rest of the loop — qty/size/color pricing — unchanged. Note `$orderQty = $carts->sum('quantity');` must become `$carts->sum(fn ($l) => $l['cart']->quantity);`.)

- [ ] **Step 4: Update the `storeByRequestFromCart` call sites that pass raw cart collections**

`getCartWiseAmounts($shop, collect($cartProducts), $request->coupon_code)` in `storeByRequestFromCart` already receives `$cartProducts` (now `['cart','copy']` lines), so it needs no change there.

- [ ] **Step 5: Write the failing feature tests**

Add to `tests/Feature/ShopAllocationTest.php`:

```php
    public function test_order_goes_to_allocated_shop_and_uses_its_delivery_charge(): void
    {
        [$master, $copy, $nearShop, $farShop] = $this->masterWithTwoCopies();
        $customerUser = \App\Models\User::factory()->create();
        $customer = \App\Models\Customer::factory()->create(['user_id' => $customerUser->id]);
        $address = \App\Models\Address::factory()->create([
            'customer_id' => $customer->id,
            'latitude' => 26.9,
            'longitude' => 75.8,
        ]);
        \App\Models\Cart::create([
            'customer_id' => $customer->id,
            'shop_id' => $farShop->id,   // pinned to the FAR shop at add-to-cart
            'product_id' => $master->id,
            'quantity' => 2,
        ]);

        $payment = $this->placeOrder($customerUser, $address, $nearShop->id);

        $order = $payment->orders->first();
        $this->assertNotNull($order);
        $this->assertSame($nearShop->id, $order->shop_id);          // allocated to NEAR shop
        $this->assertSame(20.0, (float) $order->delivery_charge);   // near shop's charge
        $this->assertSame(2, (int) $order->products->first()->pivot->quantity);
    }

    public function test_unfulfillable_line_throws_with_candidates(): void
    {
        [$master, $copy, $nearShop, $farShop] = $this->masterWithTwoCopies();
        // move the near shop copy out of stock
        $master->update(['quantity' => 0]);
        $customerUser = \App\Models\User::factory()->create();
        $customer = \App\Models\Customer::factory()->create(['user_id' => $customerUser->id]);
        $address = \App\Models\Address::factory()->create([
            'customer_id' => $customer->id,
            'latitude' => 26.9,
            'longitude' => 75.8,
        ]);
        \App\Models\Cart::create([
            'customer_id' => $customer->id,
            'shop_id' => $nearShop->id,
            'product_id' => $master->id,
            'quantity' => 2,
        ]);

        try {
            $this->placeOrder($customerUser, $address, $nearShop->id);
            $this->fail('Expected UnfulfillableOrderException');
        } catch (\App\Exceptions\UnfulfillableOrderException $e) {
            $this->assertArrayHasKey($master->id, $e->unfulfillable);
        }
    }

    public function test_atomic_decrement_rejects_oversell(): void
    {
        $shop = \App\Models\Shop::factory()->create();
        $shop->user->update(['is_active' => true]);
        $product = \App\Models\Product::factory()->create([
            'shop_id' => $shop->id,
            'quantity' => 3,
            'is_active' => true,
            'is_approve' => true,
        ]);

        $first = \App\Models\Product::query()
            ->whereKey($product->id)->where('quantity', '>=', 3)->decrement('quantity', 3);
        $second = \App\Models\Product::query()
            ->whereKey($product->id)->where('quantity', '>=', 3)->decrement('quantity', 3);

        $this->assertSame(1, $first);
        $this->assertSame(0, $second);
        $this->assertSame(0, (int) $product->fresh()->quantity);
    }

    private function placeOrder(\App\Models\User $customerUser, \App\Models\Address $address, int $shopId): \App\Models\Payment
    {
        $request = \Illuminate\Http\Request::create('/api/place-order', 'POST', [
            'shop_ids' => [$shopId],
            'address_id' => $address->id,
            'payment_method' => 'cash',
        ]);
        $request->setUserResolver(fn () => $customerUser);
        $carts = \App\Repositories\OrderRepository::query()
            ?->getModel()->newQuery(); // unused; cart resolution uses helper below
        $carts = userCart($request)->get();

        return \App\Repositories\OrderRepository::storeByRequestFromCart(
            new \App\Http\Requests\OrderRequest(),
            \App\Enums\PaymentMethod::CASH,
            $carts,
        );
    }
```

> Note: `placeOrder` is a helper; if `OrderRequest` construction is awkward in tests, simplify by calling `storeByRequestFromCart` with a plain `Request` (it only reads `address_id`, `coupon_code`, `allocations`, `note`, `is_buy_now`). `userCart()` requires a real `Request` with bearer/guest token — the `cartAccessToken()` path falls back to `auth()->user()` via `PersonalAccessToken`; for tests, seed a `CartAccessToken` for the customer or rely on `auth()->login($customerUser)` with `Customer` already present. Adjust the helper to the simplest path that resolves the test cart (e.g. `$this->actingAs($customerUser)` then `userCart(request())`), and fix the cart factory `customer_id`/`access_token` accordingly.

- [ ] **Step 6: Run tests to verify they pass**

Run: `php artisan test --compact tests/Feature/ShopAllocationTest.php`
Expected: PASS (all ShopAllocation tests, including Tasks 1–3).

- [ ] **Step 7: Commit**

```bash
git add app/Exceptions/UnfulfillableOrderException.php app/Repositories/OrderRepository.php tests/Feature/ShopAllocationTest.php
git commit -m "feat: allocate order lines to nearest shop with atomic stock guard

Co-Authored-By: Claude Fable 5 <noreply@anthropic.com>"
```

---

### Task 4: Surface unfulfillable candidates and enforce address coords in the API

**Files:**
- Modify: `app/Http/Controllers/API/OrderController.php`
- Modify: `app/Http/Requests/OrderRequest.php` (add `allocations`)
- Test: `tests/Feature/ShopAllocationTest.php`

**Interfaces:**
- Consumes: `UnfulfillableOrderException` (Task 3).
- Produces: `POST /api/place-order` response contract: on unfulfillable, HTTP 422 with body `{ message, data: { unfulfillable: { <product_id>: [candidate...] } } }`; on missing coords, HTTP 422 with message.

- [ ] **Step 1: Add `allocations` to OrderRequest rules**

In `app/Http/Requests/OrderRequest.php`, add to the rules array:

```php
            'allocations' => 'nullable|array',
            'allocations.*.product_id' => 'required|integer',
            'allocations.*.shop_id' => 'required|exists:shops,id',
```

- [ ] **Step 2: Enforce coords + catch the exception in `OrderController::store`**

In `app/Http/Controllers/API/OrderController.php`, inside `store`, right after the address ownership check (`if (! $customer->addresses()->where('id', $request->address_id)->exists())`), add:

```php
        $address = \App\Models\Address::find($request->address_id);
        if (! $address || ! $address->latitude || ! $address->longitude) {
            return $this->json('Please set your delivery location on the map before placing the order', [], 422);
        }
```

Then wrap the payment-creation call:

```php
        // Store the order
        try {
            $payment = OrderRepository::storeByRequestFromCart($request, $paymentMethod, $carts);
        } catch (\App\Exceptions\UnfulfillableOrderException $e) {
            return $this->json($e->getMessage(), [
                'unfulfillable' => $e->unfulfillable,
            ], 422);
        } catch (\RuntimeException $e) {
            return $this->json($e->getMessage(), [], 422);
        }
```

- [ ] **Step 3: Write the failing API tests**

Add to `tests/Feature/ShopAllocationTest.php`:

```php
    public function test_place_order_requires_address_coordinates(): void
    {
        [$master, $copy, $nearShop, $farShop] = $this->masterWithTwoCopies();
        $customerUser = \App\Models\User::factory()->create();
        $customer = \App\Models\Customer::factory()->create(['user_id' => $customerUser->id]);
        $address = \App\Models\Address::factory()->create(['customer_id' => $customer->id, 'latitude' => null, 'longitude' => null]);
        \App\Models\Cart::create(['customer_id' => $customer->id, 'shop_id' => $nearShop->id, 'product_id' => $master->id, 'quantity' => 2]);

        $response = $this->actingAs($customerUser, 'sanctum')->postJson('/api/place-order', [
            'shop_ids' => [$nearShop->id],
            'address_id' => $address->id,
            'payment_method' => 'Cash Payment',
        ]);

        $response->assertStatus(422)
            ->assertJsonPath('message', 'Please set your delivery location on the map before placing the order');
    }

    public function test_place_order_returns_candidates_for_unfulfillable_line(): void
    {
        [$master, $copy, $nearShop, $farShop] = $this->masterWithTwoCopies();
        $master->update(['quantity' => 0]); // near shop out of stock; far shop has stock but out of radius
        $customerUser = \App\Models\User::factory()->create();
        $customer = \App\Models\Customer::factory()->create(['user_id' => $customerUser->id]);
        $address = \App\Models\Address::factory()->create(['customer_id' => $customer->id, 'latitude' => 26.9, 'longitude' => 75.8]);
        \App\Models\Cart::create(['customer_id' => $customer->id, 'shop_id' => $nearShop->id, 'product_id' => $master->id, 'quantity' => 2]);

        $response = $this->actingAs($customerUser, 'sanctum')->postJson('/api/place-order', [
            'shop_ids' => [$nearShop->id],
            'address_id' => $address->id,
            'payment_method' => 'Cash Payment',
        ]);

        $response->assertStatus(422);
        $unfulfillable = $response->json('data.unfulfillable');
        $this->assertArrayHasKey((string) $master->id, $unfulfillable);
        $this->assertSame($farShop->id, $unfulfillable[(string) $master->id][0]['shop_id']);
    }
```

- [ ] **Step 4: Run the tests**

Run: `php artisan test --compact tests/Feature/ShopAllocationTest.php`
Expected: PASS. If the `/api/place-order` route needs `auth:sanctum` and the `Customer`/`Cart` resolution doesn't match, adjust the test seeding (`actingAs($customerUser, 'sanctum')` requires `customer_id` on the cart and the customer linked to the user).

- [ ] **Step 5: Commit**

```bash
git add app/Http/Controllers/API/OrderController.php app/Http/Requests/OrderRequest.php tests/Feature/ShopAllocationTest.php
git commit -m "feat: return unfulfillable shop candidates and require address coords

Co-Authored-By: Claude Fable 5 <noreply@anthropic.com>"
```

---

### Task 5: `/api/shop-candidates` endpoint

**Files:**
- Modify: `routes/api.php`
- Modify: `app/Http/Controllers/API/OrderController.php` (add `shopCandidates`)
- Test: `tests/Feature/ShopAllocationTest.php`

**Interfaces:**
- Consumes: `candidateShopsForLine` (Task 2).
- Produces: `POST /api/shop-candidates` → 200 JSON `{ data: { <product_id>: [candidate...] } }` where candidates are the Task-2 objects.

- [ ] **Step 1: Add the route**

In `routes/api.php`, inside the `auth:sanctum` group (near the cart routes), add:

```php
    Route::post('shop-candidates', [OrderController::class, 'shopCandidates']);
```

- [ ] **Step 2: Add the controller method**

In `app/Http/Controllers/API/OrderController.php`:

```php
    /**
     * Candidate shops (ranked by distance) for each requested product line.
     */
    public function shopCandidates(Request $request)
    {
        $request->validate([
            'address_id' => 'required|exists:addresses,id',
            'products' => 'required|array',
            'products.*.product_id' => 'required|integer|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
        ]);

        $address = Address::find($request->address_id);
        $candidates = [];

        foreach ($request->products as $line) {
            $product = Product::find($line['product_id']);
            $candidates[$product->id] = OrderRepository::candidateShopsForLine($product, (int) $line['quantity'], $address)->all();
        }

        return $this->json('shop candidates', ['shop_candidates' => $candidates]);
    }
```

- [ ] **Step 3: Write the failing test**

Add to `tests/Feature/ShopAllocationTest.php`:

```php
    public function test_shop_candidates_endpoint_returns_ranked_shops(): void
    {
        [$master, $copy, $nearShop, $farShop] = $this->masterWithTwoCopies();
        $customerUser = \App\Models\User::factory()->create();
        $customer = \App\Models\Customer::factory()->create(['user_id' => $customerUser->id]);
        $address = \App\Models\Address::factory()->create(['customer_id' => $customer->id, 'latitude' => 26.9, 'longitude' => 75.8]);

        $response = $this->actingAs($customerUser, 'sanctum')->postJson('/api/shop-candidates', [
            'address_id' => $address->id,
            'products' => [['product_id' => $master->id, 'quantity' => 2]],
        ]);

        $response->assertOk();
        $candidates = $response->json('data.shop_candidates.'.$master->id);
        $this->assertCount(2, $candidates);
        $this->assertSame($nearShop->id, $candidates[0]['shop_id']);
    }
```

- [ ] **Step 4: Run the test**

Run: `php artisan test --compact tests/Feature/ShopAllocationTest.php`
Expected: PASS.

- [ ] **Step 5: Commit**

```bash
git add routes/api.php app/Http/Controllers/API/OrderController.php tests/Feature/ShopAllocationTest.php
git commit -m "feat: add shop-candidates endpoint for manual shop picker

Co-Authored-By: Claude Fable 5 <noreply@anthropic.com>"
```

---

### Task 6: Re-order allocates to nearest shops

**Files:**
- Modify: `app/Repositories/OrderRepository.php` (`reOrder`)
- Modify: `app/Http/Controllers/API/OrderController.php` (`reOrder`)
- Test: `tests/Feature/ShopAllocationTest.php`

**Interfaces:**
- Consumes: `allocateNearestShop`, `getCartWiseAmounts`, `createNewOrder` (Task 2/3).
- Produces: `OrderRepository::reOrder(Order $order, $payment, ?int $overrideShopId = null): Collection<int, Order>` — one order per allocated shop. `OrderController::reOrder` attaches each to the payment and returns `orders`.

- [ ] **Step 1: Rewrite `reOrder` to allocate per product line**

Replace `OrderRepository::reOrder(Order $order, $payment): Order` with a version that builds per-shop orders:

```php
    public static function reOrder(Order $order, $payment): Collection
    {
        $tokens = cartAccessToken(request());
        $address = Address::find($order->address_id);
        $isMultiVendor = generaleSetting('setting')?->shop_type === 'multi';

        // group the original lines by their allocated shop
        $linesByShop = [];
        foreach ($order->products as $product) {
            $qty = (int) $product->pivot->quantity;
            $copy = $product;
            if ($isMultiVendor && $address?->latitude && $address?->longitude) {
                $copy = self::allocateNearestShop($product, $qty, $address);
            }
            $shopId = $copy?->shop_id ?? $product->shop_id;
            $linesByShop[$shopId][] = ['product' => $copy ?? $product, 'qty' => $qty];
        }

        $created = collect([]);
        foreach ($linesByShop as $shopId => $lines) {
            $shop = Shop::find($shopId);
            $linesForAmounts = collect($lines)->map(fn ($l) => [
                'cart' => (object) [
                    'quantity' => $l['qty'],
                    'size' => null,
                    'color' => null,
                    'unit' => null,
                ],
                'copy' => $l['product'],
            ]);
            $amounts = self::getCartWiseAmounts($shop, $linesForAmounts, null);

            $newOrder = self::createNewOrder((object) [
                'address_id' => $order->address_id,
                'note' => $order->instruction,
                'coupon_code' => null,
            ], $shop, \App\Enums\PaymentMethod::tryFrom($order->payment_method?->value ?? 'Cash Payment') ?? \App\Enums\PaymentMethod::CASH, $amounts);

            foreach ($lines as $line) {
                $product = $line['product'];
                $qty = $line['qty'];

                $decremented = Product::query()->whereKey($product->id)->where('quantity', '>=', $qty)->decrement('quantity', $qty);
                if (! $decremented) {
                    throw new \RuntimeException(__('Sorry, this product is no longer available in the required quantity'));
                }

                $newOrder->products()->attach($product->id, [
                    'quantity' => $qty,
                    'color' => null,
                    'size' => null,
                    'unit' => null,
                    'price' => (float) ($product->discount_price > 0 ? $product->discount_price : $product->price),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                if ($product->is_digital) {
                    $user = Auth::guard('api')->user();
                    for ($i = 0; $i < $qty; $i++) {
                        $license = $product->licenses()->whereNull('user_id')->inRandomOrder()->first();
                        if ($license) {
                            $license->update(['user_id' => $user->id, 'order_id' => $newOrder->id, 'is_used' => true]);
                        } else {
                            $license = $product->licenses()->create(['user_id' => $user->id, 'order_id' => $newOrder->id, 'is_used' => true, 'product_license' => generateLicenseKey()]);
                        }
                    }
                }
            }

            $created->push($newOrder);
        }

        $user = auth()->user();
        if ($user?->email) {
            try {
                \App\Events\OrderMailEvent::dispatch($user->email, $created->first());
            } catch (\Throwable $th) {
            }
        }

        return $created;
    }
```

> Note: this rewrite drops the per-order VAT/coupon copy fidelity in exchange for per-shop allocation; amounts come from `getCartWiseAmounts` (which now prices from the copy and uses the shop's delivery charge). The original `reOrder` digital-license + `decrement` logic is preserved inside the loop.

- [ ] **Step 2: Update `OrderController::reOrder` to attach all created orders**

In `app/Http/Controllers/API/OrderController.php`, replace:

```php
            $order = OrderRepository::reOrder($order, $payment);

            // attach payment to order
            $payment->orders()->attach($order->id);
```

with:

```php
            $orders = OrderRepository::reOrder($order, $payment);

            foreach ($orders as $createdOrder) {
                $payment->orders()->attach($createdOrder->id);
            }

            $order = $orders->first();
```

(The response still returns `OrderResource::make($order)` for the first order — the primary one.)

- [ ] **Step 3: Write the failing test**

Add to `tests/Feature/ShopAllocationTest.php`:

```php
    public function test_reorder_allocates_to_nearest_shop(): void
    {
        [$master, $copy, $nearShop, $farShop] = $this->masterWithTwoCopies();
        $customerUser = \App\Models\User::factory()->create();
        $customer = \App\Models\Customer::factory()->create(['user_id' => $customerUser->id]);
        $address = \App\Models\Address::factory()->create(['customer_id' => $customer->id, 'latitude' => 26.9, 'longitude' => 75.8]);
        $order = \App\Models\Order::factory()->create([
            'shop_id' => $farShop->id,
            'customer_id' => $customer->id,
            'address_id' => $address->id,
            'order_status' => 'Delivered',
            'payment_status' => 'Paid',
        ]);
        $order->products()->attach($master->id, ['quantity' => 2, 'color' => null, 'size' => null, 'unit' => null, 'price' => 100]);
        $payment = \App\Models\Payment::create(['amount' => 200, 'payment_method' => 'Cash Payment']);
        $this->actingAs($customerUser, 'sanctum');

        $orders = \App\Repositories\OrderRepository::reOrder($order, $payment);

        $this->assertCount(1, $orders);
        $this->assertSame($nearShop->id, $orders->first()->shop_id);
    }
```

- [ ] **Step 4: Run the tests**

Run: `php artisan test --compact tests/Feature/ShopAllocationTest.php`
Expected: PASS. If the `Payment::factory()` / `Order::factory()` shapes differ, adjust to match the existing factories.

- [ ] **Step 5: Commit**

```bash
git add app/Repositories/OrderRepository.php app/Http/Controllers/API/OrderController.php tests/Feature/ShopAllocationTest.php
git commit -m "feat: allocate re-orders to nearest shops

Co-Authored-By: Claude Fable 5 <noreply@anthropic.com>"
```

---

### Task 7: Require coordinates on addresses

**Files:**
- Modify: `app/Http/Requests/AddressRequest.php`
- Test: `tests/Feature/ShopAllocationTest.php`

**Interfaces:**
- Produces: address store/update now rejects `latitude`/`longitude` missing (HTTP 422).

- [ ] **Step 1: Make lat/lng required in `AddressRequest::rules()`**

Replace:

```php
            'longitude' => 'nullable|numeric|between:-180,180',
            'latitude' => 'nullable|numeric|between:-90,90',
```

with:

```php
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
```

- [ ] **Step 2: Write the failing test**

Add to `tests/Feature/ShopAllocationTest.php`:

```php
    public function test_address_store_requires_coordinates(): void
    {
        $user = \App\Models\User::factory()->create();
        $customer = \App\Models\Customer::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user, 'sanctum')->postJson('/api/address/store', [
            'name' => 'Test Customer',
            'phone' => '9800000000',
            'address_line' => '123 Test Street, Jaipur',
            'address_type' => 'home',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['latitude', 'longitude']);
    }
```

- [ ] **Step 3: Run the test**

Run: `php artisan test --compact tests/Feature/ShopAllocationTest.php`
Expected: PASS.

- [ ] **Step 4: Commit**

```bash
git add app/Http/Requests/AddressRequest.php tests/Feature/ShopAllocationTest.php
git commit -m "feat: require latitude/longitude on address create and update

Co-Authored-By: Claude Fable 5 <noreply@anthropic.com>"
```

---

### Task 8: Configurable allocation radius setting

**Files:**
- Create: `database/migrations/2026_08_05_add_shop_allocation_radius_to_generate_settings_table.php`
- Modify: `app/Repositories/GeneraleSettingRepository.php`
- Modify: `app/Http/Controllers/Admin/BusinessSetupController.php`
- Modify: `resources/views/admin/business-setup/shop.blade.php`
- Test: `tests/Feature/ShopAllocationTest.php`

**Interfaces:**
- Produces: `GeneraleSetting.shop_allocation_radius_km` (float, default 50) read by `candidateShopsForLine` (Task 2).

- [ ] **Step 1: Write the migration**

```bash
php artisan make:migration add_shop_allocation_radius_to_generate_settings_table
```

Edit the generated `up()`:

```php
    public function up(): void
    {
        Schema::table('generate_settings', function (Blueprint $table) {
            $table->float('shop_allocation_radius_km')->default(50)->after('default_delivery_charge');
        });
    }

    public function down(): void
    {
        Schema::table('generate_settings', function (Blueprint $table) {
            $table->dropColumn('shop_allocation_radius_km');
        });
    }
```

- [ ] **Step 2: Persist the field in the shop-setting repository**

In `app/Repositories/GeneraleSettingRepository.php`, inside `updateOrCreateShopSetting`, add to the update array:

```php
            'shop_allocation_radius_km' => $request->shop_allocation_radius_km ?? 50,
```

- [ ] **Step 3: Add the admin form field**

In `resources/views/admin/business-setup/shop.blade.php`, inside the existing form (near the other shop settings, e.g. after the commission section), add:

```blade
<div class="col-md-6">
    <label for="shop_allocation_radius_km" class="form-label">{{ __('Shop Allocation Radius (km)') }}</label>
    <input type="number" step="0.1" min="1" name="shop_allocation_radius_km" id="shop_allocation_radius_km"
        class="form-control" value="{{ $generaleSetting?->shop_allocation_radius_km ?? 50 }}">
    <div class="form-text">{{ __('Maximum distance for auto-assigning an order to a shop.') }}</div>
</div>
```

Confirm the form posts to `business-setting/shop-update` (route `business-setting.shop-update` → `BusinessSetupController@shopUpdate`), which calls `GeneraleSettingRepository::updateOrCreateShopSetting`. No controller change needed if the form already posts there.

- [ ] **Step 4: Run the migration + write the test**

Run: `php artisan migrate --no-interaction`

Add to `tests/Feature/ShopAllocationTest.php`:

```php
    public function test_allocation_uses_configured_radius(): void
    {
        [$master, $copy, $nearShop, $farShop] = $this->masterWithTwoCopies();
        \App\Models\GeneraleSetting::create(['shop_allocation_radius_km' => 1]);
        $address = \App\Models\Address::factory()->create(['latitude' => 26.9, 'longitude' => 75.8]);

        $candidates = \App\Repositories\OrderRepository::candidateShopsForLine($master, 2, $address);

        $this->assertCount(2, $candidates);
        $this->assertFalse($candidates[0]->radius_eligible); // near shop also out of 1km radius
    }
```

(If `GeneraleSetting::factory()` doesn't exist, create it or set the attribute via `\App\Models\GeneraleSetting::firstOrCreate()`.)

- [ ] **Step 5: Run the tests**

Run: `php artisan test --compact tests/Feature/ShopAllocationTest.php`
Expected: PASS.

- [ ] **Step 6: Commit**

```bash
git add database/migrations/2026_08_05_add_shop_allocation_radius_to_generate_settings_table.php app/Repositories/GeneraleSettingRepository.php resources/views/admin/business-setup/shop.blade.php tests/Feature/ShopAllocationTest.php
git commit -m "feat: add configurable shop-allocation radius setting

Co-Authored-By: Claude Fable 5 <noreply@anthropic.com>"
```

---

### Task 9: SPA — enforce coordinates and render the shop picker

**Files:**
- Modify: `resources/js/pages/Checkout.vue`
- Modify: `resources/js/pages/BuyNow.vue`
- Modify: `resources/js/pages/OrderHistory.vue` (re-order)

**Interfaces:**
- Consumes: the `POST /api/place-order` response contract (Task 4) and `POST /api/shop-candidates` (Task 5).

- [ ] **Step 1: `Checkout.vue` — guard against no-coords addresses**

Before the place-order submit, resolve the selected delivery address; if it has no `latitude`/`longitude`, show a message and stop:

```js
const selectedAddress = computed(() =>
    addressStore.addresses.find(a => a.id == form.address_id)
);
const submitOrder = async () => {
    if (!selectedAddress.value?.latitude || !selectedAddress.value?.longitude) {
        toast.error($t('Please set your delivery location on the map before placing the order'));
        router.push({ name: 'manage-address' });
        return;
    }
    // existing submit logic...
};
```

- [ ] **Step 2: `Checkout.vue` — handle the `unfulfillable` response with a picker**

Extend the place-order call so that on a 422 response containing `data.unfulfillable`, it fetches candidates and renders a per-line shop picker:

```js
const unfulfillable = ref({});
const shopCandidates = ref({});

const resolveUnfulfillable = async (data) => {
    unfulfillable.value = data.unfulfillable || {};
    const lines = Object.keys(unfulfillable.value).map(product_id => ({
        product_id: Number(product_id),
        quantity: unfulfillable.value[product_id][0]?.quantity ?? 1,
    }));
    if (!lines.length) return;
    const res = await axios.post('/shop-candidates', {
        address_id: form.address_id,
        products: lines,
    }, { headers: { Authorization: authStore.token } });
    shopCandidates.value = res.data.data.shop_candidates;
};

// in the place-order error handler:
.catch((error) => {
    if (error.response?.status === 422 && error.response.data?.data?.unfulfillable) {
        resolveUnfulfillable(error.response.data.data);
        return;
    }
    toast.error(error.response?.data?.message);
});
```

Add to the template (inside the order review section) a picker per product in `unfulfillable`:

```vue
<div v-if="Object.keys(unfulfillable).length" class="mt-4 border rounded p-3">
    <h6>{{ $t('Choose delivery shop') }}</h6>
    <div v-for="(cands, pid) in shopCandidates" :key="pid" class="mb-3">
        <p class="mb-1 text-sm text-slate-600">{{ $t('Product') }} #{{ pid }}</p>
        <label v-for="c in cands" :key="c.shop_id" class="d-flex align-items-center gap-2 border rounded p-2 mb-1">
            <input type="radio" :name="'shop_'+pid" :value="c.shop_id"
                   v-model="pickedShops[pid]">
            <span>{{ c.name }} — {{ c.distance_km }} km · {{ $t('Delivery') }} {{ c.delivery_charge }}</span>
        </label>
    </div>
</div>
```

On the next submit, include the picks in the request body:

```js
const allocations = Object.entries(pickedShops.value).map(([product_id, shop_id]) => ({
    product_id: Number(product_id),
    shop_id,
}));
// add allocations: allocations to the place-order payload
```

- [ ] **Step 3: `BuyNow.vue` — same guard + picker**

Apply the identical coords guard and the `unfulfillable` → picker handling (single product) to `BuyNow.vue`. Since Buy-Now has one line, the picker renders one product's candidate list.

- [ ] **Step 4: `OrderHistory.vue` — re-order coords guard**

In the re-order action, check the order's address has coords before calling `/api/place-order/again`; otherwise prompt the customer to update the address. (Re-order passes `allocations` the same way if the server responds `unfulfillable`.)

- [ ] **Step 5: Manual QA**

Run `npm run dev` (or `npm run build`), then in the browser: select an address with a map-picked location, add a product, check out, and confirm the order review shows the nearest shop + its delivery charge; force an unfulfillable scenario (set radius to 1 km in admin) and confirm the picker appears and the pick is honoured.

- [ ] **Step 6: Commit**

```bash
git add resources/js/pages/Checkout.vue resources/js/pages/BuyNow.vue resources/js/pages/OrderHistory.vue
git commit -m "feat: show nearest-shop allocation and manual shop picker in SPA

Co-Authored-By: Claude Fable 5 <noreply@anthropic.com>"
```

---

### Task 10: Dusk test for the shop picker

**Files:**
- Create: `tests/Browser/Customer/ShopAllocationTest.php`
- Modify: `tests/Browser/README.md` (optional note)

**Interfaces:**
- Consumes: the SPA picker (Task 9) and the `unfulfillable` response (Task 4).

- [ ] **Step 1: Write the Dusk test**

```php
<?php

namespace Tests\Browser\Customer;

use App\Models\Address;
use App\Models\Cart;
use App\Models\Customer;
use App\Models\GeneraleSetting;
use App\Models\Product;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class ShopAllocationTest extends DuskTestCase
{
    use DatabaseMigrations;

    public function test_checkout_shows_shop_picker_when_line_unfulfillable(): void
    {
        $shop = Shop::factory()->create(['latitude' => 26.91, 'longitude' => 75.79]);
        $shop->user->update(['is_active' => true]);
        $product = Product::factory()->create(['shop_id' => $shop->id, 'quantity' => 5, 'is_active' => true, 'is_approve' => true]);
        $user = User::factory()->create(['password' => bcrypt('secret')]);
        $customer = Customer::factory()->create(['user_id' => $user->id]);
        $address = Address::factory()->create(['customer_id' => $customer->id, 'latitude' => 26.9, 'longitude' => 75.8]);
        Cart::create(['customer_id' => $customer->id, 'shop_id' => $shop->id, 'product_id' => $product->id, 'quantity' => 2]);
        GeneraleSetting::create(['shop_allocation_radius_km' => 1]); // forces unfulfillable

        $this->browse(function (Browser $browser) use ($user, $address) {
            $browser->visit('/login')
                ->type('input[name="phone"]', $user->phone)
                ->type('input[name="password"]', 'secret')
                ->press('Login')
                ->visit('/checkout')
                ->waitForText('Choose delivery shop', 10)
                ->assertSee('km');
        });
    }
}
```

- [ ] **Step 2: Run the Dusk test**

Run: `php artisan dusk --filter=test_checkout_shows_shop_picker_when_line_unfulfillable tests/Browser/Customer/ShopAllocationTest.php`
Expected: PASS (adjust selectors to the real login form — see `tests/Browser/README.md`).

- [ ] **Step 3: Commit**

```bash
git add tests/Browser/Customer/ShopAllocationTest.php
git commit -m "test: dusk coverage for checkout shop picker

Co-Authored-By: Claude Fable 5 <noreply@anthropic.com>"
```

---

### Task 11: Update legacy tests + full suite + formatting

**Files:**
- Modify: any order-placement tests that assume shop-pinned behavior (e.g. `tests/Feature/ShopOrderShowTest.php`, `tests/Feature/*Order*`)

- [ ] **Step 1: Audit and fix affected tests**

Run the full suite and fix tests that now break because orders no longer necessarily go to the cart's pinned shop or delivery is no longer area-based:

Run: `php artisan test --compact`
For each failing test, either seed the expected allocation (shop lat/lng near the customer address, stock present) or assert on the allocated shop. Do not remove tests.

- [ ] **Step 2: Format PHP**

Run: `vendor/bin/pint --dirty --format agent`

- [ ] **Step 3: Final verification**

Run: `php artisan test --compact` and confirm the full suite passes.

- [ ] **Step 4: Commit**

```bash
git add -A
git commit -m "chore: update order-placement tests for smart shop allocation

Co-Authored-By: Claude Fable 5 <noreply@anthropic.com>"
```

---

## Self-Review

- **Spec coverage:** Decisions table → allocation rule (T2/T3), split per product (T3 group-by), master+shop copies (T2 masterId query), required coords (T4/T7), haversine+radius (T1/T8), customer-pick fallback (T2 override + T4 candidates + T5 endpoint + T9 picker), delivery charge from allocated shop (T3 `getCartWiseAmounts`), scope checkout/buy-now/re-order (T3/T6/T9), POS excluded (no POS files touched), Approach A inline + atomic guard (T3).
- **Placeholder scan:** No TBD/TODO; every code step is concrete. The `placeOrder` test helper (T3 Step 5) notes the cart-resolution caveat and how to adjust — an intentional seam, not a placeholder.
- **Type consistency:** `candidateShopsForLine` returns the same object shape used by `shopCandidates` (T5), `placeOrder` (T4) and the SPA (T9). `allocateNearestShop` and `reOrder` return `?Product` / `Collection<int,Order>` consistently. `storeByRequestFromCart` still returns `Payment` (T4 wraps it; T3 only adds a pre-loop allocation + the runtime exception path).
