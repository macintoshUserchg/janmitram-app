<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Models\Product;
use App\Models\Shop;
use Illuminate\Foundation\Http\FormRequest;

class StockRequestRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'notes' => ['nullable', 'string'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.color_id' => ['nullable', 'exists:colors,id'],
            'items.*.size_id' => ['nullable', 'exists:sizes,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
        ];
    }

    /**
     * Configure the validator instance with initial stock request threshold checks.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if ($validator->errors()->isNotEmpty()) {
                return;
            }

            $user = auth()->user();
            $shop = $user?->shop ?? $user?->myShop ?? generaleSetting('shop');

            if ($shop && $shop->isFirstStockTransfer()) {
                $totalValue = 0.0;
                foreach ($this->items ?? [] as $item) {
                    $qty = (int) ($item['quantity'] ?? 0);
                    if ($qty > 0 && ! empty($item['product_id'])) {
                        $product = Product::find($item['product_id']);
                        if ($product) {
                            $price = (float) ($product->discount_price > 0 ? $product->discount_price : $product->price);
                            $totalValue += $qty * $price;
                        }
                    }
                }

                if ($totalValue < Shop::MIN_FIRST_STOCK_TRANSFER_AMOUNT) {
                    $validator->errors()->add(
                        'items',
                        __(
                            'Your first initial stock request must be valued at :min or above. Current total value is :current.',
                            [
                                'min' => showCurrency(Shop::MIN_FIRST_STOCK_TRANSFER_AMOUNT),
                                'current' => showCurrency($totalValue),
                            ]
                        )
                    );
                }
            }
        });
    }
}
