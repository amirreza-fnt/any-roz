<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class DashboardSupply extends Controller
{
    public function __invoke(Request $request)
    {
        $supplyOrdersCount = Order::query()->where('sent_to_supply', true)->count();
        $supplyPendingAmount = Order::query()->where('sent_to_supply', true)->sum('final_amount');

        return view('backend.dashboard.supply', compact('supplyOrdersCount', 'supplyPendingAmount'));
    }
}
