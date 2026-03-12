<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\ProductType;
use App\Models\Product;
use App\Models\DesignTemplate;
use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Models\SavedDesign;

class DesignerController extends Controller
{
    public function create(Request $request, $productTypeSlug, $productSlug = null)
    {
        $productType = ProductType::where('slug', $productTypeSlug)->firstOrFail();
        
        $product = null;
        if ($productSlug) {
            $product = Product::where('slug', $productSlug)
                ->where('product_type_id', $productType->id)
                ->firstOrFail();
        }
        
        // Check for template parameter
        $template = null;
        $templateId = $request->query('template');
        
        if ($templateId) {
            $template = DesignTemplate::find($templateId);
            // Check if template exists and is accessible
            if ($template && !$template->is_premium) {
                // Allow access to non-premium templates
            } elseif ($template && $template->is_premium) {
                // Check if user is authenticated and has access to premium templates
                if (!auth()->check()) {
                    $template = null; // Don't allow access to premium templates for guests
                }
            }
        }
        
        return Inertia::render('Customer/Designer/Create', [
            'productType' => $productType,
            'product' => $product,
            'printAreas' => $productType->printAreas,
            'initialTemplate' => $template,
        ]);
    }

    public function edit($designId)
    {
        $design = \App\Models\SavedDesign::with(['productType', 'product'])->findOrFail($designId);
        
        // Check if user can access this design (owner or public)
        if (!$design->is_public) {
            if (auth()->check()) {
                abort_unless($design->user_id == auth()->id(), 403);
            } else {
                abort_unless($design->session_id == session()->getId(), 403);
            }
        }
        
        return Inertia::render('Customer/Designer/Edit', [
            'design' => $design,
            'printAreas' => $design->productType->printAreas,
        ]);
    }

    public function myDesigns()
    {
        // Support both authenticated users and guest sessions
        $query = \App\Models\SavedDesign::with(['productType'])->latest();
        
        if (auth()->check()) {
            // For authenticated users (customers, admin, etc.)
            $query->where('user_id', auth()->id());
        } else {
            // For guests, use session ID to identify their designs
            $query->where('session_id', session()->getId());
        }
        
        $designs = $query->paginate(20);

        return Inertia::render('Customer/Designer/MyDesigns', [
            'designs' => $designs,
        ]);
    }
}