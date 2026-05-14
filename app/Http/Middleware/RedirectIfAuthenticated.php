<?php

namespace App\Http\Middleware;

use App\Support\AdminAccess;
use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                if ($guard === 'admin') {
                    /** @var \App\Models\Admin|null $admin */
                    $admin = Auth::guard('admin')->user();
                    if (! $admin) {
                        return redirect()->route('admin.login');
                    }
                    $url = AdminAccess::firstAccessibleUrl($admin);
                    if ($url === null) {
                        abort(403, 'هیچ بخشی برای این حساب فعال نیست.');
                    }

                    return redirect()->to($url);
                }

                return redirect(RouteServiceProvider::HOME);
            }
        }

        return $next($request);
    }
}
