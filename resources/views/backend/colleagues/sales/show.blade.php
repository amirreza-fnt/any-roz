@extends('backend.views.view')

@section('main')
<div class="main-content">
    <div class="container">
        <div class="page-header d-flex flex-wrap justify-content-between align-items-center gap-2">
            <div>
                <h4>جزئیات پیش‌فاکتور #{{ $sale->id }}</h4>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ admin_home_url() }}">خانه</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.colleague.sales.index') }}">پیش‌فاکتورهای من</a></li>
                        <li class="breadcrumb-item active">#{{ $sale->id }}</li>
                    </ol>
                </nav>
            </div>
            <a href="{{ route('admin.colleague.sales.index') }}" class="btn btn-light">بازگشت به لیست</a>
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
                                    @if(auth('admin')->user()->hasPermission('orders.view'))
                                        <a href="{{ route('admin.orders.show', $sale->order_id) }}" class="btn btn-sm btn-outline-primary mr-2">مشاهدهٔ فاکتور</a>
                                    @else
                                        <span class="text-muted small">فاکتور: {{ $sale->order?->order_number }}</span>
                                    @endif
                                @endif
                            @else
                                <span class="badge badge-secondary">رد شده</span>
                            @endif
                        </div>
                        <div class="mb-2"><strong>تاریخ خرید (شمسی):</strong> {{ $sale->sale_date ? \App\Support\JalaliCalendar::formatShamsiDate($sale->sale_date) : '—' }}</div>
                        <div class="mb-2"><strong>روش پرداخت:</strong> {{ $sale->payment_method ?: '—' }}</div>
                        <div class="mb-0"><strong>یادداشت:</strong> {{ $sale->notes ?: '—' }}</div>
                    </div>
                </div>
                <div class="card border-0 shadow-sm mt-3">
                    <div class="card-header bg-white font-weight-bold">ردیف‌های خرید</div>
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
                            <tfoot>
                                <tr class="bg-light">
                                    <td class="text-left font-weight-bold">جمع</td>
                                    <td colspan="2" class="font-weight-bold">{{ number_format($sale->items->sum('unit_price')) }} تومان</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 mb-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white font-weight-bold">خریدار (همکار)</div>
                    <div class="card-body small">
                        <div><strong>نام:</strong> {{ $sale->buyer_first_name }} {{ $sale->buyer_last_name }}</div>
                        <div class="text-monospace mt-1" dir="ltr"><strong>تماس:</strong> {{ $sale->buyer_phone }}</div>
                        <div class="mt-1"><strong>فروشگاه / محل:</strong> {{ $sale->buyer_store_name ?: '—' }}</div>
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