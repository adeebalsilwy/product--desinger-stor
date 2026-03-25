<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\SavedDesign;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class SavedDesignController extends Controller
{
    /**
     * Store a newly created design.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'image' => 'nullable|string', // Base64 image
            'preview_image' => 'nullable|image|max:5120',
            'product_type_id' => 'nullable|exists:product_types,id',
            'product_id' => 'nullable|exists:products,id',
            'template_id' => 'nullable|exists:design_templates,id',
            'is_public' => 'boolean',
            'elements' => 'nullable|array',
            'garment_color' => 'nullable|string',
            'dress_size' => 'nullable|string',
            'design_data' => 'nullable', // Allow design_data to be nullable since we process it separately
        ]);

        // Handle base64 image if provided
        $imagePath = null;
        if ($request->has('image') && !empty($request->image)) {
            // Save base64 image
            $image = $request->image;
            if (strpos($image, 'data:image') === 0) {
                $image = str_replace('data:image/png;base64,', '', $image);
                $image = str_replace(' ', '+', $image);
                $imageName = 'design_' . time() . '_' . uniqid() . '.png';
                $path = storage_path('app/public/designs/' . $imageName);
                
                // Ensure directory exists
                if (!file_exists(dirname($path))) {
                    mkdir(dirname($path), 0755, true);
                }
                
                file_put_contents($path, base64_decode($image));
                $imagePath = 'designs/' . $imageName;
            }
        }

        // Prepare design data with elements
        $designData = [];
        
        // Handle design_data if sent as a single object
        if ($request->has('design_data')) {
            $requestData = $request->input('design_data');
            if (is_array($requestData) || is_object($requestData)) {
                $designData = json_decode(json_encode($requestData), true);
            }
        }
        
        // Override with individual fields if they exist separately
        if ($request->has('elements')) {
            $designData['elements'] = $request->input('elements');
        }
        if ($request->has('garment_color')) {
            $designData['garment_color'] = $request->garment_color;
        }
        if ($request->has('dress_size')) {
            $designData['dress_size'] = $request->dress_size;
        }
        if ($request->has('template')) {
            $designData['template'] = $request->template;
        }

        // Determine user_id based on authentication guard
        $userId = null;
        if (auth('customer')->check()) {
            $userId = auth('customer')->id();
        } elseif (auth()->check()) {
            $userId = auth()->id();
        }

        // Handle product_id - create a default product if none provided or if provided doesn't exist
        $productId = null;
        if ($request->product_id) {
            // If a product_id is provided, check if it exists
            if (Product::find($request->product_id)) {
                $productId = $request->product_id;
            } else {
                // If the provided product_id doesn't exist, create a default product
                $product = Product::create([
                    'name' => 'Default Product for Design',
                    'product_type_id' => $request->product_type_id,
                    'sku' => 'DEF-' . strtoupper(substr(md5(time()), 0, 8)),
                    'price' => 0,
                    'sale_price' => 0,
                    'inventory_count' => 0,
                    'is_active' => false,
                    'thumbnail_url' => null,
                ]);
                $productId = $product->id;
            }
        } else {
            // If no product_id is provided, create a default product if product_type_id is available
            if ($request->product_type_id) {
                $product = Product::create([
                    'name' => 'Default Product for Design',
                    'product_type_id' => $request->product_type_id,
                    'sku' => 'DEF-' . strtoupper(substr(md5(time()), 0, 8)),
                    'price' => 0,
                    'sale_price' => 0,
                    'inventory_count' => 0,
                    'is_active' => false,
                    'thumbnail_url' => null,
                ]);
                $productId = $product->id;
            }
        }

        // Create the saved design
        $design = SavedDesign::create([
            'user_id' => $userId,
            'session_id' => session()->getId(),
            'name' => $request->name,
            'preview_url' => $imagePath ? Storage::url($imagePath) : null,
            'product_type_id' => $request->product_type_id,
            'product_id' => $productId,
            'template_id' => $request->template_id,
            'is_public' => $request->boolean('is_public', false),
            'design_data' => json_encode($designData), // Always encode as JSON
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Design saved successfully',
            'design' => $design,
        ], 201);
    }

    /**
     * Update the specified design.
     */
    public function update(Request $request, $id)
    {
        $design = SavedDesign::findOrFail($id);

        // Check authorization
        $this->authorizeDesign($design);

        $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'preview_image' => 'nullable|image|max:5120',
            'product_type_id' => 'nullable|exists:product_types,id',
            'product_id' => 'nullable|exists:products,id',
            'template_id' => 'nullable|exists:design_templates,id',
            'is_public' => 'boolean',
            'elements' => 'nullable|array',
        ]);

        // Handle image upload if new image provided
        if ($request->hasFile('preview_image')) {
            // Delete old image
            if ($design->preview_url) {
                Storage::disk('public')->delete(str_replace('/storage/', '', $design->preview_url));
            }
            
            $imagePath = $request->file('preview_image')->store('designs', 'public');
            $design->preview_url = Storage::url($imagePath);
        }

        // Update design data with elements if provided
        if ($request->has('elements')) {
            $designData = json_decode($design->design_data, true) ?? [];
            $designData['elements'] = $request->input('elements');
            $design->design_data = json_encode($designData);
        }

        $design->update($request->only(['name', 'product_type_id', 'product_id', 'template_id', 'is_public']));

        return response()->json([
            'success' => true,
            'message' => 'Design updated successfully',
            'design' => $design,
        ]);
    }

    /**
     * Remove the specified design.
     */
    public function destroy($id)
    {
        $design = SavedDesign::findOrFail($id);

        // Check authorization
        $this->authorizeDesign($design);

        // Delete associated image
        if ($design->preview_url) {
            Storage::disk('public')->delete(str_replace('/storage/', '', $design->preview_url));
        }

        $design->delete();

        return response()->json([
            'success' => true,
            'message' => 'Design deleted successfully',
        ]);
    }

    /**
     * Authorize user to modify the design.
     */
    private function authorizeDesign(SavedDesign $design)
    {
        if (auth('customer')->check()) {
            abort_unless($design->user_id == auth('customer')->id(), 403);
        } elseif (auth()->check()) {
            $user = auth()->user();
            // Admins can modify any design
            if (!in_array($user->role, ['admin', 'staff'])) {
                abort_unless($design->user_id == auth()->id(), 403);
            }
        } else {
            // For guests, check session ID
            abort_unless($design->session_id == session()->getId(), 403);
        }
    }
}
