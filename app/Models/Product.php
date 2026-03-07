<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_type_id',
        'name',
        'slug',
        'description',
        'sku',
        'price',
        'sale_price',
        'inventory_count',
        'is_active',
        'thumbnail_url',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'sale_price' => 'decimal:2',
        'is_active' => 'boolean',
        'inventory_count' => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($product) {
            if (empty($product->slug)) {
                $product->slug = Str::slug($product->name);
            }
            
            if (empty($product->sku)) {
                $product->sku = 'PROD-' . strtoupper(Str::random(8));
            }
        });
    }

    public function productType()
    {
        return $this->belongsTo(ProductType::class);
    }

    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function storeProducts()
    {
        return $this->belongsToMany(Store::class, 'store_products')
            ->withPivot(['is_featured', 'sort_order'])
            ->withTimestamps();
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeAvailable($query)
    {
        return $query->where('is_active', true)
            ->where('inventory_count', '>', 0);
    }

    public function getFinalPriceAttribute()
    {
        return $this->sale_price ?? $this->price;
    }

    public function hasDiscount()
    {
        return $this->sale_price !== null && $this->sale_price < $this->price;
    }

    public function getDiscountPercentageAttribute()
    {
        if (!$this->hasDiscount()) {
            return 0;
        }
        
        return round((($this->price - $this->sale_price) / $this->price) * 100, 2);
    }
}
