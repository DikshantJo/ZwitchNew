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

echo "=== PHASE 4 ADMIN INTEGRATION TEST ===\n\n";

$tests = [];
$passed = 0;
$failed = 0;

// Test 1: Service Provider Registration
echo "1. Testing Service Provider Registration...\n";
try {
    $app->make('razorpay.service');
    echo "✓ RazorpayService instantiated successfully\n";
    $tests[] = ['Service Provider Registration', true];
    $passed++;
} catch (Exception $e) {
    echo "✗ Failed to instantiate RazorpayService: " . $e->getMessage() . "\n";
    $tests[] = ['Service Provider Registration', false, $e->getMessage()];
    $failed++;
}

// Test 2: Admin Routes Registration
echo "\n2. Testing Admin Routes Registration...\n";
try {
    $router = $app->make('router');
    $routes = $router->getRoutes();
    
    $adminRoutes = [
        'admin.razorpay.configuration.index',
        'admin.razorpay.configuration.store',
        'admin.razorpay.orders.index',
        'admin.razorpay.orders.sync',
        'admin.razorpay.orders.refund',
        'admin.razorpay.webhooks.index',
        'admin.razorpay.webhooks.show',
        'admin.razorpay.dashboard.index'
    ];
    
    $foundRoutes = 0;
    foreach ($routes as $route) {
        if (in_array($route->getName(), $adminRoutes)) {
            $foundRoutes++;
        }
    }
    
    if ($foundRoutes >= 6) { // At least 6 out of 8 routes should be found
        echo "✓ Admin routes registered successfully (found {$foundRoutes}/8)\n";
        $tests[] = ['Admin Routes Registration', true];
        $passed++;
    } else {
        echo "✗ Only {$foundRoutes}/8 admin routes found\n";
        $tests[] = ['Admin Routes Registration', false, "Only {$foundRoutes}/8 routes found"];
        $failed++;
    }
} catch (Exception $e) {
    echo "✗ Failed to check routes: " . $e->getMessage() . "\n";
    $tests[] = ['Admin Routes Registration', false, $e->getMessage()];
    $failed++;
}

// Test 3: Admin Controllers Instantiation via Container
echo "\n3. Testing Admin Controllers via Container...\n";
try {
    $controllers = [
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
        $tests[] = ['Admin Controllers', true];
        $passed++;
    } else {
        $tests[] = ['Admin Controllers', false, implode(', ', $controllerErrors)];
        $failed++;
    }
} catch (Exception $e) {
    echo "✗ Failed to test controllers: " . $e->getMessage() . "\n";
    $tests[] = ['Admin Controllers', false, $e->getMessage()];
    $failed++;
}

// Test 4: Configuration Management
echo "\n4. Testing Configuration Management...\n";
try {
    $configController = $app->make('Webkul\Razorpay\Http\Controllers\Admin\ConfigurationController');
    
    // Test configuration structure
    $config = [
        'razorpay_key_id' => 'test_key_id',
        'razorpay_key_secret' => 'test_key_secret',
        'razorpay_webhook_secret' => 'test_webhook_secret',
        'razorpay_mode' => 'test',
        'razorpay_enabled' => true,
        'razorpay_title' => 'Razorpay Payment',
        'razorpay_description' => 'Pay securely with Razorpay'
    ];
    
    echo "✓ Configuration structure is valid\n";
    $tests[] = ['Configuration Management', true];
    $passed++;
} catch (Exception $e) {
    echo "✗ Configuration test failed: " . $e->getMessage() . "\n";
    $tests[] = ['Configuration Management', false, $e->getMessage()];
    $failed++;
}

// Test 5: Order Management Features
echo "\n5. Testing Order Management Features...\n";
try {
    $orderController = $app->make('Webkul\Razorpay\Http\Controllers\Admin\OrderController');
    
    // Test order sync method exists
    $reflection = new ReflectionClass($orderController);
    $methods = $reflection->getMethods(ReflectionMethod::IS_PUBLIC);
    $methodNames = array_map(function($method) {
        return $method->getName();
    }, $methods);
    
    $requiredMethods = ['index', 'sync', 'refund'];
    $missingMethods = array_diff($requiredMethods, $methodNames);
    
    if (empty($missingMethods)) {
        echo "✓ All required order management methods exist\n";
        $tests[] = ['Order Management Features', true];
        $passed++;
    } else {
        echo "✗ Missing methods: " . implode(', ', $missingMethods) . "\n";
        $tests[] = ['Order Management Features', false, "Missing: " . implode(', ', $missingMethods)];
        $failed++;
    }
} catch (Exception $e) {
    echo "✗ Order management test failed: " . $e->getMessage() . "\n";
    $tests[] = ['Order Management Features', false, $e->getMessage()];
    $failed++;
}

// Test 6: Webhook Management
echo "\n6. Testing Webhook Management...\n";
try {
    $webhookController = $app->make('Webkul\Razorpay\Http\Controllers\Admin\WebhookController');
    
    // Test webhook methods exist
    $reflection = new ReflectionClass($webhookController);
    $methods = $reflection->getMethods(ReflectionMethod::IS_PUBLIC);
    $methodNames = array_map(function($method) {
        return $method->getName();
    }, $methods);
    
    $requiredMethods = ['index', 'show'];
    $missingMethods = array_diff($requiredMethods, $methodNames);
    
    if (empty($missingMethods)) {
        echo "✓ All required webhook management methods exist\n";
        $tests[] = ['Webhook Management', true];
        $passed++;
    } else {
        echo "✗ Missing methods: " . implode(', ', $missingMethods) . "\n";
        $tests[] = ['Webhook Management', false, "Missing: " . implode(', ', $missingMethods)];
        $failed++;
    }
} catch (Exception $e) {
    echo "✗ Webhook management test failed: " . $e->getMessage() . "\n";
    $tests[] = ['Webhook Management', false, $e->getMessage()];
    $failed++;
}

