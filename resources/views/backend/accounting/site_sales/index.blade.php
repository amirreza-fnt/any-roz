@extends('backend.views.view')

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/back-end/vendors/datepicker-jalali/bootstrap-datepicker.min.css') }}" type="text/css">
<style>
    .acct-site-hero { border-radius:18px; background:linear-gradient(110deg,#064e3b,#059669,#10b981); color:#fff; padding:1.5rem 1.75rem; margin-bottom:1.25rem; }
    .acct-site-hero h1 { font-size:1.2rem; font-weight:800; margin:0 0 .35rem; }
    .acct-site-hero p { margin:0; opacity:.92; font-size:.88rem; max-width:68ch; }
    .mini-kpi { border-radius:12px; background:#fff; border:1px solid #e2e8f0; padding:.85rem 1rem; }
    .mini-kpi .l { font-size:.72rem; color:#64748b; font-weight:600; }
    .mini-kpi .v { font-weight:800; font-size:1.1rem; }
</style>
@endpush

@section('main')
<div class="main-content">
    <div class="container-fluid">
        <div class="page-header mb-3">
            <h4 class="mb-1">حسابداری فروش سایت</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard.accounting') }}">حسابداری</a></li>
                    <li class="breadcrumb-item active">فروش سایت</li>
                </ol>
            </nav>
        </div>

        <div class="acct-site-hero">
            <h1>فروش کانال غیر بازاریابی (سایت و سفارش‌های قدیمی)</h1>
            <p class="mb-0">فاکتورهایی که <strong>بازاریابی</strong> نباشند (شامل سفارش‌های قدیمی بدون فیلد منبع) در این گزارش دیده می‌شوند. فروش تأییدشدهٔ بازاریابان با منبع «بازاریابی» فقط در «برنامهٔ جامع» و داشبورد حسابداری لحاظ می‌شود. <strong>تاریخ نمایش:</strong> در صورت وجود «تاریخ پرداخت» همان به شمسی است؛ وگرنه تاریخ ثبت فاکتور.</p>
        </div>

        <form method="get" class="card border-0 shadow-sm mb-3">
            <div class="card-body row align-items-end">
                @include('backend.accounting.partials.shamsi_period_filter')
                <div class="col-md-2 mb-2">
                    <label class="small text-muted">وضعیت پرداخت</label>
                    <select name="payment_status" class="form-control">
                        <option value="">همه</option>
                        @foreach(['pending'=>'در انتظار','paid'=>'پرداخت‌شده','failed'=>'ناموفق','refunded'=>'بازگشت وجه'] as $k=>$lbl)
                            <option value="{{ $k }}" @selected(request('payment_status')===$k)>{{ $lbl }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 mb-2">
                    <label class="small text-muted">وضعیت ارسال</label>
                    <select name="shipping_status" class="form-control">
                        <option value="">همه</option>
                        @foreach(\App\Models\Order::SHIPPING_STATUSES as $st)
                            <option value="{{ $st }}" @selected(request('shipping_status')===$st)>{{ \App\Models\Order::shippingStatusLabel($st) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 mb-2">
                    <button type="submit" class="btn btn-primary btn-block rounded-pill">اعمال</button>
                </div>
                <div class="col-md-2 mb-2">
                    <a href="{{ route('admin.accounting.site-sales.export', request()->all()) }}" class="btn btn-success btn-block rounded-pill">خروجی CSV</a>
                </div>
            </div>
        </form>

        <div class="row mb-3">
            <div class="col-6 col-md-3 mb-2"><div class="mini-kpi"><div class="l">فروش نهایی</div><div class="v text-success">{{ number_format($snapshot['revenue_final']) }}</div></div></div>
            <div class="col-6 col-md-3 mb-2"><div class="mini-kpi"><div class="l">سفارش‌ها</div><div class="v">{{ number_format($snapshot['order_count']) }}</div></div></div>
            <div class="col-6 col-md-3 mb-2"><div class="mini-kpi"><div class="l">سود تخمینی</div><div class="v text-primary">{{ number_format($snapshot['gross_profit_est']) }}</div></div></div>
            <div class="col-6 col-md-3 mb-2"><div class="mini-kpi"><div class="l">تخفیف</div><div class="v text-warning">{{ number_format($snapshot['total_discount']) }}</div></div></div>
        </div>

        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white font-weight-bold">روند روزانه (فروش سایت)</div>
            <div class="card-body"><div id="acct-site-daily" style="min-height:240px;"></div></div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <span class="font-weight-bold">لیست فاکتورها</span>
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th>#</th>
                            <th>شماره فاکتور</th>
                            <th>تاریخ (شمسی)</th>
                            <th>مشتری</th>
                            <th>پرداخت</th>
                            <th>ارسال</th>
                            <th>نهایی</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($orders as $o)
                            <tr>
                                <td>{{ $o->id }}</td>
                                <td class="text-monospace" dir="ltr">{{ $o->order_number }}</td>
                                <td class="text-monospace small" dir="ltr">{{ \App\Support\JalaliCalendar::formatShamsiDateTime($o->payment_date ?? $o->created_at) }}</td>
                                <td>{{ $o->user?->name ?? '—' }}</td>
                                <td><span class="badge badge-secondary">{{ \App\Models\Order::paymentStatusLabel($o->payment_status) }}</span></td>
                                <td><span class="badge badge-info">{{ \App\Models\Order::shippingStatusLabel($o->shipping_status) }}</span></td>
                                <td class="font-weight-bold">{{ number_format((float)$o->final_amount) }}</td>
                                <td>
                                    @if(Route::has('admin.orders.show'))
                                        <a href="{{ route('admin.orders.show', $o) }}" class="btn btn-sm btn-outline-primary">جزئیات / چاپ</a>
                                    @else
                                        <span class="text-muted small">—</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="8" class="text-center text-muted py-4">فاکتوری در این بازه یافت نشد.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($orders->hasPages())
                <div class="card-footer bg-white">{{ $orders->links() }}</div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
@include('backend.accounting.partials.shamsi_period_filter_scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    if (typeof ApexCharts === 'undefined') return;
    var daily = @json($dailyChart ?? $daily);
    if (!daily.length) return;
    new ApexCharts(document.querySelector('#acct-site-daily'), {
        chart: { type: 'line', height: 240, toolbar: { show: false } },
        stroke: { curve: 'smooth', width: 2 },
        colors: ['#059669', '#0f766e'],
        xaxis: {
            categories: daily.map(function (r) { return r.d_label || r.d; }),
            labels: { rotate: -35, hideOverlappingLabels: true, maxHeight: 100, trim: true }
        },
        series: [
            { name: 'فروش', data: daily.map(function (r) { return r.revenue; }) },
            { name: 'بهای تمام‌شدهٔ تخمینی', data: daily.map(function (r) { return r.cogs; }) },
        ],
        legend: { position: 'top' },
        yaxis: { labels: { formatter: function (v) { return Math.round(v).toLocaleString('fa-IR'); } } },
    }).render();
});
</script>
@endpush
