<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ShippingConfigResource;
use App\Models\ShippingConfig;
use Illuminate\Http\JsonResponse;

class ShippingController extends Controller
{
    public function methods(): JsonResponse
    {
        $methods = ShippingConfig::where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        return response()->json([
            'data' => ShippingConfigResource::collection($methods),
        ]);
    }
}
