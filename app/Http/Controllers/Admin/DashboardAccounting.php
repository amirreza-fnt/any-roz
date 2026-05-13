<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardAccounting extends Controller
{
    public function __invoke(Request $request)
    {
        return view('backend.dashboard.accounting');
    }
}
