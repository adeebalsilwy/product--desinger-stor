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
        // Get basic stats - ensure all values are properly cast to numbers and handle potential nulls
        $stats = [
            'total_users' => (int) (User::count() ?: 0),
            'active_users' => (int) (User::where('is_active', true)->count() ?: 0),
            'total_orders' => (int) (Order::count() ?: 0),
            'pending_orders' => (int) (Order::where('status', 'pending')->count() ?: 0),
            'completed_orders' => (int) (Order::where('status', 'delivered')->count() ?: 0),
            'total_products' => (int) (Product::count() ?: 0),
            'total_designs' => (int) (SavedDesign::count() ?: 0),
            'revenue_today' => (float) (Order::whereDate('created_at', today())->sum('total_amount') ?: 0),
            'revenue_month' => (float) (Order::whereMonth('created_at', today()->month)
                                   ->whereYear('created_at', today()->year)
                                   ->sum('total_amount') ?: 0),
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
            'orders_count' => (int) (Order::count() ?: 0),
            'customers_count' => (int) (\App\Models\Customer::count() ?: 0),
            'products_count' => (int) (Product::count() ?: 0),
            'revenue' => (float) (Order::sum('total_amount') ?: 0),
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
                'revenue' => (float) ($revenue ?: 0),  // Ensure it's cast to float and handle nulls
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
            $distribution[$item->status] = (int) ($item->count ?: 0);
        }
        
        // Make sure we have at least one entry to prevent empty charts
        if (empty($distribution)) {
            $distribution['no_data'] = 1;
        }
        
        return $distribution;
    }

    public function analytics(Request $request)
    {
        $startDate = $request->get('start_date', now()->subDays(30)->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));

        $analytics = [
            'revenue' => (float) (Order::whereBetween('created_at', [$startDate, $endDate])
                             ->where('payment_status', 'paid')
                             ->sum('total_amount') ?: 0),
            'orders' => (int) (Order::whereBetween('created_at', [$startDate, $endDate])
                            ->count() ?: 0),
            'users' => (int) (User::whereBetween('created_at', [$startDate, $endDate])
                          ->count() ?: 0),
            'designs' => (int) (SavedDesign::whereBetween('created_at', [$startDate, $endDate])
                                  ->count() ?: 0),
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
                    'total' => (float) ($item->total ?: 0),
                ];
            });

        return Inertia::render('Admin/Analytics', [
            'analytics' => $analytics,
            'dailyRevenue' => $dailyRevenue,
            'filters' => [
                'start_date' => $startDate,
                'end_date' => $endDate,
            ],
            'orders_count' => (int) (Order::count() ?: 0),
            'customers_count' => (int) (\App\Models\Customer::count() ?: 0),
            'products_count' => (int) (Product::count() ?: 0),
            'revenue' => (float) (Order::sum('total_amount') ?: 0),
        ]);
    }
    
    public function stats(Request $request)
    {
        // Get basic stats - ensure all values are properly cast to numbers and handle potential nulls
        $stats = [
            'total_users' => (int) (User::count() ?: 0),
            'total_orders' => (int) (Order::count() ?: 0),
            'pending_orders' => (int) (Order::where('status', 'pending')->count() ?: 0),
            'revenue_month' => (float) (Order::whereMonth('created_at', today()->month)
                                   ->whereYear('created_at', today()->year)
                                   ->sum('total_amount') ?: 0),
        ];

        // Get recent orders
        $recentOrders = Order::with(['customer', 'items'])
            ->latest()
            ->take(5)
            ->get()
            ->map(function ($order) {
                return [
                    'id' => $order->id,
                    'customer_name' => $order->customer->name ?? 'Unknown Customer',
                    'created_at' => $order->created_at,
                    'total_amount' => $order->total_amount,
                    'status' => $order->status,
                ];
            });

        return response()->json([
            'stats' => $stats,
            'recent_orders' => $recentOrders,
        ]);
    }
}