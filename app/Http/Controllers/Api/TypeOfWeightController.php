<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\TypeOfWeightResource;
use App\Models\TypeOfWeight;
use Illuminate\Http\JsonResponse;

class TypeOfWeightController extends Controller
{
    public function index(): JsonResponse
    {
        $types = TypeOfWeight::orderBy('weight')->get();

        return response()->json([
            'data' => TypeOfWeightResource::collection($types),
        ]);
    }
}
