<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    @include('backend.views.meta')
    <title>ورود مدیران</title>
    @include('backend.views.links')
    <style>
        html, body { height: 100%; margin: 0; }
        .admin-auth-page {
            min-height: 100vh;
            width: 100%;
            display: flex;
            flex-direction: column;
            background: radial-gradient(1200px 600px at 20% 10%, rgba(56, 189, 248, 0.18), transparent 55%),
                        radial-gradient(900px 500px at 90% 80%, rgba(129, 140, 248, 0.2), transparent 50%),
                        linear-gradient(165deg, #0b1220 0%, #111c2f 45%, #0b1220 100%);
        }
        .admin-auth-center {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: clamp(1rem, 4vw, 2.5rem);
            width: 100%;
            box-sizing: border-box;
        }
        .admin-auth-card {
            width: 100%;
            max-width: 440px;
            border-radius: 1rem;
            border: 1px solid rgba(255, 255, 255, 0.12);
            background: rgba(255, 255, 255, 0.97);
            box-shadow: 0 24px 60px rgba(0, 0, 0, 0.35);
        }
        .admin-auth-brand {
            font-weight: 800;
            letter-spacing: -0.02em;
            color: #0f172a;
        }
    </style>
</head>
<body class="admin-auth-page">
<div class="admin-auth-center">
    <div class="admin-auth-card card border-0">
        <div class="card-body p-4 p-md-5">
            <div class="text-center mb-4">
                <img src="{{ asset('assets/back-end/assets/media/image/logo-sm.png') }}" alt="" class="mb-3" width="48" height="48">
                <div class="admin-auth-brand h4 mb-1">ورود به پنل مدیریت</div>
                <p class="text-muted small mb-0">شماره موبایل و رمز عبور را وارد کنید</p>
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
                <button type="submit" class="btn btn-primary btn-block rounded-pill py-2 font-weight-bold">ورود</button>
            </form>
        </div>
    </div>
</div>
</body>
</html>
