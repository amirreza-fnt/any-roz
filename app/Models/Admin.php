<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Admin extends Authenticatable
{
    use Notifiable;

    /**
     * Permissions auto-granted to a colleague (خرید کلی) by default.
     *
     * @var list<string>
     */
    public const COLLEAGUE_DEFAULT_PERMISSIONS = [
        'colleague.sales.view',
        'colleague.sales.create',
        'colleague.sales.delete',
        'colleague.chat.view',
        'colleague.chat.send',
    ];

    /**
     * Permissions auto-granted to a manager marked as "پشتیبان همکاران".
     *
     * @var list<string>
     */
    public const SUPPORT_DEFAULT_PERMISSIONS = [
        'colleague.support.view',
        'colleague.support.send',
    ];

    protected $table = 'admins';

    protected $fillable = [
        'first_name',
        'last_name',
        'phone',
        'email',
        'national_id',
        'father_name',
        'birth_date',
        'position',
        'store_name',
        'address',
        'postal_code',
        'province_id',
        'city_id',
        'password',
        'permissions',
        'is_active',
        'is_super',
        'is_colleague',
        'is_colleague_support',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'permissions' => 'array',
        'is_active' => 'boolean',
        'is_super' => 'boolean',
        'is_colleague' => 'boolean',
        'is_colleague_support' => 'boolean',
        'birth_date' => 'date',
        'province_id' => 'integer',
        'city_id' => 'integer',
        'password' => 'hashed',
    ];

    public function province(): BelongsTo
    {
        return $this->belongsTo(Province::class);
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function wholesaleSales(): HasMany
    {
        return $this->hasMany(MarketingSale::class, 'marketer_id')
            ->where('sale_type', MarketingSale::SALE_TYPE_COLLEAGUE);
    }

    public function wholesaleOrders(): HasMany
    {
        return $this->hasMany(Order::class, 'marketer_id');
    }

    public function getFullNameAttribute(): string
    {
        return trim($this->first_name.' '.$this->last_name);
    }

    public function isColleague(): bool
    {
        return (bool) $this->is_colleague;
    }

    public function isColleagueSupport(): bool
    {
        return (bool) $this->is_colleague_support;
    }

    public function hasPermission(string $key): bool
    {
        if ($this->is_super) {
            return true;
        }

        $list = $this->permissions ?? [];

        return in_array($key, $list, true);
    }

    public static function assignablePermissionKeys(): array
    {
        return self::COLLEAGUE_DEFAULT_PERMISSIONS;
    }
}
