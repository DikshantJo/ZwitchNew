<?php

namespace Webkul\Razorpay\Tests\Feature;

use Tests\TestCase;
use Webkul\Razorpay\Services\RazorpayService;
use Webkul\Sales\Models\Order;
use Webkul\Customer\Models\Customer;
use Webkul\Customer\Models\CustomerGroup;
use Webkul\Core\Models\Channel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Hash;

class SecurityTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $razorpayService;
    protected $customer;
    protected $order;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->razorpayService = new RazorpayService();
        
        // Create required customer group and channel first
        $customerGroup = CustomerGroup::factory()->create([
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

        // Create channel manually to avoid factory dependencies
        $channel = Channel::create([
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

        // Create customer with proper data
        $this->customer = Customer::create([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john.doe@example.com',
            'password' => Hash::make('password'),
            'customer_group_id' => 2,
            'channel_id' => 1,
            'status' => 1
        ]);

        // Create order
        $this->order = Order::create([
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
    public function it_validates_webhook_signature_security()
    {
        $webhookPayload = json_encode([
            'entity' => 'payment',
            'id' => 'pay_test123',
            'order_id' => 'order_test123',
            'status' => 'captured'
        ]);

        // Test with valid signature
        $validSignature = hash_hmac('sha256', $webhookPayload, 'test_webhook_secret');
        $this->assertTrue($this->razorpayService->verifyWebhookSignature($webhookPayload, $validSignature));

        // Test with invalid signature
        $invalidSignature = 'invalid_signature_hash';
        $this->assertFalse($this->razorpayService->verifyWebhookSignature($webhookPayload, $invalidSignature));

        // Test with tampered payload
        $tamperedPayload = json_encode([
            'entity' => 'payment',
            'id' => 'pay_test123',
            'order_id' => 'order_test123',
            'status' => 'failed' // Changed from 'captured' to 'failed'
        ]);
        $this->assertFalse($this->razorpayService->verifyWebhookSignature($tamperedPayload, $validSignature));

        // Test with empty payload
        $emptyPayload = '';
        $this->assertFalse($this->razorpayService->verifyWebhookSignature($emptyPayload, $validSignature));

        // Test with empty signature
        $emptySignature = '';
        $this->assertFalse($this->razorpayService->verifyWebhookSignature($webhookPayload, $emptySignature));
    }

    /** @test */
    public function it_validates_payment_signature_security()
    {
        $orderId = 'order_test123';
        $paymentId = 'pay_test123';
        $secret = 'test_key_secret';

        // Test with valid signature
        $validSignature = $this->razorpayService->generateSignature($orderId, $paymentId, $secret);
        $this->assertTrue($this->razorpayService->verifySignature($orderId, $paymentId, $validSignature, $secret));

        // Test with invalid signature
        $invalidSignature = 'invalid_signature_hash';
        $this->assertFalse($this->razorpayService->verifySignature($orderId, $paymentId, $invalidSignature, $secret));

        // Test with tampered order ID
        $tamperedOrderId = 'order_tampered123';
        $this->assertFalse($this->razorpayService->verifySignature($tamperedOrderId, $paymentId, $validSignature, $secret));

        // Test with tampered payment ID
        $tamperedPaymentId = 'pay_tampered123';
        $this->assertFalse($this->razorpayService->verifySignature($orderId, $tamperedPaymentId, $validSignature, $secret));

        // Test with wrong secret
        $wrongSecret = 'wrong_key_secret';
        $this->assertFalse($this->razorpayService->verifySignature($orderId, $paymentId, $validSignature, $wrongSecret));
    }

    /** @test */
    public function it_prevents_csrf_attacks()
    {
        $paymentData = [
            'razorpay_payment_id' => 'pay_test123',
            'razorpay_order_id' => 'order_test123',
            'razorpay_signature' => 'valid_signature',
            'payment_method' => 'card',
            'amount' => 100000,
            'currency' => 'INR'
        ];

        // Test without CSRF token (should fail)
        $response = $this->post(route('razorpay.payment.success'), $paymentData);
        $response->assertStatus(419); // CSRF token mismatch

        // Test with invalid CSRF token (should fail)
        $response = $this->post(route('razorpay.payment.success'), $paymentData, [
            'X-CSRF-TOKEN' => 'invalid_token'
        ]);
        $response->assertStatus(419);
    }

    /** @test */
    public function it_validates_input_sanitization()
    {
        // Test with malicious input
        $maliciousData = [
            'razorpay_payment_id' => '<script>alert("xss")</script>',
            'razorpay_order_id' => 'order_test123',
            'razorpay_signature' => 'valid_signature',
            'payment_method' => 'card',
            'amount' => 100000,
            'currency' => 'INR'
        ];

        $response = $this->actingAs($this->customer, 'customer')
            ->post(route('razorpay.payment.success'), $maliciousData);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['razorpay_payment_id']);

        // Test with SQL injection attempt
        $sqlInjectionData = [
            'razorpay_payment_id' => "'; DROP TABLE orders; --",
            'razorpay_order_id' => 'order_test123',
            'razorpay_signature' => 'valid_signature',
            'payment_method' => 'card',
            'amount' => 100000,
            'currency' => 'INR'
        ];

        $response = $this->actingAs($this->customer, 'customer')
            ->post(route('razorpay.payment.success'), $sqlInjectionData);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['razorpay_payment_id']);
    }

    /** @test */
    public function it_validates_amount_verification()
    {
        $order = Order::factory()->create([
            'customer_id' => $this->customer->id,
            'payment_method' => 'razorpay',
            'grand_total' => 1000.00,
            'status' => 'pending'
        ]);

        // Test with amount mismatch
        $paymentData = [
            'razorpay_payment_id' => 'pay_test123',
            'razorpay_order_id' => 'order_test123',
            'razorpay_signature' => 'valid_signature',
            'payment_method' => 'card',
            'amount' => 50000, // 500.00 in paise (mismatch with order amount)
            'currency' => 'INR'
        ];

        $response = $this->actingAs($this->customer, 'customer')
            ->post(route('razorpay.payment.success'), $paymentData);

        $response->assertStatus(400);
        $response->assertJson(['error' => 'Payment amount mismatch']);

        // Test with negative amount
        $negativeAmountData = [
            'razorpay_payment_id' => 'pay_test123',
            'razorpay_order_id' => 'order_test123',
            'razorpay_signature' => 'valid_signature',
            'payment_method' => 'card',
            'amount' => -100000,
            'currency' => 'INR'
        ];

        $response = $this->actingAs($this->customer, 'customer')
            ->post(route('razorpay.payment.success'), $negativeAmountData);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['amount']);
    }

    /** @test */
    public function it_validates_currency_verification()
    {
        $order = Order::factory()->create([
            'customer_id' => $this->customer->id,
            'payment_method' => 'razorpay',
            'grand_total' => 1000.00,
            'currency_code' => 'INR',
            'status' => 'pending'
        ]);

        // Test with currency mismatch
        $paymentData = [
            'razorpay_payment_id' => 'pay_test123',
            'razorpay_order_id' => 'order_test123',
            'razorpay_signature' => 'valid_signature',
            'payment_method' => 'card',
            'amount' => 100000,
            'currency' => 'USD' // Mismatch with order currency
        ];

        $response = $this->actingAs($this->customer, 'customer')
            ->post(route('razorpay.payment.success'), $paymentData);

        $response->assertStatus(400);
        $response->assertJson(['error' => 'Payment currency mismatch']);

        // Test with unsupported currency
        $unsupportedCurrencyData = [
            'razorpay_payment_id' => 'pay_test123',
            'razorpay_order_id' => 'order_test123',
            'razorpay_signature' => 'valid_signature',
            'payment_method' => 'card',
            'amount' => 100000,
            'currency' => 'XYZ' // Unsupported currency
        ];

        $response = $this->actingAs($this->customer, 'customer')
            ->post(route('razorpay.payment.success'), $unsupportedCurrencyData);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['currency']);
    }

    /** @test */
    public function it_validates_order_ownership()
    {
        // Create order for different customer
        $otherCustomer = Customer::factory()->create([
            'email' => 'other@example.com'
        ]);

        $otherOrder = Order::factory()->create([
            'customer_id' => $otherCustomer->id,
            'payment_method' => 'razorpay',
            'grand_total' => 1000.00,
            'status' => 'pending'
        ]);

        $paymentData = [
            'razorpay_payment_id' => 'pay_test123',
            'razorpay_order_id' => 'order_test123',
            'razorpay_signature' => 'valid_signature',
            'payment_method' => 'card',
            'amount' => 100000,
            'currency' => 'INR'
        ];

        // Test with unauthorized access
        $response = $this->actingAs($this->customer, 'customer')
            ->post(route('razorpay.payment.success'), $paymentData);

        $response->assertStatus(403);
        $response->assertJson(['error' => 'Unauthorized access to order']);
    }

    /** @test */
    public function it_validates_api_key_security()
    {
        // Test with empty API key
        Config::set('paymentmethods.razorpay.key_id', '');
        Config::set('paymentmethods.razorpay.key_secret', '');

        $this->assertFalse($this->razorpayService->hasValidCredentials());

        // Test with invalid API key format
        Config::set('paymentmethods.razorpay.key_id', 'invalid_key_format');
        Config::set('paymentmethods.razorpay.key_secret', 'invalid_secret_format');

        $this->assertFalse($this->razorpayService->hasValidCredentials());

        // Test with valid API key
        Config::set('paymentmethods.razorpay.key_id', 'rzp_test_valid_key');
        Config::set('paymentmethods.razorpay.key_secret', 'valid_secret_key');

        $this->assertTrue($this->razorpayService->hasValidCredentials());
    }

    /** @test */
    public function it_validates_webhook_secret_security()
    {
        // Test with empty webhook secret
        Config::set('paymentmethods.razorpay.webhook_secret', '');

        $webhookPayload = json_encode(['test' => 'data']);
        $signature = hash_hmac('sha256', $webhookPayload, '');

        $this->expectException(\InvalidArgumentException::class);
        $this->razorpayService->verifyWebhookSignature($webhookPayload, $signature, '');

        // Test with weak webhook secret
        Config::set('paymentmethods.razorpay.webhook_secret', 'weak');

        $this->expectException(\InvalidArgumentException::class);
        $this->razorpayService->verifyWebhookSignature($webhookPayload, $signature, 'weak');
    }

    /** @test */
    public function it_validates_payment_id_format()
    {
        // Test with invalid payment ID format
        $invalidPaymentIds = [
            '',
            'invalid_format',
            'pay_',
            'pay_test_',
            '<script>alert("xss")</script>',
            'pay_test123<script>alert("xss")</script>'
        ];

        foreach ($invalidPaymentIds as $invalidId) {
            $paymentData = [
                'razorpay_payment_id' => $invalidId,
                'razorpay_order_id' => 'order_test123',
                'razorpay_signature' => 'valid_signature',
                'payment_method' => 'card',
                'amount' => 100000,
                'currency' => 'INR'
            ];

            $response = $this->actingAs($this->customer, 'customer')
                ->post(route('razorpay.payment.success'), $paymentData);

            $response->assertStatus(422);
            $response->assertJsonValidationErrors(['razorpay_payment_id']);
        }
    }

    /** @test */
    public function it_validates_order_id_format()
    {
        // Test with invalid order ID format
        $invalidOrderIds = [
            '',
            'invalid_format',
            'order_',
            'order_test_',
            '<script>alert("xss")</script>',
            'order_test123<script>alert("xss")</script>'
        ];

        foreach ($invalidOrderIds as $invalidId) {
            $paymentData = [
                'razorpay_payment_id' => 'pay_test123',
                'razorpay_order_id' => $invalidId,
                'razorpay_signature' => 'valid_signature',
                'payment_method' => 'card',
                'amount' => 100000,
                'currency' => 'INR'
            ];

            $response = $this->actingAs($this->customer, 'customer')
                ->post(route('razorpay.payment.success'), $paymentData);

            $response->assertStatus(422);
            $response->assertJsonValidationErrors(['razorpay_order_id']);
        }
    }

    /** @test */
    public function it_validates_signature_format()
    {
        // Test with invalid signature format
        $invalidSignatures = [
            '',
            'invalid_format',
            'signature_',
            '<script>alert("xss")</script>',
            'valid_signature<script>alert("xss")</script>'
        ];

        foreach ($invalidSignatures as $invalidSignature) {
            $paymentData = [
                'razorpay_payment_id' => 'pay_test123',
                'razorpay_order_id' => 'order_test123',
                'razorpay_signature' => $invalidSignature,
                'payment_method' => 'card',
                'amount' => 100000,
                'currency' => 'INR'
            ];

            $response = $this->actingAs($this->customer, 'customer')
                ->post(route('razorpay.payment.success'), $paymentData);

            $response->assertStatus(422);
            $response->assertJsonValidationErrors(['razorpay_signature']);
        }
    }

    /** @test */
    public function it_validates_payment_method_enumeration()
    {
        // Test with invalid payment method
        $invalidPaymentMethods = [
            '',
            'invalid_method',
            'card<script>alert("xss")</script>',
            'upi; DROP TABLE orders; --'
        ];

        foreach ($invalidPaymentMethods as $invalidMethod) {
            $paymentData = [
                'razorpay_payment_id' => 'pay_test123',
                'razorpay_order_id' => 'order_test123',
                'razorpay_signature' => 'valid_signature',
                'payment_method' => $invalidMethod,
                'amount' => 100000,
                'currency' => 'INR'
            ];

            $response = $this->actingAs($this->customer, 'customer')
                ->post(route('razorpay.payment.success'), $paymentData);

            $response->assertStatus(422);
            $response->assertJsonValidationErrors(['payment_method']);
        }
    }

    /** @test */
    public function it_validates_rate_limiting()
    {
        $paymentData = [
            'razorpay_payment_id' => 'pay_test123',
            'razorpay_order_id' => 'order_test123',
            'razorpay_signature' => 'valid_signature',
            'payment_method' => 'card',
            'amount' => 100000,
            'currency' => 'INR'
        ];

        // Send multiple requests rapidly
        for ($i = 0; $i < 10; $i++) {
            $response = $this->actingAs($this->customer, 'customer')
                ->post(route('razorpay.payment.success'), $paymentData);

            if ($i < 5) {
                $response->assertStatus(400); // Expected error for invalid signature
            } else {
                $response->assertStatus(429); // Too Many Requests
            }
        }
    }

    /** @test */
    public function it_validates_session_security()
    {
        // Test without authentication
        $paymentData = [
            'razorpay_payment_id' => 'pay_test123',
            'razorpay_order_id' => 'order_test123',
            'razorpay_signature' => 'valid_signature',
            'payment_method' => 'card',
            'amount' => 100000,
            'currency' => 'INR'
        ];

        $response = $this->post(route('razorpay.payment.success'), $paymentData);
        $response->assertStatus(401); // Unauthorized

        // Test with expired session
        $this->actingAs($this->customer, 'customer');
        
        // Simulate session expiration
        auth()->logout();

        $response = $this->post(route('razorpay.payment.success'), $paymentData);
        $response->assertStatus(401); // Unauthorized
    }

    /** @test */
    public function it_validates_https_enforcement()
    {
        // Test in production environment
        app()->detectEnvironment(function () {
            return 'production';
        });

        $paymentData = [
            'razorpay_payment_id' => 'pay_test123',
            'razorpay_order_id' => 'order_test123',
            'razorpay_signature' => 'valid_signature',
            'payment_method' => 'card',
            'amount' => 100000,
            'currency' => 'INR'
        ];

        // Simulate HTTP request
        $this->get('http://example.com/razorpay/payment/success');
        
        // Should redirect to HTTPS
        $response = $this->actingAs($this->customer, 'customer')
            ->post(route('razorpay.payment.success'), $paymentData);

        // In production, should enforce HTTPS
        if (app()->environment('production')) {
            $this->assertTrue(request()->isSecure());
        }
    }

    /** @test */
    public function it_validates_sql_injection_prevention()
    {
        // Test with SQL injection attempts
        $sqlInjectionAttempts = [
            "'; DROP TABLE orders; --",
            "' OR '1'='1",
            "'; UPDATE customers SET email='hacked@example.com'; --",
            "'; INSERT INTO orders VALUES (999, 'hacked'); --"
        ];

        foreach ($sqlInjectionAttempts as $attempt) {
            $paymentData = [
                'razorpay_payment_id' => $attempt,
                'razorpay_order_id' => 'order_test123',
                'razorpay_signature' => 'valid_signature',
                'payment_method' => 'card',
                'amount' => 100000,
                'currency' => 'INR'
            ];

            $response = $this->actingAs($this->customer, 'customer')
                ->post(route('razorpay.payment.success'), $paymentData);

            $response->assertStatus(422);
            $response->assertJsonValidationErrors(['razorpay_payment_id']);

            // Verify no SQL injection occurred
            $this->assertDatabaseMissing('orders', ['id' => 999]);
        }
    }

    /** @test */
    public function it_validates_xss_prevention()
    {
        // Test with XSS attempts
        $xssAttempts = [
            '<script>alert("xss")</script>',
            'javascript:alert("xss")',
            '<img src="x" onerror="alert(\'xss\')">',
            '"><script>alert("xss")</script>'
        ];

        foreach ($xssAttempts as $attempt) {
            $paymentData = [
                'razorpay_payment_id' => $attempt,
                'razorpay_order_id' => 'order_test123',
                'razorpay_signature' => 'valid_signature',
                'payment_method' => 'card',
                'amount' => 100000,
                'currency' => 'INR'
            ];

            $response = $this->actingAs($this->customer, 'customer')
                ->post(route('razorpay.payment.success'), $paymentData);

            $response->assertStatus(422);
            $response->assertJsonValidationErrors(['razorpay_payment_id']);

            // Verify XSS was prevented
            $this->assertStringNotContainsString('<script>', $response->getContent());
        }
    }

    /** @test */
    public function it_validates_parameter_pollution_prevention()
    {
        // Test with duplicate parameters
        $paymentData = [
            'razorpay_payment_id' => 'pay_test123',
            'razorpay_payment_id' => 'pay_test456', // Duplicate parameter
            'razorpay_order_id' => 'order_test123',
            'razorpay_signature' => 'valid_signature',
            'payment_method' => 'card',
            'amount' => 100000,
            'currency' => 'INR'
        ];

        $response = $this->actingAs($this->customer, 'customer')
            ->post(route('razorpay.payment.success'), $paymentData);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['razorpay_payment_id']);
    }

    /** @test */
    public function it_validates_http_method_restriction()
    {
        $paymentData = [
            'razorpay_payment_id' => 'pay_test123',
            'razorpay_order_id' => 'order_test123',
            'razorpay_signature' => 'valid_signature',
            'payment_method' => 'card',
            'amount' => 100000,
            'currency' => 'INR'
        ];

        // Test with GET method (should fail)
        $response = $this->actingAs($this->customer, 'customer')
            ->get(route('razorpay.payment.success'), $paymentData);
        $response->assertStatus(405); // Method Not Allowed

        // Test with PUT method (should fail)
        $response = $this->actingAs($this->customer, 'customer')
            ->put(route('razorpay.payment.success'), $paymentData);
        $response->assertStatus(405); // Method Not Allowed

        // Test with DELETE method (should fail)
        $response = $this->actingAs($this->customer, 'customer')
            ->delete(route('razorpay.payment.success'), $paymentData);
        $response->assertStatus(405); // Method Not Allowed

        // Test with POST method (should work)
        $response = $this->actingAs($this->customer, 'customer')
            ->post(route('razorpay.payment.success'), $paymentData);
        $response->assertStatus(400); // Expected error for invalid signature, but method allowed
    }
} 