@extends('backend.views.view')

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/back-end/vendors/dataTable/dataTables.min.css') }}" type="text/css">
<style>
    .art-thumb { width: 56px; height: 56px; object-fit: cover; border-radius: 10px; border: 1px solid rgba(0,0,0,.06); background: #f4f6f9; }
    .art-thumb-ph { width: 56px; height: 56px; border-radius: 10px; background: linear-gradient(135deg,#eef2ff,#e0e7ff); display:inline-flex; align-items:center; justify-content:center; color:#6366f1; border:1px dashed #c7d2fe; font-size:.7rem; font-weight:700; }
    .art-status { font-size: .72rem; font-weight: 700; padding: .28rem .55rem; border-radius: 999px; }
    .st-draft { background: rgba(107,114,128,.12); color:#374151; }
    .st-published { background: rgba(16,185,129,.12); color:#047857; }
    .st-archived { background: rgba(245,158,11,.12); color:#b45309; }
    .table-actions .btn { padding: .35rem .55rem; margin: 0 .1rem; }
    .table-actions form { display: inline-block; }
    #articles-table_wrapper .dataTables_filter input { margin-right: .5rem; border-radius: 8px; min-width: 220px; }
</style>
@endpush

@section('main')
<div class="main-content">
    <div class="container">
        <div class="page-header">
            <h4>مقالات</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">خانه</a></li>
                    <li class="breadcrumb-item active">مقالات</li>
                </ol>
            </nav>
        </div>

        <div class="card border-0 shadow-sm mb-3">
            <div class="card-body d-flex flex-wrap align-items-center justify-content-between gap-2">
                <div>
                    <h5 class="mb-1">مدیریت محتوا و سئو</h5>
                    <p class="text-muted small mb-0">اسلاگ، متا تگ‌ها، کانونیکال و پیش‌نمایش عمومی برای ایندکس بهتر.</p>
                </div>
                <a href="{{ route('admin.articles.create') }}" class="btn btn-primary">افزودن مقاله</a>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table id="articles-table" class="table table-striped table-bordered" style="width:100%">
                        <thead>
                            <tr>
                                <th>تصویر</th>
                                <th>عنوان</th>
                                <th>اسلاگ</th>
                                <th>وضعیت</th>
                                <th>دسته</th>
                                <th>بازدید</th>
                                <th>ویژه</th>
                                <th>تاریخ</th>
                                <th style="min-width:140px">عملیات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($articles as $a)
                                <tr>
                                    <td class="align-middle text-center">
                                        @if ($a->featured_image_url)
                                            <img src="{{ $a->featured_image_url }}" alt="" class="art-thumb">
                                        @else
                                            <span class="art-thumb-ph">بدون تصویر</span>
                                        @endif
                                    </td>
                                    <td class="align-middle font-weight-600">{{ $a->title }}</td>
                                    <td class="align-middle small text-monospace">{{ $a->slug }}</td>
                                    <td class="align-middle">
                                        @php
                                            $sc = match($a->status) {
                                                'published' => 'st-published',
                                                'archived' => 'st-archived',
                                                default => 'st-draft',
                                            };
                                            $sl = match($a->status) {
                                                'published' => 'منتشر شده',
                                                'archived' => 'آرشیو',
                                                default => 'پیش‌نویس',
                                            };
                                        @endphp
                                        <span class="art-status {{ $sc }}">{{ $sl }}</span>
                                    </td>
                                    <td class="align-middle">{{ $a->category?->title ?? '—' }}</td>
                                    <td class="align-middle">{{ number_format($a->view_count) }}</td>
                                    <td class="align-middle">{{ $a->is_featured ? 'بله' : 'خیر' }}</td>
                                    <td class="align-middle small text-muted">{{ \App\Support\JalaliCalendar::formatShamsiDateTime($a->updated_at) }}</td>
                                    <td class="align-middle text-nowrap table-actions">
                                        @if ($a->isPubliclyVisible())
                                            <a href="{{ route('articles.public', $a) }}" class="btn btn-sm btn-outline-info" target="_blank" rel="noopener" title="مشاهده عمومی">مشاهده</a>
                                        @endif
                                        <a href="{{ route('admin.articles.edit', $a) }}" class="btn btn-sm btn-outline-primary" title="ویرایش"><i data-feather="edit-2" class="width-16 height-16"></i></a>
                                        @if ($a->status !== 'archived')
                                        <form action="{{ route('admin.articles.toggle-publish', $a) }}" method="post" class="d-inline" onsubmit="return confirm('وضعیت انتشار تغییر کند؟');">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-sm btn-outline-secondary" title="تغییر انتشار">
                                                <i data-feather="repeat" class="width-16 height-16"></i>
                                            </button>
                                        </form>
                                        @endif
                                        <form action="{{ route('admin.articles.destroy', $a) }}" method="post" class="d-inline" onsubmit="return confirm('حذف شود؟');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="حذف"><i data-feather="trash-2" class="width-16 height-16"></i></button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center text-muted py-5">مقاله‌ای ثبت نشده است.</td>
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
        @if ($articles->isNotEmpty())
        $('#articles-table').DataTable({
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
            columnDefs: [{ orderable: false, targets: [0, 8] }],
            drawCallback: function () { if (typeof feather !== 'undefined') feather.replace(); }
        });
        @else
        if (typeof feather !== 'undefined') feather.replace();
        @endif
    });
</script>
@endpush
