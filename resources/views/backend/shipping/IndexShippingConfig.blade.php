@extends('backend.views.view')

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/back-end/vendors/dataTable/dataTables.min.css') }}" type="text/css">
<style>
    .sc-pill { font-size: .72rem; font-weight: 700; padding: .28rem .55rem; border-radius: 999px; }
    .sc-on { background: rgba(16,185,129,.12); color: #047857; }
    .sc-off { background: rgba(107,114,128,.12); color: #4b5563; }
    #shipping-table_wrapper .dataTables_filter input { margin-right: .5rem; border-radius: 8px; min-width: 220px; }
    .table-actions .btn { padding: .35rem .55rem; margin: 0 .1rem; }
    .table-actions form { display: inline-block; }
</style>
@endpush

@section('main')
<div class="main-content">
    <div class="container">
        <div class="page-header">
            <h4>روش‌های ارسال</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">خانه</a></li>
                    <li class="breadcrumb-item active">روش ارسال</li>
                </ol>
            </nav>
        </div>

        <div class="card border-0 shadow-sm mb-3">
            <div class="card-body d-flex flex-wrap justify-content-between align-items-center gap-2">
                <div>
                    <h5 class="mb-1">تنظیمات حمل بر اساس وزن و هزینه‌ها</h5>
                    <p class="text-muted small mb-0">حذف روش ارسال در این نسخه غیرفعال است؛ می‌توانید با تغییر وضعیت، روش را از محاسبهٔ سایت خارج کنید.</p>
                </div>
                <a href="{{ route('admin.shipping-configs.create') }}" class="btn btn-primary">افزودن روش ارسال</a>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table id="shipping-table" class="table table-striped table-bordered" style="width:100%">
                        <thead>
                            <tr>
                                <th>نام</th>
                                <th>هزینه پایه ارسال</th>
                                <th>بیمه</th>
                                <th>بسته‌بندی</th>
                                <th>حد وزن (kg)</th>
                                <th>هزینه وزن اضافه</th>
                                <th>ترتیب</th>
                                <th>وضعیت</th>
                                <th style="min-width:100px">عملیات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($configs as $c)
                                <tr>
                                    <td class="align-middle font-weight-600">{{ $c->name }}</td>
                                    <td class="align-middle">{{ number_format($c->base_shipping_cost) }}</td>
                                    <td class="align-middle">{{ number_format($c->base_insurance_cost) }}</td>
                                    <td class="align-middle">{{ number_format($c->base_packaging_cost) }}</td>
                                    <td class="align-middle">{{ $c->package_weight_limit }}</td>
                                    <td class="align-middle">{{ number_format($c->extra_weight_cost) }}</td>
                                    <td class="align-middle">{{ $c->sort_order }}</td>
                                    <td class="align-middle">
                                        <span class="sc-pill {{ $c->is_active ? 'sc-on' : 'sc-off' }}">{{ $c->is_active ? 'فعال' : 'غیرفعال' }}</span>
                                    </td>
                                    <td class="align-middle text-nowrap table-actions">
                                        <a href="{{ route('admin.shipping-configs.edit', $c) }}" class="btn btn-sm btn-outline-primary"><i data-feather="edit-2" class="width-16 height-16"></i></a>
                                        <form action="{{ route('admin.shipping-configs.toggle-status', $c) }}" method="post" class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-sm btn-outline-secondary" title="تغییر وضعیت"><i data-feather="power" class="width-16 height-16"></i></button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="9" class="text-center text-muted py-5">روشی ثبت نشده است.</td></tr>
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
    @if ($configs->isNotEmpty())
    $('#shipping-table').DataTable({
        responsive: true,
        order: [[6, 'asc']],
        pageLength: 25,
        language: {
            search: 'جستجو:',
            lengthMenu: 'نمایش _MENU_ ردیف',
            info: 'نمایش _START_ تا _END_ از _TOTAL_ ردیف',
            infoEmpty: 'موردی برای نمایش وجود ندارد',
            zeroRecords: 'موردی یافت نشد',
            paginate: { previous: 'قبلی', next: 'بعدی' }
        },
        columnDefs: [{ orderable: false, targets: [8] }],
        drawCallback: function () { if (typeof feather !== 'undefined') feather.replace(); }
    });
    @else
    if (typeof feather !== 'undefined') feather.replace();
    @endif
});
</script>
@endpush
