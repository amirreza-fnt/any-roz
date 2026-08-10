@extends('backend.views.view')

@php
    use App\Support\JalaliCalendar;
@endphp

@section('main')
<div class="main-content">
    <div class="container">
        <div class="page-header d-flex flex-wrap justify-content-between align-items-center gap-2">
            <div>
                <h4>بررسی خرید کلی #{{ $sale->id }}</h4>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ admin_home_url() }}">خانه</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard.accounting') }}">حسابداری</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.accounting.colleague-wholesale.index') }}">خرید کلی همکاران</a></li>
                        <li class="breadcrumb-item active">#{{ $sale->id }}</li>
                    </ol>
                </nav>
            </div>
            <a href="{{ route('admin.accounting.colleague-wholesale.index') }}" class="btn btn-light">بازگشت به لیست</a>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger border-0 shadow-sm mb-3">
                <div class="font-weight-bold mb-2">لطفاً خطاهای زیر را اصلاح کنید:</div>
                <ul class="mb-0 pr-3 small">
                    @foreach ($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if ($sale->isPending())
            <div class="alert alert-warning border-0 shadow-sm">
                این پیش‌فاکتور در انتظار تصمیم شماست. با تأیید، فاکتور رسمی ایجاد و <strong>به‌طور خودکار به تأمین ارسال</strong> می‌شود.
            </div>
        @elseif($sale->status === \App\Models\MarketingSale::STATUS_APPROVED)
            <div class="alert alert-success border-0 shadow-sm">
                این خرید کلی تأیید شده است. در صورت نیاز می‌توانید آن را <strong>رد</strong> کنید؛ فاکتور مرتبط لغو می‌شود و می‌توان دوباره از وضعیت «رد شده» به «تأیید» برگرداند.
            </div>
        @else
            <div class="alert alert-secondary border-0 shadow-sm">
                این خرید کلی رد شده است. در صورت اصلاح، می‌توانید دوباره <strong>تأیید و فاکتور</strong> بزنید.
            </div>
        @endif

        @if (session('approved_order_id'))
            <div class="alert alert-success border-0 shadow-sm d-flex flex-wrap justify-content-between align-items-center gap-2">
                <span>فاکتور با شمارهٔ <strong class="text-monospace" dir="ltr">{{ session('approved_order_number') }}</strong> ایجاد شد.</span>
                @if(auth('admin')->user()->hasPermission('orders.view'))
                    <a href="{{ route('admin.orders.show', session('approved_order_id')) }}" class="btn btn-sm btn-outline-success">مشاهدهٔ فاکتور</a>
                @endif
            </div>
        @endif

        <div class="row">
            <div class="col-lg-7 mb-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white font-weight-bold">اطلاعات خرید کلی</div>
                    <div class="card-body">
                        <div class="row small">
                            <div class="col-md-6 mb-2"><strong>همکار:</strong> {{ $sale->colleague?->full_name ?? ('#'.$sale->marketer_id) }}</div>
                            <div class="col-md-6 mb-2"><strong>تاریخ خرید:</strong> {{ $sale->sale_date ? JalaliCalendar::formatShamsiDate($sale->sale_date) : '—' }}</div>
                            <div class="col-md-6 mb-2"><strong>روش پرداخت:</strong> {{ $sale->payment_method ?: '—' }}</div>
                            <div class="col-md-6 mb-2"><strong>وضعیت:</strong> {{ \App\Models\MarketingSale::statusLabel($sale->status) }}</div>
                        </div>
                        <hr>
                        <div><strong>یادداشت همکار:</strong></div>
                        <div class="text-muted small">{{ $sale->notes ?: '—' }}</div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm mt-3">
                    <div class="card-header bg-white font-weight-bold">اقلام</div>
                    <div class="table-responsive">
                        <table class="table mb-0">
                            <thead><tr><th>محصول</th><th>کد</th><th>مبلغ (تومان)</th><th>مقدار / شرح</th></tr></thead>
                            <tbody>
                                @foreach ($sale->items as $it)
                                    <tr>
                                        <td>{{ $it->product?->title }}</td>
                                        <td class="text-monospace small">{{ $it->product?->tracking_code }}</td>
                                        <td class="font-weight-bold">{{ number_format($it->unit_price) }}</td>
                                        <td class="small">{{ $it->quantity_text }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="bg-light">
                                    <td colspan="2" class="text-left font-weight-bold">جمع</td>
                                    <td colspan="2" class="font-weight-bold">{{ number_format($sale->items->sum('unit_price')) }} تومان</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-lg-5 mb-3">
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-header bg-white font-weight-bold">فروشگاه / محل (طرف فاکتور)</div>
                    <div class="card-body small">
                        <div><strong>نام:</strong> {{ $sale->buyer_first_name }} {{ $sale->buyer_last_name }}</div>
                        <div class="text-monospace mt-1" dir="ltr"><strong>تماس:</strong> {{ $sale->buyer_phone }}</div>
                        <div class="mt-1"><strong>فروشگاه / محل:</strong> {{ $sale->buyer_store_name ?: '—' }}</div>
                        <hr>
                        <div><strong>پروفایل همکار:</strong>
                            @if(auth('admin')->user()->hasPermission('colleagues.view'))
                                <a href="{{ route('admin.colleagues.show', $sale->marketer_id) }}">همکار #{{ $sale->marketer_id }}</a>
                            @else
                                <span>#{{ $sale->marketer_id }}</span>
                            @endif
                        </div>
                        @if($sale->colleague)
                            <div class="mt-2 text-muted">
                                <div>استان: {{ $sale->colleague->province?->name ?? '—' }}</div>
                                <div>شهر: {{ $sale->colleague->city?->name ?? '—' }}</div>
                                <div class="mt-1">آدرس: {{ $sale->colleague->address ?: '—' }}</div>
                            </div>
                        @endif
                    </div>
                </div>

                @if($sale->status === \App\Models\MarketingSale::STATUS_APPROVED && $sale->order_id)
                    <div class="card border-0 shadow-sm border-success mb-3">
                        <div class="card-body">
                            <div class="font-weight-bold text-success mb-2">فاکتور ایجاد شد</div>
                            <a href="{{ route('admin.orders.show', $sale->order_id) }}" class="btn btn-success btn-block">مشاهدهٔ فاکتور</a>
                        </div>
                    </div>
                @endif

                @if($sale->canApprove())
                    <div class="card border-0 shadow-sm mb-3">
                        <div class="card-header bg-success text-white font-weight-bold">تأیید و صدور فاکتور</div>
                        <div class="card-body">
                            <form action="{{ route('admin.accounting.colleague-wholesale.approve', ['marketing_sale' => $sale->id]) }}" method="post">
                                @csrf
                                <div class="form-group">
                                    <label class="small text-muted">یادداشت حسابدار (اختیاری)</label>
                                    <textarea name="accountant_note" class="form-control @error('accountant_note') is-invalid @enderror" rows="2" maxlength="2000">{{ old('accountant_note') }}</textarea>
                                    @error('accountant_note')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                </div>
                                <button type="submit" class="btn btn-success btn-block font-weight-bold">تأیید و ایجاد فاکتور</button>
                            </form>
                        </div>
                    </div>
                @endif

                @if($sale->canReject())
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-danger text-white font-weight-bold">رد خرید کلی</div>
                        <div class="card-body">
                            <form action="{{ route('admin.accounting.colleague-wholesale.reject', ['marketing_sale' => $sale->id]) }}" method="post" onsubmit="return confirm('رد شود؟ در صورت وجود فاکتور، وضعیت آن به «لغو شده» تغییر می‌کند.');">
                                @csrf
                                <div class="form-group">
                                    <label class="small text-muted">دلیل / یادداشت</label>
                                    <textarea name="accountant_note" class="form-control @error('accountant_note') is-invalid @enderror" rows="2" maxlength="2000">{{ old('accountant_note') }}</textarea>
                                    @error('accountant_note')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                </div>
                                <button type="submit" class="btn btn-outline-danger btn-block">رد</button>
                            </form>
                        </div>
                    </div>
                @endif

                @if($sale->accountant_note)
                    <div class="card border-0 shadow-sm mt-3">
                        <div class="card-header bg-white font-weight-bold">یادداشت حسابدار</div>
                        <div class="card-body small">{{ $sale->accountant_note }}</div>
                    </div>
                @endif

                @if($sale->reviewed_at)
                    <div class="small text-muted mt-2">زمان بررسی: {{ JalaliCalendar::formatShamsiDateTime($sale->reviewed_at) }}
                        @if($sale->reviewer) — {{ $sale->reviewer->full_name }} @endif
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>if (typeof feather !== 'undefined') feather.replace();</script>
@endpush