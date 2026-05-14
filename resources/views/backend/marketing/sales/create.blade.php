@extends('backend.views.view')

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/back-end/vendors/datepicker-jalali/bootstrap-datepicker.min.css') }}" type="text/css">
<link rel="stylesheet" href="{{ asset('assets/back-end/vendors/select2/css/select2.min.css') }}" type="text/css">
<style>
    .ms-card { border-radius: 16px; border: 1px solid #e2e8f0; overflow: hidden; margin-bottom: 1.25rem; background: #fff; }
    .ms-card .hd { background: linear-gradient(120deg,#4f46e5,#7c3aed); color:#fff; padding:.75rem 1rem; font-weight:700; font-size:.9rem; }
    .ms-card .bd { padding: 1rem 1.1rem; background: #fafafa; }
    .ms-row-prod { border: 1px dashed #cbd5e1; border-radius: 12px; padding: .75rem; margin-bottom: .75rem; background: #fff; }
    .select2-container { width: 100% !important; }
    #buyer-preview { border-radius: 12px; }
</style>
@endpush

@php
    $oldItems = old('items', [['product_id' => '', 'unit_price' => '', 'quantity_text' => '']]);
@endphp

@section('main')
<div class="main-content">
    <div class="container">
        <div class="page-header">
            <h4>ثبت فروش</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard.marketing') }}">بازاریابی</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.marketing.sales.index') }}">فروش‌ها</a></li>
                    <li class="breadcrumb-item active">ثبت جدید</li>
                </ol>
            </nav>
        </div>

        <form method="post" action="{{ route('admin.marketing.sales.store') }}" id="marketing-sale-form" class="card border-0 shadow-sm">
            @csrf
            <div class="card-body">
                <div class="ms-card">
                    <div class="hd">انتخاب خریدار از CRM</div>
                    <div class="bd">
                        <div class="form-group mb-2">
                            <label>خریدار <span class="text-danger">*</span></label>
                            <select id="sale-buyer" class="form-control" style="width:100%" required></select>
                            <input type="hidden" name="buyer_id" id="sale-buyer-id" value="{{ old('buyer_id') }}">
                            @error('buyer_id')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                        </div>
                        <div id="buyer-preview" class="alert alert-secondary d-none mb-0">
                            <div class="row small">
                                <div class="col-md-4"><strong>نام:</strong> <span id="pv-name">—</span></div>
                                <div class="col-md-4"><strong>تماس:</strong> <span id="pv-phone" class="text-monospace" dir="ltr">—</span></div>
                                <div class="col-md-4"><strong>مغازه:</strong> <span id="pv-store">—</span></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="ms-card">
                    <div class="hd">جزئیات فروش</div>
                    <div class="bd">
                        <div class="form-row">
                            <div class="form-group col-md-4">
                                <label>تاریخ فروش (شمسی) <span class="text-danger">*</span></label>
                                <input type="text" name="sale_date_shamsi" id="sale_date_shamsi" autocomplete="off" class="form-control text-left" dir="ltr" value="{{ old('sale_date_shamsi') }}" placeholder="۱۴۰۳/۰۸/۱۵" required>
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
                        <span>محصولات و قیمت توافقی</span>
                        <button type="button" class="btn btn-sm btn-light" id="btn-add-item">+ ردیف محصول</button>
                    </div>
                    <div class="bd">
                        <p class="small text-muted">قیمت واردشده برای هر ردیف، <strong>مبلغ نهایی همان خط فروش</strong> است (نه قیمت پایهٔ سایت).</p>
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
                                            <input type="number" name="items[{{ $idx }}][unit_price]" class="form-control" min="1" value="{{ $row['unit_price'] ?? '' }}" required>
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
                <a href="{{ route('admin.marketing.sales.index') }}" class="btn btn-light">بازگشت</a>
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
    var buyersUrl = @json(route('admin.marketing.api.buyers'));
    var productsUrl = @json(route('admin.marketing.api.products'));
    var buyerBase = @json(url('/admin/marketing/api/buyers'));
    var productJsonBase = @json(url('/admin/marketing/api/products'));

    function initBuyerSelect() {
        $('#sale-buyer').select2({
            dir: 'rtl',
            width: '100%',
            placeholder: 'جست‌وجو و انتخاب خریدار...',
            allowClear: true,
            ajax: {
                url: buyersUrl,
                dataType: 'json',
                delay: 280,
                data: function (params) { return { q: params.term || '' }; },
                processResults: function (data) { return data; }
            }
        });
        var initialId = document.getElementById('sale-buyer-id').value;
        if (initialId) {
            $.getJSON(buyerBase + '/' + initialId, function (d) {
                var opt = new Option(d.first_name + ' ' + d.last_name + ' — ' + d.phone, d.id, true, true);
                $('#sale-buyer').append(opt).trigger('change');
                fillBuyerPreview(d);
            });
        }
        $('#sale-buyer').on('change', function () {
            var id = $(this).val();
            document.getElementById('sale-buyer-id').value = id || '';
            if (!id) {
                document.getElementById('buyer-preview').classList.add('d-none');
                return;
            }
            $.getJSON(buyerBase + '/' + id, function (d) { fillBuyerPreview(d); });
        });
    }
    function fillBuyerPreview(d) {
        document.getElementById('pv-name').textContent = (d.first_name || '') + ' ' + (d.last_name || '');
        document.getElementById('pv-phone').textContent = d.phone || '—';
        document.getElementById('pv-store').textContent = d.store_name || '—';
        document.getElementById('buyer-preview').classList.remove('d-none');
    }

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
        var $sel = $('#sale-items .sale-item-row:last .sale-product');
        initProductSelect($sel, null);
        rowIdx++;
    });

    $('#sale-items').on('click', '.btn-remove-item', function () {
        if ($('#sale-items .sale-item-row').length <= 1) return;
        $(this).closest('.sale-item-row').remove();
        renumberRows();
    });

    initBuyerSelect();
    $('#sale-items .sale-product').each(function () {
        var $t = $(this);
        var v = $t.val() || $t.find('option:selected').val();
        initProductSelect($t, v);
    });

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
            yearRange: (y0 - 25) + ':' + (y0 + 10)
        }));
    }

    $('#marketing-sale-form').on('submit', function () {
        document.getElementById('sale-buyer-id').value = $('#sale-buyer').val() || '';
        var id = document.getElementById('sale-buyer-id').value;
        if (!id) {
            alert('لطفاً خریدار را انتخاب کنید.');
            return false;
        }
    });

    if (typeof feather !== 'undefined') feather.replace();
})();
</script>
@endpush
