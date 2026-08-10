<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DiscountCode;
use App\Models\GiftCode;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DiscountController extends Controller
{
    public function validateCode(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'code' => ['required', 'string'],
            'type' => ['required', 'in:discount,gift'],
            'total_amount' => ['required', 'integer', 'min:0'],
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = $request->user();
        $totalAmount = (int) $request->total_amount;

        if ($request->type === 'discount') {
            return $this->checkDiscountCode($request->code, $totalAmount, $user);
        }

        return $this->checkGiftCode($request->code, $totalAmount, $user);
    }

    private function checkDiscountCode(string $code, int $totalAmount, $user): JsonResponse
    {
        $discount = DiscountCode::where('code', $code)
            ->where('status', 'active')
            ->first();

        if (!$discount) {
            return response()->json(['valid' => false, 'message' => 'کد تخفیف نامعتبر است'], 422);
        }

        if ($discount->starts_at && now()->lt($discount->starts_at)) {
            return response()->json(['valid' => false, 'message' => 'کد تخفیف هنوز فعال نشده است'], 422);
        }

        if ($discount->expires_at && now()->gt($discount->expires_at)) {
            return response()->json(['valid' => false, 'message' => 'کد تخفیف منقضی شده است'], 422);
        }

        if ($discount->usage_limit && $discount->used_count >= $discount->usage_limit) {
            return response()->json(['valid' => false, 'message' => 'کد تخفیف به حداکثر استفاده رسیده است'], 422);
        }

        if ($totalAmount < (int) $discount->min_order_amount) {
            return response()->json([
                'valid' => false,
                'message' => 'حداقل مبلغ سفارش برای استفاده از این کد ' . number_format((int) $discount->min_order_amount) . ' تومان است',
            ], 422);
        }

        $amount = 0;
        if ($discount->discount_type === 'fixed') {
            $amount = (int) $discount->discount_amount;
        } elseif ($discount->discount_type === 'percent') {
            $amount = (int) ($totalAmount * (int) $discount->discount_percent / 100);
            if ($discount->max_discount_amount && $amount > (int) $discount->max_discount_amount) {
                $amount = (int) $discount->max_discount_amount;
            }
        }

        return response()->json([
            'valid' => true,
            'type' => 'discount',
            'code' => $discount->code,
            'title' => $discount->title,
            'discount_type' => $discount->discount_type,
            'amount' => $amount,
            'description' => $discount->valueLabel(),
        ]);
    }

    private function checkGiftCode(string $code, int $totalAmount, $user): JsonResponse
    {
        $gift = GiftCode::where('code', $code)
            ->where('status', 'active')
            ->first();

        if (!$gift) {
            return response()->json(['valid' => false, 'message' => 'کد هدیه نامعتبر است'], 422);
        }

        if ($gift->starts_at && now()->lt($gift->starts_at)) {
            return response()->json(['valid' => false, 'message' => 'کد هدیه هنوز فعال نشده است'], 422);
        }

        if ($gift->expires_at && now()->gt($gift->expires_at)) {
            return response()->json(['valid' => false, 'message' => 'کد هدیه منقضی شده است'], 422);
        }

        if ($gift->usage_limit && $gift->used_count >= $gift->usage_limit) {
            return response()->json(['valid' => false, 'message' => 'کد هدیه به حداکثر استفاده رسیده است'], 422);
        }

        if (!empty($gift->user_ids) && !in_array($user->id, $gift->user_ids)) {
            return response()->json(['valid' => false, 'message' => 'این کد هدیه برای شما قابل استفاده نیست'], 422);
        }

        if ($totalAmount < (int) $gift->min_order_amount) {
            return response()->json([
                'valid' => false,
                'message' => 'حداقل مبلغ سفارش برای استفاده از این کد ' . number_format((int) $gift->min_order_amount) . ' تومان است',
            ], 422);
        }

        $amount = 0;
        if ($gift->value_type === 'fixed') {
            $amount = (int) $gift->amount;
        } elseif ($gift->value_type === 'percent') {
            $amount = (int) ($totalAmount * (int) $gift->percent / 100);
            if ($gift->max_amount && $amount > (int) $gift->max_amount) {
                $amount = (int) $gift->max_amount;
            }
        }

        return response()->json([
            'valid' => true,
            'type' => 'gift',
            'code' => $gift->code,
            'title' => $gift->title,
            'value_type' => $gift->value_type,
            'amount' => $amount,
            'description' => $gift->valueLabel(),
        ]);
    }
}
