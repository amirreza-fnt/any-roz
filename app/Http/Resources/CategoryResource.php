<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug ?? str()->slug($this->title),
            'image' => $this->image_url,
            'status' => $this->status,
            'parent_id' => $this->parent_id,
            'children' => self::collection($this->whenLoaded('children')),
            'products_count' => $this->when($this->products_count !== null, $this->products_count),
            'created_at' => $this->created_at,
        ];
    }
}
