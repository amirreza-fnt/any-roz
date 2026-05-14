<?php

namespace App\Services\Accounting;

use App\Models\Order;
use App\Models\OrderProduct;
use App\Support\JalaliCalendar;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * تحلیل مالی سفارش‌ها برای پنل حسابداری (بدون دفتر کل دوبل؛ بر پایهٔ دادهٔ فاکتورهای موجود).
 */
class AccountingAnalyticsService
{
    private function ordersAvailable(): bool
    {
        return Schema::hasTable('orders');
    }

    public function parsePeriod(Request $request, int $defaultDays = 30): array
    {
        $jFrom = trim((string) $request->input('j_date_from', ''));
        $jTo = trim((string) $request->input('j_date_to', ''));
        $toIn = $request->input('date_to');
        $fromIn = $request->input('date_from');

        try {
            if ($jFrom !== '' && $jTo !== '') {
                $from = JalaliCalendar::parseShamsiDateStartOfDay($jFrom);
                $to = JalaliCalendar::parseShamsiDateEndOfDay($jTo);
            } elseif ($jFrom !== '') {
                $from = JalaliCalendar::parseShamsiDateStartOfDay($jFrom);
                $to = $from->copy()->addDays(max(1, $defaultDays) - 1)->endOfDay();
            } elseif ($jTo !== '') {
                $to = JalaliCalendar::parseShamsiDateEndOfDay($jTo);
                $from = $to->copy()->subDays(max(1, $defaultDays) - 1)->startOfDay();
            } elseif ($fromIn || $toIn) {
                $to = $toIn ? Carbon::parse($toIn)->endOfDay() : now()->endOfDay();
                $from = $fromIn
                    ? Carbon::parse($fromIn)->startOfDay()
                    : $to->copy()->subDays(max(1, $defaultDays) - 1)->startOfDay();
            } else {
                $to = now()->endOfDay();
                $from = $to->copy()->subDays(max(1, $defaultDays) - 1)->startOfDay();
            }
        } catch (\Throwable) {
            $to = now()->endOfDay();
            $from = $to->copy()->subDays(max(1, $defaultDays) - 1)->startOfDay();
        }

        if ($from->gt($to)) {
            [$from, $to] = [$to->copy()->startOfDay(), $from->copy()->endOfDay()];
        }

        return ['from' => $from, 'to' => $to];
    }

    /**
     * @param  \Carbon\Carbon  $from
     * @param  \Carbon\Carbon  $to
     * @param  'site'|'marketing'|null  $sourceFilter
     * @return array<string, mixed>
     */
    public function financialSnapshot(Carbon $from, Carbon $to, ?string $sourceFilter = null): array
    {
        if (! $this->ordersAvailable()) {
            return $this->emptyFinancialSnapshot();
        }

        $active = $this->orderScope(Order::query(), $from, $to, $sourceFilter)
            ->where('shipping_status', '!=', Order::STATUS_CANCELLED);

        $revenue = (float) (clone $active)->sum('final_amount');
        $count = (int) (clone $active)->count();
        $avgBasket = $count > 0 ? $revenue / $count : 0.0;

        $discounts = (float) (clone $active)->sum('discount_amount');
        $shippingFee = (float) (clone $active)->sum('shipping_fee');
        $shippingCost = (float) (clone $active)->sum('shipping_cost');
        $insurance = (float) (clone $active)->sum('insurance_cost');

        $cancelled = (int) $this->orderScope(Order::query(), $from, $to, $sourceFilter)
            ->where('shipping_status', Order::STATUS_CANCELLED)
            ->count();

        $paidCount = (int) (clone $active)->where('payment_status', 'paid')->count();

        $cogs = $this->estimatedCogs($from, $to, $sourceFilter);
        $grossProfit = $revenue - $cogs;

        return [
            'revenue_final' => $revenue,
            'order_count' => $count,
            'average_basket' => $avgBasket,
            'total_discount' => $discounts,
            'total_shipping_fee' => $shippingFee,
            'total_shipping_cost' => $shippingCost,
            'total_insurance' => $insurance,
            'estimated_cogs' => $cogs,
            'gross_profit_est' => $grossProfit,
            'cancelled_count' => $cancelled,
            'paid_order_count' => $paidCount,
        ];
    }

