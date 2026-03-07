<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Font extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'display_name',
        'font_file_url',
        'preview_url',
        'license_type',
        'is_active',
        'category',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public function getPreviewTextAttribute()
    {
        return "The quick brown fox jumps over the lazy dog";
    }
}
