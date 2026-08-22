<?php

namespace App\Repositories;

use App\Http\Requests\CartRequest;
use App\Http\Resources\ColorResource;
use App\Http\Resources\SizeResource;
use App\Models\Address;
use App\Models\Area;
use App\Models\Cart;
use App\Models\Product;
use App\Models\VatTax;
use App\Support\Repositories\Repository;
use Illuminate\Support\Number;

class CartRepository extends Repository
{
    public static function model()
    {
        return Cart::class;
    }

    public static function ShopWiseCartProducts($groupCart)
    {
        $totalItems = 0;
        $shopWiseProducts = collect([]);
        $info = null;

        foreach ($groupCart as $key => $products) {
            $productArray = collect([]);

            foreach ($products as $cart) {

                $product = $cart->product;

                if (! $product) {
                    $cart->delete();
                    $info = 'Some products are removed from cart due to unavailability';

                    continue;
                }

                $totalItems++;

                $discountPercentage = $product->getDiscountPercentage($product->price, $product->discount_price);

                $totalSold = $product->orders->sum('pivot.quantity');

                $flashSale = $product->flashSales()->isActive()->first();
                $flashSaleProduct = null;
                $quantity = null;

                if ($flashSale) {
                    $flashSaleProduct = $flashSale?->products()->where('id', $product->id)->first();

                    $quantity = $flashSaleProduct?->pivot->quantity - $flashSaleProduct->pivot->sale_quantity;

                    if ($quantity == 0) {
                        $quantity = null;
                        $flashSaleProduct = null;
                    } else {
                        $discountPercentage = $flashSale?->pivot->discount;
                    }
                }

                $size = $product->sizes()?->where('id', $cart->size)->first();
                $color = $product->colors()?->where('id', $cart->color)->first();

                $sizePrice = $size?->pivot?->price ?? 0;
                $colorPrice = $color?->pivot?->price ?? 0;
                $extraPrice = $sizePrice + $colorPrice;

                $discountPrice = $product->discount_price > 0 ? ($product->discount_price + $extraPrice) : 0;
                if ($flashSaleProduct) {
                    $discountPrice = $flashSaleProduct->pivot->price + $extraPrice;
                }

                $mainPrice = $product->price + $extraPrice;

                if ($discountPrice > 0) {
                    $discountPercentage = ($mainPrice - $discountPrice) / $mainPrice * 100;
                }

                $productArray[] = (object) [
                    'id' => $product->id,
                    'quantity' => (int) $cart->quantity,
                    'is_digital' => (bool) $product?->is_digital,
                    'name' => $product->name,
                    'thumbnail' => $product->thumbnail,
                    'brand' => $product->brand?->name ?? null,
                    'price' => (float) number_format($mainPrice, 2, '.', ''),
                    'discount_price' => (float) number_format($discountPrice, 2, '.', ''),
                    'discount_percentage' => (float) number_format($discountPercentage, 2, '.', ''),
                    'rating' => (float) $product->averageRating,
                    'total_reviews' => (string) Number::abbreviate($product->reviews->count(), maxPrecision: 2),
                    'total_sold' => (string) number_format($totalSold, 0, '.', ','),
                    'color' => $color ? ColorResource::make($color) : null,
                    'size' => $size ? SizeResource::make($size) : null,
                    'unit' => $cart->unit,
                ];
            }

            if ($productArray->isEmpty()) {
                continue;
            }

            $shop = $products[0]?->shop;

            $lastOnline = $shop->last_online >= now() ? true : false;

            $shopWiseProducts[] = (object) [
                'shop_id' => $key,
                'shop_name' => $shop->name,
                'shop_logo' => $shop->logo,
                'shop_rating' => (float) $shop->averageRating,
                'shop_online' => $lastOnline,
                'products' => $productArray,
            ];
        }

        return [
            'total_items' => $totalItems,
            'shop_wise_products' => $shopWiseProducts,
            'info' => $info,
        ];
    }

