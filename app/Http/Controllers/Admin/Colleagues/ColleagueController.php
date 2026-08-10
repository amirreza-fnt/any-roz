<?php

namespace App\Http\Controllers\Admin\Colleagues;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\City;
use App\Models\MarketingSale;
use App\Models\Province;
use App\Support\AdminAccess;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class ColleagueController extends Controller
{
    public function index()
    {
        $colleagues = Admin::query()
            ->where('is_colleague', true)
            ->withCount(['wholesaleSales', 'wholesaleOrders'])
            ->orderByDesc('id')
            ->get();

        return view('backend.colleagues.index', compact('colleagues'));
    }

    public function create()
    {
        $groups = config('admin_access.permission_ui', []);
        $provinces = Province::query()->orderBy('name')->get();
        $cities = City::query()->orderBy('name')->get();
        $default = new Admin(['permissions' => Admin::COLLEAGUE_DEFAULT_PERMISSIONS]);

        return view('backend.colleagues.create', compact('groups', 'provinces', 'cities', 'default'));
    }

    public function store(Request $request)
    {
        $data = $this->validatedColleague($request, null);
        $perms = $this->mergedPermissions($request);

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
                'store_name' => $data['store_name'] ?: null,
                'address' => $data['address'] ?: null,
                'postal_code' => $data['postal_code'] ?: null,
                'province_id' => $data['province_id'] ?: null,
                'city_id' => $data['city_id'] ?: null,
                'password' => $data['password'],
                'permissions' => $perms,
                'is_active' => $request->boolean('is_active', true),
                'is_super' => false,
                'is_colleague' => true,
            ]);
        } catch (\Throwable $e) {
            report($e);
            message('error', 'ثبت همکار انجام نشد. اگر ساختار جدول admins هنوز به‌روز نشده است، ابتدا php artisan migrate را اجرا کنید.');

            return redirect()->back()->withInput();
        }

        message('success', 'همکار جدید ثبت شد.');

        return redirect()->route('admin.colleagues.index');
    }

    public function show(Admin $colleague)
    {
        $this->authorizeColleague($colleague);

        $sales = MarketingSale::query()
            ->where('marketer_id', $colleague->id)
            ->where('sale_type', MarketingSale::SALE_TYPE_COLLEAGUE)
            ->with(['items.product', 'order'])
            ->orderByDesc('id')
            ->get();

        $orders = $colleague->wholesaleOrders()->with(['items'])->orderByDesc('id')->get();

        return view('backend.colleagues.show', compact('colleague', 'sales', 'orders'));
    }

    public function edit(Admin $colleague)
    {
        $this->authorizeColleague($colleague);

        $groups = config('admin_access.permission_ui', []);
        $provinces = Province::query()->orderBy('name')->get();
        $cities = City::query()->orderBy('name')->get();

        return view('backend.colleagues.edit', compact('colleague', 'groups', 'provinces', 'cities'));
    }

    public function update(Request $request, Admin $colleague)
    {
        $this->authorizeColleague($colleague);

        $data = $this->validatedColleague($request, $colleague);
        $perms = $this->mergedPermissions($request);

        $colleague->first_name = $data['first_name'];
        $colleague->last_name = $data['last_name'];
        $colleague->phone = $data['phone'];
        $colleague->email = $data['email'] ?: null;
        $colleague->national_id = $data['national_id'] ?: null;
        $colleague->father_name = $data['father_name'] ?: null;
        $colleague->birth_date = $data['birth_date'] ?: null;
        $colleague->position = $data['position'] ?: null;
        $colleague->store_name = $data['store_name'] ?: null;
        $colleague->address = $data['address'] ?: null;
        $colleague->postal_code = $data['postal_code'] ?: null;
        $colleague->province_id = $data['province_id'] ?: null;
        $colleague->city_id = $data['city_id'] ?: null;
        $colleague->permissions = $perms;
        $colleague->is_active = $request->boolean('is_active', true);

        if (! empty($data['password'])) {
            $colleague->password = $data['password'];
        }

        $colleague->save();

        message('success', 'اطلاعات همکار به‌روزرسانی شد.');

        return redirect()->route('admin.colleagues.index');
    }

    public function toggleActive(Admin $colleague)
    {
        $this->authorizeColleague($colleague);

        if ($colleague->id === auth('admin')->id()) {
            message('warning', 'غیرفعال کردن حساب جاری مجاز نیست.');

            return redirect()->back();
        }

        $colleague->is_active = ! $colleague->is_active;
        $colleague->save();

        message('success', $colleague->is_active ? 'همکار فعال شد.' : 'همکار غیرفعال شد.');

        return redirect()->back();
    }

    public function destroy(Admin $colleague)
    {
        $this->authorizeColleague($colleague);

        if ($colleague->id === auth('admin')->id()) {
            message('warning', 'حذف حساب جاری مجاز نیست.');

            return redirect()->back();
        }

        $colleague->delete();
        message('success', 'همکار حذف شد.');

        return redirect()->route('admin.colleagues.index');
    }

    private function authorizeColleague(Admin $colleague): void
    {
        if (! $colleague->is_colleague) {
            abort(404);
        }
    }

    private function mergedPermissions(Request $request): array
    {
        $allowed = AdminAccess::allAssignableKeys();
        $input = $request->input('permissions', []);
        if (! is_array($input)) {
            $input = [];
        }
        $perms = array_values(array_intersect($input, $allowed));

        return array_values(array_unique(array_merge(Admin::COLLEAGUE_DEFAULT_PERMISSIONS, $perms)));
    }

    private function validatedColleague(Request $request, ?Admin $existing): array
    {
        foreach (['birth_date', 'email', 'national_id', 'father_name', 'position', 'store_name', 'address', 'postal_code', 'province_id', 'city_id'] as $k) {
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

        return $request->validate([
            'first_name' => ['required', 'string', 'max:120'],
            'last_name' => ['required', 'string', 'max:120'],
            'phone' => ['required', 'string', 'max:20', Rule::unique('admins', 'phone')->ignore($id)],
            'email' => ['nullable', 'email', 'max:255'],
            'national_id' => ['nullable', 'string', 'max:20'],
            'father_name' => ['nullable', 'string', 'max:120'],
            'birth_date' => ['nullable', 'date'],
            'position' => ['nullable', 'string', 'max:255'],
            'store_name' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:2000'],
            'postal_code' => ['nullable', 'string', 'max:20'],
            'province_id' => ['nullable', 'integer', 'exists:provinces,id'],
            'city_id' => ['nullable', 'integer', 'exists:cities,id'],
            'password' => $passwordRules,
        ], [], [
            'first_name' => 'نام',
            'last_name' => 'نام خانوادگی',
            'phone' => 'شماره موبایل',
            'password' => 'رمز عبور',
            'store_name' => 'نام فروشگاه',
        ]);
    }
}