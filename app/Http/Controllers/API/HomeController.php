<?php

namespace App\Http\Controllers\API;

use App\Enums\Roles;
use App\Http\Controllers\Controller;
use App\Http\Resources\BannerResource;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\FlashSaleResource;
use App\Http\Resources\ProductResource;
use App\Http\Resources\ShopResource;
use App\Models\Ad;
use App\Models\GeneraleSetting;
use App\Models\Shop;
use App\Models\User;
use App\Repositories\BannerRepository;
use App\Repositories\CategoryRepository;
use App\Repositories\FlashSaleRepository;
use App\Repositories\ProductRepository;
use App\Repositories\ShopRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Index method for retrieving banners, categories, and popular products.
     *
     * @return Some_Return_Value
     */
    public function index(Request $request)
    {
        $page = $request->page ?? 1;
        $perPage = $request->per_page ?? 8;
        $skip = ($page * $perPage) - $perPage;
        $generaleSetting = generaleSetting('setting');
        $rootShop = generaleSetting('rootShop');
        $shop = null;

        if ($generaleSetting?->shop_type == 'single') {
            $shop = $rootShop;
        } elseif ($request->filled('shop_id')) {
            $shop = Shop::where('status', 1)->find($request->shop_id);
        } elseif ($request->filled('latitude') && $request->filled('longitude')) {
            $lat = (float) $request->latitude;
            $lng = (float) $request->longitude;
            $allShops = Shop::where('status', 1)->whereNotNull('latitude')->whereNotNull('longitude')->get();
            if ($allShops->isNotEmpty()) {
                $closest = $allShops->sortBy(function ($s) use ($lat, $lng) {
                    return haversineKm($lat, $lng, (float) $s->latitude, (float) $s->longitude);
                })->first();
                if ($closest) {
                    $shop = $closest;
                }
            }
        }

        $banners = BannerRepository::query()->whereNull('shop_id')->active()->get();

        $categories = CategoryRepository::query()->active()
            ->when($rootShop, function ($q) use ($rootShop) {
                return $q->whereHas('shops', function ($query) use ($rootShop) {
                    return $query->where('shop_id', $rootShop->id);
                });
            })->whereHas('products', function ($product) {
                return $product->where('is_active', true);
            })->withCount('products')->orderByDesc('products_count')->take(10)->get();

        $popularProducts = ProductRepository::query()->isActive()
            ->when($shop, function ($query) use ($shop) {
                return $query->where('shop_id', $shop->id);
            })->when(! $shop, function ($query) {
                return $query->whereIn('id', function ($subQuery) {
                    $subQuery->selectRaw('MAX(id)')
                        ->from('products')
                        ->where('is_active', true)
                        ->where('is_approve', true)
                        ->whereNull('deleted_at')
                        ->groupBy('name');
                });
            })->withCount('orders as orders_count')
            ->withAvg('reviews as average_rating', 'rating')
            ->orderByDesc('average_rating')
            ->orderByDesc('orders_count')
            ->take(6)->get();

        $justForYou = ProductRepository::query()->isActive()->latest('id')
            ->when($shop, function ($query) use ($shop) {
                return $query->where('shop_id', $shop->id);
            })->when(! $shop, function ($query) {
                return $query->whereIn('id', function ($subQuery) {
                    $subQuery->selectRaw('MAX(id)')
                        ->from('products')
                        ->where('is_active', true)
                        ->where('is_approve', true)
                        ->whereNull('deleted_at')
                        ->groupBy('name');
                });
            });
        $total = $justForYou->count();
        $justForYou = $justForYou->skip($skip)->take($perPage)->get();

        $shops = collect([]);

        if ($generaleSetting?->shop_type != 'single') {
            $shops = ShopRepository::query()->isActive()->whereHas('products', function ($query) {
                return $query->isActive();
            })->withCount('orders')->withAvg('reviews as average_rating', 'rating')->orderByDesc('average_rating')->orderByDesc('orders_count')->take(8)->get();
        }

        $ads = Ad::where('status', 1)->latest('id')->take(2)->get();

        // get incoming flash sale
        $incomingFlashSale = FlashSaleRepository::getIncoming();

        // get running flash sale
        $runningFlashSale = FlashSaleRepository::getRunning();

        return $this->json('home', [
            'banners' => BannerResource::collection($banners),
            'ads' => BannerResource::collection($ads),
            'categories' => CategoryResource::collection($categories),
            'shops' => ShopResource::collection($shops),
            'popular_products' => ProductResource::collection($popularProducts),
            'just_for_you' => [
                'total' => $total,
                'products' => ProductResource::collection($justForYou),
            ],
            'incoming_flash_sale' => $incomingFlashSale ? FlashSaleResource::make($incomingFlashSale) : null,
            'running_flash_sale' => $runningFlashSale ? FlashSaleResource::make($runningFlashSale)->toArray(request(), 'true', 'true') : null,
        ]);
    }

    /**
     * Get recently viewed products for the current user.
     *
     * @return JsonResponse
     */
    public function recentlyViews()
    {
        $generaleSetting = GeneraleSetting::first();

        $shop = null;
        if ($generaleSetting?->shop_type == 'single') {
            $shop = User::role(Roles::ROOT->value)->first()?->shop;
        }

        /**
         * @var User $user
         */
        $user = auth()->user();

        $products = $user->recentlyViewedProducts()->when($shop, function ($query) use ($shop) {
            return $query->where('shop_id', $shop->id);
        })->where('is_active', true)->orderBy('pivot_updated_at', 'desc')->take(10)->get();

        return $this->json('recently viewed products', [
            'products' => ProductResource::collection($products),
        ]);
    }
}
