<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Clipart;
use Illuminate\Http\Request;

class ClipartController extends Controller
{
    /**
     * List all cliparts
     */
    public function index(Request $request)
    {
        $query = Clipart::query();
        
        if ($request->has('category')) {
            $query->where('clipart_category_id', $request->category);
        }
        
        if ($request->has('is_premium')) {
            $query->where('is_premium', $request->is_premium);
        }
        
        if ($request->has('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }
        
        $cliparts = $query->with('category')->latest()->paginate($request->get('per_page', 20));
        
        return response()->json([
            'data' => $cliparts->items(),
            'meta' => [
                'current_page' => $cliparts->currentPage(),
                'last_page' => $cliparts->lastPage(),
                'per_page' => $cliparts->perPage(),
                'total' => $cliparts->total(),
            ],
        ]);
    }
}
