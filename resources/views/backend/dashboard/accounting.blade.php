@extends('backend.views.view')

@push('styles')
<style>
    .acct-hero {
        border-radius: 16px;
        padding: 1.75rem;
        background: linear-gradient(120deg, #1e3a8a 0%, #3b82f6 50%, #6366f1 100%);
        color: #fff;
        margin-bottom: 1.5rem;
        box-shadow: 0 18px 45px rgba(30, 58, 138, 0.22);
    }
    .acct-hero h1 { font-size: 1.35rem; font-weight: 800; margin: 0 0 .35rem; }
    .acct-hero p { opacity: .9; margin: 0; max-width: 56ch; font-size: .92rem; }
    .acct-tiles .card { border-radius: 14px; border: 1px solid #e2e8f0; transition: .15s ease; }
    .acct-tiles .card:hover { box-shadow: 0 10px 28px rgba(15,23,42,.08); transform: translateY(-2px); }
    .acct-tiles .card h5 { font-weight: 700; font-size: 1rem; }
    .acct-badge { font-size: 1.25rem; font-weight: 800; }
</style>
@endpush

@section('main')
<div class="main-content">
    <div class="container">
        <div class="page-header">
            <h4>داشبورد حسابداری</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">داشبورد مدیریت</a></li>
                    <li class="breadcrumb-item active">حسابداری</li>
                </ol>
            </nav>
        </div>

        <div class="acct-hero">
            <h1>مرکز حسابداری و کنترل مالی</h1>
            <p>این صفحه برای اتصال به گزارش‌های مالی و ماژول‌های بعدی آماده است. اکنون می‌توانید فروش‌های ثبت‌شده توسط بازاریابان را بررسی و تأیید یا رد کنید؛ پس از تأیید، فاکتور رسمی در سیستم ایجاد و به بخش تأمین ارسال می‌شود.</p>
        </div>

        <div class="row acct-tiles mb-4">
            <div class="col-md-6 mb-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body d-flex flex-column">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h5 class="mb-1">بررسی فروش بازاریابان</h5>
                                <p class="text-muted small mb-0">تأیید یا رد فروش‌های در انتظار؛ ایجاد فاکتور و ارسال به تأمین پس از تأیید.</p>
                            </div>
                            @if(($pendingMarketingSales ?? 0) > 0)
                                <span class="badge badge-warning acct-badge">{{ $pendingMarketingSales }}</span>
                            @endif
                        </div>
                        <div class="mt-auto pt-3">
                            <a href="{{ route('admin.accounting.marketing-sales.index') }}" class="btn btn-primary btn-block rounded-pill">ورود به صف بررسی</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 mb-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body d-flex flex-column">
                        <h5 class="mb-1">گزارش‌های مالی</h5>
                        <p class="text-muted small flex-grow-1">اتصال به API حسابداری، تراز، سود و زیان و نمودارها در نسخه‌های بعدی اینجا قرار می‌گیرد.</p>
                        <button type="button" class="btn btn-outline-secondary btn-block rounded-pill" disabled>به‌زودی</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body text-center text-muted py-4">
                <i data-feather="pie-chart" class="width-40 height-40 mb-2 opacity-40"></i>
                <p class="mb-0 small">برای شروع، کارت «بررسی فروش بازاریابان» را باز کنید.</p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>if (typeof feather !== 'undefined') feather.replace();</script>
@endpush
