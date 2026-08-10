<?php

namespace App\Providers;

use App\Models\Admin;
use App\Models\ColleagueMessage;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\View\View as ViewContract;

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

        View::composer('backend.views.sidbar', function (ViewContract $view) {
            $admin = auth('admin')->user();
            $chatUnread = 0;

            if ($admin) {
                if ($admin->isColleagueSupport()) {
                    $chatUnread = ColleagueMessage::query()
                        ->where('sender_type', ColleagueMessage::SENDER_COLLEAGUE)
                        ->whereNull('read_at')
                        ->count();
                } elseif ($admin->isColleague()) {
                    $chatUnread = ColleagueMessage::query()
                        ->where('colleague_id', $admin->id)
                        ->where('sender_type', ColleagueMessage::SENDER_SUPPORT)
                        ->whereNull('read_at')
                        ->count();
                }
            }

            $view->with('chatUnread', $chatUnread);
        });

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
