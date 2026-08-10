@extends('backend.views.view')

@section('main')
<div class="main-content">
    <div class="container">
        <div class="page-header">
            <h4>افزودن همکار</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ admin_home_url() }}">خانه</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.colleagues.index') }}">همکاران</a></li>
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

        <form method="post" action="{{ route('admin.colleagues.store') }}" class="row">
            @csrf
            <div class="col-lg-4 mb-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white font-weight-bold">اطلاعات هویتی و ورود</div>
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
                            <input type="text" name="position" class="form-control" value="{{ old('position') }}" placeholder="مثلاً همکار تأمین">
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
                        <div class="custom-control custom-checkbox mb-0">
                            <input type="checkbox" class="custom-control-input" name="is_active" id="is_active" value="1" {{ old('is_active', '1') === '1' || old('is_active') === true ? 'checked' : '' }}>
                            <label class="custom-control-label" for="is_active">حساب فعال باشد</label>
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
            <div class="col-lg-4 mb-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white font-weight-bold">اطلاعات تحویل خرید کلی</div>
                    <div class="card-body">
                        <div class="form-group">
                            <label>نام فروشگاه / محل</label>
                            <input type="text" name="store_name" class="form-control" value="{{ old('store_name') }}" maxlength="255">
                        </div>
                        <div class="form-group">
                            <label>آدرس</label>
                            <textarea name="address" class="form-control" rows="3" maxlength="2000">{{ old('address') }}</textarea>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label>استان</label>
                                <select name="province_id" id="colleague-province" class="form-control">
                                    <option value="">— انتخاب —</option>
                                    @foreach ($provinces as $p)
                                        <option value="{{ $p->id }}" @selected((int) old('province_id') === (int) $p->id)>{{ $p->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-6">
                                <label>شهر</label>
                                <select name="city_id" id="colleague-city" class="form-control">
                                    <option value="">— انتخاب —</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group mb-0">
                            <label>کد پستی</label>
                            <input type="text" name="postal_code" class="form-control text-left" dir="ltr" value="{{ old('postal_code') }}" maxlength="20">
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-12 mb-3">
                <div class="alert alert-info border-0 shadow-sm small">
                    دسترسی‌های «پنل همکار» (ثبت و مشاهدهٔ پیش‌فاکتور خرید کلی) به‌صورت پیش‌فرض فعال است؛ موارد دیگر را در صورت نیاز تیک بزنید.
                </div>
                @include('backend.admins._permissions_matrix', ['groups' => $groups, 'admin' => $default])
            </div>
            <div class="col-12">
                <button type="submit" class="btn btn-primary btn-lg rounded-pill px-5">ذخیرهٔ همکار</button>
                <a href="{{ route('admin.colleagues.index') }}" class="btn btn-light btn-lg rounded-pill px-4">انصراف</a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    (function () {
        var citiesMap = {!! json_encode($cities->groupBy('province_id')->map(fn ($group) => $group->map(fn ($c) => ['id' => (int) $c->id, 'name' => $c->name])->values())) !!};

        function fillCities() {
            var pid = document.getElementById('colleague-province').value;
            var sel = document.getElementById('colleague-city');
            var prev = sel.value;
            sel.innerHTML = '';
            var opt = document.createElement('option');
            opt.value = '';
            opt.textContent = '— انتخاب —';
            sel.appendChild(opt);
            (citiesMap[pid] || []).forEach(function (c) {
                var o = document.createElement('option');
                o.value = c.id;
                o.textContent = c.name;
                if (String(c.id) === String(prev)) { o.selected = true; }
                sel.appendChild(o);
            });
        }

        document.getElementById('colleague-province').addEventListener('change', fillCities);

        @if((int) old('province_id'))
        fillCities();
        @endif
    })();
</script>
@endpush
