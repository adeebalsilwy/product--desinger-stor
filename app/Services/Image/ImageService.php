<?php

namespace App\Services\Image;

use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver as GdDriver;
use Intervention\Image\Drivers\Imagick\Driver as ImagickDriver;

class ImageService
{
    protected $manager;

    public function __construct()
    {
        // Use GD or Imagick based on availability
        $driver = extension_loaded('imagick') ? new ImagickDriver() : new GdDriver();
        $this->manager = new ImageManager($driver);
    }

    /**
     * Upload and process an image
     */
    public function upload($file, $path, $options = [])
    {
        $filename = $this->generateFilename($file);
        $fullPath = trim($path, '/') . '/' . $filename;
        
        // Save original
        $storedPath = Storage::disk($options['disk'] ?? 'public')->putFileAs($path, $file, $filename);
        
        // Generate thumbnail if requested
        $thumbnailPath = null;
        if ($options['thumbnail'] ?? false) {
            $thumbnailPath = $this->generateThumbnail($storedPath, $options['thumbnail_size'] ?? 200);
        }
        
        // Get image dimensions
        $image = $this->manager->read(Storage::disk($options['disk'] ?? 'public')->path($storedPath));
        $dimensions = [
            'width' => $image->width(),
            'height' => $image->height(),
        ];
        
        return [
            'path' => $storedPath,
            'url' => Storage::disk($options['disk'] ?? 'public')->url($storedPath),
            'thumbnail_path' => $thumbnailPath,
            'thumbnail_url' => $thumbnailPath ? Storage::disk($options['disk'] ?? 'public')->url($thumbnailPath) : null,
            'filename' => $filename,
            'mime_type' => $file->getMimeType(),
            'size' => $file->getSize(),
            'dimensions' => $dimensions,
        ];
    }

    /**
     * Generate a thumbnail from an image
     */
    public function generateThumbnail($imagePath, $size = 200)
    {
        $image = $this->manager->read(Storage::disk('public')->path($imagePath));
        
        // Resize with aspect ratio
        $image->cover($size, $size, 'center');
        
        // Generate thumbnail filename
        $pathInfo = pathinfo($imagePath);
        $thumbnailPath = dirname($imagePath) . '/' . $pathInfo['filename'] . '_thumb.' . $pathInfo['extension'];
        
        // Save thumbnail
        $image->save(Storage::disk('public')->path($thumbnailPath));
        
        return $thumbnailPath;
    }

    /**
     * Resize an image to specific dimensions
     */
    public function resize($imagePath, $width, $height, $options = [])
    {
        $image = $this->manager->read(Storage::disk('public')->path($imagePath));
        
        if ($options['crop'] ?? false) {
            $image->cover($width, $height);
        } else {
            $image->scale()->down($width, $height);
        }
        
        // Save resized image
        $image->save(Storage::disk('public')->path($imagePath));
        
        return true;
    }

    /**
     * Convert image to different format
     */
    public function convert($imagePath, $format = 'webp', $quality = 85)
    {
        $image = $this->manager->read(Storage::disk('public')->path($imagePath));
        
        $pathInfo = pathinfo($imagePath);
        $newPath = dirname($imagePath) . '/' . $pathInfo['filename'] . '.' . $format;
        
        $image->toFormat($format)->save(Storage::disk('public')->path($newPath), ['quality' => $quality]);
        
        return $newPath;
    }

    /**
     * Optimize image for web
     */
    public function optimize($imagePath, $quality = 75)
    {
        return $this->convert($imagePath, 'webp', $quality);
    }

    /**
     * Generate watermark on image
     */
    public function addWatermark($imagePath, $watermarkText, $position = 'bottom-right')
    {
        $image = $this->manager->read(Storage::disk('public')->path($imagePath));
        
        $image->place(
            $watermarkText,
            anchor: $position,
            offset: 10
        );
        
        $image->save(Storage::disk('public')->path($imagePath));
        
        return true;
    }

    /**
     * Get image information
     */
    public function getInfo($imagePath)
    {
        $image = $this->manager->read(Storage::disk('public')->path($imagePath));
        
        return [
            'width' => $image->width(),
            'height' => $image->height(),
            'mime_type' => $image->origin()->mediaType(),
            'format' => $image->origin()->fileExtension(),
        ];
    }

    /**
     * Generate unique filename
     */
    protected function generateFilename($file)
    {
        return uniqid() . '_' . time() . '.' . $file->getClientOriginalExtension();
    }

    /**
     * Create placeholder image
     */
    public function createPlaceholder($width, $height, $text = 'No Image', $color = '#cccccc')
    {
        $image = $this->manager->create()->fill($color);
        
        $image->text($text, $width / 2, $height / 2, function($font) use ($width) {
            $font->size($width / 10);
            $font->color('#ffffff');
            $font->align('center');
            $font->valign('middle');
        });
        
        $filename = 'placeholder_' . $width . 'x' . $height . '.png';
        $path = 'placeholders/' . $filename;
        
        $image->save(Storage::disk('public')->path($path));
        
        return [
            'path' => $path,
            'url' => Storage::disk('public')->url($path),
        ];
    }
}
