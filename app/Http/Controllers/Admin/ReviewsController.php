<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use App\Models\Shop;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReviewsController extends Controller
{
    /**
     * Display a listing of customer reviews with moderation controls and KPI metrics.
     */
    public function index(Request $request): View
    {
        $status = $request->query('status', 'all');
        $shopId = $request->query('shop_id');
        $search = $request->query('search');
        $rating = $request->query('rating');

        $baseQuery = Review::withoutGlobalScopes()
            ->with(['customer.user', 'product', 'shop', 'order']);

        // KPI Counts
        $totalReviews = (clone $baseQuery)->count();
        $pendingReviews = (clone $baseQuery)->where('is_active', false)->count();
        $approvedReviews = (clone $baseQuery)->where('is_active', true)->count();
        $averageRating = (clone $baseQuery)->where('is_active', true)->avg('rating') ?? 0.0;

        $reviews = $baseQuery
            ->when($status === 'pending', function ($query) {
                return $query->where('is_active', false);
            })
            ->when($status === 'approved', function ($query) {
                return $query->where('is_active', true);
            })
            ->when($shopId, function ($query) use ($shopId) {
                return $query->where('shop_id', $shopId);
            })
            ->when($rating, function ($query) use ($rating) {
                return $query->where('rating', (float) $rating);
            })
            ->when($search, function ($query) use ($search) {
                return $query->where(function ($q) use ($search) {
                    $q->where('description', 'like', "%{$search}%")
                        ->orWhereHas('product', function ($pq) use ($search) {
                            $pq->where('name', 'like', "%{$search}%");
                        })
                        ->orWhereHas('customer.user', function ($uq) use ($search) {
                            $uq->where('name', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%");
                        });
                });
            })
            ->latest('id')
            ->paginate(20)
            ->withQueryString();

        $shops = Shop::orderBy('name')->get();

        return view('admin.reviews.index', compact(
            'reviews',
            'status',
            'shopId',
            'search',
            'rating',
            'totalReviews',
            'pendingReviews',
            'approvedReviews',
            'averageRating',
            'shops'
        ));
    }

    /**
     * Toggle the active/approved status of a review.
     */
    public function toggleReview($reviewId): RedirectResponse
    {
        $review = Review::withoutGlobalScopes()->findOrFail($reviewId);

        $review->update([
            'is_active' => ! $review->is_active,
        ]);

        $message = $review->is_active ? __('Review approved and activated successfully') : __('Review deactivated successfully');

        return back()->withSuccess($message);
    }

    /**
     * Approve a customer review.
     */
    public function approve($reviewId): RedirectResponse
    {
        $review = Review::withoutGlobalScopes()->findOrFail($reviewId);

        $review->update([
            'is_active' => true,
        ]);

        return back()->withSuccess(__('Review #:id approved and published successfully.', ['id' => $review->id]));
    }

    /**
     * Reject a customer review.
     */
    public function reject($reviewId): RedirectResponse
    {
        $review = Review::withoutGlobalScopes()->findOrFail($reviewId);

        $review->update([
            'is_active' => false,
        ]);

        return back()->withSuccess(__('Review #:id rejected and hidden from public catalog.', ['id' => $review->id]));
    }

    /**
     * Submit or update an official admin reply to a review.
     */
    public function reply(Request $request, $reviewId): RedirectResponse
    {
        $request->validate([
            'reply' => ['required', 'string', 'max:1000'],
        ]);

        $review = Review::withoutGlobalScopes()->findOrFail($reviewId);

        $review->update([
            'reply' => $request->reply,
            'replied_at' => now(),
        ]);

        return back()->withSuccess(__('Official response saved successfully for review #:id.', ['id' => $review->id]));
    }

    /**
     * Delete a review.
     */
    public function destroy($reviewId): RedirectResponse
    {
        $review = Review::withoutGlobalScopes()->findOrFail($reviewId);
        $review->delete();

        return back()->withSuccess(__('Review deleted successfully.'));
    }
}
