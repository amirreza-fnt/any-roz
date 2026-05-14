<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Blade;
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
        Paginator::useBootstrap();

        Blade::directive('admincan', function (string $expression): string {
            return "<?php if(auth('admin')->check() && (auth('admin')->user()->is_super || auth('admin')->user()->hasPermission({$expression}))): ?>";
        });

        Blade::directive('endadmincan', function (): string {
            return '<?php endif; ?>';
        });

        Blade::directive('adminany', function (string $expression): string {
            return "<?php \$__admin = auth('admin')->user(); if(\$__admin && (\$__admin->is_super || \\App\\Support\\AdminAccess::hasAnyPermission(\$__admin, {$expression}))): ?>";
        });

        Blade::directive('endadminany', function (): string {
            return '<?php endif; ?>';
        });
    }
}
