<?php

/**
 * Admin panel authorization.
 *
 * - route_permissions: Laravel route name => permission key (single).
 *   Value '*' = any authenticated admin (e.g. logout).
 *   Missing key for a route under admin.* = denied unless is_super.
 *
 * - permission_ui: nested groups for the managers form (all unchecked by default when creating).
 *
 * When you add new admin routes, append to route_permissions and add matching keys under permission_ui.
 */

return [
    'route_permissions' => [
        'admin.logout' => '*',
        'admin.login' => '*',

        'admin.dashboard' => 'dashboard.main.view',
        'admin.dashboard.accounting' => 'dashboard.accounting.view',
        'admin.dashboard.supply' => 'dashboard.supply.view',
        'admin.dashboard.marketing' => 'dashboard.marketing.view',

        'admin.category.index' => 'categories.view',
        'admin.category.create' => 'categories.create',
        'admin.category.store' => 'categories.create',
        'admin.category.show' => 'categories.view',
        'admin.category.edit' => 'categories.edit',
        'admin.category.update' => 'categories.edit',
        'admin.category.destroy' => 'categories.delete',
        'admin.category.toggle-status' => 'categories.toggle_status',

        'admin.type-of-weights.index' => 'type_of_weights.view',
        'admin.type-of-weights.create' => 'type_of_weights.create',
        'admin.type-of-weights.store' => 'type_of_weights.create',
        'admin.type-of-weights.show' => 'type_of_weights.view',
        'admin.type-of-weights.edit' => 'type_of_weights.edit',
        'admin.type-of-weights.update' => 'type_of_weights.edit',
        'admin.type-of-weights.destroy' => 'type_of_weights.delete',

        'admin.products.index' => 'products.view',
        'admin.products.create' => 'products.create',
        'admin.products.store' => 'products.create',
        'admin.products.show' => 'products.view',
        'admin.products.edit' => 'products.edit',
        'admin.products.update' => 'products.edit',
        'admin.products.destroy' => 'products.delete',
        'admin.products.toggle-status' => 'products.toggle_status',
        'admin.products.toggle-suggested' => 'products.toggle_suggested',
        'admin.products.images.store' => 'products.manage_images',
        'admin.products.images.destroy' => 'products.manage_images',

        'admin.orders.index' => 'orders.view',
        'admin.orders.show' => 'orders.view',
        'admin.orders.supply' => 'orders.supply.view',
        'admin.orders.supply.show' => 'orders.supply.view',
        'admin.orders.update-status' => 'orders.update_status',
        'admin.orders.toggle-supply' => 'orders.toggle_supply',

        'admin.articles.index' => 'articles.view',
        'admin.articles.create' => 'articles.create',
        'admin.articles.store' => 'articles.create',
        'admin.articles.show' => 'articles.view',
        'admin.articles.edit' => 'articles.edit',
        'admin.articles.update' => 'articles.edit',
        'admin.articles.destroy' => 'articles.delete',
        'admin.articles.toggle-publish' => 'articles.toggle_publish',

        'admin.gift-codes.index' => 'gift_codes.view',
        'admin.gift-codes.create' => 'gift_codes.create',
        'admin.gift-codes.store' => 'gift_codes.create',
        'admin.gift-codes.show' => 'gift_codes.view',
        'admin.gift-codes.edit' => 'gift_codes.edit',
        'admin.gift-codes.update' => 'gift_codes.edit',
        'admin.gift-codes.destroy' => 'gift_codes.delete',
        'admin.gift-codes.toggle-status' => 'gift_codes.toggle_status',

        'admin.discount-codes.index' => 'discount_codes.view',
        'admin.discount-codes.create' => 'discount_codes.create',
        'admin.discount-codes.store' => 'discount_codes.create',
        'admin.discount-codes.show' => 'discount_codes.view',
        'admin.discount-codes.edit' => 'discount_codes.edit',
        'admin.discount-codes.update' => 'discount_codes.edit',
        'admin.discount-codes.destroy' => 'discount_codes.delete',
        'admin.discount-codes.toggle-status' => 'discount_codes.toggle_status',

        'admin.contact-settings.edit' => 'contact_settings.view',
        'admin.contact-settings.update' => 'contact_settings.edit',

        'admin.shipping-configs.index' => 'shipping_configs.view',
        'admin.shipping-configs.create' => 'shipping_configs.create',
        'admin.shipping-configs.store' => 'shipping_configs.create',
        'admin.shipping-configs.show' => 'shipping_configs.view',
        'admin.shipping-configs.edit' => 'shipping_configs.edit',
        'admin.shipping-configs.update' => 'shipping_configs.edit',
        'admin.shipping-configs.toggle-status' => 'shipping_configs.toggle_status',

        'admin.technical-backup.index' => 'technical_backup.view',
        'admin.technical-backup.download' => 'technical_backup.download',
        'admin.technical-backup.progress' => 'technical_backup.download',
        'admin.technical-backup.file' => 'technical_backup.download',

        'admin.users.index' => 'users.view',
        'admin.users.create' => 'users.create',
        'admin.users.store' => 'users.create',
        'admin.users.show' => 'users.view',
        'admin.users.edit' => 'users.edit',
        'admin.users.update' => 'users.edit',
        'admin.users.toggle-active' => 'users.toggle_active',

        'admin.marketing.api.products' => 'marketing.api',
        'admin.marketing.api.products.show' => 'marketing.api',
        'admin.marketing.api.buyers' => 'marketing.api',
        'admin.marketing.api.buyers.show' => 'marketing.api',
        'admin.marketing.api.provinces' => 'marketing.api',
        'admin.marketing.api.cities' => 'marketing.api',
        'admin.marketing.api.reverse-geocode' => 'marketing.api',

        'admin.marketing.buyers.index' => 'marketing.buyers.view',
        'admin.marketing.buyers.create' => 'marketing.buyers.create',
        'admin.marketing.buyers.store' => 'marketing.buyers.create',
        'admin.marketing.buyers.show' => 'marketing.buyers.view',
        'admin.marketing.buyers.edit' => 'marketing.buyers.edit',
        'admin.marketing.buyers.update' => 'marketing.buyers.edit',
        'admin.marketing.buyers.destroy' => 'marketing.buyers.delete',

        'admin.marketing.sales.index' => 'marketing.sales.view',
        'admin.marketing.sales.create' => 'marketing.sales.create',
        'admin.marketing.sales.store' => 'marketing.sales.create',
        'admin.marketing.sales.show' => 'marketing.sales.view',
        'admin.marketing.sales.destroy' => 'marketing.sales.delete',

        'admin.accounting.marketing-sales.index' => 'accounting.marketing_sales.view_list',
        'admin.accounting.marketing-sales.show' => 'accounting.marketing_sales.view_detail',
        'admin.accounting.marketing-sales.approve' => 'accounting.marketing_sales.approve',
        'admin.accounting.marketing-sales.reject' => 'accounting.marketing_sales.reject',

        'admin.accounting.professional.index' => 'accounting.professional.view',
        'admin.accounting.professional.export' => 'accounting.professional.export',
        'admin.accounting.standalone.index' => 'accounting.journal.view',
        'admin.accounting.journal.index' => 'accounting.journal.view',
        'admin.accounting.journal.create' => 'accounting.journal.create',
        'admin.accounting.journal.store' => 'accounting.journal.create',
        'admin.accounting.journal.edit' => 'accounting.journal.edit',
        'admin.accounting.journal.update' => 'accounting.journal.edit',
        'admin.accounting.journal.destroy' => 'accounting.journal.delete',
        'admin.accounting.site-sales.index' => 'accounting.site_sales.view',
        'admin.accounting.site-sales.export' => 'accounting.site_sales.export',
    ],

    'permission_ui' => [
        [
            'id' => 'dashboard',
            'label' => 'داشبوردها',
            'items' => [
                ['key' => 'dashboard.main.view', 'label' => 'داشبورد مدیریت — مشاهده'],
                ['key' => 'dashboard.accounting.view', 'label' => 'داشبورد حسابداری — مشاهده'],
                ['key' => 'dashboard.supply.view', 'label' => 'داشبورد تأمین — مشاهده'],
                ['key' => 'dashboard.marketing.view', 'label' => 'داشبورد بازاریابی — مشاهده'],
            ],
        ],
        [
            'id' => 'categories',
            'label' => 'دسته‌بندی محصولات',
            'items' => [
                ['key' => 'categories.view', 'label' => 'مشاهدهٔ لیست و جزئیات'],
                ['key' => 'categories.create', 'label' => 'افزودن'],
                ['key' => 'categories.edit', 'label' => 'ویرایش'],
                ['key' => 'categories.delete', 'label' => 'حذف'],
                ['key' => 'categories.toggle_status', 'label' => 'تغییر وضعیت'],
            ],
        ],
        [
            'id' => 'type_of_weights',
            'label' => 'انواع وزن',
            'items' => [
                ['key' => 'type_of_weights.view', 'label' => 'مشاهده'],
                ['key' => 'type_of_weights.create', 'label' => 'افزودن'],
                ['key' => 'type_of_weights.edit', 'label' => 'ویرایش'],
                ['key' => 'type_of_weights.delete', 'label' => 'حذف'],
            ],
        ],
        [
            'id' => 'products',
            'label' => 'محصولات',
            'items' => [
                ['key' => 'products.view', 'label' => 'مشاهده'],
                ['key' => 'products.create', 'label' => 'افزودن'],
                ['key' => 'products.edit', 'label' => 'ویرایش'],
                ['key' => 'products.delete', 'label' => 'حذف'],
                ['key' => 'products.toggle_status', 'label' => 'تغییر وضعیت'],
                ['key' => 'products.toggle_suggested', 'label' => 'پیشنهادی / غیرپیشنهادی'],
                ['key' => 'products.manage_images', 'label' => 'مدیریت تصاویر محصول'],
            ],
        ],
        [
            'id' => 'orders',
            'label' => 'فاکتورها و سفارشات',
            'items' => [
                ['key' => 'orders.view', 'label' => 'مشاهدهٔ فاکتورها'],
                ['key' => 'orders.update_status', 'label' => 'تغییر وضعیت ارسال'],
                ['key' => 'orders.supply.view', 'label' => 'لیست و جزئیات فاکتورهای تأمین'],
                ['key' => 'orders.toggle_supply', 'label' => 'ارسال / بازگشت از تأمین'],
            ],
        ],
        [
            'id' => 'articles',
            'label' => 'مقالات',
            'items' => [
                ['key' => 'articles.view', 'label' => 'مشاهده'],
                ['key' => 'articles.create', 'label' => 'افزودن'],
                ['key' => 'articles.edit', 'label' => 'ویرایش'],
                ['key' => 'articles.delete', 'label' => 'حذف'],
                ['key' => 'articles.toggle_publish', 'label' => 'تغییر انتشار'],
            ],
        ],
        [
            'id' => 'gift_codes',
            'label' => 'کدهای هدیه',
            'items' => [
                ['key' => 'gift_codes.view', 'label' => 'مشاهده'],
                ['key' => 'gift_codes.create', 'label' => 'افزودن'],
                ['key' => 'gift_codes.edit', 'label' => 'ویرایش'],
                ['key' => 'gift_codes.delete', 'label' => 'حذف'],
                ['key' => 'gift_codes.toggle_status', 'label' => 'تغییر وضعیت'],
            ],
        ],
        [
            'id' => 'discount_codes',
            'label' => 'کدهای تخفیف',
            'items' => [
                ['key' => 'discount_codes.view', 'label' => 'مشاهده'],
                ['key' => 'discount_codes.create', 'label' => 'افزودن'],
                ['key' => 'discount_codes.edit', 'label' => 'ویرایش'],
                ['key' => 'discount_codes.delete', 'label' => 'حذف'],
                ['key' => 'discount_codes.toggle_status', 'label' => 'تغییر وضعیت'],
            ],
        ],
        [
            'id' => 'contact_settings',
            'label' => 'مدیریت ارتباطات',
            'items' => [
                ['key' => 'contact_settings.view', 'label' => 'مشاهده'],
                ['key' => 'contact_settings.edit', 'label' => 'ذخیره / ویرایش'],
            ],
        ],
        [
            'id' => 'shipping_configs',
            'label' => 'روش‌های ارسال',
            'items' => [
                ['key' => 'shipping_configs.view', 'label' => 'مشاهده'],
                ['key' => 'shipping_configs.create', 'label' => 'افزودن'],
                ['key' => 'shipping_configs.edit', 'label' => 'ویرایش'],
                ['key' => 'shipping_configs.toggle_status', 'label' => 'تغییر وضعیت'],
            ],
        ],
        [
            'id' => 'technical_backup',
            'label' => 'پشتیبان‌گیری فنی',
            'items' => [
                ['key' => 'technical_backup.view', 'label' => 'مشاهدهٔ صفحه'],
                ['key' => 'technical_backup.download', 'label' => 'دانلود خروجی CSV (Excel)'],
            ],
        ],
        [
            'id' => 'users',
            'label' => 'کاربران سایت (مشتریان)',
            'items' => [
                ['key' => 'users.view', 'label' => 'مشاهده'],
                ['key' => 'users.create', 'label' => 'افزودن'],
                ['key' => 'users.edit', 'label' => 'ویرایش'],
                ['key' => 'users.toggle_active', 'label' => 'فعال / غیرفعال'],
            ],
        ],
        [
            'id' => 'marketing',
            'label' => 'بازاریابی (پنل بازاریاب)',
            'items' => [
                ['key' => 'marketing.api', 'label' => 'جست‌وجو و APIهای نقشه / محصول / خریدار'],
                ['key' => 'marketing.buyers.view', 'label' => 'خریداران — مشاهده'],
                ['key' => 'marketing.buyers.create', 'label' => 'خریداران — افزودن'],
                ['key' => 'marketing.buyers.edit', 'label' => 'خریداران — ویرایش'],
                ['key' => 'marketing.buyers.delete', 'label' => 'خریداران — حذف'],
                ['key' => 'marketing.sales.view', 'label' => 'فروش‌ها — مشاهده'],
                ['key' => 'marketing.sales.create', 'label' => 'فروش‌ها — ثبت'],
                ['key' => 'marketing.sales.delete', 'label' => 'فروش‌ها — حذف (در انتظار)'],
            ],
        ],
        [
            'id' => 'accounting',
            'label' => 'حسابداری — فروش بازاریابی',
            'items' => [
                ['key' => 'accounting.marketing_sales.view_list', 'label' => 'مشاهدهٔ لیست'],
                ['key' => 'accounting.marketing_sales.view_detail', 'label' => 'مشاهدهٔ جزئیات'],
                ['key' => 'accounting.marketing_sales.approve', 'label' => 'تأیید و صدور فاکتور'],
                ['key' => 'accounting.marketing_sales.reject', 'label' => 'رد فروش'],
            ],
        ],
        [
            'id' => 'accounting_suite',
            'label' => 'حسابداری — گزارش‌ها و فروش سایت',
            'items' => [
                ['key' => 'accounting.professional.view', 'label' => 'برنامهٔ جامع حسابداری — مشاهده'],
                ['key' => 'accounting.professional.export', 'label' => 'برنامهٔ جامع حسابداری — خروجی CSV'],
                ['key' => 'accounting.journal.view', 'label' => 'حسابداری مجزا — مشاهده (داشبورد و فهرست اسناد)'],
                ['key' => 'accounting.journal.create', 'label' => 'حسابداری مجزا — ثبت سند'],
                ['key' => 'accounting.journal.edit', 'label' => 'حسابداری مجزا — ویرایش سند'],
                ['key' => 'accounting.journal.delete', 'label' => 'حسابداری مجزا — حذف سند'],
                ['key' => 'accounting.site_sales.view', 'label' => 'حسابداری فروش سایت — مشاهده'],
                ['key' => 'accounting.site_sales.export', 'label' => 'حسابداری فروش سایت — خروجی CSV'],
            ],
        ],
    ],
];
