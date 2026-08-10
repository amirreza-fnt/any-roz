<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @php
        $pageTitle = $article->meta_title ?: $article->title;
        $desc = $article->meta_description ?: \Illuminate\Support\Str::limit(strip_tags($article->excerpt ?: $article->body), 160);
        $canonical = $article->canonical_url ?: url()->current();
        $og = $article->og_image_url ?: $article->featured_image_url;
    @endphp
    <title>{{ $pageTitle }}</title>
    <meta name="description" content="{{ $desc }}">
    @if ($article->meta_keywords)
        <meta name="keywords" content="{{ $article->meta_keywords }}">
    @endif
    @if ($article->noindex)
        <meta name="robots" content="noindex,nofollow">
    @else
        <meta name="robots" content="index,follow,max-image-preview:large">
    @endif
    <link rel="canonical" href="{{ $canonical }}">
    <meta property="og:type" content="article">
    <meta property="og:title" content="{{ $pageTitle }}">
    <meta property="og:description" content="{{ $desc }}">
    <meta property="og:url" content="{{ url()->current() }}">
    @if ($og)
        <meta property="og:image" content="{{ $og }}">
    @endif
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $pageTitle }}">
    <meta name="twitter:description" content="{{ $desc }}">
    @if ($og)
        <meta name="twitter:image" content="{{ $og }}">
    @endif
    <script type="application/ld+json">
{!! json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'Article',
    'headline' => $article->title,
    'description' => $desc,
    'datePublished' => optional($article->published_at ?? $article->created_at)->toIso8601String(),
    'dateModified' => optional($article->updated_at)->toIso8601String(),
    'author' => [
        '@type' => 'Person',
        'name' => $article->author?->name ?? 'نویسنده',
    ],
    'publisher' => [
        '@type' => 'Organization',
        'name' => config('app.name', 'Site'),
    ],
    'mainEntityOfPage' => [
        '@type' => 'WebPage',
        '@id' => url()->current(),
    ],
    'image' => $og ? [$og] : [],
    'articleSection' => $article->category?->title,
    'keywords' => $article->tags->pluck('name')->implode(', '),
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}
    </script>
    <style>
        :root { --ink:#0f172a; --muted:#64748b; --line:#e2e8f0; --accent:#4f46e5; }
        * { box-sizing: border-box; }
        body { margin:0; font-family: system-ui, -apple-system, "Segoe UI", Tahoma, sans-serif; color:var(--ink); background:#f8fafc; line-height:1.85; }
        .wrap { max-width: 820px; margin: 0 auto; padding: 2rem 1.25rem 3rem; }
        .hero { background: linear-gradient(120deg,#312e81,#4f46e5); color:#fff; border-radius: 18px; padding: 2rem 1.5rem; box-shadow: 0 18px 50px rgba(79,70,229,.25); }
        .hero h1 { margin:0; font-size: clamp(1.35rem, 3vw, 1.85rem); font-weight: 800; line-height:1.35; }
        .meta { margin-top: .85rem; font-size: .88rem; opacity: .92; display:flex; flex-wrap:wrap; gap:.5rem .85rem; }
        .sheet { margin-top: 1.5rem; background:#fff; border:1px solid var(--line); border-radius: 16px; padding: 1.5rem; box-shadow: 0 10px 30px rgba(15,23,42,.04); }
        .feat { width:100%; border-radius: 14px; margin-bottom:1.25rem; border:1px solid var(--line); object-fit:cover; max-height:420px; }
        .prose :where(h2,h3) { margin-top:1.5rem; margin-bottom:.5rem; color:#1e293b; }
        .prose p { margin: .75rem 0; }
        .tags { display:flex; flex-wrap:wrap; gap:.4rem; margin-top:1rem; }
        .tag { background:#eef2ff; color:#3730a3; padding:.25rem .6rem; border-radius:999px; font-size:.78rem; font-weight:600; }
        .back { display:inline-block; margin-top:1.5rem; color:var(--accent); text-decoration:none; font-weight:600; }
    </style>
</head>
<body>
<div class="wrap">
    <article>
        <header class="hero">
            <h1>{{ $article->title }}</h1>
            <div class="meta">
                @if ($article->published_at)
                    <span>تاریخ: {{ $article->published_at->format('Y/m/d') }}</span>
                @endif
                @if ($article->reading_minutes)
                    <span>زمان مطالعه: حدود {{ $article->reading_minutes }} دقیقه</span>
                @endif
                @if ($article->category)
                    <span>بخش: {{ $article->category->title }}</span>
                @endif
            </div>
        </header>
        <div class="sheet">
            @if ($article->featured_image_url)
                <img class="feat" src="{{ $article->featured_image_url }}" alt="{{ $article->title }}">
            @endif
            @if ($article->excerpt)
                <p style="font-size:1.05rem;color:#334155;font-weight:500;margin-top:0">{{ $article->excerpt }}</p>
            @endif
            <div class="prose">
                {!! $article->body !!}
            </div>
            @if ($article->tags->isNotEmpty())
                <div class="tags">
                    @foreach ($article->tags as $t)
                        <span class="tag">{{ $t->name }}</span>
                    @endforeach
                </div>
            @endif
        </div>
    </article>
    <a class="back" href="{{ url('/') }}">← بازگشت به خانه</a>
</div>
</body>
</html>
