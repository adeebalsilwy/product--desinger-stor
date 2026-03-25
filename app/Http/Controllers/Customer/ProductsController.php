<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Tshirt;
use App\Models\ProductType;
use Illuminate\Http\Request;

class ProductsController extends Controller
{
    public function index()
    {
        // Get all products for the general products page
        // Prioritize template-based products but include all active products
        $products = Product::with(['images', 'productType', 'designTemplate'])
            ->where('is_active', true)
            ->orderByRaw('is_template_based DESC, created_at DESC') // Show template-based products first
            ->paginate(20);
        
        return inertia('Customer/Products', [
            'products' => $products
        ]);
    }
    
    public function tshirtPage(Request $request, $slug)
    {
        try {
            // Try to find as a general product first, then fall back to tshirt for backward compatibility
            $product = Product::where('slug', $slug)
                ->with(['images', 'productType', 'printAreas', 'designTemplate'])
                ->first();
                
            if (!$product) {
                // Fall back to tshirt model for backward compatibility
                $tshirt = Tshirt::where('slug', $slug)
                    ->with('images')
                    ->firstOrFail();
                
                // Convert tshirt to product-like structure for backward compatibility
                $product = (object)[
                    'id' => $tshirt->id,
                    'name' => $tshirt->title,
                    'title' => $tshirt->title, // For backward compatibility with views
                    'description' => $tshirt->description,
                    'price' => $tshirt->price,
                    'images' => $tshirt->images ? $tshirt->images->toArray() : [],
                    'productType' => null,
                    'printAreas' => collect([]),
                    'is_template_based' => false,
                    'designTemplate' => null,
                    'slug' => $tshirt->slug, // Include slug for designer navigation
                ];
            } else {
                // Ensure images are properly loaded
                $product->images = $product->images ? $product->images->toArray() : [];
                
                // CRITICAL: Ensure productType is available for designer navigation
                if (!$product->productType && $product->product_type_id) {
                    $product->productType = \App\Models\ProductType::find($product->product_type_id);
                }
            }
    
            return inertia('Customer/TshirtPage', [
                'product' => $product,
                'tshirt' => $product // For backward compatibility with existing views
            ]);
        } catch (\Exception $e) {
            \Log::error('Product page error: ' . $e->getMessage());
            abort(404, 'Product not found');
        }
    }
}