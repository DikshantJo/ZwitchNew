<?php

namespace App\Providers;

use Barryvdh\Debugbar\Facades\Debugbar;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\ParallelTesting;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $allowedIPs = array_map('trim', explode(',', config('app.debug_allowed_ips')));

        $allowedIPs = array_filter($allowedIPs);

        if (empty($allowedIPs)) {
            return;
        }

        if (in_array(Request::ip(), $allowedIPs)) {
            Debugbar::enable();
        } else {
            Debugbar::disable();
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        ParallelTesting::setUpTestDatabase(function (string $database, int $token) {
            Artisan::call('db:seed');
        });

        // Manually register storage routes for Laravel 11
        $this->registerStorageRoutes();
    }

    /**
     * Register storage routes manually.
     */
    protected function registerStorageRoutes(): void
    {
        $this->app->booted(function () {
            $config = config('filesystems.disks.public');
            
            if ($config && ($config['serve'] ?? false)) {
                $uri = isset($config['url'])
                    ? rtrim(parse_url($config['url'])['path'], '/')
                    : '/storage';

                \Illuminate\Support\Facades\Route::get($uri.'/{path}', function (\Illuminate\Http\Request $request, string $path) use ($config) {
                    return (new \Illuminate\Filesystem\ServeFile(
                        'public',
                        $config,
                        app()->isProduction()
                    ))($request, $path);
                })->where('path', '.*')->name('storage.public');
            }
        });
    }
}
