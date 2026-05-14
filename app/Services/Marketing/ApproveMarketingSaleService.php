<?php

namespace App\Services\Marketing;

use App\Models\MarketingSale;
use App\Models\Order;
use App\Models\OrderHistory;
use App\Models\OrderProduct;
use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use RuntimeException;

class ApproveMarketingSaleService
{
    public function approve(MarketingSale $sale, ?string $accountantNote = null): Order
    {
        if (! $sale->canApprove()) {
            throw new RuntimeException('این فروش در وضعیت فعلی قابل تأیید نیست.');
        }

        return DB::transaction(function () use ($sale, $accountantNote) {
            /** @var MarketingSale $sale */
            $sale = MarketingSale::query()->lockForUpdate()->findOrFail($sale->id);

            if (! $sale->canApprove()) {
                throw new RuntimeException('این فروش در وضعیت فعلی قابل تأیید نیست.');
            }

            $sale->load(['items.product.images', 'buyer.province', 'buyer.city']);

            if ($sale->order_id) {
                $existing = Order::query()->lockForUpdate()->find($sale->order_id);
                if ($existing && $existing->shipping_status !== Order::STATUS_CANCELLED) {
                    throw new RuntimeException('برای این فروش فاکتور فعال وجود دارد؛ ابتدا آن را رد کنید.');
                }
            }

            $order = $this->createOrderForSale($sale);

            $sale->update([
                'status' => MarketingSale::STATUS_APPROVED,
                'order_id' => $order->id,
                'accountant_note' => $accountantNote,
                'reviewed_at' => now(),
                'reviewed_by' => auth('admin')->id(),
            ]);

            OrderHistory::create([
                'order_id' => $order->id,
                'user_id' => null,
                'admin_id' => auth('admin')->id(),
                'status' => 'marketing_sale_approved',
                'note' => 'سفارش از تأیید فروش بازاریابی شمارهٔ '.$sale->id.' ایجاد شد.',
            ]);

            return $order->fresh(['items']);
        });
    }

    public function reject(MarketingSale $sale, ?string $accountantNote = null): void
    {
        if (! $sale->canReject()) {
            throw new RuntimeException('این فروش در وضعیت فعلی قابل رد نیست.');
        }

        DB::transaction(function () use ($sale, $accountantNote) {
            /** @var MarketingSale $sale */
            $sale = MarketingSale::query()->lockForUpdate()->findOrFail($sale->id);

            if (! $sale->canReject()) {
                throw new RuntimeException('این فروش در وضعیت فعلی قابل رد نیست.');
            }

            if ($sale->status === MarketingSale::STATUS_APPROVED && $sale->order_id) {
                $order = Order::query()->lockForUpdate()->find($sale->order_id);
                if ($order) {
                    $order->shipping_status = Order::STATUS_CANCELLED;
                    $order->sent_to_supply = false;
                    $order->sent_to_supply_at = null;
                    $order->save();

                    OrderHistory::create([
                        'order_id' => $order->id,
                        'user_id' => null,
                        'admin_id' => auth('admin')->id(),
                        'status' => 'marketing_sale_rejected',
                        'note' => 'فروش بازاریابی شمارهٔ '.$sale->id.' توسط حسابدار رد شد.',
                    ]);
                }
            }

            $sale->update([
                'status' => MarketingSale::STATUS_REJECTED,
                'accountant_note' => $accountantNote,
                'reviewed_at' => now(),
                'reviewed_by' => auth('admin')->id(),
            ]);
        });
    }

