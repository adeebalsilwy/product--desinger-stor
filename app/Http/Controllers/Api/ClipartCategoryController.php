<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ClipartCategory;
use Illuminate\Http\Request;

class ClipartCategoryController extends Controller
{
    /**
     * List all clipart categories
     */
    public function index(Request $request)
    {
        $categories = ClipartCategory::withCount('cliparts')
            ->orderBy('name')
            ->get();
        
        return response()->json([
            'data' => $categories,
        ]);
    }
}
