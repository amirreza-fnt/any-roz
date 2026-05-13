@extends('backend.views.view')

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/back-end/vendors/select2/css/select2.min.css') }}" type="text/css">
<link rel="stylesheet" href="{{ asset('assets/back-end/vendors/dropzone/dropzone.css') }}" type="text/css">
<link rel="stylesheet" href="{{ asset('assets/back-end/vendors/range-slider/css/ion.rangeSlider.min.css') }}" type="text/css">
<link rel="stylesheet" href="{{ asset('assets/back-end/vendors/tagsinput/bootstrap-tagsinput.css') }}" type="text/css">
<link rel="stylesheet" href="{{ asset('assets/back-end/vendors/prism/prism.css') }}" type="text/css">
<style>
    .select2-container { width: 100% !important; }
    .weight-stock-field label { font-size: 0.85rem; color: #5f6b7a; }
    .dz-product { min-height: 160px; border: 2px dashed #cfd6df; border-radius: 10px; background: #fafbfc; }
    .product-gallery-thumb { width: 88px; height: 88px; object-fit: cover; border-radius: 8px; border: 1px solid #e5e8ef; }
</style>
@endpush

@php
    $isEdit = $type === 'edit' && $product;
    $pageTitle = $isEdit ? 'ویرایش محصول' : 'افزودن محصول';
    $formAction = $isEdit ? route('admin.products.update', $product) : route('admin.products.store');
    if ($errors->any()) {
        $statusChecked = (bool) old('status');
        $suggestedChecked = (bool) old('suggested');
    } else {
        $statusChecked = $isEdit ? $product->status === 'active' : true;
        $suggestedChecked = $isEdit ? $product->suggested === 'active' : false;
    }
    $selectedWeightIds = array_map('intval', (array) old('weight_type_ids', $isEdit ? $product->typeOfWeights->pluck('id')->all() : []));
@endphp

@section('main')

<div class="main-content">
    <div class="container">

        <div class="page-header">
            <h4>{{ $pageTitle }}</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">خانه</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.products.index') }}">محصولات</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ $pageTitle }}</li>
                </ol>
            </nav>
        </div>

        <div class="row">
            <div class="col-lg-10 mx-auto">
                <div class="card">
                    <div class="card-body">
                        <form action="{{ $formAction }}" method="post" id="product-form">
                            @csrf
                            @if ($isEdit)
                                @method('PUT')
                            @endif

                            <div class="form-row">
                                <div class="form-group col-md-8">
                                    <label for="title">عنوان <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title', $product->title ?? '') }}" required>
                                    @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="tracking_code">کد رهگیری</label>
                                    <input type="text" class="form-control @error('tracking_code') is-invalid @enderror" id="tracking_code" name="tracking_code" value="{{ old('tracking_code', $product->tracking_code ?? '') }}" placeholder="خالی = تولید خودکار">
                                    @error('tracking_code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="slug">نامک (slug)</label>
                                    <input type="text" class="form-control @error('slug') is-invalid @enderror" id="slug" name="slug" value="{{ old('slug', $product->slug ?? '') }}" placeholder="خالی = از روی عنوان">
                                    @error('slug')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="category_id">دسته‌بندی <span class="text-danger">*</span></label>
                                    <select class="form-control @error('category_id') is-invalid @enderror" id="category_id" name="category_id" required>
                                        <option value="">انتخاب کنید</option>
                                        @foreach ($categories as $cat)
                                            <option value="{{ $cat->id }}" @selected((int) old('category_id', $product->category_id ?? 0) === $cat->id)>{{ $cat->title }}</option>
                                        @endforeach
                                    </select>
                                    @error('category_id')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-4">
                                    <label for="price">قیمت <span class="text-danger">*</span></label>
                                    <input type="number" min="0" class="form-control @error('price') is-invalid @enderror" id="price" name="price" value="{{ old('price', $product->price ?? '') }}" required>
                                    @error('price')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="price_buy">قیمت خرید <span class="text-danger">*</span></label>
                                    <input type="number" min="0" class="form-control @error('price_buy') is-invalid @enderror" id="price_buy" name="price_buy" value="{{ old('price_buy', $product->price_buy ?? '') }}" required>
                                    @error('price_buy')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="price_discounted">قیمت فروش (تخفیف‌خورده) <span class="text-danger">*</span></label>
                                    <input type="number" min="0" class="form-control @error('price_discounted') is-invalid @enderror" id="price_discounted" name="price_discounted" value="{{ old('price_discounted', $product->price_discounted ?? '') }}" required>
                                    @error('price_discounted')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="product-weight-select">انواع وزن <span class="text-danger">*</span></label>
                                <select class="select2-example" id="product-weight-select" name="weight_type_ids[]" multiple="multiple" style="width:100%">
                                    @foreach ($typeOfWeights as $tw)
                                        @php
                                            $__stock = old('weights.'.$tw->id.'.stock');
                                            if ($__stock === null && $isEdit) {
                                                $__p = $product->typeOfWeights->firstWhere('id', $tw->id);
                                                $__stock = $__p ? $__p->pivot->stock : '';
                                            }
                                            $__stock = $__stock ?? '';
                                        @endphp
                                        <option value="{{ $tw->id }}"
                                            data-label="{{ e($tw->title) }}"
                                            data-stock="{{ $__stock }}"
                                            @selected(in_array($tw->id, (array) $selectedWeightIds, true))>
                                            {{ $tw->title }} @if($tw->weight !== null) ({{ $tw->weight }}) @endif
                                        </option>
                                    @endforeach
                                </select>
                                @error('weight_type_ids')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                            </div>

                            <div class="form-row" id="weight-stock-rows"></div>
                            @foreach ($typeOfWeights as $tw)
                                @error('weights.'.$tw->id.'.stock')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            @endforeach

                            <div class="form-group">
                                <label for="mini_description">توضیح کوتاه <span class="text-danger">*</span></label>
                                <textarea class="form-control @error('mini_description') is-invalid @enderror" id="mini_description" name="mini_description" rows="3" required>{{ old('mini_description', $product->mini_description ?? '') }}</textarea>
                                @error('mini_description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="form-group">
                                <label for="description">توضیح کامل <span class="text-danger">*</span></label>
                                <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="6" required>{{ old('description', $product->description ?? '') }}</textarea>
                                @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" id="status" name="status" value="1" @checked($statusChecked)>
                                        <label class="custom-control-label" for="status">فعال برای نمایش</label>
                                    </div>
                                </div>
                                <div class="form-group col-md-6">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" id="suggested" name="suggested" value="1" @checked($suggestedChecked)>
                                        <label class="custom-control-label" for="suggested">پیشنهاد ویژه</label>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between align-items-center mt-4">
                                <a href="{{ route('admin.products.index') }}" class="btn btn-light">بازگشت</a>
                                <button type="submit" class="btn btn-primary">{{ $isEdit ? 'ذخیرهٔ تغییرات' : 'ثبت محصول' }}</button>
                            </div>
                        </form>

                        @if (! $isEdit)
                            <div class="alert alert-info small mt-3 mb-0">
                                پس از ثبت محصول می‌توانید چند تصویر را در صفحهٔ ویرایش با Dropzone آپلود کنید.
                            </div>
                        @else
                            <hr class="my-4">
                            <h6 class="mb-3">تصاویر محصول</h6>
                            <p class="text-muted small">فایل‌ها را اینجا رها کنید یا کلیک کنید. برای حذف از دکمهٔ زیر هر تصویر استفاده کنید.</p>

                            <div class="row mb-4">
                                @forelse ($product->images as $img)
                                    <div class="col-6 col-md-3 mb-3 text-center">
                                        <img src="{{ $img->url }}" alt="" class="product-gallery-thumb mb-2">
                                        <form action="{{ route('admin.products.images.destroy', [$product, $img]) }}" method="post" onsubmit="return confirm('حذف این تصویر؟');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">حذف</button>
                                        </form>
                                    </div>
                                @empty
                                    <div class="col-12 text-muted small mb-2">هنوز تصویری ثبت نشده است.</div>
                                @endforelse
                            </div>

                            <form action="{{ route('admin.products.images.store', $product) }}" class="dropzone dz-product mb-0" id="product-images-dropzone"></form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="{{ asset('assets/back-end/vendors/select2/js/select2.min.js') }}"></script>
<script src="{{ asset('assets/back-end/vendors/dropzone/dropzone.js') }}"></script>
<script>
(function () {
    function snapshotStocksToOptions($sel) {
        $('#weight-stock-rows .weight-stock-field').each(function () {
            var id = $(this).data('weight-id');
            var v = $(this).find('input').val();
            $sel.find('option[value="' + id + '"]').attr('data-stock', v);
        });
    }

    function rebuildWeightRows() {
        var $sel = $('#product-weight-select');
        snapshotStocksToOptions($sel);
        var vals = $sel.val() || [];
        var $out = $('#weight-stock-rows');
        $out.empty();
        vals.forEach(function (id) {
            var opt = $sel.find('option[value="' + id + '"]');
            var label = opt.data('label') || $.trim(opt.text());
            var stock = opt.attr('data-stock');
            if (stock === undefined || stock === null) {
                stock = '';
            }
            var $wrap = $('<div class="form-group col-md-6 weight-stock-field"/>').attr('data-weight-id', id);
            $wrap.append($('<label/>').text('موجودی — ' + label));
            $wrap.append(
                $('<input type="number" min="0" class="form-control" required/>')
                    .attr('name', 'weights[' + id + '][stock]')
                    .val(stock)
            );
            $out.append($wrap);
        });
    }

    $(function () {
        $('#product-weight-select').select2({
            width: '100%',
            dir: 'rtl',
            placeholder: 'انواع وزن را انتخاب کنید'
        }).on('change', rebuildWeightRows);

        rebuildWeightRows();
    });

    @if ($isEdit)
    Dropzone.autoDiscover = false;
    $(function () {
        var dzEl = document.getElementById('product-images-dropzone');
        if (!dzEl) return;

        var csrf = @json(csrf_token());
        var uploadUrl = @json(route('admin.products.images.store', $product));

        new Dropzone('#product-images-dropzone', {
            url: uploadUrl,
            headers: { 'X-CSRF-TOKEN': csrf },
            paramName: 'file',
            maxFilesize: 8,
            acceptedFiles: 'image/*',
            addRemoveLinks: false,
            dictDefaultMessage: 'فایل‌ها را اینجا رها کنید یا کلیک کنید',
            success: function () {
                window.location.reload();
            }
        });
    });
    @endif
})();
</script>
@endpush
