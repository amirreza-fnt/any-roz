@extends('backend.views.view')

@push('styles')
<style>
    .acct-std-hero { border-radius: 20px; padding: 2rem 2.25rem; background: linear-gradient(125deg,#14532d,#166534,#15803d); color:#fff; box-shadow:0 20px 48px rgba(22,101,52,.28); margin-bottom:1.75rem; }
    .acct-std-hero h1 { font-size:1.45rem; font-weight:800; margin:0 0 .5rem; }
    .acct-std-hero p { margin:0; opacity:.93; max-width:72ch; line-height:1.8; font-size:.92rem; }
    .acct-std-kpi { border-radius:14px; border:none; box-shadow:0 8px 22px rgba(15,23,42,.07); }
    .acct-std-kpi .t { font-size:.75rem; color:#64748b; font-weight:600; }
    .acct-std-kpi .v { font-size:1.2rem; font-weight:800; }
    .acct-std-tile { border-radius:16px; border:1px solid #e2e8f0; transition:.18s ease; height:100%; }
    .acct-std-tile:hover { border-color:#86efac; box-shadow:0 12px 28px rgba(22,163,74,.12); transform:translateY(-2px); }
</style>
@endpush

@section('main')
<div class="main-content">
    <div class="container-fluid">
        <div class="page-header d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
            <div>
                <h4 class="mb-1">حسابداری مجزا (سامانهٔ حرفه‌ای)</h4>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard.accounting') }}">حسابداری</a></li>
                        <li class="breadcrumb-item active">حسابداری مجزا</li>
                    </ol>
                </nav>
            </div>
            @admincan('accounting.professional.view')
            <a href="{{ route('admin.accounting.professional.index') }}" class="btn btn-outline-secondary rounded-pill">برنامهٔ جامع فاکتورها</a>
            @endadmincan
        </div>

        <div class="acct-std-hero">
            <h1>ثبت مالی مستقل از فروش آنلاین</h1>
            <p>این بخش برای همان نقشی است که در نرم‌افزارهایی مانند <strong>هلو</strong> یا <strong>سپیدار</strong> با «دفتر روزنامه» و «معین» آشناست: هزینه‌های خارج از فاکتور، درآمدهای غیرفروشگاهی، تعدیل دوره و یادداشت‌های مالی با تاریخ شمسی و ردیابی ثبت‌کننده. فاکتورهای سایت و بازاریابی همچنان در گزارش «جامع» و «فروش سایت» محاسبه می‌شوند؛ اینجا فقط دادهٔ دستی شماست.</p>
            <p class="mb-0 mt-2 small" style="opacity:.88">برای ساختار کامل دفتر کل دوبل و چند ارزی، می‌توان در مراحل بعد جداول حساب تفصیلی و اسناد چندسطری اضافه کرد؛ فعلاً ثبت تک‌سطحی با فیلدهای معین و طرف حساب در دسترس است.</p>
        </div>

        @if(!($tableAvailable ?? false))
            <div class="alert alert-warning border-0 shadow-sm">جدول اسناد حسابداری مجزا هنوز در دیتابیس ایجاد نشده است. ابتدا <code>php artisan migrate</code> را اجرا کنید.</div>
        @else
        <div class="row mb-3">
            <div class="col-md-4 mb-2">
                <div class="card acct-std-kpi p-3 h-100">
                    <div class="t mb-1">جمع درآمد / واریز (کل دوره)</div>
                    <div class="v text-success">{{ number_format($sumIncome) }} <small class="text-muted font-weight-normal">تومان</small></div>
                </div>
            </div>
            <div class="col-md-4 mb-2">
                <div class="card acct-std-kpi p-3 h-100">
                    <div class="t mb-1">جمع هزینه / برداشت</div>
                    <div class="v text-danger">{{ number_format($sumExpense) }} <small class="text-muted font-weight-normal">تومان</small></div>
                </div>
            </div>
            <div class="col-md-4 mb-2">
                <div class="card acct-std-kpi p-3 h-100">
                    <div class="t mb-1">خالص پس از تعدیل (درآمد − هزینه + تعدیل)</div>
                    @php $net = $sumIncome - $sumExpense + $sumAdjustment; @endphp
                    <div class="v {{ $net >= 0 ? 'text-primary' : 'text-warning' }}">{{ number_format($net) }} <small class="text-muted font-weight-normal">تومان</small></div>
                </div>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-md-6 col-lg-4 mb-3">
                <div class="card acct-std-tile h-100">
                    <div class="card-body d-flex flex-column">
                        <h5 class="font-weight-bold mb-2">روزنامه و اسناد</h5>
                        <p class="small text-muted flex-grow-1">مشاهدهٔ تمام اسناد، ویرایش و حذف با همان سطح دسترسی قبلی.</p>
                        @admincan('accounting.journal.view')
                        <a href="{{ route('admin.accounting.journal.index') }}" class="btn btn-success rounded-pill mt-2">ورود به فهرست اسناد</a>
                        @else
                        <span class="text-muted small">دسترسی مشاهدهٔ اسناد برای شما فعال نیست.</span>
                        @endadmincan
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-4 mb-3">
                <div class="card acct-std-tile h-100">
                    <div class="card-body d-flex flex-column">
                        <h5 class="font-weight-bold mb-2">ثبت سند جدید</h5>
                        <p class="small text-muted flex-grow-1">درآمد، هزینه یا تعدیل با تاریخ شمسی، کد معین و طرف حساب.</p>
                        @admincan('accounting.journal.create')
                        <a href="{{ route('admin.accounting.journal.create') }}" class="btn btn-primary rounded-pill mt-2">ثبت سند</a>
                        @else
                        <span class="text-muted small">دسترسی ثبت برای شما فعال نیست.</span>
                        @endadmincan
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-4 mb-3">
                <div class="card acct-std-tile h-100">
                    <div class="card-body d-flex flex-column">
                        <h5 class="font-weight-bold mb-2">تراز سادهٔ ثبت‌ها</h5>
                        <p class="small text-muted flex-grow-1 mb-2">جمع تعدیل ثبت‌شده: <strong>{{ number_format($sumAdjustment) }}</strong> تومان</p>
                        <p class="small text-muted mb-0">برای گزارش ترکیبی با فاکتورها از «برنامهٔ جامع حسابداری» استفاده کنید.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white font-weight-bold">آخرین اسناد ثبت‌شده</div>
            <div class="table-responsive">
                <table class="table table-hover table-sm mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th>#</th>
                            <th>تاریخ (شمسی)</th>
                            <th>عنوان</th>
                            <th>نوع</th>
                            <th class="text-left" dir="ltr">مبلغ</th>
                            <th>معین</th>
                            <th>طرف حساب</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recent as $e)
                            <tr>
                                <td>{{ $e->id }}</td>
                                <td class="text-monospace" dir="ltr">{{ \App\Support\JalaliCalendar::formatShamsiDate($e->document_date) }}</td>
                                <td>{{ $e->title }}</td>
                                <td><span class="badge badge-light border">{{ \App\Models\AccountingJournalEntry::kindLabel($e->kind) }}</span></td>
                                <td class="text-left font-weight-bold" dir="ltr">{{ number_format((float) $e->amount) }}</td>
                                <td class="small text-monospace" dir="ltr">{{ $e->subsidiary_code ?? '—' }}</td>
                                <td class="small">{{ $e->counterparty ?? '—' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="text-center text-muted py-4">هنوز سندی ثبت نشده است.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
