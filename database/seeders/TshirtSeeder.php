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
use Illuminate\Support\Facades\File;

class TshirtSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
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

        // إنشاء قوالب التصميم
        $templates = collect();
        foreach ($templateData as $templateInfo) {
            
            // إنشاء مسار الصور من مجلد القوالب
            $templateSlug = Str::slug($templateInfo['name']);
            
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

        // مصفوفة بيانات القمصان بموضة يمنية
        $tshirts = [
            [
                'title' => "قميص يمني أصيل",
                'description' => "قميص يمني بتصميم أصيل يعكس التراث اليمني الغني، مزخرف بتطريزات يمنية تقليدية تعبر عن أصالة المرأة اليمنية.",
            ],
            [
                'title' => "قميص صنعاني فاخر",
                'description' => "قميص فاخر مستوحى من التصميمات الصنعانية التقليدية، يتميز بألوان تراثية وزخارف دقيقة تجسد جمال الفن اليمني.",
            ],
            [
                'title' => "قميص تعزي مطرز",
                'description' => "قميص تعزي أصيل بتطريزات ذهبية ويدوية تعكس مهارة الحرفيين اليمنيين وتراث تعز العريق.",
            ],
            [
                'title' => "قميص لحجي عصري",
                'description' => "قميص يجمع بين أصالة التصميم اللحجي ولمسة عصرية، مناسب للاستخدام اليومي مع الحفاظ على الهوية اليمنية.",
            ],
            [
                'title' => "قميص حضرمي تقليدي",
                'description' => "قميص حضرمي بتصاميم بدوية أصيلة وألوان زاهية تعكس تراث حضرموت الغني والثقافة اليمنية العريقة.",
            ],
            [
                'title' => "قميص عدن الحديث",
                'description' => "قميص عصري يجمع بين تراث عدن البحري والتصميم الحديث، يعبر عن تنوع الثقافة اليمنية وانفتاحها.",
            ],
            [
                'title' => "قميص إب الجبلي",
                'description' => "قميص مستوحى من جمال الطبيعة في إب وتصاميمها الجبلية، بألوان خضراء وزرقاء تعكس جمال اليمن الطبيعية.",
            ],
            [
                'title' => "قميص ذمار التراثي",
                'description' => "قميص يحتفي بتراث ذمار العريق وتصاميمه التاريخية، بتصاميم تحكي قصة الحضارة اليمنية القديمة.",
            ],
            [
                'title' => "قميص صعدة الأصيل",
                'description' => "قميص يعكس أصالة صعدة وتراثها الجبلي الفريد، بتصاميم هندسية مميزة وألوان ترابية دافئة.",
            ],
        ];

        // الحصول على نوع المنتج تي شيرت مع موضوع الأزياء اليمنية
        $tshirtType = ProductType::firstOrCreate([
            'name' => 'ملابس يمنية عصرية',
        ], [
            'slug' => 'yemeni-casual-wear',
            'description' => 'ملابس يمنية عصرية - "أصالة التراث بأناقة عصرية"',
            'base_price' => 79.99,
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
                
                // Get all image files from the template directory
                $templateDir = storage_path('app/public/template');
                
                if (File::exists($templateDir)) {
                    $imageFiles = File::files($templateDir);
                    $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'];
                    
                    foreach ($imageFiles as $file) {
                        $extension = strtolower($file->getExtension());
                        if (in_array($extension, $imageExtensions)) {
                            // Limit to 5 images per product
                            if ($imageOrder > 5) break;
                            
                            $fileName = $file->getFilename();
                            $newFileName = 'image_' . $imageOrder . '_' . $productSlug . '.' . $extension;
                            $newFilePath = $productImagesFolder . '/' . $newFileName;
                            
                            // Copy the image from template to product folder
                            File::copy($file->getPathname(), storage_path('app/public/' . $newFilePath));
                            
                            ShirtImage::create([
                                'tshirt_id' => $tshirt->id,
                                'url' => '/storage/' . $newFilePath,
                                'order' => $imageOrder
                            ]);
                            
                            $imageOrder++;
                        }
                    }
                }
                
                // If no template images were found, create placeholder images
                if ($imageOrder == 1) {
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
                    if ($template->thumbnail_url && $imageOrder <= 5) {
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
                    'sku' => 'YEMEN-TSHIRT-' . strtoupper(Str::random(6)),
                    'price' => 49.99,
                    'sale_price' => 44.99, // Discount price
                    'inventory_count' => rand(50, 100), // Random inventory count
                    'is_active' => true,
                    'design_template_id' => $template->id,
                    'thumbnail_url' => $template->thumbnail_url, // Use template's thumbnail as thumbnail
                    'is_template_based' => true,
                    'template_category' => $template->category,
                    'template_data' => [
                        'original_tshirt_id' => $tshirt->id,
                        'base_design' => 'yemeni-heritage-themed',
                        'colors' => ['#8B4513', '#DAA520', '#DC143C', '#4B0082'], // ألوان تراثية يمنية
                        'sizes_available' => ['XS', 'S', 'M', 'L', 'XL', 'XXL'],
                        'fabric_options' => ['قطن يمني فاخر', 'حرير طبيعي'],
                    ],
                ]);
            }
        }
    }
}