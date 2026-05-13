<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class TypeOfWeight extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'weight',
    ];

    protected $casts = [
        'weight' => 'integer',
    ];

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'product_type_of_weight', 'type_of_weight_id', 'product_id')
            ->withPivot('stock')
            ->withTimestamps();
    }
}
