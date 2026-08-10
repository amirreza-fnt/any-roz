<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CityResource;
use App\Http\Resources\ProvinceResource;
use App\Models\City;
use App\Models\Province;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProvinceController extends Controller
{
    public function provinces(): JsonResponse
    {
        $provinces = Province::orderBy('name')->get();

        return response()->json([
            'data' => ProvinceResource::collection($provinces),
        ]);
    }

    public function cities(Request $request, Province $province): JsonResponse
    {
        $cities = $province->cities()->orderBy('name')->get();

        return response()->json([
            'data' => CityResource::collection($cities),
        ]);
    }
}
