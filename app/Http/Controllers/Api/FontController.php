<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Font;
use Illuminate\Http\Request;

class FontController extends Controller
{
    /**
     * List all fonts
     */
    public function index(Request $request)
    {
        $query = Font::active();
        
        if ($request->has('category')) {
            $query->where('category', $request->category);
        }
        
        $fonts = $query->latest()->get();
        
        return response()->json([
            'data' => $fonts,
        ]);
    }
}
