@extends('backend.views.view')

@section('main')
<div class="main-content">
    <div class="container">
        <div class="page-header d-flex flex-wrap justify-content-between align-items-center gap-2">
            <div>
                <h4>فروش‌های ثبت‌شده</h4>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard.marketing') }}">بازاریابی</a></li>
                        <li class="breadcrumb-item active">فروش‌ها</li>
                    </ol>
                </nav>
            </div>
            <a href="{{ route('admin.marketing.sales.create') }}" class="btn btn-primary">ثبت فروش جدید</a>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th>#</th>
                                <th>خریدار</th>
                                <th>تاریخ فروش</th>
                                <th>جمع (تومان)</th>
                                <th>وضعیت</th>
                                <th>عملیات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($sales as $s)
                                @php $sum = $s->items->sum('unit_price'); @endphp
                                <tr>
                                    <td class="text-muted">{{ $s->id }}</td>
                                    <td>
                                        <div class="font-weight-600">{{ $s->buyer_first_name }} {{ $s->buyer_last_name }}</div>
                                        <div class="small text-muted text-monospace" dir="ltr">{{ $s->buyer_phone }}</div>
                                    </td>
                                    <td>{{ $s->sale_date ? \App\Support\JalaliCalendar::formatShamsiDate($s->sale_date) : '—' }}</td>
                                    <td class="font-weight-600">{{ number_format($sum) }}</td>
                                    <td>
                                        @if($s->status === \App\Models\MarketingSale::STATUS_PENDING)
                                            <span class="badge badge-warning">در انتظار حسابداری</span>
                                        @elseif($s->status === \App\Models\MarketingSale::STATUS_APPROVED)
                                            <span class="badge badge-success">تأیید شده</span>
                                        @else
                                            <span class="badge badge-secondary">رد شده</span>
                                        @endif
                                    </td>
                                    <td class="text-nowrap">
                                        <a href="{{ route('admin.marketing.sales.show', $s) }}" class="btn btn-sm btn-outline-primary">جزئیات</a>
                                        @if($s->isPending())
                                            <form action="{{ route('admin.marketing.sales.destroy', $s) }}" method="post" class="d-inline" onsubmit="return confirm('حذف شود؟');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger">حذف</button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="text-center text-muted py-5">فروشی ثبت نشده است.</td></tr>
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
