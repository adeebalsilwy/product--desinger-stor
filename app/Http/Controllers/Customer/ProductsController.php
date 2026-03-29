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
        $products = Product::with(['images' => function($query) {
                $query->orderBy('order');
            }, 'productType', 'designTemplate'])
            ->where('is_active', true)
            ->orderByRaw('is_template_based DESC, created_at DESC') // Show template-based products first
            ->paginate(20);
        
        // Append image URLs with proper formatting
        $products->getCollection()->transform(function($product) {
            // Ensure thumbnail_url is set from first image if not already set
            if (!$product->thumbnail_url && $product->images->count() > 0) {
                $product->thumbnail_url = $product->images->first()->url;
            }
            return $product;
        });
        
        return inertia('Customer/Products', [
            'products' => $products
        ]);
    }
    
    public function tshirtPage(Request $request, $slug)
    {
        try {
            // Try to find as a general product first, then fall back to tshirt for backward compatibility
            $product = Product::with(['images' => function($query) {
                    $query->orderBy('order');
                }, 'productType', 'printAreas', 'designTemplate'])
                ->where('slug', $slug)
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
                    'images' => $tshirt->images ? $tshirt->images->sortBy('order')->toArray() : [],
                    'productType' => null,
                    'printAreas' => collect([]),
                    'is_template_based' => false,
                    'designTemplate' => null,
                    'slug' => $tshirt->slug, // Include slug for designer navigation
                    'thumbnail_url' => $tshirt->images && $tshirt->images->count() > 0 ? $tshirt->images->first()->url : null,
                ];
            } else {
                // Ensure images are properly loaded and sorted
                $product->images = $product->images ? $product->images->toArray() : [];
                
                // Set thumbnail_url from first image if not already set
                if (!$product->thumbnail_url && !empty($product->images)) {
                    $product->thumbnail_url = $product->images[0]['url'] ?? null;
                }
                
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