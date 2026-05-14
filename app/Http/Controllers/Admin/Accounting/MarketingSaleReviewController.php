<?php

namespace App\Http\Controllers\Admin\Accounting;

use App\Http\Controllers\Controller;
use App\Models\MarketingSale;
use App\Services\Marketing\ApproveMarketingSaleService;
use Illuminate\Http\Request;

class MarketingSaleReviewController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->string('tab', 'pending')->toString();
        if (! in_array($tab, ['pending', 'approved', 'rejected'], true)) {
            $tab = 'pending';
        }

        $base = MarketingSale::query()->with(['buyer', 'items.product']);

        $sales = match ($tab) {
            'pending' => (clone $base)->where('status', MarketingSale::STATUS_PENDING)->orderByDesc('id')->get(),
            'approved' => (clone $base)->where('status', MarketingSale::STATUS_APPROVED)->orderByDesc('id')->get(),
            'rejected' => (clone $base)->where('status', MarketingSale::STATUS_REJECTED)->orderByDesc('id')->get(),
            default => collect(),
        };

        $counts = [
            'pending' => MarketingSale::query()->where('status', MarketingSale::STATUS_PENDING)->count(),
            'approved' => MarketingSale::query()->where('status', MarketingSale::STATUS_APPROVED)->count(),
            'rejected' => MarketingSale::query()->where('status', MarketingSale::STATUS_REJECTED)->count(),
        ];

        return view('backend.accounting.marketing_sales.index', compact('sales', 'tab', 'counts'));
    }

    public function show(MarketingSale $marketing_sale)
    {
        $marketing_sale->load(['buyer.province', 'buyer.city', 'items.product.images', 'order', 'reviewer']);

        return view('backend.accounting.marketing_sales.show', ['sale' => $marketing_sale]);
    }

    public function approve(Request $request, MarketingSale $marketing_sale, ApproveMarketingSaleService $service)
    {
        $request->validate([
            'accountant_note' => 'nullable|string|max:2000',
        ]);

        if (! $marketing_sale->isPending()) {
            message('warning', 'این فروش قبلاً بررسی شده است.');

            return redirect()->route('admin.accounting.marketing-sales.show', $marketing_sale);
        }

        try {
            $order = $service->approve($marketing_sale, $request->input('accountant_note'));
        } catch (\Throwable $e) {
            message('danger', $e->getMessage());

            return redirect()->back()->withInput();
        }

        message('success', 'فروش تأیید شد و فاکتور شمارهٔ '.$order->order_number.' ایجاد گردید.');

        return redirect()->route('admin.orders.show', $order);
    }

    public function reject(Request $request, MarketingSale $marketing_sale)
    {
        $data = $request->validate([
            'accountant_note' => 'nullable|string|max:2000',
        ]);

        if (! $marketing_sale->isPending()) {
            message('warning', 'این فروش قبلاً بررسی شده است.');

            return redirect()->route('admin.accounting.marketing-sales.show', $marketing_sale);
        }

        $marketing_sale->update([
            'status' => MarketingSale::STATUS_REJECTED,
            'accountant_note' => $data['accountant_note'] ?? null,
            'reviewed_at' => now(),
            'reviewed_by' => auth()->id(),
        ]);

        message('success', 'فروش رد شد.');

        return redirect()->route('admin.accounting.marketing-sales.index', ['tab' => 'rejected']);
    }
}
