<?php

use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['web', 'admin'], 'prefix' => 'admin/razorpay'], function () {
    // Configuration routes
    Route::prefix('configuration')->group(function () {
        Route::get('/', 'Webkul\Razorpay\Http\Controllers\Admin\ConfigurationController@index')->name('admin.razorpay.configuration.index');
        Route::post('/', 'Webkul\Razorpay\Http\Controllers\Admin\ConfigurationController@store')->name('admin.razorpay.configuration.store');
        Route::get('/test-api', 'Webkul\Razorpay\Http\Controllers\Admin\ConfigurationController@testApi')->name('admin.razorpay.configuration.test-api');
        Route::get('/payment-methods', 'Webkul\Razorpay\Http\Controllers\Admin\ConfigurationController@getPaymentMethods')->name('admin.razorpay.configuration.payment-methods');
        Route::get('/currencies', 'Webkul\Razorpay\Http\Controllers\Admin\ConfigurationController@getCurrencies')->name('admin.razorpay.configuration.currencies');
    });

    // Order management routes
    Route::prefix('orders')->group(function () {
        Route::get('/', 'Webkul\Razorpay\Http\Controllers\Admin\OrderController@index')->name('admin.razorpay.orders.index');
        Route::get('/{id}', 'Webkul\Razorpay\Http\Controllers\Admin\OrderController@show')->name('admin.razorpay.orders.show');
        Route::post('/{id}/sync', 'Webkul\Razorpay\Http\Controllers\Admin\OrderController@sync')->name('admin.razorpay.orders.sync');
        Route::post('/{id}/refund', 'Webkul\Razorpay\Http\Controllers\Admin\OrderController@refund')->name('admin.razorpay.orders.refund');
        Route::get('/statistics', 'Webkul\Razorpay\Http\Controllers\Admin\OrderController@statistics')->name('admin.razorpay.orders.statistics');
    });

    // Webhook management routes
    Route::prefix('webhooks')->group(function () {
        Route::get('/', 'Webkul\Razorpay\Http\Controllers\Admin\WebhookController@index')->name('admin.razorpay.webhooks.index');
        Route::get('/{id}', 'Webkul\Razorpay\Http\Controllers\Admin\WebhookController@show')->name('admin.razorpay.webhooks.show');
        Route::post('/{id}/retry', 'Webkul\Razorpay\Http\Controllers\Admin\WebhookController@retry')->name('admin.razorpay.webhooks.retry');
    });

    // Dashboard route
    Route::get('/dashboard', 'Webkul\Razorpay\Http\Controllers\Admin\DashboardController@index')->name('admin.razorpay.dashboard');
}); 