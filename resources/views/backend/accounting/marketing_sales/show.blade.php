@extends('backend.views.view')

@section('main')
<div class="main-content">
    <div class="container">
        <div class="page-header d-flex flex-wrap justify-content-between align-items-center gap-2">
            <div>
                <h4>بررسی فروش #{{ $sale->id }}</h4>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard.accounting') }}">حسابداری</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.accounting.marketing-sales.index') }}">فروش بازاریابی</a></li>
                        <li class="breadcrumb-item active">#{{ $sale->id }}</li>
                    </ol>
                </nav>
            </div>
            <a href="{{ route('admin.accounting.marketing-sales.index') }}" class="btn btn-light">بازگشت به لیست</a>
        </div>

        @if ($sale->isPending())
            <div class="alert alert-warning border-0 shadow-sm">
                این فروش در انتظار تصمیم شماست. با تأیید، فاکتور رسمی ایجاد و <strong>به‌طور خودکار به بخش تأمین ارسال</strong> می‌شود.
            </div>
        @endif

        <div class="row">
            <div class="col-lg-7 mb-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white font-weight-bold">اطلاعات فروش</div>
                    <div class="card-body">
                        <div class="row small">
                            <div class="col-md-6 mb-2"><strong>بازاریاب:</strong> <span class="text-monospace" dir="ltr">#{{ $sale->marketer_id }}</span></div>
                            <div class="col-md-6 mb-2"><strong>تاریخ فروش:</strong> {{ $sale->sale_date ? \App\Support\JalaliCalendar::formatShamsiDate($sale->sale_date) : '—' }}</div>
                            <div class="col-md-6 mb-2"><strong>روش پرداخت:</strong> {{ $sale->payment_method ?: '—' }}</div>
                            <div class="col-md-6 mb-2"><strong>وضعیت:</strong> {{ \App\Models\MarketingSale::statusLabel($sale->status) }}</div>
                        </div>
                        <hr>
                        <div><strong>یادداشت بازاریاب:</strong></div>
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
                    <div class="card-header bg-white font-weight-bold">خریدار</div>
                    <div class="card-body small">
                        <div><strong>نام:</strong> {{ $sale->buyer_first_name }} {{ $sale->buyer_last_name }}</div>
                        <div class="text-monospace mt-1" dir="ltr"><strong>تماس:</strong> {{ $sale->buyer_phone }}</div>
                        <div class="mt-1"><strong>مغازه:</strong> {{ $sale->buyer_store_name ?: '—' }}</div>
                        <hr>
                        <div><strong>پروفایل CRM:</strong> <a href="{{ route('admin.marketing.buyers.edit', $sale->buyer_id) }}">خریدار #{{ $sale->buyer_id }}</a></div>
                        @if($sale->buyer)
                            <div class="mt-2 text-muted">
                                <div>استان: {{ $sale->buyer->province?->name ?? '—' }}</div>
                                <div>شهر: {{ $sale->buyer->city?->name ?? '—' }}</div>
                                <div class="mt-1">آدرس: {{ $sale->buyer->address ?: '—' }}</div>
                            </div>
                        @endif
                    </div>
                </div>

                @if($sale->status === \App\Models\MarketingSale::STATUS_APPROVED && $sale->order_id)
                    <div class="card border-0 shadow-sm border-success">
                        <div class="card-body">
                            <div class="font-weight-bold text-success mb-2">فاکتور ایجاد شد</div>
                            <a href="{{ route('admin.orders.show', $sale->order_id) }}" class="btn btn-success btn-block">مشاهدهٔ فاکتور</a>
                        </div>
                    </div>
                @endif

                @if($sale->isPending())
                    <div class="card border-0 shadow-sm mb-3">
                        <div class="card-header bg-success text-white font-weight-bold">تأیید و صدور فاکتور</div>
                        <div class="card-body">
                            <form action="{{ route('admin.accounting.marketing-sales.approve', $sale) }}" method="post">
                                @csrf
                                @method('PATCH')
                                <div class="form-group">
                                    <label class="small text-muted">یادداشت حسابدار (اختیاری)</label>
                                    <textarea name="accountant_note" class="form-control" rows="2" maxlength="2000"></textarea>
                                </div>
                                <button type="submit" class="btn btn-success btn-block font-weight-bold">تأیید و ایجاد فاکتور</button>
                            </form>
                        </div>
                    </div>
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-danger text-white font-weight-bold">رد فروش</div>
                        <div class="card-body">
                            <form action="{{ route('admin.accounting.marketing-sales.reject', $sale) }}" method="post" onsubmit="return confirm('فروش رد شود؟');">
                                @csrf
                                @method('PATCH')
                                <div class="form-group">
                                    <label class="small text-muted">دلیل / یادداشت</label>
                                    <textarea name="accountant_note" class="form-control" rows="2" maxlength="2000"></textarea>
                                </div>
                                <button type="submit" class="btn btn-outline-danger btn-block">رد</button>
                            </form>
                        </div>
                    </div>
                @endif

                @if($sale->accountant_note && ! $sale->isPending())
                    <div class="card border-0 shadow-sm mt-3">
                        <div class="card-header bg-white font-weight-bold">یادداشت حسابدار</div>
                        <div class="card-body small">{{ $sale->accountant_note }}</div>
                    </div>
                @endif

                @if($sale->reviewed_at)
                    <div class="small text-muted mt-2">زمان بررسی: {{ $sale->reviewed_at->format('Y/m/d H:i') }}
                        @if($sale->reviewer) — {{ $sale->reviewer->name }} @endif
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
