@extends('backend.views.view')

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/back-end/vendors/dataTable/dataTables.min.css') }}" type="text/css">
<style>
    .gc-code { font-family: ui-monospace, monospace; font-weight: 700; letter-spacing: .04em; }
    .gc-pill { font-size: .72rem; font-weight: 700; padding: .28rem .55rem; border-radius: 999px; }
    .gc-active { background: rgba(16,185,129,.12); color: #047857; }
    .gc-inactive { background: rgba(239,68,68,.1); color: #b91c1c; }
    .gc-scope { font-size: .78rem; color: #475569; }
    #gift-codes-table_wrapper .dataTables_filter input { margin-right: .5rem; border-radius: 8px; min-width: 220px; }
    .table-actions .btn { padding: .35rem .55rem; margin: 0 .1rem; }
    .table-actions form { display: inline-block; }
</style>
@endpush

@section('main')
<div class="main-content">
    <div class="container">
        <div class="page-header">
            <h4>کدهای هدیه</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">خانه</a></li>
                    <li class="breadcrumb-item active">کدهای هدیه</li>
                </ol>
            </nav>
        </div>

        <div class="card border-0 shadow-sm mb-3">
            <div class="card-body d-flex flex-wrap justify-content-between align-items-center gap-2">
                <div>
                    <h5 class="mb-1">اعتبار هدیه برای سبد خرید</h5>
                    <p class="text-muted small mb-0">مبلغ ثابت یا درصدی با سقف؛ حداقل خرید، محدودیت استفاده و محدودهٔ محصول/دسته.</p>
                </div>
                <a href="{{ route('admin.gift-codes.create') }}" class="btn btn-primary">افزودن کد هدیه</a>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table id="gift-codes-table" class="table table-striped table-bordered" style="width:100%">
                        <thead>
                            <tr>
                                <th>کد</th>
                                <th>عنوان</th>
                                <th>مقدار هدیه</th>
                                <th>حداقل خرید</th>
                                <th>استفاده</th>
                                <th>محدوده</th>
                                <th>وضعیت</th>
                                <th>انقضا</th>
                                <th style="min-width:120px">عملیات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($giftCodes as $g)
                                <tr>
                                    <td class="align-middle gc-code">{{ $g->code }}</td>
                                    <td class="align-middle">{{ $g->title ?: '—' }}</td>
                                    <td class="align-middle">{{ $g->valueLabel() }}</td>
                                    <td class="align-middle">{{ number_format($g->min_order_amount) }}</td>
                                    <td class="align-middle small">{{ number_format($g->used_count) }}@if($g->usage_limit) / {{ number_format($g->usage_limit) }}@else <span class="text-muted">/ ∞</span>@endif</td>
                                    <td class="align-middle gc-scope">
                                        @if ($g->applies_to === 'all') همه محصولات
                                        @elseif($g->applies_to === 'categories') دسته‌های انتخابی ({{ count($g->category_ids ?? []) }})
                                        @else محصولات انتخابی ({{ count($g->product_ids ?? []) }})
                                        @endif
                                    </td>
                                    <td class="align-middle">
                                        <span class="gc-pill {{ $g->status === 'active' ? 'gc-active' : 'gc-inactive' }}">{{ $g->status === 'active' ? 'فعال' : 'غیرفعال' }}</span>
                                    </td>
                                    <td class="align-middle small text-muted">{{ $g->expires_at?->format('Y/m/d') ?? '—' }}</td>
                                    <td class="align-middle text-nowrap table-actions">
                                        <a href="{{ route('admin.gift-codes.edit', $g) }}" class="btn btn-sm btn-outline-primary"><i data-feather="edit-2" class="width-16 height-16"></i></a>
                                        <form action="{{ route('admin.gift-codes.toggle-status', $g) }}" method="post" class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-sm btn-outline-secondary" title="تغییر وضعیت"><i data-feather="power" class="width-16 height-16"></i></button>
                                        </form>
                                        <form action="{{ route('admin.gift-codes.destroy', $g) }}" method="post" class="d-inline" onsubmit="return confirm('حذف شود؟');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger"><i data-feather="trash-2" class="width-16 height-16"></i></button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="9" class="text-center text-muted py-5">کد هدیه‌ای ثبت نشده است.</td></tr>
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
    @if ($giftCodes->isNotEmpty())
    $('#gift-codes-table').DataTable({
        responsive: true,
        order: [[0, 'desc']],
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
