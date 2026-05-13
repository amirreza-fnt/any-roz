<div class="navigation-menu-body">
    <ul>
        <li class="navigation-divider">صفحات : </li>
        <li>
            <a class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                <i class="nav-link-icon" data-feather="bar-chart-2"></i>
                <span>داشبورد مدیریت</span>
            </a>
        </li>
        <li>
            <a class="{{ request()->routeIs('admin.dashboard.accounting') ? 'active' : '' }}" href="{{ route('admin.dashboard.accounting') }}">
                <i class="nav-link-icon" data-feather="pie-chart"></i>
                <span>داشبورد حسابداری</span>
            </a>
        </li>
        <li>
            <a class="{{ request()->routeIs('admin.dashboard.marketing') ? 'active' : '' }}" href="{{ route('admin.dashboard.marketing') }}">
                <i class="nav-link-icon" data-feather="trending-up"></i>
                <span>داشبورد بازاریابی</span>
            </a>
        </li>
        <li>
            <a class="{{ request()->routeIs('admin.orders.supply') || request()->routeIs('admin.orders.supply.show') ? 'active' : '' }}" href="{{ route('admin.orders.supply') }}">
                <i class="nav-link-icon" data-feather="truck"></i>
                <span>داشبورد تأمین</span>
            </a>
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
        <li class="{{ request()->routeIs('admin.products.*') ? 'open' : '' }}">
            <a href="javascript:;">
                <i class="nav-link-icon" data-feather="package"></i>
                <span>محصولات</span>
            </a>
            <ul>
                <li>
                    <a class="{{ request()->routeIs('admin.products.create') ? 'active' : '' }}" href="{{ route('admin.products.create') }}">افزودن محصول</a>
                </li>
                <li>
                    <a class="{{ request()->routeIs('admin.products.index') ? 'active' : '' }}" href="{{ route('admin.products.index') }}">لیست محصولات</a>
                </li>
            </ul>
        </li>
        <li class="{{ request()->routeIs('admin.orders.*') ? 'open' : '' }}">
            <a href="javascript:;">
                <i class="nav-link-icon" data-feather="file-text"></i>
                <span>فاکتورها</span>
            </a>
            <ul>
                <li>
                    <a class="{{ request()->routeIs('admin.orders.index') ? 'active' : '' }}" href="{{ route('admin.orders.index') }}">همهٔ فاکتورها</a>
                </li>
            </ul>
        </li>
        <li class="{{ request()->routeIs('admin.users.*') ? 'open' : '' }}">
            <a href="javascript:;">
                <i class="nav-link-icon" data-feather="users"></i>
                <span>کاربران</span>
            </a>
            <ul>
                <li>
                    <a class="{{ request()->routeIs('admin.users.create') ? 'active' : '' }}" href="{{ route('admin.users.create') }}">افزودن کاربر</a>
                </li>
                <li>
                    <a class="{{ request()->routeIs('admin.users.index') ? 'active' : '' }}" href="{{ route('admin.users.index') }}">لیست کاربران</a>
                </li>
            </ul>
        </li>
        <li class="{{ request()->routeIs('admin.articles.*') ? 'open' : '' }}">
            <a href="javascript:;">
                <i class="nav-link-icon" data-feather="book-open"></i>
                <span>مقالات</span>
            </a>
            <ul>
                <li>
                    <a class="{{ request()->routeIs('admin.articles.create') ? 'active' : '' }}" href="{{ route('admin.articles.create') }}">افزودن مقاله</a>
                </li>
                <li>
                    <a class="{{ request()->routeIs('admin.articles.index') ? 'active' : '' }}" href="{{ route('admin.articles.index') }}">لیست مقالات</a>
                </li>
            </ul>
        </li>
        <li class="{{ request()->routeIs('admin.gift-codes.*') ? 'open' : '' }}">
            <a href="javascript:;">
                <i class="nav-link-icon" data-feather="award"></i>
                <span>کدهای هدیه</span>
            </a>
            <ul>
                <li>
                    <a class="{{ request()->routeIs('admin.gift-codes.create') ? 'active' : '' }}" href="{{ route('admin.gift-codes.create') }}">افزودن کد هدیه</a>
                </li>
                <li>
                    <a class="{{ request()->routeIs('admin.gift-codes.index') ? 'active' : '' }}" href="{{ route('admin.gift-codes.index') }}">لیست کدهای هدیه</a>
                </li>
            </ul>
        </li>
        <li class="{{ request()->routeIs('admin.discount-codes.*') ? 'open' : '' }}">
            <a href="javascript:;">
                <i class="nav-link-icon" data-feather="tag"></i>
                <span>کدهای تخفیف</span>
            </a>
            <ul>
                <li>
                    <a class="{{ request()->routeIs('admin.discount-codes.create') ? 'active' : '' }}" href="{{ route('admin.discount-codes.create') }}">افزودن کد تخفیف</a>
                </li>
                <li>
                    <a class="{{ request()->routeIs('admin.discount-codes.index') ? 'active' : '' }}" href="{{ route('admin.discount-codes.index') }}">لیست کدهای تخفیف</a>
                </li>
            </ul>
        </li>
        <li>
            <a class="{{ request()->routeIs('admin.contact-settings.*') ? 'active' : '' }}" href="{{ route('admin.contact-settings.edit') }}">
                <i class="nav-link-icon" data-feather="phone"></i>
                <span>مدیریت ارتباطات</span>
            </a>
        </li>
        <li class="{{ request()->routeIs('admin.shipping-configs.*') ? 'open' : '' }}">
            <a href="javascript:;">
                <i class="nav-link-icon" data-feather="truck"></i>
                <span>روش ارسال</span>
            </a>
            <ul>
                <li>
                    <a class="{{ request()->routeIs('admin.shipping-configs.create') ? 'active' : '' }}" href="{{ route('admin.shipping-configs.create') }}">افزودن روش</a>
                </li>
                <li>
                    <a class="{{ request()->routeIs('admin.shipping-configs.index') ? 'active' : '' }}" href="{{ route('admin.shipping-configs.index') }}">لیست روش‌ها</a>
                </li>
            </ul>
        </li>
        <li>
            <a class="{{ request()->routeIs('admin.technical-backup.*') ? 'active' : '' }}" href="{{ route('admin.technical-backup.index') }}">
                <i class="nav-link-icon" data-feather="download"></i>
                <span>پشتیبان‌گیری فنی</span>
            </a>
        </li>
    </ul>
</div>
