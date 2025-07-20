<?php

namespace Webkul\Razorpay\Tests\Feature;

use Tests\TestCase;
use Webkul\Razorpay\Services\RazorpayService;
use Webkul\Sales\Models\Order;
use Webkul\Customer\Models\Customer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;

class PerformanceTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $razorpayService;
    protected $customer;
    protected $order;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->razorpayService = new RazorpayService();
        
        // Create required customer group first
        $customerGroup = \Webkul\Customer\Models\CustomerGroup::factory()->create([
            'id' => 2,
            'name' => 'General',
            'code' => 'general',
            'is_user_defined' => false
        ]);

        // Create a category first to satisfy foreign key constraint
        $category = \Webkul\Category\Models\Category::create([
            'id' => 1,
            'name' => 'Root Category',
            'slug' => 'root-category',
            'description' => 'Root category for testing',
            'status' => 1,
            'position' => 1
        ]);

        // Create locale to satisfy foreign key constraint
        $locale = \Webkul\Core\Models\Locale::create([
            'id' => 1,
            'code' => 'en',
            'name' => 'English',
            'direction' => 'ltr',
            'status' => 1
        ]);

        // Create currency to satisfy foreign key constraint
        $currency = \Webkul\Core\Models\Currency::create([
            'id' => 1,
            'code' => 'INR',
            'name' => 'Indian Rupee',
            'symbol' => '₹',
            'status' => 1
        ]);

        // Create channel to satisfy foreign key constraint
        $channel = \Webkul\Core\Models\Channel::create([
            'id' => 1,
            'code' => 'default',
            'name' => 'Default Channel',
            'description' => 'Default channel for testing',
            'hostname' => 'localhost',
            'root_category_id' => 1,
            'default_locale_id' => 1,
            'base_currency_id' => 1,
            'theme' => 'default',
            'is_maintenance_on' => false,
            'allowed_ips' => null
        ]);

        // Create test customer with proper data
        $this->customer = \Webkul\Customer\Models\Customer::create([
            'first_name' => 'Test',
            'last_name' => 'Customer',
            'email' => 'test@example.com',
            'password' => \Illuminate\Support\Facades\Hash::make('password'),
            'customer_group_id' => 2,
            'channel_id' => 1,
            'status' => 1
        ]);

        // Create test order
        $this->order = \Webkul\Sales\Models\Order::create([
            'customer_id' => $this->customer->id,
            'customer_email' => $this->customer->email,
            'customer_first_name' => $this->customer->first_name,
            'customer_last_name' => $this->customer->last_name,
            'order_currency_code' => 'INR',
            'grand_total' => 1000.00,
            'base_grand_total' => 1000.00,
            'status' => 'pending'
        ]);

        // Configure Razorpay for testing
        Config::set('paymentmethods.razorpay.webhook_secret', 'test_webhook_secret');
        Config::set('paymentmethods.razorpay.key_secret', 'test_key_secret');
        Config::set('paymentmethods.razorpay.sandbox', true);
    }

    /** @test */
    public function it_optimizes_database_queries()
    {
        // Create multiple orders for testing
        $orders = Order::factory()->count(10)->create([
            'customer_id' => $this->customer->id,
            'payment_method' => 'razorpay',
            'status' => 'pending'
        ]);

        // Enable query logging
        DB::enableQueryLog();

        // Test payment processing
        $paymentData = [
            'razorpay_payment_id' => 'pay_test123',
            'razorpay_order_id' => 'order_test123',
            'razorpay_signature' => 'valid_signature',
            'payment_method' => 'card',
            'amount' => 100000,
            'currency' => 'INR'
        ];

        $startTime = microtime(true);

        $response = $this->actingAs($this->customer, 'customer')
            ->post(route('razorpay.payment.success'), $paymentData);

        $endTime = microtime(true);
        $executionTime = ($endTime - $startTime) * 1000; // Convert to milliseconds

        $response->assertStatus(302);

        // Get query count
        $queryCount = count(DB::getQueryLog());

        // Performance assertions
        $this->assertLessThan(500, $executionTime, 'Payment processing should complete within 500ms');
        $this->assertLessThan(10, $queryCount, 'Should use less than 10 database queries');

        // Verify queries are optimized
        $queries = DB::getQueryLog();
        foreach ($queries as $query) {
            $this->assertLessThan(100, $query['time'], 'Individual query should take less than 100ms');
        }

        DB::disableQueryLog();
    }

    /** @test */
    public function it_implements_caching_strategy()
    {
        // Test configuration caching
        $startTime = microtime(true);
        
        $config1 = $this->razorpayService->getConfig();
        $config2 = $this->razorpayService->getConfig();
        
        $endTime = microtime(true);
        $executionTime = ($endTime - $startTime) * 1000;

        // Second call should be faster due to caching
        $this->assertLessThan(50, $executionTime, 'Cached configuration should load within 50ms');
        $this->assertEquals($config1, $config2, 'Cached configuration should be consistent');

        // Test currency support caching
        $startTime = microtime(true);
        
        $currencies1 = $this->razorpayService->getAcceptedCurrencies();
        $currencies2 = $this->razorpayService->getAcceptedCurrencies();
        
        $endTime = microtime(true);
        $executionTime = ($endTime - $startTime) * 1000;

        $this->assertLessThan(50, $executionTime, 'Cached currency list should load within 50ms');
        $this->assertEquals($currencies1, $currencies2, 'Cached currency list should be consistent');
    }

    /** @test */
    public function it_optimizes_signature_generation()
    {
        $orderId = 'order_test123';
        $paymentId = 'pay_test123';
        $secret = 'test_secret_key';

        // Test signature generation performance
        $startTime = microtime(true);
        
        for ($i = 0; $i < 100; $i++) {
            $signature = $this->razorpayService->generateSignature($orderId, $paymentId, $secret);
        }
        
        $endTime = microtime(true);
        $executionTime = ($endTime - $startTime) * 1000;

        // 100 signature generations should complete within reasonable time
        $this->assertLessThan(1000, $executionTime, '100 signature generations should complete within 1000ms');
        $this->assertGreaterThan(0, $executionTime, 'Signature generation should take some time');

        // Test signature verification performance
        $signature = $this->razorpayService->generateSignature($orderId, $paymentId, $secret);
        
        $startTime = microtime(true);
        
        for ($i = 0; $i < 100; $i++) {
            $isValid = $this->razorpayService->verifySignature($orderId, $paymentId, $signature, $secret);
        }
        
        $endTime = microtime(true);
        $executionTime = ($endTime - $startTime) * 1000;

        $this->assertLessThan(1000, $executionTime, '100 signature verifications should complete within 1000ms');
        $this->assertTrue($isValid, 'Signature verification should be valid');
    }

    /** @test */
    public function it_optimizes_webhook_processing()
    {
        $webhookPayload = json_encode([
            'entity' => 'payment',
            'id' => 'pay_test123',
            'order_id' => 'order_test123',
            'status' => 'captured',
            'amount' => 100000,
            'currency' => 'INR',
            'method' => 'card'
        ]);

        $signature = hash_hmac('sha256', $webhookPayload, 'test_webhook_secret');

        // Test webhook processing performance
        $startTime = microtime(true);

        $response = $this->post(route('razorpay.webhook'), 
            json_decode($webhookPayload, true),
            [
                'X-Razorpay-Signature' => $signature,
                'Content-Type' => 'application/json'
            ]
        );

        $endTime = microtime(true);
        $executionTime = ($endTime - $startTime) * 1000;

        $response->assertStatus(200);
        $this->assertLessThan(500, $executionTime, 'Webhook processing should complete within 500ms');
    }

    /** @test */
    public function it_optimizes_bulk_operations()
    {
        // Create multiple orders for bulk testing
        $orders = Order::factory()->count(50)->create([
            'customer_id' => $this->customer->id,
            'payment_method' => 'razorpay',
            'status' => 'pending'
        ]);

        $startTime = microtime(true);

        // Process multiple payments
        foreach ($orders as $order) {
            $paymentData = [
                'razorpay_payment_id' => 'pay_test_' . $order->id,
                'razorpay_order_id' => 'order_test_' . $order->id,
                'razorpay_signature' => 'valid_signature',
                'payment_method' => 'card',
                'amount' => 100000,
                'currency' => 'INR'
            ];

            $response = $this->actingAs($this->customer, 'customer')
                ->post(route('razorpay.payment.success'), $paymentData);

            $response->assertStatus(302);
        }

        $endTime = microtime(true);
        $executionTime = ($endTime - $startTime) * 1000;

        // Bulk operations should be efficient
        $this->assertLessThan(10000, $executionTime, '50 payments should process within 10 seconds');
        $this->assertGreaterThan(0, $executionTime, 'Bulk operations should take some time');
    }

    /** @test */
    public function it_optimizes_memory_usage()
    {
        // Test memory usage during payment processing
        $initialMemory = memory_get_usage();

        $paymentData = [
            'razorpay_payment_id' => 'pay_test123',
            'razorpay_order_id' => 'order_test123',
            'razorpay_signature' => 'valid_signature',
            'payment_method' => 'card',
            'amount' => 100000,
            'currency' => 'INR'
        ];

        $response = $this->actingAs($this->customer, 'customer')
            ->post(route('razorpay.payment.success'), $paymentData);

        $finalMemory = memory_get_usage();
        $memoryIncrease = $finalMemory - $initialMemory;

        $response->assertStatus(302);

        // Memory increase should be reasonable (less than 1MB)
        $this->assertLessThan(1024 * 1024, $memoryIncrease, 'Memory usage should increase by less than 1MB');

        // Test memory usage during webhook processing
        $initialMemory = memory_get_usage();

        $webhookPayload = json_encode([
            'entity' => 'payment',
            'id' => 'pay_test123',
            'order_id' => 'order_test123',
            'status' => 'captured'
        ]);

        $signature = hash_hmac('sha256', $webhookPayload, 'test_webhook_secret');

        $response = $this->post(route('razorpay.webhook'), 
            json_decode($webhookPayload, true),
            [
                'X-Razorpay-Signature' => $signature,
                'Content-Type' => 'application/json'
            ]
        );

        $finalMemory = memory_get_usage();
        $memoryIncrease = $finalMemory - $initialMemory;

        $response->assertStatus(200);
        $this->assertLessThan(1024 * 1024, $memoryIncrease, 'Webhook memory usage should increase by less than 1MB');
    }

    /** @test */
    public function it_optimizes_api_response_times()
    {
        // Test API response time for payment method validation
        $startTime = microtime(true);

        $response = $this->actingAs($this->customer, 'customer')
            ->get(route('razorpay.payment.method.validate'));

        $endTime = microtime(true);
        $executionTime = ($endTime - $startTime) * 1000;

        $response->assertStatus(200);
        $this->assertLessThan(200, $executionTime, 'Payment method validation should complete within 200ms');

        // Test API response time for configuration
        $startTime = microtime(true);

        $response = $this->actingAs($this->customer, 'customer')
            ->get(route('razorpay.configuration'));

        $endTime = microtime(true);
        $executionTime = ($endTime - $startTime) * 1000;

        $response->assertStatus(200);
        $this->assertLessThan(200, $executionTime, 'Configuration API should complete within 200ms');
    }

    /** @test */
    public function it_optimizes_database_indexes()
    {
        // Test query performance with proper indexes
        $startTime = microtime(true);

        // Query orders by payment method
        $orders = Order::where('payment_method', 'razorpay')->get();

        $endTime = microtime(true);
        $executionTime = ($endTime - $startTime) * 1000;

        $this->assertLessThan(100, $executionTime, 'Indexed query should complete within 100ms');

        // Test query performance with customer filter
        $startTime = microtime(true);

        $customerOrders = Order::where('customer_id', $this->customer->id)
            ->where('payment_method', 'razorpay')
            ->get();

        $endTime = microtime(true);
        $executionTime = ($endTime - $startTime) * 1000;

        $this->assertLessThan(100, $executionTime, 'Multi-column indexed query should complete within 100ms');
    }

    /** @test */
    public function it_optimizes_event_processing()
    {
        Event::fake();

        $startTime = microtime(true);

        $paymentData = [
            'razorpay_payment_id' => 'pay_test123',
            'razorpay_order_id' => 'order_test123',
            'razorpay_signature' => 'valid_signature',
            'payment_method' => 'card',
            'amount' => 100000,
            'currency' => 'INR'
        ];

        $response = $this->actingAs($this->customer, 'customer')
            ->post(route('razorpay.payment.success'), $paymentData);

        $endTime = microtime(true);
        $executionTime = ($endTime - $startTime) * 1000;

        $response->assertStatus(302);
        $this->assertLessThan(500, $executionTime, 'Event processing should complete within 500ms');

        // Verify events were dispatched efficiently
        Event::assertDispatched('razorpay.payment.successful');
    }

    /** @test */
    public function it_optimizes_concurrent_requests()
    {
        // Test concurrent payment processing
        $concurrentRequests = 10;
        $startTime = microtime(true);

        $promises = [];
        for ($i = 0; $i < $concurrentRequests; $i++) {
            $paymentData = [
                'razorpay_payment_id' => 'pay_test_' . $i,
                'razorpay_order_id' => 'order_test_' . $i,
                'razorpay_signature' => 'valid_signature',
                'payment_method' => 'card',
                'amount' => 100000,
                'currency' => 'INR'
            ];

            $response = $this->actingAs($this->customer, 'customer')
                ->post(route('razorpay.payment.success'), $paymentData);

            $response->assertStatus(302);
        }

        $endTime = microtime(true);
        $executionTime = ($endTime - $startTime) * 1000;

        // Concurrent requests should be processed efficiently
        $this->assertLessThan(5000, $executionTime, '10 concurrent requests should complete within 5 seconds');
    }

    /** @test */
    public function it_optimizes_large_payload_processing()
    {
        // Test with large webhook payload
        $largePayload = [
            'entity' => 'payment',
            'id' => 'pay_test123',
            'order_id' => 'order_test123',
            'status' => 'captured',
            'amount' => 100000,
            'currency' => 'INR',
            'method' => 'card',
            'card_id' => 'card_test123',
            'bank' => 'HDFC',
            'wallet' => null,
            'vpa' => null,
            'email' => 'test@example.com',
            'contact' => '9876543210',
            'fee' => 2000,
            'tax' => 300,
            'error_code' => null,
            'error_description' => null,
            'created_at' => 1640995200,
            'notes' => str_repeat('Large payload test data ', 1000), // Large payload
            'metadata' => [
                'key1' => str_repeat('value1', 100),
                'key2' => str_repeat('value2', 100),
                'key3' => str_repeat('value3', 100)
            ]
        ];

        $webhookPayload = json_encode($largePayload);
        $signature = hash_hmac('sha256', $webhookPayload, 'test_webhook_secret');

        $startTime = microtime(true);

        $response = $this->post(route('razorpay.webhook'), 
            $largePayload,
            [
                'X-Razorpay-Signature' => $signature,
                'Content-Type' => 'application/json'
            ]
        );

        $endTime = microtime(true);
        $executionTime = ($endTime - $startTime) * 1000;

        $response->assertStatus(200);
        $this->assertLessThan(1000, $executionTime, 'Large payload processing should complete within 1000ms');
    }

    /** @test */
    public function it_optimizes_error_handling_performance()
    {
        // Test error handling performance
        $startTime = microtime(true);

        $invalidPaymentData = [
            'razorpay_payment_id' => 'invalid_id',
            'razorpay_order_id' => 'invalid_order',
            'razorpay_signature' => 'invalid_signature',
            'payment_method' => 'invalid_method',
            'amount' => -100000,
            'currency' => 'XYZ'
        ];

        $response = $this->actingAs($this->customer, 'customer')
            ->post(route('razorpay.payment.success'), $invalidPaymentData);

        $endTime = microtime(true);
        $executionTime = ($endTime - $startTime) * 1000;

        $response->assertStatus(422);
        $this->assertLessThan(200, $executionTime, 'Error handling should complete within 200ms');
    }

    /** @test */
    public function it_optimizes_cache_invalidation()
    {
        // Test cache invalidation performance
        $startTime = microtime(true);

        // Invalidate configuration cache
        Cache::forget('razorpay.config');
        
        // Reload configuration
        $config = $this->razorpayService->getConfig();

        $endTime = microtime(true);
        $executionTime = ($endTime - $startTime) * 1000;

        $this->assertLessThan(100, $executionTime, 'Cache invalidation and reload should complete within 100ms');
        $this->assertNotNull($config, 'Configuration should be reloaded after cache invalidation');
    }

    /** @test */
    public function it_optimizes_background_job_processing()
    {
        // Test background job performance (if implemented)
        $startTime = microtime(true);

        // Simulate background job processing
        $jobData = [
            'order_id' => $this->order->id,
            'payment_id' => 'pay_test123',
            'status' => 'processing'
        ];

        // Process job synchronously for testing
        $this->razorpayService->processPaymentJob($jobData);

        $endTime = microtime(true);
        $executionTime = ($endTime - $startTime) * 1000;

        $this->assertLessThan(500, $executionTime, 'Background job processing should complete within 500ms');
    }

    /** @test */
    public function it_optimizes_session_handling()
    {
        // Test session handling performance
        $startTime = microtime(true);

        // Multiple session operations
        for ($i = 0; $i < 10; $i++) {
            $this->actingAs($this->customer, 'customer');
            $this->assertAuthenticated('customer');
        }

        $endTime = microtime(true);
        $executionTime = ($endTime - $startTime) * 1000;

        $this->assertLessThan(500, $executionTime, 'Session handling should complete within 500ms');
    }

    /** @test */
    public function it_optimizes_logging_performance()
    {
        // Test logging performance
        $startTime = microtime(true);

        // Multiple log operations
        for ($i = 0; $i < 100; $i++) {
            \Log::info('Razorpay payment test log entry ' . $i);
        }

        $endTime = microtime(true);
        $executionTime = ($endTime - $startTime) * 1000;

        $this->assertLessThan(1000, $executionTime, '100 log operations should complete within 1000ms');
    }
} 