@extends('backend.views.view')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/back-end/vendors/datepicker-jalali/bootstrap-datepicker.min.css') }}" type="text/css">
    <link rel="stylesheet" href="{{ asset('assets/back-end/vendors/select2/css/select2.min.css') }}" type="text/css">
    <style>
        .ms-card { border-radius: 16px; border: 1px solid #e2e8f0; overflow: hidden; margin-bottom: 1.25rem; background: #fff; }
        .ms-card .hd { background: linear-gradient(120deg,#0d9488,#0f766e); color:#fff; padding:.75rem 1rem; font-weight:700; font-size:.9rem; }
        .ms-card .bd { padding: 1rem 1.1rem; background: #fafafa; }
        .ms-row-prod { border: 1px dashed #cbd5e1; border-radius: 12px; padding: .75rem; margin-bottom: .75rem; background: #fff; }
        .select2-container { width: 100% !important; }
        .btn-today { font-size: 0.75rem; padding: 0.2rem 0.5rem; margin-right: 0.5rem; }
    </style>
@endpush

@php
    $oldItems = old('items', [['product_id' => '', 'unit_price' => '', 'quantity_text' => '']]);
@endphp

@section('main')
    <div class="main-content">
        <div class="container">
            <div class="page-header">
                <h4>ثبت پیش‌فاکتور خرید کلی</h4>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.colleague.sales.index') }}">پیش‌فاکتورهای من</a></li>
                        <li class="breadcrumb-item active">ثبت جدید</li>
                    </ol>
                </nav>
            </div>

            @if($colleague)
                <div class="alert alert-teal border-0 shadow-sm">
                    <div class="row small">
                        <div class="col-md-4"><strong>نام:</strong> {{ $colleague->full_name }}</div>
                        <div class="col-md-4"><strong>تماس:</strong> <span class="text-monospace" dir="ltr">{{ $colleague->phone }}</span></div>
                        <div class="col-md-4"><strong>فروشگاه / محل:</strong> {{ $colleague->store_name ?: '—' }}</div>
                    </div>
                </div>
            @endif

            <form method="post" action="{{ route('admin.colleague.sales.store') }}" id="colleague-sale-form" class="card border-0 shadow-sm">
                @csrf
                <div class="card-body">
                    <div class="ms-card">
                        <div class="hd">جزئیات خرید کلی</div>
                        <div class="bd">
                            <div class="form-row">
                                <div class="form-group col-md-4">
                                    <label>تاریخ خرید (شمسی) <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="text" name="sale_date_shamsi" id="sale_date_shamsi" autocomplete="off" class="form-control text-left" dir="ltr" value="{{ old('sale_date_shamsi') }}" placeholder="۱۴۰۴/۰۵/۱۸" required>
                                        <div class="input-group-append">
                                            <button type="button" class="btn btn-sm btn-outline-primary btn-today" id="btn-today-sale">امروز</button>
                                        </div>
                                    </div>
                                    @error('sale_date_shamsi')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                                </div>
                                <div class="form-group col-md-8">
                                    <label>روش پرداخت (متنی)</label>
                                    <input type="text" name="payment_method" class="form-control" value="{{ old('payment_method') }}" maxlength="255" placeholder="مثلاً کارت به کارت، نقدی، ...">
                                </div>
                            </div>
                            <div class="form-group mb-0">
                                <label>توضیحات</label>
                                <textarea name="notes" class="form-control" rows="2" maxlength="5000">{{ old('notes') }}</textarea>
                            </div>
                        </div>
                    </div>
                    <div class="ms-card">
                        <div class="hd d-flex justify-content-between align-items-center">
                            <span>محصولات و قیمت توافقی خرید کلی</span>
                            <button type="button" class="btn btn-sm btn-light" id="btn-add-item">+ ردیف محصول</button>
                        </div>
                        <div class="bd">
                            <p class="small text-muted">قیمت واردشده برای هر ردیف، <strong>مبلغ نهایی همان خط</strong> است (نه قیمت پایهٔ سایت).</p>
                            <div id="sale-items">
                                @foreach ($oldItems as $idx => $row)
                                    <div class="ms-row-prod sale-item-row" data-idx="{{ $idx }}">
                                        <div class="form-row align-items-end">
                                            <div class="form-group col-lg-5 col-md-12">
                                                <label>محصول</label>
                                                <select name="items[{{ $idx }}][product_id]" class="form-control sale-product" style="width:100%" data-idx="{{ $idx }}" required>
                                                    @if(! empty($row['product_id']))
                                                        <option value="{{ (int) $row['product_id'] }}" selected>محصول #{{ (int) $row['product_id'] }}</option>
                                                    @endif
                                                </select>
                                            </div>
                                            <div class="form-group col-lg-3 col-md-6">
                                                <label>مبلغ فروش (تومان)</label>
                                                <input type="text" name="items[{{ $idx }}][unit_price]" class="form-control price-input" min="1" value="{{ $row['unit_price'] ?? '' }}" required>
                                            </div>
                                            <div class="form-group col-lg-3 col-md-5">
                                                <label>مقدار / وزن (متنی)</label>
                                                <input type="text" name="items[{{ $idx }}][quantity_text]" class="form-control" value="{{ $row['quantity_text'] ?? '' }}" maxlength="500" required placeholder="مثلاً ۲ کیلو، ۱۰ عدد، ...">
                                            </div>
                                            <div class="form-group col-lg-1 col-md-1 text-center">
                                                <label class="d-none d-lg-block">&nbsp;</label>
                                                <button type="button" class="btn btn-outline-danger btn-sm btn-remove-item" title="حذف ردیف">&times;</button>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            @error('items')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                            @error('items.*')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-white d-flex justify-content-between">
                    <a href="{{ route('admin.colleague.sales.index') }}" class="btn btn-light">بازگشت</a>
                    <button type="submit" class="btn btn-primary px-4">ارسال برای حسابداری</button>
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
            var productsUrl = @json(route('admin.colleague.sales.api.products'));
            var productJsonBase = @json(url('/admin/colleague-sales/api/products'));

            function initProductSelect($sel, selectedId) {
                if ($sel.hasClass('select2-hidden-accessible')) {
                    $sel.select2('destroy');
                }
                $sel.select2({
                    dir: 'rtl',
                    width: '100%',
                    placeholder: 'جست‌وجوی محصول...',
                    ajax: {
                        url: productsUrl,
                        dataType: 'json',
                        delay: 280,
                        data: function (params) { return { q: params.term || '' }; },
                        processResults: function (data) { return data; }
                    }
                });
                if (selectedId) {
                    $.getJSON(productJsonBase + '/' + selectedId, function (d) {
                        var opt = new Option(d.text, d.id, true, true);
                        $sel.append(opt).trigger('change');
                    });
                }
            }

            var rowIdx = {{ count($oldItems) }};
            function renumberRows() {
                $('#sale-items .sale-item-row').each(function (i) {
                    $(this).attr('data-idx', i);
                    $(this).find('select.sale-product').attr('name', 'items[' + i + '][product_id]').attr('data-idx', i);
                    $(this).find('input[type=number]').attr('name', 'items[' + i + '][unit_price]');
                    $(this).find('input[type=text]').not('.select2-search__field').attr('name', 'items[' + i + '][quantity_text]');
                });
            }

            $('#btn-add-item').on('click', function () {
                var html = '<div class="ms-row-prod sale-item-row" data-idx="' + rowIdx + '">' +
                    '<div class="form-row align-items-end">' +
                    '<div class="form-group col-lg-5 col-md-12"><label>محصول</label>' +
                    '<select name="items[' + rowIdx + '][product_id]" class="form-control sale-product" style="width:100%" required></select></div>' +
                    '<div class="form-group col-lg-3 col-md-6"><label>مبلغ فروش (تومان)</label>' +
                    '<input type="number" name="items[' + rowIdx + '][unit_price]" class="form-control" min="1" required></div>' +
                    '<div class="form-group col-lg-3 col-md-5"><label>مقدار / وزن (متنی)</label>' +
                    '<input type="text" name="items[' + rowIdx + '][quantity_text]" class="form-control" maxlength="500" required placeholder="مثلاً ۲ کیلو"></div>' +
                    '<div class="form-group col-lg-1 col-md-1 text-center"><label class="d-none d-lg-block">&nbsp;</label>' +
                    '<button type="button" class="btn btn-outline-danger btn-sm btn-remove-item">&times;</button></div></div></div>';
                $('#sale-items').append(html);
                initProductSelect($('#sale-items .sale-item-row:last .sale-product'), null);
                rowIdx++;
            });

            $('#sale-items').on('click', '.btn-remove-item', function () {
                if ($('#sale-items .sale-item-row').length <= 1) return;
                $(this).closest('.sale-item-row').remove();
                renumberRows();
            });

            $('#sale-items .sale-product').each(function () {
                var $t = $(this);
                var v = $t.val() || $t.find('option:selected').val();
                initProductSelect($t, v);
            });

            function selectToday(targetSelector) {
                var $input = $(targetSelector);
                var $dp = $input.data('datepicker');
                if (!$dp) { setTimeout(function () { selectToday(targetSelector); }, 100); return; }
                $input.datepicker('show');
                setTimeout(function () {
                    var today = new JalaliDate();
                    var year = today.getFullYear();
                    var month = today.getMonth() + 1;
                    var day = today.getDate();
                    var dateStr = year + '/' + (month < 10 ? '0' + month : month) + '/' + (day < 10 ? '0' + day : day);
                    $input.val(dateStr);
                    $input.datepicker('update');
                }, 200);
            }

            $('#btn-today-sale').on('click', function () { selectToday('#sale_date_shamsi'); });

            if (typeof $.fn.datepicker !== 'undefined') {
                var cal = typeof JalaliDate !== 'undefined' ? JalaliDate : undefined;
                var base = $.datepicker.regional['fa'] || {};
                $('#sale_date_shamsi').datepicker($.extend({}, base, {
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
                        var $el = $('#sale_date_shamsi');
                        setTimeout(function () {
                            var d = $el.datepicker('getDate');
                            if (!d || typeof d.getFullYear !== 'function') return;
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