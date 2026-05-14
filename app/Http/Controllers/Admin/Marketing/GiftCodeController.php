<?php

namespace App\Http\Controllers\Admin\Marketing;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\GiftCode;
use App\Models\Product;
use App\Models\User;
use App\Support\JalaliCalendar;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class GiftCodeController extends Controller
{
    public function index()
    {
        $giftCodes = GiftCode::query()->orderByDesc('id')->get();

        return view('backend.marketing.gift_codes.IndexGiftCode', compact('giftCodes'));
    }

    public function create()
    {
        $type = 'create';
        $giftCode = null;
        $categories = Category::orderBy('title')->get();
        $products = Product::orderBy('title')->get();
        $users = User::query()->orderBy('name')->orderBy('id')->get();

        return view('backend.marketing.gift_codes.CreateOrUpdateGiftCode', compact('type', 'giftCode', 'categories', 'products', 'users'));
    }

    public function store(Request $request)
    {
        $data = $this->validatedGiftCode($request);

        GiftCode::create($data);

        message('success', 'کد هدیه ثبت شد.');

        return redirect()->route('admin.gift-codes.index');
    }

    public function show(GiftCode $gift_code)
    {
        return redirect()->route('admin.gift-codes.edit', $gift_code);
    }

    public function edit(GiftCode $gift_code)
    {
        $type = 'edit';
        $giftCode = $gift_code;
        $categories = Category::orderBy('title')->get();
        $products = Product::orderBy('title')->get();
        $users = User::query()->orderBy('name')->orderBy('id')->get();

        return view('backend.marketing.gift_codes.CreateOrUpdateGiftCode', compact('type', 'giftCode', 'categories', 'products', 'users'));
    }

    public function update(Request $request, GiftCode $gift_code)
    {
        $data = $this->validatedGiftCode($request, $gift_code->id);
        $gift_code->update($data);

        message('success', 'کد هدیه به‌روزرسانی شد.');

        return redirect()->route('admin.gift-codes.index');
    }

    public function destroy(GiftCode $gift_code)
    {
        $gift_code->delete();
        message('success', 'کد هدیه حذف شد.');

        return redirect()->route('admin.gift-codes.index');
    }

    public function toggleStatus(GiftCode $gift_code)
    {
        $gift_code->status = $gift_code->status === 'active' ? 'inactive' : 'active';
        $gift_code->save();
        message('success', 'وضعیت کد هدیه تغییر کرد.');

        return redirect()->back();
    }

    private function validatedGiftCode(Request $request, ?int $ignoreId = null): array
    {
        $codeRule = $ignoreId
            ? Rule::unique('gift_codes', 'code')->ignore($ignoreId)
            : Rule::unique('gift_codes', 'code');

        $base = $request->validate([
            'code' => ['required', 'string', 'max:64', $codeRule],
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:5000',
            'value_type' => ['required', Rule::in(['fixed', 'percent'])],
            'amount' => 'nullable|integer|min:0',
            'percent' => 'nullable|integer|min:1|max:100',
            'max_amount' => 'nullable|integer|min:0',
            'min_order_amount' => 'nullable|integer|min:0',
            'usage_limit' => 'nullable|integer|min:1',
            'per_user_limit' => 'nullable|integer|min:1',
            'starts_at_shamsi' => 'nullable|string|max:32',
            'expires_at_shamsi' => 'nullable|string|max:32',
            'applies_to' => ['required', Rule::in(['all', 'categories', 'products'])],
            'category_ids' => 'nullable|array',
            'category_ids.*' => 'integer|exists:categories,id',
            'product_ids' => 'nullable|array',
            'product_ids.*' => 'integer|exists:products,id',
            'user_ids' => 'nullable|array',
            'user_ids.*' => 'integer|exists:users,id',
            'status' => ['required', Rule::in(['active', 'inactive'])],
        ]);

        if ($base['value_type'] === 'fixed') {
            $request->validate([
                'amount' => 'required|integer|min:1',
            ]);
            $base['percent'] = null;
            $base['max_amount'] = null;
        } else {
            $request->validate([
                'percent' => 'required|integer|min:1|max:100',
            ]);
            $base['amount'] = null;
        }

        if ($base['applies_to'] === 'categories') {
            $request->validate([
                'category_ids' => 'required|array|min:1',
            ]);
            $base['product_ids'] = null;
        } elseif ($base['applies_to'] === 'products') {
            $request->validate([
                'product_ids' => 'required|array|min:1',
            ]);
            $base['category_ids'] = null;
        } else {
            $base['category_ids'] = null;
            $base['product_ids'] = null;
        }

        $base['code'] = mb_strtoupper(trim($base['code']), 'UTF-8');
        $base['min_order_amount'] = (int) ($base['min_order_amount'] ?? 0);
        $base['per_user_limit'] = (int) ($base['per_user_limit'] ?? 1);
        $base['category_ids'] = $base['category_ids'] ? array_values(array_unique(array_map('intval', $base['category_ids']))) : null;
        $base['product_ids'] = $base['product_ids'] ? array_values(array_unique(array_map('intval', $base['product_ids']))) : null;
        $base['user_ids'] = ! empty($base['user_ids']) ? array_values(array_unique(array_map('intval', $base['user_ids']))) : null;

        $startsRaw = trim((string) ($base['starts_at_shamsi'] ?? ''));
        $expiresRaw = trim((string) ($base['expires_at_shamsi'] ?? ''));
        unset($base['starts_at_shamsi'], $base['expires_at_shamsi']);

        $startsAt = null;
        $expiresAt = null;
        if ($startsRaw !== '') {
            if (! preg_match('/^\d{4}\/\d{1,2}\/\d{1,2}$/', $startsRaw)) {
                throw ValidationException::withMessages([
                    'starts_at_shamsi' => 'فرمت تاریخ شروع باید به صورت سال/ماه/روز باشد.',
                ]);
            }
            try {
                $startsAt = JalaliCalendar::parseShamsiDateStartOfDay($startsRaw);
            } catch (\InvalidArgumentException $e) {
                throw ValidationException::withMessages(['starts_at_shamsi' => $e->getMessage()]);
            }
        }
        if ($expiresRaw !== '') {
            if (! preg_match('/^\d{4}\/\d{1,2}\/\d{1,2}$/', $expiresRaw)) {
                throw ValidationException::withMessages([
                    'expires_at_shamsi' => 'فرمت تاریخ پایان باید به صورت سال/ماه/روز باشد.',
                ]);
            }
            try {
                $expiresAt = JalaliCalendar::parseShamsiDateEndOfDay($expiresRaw);
            } catch (\InvalidArgumentException $e) {
                throw ValidationException::withMessages(['expires_at_shamsi' => $e->getMessage()]);
            }
        }
        if ($startsAt && $expiresAt && $expiresAt->lt($startsAt)) {
            throw ValidationException::withMessages([
                'expires_at_shamsi' => 'تاریخ پایان باید بعد از تاریخ شروع باشد.',
            ]);
        }
        $base['starts_at'] = $startsAt;
        $base['expires_at'] = $expiresAt;

        $base['usage_limit'] = isset($base['usage_limit']) && $base['usage_limit'] !== '' ? (int) $base['usage_limit'] : null;
        if (($base['value_type'] ?? '') === 'percent') {
            $base['max_amount'] = isset($base['max_amount']) && $base['max_amount'] !== '' ? (int) $base['max_amount'] : null;
        }

        return $base;
    }
}
