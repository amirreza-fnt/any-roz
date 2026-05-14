@extends('backend.views.view')

@section('main')
<div class="main-content">
    <div class="container">
        <div class="page-header">
            <h4>پشتیبان‌گیری فنی</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">خانه</a></li>
                    <li class="breadcrumb-item active">پشتیبان‌گیری</li>
                </ol>
            </nav>
        </div>

        <div class="card border-0 shadow-sm mb-3">
            <div class="card-body">
                <p class="text-muted mb-0">یک فایل <strong>CSV</strong> (با BOM برای Excel) دانلود می‌شود؛ هر بخش انتخاب‌شده با یک سربرگ جدا می‌شود. بخش‌های بدون داده در فایل نمی‌آیند.</p>
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
                <button type="submit" class="btn btn-primary">دانلود CSV</button>
            </div>
        </form>
    </div>
</div>
@endsection
