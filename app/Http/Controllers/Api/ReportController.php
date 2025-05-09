<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class ReportController extends Controller
{
    public function salesReport(Request $request)
    {
        $request->validate([
            'date_from' => 'required|date',
            'date_to' => 'required|date|after_or_equal:date_from',
        ]);

        $dateFrom = Carbon::parse($request->date_from)->startOfDay();
        $dateTo = Carbon::parse($request->date_to)->endOfDay();

        $totalSales = Order::where('created_at', '>=', $dateFrom)
            ->where('created_at', '<=', $dateTo)
            ->where('status', '=', 'confirmed')
            ->sum('total_amount');


        return response()->json([
            'date_from' => $dateFrom,
            'date_to' => $dateTo,
            'total_sales' => $totalSales,
        ]);
    }
}
