<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // This migration is already completed manually
        // The tshirt_id column has been renamed to product_id in the order_product table
        // This migration now serves as documentation of the change
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop the foreign key constraint with the new name
        Schema::table('order_product', function (Blueprint $table) {
            $table->dropForeign(['order_product_product_id_foreign']);
        });
        
        // Rename the column back from product_id to tshirt_id
        DB::statement('ALTER TABLE order_product CHANGE product_id tshirt_id BIGINT UNSIGNED');
        
        // Add the foreign key constraint back with the old column
        Schema::table('order_product', function (Blueprint $table) {
            $table->foreign('tshirt_id')->references('id')->on('products')->onDelete('cascade');
        });
    }
};