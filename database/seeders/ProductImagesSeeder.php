<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ShirtImage;
use App\Models\DesignTemplate;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProductImagesSeeder extends Seeder
{
    /**
     * Run the database seeds to add images to existing products using template images.
     */
    public function run(): void
    {
        // Disable foreign key checks temporarily
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        // Get all products that don't have images yet
        $products = Product::doesntHave('images')->get();
        
        $this->command->info("Processing {$products->count()} products for image addition...");
        
        foreach ($products as $product) {
            // Check if the product already has a dedicated folder
            $productImagesFolder = 'products/' . $product->slug;
            
            // If the product folder exists and has images, use them
            if (Storage::disk('public')->exists($productImagesFolder)) {
                $imageFiles = Storage::disk('public')->files($productImagesFolder);
                
                $imageOrder = 1;
                
                foreach ($imageFiles as $imageFile) {
                    $extension = pathinfo($imageFile, PATHINFO_EXTENSION);
                    
                    // Only add image files
                    if (in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                        // Check if this image already exists for this product
                        $existingImage = ShirtImage::where('tshirt_id', $product->id)
                            ->where('url', '/storage/' . $imageFile)
                            ->first();
                        
                        if (!$existingImage) {
                            ShirtImage::create([
                                'tshirt_id' => $product->id, // Using product id in tshirt_id field for compatibility
                                'url' => '/storage/' . $imageFile,
                                'order' => $imageOrder++
                            ]);
                            
                            $this->command->info("Added image {$imageFile} to product {$product->name}");
                        }
                    }
                }
            }
            
            // If no images were found in the product folder, use the original method
            $imageCount = ShirtImage::where('tshirt_id', $product->id)->count();
            if ($imageCount == 0) {
                // Get all tshirt image directories from storage
                $tshirtDirs = [];
                
                if (Storage::disk('public')->exists('tshirts')) {
                    $tshirtDirs = Storage::disk('public')->directories('tshirts');
                }
                
                if (!empty($tshirtDirs)) {
                    // Pick a random tshirt directory to use for this product
                    $selectedDir = $tshirtDirs[array_rand($tshirtDirs)];
                    
                    // Get all images from the selected directory
                    $imageFiles = Storage::disk('public')->files($selectedDir);
                    
                    // Create a dedicated folder for this product if it doesn't exist
                    $productImagesFolder = 'products/' . $product->slug;
                    if (!Storage::disk('public')->exists($productImagesFolder)) {
                        Storage::disk('public')->makeDirectory($productImagesFolder);
                    }
                    
                    $imageOrder = 1;
                    
                    // Add images from the tshirt directory to the product
                    foreach ($imageFiles as $index => $imageFile) {
                        if ($imageOrder > 5) { // Maximum 5 images per product
                            break;
                        }
                        
                        $extension = pathinfo($imageFile, PATHINFO_EXTENSION);
                        
                        // Only add image files
                        if (in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                            // Copy the image to the product's dedicated folder
                            $newImagePath = $productImagesFolder . '/' . 'additional_' . ($index + 1) . '_' . $product->slug . '.' . $extension;
                            
                            Storage::disk('public')->copy($imageFile, $newImagePath);
                            
                            ShirtImage::create([
                                'tshirt_id' => $product->id, // Using product id in tshirt_id field for compatibility
                                'url' => '/storage/' . $newImagePath,
                                'order' => $imageOrder++
                            ]);
                            
                            $this->command->info("Added image {$newImagePath} to product {$product->name}");
                        }
                    }
                }
                
                // If still no images were found, use template images as fallback
                if (ShirtImage::where('tshirt_id', $product->id)->count() == 0) {
                    // Try to get a template to use for images
                    $template = null;
                    
                    if ($product->design_template_id) {
                        $template = DesignTemplate::find($product->design_template_id);
                    } else {
                        $template = DesignTemplate::first();
                    }
                    
                    if ($template) {
                        // Create a dedicated folder for this product if it doesn't exist
                        $productImagesFolder = 'products/' . $product->slug;
                        if (!Storage::disk('public')->exists($productImagesFolder)) {
                            Storage::disk('public')->makeDirectory($productImagesFolder);
                        }
                        
                        // Copy template images to the product folder and add to database
                        if ($template->preview_url) {
                            $previewFileName = pathinfo(parse_url($template->preview_url, PHP_URL_PATH), PATHINFO_BASENAME);
                            $newPreviewPath = $productImagesFolder . '/' . 'mainImage_' . $product->slug . '.' . pathinfo($previewFileName, PATHINFO_EXTENSION);
                            
                            // Extract the original template file path
                            $originalTemplatePath = str_replace('/storage/', '', parse_url($template->preview_url, PHP_URL_PATH));
                            
                            if (Storage::disk('public')->exists($originalTemplatePath)) {
                                Storage::disk('public')->copy($originalTemplatePath, $newPreviewPath);
                                
                                ShirtImage::create([
                                    'tshirt_id' => $product->id,
                                    'url' => '/storage/' . $newPreviewPath,
                                    'order' => 1
                                ]);
                            }
                        }
                        
                        if ($template->thumbnail_url) {
                            $thumbnailFileName = pathinfo(parse_url($template->thumbnail_url, PHP_URL_PATH), PATHINFO_BASENAME);
                            $newThumbnailPath = $productImagesFolder . '/' . 'thumbnail_' . $product->slug . '.' . pathinfo($thumbnailFileName, PATHINFO_EXTENSION);
                            
                            // Extract the original template file path
                            $originalTemplatePath = str_replace('/storage/', '', parse_url($template->thumbnail_url, PHP_URL_PATH));
                            
                            if (Storage::disk('public')->exists($originalTemplatePath)) {
                                Storage::disk('public')->copy($originalTemplatePath, $newThumbnailPath);
                                
                                ShirtImage::create([
                                    'tshirt_id' => $product->id,
                                    'url' => '/storage/' . $newThumbnailPath,
                                    'order' => 2
                                ]);
                            }
                        }
                        
                        $this->command->info("Added template images to product {$product->name}");
                    }
                }
            }
            
            // Set the first image as the product thumbnail if not already set
            if (!$product->thumbnail_url) {
                $firstImage = $product->images()->orderBy('order')->first();
                if ($firstImage) {
                    $product->update(['thumbnail_url' => $firstImage->url]);
                    $this->command->info("Set thumbnail for product {$product->name}");
                }
            }
        }
        
        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        
        $this->command->info('Product images seeding completed!');
    }
}