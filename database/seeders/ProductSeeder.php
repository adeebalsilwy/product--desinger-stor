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
        // إنشاء أنواع المنتجات اليمنية
        $dressType = ProductType::firstOrCreate([
            'name' => 'فستان يمني فاخر',
        ], [
            'slug' => 'yemeni-luxury-dress',
            'description' => 'فساتين يمنية فاخرة مطرزة يدوياً - "أناقة مخيطة بإتقان"'
]);

        $abayaType = ProductType::firstOrCreate([
            'name' => 'عباية يمنية تقليدية',
        ], [
            'slug' => 'traditional-yemeni-abaya',
            'description' => 'عبايات يمنية تقليدية بتطريز ذهبي - "أصالة وأناقة يمنية"',
            'base_price' => 399.99,
            'is_active' => true,
        ]);

        $blouseType = ProductType::firstOrCreate([
            'name' => 'بلوزة يمنية عصرية',
        ], [
            'slug' => 'modern-yemeni-blouse',
            'description' => 'بلوزات يمنية عصرية تجمع بين الأصالة والمعاصرة',
            'base_price' => 79.99,
            'is_active' => true,
        ]);

        // إنشاء قوالب تصميم للأزياء اليمنية
        $templateData = [
            [
                'name' => 'فستان صنعاني فاخر',
                'description' => 'فستان صنعاني فاخر بتطريز ذهبي دقيق وتصاميم مستوحاة من التراث اليمني الأصيل',
                'category' => 'traditional',
                'is_premium' => true,
                'price' => 25.99,
            ],
            [
                'name' => 'عباية تعز الذهبية',
                'description' => 'عباية فاخرة من تعز مطرزة بخيوط الذهب، مثالية للمناسبات الخاصة والأعراس',
                'category' => 'luxury',
                'is_premium' => true,
                'price' => 35.99,
            ],
            [
                'name' => 'بلوزة عدن العصرية',
                'description' => 'بلوزة عصرية تجمع بين أصالة الماضي وجمال الحاضر، مناسبة للاستخدام اليومي',
                'category' => 'casual',
                'is_premium' => false,
                'price' => 0.00,
            ],
            [
                'name' => 'فستان عروس حضرمي',
                'description' => 'فستان عروس تقليدي من حضرموت بتطريزات ملونة وزخارف بدوية أصيلة',
                'category' => 'wedding',
                'is_premium' => true,
                'price' => 45.99,
            ],
            [
                'name' => 'ثوب صيفي يمني',
                'description' => 'ثوب صيفي خفيف بألوان زاهية وتصاميم مزهرة تعكس جمال الطبيعة اليمنية',
                'category' => 'summer',
                'is_premium' => false,
                'price' => 0.00,
            ],
        ];

        // Create design templates
        $templates = collect();
        foreach ($templateData as $templateInfo) {
            // تحديد نوع المنتج بناءً على الفئة
            $productTypeId = $dressType->id;
            if ($templateInfo['category'] === 'luxury') {
                $productTypeId = $abayaType->id;
            } elseif ($templateInfo['category'] === 'casual') {
                $productTypeId = $blouseType->id;
            }
            
            // إنشاء مسار الصور من مجلد القوالب
            $templateSlug = Str::slug($templateInfo['name']);
            $thumbnailPath = 'template/' . $templateSlug . '_thumb.jpg';
            $previewPath = 'template/' . $templateSlug . '_preview.jpg';
            
            // استخدام الصور من المسار المحدد F:\my project\laravel\ghyda\d-shirts-main\storage\app\public\template
            $templateDir = storage_path('app/public/template');
            
            if (File::exists($templateDir)) {
                // Get all image files from the template directory
                $imageFiles = File::files($templateDir);
                $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'];
                
                $firstImage = null;
                $firstThumbnail = null;
                
                foreach ($imageFiles as $file) {
                    $extension = strtolower($file->getExtension());
                    if (in_array($extension, $imageExtensions)) {
                        $fileName = $file->getFilename();
                        
                        if (!$firstImage) {
                            $firstImage = $fileName;
                        }
                        
                        if (str_contains(strtolower($fileName), 'thumb') || str_contains(strtolower($fileName), 'thumbnail')) {
                            $firstThumbnail = $fileName;
                            break;
                        }
                    }
                }
                
                if (!$firstThumbnail && $firstImage) {
                    $firstThumbnail = $firstImage; // Use the same image if no specific thumbnail found
                }
                
                if ($firstThumbnail) {
                    $thumbnailPath = 'template/' . $firstThumbnail;
                } else {
                    // Fallback to SVG if no images exist
                    $thumbnailPath = 'template/' . $templateSlug . '_thumb.jpg';
                    Storage::disk('public')->put($thumbnailPath, '<svg xmlns="http://www.w3.org/2000/svg" width="200" height="200" viewBox="0 0 200 200"><rect width="200" height="200" fill="#f5e6d3"/><circle cx="100" cy="100" r="50" fill="#d4a5a2" opacity="0.5"/><text x="100" y="110" font-family="Arial" font-size="12" fill="#8B4513" text-anchor="middle">قالب يمني</text></svg>');
                }
                
                if ($firstImage) {
                    $previewPath = 'template/' . $firstImage;
                } else {
                    // Fallback to SVG if no images exist
                    $previewPath = 'template/' . $templateSlug . '_preview.jpg';
                    Storage::disk('public')->put($previewPath, '<svg xmlns="http://www.w3.org/2000/svg" width="400" height="400" viewBox="0 0 400 400"><rect width="400" height="400" fill="#f5e6d3"/><circle cx="200" cy="200" r="100" fill="#d4a5a2" opacity="0.5"/><text x="200" y="210" font-family="Arial" font-size="20" fill="#8B4513" text-anchor="middle">تصميم يمني أصيل</text></svg>');
                }
            } else {
                // Create placeholder SVG images if template directory doesn't exist
                $thumbnailPath = 'template/' . $templateSlug . '_thumb.jpg';
                $previewPath = 'template/' . $templateSlug . '_preview.jpg';
                
                Storage::disk('public')->put($thumbnailPath, '<svg xmlns="http://www.w3.org/2000/svg" width="200" height="200" viewBox="0 0 200 200"><rect width="200" height="200" fill="#f5e6d3"/><circle cx="100" cy="100" r="50" fill="#d4a5a2" opacity="0.5"/><text x="100" y="110" font-family="Arial" font-size="12" fill="#8B4513" text-anchor="middle">قالب يمني</text></svg>');
                
                Storage::disk('public')->put($previewPath, '<svg xmlns="http://www.w3.org/2000/svg" width="400" height="400" viewBox="0 0 400 400"><rect width="400" height="400" fill="#f5e6d3"/><circle cx="200" cy="200" r="100" fill="#d4a5a2" opacity="0.5"/><text x="200" y="210" font-family="Arial" font-size="20" fill="#8B4513" text-anchor="middle">تصميم يمني أصيل</text></svg>');
            }
            
            $template = DesignTemplate::firstOrCreate([
                'name' => $templateInfo['name'],
            ], [
                'description' => $templateInfo['description'],
                'product_type_id' => $productTypeId,
                'category' => $templateInfo['category'],
                'tags' => ['يمني', 'أصيل', 'تراث', $templateInfo['category'], 'أزياء يمنية'],
                'thumbnail_url' => '/storage/' . $thumbnailPath,
                'preview_url' => '/storage/' . $previewPath,
                'design_data' => [],
                'is_premium' => $templateInfo['is_premium'],
                'price' => $templateInfo['price'],
                'usage_count' => 0,
            ]);
            $templates->push($template);
        }

        // إنشاء منتجات قائمة على القوالب للأزياء اليمنية
        $products = [
            [
                'name' => "فستان صنعاني فاخر بالذهب",
                'description' => 'فستان صنعاني تقليدي فاخر مطرز بخيوط الذهب الدقيقة، بتصاميم مستوحاة من التراث اليمني الأصيل. يعكس جمال وأناقة المرأة اليمنية في المناسبات الخاصة.',
                'price' => 599.99,
                'template_category' => 'traditional',
                'product_type_id' => $dressType->id,
            ],
            [
                'name' => "عباية تعز الملكية",
                'description' => 'عباية فاخرة من تعز بتطريز ذهبي يدوي دقيق، تصميم ملكي خاص يجمع بين الأصالة والفخامة، مثالي للأعراس والمناسبات الرسمية.',
                'price' => 899.99,
                'template_category' => 'luxury',
                'product_type_id' => $abayaType->id,
            ],
            [
                'name' => "بلوزة عدن العصرية",
                'description' => 'بلوزة يمنية عصرية تجمع بين أصالة الماضي وجمال الحاضر، تصميم أنيق ومناسب للاستخدام اليومي للمرأة اليمنية الحديثة.',
                'price' => 149.99,
                'template_category' => 'casual',
                'product_type_id' => $blouseType->id,
            ],
            [
                'name' => "فستان عروس حضرمي تقليدي",
                'description' => 'فستان عروس تقليدي من حضرموت بتطريزات ملونة وزخارف بدوية أصيلة، تصميم فريد يعكس تراث العروس اليمنية الساحلي.',
                'price' => 1299.99,
                'template_category' => 'wedding',
                'product_type_id' => $dressType->id,
            ],
            [
                'name' => "ثوب صيفي يمني مزهر",
                'description' => 'ثوب صيفي خفيف بألوان زاهية وتصاميم مزهرة تعكس جمال الطبيعة اليمنية، مناسب للجو الدافئ ويبرز الأنوثة اليمنية.',
                'price' => 249.99,
                'template_category' => 'summer',
                'product_type_id' => $dressType->id,
            ],
            [
                'name' => "جلابية لحجية فاخرة",
                'description' => 'جلابية لحجية فاخرة بتطريزات فضية دقيقة، تصميم أنيق يجمع بين تراث لحج وأناقته، مثالي للمناسبات المسائية.',
                'price' => 749.99,
                'template_category' => 'evening',
                'product_type_id' => $dressType->id,
            ],
            [
                'name' => "مجموعة حجاب يمني مطرز",
                'description' => 'مجموعة حجاب يمني فاخر مطرز بتطريز يدوي دقيق مع إكسسوارات متطابقة، تصميم يعكس الحشمة والأناقة اليمنية التقليدية.',
                'price' => 199.99,
                'template_category' => 'traditional',
                'product_type_id' => $blouseType->id,
            ],
            [
                'name' => "بلوزة عملية للأنشطة اليومية",
                'description' => 'بلوزة يمنية عملية مصممة خصيصاً للأنشطة اليومية والعمل، تجمع بين الاحترافية والأناقة اليمنية الأصيلة.',
                'price' => 179.99,
                'template_category' => 'professional',
                'product_type_id' => $blouseType->id,
            ],
            [
                'name' => "إكسسوارات عروس يمنية تقليدية",
                'description' => 'مجموعة إكسسوارات عروس يمنية تقليدية تشمل أحزمة ومجوهرات تقليدية، تصاميم يدوية تعكس جمال التراث اليمني للأعراس.',
                'price' => 449.99,
                'template_category' => 'wedding',
                'product_type_id' => $blouseType->id,
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
                'sku' => 'YEMEN-' . strtoupper(Str::random(6)),
                'price' => $productData['price'],
                'sale_price' => $productData['price'] > 300 ? $productData['price'] * 0.85 : null, // خصم 15% على المنتجات الغالية
                'inventory_count' => rand(15, 40),
                'is_active' => true,
                'design_template_id' => $template->id,
                'template_data' => [
                    'base_template_id' => $template->id,
                    'colors' => ['#8B4513', '#DAA520', '#DC143C', '#4B0082', '#2F4F4F'], // ألوان تراثية يمنية
                    'sizes_available' => ['XS', 'S', 'M', 'L', 'XL', 'XXL'],
                    'fabric_options' => ['قطن يمني', 'حرير طبيعي', 'كتان فاخر', 'شيفون'],
                    'customization_available' => true,
                ],
                'is_template_based' => true,
                'template_category' => $productData['template_category'],
                'thumbnail_url' => $template->thumbnail_url, // Set the thumbnail from template
            ]);
            
            // Set the thumbnail URL for the product
            $product->update(['thumbnail_url' => $template->thumbnail_url]);
            
            // إنشاء مجلد مخصص لصور هذا المنتج باستخدام مسار القوالب
            $productImagesFolder = 'products/' . $slug;
            if (!Storage::disk('public')->exists($productImagesFolder)) {
                Storage::disk('public')->makeDirectory($productImagesFolder);
            }
            
            // نسخ صور القوالب من المسار المحدد إلى مجلد المنتج
            // F:\my project\laravel\ghyda\d-shirts-main\storage\app\public\template
            $this->copyTemplateImagesToProductFolder($product, $template, $productImagesFolder);
        }

        // إنشاء منتجات إضافية غير قائمة على القوالب للتنوع
        $regularProducts = [
            [
                'name' => "وشاح يمني تقليدي مطرز",
                'description' => 'وشاح يمني أصيل بتطريزات يدوية تعكس التراث اليمني الغني، تصميم فريد من نوعه.',
                'price' => 99.99,
                'product_type_id' => $blouseType->id,
            ],
            [
                'name' => "عباية ملكية فاخرة جداً",
                'description' => 'عباية ملكية استثنائية بتطريز ذهبي وفضي دقيق، قمة الفخامة اليمنية والأناقة.',
                'price' => 1499.99,
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
                'sku' => 'YEMEN-BASIC-' . strtoupper(Str::random(6)),
                'price' => $productData['price'],
                'inventory_count' => rand(20, 50),
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
     * نسخ صور القوالب إلى مجلد المنتج المخصص
     */
    private function copyTemplateImagesToProductFolder($product, $template, $productImagesFolder)
    {
        // Get all image files from the template directory
        $templateDir = storage_path('app/public/template');
        
        if (!File::exists($templateDir)) {
            $this->command->warn("  ⚠️ Template directory not found: {$templateDir}");
            return;
        }
        
        // Get all image files from template directory
        $validExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'];
        $templateFiles = [];
        
        foreach (scandir($templateDir) as $file) {
            $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
            if (in_array($extension, $validExtensions) && is_file($templateDir . '/' . $file)) {
                $templateFiles[] = $file;
            }
        }
        
        if (empty($templateFiles)) {
            $this->command->warn("  ⚠️ No template images found in: {$templateDir}");
            return;
        }
        
        // Shuffle and select 5 random images
        shuffle($templateFiles);
        $selectedTemplates = array_slice($templateFiles, 0, 5);
        
        $imageOrder = 1;
        
        foreach ($selectedTemplates as $templateFile) {
            try {
                $newFileName = "image_{$imageOrder}_{$product->slug}." . pathinfo($templateFile, PATHINFO_EXTENSION);
                $newFilePath = $productImagesFolder . '/' . $newFileName;
                
                // Copy the image from template to product folder
                File::copy($templateDir . '/' . $templateFile, storage_path('app/public/' . $newFilePath));
                
                // Create database record for the image
                ShirtImage::create([
                    'tshirt_id' => $product->id,
                    'url' => '/storage/' . $newFilePath,
                    'order' => $imageOrder
                ]);
                
                $this->command->info("  ✓ Created: {$newFileName} (from: {$templateFile})");
                
                $imageOrder++;
            } catch (\Exception $e) {
                $this->command->error("  ✗ Failed to copy {$templateFile}: " . $e->getMessage());
            }
        }
        
        // Set first image as thumbnail
        if ($imageOrder > 1 && !$product->thumbnail_url) {
            $firstImage = ShirtImage::where('tshirt_id', $product->id)->orderBy('order')->first();
            if ($firstImage) {
                $product->update(['thumbnail_url' => $firstImage->url]);
                $this->command->info("  ✓ Set thumbnail URL");
            }
        }
    }





}