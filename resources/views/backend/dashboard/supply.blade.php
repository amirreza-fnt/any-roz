@extends('backend.views.view')

@section('main')
<div class="main-content">
    <div class="container">
        <div class="page-header">
            <h4>داشبورد تأمین</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">داشبورد مدیریت</a></li>
                    <li class="breadcrumb-item active">تأمین</li>
                </ol>
            </nav>
        </div>

        <div class="row">
            <div class="col-md-4 mb-3">
                <div class="card border-0 shadow-sm h-100" style="border-right: 4px solid #6366f1 !important;">
                    <div class="card-body">
                        <h6 class="text-muted small text-uppercase">فاکتور در تأمین</h6>
                        <p class="display-4 font-weight-bold text-primary mb-0">{{ number_format($supplyOrdersCount) }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card border-0 shadow-sm h-100" style="border-right: 4px solid #10b981 !important;">
                    <div class="card-body">
                        <h6 class="text-muted small text-uppercase">جمع مبلغ (تومان)</h6>
                        <p class="h2 font-weight-bold text-success mb-0">{{ number_format((float) $supplyPendingAmount) }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3 d-flex align-items-stretch">
                <div class="card border-0 shadow-sm w-100 d-flex align-items-center justify-content-center p-4">
                    <a href="{{ route('admin.orders.supply') }}" class="btn btn-primary btn-lg btn-block">
                        <i data-feather="list" class="width-20 height-20"></i>
                        <span class="mr-2">لیست فاکتورهای تأمین</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>if (typeof feather !== 'undefined') feather.replace();</script>
@endpush
