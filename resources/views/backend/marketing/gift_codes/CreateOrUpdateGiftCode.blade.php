@extends('backend.views.view')

@php
    $isEdit = ($type ?? '') === 'edit';
    $gc = $giftCode;
    $selCats = old('category_ids', $isEdit && $gc ? ($gc->category_ids ?? []) : []);
    $selProds = old('product_ids', $isEdit && $gc ? ($gc->product_ids ?? []) : []);
    $selUsers = old('user_ids', $isEdit && $gc ? ($gc->user_ids ?? []) : []);
    $vt = old('value_type', $isEdit && $gc ? $gc->value_type : 'fixed');
    $ap = old('applies_to', $isEdit && $gc ? $gc->applies_to : 'all');
    $startsShamsi = old('starts_at_shamsi', $isEdit && $gc?->starts_at ? \App\Support\JalaliCalendar::formatShamsiDate($gc->starts_at) : '');
    $expiresShamsi = old('expires_at_shamsi', $isEdit && $gc?->expires_at ? \App\Support\JalaliCalendar::formatShamsiDate($gc->expires_at) : '');
@endphp

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/back-end/vendors/datepicker-jalali/bootstrap-datepicker.min.css') }}" type="text/css">
<link rel="stylesheet" href="{{ asset('assets/back-end/vendors/select2/css/select2.min.css') }}" type="text/css">
<style>
    .select2-container { width: 100% !important; }
    .gc-card { border-radius: 14px; border: 1px solid #e5e7eb; overflow: hidden; margin-bottom: 1rem; }
    .gc-card .hd { background: linear-gradient(120deg,#0f766e,#14b8a6); color:#fff; padding:.75rem 1rem; font-weight:700; font-size:.9rem; }
    .gc-card .bd { padding: 1rem; background: #fafafa; }
    .mode-pill { cursor: pointer; border-radius: 999px; padding: .4rem 1rem; border: 2px solid #e5e7eb; font-weight: 600; font-size: .85rem; margin-left: .35rem; display: inline-block; }
    .mode-pill.is-on { border-color: #14b8a6; background: rgba(20,184,166,.12); color: #0f766e; }
</style>
@endpush

@section('main')
<div class="main-content">
    <div class="container">
        <div class="page-header">
            <h4>{{ $isEdit ? 'ویرایش کد هدیه' : 'افزودن کد هدیه' }}</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">خانه</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.gift-codes.index') }}">کدهای هدیه</a></li>
                    <li class="breadcrumb-item active">{{ $isEdit ? 'ویرایش' : 'افزودن' }}</li>
                </ol>
            </nav>
        </div>

        <form method="post" action="{{ $isEdit ? route('admin.gift-codes.update', $gc) : route('admin.gift-codes.store') }}" class="card border-0 shadow-sm">
            @csrf
            @if ($isEdit) @method('PUT') @endif

            <div class="card-body">
                <div class="gc-card">
                    <div class="hd">کد و عنوان</div>
                    <div class="bd">
                        <div class="form-row">
                            <div class="form-group col-md-4">
                                <label>کد <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="text" name="code" id="gift-code-input" class="form-control text-monospace" value="{{ old('code', $gc?->code ?? '') }}" required maxlength="64">
                                    <div class="input-group-append">
                                        <button type="button" class="btn btn-outline-secondary" type="button" id="btn-rand-gift">تصادفی</button>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group col-md-8">
                                <label>عنوان کوتاه</label>
                                <input type="text" name="title" class="form-control" value="{{ old('title', $gc?->title ?? '') }}" maxlength="255" placeholder="مثلاً هدیه نوروزی">
                            </div>
                        </div>
                        <div class="form-group mb-0">
                            <label>توضیحات داخلی</label>
                            <textarea name="description" class="form-control" rows="2" maxlength="5000">{{ old('description', $gc?->description ?? '') }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="gc-card">
                    <div class="hd">نوع و مقدار هدیه</div>
                    <div class="bd">
                        <input type="hidden" name="value_type" id="gift-value-type" value="{{ $vt }}">
                        <div class="mb-3">
                            <span class="mode-pill {{ $vt === 'fixed' ? 'is-on' : '' }}" data-vt="fixed">مبلغ ثابت (تومان)</span>
                            <span class="mode-pill {{ $vt === 'percent' ? 'is-on' : '' }}" data-vt="percent">درصدی از سبد + سقف</span>
                        </div>
                        <div class="form-row" id="gift-wrap-fixed" style="{{ $vt === 'fixed' ? '' : 'display:none' }}">
                            <div class="form-group col-md-6">
                                <label>مبلغ هدیه (تومان)</label>
                                <input type="number" name="amount" class="form-control" min="1" value="{{ old('amount', $gc?->amount ?? '') }}" placeholder="مثلاً 500000">
                            </div>
                        </div>
                        <div id="gift-wrap-percent" style="{{ $vt === 'percent' ? '' : 'display:none' }}">
                            <div class="form-row">
                                <div class="form-group col-md-4">
                                    <label>درصد از مبلغ سبد</label>
                                    <input type="number" name="percent" class="form-control" min="1" max="100" value="{{ old('percent', $gc?->percent ?? '') }}">
                                </div>
                                <div class="form-group col-md-8">
                                    <label>سقف هدیه (تومان) — اختیاری</label>
                                    <input type="number" name="max_amount" class="form-control" min="0" value="{{ old('max_amount', $gc?->max_amount ?? '') }}" placeholder="برای کنترل حداکثر مبلغ هدیه">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="gc-card">
                    <div class="hd">شرایط استفاده</div>
                    <div class="bd">
                        <div class="form-row">
                            <div class="form-group col-md-4">
                                <label>حداقل مبلغ سفارش (تومان)</label>
                                <input type="number" name="min_order_amount" class="form-control" min="0" value="{{ old('min_order_amount', $gc?->min_order_amount ?? 0) }}">
                            </div>
                            <div class="form-group col-md-4">
                                <label>حداکثر دفعات استفاده (کل)</label>
                                <input type="number" name="usage_limit" class="form-control" min="1" value="{{ old('usage_limit', $gc?->usage_limit ?? '') }}" placeholder="خالی = نامحدود">
                            </div>
                            <div class="form-group col-md-4">
                                <label>حداکثر برای هر کاربر</label>
                                <input type="number" name="per_user_limit" class="form-control" min="1" value="{{ old('per_user_limit', $gc?->per_user_limit ?? 1) }}">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label>شروع اعتبار (شمسی)</label>
                                <input type="text" name="starts_at_shamsi" autocomplete="off" class="form-control text-left gc-shamsi-date" dir="ltr" value="{{ $startsShamsi }}" placeholder="مثلاً ۱۴۰۳/۰۱/۰۱">
                                @error('starts_at_shamsi')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                            </div>
                            <div class="form-group col-md-6">
                                <label>پایان اعتبار (شمسی)</label>
                                <input type="text" name="expires_at_shamsi" autocomplete="off" class="form-control text-left gc-shamsi-date" dir="ltr" value="{{ $expiresShamsi }}" placeholder="مثلاً ۱۴۰۳/۱۲/۲۹">
                                @error('expires_at_shamsi')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="gc-card">
                    <div class="hd">محدودیت کاربران</div>
                    <div class="bd">
                        <p class="text-muted small mb-2">در صورت خالی ماندن، کد برای همهٔ کاربران قابل استفاده است؛ در غیر این صورت فقط برای کاربران انتخاب‌شده اعمال می‌شود.</p>
                        <label>انتخاب کاربران</label>
                        <select name="user_ids[]" id="gift-users" class="select2-example" multiple style="width:100%">
                            @foreach ($users as $u)
                                <option value="{{ $u->id }}" @selected(in_array($u->id, (array) $selUsers, true))>{{ $u->name }} — {{ $u->email }}</option>
                            @endforeach
                        </select>
                        @error('user_ids')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                        @error('user_ids.*')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="gc-card">
                    <div class="hd">محدودهٔ محصولات</div>
                    <div class="bd">
                        <input type="hidden" name="applies_to" id="gift-applies-to" value="{{ $ap }}">
                        <div class="mb-3">
                            <span class="mode-pill {{ $ap === 'all' ? 'is-on' : '' }}" data-ap="all">همه محصولات</span>
                            <span class="mode-pill {{ $ap === 'categories' ? 'is-on' : '' }}" data-ap="categories">دسته‌های خاص</span>
                            <span class="mode-pill {{ $ap === 'products' ? 'is-on' : '' }}" data-ap="products">محصولات خاص</span>
                        </div>
                        <div id="gift-wrap-cats" style="{{ $ap === 'categories' ? '' : 'display:none' }}">
                            <label>انتخاب دسته‌ها</label>
                            <select name="category_ids[]" id="gift-cats" class="select2-example" multiple style="width:100%">
                                @foreach ($categories as $c)
                                    <option value="{{ $c->id }}" @selected(in_array($c->id, (array) $selCats, true))>{{ $c->title }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div id="gift-wrap-prods" style="{{ $ap === 'products' ? '' : 'display:none' }}">
                            <label>انتخاب محصولات</label>
                            <select name="product_ids[]" id="gift-prods" class="select2-example" multiple style="width:100%">
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
                        <option value="active" @selected(old('status', $gc?->status ?? 'active') === 'active')>فعال</option>
                        <option value="inactive" @selected(old('status', $gc?->status ?? 'active') === 'inactive')>غیرفعال</option>
                    </select>
                </div>
            </div>

            <div class="card-footer bg-white d-flex justify-content-between">
                <a href="{{ route('admin.gift-codes.index') }}" class="btn btn-light">بازگشت</a>
                <button type="submit" class="btn btn-primary px-4">ذخیره</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
@php
    $shamsiYear = \App\Support\JalaliCalendar::currentJalaliYear();
@endphp
<script src="{{ asset('assets/back-end/vendors/select2/js/select2.min.js') }}"></script>
<script src="{{ asset('assets/back-end/vendors/datepicker-jalali/bootstrap-datepicker.fa.min.js') }}"></script>
<script>
(function () {
    var y0 = {{ (int) $shamsiYear }};
    function randCode() {
        const chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
        let s = '';
        for (let i = 0; i < 10; i++) s += chars.charAt(Math.floor(Math.random() * chars.length));
        return s;
    }
    $('#btn-rand-gift').on('click', function () { $('#gift-code-input').val(randCode()); });

    $('.mode-pill[data-vt]').on('click', function () {
        const v = $(this).data('vt');
        $('#gift-value-type').val(v);
        $('.mode-pill[data-vt]').removeClass('is-on');
        $(this).addClass('is-on');
        $('#gift-wrap-fixed').toggle(v === 'fixed');
        $('#gift-wrap-percent').toggle(v === 'percent');
    });
    $('.mode-pill[data-ap]').on('click', function () {
        const v = $(this).data('ap');
        $('#gift-applies-to').val(v);
        $('.mode-pill[data-ap]').removeClass('is-on');
        $(this).addClass('is-on');
        $('#gift-wrap-cats').toggle(v === 'categories');
        $('#gift-wrap-prods').toggle(v === 'products');
    });

    function initS2() {
        if (typeof $.fn.select2 === 'undefined') return;
        $('#gift-cats, #gift-prods, #gift-users').select2({ dir: 'rtl', width: '100%', placeholder: 'انتخاب کنید...' });
    }
    initS2();

    if (typeof $.fn.datepicker !== 'undefined') {
        var cal = typeof JalaliDate !== 'undefined' ? JalaliDate : undefined;
        var base = $.datepicker.regional['fa'] || {};
        $('input.gc-shamsi-date').datepicker($.extend({}, base, {
            calendar: cal,
            dateFormat: 'yy/mm/dd',
            showOtherMonths: true,
            selectOtherMonths: true,
            changeMonth: true,
            changeYear: true,
            showButtonPanel: true,
            yearRange: (y0 - 25) + ':' + (y0 + 10),
            defaultDate: cal ? new cal() : undefined,
            onSelect: function () {
                var $el = $(this);
                setTimeout(function () {
                    var d = $el.datepicker('getDate');
                    if (! d || typeof d.getFullYear !== 'function') {
                        return;
                    }
                    var y = d.getFullYear();
                    var m = d.getMonth() + 1;
                    var da = d.getDate();
                    var p = function (n) { return n < 10 ? '0' + n : String(n); };
                    $el.val(y + '/' + p(m) + '/' + p(da));
                }, 0);
            }
        }));
    }

    if (typeof feather !== 'undefined') feather.replace();
})();
</script>
@endpush
