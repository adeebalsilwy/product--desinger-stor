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
        Schema::table('orders', function (Blueprint $table) {
            $table->string('reference_number')->nullable()->after('payment_id');
            $table->date('transfer_date')->nullable()->after('reference_number');
            $table->text('bank_details')->nullable()->after('transfer_date');
            $table->string('receipt_path')->nullable()->after('bank_details');
            $table->enum('payment_status', ['unpaid', 'paid', 'bank_transfer', 'pending_bank_transfer'])->default('unpaid')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['reference_number', 'transfer_date', 'bank_details', 'receipt_path']);
            $table->enum('payment_status', ['unpaid', 'paid'])->default('unpaid')->change();
        });
    }
};