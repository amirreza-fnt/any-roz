@extends('backend.views.view')

@push('styles')
<style>
    .dash-hero {
        background: linear-gradient(125deg, #0f172a 0%, #312e81 42%, #4338ca 100%);
        border-radius: 22px;
        color: #fff;
        padding: 2rem 2.25rem;
        position: relative;
        overflow: hidden;
        box-shadow: 0 18px 40px rgba(15, 23, 42, 0.35);
    }
    .dash-hero::after {
        content: '';
        position: absolute;
        inset: -40% -20% auto auto;
        width: 55%;
        height: 140%;
        background: radial-gradient(circle at 30% 30%, rgba(255,255,255,0.14), transparent 55%);
        pointer-events: none;
    }
    .dash-hero h2 { font-weight: 800; letter-spacing: -0.02em; }
    .dash-hero .sub { opacity: 0.88; font-size: 0.95rem; }
    .dash-stat-card {
        border-radius: 18px;
        border: none;
        transition: transform 0.22s ease, box-shadow 0.22s ease;
        position: relative;
        overflow: hidden;
    }
    .dash-stat-card::before {
        content: '';
        position: absolute;
        inset-inline-start: 0;
        top: 0;
        bottom: 0;
        width: 4px;
        border-radius: 4px 0 0 4px;
        opacity: 0.85;
    }
    .dash-stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 14px 32px rgba(15, 23, 42, 0.12) !important;
    }
    .dash-stat-card.tone-primary::before { background: #4f46e5; }
    .dash-stat-card.tone-success::before { background: #059669; }
    .dash-stat-card.tone-info::before { background: #0284c7; }
    .dash-stat-card.tone-amber::before { background: #d97706; }
    .dash-stat-card.tone-slate::before { background: #64748b; }
    .dash-stat-card.tone-rose::before { background: #e11d48; }
    .dash-stat-card.tone-violet::before { background: #7c3aed; }
    .dash-stat-card.tone-teal::before { background: #0d9488; }
    .dash-stat-card .num {
        font-size: 1.85rem;
        font-weight: 800;
        letter-spacing: -0.03em;
        line-height: 1.1;
    }
    .dash-stat-card .lbl { font-size: 0.82rem; color: #64748b; font-weight: 600; }
    .dash-mini-link {
        border-radius: 14px;
        border: 1px solid rgba(148, 163, 184, 0.35);
        padding: 0.85rem 1rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        color: #334155;
        background: #fff;
        transition: border-color 0.2s, background 0.2s;
    }
    .dash-mini-link:hover {
        border-color: #6366f1;
        background: #f8fafc;
        color: #1e293b;
        text-decoration: none;
    }
</style>
@endpush

@section('main')
<div class="main-content">
    <div class="container">
        <div class="dash-hero mb-4">
            <div class="position-relative" style="z-index: 1;">
                <div class="d-flex flex-wrap justify-content-between align-items-start gap-3">
                    <div>
                        <h2 class="mb-2">داشبورد مدیریت</h2>
                        <p class="sub mb-0">سلام {{ auth('admin')->user()->full_name }} — نمای کلی فروشگاه در یک نگاه.</p>
                    </div>
                    <div class="text-left small" dir="ltr" style="opacity: 0.9;">
                        <div>{{ \App\Support\JalaliCalendar::formatShamsiDateTime(now()) }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-6 col-xl-3 mb-3">
                <div class="card dash-stat-card tone-primary border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="lbl mb-1">سفارش‌ها</div>
                        <div class="num text-primary">{{ number_format($stats['orders']) }}</div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3 mb-3">
                <div class="card dash-stat-card tone-success border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="lbl mb-1">محصولات</div>
                        <div class="num text-success">{{ number_format($stats['products']) }}</div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3 mb-3">
                <div class="card dash-stat-card tone-info border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="lbl mb-1">دسته‌بندی‌ها</div>
                        <div class="num text-info">{{ number_format($stats['categories']) }}</div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3 mb-3">
                <div class="card dash-stat-card tone-slate border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="lbl mb-1">کاربران سایت</div>
                        <div class="num text-secondary">{{ number_format($stats['users']) }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-6 col-xl-3 mb-3">
                <div class="card dash-stat-card tone-violet border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="lbl mb-1">مقالات</div>
                        <div class="num" style="color:#5b21b6;">{{ number_format($stats['articles']) }}</div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3 mb-3">
                <div class="card dash-stat-card tone-amber border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="lbl mb-1">فروش بازاریابی در انتظار حسابداری</div>
                        <div class="num text-warning">{{ number_format($stats['marketing_pending']) }}</div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3 mb-3">
                <div class="card dash-stat-card tone-rose border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="lbl mb-1">کدهای هدیه</div>
                        <div class="num text-danger">{{ number_format($stats['gift_codes']) }}</div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3 mb-3">
                <div class="card dash-stat-card tone-teal border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="lbl mb-1">کدهای تخفیف</div>
                        <div class="num" style="color:#0f766e;">{{ number_format($stats['discount_codes']) }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-2">
            <div class="col-lg-6 mb-3">
                <div class="font-weight-bold mb-2 text-muted small text-uppercase" style="letter-spacing:.06em;">میانبرها</div>
                <div class="d-flex flex-column gap-2">
                    @admincan('orders.view')
                        <a class="dash-mini-link" href="{{ route('admin.orders.index') }}"><span>فاکتورها</span><i data-feather="chevron-left" class="width-16 height-16"></i></a>
                    @endadmincan
                    @admincan('products.view')
                        <a class="dash-mini-link" href="{{ route('admin.products.index') }}"><span>محصولات</span><i data-feather="chevron-left" class="width-16 height-16"></i></a>
                    @endadmincan
                    @adminany(['accounting.marketing_sales.view_list', 'accounting.marketing_sales.view_detail', 'accounting.marketing_sales.approve'])
                        <a class="dash-mini-link" href="{{ route('admin.accounting.marketing-sales.index') }}"><span>بررسی فروش بازاریابان</span><i data-feather="chevron-left" class="width-16 height-16"></i></a>
                    @endadminany
                </div>
            </div>
            <div class="col-lg-6 mb-3">
                <div class="font-weight-bold mb-2 text-muted small text-uppercase" style="letter-spacing:.06em;">وضعیت</div>
                <div class="card border-0 shadow-sm h-100" style="border-radius: 18px;">
                    <div class="card-body text-muted small leading-relaxed">
                        اعداد بالا از پایگاه داده به‌صورت زنده خوانده می‌شوند. برای ورود به هر بخش از منوی کناری یا میانبرها استفاده کنید.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>if (typeof feather !== 'undefined') feather.replace();</script>
@endpush
