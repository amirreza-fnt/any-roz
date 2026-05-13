<?php

namespace App\Http\Controllers\Admin\Marketing;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\GiftCode;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

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

        return view('backend.marketing.gift_codes.CreateOrUpdateGiftCode', compact('type', 'giftCode', 'categories', 'products'));
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

        return view('backend.marketing.gift_codes.CreateOrUpdateGiftCode', compact('type', 'giftCode', 'categories', 'products'));
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
            'starts_at' => 'nullable|date',
            'expires_at' => 'nullable|date|after_or_equal:starts_at',
            'applies_to' => ['required', Rule::in(['all', 'categories', 'products'])],
            'category_ids' => 'nullable|array',
            'category_ids.*' => 'integer|exists:categories,id',
            'product_ids' => 'nullable|array',
            'product_ids.*' => 'integer|exists:products,id',
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

        $base['usage_limit'] = isset($base['usage_limit']) && $base['usage_limit'] !== '' ? (int) $base['usage_limit'] : null;
        foreach (['starts_at', 'expires_at'] as $k) {
            if (array_key_exists($k, $base) && $base[$k] === '') {
                $base[$k] = null;
            }
        }
        if (($base['value_type'] ?? '') === 'percent') {
            $base['max_amount'] = isset($base['max_amount']) && $base['max_amount'] !== '' ? (int) $base['max_amount'] : null;
        }

        return $base;
    }
}
