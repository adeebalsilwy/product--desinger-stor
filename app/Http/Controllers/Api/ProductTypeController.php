<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ProductType;
use Illuminate\Http\Request;

class ProductTypeController extends Controller
{
    /**
     * List all product types
     */
    public function index(Request $request)
    {
        $query = ProductType::active();
        
        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }
        
        $productTypes = $query->withCount(['products', 'templates'])->latest()->paginate($request->get('per_page', 20));
        
        return response()->json([
            'data' => $productTypes->items(),
            'meta' => [
                'current_page' => $productTypes->currentPage(),
                'last_page' => $productTypes->lastPage(),
                'per_page' => $productTypes->perPage(),
                'total' => $productTypes->total(),
            ],
        ]);
    }

    /**
     * Get a specific product type
     */
    public function show($slug)
    {
        $productType = ProductType::with(['printAreas', 'templates', 'products'])
            ->where('slug', $slug)
            ->firstOrFail();
        
        return response()->json([
            'data' => $productType,
        ]);
    }
}
