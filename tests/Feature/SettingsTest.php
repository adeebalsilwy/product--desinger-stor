<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SettingsTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create admin user
        $this->admin = User::factory()->create([
            'email' => 'admin@example.com',
            'role' => 'admin',
        ]);
    }

    /** @test */
    public function admin_can_view_settings_page()
    {
        $response = $this->actingAs($this->admin)->get('/admin/settings');
        
        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Admin/Settings/Index')
            ->has('settings')
        );
    }

    /** @test */
    public function non_admin_cannot_access_settings_page()
    {
        $user = User::factory()->create(['role' => 'customer']);
        
        $response = $this->actingAs($user)->get('/admin/settings');
        
        $response->assertStatus(403);
    }

    /** @test */
    public function admin_can_update_basic_settings()
    {
        $settings = Setting::create([
            'site_name' => 'Original Name',
            'site_email' => 'original@example.com',
            'site_currency' => 'USD',
            'tax_rate' => 10.50,
            'products_per_page' => 12,
        ]);

        $response = $this->actingAs($this->admin)->put('/admin/settings', [
            'site_name' => 'Updated Name',
            'site_email' => 'updated@example.com',
            'site_description' => 'New description',
            'site_currency' => 'EUR',
            'tax_rate' => 15.75,
            'products_per_page' => 24,
            'enable_registration' => true,
            'enable_reviews' => false,
        ]);

        $response->assertStatus(302);
        $response->assertRedirect('/admin/settings');
        
        $this->assertDatabaseHas('settings', [
            'site_name' => 'Updated Name',
            'site_email' => 'updated@example.com',
            'site_description' => 'New description',
            'site_currency' => 'EUR',
            'tax_rate' => 15.75,
            'products_per_page' => 24,
            'enable_registration' => true,
            'enable_reviews' => false,
        ]);
    }

    /** @test */
    public function admin_can_update_brand_colors()
    {
        $settings = Setting::create([
            'site_name' => 'Test Site',
            'site_currency' => 'USD',
            'tax_rate' => 0,
            'products_per_page' => 12,
        ]);

        $response = $this->actingAs($this->admin)->put('/admin/settings', [
            'site_name' => 'Test Site',
            'site_currency' => 'USD',
            'tax_rate' => 0,
            'products_per_page' => 12,
            'brand_primary_color' => '#ff0000',
            'brand_secondary_color' => '#00ff00',
            'brand_accent_color' => '#0000ff',
            'brand_text_color' => '#ffff00',
            'brand_bg_color' => '#ff00ff',
            'brand_tagline' => 'New Tagline',
        ]);

        $response->assertStatus(302);
        
        $this->assertDatabaseHas('settings', [
            'brand_primary_color' => '#ff0000',
            'brand_secondary_color' => '#00ff00',
            'brand_accent_color' => '#0000ff',
            'brand_text_color' => '#ffff00',
            'brand_bg_color' => '#ff00ff',
            'brand_tagline' => 'New Tagline',
        ]);
    }

    /** @test */
    public function admin_can_update_social_media_settings()
    {
        $settings = Setting::create([
            'site_name' => 'Test Site',
            'site_currency' => 'USD',
            'tax_rate' => 0,
            'products_per_page' => 12,
        ]);

        $response = $this->actingAs($this->admin)->put('/admin/settings', [
            'site_name' => 'Test Site',
            'site_currency' => 'USD',
            'tax_rate' => 0,
            'products_per_page' => 12,
            'facebook_url' => 'https://facebook.com/newpage',
            'twitter_url' => 'https://twitter.com/newhandle',
            'instagram_url' => 'https://instagram.com/newprofile',
            'linkedin_url' => 'https://linkedin.com/newcompany',
        ]);

        $response->assertStatus(302);
        
        $this->assertDatabaseHas('settings', [
            'facebook_url' => 'https://facebook.com/newpage',
            'twitter_url' => 'https://twitter.com/newhandle',
            'instagram_url' => 'https://instagram.com/newprofile',
            'linkedin_url' => 'https://linkedin.com/newcompany',
        ]);
    }

    /** @test */
    public function admin_can_update_seo_settings()
    {
        $settings = Setting::create([
            'site_name' => 'Test Site',
            'site_currency' => 'USD',
            'tax_rate' => 0,
            'products_per_page' => 12,
        ]);

        $response = $this->actingAs($this->admin)->put('/admin/settings', [
            'site_name' => 'Test Site',
            'site_currency' => 'USD',
            'tax_rate' => 0,
            'products_per_page' => 12,
            'meta_keywords' => 'test, keywords, seo',
            'meta_description' => 'Test meta description for SEO',
            'analytics_id' => 'GA-123456789',
        ]);

        $response->assertStatus(302);
        
        $this->assertDatabaseHas('settings', [
            'meta_keywords' => 'test, keywords, seo',
            'meta_description' => 'Test meta description for SEO',
            'analytics_id' => 'GA-123456789',
        ]);
    }

    /** @test */
    public function admin_can_update_maintenance_mode()
    {
        $settings = Setting::create([
            'site_name' => 'Test Site',
            'site_currency' => 'USD',
            'tax_rate' => 0,
            'products_per_page' => 12,
        ]);

        $response = $this->actingAs($this->admin)->put('/admin/settings', [
            'site_name' => 'Test Site',
            'site_currency' => 'USD',
            'tax_rate' => 0,
            'products_per_page' => 12,
            'maintenance_mode' => true,
            'maintenance_message' => 'Site is under maintenance',
        ]);

        $response->assertStatus(302);
        
        $this->assertDatabaseHas('settings', [
            'maintenance_mode' => true,
            'maintenance_message' => 'Site is under maintenance',
        ]);
    }

    /** @test */
    public function settings_validation_works_correctly()
    {
        $settings = Setting::create([
            'site_name' => 'Test Site',
            'site_currency' => 'USD',
            'tax_rate' => 0,
            'products_per_page' => 12,
        ]);

        // Test invalid color format
        $response = $this->actingAs($this->admin)->put('/admin/settings', [
            'site_name' => 'Test Site',
            'site_currency' => 'USD',
            'tax_rate' => 0,
            'products_per_page' => 12,
            'brand_primary_color' => 'invalid-color', // Invalid hex format
        ]);

        $response->assertSessionHasErrors(['brand_primary_color']);
        
        // Test invalid tax rate
        $response = $this->actingAs($this->admin)->put('/admin/settings', [
            'site_name' => 'Test Site',
            'site_currency' => 'USD',
            'tax_rate' => 150, // Invalid tax rate > 100
            'products_per_page' => 12,
        ]);

        $response->assertSessionHasErrors(['tax_rate']);
        
        // Test invalid products per page
        $response = $this->actingAs($this->admin)->put('/admin/settings', [
            'site_name' => 'Test Site',
            'site_currency' => 'USD',
            'tax_rate' => 0,
            'products_per_page' => 500, // Invalid > 100
        ]);

        $response->assertSessionHasErrors(['products_per_page']);
    }
}