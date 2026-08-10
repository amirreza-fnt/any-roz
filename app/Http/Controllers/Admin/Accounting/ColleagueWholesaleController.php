<?php

namespace App\Http\Controllers\Admin\Accounting;

use App\Http\Controllers\Controller;
use App\Models\MarketingSale;
use App\Services\Marketing\ApproveMarketingSaleService;
use Illuminate\Http\Request;

class ColleagueWholesaleController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->string('tab', 'pending')->toString();
        if (! in_array($tab, ['pending', 'approved', 'rejected'], true)) {
            $tab = 'pending';
        }

        $base = MarketingSale::query()
            ->where('sale_type', MarketingSale::SALE_TYPE_COLLEAGUE)
            ->with(['colleague', 'items.product']);

        $sales = match ($tab) {
            'pending' => (clone $base)->where('status', MarketingSale::STATUS_PENDING)->orderByDesc('id')->get(),
            'approved' => (clone $base)->where('status', MarketingSale::STATUS_APPROVED)->orderByDesc('id')->get(),
            'rejected' => (clone $base)->where('status', MarketingSale::STATUS_REJECTED)->orderByDesc('id')->get(),
            default => collect(),
        };

        $counts = [
            'pending' => MarketingSale::query()->where('sale_type', MarketingSale::SALE_TYPE_COLLEAGUE)->where('status', MarketingSale::STATUS_PENDING)->count(),
            'approved' => MarketingSale::query()->where('sale_type', MarketingSale::SALE_TYPE_COLLEAGUE)->where('status', MarketingSale::STATUS_APPROVED)->count(),
            'rejected' => MarketingSale::query()->where('sale_type', MarketingSale::SALE_TYPE_COLLEAGUE)->where('status', MarketingSale::STATUS_REJECTED)->count(),
        ];

        return view('backend.accounting.colleague_wholesale.index', compact('sales', 'tab', 'counts'));
    }

    public function show(MarketingSale $marketing_sale)
    {
        $marketing_sale->load(['colleague.province', 'colleague.city', 'items.product.images', 'order', 'reviewer']);

        return view('backend.accounting.colleague_wholesale.show', ['sale' => $marketing_sale]);
    }

    public function approve(Request $request, MarketingSale $marketing_sale, ApproveMarketingSaleService $service)
    {
        $request->validate([
            'accountant_note' => 'nullable|string|max:2000',
        ]);

        if (! $marketing_sale->canApprove() || ! $marketing_sale->isColleagueSale()) {
            message('warning', 'این پیش‌فاکتور در وضعیت فعلی قابل تأیید نیست.');

            return redirect()->route('admin.accounting.colleague-wholesale.show', $marketing_sale);
        }

        try {
            $order = $service->approve($marketing_sale, $request->input('accountant_note'));
        } catch (\Throwable $e) {
            message('error', $e->getMessage());

            return redirect()->back()->withInput();
        }

        message('success', 'خرید کلی تأیید شد و فاکتور شمارهٔ '.$order->order_number.' ایجاد گردید.');

        return redirect()
            ->route('admin.accounting.colleague-wholesale.show', $marketing_sale)
            ->with('approved_order_id', $order->id)
            ->with('approved_order_number', $order->order_number);
    }

    public function reject(Request $request, MarketingSale $marketing_sale, ApproveMarketingSaleService $service)
    {
        $data = $request->validate([
            'accountant_note' => 'nullable|string|max:2000',
        ]);

        if (! $marketing_sale->canReject() || ! $marketing_sale->isColleagueSale()) {
            message('warning', 'این پیش‌فاکتور در وضعیت فعلی قابل رد نیست.');

            return redirect()->route('admin.accounting.colleague-wholesale.show', $marketing_sale);
        }

        try {
            $service->reject($marketing_sale, $data['accountant_note'] ?? null);
        } catch (\Throwable $e) {
            message('error', $e->getMessage());

            return redirect()->back()->withInput();
        }

        message('success', 'خرید کلی رد شد.');

        return redirect()->route('admin.accounting.colleague-wholesale.index', ['tab' => 'rejected']);
    }
}