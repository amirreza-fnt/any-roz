@extends('backend.views.view')

@section('main')
<div class="main-content">
    <div class="container">
        <div class="page-header">
            <h4>پشتیبان‌گیری فنی</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ admin_home_url() }}">خانه</a></li>
                    <li class="breadcrumb-item active">پشتیبان‌گیری</li>
                </ol>
            </nav>
        </div>

        @if (session('warning'))
            <div class="alert alert-warning border-0 shadow-sm mb-3">{{ session('warning') }}</div>
        @endif
        @if (session('success'))
            <div class="alert alert-success border-0 shadow-sm mb-3">{{ session('success') }}</div>
        @endif

        <div class="card border-0 shadow-sm mb-3">
            <div class="card-body">
                <p class="text-muted mb-0">برای <strong>هر بخش انتخاب‌شده یک فایل CSV جدا</strong> (سازگار با Excel، با BOM) دانلود می‌شود. پس از ارسال فرم، صفحهٔ دانلود باز می‌شود و مرورگر فایل‌ها را پشت‌سرهم ذخیره می‌کند. بخش‌های بدون داده در خروج نمی‌آیند.</p>
            </div>
        </div>

        <form method="post" action="{{ route('admin.technical-backup.download') }}" class="card border-0 shadow-sm">
            @csrf
            <div class="card-body">
                <h6 class="font-weight-bold mb-3">انتخاب بخش‌ها</h6>
                <div class="row">
                    @foreach ($moduleLabels as $key => $label)
                        <div class="col-md-4 mb-2">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" name="modules[]" value="{{ $key }}" id="mod-{{ $key }}">
                                <label class="custom-control-label" for="mod-{{ $key }}">{{ $label }}</label>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="card-footer bg-white">
                <button type="submit" class="btn btn-primary">شروع دانلود فایل‌ها</button>
            </div>
        </form>
    </div>
</div>
@endsection
