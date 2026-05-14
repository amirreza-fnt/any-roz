@extends('backend.views.view')

@section('main')
<div class="main-content">
    <div class="container">
        <div class="page-header">
            <h4>ویرایش مدیر</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">خانه</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.managers.index') }}">مدیران</a></li>
                    <li class="breadcrumb-item active">ویرایش</li>
                </ol>
            </nav>
        </div>

        <form method="post" action="{{ route('admin.managers.update', $admin) }}" class="row">
            @csrf
            @method('PUT')
            <div class="col-lg-4 mb-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white font-weight-bold">اطلاعات هویتی</div>
                    <div class="card-body">
                        <div class="form-group">
                            <label>نام <span class="text-danger">*</span></label>
                            <input type="text" name="first_name" class="form-control" value="{{ old('first_name', $admin->first_name) }}" required>
                        </div>
                        <div class="form-group">
                            <label>نام خانوادگی <span class="text-danger">*</span></label>
                            <input type="text" name="last_name" class="form-control" value="{{ old('last_name', $admin->last_name) }}" required>
                        </div>
                        <div class="form-group">
                            <label>شماره موبایل <span class="text-danger">*</span></label>
                            <input type="text" name="phone" class="form-control text-left" dir="ltr" value="{{ old('phone', $admin->phone) }}" required>
                        </div>
                        <div class="form-group">
                            <label>سمت</label>
                            <input type="text" name="position" class="form-control" value="{{ old('position', $admin->position) }}" placeholder="مثلاً حسابدار">
                        </div>
                        <div class="form-group">
                            <label>رمز عبور جدید</label>
                            <input type="password" name="password" class="form-control" autocomplete="new-password" placeholder="در صورت خالی ماندن، رمز قبلی حفظ می‌شود">
                        </div>
                        <div class="form-group">
                            <label>تکرار رمز عبور</label>
                            <input type="password" name="password_confirmation" class="form-control" autocomplete="new-password">
                        </div>
                        <div class="custom-control custom-checkbox mb-2">
                            <input type="checkbox" class="custom-control-input" name="is_active" id="is_active" value="1" {{ old('is_active', $admin->is_active) ? 'checked' : '' }}>
                            <label class="custom-control-label" for="is_active">حساب فعال باشد</label>
                        </div>
                        @if(auth('admin')->user()->is_super && $admin->id !== auth('admin')->id())
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" name="is_super" id="is_super" value="1" {{ old('is_super', $admin->is_super) ? 'checked' : '' }}>
                                <label class="custom-control-label" for="is_super">سوپرادمین (دسترسی کامل)</label>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-lg-4 mb-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white font-weight-bold">اطلاعات اختیاری</div>
                    <div class="card-body">
                        <div class="form-group">
                            <label>ایمیل</label>
                            <input type="email" name="email" class="form-control text-left" dir="ltr" value="{{ old('email', $admin->email) }}">
                        </div>
                        <div class="form-group">
                            <label>کد ملی</label>
                            <input type="text" name="national_id" class="form-control text-left" dir="ltr" value="{{ old('national_id', $admin->national_id) }}">
                        </div>
                        <div class="form-group">
                            <label>نام پدر</label>
                            <input type="text" name="father_name" class="form-control" value="{{ old('father_name', $admin->father_name) }}">
                        </div>
                        <div class="form-group mb-0">
                            <label>تاریخ تولد</label>
                            <input type="date" name="birth_date" class="form-control text-left" dir="ltr" value="{{ old('birth_date', optional($admin->birth_date)->format('Y-m-d')) }}">
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-12 mb-3">
                @if($admin->is_super)
                    <div class="alert alert-secondary border-0 small">این کاربر سوپرادمین است و به‌صورت خودکار به همهٔ بخش‌ها دسترسی دارد؛ تیک‌های زیر برای سوپرادمین اعمال نمی‌شوند.</div>
                @else
                    @include('backend.admins._permissions_matrix', ['groups' => $groups, 'admin' => $admin])
                @endif
            </div>
            <div class="col-12">
                <button type="submit" class="btn btn-primary btn-lg rounded-pill px-5">ذخیرهٔ تغییرات</button>
                <a href="{{ route('admin.managers.index') }}" class="btn btn-light btn-lg rounded-pill px-4">بازگشت</a>
            </div>
        </form>
    </div>
</div>
@endsection
