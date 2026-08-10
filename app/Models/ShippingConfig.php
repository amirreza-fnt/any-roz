<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShippingConfig extends Model
{
    protected $fillable = [
        'name',
        'base_shipping_cost',
        'base_insurance_cost',
        'base_packaging_cost',
        'package_weight_limit',
        'extra_weight_cost',
        'is_active',
        'sort_order',
        'description',
        'additional_settings',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'additional_settings' => 'array',
    ];
}
