<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderProduct extends Model
{
    protected $fillable = [
        'order_id',
        'product_id',
        'product_name',
        'product_code',
        'product_image',
        'quantity',
        'unit_price',
        'discount_percent',
        'discount_amount',
        'final_price',
        'product_options',
    ];

    protected $casts = [
        'product_options' => 'array',
        'quantity' => 'integer',
        'discount_percent' => 'integer',
        'discount_amount' => 'integer',
        'final_price' => 'integer',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
