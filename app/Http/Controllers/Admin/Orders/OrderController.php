<?php

namespace App\Http\Controllers\Admin\Orders;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderHistory;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::query()
            ->with(['user'])
            ->withCount('items')
            ->orderByDesc('id')
            ->get();

        $supplyMode = false;
        $pageTitle = 'فاکتورها و سفارشات';

        return view('backend.orders.index', compact('orders', 'supplyMode', 'pageTitle'));
    }

    public function supplyIndex()
    {
        $orders = Order::query()
            ->with(['user'])
            ->withCount('items')
            ->where('sent_to_supply', true)
            ->orderByDesc('sent_to_supply_at')
            ->orderByDesc('id')
            ->get();

        $supplyMode = true;
        $pageTitle = 'فاکتورهای ارسال‌شده به تأمین';

        return view('backend.orders.index', compact('orders', 'supplyMode', 'pageTitle'));
    }

    public function show(Order $order)
    {
        $order->load(['user', 'items.product', 'histories.user']);

        return view('backend.orders.show', compact('order'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $data = $request->validate([
            'shipping_status' => ['required', Rule::in(Order::SHIPPING_STATUSES)],
            'shipping_tracking_code' => 'nullable|string|max:100',
            'note' => 'nullable|string|max:2000',
        ]);

        $prev = $order->shipping_status;
        $order->shipping_status = $data['shipping_status'];
        $order->shipping_tracking_code = $data['shipping_tracking_code'] ?? null;
        $order->save();

        $line = 'وضعیت: '.Order::shippingStatusLabel($prev).' ← '.Order::shippingStatusLabel($data['shipping_status']);
        $note = $line;
        if (! empty($data['note'])) {
            $note .= "\n".$data['note'];
        }

        OrderHistory::create([
            'order_id' => $order->id,
            'user_id' => auth()->id(),
            'status' => 'shipping_status_changed',
            'note' => $note,
        ]);

        message('success', 'وضعیت سفارش به‌روزرسانی شد.');

        return redirect()->back();
    }

    public function toggleSupply(Request $request, Order $order)
    {
        $data = $request->validate([
            'note' => 'nullable|string|max:2000',
        ]);

        if ($order->sent_to_supply) {
            $order->sent_to_supply = false;
            $order->sent_to_supply_at = null;
            $order->save();

            OrderHistory::create([
                'order_id' => $order->id,
                'user_id' => auth()->id(),
                'status' => 'recalled_from_supply',
                'note' => ! empty($data['note']) ? $data['note'] : 'بازگشت از بخش تأمین',
            ]);

            message('success', 'فاکتور از بخش تأمین خارج شد.');
        } else {
            $order->sent_to_supply = true;
            $order->sent_to_supply_at = now();
            $order->save();

            OrderHistory::create([
                'order_id' => $order->id,
                'user_id' => auth()->id(),
                'status' => 'sent_to_supply',
                'note' => ! empty($data['note']) ? $data['note'] : 'ارسال به بخش تأمین',
            ]);

            message('success', 'فاکتور به بخش تأمین ارسال شد.');
        }

        return redirect()->back();
    }
}
