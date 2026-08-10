<?php

namespace App\Http\Middleware;

use App\Support\AdminAccess;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdminPermission
{
    public function handle(Request $request, Closure $next): Response
    {
        $admin = auth('admin')->user();
        if (! $admin) {
            return redirect()->guest(route('admin.login'));
        }

        if ($admin->is_super) {
            return $next($request);
        }

        $routeName = $request->route()?->getName();
        $perm = AdminAccess::permissionForRoute($routeName);

        if ($perm === '*') {
            return $next($request);
        }

        if ($perm === null) {
            abort(403, 'دسترسی برای این بخش تعریف نشده است؛ با مدیر اصلی تماس بگیرید.');
        }

        if (! $admin->hasPermission($perm)) {
            abort(403, 'شما به این بخش دسترسی ندارید.');
        }

        return $next($request);
    }
}
