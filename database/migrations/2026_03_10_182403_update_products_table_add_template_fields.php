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
        Schema::table('products', function (Blueprint $table) {
            // Add template-related fields
            $table->foreignId('design_template_id')->nullable()->after('product_type_id')->constrained('design_templates');
            $table->json('template_data')->nullable()->after('description');
            $table->boolean('is_template_based')->default(false)->after('template_data');
            $table->string('template_category')->nullable()->after('is_template_based');
            
            // Make sure we have all necessary fields
            if (!Schema::hasColumn('products', 'thumbnail_url')) {
                $table->string('thumbnail_url')->nullable()->after('description');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['design_template_id']);
            $table->dropColumn(['design_template_id', 'template_data', 'is_template_based', 'template_category']);
        });
    }
};