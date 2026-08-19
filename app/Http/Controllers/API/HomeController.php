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
        $targetBranchShops = $this->getEligibleBranchShops($request, $rootShop);
        $targetShopIds = $targetBranchShops->pluck('id')->toArray();

        $banners = BannerRepository::query()->whereNull('shop_id')->active()->get();

        $categories = CategoryRepository::query()->active()
            ->when($rootShop, function ($q) use ($rootShop) {
                return $q->whereHas('shops', function ($query) use ($rootShop) {
                    return $query->where('shop_id', $rootShop->id);
                });
            })->whereHas('products', function ($product) {
                return $product->where('is_active', true);
            })->withCount('products')->orderByDesc('products_count')->take(10)->get();

        // Round-robin selection across local branch shops (1 top product per shop in sequence without redundancy)
        $popularProducts = collect([]);
        $usedProductNames = collect([]);

        if (! empty($targetShopIds)) {
            $shopProductsMap = [];
            foreach ($targetBranchShops as $bShop) {
                $shopProductsMap[$bShop->id] = ProductRepository::query()
                    ->isActive()
                    ->where('shop_id', $bShop->id)
                    ->withCount('orders as orders_count')
                    ->withAvg('reviews as average_rating', 'rating')
                    ->orderByDesc('average_rating')
                    ->orderByDesc('orders_count')
                    ->take(10)
                    ->get();
            }

            for ($round = 0; $round < 10; $round++) {
                $addedInRound = false;
                foreach ($targetBranchShops as $bShop) {
                    if ($popularProducts->count() >= 12) {
                        break 2;
                    }
                    $items = $shopProductsMap[$bShop->id] ?? collect([]);
                    foreach ($items as $product) {
                        $normName = strtolower(trim($product->name));
                        if (! $usedProductNames->contains($normName)) {
                            $popularProducts->push($product);
                            $usedProductNames->push($normName);
                            $addedInRound = true;
                            break;
                        }
                    }
                }
                if (! $addedInRound) {
                    break;
                }
            }
        }

        // Just For You curated across active branch shops in vicinity without redundancy (excluding Shop 1)
        $justForYouQuery = ProductRepository::query()->isActive()
            ->when(! empty($targetShopIds), function ($query) use ($targetShopIds) {
                return $query->whereIn('shop_id', $targetShopIds)
                    ->whereIn('id', function ($subQuery) use ($targetShopIds) {
                        $subQuery->selectRaw('MAX(id)')
                            ->from('products')
                            ->where('is_active', true)
                            ->where('is_approve', true)
                            ->whereNull('deleted_at')
                            ->whereIn('shop_id', $targetShopIds)
                            ->groupBy('name');
                    });
            })
            ->latest('id');

        $total = $justForYouQuery->count();
        $justForYou = $justForYouQuery->skip($skip)->take($perPage)->get();

        $shops = collect([]);

        if ($generaleSetting?->shop_type != 'single') {
            $shops = $targetBranchShops->take(8);
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

    /**
     * Get eligible branch shops for the vicinity/city, strictly excluding Main Shop (Shop 1).
     */
    protected function getEligibleBranchShops(Request $request, ?Shop $rootShop)
    {
        $rootShopIds = array_values(array_unique(array_filter([1, $rootShop?->id])));

        $baseQuery = Shop::where('status', 1)
            ->whereNotIn('id', $rootShopIds)
            ->where('name', 'not like', '%Main Janmitram%');

        $city = $request->city;

        if (! empty($city)) {
            $cityTerm = strtolower(trim($city));
            $searchTerms = [$cityTerm];
            if (str_contains($cityTerm, 'jaipur')) {
                $searchTerms[] = 'jpr';
            } elseif ($cityTerm === 'jpr') {
                $searchTerms[] = 'jaipur';
            }

            $cityShops = (clone $baseQuery)->where(function ($q) use ($searchTerms) {
                foreach ($searchTerms as $term) {
                    $q->orWhere('name', 'like', "%{$term}%")
                        ->orWhere('address', 'like', "%{$term}%");
                }
            })->get();

            if ($cityShops->isNotEmpty()) {
                return $cityShops;
            }
        }

        // If no city specified or no local branch in that city, return all other active branch shops
        return $baseQuery->get();
    }
}
