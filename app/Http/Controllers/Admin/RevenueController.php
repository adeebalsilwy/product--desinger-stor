<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class RevenueController extends Controller
{
    public function index()
    {
        $now = Carbon::now();

        // Base query for monthly grouping
        $monthlyQuery = Order::select(
            DB::raw('DATE_FORMAT(created_at, "%Y-%m") as date'),
            DB::raw('SUM(total_amount) as income'),
            DB::raw('SUM(total_tshirts) as tshirts'),
            DB::raw('COUNT(*) as orders'),
            DB::raw('SUM(SUM(total_amount)) OVER (ORDER BY DATE_FORMAT(created_at, "%Y-%m")) as cumulative_income')
        )
            ->where('status', '!=', 'cancelled')
            ->where('payment_status', '=', 'paid')
            ->groupBy('date');

        // Modified query for daily grouping
        $dailyQuery = Order::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('SUM(total_amount) as income'),
            DB::raw('SUM(total_tshirts) as tshirts'),
            DB::raw('COUNT(*) as orders'),
            DB::raw('SUM(SUM(total_amount)) OVER (ORDER BY DATE(created_at)) as cumulative_income')
        )
            ->where('status', '!=', 'cancelled')
            ->where('payment_status', '=', 'paid')
            ->groupBy('date');

        return inertia('Admin/Revenue', [
            'allTimeData' => $monthlyQuery->get(),
            'lastYearData' => $monthlyQuery->clone()
                ->whereBetween('created_at', [
                    $now->copy()->subYear()->startOfYear(),
                    $now->copy()->subYear()->endOfYear()
                ])
                ->get(),
            'lastMonthData' => $dailyQuery->clone()
                ->whereBetween('created_at', [
                    $now->copy()->subMonth()->startOfMonth(),
                    $now->copy()->subMonth()->endOfMonth()
                ])
                ->get(),
            'lastWeekData' => $dailyQuery->clone()
                ->whereBetween('created_at', [
                    $now->copy()->subWeek()->startOfWeek(),
                    $now->copy()->subWeek()->endOfWeek()
                ])
                ->get(),
            'orders_count' => Order::count(),
            'customers_count' => \App\Models\Customer::count(),
            'tshirts_count' => \App\Models\Product::count(),
            'revenue' => Order::sum('total_amount'),
        ]);
    }
}
