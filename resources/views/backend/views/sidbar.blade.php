<div class="navigation-menu-body">
    <ul>
        <li class="navigation-divider">صفحات : </li>

        @admincan('dashboard.main.view')
        <li>
            <a class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                <i class="nav-link-icon" data-feather="bar-chart-2"></i>
                <span>داشبورد مدیریت</span>
            </a>
        </li>
        @endadmincan

        @admincan('dashboard.supply.view')
        <li>
            <a class="{{ request()->routeIs('admin.orders.supply') || request()->routeIs('admin.orders.supply.show') ? 'active' : '' }}" href="{{ route('admin.orders.supply') }}">
                <i class="nav-link-icon" data-feather="truck"></i>
                <span>داشبورد تأمین</span>
            </a>
        </li>
        @endadmincan

        @adminany(['dashboard.accounting.view', 'accounting.marketing_sales.view_list', 'accounting.marketing_sales.view_detail', 'accounting.marketing_sales.approve', 'accounting.marketing_sales.reject'])
        <li class="{{ request()->routeIs('admin.dashboard.accounting') || request()->routeIs('admin.accounting.*') ? 'open' : '' }}">
            <a href="javascript:;">
                <i class="nav-link-icon" data-feather="pie-chart"></i>
                <span>حسابداری</span>
            </a>
            <ul>
                @admincan('dashboard.accounting.view')
                <li>
                    <a class="{{ request()->routeIs('admin.dashboard.accounting') ? 'active' : '' }}" href="{{ route('admin.dashboard.accounting') }}">داشبورد حسابداری</a>
                </li>
                @endadmincan
                @adminany(['accounting.marketing_sales.view_list', 'accounting.marketing_sales.view_detail', 'accounting.marketing_sales.approve', 'accounting.marketing_sales.reject'])
                <li>
                    <a class="{{ request()->routeIs('admin.accounting.marketing-sales.*') ? 'active' : '' }}" href="{{ route('admin.accounting.marketing-sales.index') }}">بررسی فروش بازاریابان</a>
                </li>
                @endadminany
            </ul>
        </li>
        @endadminany

        @adminany(['dashboard.marketing.view', 'marketing.buyers.view', 'marketing.buyers.create', 'marketing.sales.view', 'marketing.sales.create'])
        <li class="{{ request()->routeIs('admin.dashboard.marketing') || request()->routeIs('admin.marketing.*') ? 'open' : '' }}">
            <a href="javascript:;">
                <i class="nav-link-icon" data-feather="trending-up"></i>
                <span>بازاریابی</span>
            </a>
            <ul>
                @admincan('dashboard.marketing.view')
                <li>
                    <a class="{{ request()->routeIs('admin.dashboard.marketing') ? 'active' : '' }}" href="{{ route('admin.dashboard.marketing') }}">داشبورد بازاریابی</a>
                </li>
                @endadmincan
                @adminany(['marketing.buyers.view', 'marketing.buyers.create'])
                <li>
                    <a class="{{ request()->routeIs('admin.marketing.buyers.*') ? 'active' : '' }}" href="{{ route('admin.marketing.buyers.index') }}">خریداران (CRM)</a>
                </li>
                @endadminany
                @admincan('marketing.sales.create')
                <li>
                    <a class="{{ request()->routeIs('admin.marketing.sales.create') ? 'active' : '' }}" href="{{ route('admin.marketing.sales.create') }}">ثبت فروش</a>
                </li>
                @endadmincan
                @admincan('marketing.sales.view')
                <li>
                    <a class="{{ request()->routeIs('admin.marketing.sales.index') || request()->routeIs('admin.marketing.sales.show') ? 'active' : '' }}" href="{{ route('admin.marketing.sales.index') }}">فروش‌های من</a>
                </li>
                @endadmincan
            </ul>
        </li>
        @endadminany

        @adminany(['categories.view', 'categories.create'])
        <li class="{{ request()->routeIs('admin.category.*') ? 'open' : '' }}">
            <a href="javascript:;">
                <i class="nav-link-icon" data-feather="layers"></i>
                <span>دسته‌بندی‌ها</span>
            </a>
            <ul>
                @admincan('categories.create')
                <li>
                    <a class="{{ request()->routeIs('admin.category.create') ? 'active' : '' }}" href="{{ route('admin.category.create') }}">افزودن دسته‌بندی</a>
                </li>
                @endadmincan
                @admincan('categories.view')
                <li>
                    <a class="{{ request()->routeIs('admin.category.index') ? 'active' : '' }}" href="{{ route('admin.category.index') }}">نمایش همهٔ دسته‌ها</a>
                </li>
                @endadmincan
            </ul>
        </li>
        @endadminany

        @adminany(['type_of_weights.view', 'type_of_weights.create'])
        <li class="{{ request()->routeIs('admin.type-of-weights.*') ? 'open' : '' }}">
            <a href="javascript:;">
                <i class="nav-link-icon" data-feather="anchor"></i>
                <span>انواع وزن</span>
            </a>
            <ul>
                @admincan('type_of_weights.create')
                <li>
                    <a class="{{ request()->routeIs('admin.type-of-weights.create') ? 'active' : '' }}" href="{{ route('admin.type-of-weights.create') }}">افزودن نوع وزن</a>
                </li>
                @endadmincan
                @admincan('type_of_weights.view')
                <li>
                    <a class="{{ request()->routeIs('admin.type-of-weights.index') ? 'active' : '' }}" href="{{ route('admin.type-of-weights.index') }}">نمایش همه</a>
                </li>
                @endadmincan
            </ul>
        </li>
        @endadminany

        @adminany(['products.view', 'products.create'])
        <li class="{{ request()->routeIs('admin.products.*') ? 'open' : '' }}">
            <a href="javascript:;">
                <i class="nav-link-icon" data-feather="package"></i>
                <span>محصولات</span>
            </a>
            <ul>
                @admincan('products.create')
                <li>
                    <a class="{{ request()->routeIs('admin.products.create') ? 'active' : '' }}" href="{{ route('admin.products.create') }}">افزودن محصول</a>
                </li>
                @endadmincan
                @admincan('products.view')
                <li>
                    <a class="{{ request()->routeIs('admin.products.index') ? 'active' : '' }}" href="{{ route('admin.products.index') }}">لیست محصولات</a>
                </li>
                @endadmincan
            </ul>
        </li>
        @endadminany

        @admincan('orders.view')
        <li class="{{ request()->routeIs('admin.orders.index') || request()->routeIs('admin.orders.show') || request()->routeIs('admin.orders.update-status') || request()->routeIs('admin.orders.toggle-supply') ? 'open' : '' }}">
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
        @endadmincan

        @adminany(['users.view', 'users.create'])
        <li class="{{ request()->routeIs('admin.users.*') ? 'open' : '' }}">
            <a href="javascript:;">
                <i class="nav-link-icon" data-feather="users"></i>
                <span>کاربران</span>
            </a>
            <ul>
                @admincan('users.create')
                <li>
                    <a class="{{ request()->routeIs('admin.users.create') ? 'active' : '' }}" href="{{ route('admin.users.create') }}">افزودن کاربر</a>
                </li>
                @endadmincan
                @admincan('users.view')
                <li>
                    <a class="{{ request()->routeIs('admin.users.index') ? 'active' : '' }}" href="{{ route('admin.users.index') }}">لیست کاربران</a>
                </li>
                @endadmincan
            </ul>
        </li>
        @endadminany

        @adminany(['articles.view', 'articles.create'])
        <li class="{{ request()->routeIs('admin.articles.*') ? 'open' : '' }}">
            <a href="javascript:;">
                <i class="nav-link-icon" data-feather="book-open"></i>
                <span>مقالات</span>
            </a>
            <ul>
                @admincan('articles.create')
                <li>
                    <a class="{{ request()->routeIs('admin.articles.create') ? 'active' : '' }}" href="{{ route('admin.articles.create') }}">افزودن مقاله</a>
                </li>
                @endadmincan
                @admincan('articles.view')
                <li>
                    <a class="{{ request()->routeIs('admin.articles.index') ? 'active' : '' }}" href="{{ route('admin.articles.index') }}">لیست مقالات</a>
                </li>
                @endadmincan
            </ul>
        </li>
        @endadminany

        @adminany(['gift_codes.view', 'gift_codes.create'])
        <li class="{{ request()->routeIs('admin.gift-codes.*') ? 'open' : '' }}">
            <a href="javascript:;">
                <i class="nav-link-icon" data-feather="award"></i>
                <span>کدهای هدیه</span>
            </a>
            <ul>
                @admincan('gift_codes.create')
                <li>
                    <a class="{{ request()->routeIs('admin.gift-codes.create') ? 'active' : '' }}" href="{{ route('admin.gift-codes.create') }}">افزودن کد هدیه</a>
                </li>
                @endadmincan
                @admincan('gift_codes.view')
                <li>
                    <a class="{{ request()->routeIs('admin.gift-codes.index') ? 'active' : '' }}" href="{{ route('admin.gift-codes.index') }}">لیست کدهای هدیه</a>
                </li>
                @endadmincan
            </ul>
        </li>
        @endadminany

        @adminany(['discount_codes.view', 'discount_codes.create'])
        <li class="{{ request()->routeIs('admin.discount-codes.*') ? 'open' : '' }}">
            <a href="javascript:;">
                <i class="nav-link-icon" data-feather="tag"></i>
                <span>کدهای تخفیف</span>
            </a>
            <ul>
                @admincan('discount_codes.create')
                <li>
                    <a class="{{ request()->routeIs('admin.discount-codes.create') ? 'active' : '' }}" href="{{ route('admin.discount-codes.create') }}">افزودن کد تخفیف</a>
                </li>
                @endadmincan
                @admincan('discount_codes.view')
                <li>
                    <a class="{{ request()->routeIs('admin.discount-codes.index') ? 'active' : '' }}" href="{{ route('admin.discount-codes.index') }}">لیست کدهای تخفیف</a>
                </li>
                @endadmincan
            </ul>
        </li>
        @endadminany

        @adminany(['shipping_configs.view', 'shipping_configs.create'])
        <li class="{{ request()->routeIs('admin.shipping-configs.*') ? 'open' : '' }}">
            <a href="javascript:;">
                <i class="nav-link-icon" data-feather="truck"></i>
                <span>روش ارسال</span>
            </a>
            <ul>
                @admincan('shipping_configs.create')
                <li>
                    <a class="{{ request()->routeIs('admin.shipping-configs.create') ? 'active' : '' }}" href="{{ route('admin.shipping-configs.create') }}">افزودن روش</a>
                </li>
                @endadmincan
                @admincan('shipping_configs.view')
                <li>
                    <a class="{{ request()->routeIs('admin.shipping-configs.index') ? 'active' : '' }}" href="{{ route('admin.shipping-configs.index') }}">لیست روش‌ها</a>
                </li>
                @endadmincan
            </ul>
        </li>
        @endadminany

        @admincan('contact_settings.view')
        <li>
            <a class="{{ request()->routeIs('admin.contact-settings.*') ? 'active' : '' }}" href="{{ route('admin.contact-settings.edit') }}">
                <i class="nav-link-icon" data-feather="phone"></i>
                <span>مدیریت ارتباطات</span>
            </a>
        </li>
        @endadmincan

        @admincan('technical_backup.view')
        <li>
            <a class="{{ request()->routeIs('admin.technical-backup.*') ? 'active' : '' }}" href="{{ route('admin.technical-backup.index') }}">
                <i class="nav-link-icon" data-feather="download"></i>
                <span>پشتیبان‌گیری فنی</span>
            </a>
        </li>
        @endadmincan

        @if (auth('admin')->user()->is_super)
        <li>
            <a class="{{ request()->routeIs('admin.managers.*') ? 'active' : '' }}" href="{{ route('admin.managers.index') }}">
                <i class="nav-link-icon" data-feather="shield"></i>
                <span>مدیران و دسترسی‌ها</span>
            </a>
        </li>
        @endif
    </ul>
</div>
