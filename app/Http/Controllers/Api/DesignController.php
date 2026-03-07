<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SavedDesign;
use App\Models\ProductType;
use App\Models\DesignTemplate;
use App\Models\UserAsset;
use App\Services\Design\ExportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class DesignController extends Controller
{
    protected $exportService;

    public function __construct(ExportService $exportService)
    {
        $this->exportService = $exportService;
    }

    /**
     * List all designs for authenticated user
     */
    public function index(Request $request)
    {
        $query = SavedDesign::query();
        
        if ($request->has('product_type')) {
            $query->where('product_type_id', $request->product_type);
        }
        
        if ($request->has('is_template')) {
            $query->where('is_template', $request->is_template);
        }
        
        // User's own designs or guest designs by session
        if (Auth::check()) {
            $query->where('user_id', Auth::id());
        } else {
            $query->where('session_id', $request->session()->getId());
        }
        
        $designs = $query->with('productType')
            ->latest()
            ->paginate($request->get('per_page', 20));
        
        return response()->json([
            'data' => $designs->items(),
            'meta' => [
                'current_page' => $designs->currentPage(),
                'last_page' => $designs->lastPage(),
                'per_page' => $designs->perPage(),
                'total' => $designs->total(),
            ],
        ]);
    }

    /**
     * Get a specific design
     */
    public function show(SavedDesign $design)
    {
        // Check authorization
        if (!$this->canAccessDesign($design)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        $design->load(['productType', 'orderItems']);
        
        return response()->json([
            'data' => $design,
        ]);
    }

    /**
     * Create a new design
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_type_id' => 'required|exists:product_types,id',
            'name' => 'nullable|string|max:255',
            'design_data' => 'required|array',
            'is_public' => 'boolean',
        ]);
        
        $design = SavedDesign::create([
            'user_id' => Auth::id(),
            'session_id' => $request->session()->getId(),
            'product_type_id' => $validated['product_type_id'],
            'name' => $validated['name'] ?? 'Untitled Design',
            'design_data' => $validated['design_data'],
            'is_public' => $validated['is_public'] ?? false,
        ]);
        
        // Generate initial preview
        dispatch(new \App\Jobs\GenerateDesignPreview($design));
        
        return response()->json([
            'data' => $design->fresh(),
            'message' => 'Design created successfully',
        ], 201);
    }

    /**
     * Update an existing design
     */
    public function update(Request $request, SavedDesign $design)
    {
        if (!$this->canAccessDesign($design)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'design_data' => 'sometimes|array',
            'is_public' => 'sometimes|boolean',
        ]);
        
        $design->update($validated);
        
        // Regenerate preview if design data changed
        if (isset($validated['design_data'])) {
            dispatch(new \App\Jobs\GenerateDesignPreview($design));
        }
        
        return response()->json([
            'data' => $design->fresh(),
            'message' => 'Design updated successfully',
        ]);
    }

    /**
     * Delete a design
     */
    public function destroy(SavedDesign $design)
    {
        if (!$this->canAccessDesign($design)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        $design->delete();
        
        return response()->json([
            'message' => 'Design deleted successfully',
        ]);
    }

    /**
     * Duplicate a design
     */
    public function duplicate(SavedDesign $design)
    {
        if (!$this->canAccessDesign($design)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        $newDesign = $design->duplicate();
        
        return response()->json([
            'data' => $newDesign,
            'message' => 'Design duplicated successfully',
        ], 201);
    }

    /**
     * Export design in various formats
     */
    public function export(Request $request, SavedDesign $design)
    {
        if (!$this->canAccessDesign($design)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        $format = $request->get('format', 'high_res');
        
        try {
            switch ($format) {
                case 'high_res':
                    $url = $this->exportService->exportHighRes($design);
                    break;
                case 'preview':
                    $url = $this->exportService->generatePreview($design);
                    break;
                case 'pdf':
                    $url = $this->exportService->exportToPdf($design);
                    break;
                case 'layers':
                    $layers = $this->exportService->exportLayers($design);
                    return response()->json([
                        'data' => $layers,
                    ]);
                default:
                    return response()->json(['error' => 'Invalid export format'], 400);
            }
            
            return response()->json([
                'data' => ['url' => $url],
                'message' => 'Design exported successfully',
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Export failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Use a template to create a new design
     */
    public function useTemplate(DesignTemplate $template)
    {
        $design = $template->useByUser(Auth::user());
        
        return response()->json([
            'data' => $design,
            'message' => 'Template applied successfully',
        ], 201);
    }

    /**
     * Save design as template
     */
    public function saveAsTemplate(Request $request, SavedDesign $design)
    {
        if (!$this->canAccessDesign($design)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'nullable|string|max:100',
            'tags' => 'nullable|array',
            'is_premium' => 'boolean',
            'price' => 'numeric|min:0',
        ]);
        
        $template = DesignTemplate::create([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'product_type_id' => $design->product_type_id,
            'category' => $validated['category'] ?? null,
            'tags' => $validated['tags'] ?? [],
            'design_data' => $design->design_data,
            'is_premium' => $validated['is_premium'] ?? false,
            'price' => $validated['price'] ?? 0.00,
            'created_by' => Auth::id(),
        ]);
        
        return response()->json([
            'data' => $template,
            'message' => 'Design saved as template successfully',
        ], 201);
    }

    /**
     * Generate/refresh design preview
     */
    public function generatePreview(SavedDesign $design)
    {
        if (!$this->canAccessDesign($design)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        try {
            $previewUrl = $this->exportService->generatePreview($design);
            
            return response()->json([
                'data' => ['preview_url' => $previewUrl],
                'message' => 'Preview generated successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Preview generation failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Check if user can access a design
     */
    protected function canAccessDesign(SavedDesign $design): bool
    {
        // Admin can access all
        if (Auth::check() && Auth::user()->role === 'admin') {
            return true;
        }
        
        // Owner can access
        if (Auth::check() && $design->user_id === Auth::id()) {
            return true;
        }
        
        // Guest can access by session
        if (!Auth::check() && $design->session_id === request()->session()->getId()) {
            return true;
        }
        
        // Public designs are accessible
        if ($design->is_public) {
            return true;
        }
        
        return false;
    }
}
