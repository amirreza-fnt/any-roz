<?php

namespace App\Http\Controllers\Admin\Accounting;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\Accounting\AccountingAnalyticsService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AccountingProfessionalController extends Controller
{
    public function index(Request $request, AccountingAnalyticsService $analytics)
    {
        $period = $analytics->parsePeriod($request, 30);
        $from = $period['from'];
        $to = $period['to'];

        $snapshot = $analytics->financialSnapshot($from, $to, null);
        $snapshotSite = $analytics->financialSnapshot($from, $to, Order::SOURCE_SITE);
        $snapshotMkt = $analytics->financialSnapshot($from, $to, Order::SOURCE_MARKETING);

        $daily = $analytics->dailyRevenueSeries($from, $to, null);
        $monthly = $analytics->monthlyRevenueLast12(null);
        $dailyChart = $analytics->compressDailySeriesForChart($daily);
        $monthlyChart = $analytics->compressMonthlySeriesForChart($monthly);
        $byStatus = $analytics->ordersByShippingStatus($from, $to, null);
        $bySource = $analytics->revenueBySource($from, $to);

        return view('backend.accounting.professional.index', [
            'from' => $from,
            'to' => $to,
            'snapshot' => $snapshot,
            'snapshotSite' => $snapshotSite,
            'snapshotMkt' => $snapshotMkt,
            'daily' => $daily,
            'dailyChart' => $dailyChart,
            'monthly' => $monthly,
            'monthlyChart' => $monthlyChart,
            'byStatus' => $byStatus,
            'bySource' => $bySource,
        ]);
    }

    public function export(Request $request, AccountingAnalyticsService $analytics): StreamedResponse
    {
        $period = $analytics->parsePeriod($request, 90);
        $from = $period['from'];
        $to = $period['to'];

        $snapshot = $analytics->financialSnapshot($from, $to, null);
        $daily = $analytics->dailyRevenueSeries($from, $to, null);

        $filename = 'accounting-report-'.$from->format('Y-m-d').'_to_'.$to->format('Y-m-d').'.csv';

        return response()->streamDownload(function () use ($snapshot, $daily) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF");
            fputcsv($out, ['بخش', 'کلید', 'مقدار']);
            foreach ($snapshot as $k => $v) {
                fputcsv($out, ['خلاصه', $k, is_scalar($v) ? (string) $v : json_encode($v, JSON_UNESCAPED_UNICODE)]);
            }
            fputcsv($out, []);
            fputcsv($out, ['روز_میلادی', 'روز_شمسی', 'فروش', 'تعداد سفارش', 'بهای تمام‌شده برآوردی']);
            foreach ($daily as $row) {
                fputcsv($out, [
                    $row['d'],
                    $row['d_label'] ?? '',
                    $row['revenue'],
                    $row['orders'],
                    $row['cogs'],
                ]);
            }
            fclose($out);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}
