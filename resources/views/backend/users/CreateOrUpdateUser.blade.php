@extends('backend.views.view')

@php
    $isEdit = ($type ?? '') === 'edit';
    $pageTitle = $isEdit ? 'ویرایش کاربر' : 'افزودن کاربر';
    $action = $isEdit ? route('admin.users.update', $user) : route('admin.users.store');
@endphp

@section('main')
<div class="main-content">
    <div class="container">
        <div class="page-header">
            <h4>{{ $pageTitle }}</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">خانه</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">کاربران</a></li>
                    <li class="breadcrumb-item active">{{ $pageTitle }}</li>
                </ol>
            </nav>
        </div>

        <form method="post" action="{{ $action }}" class="card border-0 shadow-sm">
            @csrf
            @if ($isEdit)
                @method('PUT')
            @endif

            <div class="card-body">
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>نام و نام خانوادگی <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" required maxlength="255" value="{{ old('name', $user?->name) }}">
                    </div>
                    <div class="form-group col-md-6">
                        <label>ایمیل <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control" dir="ltr" required maxlength="255" value="{{ old('email', $user?->email) }}">
                    </div>
                </div>
                <div class="form-group">
                    <label>شماره موبایل</label>
                    <input type="text" name="mobile" class="form-control" dir="ltr" maxlength="20" placeholder="09309118558" value="{{ old('mobile', $user?->mobile) }}">
                    <small class="text-muted">۱۱ رقم، شروع با ۰۹؛ در صورت خالی بودن ذخیره نمی‌شود.</small>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>رمز عبور @if (! $isEdit) <span class="text-danger">*</span> @else <span class="text-muted small">(در صورت خالی ماندن تغییر نمی‌کند)</span> @endif</label>
                        <input type="password" name="password" class="form-control" @if(! $isEdit) required minlength="8" @else minlength="8" @endif autocomplete="new-password">
                    </div>
                    <div class="form-group col-md-6">
                        <label>تکرار رمز عبور @if (! $isEdit) <span class="text-danger">*</span> @endif</label>
                        <input type="password" name="password_confirmation" class="form-control" @if(! $isEdit) required minlength="8" @else minlength="8" @endif autocomplete="new-password">
                    </div>
                </div>
                <div class="form-group mb-0">
                    @php
                        $isActiveDefault = $user ? (bool) $user->is_active : true;
                        $isActiveOld = old('is_active', $isActiveDefault ? '1' : '0');
                    @endphp
                    <div class="custom-control custom-checkbox">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" class="custom-control-input" id="user-is-active" name="is_active" value="1" @checked((string) $isActiveOld === '1')>
                        <label class="custom-control-label" for="user-is-active">حساب کاربری فعال باشد</label>
                    </div>
                    <small class="text-muted">کاربر غیرفعال در صورت اعمال محدودیت ورود، نمی‌تواند وارد سایت شود.</small>
                </div>
            </div>

            <div class="card-footer bg-white d-flex justify-content-between">
                <a href="{{ route('admin.users.index') }}" class="btn btn-light">بازگشت</a>
                <button type="submit" class="btn btn-primary px-4">ذخیره</button>
            </div>
        </form>
    </div>
</div>
@endsection
