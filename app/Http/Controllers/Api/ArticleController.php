<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ArticleListResource;
use App\Http\Resources\ArticleResource;
use App\Models\Article;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Article::published()
            ->with(['category', 'tags'])
            ->latest('published_at');

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->boolean('featured')) {
            $query->where('is_featured', true);
        }

        $perPage = min((int) $request->input('per_page', 12), 50);
        $articles = $query->paginate($perPage);

        return response()->json([
            'data' => ArticleListResource::collection($articles),
            'meta' => [
                'current_page' => $articles->currentPage(),
                'last_page' => $articles->lastPage(),
                'per_page' => $articles->perPage(),
                'total' => $articles->total(),
            ],
        ]);
    }

    public function show(Request $request, Article $article): JsonResponse
    {
        if (!$article->isPubliclyVisible()) {
            return response()->json(['message' => 'مقاله یافت نشد'], 404);
        }

        $article->increment('view_count');
        $article->load(['tags', 'category', 'author']);

        $related = Article::published()
            ->where('id', '!=', $article->id)
            ->where(function ($q) use ($article) {
                $q->where('category_id', $article->category_id)
                  ->orWhereIn('id', $article->tags->pluck('id'));
            })
            ->take(4)
            ->get();

        return response()->json([
            'data' => new ArticleResource($article),
            'related_articles' => ArticleListResource::collection($related),
        ]);
    }
}
