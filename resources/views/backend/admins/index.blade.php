@extends('backend.views.view')

@section('main')
<div class="main-content">
    <div class="container">
        <div class="page-header d-flex justify-content-between align-items-center flex-wrap">
            <div>
                <h4 class="mb-1">مدیران سیستم</h4>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ admin_home_url() }}">خانه</a></li>
                        <li class="breadcrumb-item active">مدیران</li>
                    </ol>
                </nav>
            </div>
            <a href="{{ route('admin.managers.create') }}" class="btn btn-primary rounded-pill px-4">افزودن مدیر</a>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th>#</th>
                            <th>نام</th>
                            <th>موبایل</th>
                            <th>سمت</th>
                            <th>نوع</th>
                            <th>وضعیت</th>
                            <th class="text-left">عملیات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($admins as $a)
                            <tr>
                                <td>{{ $a->id }}</td>
                                <td>{{ $a->full_name }}</td>
                                <td class="text-left" dir="ltr">{{ $a->phone }}</td>
                                <td>{{ $a->position ?: '—' }}</td>
                                <td>
                                    @if($a->is_super)
                                        <span class="badge badge-dark">سوپرادمین</span>
                                    @else
                                        <span class="badge badge-secondary">مدیر</span>
                                    @endif
                                </td>
                                <td>
                                    @if($a->is_active)
                                        <span class="badge badge-success">فعال</span>
                                    @else
                                        <span class="badge badge-warning text-dark">غیرفعال</span>
                                    @endif
                                </td>
                                <td class="text-left">
                                    <a href="{{ route('admin.managers.edit', $a) }}" class="btn btn-sm btn-outline-primary">ویرایش</a>
                                    @if($a->id !== auth('admin')->id())
                                        <form action="{{ route('admin.managers.toggle-active', $a) }}" method="post" class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-sm btn-outline-secondary">تغییر وضعیت</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
