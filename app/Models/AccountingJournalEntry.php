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
        'amount',
        'description',
        'admin_id',
    ];

    protected function casts(): array
    {
        return [
            'document_date' => 'date',
            'amount' => 'decimal:2',
        ];
    }

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
