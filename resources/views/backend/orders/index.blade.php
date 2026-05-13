@extends('backend.views.view')

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/back-end/vendors/dataTable/dataTables.min.css') }}" type="text/css">
<style>
    .inv-badge { font-size: 0.72rem; font-weight: 600; padding: 0.28rem 0.55rem; border-radius: 999px; }
    .inv-pay-pending { background: rgba(245, 158, 11, 0.15); color: #b45309; }
    .inv-pay-paid { background: rgba(16, 185, 129, 0.12); color: #047857; }
    .inv-pay-failed { background: rgba(239, 68, 68, 0.12); color: #b91c1c; }
    .inv-pay-refunded { background: rgba(107, 114, 128, 0.15); color: #4b5563; }
    .inv-ship { font-size: 0.75rem; font-weight: 600; }
    .supply-pill { background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%); color: #fff; font-size: 0.68rem; padding: 0.25rem 0.5rem; border-radius: 6px; }
    .orders-toolbar .btn { border-radius: 10px; }
    #orders-table_wrapper .dataTables_filter input { margin-right: 0.5rem; border-radius: 8px; min-width: 220px; }
    .mini-form-status .form-control { min-width: 140px; }
</style>
@endpush

@section('main')

<div class="main-content">
    <div class="container">

        <div class="page-header">
            <h4>{{ $pageTitle }}</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">خانه</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ $pageTitle }}</li>
                </ol>
            </nav>
        </div>

        <div class="orders-toolbar card border-0 shadow-sm mb-3">
            <div class="card-body py-3 d-flex flex-wrap align-items-center justify-content-between gap-2">
                <div class="d-flex flex-wrap align-items-center gap-2">
                    <a href="{{ route('admin.orders.index') }}" class="btn btn-sm {{ $supplyMode ? 'btn-outline-primary' : 'btn-primary' }}">همهٔ فاکتورها</a>
                    <a href="{{ route('admin.orders.supply') }}" class="btn btn-sm {{ $supplyMode ? 'btn-primary' : 'btn-outline-primary' }}">ارسال‌شده به تأمین</a>
                </div>
                @if ($supplyMode)
                    <span class="supply-pill">فقط فاکتورهای ارسال‌شده به بخش تأمین</span>
                @else
                    <span class="text-muted small">فاکتورها پس از خرید کاربر اینجا نمایش داده می‌شوند؛ از اینجا وضعیت را مدیریت کنید یا به تأمین بفرستید.</span>
                @endif
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table id="orders-table" class="table table-striped table-bordered" style="width:100%">
                        <thead>
                            <tr>
                                <th>شماره سفارش</th>
                                <th>خریدار</th>
                                <th>مبلغ نهایی</th>
                                <th>پرداخت</th>
                                <th style="min-width:220px">وضعیت سفارش</th>
                                <th>کد رهگیری پست</th>
                                <th>تأمین</th>
                                <th>تاریخ</th>
                                <th style="min-width:120px">عملیات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($orders as $o)
                                <tr>
                                    <td class="align-middle font-weight-600 text-monospace">{{ $o->order_number }}</td>
                                    <td class="align-middle">
                                        <div class="font-weight-500">{{ $o->user?->name ?? '—' }}</div>
                                        <div class="small text-muted">{{ $o->user?->email }}</div>
                                    </td>
                                    <td class="align-middle">{{ number_format((float) $o->final_amount) }} <span class="text-muted small">تومان</span></td>
                                    <td class="align-middle">
                                        @php $pc = match($o->payment_status) { 'pending' => 'inv-pay-pending', 'paid' => 'inv-pay-paid', 'failed' => 'inv-pay-failed', 'refunded' => 'inv-pay-refunded', default => 'inv-pay-pending' }; @endphp
                                        <span class="inv-badge {{ $pc }}">{{ \App\Models\Order::paymentStatusLabel($o->payment_status) }}</span>
                                    </td>
                                    <td class="align-middle mini-form-status">
                                        <form action="{{ route('admin.orders.update-status', $o) }}" method="post" class="mb-1">
                                            @csrf
                                            @method('PATCH')
                                            <div class="d-flex flex-wrap align-items-center gap-1">
                                                <select name="shipping_status" class="form-control form-control-sm" title="وضعیت">
                                                    @foreach (\App\Models\Order::SHIPPING_STATUSES as $st)
                                                        <option value="{{ $st }}" @selected($o->shipping_status === $st)>{{ \App\Models\Order::shippingStatusLabel($st) }}</option>
                                                    @endforeach
                                                </select>
                                                <button type="submit" class="btn btn-sm btn-primary">ثبت</button>
                                            </div>
                                            <input type="text" name="shipping_tracking_code" value="{{ $o->shipping_tracking_code }}" class="form-control form-control-sm mt-1" placeholder="کد رهگیری پست">
                                            <input type="text" name="note" class="form-control form-control-sm mt-1" placeholder="یادداشت (اختیاری)">
                                        </form>
                                    </td>
                                    <td class="align-middle small text-monospace">{{ $o->shipping_tracking_code ?: '—' }}</td>
                                    <td class="align-middle text-center">
                                        @if ($o->sent_to_supply)
                                            <span class="badge badge-success">ارسال شد</span>
                                        @else
                                            <span class="badge badge-light border">خیر</span>
                                        @endif
                                    </td>
                                    <td class="align-middle small text-muted">{{ $o->created_at?->format('Y/m/d H:i') }}</td>
                                    <td class="align-middle text-nowrap">
                                        <a href="{{ route('admin.orders.show', $o) }}" class="btn btn-sm btn-outline-primary" title="جزئیات و چاپ">
                                            <i data-feather="file-text" class="width-16 height-16"></i>
                                        </a>
                                        @if (! $o->sent_to_supply)
                                            <form action="{{ route('admin.orders.send-to-supply', $o) }}" method="post" class="d-inline" onsubmit="return confirm('این فاکتور به بخش تأمین ارسال شود؟');">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn btn-sm btn-outline-secondary" title="ارسال به بخش تأمین">
                                                    <i data-feather="send" class="width-16 height-16"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center text-muted py-5">فاکتوری برای نمایش وجود ندارد.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="{{ asset('assets/back-end/vendors/dataTable/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('assets/back-end/vendors/dataTable/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('assets/back-end/vendors/dataTable/dataTables.responsive.min.js') }}"></script>
<script>
    $(function () {
        @if ($orders->isNotEmpty())
        $('#orders-table').DataTable({
            responsive: true,
            order: [[7, 'desc']],
            pageLength: 25,
            language: {
                search: 'جستجو:',
                lengthMenu: 'نمایش _MENU_ ردیف',
                info: 'نمایش _START_ تا _END_ از _TOTAL_ ردیف',
                infoEmpty: 'موردی برای نمایش وجود ندارد',
                zeroRecords: 'موردی یافت نشد',
                paginate: { previous: 'قبلی', next: 'بعدی' }
            },
            columnDefs: [
                { orderable: false, targets: [4, 8] }
            ],
            drawCallback: function () {
                if (typeof feather !== 'undefined') feather.replace();
            }
        });
        @else
        if (typeof feather !== 'undefined') feather.replace();
        @endif
    });
</script>
@endpush
