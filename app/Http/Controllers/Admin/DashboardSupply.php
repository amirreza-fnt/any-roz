<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardSupply extends Controller
{
    public function __invoke(Request $request)
    {
        return redirect()->route('admin.orders.supply');
    }
}
