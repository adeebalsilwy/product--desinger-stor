<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Laravel\Facades\Image;
use Illuminate\Support\Str;

class ImageService
{
    /**
     * Upload and process site logo
     */
    public function uploadLogo(UploadedFile $file, $previousLogo = null)
    {
        // Delete old logo if exists
        if ($previousLogo) {
            $this->deleteImage($previousLogo);
        }

        // Generate unique filename
        $filename = 'logo-' . time() . '-' . Str::random(10) . '.' . $file->getClientOriginalExtension();
        
        // Process and store image
        $image = Image::read($file->getRealPath());
        
        // Resize logo to appropriate dimensions while maintaining aspect ratio
        $image->scaleDown(300, 100);
        
        // Save to storage
        $path = 'logos/' . $filename;
        Storage::disk('public')->put($path, $image->encode());
        
        return 'storage/' . $path;
    }

    /**
     * Upload and process favicon
     */
    public function uploadFavicon(UploadedFile $file, $previousFavicon = null)
    {
        // Delete old favicon if exists
        if ($previousFavicon) {
            $this->deleteImage($previousFavicon);
        }

        // Generate unique filename
        $filename = 'favicon-' . time() . '-' . Str::random(10) . '.' . $file->getClientOriginalExtension();
        
        // Process and store image
        $image = Image::read($file->getRealPath());
        
        // Resize favicon to 32x32 pixels
        $image->cover(32, 32);
        
        // Save to storage
        $path = 'favicons/' . $filename;
        Storage::disk('public')->put($path, $image->encode());
        
        return 'storage/' . $path;
    }

    /**
     * Upload general image (for other image uploads)
     */
    public function uploadImage(UploadedFile $file, $folder = 'images', $resizeWidth = null, $resizeHeight = null)
    {
        $filename = Str::random(20) . '.' . $file->getClientOriginalExtension();
        $path = $folder . '/' . $filename;
        
        $image = Image::read($file->getRealPath());
        
        // Resize if dimensions are provided
        if ($resizeWidth && $resizeHeight) {
            $image->scaleDown($resizeWidth, $resizeHeight);
        }
        
        Storage::disk('public')->put($path, $image->encode());
        
        return 'storage/' . $path;
    }

    /**
     * Delete an image from storage
     */
    public function deleteImage($imagePath)
    {
        if ($imagePath) {
            $storagePath = str_replace('storage/', '', $imagePath);
            if (Storage::disk('public')->exists($storagePath)) {
                Storage::disk('public')->delete($storagePath);
                return true;
            }
        }
        return false;
    }

    /**
     * Get image URL
     */
    public function getImageUrl($imagePath)
    {
        if (!$imagePath) {
            return null;
        }
        
        // If it's already a full URL, return as is
        if (filter_var($imagePath, FILTER_VALIDATE_URL)) {
            return $imagePath;
        }
        
        // If it starts with storage/, return the URL
        if (Str::startsWith($imagePath, 'storage/')) {
            return asset($imagePath);
        }
        
        // If it's just a path, prepend storage/
        return asset('storage/' . $imagePath);
    }
}