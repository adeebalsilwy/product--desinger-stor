<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductType;
use App\Models\DesignTemplate;
use App\Models\ShirtImage;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // Ensure we have a product type for Ahlam's Girls products
        $dressType = ProductType::firstOrCreate([
            'name' => 'Elegant Dress',
        ], [
            'slug' => 'elegant-dress',
            'description' => 'Elegant women\'s dresses from Ahlam\'s Girls - "Elegance, Sewn to Perfection"',
            'base_price' => 89.99,
            'is_active' => true,
        ]);

        $abayaType = ProductType::firstOrCreate([
            'name' => 'Abaya',
        ], [
            'slug' => 'abaya',
            'description' => 'Elegant abayas from Ahlam\'s Girls - "Elegance, Sewn to Perfection"',
            'base_price' => 149.99,
            'is_active' => true,
        ]);

        $tshirtType = ProductType::firstOrCreate([
            'name' => 'Casual Wear',
        ], [
            'slug' => 'casual-wear',
            'description' => 'Casual wear from Ahlam\'s Girls - "Elegance, Sewn to Perfection"',
            'base_price' => 29.99,
            'is_active' => true,
        ]);

        // Create sample design templates for Ahlam's Girls brand
        $templateData = [
            [
                'name' => 'Elegant Black Dress',
                'description' => 'An elegant black dress with sophisticated design, suitable for formal occasions',
                'category' => 'elegant',
                'is_premium' => true,
                'price' => 12.99,
            ],
            [
                'name' => 'Floral Abaya',
                'description' => 'Elegant abaya with delicate floral patterns, perfect for traditional occasions',
                'category' => 'traditional',
                'is_premium' => true,
                'price' => 15.99,
            ],
            [
                'name' => 'Casual Elegant Top',
                'description' => 'Stylish casual top with elegant touches for everyday wear',
                'category' => 'casual',
                'is_premium' => false,
                'price' => 0.00,
            ],
            [
                'name' => 'Wedding Guest Dress',
                'description' => 'Perfect dress for wedding guests with intricate details',
                'category' => 'wedding',
                'is_premium' => true,
                'price' => 18.99,
            ],
            [
                'name' => 'Summer Floral Dress',
                'description' => 'Light summer dress with floral patterns, perfect for warm weather',
                'category' => 'summer',
                'is_premium' => false,
                'price' => 0.00,
            ],
        ];

        // Create design templates
        $templates = collect();
        foreach ($templateData as $templateInfo) {
            // Determine product type based on category
            $productTypeId = $dressType->id;
            if ($templateInfo['category'] === 'traditional') {
                $productTypeId = $abayaType->id;
            } elseif ($templateInfo['category'] === 'casual') {
                $productTypeId = $tshirtType->id;
            }
            
            // Create placeholder template images
            $templateSlug = Str::slug($templateInfo['name']);
            $thumbnailPath = 'templates/thumbnails/' . $templateSlug . '_thumb.jpg';
            $previewPath = 'templates/' . $templateSlug . '_preview.jpg';
            
            // Create placeholder SVG images if they don't exist
            if (!Storage::disk('public')->exists($thumbnailPath)) {
                Storage::disk('public')->put($thumbnailPath, '<svg xmlns="http://www.w3.org/2000/svg" width="200" height="200" viewBox="0 0 200 200"><rect width="200" height="200" fill="#f0f0f0"/><circle cx="100" cy="100" r="50" fill="#d0d0d0" opacity="0.5"/><text x="100" y="110" font-family="Arial" font-size="12" fill="#999" text-anchor="middle">Template Thumb</text></svg>');
            }
            
            if (!Storage::disk('public')->exists($previewPath)) {
                Storage::disk('public')->put($previewPath, '<svg xmlns="http://www.w3.org/2000/svg" width="400" height="400" viewBox="0 0 400 400"><rect width="400" height="400" fill="#f0f0f0"/><circle cx="200" cy="200" r="100" fill="#d0d0d0" opacity="0.5"/><text x="200" y="210" font-family="Arial" font-size="20" fill="#999" text-anchor="middle">Template Preview</text></svg>');
            }
            
            $template = DesignTemplate::firstOrCreate([
                'name' => $templateInfo['name'],
            ], [
                'description' => $templateInfo['description'],
                'product_type_id' => $productTypeId,
                'category' => $templateInfo['category'],
                'tags' => ['ahlam', 'girls', 'yemen', $templateInfo['category'], 'elegant'],
                'thumbnail_url' => '/storage/' . $thumbnailPath,
                'preview_url' => '/storage/' . $previewPath,
                'design_data' => [],
                'is_premium' => $templateInfo['is_premium'],
                'price' => $templateInfo['price'],
                'usage_count' => 0,
            ]);
            $templates->push($template);
        }

        // Create template-based products for Ahlam's Girls
        $products = [
            [
                'name' => "Ahlam's Elegant Black Dress",
                'description' => 'An elegant black dress with sophisticated design, suitable for formal occasions. Features delicate lacework and a flattering silhouette that embodies the elegance of Ahlam\'s Girls brand.',
                'price' => 199.99,
                'template_category' => 'elegant',
                'product_type_id' => $dressType->id,
            ],
            [
                'name' => "Ahlam's Floral Abaya",
                'description' => 'Elegant abaya with delicate floral patterns, perfect for traditional occasions. Crafted with premium fabric and attention to detail, representing "Elegance, Sewn to Perfection".',
                'price' => 299.99,
                'template_category' => 'traditional',
                'product_type_id' => $abayaType->id,
            ],
            [
                'name' => "Ahlam's Casual Elegant Top",
                'description' => 'Stylish casual top with elegant touches for everyday wear. Perfect blend of comfort and sophistication, ideal for the modern Yemeni woman.',
                'price' => 59.99,
                'template_category' => 'casual',
                'product_type_id' => $tshirtType->id,
            ],
            [
                'name' => "Ahlam's Wedding Guest Dress",
                'description' => 'Perfect dress for wedding guests with intricate details. Designed with premium materials and exquisite craftsmanship, embodying the luxury of Ahlam\'s Girls.',
                'price' => 249.99,
                'template_category' => 'wedding',
                'product_type_id' => $dressType->id,
            ],
            [
                'name' => "Ahlam's Summer Floral Dress",
                'description' => 'Light summer dress with floral patterns, perfect for warm weather. Features breathable fabric and elegant design suitable for the Yemeni climate.',
                'price' => 149.99,
                'template_category' => 'summer',
                'product_type_id' => $dressType->id,
            ],
            [
                'name' => "Ahlam's Premium Evening Gown",
                'description' => 'Exquisite evening gown for special occasions. Features hand-sewn details and premium fabrics that showcase the perfection of Ahlam\'s Girls craftsmanship.',
                'price' => 399.99,
                'template_category' => 'evening',
                'product_type_id' => $dressType->id,
            ],
            [
                'name' => "Ahlam's Traditional Hijab Set",
                'description' => 'Elegant hijab set with matching accessories. Designed with premium fabrics and subtle embellishments, reflecting the traditional values of Yemeni women.',
                'price' => 79.99,
                'template_category' => 'traditional',
                'product_type_id' => $tshirtType->id,
            ],
            [
                'name' => "Ahlam's Office Wear Blouse",
                'description' => 'Professional blouse for office wear with elegant details. Perfect for the modern working woman who values both professionalism and style.',
                'price' => 89.99,
                'template_category' => 'professional',
                'product_type_id' => $tshirtType->id,
            ],
            [
                'name' => "Ahlam's Bridal Accessories",
                'description' => 'Beautiful bridal accessories to complement your special day. Carefully crafted pieces that add the perfect touch to your Ahlam\'s Girls wedding dress.',
                'price' => 179.99,
                'template_category' => 'wedding',
                'product_type_id' => $tshirtType->id,
            ],
        ];

        // Create template-based products
        foreach ($products as $index => $productData) {
            $template = $templates->get($index % $templates->count());
            $slug = Str::slug($productData['name']);
            
            // Skip if a product with this slug already exists
            if (Product::where('slug', $slug)->exists()) {
                continue;
            }
            
            $product = Product::create([
                'product_type_id' => $productData['product_type_id'],
                'name' => $productData['name'],
                'slug' => $slug,
                'description' => $productData['description'],
                'sku' => 'AHLAGIRLS-' . strtoupper(Str::random(6)),
                'price' => $productData['price'],
                'sale_price' => $productData['price'] > 100 ? $productData['price'] * 0.85 : null, // 15% discount on higher priced items
                'inventory_count' => rand(20, 50),
                'is_active' => true,
                'design_template_id' => $template->id,
                'template_data' => [
                    'base_template_id' => $template->id,
                    'colors' => ['#000000', '#FFFFFF', '#8B4513', '#DC143C', '#4B0082'], // Elegant colors
                    'sizes_available' => ['XS', 'S', 'M', 'L', 'XL', 'XXL'],
                    'fabric_options' => ['Cotton', 'Silk', 'Lace', 'Chiffon'],
                    'customization_available' => true,
                ],
                'is_template_based' => true,
                'template_category' => $productData['template_category'],
            ]);
            
            // Create a dedicated folder for this product's images
            $productImagesFolder = 'products/' . $slug;
            if (!Storage::disk('public')->exists($productImagesFolder)) {
                Storage::disk('public')->makeDirectory($productImagesFolder);
            }
            
            // Copy template images to the product's dedicated folder
            $this->copyTemplateImagesToProductFolder($product, $template, $productImagesFolder);
        }

        // Create some non-template based products as well for variety
        $regularProducts = [
            [
                'name' => "Ahlam's Signature Scarf",
                'description' => 'Signature scarf from Ahlam\'s Girls collection, featuring elegant patterns inspired by Yemeni traditions.',
                'price' => 49.99,
                'product_type_id' => $tshirtType->id,
            ],
            [
                'name' => "Ahlam's Premium Abaya",
                'description' => 'Premium quality abaya with refined details, representing the pinnacle of Yemeni elegance.',
                'price' => 349.99,
                'product_type_id' => $abayaType->id,
            ],
        ];

        foreach ($regularProducts as $productData) {
            $slug = Str::slug($productData['name']);
            
            // Skip if a product with this slug already exists
            if (Product::where('slug', $slug)->exists()) {
                continue;
            }
            
            $product = Product::create([
                'product_type_id' => $productData['product_type_id'],
                'name' => $productData['name'],
                'slug' => $slug,
                'description' => $productData['description'],
                'sku' => 'AHLAGIRLS-BASIC-' . strtoupper(Str::random(6)),
                'price' => $productData['price'],
                'inventory_count' => rand(30, 70),
                'is_active' => true,
                'is_template_based' => false,
            ]);
            
            // Create a dedicated folder for this product's images
            $productImagesFolder = 'products/' . $slug;
            if (!Storage::disk('public')->exists($productImagesFolder)) {
                Storage::disk('public')->makeDirectory($productImagesFolder);
            }
            
            // Copy template images to the product's dedicated folder
            $this->copyTemplateImagesToProductFolder($product, $templates->first(), $productImagesFolder);
        }
    }
    
    /**
     * Copy template images to the product's dedicated folder
     */
    private function copyTemplateImagesToProductFolder($product, $template, $productImagesFolder)
    {
        $imageOrder = 1;
        
        // Copy the template's preview image to the product folder as main image
        if ($template->preview_url) {
            $previewFileName = pathinfo(parse_url($template->preview_url, PHP_URL_PATH), PATHINFO_BASENAME);
            $newPreviewPath = $productImagesFolder . '/mainImage_' . $product->slug . '.' . pathinfo($previewFileName, PATHINFO_EXTENSION);
            
            // Extract the original template file path
            $originalTemplatePath = str_replace('/storage/', '', parse_url($template->preview_url, PHP_URL_PATH));
            
            if (Storage::disk('public')->exists($originalTemplatePath)) {
                Storage::disk('public')->copy($originalTemplatePath, $newPreviewPath);
                
                ShirtImage::create([
                    'tshirt_id' => $product->id, // Using product id in tshirt_id field for compatibility
                    'url' => '/storage/' . $newPreviewPath,
                    'order' => $imageOrder++
                ]);
                
                // Set as thumbnail if not already set
                if (!$product->thumbnail_url) {
                    $product->update(['thumbnail_url' => '/storage/' . $newPreviewPath]);
                }
            }
        }
        
        // Copy the template's thumbnail image to the product folder
        if ($template->thumbnail_url) {
            $thumbnailFileName = pathinfo(parse_url($template->thumbnail_url, PHP_URL_PATH), PATHINFO_BASENAME);
            $newThumbnailPath = $productImagesFolder . '/thumbnail_' . $product->slug . '.' . pathinfo($thumbnailFileName, PATHINFO_EXTENSION);
            
            // Extract the original template file path
            $originalTemplatePath = str_replace('/storage/', '', parse_url($template->thumbnail_url, PHP_URL_PATH));
            
            if (Storage::disk('public')->exists($originalTemplatePath)) {
                Storage::disk('public')->copy($originalTemplatePath, $newThumbnailPath);
                
                ShirtImage::create([
                    'tshirt_id' => $product->id, // Using product id in tshirt_id field for compatibility
                    'url' => '/storage/' . $newThumbnailPath,
                    'order' => $imageOrder++
                ]);
            }
        }
        
        // Add additional template images to reach up to 5 images per product
        // Copy the template's preview image again as additional images with different names
        if ($template->preview_url && $imageOrder <= 5) {
            $previewFileName = pathinfo(parse_url($template->preview_url, PHP_URL_PATH), PATHINFO_BASENAME);
            $additionalIndex = 1;
            while ($imageOrder <= 5) {
                $newAdditionalPath = $productImagesFolder . '/additional_' . $additionalIndex . '_' . $product->slug . '.' . pathinfo($previewFileName, PATHINFO_EXTENSION);
                
                // Extract the original template file path
                $originalTemplatePath = str_replace('/storage/', '', parse_url($template->preview_url, PHP_URL_PATH));
                
                if (Storage::disk('public')->exists($originalTemplatePath)) {
                    Storage::disk('public')->copy($originalTemplatePath, $newAdditionalPath);
                    
                    ShirtImage::create([
                        'tshirt_id' => $product->id, // Using product id in tshirt_id field for compatibility
                        'url' => '/storage/' . $newAdditionalPath,
                        'order' => $imageOrder++
                    ]);
                }
                $additionalIndex++;
            }
        }
    }
}