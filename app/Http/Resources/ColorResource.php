<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ColorResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $lang = request()->header('accept-language') ?? 'en';

        $price = $this->pivot->price ?? 0;

        $translation = $lang != 'en' ? $this->translations()?->where('lang', $lang)->first() : null;

        return [
            'id' => $this->id,
            'name' => $translation ? $translation->name : $this->name,
            'color_code' => $this->color_code,
            'price' => (float) number_format($price, 2, '.', ''),
        ];
    }
}
