<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SavedDesign;
use App\Models\User;
use App\Models\ProductType;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DesignManagementController extends Controller
{
    public function index(Request $request)
    {
        $query = SavedDesign::with(['user', 'productType']);

        // Search functionality
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($userQuery) use ($search) {
                      $userQuery->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by product type
        if ($request->has('product_type')) {
            $query->where('product_type_id', $request->product_type);
        }

        // Filter by user
        if ($request->has('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filter by visibility
        if ($request->has('is_public')) {
            $query->where('is_public', $request->is_public);
        }

        $designs = $query->orderBy('created_at', 'desc')->paginate(20);

        return Inertia::render('Admin/Designs/Index', [
            'designs' => $designs,
            'filters' => [
                'search' => $request->search,
                'product_type' => $request->product_type,
                'user_id' => $request->user_id,
                'is_public' => $request->is_public,
            ],
            'productTypes' => ProductType::all(),
            'users' => User::all(),
            'orders_count' => \App\Models\Order::count(),
            'customers_count' => \App\Models\Customer::count(),
            'tshirts_count' => \App\Models\Product::count(),
            'revenue' => \App\Models\Order::sum('total_amount'),
        ]);
    }

    public function show(SavedDesign $design)
    {
        $design->load(['user', 'productType', 'orderItems']);

        return Inertia::render('Admin/Designs/Show', [
            'design' => $design,
        ]);
    }

    public function destroy(SavedDesign $design)
    {
        $design->delete();

        return redirect()->route('admin.designs.index')->with('message', 'Design deleted successfully.');
    }

    public function toggleVisibility(SavedDesign $design)
    {
        $design->update(['is_public' => !$design->is_public]);

        return redirect()->back()->with('message', 'Design visibility updated successfully.');
    }

    public function makeFeatured(SavedDesign $design)
    {
        $design->update(['is_template' => true]);

        return redirect()->back()->with('message', 'Design marked as template successfully.');
    }
}