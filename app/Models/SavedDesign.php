<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class SavedDesign extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'session_id',
        'product_type_id',
        'product_id',
        'template_id',
        'name',
        'design_data',
        'thumbnail_url',
        'preview_url',
        'print_files',
        'is_public',
        'is_template',
        'elements',
    ];

    protected $casts = [
        'design_data' => 'array',
        'print_files' => 'array',
        'is_public' => 'boolean',
        'is_template' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function productType()
    {
        return $this->belongsTo(ProductType::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function template()
    {
        return $this->belongsTo(DesignTemplate::class, 'template_id');
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Get the design canvas data
     */
    public function getCanvasDataAttribute()
    {
        return json_decode($this->design_data, true);
    }

    /**
     * Get the elements array
     */
    public function getElementsAttribute()
    {
        $data = json_decode($this->design_data, true);
        return $data['elements'] ?? [];
    }

    /**
     * Update specific layer in design
     */
    public function updateLayer($layerId, array $properties)
    {
        $data = $this->design_data;
        
        if (isset($data['objects'])) {
            foreach ($data['objects'] as &$object) {
                if ($object['id'] === $layerId) {
                    $object = array_merge($object, $properties);
                    break;
                }
            }
        }
        
        $this->design_data = json_encode($data);
        $this->save();
    }

    /**
     * Generate and save thumbnail from design
     */
    public function generateThumbnail()
    {
        // This would be handled by a job in production
        // For now, return placeholder
        return '/storage/designs/thumbnails/' . $this->id . '.jpg';
    }

    /**
     * Export design as high-resolution image
     */
    public function exportHighRes()
    {
        // Delegate to service class
        $exportService = app(\App\Services\Design\ExportService::class);
        return $exportService->exportHighRes($this);
    }

    /**
     * Duplicate this design
     */
    public function duplicate($newName = null)
    {
        return static::create([
            'user_id' => $this->user_id,
            'product_type_id' => $this->product_type_id,
            'name' => $newName ?? $this->name . ' (Copy)',
            'design_data' => $this->design_data,
            'is_public' => false,
        ]);
    }

    /**
     * Scope for user's designs
     */
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope for guest designs by session
     */
    public function scopeBySession($query, $sessionId)
    {
        return $query->where('session_id', $sessionId);
    }

    /**
     * Scope for public designs
     */
    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }
}
