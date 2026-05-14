@extends('backend.views.view')

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/back-end/vendors/datepicker-jalali/bootstrap-datepicker.min.css') }}" type="text/css">
<style>
    .acct-pro-hero { border-radius: 20px; padding: 1.75rem 2rem; background: linear-gradient(120deg,#1e1b4b,#4338ca,#6366f1); color:#fff; margin-bottom:1.5rem; box-shadow:0 18px 40px rgba(49,46,129,.3); }
    .acct-pro-hero h1 { font-size:1.35rem; font-weight:800; margin:0 0 .4rem; }
    .acct-pro-hero p { margin:0; opacity:.9; font-size:.9rem; max-width:70ch; line-height:1.7; }
    .acct-metric { border-radius:14px; border:none; box-shadow:0 6px 18px rgba(15,23,42,.06); }
    .acct-metric .t { font-size:.75rem; color:#64748b; font-weight:600; }
    .acct-metric .v { font-size:1.25rem; font-weight:800; color:#0f172a; }
    .acct-glossary .card-header { cursor:pointer; }
</style>
@endpush

@section('main')
<div class="main-content">
    <div class="container-fluid">
        <div class="page-header d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
            <div>
                <h4 class="mb-1">برنامهٔ جامع حسابداری</h4>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard.accounting') }}">حسابداری</a></li>
                        <li class="breadcrumb-item active">گزارش حرفه‌ای</li>
                    </ol>
                </nav>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('admin.accounting.professional.export', request()->only(['j_date_from','j_date_to','date_from','date_to'])) }}" class="btn btn-success rounded-pill">خروجی CSV خلاصه</a>
                @adminany(['accounting.journal.view', 'accounting.journal.create'])
                <a href="{{ route('admin.accounting.journal.index') }}" class="btn btn-outline-primary rounded-pill">دفتر اسناد مجزا</a>
                @endadminany
                @if(Route::has('admin.technical-backup.index'))
                <a href="{{ route('admin.technical-backup.index') }}" class="btn btn-outline-light border rounded-pill text-dark" onclick="return confirm('به صفحهٔ پشتیبان‌گیری فنی بروید؟');">پشتیبان‌گیری داده‌ها</a>
                @endif
            </div>
        </div>

        <div class="acct-pro-hero">
            <h1>دید ۳۶۰ درجه بر فروش و سود تخمینی</h1>
            <p>«فروش نهایی» یعنی مبلغی که مشتری واقعاً پرداخت کرده (بعد از تخفیف). «بهای تمام‌شدهٔ تخمینی» از روی <strong>قیمت خرید فعلی</strong> کالا در کارت محصول ضرب در تعداد فروش محاسبه می‌شود — اگر قیمت خرید را پر نکرده باشید، سود واقعی را دست‌کم می‌گیرید. «سود ناخالص تخمینی» = فروش − بهای تمام‌شدهٔ تخمینی (هزینه‌های عملیاتی دیگر اینجا لحاظ نشده‌اند).</p>
            <p class="mb-0 mt-2 small" style="opacity:.88"><strong>تاریخ گزارش:</strong> اگر برای فاکتور «تاریخ پرداخت» ثبت شده باشد، همان ملاک قرار می‌گیرد؛ در غیر این صورت از «تاریخ ثبت فاکتور» استفاده می‌شود تا فروش بازاریابی با تاریخ واقعی فروش هم‌تراز بماند.</p>
        </div>

        <form method="get" class="card border-0 shadow-sm mb-4">
            <div class="card-body row align-items-end">
                @include('backend.accounting.partials.shamsi_period_filter')
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary rounded-pill">به‌روزرسانی گزارش</button>
                </div>
            </div>
        </form>

        <div class="row mb-3">
            @php $tiles = [
                ['t'=>'فروش نهایی دوره','v'=>number_format($snapshot['revenue_final']),'u'=>'تومان','c'=>'primary'],
                ['t'=>'سفارش‌های فعال','v'=>number_format($snapshot['order_count']),'u'=>'عدد','c'=>'info'],
                ['t'=>'میانگین سبد خرید','v'=>number_format($snapshot['average_basket']),'u'=>'تومان','c'=>'secondary'],
                ['t'=>'جمع تخفیفات','v'=>number_format($snapshot['total_discount']),'u'=>'تومان','c'=>'warning'],
                ['t'=>'دریافتی بابت ارسال از مشتری','v'=>number_format($snapshot['total_shipping_fee']),'u'=>'تومان','c'=>'dark'],
                ['t'=>'هزینهٔ حمل ثبت‌شده در فاکتور','v'=>number_format($snapshot['total_shipping_cost']),'u'=>'تومان','c'=>'dark'],
                ['t'=>'بیمهٔ ثبت‌شده','v'=>number_format($snapshot['total_insurance']),'u'=>'تومان','c'=>'dark'],
                ['t'=>'بهای تمام‌شدهٔ تخمینی','v'=>number_format($snapshot['estimated_cogs']),'u'=>'تومان','c'=>'danger'],
                ['t'=>'سود ناخالص تخمینی','v'=>number_format($snapshot['gross_profit_est']),'u'=>'تومان','c'=>'success'],
                ['t'=>'سفارش لغوشده در بازه','v'=>number_format($snapshot['cancelled_count']),'u'=>'عدد','c'=>'secondary'],
            ]; @endphp
            @foreach($tiles as $tile)
            <div class="col-6 col-lg-4 col-xl-3 mb-3">
                <div class="card acct-metric h-100 p-3">
                    <div class="t mb-1">{{ $tile['t'] }}</div>
                    <div class="v text-{{ $tile['c'] }}">{{ $tile['v'] }} <small class="font-weight-normal text-muted">{{ $tile['u'] }}</small></div>
                </div>
            </div>
            @endforeach
        </div>

        <div class="row">
            <div class="col-lg-4 mb-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white font-weight-bold">تفکیک منبع فروش</div>
                    <div class="card-body">
                        <div id="chart-source"></div>
                        <ul class="list-unstyled small mb-0 mt-2 text-muted">
                            <li><strong class="text-dark">سایت:</strong> {{ number_format($snapshotSite['revenue_final']) }} تومان — {{ $snapshotSite['order_count'] }} سفارش</li>
                            <li><strong class="text-dark">بازاریابی:</strong> {{ number_format($snapshotMkt['revenue_final']) }} تومان — {{ $snapshotMkt['order_count'] }} سفارش</li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 mb-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white font-weight-bold">وضعیت ارسال (کل سفارش‌ها در بازه)</div>
                    <div class="card-body"><div id="chart-status"></div></div>
                </div>
            </div>
            <div class="col-lg-4 mb-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white font-weight-bold">۱۲ ماه اخیر — فروش ماهانه</div>
                    <div class="card-body"><div id="chart-monthly"></div></div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white font-weight-bold">خلاصهٔ روزانه در بازهٔ انتخابی</div>
            <div class="table-responsive">
                <table class="table table-striped table-sm mb-0">
                    <thead><tr><th>روز</th><th>فروش</th><th>تعداد سفارش</th><th>بهای تمام‌شدهٔ تخمینی</th><th>سود روز</th></tr></thead>
                    <tbody>
                        @foreach($daily as $row)
                            @php $gp = $row['revenue'] - $row['cogs']; @endphp
                            <tr>
                                <td class="text-monospace" dir="ltr">{{ $row['d_label'] ?? $row['d'] }}</td>
                                <td>{{ number_format($row['revenue']) }}</td>
                                <td>{{ $row['orders'] }}</td>
                                <td>{{ number_format($row['cogs']) }}</td>
                                <td class="font-weight-bold {{ $gp >= 0 ? 'text-success' : 'text-danger' }}">{{ number_format($gp) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="accordion acct-glossary mb-5" id="glossary">
            <div class="card border-0 shadow-sm mb-2">
                <div class="card-header bg-light" data-toggle="collapse" data-target="#g1"><strong>واژه‌نامهٔ ساده</strong> — کلیک کنید</div>
                <div id="g1" class="collapse show" data-parent="#glossary">
                    <div class="card-body small text-muted">
                        <p class="mb-2"><strong>فاکتور فعال:</strong> سفارشی که وضعیت آن «لغو شده» نباشد؛ برای محاسبهٔ فروش استفاده می‌شود.</p>
                        <p class="mb-2"><strong>بهای تمام‌شدهٔ تخمینی:</strong> حاصل ضرب «قیمت خرید» ثبت‌شده در محصول در «تعداد فروخته‌شده در همان فاکتور». اگر کالا حذف شده باشد، از آخرین قیمت خرید ذخیره‌شده استفاده می‌شود.</p>
                        <p class="mb-0"><strong>چاپ فاکتور:</strong> @if(Route::has('admin.orders.index'))از منوی <a href="{{ route('admin.orders.index') }}">فاکتورها</a> وارد جزئیات شوید و دکمهٔ «چاپ فاکتور» را بزنید.@elseاز بخش فاکتورهای پنل مدیریت، جزئیات سفارش را باز کنید و چاپ مرورگر را بزنید.@endif</p>
                    </div>
                </div>
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
    var bySource = @json($bySource);
    if ((bySource.site + bySource.marketing) > 0) {
        new ApexCharts(document.querySelector('#chart-source'), {
            chart: { type: 'donut', height: 280 },
            labels: ['فروش سایت', 'فروش بازاریابی'],
            series: [bySource.site, bySource.marketing],
            legend: { position: 'bottom' },
            colors: ['#0ea5e9', '#a855f7'],
        }).render();
    } else {
        document.querySelector('#chart-source').innerHTML = '<p class="text-muted small p-3 mb-0">هنوز فروشی در این بازه ثبت نشده است.</p>';
    }

    var byStatus = @json($byStatus);
    var stLabels = Object.keys(byStatus);
    var stSeries = stLabels.map(function (k) { return byStatus[k]; });
    if (stLabels.length) {
        new ApexCharts(document.querySelector('#chart-status'), {
            chart: { type: 'pie', height: 280 },
            labels: stLabels,
            series: stSeries,
            legend: { position: 'bottom' },
        }).render();
    } else {
        document.querySelector('#chart-status').innerHTML = '<p class="text-muted small p-3 mb-0">داده‌ای برای نمودار وضعیت نیست.</p>';
    }

    var monthly = @json($monthly);
    if (monthly.length) {
        new ApexCharts(document.querySelector('#chart-monthly'), {
            chart: { type: 'bar', height: 280, toolbar: { show: false } },
            plotOptions: { bar: { borderRadius: 6, columnWidth: '55%' } },
            xaxis: { categories: monthly.map(function (m) { return m.ym_label || m.ym; }) },
            series: [{ name: 'فروش', data: monthly.map(function (m) { return m.revenue; }) }],
            colors: ['#4f46e5'],
            dataLabels: { enabled: false },
        }).render();
    } else {
        document.querySelector('#chart-monthly').innerHTML = '<p class="text-muted small p-3 mb-0">داده‌ای برای نمودار ماهانه نیست.</p>';
    }

    if (typeof feather !== 'undefined') feather.replace();
});
</script>
@endpush
