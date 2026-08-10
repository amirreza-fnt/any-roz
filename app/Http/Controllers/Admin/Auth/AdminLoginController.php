<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Support\AdminAccess;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AdminLoginController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::guard('admin')->check()) {
            /** @var Admin $admin */
            $admin = Auth::guard('admin')->user();
            $url = AdminAccess::firstAccessibleUrl($admin);
            if ($url === null) {
                abort(403, 'هیچ بخشی برای این حساب فعال نیست.');
            }

            return redirect()->to($url);
        }

        return view('backend.auth.admin-login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'phone' => ['required', 'string', 'max:20'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::guard('admin')->attempt([
            'phone' => $credentials['phone'],
            'password' => $credentials['password'],
        ], $request->boolean('remember'))) {
            throw ValidationException::withMessages([
                'phone' => 'شماره موبایل یا رمز عبور نادرست است.',
            ]);
        }

        /** @var Admin $admin */
        $admin = Auth::guard('admin')->user();
        if (! $admin->is_active) {
            Auth::guard('admin')->logout();
            throw ValidationException::withMessages([
                'phone' => 'این حساب غیرفعال است.',
            ]);
        }

        $request->session()->regenerate();

        $url = AdminAccess::firstAccessibleUrl($admin);
        if ($url === null) {
            Auth::guard('admin')->logout();
            throw ValidationException::withMessages([
                'phone' => 'هیچ بخشی برای این حساب فعال نیست؛ با مدیر اصلی تماس بگیرید.',
            ]);
        }

        return redirect()->to($url);
    }

    public function logout(Request $request)
    {
        Auth::guard('admin')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }
}
