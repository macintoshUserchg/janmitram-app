<?php

namespace App\Repositories;

use App\Enums\DiscountType;
use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Events\OrderMailEvent;
use App\Exceptions\UnfulfillableOrderException;
use App\Http\Requests\OrderRequest;
use App\Models\Address;
use App\Models\AdminCoupon;
use App\Models\CartAccessToken;
use App\Models\Customer;
use App\Models\GeneraleSetting;
use App\Models\Order;
use App\Models\OrderVatTax;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Shop;
use App\Models\VatTax;
use App\Services\NotificationServices;
use App\Support\Repositories\Repository;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderRepository extends Repository
{
    /**
     * base method
     *
     * @method model()
     */
    public static function model()
    {
        return Order::class;
    }

    public static function getShopSales($shopId)
    {
        return self::query()->withoutGlobalScopes()->where('shop_id', $shopId)->get();
    }

    /**
     * Store new order from cart
     */
    public static function storeByRequestFromCart(OrderRequest $request, $paymentMethod, $carts): Payment
    {
        return DB::transaction(function () use ($request, $paymentMethod, $carts) {
            return self::storeByRequestFromCartInTransaction($request, $paymentMethod, $carts);
        });
    }

    private static function storeByRequestFromCartInTransaction(OrderRequest $request, $paymentMethod, $carts): Payment
    {
        $totalPayableAmount = 0;

        $payment = Payment::create([
            'amount' => $totalPayableAmount,
            'payment_method' => $request->payment_method,
        ]);

        $tokens = cartAccessToken(request());

        $isMultiVendor = generaleSetting('setting')?->shop_type === 'multi';
        $overrides = collect($request->allocations ?? [])->keyBy('product_id');
        $address = Address::find($request->address_id);

        $lines = collect($carts)->map(fn ($cart) => ['cart' => $cart]);

        $fulfillFromNearest = filter_var($request->fulfill_from_nearest_shop ?? true, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? true;

        $shopLines = self::groupLinesByShop($lines, $address, $isMultiVendor, $overrides, $fulfillFromNearest);

        foreach ($shopLines as $shopId => $cartProducts) {
            $shop = Shop::find($shopId);

            $created = self::createOrderForShop(
                $shop,
                collect($cartProducts),
                $payment,
                $paymentMethod,
                $request,
                $request->coupon_code,
            );

            $totalPayableAmount += $created['payableAmount'];
        }

        $payment->update([
            'amount' => $totalPayableAmount,
        ]);

        $isBuyNow = $request->is_buy_now ?? false;
        userCart(request())->whereIn('shop_id', $request->shop_ids)->where('is_buy_now', $isBuyNow)->delete();

        CartAccessToken::where('access_token', $tokens['access_token'])->delete();

        return $payment;
    }

    /**
     * Group cart lines by the shop that will fulfil them. In multi-vendor mode
     * with fulfillFromNearest=true, each line is allocated to the nearest shop with enough stock;
     * in strict mode (fulfillFromNearest=false) or single-vendor, lines are fulfilled strictly
     * from the shop selected in the cart.
     *
     * @param  Collection  $lines  collection of ['cart' => $cart]
     * @return array<string, array<int, array{cart: mixed, copy: Product}>>
     */
    private static function groupLinesByShop(Collection $lines, Address $address, bool $isMultiVendor, ?Collection $overrides = null, bool $fulfillFromNearest = true): array
    {
        $shopLines = [];
        $unfulfillable = [];

        foreach ($lines as $line) {
            $cart = $line['cart'];

            if (! $isMultiVendor || ! $fulfillFromNearest) {
                if ($cart->product->quantity < $cart->quantity) {
                    $shopName = $cart->product->shop?->name ?? 'Selected Shop';
                    throw new \RuntimeException(__(
                        'Sorry, this product is no longer available in the required quantity. The selected shop ":shop" does not have enough stock for ":product" (Available: :available units). Please reduce quantity or enable "Auto-deliver from nearest shop".',
                        [
                            'shop' => $shopName,
                            'product' => $cart->product->name,
                            'available' => (int) $cart->product->quantity,
                        ]
                    ));
                }

                $shopLines[$cart->shop_id][] = ['cart' => $cart, 'copy' => $cart->product];

                continue;
            }

            $allocated = self::allocateNearestShop(
                $cart->product,
                (int) $cart->quantity,
                $address,
                $overrides?->get($cart->product_id)?->shop_id,
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

        return $shopLines;
    }

    /**
     * Create one order for a single shop from its allocated lines: compute the
     * amounts, create the order, link the payment, then process each line
     * (stock decrement, pricing including active flash sales, attach, digital
     * licenses). Persist VAT and dispatch the order email.
     *
     * @param  Collection  $lines  collection of ['cart' => $cart, 'copy' => Product]
     * @return array{order: Order, payableAmount: mixed}
     */
    private static function createOrderForShop(
        Shop $shop,
        Collection $lines,
        Payment $payment,
        PaymentMethod $paymentMethod,
        object $orderData,
        ?string $couponCode = null,
    ): array {
        $getCartAmounts = self::getCartWiseAmounts($shop, $lines, $couponCode, $orderData->card_number ?? null);

        $order = self::createNewOrder($orderData, $shop, $paymentMethod, $getCartAmounts);

        $payment->orders()->attach($order->id);

        foreach ($lines as $line) {
            $cart = $line['cart'];
            $product = $line['copy'];

            $decremented = Product::query()
                ->whereKey($product->id)
                ->where('quantity', '>=', $cart->quantity)
                ->decrement('quantity', $cart->quantity);

            if (! $decremented) {
                throw new \RuntimeException(__('Sorry, this product is no longer available in the required quantity'));
            }

            $price = $product->discount_price > 0 ? min($product->discount_price, $product->price) : $product->price;

            $flashSale = $product->flashSales()->isActive()->first();
            $flashSaleProduct = null;
            $quantity = 0;

            $saleQty = $cart->quantity;

            if ($flashSale) {
                $flashSaleProduct = $flashSale?->products()->where('id', $product->id)->first();

                $quantity = $flashSaleProduct?->pivot->quantity - $flashSaleProduct?->pivot->sale_quantity;

                if ($quantity == 0) {
                    $flashSaleProduct = null;
                } else {
                    $price = $flashSaleProduct->pivot->price;
                    $saleQty += $flashSaleProduct->pivot->sale_quantity;

                    $flashSale->products()->updateExistingPivot($product->id, [
                        'sale_quantity' => $saleQty,
                    ]);
                }
            }

            $sizePrice = $product->sizes()?->where('id', $cart->size)->first()?->pivot?->price ?? 0;
            $price = $price + $sizePrice;

            $colorPrice = $product->colors()?->where('id', $cart->color)->first()?->pivot?->price ?? 0;
            $price = $price + $colorPrice;

            $size = $product->sizes()?->where('id', $cart->size)->first();
            $color = $product->colors()?->where('id', $cart->color)->first();

            $order->products()->attach($product->id, [
                'quantity' => $cart->quantity,
                'color' => $color?->name,
                'size' => $size?->name,
                'unit' => $cart->unit,
                'price' => $price,
                'buying_price' => $product->buyingPrice() ?? 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            if (function_exists('module_exists') && module_exists('Purchase')) {
                $order->productStockOuts()->create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'quantity' => $cart->quantity,
                ]);
            }

            // digital product license generation
            if ($product->is_digital == true) {
                $userId = Auth::guard('api')->user()->id;
                $quantity = $cart->quantity;

                for ($i = 0; $i < $quantity; $i++) {
                    $license = $product->licenses()
                        ->whereNull('user_id')
                        ->inRandomOrder()
                        ->first();

                    if ($license) {
                        $license->update([
                            'user_id' => $userId,
                            'order_id' => $order->id,
                            'is_used' => true,
                        ]);
                    } else {
                        $newLicenseKey = generateLicenseKey();

                        $license = $product->licenses()->create([
                            'user_id' => $userId,
                            'order_id' => $order->id,
                            'is_used' => true,
                            'product_license' => $newLicenseKey,
                        ]);
                    }
                }

                if ($order->payment_status == PaymentStatus::PENDING->value) {
                    $order->order_status = OrderStatus::PENDING->value;
                } else {
                    $order->order_status = OrderStatus::DELIVERED->value;
                }

                $order->save();
            }
            // digital product license generation
        }

        foreach ($getCartAmounts['allVatTaxes'] ?? [] as $vatTax) {
            if (! $vatTax) {
                continue;
            }

            OrderVatTax::create([
                'order_id' => $order->id,
                'name' => $vatTax->name,
                'percentage' => $vatTax->percentage,
                'amount' => $vatTax->amount,
            ]);
        }

        $tokens = cartAccessToken(request());
        $customerId = $orderData->customer_id ?? $tokens['customer_id'] ?? null;
        $user = Customer::find($customerId)?->user ?? null;
        if ($user?->email) {
            try {
                OrderMailEvent::dispatch($user->email, $order);
            } catch (\Throwable $th) {
            }
        }

        return ['order' => $order, 'payableAmount' => $getCartAmounts['payableAmount']];
    }

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
            'price' => (float) ($copy->discount_price > 0 ? min($copy->discount_price, $copy->price) : $copy->price),
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

    private static function createNewOrder($request, $shop, $paymentMethod, $getCartAmounts)
    {
        $lastOrderId = self::query()->max('id');

        $tokens = cartAccessToken(request());

        $address = Address::find($request->address_id);

        $order = self::create([
            'shop_id' => $shop->id,
            'order_code' => str_pad($lastOrderId + 1, 6, '0', STR_PAD_LEFT),
            'prefix' => $shop->prefix ?? 'RC',
            // 'customer_id' => auth()->user()->customer->id,
            'customer_id' => $request->customer_id ?? $tokens['customer_id'] ?? null,
            'coupon_id' => $getCartAmounts['coupon'],
            'delivery_charge' => $getCartAmounts['deliveryCharge'],
            'payable_amount' => $getCartAmounts['payableAmount'],
            'total_amount' => $getCartAmounts['totalAmount'],
            'tax_amount' => $getCartAmounts['totalTaxAmount'],
            'coupon_discount' => max(0, $getCartAmounts['discount'] - ($getCartAmounts['cardDiscount'] ?? 0)),
            'card_id' => $getCartAmounts['card'] ?? null,
            'card_discount' => $getCartAmounts['cardDiscount'] ?? 0,
            'payment_method' => $paymentMethod->value,
            'order_status' => OrderStatus::PENDING->value,
            'address_id' => $request->address_id,
            'instruction' => $request->note,
            'payment_status' => PaymentStatus::PENDING->value,
            'order_area' => $address->getArea->name ?? null,
        ]);

        return $order;
    }

    private static function getDeliveryAmount()
    {
        $address = Address::find(request()->address_id);
        if (! $address) {
            return 0;
        }

        return $address->deliveryAmount();
    }

    private static function getCartWiseAmounts(Shop $shop, $carts, $couponCode = null, $cardNumber = null): array
    {
        $totalAmount = 0;
        $discount = 0;
        $coupon = null;
        $totalTaxAmount = 0;
        $globalBase = 0;
        $perProduct = [];

        $orderQty = $carts->sum(fn ($l) => $l['cart']->quantity);
        $deliveryCharge = $shop->delivery_charge > 0 ? (float) $shop->delivery_charge : self::getDeliveryAmount();

        $allVatTaxes = [];

        foreach ($carts ?? [] as $line) {
            $cart = $line['cart'];
            $product = $line['copy'];

            if (! $cart) {
                continue;
            }
            if ($product->is_digital) {
                $deliveryCharge = 0;
            }
            $price = $product->discount_price > 0 ? min($product->discount_price, $product->price) : $product->price;

            $flashSale = $product->flashSales()->isActive()->first();
            $flashSaleProduct = null;
            $quantity = 0;

            if ($flashSale) {
                $flashSaleProduct = $flashSale?->products()->where('id', $product->id)->first();

                $quantity = $flashSaleProduct?->pivot->quantity - $flashSaleProduct?->pivot->sale_quantity;

                if ($quantity == 0) {
                    $flashSaleProduct = null;
                } else {
                    $price = $flashSaleProduct->pivot->price;
                }
            }
            $sizePrice = $product->sizes()?->where('id', $cart->size)->first()->pivot?->price ?? 0;
            $price = $price + $sizePrice;

            $colorPrice = $product->colors()?->where('id', $cart->color)->first()->pivot?->price ?? 0;
            $price = $price + $colorPrice;

            $lineTotal = $price * $cart->quantity;
            $totalAmount += $lineTotal;

            $assignedActive = $product->vatTaxes()->where('is_active', true)->get();
            if ($assignedActive->isEmpty() && $product->master_product_id) {
                $assignedActive = $product->masterProduct?->vatTaxes()->where('is_active', true)->get() ?? collect();
            }

            if ($assignedActive->isNotEmpty()) {
                foreach ($assignedActive as $rate) {
                    $perProduct[$rate->id] = ($perProduct[$rate->id] ?? 0) + round($lineTotal * ($rate->percentage / 100), 2);
                }
            } else {
                $globalBase += $lineTotal;
            }
        }

        // order vat taxes: one default rate on the global base, plus per-product overrides
        $defaultVatTax = VatTaxRepository::getDefaultVatTax();

        if ($defaultVatTax?->name && $defaultVatTax->percentage > 0) {
            $amount = round($globalBase * ($defaultVatTax->percentage / 100), 2) + ($perProduct[$defaultVatTax->id] ?? 0);

            if ($amount > 0) {
                $allVatTaxes[] = (object) [
                    'name' => $defaultVatTax->name,
                    'percentage' => $defaultVatTax->percentage,
                    'amount' => round($amount, 2),
                ];

                $totalTaxAmount += $amount;
            }
        }

        foreach ($perProduct as $rateId => $amount) {
            if ($rateId == $defaultVatTax?->id || $amount <= 0) {
                continue;
            }

            $rate = VatTax::find($rateId);

            if ($rate?->name && $rate->percentage > 0) {
                $allVatTaxes[] = (object) [
                    'name' => $rate->name,
                    'percentage' => $rate->percentage,
                    'amount' => round($amount, 2),
                ];

                $totalTaxAmount += $amount;
            }
        }

        // a valid card discount takes precedence over coupons (instead-of)
        $card = null;
        $cardDiscount = 0;

        if ($cardNumber) {
            $card = CardRepository::resolveForCustomer($cardNumber, cartAccessToken(request())['customer_id'] ?? null);

            if ($card) {
                $cardDiscount = CardRepository::discountFor($totalAmount);
            }
        }

        if ($cardDiscount > 0) {
            $discount += $cardDiscount;
        } else {
            // get coupon discount
            $couponDiscount = self::getCouponDiscount($totalAmount, $shop->id, $couponCode);

            // check coupon discount amount
            if ($couponDiscount['total_discount_amount'] > 0) {
                $discount += $couponDiscount['total_discount_amount'];
                $coupon = $couponDiscount['coupon'];
            }
        }

        // calculate payable amount
        $payableAmount = ($totalAmount + $deliveryCharge + $totalTaxAmount) - $discount;

        // return array
        return [
            'totalAmount' => $totalAmount,
            'totalTaxAmount' => $totalTaxAmount,
            'payableAmount' => $payableAmount,
            'discount' => $discount,
            'deliveryCharge' => $deliveryCharge,
            'coupon' => $coupon?->id,
            'card' => $card?->id,
            'cardDiscount' => $cardDiscount,
            'allVatTaxes' => $allVatTaxes,
        ];
    }

    /**
     * Re-order: allocate each original product line to the nearest shop and
     * create one new order per allocated shop.
     *
     * @return Collection<int, Order>
     */
    public static function reOrder(Order $order, $payment): Collection
    {
        return DB::transaction(function () use ($order, $payment) {
            return self::reOrderInTransaction($order, $payment);
        });
    }

    private static function reOrderInTransaction(Order $order, $payment): Collection
    {
        $address = Address::find($order->address_id);
        $isMultiVendor = generaleSetting('setting')?->shop_type === 'multi';

        // rebuild the original lines as cart-like objects so a reorder re-prices
        // and re-allocates exactly like a fresh checkout of the same items
        $lines = collect($order->products)->map(function ($product) {
            $sizeId = $product->sizes()->where('name', $product->pivot->size)->value('id');
            $colorId = $product->colors()->where('name', $product->pivot->color)->value('id');

            return ['cart' => (object) [
                'product_id' => $product->id,
                'quantity' => (int) $product->pivot->quantity,
                'shop_id' => $product->shop_id,
                'product' => $product,
                'size' => $sizeId,
                'color' => $colorId,
                'unit' => $product->pivot->unit,
            ]];
        });

        $shopLines = self::groupLinesByShop($lines, $address, $isMultiVendor);

        $orderData = (object) [
            'address_id' => $order->address_id,
            'note' => $order->instruction,
            'customer_id' => $order->customer_id,
        ];

        $paymentMethod = PaymentMethod::tryFrom($order->payment_method?->value ?? 'Cash Payment') ?? PaymentMethod::CASH;

        $created = collect([]);
        $totalPayableAmount = 0;

        foreach ($shopLines as $shopId => $cartProducts) {
            $shop = Shop::find($shopId);

            $result = self::createOrderForShop($shop, collect($cartProducts), $payment, $paymentMethod, $orderData);

            $created->push($result['order']);
            $totalPayableAmount += $result['payableAmount'];
        }

        $payment->update([
            'amount' => $totalPayableAmount,
        ]);

        return $created;
    }

    /**
     * Get applied coupon orders
     *
     * @param  mixed  $coupon
     * @return Collection
     */
    public static function getAppliedCouponOrders($coupon)
    {
        $tokens = cartAccessToken(request());
        $customer = Customer::firstWhere('id', $tokens['customer_id']) ?? null;

        return $customer?->orders()?->where('coupon_id', $coupon->id)->get() ?? [];
    }

    /**
     * Get coupon discount
     *
     * @param  mixed  $totalAmount
     * @param  mixed  $shopId
     * @param  mixed  $couponCode
     * @return array
     */
    public static function getCouponDiscount($totalAmount, $shopId, $couponCode = null)
    {
        $totalOrderAmount = 0;
        $totalDiscountAmount = 0;
        $coupon = null;

        if ($couponCode) {
            $shop = Shop::find($shopId);
            $coupon = $shop->coupons()->where('code', $couponCode)->Active()->isValid()->first();

            if (! $coupon) {
                $coupon = AdminCoupon::where('shop_id', $shopId)->whereHas('coupon', function ($query) use ($couponCode) {
                    $query->where('code', $couponCode)->Active()->isValid();
                })->first()?->coupon;
            }

            if ($coupon) {
                $discount = self::getCouponDiscountAmount($coupon, $totalAmount);

                $totalOrderAmount += $discount['total_amount'];
                $totalDiscountAmount += $discount['discount_amount'];
            }
        }

        return [
            'total_order_amount' => $totalOrderAmount,
            'total_discount_amount' => $totalDiscountAmount,
            'coupon' => $coupon,
        ];
    }

    /**
     * Get coupon discount amount
     *
     * @param  mixed  $coupon
     * @param  mixed  $totalAmount
     * @return array
     */
    private static function getCouponDiscountAmount($coupon, $totalAmount)
    {
        $appliedOrders = self::getAppliedCouponOrders($coupon);

        $amount = $coupon->type->value == DiscountType::PERCENTAGE->value ? ($totalAmount * $coupon->discount) / 100 : $coupon->discount;

        $couponDiscount = 0;
        if ($appliedOrders->count() < ($coupon->limit_for_user ?? 500) && $coupon->min_amount <= $totalAmount) {
            $couponDiscount = $amount;
            if ($coupon->max_discount_amount && $coupon->max_discount_amount < $amount) {
                $couponDiscount = $coupon->max_discount_amount;
            }
        }

        return [
            'total_amount' => $totalAmount,
            'discount_amount' => (float) round($couponDiscount ?? 0, 2),
        ];
    }

    /**
     * Order status update from rider
     */
    public static function OrderStatusUpdateFromRider(Order $order, $driverOrder, $orderStatus)
    {
        if ($orderStatus == OrderStatus::PROCESSING->value) {
            $driverOrder->update(['is_accept' => true]);
        }

        $order->update([
            'order_status' => ($orderStatus == 'deliveredAndPaid') ? OrderStatus::DELIVERED->value : $orderStatus,
        ]);

        if ($orderStatus == OrderStatus::PICKUP->value) {
            $order->update([
                'pick_date' => now(),
                'order_status' => OrderStatus::ON_THE_WAY->value,
            ]);
        }

        $paymentMethod = $order->payment_method->value;

        $isDelivery = false;
        if ($paymentMethod != PaymentMethod::CASH->value && $orderStatus == OrderStatus::DELIVERED->value) {
            $isDelivery = true;
        }

        if (($orderStatus == 'deliveredAndPaid') || $isDelivery) {

            $driverOrder->update(['is_completed' => true]);

            if ($paymentMethod == PaymentMethod::CASH->value) {
                $driverOrder->update(['cash_collect' => true]);

                $totalCashCollected = $driverOrder->driver->total_cash_collected + $order->payable_amount;

                $driverOrder->driver->update([
                    'total_cash_collected' => $totalCashCollected,
                ]);
            }

            $generaleSetting = GeneraleSetting::first();

            $commission = 0;

            if ($generaleSetting?->business_based_on == 'commission' && $generaleSetting?->commission_charge != 'monthly') {

                if ($generaleSetting?->commission_type != 'fixed') {
                    $commission = $order->total_amount * $generaleSetting->commission / 100;
                } else {
                    $commission = $generaleSetting->commission ?? 0;
                }
            }

            $order->update([
                'delivery_date' => now(),
                'delivered_at' => now(),
                'payment_status' => PaymentStatus::PAID->value,
                'admin_commission' => $commission,
            ]);

            $wallet = $order->shop->user->wallet;

            if ($wallet == null) {
                $wallet = WalletRepository::storeByRequest($order->shop->user);
            }

            TransactionRepository::storeByRequest(
                $wallet,
                $order->total_amount,
                'credit',
                false,
                false,
                'order_sale',
                "Order sale proceeds for order #{$order->prefix}{$order->order_code}"
            );

            if ($generaleSetting?->business_based_on == 'commission') {
                TransactionRepository::storeByRequest($wallet, $commission, 'debit', true, true, 'admin commission added', 'order commission added in admin wallet');
            }

            $driverWallet = DriverRepository::getWallet($driverOrder->driver);

            $deliveryCharge = $order->delivery_charge;

            WalletRepository::updateByRequest($driverWallet, $deliveryCharge, 'credit');
        }

        $user = $order->customer?->user;

        $message = "Hello {$user?->name}. Your order status is {$orderStatus}. OrderID: {$order->prefix}{$order->order_code}";

        $title = 'Order Status Update';

        $devices = $user?->devices;

        if (count($devices) > 0) {

            $deviceKeys = $devices->pluck('key')->toArray();
            try {
                NotificationServices::sendNotification($message, $deviceKeys, $title);
            } catch (\Throwable $th) {
            }
        }

        NotificationRepository::storeByRequest((object) [
            'title' => $title,
            'content' => $message,
            'user_id' => $user?->id,
            'url' => $order->id,
            'type' => 'order',
            'icon' => null,
            'is_read' => false,
        ]);
    }
}
