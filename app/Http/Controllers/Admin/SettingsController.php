<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Services\ImageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class SettingsController extends Controller
{
    protected $imageService;

    public function __construct(ImageService $imageService)
    {
        $this->imageService = $imageService;
    }

    /**
     * Show the settings page
     */
    public function index()
    {
        $settings = Setting::getSettings();
        return inertia('Admin/Settings/Index', compact('settings'));
    }

    /**
     * Update the settings
     */
    public function update(Request $request)
    {
        $request->validate([
            'site_name' => 'required|string|max:255',
            'site_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'site_favicon' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,ico|max:1024',
            'site_description' => 'nullable|string',
            'site_email' => 'nullable|email',
            'site_phone' => 'nullable|string|max:20',
            'site_address' => 'nullable|string',
            'site_currency' => 'required|string|size:3',
            'tax_rate' => 'required|numeric|min:0|max:100',
            'maintenance_mode' => 'boolean',
            'maintenance_message' => 'nullable|string',
            'facebook_url' => 'nullable|url',
            'twitter_url' => 'nullable|url',
            'instagram_url' => 'nullable|url',
            'linkedin_url' => 'nullable|url',
            'meta_keywords' => 'nullable|string',
            'meta_description' => 'nullable|string',
            'analytics_id' => 'nullable|string',
            'enable_registration' => 'boolean',
            'enable_reviews' => 'boolean',
            'products_per_page' => 'required|integer|min:1|max:100',
            // Brand Colors Validation
            'brand_primary_color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'brand_secondary_color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'brand_accent_color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'brand_bg_color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'brand_text_color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'brand_tagline' => 'nullable|string|max:255',
            'brand_logo_woman' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'brand_script_font' => 'nullable|string|max:255',
            'brand_regular_font' => 'nullable|string|max:255',
        ]);

        $settings = Setting::first() ?? new Setting();
        
        // Handle logo upload using ImageService
        if ($request->hasFile('site_logo')) {
            try {
                $logoPath = $this->imageService->uploadLogo(
                    $request->file('site_logo'), 
                    $settings->site_logo
                );
                $settings->site_logo = $logoPath;
            } catch (\Exception $e) {
                return redirect()->back()->with('error', 'Failed to upload logo: ' . $e->getMessage());
            }
        }

        // Handle favicon upload using ImageService
        if ($request->hasFile('site_favicon')) {
            try {
                $faviconPath = $this->imageService->uploadFavicon(
                    $request->file('site_favicon'), 
                    $settings->site_favicon
                );
                $settings->site_favicon = $faviconPath;
            } catch (\Exception $e) {
                return redirect()->back()->with('error', 'Failed to upload favicon: ' . $e->getMessage());
            }
        }

        // Handle brand logo woman upload using ImageService
        if ($request->hasFile('brand_logo_woman')) {
            try {
                $brandLogoPath = $this->imageService->uploadLogo(
                    $request->file('brand_logo_woman'), 
                    $settings->brand_logo_woman
                );
                $settings->brand_logo_woman = $brandLogoPath;
            } catch (\Exception $e) {
                return redirect()->back()->with('error', 'Failed to upload brand logo: ' . $e->getMessage());
            }
        }

        // Update other settings
        $settings->fill($request->except(['site_logo', 'site_favicon', 'brand_logo_woman']));
        $settings->save();

        return redirect()->back()->with('success', 'Settings updated successfully!');
    }
}