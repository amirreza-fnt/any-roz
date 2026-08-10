<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MarketingBuyer extends Model
{
    protected $fillable = [
        'marketer_id',
        'first_name',
        'last_name',
        'phone',
        'address',
        'province_id',
        'city_id',
        'store_name',
        'postal_code',
        'latitude',
        'longitude',
        'notes',
    ];

    protected $casts = [
        'latitude' => 'float',
        'longitude' => 'float',
    ];

    public function province(): BelongsTo
    {
        return $this->belongsTo(Province::class);
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function sales(): HasMany
    {
        return $this->hasMany(MarketingSale::class, 'buyer_id');
    }

    public function getFullNameAttribute(): string
    {
        return trim($this->first_name.' '.$this->last_name);
    }

    public function getSelect2LabelAttribute(): string
    {
        $parts = array_filter([
            $this->full_name,
            $this->phone,
            $this->store_name,
        ]);

        return implode(' — ', $parts);
    }
}
