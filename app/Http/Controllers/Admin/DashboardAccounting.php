<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\Accounting\AccountingAnalyticsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class DashboardAccounting extends Controller
{
    /**
     * مرکز یکپارچهٔ حسابداری (ناوبری + خلاصهٔ کل).
     */
    public function __invoke(Request $request, AccountingAnalyticsService $analytics)
    {
        $period = $analytics->parsePeriod($request, 30);
        $from = $period['from'];
        $to = $period['to'];

        $unified = $analytics->financialSnapshot($from, $to, null);
        $sitePart = $analytics->financialSnapshot($from, $to, Order::SOURCE_SITE);
        $mktPart = $analytics->financialSnapshot($from, $to, Order::SOURCE_MARKETING);
        $bySource = $analytics->revenueBySource($from, $to);
        $sparkStart = $to->copy()->subDays(6)->startOfDay();
        if ($sparkStart->lt($from)) {
            $sparkStart = $from->copy()->startOfDay();
        }
        $spark = $analytics->dailyRevenueSeries($sparkStart, $to, null);

        $pendingMarketingSales = 0;
        if (Schema::hasTable('marketing_sales')) {
            $pendingMarketingSales = (int) \Illuminate\Support\Facades\DB::table('marketing_sales')
                ->where('status', 'pending_accounting')
                ->count();
        }

        return view('backend.dashboard.accounting', compact(
            'from',
            'to',
            'unified',
            'sitePart',
            'mktPart',
            'bySource',
            'spark',
            'pendingMarketingSales'
        ));
    }
}
