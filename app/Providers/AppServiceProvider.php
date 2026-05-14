<?php

namespace App\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\File;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Blade::if('admincan', static function (?string $permission): bool {
            if ($permission === null || $permission === '') {
                return false;
            }
            $admin = auth('admin')->user();
            if (! $admin) {
                return false;
            }

            return $admin->is_super || $admin->hasPermission($permission);
        });

        Blade::if('adminany', static function ($permissions): bool {
            if (! is_array($permissions)) {
                return false;
            }
            $admin = auth('admin')->user();
            if (! $admin) {
                return false;
            }
            if ($admin->is_super) {
                return true;
            }
            foreach ($permissions as $p) {
                if ($admin->hasPermission($p)) {
                    return true;
                }
            }

            return false;
        });

        foreach ([
            public_path('uploads'),
            public_path('uploads/images/category'),
            public_path('uploads/images/product'),
            public_path('uploads/images/article'),
        ] as $dir) {
            File::ensureDirectoryExists($dir);
        }
    }
}
