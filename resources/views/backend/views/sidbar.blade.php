<div class="navigation-menu-body">
    <ul>
        <li class="navigation-divider">صفحات : </li>
        <li class="{{ request()->routeIs('admin.dashboard') ? 'open' : '' }}">
            <a href="{{ route('admin.dashboard') }}">
                <i class="nav-link-icon" data-feather="bar-chart-2"></i>
                <span>داشبورد</span>
            </a>
            <ul>
                <li>
                    <a class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">داشبورد مدیریت</a>
                </li>
                <li><a href="dashboard-two.html">داشبورد حسابداری</a></li>
            </ul>
        </li>
        <li class="{{ request()->routeIs('admin.category.*') ? 'open' : '' }}">
            <a href="javascript:;">
                <i class="nav-link-icon" data-feather="layers"></i>
                <span>دسته‌بندی‌ها</span>
            </a>
            <ul>
                <li>
                    <a class="{{ request()->routeIs('admin.category.create') ? 'active' : '' }}" href="{{ route('admin.category.create') }}">افزودن دسته‌بندی</a>
                </li>
                <li>
                    <a class="{{ request()->routeIs('admin.category.index') ? 'active' : '' }}" href="{{ route('admin.category.index') }}">نمایش همهٔ دسته‌ها</a>
                </li>
            </ul>
        </li>
        <li class="{{ request()->routeIs('admin.type-of-weights.*') ? 'open' : '' }}">
            <a href="javascript:;">
                <i class="nav-link-icon" data-feather="anchor"></i>
                <span>انواع وزن</span>
            </a>
            <ul>
                <li>
                    <a class="{{ request()->routeIs('admin.type-of-weights.create') ? 'active' : '' }}" href="{{ route('admin.type-of-weights.create') }}">افزودن نوع وزن</a>
                </li>
                <li>
                    <a class="{{ request()->routeIs('admin.type-of-weights.index') ? 'active' : '' }}" href="{{ route('admin.type-of-weights.index') }}">نمایش همه</a>
                </li>
            </ul>
        </li>
    </ul>
</div>
