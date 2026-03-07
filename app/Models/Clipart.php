<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Clipart extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'clipart_category_id',
        'image_url',
        'thumbnail_url',
        'svg_content',
        'tags',
        'is_premium',
        'price',
        'download_count',
    ];

    protected $casts = [
        'tags' => 'array',
        'is_premium' => 'boolean',
        'price' => 'decimal:2',
        'download_count' => 'integer',
    ];

    public function category()
    {
        return $this->belongsTo(ClipartCategory::class, 'clipart_category_id');
    }

    public function scopePremium($query)
    {
        return $query->where('is_premium', true);
    }

    public function scopeFree($query)
    {
        return $query->where('is_premium', false);
    }

    /**
     * Increment download count
     */
    public function incrementDownloadCount()
    {
        $this->increment('download_count');
    }
}
