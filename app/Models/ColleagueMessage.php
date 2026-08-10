<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ColleagueMessage extends Model
{
    public const SENDER_COLLEAGUE = 'colleague';

    public const SENDER_SUPPORT = 'support';

    protected $fillable = [
        'colleague_id',
        'sender_type',
        'sender_id',
        'body',
        'read_at',
    ];

    protected $casts = [
        'read_at' => 'datetime',
    ];

    public function colleague(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'colleague_id');
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'sender_id');
    }

    public function isFromColleague(): bool
    {
        return $this->sender_type === self::SENDER_COLLEAGUE;
    }
}