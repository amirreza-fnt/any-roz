@extends('backend.views.view')

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/back-end/vendors/dataTable/dataTables.min.css') }}" type="text/css">
<style>
    .msq-tabs .nav-link { border-radius: 10px 10px 0 0; font-weight: 600; }
    .msq-tabs .nav-link.active { background: linear-gradient(90deg,#1e40af,#3b82f6); color: #fff !important; }
    .msq-pill { font-size: .72rem; font-weight: 700; padding: .25rem .5rem; border-radius: 999px; }
</style>
@endpush

@section('main')
<div class="main-content">
    <div class="container">
        <div class="page-header">
            <h4>بررسی فروش بازاریابان</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard.accounting') }}">حسابداری</a></li>
                    <li class="breadcrumb-item active">فروش بازاریابی</li>
                </ol>
            </nav>
        </div>

        <ul class="nav nav-tabs msq-tabs mb-3 border-0">
            <li class="nav-item">
                <a class="nav-link {{ $tab === 'pending' ? 'active' : '' }} text-dark" href="{{ route('admin.accounting.marketing-sales.index', ['tab' => 'pending']) }}">
                    در انتظار <span class="badge badge-light text-dark ml-1">{{ $counts['pending'] }}</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ $tab === 'approved' ? 'active' : '' }} text-dark" href="{{ route('admin.accounting.marketing-sales.index', ['tab' => 'approved']) }}">
                    تأیید شده <span class="badge badge-light text-dark ml-1">{{ $counts['approved'] }}</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ $tab === 'rejected' ? 'active' : '' }} text-dark" href="{{ route('admin.accounting.marketing-sales.index', ['tab' => 'rejected']) }}">
                    رد شده <span class="badge badge-light text-dark ml-1">{{ $counts['rejected'] }}</span>
                </a>
            </li>
        </ul>

        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table id="acct-ms-table" class="table table-striped table-bordered mb-0" style="width:100%">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>خریدار</th>
                                <th>بازاریاب</th>
                                <th>تاریخ فروش</th>
                                <th>جمع</th>
                                <th>وضعیت</th>
                                <th>عملیات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($sales as $s)
                                @php $sum = $s->items->sum('unit_price'); @endphp
                                <tr>
                                    <td>{{ $s->id }}</td>
                                    <td>
                                        <div class="font-weight-600">{{ $s->buyer_first_name }} {{ $s->buyer_last_name }}</div>
                                        <div class="small text-muted text-monospace" dir="ltr">{{ $s->buyer_phone }}</div>
                                    </td>
                                    <td><span class="text-monospace" dir="ltr">#{{ $s->marketer_id }}</span></td>
                                    <td>{{ $s->sale_date ? \App\Support\JalaliCalendar::formatShamsiDate($s->sale_date) : '—' }}</td>
                                    <td>{{ number_format($sum) }}</td>
                                    <td>
                                        @if($s->status === \App\Models\MarketingSale::STATUS_PENDING)
                                            <span class="msq-pill bg-warning text-dark">در انتظار</span>
                                        @elseif($s->status === \App\Models\MarketingSale::STATUS_APPROVED)
                                            <span class="msq-pill bg-success text-white">تأیید</span>
                                        @else
                                            <span class="msq-pill bg-secondary text-white">رد</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.accounting.marketing-sales.show', $s) }}" class="btn btn-sm btn-outline-primary">بررسی</a>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="7" class="text-center text-muted py-5">موردی در این تب وجود ندارد.</td></tr>
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
<script>
$(function () {
    @if ($sales->isNotEmpty())
    $('#acct-ms-table').DataTable({
        responsive: true,
        order: [[0, 'desc']],
        pageLength: 25,
        language: {
            search: 'جستجو:',
            lengthMenu: 'نمایش _MENU_ ردیف',
            info: 'نمایش _START_ تا _END_ از _TOTAL_ ردیف',
            infoEmpty: 'موردی نیست',
            zeroRecords: 'موردی یافت نشد',
            paginate: { previous: 'قبلی', next: 'بعدی' }
        },
        columnDefs: [{ orderable: false, targets: [6] }],
    });
    @endif
    if (typeof feather !== 'undefined') feather.replace();
});
</script>
@endpush
