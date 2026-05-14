@extends('backend.views.view')

@section('main')
<div class="main-content">
    <div class="container">
        <div class="page-header d-flex flex-wrap justify-content-between align-items-center gap-2">
            <div>
                <h4>خریداران (CRM)</h4>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard.marketing') }}">بازاریابی</a></li>
                        <li class="breadcrumb-item active">خریداران</li>
                    </ol>
                </nav>
            </div>
            <a href="{{ route('admin.marketing.buyers.create') }}" class="btn btn-primary">افزودن خریدار</a>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th>#</th>
                                <th>نام</th>
                                <th>تماس</th>
                                <th>مغازه</th>
                                <th>استان / شهر</th>
                                <th>موقعیت</th>
                                <th class="text-left" style="min-width:120px">عملیات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($buyers as $b)
                                <tr>
                                    <td class="text-muted">{{ $b->id }}</td>
                                    <td class="font-weight-600">{{ $b->full_name }}</td>
                                    <td class="text-monospace" dir="ltr">{{ $b->phone }}</td>
                                    <td>{{ $b->store_name ?: '—' }}</td>
                                    <td class="small">
                                        {{ $b->province?->name ?? '—' }}
                                        @if($b->city) / {{ $b->city->name }} @endif
                                    </td>
                                    <td>
                                        @if($b->latitude && $b->longitude)
                                            <span class="badge badge-success">ثبت شده</span>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td class="text-nowrap">
                                        <a href="{{ route('admin.marketing.buyers.edit', $b) }}" class="btn btn-sm btn-outline-primary"><i data-feather="edit-2" class="width-16 height-16"></i></a>
                                        <form action="{{ route('admin.marketing.buyers.destroy', $b) }}" method="post" class="d-inline" onsubmit="return confirm('حذف شود؟');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger"><i data-feather="trash-2" class="width-16 height-16"></i></button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="7" class="text-center text-muted py-5">خریداری ثبت نشده است.</td></tr>
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
