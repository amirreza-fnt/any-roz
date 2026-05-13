@extends('backend.views.view')

@php
    $isEdit = ($type ?? '') === 'edit';
    $pageTitle = $isEdit ? 'ویرایش مقاله' : 'افزودن مقاله';
    $formAction = $isEdit ? route('admin.articles.update', $article) : route('admin.articles.store');
    $tagsDefault = old('tags_input', $isEdit ? $article->tags->pluck('name')->implode('، ') : '');
@endphp

@push('styles')
<style>
    .seo-card { border-radius: 14px; border: 1px solid #e5e7eb; overflow: hidden; }
    .seo-card .head { background: linear-gradient(120deg,#312e81,#4f46e5); color:#fff; padding:.85rem 1rem; font-weight:700; font-size:.9rem; }
    .seo-card .body { padding: 1rem; background: #fafafa; }
    .art-preview { border: 1px dashed #cbd5e1; border-radius: 12px; padding: .75rem; background: #fff; }
    .art-preview img { max-height: 120px; border-radius: 10px; object-fit: cover; }
    .hint { font-size: .75rem; color: #64748b; }
</style>
@endpush

@section('main')
<div class="main-content">
    <div class="container">
        <div class="page-header">
            <h4>{{ $pageTitle }}</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">خانه</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.articles.index') }}">مقالات</a></li>
                    <li class="breadcrumb-item active">{{ $pageTitle }}</li>
                </ol>
            </nav>
        </div>

        <form action="{{ $formAction }}" method="post" enctype="multipart/form-data" class="card border-0 shadow-sm">
            @csrf
            @if ($isEdit)
                @method('PUT')
            @endif

            <div class="card-body">
                <div class="row">
                    <div class="col-lg-8">
                        <div class="form-group">
                            <label>عنوان <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control" value="{{ old('title', $article->title ?? '') }}" required maxlength="255">
                        </div>
                        <div class="form-group">
                            <label>اسلاگ (اختیاری)</label>
                            <input type="text" name="slug" class="form-control text-monospace" value="{{ old('slug', $article->slug ?? '') }}" maxlength="255" placeholder="اگر خالی باشد از عنوان ساخته می‌شود">
                            <div class="hint mt-1">برای سئو، اسلاگ کوتاه، انگلیسی یا لاتین و بدون فاصله ترجیح داده می‌شود.</div>
                        </div>
                        <div class="form-group">
                            <label>خلاصه</label>
                            <textarea name="excerpt" class="form-control" rows="3" maxlength="2000" placeholder="برای کارت‌ها و snippet گوگل">{{ old('excerpt', $article->excerpt ?? '') }}</textarea>
                        </div>
                        <div class="form-group">
                            <label>متن مقاله <span class="text-danger">*</span></label>
                            <textarea name="body" class="form-control" rows="16" required placeholder="می‌توانید HTML ساده (h2, p, ul) استفاده کنید">{{ old('body', $article->body ?? '') }}</textarea>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="form-group">
                            <label>وضعیت</label>
                            <select name="status" class="form-control">
                                @foreach (['draft' => 'پیش‌نویس', 'published' => 'منتشر شده', 'archived' => 'آرشیو'] as $v => $lbl)
                                    <option value="{{ $v }}" @selected(old('status', $article->status ?? 'draft') === $v)>{{ $lbl }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>تاریخ انتشار</label>
                            <input type="datetime-local" name="published_at" class="form-control"
                                value="{{ old('published_at', $isEdit && $article->published_at ? $article->published_at->format('Y-m-d\TH:i') : '') }}">
                            <div class="hint mt-1">برای زمان‌بندی انتشار در آینده تنظیم کنید؛ خالی یعنی بلافاصله پس از انتشار.</div>
                        </div>
                        <div class="form-group">
                            <label>دسته‌بندی (اختیاری)</label>
                            <select name="category_id" class="form-control">
                                <option value="">—</option>
                                @foreach ($categories as $c)
                                    <option value="{{ $c->id }}" @selected((string) old('category_id', $article->category_id ?? '') === (string) $c->id)>{{ $c->title }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>برچسب‌ها</label>
                            <input type="text" name="tags_input" class="form-control" value="{{ $tagsDefault }}" placeholder="با ویرگول یا ، جدا کنید">
                            <div class="hint mt-1">مثال: فروشگاه، آموزش، تخفیف</div>
                        </div>
                        <div class="form-group form-check">
                            <input type="checkbox" name="is_featured" value="1" class="form-check-input" id="is_featured" @checked(old('is_featured', $article->is_featured ?? false))>
                            <label class="form-check-label" for="is_featured">مقاله ویژه</label>
                        </div>
                        <div class="form-group form-check">
                            <input type="checkbox" name="noindex" value="1" class="form-check-input" id="noindex" @checked(old('noindex', $article->noindex ?? false))>
                            <label class="form-check-label" for="noindex">noindex (عدم ایندکس)</label>
                        </div>
                        @if ($isEdit)
                            <div class="form-group form-check">
                                <input type="checkbox" name="og_follow_featured" value="1" class="form-check-input" id="og_follow_featured" @checked(old('og_follow_featured', true))>
                                <label class="form-check-label" for="og_follow_featured">تصویر OG همان تصویر شاخص باشد (در صورت نبود فایل جداگانه)</label>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="row mt-2">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>تصویر شاخص</label>
                            <input type="file" name="featured_image" class="form-control-file" accept="image/*">
                            @if ($isEdit && $article->featured_image_url)
                                <div class="art-preview mt-2">
                                    <img src="{{ $article->featured_image_url }}" alt="">
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>تصویر Open Graph (اختیاری)</label>
                            <input type="file" name="og_image" class="form-control-file" accept="image/*">
                            @if ($isEdit && $article->og_image_url)
                                <div class="art-preview mt-2">
                                    <img src="{{ $article->og_image_url }}" alt="">
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="seo-card mt-4">
                    <div class="head">تنظیمات سئو</div>
                    <div class="body">
                        <div class="form-group">
                            <label>عنوان متا (title)</label>
                            <input type="text" name="meta_title" class="form-control" value="{{ old('meta_title', $article->meta_title ?? '') }}" maxlength="255" placeholder="پیش‌فرض: عنوان مقاله">
                        </div>
                        <div class="form-group">
                            <label>توضیحات متا (description)</label>
                            <textarea name="meta_description" class="form-control" rows="3" maxlength="500">{{ old('meta_description', $article->meta_description ?? '') }}</textarea>
                            <div class="hint mt-1">حدود ۱۵۰–۱۶۰ کاراکتر برای نتایج گوگل ایده‌آل است.</div>
                        </div>
                        <div class="form-group">
                            <label>کلمات کلیدی</label>
                            <input type="text" name="meta_keywords" class="form-control" value="{{ old('meta_keywords', $article->meta_keywords ?? '') }}" maxlength="512" placeholder="با ویرگول جدا کنید">
                        </div>
                        <div class="form-group">
                            <label>کلمه کلیدی کانونی (Focus)</label>
                            <input type="text" name="focus_keyword" class="form-control" value="{{ old('focus_keyword', $article->focus_keyword ?? '') }}" maxlength="120">
                        </div>
                        <div class="form-group">
                            <label>آدرس کانونیکال</label>
                            <input type="url" name="canonical_url" class="form-control" dir="ltr" value="{{ old('canonical_url', $article->canonical_url ?? '') }}" maxlength="512" placeholder="https://...">
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-footer bg-white d-flex flex-wrap gap-2 justify-content-between align-items-center">
                <a href="{{ route('admin.articles.index') }}" class="btn btn-light">بازگشت</a>
                <button type="submit" class="btn btn-primary px-4">ذخیره</button>
            </div>
        </form>
    </div>
</div>
@endsection
