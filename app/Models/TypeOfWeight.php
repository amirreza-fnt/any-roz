<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

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

    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'weight_id');
    }
}
