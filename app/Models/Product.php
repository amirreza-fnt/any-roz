<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'title',
        'slug',
        'tracking_code',
        'category_id',
        'price',
        'price_buy',
        'price_discounted',
        'stock',
        'status',
        'suggested',
        'mini_description',
        'description',
    ];

    protected $casts = [
        'price' => 'integer',
        'price_buy' => 'integer',
        'price_discounted' => 'integer',
        'stock' => 'integer',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function typeOfWeights(): BelongsToMany
    {
        return $this->belongsToMany(TypeOfWeight::class, 'product_type_of_weight', 'product_id', 'type_of_weight_id')
            ->withPivot('stock')
            ->withTimestamps();
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order')->orderBy('id');
    }

    public function getPrimaryImageUrlAttribute(): ?string
    {
        $first = $this->images->first();

        return $first?->url;
    }
}
