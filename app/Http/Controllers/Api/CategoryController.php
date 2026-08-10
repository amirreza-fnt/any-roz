<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $categories = Category::where('status', 'active')
            ->withCount('articles')
            ->with('children')
            ->orderBy('id')
            ->get();

        return response()->json([
            'data' => CategoryResource::collection($categories),
        ]);
    }

    public function show(Request $request, Category $category): JsonResponse
    {
        if ($category->status !== 'active') {
            return response()->json(['message' => 'دسته‌بندی یافت نشد'], 404);
        }

        $category->load(['children', 'parent']);

        return response()->json([
            'data' => new CategoryResource($category),
        ]);
    }
}
