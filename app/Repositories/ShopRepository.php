<?php

namespace App\Repositories;

use App\Http\Requests\ShopCreateRequest;
use App\Models\Shop;
use App\Models\ShopKyc;
use App\Support\Repositories\Repository;
use Carbon\Carbon;

class ShopRepository extends Repository
{
    /**
     * base method
     *
     * @method model()
     */
    public static function model()
    {
        return Shop::class;
    }

    /**
     * new shop creation by request.
     */
    public static function storeByRequest(ShopCreateRequest $request): Shop
    {
        // create new user
        $user = UserRepository::registerNewUser($request);

        // assign role
        $user->assignRole('shop');

        // create wallet
        WalletRepository::storeByRequest($user);

        // shop logo
        $thumbnail = MediaRepository::storeByRequest($request->shop_logo, 'shops/logo', 'image');

        // shop banner
        $banner = null;
        if ($request->hasFile('shop_banner')) {
            $banner = MediaRepository::storeByRequest($request->shop_banner, 'shops/banner', 'image');
        }

        // resolve parent / sponsor shop in MLM network
        $parentShopId = $request->parent_shop_id;
        if (! $parentShopId && $request->filled('ref')) {
            $sponsor = Shop::findByReferralCode($request->ref);
            $parentShopId = $sponsor?->id;
        } elseif (! $parentShopId && $request->filled('sponsor_code')) {
            $sponsor = Shop::findByReferralCode($request->sponsor_code);
            $parentShopId = $sponsor?->id;
        }

        // create new shop and return
        $shop = self::create([
            'user_id' => $user->id,
            'name' => $request->shop_name,
            'logo_id' => $thumbnail ? $thumbnail->id : null,
            'banner_id' => $banner ? $banner->id : null,
            'delivery_charge' => $request->delivery_charge ?? 0,
            'address' => $request->address,
            'description' => $request->description,
            'status' => true,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'warehouse_id' => $request->warehouse_id,
            'parent_shop_id' => $parentShopId,
        ]);

        // store KYC (Aadhaar / PAN / bank) when present — shared callers
        // (admin create, API seller register, downline create) send none.
        if ($request->hasFile('aadhaar_card') || $request->hasFile('pan_card')
            || $request->hasFile('other_documents') || $request->filled('aadhaar_number')) {
            $aadhaarCard = $request->hasFile('aadhaar_card')
                ? MediaRepository::storeByRequest($request->aadhaar_card, 'shops/kyc', 'image')
                : null;
            $panCard = $request->hasFile('pan_card')
                ? MediaRepository::storeByRequest($request->pan_card, 'shops/kyc', 'image')
                : null;
            $otherDocs = $request->hasFile('other_documents')
                ? MediaRepository::storeByRequest($request->other_documents, 'shops/kyc', 'image')
                : null;

            ShopKyc::create([
                'shop_id' => $shop->id,
                'aadhaar_card_id' => $aadhaarCard?->id,
                'pan_card_id' => $panCard?->id,
                'other_documents_id' => $otherDocs?->id,
                'aadhaar_number' => $request->aadhaar_number,
                'pan_number' => $request->pan_number,
                'bank_name' => $request->bank_name,
                'ifsc' => $request->ifsc,
                'account_number' => $request->account_number,
                'qualification' => $request->qualification,
            ]);
        }

        return $shop;
    }

    /**
     * update shop by request.
     */
    public static function updateByRequest($shop, $request): Shop
    {
        // update shop user
        UserRepository::updateByRequest($request, $shop->user);

        // shop logo
        $thumbnail = self::updateLogo($shop, $request);

        // shop banner
        $banner = self::updateBanner($shop, $request);

        // update shop
        self::update($shop, [
            'name' => $request->shop_name,
            'logo_id' => $thumbnail ? $thumbnail->id : null,
            'banner_id' => $banner ? $banner->id : null,
            'delivery_charge' => $request->delivery_charge ?? 0,
            'address' => $request->address,
            'description' => $request->description,
            'parent_shop_id' => $request->has('parent_shop_id') ? $request->parent_shop_id : $shop->parent_shop_id,
            'min_order_amount' => $request->min_order_amount ?? $shop->min_order_amount,
            'prefix' => $request->prefix ?? $shop->prefix,
            'opening_time' => $request->opening_time ?? $shop->opening_time,
            'closing_time' => $request->closing_time ?? $shop->closing_time,
            'estimated_delivery_time' => $request->estimated_delivery_time ?? $shop->estimated_delivery_time,
            'latitude' => $request->latitude ?? $shop->latitude,
            'longitude' => $request->longitude ?? $shop->longitude,
            'warehouse_id' => $request->warehouse_id ?? $shop->warehouse_id,
        ]);

        return $shop;
    }

    public static function updateShopSetting($shop, $request): Shop
    {
        $openTime = $request->opening_time ? Carbon::parse($request->opening_time)->format('H:i:s') : $shop->opening_time;
        $closeTime = $request->closing_time ? Carbon::parse($request->closing_time)->format('H:i:s') : $shop->closing_time;
        // update shop
        self::update($shop, [
            'delivery_charge' => $request->delivery_charge ?? 0,
            'min_order_amount' => $request->min_order_amount ?? $shop->min_order_amount,
            'prefix' => $request->prefix ?? $shop->prefix,
            'opening_time' => $openTime,
            'closing_time' => $closeTime,
            'estimated_delivery_time' => $request->estimated_delivery_time ?? $shop->estimated_delivery_time,
            'off_day' => $request->off_day ? array_map(function ($value) {
                return strtolower($value);
            }, $request->off_day) : null,
            'latitude' => $request->latitude ?? $shop->latitude,
            'longitude' => $request->longitude ?? $shop->longitude,

        ]);

        return $shop;
    }

    public static function updateShopInfo($shop, $request): Shop
    {
        // shop logo
        $thumbnail = self::updateLogo($shop, $request);

        // shop banner
        $banner = self::updateBanner($shop, $request);

        // update shop
        self::update($shop, [
            'name' => $request->name,
            'logo_id' => $thumbnail ? $thumbnail->id : null,
            'banner_id' => $banner ? $banner->id : null,
            'address' => $request->address,
            'description' => $request->description,
            'latitude' => $request->latitude ?? $shop->latitude,
            'longitude' => $request->longitude ?? $shop->longitude,
        ]);

        return $shop;
    }

    /**
     * Update or create a logo for the shop.
     */
    private static function updateLogo($shop, $request)
    {
        $thumbnail = $shop?->mediaLogo;
        // if logo and thumbnail is not null
        if ($request->hasFile('shop_logo')) {
            // update logo from mediaRepository
            $thumbnail = MediaRepository::updateByRequest(
                $request->shop_logo,
                'shops/logo',
                'image',
                $thumbnail
            );
        }

        return $thumbnail;
    }

    /**
     * Update or create a banner for the shop.
     */
    private static function updateBanner($shop, $request)
    {
        $thumbnail = $shop?->mediaBanner;
        // if banner and thumbnail is not null
        if ($request->hasFile('shop_banner')) {
            // update banner from mediaRepository
            $thumbnail = MediaRepository::updateByRequest(
                $request->shop_banner,
                'shops/banner',
                'image',
                $thumbnail
            );
        }

        return $thumbnail;
    }
}
