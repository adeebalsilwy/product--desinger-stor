<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DesignTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'product_type_id',
        'category',
        'tags',
        'thumbnail_url',
        'preview_url',
        'design_data',
        'is_premium',
        'price',
        'usage_count',
        'created_by',
    ];

    protected $casts = [
        'tags' => 'array',
        'design_data' => 'array',
        'is_premium' => 'boolean',
        'price' => 'decimal:2',
        'usage_count' => 'integer',
    ];

    public function productType()
    {
        return $this->belongsTo(ProductType::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public function scopePremium($query)
    {
        return $query->where('is_premium', true);
    }

    public function scopeFree($query)
    {
        return $query->where('is_premium', false);
    }

    public function scopePopular($query)
    {
        return $query->orderByDesc('usage_count');
    }

    /**
     * Use this template to create a new design
     */
    public function useByUser($user = null)
    {
        $this->increment('usage_count');
        
        return SavedDesign::create([
            'user_id' => $user?->id,
            'product_type_id' => $this->product_type_id,
            'name' => $this->name . ' (Customized)',
            'design_data' => $this->design_data,
        ]);
    }

    /**
     * Add tags to template
     */
    public function addTags(array $tags)
    {
        $currentTags = $this->tags ?? [];
        $this->tags = array_unique(array_merge($currentTags, $tags));
        $this->save();
    }
}
