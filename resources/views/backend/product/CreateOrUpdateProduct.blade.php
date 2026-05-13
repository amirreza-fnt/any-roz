@extends('backend.views.view')

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/back-end/vendors/select2/css/select2.min.css') }}" type="text/css">
<link rel="stylesheet" href="{{ asset('assets/back-end/vendors/dropzone/dropzone.css') }}" type="text/css">
<link rel="stylesheet" href="{{ asset('assets/back-end/vendors/range-slider/css/ion.rangeSlider.min.css') }}" type="text/css">
<link rel="stylesheet" href="{{ asset('assets/back-end/vendors/tagsinput/bootstrap-tagsinput.css') }}" type="text/css">
<link rel="stylesheet" href="{{ asset('assets/back-end/vendors/prism/prism.css') }}" type="text/css">
<style>
    .select2-container { width: 100% !important; }
    .dz-product { min-height: 160px; border: 2px dashed #cfd6df; border-radius: 10px; background: #fafbfc; }
    .product-gallery-thumb { width: 88px; height: 88px; object-fit: cover; border-radius: 8px; border: 1px solid #e5e8ef; }
    .pricing-mode-card { border-radius: 12px; overflow: hidden; border: 1px solid #e8ecf2; transition: box-shadow .2s, border-color .2s; }
    .pricing-mode-card.is-active { border-color: rgba(99, 102, 241, 0.45); box-shadow: 0 6px 20px rgba(99, 102, 241, 0.08); }
    .pricing-mode-card .mode-head { cursor: pointer; user-select: none; padding: 0.9rem 1rem; background: linear-gradient(180deg, #fafbff 0%, #f4f6fb 100%); border-bottom: 1px solid #eef0f5; }
    .pricing-mode-card .mode-head:hover { background: #f0f3fa; }
    .pricing-mode-card .mode-head .chev { transition: transform .2s; }
    .pricing-mode-card .mode-head.collapsed .chev { transform: rotate(-90deg); }
    .weight-variant-card { border: 1px solid #e8ecf2; border-radius: 10px; margin-bottom: 0.65rem; overflow: hidden; background: #fff; }
    .weight-variant-card .wv-head { padding: 0.65rem 0.85rem; background: #f8fafc; border-bottom: 1px solid #eef0f5; cursor: pointer; }
    .weight-variant-card .wv-head:hover { background: #f0f4fa; }
    .weight-variant-card .wv-body { padding: 0.85rem 0.85rem 0.25rem; }
    .mode-pill { font-size: 0.7rem; font-weight: 600; padding: 0.2rem 0.5rem; border-radius: 999px; }
    .mode-pill-on { background: rgba(99, 102, 241, 0.15); color: #4f46e5; }
    .mode-pill-off { background: #eef0f5; color: #6b7280; }
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
    $hasVariantRows = count($selectedWeightIds) > 0;
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

                            <p class="text-muted small mb-2" id="pricing-mode-summary">
                                <span id="pricing-mode-summary-text"></span>
                            </p>

                            <div class="pricing-mode-card mb-3 {{ $hasVariantRows ? 'd-none' : 'is-active' }}" id="card-fixed-pricing">
                                <div class="mode-head d-flex justify-content-between align-items-start" data-toggle="collapse" data-target="#collapse-fixed-pricing" aria-expanded="true">
                                    <div class="pr-2">
                                        <strong class="d-block">قیمت و موجودی یکسان برای کل محصول</strong>
                                        <span class="small text-muted">تا وقتی نوع وزنی انتخاب نکرده‌اید، همین بخش معتبر است.</span>
                                    </div>
                                    <div class="d-flex align-items-center flex-shrink-0">
                                        <span class="mode-pill mode-pill-on {{ $hasVariantRows ? 'd-none' : '' }}" id="pill-fixed-on">فعال</span>
                                        <span class="mode-pill mode-pill-off {{ $hasVariantRows ? '' : 'd-none' }}" id="pill-fixed-off">غیرفعال</span>
                                        <i data-feather="chevron-down" class="width-18 height-18 mr-2 text-muted"></i>
                                    </div>
                                </div>
                                <div id="collapse-fixed-pricing" class="collapse show">
                                    <div class="card-body pt-3">
                                        <div class="form-row">
                                            <div class="form-group col-md-4">
                                                <label for="price">قیمت <span class="text-danger">*</span></label>
                                                <input type="number" min="0" class="form-control @error('price') is-invalid @enderror" id="price" name="price" value="{{ old('price', $product->price ?? '') }}">
                                                @error('price')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                            </div>
                                            <div class="form-group col-md-4">
                                                <label for="price_buy">قیمت خرید <span class="text-danger">*</span></label>
                                                <input type="number" min="0" class="form-control @error('price_buy') is-invalid @enderror" id="price_buy" name="price_buy" value="{{ old('price_buy', $product->price_buy ?? '') }}">
                                                @error('price_buy')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                            </div>
                                            <div class="form-group col-md-4">
                                                <label for="price_discounted">قیمت فروش (تخفیف‌خورده) <span class="text-danger">*</span></label>
                                                <input type="number" min="0" class="form-control @error('price_discounted') is-invalid @enderror" id="price_discounted" name="price_discounted" value="{{ old('price_discounted', $product->price_discounted ?? '') }}">
                                                @error('price_discounted')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                            </div>
                                        </div>
                                        <div class="form-row">
                                            <div class="form-group col-md-4 mb-0">
                                                <label for="stock">موجودی کل <span class="text-danger">*</span></label>
                                                <input type="number" min="0" class="form-control @error('stock') is-invalid @enderror" id="stock" name="stock" value="{{ old('stock', $product->stock ?? '') }}">
                                                @error('stock')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group mb-2">
                                <label for="product-weight-select" class="d-flex align-items-center flex-wrap">
                                    <span>انواع وزن</span>
                                    <span class="badge badge-light border mr-2">اختیاری</span>
                                </label>
                                <p class="small text-muted mb-2">اگر یک یا چند نوع وزن انتخاب کنید، برای هر کدام باید <strong>موجودی و هر سه قیمت</strong> را جدا وارد کنید؛ بخش «قیمت ثابت» بالا غیرفعال می‌شود.</p>
                                <select class="select2-example" id="product-weight-select" name="weight_type_ids[]" multiple="multiple" style="width:100%">
                                    @foreach ($typeOfWeights as $tw)
                                        @php
                                            $__p = $isEdit ? $product->typeOfWeights->firstWhere('id', $tw->id) : null;
                                            $__stock = old('weights.'.$tw->id.'.stock', $__p?->pivot->stock ?? '');
                                            $__price = old('weights.'.$tw->id.'.price', $__p?->pivot->price ?? '');
                                            $__priceBuy = old('weights.'.$tw->id.'.price_buy', $__p?->pivot->price_buy ?? '');
                                            $__priceDisc = old('weights.'.$tw->id.'.price_discounted', $__p?->pivot->price_discounted ?? '');
                                        @endphp
                                        <option value="{{ $tw->id }}"
                                            data-label="{{ e($tw->title) }}"
                                            data-stock="{{ $__stock }}"
                                            data-price="{{ $__price }}"
                                            data-price-buy="{{ $__priceBuy }}"
                                            data-price-discounted="{{ $__priceDisc }}"
                                            @selected(in_array($tw->id, (array) $selectedWeightIds, true))>
                                            {{ $tw->title }} @if($tw->weight !== null) ({{ $tw->weight }}) @endif
                                        </option>
                                    @endforeach
                                </select>
                                @error('weight_type_ids')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                            </div>

                            <div class="pricing-mode-card mb-4 {{ $hasVariantRows ? 'is-active' : 'd-none' }}" id="card-weight-variants">
                                <div class="mode-head d-flex justify-content-between align-items-start" data-toggle="collapse" data-target="#collapse-weight-variants" aria-expanded="true">
                                    <div class="pr-2">
                                        <strong class="d-block">قیمت و موجودی به‌ازای هر نوع وزن</strong>
                                        <span class="small text-muted">هر کارت را باز کنید و چهار مقدار را برای همان وزن وارد کنید.</span>
                                    </div>
                                    <div class="d-flex align-items-center flex-shrink-0">
                                        <span class="mode-pill mode-pill-on {{ $hasVariantRows ? '' : 'd-none' }}" id="pill-variants-on">فعال</span>
                                        <span class="mode-pill mode-pill-off {{ $hasVariantRows ? 'd-none' : '' }}" id="pill-variants-off">غیرفعال</span>
                                        <i data-feather="chevron-down" class="width-18 height-18 mr-2 text-muted"></i>
                                    </div>
                                </div>
                                <div id="collapse-weight-variants" class="collapse show">
                                    <div class="card-body pt-2 pb-2">
                                        <div id="weight-variants-accordion"></div>
                                    </div>
                                </div>
                            </div>

                            @foreach ($typeOfWeights as $tw)
                                @foreach (['stock', 'price', 'price_buy', 'price_discounted'] as $f)
                                    @error('weights.'.$tw->id.'.'.$f)
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                @endforeach
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
    function snapshotVariantDataToOptions($sel) {
        $('#weight-variants-accordion .weight-variant-card').each(function () {
            var id = $(this).data('weight-id');
            var $root = $(this);
            $sel.find('option[value="' + id + '"]').attr({
                'data-stock': $root.find('input[name="weights[' + id + '][stock]"]').val() || '',
                'data-price': $root.find('input[name="weights[' + id + '][price]"]').val() || '',
                'data-price-buy': $root.find('input[name="weights[' + id + '][price_buy]"]').val() || '',
                'data-price-discounted': $root.find('input[name="weights[' + id + '][price_discounted]"]').val() || ''
            });
        });
    }

    function rebuildWeightVariantCards() {
        var $sel = $('#product-weight-select');
        snapshotVariantDataToOptions($sel);
        var vals = $sel.val() || [];
        var $acc = $('#weight-variants-accordion');
        $acc.empty();

        vals.forEach(function (id) {
            var opt = $sel.find('option[value="' + id + '"]');
            var label = opt.data('label') || $.trim(opt.text());
            var sid = 'wv-collapse-' + id;

            var $card = $('<div class="weight-variant-card" data-weight-id="' + id + '"/>');
            var $head = $('<div class="wv-head d-flex justify-content-between align-items-center" data-toggle="collapse" data-target="#' + sid + '" aria-expanded="true"/>');
            $head.append($('<div/>').append($('<strong class="d-block"/>').text(label)).append($('<span class="small text-muted wv-preview"/>')));
            $head.append($('<i data-feather="chevron-down" class="width-18 height-18 text-muted chev-toggle"/>'));
            $card.append($head);

            var $body = $('<div id="' + sid + '" class="wv-body collapse show"/>');
            var $row1 = $('<div class="form-row"/>');
            $row1.append(fieldCol(id, 'stock', 'موجودی', opt.attr('data-stock')));
            $row1.append(fieldCol(id, 'price', 'قیمت', opt.attr('data-price')));
            var $row2 = $('<div class="form-row"/>');
            $row2.append(fieldCol(id, 'price_buy', 'قیمت خرید', opt.attr('data-price-buy')));
            $row2.append(fieldCol(id, 'price_discounted', 'قیمت فروش (تخفیف)', opt.attr('data-price-discounted')));
            $body.append($row1).append($row2);
            $card.append($body);
            $acc.append($card);

            updateVariantPreview($card);
            $body.find('input').on('input', function () { updateVariantPreview($card); });
        });

        if (typeof feather !== 'undefined') feather.replace();
    }

    function fieldCol(id, field, lbl, val) {
        val = val === undefined || val === null ? '' : val;
        var $g = $('<div class="form-group col-md-6"/>');
        $g.append($('<label class="small text-muted"/>').text(lbl));
        $g.append(
            $('<input type="number" min="0" class="form-control" required/>')
                .attr('name', 'weights[' + id + '][' + field + ']')
                .val(val)
        );
        return $g;
    }

    function updateVariantPreview($card) {
        var id = $card.data('weight-id');
        var disc = $card.find('input[name="weights[' + id + '][price_discounted]"]').val();
        var st = $card.find('input[name="weights[' + id + '][stock]"]').val();
        var txt = 'موجودی: ' + (st || '۰') + ' — فروش: ' + (disc || '۰');
        $card.find('.wv-preview').text(txt);
    }

    function refreshPricingMode() {
        var has = ($('#product-weight-select').val() || []).length > 0;
        var $fixed = $('#card-fixed-pricing');
        var $varC = $('#card-weight-variants');
        var $fixedInputs = $('#collapse-fixed-pricing input[name="price"], #collapse-fixed-pricing input[name="price_buy"], #collapse-fixed-pricing input[name="price_discounted"], #collapse-fixed-pricing input[name="stock"]');

        if (has) {
            $fixed.addClass('d-none').removeClass('is-active');
            $varC.removeClass('d-none').addClass('is-active');
            $fixedInputs.prop('disabled', true).prop('required', false);
            $('#pill-fixed-on').addClass('d-none'); $('#pill-fixed-off').removeClass('d-none');
            $('#pill-variants-on').removeClass('d-none'); $('#pill-variants-off').addClass('d-none');
            $('#pricing-mode-summary-text').text('حالت فعال: قیمت و موجودی جدا برای هر نوع وزن انتخاب‌شده.');
        } else {
            $fixed.removeClass('d-none').addClass('is-active');
            $varC.addClass('d-none').removeClass('is-active');
            $fixedInputs.prop('disabled', false).prop('required', true);
            $('#pill-fixed-on').removeClass('d-none'); $('#pill-fixed-off').addClass('d-none');
            $('#pill-variants-on').addClass('d-none'); $('#pill-variants-off').removeClass('d-none');
            $('#pricing-mode-summary-text').text('حالت فعال: یک قیمت و موجودی ثابت برای کل محصول (بدون تفکیک وزن).');
        }

        if (typeof feather !== 'undefined') feather.replace();
    }

    $(function () {
        $('#product-weight-select').select2({
            width: '100%',
            dir: 'rtl',
            placeholder: 'در صورت نیاز، انواع وزن را انتخاب کنید'
        }).on('change', function () {
            rebuildWeightVariantCards();
            refreshPricingMode();
        });

        rebuildWeightVariantCards();
        refreshPricingMode();
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
