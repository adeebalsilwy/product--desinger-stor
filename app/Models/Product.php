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
        'design_template_id',
        'template_data',
        'is_template_based',
        'template_category',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'sale_price' => 'decimal:2',
        'is_active' => 'boolean',
        'inventory_count' => 'integer',
        'template_data' => 'array',
        'is_template_based' => 'boolean',
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

    public function designTemplate()
    {
        return $this->belongsTo(DesignTemplate::class);
    }

    public function images()
    {
        return $this->hasMany(ShirtImage::class, 'tshirt_id');
    }

    public function printAreas()
    {
        return $this->hasManyThrough(
            PrintArea::class,
            ProductType::class,
            'id',         // Foreign key on ProductType table...
            'product_type_id', // Foreign key on PrintArea table...
            'product_type_id', // Local key on Product table...
            'id'          // Local key on ProductType table...
        );
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

    public function scopeTemplateBased($query)
    {
        return $query->where('is_template_based', true);
    }

    public function scopeByTemplateCategory($query, $category)
    {
        return $query->where('template_category', $category);
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