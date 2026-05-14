<?php

/**
 * قطعهٔ آماده برای ادغام در config/admin_access.php پروژهٔ کامل.
 *
 * ۱) تمام کلیدهای route_permissions زیر را به آرایهٔ route_permissions اضافه کنید.
 * ۲) گروه permission_ui را به آرایهٔ permission_ui اضافه کنید (یا با گروه «حسابداری» موجود ادغام کنید).
 */
return [
    'route_permissions' => [
        'admin.dashboard.accounting' => 'dashboard.accounting.view',
        'admin.accounting.professional.index' => 'accounting.professional.view',
        'admin.accounting.professional.export' => 'accounting.professional.export',
        'admin.accounting.site-sales.index' => 'accounting.site_sales.view',
        'admin.accounting.site-sales.export' => 'accounting.site_sales.export',
    ],
    'permission_ui' => [
        [
            'id' => 'accounting_suite',
            'label' => 'حسابداری — گزارش‌ها و فروش سایت',
            'items' => [
                ['key' => 'accounting.professional.view', 'label' => 'برنامهٔ جامع حسابداری — مشاهده'],
                ['key' => 'accounting.professional.export', 'label' => 'برنامهٔ جامع حسابداری — خروجی CSV'],
                ['key' => 'accounting.site_sales.view', 'label' => 'حسابداری فروش سایت — مشاهده'],
                ['key' => 'accounting.site_sales.export', 'label' => 'حسابداری فروش سایت — خروجی CSV'],
            ],
        ],
    ],
];
