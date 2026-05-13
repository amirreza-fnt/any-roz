@extends('backend.views.view')

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/back-end/vendors/dataTable/dataTables.min.css') }}" type="text/css">
<style>
    .category-thumb {
        width: 52px;
        height: 52px;
        object-fit: cover;
        border-radius: 8px;
        border: 1px solid rgba(0, 0, 0, 0.06);
        background: #f4f6f9;
    }
    .category-thumb-placeholder {
        width: 52px;
        height: 52px;
        border-radius: 8px;
        background: linear-gradient(135deg, #f0f2f5 0%, #e8ecf2 100%);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: #9aa4b2;
        border: 1px dashed #cfd6df;
    }
    .status-pill {
        font-size: 0.75rem;
        font-weight: 600;
        padding: 0.35rem 0.65rem;
        border-radius: 999px;
        letter-spacing: 0.02em;
    }
    .status-pill-active {
        background: rgba(16, 185, 129, 0.12);
        color: #059669;
    }
    .status-pill-inactive {
        background: rgba(239, 68, 68, 0.1);
        color: #b91c1c;
    }
    .table-actions .btn {
        padding: 0.35rem 0.55rem;
        margin: 0 0.1rem;
    }
    .table-actions form {
        display: inline-block;
    }
    #categories-table_wrapper .dataTables_filter input {
        margin-right: 0.5rem;
        border-radius: 6px;
        min-width: 220px;
    }
</style>
@endpush

@section('main')

<div class="main-content">
    <div class="container">

        <div class="page-header">
            <h4>لیست دسته‌بندی‌ها</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.dashboard') }}">خانه</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.category.index') }}">دسته‌بندی‌ها</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">نمایش همه</li>
                </ol>
            </nav>
        </div>

        <div class="row">
            <div class="col-md-12">

                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body d-flex flex-wrap align-items-center justify-content-between">
                        <div>
                            <h6 class="card-title mb-1">مدیریت دسته‌بندی محصولات</h6>
                            <p class="text-muted small mb-0">مشاهده، ویرایش، تغییر وضعیت و حذف دسته‌ها از اینجا انجام می‌شود.</p>
                        </div>
                        <a href="{{ route('admin.category.create') }}" class="btn btn-primary mt-2 mt-md-0">
                            <i data-feather="plus" class="width-16 height-16"></i>
                            <span class="mr-1">افزودن دسته جدید</span>
                        </a>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="categories-table" class="table table-striped table-bordered" style="width:100%">
                                <thead>
                                    <tr>
                                        <th style="width:72px">تصویر</th>
                                        <th>عنوان</th>
                                        <th>دسته والد</th>
                                        <th style="width:110px">وضعیت</th>
                                        <th style="width:220px">عملیات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($categories as $cat)
                                        <tr>
                                            <td class="align-middle text-center">
                                                @if ($cat->image_url)
                                                    <img src="{{ $cat->image_url }}" alt="{{ $cat->title }}" class="category-thumb">
                                                @else
                                                    <span class="category-thumb-placeholder" title="بدون تصویر">
                                                        <i data-feather="image" class="width-20 height-20"></i>
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="align-middle font-weight-500">{{ $cat->title }}</td>
                                            <td class="align-middle text-muted">
                                                {{ $cat->parent?->title ?? '—' }}
                                            </td>
                                            <td class="align-middle">
                                                @if ($cat->status === 'active')
                                                    <span class="status-pill status-pill-active">فعال</span>
                                                @else
                                                    <span class="status-pill status-pill-inactive">غیرفعال</span>
                                                @endif
                                            </td>
                                            <td class="align-middle table-actions text-nowrap">
                                                <form action="{{ route('admin.category.toggle-status', $cat) }}" method="post" class="d-inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="btn btn-sm btn-outline-{{ $cat->status === 'active' ? 'warning' : 'success' }}" title="{{ $cat->status === 'active' ? 'غیرفعال کردن' : 'فعال کردن' }}">
                                                        <i data-feather="{{ $cat->status === 'active' ? 'pause-circle' : 'play-circle' }}" class="width-16 height-16"></i>
                                                    </button>
                                                </form>
                                                <a href="{{ route('admin.category.edit', $cat) }}" class="btn btn-sm btn-outline-primary" title="ویرایش">
                                                    <i data-feather="edit-2" class="width-16 height-16"></i>
                                                </a>
                                                <form action="{{ route('admin.category.destroy', $cat) }}" method="post" class="d-inline" onsubmit="return confirm('این دسته حذف شود؟');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="حذف">
                                                        <i data-feather="trash-2" class="width-16 height-16"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center text-muted py-5">
                                                هنوز دسته‌ای ثبت نشده است.
                                                <a href="{{ route('admin.category.create') }}" class="d-block mt-2">افزودن اولین دسته</a>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
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
        @if ($categories->isNotEmpty())
        $('#categories-table').DataTable({
            responsive: true,
            order: [[1, 'asc']],
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
                { orderable: false, targets: [0, 4] }
            ],
            drawCallback: function () {
                if (typeof feather !== 'undefined') {
                    feather.replace();
                }
            }
        });
        @else
        if (typeof feather !== 'undefined') {
            feather.replace();
        }
        @endif
    });
</script>
@endpush
