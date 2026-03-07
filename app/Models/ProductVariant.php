<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'variant_type',
        'variant_value',
        'price_modifier',
        'inventory_count',
        'sku',
    ];

    protected $casts = [
        'price_modifier' => 'decimal:2',
        'inventory_count' => 'integer',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function getFullSkuAttribute()
    {
        return $this->sku ?? "{$this->product->sku}-{$this->variant_type}-{$this->variant_value}";
    }

    public function getPriceWithModifierAttribute()
    {
        return $this->product->price + $this->price_modifier;
    }

    public function scopeByType($query, $type)
    {
        return $query->where('variant_type', $type);
    }

    public function scopeAvailable($query)
    {
        return $query->where('inventory_count', '>', 0);
    }
}
