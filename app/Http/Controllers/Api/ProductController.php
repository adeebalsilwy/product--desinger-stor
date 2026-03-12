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

    /**
     * Get product types
     */
    public function productTypes()
    {
        try {
            $productTypes = \App\Models\ProductType::where('is_active', true)->get();
            
            return response()->json($productTypes);
        } catch (\Exception $e) {
            \Log::error('Error fetching product types: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to fetch product types'], 500);
        }
    }

    /**
     * Get design templates
     */
    public function designTemplates()
    {
        try {
            $designTemplates = \App\Models\DesignTemplate::all();
            
            return response()->json($designTemplates);
        } catch (\Exception $e) {
            \Log::error('Error fetching design templates: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to fetch design templates'], 500);
        }
    }
}
