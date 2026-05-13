<!-- begin::logo -->
    <div id="logo">
        <a href="index.html">
            <img class="logo" src="{{ asset('assets/back-end/assets/media/image/logo.png') }}" alt="logo">
            <img class="logo-sm" src="{{ asset('assets/back-end/assets/media/image/logo-sm.png') }}" alt="small logo">
            <img class="logo-dark" src="{{ asset('assets/back-end/assets/media/image/logo-dark.png') }}" alt="dark logo">
        </a>
    </div>
    <!-- end::logo -->

<header class="navigation-header">
    <figure class="avatar avatar-state-success">
        <img src="{{ asset('assets/back-end/assets/media/image/user/man_avatar3.jpg') }}" class="rounded-circle" alt="image">
    </figure>
    <div>
        <h5>جان اسنو</h5>
        <p class="text-muted line-height-20 m-b-25">مدیر</p>
        <ul class="nav">
            <li class="nav-item">
                <a href="profile.html" class="btn nav-link bg-info-bright" title="پروفایل" data-toggle="tooltip">
                    <i data-feather="user"></i>
                </a>
            </li>
            <li class="nav-item">
                <a href="#" class="btn nav-link bg-success-bright" title="تنظیمات" data-toggle="tooltip">
                    <i data-feather="settings"></i>
                </a>
            </li>
            <li class="nav-item">
                <a href="login.html" class="btn nav-link bg-danger-bright" title="خروج" data-toggle="tooltip">
                    <i data-feather="log-out"></i>
                </a>
            </li>
        </ul>
    </div>
</header>