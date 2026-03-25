<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Tshirt;

class HomePageController extends Controller
{
    public function index()
    {
        // Get featured products - prioritize template-based products
        $featuredProducts = Product::with(['images', 'productType'])
            ->where('is_active', true)
            ->orderBy('is_template_based', 'desc')
            ->orderBy('created_at', 'desc')
            ->limit(8)
            ->get()
            ->map(function ($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'title' => $product->name, // For backward compatibility
                    'slug' => $product->slug,
                    'description' => $product->description,
                    'price' => $product->sale_price ?? $product->price,
                    'image_url' => $product->images->first()?->url ?? $product->thumbnail_url ?? '/images/logo.jpeg',
                    'is_featured' => false,
                    'is_template_based' => $product->is_template_based,
                    'rating' => 4.9,
                ];
            });

        // If no products found, fall back to tshirts for backward compatibility
        if ($featuredProducts->isEmpty()) {
            $tshirts = Tshirt::where('is_active', true)
                ->with(['images' => function ($query) {
                    $query->where('order', 1)->limit(1);
                }])
                ->limit(8)
                ->get()
                ->map(function ($tshirt) {
                    return [
                        'id' => $tshirt->id,
                        'name' => $tshirt->title,
                        'title' => $tshirt->title,
                        'slug' => $tshirt->slug,
                        'description' => $tshirt->description,
                        'price' => $tshirt->price,
                        'image_url' => $tshirt->images->first()?->url ?? '/images/logo.jpeg',
                        'is_featured' => false,
                        'is_template_based' => false,
                        'rating' => 4.9,
                    ];
                });
            
            $featuredProducts = $tshirts;
        }

        return inertia('Customer/HomePage', [
            'featuredProducts' => $featuredProducts
        ]);
    }
}
