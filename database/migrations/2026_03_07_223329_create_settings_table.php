<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('settings')) {
            Schema::create('settings', function (Blueprint $table) {
                $table->id();
                $table->string('site_name')->default('Ahlam’s Girls   ');
                $table->string('site_logo')->nullable();
                $table->string('site_favicon')->nullable();
                $table->text('site_description')->nullable();
                // Brand Colors
                $table->string('brand_primary_color')->default('#1a1a2e');
                $table->string('brand_secondary_color')->default('#16213e');
                $table->string('brand_accent_color')->default('#e94560');
                $table->string('brand_text_color')->default('#ffffff');
                $table->string('brand_bg_color')->default('#0f3460');
                // Brand Identity
                $table->string('brand_logo_woman')->nullable()->comment('Elegant woman silhouette with black dress and flower hat');
                $table->string('brand_tagline')->default('Elegance, Sewn to Perfection');
                $table->string('brand_script_font')->nullable();
                $table->string('brand_regular_font')->nullable();
                $table->string('site_email')->nullable();
                $table->string('site_phone')->nullable();
                $table->text('site_address')->nullable();
                $table->string('site_currency')->default('USD');
                $table->decimal('tax_rate', 5, 2)->default(0.00);
                $table->boolean('maintenance_mode')->default(false);
                $table->text('maintenance_message')->nullable();
                $table->string('facebook_url')->nullable();
                $table->string('twitter_url')->nullable();
                $table->string('instagram_url')->nullable();
                $table->string('linkedin_url')->nullable();
                $table->text('meta_keywords')->nullable();
                $table->text('meta_description')->nullable();
                $table->string('analytics_id')->nullable();
                $table->boolean('enable_registration')->default(true);
                $table->boolean('enable_reviews')->default(true);
                $table->integer('products_per_page')->default(12);
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
