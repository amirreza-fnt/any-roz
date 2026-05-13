@extends('backend.views.view')

@section('main')
<div class="main-content">
    <div class="container">
        <div class="page-header">
            <h4>داشبورد حسابداری</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">داشبورد مدیریت</a></li>
                    <li class="breadcrumb-item active">حسابداری</li>
                </ol>
            </nav>
        </div>
        <div class="card border-0 shadow-sm">
            <div class="card-body p-5 text-center text-muted">
                <i data-feather="pie-chart" class="width-48 height-48 mb-3 opacity-50"></i>
                <p class="lead mb-0">این بخش برای اتصال به گزارش‌های مالی و حسابداری آماده است.</p>
                <p class="small mt-2">پس از اتصال API یا ماژول حسابداری، نمودارها و جداول اینجا نمایش داده می‌شوند.</p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>if (typeof feather !== 'undefined') feather.replace();</script>
@endpush
