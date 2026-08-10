<?php

namespace App\Http\Controllers\Admin\Marketing;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\MarketingBuyer;
use App\Models\Province;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class MarketingBuyerController extends Controller
{
    private function marketerId(): int
    {
        return (int) config('marketing.default_marketer_id');
    }

    public function index()
    {
        $buyers = MarketingBuyer::query()
            ->where('marketer_id', $this->marketerId())
            ->with(['province', 'city'])
            ->orderByDesc('id')
            ->get();

        return view('backend.marketing.buyers.index', compact('buyers'));
    }

    public function create()
    {
        $buyer = null;
        $provinces = Province::query()->orderBy('name')->get();

        return view('backend.marketing.buyers.create', compact('buyer', 'provinces'));
    }

    public function store(Request $request)
    {
        $data = $this->validatedBuyer($request);
        $data['marketer_id'] = $this->marketerId();
        MarketingBuyer::create($data);
        message('success', 'خریدار ثبت شد.');

        return redirect()->route('admin.marketing.buyers.index');
    }

    public function edit(MarketingBuyer $buyer)
    {
        $this->authorizeBuyer($buyer);
        $provinces = Province::query()->orderBy('name')->get();
        $cities = $buyer->province_id
            ? City::query()->where('province_id', $buyer->province_id)->orderBy('name')->get()
            : collect();

        return view('backend.marketing.buyers.edit', compact('buyer', 'provinces', 'cities'));
    }

    public function update(Request $request, MarketingBuyer $buyer)
    {
        $this->authorizeBuyer($buyer);
        $buyer->update($this->validatedBuyer($request));
        message('success', 'اطلاعات خریدار به‌روزرسانی شد.');

        return redirect()->route('admin.marketing.buyers.index');
    }

    public function destroy(MarketingBuyer $buyer)
    {
        $this->authorizeBuyer($buyer);
        if ($buyer->sales()->exists()) {
            message('warning', 'این خریدار در فروش‌های ثبت‌شده استفاده شده و قابل حذف نیست.');

            return redirect()->back();
        }
        $buyer->delete();
        message('success', 'خریدار حذف شد.');

        return redirect()->route('admin.marketing.buyers.index');
    }

    private function authorizeBuyer(MarketingBuyer $buyer): void
    {
        if ((int) $buyer->marketer_id !== $this->marketerId()) {
            abort(404);
        }
    }

    private function validatedBuyer(Request $request): array
    {
        $data = $request->validate([
            'first_name' => 'required|string|max:120',
            'last_name' => 'required|string|max:120',
            'phone' => ['required', 'string', 'max:32', 'regex:/^09[0-9]{9}$/'],
            'address' => 'nullable|string|max:2000',
            'province_id' => 'nullable|integer|exists:provinces,id',
            'city_id' => [
                'nullable',
                'integer',
                Rule::exists('cities', 'id')->where(function ($q) use ($request) {
                    if ($request->filled('province_id')) {
                        $q->where('province_id', $request->integer('province_id'));
                    }
                }),
            ],
            'store_name' => 'nullable|string|max:255',
            'postal_code' => 'nullable|string|max:20',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'notes' => 'nullable|string|max:5000',
        ]);

        if (! empty($data['city_id'] ?? null) && ! empty($data['province_id'] ?? null)) {
            $city = City::query()->find((int) $data['city_id']);
            if (! $city || (int) $city->province_id !== (int) $data['province_id']) {
                throw ValidationException::withMessages([
                    'city_id' => 'شهر باید متعلق به استان انتخاب‌شده باشد.',
                ]);
            }
        }

        return $data;
    }
}
