<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DesignTemplate;
use Illuminate\Http\Request;

class TemplateController extends Controller
{
    /**
     * List all templates
     */
    public function index(Request $request)
    {
        $query = DesignTemplate::query();
        
        if ($request->has('product_type')) {
            $query->where('product_type_id', $request->product_type);
        }
        
        if ($request->has('category')) {
            $query->where('category', $request->category);
        }
        
        if ($request->has('is_premium')) {
            $query->where('is_premium', $request->is_premium);
        }
        
        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->boolean('my_templates')) {
            if (auth()->check()) {
                $query->where('created_by', auth()->id());
            } else {
                $query->whereRaw('1 = 0');
            }
        }
        
        $templates = $query->with('productType')
            ->orderBy('name')
            ->paginate($request->get('per_page', 20));
        
        return response()->json([
            'data' => $templates->items(),
            'meta' => [
                'current_page' => $templates->currentPage(),
                'last_page' => $templates->lastPage(),
                'per_page' => $templates->perPage(),
                'total' => $templates->total(),
            ],
        ]);
    }

    /**
     * Get a specific template
     */
    public function show(DesignTemplate $template)
    {
        $template->load(['productType', 'creator']);
        
        return response()->json([
            'data' => $template,
        ]);
    }
}
