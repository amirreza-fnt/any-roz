@extends('backend.views.view')

@section('main')
<div class="main-content">
    <div class="container">
        <div class="page-header d-flex flex-wrap justify-content-between align-items-center gap-2">
            <div>
                <h4>بررسی خرید کلی همکاران</h4>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ admin_home_url() }}">خانه</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard.accounting') }}">حسابداری</a></li>
                        <li class="breadcrumb-item active">خرید کلی همکاران</li>
                    </ol>
                </nav>
            </div>
        </div>

        <ul class="nav nav-tabs mb-3">
            <li class="nav-item">
                <a class="nav-link {{ $tab === 'pending' ? 'active' : '' }}" href="{{ route('admin.accounting.colleague-wholesale.index', ['tab' => 'pending']) }}">
                    در انتظار حسابداری <span class="badge badge-warning text-dark">{{ number_format($counts['pending']) }}</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ $tab === 'approved' ? 'active' : '' }}" href="{{ route('admin.accounting.colleague-wholesale.index', ['tab' => 'approved']) }}">
                    تأیید شده <span class="badge badge-success">{{ number_format($counts['approved']) }}</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ $tab === 'rejected' ? 'active' : '' }}" href="{{ route('admin.accounting.colleague-wholesale.index', ['tab' => 'rejected']) }}">
                    رد شده <span class="badge badge-secondary">{{ number_format($counts['rejected']) }}</span>
                </a>
            </li>
        </ul>

        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th>#</th>
                                <th>همکار</th>
                                <th>تاریخ خرید</th>
                                <th>اقلام</th>
                                <th>جمع (تومان)</th>
                                <th>وضعیت</th>
                                <th class="text-left">عملیات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($sales as $s)
                                @php $sum = $s->items->sum('unit_price'); @endphp
                                <tr>
                                    <td class="text-muted">{{ $s->id }}</td>
                                    <td>
                                        <div class="font-weight-600">{{ $s->colleague?->full_name ?? ('#'.$s->marketer_id) }}</div>
                                        <div class="small text-muted text-monospace" dir="ltr">{{ $s->buyer_phone }}</div>
                                    </td>
                                    <td>{{ $s->sale_date ? \App\Support\JalaliCalendar::formatShamsiDate($s->sale_date) : '—' }}</td>
                                    <td>{{ $s->items->count() }}</td>
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
                                    <td class="text-left text-nowrap">
                                        <a href="{{ route('admin.accounting.colleague-wholesale.show', $s) }}" class="btn btn-sm btn-outline-primary">بررسی</a>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="7" class="text-center text-muted py-5">موردی یافت نشد.</td></tr>
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