@extends('backend.views.view')

@section('main')
<div class="main-content">
    <div class="container">
        <div class="page-header">
            <h4>افزودن مدیر</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ admin_home_url() }}">خانه</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.managers.index') }}">مدیران</a></li>
                    <li class="breadcrumb-item active">افزودن</li>
                </ol>
            </nav>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger border-0 shadow-sm mb-3">
                <div class="font-weight-bold mb-2">لطفاً خطاهای زیر را اصلاح کنید:</div>
                <ul class="mb-0 pr-3 small">
                    @foreach ($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="post" action="{{ route('admin.managers.store') }}" class="row">
            @csrf
            <div class="col-lg-4 mb-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white font-weight-bold">اطلاعات هویتی</div>
                    <div class="card-body">
                        <div class="form-group">
                            <label>نام <span class="text-danger">*</span></label>
                            <input type="text" name="first_name" class="form-control" value="{{ old('first_name') }}" required>
                        </div>
                        <div class="form-group">
                            <label>نام خانوادگی <span class="text-danger">*</span></label>
                            <input type="text" name="last_name" class="form-control" value="{{ old('last_name') }}" required>
                        </div>
                        <div class="form-group">
                            <label>شماره موبایل <span class="text-danger">*</span></label>
                            <input type="text" name="phone" class="form-control text-left" dir="ltr" value="{{ old('phone') }}" required>
                        </div>
                        <div class="form-group">
                            <label>سمت</label>
                            <input type="text" name="position" class="form-control" value="{{ old('position') }}" placeholder="مثلاً حسابدار">
                        </div>
                        <div class="form-group">
                            <label>رمز عبور <span class="text-danger">*</span></label>
                            <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required autocomplete="new-password">
                            @error('password')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>
                        <div class="form-group">
                            <label>تکرار رمز عبور <span class="text-danger">*</span></label>
                            <input type="password" name="password_confirmation" class="form-control @error('password') is-invalid @enderror" required autocomplete="new-password">
                        </div>
                        <input type="hidden" name="is_active" value="0">
                        <div class="custom-control custom-checkbox mb-2">
                            <input type="checkbox" class="custom-control-input" name="is_active" id="is_active" value="1" {{ old('is_active', '1') === '1' || old('is_active') === true ? 'checked' : '' }}>
                            <label class="custom-control-label" for="is_active">حساب فعال باشد</label>
                        </div>
                        <input type="hidden" name="is_super" value="0">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" name="is_super" id="is_super" value="1" {{ old('is_super', '0') === '1' || old('is_super') === true ? 'checked' : '' }}>
                            <label class="custom-control-label" for="is_super">سوپرادمین (دسترسی کامل)</label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 mb-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white font-weight-bold">اطلاعات اختیاری</div>
                    <div class="card-body">
                        <div class="form-group">
                            <label>ایمیل</label>
                            <input type="email" name="email" class="form-control text-left" dir="ltr" value="{{ old('email') }}">
                        </div>
                        <div class="form-group">
                            <label>کد ملی</label>
                            <input type="text" name="national_id" class="form-control text-left" dir="ltr" value="{{ old('national_id') }}">
                        </div>
                        <div class="form-group">
                            <label>نام پدر</label>
                            <input type="text" name="father_name" class="form-control" value="{{ old('father_name') }}">
                        </div>
                        <div class="form-group mb-0">
                            <label>تاریخ تولد</label>
                            <input type="date" name="birth_date" class="form-control text-left" dir="ltr" value="{{ old('birth_date') }}">
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-12 mb-3">
                <div class="alert alert-info border-0 shadow-sm small">
                    همهٔ تیک‌های دسترسی به‌صورت پیش‌فرض خاموش هستند؛ فقط موارد لازم را فعال کنید.
                </div>
                @include('backend.admins._permissions_matrix', ['groups' => $groups, 'admin' => null])
            </div>
            <div class="col-12">
                <button type="submit" class="btn btn-primary btn-lg rounded-pill px-5">ذخیرهٔ مدیر</button>
                <a href="{{ route('admin.managers.index') }}" class="btn btn-light btn-lg rounded-pill px-4">انصراف</a>
            </div>
        </form>
    </div>
</div>
@endsection
