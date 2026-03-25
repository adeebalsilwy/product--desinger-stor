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
        // First, get all image files from the template directory
        $templateDir = storage_path('app/public/template');
        
        if (!File::exists($templateDir)) {
            // If template directory doesn't exist, create placeholder images
            $this->generatePlaceholderImages($product, $productImagesFolder);
            return;
        }
        
        // Get all image files from the template directory
        $imageFiles = File::files($templateDir);
        $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'];
        
        $imageOrder = 1;
        
        foreach ($imageFiles as $file) {
            $extension = strtolower($file->getExtension());
            if (in_array($extension, $imageExtensions)) {
                // Limit to 5 images per product
                if ($imageOrder > 5) break;
                
                $fileName = $file->getFilename();
                $newFileName = 'image_' . $imageOrder . '_' . $product->slug . '.' . $extension;
                $newFilePath = $productImagesFolder . '/' . $newFileName;
                
                // Copy the image from template to product folder
                File::copy($file->getPathname(), storage_path('app/public/' . $newFilePath));
                
                // Create database record for the image
                ShirtImage::create([
                    'tshirt_id' => $product->id,
                    'url' => '/storage/' . $newFilePath,
                    'order' => $imageOrder
                ]);
                
                $imageOrder++;
            }
        }
        
        // If no images were copied, generate placeholders
        if ($imageOrder == 1) {
            $this->generatePlaceholderImages($product, $productImagesFolder);
        }
    }
    
    private function generatePlaceholderImages($product, $productImagesFolder)
    {
        // Generate real product images with proper colors and styling
        $this->generateRealProductImages($product, $productImagesFolder);
    }
    
    /**
     * إنشاء صور منتج حقيقية بألوان وتصاميم احترافية
     */
    private function generateRealProductImages($product, $folder)
    {
        if (!class_exists('\Intervention\Image\ImageManagerStatic')) {
            $this->command->warn("  ⚠️ Intervention Image غير متوفر، يتم تجاوز إنشاء الصور");
            return;
        }
        
        \Illuminate\Support\Facades\Log::info("إنشاء صور للمنتج: {$product->name}", [
            'slug' => $product->slug,
            'type' => $product->productType ? $product->productType->name : 'Unknown'
        ]);
        
        // الحصول على الألوان بناءً على نوع المنتج
        $colors = $this->getColorsForProduct($product);
        
        // إنشاء 5 صور احترافية للمنتج
        $imageConfigs = [
            ['type' => 'mainImage', 'color' => $colors[0], 'accent' => $colors[1], 'text' => $product->name],
            ['type' => 'thumbnail', 'color' => $colors[1], 'accent' => $colors[2], 'text' => 'معاينة'],
            ['type' => 'additional_1', 'color' => $colors[2], 'accent' => $colors[3], 'text' => 'عرض التفاصيل'],
            ['type' => 'additional_2', 'color' => $colors[3], 'accent' => $colors[4], 'text' => 'المنظر الجانبي'],
            ['type' => 'additional_3', 'color' => $colors[4], 'accent' => $colors[0], 'text' => 'المنظر الخلفي'],
        ];
        
        $imageOrder = 1;
        
        foreach ($imageConfigs as $config) {
            try {
                $imageName = "{$config['type']}_{$product->slug}.jpg";
                $imagePath = "{$folder}/{$imageName}";
                
                // Create high-quality product image (1000x1000)
                $img = \Intervention\Image\ImageManagerStatic::canvas(1000, 1000, '#ffffff');
                
                // Draw gradient background
                $this->drawGradientBackground($img, $config['color']);
                
                // Draw product representation
                $this->drawProductRepresentation($img, $product, $config['color'], $config['accent']);
                
                // Add elegant text overlay
                $img->text($config['text'], 500, 900, function($font) {
                    $font->file(public_path('fonts/arial.ttf'));
                    $font->size(28);
                    $font->color('#333333');
                    $font->align('center');
                    $font->valign('top');
                    $font->shadow([0, 2, 'rgba(0,0,0,0.1)']);
                });
                
                // إضافة نص عربي أنيق في الأعلى
                $img->text('أزياء يمنية أصيلة', 500, 40, function($font) {
                    $font->file(public_path('fonts/arial.ttf'));
                    $font->size(20);
                    $font->color('#DAA520'); // لون ذهبي
                    $font->align('center');
                });
                
                // حفظ صورة JPG عالية الجودة
                Storage::disk('public')->put($imagePath, $img->encode('jpg', 95));
                
                // إنشاء سجل قاعدة البيانات
                ShirtImage::create([
                    'tshirt_id' => $product->id,
                    'url' => '/storage/' . $imagePath,
                    'order' => $imageOrder++
                ]);
                
                $this->command->info("  ✓ تم الإنشاء: {$imageName}");
                
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error("فشل إنشاء صورة لـ {$product->slug}: " . $e->getMessage());
                $this->command->error("  ✗ فشل إنشاء {$config['type']}: " . $e->getMessage());
            }
        }
        
        // تعيين الصورة الأولى كصورة مصغرة
        if ($imageOrder > 1 && !$product->thumbnail_url) {
            $firstImage = ShirtImage::where('tshirt_id', $product->id)->orderBy('order')->first();
            if ($firstImage) {
                $product->update(['thumbnail_url' => $firstImage->url]);
                $this->command->info("  ✓ تم تعيين URL الصورة المصغرة");
            }
        }
    }
    
    /**
     * Draw gradient background
     */
    private function drawGradientBackground($img, $color)
    {
        // Create gradient effect
        for ($i = 0; $i < 1000; $i += 10) {
            $alpha = 1 - ($i / 1000);
            $img->rectangle(0, $i, 1000, $i + 10, function($draw) use ($color, $alpha) {
                $draw->background($color);
                $draw->opacity($alpha * 0.3);
            });
        }
    }
    
    /**
     * رسم تمثيل المنتج بناءً على النوع
     */
    private function drawProductRepresentation($img, $product, $mainColor, $accentColor)
    {
        $productType = $product->productType ? strtolower($product->productType->name) : '';
        
        if (str_contains($productType, 'فستان') || str_contains($productType, 'jalabiya') || str_contains($productType, 'عباية')) {
            // رسم شكل أنيق للفسان أو العباية
            $img->ellipse(500, 350, 180, 220, function($draw) use ($mainColor) {
                $draw->background($mainColor);
                $draw->border(3, '#ffffff');
                $draw->opacity(0.9);
            });
            $img->polygon([400, 450, 600, 450, 550, 700, 450, 700], function($draw) use ($mainColor) {
                $draw->background($mainColor);
                $draw->border(2, '#ffffff');
                $draw->opacity(0.85);
            });
            
            // إضافة عناصر زخرفية يمنية
            $img->ellipse(500, 350, 80, 100, function($draw) use ($accentColor) {
                $draw->background($accentColor);
                $draw->opacity(0.3);
            });
        } elseif (str_contains($productType, 'بلوزة') || str_contains($productType, 'casual') || str_contains($productType, 'ثوب')) {
            // رسم شكل بلوزة/ثوب
            $img->rectangle(350, 250, 650, 550, function($draw) use ($mainColor) {
                $draw->background($mainColor);
                $draw->border(3, '#ffffff');
                $draw->opacity(0.9);
            });
            $img->rectangle(300, 250, 350, 330, function($draw) use ($mainColor) {
                $draw->background($mainColor);
                $draw->opacity(0.85);
            });
            $img->rectangle(650, 250, 700, 330, function($draw) use ($mainColor) {
                $draw->background($mainColor);
                $draw->opacity(0.85);
            });
            
            // إضافة تفاصيل تصميم يمني
            $img->circle(500, 400, 50, function($draw) use ($accentColor) {
                $draw->background($accentColor);
                $draw->border(2, '#ffffff');
                $draw->opacity(0.4);
            });
        } elseif (str_contains($productType, 'وشاح') || str_contains($productType, 'حجاب')) {
            // رسم شكل وشاح/قماش متدفق
            $img->ellipse(500, 400, 250, 180, function($draw) use ($mainColor) {
                $draw->background($mainColor);
                $draw->border(3, '#ffffff');
                $draw->opacity(0.8);
            });
            
            // تأثير القماش المتدفق
            $img->ellipse(400, 500, 150, 100, function($draw) use ($accentColor) {
                $draw->background($accentColor);
                $draw->opacity(0.5);
            });
        } else {
            // مربع منتج عام فاخر
            $img->rectangle(300, 300, 700, 700, function($draw) use ($mainColor) {
                $draw->background($mainColor);
                $draw->border(4, '#ffffff');
                $draw->opacity(0.9);
            });
            
            // إضافة شارة فاخرة
            $img->circle(500, 500, 80, function($draw) use ($accentColor) {
                $draw->background($accentColor);
                $draw->border(3, '#ffffff');
                $draw->opacity(0.6);
            });
        }
    }
    
    /**
     * الحصول على الألوان للمنتج بناءً على النوع
     */
    private function getColorsForProduct($product)
    {
        $productType = $product->productType ? strtolower($product->productType->name) : '';
        
        if (str_contains($productType, 'فستان') || str_contains($productType, 'gown') || str_contains($productType, 'سهره')) {
            // ألوان السهرات الفاخرة
            return ['#1a1a2e', '#16213e', '#0f3460', '#e94560', '#533483'];
        } elseif (str_contains($productType, 'عباية')) {
            // ألوان العبايات التقليدية
            return ['#000000', '#1a1a1a', '#2d2d2d', '#3d3d3d', '#DAA520']; // أسود مع ذهبي
        } elseif (str_contains($productType, 'عرس') || str_contains($productType, 'عروس')) {
            // ألوان الزفاف الباستيلية
            return ['#f8e1e7', '#f0c1d1', '#e8a1b9', '#df81a1', '#d66189'];
        } elseif (str_contains($productType, 'بلوزة') || str_contains($productType, 'casual') || str_contains($productType, 'ثوب')) {
            // ألوان زاهية يمنية تقليدية
            return ['#8B4513', '#DAA520', '#DC143C', '#4B0082', '#2F4F4F'];
        } elseif (str_contains($productType, 'وشاح') || str_contains($productType, 'حجاب')) {
            // ألوان ناعمة للوشاحات
            return ['#d4a5a5', '#c8a2c8', '#a2b5c8', '#a2c8b5', '#f5e6d3'];
        } else {
            // ألوان افتراضية تراثية
            return ['#8B4513', '#DAA520', '#DC143C', '#4B0082', '#2F4F4F'];
        }
    }
}