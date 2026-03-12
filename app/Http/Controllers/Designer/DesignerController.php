<?php

namespace App\Http\Controllers\Designer;

use App\Http\Controllers\Controller;
use App\Models\ProductType;
use App\Models\Product;
use App\Models\DesignTemplate;
use App\Models\SavedDesign;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
                if (!$this->isAuthenticated()) {
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
        $design = SavedDesign::with(['productType', 'product'])->findOrFail($designId);
        
        // Check if user can access this design (owner or public)
        if (!$design->is_public) {
            if ($this->isAuthenticated()) {
                // Admins can access any design
                if ($this->isCurrentUserAdmin()) {
                    // Allow admin access
                } else {
                    abort_unless($this->getCurrentUserId() == $design->user_id, 403);
                }
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
        $query = SavedDesign::with(['user', 'productType'])->latest(); // Include user info for admin
        
        if ($this->isAuthenticated()) {
            // For admin users, show all designs
            if ($this->isCurrentUserAdmin()) {
                // Admins can see all designs
            } else {
                // Regular authenticated users see only their own designs
                $query->where('user_id', $this->getCurrentUserId());
            }
        } else {
            // For guests, use session ID to identify their designs
            $query->where('session_id', session()->getId());
        }
        
        $designs = $query->paginate(20);

        return Inertia::render('Customer/Designer/MyDesigns', [
            'designs' => $designs,
        ]);
    }
    
    /**
     * Check if any user is authenticated (across any guard)
     */
    private function isAuthenticated()
    {
        return Auth::check() || auth('customer')->check();
    }
    
    /**
     * Get the current user ID regardless of guard
     */
    private function getCurrentUserId()
    {
        if (Auth::check()) {
            return Auth::id();
        } elseif (auth('customer')->check()) {
            return auth('customer')->id();
        }
        
        return null;
    }
    
    /**
     * Check if current user is an admin or staff
     */
    private function isCurrentUserAdmin()
    {
        if (Auth::check()) {
            $user = Auth::user();
            return in_array($user->role, ['admin', 'staff']);
        }
        
        return false;
    }
}