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
        // Check if the table exists with the old name and column
        if (Schema::hasTable('order_tshirt')) {
            // Check if the table has been renamed already
            if (!Schema::hasTable('order_product')) {
                // Rename the table from order_tshirt to order_product
                DB::statement('RENAME TABLE order_tshirt TO order_product');
            }
            
            // Check if the column has been renamed already
            $columns = Schema::getColumnListing('order_product');
            if (in_array('tshirt_id', $columns) && !in_array('product_id', $columns)) {
                // Rename the column from tshirt_id to product_id
                DB::statement('ALTER TABLE order_product CHANGE tshirt_id product_id BIGINT UNSIGNED');
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Rename the column back from product_id to tshirt_id
        $columns = Schema::getColumnListing('order_product');
        if (in_array('product_id', $columns) && !in_array('tshirt_id', $columns)) {
            DB::statement('ALTER TABLE order_product CHANGE product_id tshirt_id BIGINT UNSIGNED');
        }
        
        // Rename the table back from order_product to order_tshirt
        if (Schema::hasTable('order_product')) {
            DB::statement('RENAME TABLE order_product TO order_tshirt');
        }
    }
};