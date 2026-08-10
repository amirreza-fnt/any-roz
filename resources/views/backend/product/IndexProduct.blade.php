@extends('backend.views.view')

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/back-end/vendors/dataTable/dataTables.min.css') }}" type="text/css">
<style>
    .product-thumb {
        width: 52px;
        height: 52px;
        object-fit: cover;
        border-radius: 8px;
        border: 1px solid rgba(0, 0, 0, 0.06);
        background: #f4f6f9;
    }
    .product-thumb-placeholder {
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
        font-size: 0.72rem;
        font-weight: 600;
        padding: 0.3rem 0.55rem;
        border-radius: 999px;
    }
    .status-pill-active { background: rgba(16, 185, 129, 0.12); color: #059669; }
    .status-pill-inactive { background: rgba(239, 68, 68, 0.1); color: #b91c1c; }
    .table-actions .btn { padding: 0.35rem 0.5rem; margin: 0 0.08rem; }
    .table-actions form { display: inline-block; }
    #products-table_wrapper .dataTables_filter input {
        margin-right: 0.5rem;
        border-radius: 6px;
        min-width: 220px;
    }
    .weight-tags { font-size: 0.8rem; line-height: 1.5; }
</style>
@endpush

@section('main')

<div class="main-content">
    <div class="container">

        <div class="page-header">
            <h4>لیست محصولات</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">خانه</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.products.index') }}">محصولات</a></li>
                    <li class="breadcrumb-item active" aria-current="page">نمایش همه</li>
                </ol>
            </nav>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body d-flex flex-wrap align-items-center justify-content-between">
                        <div>
                            <h6 class="card-title mb-1">مدیریت محصولات</h6>
                            <p class="text-muted small mb-0">ثابت یا تفکیک وزن با قیمت و موجودی جدا، تصاویر چندگانه و وضعیت‌ها.</p>
                        </div>
                        <a href="{{ route('admin.products.create') }}" class="btn btn-primary mt-2 mt-md-0">
                            <i data-feather="plus" class="width-16 height-16"></i>
                            <span class="mr-1">افزودن محصول</span>
                        </a>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="products-table" class="table table-striped table-bordered" style="width:100%">
                                <thead>
                                    <tr>
                                        <th style="width:64px">تصویر</th>
                                        <th>عنوان</th>
                                        <th>کد رهگیری</th>
                                        <th>دسته</th>
                                        <th>وزن‌ها / موجودی</th>
                                        <th style="width:72px">جمع موجودی</th>
                                        <th style="width:88px">قیمت فروش</th>
                                        <th style="width:88px">نمایش</th>
                                        <th style="width:88px">پیشنهاد</th>
                                        <th style="width:200px">عملیات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($products as $p)
                                        <tr>
                                            <td class="align-middle text-center">
                                                @if ($p->primary_image_url)
                                                    <img src="{{ $p->primary_image_url }}" alt="" class="product-thumb">
                                                @else
                                                    <span class="product-thumb-placeholder"><i data-feather="image" class="width-18 height-18"></i></span>
                                                @endif
                                            </td>
                                            <td class="align-middle font-weight-500">{{ $p->title }}</td>
                                            <td class="align-middle text-monospace small">{{ $p->tracking_code }}</td>
                                            <td class="align-middle text-muted">{{ $p->category?->title ?? '—' }}</td>
                                            <td class="align-middle weight-tags text-muted">
                                                @forelse ($p->typeOfWeights as $tw)
                                                    <div class="mb-1">
                                                        <span class="text-dark font-weight-500">{{ $tw->title }}</span>
                                                        <span class="d-block small">موجودی {{ number_format($tw->pivot->stock) }} — خرید {{ number_format($tw->pivot->price_buy) }} — فروش {{ number_format($tw->pivot->price_discounted) }}</span>
                                                    </div>
                                                @empty
                                                    <span class="text-muted">ثابت (بدون وزن)</span>
                                                @endforelse
                                            </td>
                                            <td class="align-middle">{{ number_format($p->stock) }}</td>
                                            <td class="align-middle">
                                                @if ($p->typeOfWeights->isEmpty())
                                                    {{ number_format($p->price_discounted) }}
                                                @else
                                                    @php
                                                        $__minDisc = $p->typeOfWeights->map(fn ($tw) => (int) $tw->pivot->price_discounted)->min();
                                                    @endphp
                                                    <span class="small">از {{ number_format($__minDisc) }}</span>
                                                @endif
                                            </td>
                                            <td class="align-middle">
                                                @if ($p->status === 'active')
                                                    <span class="status-pill status-pill-active">فعال</span>
                                                @else
                                                    <span class="status-pill status-pill-inactive">غیرفعال</span>
                                                @endif
                                            </td>
                                            <td class="align-middle">
                                                @if ($p->suggested === 'active')
                                                    <span class="status-pill status-pill-active">بله</span>
                                                @else
                                                    <span class="status-pill status-pill-inactive">خیر</span>
                                                @endif
                                            </td>
                                            <td class="align-middle table-actions text-nowrap">
                                                <form action="{{ route('admin.products.toggle-status', $p) }}" method="post" class="d-inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="btn btn-sm btn-outline-{{ $p->status === 'active' ? 'warning' : 'success' }}" title="تغییر نمایش">
                                                        <i data-feather="{{ $p->status === 'active' ? 'eye-off' : 'eye' }}" class="width-16 height-16"></i>
                                                    </button>
                                                </form>
                                                <form action="{{ route('admin.products.toggle-suggested', $p) }}" method="post" class="d-inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="btn btn-sm btn-outline-{{ $p->suggested === 'active' ? 'secondary' : 'primary' }}" title="پیشنهاد ویژه">
                                                        <i data-feather="star" class="width-16 height-16"></i>
                                                    </button>
                                                </form>
                                                <a href="{{ route('admin.products.edit', $p) }}" class="btn btn-sm btn-outline-primary" title="ویرایش">
                                                    <i data-feather="edit-2" class="width-16 height-16"></i>
                                                </a>
                                                <form action="{{ route('admin.products.destroy', $p) }}" method="post" class="d-inline" onsubmit="return confirm('حذف این محصول؟');">
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
                                            <td colspan="10" class="text-center text-muted py-5">
                                                محصولی ثبت نشده است.
                                                <a href="{{ route('admin.products.create') }}" class="d-block mt-2">افزودن محصول</a>
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
        @if ($products->isNotEmpty())
        $('#products-table').DataTable({
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
            columnDefs: [{ orderable: false, targets: [0, 9] }],
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
