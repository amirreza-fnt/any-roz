<?php

namespace App\Http\Controllers\Admin\Accounting;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\Accounting\AccountingAnalyticsService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AccountingSiteSalesController extends Controller
{
    public function index(Request $request, AccountingAnalyticsService $analytics)
    {
        $period = $analytics->parsePeriod($request, 30);
        $from = $period['from'];
        $to = $period['to'];

        $snapshot = $analytics->financialSnapshot($from, $to, Order::SOURCE_SITE);
        $daily = $analytics->dailyRevenueSeries($from, $to, Order::SOURCE_SITE);
        $byStatus = $analytics->ordersByShippingStatus($from, $to, Order::SOURCE_SITE);

        $q = $analytics->siteOrdersQuery($from, $to);

        if ($request->filled('payment_status')) {
            $request->validate([
                'payment_status' => ['string', Rule::in(['pending', 'paid', 'failed', 'refunded'])],
            ]);
            $q->where('payment_status', $request->string('payment_status')->toString());
        }

        if ($request->filled('shipping_status')) {
            $request->validate([
                'shipping_status' => ['string', Rule::in(Order::SHIPPING_STATUSES)],
            ]);
            $q->where('shipping_status', $request->string('shipping_status')->toString());
        }

        $orders = $q->paginate(30)->withQueryString();

        return view('backend.accounting.site_sales.index', [
            'from' => $from,
            'to' => $to,
            'snapshot' => $snapshot,
            'daily' => $daily,
            'byStatus' => $byStatus,
            'orders' => $orders,
        ]);
    }

    public function export(Request $request, AccountingAnalyticsService $analytics): StreamedResponse
    {
        $period = $analytics->parsePeriod($request, 90);
        $from = $period['from'];
        $to = $period['to'];

        $q = $analytics->siteOrdersQuery($from, $to);

        if ($request->filled('payment_status')) {
            $request->validate([
                'payment_status' => ['string', Rule::in(['pending', 'paid', 'failed', 'refunded'])],
            ]);
            $q->where('payment_status', $request->string('payment_status')->toString());
        }

        if ($request->filled('shipping_status')) {
            $request->validate([
                'shipping_status' => ['string', Rule::in(Order::SHIPPING_STATUSES)],
            ]);
            $q->where('shipping_status', $request->string('shipping_status')->toString());
        }

        $orders = $q->withCount('items')->get();

        $filename = 'site-orders-accounting-'.$from->format('Y-m-d').'_to_'.$to->format('Y-m-d').'.csv';

        return response()->streamDownload(function () use ($orders) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF");
            fputcsv($out, [
                'id', 'order_number', 'created_at', 'payment_status', 'shipping_status',
                'final_amount', 'total_amount', 'discount_amount', 'shipping_fee', 'shipping_cost', 'insurance_cost',
                'items_count', 'user_id',
            ]);
            foreach ($orders as $o) {
                fputcsv($out, [
                    $o->id,
                    $o->order_number,
                    (string) $o->created_at,
                    $o->payment_status,
                    $o->shipping_status,
                    (string) $o->final_amount,
                    (string) $o->total_amount,
                    (string) $o->discount_amount,
                    (string) $o->shipping_fee,
                    (string) $o->shipping_cost,
                    (string) $o->insurance_cost,
                    $o->items_count ?? $o->items()->count(),
                    $o->user_id,
                ]);
            }
            fclose($out);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}
