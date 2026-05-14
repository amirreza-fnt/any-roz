<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MarketingSale;
use Illuminate\Http\Request;

class DashboardAccounting extends Controller
{
    public function __invoke(Request $request)
    {
        $pendingMarketingSales = MarketingSale::query()
            ->where('status', MarketingSale::STATUS_PENDING)
            ->count();

        return view('backend.dashboard.accounting', compact('pendingMarketingSales'));
    }
}
