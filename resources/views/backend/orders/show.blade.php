@extends('backend.views.view')

@php
    use App\Support\JalaliCalendar;
    $hideFinancials = $hideFinancials ?? false;
    $listBackRoute = $listBackRoute ?? route('admin.orders.index');
    $payClass = match($order->payment_status) {
        'pending' => 'inv-pay-pending',
        'paid' => 'inv-pay-paid',
        'failed' => 'inv-pay-failed',
        'refunded' => 'inv-pay-refunded',
        default => 'inv-pay-pending',
    };
@endphp

@push('styles')
<style>
    :root {
        --inv-ink: #0f172a;
        --inv-muted: #64748b;
        --inv-line: #e2e8f0;
        --inv-accent: #4f46e5;
        --inv-soft: #f8fafc;
    }
    .inv-badge { font-size: 0.72rem; font-weight: 600; padding: 0.28rem 0.55rem; border-radius: 999px; }
    .inv-pay-pending { background: rgba(245, 158, 11, 0.15); color: #b45309; }
    .inv-pay-paid { background: rgba(16, 185, 129, 0.12); color: #047857; }
    .inv-pay-failed { background: rgba(239, 68, 68, 0.12); color: #b91c1c; }
    .inv-pay-refunded { background: rgba(107, 114, 128, 0.15); color: #4b5563; }
    .inv-toolbar {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 1rem;
    }
    .invoice-sheet {
        background: #fff;
        border-radius: 16px;
        border: 1px solid var(--inv-line);
        box-shadow: 0 18px 50px rgba(15, 23, 42, 0.06);
        overflow: hidden;
        max-width: 980px;
        margin: 0 auto;
    }
    .invoice-sheet.is-supply-view .supply-hide-print { display: none !important; }
    .invoice-hero {
        background: linear-gradient(120deg, #312e81 0%, #4f46e5 45%, #6366f1 100%);
        color: #fff;
        padding: 1.75rem 1.5rem;
        position: relative;
    }
    .invoice-hero::after {
        content: '';
        position: absolute;
        inset: 0;
        background: radial-gradient(circle at 20% 20%, rgba(255,255,255,.18), transparent 45%);
        pointer-events: none;
    }
    .invoice-hero h1 { font-size: 1.35rem; font-weight: 800; margin: 0; position: relative; z-index: 1; }
    .invoice-hero .sub { opacity: .9; font-size: .88rem; margin-top: .35rem; position: relative; z-index: 1; }
    .invoice-body { padding: 1.25rem 1.5rem 1.75rem; }
    .inv-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1rem; }
    .inv-card {
        border: 1px solid var(--inv-line);
        border-radius: 12px;
        padding: 1rem;
        background: var(--inv-soft);
    }
    .inv-card h3 { font-size: .78rem; text-transform: uppercase; letter-spacing: .04em; color: var(--inv-muted); margin: 0 0 .5rem; font-weight: 700; }
    .inv-kv { font-size: .92rem; color: var(--inv-ink); line-height: 1.55; }
    .inv-products-wrap { margin-top: 1.25rem; border: 1px solid var(--inv-line); border-radius: 12px; overflow: hidden; }
    .inv-products-head {
        display: none;
        grid-template-columns: 56px 1fr 90px 110px 110px 120px;
        gap: .5rem;
        padding: .65rem .75rem;
        background: #eef2ff;
        font-size: .72rem;
        font-weight: 700;
        color: #3730a3;
    }
    .invoice-sheet.is-supply-view .inv-products-head {
        grid-template-columns: 56px 1fr 80px;
    }
    @media (min-width: 768px) {
        .inv-products-head { display: grid; }
    }
    .inv-line {
        display: grid;
        grid-template-columns: 56px 1fr 90px 110px 110px 120px;
        gap: .5rem;
        padding: .75rem;
        align-items: center;
        border-top: 1px solid var(--inv-line);
        font-size: .86rem;
    }
    .invoice-sheet.is-supply-view .inv-line {
        grid-template-columns: 56px 1fr 80px;
    }
    .inv-line:nth-child(even) { background: #fafafa; }
    .inv-thumb {
        width: 48px; height: 48px; border-radius: 10px; object-fit: cover;
        border: 1px solid var(--inv-line); background: #f1f5f9;
    }
    .inv-totals { margin-top: 1rem; display: flex; justify-content: flex-end; }
    .inv-totals-inner { min-width: 280px; border: 1px solid var(--inv-line); border-radius: 12px; overflow: hidden; }
    .inv-totals-row { display: flex; justify-content: space-between; padding: .55rem .85rem; font-size: .88rem; border-bottom: 1px solid var(--inv-line); }
    .inv-totals-row:last-child { border-bottom: 0; font-weight: 800; font-size: 1rem; background: #eef2ff; color: #312e81; }
    .timeline { margin-top: 1.5rem; border-top: 1px dashed var(--inv-line); padding-top: 1rem; }
    .timeline-item { border-right: 3px solid var(--inv-accent); padding: .5rem 1rem .5rem 0; margin-bottom: .5rem; background: #fafafa; border-radius: 0 10px 10px 0; }
    @media (max-width: 768px) {
        .inv-products-head, .inv-line {
            grid-template-columns: 48px 1fr;
            grid-auto-rows: auto;
        }
        .inv-products-head { display: none; }
        .inv-line .cell-qty::before { content: 'تعداد: '; color: var(--inv-muted); font-size: .75rem; }
        .inv-line .cell-unit::before { content: 'قیمت واحد: '; color: var(--inv-muted); font-size: .75rem; display: block; }
        .inv-line .cell-disc::before { content: 'تخفیف: '; color: var(--inv-muted); font-size: .75rem; display: block; }
        .inv-line .cell-final::before { content: 'جمع سطر: '; color: var(--inv-muted); font-size: .75rem; display: block; }
    }
    @media print {
        .no-print { display: none !important; }
        .navigation, #main > .header, footer { display: none !important; }
        #main { margin: 0 !important; padding: 0 !important; }
        .main-content { padding: 0 !important; }
        .invoice-sheet { box-shadow: none !important; border: none !important; max-width: 100% !important; }
        .invoice-sheet.is-supply-view .supply-hide-print { display: none !important; }
        body { background: #fff !important; }
    }
</style>
@endpush

@section('main')

<div class="main-content">
    <div class="container">

        <div class="no-print page-header">
            <h4>فاکتور {{ $order->order_number }}</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">خانه</a></li>
                    <li class="breadcrumb-item"><a href="{{ $listBackRoute }}">فاکتورها</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ $order->order_number }}</li>
                </ol>
            </nav>
        </div>

        <div class="no-print inv-toolbar">
            <div class="d-flex flex-wrap gap-2 align-items-center">
                <a href="{{ $listBackRoute }}" class="btn btn-light">بازگشت به لیست</a>
                <button type="button" class="btn btn-primary" onclick="window.print()">
                    <i data-feather="printer" class="width-16 height-16"></i>
                    <span class="mr-1">چاپ فاکتور</span>
                </button>
            </div>
            <div class="d-flex flex-wrap gap-2 align-items-center">
                <form action="{{ route('admin.orders.toggle-supply', $order) }}" method="post" class="d-flex flex-wrap align-items-center gap-2" onsubmit="return confirm(@json($order->sent_to_supply ? 'این فاکتور از بخش تأمین خارج شود؟' : 'این فاکتور به بخش تأمین ارسال شود؟'));">
                    @csrf
                    @method('PATCH')
                    <input type="text" name="note" class="form-control form-control-sm" style="min-width:200px" placeholder="یادداشت (اختیاری)">
                    <button type="submit" class="btn btn-sm {{ $order->sent_to_supply ? 'btn-outline-warning' : 'btn-primary' }}">
                        {{ $order->sent_to_supply ? 'بازگشت از بخش تأمین' : 'ارسال به بخش تأمین' }}
                    </button>
                </form>
                @if ($order->sent_to_supply && $order->sent_to_supply_at)
                    <span class="badge badge-light border text-dark small">آخرین ارسال: {{ $order->sent_to_supply_at ? JalaliCalendar::formatShamsiDateTime($order->sent_to_supply_at) : '—' }}</span>
                @endif
            </div>
        </div>

        <div class="no-print card mb-3">
            <div class="card-body py-3">
                <h6 class="mb-2">تغییر وضعیت سفارش</h6>
                <form action="{{ route('admin.orders.update-status', $order) }}" method="post" class="form-row align-items-end">
                    @csrf
                    @method('PATCH')
                    <div class="form-group col-md-3">
                        <label class="small text-muted">وضعیت</label>
                        <select name="shipping_status" class="form-control">
                            @foreach (\App\Models\Order::SHIPPING_STATUSES as $st)
                                <option value="{{ $st }}" @selected($order->shipping_status === $st)>{{ \App\Models\Order::shippingStatusLabel($st) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-md-3">
                        <label class="small text-muted">کد رهگیری پست</label>
                        <input type="text" name="shipping_tracking_code" class="form-control" value="{{ $order->shipping_tracking_code }}" placeholder="اختیاری">
                    </div>
                    <div class="form-group col-md-4">
                        <label class="small text-muted">یادداشت</label>
                        <input type="text" name="note" class="form-control" placeholder="اختیاری">
                    </div>
                    <div class="form-group col-md-2">
                        <button type="submit" class="btn btn-primary btn-block">ثبت تغییرات</button>
                    </div>
                </form>
            </div>
        </div>

        <div id="invoice-print" class="invoice-sheet mb-5 {{ $hideFinancials ? 'is-supply-view' : '' }}">
            <div class="invoice-hero">
                <h1 class="d-flex flex-wrap align-items-center gap-2">
                    فاکتور فروش
                    @if (($order->source ?? \App\Models\Order::SOURCE_SITE) === \App\Models\Order::SOURCE_MARKETING)
                        <span class="badge badge-light text-primary border" style="font-size:0.7rem">فروش بازاریابی</span>
                    @endif
                </h1>
                <div class="sub d-flex flex-wrap gap-3">
                    <span>شماره سفارش: <strong>{{ $order->order_number }}</strong></span>
                    <span>تاریخ: <strong>{{ JalaliCalendar::formatShamsiDateTime($order->created_at) }}</strong></span>
                    <span>وضعیت: <strong>{{ \App\Models\Order::shippingStatusLabel($order->shipping_status) }}</strong></span>
                </div>
            </div>
            <div class="invoice-body">
                <div class="inv-grid">
                    <div class="inv-card">
                        <h3>خریدار</h3>
                        <div class="inv-kv">
                            @if (($order->source ?? \App\Models\Order::SOURCE_SITE) === \App\Models\Order::SOURCE_MARKETING)
                                <div><strong>{{ $order->shipping_recipient_name }}</strong></div>
                                <div class="text-muted small text-monospace" dir="ltr">{{ $order->shipping_phone }}</div>
                                <div class="text-muted small mt-1">ثبت‌شده از پنل بازاریابی @if($order->marketer_id) — بازاریاب #{{ $order->marketer_id }} @endif</div>
                            @else
                                <div><strong>{{ $order->user?->name ?? '—' }}</strong></div>
                                <div class="text-muted small">{{ $order->user?->email }}</div>
                                @if ($order->user?->mobile)
                                    <div class="text-muted small text-monospace" dir="ltr">{{ $order->user->mobile }}</div>
                                @endif
                            @endif
                        </div>
                    </div>
                    <div class="inv-card">
                        <h3>پرداخت</h3>
                        <div class="inv-kv">
                            <div><span class="inv-badge {{ $payClass }}">{{ \App\Models\Order::paymentStatusLabel($order->payment_status) }}</span></div>
                            @if ($order->payment_method)
                                <div class="mt-1 small text-muted">روش: {{ $order->payment_method }}</div>
                            @endif
                            @if ($order->payment_transaction_id)
                                <div class="small text-monospace">تراکنش: {{ $order->payment_transaction_id }}</div>
                            @endif
                            @if ($order->payment_date)
                                <div class="small text-muted">زمان پرداخت: {{ JalaliCalendar::formatShamsiDateTime($order->payment_date) }}</div>
                            @endif
                        </div>
                    </div>
                    <div class="inv-card supply-hide-print">
                        <h3>جمع مالی</h3>
                        <div class="inv-kv">
                            <div>جمع کالا: {{ number_format((float) $order->total_amount) }} تومان</div>
                            <div class="small text-muted">هزینه ارسال (کارمزد): {{ number_format((float) $order->shipping_fee) }}</div>
                            <div class="small text-muted">تخفیف سفارش: {{ number_format((float) $order->discount_amount) }}</div>
                            <div class="small text-muted">هزینه حمل: {{ number_format((float) $order->shipping_cost) }} | بیمه: {{ number_format((float) $order->insurance_cost) }}</div>
                            <div class="mt-1 font-weight-bold text-primary">مبلغ نهایی: {{ number_format((float) $order->final_amount) }} تومان</div>
                            <div class="small text-muted">وزن کل: {{ number_format((float) $order->total_weight, 2) }} کیلو</div>
                        </div>
                    </div>
                </div>

                <div class="inv-card mt-3">
                    <h3>ارسال و گیرنده</h3>
                    <div class="inv-kv">
                        <div class="mb-1"><strong>روش ارسال:</strong> {{ $order->shipping_method ?: '—' }}</div>
                        <div class="mb-1"><strong>گیرنده:</strong> {{ $order->shipping_recipient_name }} — {{ $order->shipping_phone }}</div>
                        <div class="mb-1"><strong>آدرس:</strong> {{ $order->shipping_state }}، {{ $order->shipping_city }}، کدپستی {{ $order->shipping_postal_code }}</div>
                        <div><strong>نشانی کامل:</strong> {{ $order->shipping_address }}</div>
                        @if ($order->shipping_tracking_code)
                            <div class="mt-2 small text-monospace"><strong>کد رهگیری پست:</strong> {{ $order->shipping_tracking_code }}</div>
                        @endif
                    </div>
                </div>

                @if ($order->notes)
                    <div class="inv-card mt-3">
                        <h3>یادداشت سفارش</h3>
                        <div class="inv-kv">{{ $order->notes }}</div>
                    </div>
                @endif

                <div class="inv-products-wrap">
                    <div class="inv-products-head">
                        <span></span>
                        <span>کالا</span>
                        <span class="text-center">تعداد</span>
                        <span class="text-left supply-hide-print">قیمت واحد</span>
                        <span class="text-left supply-hide-print">تخفیف</span>
                        <span class="text-left supply-hide-print">جمع سطر</span>
                    </div>
                    @foreach ($order->items as $line)
                        <div class="inv-line">
                            <div>
                                @if ($line->image_url)
                                    <img src="{{ $line->image_url }}" alt="" class="inv-thumb">
                                @else
                                    <div class="inv-thumb d-flex align-items-center justify-content-center text-muted">
                                        <i data-feather="image" class="width-18 height-18"></i>
                                    </div>
                                @endif
                            </div>
                            <div>
                                <div class="font-weight-600">{{ $line->product_name }}</div>
                                @if ($line->product_code)
                                    <div class="small text-muted text-monospace">کد: {{ $line->product_code }}</div>
                                @endif
                                @if (! empty($line->product_options['quantity_text']))
                                    <div class="small text-info mt-1">مقدار / شرح فروش: {{ $line->product_options['quantity_text'] }}</div>
                                @endif
                                @if ($line->product_options && count($line->product_options))
                                    <details class="small mt-1"><summary>ویژگی‌ها</summary><pre class="mb-0 mt-1 small bg-light p-2 rounded">{{ json_encode($line->product_options, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) }}</pre></details>
                                @endif
                            </div>
                            <div class="cell-qty text-center font-weight-600">{{ number_format($line->quantity) }}</div>
                            <div class="cell-unit supply-hide-print">{{ $line->unit_price }}</div>
                            <div class="cell-disc small supply-hide-print">{{ $line->discount_percent }}٪ — {{ number_format($line->discount_amount) }}</div>
                            <div class="cell-final font-weight-bold supply-hide-print">{{ number_format($line->final_price) }} <span class="text-muted small font-weight-normal">تومان</span></div>
                        </div>
                    @endforeach
                </div>

                <div class="inv-totals supply-hide-print">
                    <div class="inv-totals-inner">
                        <div class="inv-totals-row"><span>جمع کالاها</span><span>{{ number_format((float) $order->total_amount) }}</span></div>
                        <div class="inv-totals-row"><span>هزینه ارسال (کارمزد)</span><span>{{ number_format((float) $order->shipping_fee) }}</span></div>
                        <div class="inv-totals-row"><span>تخفیف</span><span>{{ number_format((float) $order->discount_amount) }}</span></div>
                        <div class="inv-totals-row"><span>حمل و بیمه</span><span>{{ number_format((float) $order->shipping_cost + (float) $order->insurance_cost) }}</span></div>
                        <div class="inv-totals-row"><span>مبلغ نهایی</span><span>{{ number_format((float) $order->final_amount) }} تومان</span></div>
                    </div>
                </div>

                @if ($order->histories->isNotEmpty())
                    <div class="timeline mt-3">
                        <h6 class="font-weight-bold mb-2">تاریخچه رویدادها</h6>
                        @foreach ($order->histories as $h)
                            <div class="timeline-item">
                                <div class="small text-muted">{{ JalaliCalendar::formatShamsiDateTime($h->created_at) }} @if($h->admin) — {{ $h->admin->full_name }} @elseif($h->user) — {{ $h->user->name }} @endif</div>
                                <div class="font-weight-600">{{ $h->status }}</div>
                                @if ($h->note)
                                    <div class="small mt-1" style="white-space:pre-wrap;">{{ $h->note }}</div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        if (typeof feather !== 'undefined') feather.replace();
    });
</script>
@endpush
