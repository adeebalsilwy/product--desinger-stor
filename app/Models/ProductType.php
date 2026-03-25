<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ProductType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'image_url',
        'base_price',
        'is_active',
    ];

    protected $casts = [
        'base_price' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($productType) {
            if (empty($productType->slug)) {
                $productType->slug = Str::slug($productType->name);
            }
        });
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function printAreas()
    {
        return $this->hasMany(PrintArea::class);
    }

    public function templates()
    {
        return $this->hasMany(DesignTemplate::class);
    }

    public function savedDesigns()
    {
        return $this->hasMany(SavedDesign::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
