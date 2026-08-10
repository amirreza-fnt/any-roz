@extends('backend.views.view')

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/back-end/vendors/dataTable/dataTables.min.css') }}" type="text/css">
<style>
    .table-actions .btn {
        padding: 0.35rem 0.55rem;
        margin: 0 0.1rem;
    }
    .table-actions form {
        display: inline-block;
    }
    #type-of-weights-table_wrapper .dataTables_filter input {
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
            <h4>انواع وزن</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.dashboard') }}">خانه</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.type-of-weights.index') }}">انواع وزن</a>
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
                            <h6 class="card-title mb-1">مدیریت نوع وزن</h6>
                            <p class="text-muted small mb-0">فقط عنوان و مقدار وزن (اختیاری) مطابق جدول دیتابیس.</p>
                        </div>
                        <a href="{{ route('admin.type-of-weights.create') }}" class="btn btn-primary mt-2 mt-md-0">
                            <i data-feather="plus" class="width-16 height-16"></i>
                            <span class="mr-1">افزودن نوع وزن</span>
                        </a>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="type-of-weights-table" class="table table-striped table-bordered" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>عنوان</th>
                                        <th style="width:120px">مقدار وزن</th>
                                        <th style="width:160px">عملیات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($typeOfWeights as $row)
                                        <tr>
                                            <td class="align-middle font-weight-500">{{ $row->title }}</td>
                                            <td class="align-middle text-muted">
                                                {{ $row->weight !== null ? number_format($row->weight) : '—' }}
                                            </td>
                                            <td class="align-middle table-actions text-nowrap">
                                                <a href="{{ route('admin.type-of-weights.edit', $row) }}" class="btn btn-sm btn-outline-primary" title="ویرایش">
                                                    <i data-feather="edit-2" class="width-16 height-16"></i>
                                                </a>
                                                <form action="{{ route('admin.type-of-weights.destroy', $row) }}" method="post" class="d-inline" onsubmit="return confirm('این مورد حذف شود؟');">
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
                                            <td colspan="3" class="text-center text-muted py-5">
                                                هنوز نوع وزنی ثبت نشده است.
                                                <a href="{{ route('admin.type-of-weights.create') }}" class="d-block mt-2">افزودن اولین مورد</a>
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
        @if ($typeOfWeights->isNotEmpty())
        $('#type-of-weights-table').DataTable({
            responsive: true,
            order: [[0, 'asc']],
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
                { orderable: false, targets: [2] }
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
