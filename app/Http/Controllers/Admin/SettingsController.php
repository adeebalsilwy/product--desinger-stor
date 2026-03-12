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
        // Clear any previous log encoding issues
        if (file_exists(storage_path('logs/laravel.log'))) {
            $logContent = file_get_contents(storage_path('logs/laravel.log'));
            if (preg_match('/[\x{4e00}-\x{9fff}]/u', $logContent)) {
                // Delete corrupted log file
                unlink(storage_path('logs/laravel.log'));
            }
        }
        
        \Log::info('=== Settings Update Started ===');
        \Log::info('Request method: ' . $request->method());
        \Log::info('Is PUT request: ' . ($request->isMethod('put') ? 'Yes' : 'No'));
        \Log::info('Is POST request: ' . ($request->isMethod('post') ? 'Yes' : 'No'));
        \Log::info('Request content type: ' . $request->header('Content-Type'));
        
        // Check for _method spoofing (Inertia uses POST with _method=PUT for FormData)
        $actualMethod = $request->input('_method', $request->method());
        \Log::info('_method field: ' . ($request->input('_method') ?? 'NOT SET'));
        \Log::info('Actual HTTP method: ' . $request->server->get('REQUEST_METHOD'));
        \Log::info('Effective method after spoofing: ' . $actualMethod);
        
        // Force Laravel to recognize method spoofing
        if ($request->isMethod('post') && strtoupper($actualMethod) === 'PUT') {
            \Log::info('Detected Inertia PUT request with method spoofing');
        }
        
        // Get all input data
        $allInput = $request->all();
        \Log::info('Request data keys: ' . implode(', ', array_keys($allInput)));
        \Log::info('Has files: ' . ($request->files->count() > 0 ? 'Yes' : 'No'));
        \Log::info('All input data count: ' . count($allInput));
        
        // Log all input data in English
        foreach ($allInput as $key => $value) {
            if (is_null($value)) {
                \Log::debug("Input - Key: {$key} | Value: NULL");
            } elseif (is_array($value)) {
                \Log::debug("Input - Key: {$key} | Type: ARRAY (" . count($value) . " items)");
            } else {
                $valueStr = is_string($value) ? substr($value, 0, 100) : (string)$value;
                \Log::debug("Input - Key: {$key} | Value: " . $valueStr);
            }
        }
        
        // Ensure we have the required fields
        $siteName = $request->input('site_name');
        $productsPerPage = $request->input('products_per_page');
        
        \Log::info('site_name from request: ' . ($siteName ?? 'NULL'));
        \Log::info('products_per_page from request: ' . ($productsPerPage ?? 'NULL'));
        
        // If fields are missing, try to get them from the request differently
        if (empty($siteName) || empty($productsPerPage)) {
            \Log::warning('Required fields missing, attempting to retrieve from raw input');
            $rawContent = $request->getContent();
            \Log::debug('Raw request content: ' . substr($rawContent, 0, 500));
        }
        
        // Check for _method spoofing (Inertia uses POST with _method=PUT)
        \Log::info('_method field: ' . ($request->input('_method') ?? 'NOT SET'));
        \Log::info('Actual HTTP method: ' . $request->server->get('REQUEST_METHOD'));
        
        // Force Laravel to parse the request body for PUT/POST with files
        if ($request->isMethod('post') && $request->input('_method') === 'put') {
            \Log::info('Detected Inertia PUT request with method spoofing');
        }
        
        // Clean up empty objects from file uploads (frontend might send empty objects)
        $allInput = $request->all();
        foreach (['site_logo', 'site_favicon', 'brand_logo_woman'] as $fileField) {
            if (isset($allInput[$fileField]) && is_array($allInput[$fileField]) && empty($allInput[$fileField])) {
                \Log::debug("Cleaning up empty object for field: {$fileField}");
                $request->merge([$fileField => null]);
            }
        }
        
        // Check for _method spoofing (Inertia uses POST with _method=PUT)
        \Log::info('_method field: ' . ($request->input('_method') ?? 'NOT SET'));
        \Log::info('Actual HTTP method: ' . $request->server->get('REQUEST_METHOD'));
        
        // Log file information
        if ($request->hasFile('site_logo')) {
            $file = $request->file('site_logo');
            \Log::info('Site logo file detected - Name: ' . $file->getClientOriginalName() . ', Size: ' . $file->getSize() . ' bytes');
        } else {
            \Log::debug('No site logo file uploaded');
        }
        
        if ($request->hasFile('brand_logo_woman')) {
            $file = $request->file('brand_logo_woman');
            \Log::info('Brand logo file detected - Name: ' . $file->getClientOriginalName() . ', Size: ' . $file->getSize() . ' bytes');
        } else {
            \Log::debug('No brand logo file uploaded');
        }
        
        if ($request->hasFile('site_favicon')) {
            $file = $request->file('site_favicon');
            \Log::info('Favicon file detected - Name: ' . $file->getClientOriginalName() . ', Size: ' . $file->getSize() . ' bytes');
        } else {
            \Log::debug('No favicon file uploaded');
        }
        
        $validated = $request->validate([
            'site_name' => 'required|string|max:255',
            'site_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'site_favicon' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,ico|max:1024',
            'site_description' => 'nullable|string',
            'site_email' => 'nullable|email',
            'site_phone' => 'nullable|string|max:20',
            'site_address' => 'nullable|string',
            'site_currency' => 'nullable|string|size:3',
            'tax_rate' => 'nullable|numeric|min:0|max:100',
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
        ], [
            // Custom error messages in English to ensure consistency
            'site_name.required' => 'The site name field is required.',
            'products_per_page.required' => 'The products per page field is required.',
        ]);
        
        \Log::info('Validation rules applied successfully');
        \Log::info('Validated data keys: ' . implode(', ', array_keys($validated)));

        $settings = Setting::first() ?? new Setting();
        
        \Log::info('Settings record: ' . ($settings->exists ? 'Existing' : 'New'));
        \Log::info('Current site_name before update: ' . ($settings->site_name ?? 'null'));
        
        // Handle logo upload using ImageService
        if ($request->hasFile('site_logo')) {
            try {
                \Log::info('Attempting to upload site logo...');
                $logoPath = $this->imageService->uploadLogo(
                    $request->file('site_logo'), 
                    $settings->site_logo
                );
                $settings->site_logo = $logoPath;
                \Log::info('Site logo uploaded successfully: ' . $logoPath);
            } catch (\Exception $e) {
                \Log::error('Failed to upload site logo: ' . $e->getMessage());
                \Log::error('Exception trace: ' . $e->getTraceAsString());
                return redirect()->back()->with('error', 'Failed to upload logo: ' . $e->getMessage());
            }
        }

        // Handle favicon upload using ImageService
        if ($request->hasFile('site_favicon')) {
            try {
                \Log::info('Attempting to upload favicon...');
                $faviconPath = $this->imageService->uploadFavicon(
                    $request->file('site_favicon'), 
                    $settings->site_favicon
                );
                $settings->site_favicon = $faviconPath;
                \Log::info('Favicon uploaded successfully: ' . $faviconPath);
            } catch (\Exception $e) {
                \Log::error('Failed to upload favicon: ' . $e->getMessage());
                \Log::error('Exception trace: ' . $e->getTraceAsString());
                return redirect()->back()->with('error', 'Failed to upload favicon: ' . $e->getMessage());
            }
        }

        // Handle brand logo woman upload using ImageService
        if ($request->hasFile('brand_logo_woman')) {
            try {
                \Log::info('Attempting to upload brand logo...');
                $brandLogoPath = $this->imageService->uploadLogo(
                    $request->file('brand_logo_woman'), 
                    $settings->brand_logo_woman
                );
                $settings->brand_logo_woman = $brandLogoPath;
                \Log::info('Brand logo uploaded successfully: ' . $brandLogoPath);
            } catch (\Exception $e) {
                \Log::error('Failed to upload brand logo: ' . $e->getMessage());
                \Log::error('Exception trace: ' . $e->getTraceAsString());
                return redirect()->back()->with('error', 'Failed to upload brand logo: ' . $e->getMessage());
            }
        }

        // Update other settings
        $settings->fill(
            collect($request->except(['site_logo', 'site_favicon', 'brand_logo_woman']))
                ->reject(function ($value, $key) {
                    return str_ends_with($key, '_preview');
                })
                ->toArray()
        );
        
        \Log::info('Filling settings with data: ' . json_encode($request->except(['site_logo', 'site_favicon', 'brand_logo_woman', '*_preview'])));
        
        try {
            $settings->save();
            \Log::info('Settings saved successfully!');
            \Log::info('New site_name: ' . $settings->site_name);
            \Log::info('New brand_primary_color: ' . $settings->brand_primary_color);
            \Log::info('=== Settings Update Completed Successfully ===');
        } catch (\Exception $e) {
            \Log::error('Failed to save settings: ' . $e->getMessage());
            \Log::error('Exception trace: ' . $e->getTraceAsString());
            return redirect()->back()->with('error', 'Failed to save settings: ' . $e->getMessage());
        }

        return redirect()->back()->with('success', 'Settings updated successfully!');
    }
}