<?php

namespace App\Http\Controllers\Admin\Marketing;

use App\Http\Controllers\Controller;
use App\Models\MarketingBuyer;
use App\Models\MarketingSale;
use Illuminate\Http\Request;

class MarketingPanelController extends Controller
{
    public function __invoke(Request $request)
    {
        $mid = (int) config('marketing.default_marketer_id');

        $buyersCount = MarketingBuyer::query()->where('marketer_id', $mid)->count();
        $salesPending = MarketingSale::query()->where('marketer_id', $mid)->where('status', MarketingSale::STATUS_PENDING)->count();
        $salesApproved = MarketingSale::query()->where('marketer_id', $mid)->where('status', MarketingSale::STATUS_APPROVED)->count();
        $salesRejected = MarketingSale::query()->where('marketer_id', $mid)->where('status', MarketingSale::STATUS_REJECTED)->count();

        return view('backend.marketing.panel.dashboard', compact(
            'buyersCount',
            'salesPending',
            'salesApproved',
            'salesRejected',
            'mid'
        ));
    }
}
