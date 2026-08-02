<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StockAssignmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'from_warehouse_id' => ['required', 'exists:warehouses,id'],
            'shop_id' => ['required', 'exists:shops,id'],
            'notes' => ['nullable', 'string'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.color_id' => ['nullable', 'exists:colors,id'],
            'items.*.size_id' => ['nullable', 'exists:sizes,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
        ];
    }
}