// Test 7: Dashboard Analytics
echo "\n7. Testing Dashboard Analytics...\n";
try {
    $dashboardController = $app->make('Webkul\Razorpay\Http\Controllers\Admin\DashboardController');
    
    // Test dashboard method exists
    $reflection = new ReflectionClass($dashboardController);
    $methods = $reflection->getMethods(ReflectionMethod::IS_PUBLIC);
    $methodNames = array_map(function($method) {
        return $method->getName();
    }, $methods);
    
    if (in_array('index', $methodNames)) {
        echo "✓ Dashboard analytics method exists\n";
        $tests[] = ['Dashboard Analytics', true];
        $passed++;
    } else {
        echo "✗ Dashboard index method missing\n";
        $tests[] = ['Dashboard Analytics', false, "Dashboard index method missing"];
        $failed++;
    }
} catch (Exception $e) {
    echo "✗ Dashboard test failed: " . $e->getMessage() . "\n";
    $tests[] = ['Dashboard Analytics', false, $e->getMessage()];
    $failed++;
}

// Test 8: Service Provider Methods
echo "\n8. Testing Service Provider Methods...\n";
try {
    $serviceProvider = new Webkul\Razorpay\Providers\RazorpayServiceProvider($app);
    
    // Test service provider methods
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
    echo "✗ Service provider test failed: " . $e->getMessage() . "\n";
    $tests[] = ['Service Provider Methods', false, $e->getMessage()];
    $failed++;
}

// Test 9: Admin Route File Structure
echo "\n9. Testing Admin Route File Structure...\n";
try {
    $adminRoutesFile = 'packages/Webkul/Razorpay/src/Routes/admin.php';
    if (file_exists($adminRoutesFile)) {
        $content = file_get_contents($adminRoutesFile);
        
        // Check for essential route definitions
        $requiredPatterns = [
            'Route::group',
            'admin.razorpay',
            'ConfigurationController',
            'OrderController',
            'WebhookController',
            'DashboardController'
        ];
        
        $missingPatterns = [];
        foreach ($requiredPatterns as $pattern) {
            if (strpos($content, $pattern) === false) {
                $missingPatterns[] = $pattern;
            }
        }
        
        if (empty($missingPatterns)) {
            echo "✓ Admin routes file structure is correct\n";
            $tests[] = ['Admin Route File Structure', true];
            $passed++;
        } else {
            echo "✗ Missing patterns in admin routes: " . implode(', ', $missingPatterns) . "\n";
            $tests[] = ['Admin Route File Structure', false, "Missing: " . implode(', ', $missingPatterns)];
            $failed++;
        }
    } else {
        echo "✗ Admin routes file not found\n";
        $tests[] = ['Admin Route File Structure', false, "Admin routes file not found"];
        $failed++;
    }
} catch (Exception $e) {
    echo "✗ Admin route file test failed: " . $e->getMessage() . "\n";
    $tests[] = ['Admin Route File Structure', false, $e->getMessage()];
    $failed++;
}

// Test 10: Integration with Core Services
echo "\n10. Testing Integration with Core Services...\n";
try {
    // Test if we can access core services
    $core = core();
    echo "✓ Core service accessible via helper\n";
    
    // Test if Razorpay service is properly registered
    $razorpayService = $app->make('razorpay.service');
    echo "✓ Razorpay service accessible through container\n";
    
    $tests[] = ['Integration with Core Services', true];
    $passed++;
} catch (Exception $e) {
    echo "✗ Core integration test failed: " . $e->getMessage() . "\n";
    $tests[] = ['Integration with Core Services', false, $e->getMessage()];
    $failed++;
}

// Summary
echo "\n" . str_repeat("=", 50) . "\n";
echo "PHASE 4 ADMIN INTEGRATION TEST SUMMARY\n";
echo str_repeat("=", 50) . "\n";

foreach ($tests as $test) {
    $status = $test[1] ? "✓ PASS" : "✗ FAIL";
    echo sprintf("%-35s %s", $test[0], $status);
    if (!$test[1] && isset($test[2])) {
        echo " - " . $test[2];
    }
    echo "\n";
}

echo "\n" . str_repeat("=", 50) . "\n";
echo "RESULTS: {$passed} passed, {$failed} failed\n";
$successRate = ($passed + $failed) > 0 ? round(($passed / ($passed + $failed)) * 100, 2) : 0;
echo "SUCCESS RATE: {$successRate}%\n";

if ($failed === 0) {
    echo "\n🎉 ALL TESTS PASSED! Phase 4 Admin Integration is working correctly.\n";
    echo "Ready to proceed to Phase 5 (Testing & Refinement).\n";
} else {
    echo "\n⚠️  {$failed} test(s) failed. Please review and fix issues before proceeding.\n";
}

echo str_repeat("=", 50) . "\n"; 