<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;

class ArticlePublicController extends Controller
{
    public function show(Request $request, Article $article)
    {
        if (! $article->isPubliclyVisible()) {
            abort(404);
        }

        $article->increment('view_count');
        $article->load(['tags', 'category', 'author']);

        return view('frontend.article', compact('article'));
    }
}
