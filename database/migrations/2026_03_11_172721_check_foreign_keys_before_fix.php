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
        // Check foreign keys for the order_product table
        $foreignKeys = DB::select("
            SELECT 
                CONSTRAINT_NAME,
                COLUMN_NAME,
                REFERENCED_TABLE_NAME,
                REFERENCED_COLUMN_NAME
            FROM 
                information_schema.KEY_COLUMN_USAGE 
            WHERE 
                TABLE_SCHEMA = DATABASE() 
                AND TABLE_NAME = 'order_product'
                AND REFERENCED_TABLE_NAME IS NOT NULL
        ");
        
        echo "Foreign keys for order_product table:\n";
        foreach ($foreignKeys as $foreignKey) {
            echo "Constraint: {$foreignKey->CONSTRAINT_NAME}, Column: {$foreignKey->COLUMN_NAME}, References: {$foreignKey->REFERENCED_TABLE_NAME}.{$foreignKey->REFERENCED_COLUMN_NAME}\n";
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