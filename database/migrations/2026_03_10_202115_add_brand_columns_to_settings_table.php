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
        Schema::table('settings', function (Blueprint $table) {

           if(!Schema::hasColumn('settings', 'brand_primary_color')) $table->string('brand_primary_color')->default('#2c3e50')->after('products_per_page');
           if(!Schema::hasColumn('settings', 'brand_secondary_color')) $table->string('brand_secondary_color')->default('#34495e')->after('brand_primary_color');
           if(!Schema::hasColumn('settings', 'brand_accent_color')) $table->string('brand_accent_color')->default('#e74c3c')->after('brand_secondary_color');
           if(!Schema::hasColumn('settings', 'brand_bg_color')) $table->string('brand_bg_color')->default('#ecf0f1')->after('brand_accent_color');
           if(!Schema::hasColumn('settings', 'brand_text_color')) $table->string('brand_text_color')->default('#2c3e50')->after('brand_bg_color');
           if(!Schema::hasColumn('settings', 'brand_tagline')) $table->string('brand_tagline')->default('Elegance, Sewn to Perfection')->after('brand_text_color');
           if(!Schema::hasColumn('settings', 'brand_logo_woman')) $table->string('brand_logo_woman')->nullable()->after('brand_tagline');
           if(!Schema::hasColumn('settings', 'brand_script_font')) $table->string('brand_script_font')->default('brand-script')->after('brand_logo_woman');
           if(!Schema::hasColumn('settings', 'brand_regular_font')) $table->string('brand_regular_font')->default('brand-elegant')->after('brand_script_font');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn([
                'brand_primary_color',
                'brand_secondary_color',
                'brand_accent_color',
                'brand_bg_color',
                'brand_text_color',
                'brand_tagline',
                'brand_logo_woman',
                'brand_script_font',
                'brand_regular_font'
            ]);
        });
    }
};
