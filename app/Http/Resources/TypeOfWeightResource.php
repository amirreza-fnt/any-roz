<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TypeOfWeightResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'weight' => $this->weight,
            'pivot' => $this->when($this->pivot, function () {
                return [
                    'stock' => (int) $this->pivot->stock,
                    'price' => (int) $this->pivot->price,
                    'price_buy' => (int) $this->pivot->price_buy,
                    'price_discounted' => (int) $this->pivot->price_discounted,
                ];
            }),
        ];
    }
}
