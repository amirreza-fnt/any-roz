@extends('backend.views.view')

@php
    use App\Support\JalaliCalendar;
    $isEdit = ($type ?? '') === 'edit';
    $dc = $discountCode;
    $selCats = old('category_ids', $isEdit && $dc ? ($dc->category_ids ?? []) : []);
    $selProds = old('product_ids', $isEdit && $dc ? ($dc->product_ids ?? []) : []);
    $dt = old('discount_type', $isEdit && $dc ? $dc->discount_type : 'percent');
    $ap = old('applies_to', $isEdit && $dc ? $dc->applies_to : 'all');
    $startsShamsi = old('starts_at_shamsi', $isEdit && $dc?->starts_at ? JalaliCalendar::formatShamsiDate($dc->starts_at) : '');
    $expiresShamsi = old('expires_at_shamsi', $isEdit && $dc?->expires_at ? JalaliCalendar::formatShamsiDate($dc->expires_at) : '');
@endphp

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/back-end/vendors/datepicker-jalali/bootstrap-datepicker.min.css') }}" type="text/css">
<link rel="stylesheet" href="{{ asset('assets/back-end/vendors/select2/css/select2.min.css') }}" type="text/css">
<style>
    .select2-container { width: 100% !important; }
    .dc-card { border-radius: 14px; border: 1px solid #e5e7eb; overflow: hidden; margin-bottom: 1rem; }
    .dc-card .hd { background: linear-gradient(120deg,#4f46e5,#7c3aed); color:#fff; padding:.75rem 1rem; font-weight:700; font-size:.9rem; }
    .dc-card .bd { padding: 1rem; background: #fafafa; }
    .mode-pill { cursor: pointer; border-radius: 999px; padding: .4rem 1rem; border: 2px solid #e5e7eb; font-weight: 600; font-size: .85rem; margin-left: .35rem; display: inline-block; }
    .mode-pill.is-on { border-color: #6366f1; background: rgba(99,102,241,.12); color: #3730a3; }
</style>
@endpush

@section('main')
<div class="main-content">
    <div class="container">
        <div class="page-header">
            <h4>{{ $isEdit ? 'ویرایش کد تخفیف' : 'افزودن کد تخفیف' }}</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">خانه</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.discount-codes.index') }}">کدهای تخفیف</a></li>
                    <li class="breadcrumb-item active">{{ $isEdit ? 'ویرایش' : 'افزودن' }}</li>
                </ol>
            </nav>
        </div>

        <form method="post" action="{{ $isEdit ? route('admin.discount-codes.update', $dc) : route('admin.discount-codes.store') }}" class="card border-0 shadow-sm">
            @csrf
            @if ($isEdit) @method('PUT') @endif

            <div class="card-body">
                <div class="dc-card">
                    <div class="hd">کد و عنوان</div>
                    <div class="bd">
                        <div class="form-row">
                            <div class="form-group col-md-4">
                                <label>کد <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="text" name="code" id="disc-code-input" class="form-control text-monospace" value="{{ old('code', $dc?->code ?? '') }}" required maxlength="64">
                                    <div class="input-group-append">
                                        <button type="button" class="btn btn-outline-secondary" id="btn-rand-disc">تصادفی</button>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group col-md-8">
                                <label>عنوان کوتاه</label>
                                <input type="text" name="title" class="form-control" value="{{ old('title', $dc?->title ?? '') }}" maxlength="255">
                            </div>
                        </div>
                        <div class="form-group mb-0">
                            <label>توضیحات داخلی</label>
                            <textarea name="description" class="form-control" rows="2" maxlength="5000">{{ old('description', $dc?->description ?? '') }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="dc-card">
                    <div class="hd">نوع و مقدار تخفیف</div>
                    <div class="bd">
                        <input type="hidden" name="discount_type" id="disc-type" value="{{ $dt }}">
                        <div class="mb-3">
                            <span class="mode-pill {{ $dt === 'percent' ? 'is-on' : '' }}" data-dt="percent">درصدی + سقف</span>
                            <span class="mode-pill {{ $dt === 'fixed' ? 'is-on' : '' }}" data-dt="fixed">مبلغ ثابت (تومان)</span>
                        </div>
                        <div id="disc-wrap-percent" style="{{ $dt === 'percent' ? '' : 'display:none' }}">
                            <div class="form-row">
                                <div class="form-group col-md-4">
                                    <label>درصد تخفیف</label>
                                    <input type="number" name="discount_percent" class="form-control" min="1" max="100" value="{{ old('discount_percent', $dc?->discount_percent ?? '') }}">
                                </div>
                                <div class="form-group col-md-8">
                                    <label>سقف تخفیف (تومان) — اختیاری</label>
                                    <input type="number" name="max_discount_amount" class="form-control" min="0" value="{{ old('max_discount_amount', $dc?->max_discount_amount ?? '') }}">
                                </div>
                            </div>
                        </div>
                        <div class="form-row" id="disc-wrap-fixed" style="{{ $dt === 'fixed' ? '' : 'display:none' }}">
                            <div class="form-group col-md-6">
                                <label>مبلغ تخفیف (تومان)</label>
                                <input type="number" name="discount_amount" class="form-control" min="1" value="{{ old('discount_amount', $dc?->discount_amount ?? '') }}">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="dc-card">
                    <div class="hd">شرایط استفاده</div>
                    <div class="bd">
                        <div class="form-row">
                            <div class="form-group col-md-4">
                                <label>حداقل مبلغ سفارش (تومان)</label>
                                <input type="number" name="min_order_amount" class="form-control" min="0" value="{{ old('min_order_amount', $dc?->min_order_amount ?? 0) }}">
                            </div>
                            <div class="form-group col-md-4">
                                <label>حداکثر دفعات استفاده (کل)</label>
                                <input type="number" name="usage_limit" class="form-control" min="1" value="{{ old('usage_limit', $dc?->usage_limit ?? '') }}" placeholder="خالی = نامحدود">
                            </div>
                            <div class="form-group col-md-4">
                                <label>حداکثر برای هر کاربر</label>
                                <input type="number" name="per_user_limit" class="form-control" min="1" value="{{ old('per_user_limit', $dc?->per_user_limit ?? 1) }}">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label>شروع اعتبار (شمسی)</label>
                                <input type="text" name="starts_at_shamsi" autocomplete="off" class="form-control text-left dc-shamsi-date" dir="ltr" value="{{ $startsShamsi }}" placeholder="مثلاً ۱۴۰۳/۰۱/۰۱">
                                @error('starts_at_shamsi')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                            </div>
                            <div class="form-group col-md-6">
                                <label>پایان اعتبار (شمسی)</label>
                                <input type="text" name="expires_at_shamsi" autocomplete="off" class="form-control text-left dc-shamsi-date" dir="ltr" value="{{ $expiresShamsi }}" placeholder="مثلاً ۱۴۰۳/۱۲/۲۹">
                                @error('expires_at_shamsi')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="dc-card">
                    <div class="hd">محدودهٔ محصولات</div>
                    <div class="bd">
                        <input type="hidden" name="applies_to" id="disc-applies-to" value="{{ $ap }}">
                        <div class="mb-3">
                            <span class="mode-pill {{ $ap === 'all' ? 'is-on' : '' }}" data-ap="all">همه محصولات</span>
                            <span class="mode-pill {{ $ap === 'categories' ? 'is-on' : '' }}" data-ap="categories">دسته‌های خاص</span>
                            <span class="mode-pill {{ $ap === 'products' ? 'is-on' : '' }}" data-ap="products">محصولات خاص</span>
                        </div>
                        <div id="disc-wrap-cats" style="{{ $ap === 'categories' ? '' : 'display:none' }}">
                            <label>انتخاب دسته‌ها</label>
                            <select name="category_ids[]" id="disc-cats" class="select2-example" multiple style="width:100%">
                                @foreach ($categories as $c)
                                    <option value="{{ $c->id }}" @selected(in_array($c->id, (array) $selCats, true))>{{ $c->title }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div id="disc-wrap-prods" style="{{ $ap === 'products' ? '' : 'display:none' }}">
                            <label>انتخاب محصولات</label>
                            <select name="product_ids[]" id="disc-prods" class="select2-example" multiple style="width:100%">
                                @foreach ($products as $p)
                                    <option value="{{ $p->id }}" @selected(in_array($p->id, (array) $selProds, true))>{{ $p->title }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label>وضعیت</label>
                    <select name="status" class="form-control" style="max-width:220px">
                        <option value="active" @selected(old('status', $dc?->status ?? 'active') === 'active')>فعال</option>
                        <option value="inactive" @selected(old('status', $dc?->status ?? 'active') === 'inactive')>غیرفعال</option>
                    </select>
                </div>
            </div>

            <div class="card-footer bg-white d-flex justify-content-between">
                <a href="{{ route('admin.discount-codes.index') }}" class="btn btn-light">بازگشت</a>
                <button type="submit" class="btn btn-primary px-4">ذخیره</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('assets/back-end/vendors/select2/js/select2.min.js') }}"></script>
<script src="{{ asset('assets/back-end/vendors/datepicker-jalali/bootstrap-datepicker.fa.min.js') }}"></script>
<script>
(function () {
    function randCode() {
        const chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
        let s = '';
        for (let i = 0; i < 10; i++) s += chars.charAt(Math.floor(Math.random() * chars.length));
        return s;
    }
    $('#btn-rand-disc').on('click', function () { $('#disc-code-input').val(randCode()); });

    $('.mode-pill[data-dt]').on('click', function () {
        const v = $(this).data('dt');
        $('#disc-type').val(v);
        $('.mode-pill[data-dt]').removeClass('is-on');
        $(this).addClass('is-on');
        $('#disc-wrap-fixed').toggle(v === 'fixed');
        $('#disc-wrap-percent').toggle(v === 'percent');
    });
    $('.mode-pill[data-ap]').on('click', function () {
        const v = $(this).data('ap');
        $('#disc-applies-to').val(v);
        $('.mode-pill[data-ap]').removeClass('is-on');
        $(this).addClass('is-on');
        $('#disc-wrap-cats').toggle(v === 'categories');
        $('#disc-wrap-prods').toggle(v === 'products');
    });
    if (typeof $.fn.select2 !== 'undefined') {
        $('#disc-cats, #disc-prods').select2({ dir: 'rtl', width: '100%', placeholder: 'انتخاب کنید...' });
    }

    if (typeof $.fn.datepicker !== 'undefined') {
        $('input.dc-shamsi-date').datepicker({
            dateFormat: 'yy/mm/dd',
            showOtherMonths: true,
            selectOtherMonths: true,
            changeMonth: true,
            changeYear: true,
            showButtonPanel: true
        });
    }

    if (typeof feather !== 'undefined') feather.replace();
})();
</script>
@endpush