    /**
     * Store or update cart by request.
     */
    public static function storeOrUpdateByRequest(CartRequest $request, Product $product): Cart
    {
        $size = $request->size;
        $color = $request->color;
        $unit = $request->unit ?? $product->unit?->name;

        $tokens = cartAccessToken(request());

        $isBuyNow = $request->is_buy_now ?? false;

        // $customer = auth()->user()->customer;

        // $cart = $customer->carts()?->where('product_id', $product->id)->where('is_buy_now', $isBuyNow)->first();

        $cart = userCart($request)->where('product_id', $product->id)->where('is_buy_now', $isBuyNow)->first();

        if ($cart) {
            $cart->update([
                'quantity' => $isBuyNow ? 1 : $cart->quantity + 1,
                'size' => $request->size ?? $cart->size,
                'color' => $request->color ?? $cart->color,
                'unit' => $request->unit ?? $cart->unit,
            ]);

            return $cart;
        }

        return self::create([
            'product_id' => $request->product_id,
            'shop_id' => $request->shop_id ?? $product->shop->id,
            'is_buy_now' => $isBuyNow,
            // 'customer_id' => $customer->id,
            'customer_id' => $tokens['customer_id'] ?? null,
            'quantity' => $request->quantity ?? 1,
            'size' => $size,
            'color' => $color,
            'unit' => $unit,
            'access_token' => $tokens['access_token'] ?? '',
        ]);
    }

    private static function getDeliveryAmount(): float
    {
        // 1. Check from selected customer address
        if ($address = Address::find(request()->address_id)) {
            return (float) $address->deliveryAmount();
        }

        // 2. Check from request city (e.g. guest checkout)
        $cityName = trim(request()->city ?? '');
        if (! empty($cityName)) {
            $cityRate = Area::where('is_active', true)
                ->where(function ($q) use ($cityName) {
                    $q->whereRaw('LOWER(name) = ?', [strtolower($cityName)])
                        ->orWhereRaw('LOWER(name) LIKE ?', ['%'.strtolower($cityName).'%'])
                        ->orWhereRaw('? LIKE CONCAT("%", LOWER(name), "%")', [strtolower($cityName)]);
                })
                ->first();

            if ($cityRate) {
                return (float) $cityRate->delivery_amount;
            }
        }

        // 3. Legacy area fallback
        if (request()->area_id) {
            return (float) (Area::find(request()->area_id)?->delivery_amount ?? 0);
        }

        return 0.0;
    }

