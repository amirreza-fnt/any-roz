<?php

namespace App\Support;

use App\Models\Admin;

final class AdminAccess
{
    /**
     * Landing targets in priority order (dashboards first, then main modules).
     *
     * @var list<array{route: string, perm: string}>
     */
    private const LANDING_ROUTE_ORDER = [
        ['route' => 'admin.dashboard', 'perm' => 'dashboard.main.view'],
        ['route' => 'admin.dashboard.accounting', 'perm' => 'dashboard.accounting.view'],
        ['route' => 'admin.dashboard.supply', 'perm' => 'dashboard.supply.view'],
        ['route' => 'admin.dashboard.marketing', 'perm' => 'dashboard.marketing.view'],
        ['route' => 'admin.category.index', 'perm' => 'categories.view'],
        ['route' => 'admin.type-of-weights.index', 'perm' => 'type_of_weights.view'],
        ['route' => 'admin.products.index', 'perm' => 'products.view'],
        ['route' => 'admin.orders.index', 'perm' => 'orders.view'],
        ['route' => 'admin.orders.supply', 'perm' => 'orders.supply.view'],
        ['route' => 'admin.articles.index', 'perm' => 'articles.view'],
        ['route' => 'admin.gift-codes.index', 'perm' => 'gift_codes.view'],
        ['route' => 'admin.discount-codes.index', 'perm' => 'discount_codes.view'],
        ['route' => 'admin.contact-settings.edit', 'perm' => 'contact_settings.view'],
        ['route' => 'admin.shipping-configs.index', 'perm' => 'shipping_configs.view'],
        ['route' => 'admin.users.index', 'perm' => 'users.view'],
        ['route' => 'admin.technical-backup.index', 'perm' => 'technical_backup.view'],
        ['route' => 'admin.marketing.buyers.index', 'perm' => 'marketing.buyers.view'],
        ['route' => 'admin.marketing.sales.index', 'perm' => 'marketing.sales.view'],
        ['route' => 'admin.accounting.marketing-sales.index', 'perm' => 'accounting.marketing_sales.view_list'],
    ];

    /**
     * @return list<string>
     */
    public static function allAssignableKeys(): array
    {
        $keys = [];
        foreach (config('admin_access.permission_ui', []) as $group) {
            foreach ($group['items'] ?? [] as $item) {
                if (! empty($item['key'])) {
                    $keys[] = $item['key'];
                }
            }
        }

        return array_values(array_unique($keys));
    }

    public static function permissionForRoute(?string $routeName): ?string
    {
        if ($routeName === null) {
            return null;
        }

        $map = config('admin_access.route_permissions', []);

        return array_key_exists($routeName, $map) ? $map[$routeName] : null;
    }

    /**
     * First URL the admin is allowed to open (dashboards first). Null if no permission at all.
     */
    public static function firstAccessibleUrl(Admin $admin): ?string
    {
        if ($admin->is_super) {
            return route('admin.dashboard');
        }

        foreach (self::LANDING_ROUTE_ORDER as $row) {
            if ($admin->hasPermission($row['perm'])) {
                return route($row['route']);
            }
        }

        foreach (config('admin_access.route_permissions', []) as $routeName => $perm) {
            if ($perm === '*' || $perm === null || ! is_string($routeName)) {
                continue;
            }
            if (! $admin->hasPermission($perm)) {
                continue;
            }
            try {
                return route($routeName);
            } catch (\Throwable) {
                continue;
            }
        }

        return null;
    }

    /**
     * @param  list<string>  $permissions
     */
    public static function hasAnyPermission(Admin $admin, array $permissions): bool
    {
        if ($admin->is_super) {
            return true;
        }

        foreach ($permissions as $perm) {
            if ($admin->hasPermission($perm)) {
                return true;
            }
        }

        return false;
    }
}
