<div class="navigation-menu-body">
    <ul>
        <li class="navigation-divider">صفحات : </li>
        <li class="open">
            <a href="{{ route('admin.dashboard') }}">
                <i class="nav-link-icon" data-feather="bar-chart-2"></i>
                <span>داشبورد</span>
            </a>
            <ul>
                <li><a class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">داشبورد مدیریت</a></li>
                <li><a class="{{ request()->routeIs('admin.dashboard.accounting') || request()->routeIs('admin.accounting.*') ? 'active' : '' }}" href="{{ route('admin.dashboard.accounting') }}">داشبورد حسابداری</a></li>
            </ul>
        </li>
        <li class="{{ request()->routeIs('admin.accounting.*') ? 'open' : '' }}">
            <a href="javascript:;">
                <i class="nav-link-icon" data-feather="pie-chart"></i>
                <span>حسابداری</span>
            </a>
            <ul>
                <li><a class="{{ request()->routeIs('admin.dashboard.accounting') ? 'active' : '' }}" href="{{ route('admin.dashboard.accounting') }}">مرکز یکپارچه</a></li>
                <li><a class="{{ request()->routeIs('admin.accounting.professional.*') ? 'active' : '' }}" href="{{ route('admin.accounting.professional.index') }}">برنامهٔ جامع حسابداری</a></li>
                <li><a class="{{ request()->routeIs('admin.accounting.site-sales.*') ? 'active' : '' }}" href="{{ route('admin.accounting.site-sales.index') }}">فروش سایت</a></li>
                @if(Route::has('admin.accounting.marketing-sales.index'))
                    <li><a class="{{ request()->routeIs('admin.accounting.marketing-sales.*') ? 'active' : '' }}" href="{{ route('admin.accounting.marketing-sales.index') }}">فروش بازاریابان</a></li>
                @endif
            </ul>
        </li>
        <li>
            <a href="javascript:;">
                <i class="nav-link-icon" data-feather="layers"></i>
                <span>دسته بندی ها</span>
            </a>
            <ul>
                <li><a class="{{ request()->routeIs('admin.category.create') ? 'active' : '' }}" href="{{ route('admin.category.create') }}"> افزودن دسته بندی </a></li>
                <li><a class="{{ request()->routeIs('admin.category.index') ? 'active' : '' }}" href="{{ route('admin.category.index') }}"> نمایش دسته بندی </a></li>
            </ul>
        </li>
    </ul>
</div>
