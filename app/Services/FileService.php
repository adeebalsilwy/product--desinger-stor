<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Http\UploadedFile;
use Intervention\Image\Laravel\Facades\Image;

class FileService
{
    /**
     * Upload a file to the specified directory
     *
     * @param UploadedFile $file
     * @param string $directory
     * @param string|null $filename
     * @param array $options
     * @return array
     */
    public function uploadFile(UploadedFile $file, string $directory, string $filename = null, array $options = []): array
    {
        // Generate filename if not provided
        if (!$filename) {
            $extension = $file->getClientOriginalExtension();
            $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $timestamp = time();
            $filename = $timestamp . '_' . Str::slug($originalName) . '.' . $extension;
        }

        // Store the file
        $path = $file->storeAs($directory, $filename, 'public');

        $result = [
            'original_name' => $file->getClientOriginalName(),
            'stored_filename' => $filename,
            'file_path' => $path,
            'file_url' => Storage::url($path),
            'file_type' => $file->getClientMimeType(),
            'file_size' => $file->getSize(),
            'extension' => $file->getClientOriginalExtension(),
        ];

        // Extract dimensions if it's an image
        if (in_array($result['extension'], ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'svg'])) {
            $result = array_merge($result, $this->getImageDimensions($file));
        }

        return $result;
    }

    /**
     * Create a thumbnail for an image
     *
     * @param string $imagePath
     * @param int $width
     * @param int $height
     * @param string|null $thumbnailDirectory
     * @return string|null
     */
    public function createThumbnail(string $imagePath, int $width = 150, int $height = 150, string $thumbnailDirectory = null): ?string
    {
        if (!Storage::disk('public')->exists($imagePath)) {
            return null;
        }

        // Get the original file
        $imageData = Storage::disk('public')->get($imagePath);
        
        // Create temporary file to process with Intervention Image
        $tempFilePath = sys_get_temp_dir() . '/' . uniqid() . '.tmp';
        file_put_contents($tempFilePath, $imageData);
        
        try {
            $image = Image::read($tempFilePath);
            
            // Resize the image
            $image->cover($width, $height);

            // Determine thumbnail path
            $thumbnailDir = $thumbnailDirectory ?: 'thumbnails';
            $originalFilename = basename($imagePath);
            $thumbnailFilename = 'thumb_' . $originalFilename;
            $thumbnailPath = $thumbnailDir . '/' . $thumbnailFilename;

            // Ensure the thumbnail directory exists
            $thumbnailDirPath = 'public/' . dirname($thumbnailPath);
            if (!Storage::disk('public')->exists(dirname($thumbnailPath))) {
                Storage::disk('public')->makeDirectory(dirname($thumbnailPath));
            }

            // Save the thumbnail to storage
            Storage::disk('public')->put($thumbnailPath, $image->encode(), 'public');

            // Clean up temp file
            unlink($tempFilePath);
            
            return Storage::url($thumbnailPath);
        } catch (\Exception $e) {
            // Clean up temp file in case of error
            if (file_exists($tempFilePath)) {
                unlink($tempFilePath);
            }
            
            // Log the error or handle as appropriate
            \Log::error('Thumbnail creation failed: ' . $e->getMessage());
            
            // Return original image URL as fallback
            return Storage::url($imagePath);
        }
    }

    /**
     * Organize files in subdirectories based on date
     *
     * @param string $baseDirectory
     * @return string
     */
    public function getOrganizedDirectory(string $baseDirectory): string
    {
        $datePath = date('Y/m/d');
        return $baseDirectory . '/' . $datePath;
    }

    /**
     * Validate file before upload
     *
     * @param UploadedFile $file
     * @param array $allowedTypes
     * @param int $maxSize
     * @return bool
     */
    public function validateFile(UploadedFile $file, array $allowedTypes = [], int $maxSize = 10240): bool
    {
        // Check file size (in KB)
        if ($file->getSize() > ($maxSize * 1024)) {
            return false;
        }

        // Check file type
        if (!empty($allowedTypes)) {
            $mimeType = $file->getMimeType();
            $extension = $file->getClientOriginalExtension();

            $isValidType = false;
            foreach ($allowedTypes as $allowedType) {
                if ($mimeType === $allowedType || $extension === $allowedType) {
                    $isValidType = true;
                    break;
                }
            }

            if (!$isValidType) {
                return false;
            }
        }

        return true;
    }

    /**
     * Get image dimensions
     *
     * @param UploadedFile $file
     * @return array
     */
    private function getImageDimensions(UploadedFile $file): array
    {
        try {
            $image = Image::read($file);
            return [
                'width' => $image->width(),
                'height' => $image->height(),
            ];
        } catch (\Exception $e) {
            return [
                'width' => null,
                'height' => null,
            ];
        }
    }

    /**
     * Delete a file
     *
     * @param string $filePath
     * @return bool
     */
    public function deleteFile(string $filePath): bool
    {
        return Storage::disk('public')->delete($filePath);
    }

    /**
     * Copy a file to another location
     *
     * @param string $sourcePath
     * @param string $destinationPath
     * @return bool
     */
    public function copyFile(string $sourcePath, string $destinationPath): bool
    {
        if (!Storage::disk('public')->exists($sourcePath)) {
            return false;
        }

        $fileContent = Storage::disk('public')->get($sourcePath);
        return Storage::disk('public')->put($destinationPath, $fileContent, 'public');
    }
}