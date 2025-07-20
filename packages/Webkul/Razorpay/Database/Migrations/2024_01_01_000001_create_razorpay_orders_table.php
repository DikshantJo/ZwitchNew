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
        Schema::create('razorpay_orders', function (Blueprint $table) {
            $table->id();
            $table->string('razorpay_order_id')->unique();
            $table->string('bagisto_order_id')->nullable();
            $table->decimal('amount', 12, 4);
            $table->string('currency', 3)->default('INR');
            $table->string('receipt')->nullable();
            $table->json('notes')->nullable();
            $table->string('status')->default('created');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate();
            
            $table->index(['razorpay_order_id']);
            $table->index(['bagisto_order_id']);
            $table->index(['status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('razorpay_orders');
    }
}; 