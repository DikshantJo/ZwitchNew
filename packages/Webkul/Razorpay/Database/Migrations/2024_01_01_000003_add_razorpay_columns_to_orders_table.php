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
            $table->string('razorpay_payment_id')->nullable()->after('cart_id');
            $table->string('razorpay_order_id')->nullable()->after('razorpay_payment_id');
            $table->string('razorpay_signature')->nullable()->after('razorpay_order_id');
            
            $table->index(['razorpay_payment_id']);
            $table->index(['razorpay_order_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex(['razorpay_payment_id']);
            $table->dropIndex(['razorpay_order_id']);
            
            $table->dropColumn([
                'razorpay_payment_id',
                'razorpay_order_id',
                'razorpay_signature'
            ]);
        });
    }
}; 