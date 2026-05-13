@extends('backend.views.view')

@section('main')

@php
    $isEdit = $type === 'edit' && $category;
    $pageTitle = $isEdit ? 'ویرایش دسته‌بندی' : 'افزودن دسته‌بندی';
    $formAction = $isEdit ? route('admin.category.update', $category) : route('admin.category.store');
    $statusChecked = old('status', $isEdit ? ($category->status === 'active' ? '1' : '0') : '1') === '1';
@endphp

<div class="main-content">
    <div class="container">

        <div class="page-header">
            <h4>{{ $pageTitle }}</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.dashboard') }}">خانه</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.category.index') }}">دسته‌بندی‌ها</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">{{ $pageTitle }}</li>
                </ol>
            </nav>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-body d-flex flex-wrap justify-content-between align-items-center">
                        <a href="{{ route('admin.category.index') }}" class="btn btn-outline-secondary btn-sm">
                            <i data-feather="arrow-right" class="width-16 height-16"></i>
                            <span class="mr-1">بازگشت به لیست</span>
                        </a>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title mb-4">{{ $pageTitle }}</h6>

                        <div id="alert-container" class="alert-container"></div>

                        @if ($errors->any())
                            @foreach ($errors->all() as $error)
                                <div class="custom-alert alert-error mb-3" role="alert">
                                    <div class="alert-content">
                                        <div class="alert-icon">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <circle cx="12" cy="12" r="10"></circle>
                                                <line x1="12" y1="8" x2="12" y2="12"></line>
                                                <line x1="12" y1="16" x2="12.01" y2="16"></line>
                                            </svg>
                                        </div>
                                        <div class="alert-text">
                                            <strong>خطا:</strong> {{ $error }}
                                        </div>
                                    </div>
                                    <button type="button" class="alert-close" onclick="closeAlert(this)">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <line x1="18" y1="6" x2="6" y2="18"></line>
                                            <line x1="6" y1="6" x2="18" y2="18"></line>
                                        </svg>
                                    </button>
                                </div>
                            @endforeach
                        @endif

                        <div class="row">
                            <div class="col-12">
                                <form class="row" method="post" action="{{ $formAction }}" enctype="multipart/form-data" id="category-form">
                                    @csrf
                                    @if ($isEdit)
                                        @method('PUT')
                                    @endif

                                    <div class="form-group col-lg-6 col-12">
                                        <label for="category-title">عنوان</label>
                                        <input name="title" type="text" class="form-control text-left" id="category-title" value="{{ old('title', $category->title ?? '') }}" placeholder="عنوان دسته‌بندی را وارد کنید" dir="ltr" required>
                                    </div>

                                    <div class="form-group col-lg-6 col-12">
                                        <label for="category-image">تصویر دسته‌بندی</label>
                                        <div class="custom-file">
                                            <input name="image" type="file" class="custom-file-input" id="category-image" accept="image/*">
                                            <label class="custom-file-label" for="category-image" data-default="انتخاب تصویر">انتخاب تصویر</label>
                                        </div>
                                        <small class="form-text text-muted">فرمت تصویر؛ در صورت انتخاب فایل جدید، تصویر قبلی (در حالت ویرایش) جایگزین می‌شود.</small>
                                    </div>

                                    @if ($isEdit && $category->image_url)
                                        <div class="form-group col-12">
                                            <label class="d-block">تصویر فعلی</label>
                                            <div class="d-flex align-items-center p-3 rounded border bg-light">
                                                <img src="{{ $category->image_url }}" alt="{{ $category->title }}" id="current-category-image" class="rounded mr-3" style="max-height: 88px; max-width: 120px; object-fit: cover;">
                                                <span class="text-muted small">پیش‌نمایش در کنار فرم نمایش داده می‌شود.</span>
                                            </div>
                                        </div>
                                    @endif

                                    <div class="form-group col-12" id="new-image-preview-wrap" style="display:none;">
                                        <label class="d-block">پیش‌نمایش تصویر جدید</label>
                                        <img src="" alt="" id="new-image-preview" class="rounded border" style="max-height: 120px; max-width: 160px; object-fit: cover;">
                                    </div>

                                    <div class="form-group col-lg-6 col-12">
                                        <label for="parent">دسته والد</label>
                                        <select name="parent_id" class="form-control" id="parent">
                                            <option value="">انتخاب کنید</option>
                                            @foreach ($parents as $p)
                                                <option value="{{ $p->id }}" @selected(old('parent_id', $category->parent_id ?? '') == $p->id)>
                                                    {{ $p->title }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group col-lg-6 col-12 d-flex align-items-end">
                                        <div class="custom-control custom-switch custom-checkbox-success w-100">
                                            <input type="hidden" name="status" value="0">
                                            <input name="status" type="checkbox" class="custom-control-input" id="category-status-switch" value="1" @checked($statusChecked)>
                                            <label class="custom-control-label" for="category-status-switch">وضعیت دسته‌بندی (فعال)</label>
                                        </div>
                                    </div>

                                    <div class="form-group col-12 mt-2">
                                        <small class="form-text text-muted d-block mb-2">در صورت اطمینان از صحت اطلاعات، ذخیره را بزنید.</small>
                                        <button type="submit" class="btn btn-primary pl-4 pr-4">
                                            {{ $isEdit ? 'ذخیرهٔ تغییرات' : 'ثبت دسته' }}
                                        </button>
                                        <a href="{{ route('admin.category.index') }}" class="btn btn-light mr-2">انصراف</a>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    (function () {
        var input = document.getElementById('category-image');
        if (!input) return;
        var label = input.nextElementSibling;
        var previewWrap = document.getElementById('new-image-preview-wrap');
        var previewImg = document.getElementById('new-image-preview');
        input.addEventListener('change', function () {
            var file = this.files && this.files[0];
            if (label) {
                label.textContent = file ? file.name : (label.getAttribute('data-default') || 'انتخاب تصویر');
            }
            if (file && previewWrap && previewImg) {
                previewImg.src = URL.createObjectURL(file);
                previewWrap.style.display = 'block';
            } else if (previewWrap) {
                previewWrap.style.display = 'none';
                if (previewImg) previewImg.src = '';
            }
        });
    })();
</script>
@endpush
