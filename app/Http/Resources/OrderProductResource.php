<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'product_id' => $this->product_id,
            'product_name' => $this->product_name,
            'product_code' => $this->product_code,
            'product_image' => $this->product_image,
            'quantity' => $this->quantity,
            'unit_price' => (int) $this->unit_price,
            'discount_percent' => $this->discount_percent,
            'discount_amount' => (int) $this->discount_amount,
            'final_price' => (int) $this->final_price,
            'product_options' => $this->product_options,
        ];
    }
}
