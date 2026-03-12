<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Check the foreign key names for order_tshirt table
        $foreignKeys = DB::select("
            SELECT 
                CONSTRAINT_NAME 
            FROM 
                information_schema.KEY_COLUMN_USAGE 
            WHERE 
                TABLE_SCHEMA = DATABASE() 
                AND TABLE_NAME = 'order_tshirt'
                AND REFERENCED_TABLE_NAME = 'tshirts'
        ");
        
        foreach ($foreignKeys as $foreignKey) {
            var_dump($foreignKey->CONSTRAINT_NAME);
        }
        
        // Also check for references to products table
        $productForeignKeys = DB::select("
            SELECT 
                CONSTRAINT_NAME 
            FROM 
                information_schema.KEY_COLUMN_USAGE 
            WHERE 
                TABLE_SCHEMA = DATABASE() 
                AND TABLE_NAME = 'order_product'
                AND REFERENCED_TABLE_NAME = 'products'
        ");
        
        foreach ($productForeignKeys as $foreignKey) {
            var_dump($foreignKey->CONSTRAINT_NAME);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};