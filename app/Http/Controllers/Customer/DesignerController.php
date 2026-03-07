<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\ProductType;
use App\Models\Product;
use Inertia\Inertia;
use Illuminate\Http\Request;

class DesignerController extends Controller
{
    public function create($productTypeSlug, $productSlug = null)
    {
        $productType = ProductType::where('slug', $productTypeSlug)->firstOrFail();
        
        $product = null;
        if ($productSlug) {
            $product = Product::where('slug', $productSlug)
                ->where('product_type_id', $productType->id)
                ->firstOrFail();
        }
        
        return Inertia::render('Customer/Designer/Create', [
            'productType' => $productType,
            'product' => $product,
            'printAreas' => $productType->printAreas,
        ]);
    }

    public function edit($designId)
    {
        $design = \App\Models\SavedDesign::with(['productType', 'product'])->findOrFail($designId);
        
        return Inertia::render('Customer/Designer/Edit', [
            'design' => $design,
            'printAreas' => $design->productType->printAreas,
        ]);
    }

    public function myDesigns()
    {
        $designs = \App\Models\SavedDesign::where('user_id', auth()->id())
            ->with(['productType'])
            ->latest()
            ->paginate(20);

        return Inertia::render('Customer/Designer/MyDesigns', [
            'designs' => $designs,
        ]);
    }
}