    public function estimatedCogs(Carbon $from, Carbon $to, ?string $sourceFilter = null): float
    {
        if (! $this->ordersAvailable() || ! Schema::hasTable('order_products')) {
            return 0.0;
        }

        $q = DB::table('order_products as op')
            ->join('orders as o', 'o.id', '=', 'op.order_id')
            ->leftJoin('products as p', function ($join) {
                $join->on('p.id', '=', 'op.product_id')
                    ->whereNull('p.deleted_at');
            })
            ->whereRaw('COALESCE(o.payment_date, o.created_at) >= ?', [$from->copy()->startOfDay()])
            ->whereRaw('COALESCE(o.payment_date, o.created_at) <= ?', [$to->copy()->endOfDay()])
            ->where('o.shipping_status', '!=', Order::STATUS_CANCELLED);

        if ($sourceFilter !== null) {
            $q->where('o.source', $sourceFilter);
        }

        $raw = $q->selectRaw('SUM(op.quantity * COALESCE(p.price_buy, 0)) as cogs')->value('cogs');

        return (float) ($raw ?? 0);
    }

    /**
     * @return list<array{d:string, d_label:string, revenue:float, orders:int, cogs:float}>
     */
    public function dailyRevenueSeries(Carbon $from, Carbon $to, ?string $sourceFilter = null): array
    {
        if (! $this->ordersAvailable()) {
            return $this->emptyDailySeries($from, $to);
        }

        $rows = $this->orderScope(Order::query(), $from, $to, $sourceFilter)
            ->where('shipping_status', '!=', Order::STATUS_CANCELLED)
            ->get(['id', 'created_at', 'payment_date', 'final_amount']);

        $byDay = [];
        foreach ($rows as $o) {
            $event = $o->payment_date ?? $o->created_at;
            $d = $event->toDateString();
            if (! isset($byDay[$d])) {
                $byDay[$d] = ['revenue' => 0.0, 'orders' => 0, 'ids' => []];
            }
            $byDay[$d]['revenue'] += (float) $o->final_amount;
            $byDay[$d]['ids'][$o->id] = true;
        }

        $cogsByDay = [];
        $orderIds = $rows->pluck('id')->unique()->values()->all();
        if ($orderIds !== []) {
            $chunks = array_chunk($orderIds, 500);
            foreach ($chunks as $chunk) {
                $lines = OrderProduct::query()
                    ->whereIn('order_id', $chunk)
                    ->with(['product' => fn ($q) => $q->withTrashed()])
                    ->get(['order_id', 'quantity', 'product_id']);

                foreach ($lines as $line) {
                    $oid = $line->order_id;
                    $order = $rows->firstWhere('id', $oid);
                    if (! $order) {
                        continue;
                    }
                    $event = $order->payment_date ?? $order->created_at;
                    $d = $event->toDateString();
                    $buy = (int) ($line->product?->price_buy ?? 0);
                    $cogsByDay[$d] = ($cogsByDay[$d] ?? 0) + ($line->quantity * $buy);
                }
            }
        }

        $out = [];
        $cursor = $from->copy()->startOfDay();
        $end = $to->copy()->startOfDay();
        while ($cursor->lte($end)) {
            $key = $cursor->toDateString();
            $bucket = $byDay[$key] ?? ['revenue' => 0.0, 'orders' => 0];
            $ordersCount = isset($bucket['ids']) ? count($bucket['ids']) : ($bucket['orders'] ?? 0);
            $out[] = [
                'd' => $key,
                'd_label' => JalaliCalendar::formatShamsiDate($cursor->copy()),
                'revenue' => (float) ($bucket['revenue'] ?? 0),
                'orders' => $ordersCount,
                'cogs' => (float) ($cogsByDay[$key] ?? 0),
            ];
            $cursor->addDay();
        }

        return $out;
    }

    /**
     * @return list<array{ym:string, ym_label:string, revenue:float, orders:int}>
     */
    public function monthlyRevenueLast12(?string $sourceFilter = null): array
    {
        if (! $this->ordersAvailable()) {
            return [];
        }

        $from = now()->subMonths(11)->startOfMonth()->startOfDay();

        $q = Order::query()
            ->whereRaw('COALESCE(payment_date, created_at) >= ?', [$from])
            ->where('shipping_status', '!=', Order::STATUS_CANCELLED);

        if ($sourceFilter !== null) {
            $q->where('source', $sourceFilter);
        }

        $grouped = $q->get(['created_at', 'payment_date', 'final_amount', 'id'])
            ->groupBy(function (Order $o) {
                $dt = $o->payment_date ?? $o->created_at;

                return $dt->format('Y-m');
            });

        $out = [];
        foreach ($grouped->sortKeys() as $ym => $group) {
            $labelDate = Carbon::createFromFormat('Y-m', $ym)->startOfMonth();
            $out[] = [
                'ym' => $ym,
                'ym_label' => JalaliCalendar::formatShamsiYearMonth($labelDate),
                'revenue' => (float) $group->sum('final_amount'),
                'orders' => $group->unique('id')->count(),
            ];
        }

        return $out;
    }

