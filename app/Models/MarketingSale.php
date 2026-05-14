<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MarketingSale extends Model
{
    public const STATUS_PENDING = 'pending_accounting';

    public const STATUS_APPROVED = 'approved';

    public const STATUS_REJECTED = 'rejected';

    protected $fillable = [
        'marketer_id',
        'buyer_id',
        'status',
        'buyer_first_name',
        'buyer_last_name',
        'buyer_phone',
        'buyer_store_name',
        'payment_method',
        'notes',
        'sale_date',
        'accountant_note',
        'reviewed_at',
        'reviewed_by',
        'order_id',
    ];

    protected $casts = [
        'sale_date' => 'date',
        'reviewed_at' => 'datetime',
    ];

    public function buyer(): BelongsTo
    {
        return $this->belongsTo(MarketingBuyer::class, 'buyer_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(MarketingSaleItem::class)->orderBy('sort_order')->orderBy('id');
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public static function statusLabel(string $status): string
    {
        return match ($status) {
            self::STATUS_PENDING => 'در انتظار حسابداری',
            self::STATUS_APPROVED => 'تأیید شده',
            self::STATUS_REJECTED => 'رد شده',
            default => $status,
        };
    }

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }
}
