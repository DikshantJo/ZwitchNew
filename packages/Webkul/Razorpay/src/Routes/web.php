<?php

use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['web']], function () {
    Route::prefix('razorpay')->group(function () {
        // Checkout routes
        Route::get('/checkout', 'Webkul\Razorpay\Http\Controllers\PaymentController@checkout')->name('razorpay.checkout');
        Route::get('/success', 'Webkul\Razorpay\Http\Controllers\PaymentController@success')->name('razorpay.success');
        Route::get('/failure', 'Webkul\Razorpay\Http\Controllers\PaymentController@failure')->name('razorpay.failure');
        
        // API routes
        Route::get('/api/test', 'Webkul\Razorpay\Http\Controllers\PaymentController@testApi')->name('razorpay.api.test');
        Route::get('/api/methods', 'Webkul\Razorpay\Http\Controllers\PaymentController@getPaymentMethods')->name('razorpay.api.methods');
        Route::get('/api/currencies', 'Webkul\Razorpay\Http\Controllers\PaymentController@getCurrencies')->name('razorpay.api.currencies');
        Route::post('/api/validate', 'Webkul\Razorpay\Http\Controllers\PaymentController@validatePaymentMethod')->name('razorpay.api.validate');
        Route::get('/api/config', 'Webkul\Razorpay\Http\Controllers\PaymentController@getConfiguration')->name('razorpay.api.config');
        
        // Webhook route
        Route::post('/webhook', 'Webkul\Razorpay\Http\Controllers\WebhookController@handle')->name('razorpay.webhook');
    });
}); 