@extends('backend.views.view')

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/back-end/vendors/dataTable/dataTables.min.css') }}" type="text/css">
<style>
    #users-table_wrapper .dataTables_filter input { margin-right: .5rem; border-radius: 8px; min-width: 220px; }
    .table-actions .btn { padding: .35rem .55rem; margin: 0 .1rem; }
    .table-actions form { display: inline-block; }
</style>
@endpush

@section('main')
<div class="main-content">
    <div class="container">
        <div class="page-header">
            <h4>کاربران</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">خانه</a></li>
                    <li class="breadcrumb-item active">کاربران</li>
                </ol>
            </nav>
        </div>

        <div class="card border-0 shadow-sm mb-3">
            <div class="card-body d-flex flex-wrap justify-content-between align-items-center gap-2">
                <p class="text-muted small mb-0">مدیریت حساب‌های کاربری؛ حذف کاربر در این پنل وجود ندارد. می‌توانید وضعیت فعال / غیرفعال را تغییر دهید.</p>
                <a href="{{ route('admin.users.create') }}" class="btn btn-primary">افزودن کاربر</a>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table id="users-table" class="table table-striped table-bordered" style="width:100%">
                        <thead>
                            <tr>
                                <th>شناسه</th>
                                <th>نام</th>
                                <th>ایمیل</th>
                                <th>موبایل</th>
                                <th>تاریخ ثبت</th>
                                <th>وضعیت</th>
                                <th style="min-width:120px">عملیات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($users as $u)
                                <tr>
                                    <td class="align-middle">{{ $u->id }}</td>
                                    <td class="align-middle font-weight-600">{{ $u->name }}</td>
                                    <td class="align-middle small" dir="ltr">{{ $u->email }}</td>
                                    <td class="align-middle text-monospace" dir="ltr">{{ $u->mobile ?: '—' }}</td>
                                    <td class="align-middle small text-muted">{{ \App\Support\JalaliCalendar::formatShamsiDateTime($u->created_at) }}</td>
                                    <td class="align-middle">
                                        @if($u->is_active)
                                            <span class="badge badge-success">فعال</span>
                                        @else
                                            <span class="badge badge-secondary">غیرفعال</span>
                                        @endif
                                    </td>
                                    <td class="align-middle text-nowrap table-actions">
                                        <a href="{{ route('admin.users.edit', $u) }}" class="btn btn-sm btn-outline-primary"><i data-feather="edit-2" class="width-16 height-16"></i></a>
                                        @if ($u->id !== auth()->id())
                                            <form action="{{ route('admin.users.toggle-active', $u) }}" method="post" class="d-inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn btn-sm btn-outline-secondary" title="{{ $u->is_active ? 'غیرفعال کردن' : 'فعال کردن' }}"><i data-feather="power" class="width-16 height-16"></i></button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="7" class="text-center text-muted py-5">کاربری ثبت نشده است.</td></tr>
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
    @if ($users->isNotEmpty())
    $('#users-table').DataTable({
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
        columnDefs: [{ orderable: false, targets: [6] }],
        drawCallback: function () { if (typeof feather !== 'undefined') feather.replace(); }
    });
    @else
    if (typeof feather !== 'undefined') feather.replace();
    @endif
});
</script>
@endpush
