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

    public const SALE_TYPE_MARKETER = 'marketer';

    public const SALE_TYPE_COLLEAGUE = 'colleague';

    protected $fillable = [
        'marketer_id',
        'buyer_id',
        'sale_type',
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
        'sale_type' => 'string',
    ];

    public function buyer(): BelongsTo
    {
        return $this->belongsTo(MarketingBuyer::class, 'buyer_id');
    }

    public function colleague(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'marketer_id');
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
        return $this->belongsTo(Admin::class, 'reviewed_by');
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

    public function isColleagueSale(): bool
    {
        return $this->sale_type === self::SALE_TYPE_COLLEAGUE;
    }

    public function isMarketerSale(): bool
    {
        return $this->sale_type === self::SALE_TYPE_MARKETER;
    }

    public function canApprove(): bool
    {
        if ($this->status === self::STATUS_PENDING) {
            return true;
        }
        if ($this->status !== self::STATUS_REJECTED) {
            return false;
        }
        if (! $this->order_id) {
            return true;
        }
        $order = $this->relationLoaded('order') ? $this->order : $this->order()->first();

        return $order && $order->shipping_status === Order::STATUS_CANCELLED;
    }

    public function canReject(): bool
    {
        return in_array($this->status, [self::STATUS_PENDING, self::STATUS_APPROVED], true);
    }
}
