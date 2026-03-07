<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PrintArea;
use Illuminate\Http\Request;

class PrintAreaController extends Controller
{
    /**
     * Get print areas by product type
     */
    public function byProductType($slug)
    {
        $productType = \App\Models\ProductType::where('slug', $slug)->firstOrFail();
        
        $printAreas = $productType->printAreas()
            ->orderBy('is_default', 'desc')
            ->get();
        
        return response()->json([
            'data' => $printAreas,
        ]);
    }
}
