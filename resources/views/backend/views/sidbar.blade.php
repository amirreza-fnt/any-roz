<div class="navigation-menu-body">
        <ul>
            <li class="navigation-divider">صفحات : </li>
            <li class="open">
                <a href="{{ route('admin.dashboard') }}">
                    <i class="nav-link-icon" data-feather="bar-chart-2"></i>
                    <span>داشبورد</span>
                </a>
                <ul>
                    <li><a class="active" href="{{ route('admin.dashboard') }}">داشبورد مدیریت</a></li>
                    <li><a href="dashboard-two.html">داشبورد حسابداری</a></li>
                </ul>
            </li>
            <li>
                <a href="javascript:;">
                    <i class="nav-link-icon" data-feather="bar-chart-2"></i>
                    <span>دسته بندی ها</span>
                </a>
                <ul>
                    <li><a class="active" href="{{ route('admin.category.create') }}"> افزودن دسته بندی </a></li>
                    <li><a class="active" href="{{ route('admin.category.index') }}"> نمایش دسته بندی </a></li>
                </ul>
            </li>
        </ul>
    </div>