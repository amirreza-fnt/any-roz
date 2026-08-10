<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrderListResource;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Models\OrderHistory;
use App\Models\OrderProduct;
use App\Models\Product;
use App\Models\ShippingConfig;
use App\Models\DiscountCode;
use App\Models\GiftCode;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $orders = $request->user()
            ->orders()
            ->withCount('items')
            ->latest()
            ->paginate(20);

        return response()->json([
            'data' => OrderListResource::collection($orders),
            'meta' => [
                'current_page' => $orders->currentPage(),
                'last_page' => $orders->lastPage(),
                'total' => $orders->total(),
            ],
        ]);
    }

    public function show(Request $request, Order $order): JsonResponse
    {
        if ($order->user_id !== $request->user()->id) {
            return response()->json(['message' => 'سفارش یافت نشد'], 404);
        }

        $order->load(['items', 'histories']);

        return response()->json([
            'data' => new OrderResource($order),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.type_of_weight_id' => ['nullable', 'exists:type_of_weights,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'shipping_method_id' => ['required', 'exists:shipping_configs,id'],
            'address_id' => ['required', 'exists:addresses,id'],
            'discount_code' => ['nullable', 'string'],
            'gift_code' => ['nullable', 'string'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = $request->user();
        $address = $user->addresses()->where('is_active', true)->findOrFail($request->address_id);

        $shippingMethod = ShippingConfig::where('is_active', true)->findOrFail($request->shipping_method_id);

        return DB::transaction(function () use ($request, $user, $address, $shippingMethod) {
            $totalAmount = 0;
            $totalWeight = 0;
            $orderProducts = [];

            foreach ($request->items as $item) {
                $product = Product::where('status', 'active')
                    ->with('typeOfWeights')
                    ->findOrFail($item['product_id']);

                $quantity = (int) $item['quantity'];
                $unitPrice = (int) $product->price;
                $discountedPrice = (int) $product->price_discounted;
                $weight = 0;

                if (!empty($item['type_of_weight_id'])) {
                    $pivot = $product->typeOfWeights()
                        ->where('type_of_weight_id', $item['type_of_weight_id'])
                        ->first();

                    if (!$pivot) {
                        return response()->json([
                            'message' => "تنوع وزنی برای محصول {$product->title} یافت نشد",
                        ], 422);
                    }

                    if ((int) $pivot->pivot->stock < $quantity) {
                        return response()->json([
                            'message' => "موجودی {$pivot->title} برای محصول {$product->title} ناکافی است",
                        ], 422);
                    }

                    $pivotPrice = (int) $pivot->pivot->price;
                    $pivotDiscounted = (int) $pivot->pivot->price_discounted;
                    $unitPrice = $pivotDiscounted > 0 ? $pivotDiscounted : $pivotPrice;
                    $weight = (int) $pivot->weight;
                } else {
                    if ((int) $product->stock < $quantity) {
                        return response()->json([
                            'message' => "موجودی محصول {$product->title} ناکافی است",
                        ], 422);
                    }

                    $unitPrice = $discountedPrice > 0 ? $discountedPrice : $unitPrice;
                }

                $lineTotal = $unitPrice * $quantity;
                $totalAmount += $lineTotal;
                $totalWeight += $weight * $quantity;

                $orderProducts[] = [
                    'product_id' => $product->id,
                    'product_name' => $product->title,
                    'product_code' => $product->tracking_code,
                    'product_image' => $product->primary_image_url,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'discount_percent' => 0,
                    'discount_amount' => 0,
                    'final_price' => $lineTotal,
                    'product_options' => !empty($item['type_of_weight_id'])
                        ? ['type_of_weight_id' => $item['type_of_weight_id']]
                        : null,
                ];
            }

            $shippingFee = $this->calculateShippingCost($shippingMethod, $totalWeight);
            $discountAmount = 0;

            if ($request->filled('discount_code')) {
                $discountResult = $this->validateDiscountCode($request->discount_code, $totalAmount, $user);
                if (!empty($discountResult['error'])) {
                    return response()->json(['message' => $discountResult['error']], 422);
                }
                $discountAmount = $discountResult['amount'];
            }

            if ($request->filled('gift_code')) {
                $giftResult = $this->validateGiftCode($request->gift_code, $totalAmount, $user);
                if (!empty($giftResult['error'])) {
                    return response()->json(['message' => $giftResult['error']], 422);
                }
                $discountAmount += $giftResult['amount'];
            }

            $finalAmount = max(0, $totalAmount + $shippingFee - $discountAmount);

            $orderNumber = 'ORD-' . now()->format('Ymd') . '-' . strtoupper(substr(uniqid(), -6));

            $order = Order::create([
                'user_id' => $user->id,
                'source' => Order::SOURCE_SITE,
                'order_number' => $orderNumber,
                'total_amount' => $totalAmount,
                'shipping_fee' => $shippingFee,
                'discount_amount' => $discountAmount,
                'shipping_cost' => $shippingMethod->base_shipping_cost,
                'insurance_cost' => $shippingMethod->base_insurance_cost,
                'final_amount' => $finalAmount,
                'total_weight' => $totalWeight,
                'payment_status' => 'pending',
                'shipping_status' => Order::STATUS_PENDING_REVIEW,
                'shipping_method' => $shippingMethod->name,
                'shipping_address' => $address->full_address,
                'shipping_city' => $address->city_name,
                'shipping_state' => $address->province_name,
                'shipping_postal_code' => $address->postal_code,
                'shipping_recipient_name' => $address->first_name . ' ' . $address->last_name,
                'shipping_phone' => $address->mobile,
                'notes' => $request->notes,
            ]);

            foreach ($orderProducts as $op) {
                $order->items()->create($op);
            }

            OrderHistory::create([
                'order_id' => $order->id,
                'user_id' => $user->id,
                'status' => Order::STATUS_PENDING_REVIEW,
                'note' => 'سفارش ثبت شد',
            ]);

            $order->load(['items', 'histories']);

            return response()->json([
                'message' => 'سفارش با موفقیت ثبت شد',
                'data' => new OrderResource($order),
            ], 201);
        });
    }

    public function cancel(Request $request, Order $order): JsonResponse
    {
        if ($order->user_id !== $request->user()->id) {
            return response()->json(['message' => 'سفارش یافت نشد'], 404);
        }

        if (!in_array($order->shipping_status, [Order::STATUS_PENDING_REVIEW, Order::STATUS_PACKAGING])) {
            return response()->json([
                'message' => 'امکان لغو سفارش در وضعیت فعلی وجود ندارد',
            ], 422);
        }

        DB::transaction(function () use ($order, $request) {
            $order->update(['shipping_status' => Order::STATUS_CANCELLED]);

            OrderHistory::create([
                'order_id' => $order->id,
                'user_id' => $request->user()->id,
                'status' => Order::STATUS_CANCELLED,
                'note' => 'سفارش توسط کاربر لغو شد',
            ]);
        });

        return response()->json([
            'message' => 'سفارش با موفقیت لغو شد',
        ]);
    }

    private function calculateShippingCost(ShippingConfig $method, float $totalWeight): int
    {
        $cost = (int) $method->base_shipping_cost + (int) $method->base_insurance_cost + (int) $method->base_packaging_cost;

        if ($totalWeight > (int) $method->package_weight_limit && (int) $method->extra_weight_cost > 0) {
            $extraWeight = $totalWeight - (int) $method->package_weight_limit;
            $cost += ceil($extraWeight) * (int) $method->extra_weight_cost;
        }

        return $cost;
    }

    private function validateDiscountCode(string $code, int $totalAmount, $user): array
    {
        $discount = DiscountCode::where('code', $code)
            ->where('status', 'active')
            ->first();

        if (!$discount) {
            return ['error' => 'کد تخفیف نامعتبر است'];
        }

        if ($discount->starts_at && now()->lt($discount->starts_at)) {
            return ['error' => 'کد تخفیف هنوز فعال نشده است'];
        }

        if ($discount->expires_at && now()->gt($discount->expires_at)) {
            return ['error' => 'کد تخفیف منقضی شده است'];
        }

        if ($discount->usage_limit && $discount->used_count >= $discount->usage_limit) {
            return ['error' => 'کد تخفیف به حداکثر استفاده رسیده است'];
        }

        if ($totalAmount < (int) $discount->min_order_amount) {
            return ['error' => 'حداقل مبلغ سفارش برای استفاده از این کد ' . number_format((int) $discount->min_order_amount) . ' تومان است'];
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

        $discount->increment('used_count');

        return ['amount' => $amount];
    }

    private function validateGiftCode(string $code, int $totalAmount, $user): array
    {
        $gift = GiftCode::where('code', $code)
            ->where('status', 'active')
            ->first();

        if (!$gift) {
            return ['error' => 'کد هدیه نامعتبر است'];
        }

        if ($gift->starts_at && now()->lt($gift->starts_at)) {
            return ['error' => 'کد هدیه هنوز فعال نشده است'];
        }

        if ($gift->expires_at && now()->gt($gift->expires_at)) {
            return ['error' => 'کد هدیه منقضی شده است'];
        }

        if ($gift->usage_limit && $gift->used_count >= $gift->usage_limit) {
            return ['error' => 'کد هدیه به حداکثر استفاده رسیده است'];
        }

        if (!empty($gift->user_ids) && !in_array($user->id, $gift->user_ids)) {
            return ['error' => 'این کد هدیه برای شما قابل استفاده نیست'];
        }

        if ($totalAmount < (int) $gift->min_order_amount) {
            return ['error' => 'حداقل مبلغ سفارش برای استفاده از این کد ' . number_format((int) $gift->min_order_amount) . ' تومان است'];
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

        $gift->increment('used_count');

        return ['amount' => $amount];
    }
}
