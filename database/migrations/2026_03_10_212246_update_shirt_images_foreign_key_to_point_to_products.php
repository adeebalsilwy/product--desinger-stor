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
        // Drop the existing foreign key constraint to tshirts table
        Schema::table('shirt_images', function (Blueprint $table) {
            $table->dropForeign(['tshirt_id']);
        });
        
        // Update the foreign key constraint to point to products table instead
        Schema::table('shirt_images', function (Blueprint $table) {
            $table->foreign('tshirt_id')->references('id')->on('products')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop the foreign key constraint to products table
        Schema::table('shirt_images', function (Blueprint $table) {
            $table->dropForeign(['tshirt_id']);
        });
        
        // Add back the foreign key constraint to tshirts table
        Schema::table('shirt_images', function (Blueprint $table) {
            $table->foreign('tshirt_id')->references('id')->on('tshirts')->onDelete('cascade');
        });
    }
};