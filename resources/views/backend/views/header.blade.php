<div class="header d-flex align-items-center justify-content-between flex-wrap">
    <ul class="navbar-nav d-flex flex-row align-items-center">
        <li class="nav-item navigation-toggler">
            <a href="#" class="nav-link" aria-label="باز و بسته کردن منو">
                <i data-feather="menu"></i>
            </a>
        </li>
        <li class="nav-item d-none d-sm-flex align-items-center ml-2">
            <a href="{{ route('admin.dashboard') }}" class="navbar-brand d-flex align-items-center mb-0 py-0">
                <img class="logo logo-sm mr-2" src="{{ asset('assets/back-end/assets/media/image/logo-sm.png') }}" alt="لوگو">
                <span class="font-weight-600 text-dark">پنل مدیریت آنی‌رز</span>
            </a>
        </li>
    </ul>
    <div class="header-right d-flex align-items-center pr-2">
        <div class="text-muted small text-right">
            <div class="d-none d-md-block">{{ auth('admin')->user()->full_name }}</div>
            <div class="opacity-75" style="font-size: 11px;">{{ now()->format('Y/m/d H:i') }}</div>
        </div>
    </div>
</div>
