<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class UserAsset extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'session_id',
        'original_filename',
        'stored_filename',
        'file_path',
        'file_url',
        'thumbnail_url',
        'file_type',
        'file_size',
        'mime_type',
        'width',
        'height',
        'storage_disk',
    ];

    protected $casts = [
        'file_size' => 'integer',
        'width' => 'integer',
        'height' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the file URL
     */
    public function getUrlAttribute()
    {
        if ($this->storage_disk === 's3') {
            return Storage::disk('s3')->url($this->file_path);
        }
        
        return Storage::disk('public')->url($this->file_path);
    }

    /**
     * Get thumbnail or generate one
     */
    public function getThumbnailUrlAttribute()
    {
        if ($this->attributes['thumbnail_url']) {
            return $this->attributes['thumbnail_url'];
        }
        
        // Auto-generate thumbnail for images
        if (str_starts_with($this->mime_type, 'image/')) {
            return $this->file_url . '?thumb=1';
        }
        
        return null;
    }

    /**
     * Check if asset is an image
     */
    public function isImage()
    {
        return str_starts_with($this->mime_type, 'image/');
    }

    /**
     * Check if asset is SVG
     */
    public function isSvg()
    {
        return $this->mime_type === 'image/svg+xml' || 
               str_ends_with(strtolower($this->original_filename), '.svg');
    }

    /**
     * Delete the file from storage
     */
    public function deleteFile()
    {
        try {
            Storage::disk($this->storage_disk)->delete($this->file_path);
            
            // Delete thumbnail if exists
            if ($this->thumbnail_url) {
                $thumbnailPath = parse_url($this->thumbnail_url, PHP_URL_PATH);
                if ($thumbnailPath) {
                    Storage::disk($this->storage_disk)->delete(ltrim($thumbnailPath, '/'));
                }
            }
        } catch (\Exception $e) {
            \Log::error('Failed to delete asset file: ' . $e->getMessage());
        }
    }

    /**
     * Protected from deletion if used in designs
     */
    public function isInUse()
    {
        // Check if this asset is used in any saved designs
        $designs = SavedDesign::where('design_data->objects', 'like', "%{$this->file_url}%")->count();
        return $designs > 0;
    }

    /**
     * Scope by session for guest users
     */
    public function scopeBySession($query, $sessionId)
    {
        return $query->where('session_id', $sessionId);
    }

    /**
     * Scope by file type
     */
    public function scopeByType($query, $type)
    {
        return $query->where('file_type', $type);
    }

    /**
     * Scope for images only
     */
    public function scopeImages($query)
    {
        return $query->where('file_type', 'image');
    }

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($asset) {
            $asset->deleteFile();
        });
    }
}
