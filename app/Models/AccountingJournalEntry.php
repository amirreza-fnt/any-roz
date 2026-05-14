<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AccountingJournalEntry extends Model
{
    public const KIND_INCOME = 'income';

    public const KIND_EXPENSE = 'expense';

    public const KIND_ADJUSTMENT = 'adjustment';

    protected $fillable = [
        'document_date',
        'document_no',
        'title',
        'kind',
        'subsidiary_code',
        'counterparty',
        'category',
        'journal_payment_method',
        'external_reference',
        'cost_center',
        'vat_rate',
        'vat_amount',
        'amount',
        'description',
        'admin_id',
    ];

    protected $casts = [
        'document_date' => 'date',
        'amount' => 'decimal:2',
        'vat_rate' => 'decimal:2',
        'vat_amount' => 'decimal:2',
    ];

    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'admin_id');
    }

    public static function kindLabel(string $kind): string
    {
        return match ($kind) {
            self::KIND_INCOME => 'درآمد / واریز',
            self::KIND_EXPENSE => 'هزینه / برداشت',
            self::KIND_ADJUSTMENT => 'تعدیل / یادداشت مالی',
            default => $kind,
        };
    }

    /** @return list<string> */
    public static function kinds(): array
    {
        return [self::KIND_INCOME, self::KIND_EXPENSE, self::KIND_ADJUSTMENT];
    }
}
