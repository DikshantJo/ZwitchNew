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
        Schema::create('razorpay_payments', function (Blueprint $table) {
            $table->id();
            $table->string('razorpay_payment_id')->unique();
            $table->string('razorpay_order_id');
            $table->string('bagisto_order_id')->nullable();
            $table->decimal('amount', 12, 4);
            $table->string('currency', 3)->default('INR');
            $table->string('method')->nullable();
            $table->string('status')->default('created');
            $table->string('bank')->nullable();
            $table->string('wallet')->nullable();
            $table->string('vpa')->nullable();
            $table->string('card_id')->nullable();
            $table->string('emi_month')->nullable();
            $table->json('notes')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate();
            
            $table->index(['razorpay_payment_id']);
            $table->index(['razorpay_order_id']);
            $table->index(['bagisto_order_id']);
            $table->index(['status']);
            $table->index(['method']);
            
            $table->foreign('razorpay_order_id')->references('razorpay_order_id')->on('razorpay_orders')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('razorpay_payments');
    }
}; 