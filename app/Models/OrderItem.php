<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'product_id',
        'product_type_id',
        'saved_design_id',
        'quantity',
        'unit_price',
        'total_price',
        'customization_data',
        'print_file_url',
        'status',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'decimal:2',
        'total_price' => 'decimal:2',
        'customization_data' => 'array',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function productType()
    {
        return $this->belongsTo(ProductType::class);
    }

    public function savedDesign()
    {
        return $this->belongsTo(SavedDesign::class);
    }

    /**
     * Get customization details (size, color, print area, etc.)
     */
    public function getCustomizationsAttribute()
    {
        return $this->customization_data ?? [];
    }

    /**
     * Check if item has a custom design
     */
    public function hasCustomDesign()
    {
        return $this->saved_design_id !== null;
    }

    /**
     * Get design data if available
     */
    public function getDesignDataAttribute()
    {
        return $this->savedDesign?->design_data;
    }

    /**
     * Update item status
     */
    public function updateStatus($newStatus)
    {
        $oldStatus = $this->status;
        $this->status = $newStatus;
        $this->save();
        
        // Log the status change
        $this->order->histories()->create([
            'status_from' => $oldStatus,
            'status_to' => $newStatus,
            'notes' => "Item status updated from {$oldStatus} to {$newStatus}",
        ]);
    }

    /**
     * Generate print file for this item
     */
    public function generatePrintFile()
    {
        if (!$this->hasCustomDesign()) {
            return null;
        }
        
        $exportService = app(\App\Services\Design\ExportService::class);
        $printFileUrl = $exportService->generatePrintFile($this->savedDesign);
        
        $this->update(['print_file_url' => $printFileUrl]);
        
        return $printFileUrl;
    }

    /**
     * Scope by order
     */
    public function scopeByOrder($query, $orderId)
    {
        return $query->where('order_id', $orderId);
    }

    /**
     * Scope by status
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }
}
