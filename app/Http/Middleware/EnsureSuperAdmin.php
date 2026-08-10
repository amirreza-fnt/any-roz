<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureSuperAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        $admin = auth('admin')->user();
        if (! $admin || ! $admin->is_super) {
            abort(403, 'فقط مدیر اصلی (سوپرادمین) به این بخش دسترسی دارد.');
        }

        return $next($request);
    }
}
