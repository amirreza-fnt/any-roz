@extends('backend.views.view')

@section('main')
<div class="main-content">
    <div class="container">
        <div class="page-header d-flex flex-wrap justify-content-between align-items-center gap-2">
            <div>
                <h4>همکاران (خرید کلی)</h4>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ admin_home_url() }}">خانه</a></li>
                        <li class="breadcrumb-item active">همکاران</li>
                    </ol>
                </nav>
            </div>
            <a href="{{ route('admin.colleagues.create') }}" class="btn btn-primary rounded-pill px-4">افزودن همکار</a>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th>#</th>
                                <th>نام</th>
                                <th>موبایل</th>
                                <th>فروشگاه / محل</th>
                                <th>پیش‌فاکتورها</th>
                                <th>فاکتورها</th>
                                <th>وضعیت</th>
                                <th class="text-left">عملیات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($colleagues as $c)
                                <tr>
                                    <td class="text-muted">{{ $c->id }}</td>
                                    <td>
                                        <a href="{{ route('admin.colleagues.show', $c) }}" class="font-weight-600 text-primary">{{ $c->full_name }}</a>
                                        @if($c->position)
                                            <div class="small text-muted">{{ $c->position }}</div>
                                        @endif
                                    </td>
                                    <td class="text-left text-monospace" dir="ltr">{{ $c->phone }}</td>
                                    <td>{{ $c->store_name ?: '—' }}</td>
                                    <td>{{ number_format($c->wholesale_sales_count) }}</td>
                                    <td>{{ number_format($c->wholesale_orders_count) }}</td>
                                    <td>
                                        @if($c->is_active)
                                            <span class="badge badge-success">فعال</span>
                                        @else
                                            <span class="badge badge-warning text-dark">غیرفعال</span>
                                        @endif
                                    </td>
                                    <td class="text-left text-nowrap">
                                        <a href="{{ route('admin.colleagues.show', $c) }}" class="btn btn-sm btn-outline-primary">فاکتورها</a>
                                        <a href="{{ route('admin.colleagues.edit', $c) }}" class="btn btn-sm btn-outline-secondary">ویرایش</a>
                                        @if($c->id !== auth('admin')->id())
                                            <form action="{{ route('admin.colleagues.toggle-active', $c) }}" method="post" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-outline-info">تغییر وضعیت</button>
                                            </form>
                                            <form action="{{ route('admin.colleagues.destroy', $c) }}" method="post" class="d-inline" onsubmit="return confirm('حذف همکار؟ این عمل غیرقابل بازگشت است.');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger">حذف</button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="8" class="text-center text-muted py-5">هنوز همکاری ثبت نشده است.</td></tr>
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
<script>if (typeof feather !== 'undefined') feather.replace();</script>
@endpush
