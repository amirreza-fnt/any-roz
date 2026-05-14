<?php

namespace App\Http\Controllers\Admin\Articles;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Category;
use App\Models\Tag;
use App\Support\PublicUploads;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ArticleController extends Controller
{
    public function index()
    {
        $articles = Article::with(['category', 'tags', 'author'])
            ->orderByDesc('id')
            ->get();

        return view('backend.article.IndexArticle', compact('articles'));
    }

    public function create()
    {
        $type = 'create';
        $article = null;
        $categories = Category::orderBy('title')->get();

        return view('backend.article.CreateOrUpdateArticle', compact('type', 'article', 'categories'));
    }

    public function store(Request $request)
    {
        $data = $this->validatedArticle($request);
        $slug = $this->uniqueSlug($data['title'], $data['slug'] ?? null, null);

        $featured = null;
        if ($request->hasFile('featured_image')) {
            $featured = PublicUploads::store($request->file('featured_image'), 'images/article');
        }
        $og = null;
        if ($request->hasFile('og_image')) {
            $og = PublicUploads::store($request->file('og_image'), 'images/article');
        } elseif ($featured) {
            $og = $featured;
        }

        $reading = $this->estimateReadingMinutes($data['body']);

        $article = Article::create([
            'title' => $data['title'],
            'slug' => $slug,
            'excerpt' => $data['excerpt'] ?? null,
            'body' => $data['body'],
            'featured_image' => $featured,
            'meta_title' => $data['meta_title'] ?: $data['title'],
            'meta_description' => $data['meta_description'] ?? null,
            'meta_keywords' => $data['meta_keywords'] ?? null,
            'canonical_url' => $data['canonical_url'] ?? null,
            'og_image' => $og,
            'focus_keyword' => $data['focus_keyword'] ?? null,
            'noindex' => (bool) ($request->boolean('noindex')),
            'status' => $data['status'],
            'published_at' => $data['published_at'] ?? null,
            'author_id' => \App\Models\User::query()->orderBy('id')->value('id'),
            'category_id' => $data['category_id'] ?? null,
            'reading_minutes' => $reading,
            'is_featured' => $request->boolean('is_featured'),
        ]);

        $this->syncTagsFromInput($article, $request->input('tags_input', ''));

        message('success', 'مقاله ثبت شد.');

        return redirect()->route('admin.articles.edit', $article);
    }

    public function show(Article $article)
    {
        return redirect()->route('admin.articles.edit', $article);
    }

    public function edit(Article $article)
    {
        $type = 'edit';
        $article->load(['tags']);
        $categories = Category::orderBy('title')->get();

        return view('backend.article.CreateOrUpdateArticle', compact('type', 'article', 'categories'));
    }

    public function update(Request $request, Article $article)
    {
        $data = $this->validatedArticle($request, $article->id);
        $slug = $this->uniqueSlug($data['title'], $data['slug'] ?? null, $article->id);

        $featured = $article->featured_image;
        if ($request->hasFile('featured_image')) {
            PublicUploads::delete($article->featured_image);
            $featured = PublicUploads::store($request->file('featured_image'), 'images/article');
        }
        $og = $article->og_image;
        if ($request->hasFile('og_image')) {
            PublicUploads::delete($article->og_image);
            $og = PublicUploads::store($request->file('og_image'), 'images/article');
        } elseif ($request->boolean('og_follow_featured') && $featured) {
            $og = $featured;
        }

        $reading = $this->estimateReadingMinutes($data['body']);

        $article->update([
            'title' => $data['title'],
            'slug' => $slug,
            'excerpt' => $data['excerpt'] ?? null,
            'body' => $data['body'],
            'featured_image' => $featured,
            'meta_title' => $data['meta_title'] ?: $data['title'],
            'meta_description' => $data['meta_description'] ?? null,
            'meta_keywords' => $data['meta_keywords'] ?? null,
            'canonical_url' => $data['canonical_url'] ?? null,
            'og_image' => $og,
            'focus_keyword' => $data['focus_keyword'] ?? null,
            'noindex' => (bool) ($request->boolean('noindex')),
            'status' => $data['status'],
            'published_at' => $data['published_at'] ?? null,
            'category_id' => $data['category_id'] ?? null,
            'reading_minutes' => $reading,
            'is_featured' => $request->boolean('is_featured'),
        ]);

        $this->syncTagsFromInput($article, $request->input('tags_input', ''));

        message('success', 'مقاله به‌روزرسانی شد.');

        return redirect()->route('admin.articles.edit', $article);
    }

    public function destroy(Article $article)
    {
        try {
            PublicUploads::delete($article->featured_image);
            PublicUploads::delete($article->og_image);
            $article->tags()->detach();
            $article->delete();
            message('success', 'مقاله حذف شد.');
        } catch (\Exception $e) {
            message('error', 'حذف با خطا مواجه شد.');
        }

        return redirect()->route('admin.articles.index');
    }

    public function togglePublish(Article $article)
    {
        if ($article->status === 'archived') {
            message('warning', 'مقالهٔ آرشیو شده را ابتدا از حالت آرشیو خارج کنید.');

            return redirect()->back();
        }

        if ($article->status === 'published') {
            $article->status = 'draft';
            $article->published_at = null;
            $article->save();
            message('success', 'مقاله به پیش‌نویس تغییر کرد.');
        } else {
            $article->status = 'published';
            $article->published_at = $article->published_at ?? now();
            $article->save();
            message('success', 'مقاله منتشر شد.');
        }

        return redirect()->back();
    }

    private function validatedArticle(Request $request, ?int $articleId = null): array
    {
        $slugRule = $articleId
            ? Rule::unique('articles', 'slug')->ignore($articleId)
            : Rule::unique('articles', 'slug');

        return $request->validate([
            'title' => 'required|string|max:255',
            'slug' => ['nullable', 'string', 'max:255', $slugRule],
            'excerpt' => 'nullable|string|max:2000',
            'body' => 'required|string',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string|max:512',
            'canonical_url' => 'nullable|string|max:512',
            'focus_keyword' => 'nullable|string|max:120',
            'status' => ['required', Rule::in(['draft', 'published', 'archived'])],
            'published_at' => 'nullable|date',
            'category_id' => 'nullable|exists:categories,id',
            'featured_image' => 'nullable|image|max:8192',
            'og_image' => 'nullable|image|max:8192',
        ]);
    }

    private function uniqueSlug(string $title, ?string $slugInput, ?int $ignoreId): string
    {
        if ($slugInput !== null && trim($slugInput) === '') {
            $slugInput = null;
        }

        $base = $slugInput ? Str::slug($slugInput) : Str::slug($title);
        if ($base === '') {
            $base = 'article-'.Str::lower(Str::random(8));
        }

        $slug = $base;
        $i = 1;
        while (Article::where('slug', $slug)->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))->exists()) {
            $slug = $base.'-'.$i;
            $i++;
        }

        return $slug;
    }

    private function estimateReadingMinutes(string $body): int
    {
        $text = strip_tags($body);
        $words = preg_split('/\s+/u', $text, -1, PREG_SPLIT_NO_EMPTY);

        return max(1, (int) ceil(count($words) / 200));
    }

    private function syncTagsFromInput(Article $article, string $raw): void
    {
        $raw = trim($raw);
        if ($raw === '') {
            $article->tags()->detach();

            return;
        }

        $parts = preg_split('/[,،]+/u', $raw, -1, PREG_SPLIT_NO_EMPTY);
        $ids = [];
        foreach ($parts as $part) {
            $name = trim($part);
            if ($name === '') {
                continue;
            }
            $ids[] = Tag::findOrCreateFromName($name)->id;
        }
        $article->tags()->sync(array_unique($ids));
    }
}
