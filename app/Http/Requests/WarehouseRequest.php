<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class WarehouseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        $id = $this->route('warehouse')?->id;

        return [
            'name' => ['required', 'string', 'max:191'],
            'address' => ['nullable', 'string'],
        ];
    }
}
