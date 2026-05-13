<?php

namespace App\Http\Controllers\Admin\Marketing;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\DiscountCode;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DiscountCodeController extends Controller
{
    public function index()
    {
        $discountCodes = DiscountCode::query()->orderByDesc('id')->get();

        return view('backend.marketing.discount_codes.IndexDiscountCode', compact('discountCodes'));
    }

    public function create()
    {
        $type = 'create';
        $discountCode = null;
        $categories = Category::orderBy('title')->get();
        $products = Product::orderBy('title')->get();

        return view('backend.marketing.discount_codes.CreateOrUpdateDiscountCode', compact('type', 'discountCode', 'categories', 'products'));
    }

    public function store(Request $request)
    {
        $data = $this->validatedDiscountCode($request);

        DiscountCode::create($data);

        message('success', 'کد تخفیف ثبت شد.');

        return redirect()->route('admin.discount-codes.index');
    }

    public function show(DiscountCode $discount_code)
    {
        return redirect()->route('admin.discount-codes.edit', $discount_code);
    }

    public function edit(DiscountCode $discount_code)
    {
        $type = 'edit';
        $discountCode = $discount_code;
        $categories = Category::orderBy('title')->get();
        $products = Product::orderBy('title')->get();

        return view('backend.marketing.discount_codes.CreateOrUpdateDiscountCode', compact('type', 'discountCode', 'categories', 'products'));
    }

    public function update(Request $request, DiscountCode $discount_code)
    {
        $data = $this->validatedDiscountCode($request, $discount_code->id);
        $discount_code->update($data);

        message('success', 'کد تخفیف به‌روزرسانی شد.');

        return redirect()->route('admin.discount-codes.index');
    }

    public function destroy(DiscountCode $discount_code)
    {
        $discount_code->delete();
        message('success', 'کد تخفیف حذف شد.');

        return redirect()->route('admin.discount-codes.index');
    }

    public function toggleStatus(DiscountCode $discount_code)
    {
        $discount_code->status = $discount_code->status === 'active' ? 'inactive' : 'active';
        $discount_code->save();
        message('success', 'وضعیت کد تخفیف تغییر کرد.');

        return redirect()->back();
    }

    private function validatedDiscountCode(Request $request, ?int $ignoreId = null): array
    {
        $codeRule = $ignoreId
            ? Rule::unique('discount_codes', 'code')->ignore($ignoreId)
            : Rule::unique('discount_codes', 'code');

        $base = $request->validate([
            'code' => ['required', 'string', 'max:64', $codeRule],
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:5000',
            'discount_type' => ['required', Rule::in(['fixed', 'percent'])],
            'discount_amount' => 'nullable|integer|min:0',
            'discount_percent' => 'nullable|integer|min:1|max:100',
            'max_discount_amount' => 'nullable|integer|min:0',
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

        if ($base['discount_type'] === 'fixed') {
            $request->validate([
                'discount_amount' => 'required|integer|min:1',
            ]);
            $base['discount_percent'] = null;
            $base['max_discount_amount'] = null;
        } else {
            $request->validate([
                'discount_percent' => 'required|integer|min:1|max:100',
            ]);
            $base['discount_amount'] = null;
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
        if (($base['discount_type'] ?? '') === 'percent') {
            $base['max_discount_amount'] = isset($base['max_discount_amount']) && $base['max_discount_amount'] !== '' ? (int) $base['max_discount_amount'] : null;
        }

        return $base;
    }
}
