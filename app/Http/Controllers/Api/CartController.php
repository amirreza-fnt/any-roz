<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DiscountCode;
use App\Models\GiftCode;
use App\Models\Product;
use App\Models\ShippingConfig;
use App\Models\Order;
use App\Models\OrderHistory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class CartController extends Controller
{
    private function getItems(Request $request): array
    {
        return $request->session()->get('cart.items', []);
    }

    private function setItems(Request $request, array $items): void
    {
        $request->session()->put('cart.items', $items);
    }

    private function getMeta(Request $request): array
    {
        return $request->session()->get('cart.meta', [
            'discount_code' => null,
            'discount_type' => null,
            'gift_code' => null,
            'shipping_method_id' => null,
        ]);
    }

    private function setMeta(Request $request, array $meta): void
    {
        $request->session()->put('cart.meta', $meta);
    }

    private function clearMeta(Request $request): void
    {
        $request->session()->forget('cart.meta');
    }

    private function generateItemId(): string
    {
        return Str::random(16);
    }

    public function index(Request $request): JsonResponse
    {
        $items = $this->getItems($request);
        $meta = $this->getMeta($request);

        if (empty($items)) {
            return response()->json([
                'data' => [
                    'items' => [],
                    'summary' => [
                        'total_items' => 0,
                        'total_quantity' => 0,
                        'total_amount' => 0,
                        'total_weight' => 0,
                        'shipping_fee' => 0,
                        'discount_amount' => 0,
                        'final_amount' => 0,
                    ],
                    'discount' => null,
                    'shipping' => null,
                ],
            ]);
        }

        $productIds = array_unique(array_map(fn($i) => $i['product_id'], $items));
        $products = Product::where('status', 'active')
            ->whereIn('id', $productIds)
            ->with(['images', 'typeOfWeights'])
            ->get()
            ->keyBy('id');

        $cartItems = [];
        $totalAmount = 0;
        $totalWeight = 0;

        foreach ($items as $itemId => $item) {
            $product = $products->get($item['product_id']);
            if (!$product) {
                continue;
            }

            $quantity = max(1, (int) $item['quantity']);
            $unitPrice = (int) $product->price;
            $discountedPrice = (int) $product->price_discounted;
            $weight = 0;
            $typeOfWeight = null;
            $availableStock = (int) $product->stock;

            if (!empty($item['type_of_weight_id'])) {
                $pivot = $product->typeOfWeights->firstWhere('id', $item['type_of_weight_id']);
                if ($pivot) {
                    $pivotPrice = (int) $pivot->pivot->price;
                    $pivotDiscounted = (int) $pivot->pivot->price_discounted;
                    $unitPrice = $pivotDiscounted > 0 ? $pivotDiscounted : $pivotPrice;
                    $weight = (int) $pivot->weight;
                    $availableStock = (int) $pivot->pivot->stock;
                    $typeOfWeight = [
                        'id' => $pivot->id,
                        'title' => $pivot->title,
                        'weight' => $pivot->weight,
                    ];
                } else {
                    $availableStock = 0;
                }
            } else {
                $unitPrice = $discountedPrice > 0 ? $discountedPrice : $unitPrice;
            }

            $lineTotal = $unitPrice * $quantity;
            $totalAmount += $lineTotal;
            $totalWeight += $weight * $quantity;

            $cartItems[] = [
                'item_id' => $itemId,
                'product_id' => $product->id,
                'product' => [
                    'id' => $product->id,
                    'title' => $product->title,
                    'slug' => $product->slug,
                    'tracking_code' => $product->tracking_code,
                    'primary_image' => $product->primary_image_url,
                    'price' => (int) $product->price,
                    'price_discounted' => (int) $product->price_discounted,
                    'has_discount' => $discountedPrice > 0 && $discountedPrice < (int) $product->price,
                ],
                'type_of_weight' => $typeOfWeight,
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'line_total' => $lineTotal,
                'available_stock' => $availableStock,
                'weight' => $weight,
            ];
        }

        $shippingFee = 0;
        $shippingInfo = null;
        if (!empty($meta['shipping_method_id'])) {
            $method = ShippingConfig::find($meta['shipping_method_id']);
            if ($method && $method->is_active) {
                $shippingFee = $this->calculateShippingCost($method, $totalWeight);
                $shippingInfo = [
                    'id' => $method->id,
                    'name' => $method->name,
                    'fee' => $shippingFee,
                ];
            }
        }

        $discountAmount = 0;
        $discountInfo = null;

        if (!empty($meta['discount_code']) && $meta['discount_type'] === 'discount') {
            $result = $this->validateDiscountCode($meta['discount_code'], $totalAmount, $request);
            if (!isset($result['error'])) {
                $discountAmount = $result['amount'];
                $discountInfo = [
                    'code' => $meta['discount_code'],
                    'type' => 'discount',
                    'title' => $result['title'] ?? null,
                    'description' => $result['description'] ?? null,
                    'amount' => $discountAmount,
                ];
            } else {
                $this->setMeta($request, array_merge($meta, ['discount_code' => null, 'discount_type' => null]));
            }
        }

        if (!empty($meta['gift_code'])) {
            $result = $this->validateGiftCode($meta['gift_code'], $totalAmount, $request);
            if (!isset($result['error'])) {
                $discountAmount += $result['amount'];
                $discountInfo = [
                    'code' => $meta['gift_code'],
                    'type' => 'gift',
                    'title' => $result['title'] ?? null,
                    'description' => $result['description'] ?? null,
                    'amount' => $result['amount'],
                ];
            } else {
                $this->setMeta($request, array_merge($meta, ['gift_code' => null]));
            }
        }

        $finalAmount = max(0, $totalAmount + $shippingFee - $discountAmount);

        return response()->json([
            'data' => [
                'items' => $cartItems,
                'summary' => [
                    'total_items' => count($cartItems),
                    'total_quantity' => array_sum(array_map(fn($i) => $i['quantity'], $cartItems)),
                    'total_amount' => $totalAmount,
                    'total_weight' => $totalWeight,
                    'shipping_fee' => $shippingFee,
                    'discount_amount' => $discountAmount,
                    'final_amount' => $finalAmount,
                ],
                'discount' => $discountInfo,
                'shipping' => $shippingInfo,
            ],
        ]);
    }

    public function addItem(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'product_id' => ['required', 'exists:products,id'],
            'type_of_weight_id' => ['nullable', 'exists:type_of_weights,id'],
            'quantity' => ['required', 'integer', 'min:1', 'max:999'],
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $product = Product::where('status', 'active')
            ->with('typeOfWeights')
            ->findOrFail($request->product_id);

        $quantity = (int) $request->quantity;
        $typeOfWeightId = $request->filled('type_of_weight_id') ? (int) $request->type_of_weight_id : null;

        if ($typeOfWeightId) {
            $pivot = $product->typeOfWeights->firstWhere('id', $typeOfWeightId);
            if (!$pivot) {
                return response()->json(['message' => 'تنوع وزنی انتخاب شده برای این محصول موجود نیست'], 422);
            }
            if ((int) $pivot->pivot->stock < $quantity) {
                return response()->json(['message' => "موجودی ناکافی. حداکثر تعداد: {$pivot->pivot->stock}"], 422);
            }
        } else {
            if ((int) $product->stock < $quantity) {
                return response()->json(['message' => "موجودی ناکافی. حداکثر تعداد: {$product->stock}"], 422);
            }
        }

        $items = $this->getItems($request);

        $existingItemId = null;
        foreach ($items as $itemId => $item) {
            if ((int) $item['product_id'] === (int) $product->id && $item['type_of_weight_id'] === $typeOfWeightId) {
                $existingItemId = $itemId;
                break;
            }
        }

        if ($existingItemId) {
            $newQty = $items[$existingItemId]['quantity'] + $quantity;
            if ($typeOfWeightId) {
                $pivot = $product->typeOfWeights->firstWhere('id', $typeOfWeightId);
                $maxStock = (int) $pivot->pivot->stock;
            } else {
                $maxStock = (int) $product->stock;
            }
            if ($newQty > $maxStock) {
                return response()->json(['message' => "مجموع تعداد در سبد خرید از موجودی بیشتر است. حداکثر قابل اضافه شدن: " . ($maxStock - $items[$existingItemId]['quantity'])], 422);
            }
            $items[$existingItemId]['quantity'] = $newQty;
        } else {
            $items[$this->generateItemId()] = [
                'product_id' => (int) $product->id,
                'type_of_weight_id' => $typeOfWeightId,
                'quantity' => $quantity,
            ];
        }

        $this->setItems($request, $items);

        return response()->json([
            'message' => 'محصول با موفقیت به سبد خرید اضافه شد',
        ]);
    }

    public function updateItem(Request $request, string $itemId): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'quantity' => ['required', 'integer', 'min:1', 'max:999'],
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $items = $this->getItems($request);

        if (!isset($items[$itemId])) {
            return response()->json(['message' => 'آیتم مورد نظر در سبد خرید یافت نشد'], 404);
        }

        $item = $items[$itemId];
        $quantity = (int) $request->quantity;

        $product = Product::where('status', 'active')
            ->with('typeOfWeights')
            ->find($item['product_id']);

        if (!$product) {
            unset($items[$itemId]);
            $this->setItems($request, $items);
            return response()->json(['message' => 'محصول مورد نظر دیگر موجود نیست'], 422);
        }

        if (!empty($item['type_of_weight_id'])) {
            $pivot = $product->typeOfWeights->firstWhere('id', $item['type_of_weight_id']);
            if (!$pivot || (int) $pivot->pivot->stock < $quantity) {
                $maxStock = $pivot ? (int) $pivot->pivot->stock : 0;
                return response()->json(['message' => "موجودی ناکافی. حداکثر تعداد مجاز: {$maxStock}"], 422);
            }
        } else {
            if ((int) $product->stock < $quantity) {
                return response()->json(['message' => "موجودی ناکافی. حداکثر تعداد مجاز: {$product->stock}"], 422);
            }
        }

        $items[$itemId]['quantity'] = $quantity;
        $this->setItems($request, $items);

        return response()->json(['message' => 'تعداد محصول با موفقیت به‌روزرسانی شد']);
    }

    public function removeItem(Request $request, string $itemId): JsonResponse
    {
        $items = $this->getItems($request);

        if (!isset($items[$itemId])) {
            return response()->json(['message' => 'آیتم مورد نظر در سبد خرید یافت نشد'], 404);
        }

        unset($items[$itemId]);
        $this->setItems($request, $items);

        return response()->json(['message' => 'محصول با موفقیت از سبد خرید حذف شد']);
    }

    public function clear(Request $request): JsonResponse
    {
        $request->session()->forget('cart');

        return response()->json(['message' => 'سبد خرید با موفقیت خالی شد']);
    }

    public function applyDiscount(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'code' => ['required', 'string'],
            'type' => ['required', 'in:discount,gift'],
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $items = $this->getItems($request);
        if (empty($items)) {
            return response()->json(['message' => 'سبد خرید خالی است'], 422);
        }

        $meta = $this->getMeta($request);
        $totalAmount = $this->getRawTotal($items);

        if ($request->type === 'discount') {
            $result = $this->validateDiscountCode($request->code, $totalAmount, $request);
            if (isset($result['error'])) {
                return response()->json(['message' => $result['error']], 422);
            }
            $meta['discount_code'] = $request->code;
            $meta['discount_type'] = 'discount';
        } else {
            $result = $this->validateGiftCode($request->code, $totalAmount, $request);
            if (isset($result['error'])) {
                return response()->json(['message' => $result['error']], 422);
            }
            $meta['gift_code'] = $request->code;
        }

        $this->setMeta($request, $meta);

        return response()->json([
            'message' => 'کد با موفقیت اعمال شد',
            'data' => [
                'type' => $request->type,
                'code' => $request->code,
                'amount' => $result['amount'],
                'title' => $result['title'] ?? null,
                'description' => $result['description'] ?? null,
            ],
        ]);
    }

    public function removeDiscount(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'type' => ['required', 'in:discount,gift'],
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $meta = $this->getMeta($request);

        if ($request->type === 'discount') {
            $meta['discount_code'] = null;
            $meta['discount_type'] = null;
        } else {
            $meta['gift_code'] = null;
        }

        $this->setMeta($request, $meta);

        return response()->json(['message' => 'کد با موفقیت حذف شد']);
    }

    public function selectShipping(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'shipping_method_id' => ['required', 'exists:shipping_configs,id'],
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $method = ShippingConfig::where('is_active', true)->find($request->shipping_method_id);
        if (!$method) {
            return response()->json(['message' => 'روش ارسال نامعتبر است'], 422);
        }

        $meta = $this->getMeta($request);
        $meta['shipping_method_id'] = (int) $request->shipping_method_id;
        $this->setMeta($request, $meta);

        return response()->json(['message' => 'روش ارسال با موفقیت انتخاب شد']);
    }

    public function sync(Request $request): JsonResponse
    {
        $items = $this->getItems($request);
        if (empty($items)) {
            return response()->json(['message' => 'سبد خرید خالی است'], 422);
        }

        $productIds = array_unique(array_map(fn($i) => $i['product_id'], $items));
        $products = Product::where('status', 'active')
            ->whereIn('id', $productIds)
            ->with('typeOfWeights')
            ->get()
            ->keyBy('id');

        $updated = false;
        foreach ($items as $itemId => $item) {
            $product = $products->get($item['product_id']);
            if (!$product) {
                unset($items[$itemId]);
                $updated = true;
                continue;
            }

            if (!empty($item['type_of_weight_id'])) {
                $pivot = $product->typeOfWeights->firstWhere('id', $item['type_of_weight_id']);
                if (!$pivot) {
                    unset($items[$itemId]);
                    $updated = true;
                    continue;
                }
                if ((int) $pivot->pivot->stock < $item['quantity']) {
                    $items[$itemId]['quantity'] = max(1, (int) $pivot->pivot->stock);
                    $updated = true;
                }
            } else {
                if ((int) $product->stock < $item['quantity']) {
                    $items[$itemId]['quantity'] = max(1, (int) $product->stock);
                    $updated = true;
                }
            }
        }

        if ($updated) {
            $this->setItems($request, $items);
        }

        return response()->json([
            'message' => $updated ? 'موجودی برخی محصولات به‌روزرسانی شد' : 'همه محصولات به‌روز هستند',
            'synced' => $updated,
        ]);
    }

    public function checkout(Request $request): JsonResponse
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['message' => 'برای ثبت سفارش باید وارد شوید'], 401);
        }

        $items = $this->getItems($request);
        if (empty($items)) {
            return response()->json(['message' => 'سبد خرید خالی است'], 422);
        }

        $validator = Validator::make($request->all(), [
            'address_id' => ['required', 'exists:addresses,id'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $address = $user->addresses()->where('is_active', true)->findOrFail($request->address_id);
        $meta = $this->getMeta($request);

        return DB::transaction(function () use ($request, $user, $address, $items, $meta) {
            $totalAmount = 0;
            $totalWeight = 0;
            $orderProducts = [];
            $productIds = array_unique(array_map(fn($i) => $i['product_id'], $items));

            $products = Product::where('status', 'active')
                ->whereIn('id', $productIds)
                ->with('typeOfWeights')
                ->get()
                ->keyBy('id');

            foreach ($items as $item) {
                $product = $products->get($item['product_id']);
                if (!$product) {
                    return response()->json(['message' => 'محصولی در سبد خرید وجود دارد که دیگر موجود نیست'], 422);
                }

                $quantity = max(1, (int) $item['quantity']);
                $unitPrice = (int) $product->price;
                $discountedPrice = (int) $product->price_discounted;
                $weight = 0;

                if (!empty($item['type_of_weight_id'])) {
                    $pivot = $product->typeOfWeights->firstWhere('id', $item['type_of_weight_id']);
                    if (!$pivot) {
                        return response()->json(['message' => "تنوع وزنی برای محصول {$product->title} یافت نشد"], 422);
                    }
                    if ((int) $pivot->pivot->stock < $quantity) {
                        return response()->json(['message' => "موجودی {$pivot->title} برای محصول {$product->title} ناکافی است"], 422);
                    }
                    $pivotPrice = (int) $pivot->pivot->price;
                    $pivotDiscounted = (int) $pivot->pivot->price_discounted;
                    $unitPrice = $pivotDiscounted > 0 ? $pivotDiscounted : $pivotPrice;
                    $weight = (int) $pivot->weight;
                } else {
                    if ((int) $product->stock < $quantity) {
                        return response()->json(['message' => "موجودی محصول {$product->title} ناکافی است"], 422);
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

            $discountAmount = 0;
            if (!empty($meta['discount_code']) && $meta['discount_type'] === 'discount') {
                $result = $this->validateDiscountCode($meta['discount_code'], $totalAmount, $request);
                if (!isset($result['error'])) {
                    $discountAmount = $result['amount'];
                }
            }
            if (!empty($meta['gift_code'])) {
                $result = $this->validateGiftCode($meta['gift_code'], $totalAmount, $request);
                if (!isset($result['error'])) {
                    $discountAmount += $result['amount'];
                }
            }

            $shippingFee = 0;
            if (!empty($meta['shipping_method_id'])) {
                $method = ShippingConfig::find($meta['shipping_method_id']);
                if ($method) {
                    $shippingFee = $this->calculateShippingCost($method, $totalWeight);
                }
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
                'final_amount' => $finalAmount,
                'total_weight' => $totalWeight,
                'payment_status' => 'pending',
                'shipping_status' => Order::STATUS_PENDING_REVIEW,
                'shipping_method' => $method->name ?? null,
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
                'note' => 'سفارش از سبد خرید ثبت شد',
            ]);

            $request->session()->forget('cart');

            $order->load(['items', 'histories']);

            return response()->json([
                'message' => 'سفارش با موفقیت ثبت شد',
                'data' => new \App\Http\Resources\OrderResource($order),
            ], 201);
        });
    }

    public function itemCount(Request $request): JsonResponse
    {
        $items = $this->getItems($request);
        $count = array_sum(array_map(fn($i) => $i['quantity'], $items));

        return response()->json(['data' => ['count' => $count]]);
    }

    private function getRawTotal(array $items): int
    {
        if (empty($items)) return 0;

        $productIds = array_unique(array_map(fn($i) => $i['product_id'], $items));
        $products = Product::where('status', 'active')
            ->whereIn('id', $productIds)
            ->with('typeOfWeights')
            ->get()
            ->keyBy('id');

        $total = 0;
        foreach ($items as $item) {
            $product = $products->get($item['product_id']);
            if (!$product) continue;

            $qty = max(1, (int) $item['quantity']);

            if (!empty($item['type_of_weight_id'])) {
                $pivot = $product->typeOfWeights->firstWhere('id', $item['type_of_weight_id']);
                if ($pivot) {
                    $price = (int) $pivot->pivot->price_discounted > 0 ? (int) $pivot->pivot->price_discounted : (int) $pivot->pivot->price;
                    $total += $price * $qty;
                }
            } else {
                $price = (int) $product->price_discounted > 0 ? (int) $product->price_discounted : (int) $product->price;
                $total += $price * $qty;
            }
        }

        return $total;
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

    private function validateDiscountCode(string $code, int $totalAmount, $request): array
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

        return [
            'amount' => $amount,
            'title' => $discount->title,
            'description' => $discount->valueLabel(),
        ];
    }

    private function validateGiftCode(string $code, int $totalAmount, $request): array
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

        if (!empty($gift->user_ids) && $request->user() && !in_array($request->user()->id, $gift->user_ids)) {
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

        return [
            'amount' => $amount,
            'title' => $gift->title,
            'description' => $gift->valueLabel(),
        ];
    }
}
