<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrintArea extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_type_id',
        'name',
        'display_name',
        'width_mm',
        'height_mm',
        'offset_x',
        'offset_y',
        'preview_image_url',
        'template_image_url',
        'is_default',
    ];

    protected $casts = [
        'width_mm' => 'decimal:2',
        'height_mm' => 'decimal:2',
        'offset_x' => 'integer',
        'offset_y' => 'integer',
        'is_default' => 'boolean',
    ];

    public function productType()
    {
        return $this->belongsTo(ProductType::class);
    }

    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    public function getDimensionsAttribute()
    {
        return [
            'width' => $this->width_mm,
            'height' => $this->height_mm,
            'offset_x' => $this->offset_x,
            'offset_y' => $this->offset_y,
        ];
    }

    /**
     * Calculate the canvas size needed for this print area
     */
    public function getRequiredCanvasSizeAttribute()
    {
        // Convert mm to pixels (assuming 300 DPI)
        $dpi = 300;
        $mmToInch = 0.0393701;
        
        $widthPx = ceil($this->width_mm * $mmToInch * $dpi);
        $heightPx = ceil($this->height_mm * $mmToInch * $dpi);
        
        return [
            'width_px' => $widthPx,
            'height_px' => $heightPx,
        ];
    }
}