    public static function checkoutByRequest($request, $carts)
    {
        $totalAmount = 0;
        $deliveryCharge = 0;
        $couponDiscount = 0;
        $payableAmount = 0;

        $globalBase = 0;
        $perProduct = [];
        $totalOrderTaxAmount = 0;
        $vatTaxesArray = [];
        $tokens = cartAccessToken(request());

        foreach ($carts ?? [] as $cart) {

            if (! $cart) {
                continue;
            }

            $product = $cart->product;
            $flashSale = $product->flashSales()->isActive()->first();
            $flashSaleProduct = null;
            $quantity = null;

            $price = $product->discount_price > 0 ? $product->discount_price : $product->price;

            if ($flashSale) {
                $flashSaleProduct = $flashSale?->products()->where('id', $product->id)->first();

                $quantity = $flashSaleProduct?->pivot->quantity - $flashSaleProduct->pivot->sale_quantity;

                if ($quantity == 0) {
                    $quantity = null;
                    $flashSaleProduct = null;
                } else {
                    $price = $flashSaleProduct->pivot->price;
                }
            }

            $sizePrice = $product->sizes()?->where('id', $cart->size)->first()?->pivot?->price ?? 0;
            $price = $price + $sizePrice;

            $colorPrice = $product->colors()?->where('id', $cart->color)->first()?->pivot?->price ?? 0;
            $price = $price + $colorPrice;

            $lineTotal = $price * $cart->quantity;
            $totalAmount += $lineTotal;

            $assignedActive = $product->vatTaxes()->where('is_active', true)->get();

            if ($assignedActive->isNotEmpty()) {
                foreach ($assignedActive as $rate) {
                    $perProduct[$rate->id] = ($perProduct[$rate->id] ?? 0) + round($lineTotal * ($rate->percentage / 100), 2);
                }
            } else {
                $globalBase += $lineTotal;
            }
        }

        $groupCarts = $carts->groupBy('shop_id');

        // get delivery charge
        $deliveryCharge = 0;
        foreach ($groupCarts as $shopId => $shopCarts) {

            $productQty = 0;

            foreach ($shopCarts as $cart) {
                $productQty += $cart->quantity;
            }

            if ($productQty > 0) {
                if ($cart->product?->is_digital != true) {
                    $deliveryCharge = self::getDeliveryAmount();
                }
            }
        }

        // generate array for get discount
        $products = collect([]);
        foreach ($carts as $cart) {
            $products->push([
                'id' => $cart->product_id,
                'quantity' => (int) $cart->quantity,
                'shop_id' => $cart->shop_id,
            ]);
        }
        $array = (object) [
            'coupon_code' => $request->coupon_code,
            'card_number' => $request->card_number ?? null,
            'products' => $products,
        ];

        $cardDiscount = 0;
        $cardError = null;

        if ($tokens['customer_id']) {
            // get coupon discount
            $getDiscount = CouponRepository::getCouponDiscount($array);

            $couponDiscount = $getDiscount['discount_amount'];
            $cardDiscount = (float) ($getDiscount['card_discount_amount'] ?? 0);
            $couponDiscount -= $cardDiscount; // coupon portion only; the card discount is shown separately
            $card = $getDiscount['card'] ?? null;

            if ($request->card_number ?? null) {
                if (! $card) {
                    $cardError = __('Invalid or inactive card');
                } elseif ($cardDiscount == 0) {
                    $cardError = __('Minimum order amount not met');
                }
            }
        }

        // Discounts apply strictly to product subtotal (never deducting from delivery charges)
        $discountedItems = max(0, (float) $totalAmount - $couponDiscount - $cardDiscount);
        $payableAmount = $discountedItems + (float) $deliveryCharge;

        // get order base tax: one default rate on the global base, plus per-product overrides
        $defaultVatTax = VatTaxRepository::getDefaultVatTax();

        if ($defaultVatTax && $defaultVatTax->name && $defaultVatTax->percentage > 0) {
            $amount = round($globalBase * ($defaultVatTax->percentage / 100), 2) + ($perProduct[$defaultVatTax->id] ?? 0);

            if ($amount > 0) {
                $vatTaxesArray[] = [
                    'id' => $defaultVatTax->id,
                    'name' => $defaultVatTax->name,
                    'percentage' => $defaultVatTax->percentage,
                    'amount' => round($amount, 2),
                ];
            }
        }

        foreach ($perProduct as $rateId => $amount) {
            if ($rateId == $defaultVatTax?->id || $amount <= 0) {
                continue;
            }

            $rate = VatTax::find($rateId);

            if ($rate?->name && $rate->percentage > 0) {
                $vatTaxesArray[] = [
                    'id' => $rate->id,
                    'name' => $rate->name,
                    'percentage' => $rate->percentage,
                    'amount' => round($amount, 2),
                ];
            }
        }

        $totalOrderTaxAmount = array_sum(array_column($vatTaxesArray, 'amount'));

        $payableAmount += $totalOrderTaxAmount;

        return [
            'total_amount' => (float) round($totalAmount, 2),
            'delivery_charge' => (float) round($deliveryCharge, 2),
            'coupon_discount' => (float) round($couponDiscount, 2),
            'card_discount' => (float) round($cardDiscount, 2),
            'card_error' => $cardError,
            'order_tax_amount' => (float) round($totalOrderTaxAmount, 2),
            'payable_amount' => (float) round($payableAmount, 2),
            'all_vat_taxes' => $vatTaxesArray,
        ];
    }
}
