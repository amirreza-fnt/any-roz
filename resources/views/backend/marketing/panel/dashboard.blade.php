@extends('backend.views.view')

@push('styles')
<style>
    .mk-hero {
        border-radius: 18px;
        padding: 2rem 1.75rem;
        background: linear-gradient(125deg, #0f766e 0%, #14b8a6 42%, #2dd4bf 100%);
        color: #fff;
        box-shadow: 0 20px 50px rgba(15, 118, 110, 0.25);
        margin-bottom: 1.5rem;
    }
    .mk-hero h1 { font-size: 1.5rem; font-weight: 800; margin: 0 0 .35rem; }
    .mk-hero p { opacity: .92; margin: 0; max-width: 52ch; }
    .mk-stat {
        border-radius: 14px;
        border: 1px solid #e2e8f0;
        padding: 1rem 1.1rem;
        background: #fff;
        height: 100%;
        transition: transform .15s ease, box-shadow .15s ease;
    }
    .mk-stat:hover { transform: translateY(-2px); box-shadow: 0 12px 28px rgba(15,23,42,.08); }
    .mk-stat .n { font-size: 1.75rem; font-weight: 800; color: #0f172a; }
    .mk-stat .l { font-size: .78rem; color: #64748b; font-weight: 600; text-transform: uppercase; letter-spacing: .04em; }
    .mk-card {
        border-radius: 16px;
        border: 1px solid #e2e8f0;
        overflow: hidden;
        background: #fff;
        height: 100%;
    }
    .mk-card .ch { padding: 1rem 1.15rem; font-weight: 700; font-size: .95rem; border-bottom: 1px solid #f1f5f9; }
    .mk-card .cb { padding: 1.1rem 1.15rem; }
    .mk-card .cb p { color: #64748b; font-size: .88rem; margin-bottom: 1rem; }
    .mk-btn { border-radius: 10px; font-weight: 600; }
</style>
@endpush

@section('main')
<div class="main-content">
    <div class="container">
        <div class="page-header">
            <h4>پنل بازاریابی</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">خانه</a></li>
                    <li class="breadcrumb-item active">بازاریابی</li>
                </ol>
            </nav>
        </div>

        <div class="mk-hero">
            <h1>سلام؛ آمادهٔ ثبت ارتباط و فروش هستید</h1>
            <p>خریداران را در CRM ثبت کنید، سپس فروش را با انتخاب محصول و قیمت واقعی توافقی وارد کنید. پس از ارسال، حسابدار بررسی می‌کند و در صورت تأیید، فاکتور در سامانه ایجاد و به تأمین ارسال می‌شود.</p>
        </div>

        <div class="row mb-4">
            <div class="col-md-3 col-6 mb-3">
                <div class="mk-stat">
                    <div class="l">خریداران CRM</div>
                    <div class="n">{{ number_format($buyersCount) }}</div>
                </div>
            </div>
            <div class="col-md-3 col-6 mb-3">
                <div class="mk-stat">
                    <div class="l">در انتظار حسابداری</div>
                    <div class="n text-warning">{{ number_format($salesPending) }}</div>
                </div>
            </div>
            <div class="col-md-3 col-6 mb-3">
                <div class="mk-stat">
                    <div class="l">تأیید شده</div>
                    <div class="n text-success">{{ number_format($salesApproved) }}</div>
                </div>
            </div>
            <div class="col-md-3 col-6 mb-3">
                <div class="mk-stat">
                    <div class="l">رد شده</div>
                    <div class="n text-danger">{{ number_format($salesRejected) }}</div>
                </div>
            </div>
        </div>

        <div class="alert alert-light border shadow-sm small mb-4">
            شناسهٔ بازاریاب فعلی برای ثبت رکوردها: <strong class="text-monospace" dir="ltr">{{ $mid }}</strong>
            — پس از پیاده‌سازی احراز هویت بازاریاب، این مقدار از نشست کاربر خوانده می‌شود.
        </div>

        <div class="row">
            <div class="col-lg-4 mb-3">
                <div class="mk-card">
                    <div class="ch text-white" style="background:linear-gradient(90deg,#0d9488,#14b8a6)">خریداران (CRM)</div>
                    <div class="cb">
                        <p>ثبت نام، تماس، آدرس، استان و شهر با جست‌وجو، موقعیت روی نقشه و نام مغازه.</p>
                        <a href="{{ route('admin.marketing.buyers.index') }}" class="btn btn-outline-primary mk-btn btn-block">مدیریت خریداران</a>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 mb-3">
                <div class="mk-card">
                    <div class="ch text-white" style="background:linear-gradient(90deg,#6366f1,#8b5cf6)">ثبت فروش</div>
                    <div class="cb">
                        <p>انتخاب خریدار از CRM، محصولات سایت با قیمت توافقی، روش پرداخت و تاریخ فروش شمسی.</p>
                        <a href="{{ route('admin.marketing.sales.create') }}" class="btn btn-primary mk-btn btn-block">ثبت فروش جدید</a>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 mb-3">
                <div class="mk-card">
                    <div class="ch text-white" style="background:linear-gradient(90deg,#ea580c,#f97316)">فروش‌های من</div>
                    <div class="cb">
                        <p>وضعیت بررسی حسابداری، جزئیات و حذف فقط برای موارد در انتظار.</p>
                        <a href="{{ route('admin.marketing.sales.index') }}" class="btn btn-outline-secondary mk-btn btn-block">لیست فروش‌ها</a>
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
