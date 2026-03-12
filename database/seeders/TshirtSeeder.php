<?php

namespace Database\Seeders;

use App\Helpers\TitleToFolderName;
use App\Models\ShirtImage;
use App\Models\Tshirt;
use App\Models\Product;
use App\Models\ProductType;
use App\Models\DesignTemplate;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class TshirtSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // Get or create design templates first
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

        // Array of t-shirt data with Ahlam's Girls brand theme
        $tshirts = [
            [
                'title' => "Ahlam's Elegant Women Fashion T-Shirt",
                'description' => "This elegant t-shirt design reflects the sophistication of Ahlam's Girls brand. Featuring a sophisticated logo with an elegant woman in a black dress and hat adorned with flowers, this t-shirt embodies the brand's motto 'Elegance, Sewn to Perfection'. Perfect for fashion-conscious women who appreciate elegant style.",
            ],
            [
                'title' => "Ahlam's Girls Premium Casual T-Shirt",
                'description' => "A premium casual t-shirt inspired by Ahlam's Girls elegant fashion line. Features delicate floral accents and sophisticated design elements that reflect the brand's commitment to elegant fashion for Yemeni women.",
            ],
            [
                'title' => "Elegance, Sewn to Perfection T-Shirt",
                'description' => "This t-shirt proudly displays the Ahlam's Girls motto: 'Elegance, Sewn to Perfection'. Designed with elegant typography and subtle floral elements that represent the brand's dedication to quality and style.",
            ],
            [
                'title' => "Ahlam's Yemeni Elegance T-Shirt",
                'description' => "A celebration of Yemeni elegance in fashion. This t-shirt features design elements inspired by traditional Yemeni aesthetics combined with modern sophistication, representing the unique style of Ahlam's Girls.",
            ],
            [
                'title' => "Sophisticated Woman Style T-Shirt",
                'description' => "Designed for the sophisticated woman who appreciates elegance in every detail. This t-shirt reflects the refined style of Ahlam's Girls brand with its elegant design and premium quality.",
            ],
            [
                'title' => "Ahlam's Floral Fashion T-Shirt",
                'description' => "This elegant t-shirt features delicate floral patterns that complement the elegant woman figure logo of Ahlam's Girls. The transparent flowers add a soft feminine touch that defines the brand's elegant style.",
            ],
            [
                'title' => "Black Dress Elegance T-Shirt",
                'description' => "Inspired by the signature black dress in Ahlam's Girls logo, this t-shirt captures the essence of elegant dressing. Perfect for women who value sophisticated style and quality craftsmanship.",
            ],
            [
                'title' => "Yemeni Fashion Excellence T-Shirt",
                'description' => "A tribute to Yemeni fashion excellence as represented by Ahlam's Girls. This t-shirt combines traditional values with contemporary style, embodying the perfect balance of elegance and modernity.",
            ],
            [
                'title' => "Ahlam's Logo Heritage T-Shirt",
                'description' => "Featuring the beautiful Ahlam's Girls logo with the elegant woman in black dress and flower-adorned hat, this t-shirt celebrates the brand's heritage and commitment to elegant fashion in Yemen.",
            ],
        ];

        // Get or create the T-shirt product type with Ahlam's Girls theme
        $tshirtType = ProductType::firstOrCreate([
            'name' => 'Casual Wear',
        ], [
            'slug' => 'casual-wear',
            'description' => 'Casual wear from Ahlam\'s Girls - "Elegance, Sewn to Perfection"',
            'base_price' => 29.99,
            'is_active' => true,
        ]);

        // Seed each t-shirt
        foreach ($tshirts as $index => $tshirtData) {
            // Select a template for this t-shirt
            $template = $templates->get($index % $templates->count());
            
            // Create T-shirt record for backward compatibility if it doesn't exist
            $tshirt = Tshirt::firstOrCreate([
                'slug' => Str::slug($tshirtData['title'], '-'),
            ], [
                'name' => $tshirtData['title'],
                'description' => $tshirtData['description'],
                'price' => 49.99, // Premium pricing for Ahlam's Girls brand
                'images_folder_name' => Str::slug($tshirtData['title'], '-'),
                'sku' => 'TS-' . strtoupper(Str::random(8)), // Generate a temporary SKU
                'is_active' => true, // Set as active
            ]);

            // Create a dedicated folder for this product's images
            $productSlug = Str::slug($tshirtData['title'], '-');
            $productImagesFolder = 'products/' . $productSlug;
            if (!Storage::disk('public')->exists($productImagesFolder)) {
                Storage::disk('public')->makeDirectory($productImagesFolder);
            }

            // Only create images if they don't exist
            $existingImagesCount = $tshirt->images()->count();
            if ($existingImagesCount === 0) {
                $imageOrder = 1;
                
                // Copy the template's preview image to the product folder as main image
                if ($template->preview_url) {
                    $previewFileName = pathinfo(parse_url($template->preview_url, PHP_URL_PATH), PATHINFO_BASENAME);
                    $newPreviewPath = $productImagesFolder . '/mainImage_' . $productSlug . '.' . pathinfo($previewFileName, PATHINFO_EXTENSION);
                    
                    // Extract the original template file path
                    $originalTemplatePath = str_replace('/storage/', '', parse_url($template->preview_url, PHP_URL_PATH));
                    
                    if (Storage::disk('public')->exists($originalTemplatePath)) {
                        Storage::disk('public')->copy($originalTemplatePath, $newPreviewPath);
                        
                        ShirtImage::create([
                            'tshirt_id' => $tshirt->id,
                            'url' => '/storage/' . $newPreviewPath,
                            'order' => $imageOrder++
                        ]);
                    }
                }
                
                // Copy the template's thumbnail image to the product folder
                if ($template->thumbnail_url) {
                    $thumbnailFileName = pathinfo(parse_url($template->thumbnail_url, PHP_URL_PATH), PATHINFO_BASENAME);
                    $newThumbnailPath = $productImagesFolder . '/thumbnail_' . $productSlug . '.' . pathinfo($thumbnailFileName, PATHINFO_EXTENSION);
                    
                    // Extract the original template file path
                    $originalTemplatePath = str_replace('/storage/', '', parse_url($template->thumbnail_url, PHP_URL_PATH));
                    
                    if (Storage::disk('public')->exists($originalTemplatePath)) {
                        Storage::disk('public')->copy($originalTemplatePath, $newThumbnailPath);
                        
                        ShirtImage::create([
                            'tshirt_id' => $tshirt->id,
                            'url' => '/storage/' . $newThumbnailPath,
                            'order' => $imageOrder++
                        ]);
                    }
                }
                
                // Add additional template images if available
                // Use the same template images for additional images
                if ($template->preview_url && $imageOrder <= 5) {
                    $previewFileName = pathinfo(parse_url($template->preview_url, PHP_URL_PATH), PATHINFO_BASENAME);
                    $newAdditionalPath = $productImagesFolder . '/additional_1_' . $productSlug . '.' . pathinfo($previewFileName, PATHINFO_EXTENSION);
                    
                    // Extract the original template file path
                    $originalTemplatePath = str_replace('/storage/', '', parse_url($template->preview_url, PHP_URL_PATH));
                    
                    if (Storage::disk('public')->exists($originalTemplatePath)) {
                        Storage::disk('public')->copy($originalTemplatePath, $newAdditionalPath);
                        
                        ShirtImage::create([
                            'tshirt_id' => $tshirt->id,
                            'url' => '/storage/' . $newAdditionalPath,
                            'order' => $imageOrder++
                        ]);
                    }
                }
            }

            // Also create a corresponding template-based product for the new system if it doesn't exist
            $productSlug = Str::slug($tshirtData['title'], '-');
            $existingProduct = Product::where('slug', $productSlug)->first();
            
            if (!$existingProduct) {
                Product::create([
                    'product_type_id' => $tshirtType->id,
                    'name' => $tshirtData['title'],
                    'slug' => $productSlug,
                    'description' => $tshirtData['description'],
                    'sku' => 'AHLAGIRLS-TSHIRT-' . strtoupper(Str::random(6)),
                    'price' => 49.99,
                    'sale_price' => 44.99, // Discount price
                    'inventory_count' => rand(50, 100), // Random inventory count
                    'is_active' => true,
                    'design_template_id' => $template->id,
                    'thumbnail_url' => $template->preview_url, // Use template's preview as thumbnail
                    'is_template_based' => true,
                    'template_category' => $template->category,
                    'template_data' => [
                        'original_tshirt_id' => $tshirt->id,
                        'base_design' => 'elegant-women-themed',
                        'colors' => ['#000000', '#FFFFFF', '#8B4513', '#DC143C'], // Elegant colors
                        'sizes_available' => ['XS', 'S', 'M', 'L', 'XL', 'XXL'],
                        'fabric_options' => ['Premium Cotton', 'Silk Touch'],
                    ],
                ]);
            }
        }
    }
}