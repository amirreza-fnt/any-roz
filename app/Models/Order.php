<?php

namespace App\Models;

use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    public const STATUS_PENDING_REVIEW = 'pending_review';

    public const STATUS_PACKAGING = 'packaging';

    public const STATUS_SHIPPING = 'shipping';

    public const STATUS_COMPLETED = 'completed';

    public const STATUS_CANCELLED = 'cancelled';

    public const SHIPPING_STATUSES = [
        self::STATUS_PENDING_REVIEW,
        self::STATUS_PACKAGING,
        self::STATUS_SHIPPING,
        self::STATUS_COMPLETED,
        self::STATUS_CANCELLED,
    ];

    public const SOURCE_SITE = 'site';

    public const SOURCE_MARKETING = 'marketing';

    protected $fillable = [
        'user_id',
        'source',
        'marketer_id',
        'marketing_sale_id',
        'order_number',
        'total_amount',
        'shipping_fee',
        'discount_amount',
        'shipping_cost',
        'insurance_cost',
        'final_amount',
        'total_weight',
        'payment_status',
        'payment_method',
        'payment_transaction_id',
        'payment_date',
        'shipping_status',
        'shipping_method',
        'shipping_address',
        'shipping_city',
        'shipping_state',
        'shipping_postal_code',
        'shipping_recipient_name',
        'shipping_phone',
        'shipping_tracking_code',
        'notes',
        'sent_to_supply',
        'sent_to_supply_at',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'shipping_fee' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'shipping_cost' => 'decimal:2',
        'insurance_cost' => 'decimal:2',
        'final_amount' => 'decimal:2',
        'total_weight' => 'decimal:2',
        'payment_date' => 'datetime',
        'sent_to_supply' => 'boolean',
        'sent_to_supply_at' => 'datetime',
    ];

    public static function shippingStatusLabel(string $status): string
    {
        return match ($status) {
            self::STATUS_PENDING_REVIEW => 'در حال بررسی',
            self::STATUS_PACKAGING => 'در حال بسته‌بندی',
            self::STATUS_SHIPPING => 'در حال ارسال',
            self::STATUS_COMPLETED => 'اتمام رسیده',
            self::STATUS_CANCELLED => 'لغو شده',
            default => $status,
        };
    }

    public static function paymentStatusLabel(string $status): string
    {
        return match ($status) {
            'pending' => 'در انتظار پرداخت',
            'paid' => 'پرداخت شده',
            'failed' => 'ناموفق',
            'refunded' => 'بازگشت وجه',
            default => $status,
        };
    }

    /**
     * تاریخ رویداد مالی برای گزارش حسابداری: اگر «تاریخ پرداخت» معتبر (۱۹۹۰–۲۱۰۰ میلادی) باشد همان، وگرنه تاریخ ثبت سفارش.
     * (تاریخ پرداخت خراب/اشتباه باعث حذف فاکتور از بازهٔ گزارش نمی‌شود.)
     */
    public function accountingEventAt(): CarbonInterface
    {
        $created = $this->created_at ? Carbon::parse($this->created_at) : now();

        if ($this->payment_date) {
            $p = Carbon::parse($this->payment_date);
            $y = (int) $p->format('Y');
            if ($y >= 1990 && $y <= 2100) {
                return $p;
            }
        }

        return $created;
    }

    /** فاکتورهای کانال سایت (شامل رکوردهای قدیمی بدون فیلد منبع). */
    public function scopeSiteChannelAccounting(Builder $query): Builder
    {
        return $query->where(function ($w) {
            $w->where('source', self::SOURCE_SITE)
                ->orWhereNull('source')
                ->orWhere('source', '');
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderProduct::class);
    }

    public function histories(): HasMany
    {
        return $this->hasMany(OrderHistory::class, 'order_id')->orderByDesc('id');
    }
}
