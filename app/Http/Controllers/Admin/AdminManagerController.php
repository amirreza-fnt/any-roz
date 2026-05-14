<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Support\AdminAccess;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Throwable;

class AdminManagerController extends Controller
{
    public function index()
    {
        $admins = Admin::query()->orderByDesc('id')->get();

        return view('backend.admins.index', compact('admins'));
    }

    public function create()
    {
        $admin = null;
        $groups = config('admin_access.permission_ui', []);

        return view('backend.admins.create', compact('admin', 'groups'));
    }

    public function store(Request $request)
    {
        $data = $this->validatedAdmin($request, null);
        $perms = $this->normalizedPermissions($request);

        try {
            Admin::create([
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'phone' => $data['phone'],
                'email' => $data['email'] ?: null,
                'national_id' => $data['national_id'] ?: null,
                'father_name' => $data['father_name'] ?: null,
                'birth_date' => $data['birth_date'] ?: null,
                'position' => $data['position'] ?: null,
                'password' => $data['password'],
                'permissions' => $perms,
                'is_active' => $request->boolean('is_active', true),
                'is_super' => $request->boolean('is_super') && auth('admin')->user()->is_super,
            ]);
        } catch (Throwable $e) {
            report($e);
            message('error', 'ثبت مدیر انجام نشد. اگر جدول admins را migrate نکرده‌اید، ابتدا php artisan migrate را اجرا کنید.');

            return redirect()->back()->withInput();
        }

        message('success', 'مدیر جدید ثبت شد.');

        return redirect()->route('admin.managers.index');
    }

    public function edit(Admin $manager)
    {
        if ($manager->is_super && ! auth('admin')->user()->is_super) {
            abort(403);
        }

        $admin = $manager;
        $groups = config('admin_access.permission_ui', []);

        return view('backend.admins.edit', compact('admin', 'groups'));
    }

    public function update(Request $request, Admin $manager)
    {
        if ($manager->is_super && ! auth('admin')->user()->is_super) {
            abort(403);
        }

        $data = $this->validatedAdmin($request, $manager);
        $perms = $this->normalizedPermissions($request);

        $manager->first_name = $data['first_name'];
        $manager->last_name = $data['last_name'];
        $manager->phone = $data['phone'];
        $manager->email = $data['email'] ?: null;
        $manager->national_id = $data['national_id'] ?: null;
        $manager->father_name = $data['father_name'] ?: null;
        $manager->birth_date = $data['birth_date'] ?: null;
        $manager->position = $data['position'] ?: null;
        $manager->permissions = $perms;
        $manager->is_active = $request->boolean('is_active', true);

        if (auth('admin')->user()->is_super) {
            if ($manager->id === auth('admin')->id()) {
                $manager->is_super = true;
            } else {
                $manager->is_super = $request->boolean('is_super');
            }
        }

        if (! empty($data['password'])) {
            $manager->password = $data['password'];
        }

        $manager->save();

        message('success', 'اطلاعات مدیر به‌روزرسانی شد.');

        return redirect()->route('admin.managers.index');
    }

    public function toggleActive(Admin $manager)
    {
        if ($manager->id === auth('admin')->id()) {
            message('warning', 'غیرفعال کردن حساب جاری مجاز نیست.');

            return redirect()->back();
        }

        if ($manager->is_super && ! auth('admin')->user()->is_super) {
            abort(403);
        }

        $manager->is_active = ! $manager->is_active;
        $manager->save();

        message('success', $manager->is_active ? 'مدیر فعال شد.' : 'مدیر غیرفعال شد.');

        return redirect()->back();
    }

    private function validatedAdmin(Request $request, ?Admin $existing): array
    {
        foreach (['birth_date', 'email', 'national_id', 'father_name', 'position'] as $k) {
            if ($request->input($k) === '') {
                $request->merge([$k => null]);
            }
        }

        $id = $existing?->id;

        $passwordRules = [
            'nullable',
            'string',
            'confirmed',
            Password::min(8)->letters()->mixedCase()->numbers()->symbols(),
        ];
        if (! $existing) {
            $passwordRules[0] = 'required';
        }

        $data = $request->validate([
            'first_name' => ['required', 'string', 'max:120'],
            'last_name' => ['required', 'string', 'max:120'],
            'phone' => ['required', 'string', 'max:20', Rule::unique('admins', 'phone')->ignore($id)],
            'email' => ['nullable', 'email', 'max:255'],
            'national_id' => ['nullable', 'string', 'max:20'],
            'father_name' => ['nullable', 'string', 'max:120'],
            'birth_date' => ['nullable', 'date'],
            'position' => ['nullable', 'string', 'max:255'],
            'password' => $passwordRules,
        ], [], [
            'first_name' => 'نام',
            'last_name' => 'نام خانوادگی',
            'phone' => 'شماره موبایل',
            'password' => 'رمز عبور',
        ]);

        foreach (['email', 'national_id', 'father_name', 'birth_date', 'position'] as $k) {
            if (array_key_exists($k, $data) && $data[$k] === '') {
                $data[$k] = null;
            }
        }

        return $data;
    }

    /**
     * @return list<string>
     */
    private function normalizedPermissions(Request $request): array
    {
        $allowed = AdminAccess::allAssignableKeys();
        $input = $request->input('permissions', []);
        if (! is_array($input)) {
            return [];
        }

        return array_values(array_intersect($input, $allowed));
    }
}
