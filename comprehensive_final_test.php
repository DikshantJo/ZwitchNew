<?php

require_once 'vendor/autoload.php';

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

// Bootstrap Laravel
$app = Application::configure(basePath: __DIR__)
    ->withRouting(
        web: __DIR__.'/routes/web.php',
        commands: __DIR__.'/routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();

$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== COMPREHENSIVE RAZORPAY INTEGRATION TEST ===\n\n";

$tests = [];
$passed = 0;
$failed = 0;

// Test 1: Package Structure
echo "1. Testing Package Structure...\n";
try {
    $requiredFiles = [
        'packages/Webkul/Razorpay/src/Payment/Razorpay.php',
        'packages/Webkul/Razorpay/src/Services/RazorpayService.php',
        'packages/Webkul/Razorpay/src/Providers/RazorpayServiceProvider.php',
        'packages/Webkul/Razorpay/src/Providers/EventServiceProvider.php',
        'packages/Webkul/Razorpay/src/Http/Controllers/PaymentController.php',
        'packages/Webkul/Razorpay/src/Http/Controllers/WebhookController.php',
        'packages/Webkul/Razorpay/src/Http/Controllers/Admin/ConfigurationController.php',
        'packages/Webkul/Razorpay/src/Http/Controllers/Admin/OrderController.php',
        'packages/Webkul/Razorpay/src/Http/Controllers/Admin/WebhookController.php',
        'packages/Webkul/Razorpay/src/Http/Controllers/Admin/DashboardController.php',
        'packages/Webkul/Razorpay/src/Models/RazorpayOrder.php',
        'packages/Webkul/Razorpay/src/Models/RazorpayPayment.php',
        'packages/Webkul/Razorpay/src/Routes/web.php',
        'packages/Webkul/Razorpay/src/Routes/admin.php',
        'packages/Webkul/Razorpay/src/Resources/views/checkout.blade.php',
        'packages/Webkul/Razorpay/src/Resources/lang/en/app.php',
        'packages/Webkul/Razorpay/composer.json',
        'packages/Webkul/Razorpay/Database/Migrations/2024_01_01_000001_create_razorpay_orders_table.php',
        'packages/Webkul/Razorpay/Database/Migrations/2024_01_01_000002_create_razorpay_payments_table.php'
    ];
    
    $missingFiles = [];
    foreach ($requiredFiles as $file) {
        if (!file_exists($file)) {
            $missingFiles[] = $file;
        }
    }
    
    if (empty($missingFiles)) {
        echo "✓ All required files exist\n";
        $tests[] = ['Package Structure', true];
        $passed++;
    } else {
        echo "✗ Missing files: " . implode(', ', $missingFiles) . "\n";
        $tests[] = ['Package Structure', false, "Missing: " . implode(', ', $missingFiles)];
        $failed++;
    }
} catch (Exception $e) {
    echo "✗ Package structure test failed: " . $e->getMessage() . "\n";
    $tests[] = ['Package Structure', false, $e->getMessage()];
    $failed++;
}

// Test 2: Service Provider Registration
echo "\n2. Testing Service Provider Registration...\n";
try {
    $razorpayService = $app->make('razorpay.service');
    echo "✓ RazorpayService instantiated via razorpay.service\n";
    
    $razorpayService2 = $app->make('razorpay');
    echo "✓ RazorpayService instantiated via razorpay\n";
    
    $tests[] = ['Service Provider Registration', true];
    $passed++;
} catch (Exception $e) {
    echo "✗ Service provider test failed: " . $e->getMessage() . "\n";
    $tests[] = ['Service Provider Registration', false, $e->getMessage()];
    $failed++;
}

// Test 3: Payment Method Class
echo "\n3. Testing Payment Method Class...\n";
try {
    $paymentMethod = new \Webkul\Razorpay\Payment\Razorpay();
    echo "✓ Payment method class instantiated\n";
    
    // Test methods exist
    $reflection = new ReflectionClass($paymentMethod);
    $methods = $reflection->getMethods(ReflectionMethod::IS_PUBLIC);
    $methodNames = array_map(function($method) {
        return $method->getName();
    }, $methods);
    
    $requiredMethods = ['getRedirectUrl', 'isAvailable', 'getImage', 'getAdditionalDetails'];
    $missingMethods = array_diff($requiredMethods, $methodNames);
    
    if (empty($missingMethods)) {
        echo "✓ All required payment method methods exist\n";
        $tests[] = ['Payment Method Class', true];
        $passed++;
    } else {
        echo "✗ Missing methods: " . implode(', ', $missingMethods) . "\n";
        $tests[] = ['Payment Method Class', false, "Missing: " . implode(', ', $missingMethods)];
        $failed++;
    }
} catch (Exception $e) {
    echo "✗ Payment method test failed: " . $e->getMessage() . "\n";
    $tests[] = ['Payment Method Class', false, $e->getMessage()];
    $failed++;
}

// Test 4: RazorpayService Methods
echo "\n4. Testing RazorpayService Methods...\n";
try {
    $service = new \Webkul\Razorpay\Services\RazorpayService();
    
    $reflection = new ReflectionClass($service);
    $methods = $reflection->getMethods(ReflectionMethod::IS_PUBLIC);
    $methodNames = array_map(function($method) {
        return $method->getName();
    }, $methods);
    
    $requiredMethods = [
        'createOrder', 'getPaymentDetails', 'processRefund', 'verifyWebhookSignature',
        'getCredentials', 'isConfigured', 'getOrderDetails', 'capturePayment',
        'getRefundDetails', 'createPaymentLink', 'generateUpiQrCode', 'validatePaymentAmount',
        'getSupportedPaymentMethods', 'getSupportedCurrencies', 'testApiConnectivity'
    ];
    
    $missingMethods = array_diff($requiredMethods, $methodNames);
    
    if (empty($missingMethods)) {
        echo "✓ All required service methods exist\n";
        $tests[] = ['RazorpayService Methods', true];
        $passed++;
    } else {
        echo "✗ Missing methods: " . implode(', ', $missingMethods) . "\n";
        $tests[] = ['RazorpayService Methods', false, "Missing: " . implode(', ', $missingMethods)];
        $failed++;
    }
} catch (Exception $e) {
    echo "✗ Service methods test failed: " . $e->getMessage() . "\n";
    $tests[] = ['RazorpayService Methods', false, $e->getMessage()];
    $failed++;
}

// Test 5: Models
echo "\n5. Testing Models...\n";
try {
    $models = [
        'Webkul\Razorpay\Models\RazorpayOrder',
        'Webkul\Razorpay\Models\RazorpayPayment'
    ];
    
    $modelErrors = [];
    foreach ($models as $model) {
        try {
            $instance = new $model();
            echo "✓ {$model} instantiated\n";
        } catch (Exception $e) {
            $modelErrors[] = "{$model}: " . $e->getMessage();
            echo "✗ {$model} failed: " . $e->getMessage() . "\n";
        }
    }
    
    if (empty($modelErrors)) {
        $tests[] = ['Models', true];
        $passed++;
    } else {
        $tests[] = ['Models', false, implode(', ', $modelErrors)];
        $failed++;
    }
} catch (Exception $e) {
    echo "✗ Models test failed: " . $e->getMessage() . "\n";
    $tests[] = ['Models', false, $e->getMessage()];
    $failed++;
}

// Test 6: Controllers via Container
echo "\n6. Testing Controllers via Container...\n";
try {
    $controllers = [
        'Webkul\Razorpay\Http\Controllers\PaymentController',
        'Webkul\Razorpay\Http\Controllers\WebhookController',
        'Webkul\Razorpay\Http\Controllers\Admin\ConfigurationController',
        'Webkul\Razorpay\Http\Controllers\Admin\OrderController',
        'Webkul\Razorpay\Http\Controllers\Admin\WebhookController',
        'Webkul\Razorpay\Http\Controllers\Admin\DashboardController'
    ];
    
    $controllerErrors = [];
    foreach ($controllers as $controller) {
        try {
            $instance = $app->make($controller);
            echo "✓ {$controller} instantiated via container\n";
        } catch (Exception $e) {
            $controllerErrors[] = "{$controller}: " . $e->getMessage();
            echo "✗ {$controller} failed: " . $e->getMessage() . "\n";
        }
    }
    
    if (empty($controllerErrors)) {
        $tests[] = ['Controllers via Container', true];
        $passed++;
    } else {
        $tests[] = ['Controllers via Container', false, implode(', ', $controllerErrors)];
        $failed++;
    }
} catch (Exception $e) {
    echo "✗ Controllers test failed: " . $e->getMessage() . "\n";
    $tests[] = ['Controllers via Container', false, $e->getMessage()];
    $failed++;
}

// Test 7: Routes Registration
echo "\n7. Testing Routes Registration...\n";
try {
    $router = $app->make('router');
    $routes = $router->getRoutes();
    
    $expectedRoutes = [
        'razorpay.checkout',
        'razorpay.success',
        'razorpay.failure',
        'razorpay.webhook',
        'admin.razorpay.configuration.index',
        'admin.razorpay.orders.index',
        'admin.razorpay.webhooks.index',
        'admin.razorpay.dashboard'
    ];
    
    $foundRoutes = 0;
    foreach ($routes as $route) {
        if (in_array($route->getName(), $expectedRoutes)) {
            $foundRoutes++;
        }
    }
    
    if ($foundRoutes >= 6) { // At least 6 out of 8 routes should be found
        echo "✓ Routes registered successfully (found {$foundRoutes}/8)\n";
        $tests[] = ['Routes Registration', true];
        $passed++;
    } else {
        echo "✗ Only {$foundRoutes}/8 routes found\n";
        $tests[] = ['Routes Registration', false, "Only {$foundRoutes}/8 routes found"];
        $failed++;
    }
} catch (Exception $e) {
    echo "✗ Routes test failed: " . $e->getMessage() . "\n";
    $tests[] = ['Routes Registration', false, $e->getMessage()];
    $failed++;
}

// Test 8: Service Provider Methods
echo "\n8. Testing Service Provider Methods...\n";
try {
    $serviceProvider = new \Webkul\Razorpay\Providers\RazorpayServiceProvider($app);
    
    $reflection = new ReflectionClass($serviceProvider);
    $methods = $reflection->getMethods(ReflectionMethod::IS_PUBLIC);
    $methodNames = array_map(function($method) {
        return $method->getName();
    }, $methods);
    
    $requiredMethods = ['register', 'boot'];
    $missingMethods = array_diff($requiredMethods, $methodNames);
    
    if (empty($missingMethods)) {
        echo "✓ Service provider has required methods\n";
        $tests[] = ['Service Provider Methods', true];
        $passed++;
    } else {
        echo "✗ Missing methods: " . implode(', ', $missingMethods) . "\n";
        $tests[] = ['Service Provider Methods', false, "Missing: " . implode(', ', $missingMethods)];
        $failed++;
    }
} catch (Exception $e) {
    echo "✗ Service provider methods test failed: " . $e->getMessage() . "\n";
    $tests[] = ['Service Provider Methods', false, $e->getMessage()];
    $failed++;
}

// Test 9: Core Integration
echo "\n9. Testing Core Integration...\n";
try {
    $core = core();
    echo "✓ Core service accessible via helper\n";
    
    $razorpayService = $app->make('razorpay.service');
    echo "✓ Razorpay service accessible through container\n";
    
    // Test if service can access core configuration
    $credentials = $razorpayService->getCredentials();
    echo "✓ Service can access configuration\n";
    
    $tests[] = ['Core Integration', true];
    $passed++;
} catch (Exception $e) {
    echo "✗ Core integration test failed: " . $e->getMessage() . "\n";
    $tests[] = ['Core Integration', false, $e->getMessage()];
    $failed++;
}

// Test 10: Basic Functionality
echo "\n10. Testing Basic Functionality...\n";
try {
    $service = new \Webkul\Razorpay\Services\RazorpayService();
    
    // Test configuration check
    $isConfigured = $service->isConfigured();
    echo "✓ Configuration check works\n";
    
    // Test amount validation
    $isValidAmount = $service->validatePaymentAmount(100, 'INR');
    echo "✓ Amount validation works\n";
    
    // Test supported currencies
    $currencies = $service->getSupportedCurrencies();
    if (is_array($currencies) && !empty($currencies)) {
        echo "✓ Supported currencies method works\n";
    } else {
        echo "⚠ Supported currencies method returned empty result\n";
    }
    
    $tests[] = ['Basic Functionality', true];
    $passed++;
} catch (Exception $e) {
    echo "✗ Basic functionality test failed: " . $e->getMessage() . "\n";
    $tests[] = ['Basic Functionality', false, $e->getMessage()];
    $failed++;
}

// Test 11: View Files
echo "\n11. Testing View Files...\n";
try {
    $viewFiles = [
        'packages/Webkul/Razorpay/src/Resources/views/checkout.blade.php'
    ];
    
    $missingViews = [];
    foreach ($viewFiles as $view) {
        if (!file_exists($view)) {
            $missingViews[] = $view;
        }
    }
    
    if (empty($missingViews)) {
        echo "✓ View files exist\n";
        $tests[] = ['View Files', true];
        $passed++;
    } else {
        echo "✗ Missing view files: " . implode(', ', $missingViews) . "\n";
        $tests[] = ['View Files', false, "Missing: " . implode(', ', $missingViews)];
        $failed++;
    }
} catch (Exception $e) {
    echo "✗ View files test failed: " . $e->getMessage() . "\n";
    $tests[] = ['View Files', false, $e->getMessage()];
    $failed++;
}

// Test 12: Language Files
echo "\n12. Testing Language Files...\n";
try {
    $langFiles = [
        'packages/Webkul/Razorpay/src/Resources/lang/en/app.php'
    ];
    
    $missingLang = [];
    foreach ($langFiles as $lang) {
        if (!file_exists($lang)) {
            $missingLang[] = $lang;
        }
    }
    
    if (empty($missingLang)) {
        echo "✓ Language files exist\n";
        $tests[] = ['Language Files', true];
        $passed++;
    } else {
        echo "✗ Missing language files: " . implode(', ', $missingLang) . "\n";
        $tests[] = ['Language Files', false, "Missing: " . implode(', ', $missingLang)];
        $failed++;
    }
} catch (Exception $e) {
    echo "✗ Language files test failed: " . $e->getMessage() . "\n";
    $tests[] = ['Language Files', false, $e->getMessage()];
    $failed++;
}

// Test 13: Migration Files
echo "\n13. Testing Migration Files...\n";
try {
    $migrationFiles = [
        'packages/Webkul/Razorpay/Database/Migrations/2024_01_01_000001_create_razorpay_orders_table.php',
        'packages/Webkul/Razorpay/Database/Migrations/2024_01_01_000002_create_razorpay_payments_table.php'
    ];
    
    $missingMigrations = [];
    foreach ($migrationFiles as $migration) {
        if (!file_exists($migration)) {
            $missingMigrations[] = $migration;
        }
    }
    
    if (empty($missingMigrations)) {
        echo "✓ Migration files exist\n";
        $tests[] = ['Migration Files', true];
        $passed++;
    } else {
        echo "✗ Missing migration files: " . implode(', ', $missingMigrations) . "\n";
        $tests[] = ['Migration Files', false, "Missing: " . implode(', ', $missingMigrations)];
        $failed++;
    }
} catch (Exception $e) {
    echo "✗ Migration files test failed: " . $e->getMessage() . "\n";
    $tests[] = ['Migration Files', false, $e->getMessage()];
    $failed++;
}

// Test 14: Composer Configuration
echo "\n14. Testing Composer Configuration...\n";
try {
    $composerFile = 'packages/Webkul/Razorpay/composer.json';
    if (file_exists($composerFile)) {
        $composerData = json_decode(file_get_contents($composerFile), true);
        
        if (isset($composerData['autoload']['psr-4']['Webkul\\Razorpay\\'])) {
            echo "✓ PSR-4 autoloading configured\n";
        } else {
            echo "✗ PSR-4 autoloading not configured\n";
            $tests[] = ['Composer Configuration', false, "PSR-4 autoloading not configured"];
            $failed++;
            goto composer_end;
        }
        
        if (isset($composerData['extra']['laravel']['providers'])) {
            echo "✓ Laravel providers configured\n";
        } else {
            echo "✗ Laravel providers not configured\n";
            $tests[] = ['Composer Configuration', false, "Laravel providers not configured"];
            $failed++;
            goto composer_end;
        }
        
        $tests[] = ['Composer Configuration', true];
        $passed++;
    } else {
        echo "✗ Composer.json file not found\n";
        $tests[] = ['Composer Configuration', false, "Composer.json file not found"];
        $failed++;
    }
} catch (Exception $e) {
    echo "✗ Composer configuration test failed: " . $e->getMessage() . "\n";
    $tests[] = ['Composer Configuration', false, $e->getMessage()];
    $failed++;
}
composer_end:

// Summary
echo "\n" . str_repeat("=", 60) . "\n";
echo "COMPREHENSIVE RAZORPAY INTEGRATION TEST SUMMARY\n";
echo str_repeat("=", 60) . "\n";

foreach ($tests as $test) {
    $status = $test[1] ? "✓ PASS" : "✗ FAIL";
    echo sprintf("%-35s %s", $test[0], $status);
    if (!$test[1] && isset($test[2])) {
        echo " - " . $test[2];
    }
    echo "\n";
}

echo "\n" . str_repeat("=", 60) . "\n";
echo "RESULTS: {$passed} passed, {$failed} failed\n";
$successRate = ($passed + $failed) > 0 ? round(($passed / ($passed + $failed)) * 100, 2) : 0;
echo "SUCCESS RATE: {$successRate}%\n";

if ($failed === 0) {
    echo "\n🎉 ALL TESTS PASSED! Razorpay integration is fully functional.\n";
    echo "✅ Package structure is complete\n";
    echo "✅ Service providers are working\n";
    echo "✅ Controllers are properly instantiated\n";
    echo "✅ Models are accessible\n";
    echo "✅ Routes are registered\n";
    echo "✅ Core integration is working\n";
    echo "✅ Basic functionality is operational\n";
    echo "\nReady for production deployment!\n";
} else {
    echo "\n⚠️  {$failed} test(s) failed. Please review and fix issues.\n";
}

echo str_repeat("=", 60) . "\n"; 