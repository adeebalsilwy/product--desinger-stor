<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Get user's orders
        $orders = $user->orders()->with('items')->latest()->get();
        
        // Get recent orders (last 5)
        $recentOrders = $user->orders()->with('items')->latest()->limit(5)->get();
        
        // Calculate order statistics
        $orderStats = [
            'total' => $user->orders()->count(),
            'delivered' => $user->orders()->where('status', 'delivered')->count(),
            'pending' => $user->orders()->whereIn('status', ['pending', 'processing'])->count(),
            'total_spent' => $user->orders()->where('payment_status', 'paid')->sum('total_amount'),
        ];
        
        return Inertia::render('Customer/Dashboard', [
            'user' => $user,
            'orders' => $orders,
            'recentOrders' => $recentOrders,
            'orderStats' => $orderStats,
        ]);
    }
}