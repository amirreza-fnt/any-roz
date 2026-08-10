<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductListResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'tracking_code' => $this->tracking_code,
            'category_id' => $this->category_id,
            'category' => new CategoryResource($this->whenLoaded('category')),
            'price' => (int) $this->price,
            'price_discounted' => (int) $this->price_discounted,
            'stock' => (int) $this->stock,
            'status' => $this->status,
            'suggested' => $this->suggested,
            'mini_description' => $this->mini_description,
            'primary_image' => $this->primary_image_url,
            'images' => ProductImageResource::collection($this->whenLoaded('images')),
            'type_of_weights' => TypeOfWeightResource::collection($this->whenLoaded('typeOfWeights')),
            'created_at' => $this->created_at,
        ];
    }
}
