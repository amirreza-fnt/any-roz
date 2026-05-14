@extends('backend.views.view')

@section('main')
<div class="main-content">
    <div class="container">
        <div class="page-header d-flex flex-wrap justify-content-between align-items-center gap-2">
            <div>
                <h4>جزئیات فروش #{{ $sale->id }}</h4>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard.marketing') }}">بازاریابی</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.marketing.sales.index') }}">فروش‌ها</a></li>
                        <li class="breadcrumb-item active">#{{ $sale->id }}</li>
                    </ol>
                </nav>
            </div>
            <a href="{{ route('admin.marketing.sales.index') }}" class="btn btn-light">بازگشت</a>
        </div>

        <div class="row">
            <div class="col-lg-8 mb-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white font-weight-bold">خلاصه</div>
                    <div class="card-body">
                        <div class="mb-2"><strong>وضعیت:</strong>
                            @if($sale->status === \App\Models\MarketingSale::STATUS_PENDING)
                                <span class="badge badge-warning">در انتظار حسابداری</span>
                            @elseif($sale->status === \App\Models\MarketingSale::STATUS_APPROVED)
                                <span class="badge badge-success">تأیید شده</span>
                                @if($sale->order_id)
                                    <a href="{{ route('admin.orders.show', $sale->order_id) }}" class="btn btn-sm btn-outline-primary mr-2">مشاهدهٔ فاکتور</a>
                                @endif
                            @else
                                <span class="badge badge-secondary">رد شده</span>
                            @endif
                        </div>
                        <div class="mb-2"><strong>تاریخ فروش (شمسی):</strong> {{ $sale->sale_date ? \App\Support\JalaliCalendar::formatShamsiDate($sale->sale_date) : '—' }}</div>
                        <div class="mb-2"><strong>روش پرداخت:</strong> {{ $sale->payment_method ?: '—' }}</div>
                        <div class="mb-0"><strong>یادداشت:</strong> {{ $sale->notes ?: '—' }}</div>
                    </div>
                </div>
                <div class="card border-0 shadow-sm mt-3">
                    <div class="card-header bg-white font-weight-bold">ردیف‌های فروش</div>
                    <div class="table-responsive">
                        <table class="table mb-0">
                            <thead><tr><th>محصول</th><th>مبلغ (تومان)</th><th>مقدار / شرح</th></tr></thead>
                            <tbody>
                                @foreach ($sale->items as $it)
                                    <tr>
                                        <td>{{ $it->product?->title ?? '—' }}</td>
                                        <td class="font-weight-600">{{ number_format($it->unit_price) }}</td>
                                        <td class="small">{{ $it->quantity_text }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 mb-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white font-weight-bold">خریدار (ثبت‌شده)</div>
                    <div class="card-body small">
                        <div><strong>نام:</strong> {{ $sale->buyer_first_name }} {{ $sale->buyer_last_name }}</div>
                        <div class="text-monospace mt-1" dir="ltr"><strong>تماس:</strong> {{ $sale->buyer_phone }}</div>
                        <div class="mt-1"><strong>مغازه:</strong> {{ $sale->buyer_store_name ?: '—' }}</div>
                        <hr>
                        <div><strong>CRM:</strong> <a href="{{ route('admin.marketing.buyers.edit', $sale->buyer_id) }}">پروفایل خریدار #{{ $sale->buyer_id }}</a></div>
                    </div>
                </div>
                @if($sale->accountant_note)
                    <div class="card border-0 shadow-sm mt-3">
                        <div class="card-header bg-white font-weight-bold">یادداشت حسابدار</div>
                        <div class="card-body small">{{ $sale->accountant_note }}</div>
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
