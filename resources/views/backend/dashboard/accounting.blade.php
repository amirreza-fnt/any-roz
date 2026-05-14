@extends('backend.views.view')

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/back-end/vendors/datepicker-jalali/bootstrap-datepicker.min.css') }}" type="text/css">
<style>
    .acct-hub-hero {
        border-radius: 20px;
        padding: 2rem 2.25rem;
        background: linear-gradient(118deg, #0c4a6e 0%, #0369a1 38%, #2563eb 100%);
        color: #fff;
        box-shadow: 0 20px 50px rgba(12, 74, 110, 0.28);
        margin-bottom: 1.75rem;
    }
    .acct-hub-hero h1 { font-size: 1.45rem; font-weight: 800; margin: 0 0 .5rem; }
    .acct-hub-hero p { margin: 0; opacity: .92; max-width: 62ch; font-size: .95rem; line-height: 1.75; }
    .acct-kpi {
        border-radius: 16px;
        border: none;
        background: #fff;
        box-shadow: 0 8px 24px rgba(15,23,42,.06);
    }
    .acct-kpi .v { font-size: 1.35rem; font-weight: 800; color: #0f172a; }
    .acct-kpi .l { font-size: .78rem; color: #64748b; font-weight: 600; }
    .acct-tile {
        border-radius: 16px;
        border: 1px solid #e2e8f0;
        transition: .18s ease;
        height: 100%;
    }
    .acct-tile:hover { border-color: #93c5fd; box-shadow: 0 12px 28px rgba(37,99,235,.12); transform: translateY(-2px); }
    .acct-tile h5 { font-weight: 700; font-size: 1.02rem; }
    .acct-tile .lead { font-size: .88rem; color: #64748b; line-height: 1.65; }
    .badge-soft { background: rgba(255,255,255,.2); color: #fff; font-weight: 700; padding: .35rem .65rem; border-radius: 999px; }
</style>
@endpush

@section('main')
<div class="main-content">
    <div class="container">
        <div class="page-header d-flex flex-wrap justify-content-between align-items-center gap-2">
            <div>
                <h4 class="mb-1">داشبورد حسابداری</h4>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">داشبورد مدیریت</a></li>
                        <li class="breadcrumb-item active">حسابداری</li>
                    </ol>
                </nav>
            </div>
            <div class="text-muted small text-left" dir="ltr">{{ \App\Support\JalaliCalendar::formatShamsiDate($from) }} — {{ \App\Support\JalaliCalendar::formatShamsiDate($to) }}</div>
        </div>

        <div class="acct-hub-hero">
            <div class="d-flex flex-wrap justify-content-between align-items-start gap-3">
                <div>
                    <h1>مرکز یکپارچهٔ حسابداری</h1>
                    <p>اینجا نمای کلی تمام فروش ثبت‌شده در سایت (فاکتورها) و بخش‌های تخصصی کنار هم قرار دارد. نیازی به دانش قبلی حسابداری نیست؛ هر کارت توضیح می‌دهد چه کاری انجام می‌دهد و اعداد به زبان ساده فروش، تخفیف و سود تخمینی را نشان می‌دهند.</p>
                </div>
                @if(($pendingMarketingSales ?? 0) > 0)
                    <span class="badge-soft">فروش بازاریابی در انتظار: {{ $pendingMarketingSales }}</span>
                @endif
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-6 col-md-3 mb-2">
                <div class="card acct-kpi p-3">
                    <div class="l mb-1">کل فروش (فاکتورهای غیرلغو)</div>
                    <div class="v text-primary">{{ number_format($unified['revenue_final']) }} <small class="font-weight-normal">تومان</small></div>
                </div>
            </div>
            <div class="col-6 col-md-3 mb-2">
                <div class="card acct-kpi p-3">
                    <div class="l mb-1">تعداد سفارش</div>
                    <div class="v text-info">{{ number_format($unified['order_count']) }}</div>
                </div>
            </div>
            <div class="col-6 col-md-3 mb-2">
                <div class="card acct-kpi p-3">
                    <div class="l mb-1">سود ناخالص تخمینی</div>
                    <div class="v text-success">{{ number_format($unified['gross_profit_est']) }} <small class="font-weight-normal">تومان</small></div>
                </div>
            </div>
            <div class="col-6 col-md-3 mb-2">
                <div class="card acct-kpi p-3">
                    <div class="l mb-1">سهم سایت / بازاریابی (جمع = کل فروش دوره)</div>
                    <div class="v small text-dark">{{ number_format($bySource['site']) }} / {{ number_format($bySource['marketing']) }}</div>
                    <div class="text-muted small mt-1">کل تأییدشده در بازه: {{ number_format($bySource['total'] ?? ($bySource['site'] + $bySource['marketing'])) }} تومان</div>
                </div>
            </div>
        </div>

        <h6 class="text-muted font-weight-bold mb-3">بخش‌های پنل حسابداری</h6>
        <div class="row">
            <div class="col-md-6 col-xl-4 mb-3">
                <div class="card acct-tile h-100">
                    <div class="card-body d-flex flex-column">
                        <h5 class="mb-2">۱ — برنامهٔ جامع حسابداری</h5>
                        <p class="lead flex-grow-1">نمودارها، خلاصهٔ روزانه، تفکیک منبع فروش، وضعیت ارسال و خروجی CSV؛ به‌همراه <strong>حسابداری مجزا</strong> برای ثبت هزینه/درآمد و معین خارج از فاکتور سایت.</p>
                        <a href="{{ route('admin.accounting.professional.index', ['j_date_from' => \App\Support\JalaliCalendar::formatShamsiDate($from), 'j_date_to' => \App\Support\JalaliCalendar::formatShamsiDate($to)]) }}" class="btn btn-primary rounded-pill mt-2">ورود</a>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-xl-4 mb-3">
                <div class="card acct-tile h-100 border-warning" style="border-width:2px;">
                    <div class="card-body d-flex flex-column">
                        <h5 class="mb-2">۲ — فروش بازاریابان</h5>
                        <p class="lead flex-grow-1">بررسی و تأیید یا رد فروش‌های ثبت‌شده توسط بازاریابان؛ پس از تأیید فاکتور رسمی ساخته می‌شود.</p>
                        <a href="{{ route('admin.accounting.marketing-sales.index') }}" class="btn btn-warning text-dark rounded-pill mt-2">ورود به صف بررسی</a>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-xl-4 mb-3">
                <div class="card acct-tile h-100">
                    <div class="card-body d-flex flex-column">
                        <h5 class="mb-2">۳ — حسابداری فروش سایت</h5>
                        <p class="lead flex-grow-1">فقط فاکتورهایی که <strong>بازاریابی نیستند</strong> (سایت و رکوردهای قدیمی بدون منبع)؛ فیلتر وضعیت، جمع مبالغ و خروجی CSV.</p>
                        <a href="{{ route('admin.accounting.site-sales.index', ['j_date_from' => \App\Support\JalaliCalendar::formatShamsiDate($from), 'j_date_to' => \App\Support\JalaliCalendar::formatShamsiDate($to)]) }}" class="btn btn-outline-primary rounded-pill mt-2">ورود</a>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-xl-4 mb-3">
                <div class="card acct-tile h-100 opacity-75">
                    <div class="card-body d-flex flex-column">
                        <h5 class="mb-2">۴ — فلوچارت فرایندها</h5>
                        <p class="lead flex-grow-1">برای مراحل بعدی توسعه نگه داشته شده است.</p>
                        <button type="button" class="btn btn-light rounded-pill mt-2" disabled>به‌زودی</button>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-xl-4 mb-3">
                <div class="card acct-tile h-100 border-primary" style="border-width:2px;">
                    <div class="card-body d-flex flex-column">
                        <h5 class="mb-2">۵ — مدیریت یکپارچه (همین صفحه)</h5>
                        <p class="lead flex-grow-1">خلاصهٔ دورهٔ انتخابی، مقایسهٔ سهم سایت و بازاریابی و دسترسی سریع به همهٔ زیربخش‌ها. بازهٔ زمان را با تقویم <strong>شمسی</strong> تنظیم کنید.</p>
                        <form method="get" class="mt-2">
                            <div class="row no-gutters gutter-sm align-items-end">
                                @include('backend.accounting.partials.shamsi_period_filter')
                            </div>
                            <div class="mt-2">
                                <button type="submit" class="btn btn-sm btn-primary btn-block rounded-pill">اعمال بازه</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm mt-2">
            <div class="card-header bg-white font-weight-bold">روند ۷ روز اخیر (فروش روزانه)</div>
            <div class="card-body">
                <div id="acct-hub-spark" style="min-height: 260px;"></div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
@include('backend.accounting.partials.shamsi_period_filter_scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    if (typeof ApexCharts === 'undefined') return;
    var spark = @json($spark);
    if (!spark || !spark.length) {
        var el = document.querySelector('#acct-hub-spark');
        if (el) el.innerHTML = '<p class="text-muted small mb-0 p-3">داده‌ای برای این بازه ثبت نشده است.</p>';
        return;
    }
    var categories = spark.map(function (r) { return r.d_label || r.d; });
    var series = [{ name: 'فروش', data: spark.map(function (r) { return r.revenue; }) }];
    new ApexCharts(document.querySelector('#acct-hub-spark'), {
        chart: { type: 'area', height: 260, toolbar: { show: false }, fontFamily: 'inherit' },
        dataLabels: { enabled: false },
        stroke: { curve: 'smooth', width: 2 },
        colors: ['#2563eb'],
        fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.35, opacityTo: 0.05 } },
        xaxis: { categories: categories, labels: { rotate: -35, hideOverlappingLabels: true, maxHeight: 90, trim: true } },
        yaxis: { labels: { formatter: function (v) { return Math.round(v).toLocaleString('fa-IR'); } } },
        series: series,
    }).render();
    if (typeof feather !== 'undefined') feather.replace();
});
</script>
@endpush
