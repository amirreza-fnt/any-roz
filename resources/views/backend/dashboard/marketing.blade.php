@extends('backend.views.view')

@section('main')
<div class="main-content">
    <div class="container">
        <div class="page-header">
            <h4>داشبورد بازاریابی</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">داشبورد مدیریت</a></li>
                    <li class="breadcrumb-item active">بازاریابی</li>
                </ol>
            </nav>
        </div>
        <div class="card border-0 shadow-sm">
            <div class="card-body p-5 text-center text-muted">
                <i data-feather="trending-up" class="width-48 height-48 mb-3 opacity-50"></i>
                <p class="lead mb-0">این بخش در حال توسعه است.</p>
                <p class="small mt-2">پس از آماده‌شدن گزارش‌های کمپین و فروش، اینجا نمایش داده می‌شوند.</p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>if (typeof feather !== 'undefined') feather.replace();</script>
@endpush
