<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ShippingConfigResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'base_shipping_cost' => (int) $this->base_shipping_cost,
            'base_insurance_cost' => (int) $this->base_insurance_cost,
            'base_packaging_cost' => (int) $this->base_packaging_cost,
            'package_weight_limit' => (int) $this->package_weight_limit,
            'extra_weight_cost' => (int) $this->extra_weight_cost,
            'extra_weight_per_kg' => $this->extra_weight_cost > 0 ? (int) $this->extra_weight_cost : 0,
            'description' => $this->description,
            'sort_order' => $this->sort_order,
            'is_active' => $this->is_active,
        ];
    }
}