    private function createOrderForSale(MarketingSale $sale): Order
    {
        $buyer = $sale->buyer;
        $total = (int) $sale->items->sum('unit_price');
        $totalDecimal = number_format($total, 2, '.', '');

        $userId = $this->resolveOrderUserId();
        $orderNumber = $this->uniqueOrderNumber();

        $order = Order::create([
            'user_id' => $userId,
            'order_number' => $orderNumber,
            'total_amount' => $totalDecimal,
            'shipping_fee' => '0.00',
            'discount_amount' => '0.00',
            'shipping_cost' => '0.00',
            'insurance_cost' => '0.00',
            'final_amount' => $totalDecimal,
            'total_weight' => '0.00',
            'payment_status' => 'paid',
            'payment_method' => Str::limit((string) ($sale->payment_method ?? ''), 50),
            'payment_transaction_id' => null,
            'payment_date' => $this->safeOrderPaymentDate($sale),
            'shipping_status' => Order::STATUS_PENDING_REVIEW,
            'shipping_method' => 'فروش بازاریابی',
            'shipping_address' => $buyer->address ?: '—',
            'shipping_city' => $buyer->city?->name ?? '—',
            'shipping_state' => $buyer->province?->name ?? '—',
            'shipping_postal_code' => $buyer->postal_code ?: '0000000000',
            'shipping_recipient_name' => trim($sale->buyer_first_name.' '.$sale->buyer_last_name),
            'shipping_phone' => $sale->buyer_phone,
            'shipping_tracking_code' => null,
            'notes' => $this->composeOrderNotes($sale),
            'sent_to_supply' => true,
            'sent_to_supply_at' => now(),
            'source' => Order::SOURCE_MARKETING,
            'marketer_id' => $sale->marketer_id,
            'marketing_sale_id' => $sale->id,
        ]);

        foreach ($sale->items as $line) {
            $product = $line->product;
            if (! $product) {
                throw new RuntimeException('یکی از اقلام فروش به محصول حذف‌شده یا نامعتبر لینک شده است؛ ابتدا اقلام را اصلاح کنید.');
            }

            $imagePath = $product->images->first()?->path;

            OrderProduct::create([
                'order_id' => $order->id,
                'product_id' => $product->id,
                'product_name' => $product->title,
                'product_code' => $product->tracking_code,
                'product_image' => $imagePath,
                'quantity' => 1,
                'unit_price' => (string) $line->unit_price,
                'discount_percent' => 0,
                'discount_amount' => 0,
                'final_price' => (int) $line->unit_price,
                'product_options' => [
                    'quantity_text' => $line->quantity_text,
                    'marketing_sale_item_id' => $line->id,
                    'marketer_id' => $sale->marketer_id,
                ],
            ]);
        }

        return $order;
    }

    /**
     * تاریخ پرداخت فاکتور باید میلادی معتبر برای MySQL باشد؛ دادهٔ قدیمی/اشتباه فروش
     * (مثلاً سال شمسی به‌جای میلادی در فیلد) را به «امروز» برمی‌گردانیم.
     */
    private function safeOrderPaymentDate(MarketingSale $sale): Carbon
    {
        $d = $sale->sale_date;
        if ($d === null) {
            return now()->startOfDay();
        }

        $c = $d instanceof CarbonInterface ? Carbon::instance($d) : Carbon::parse($d);
        $y = (int) $c->year;
        if ($y < 1990 || $y > 2100) {
            return now()->startOfDay();
        }

        return $c->copy()->startOfDay();
    }

    private function composeOrderNotes(MarketingSale $sale): string
    {
        $parts = [];
        if ($sale->notes) {
            $parts[] = trim($sale->notes);
        }
        if ($sale->buyer_store_name) {
            $parts[] = 'نام مغازه: '.$sale->buyer_store_name;
        }
        $parts[] = 'شناسهٔ فروش بازاریابی: '.$sale->id;

        return implode("\n", array_filter($parts));
    }

    private function resolveOrderUserId(): int
    {
        $id = (int) config('marketing.order_owner_user_id');
        if (User::query()->whereKey($id)->exists()) {
            return $id;
        }

        $first = User::query()->orderBy('id')->value('id');
        if (! $first) {
            throw new RuntimeException('هیچ کاربری در سیستم ثبت نشده است؛ امکان ایجاد سفارش وجود ندارد.');
        }

        return (int) $first;
    }

    private function uniqueOrderNumber(): string
    {
        do {
            $num = 'MKT-'.now()->format('Ymd').'-'.strtoupper(Str::random(5));
        } while (Order::query()->where('order_number', $num)->exists());

        return $num;
    }
}
