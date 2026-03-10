<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Setting extends Model
{
    protected $fillable = [
        'site_name',
        'site_logo',
        'site_favicon',
        'site_description',
        'site_email',
        'site_phone',
        'site_address',
        'site_currency',
        'tax_rate',
        'maintenance_mode',
        'maintenance_message',
        'facebook_url',
        'twitter_url',
        'instagram_url',
        'linkedin_url',
        'meta_keywords',
        'meta_description',
        'analytics_id',
        'enable_registration',
        'enable_reviews',
        'products_per_page',
        // Brand colors and identity
        'brand_primary_color',
        'brand_secondary_color',
        'brand_accent_color',
        'brand_text_color',
        'brand_bg_color',
        'brand_logo_woman',
        'brand_tagline',
        'brand_script_font',
        'brand_regular_font',
    ];

    protected $casts = [
        'tax_rate' => 'decimal:2',
        'maintenance_mode' => 'boolean',
        'enable_registration' => 'boolean',
        'enable_reviews' => 'boolean',
        'products_per_page' => 'integer'
    ];

    /**
     * Get the site settings
     */
    public static function getSettings()
    {
        return self::first() ?? new self();
    }

    /**
     * Get a specific setting value
     */
    public static function getValue($key, $default = null)
    {
        $settings = self::getSettings();
        return $settings->$key ?? $default;
    }

    /**
     * Get the full URL for the site logo
     */
    public function getSiteLogoUrlAttribute()
    {
        if (!$this->site_logo) {
            return null;
        }
        
        // If it's already a full URL, return as is
        if (filter_var($this->site_logo, FILTER_VALIDATE_URL)) {
            return $this->site_logo;
        }
        
        // If it starts with storage/, return the URL
        if (str_starts_with($this->site_logo, 'storage/')) {
            return asset($this->site_logo);
        }
        
        // If it's just a path, prepend storage/
        return asset('storage/' . $this->site_logo);
    }

    /**
     * Get the full URL for the favicon
     */
    public function getSiteFaviconUrlAttribute()
    {
        if (!$this->site_favicon) {
            return null;
        }
        
        // If it's already a full URL, return as is
        if (filter_var($this->site_favicon, FILTER_VALIDATE_URL)) {
            return $this->site_favicon;
        }
        
        // If it starts with storage/, return the URL
        if (str_starts_with($this->site_favicon, 'storage/')) {
            return asset($this->site_favicon);
        }
        
        // If it's just a path, prepend storage/
        return asset('storage/' . $this->site_favicon);
    }

    /**
     * Check if logo exists
     */
    public function hasLogo()
    {
        return !empty($this->site_logo) && Storage::disk('public')->exists(str_replace('storage/', '', $this->site_logo));
    }

    /**
     * Check if favicon exists
     */
    public function hasFavicon()
    {
        return !empty($this->site_favicon) && Storage::disk('public')->exists(str_replace('storage/', '', $this->site_favicon));
    }

    /**
     * Get the full URL for the brand logo woman
     */
    public function getBrandLogoWomanUrlAttribute()
    {
        if (!$this->brand_logo_woman) {
            return null;
        }
        
        // If it's already a full URL, return as is
        if (filter_var($this->brand_logo_woman, FILTER_VALIDATE_URL)) {
            return $this->brand_logo_woman;
        }
        
        // If it starts with storage/, return the URL
        if (str_starts_with($this->brand_logo_woman, 'storage/')) {
            return asset($this->brand_logo_woman);
        }
        
        // If it's just a path, prepend storage/
        return asset('storage/' . $this->brand_logo_woman);
    }

    /**
     * Check if brand logo woman exists
     */
    public function hasBrandLogoWoman()
    {
        return !empty($this->brand_logo_woman) && Storage::disk('public')->exists(str_replace('storage/', '', $this->brand_logo_woman));
    }
}