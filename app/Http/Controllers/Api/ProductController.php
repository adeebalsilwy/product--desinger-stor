<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductType;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * List all active products
     */
    public function index(Request $request)
    {
        $query = Product::with('productType')->active();
        
        if ($request->has('type')) {
            $query->where('product_type_id', $request->type);
        }
        
        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }
        
        $products = $query->latest()->paginate($request->get('per_page', 20));
        
        return response()->json([
            'data' => $products->items(),
            'meta' => [
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
                'per_page' => $products->perPage(),
                'total' => $products->total(),
            ],
        ]);
    }

    /**
     * Get a specific product
     */
    public function show($slug)
    {
        $product = Product::with(['productType.printAreas', 'variants'])
            ->where('slug', $slug)
            ->firstOrFail();
        
        return response()->json([
            'data' => $product,
        ]);
    }
}
