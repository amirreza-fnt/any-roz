<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MarketingSaleItem extends Model
{
    protected $fillable = [
        'marketing_sale_id',
        'product_id',
        'unit_price',
        'quantity_text',
        'sort_order',
    ];

    protected $casts = [
        'unit_price' => 'integer',
        'sort_order' => 'integer',
    ];

    public function sale(): BelongsTo
    {
        return $this->belongsTo(MarketingSale::class, 'marketing_sale_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