    /**
     * @return array<string, int>
     */
    public function ordersByShippingStatus(Carbon $from, Carbon $to, ?string $sourceFilter = null): array
    {
        if (! $this->ordersAvailable()) {
            return [];
        }

        $rows = $this->orderScope(Order::query(), $from, $to, $sourceFilter)
            ->selectRaw('shipping_status, COUNT(*) as c')
            ->groupBy('shipping_status')
            ->pluck('c', 'shipping_status')
            ->all();

        $labels = [];
        foreach ($rows as $status => $count) {
            $labels[Order::shippingStatusLabel((string) $status)] = (int) $count;
        }

        return $labels;
    }

    /**
     * @return array{site: float, marketing: float}
     */
    public function revenueBySource(Carbon $from, Carbon $to): array
    {
        if (! $this->ordersAvailable()) {
            return ['site' => 0.0, 'marketing' => 0.0];
        }

        $base = Order::query()
            ->where('shipping_status', '!=', Order::STATUS_CANCELLED);
        $this->applyAccountingDateBetween($base, $from, $to);

        $site = (float) (clone $base)->where('source', Order::SOURCE_SITE)->sum('final_amount');
        $mkt = (float) (clone $base)->where('source', Order::SOURCE_MARKETING)->sum('final_amount');

        return ['site' => $site, 'marketing' => $mkt];
    }

    /**
     * @return Builder<Order>
     */
    public function siteOrdersQuery(Carbon $from, Carbon $to): Builder
    {
        if (! $this->ordersAvailable()) {
            return Order::query()->whereRaw('0 = 1');
        }

        return Order::query()
            ->where('source', Order::SOURCE_SITE)
            ->whereRaw('COALESCE(payment_date, created_at) >= ?', [$from->copy()->startOfDay()])
            ->whereRaw('COALESCE(payment_date, created_at) <= ?', [$to->copy()->endOfDay()])
            ->with(['user'])
            ->withCount('items')
            ->orderByDesc('id');
    }

    private function orderScope(Builder $q, Carbon $from, Carbon $to, ?string $sourceFilter): Builder
    {
        $this->applyAccountingDateBetween($q, $from, $to);
        if ($sourceFilter !== null) {
            $q->where('source', $sourceFilter);
        }

        return $q;
    }

    private function applyAccountingDateBetween(Builder $q, Carbon $from, Carbon $to): void
    {
        $q->whereRaw('COALESCE(payment_date, created_at) >= ?', [$from->copy()->startOfDay()])
            ->whereRaw('COALESCE(payment_date, created_at) <= ?', [$to->copy()->endOfDay()]);
    }

    /**
     * @return array<string, mixed>
     */
    private function emptyFinancialSnapshot(): array
    {
        return [
            'revenue_final' => 0.0,
            'order_count' => 0,
            'average_basket' => 0.0,
            'total_discount' => 0.0,
            'total_shipping_fee' => 0.0,
            'total_shipping_cost' => 0.0,
            'total_insurance' => 0.0,
            'estimated_cogs' => 0.0,
            'gross_profit_est' => 0.0,
            'cancelled_count' => 0,
            'paid_order_count' => 0,
        ];
    }

    /**
     * @return list<array{d:string, d_label:string, revenue:float, orders:int, cogs:float}>
     */
    private function emptyDailySeries(Carbon $from, Carbon $to): array
    {
        $out = [];
        $cursor = $from->copy()->startOfDay();
        $end = $to->copy()->startOfDay();
        while ($cursor->lte($end)) {
            $out[] = [
                'd' => $cursor->toDateString(),
                'd_label' => JalaliCalendar::formatShamsiDate($cursor->copy()),
                'revenue' => 0.0,
                'orders' => 0,
                'cogs' => 0.0,
            ];
            $cursor->addDay();
        }

        return $out;
    }
}
