<?php

namespace Webkul\Razorpay\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Event;
use Webkul\Core\Providers\CoreModuleServiceProvider;

class RazorpayServiceProvider extends CoreModuleServiceProvider
{
    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        parent::boot();

        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');
        $this->loadRoutesFrom(__DIR__ . '/../Routes/web.php');
        $this->loadRoutesFrom(__DIR__ . '/../Routes/admin.php');
        $this->loadTranslationsFrom(__DIR__ . '/../Resources/lang', 'razorpay');
        $this->loadViewsFrom(__DIR__ . '/../Resources/views', 'razorpay');

        $this->publishes([
            __DIR__ . '/../Resources/views' => resource_path('views/vendor/razorpay'),
        ]);

        $this->publishes([
            __DIR__ . '/../Resources/lang' => resource_path('lang/vendor/razorpay'),
        ]);

        $this->publishes([
            __DIR__ . '/../Resources/assets' => public_path('vendor/razorpay'),
        ], 'public');

        $this->app->register(EventServiceProvider::class);

        // Register the Razorpay payment method
        $this->app->singleton('payment_methods', function ($app) {
            return [
                'razorpay' => \Webkul\Razorpay\Payment\Razorpay::class,
            ];
        });
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('razorpay', function ($app) {
            return new \Webkul\Razorpay\Services\RazorpayService();
        });
        $this->app->singleton('razorpay.service', function ($app) {
            return new \Webkul\Razorpay\Services\RazorpayService();
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['razorpay'];
    }
} 