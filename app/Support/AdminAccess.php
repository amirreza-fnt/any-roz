<?php

namespace App\Support;

final class AdminAccess
{
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
}
