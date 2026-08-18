<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Http\Requests\ReviewRequest;
use App\Models\Product;
use App\Models\Review;
use App\Support\Repositories\Repository;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ReviewRepository extends Repository
{
    /**
     * Base method
     */
    public static function model(): string
    {
        return Review::class;
    }

    /**
     * Store a new review based on the request data and product.
     */
    public static function storeByRequest(ReviewRequest $request, Product $product): Review
    {
        $photos = [];
        if ($request->has('photos') && is_array($request->photos)) {
            foreach ($request->photos as $photo) {
                if ($photo instanceof UploadedFile) {
                    $path = $photo->store('reviews', 'public');
                    $photos[] = Storage::url($path);
                } elseif (is_string($photo) && ! empty($photo)) {
                    $photos[] = $photo;
                }
            }
        }

        return self::create([
            'customer_id' => auth()->user()->customer->id,
            'product_id' => $product->id,
            'shop_id' => $product->shop?->id,
            'order_id' => $request->order_id,
            'rating' => $request->rating,
            'description' => $request->description,
            'photos' => ! empty($photos) ? $photos : null,
            'is_active' => false, // Requires Admin Moderation & Approval
        ]);
    }
}
