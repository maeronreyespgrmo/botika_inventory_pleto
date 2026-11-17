<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function __invoke()
    {
        return view('dashboard', [
            'medicineCount' => DB::table('medicines')->count(),
            'activeCount' => DB::table('medicines')->where('is_active', true)->count(),
            'supplierCount' => DB::table('suppliers')->count(),
            'purchaseCount' => DB::table('purchases')->count(),
            'medicines' => DB::table('medicines')->latest('id')->limit(8)->get(),
        ]);
    }
}
