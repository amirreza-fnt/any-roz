<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    @include('backend.views.meta')
    <title>ورود مدیران</title>
    @include('backend.views.links')
    <style>
        .auth-wrap { min-height: 100vh; display: flex; align-items: center; justify-content: center; background: linear-gradient(135deg, #0f172a 0%, #1e293b 45%, #0f172a 100%); padding: 2rem; }
        .auth-card { max-width: 420px; width: 100%; border-radius: 1rem; box-shadow: 0 25px 50px -12px rgba(0,0,0,.45); border: 1px solid rgba(255,255,255,.08); background: rgba(255,255,255,.98); }
        .auth-card .card-body { padding: 2rem; }
        .auth-brand { font-weight: 800; letter-spacing: -0.02em; color: #0f172a; }
    </style>
</head>
<body>
<div class="auth-wrap">
    <div class="auth-card card border-0">
        <div class="card-body">
            <div class="text-center mb-4">
                <div class="auth-brand h4 mb-1">ورود به پنل مدیریت</div>
                <p class="text-muted small mb-0">شماره موبایل و رمز عبور مدیر را وارد کنید</p>
            </div>
            @if ($errors->any())
                <div class="alert alert-danger small">{{ $errors->first() }}</div>
            @endif
            <form method="post" action="{{ route('admin.login') }}">
                @csrf
                <div class="form-group">
                    <label>شماره موبایل</label>
                    <input type="text" name="phone" value="{{ old('phone') }}" class="form-control text-left" dir="ltr" autocomplete="username" required autofocus>
                </div>
                <div class="form-group">
                    <label>رمز عبور</label>
                    <input type="password" name="password" class="form-control" autocomplete="current-password" required>
                </div>
                <div class="custom-control custom-checkbox mb-3">
                    <input type="checkbox" class="custom-control-input" name="remember" id="remember" value="1">
                    <label class="custom-control-label" for="remember">مرا به خاطر بسپار</label>
                </div>
                <button type="submit" class="btn btn-primary btn-block rounded-pill py-2">ورود</button>
            </form>
        </div>
    </div>
</div>
</body>
</html>
