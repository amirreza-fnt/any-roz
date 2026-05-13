<?php

namespace App\Providers;

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
        foreach ([
            public_path('uploads'),
            public_path('uploads/images/category'),
            public_path('uploads/images/type-of-weight'),
        ] as $dir) {
            File::ensureDirectoryExists($dir);
        }
    }
}
