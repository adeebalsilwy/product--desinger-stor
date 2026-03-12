<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\TitleToFolderName;
use App\Http\Controllers\Controller;
use App\Models\ShirtImage;
use App\Models\Tshirt;
use App\Models\Product; // Import Product model
use App\Models\ProductType;
use Error;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductsController extends Controller
{
    public function index()
    {
        // Get both legacy T-shirts and new template-based products
        $tshirts = Tshirt::with('images')
            ->orderBy('created_at', 'desc')
            ->paginate(9)
            ->through(function ($tshirt) {
                return [
                    'id' => $tshirt->id,
                    'title' => $tshirt->title,
                    'slug' => $tshirt->slug,
                    'description' => $tshirt->description,
                    'price' => $tshirt->price,
                    'listed' => $tshirt->listed,
                    'mainImage' => $tshirt->getMainImage(),
                    'otherImages' => $tshirt->getOtherImages(),
                    'imagesFolderName' => $tshirt->getImagesFolderName(),
                    'totalSells' => $tshirt->getTotalSales(),
                    'totalRevenue' => $tshirt->getTotalRevenue(),
                    'type' => 'legacy_tshirt', // Mark as legacy
                ];
            });

        // Get template-based products
        $products = Product::with(['productType', 'designTemplate'])
            ->orderBy('created_at', 'desc')
            ->paginate(9)
            ->through(function ($product) {
                return [
                    'id' => $product->id,
                    'title' => $product->name,
                    'name' => $product->name,
                    'slug' => $product->slug,
                    'description' => $product->description,
                    'price' => $product->price,
                    'is_active' => $product->is_active,
                    'thumbnail_url' => $product->thumbnail_url,
                    'is_template_based' => $product->is_template_based,
                    'template_category' => $product->template_category,
                    'product_type' => $product->productType ? $product->productType->name : null,
                    'design_template' => $product->designTemplate ? $product->designTemplate->name : null,
                    'type' => 'product', // Mark as new product
                ];
            });

        // Combine both collections for the view
        $combinedItems = collect([...$tshirts->items(), ...$products->items()])
            ->sortByDesc('id') // Sort by ID descending to show newest first
            ->values();

        // Create a new paginator for combined results
        $currentPage = \Illuminate\Pagination\LengthAwarePaginator::resolveCurrentPage();
        $perPage = 18; // Total items per page (9 from each)
        $currentItems = $combinedItems->slice(($currentPage - 1) * $perPage, $perPage)->values();
        
        $paginator = new \Illuminate\Pagination\LengthAwarePaginator(
            $currentItems,
            $combinedItems->count(),
            $perPage,
            $currentPage,
            [
                'path' => \Request::url(),
                'pageName' => 'page',
            ]
        );

        return inertia('Admin/Products', [
            'tshirts' => $paginator,
            'orders_count' => \App\Models\Order::count(),
            'customers_count' => \App\Models\Customer::count(),
            'tshirts_count' => \App\Models\Tshirt::count(), // Legacy count
            'products_count' => \App\Models\Product::count(), // New products count
            'revenue' => \App\Models\Order::sum('total_amount'),
        ]);
    }


    public function store(Request $request)
    {  // if (! Gate::allows('super_admin')) {
        //     return back()->withErrors([
        //         'authorization' => config('messages.guest_admin_restriction'),
        //     ]);
        // }

        // Log incoming request data for debugging
        \Log::info('Store Tshirt Request Data:', $request->all());
        \Log::info('Store Tshirt - is_template_based value:', [$request->input('is_template_based')]);
        \Log::info('Store Tshirt - design_template_id value:', [$request->input('design_template_id')]);
        \Log::info('Store Tshirt - isTemplateBased decision:', [
            'is_template_based_exists' => $request->has('is_template_based'),
            'is_template_based_bool' => $request->boolean('is_template_based'),
            'design_template_id_filled' => $request->filled('design_template_id'),
            'design_template_id_value' => $request->input('design_template_id'),
            'isTemplateBased_final' => ($request->has('is_template_based') && $request->boolean('is_template_based')) || 
                                  ($request->filled('design_template_id') && $request->input('design_template_id') !== '')
        ]);

        // Check if this is a template-based product creation
        // Only consider template-based if is_template_based is explicitly true OR design_template_id is filled
        $isTemplateBased = ($request->has('is_template_based') && $request->boolean('is_template_based')) || 
                       ($request->filled('design_template_id') && $request->input('design_template_id') !== '');

        \Log::info('Store Tshirt - Final decision: ' . ($isTemplateBased ? 'Template-based' : 'Legacy T-shirt'));

        if ($isTemplateBased) {
            // Handle template-based product creation
            return $this->storeTemplateProduct($request);
        } else {
            // Handle legacy T-shirt creation
            return $this->storeLegacyTshirt($request);
        }
    }

    private function storeTemplateProduct(Request $request)
    {
        // Validate request data for template-based product
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'description' => 'required|string',
            'product_type_id' => 'required|exists:product_types,id',
            'is_active' => 'boolean',
            'design_template_id' => 'nullable|exists:design_templates,id',
            'is_template_based' => 'boolean',
            'template_category' => 'nullable|string|max:255',
            'thumbnail_url' => 'nullable|url',
        ]);

        // Create a new Product instance and save it
        $product = new Product();
        $product->product_type_id = $validatedData['product_type_id'];
        $product->name = $validatedData['name'];
        $product->slug = Str::slug($validatedData['name'], '-');
        $product->price = $validatedData['price'];
        $product->description = $validatedData['description'];
        $product->sku = 'PROD-' . strtoupper(Str::random(8)); // Generate SKU
        $product->is_active = $validatedData['is_active'] ?? true;
        $product->thumbnail_url = $validatedData['thumbnail_url'] ?? null;
        $product->design_template_id = $validatedData['design_template_id'] ?? null;
        $product->is_template_based = $validatedData['is_template_based'] ?? true;
        $product->template_category = $validatedData['template_category'] ?? null;
        $product->template_data = [
            'colors' => $request->input('available_colors', []),
            'sizes' => $request->input('available_sizes', ['XS', 'S', 'M', 'L', 'XL', 'XXL']),
            'base_template_id' => $validatedData['design_template_id']
        ];
        $product->save();

        return redirect()->route('admin.products.index');
    }

    private function storeLegacyTshirt(Request $request)
    {
        // Validate request data for legacy T-shirt
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'description' => 'required|string',
            'mainImage' => 'required|image|max:1024',
            'secondImage' => 'nullable|image|max:1024',
            'thirdImage' => 'nullable|image|max:1024',
            'forthImage' => 'nullable|image|max:1024',
            'fifthImage' => 'nullable|image|max:1024',
        ]);

        // Generate folder name from the first 3 words of the title
        $folderName = TitleToFolderName::convert($validatedData['title']);

        // Create a new Tshirt instance and save it
        $tshirt = new Tshirt();
        $tshirt->title = $validatedData['title'];
        $tshirt->slug = Str::slug($validatedData['title'], '-');
        $tshirt->price = $validatedData['price'];
        $tshirt->description = $validatedData['description'];
        $tshirt->images_folder_name = $folderName;
        $tshirt->save();

        // Array of images with their corresponding order
        $images = [
            'mainImage' => 1,
            'secondImage' => 2,
            'thirdImage' => 3,
            'forthImage' => 4,
            'fifthImage' => 5,
        ];

        // Loop through each image input
        foreach ($images as $imageKey => $order) {
            // Check if the image exists in the request
            if ($request->hasFile($imageKey)) {
                $file = $request->file($imageKey);
                $extension = $file->getClientOriginalExtension(); // Get original extension
                $filename = "{$imageKey}.{$extension}";

                // Store the image in the public disk under the specified folder
                $path = $file->storeAs("tshirts/{$folderName}", $filename, 'public');

                // Create a new TshirtImage instance
                $tshirtImage = new ShirtImage();
                $tshirtImage->order = $order;
                $tshirtImage->tshirt_id = $tshirt->id;
                // $tshirtImage->url = Storage::url($path); // Get URL for public access
                $tshirtImage->url = '/storage/' . $path; // Get URL for public access
                $tshirtImage->save();
            }
        }

        // Also create a corresponding Product entry for consistency
        $tshirtType = ProductType::firstOrCreate([
            'name' => 'T-Shirt',
        ], [
            'slug' => 't-shirt',
            'description' => 'Customizable t-shirts',
            'base_price' => 19.99,
            'is_active' => true,
        ]);

        Product::create([
            'product_type_id' => $tshirtType->id,
            'name' => $validatedData['title'],
            'slug' => Str::slug($validatedData['title'], '-'),
            'description' => $validatedData['description'],
            'sku' => 'PROD-' . strtoupper(Str::random(8)),
            'price' => $validatedData['price'],
            'inventory_count' => 100, // Default inventory
            'is_active' => true,
            'thumbnail_url' => $path ? '/storage/' . $path : null,
            'is_template_based' => false, // Not template-based since it's a legacy T-shirt
            'template_category' => 'legacy',
            'template_data' => [
                'original_tshirt_id' => $tshirt->id,
                'legacy_migration' => true,
            ],
        ]);

        // Redirect to the products route
        return redirect()->route('admin.products.index');
    }

    public function create()
    {  // if (! Gate::allows('super_admin')) {
        //     return back()->withErrors([
        //         'authorization' => config('messages.guest_admin_restriction'),
        //     ]);
        // }
        
        // Get product types and templates for the form
        $productTypes = ProductType::all();
        $designTemplates = \App\Models\DesignTemplate::all();
        
        return inertia('Admin/Products/Create', [
            'productTypes' => $productTypes,
            'designTemplates' => $designTemplates,
        ]);
    }

    public function update(Request $request, $id)
    {
        // Check if the ID belongs to a Tshirt or Product model
        $tshirt = Tshirt::find($id);
        $product = Product::find($id);
        
        if ($tshirt) {
            // Update legacy T-shirt
            return $this->updateLegacyTshirt($request, $tshirt);
        } elseif ($product) {
            // Update template-based product
            return $this->updateTemplateProduct($request, $product);
        } else {
            abort(404, 'Product not found');
        }
    }

    private function updateTemplateProduct(Request $request, Product $product)
    {  // if (! Gate::allows('super_admin')) {
        //     return back()->withErrors([
        //         'authorization' => config('messages.guest_admin_restriction'),
        //     ]);
        // }

        // Validate request data for template-based product
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'description' => 'required|string',
            'product_type_id' => 'required|exists:product_types,id',
            'is_active' => 'boolean',
            'design_template_id' => 'nullable|exists:design_templates,id',
            'is_template_based' => 'boolean',
            'template_category' => 'nullable|string|max:255',
            'thumbnail_url' => 'nullable|url',
        ]);

        // Update the product
        $product->product_type_id = $validatedData['product_type_id'];
        $product->name = $validatedData['name'];
        $product->slug = Str::slug($validatedData['name'], '-');
        $product->price = $validatedData['price'];
        $product->description = $validatedData['description'];
        $product->is_active = $validatedData['is_active'] ?? true;
        $product->thumbnail_url = $validatedData['thumbnail_url'] ?? $product->thumbnail_url;
        $product->design_template_id = $validatedData['design_template_id'] ?? null;
        $product->is_template_based = $validatedData['is_template_based'] ?? true;
        $product->template_category = $validatedData['template_category'] ?? null;
        $product->template_data = [
            'colors' => $request->input('available_colors', $product->template_data['colors'] ?? []),
            'sizes' => $request->input('available_sizes', $product->template_data['sizes'] ?? ['XS', 'S', 'M', 'L', 'XL', 'XXL']),
            'base_template_id' => $validatedData['design_template_id'] ?? $product->template_data['base_template_id'] ?? null
        ];
        $product->save();

        return redirect()->route('admin.products.index');
    }

    private function updateLegacyTshirt(Request $request, Tshirt $tshirt)
    {  // if (! Gate::allows('super_admin')) {
        //     return back()->withErrors([
        //         'authorization' => config('messages.guest_admin_restriction'),
        //     ]);
        // }

        // Validate request data
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'description' => 'required|string',
            'listed' => 'required|boolean',
            'mainImage' => 'nullable|max:1024',
            'secondImage' => 'nullable|max:1024',
            'thirdImage' => 'nullable|max:1024',
            'forthImage' => 'nullable|max:1024',
            'fifthImage' => 'nullable|max:1024',
        ]);

        // Tshirt folder name
        $folderName = $tshirt->getImagesFolderName();

        // Only update text fields if they have changed
        $changes = [];
        if ($tshirt->title !== $validatedData['title']) {
            $changes['title'] = $validatedData['title'];
            $changes['slug'] = Str::slug($validatedData['title'], '-');
        }
        if ($tshirt->price !== (float) $validatedData['price']) {
            $changes['price'] = (float) $validatedData['price'];
        }
        if ($tshirt->description !== $validatedData['description']) {
            $changes['description'] = $validatedData['description'];
        }
        if ($tshirt->listed !== $validatedData['listed']) {
            $changes['listed'] = $validatedData['listed'];
        }
        if (!empty($changes)) {
            $tshirt->update($changes);
        }

        // Array of images with their corresponding order and field names
        $images = [
            'mainImage' => 1,
            'secondImage' => 2,
            'thirdImage' => 3,
            'forthImage' => 4,
            'fifthImage' => 5,
        ];

        // Loop through each image input
        foreach ($images as $imageKey => $order) {
            // Find the current image path in the database
            $currentImage = ShirtImage::where('tshirt_id', $tshirt->id)->where('order', $order)->first();
            $currentImagePath = $currentImage ? $currentImage->url : null;

            // Check if a new file is uploaded for this image
            if ($request->hasFile($imageKey)) {
                // Remove the old image from storage if it exists
                if ($currentImagePath) {
                    Storage::delete(str_replace('/storage/', 'public/', $currentImagePath));
                }

                // Store the new image
                $file = $request->file($imageKey);
                $extension = $file->getClientOriginalExtension();
                $filename = "{$imageKey}.{$extension}";
                $newImagePath = $file->storeAs("tshirts/{$folderName}", $filename, 'public');

                // Update or create the database record for this image
                ShirtImage::updateOrCreate(
                    ['tshirt_id' => $tshirt->id, 'order' => $order],
                    ['url' => '/storage/' . $newImagePath]
                );
            } elseif (
                $request->input($imageKey) === null
            ) {
                // If the field is null, remove from storage and database
                if ($currentImagePath) {
                    Storage::delete(str_replace('/storage/', 'public/', $currentImagePath));
                    ShirtImage::where('tshirt_id', $tshirt->id)->where('order', $order)->delete();
                }
            }
            // If "originalImage" is set, we leave the existing image unchanged
        }

        // Also update the corresponding Product entry if it exists
        $correspondingProduct = Product::whereJsonContains('template_data', ['original_tshirt_id' => $tshirt->id])
            ->orWhere('name', $tshirt->title)
            ->first();
            
        if ($correspondingProduct) {
            $correspondingProduct->update([
                'name' => $validatedData['title'],
                'slug' => Str::slug($validatedData['title'], '-'),
                'description' => $validatedData['description'],
                'price' => $validatedData['price'],
                'is_active' => $validatedData['listed'],
            ]);
        }

        // Redirect to the t-shirts route
        return redirect()->back();
    }

    public function show($id)
    {  // if (! Gate::allows('super_admin')) {
        //     return back()->withErrors([
        //         'authorization' => config('messages.guest_admin_restriction'),
        //     ]);
        // }

        // Check if the ID belongs to a Tshirt or Product model
        $tshirt = Tshirt::find($id);
        $product = Product::with(['productType', 'designTemplate'])->find($id);
        
        if ($tshirt) {
            // Show legacy T-shirt
            return $this->showLegacyTshirt($tshirt);
        } elseif ($product) {
            // Show template-based product
            return $this->showTemplateProduct($product);
        } else {
            abort(404, 'Product not found');
        }
    }
    
    private function showTemplateProduct(Product $product)
    {  // if (! Gate::allows('super_admin')) {
        //     return back()->withErrors([
        //         'authorization' => config('messages.guest_admin_restriction'),
        //     ]);
        // }
        
        $formattedProduct = [
            'id' => $product->id,
            'name' => $product->name,
            'title' => $product->name, // For backward compatibility
            'slug' => $product->slug,
            'description' => $product->description,
            'price' => $product->price,
            'is_active' => $product->is_active,
            'sku' => $product->sku,
            'inventory_count' => $product->inventory_count,
            'thumbnail_url' => $product->thumbnail_url,
            'is_template_based' => $product->is_template_based,
            'template_category' => $product->template_category,
            'product_type' => $product->productType ? $product->productType->name : null,
            'design_template' => $product->designTemplate ? $product->designTemplate->name : null,
            'template_data' => $product->template_data,
            'final_price' => $product->final_price,
            'has_discount' => $product->hasDiscount(),
            'discount_percentage' => $product->getDiscountPercentageAttribute(),
            'type' => 'product',
            'created_at' => $product->created_at,
            'updated_at' => $product->updated_at,
        ];
        
        return inertia('Admin/Products/Show', [
            'product' => $formattedProduct
        ]);
    }
    
    private function showLegacyTshirt(Tshirt $tshirt)
    {  // if (! Gate::allows('super_admin')) {
        //     return back()->withErrors([
        //         'authorization' => config('messages.guest_admin_restriction'),
        //     ]);
        // }
        
        $mainImage = $tshirt->getMainImage();
        
        // Get other images by filtering out the main image based on order (order != 1)
        $otherImagesCollection = $tshirt->images->filter(function ($image) {
            return $image->order !== 1;
        });
        
        // Convert other images collection to array with proper structure
        $otherImages = [];
        foreach ($otherImagesCollection as $image) {
            $otherImages[$image->order] = [
                'id' => $image->id,
                'url' => $image->url,
                'order' => $image->order,
            ];
        }
        
        $formattedProduct = [
            'id' => $tshirt->id,
            'title' => $tshirt->title,
            'name' => $tshirt->title, // For consistency
            'slug' => $tshirt->slug,
            'description' => $tshirt->description,
            'price' => $tshirt->price,
            'listed' => $tshirt->listed,
            'mainImage' => $mainImage ? [
                'id' => $mainImage->id,
                'url' => $mainImage->url,
                'order' => $mainImage->order,
            ] : null,
            'otherImages' => $otherImages,
            'imagesFolderName' => $tshirt->getImagesFolderName(),
            'totalSells' => $tshirt->getTotalSales(),
            'totalRevenue' => $tshirt->getTotalRevenue(),
            'type' => 'legacy_tshirt',
            'created_at' => $tshirt->created_at,
            'updated_at' => $tshirt->updated_at,
        ];
        
        return inertia('Admin/Tshirts/Show', [
            'product' => $formattedProduct
        ]);
    }
    
    public function destroy($id)
    {
        // Check if the ID belongs to a Tshirt or Product model
        $tshirt = Tshirt::find($id);
        $product = Product::find($id);
        
        if ($tshirt) {
            // Delete legacy T-shirt
            return $this->destroyLegacyTshirt($tshirt);
        } elseif ($product) {
            // Delete template-based product
            return $this->destroyTemplateProduct($product);
        } else {
            abort(404, 'Product not found');
        }
    }

    private function destroyTemplateProduct(Product $product)
    {  // if (! Gate::allows('super_admin')) {
        //     return back()->withErrors([
        //         'authorization' => config('messages.guest_admin_restriction'),
        //     ]);
        // }

        // Delete the product
        $product->delete();

        return redirect()->route('admin.products.index');
    }

    private function destroyLegacyTshirt(Tshirt $tshirt)
    {  // if (! Gate::allows('super_admin')) {
        //     return back()->withErrors([
        //         'authorization' => config('messages.guest_admin_restriction'),
        //     ]);
        // }
        
        // Delete the images
        $tshirt->images()->delete();

        // delete the images folder
        $folderPath = "tshirts/{$tshirt->getImagesFolderName()}";
        Storage::disk('public')->deleteDirectory($folderPath);

        // Also delete the corresponding Product entry if it exists
        $correspondingProduct = Product::whereJsonContains('template_data', ['original_tshirt_id' => $tshirt->id])
            ->orWhere('name', $tshirt->title)
            ->first();
            
        if ($correspondingProduct) {
            $correspondingProduct->delete();
        }

        // delete the tshirt
        $tshirt->delete();

        return redirect()->route('admin.products.index');
    }
}