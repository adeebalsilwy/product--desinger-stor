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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('customers')->onDelete('cascade');
            $table->enum('status', ['pending', 'processing', 'delivered', 'cancelled'])->default('pending');
            $table->integer('total_tshirts')->nullable();
            $table->float('total_amount')->nullable();
            $table->string('tracking_number')->nullable();
            $table->enum('payment_status', ['unpaid', 'paid'])->default('unpaid');
            $table->string('payment_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
