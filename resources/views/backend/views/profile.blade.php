<!-- begin::logo -->
    <div id="logo">
        <a href="{{ admin_home_url() }}">
            <img style="border-radius: 5px" class="logo" src="{{ asset('assets/back-end/assets/media/image/logo.png') }}" width="80" alt="logo">
            <img style="border-radius: 5px" class="logo-sm" src="{{ asset('assets/back-end/assets/media/image/logo-sm.png') }}" width="80" alt="small logo">
            <img style="border-radius: 5px" class="logo-dark" src="{{ asset('assets/back-end/assets/media/image/logo-dark.png') }}" width="80" alt="small logo">
        </a>
    </div>
    <!-- end::logo -->
<header class="navigation-header">
    <figure class="avatar avatar-state-success">
        <img src="{{ asset('assets/back-end/assets/media/image/image1.png') }}" class="rounded-circle" alt="image">
    </figure>
    <div>
        <h5>{{ auth('admin')->user()->full_name }}</h5>
        <p class="text-muted line-height-20 m-b-25">{{ auth('admin')->user()->position ?: 'مدیر' }}</p>
        <ul class="nav">
            <li class="nav-item">
                <a href="{{ admin_home_url() }}" class="btn nav-link bg-info-bright" title="داشبورد" data-toggle="tooltip">
                    <i data-feather="home"></i>
                </a>
            </li>
            <li class="nav-item">
                <form action="{{ route('admin.logout') }}" method="post" class="d-inline">
                    @csrf
                    <button type="submit" class="btn nav-link bg-danger-bright border-0" title="خروج" data-toggle="tooltip">
                        <i data-feather="log-out"></i>
                    </button>
                </form>
            </li>
        </ul>
    </div>
</header>
