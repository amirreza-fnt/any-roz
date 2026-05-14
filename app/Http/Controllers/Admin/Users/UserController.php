<?php

namespace App\Http\Controllers\Admin\Users;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index()
    {
        $users = User::query()->orderByDesc('id')->get();

        return view('backend.users.IndexUser', compact('users'));
    }

    public function create()
    {
        $type = 'create';
        $user = null;

        return view('backend.users.CreateOrUpdateUser', compact('type', 'user'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'mobile' => ['nullable', 'string', 'max:20', 'regex:/^(|09[0-9]{9})$/'],
            'password' => 'required|string|min:8|confirmed',
            'is_active' => 'nullable|boolean',
        ]);
        $data['mobile'] = $data['mobile'] === '' ? null : $data['mobile'];

        User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'mobile' => $data['mobile'] ?? null,
            'password' => Hash::make($data['password']),
            'is_active' => $request->boolean('is_active', true),
        ]);

        message('success', 'کاربر ثبت شد.');

        return redirect()->route('admin.users.index');
    }

    public function show(User $user)
    {
        return redirect()->route('admin.users.edit', $user);
    }

    public function edit(User $user)
    {
        $type = 'edit';

        return view('backend.users.CreateOrUpdateUser', compact('type', 'user'));
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'mobile' => ['nullable', 'string', 'max:20', 'regex:/^(|09[0-9]{9})$/'],
            'password' => 'nullable|string|min:8|confirmed',
            'is_active' => 'nullable|boolean',
        ]);
        $data['mobile'] = ($data['mobile'] ?? '') === '' ? null : $data['mobile'];

        if ($user->id === auth()->id() && ! $request->boolean('is_active')) {
            message('warning', 'غیرفعال کردن حسابی که با آن وارد شده‌اید مجاز نیست.');

            return redirect()->back()->withInput();
        }

        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->mobile = $data['mobile'] ?? null;
        $user->is_active = $request->boolean('is_active');
        if (! empty($data['password'])) {
            $user->password = Hash::make($data['password']);
        }
        $user->save();

        message('success', 'کاربر به‌روزرسانی شد.');

        return redirect()->route('admin.users.index');
    }

    public function toggleActive(User $user)
    {
        if ($user->id === auth()->id()) {
            message('warning', 'تغییر وضعیت حسابی که با آن وارد شده‌اید مجاز نیست.');

            return redirect()->back();
        }

        $user->is_active = ! $user->is_active;
        $user->save();

        message('success', $user->is_active ? 'کاربر فعال شد.' : 'کاربر غیرفعال شد.');

        return redirect()->back();
    }
}
