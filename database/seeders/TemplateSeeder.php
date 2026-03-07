<?php

namespace Database\Seeders;

use App\Models\DesignTemplate;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class TemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the admin user to assign as creator
        $adminUser = User::where('role', 'admin')->first();
        if (!$adminUser) {
            $adminUser = User::first(); // fallback to first user
        }

        // Define the template directory path
        $templateDir = storage_path('app/public/template');
        
        // Check if directory exists
        if (!File::exists($templateDir)) {
            $this->command->error('Template directory does not exist: ' . $templateDir);
            return;
        }
        
        // Get all files from the template directory
        $files = File::files($templateDir);
        
        $templatesToInsert = [];
        
        foreach ($files as $file) {
            $fileName = $file->getFilename();
            
            // Only process image files
            $extension = strtolower($file->getExtension());
            if (!in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'svg'])) {
                continue;
            }
            
            // Generate a name from the filename (remove timestamp and extension)
            $name = $this->generateTemplateName($fileName);
            
            // Create proper URLs for the files (they will be accessible via /storage/)
            $previewUrl = '/storage/template/' . $fileName;
            $thumbnailUrl = '/storage/template/thumbnails/' . $fileName;
            
            // Create the template record
            $templatesToInsert[] = [
                'name' => $name,
                'description' => 'Template imported from storage directory',
                'category' => $this->getCategoryFromName($name),
                'thumbnail_url' => $thumbnailUrl,
                'preview_url' => $previewUrl,
                'design_data' => json_encode([]), // Empty initially
                'is_premium' => false,
                'price' => 0.00,
                'usage_count' => 0,
                'created_by' => $adminUser->id ?? null,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        
        // Insert templates in chunks to avoid memory issues
        $chunks = array_chunk($templatesToInsert, 100);
        
        foreach ($chunks as $chunk) {
            DesignTemplate::insert($chunk);
        }
        
        $this->command->info(count($templatesToInsert) . ' templates seeded successfully.');
    }
    
    /**
     * Generate a template name from the filename
     */
    private function generateTemplateName(string $fileName): string
    {
        // Remove extension
        $name = pathinfo($fileName, PATHINFO_FILENAME);
        
        // Replace underscores and Arabic characters with spaces
        $name = preg_replace('/[^\w\s]/u', ' ', $name);
        
        // Clean up multiple spaces
        $name = trim(preg_replace('/\s+/', ' ', $name));
        
        // Capitalize first letter of each word
        $name = ucwords($name);
        
        // If name is empty or just numbers, generate a generic name
        if (empty($name) || ctype_digit(str_replace(' ', '', $name))) {
            $name = 'Template ' . Str::random(6);
        }
        
        return $name;
    }
    
    /**
     * Determine category based on template name
     */
    private function getCategoryFromName(string $name): string
    {
        $nameLower = strtolower($name);
        
        if (str_contains($nameLower, 'tshirt') || str_contains($nameLower, 'shirt') || str_contains($nameLower, 'tee')) {
            return 't-shirt';
        } elseif (str_contains($nameLower, 'hoodie') || str_contains($nameLower, 'sweat')) {
            return 'hoodie';
        } elseif (str_contains($nameLower, 'mug') || str_contains($nameLower, 'cup')) {
            return 'mug';
        } elseif (str_contains($nameLower, 'poster') || str_contains($nameLower, 'wall')) {
            return 'poster';
        } elseif (str_contains($nameLower, 'bag') || str_contains($nameLower, 'backpack')) {
            return 'bag';
        } elseif (str_contains($nameLower, 'hat') || str_contains($nameLower, 'cap')) {
            return 'hat';
        } else {
            return 't-shirt'; // Default category
        }
    }
}