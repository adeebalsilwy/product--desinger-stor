<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Order;
use App\Models\Product;
use App\Models\SavedDesign;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // Get basic stats - ensure all values are properly cast to numbers
        $stats = [
            'total_users' => (int) User::count(),
            'active_users' => (int) User::where('is_active', true)->count(),
            'total_orders' => (int) Order::count(),
            'pending_orders' => (int) Order::where('status', 'pending')->count(),
            'completed_orders' => (int) Order::where('status', 'delivered')->count(),
            'total_products' => (int) Product::count(),
            'total_designs' => (int) SavedDesign::count(),
            'revenue_today' => (float) Order::whereDate('created_at', today())->sum('total_amount'),
            'revenue_month' => (float) Order::whereMonth('created_at', today()->month)
                                   ->whereYear('created_at', today()->year)
                                   ->sum('total_amount'),
        ];

        // Get recent orders
        $recentOrders = Order::with(['customer', 'items'])
            ->latest()
            ->take(10)
            ->get();

        // Get recent users
        $recentUsers = User::latest()->take(10)->get();

        // Get recent designs
        $recentDesigns = SavedDesign::with('user', 'productType')
            ->latest()
            ->take(10)
            ->get();

        // Get monthly revenue chart data
        $monthlyRevenue = $this->getMonthlyRevenueData();

        // Get order status distribution
        $orderStatuses = $this->getOrderStatusDistribution();

        return Inertia::render('Admin/Dashboard', [
            'stats' => $stats,
            'recentOrders' => $recentOrders,
            'recentUsers' => $recentUsers,
            'recentDesigns' => $recentDesigns,
            'monthlyRevenue' => $monthlyRevenue,
            'orderStatuses' => $orderStatuses,
            'orders_count' => (int) Order::count(),
            'customers_count' => (int) \App\Models\Customer::count(),
            'tshirts_count' => (int) Product::count(),
            'revenue' => (float) Order::sum('total_amount'),
        ]);
    }

    private function getMonthlyRevenueData()
    {
        $data = [];
        for ($i = 11; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $revenue = Order::whereYear('created_at', $month->year)
                          ->whereMonth('created_at', $month->month)
                          ->sum('total_amount');
            
            $data[] = [
                'month' => $month->format('M Y'),
                'revenue' => (float) $revenue,  // Ensure it's cast to float
            ];
        }
        
        return $data;
    }

    private function getOrderStatusDistribution()
    {
        $results = Order::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->get();
            
        $distribution = [];
        foreach ($results as $item) {
            $distribution[$item->status] = (int) $item->count;
        }
        
        return $distribution;
    }

    public function analytics(Request $request)
    {
        $startDate = $request->get('start_date', now()->subDays(30)->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));

        $analytics = [
            'revenue' => (float) Order::whereBetween('created_at', [$startDate, $endDate])
                             ->where('payment_status', 'paid')
                             ->sum('total_amount'),
            'orders' => (int) Order::whereBetween('created_at', [$startDate, $endDate])
                            ->count(),
            'users' => (int) User::whereBetween('created_at', [$startDate, $endDate])
                          ->count(),
            'designs' => (int) SavedDesign::whereBetween('created_at', [$startDate, $endDate])
                                  ->count(),
        ];

        // Daily revenue chart
        $dailyRevenue = Order::selectRaw('DATE(created_at) as date, SUM(total_amount) as total')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->where('payment_status', 'paid')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->map(function ($item) {
                return [
                    'date' => $item->date,
                    'total' => (float) $item->total,
                ];
            });

        return Inertia::render('Admin/Analytics', [
            'analytics' => $analytics,
            'dailyRevenue' => $dailyRevenue,
            'filters' => [
                'start_date' => $startDate,
                'end_date' => $endDate,
            ],
            'orders_count' => (int) Order::count(),
            'customers_count' => (int) \App\Models\Customer::count(),
            'tshirts_count' => (int) Product::count(),
            'revenue' => (float) Order::sum('total_amount'),
        ]);
    }
}