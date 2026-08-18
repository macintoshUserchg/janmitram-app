<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\ReviewResource;
use App\Models\Product;
use App\Repositories\ProductRepository;
use App\Repositories\ReviewRepository;
use App\Repositories\ShopRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    /**
     * Retrieve a paginated list of approved reviews based on the provided request parameters.
     */
    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'product_id' => 'nullable|exists:products,id',
            'shop_id' => 'nullable|exists:shops,id',
        ]);

        $productID = $request->product_id;
        $shopID = $request->shop_id;

        $page = (int) ($request->page ?? 1);
        $perPage = (int) ($request->per_page ?? 10);
        $skip = ($page * $perPage) - $perPage;

        $productIds = [];
        if ($productID) {
            $product = Product::find($productID);
            if ($product) {
                $masterId = $product->master_product_id ?? $product->id;
                $productIds = Product::where('id', $masterId)
                    ->orWhere('master_product_id', $masterId)
                    ->pluck('id')
                    ->toArray();
            } else {
                $productIds = [$productID];
            }
        }

        $reviewsQuery = ReviewRepository::query()
            ->when(! empty($productIds), function ($query) use ($productIds) {
                return $query->whereIn('product_id', $productIds);
            })
            ->when($shopID, function ($query) use ($shopID) {
                return $query->where('shop_id', $shopID);
            })
            ->with(['customer.user', 'product', 'shop'])
            ->latest('id');

        $total = $reviewsQuery->count();

        $reviews = (clone $reviewsQuery)
            ->when($perPage && $page, function ($query) use ($perPage, $skip) {
                return $query->skip($skip)->take($perPage);
            })
            ->get();

        $shopOrProduct = null;
        if ($request->shop_id) {
            $shopOrProduct = ShopRepository::findOrFail($request->shop_id);
        } elseif ($request->product_id) {
            $shopOrProduct = ProductRepository::findOrFail($request->product_id);
        }

        $averageRatingAndPercentage = null;
        if ($shopOrProduct) {
            $approvedReviewsQuery = ! empty($productIds)
                ? ReviewRepository::query()->whereIn('product_id', $productIds)
                : $shopOrProduct->reviews();

            $totalReview = $approvedReviewsQuery->count();
            $avgRating = $totalReview > 0 ? (float) $approvedReviewsQuery->avg('rating') : 0.0;
            $averageRating = number_format($avgRating > 0 ? $avgRating : ($request->shop_id ? 5.0 : 0.0), 1, '.', '');

            $ratingOne = (clone $approvedReviewsQuery)->whereBetween('rating', [1.0, 1.9])->count();
            $ratingTwo = (clone $approvedReviewsQuery)->whereBetween('rating', [2.0, 2.9])->count();
            $ratingThree = (clone $approvedReviewsQuery)->whereBetween('rating', [3.0, 3.9])->count();
            $ratingFour = (clone $approvedReviewsQuery)->whereBetween('rating', [4.0, 4.9])->count();
            $ratingFive = (clone $approvedReviewsQuery)->where('rating', 5)->count();

            $percentageOne = $totalReview ? (($ratingOne / $totalReview) * 100) : 0;
            $percentageTwo = $totalReview ? (($ratingTwo / $totalReview) * 100) : 0;
            $percentageThree = $totalReview ? (($ratingThree / $totalReview) * 100) : 0;
            $percentageFour = $totalReview ? (($ratingFour / $totalReview) * 100) : 0;
            $percentageFive = $totalReview ? (($ratingFive / $totalReview) * 100) : 0;

            $averageRatingAndPercentage = [
                'rating' => (float) $averageRating,
                'total_review' => (int) $totalReview,
                'percentages' => [
                    '1' => (float) number_format($percentageOne, 2, '.', ''),
                    '2' => (float) number_format($percentageTwo, 2, '.', ''),
                    '3' => (float) number_format($percentageThree, 2, '.', ''),
                    '4' => (float) number_format($percentageFour, 2, '.', ''),
                    '5' => (float) number_format($percentageFive, 2, '.', ''),
                ],
            ];
        }

        return $this->json('reviews', [
            'average_rating_percentage' => $averageRatingAndPercentage,
            'total' => $total,
            'reviews' => ReviewResource::collection($reviews),
        ]);
    }
}
