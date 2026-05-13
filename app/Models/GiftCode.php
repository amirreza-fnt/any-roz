<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GiftCode extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'code',
        'title',
        'description',
        'value_type',
        'amount',
        'percent',
        'max_amount',
        'min_order_amount',
        'usage_limit',
        'used_count',
        'per_user_limit',
        'starts_at',
        'expires_at',
        'applies_to',
        'category_ids',
        'product_ids',
        'status',
    ];

    protected $casts = [
        'amount' => 'integer',
        'percent' => 'integer',
        'max_amount' => 'integer',
        'min_order_amount' => 'integer',
        'usage_limit' => 'integer',
        'used_count' => 'integer',
        'per_user_limit' => 'integer',
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
        'category_ids' => 'array',
        'product_ids' => 'array',
    ];

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function valueLabel(): string
    {
        if ($this->value_type === 'fixed') {
            return number_format((int) $this->amount).' تومان';
        }

        $p = (int) $this->percent;
        $cap = $this->max_amount ? ' — سقف '.number_format((int) $this->max_amount).' تومان' : '';

        return $p.'٪ از مبلغ سفارش'.$cap;
    }
}
