<?php

namespace App\Services\Image;

use Illuminate\Support\Facades\Storage;
use Intervention\Image\Laravel\Facades\Image as InterventionImage;
use Exception;

class ImageService
{
    public function __construct()
    {
        // Constructor no longer needs to create the manager, 
        // it will be provided by the Laravel facade
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
        
        // Get image dimensions - attempt to get from file if Intervention Image is available
        try {
            $image = InterventionImage::read(Storage::disk($options['disk'] ?? 'public')->path($storedPath));
            $dimensions = [
                'width' => $image->width(),
                'height' => $image->height(),
            ];
        } catch (Exception $e) {
            // Fallback to getimagesize if Intervention Image fails
            $filePath = Storage::disk($options['disk'] ?? 'public')->path($storedPath);
            if (file_exists($filePath)) {
                $size = getimagesize($filePath);
                $dimensions = [
                    'width' => $size[0] ?? null,
                    'height' => $size[1] ?? null,
                ];
            } else {
                $dimensions = [
                    'width' => null,
                    'height' => null,
                ];
            }
        }
        
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
        try {
            $image = InterventionImage::read(Storage::disk('public')->path($imagePath));
            
            // Resize with aspect ratio
            $image->cover($size, $size, 'center');
            
            // Generate thumbnail filename
            $pathInfo = pathinfo($imagePath);
            $thumbnailPath = dirname($imagePath) . '/' . $pathInfo['filename'] . '_thumb.' . $pathInfo['extension'];
            
            // Save thumbnail
            $image->save(Storage::disk('public')->path($thumbnailPath));
            
            return $thumbnailPath;
        } catch (Exception $e) {
            // If thumbnail generation fails, return null
            return null;
        }
    }

    /**
     * Resize an image to specific dimensions
     */
    public function resize($imagePath, $width, $height, $options = [])
    {
        try {
            $image = InterventionImage::read(Storage::disk('public')->path($imagePath));
            
            if ($options['crop'] ?? false) {
                $image->cover($width, $height);
            } else {
                $image->scale()->down($width, $height);
            }
            
            // Save resized image
            $image->save(Storage::disk('public')->path($imagePath));
            
            return true;
        } catch (Exception $e) {
            // If resize fails, return false
            return false;
        }
    }

    /**
     * Convert image to different format
     */
    public function convert($imagePath, $format = 'webp', $quality = 85)
    {
        try {
            $image = InterventionImage::read(Storage::disk('public')->path($imagePath));
            
            $pathInfo = pathinfo($imagePath);
            $newPath = dirname($imagePath) . '/' . $pathInfo['filename'] . '.' . $format;
            
            $image->toFormat($format)->save(Storage::disk('public')->path($newPath), ['quality' => $quality]);
            
            return $newPath;
        } catch (Exception $e) {
            // If conversion fails, return original path
            return $imagePath;
        }
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
        try {
            $image = InterventionImage::read(Storage::disk('public')->path($imagePath));
            
            $image->place(
                $watermarkText,
                anchor: $position,
                offset: 10
            );
            
            $image->save(Storage::disk('public')->path($imagePath));
            
            return true;
        } catch (Exception $e) {
            // If watermarking fails, return false
            return false;
        }
    }

    /**
     * Get image information
     */
    public function getInfo($imagePath)
    {
        try {
            $image = InterventionImage::read(Storage::disk('public')->path($imagePath));
            
            return [
                'width' => $image->width(),
                'height' => $image->height(),
                'mime_type' => $image->origin()->mediaType(),
                'format' => $image->origin()->fileExtension(),
            ];
        } catch (Exception $e) {
            // Fallback to getimagesize if Intervention Image fails
            $filePath = Storage::disk('public')->path($imagePath);
            if (file_exists($filePath)) {
                $size = getimagesize($filePath);
                return [
                    'width' => $size[0] ?? null,
                    'height' => $size[1] ?? null,
                    'mime_type' => $size['mime_type'] ?? mime_content_type($filePath),
                    'format' => pathinfo($filePath, PATHINFO_EXTENSION) ?? null,
                ];
            } else {
                return [
                    'width' => null,
                    'height' => null,
                    'mime_type' => null,
                    'format' => null,
                ];
            }
        }
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
        try {
            $image = InterventionImage::create()->fill($color);
            
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
        } catch (Exception $e) {
            // If placeholder creation fails, return basic info
            $filename = 'placeholder_' . $width . 'x' . $height . '.jpg';
            $path = 'placeholders/' . $filename;
            
            // Create a simple file as fallback
            $filePath = Storage::disk('public')->path($path);
            if (!file_exists(dirname($filePath))) {
                mkdir(dirname($filePath), 0755, true);
            }
            file_put_contents($filePath, '');
            
            return [
                'path' => $path,
                'url' => Storage::disk('public')->url($path),
            ];
        }
    }
}