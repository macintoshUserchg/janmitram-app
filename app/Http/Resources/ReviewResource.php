<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReviewResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'customer_name' => $this->customer?->user?->name ?? 'Janmitram Customer',
            'customer_profile' => $this->customer?->user?->thumbnail ?? asset('default/default.jpg'),
            'product_id' => $this->product_id,
            'product_name' => $this->product?->name,
            'shop_id' => $this->shop_id,
            'shop_name' => $this->shop?->name,
            'rating' => (float) $this->rating,
            'description' => $this->description,
            'photos' => $this->photos ?? [],
            'reply' => $this->reply,
            'replied_at' => $this->replied_at?->format('F d, Y'),
            'is_active' => (bool) $this->is_active,
            'created_at' => $this->created_at?->format('F d, Y'),
        ];
    }
}
