@extends('backend.views.view')

@php
    use App\Models\MarketingSale;
    use App\Models\Order;
    use App\Support\JalaliCalendar;
@endphp

@section('main')
<div class="main-content">
    <div class="container">
        <div class="page-header d-flex flex-wrap justify-content-between align-items-center gap-2">
            <div>
                <h4>همکار: {{ $colleague->full_name }}</h4>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ admin_home_url() }}">خانه</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.colleagues.index') }}">همکاران</a></li>
                        <li class="breadcrumb-item active">{{ $colleague->full_name }}</li>
                    </ol>
                </nav>
            </div>
            <div>
                <a href="{{ route('admin.colleagues.edit', $colleague) }}" class="btn btn-outline-secondary">ویرایش</a>
                <a href="{{ route('admin.colleagues.index') }}" class="btn btn-light">بازگشت به لیست</a>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-4 mb-3">
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-header bg-white font-weight-bold">اطلاعات همکار</div>
                    <div class="card-body small">
                        <div><strong>نام:</strong> {{ $colleague->full_name }}</div>
                        <div class="mt-1"><strong>موبایل:</strong> <span class="text-monospace" dir="ltr">{{ $colleague->phone }}</span></div>
                        <div class="mt-1"><strong>سمت:</strong> {{ $colleague->position ?: '—' }}</div>
                        <div class="mt-1"><strong>فروشگاه / محل:</strong> {{ $colleague->store_name ?: '—' }}</div>
                        <div class="mt-1"><strong>استان:</strong> {{ $colleague->province?->name ?? '—' }}</div>
                        <div class="mt-1"><strong>شهر:</strong> {{ $colleague->city?->name ?? '—' }}</div>
                        <div class="mt-1"><strong>کد پستی:</strong> {{ $colleague->postal_code ?: '—' }}</div>
                        <div class="mt-1"><strong>آدرس:</strong> {{ $colleague->address ?: '—' }}</div>
                        <hr>
                        <div>
                            <strong>وضعیت: </strong>
                            @if($colleague->is_active)
                                <span class="badge badge-success">فعال</span>
                            @else
                                <span class="badge badge-warning text-dark">غیرفعال</span>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-around text-center">
                            <div>
                                <div class="font-weight-bold text-primary h4 mb-0">{{ number_format($sales->count()) }}</div>
                                <div class="small text-muted">پیش‌فاکتور</div>
                            </div>
                            <div class="border-left"></div>
                            <div>
                                <div class="font-weight-bold text-success h4 mb-0">{{ number_format($orders->count()) }}</div>
                                <div class="small text-muted">فاکتور ثبت‌شده</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-8 mb-3">
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-header bg-white font-weight-bold d-flex justify-content-between align-items-center">
                        <span>پیش‌فاکتورهای خرید کلی</span>
                        <span class="badge badge-light text-muted">{{ $sales->count() }}</span>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="thead-light">
                                <tr>
                                    <th>#</th>
                                    <th>تاریخ</th>
                                    <th>اقلام</th>
                                    <th>جمع (تومان)</th>
                                    <th>وضعیت</th>
                                    <th>فاکتور</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($sales as $s)
                                    @php $sum = $s->items->sum('unit_price'); @endphp
                                    <tr>
                                        <td class="text-muted">{{ $s->id }}</td>
                                        <td>{{ $s->sale_date ? JalaliCalendar::formatShamsiDate($s->sale_date) : '—' }}</td>
                                        <td class="small">{{ $s->items->count() }} قلم</td>
                                        <td class="font-weight-600">{{ number_format($sum) }}</td>
                                        <td>
                                            @if($s->isPending())
                                                <span class="badge badge-warning">در انتظار حسابداری</span>
                                            @elseif($s->status === MarketingSale::STATUS_APPROVED)
                                                <span class="badge badge-success">تأیید شده</span>
                                            @else
                                                <span class="badge badge-secondary">رد شده</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($s->status === MarketingSale::STATUS_APPROVED && $s->order)
                                                @if(auth('admin')->user()->hasPermission('orders.view'))
                                                    <a href="{{ route('admin.orders.show', $s->order) }}" class="btn btn-sm btn-outline-primary text-nowrap">{{ $s->order->order_number }}</a>
                                                @else
                                                    <span class="text-monospace small">{{ $s->order->order_number }}</span>
                                                @endif
                                            @else
                                                <span class="text-muted small">—</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="6" class="text-center text-muted py-4">پیش‌فاکتوری ثبت نشده است.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white font-weight-bold d-flex justify-content-between align-items-center">
                        <span>فاکتورهای ثبت‌شده (خرید کلی)</span>
                        <span class="badge bg-light text-muted">{{ $orders->count() }}</span>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="thead-light">
                                <tr>
                                    <th>شماره فاکتور</th>
                                    <th>تاریخ</th>
                                    <th>مبلغ نهایی (تومان)</th>
                                    <th>وضعیت ارسال</th>
                                    <th>اقلام</th>
                                    <th>عملیات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($orders as $order)
                                    <tr>
                                        <td class="text-monospace" dir="ltr">{{ $order->order_number }}</td>
                                        <td>{{ $order->payment_date ? JalaliCalendar::formatShamsiDate($order->payment_date) : '—' }}</td>
                                        <td class="font-weight-600">{{ number_format((float) $order->final_amount) }}</td>
                                        <td>
                                            <span class="badge {{ $order->shipping_status === Order::STATUS_CANCELLED ? 'badge-danger' : ($order->shipping_status === Order::STATUS_COMPLETED ? 'badge-success' : 'badge-info') }}">
                                                {{ Order::shippingStatusLabel($order->shipping_status) }}
                                            </span>
                                        </td>
                                        <td>{{ $order->items->count() }} قلم</td>
                                        <td>
                                            @if(auth('admin')->user()->hasPermission('orders.view'))
                                                <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-sm btn-outline-primary">جزئیات</a>
                                            @else
                                                <span class="text-muted small">—</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="6" class="text-center text-muted py-4">هنوز فاکتوری برای این همکار صادر نشده است.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
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