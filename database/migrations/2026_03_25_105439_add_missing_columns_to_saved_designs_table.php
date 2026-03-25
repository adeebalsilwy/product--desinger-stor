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
        Schema::table('saved_designs', function (Blueprint $table) {
            // Add product_id column if it doesn't exist
            if (!Schema::hasColumn('saved_designs', 'product_id')) {
                $table->foreignId('product_id')->nullable()->constrained('products')->nullOnDelete();
            }
            
            // Add template_id column if it doesn't exist
            if (!Schema::hasColumn('saved_designs', 'template_id')) {
                $table->foreignId('template_id')->nullable()->constrained('design_templates')->nullOnDelete();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('saved_designs', function (Blueprint $table) {
            // Drop foreign keys first if they exist
            $foreignKeys = [];
            try {
                $pdo = Schema::getConnection()->getPdo();
                $result = $pdo->query("SELECT CONSTRAINT_NAME FROM information_schema.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'saved_designs' AND COLUMN_NAME = 'template_id'");
                $constraint = $result->fetch();
                if ($constraint) {
                    $foreignKeys[] = $constraint['CONSTRAINT_NAME'];
                }
            } catch (\Exception $e) {
                // Handle exception
            }
            
            try {
                $pdo = Schema::getConnection()->getPdo();
                $result = $pdo->query("SELECT CONSTRAINT_NAME FROM information_schema.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'saved_designs' AND COLUMN_NAME = 'product_id'");
                $constraint = $result->fetch();
                if ($constraint) {
                    $foreignKeys[] = $constraint['CONSTRAINT_NAME'];
                }
            } catch (\Exception $e) {
                // Handle exception
            }
            
            // Drop foreign key constraints
            foreach ($foreignKeys as $fk) {
                $table->dropForeign([$fk]);
            }
            
            // Drop columns if they exist
            if (Schema::hasColumn('saved_designs', 'template_id')) {
                $table->dropColumn('template_id');
            }
            if (Schema::hasColumn('saved_designs', 'product_id')) {
                $table->dropColumn('product_id');
            }
        });
    }
};