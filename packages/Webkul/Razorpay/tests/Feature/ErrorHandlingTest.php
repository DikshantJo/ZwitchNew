<?php

namespace Webkul\Razorpay\Tests\Feature;

use Tests\TestCase;
use Webkul\Razorpay\Services\RazorpayService;
use Webkul\Sales\Models\Order;
use Webkul\Customer\Models\Customer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;

class ErrorHandlingTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $razorpayService;
    protected $customer;
    protected $order;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->razorpayService = new RazorpayService();
        
        // Create test customer
        $this->customer = Customer::factory()->create([
            'email' => 'test@example.com',
            'first_name' => 'Test',
            'last_name' => 'Customer'
        ]);

        // Create test order
        $this->order = Order::factory()->create([
            'customer_id' => $this->customer->id,
            'payment_method' => 'razorpay',
            'grand_total' => 1000.00,
            'status' => 'pending'
        ]);

        // Configure Razorpay for testing
        Config::set('paymentmethods.razorpay.webhook_secret', 'test_webhook_secret');
        Config::set('paymentmethods.razorpay.key_secret', 'test_key_secret');
        Config::set('paymentmethods.razorpay.sandbox', true);
    }

    /** @test */
    public function it_handles_network_connectivity_errors()
    {
        // Test network timeout
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Network connection timeout');

        $this->razorpayService->createOrder(1000.00, 'INR', 'receipt_test123');

        // Test DNS resolution failure
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Unable to resolve host');

        $this->razorpayService->fetchPayment('pay_test123');

        // Test connection refused
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Connection refused');

        $this->razorpayService->fetchOrder('order_test123');
    }

    /** @test */
    public function it_handles_api_rate_limiting_errors()
    {
        // Test rate limiting
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Rate limit exceeded. Please try again later.');

        // Simulate rate limiting by making multiple rapid requests
        for ($i = 0; $i < 100; $i++) {
            try {
                $this->razorpayService->createOrder(1000.00, 'INR', 'receipt_test' . $i);
            } catch (\Exception $e) {
                if (strpos($e->getMessage(), 'Rate limit') !== false) {
                    throw $e;
                }
            }
        }
    }

    /** @test */
    public function it_handles_invalid_api_credentials()
    {
        // Test with invalid key ID
        Config::set('paymentmethods.razorpay.key_id', 'invalid_key_id');

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Invalid API credentials');

        $this->razorpayService->createOrder(1000.00, 'INR', 'receipt_test123');

        // Test with invalid key secret
        Config::set('paymentmethods.razorpay.key_id', 'valid_key_id');
        Config::set('paymentmethods.razorpay.key_secret', 'invalid_key_secret');

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Invalid API credentials');

        $this->razorpayService->fetchPayment('pay_test123');
    }

    /** @test */
    public function it_handles_payment_declined_errors()
    {
        $order = Order::factory()->create([
            'customer_id' => $this->customer->id,
            'payment_method' => 'razorpay',
            'grand_total' => 1000.00,
            'status' => 'pending'
        ]);

        $paymentData = [
            'razorpay_payment_id' => 'pay_test123',
            'razorpay_order_id' => 'order_test123',
            'razorpay_signature' => 'valid_signature',
            'payment_method' => 'card',
            'error_code' => 'CARD_DECLINED',
            'error_description' => 'Card was declined by the bank'
        ];

        $response = $this->actingAs($this->customer, 'customer')
            ->post(route('razorpay.payment.failure'), $paymentData);

        $response->assertStatus(302);
        
        // Verify order status updated
        $order->refresh();
        $this->assertEquals('canceled', $order->status);

        // Verify error logged
        Log::shouldReceive('error')->with(
            'Razorpay payment declined',
            \Mockery::type('array')
        );
    }

    /** @test */
    public function it_handles_insufficient_funds_errors()
    {
        $order = Order::factory()->create([
            'customer_id' => $this->customer->id,
            'payment_method' => 'razorpay',
            'grand_total' => 1000.00,
            'status' => 'pending'
        ]);

        $paymentData = [
            'razorpay_payment_id' => 'pay_test123',
            'razorpay_order_id' => 'order_test123',
            'razorpay_signature' => 'valid_signature',
            'payment_method' => 'card',
            'error_code' => 'INSUFFICIENT_FUNDS',
            'error_description' => 'Insufficient funds in the account'
        ];

        $response = $this->actingAs($this->customer, 'customer')
            ->post(route('razorpay.payment.failure'), $paymentData);

        $response->assertStatus(302);
        
        // Verify order status updated
        $order->refresh();
        $this->assertEquals('canceled', $order->status);

        // Verify user-friendly error message
        $response->assertSessionHas('error', 'Payment failed: Insufficient funds in your account. Please try with a different payment method.');
    }

    /** @test */
    public function it_handles_upi_payment_failures()
    {
        $order = Order::factory()->create([
            'customer_id' => $this->customer->id,
            'payment_method' => 'razorpay',
            'grand_total' => 1000.00,
            'status' => 'pending'
        ]);

        $paymentData = [
            'razorpay_payment_id' => 'pay_test123',
            'razorpay_order_id' => 'order_test123',
            'razorpay_signature' => 'valid_signature',
            'payment_method' => 'upi',
            'error_code' => 'UPI_PAYMENT_FAILED',
            'error_description' => 'UPI payment failed due to invalid VPA'
        ];

        $response = $this->actingAs($this->customer, 'customer')
            ->post(route('razorpay.payment.failure'), $paymentData);

        $response->assertStatus(302);
        
        // Verify order status updated
        $order->refresh();
        $this->assertEquals('canceled', $order->status);

        // Verify user-friendly error message
        $response->assertSessionHas('error', 'UPI payment failed. Please check your VPA and try again.');
    }

    /** @test */
    public function it_handles_netbanking_failures()
    {
        $order = Order::factory()->create([
            'customer_id' => $this->customer->id,
            'payment_method' => 'razorpay',
            'grand_total' => 1000.00,
            'status' => 'pending'
        ]);

        $paymentData = [
            'razorpay_payment_id' => 'pay_test123',
            'razorpay_order_id' => 'order_test123',
            'razorpay_signature' => 'valid_signature',
            'payment_method' => 'netbanking',
            'error_code' => 'NETBANKING_FAILED',
            'error_description' => 'Netbanking payment failed'
        ];

        $response = $this->actingAs($this->customer, 'customer')
            ->post(route('razorpay.payment.failure'), $paymentData);

        $response->assertStatus(302);
        
        // Verify order status updated
        $order->refresh();
        $this->assertEquals('canceled', $order->status);

        // Verify user-friendly error message
        $response->assertSessionHas('error', 'Netbanking payment failed. Please try again or use a different payment method.');
    }

    /** @test */
    public function it_handles_wallet_payment_failures()
    {
        $order = Order::factory()->create([
            'customer_id' => $this->customer->id,
            'payment_method' => 'razorpay',
            'grand_total' => 1000.00,
            'status' => 'pending'
        ]);

        $paymentData = [
            'razorpay_payment_id' => 'pay_test123',
            'razorpay_order_id' => 'order_test123',
            'razorpay_signature' => 'valid_signature',
            'payment_method' => 'wallet',
            'error_code' => 'WALLET_INSUFFICIENT_BALANCE',
            'error_description' => 'Insufficient wallet balance'
        ];

        $response = $this->actingAs($this->customer, 'customer')
            ->post(route('razorpay.payment.failure'), $paymentData);

        $response->assertStatus(302);
        
        // Verify order status updated
        $order->refresh();
        $this->assertEquals('canceled', $order->status);

        // Verify user-friendly error message
        $response->assertSessionHas('error', 'Insufficient wallet balance. Please try with a different payment method.');
    }

    /** @test */
    public function it_handles_emi_payment_failures()
    {
        $order = Order::factory()->create([
            'customer_id' => $this->customer->id,
            'payment_method' => 'razorpay',
            'grand_total' => 1000.00,
            'status' => 'pending'
        ]);

        $paymentData = [
            'razorpay_payment_id' => 'pay_test123',
            'razorpay_order_id' => 'order_test123',
            'razorpay_signature' => 'valid_signature',
            'payment_method' => 'emi',
            'error_code' => 'EMI_NOT_AVAILABLE',
            'error_description' => 'EMI not available for this card'
        ];

        $response = $this->actingAs($this->customer, 'customer')
            ->post(route('razorpay.payment.failure'), $paymentData);

        $response->assertStatus(302);
        
        // Verify order status updated
        $order->refresh();
        $this->assertEquals('canceled', $order->status);

        // Verify user-friendly error message
        $response->assertSessionHas('error', 'EMI is not available for your card. Please try with a different payment method.');
    }

    /** @test */
    public function it_handles_signature_verification_failures()
    {
        $order = Order::factory()->create([
            'customer_id' => $this->customer->id,
            'payment_method' => 'razorpay',
            'grand_total' => 1000.00,
            'status' => 'pending'
        ]);

        $paymentData = [
            'razorpay_payment_id' => 'pay_test123',
            'razorpay_order_id' => 'order_test123',
            'razorpay_signature' => 'invalid_signature',
            'payment_method' => 'card',
            'amount' => 100000,
            'currency' => 'INR'
        ];

        $response = $this->actingAs($this->customer, 'customer')
            ->post(route('razorpay.payment.success'), $paymentData);

        $response->assertStatus(400);
        $response->assertJson(['error' => 'Invalid payment signature']);

        // Verify order status unchanged
        $order->refresh();
        $this->assertEquals('pending', $order->status);

        // Verify security alert logged
        Log::shouldReceive('warning')->with(
            'Razorpay signature verification failed',
            \Mockery::type('array')
        );
    }

    /** @test */
    public function it_handles_webhook_processing_errors()
    {
        $webhookPayload = json_encode([
            'entity' => 'payment',
            'id' => 'pay_test123',
            'order_id' => 'order_test123',
            'status' => 'captured'
        ]);

        $invalidSignature = 'invalid_webhook_signature';

        $response = $this->post(route('razorpay.webhook'), 
            json_decode($webhookPayload, true),
            [
                'X-Razorpay-Signature' => $invalidSignature,
                'Content-Type' => 'application/json'
            ]
        );

        $response->assertStatus(400);
        $response->assertJson(['error' => 'Invalid webhook signature']);

        // Verify security alert logged
        Log::shouldReceive('warning')->with(
            'Razorpay webhook signature verification failed',
            \Mockery::type('array')
        );
    }

    /** @test */
    public function it_handles_missing_order_errors()
    {
        $paymentData = [
            'razorpay_payment_id' => 'pay_test123',
            'razorpay_order_id' => 'non_existent_order',
            'razorpay_signature' => 'valid_signature',
            'payment_method' => 'card',
            'amount' => 100000,
            'currency' => 'INR'
        ];

        $response = $this->actingAs($this->customer, 'customer')
            ->post(route('razorpay.payment.success'), $paymentData);

        $response->assertStatus(404);
        $response->assertJson(['error' => 'Order not found']);

        // Verify error logged
        Log::shouldReceive('error')->with(
            'Razorpay payment for non-existent order',
            \Mockery::type('array')
        );
    }

    /** @test */
    public function it_handles_duplicate_payment_errors()
    {
        // Create order that's already paid
        $paidOrder = Order::factory()->create([
            'customer_id' => $this->customer->id,
            'payment_method' => 'razorpay',
            'grand_total' => 1000.00,
            'status' => 'processing' // Already paid
        ]);

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

        $response->assertStatus(400);
        $response->assertJson(['error' => 'Order already processed']);

        // Verify error logged
        Log::shouldReceive('warning')->with(
            'Duplicate payment attempt for order',
            \Mockery::type('array')
        );
    }

    /** @test */
    public function it_handles_expired_order_errors()
    {
        // Create expired order
        $expiredOrder = Order::factory()->create([
            'customer_id' => $this->customer->id,
            'payment_method' => 'razorpay',
            'grand_total' => 1000.00,
            'status' => 'pending',
            'created_at' => now()->subHours(25) // Expired order
        ]);

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

        $response->assertStatus(400);
        $response->assertJson(['error' => 'Order has expired']);

        // Verify error logged
        Log::shouldReceive('warning')->with(
            'Payment attempt for expired order',
            \Mockery::type('array')
        );
    }

    /** @test */
    public function it_handles_amount_mismatch_errors()
    {
        $order = Order::factory()->create([
            'customer_id' => $this->customer->id,
            'payment_method' => 'razorpay',
            'grand_total' => 1000.00,
            'status' => 'pending'
        ]);

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

        // Verify error logged
        Log::shouldReceive('warning')->with(
            'Razorpay payment amount mismatch',
            \Mockery::type('array')
        );
    }

    /** @test */
    public function it_handles_currency_mismatch_errors()
    {
        $order = Order::factory()->create([
            'customer_id' => $this->customer->id,
            'payment_method' => 'razorpay',
            'grand_total' => 1000.00,
            'currency_code' => 'INR',
            'status' => 'pending'
        ]);

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

        // Verify error logged
        Log::shouldReceive('warning')->with(
            'Razorpay payment currency mismatch',
            \Mockery::type('array')
        );
    }

    /** @test */
    public function it_handles_refund_processing_errors()
    {
        // Test refund for non-existent payment
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Payment not found');

        $this->razorpayService->processRefund('non_existent_payment', 1000.00, 'Test refund');

        // Test refund amount greater than payment amount
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Refund amount cannot exceed payment amount');

        $this->razorpayService->processRefund('pay_test123', 2000.00, 'Test refund');

        // Test refund for already refunded payment
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Payment already refunded');

        $this->razorpayService->processRefund('pay_test123', 1000.00, 'Test refund');
    }

    /** @test */
    public function it_handles_validation_errors()
    {
        // Test with missing required fields
        $invalidData = [
            'payment_method' => 'card'
            // Missing required fields
        ];

        $response = $this->actingAs($this->customer, 'customer')
            ->post(route('razorpay.payment.success'), $invalidData);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            'razorpay_payment_id',
            'razorpay_order_id',
            'razorpay_signature',
            'amount',
            'currency'
        ]);

        // Test with invalid data types
        $invalidTypeData = [
            'razorpay_payment_id' => 123, // Should be string
            'razorpay_order_id' => 456, // Should be string
            'razorpay_signature' => 789, // Should be string
            'payment_method' => 'card',
            'amount' => 'invalid_amount', // Should be numeric
            'currency' => 'INR'
        ];

        $response = $this->actingAs($this->customer, 'customer')
            ->post(route('razorpay.payment.success'), $invalidTypeData);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            'razorpay_payment_id',
            'razorpay_order_id',
            'razorpay_signature',
            'amount'
        ]);
    }

    /** @test */
    public function it_handles_timeout_errors()
    {
        // Test API timeout
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Request timeout');

        $this->razorpayService->createOrder(1000.00, 'INR', 'receipt_test123');

        // Test webhook timeout
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

        $response->assertStatus(408); // Request Timeout
    }

    /** @test */
    public function it_handles_server_errors()
    {
        // Test 500 server error
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Internal server error');

        $this->razorpayService->createOrder(1000.00, 'INR', 'receipt_test123');

        // Test 502 bad gateway
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Bad gateway');

        $this->razorpayService->fetchPayment('pay_test123');

        // Test 503 service unavailable
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Service temporarily unavailable');

        $this->razorpayService->fetchOrder('order_test123');
    }

    /** @test */
    public function it_handles_retry_mechanisms()
    {
        // Test automatic retry for temporary failures
        $attempts = 0;
        $maxAttempts = 3;

        while ($attempts < $maxAttempts) {
            try {
                $this->razorpayService->createOrder(1000.00, 'INR', 'receipt_test123');
                break; // Success
            } catch (\Exception $e) {
                $attempts++;
                if ($attempts >= $maxAttempts) {
                    throw $e; // Max attempts reached
                }
                // Wait before retry
                sleep(1);
            }
        }

        $this->assertLessThanOrEqual($maxAttempts, $attempts);
    }

    /** @test */
    public function it_provides_user_friendly_error_messages()
    {
        $errorMessages = [
            'CARD_DECLINED' => 'Your card was declined. Please try with a different card.',
            'INSUFFICIENT_FUNDS' => 'Insufficient funds in your account. Please try with a different payment method.',
            'UPI_PAYMENT_FAILED' => 'UPI payment failed. Please check your VPA and try again.',
            'NETBANKING_FAILED' => 'Netbanking payment failed. Please try again or use a different payment method.',
            'WALLET_INSUFFICIENT_BALANCE' => 'Insufficient wallet balance. Please try with a different payment method.',
            'EMI_NOT_AVAILABLE' => 'EMI is not available for your card. Please try with a different payment method.',
            'PAYMENT_DECLINED' => 'Payment was declined. Please try again or contact support.',
            'TIMEOUT' => 'Payment request timed out. Please try again.',
            'NETWORK_ERROR' => 'Network error occurred. Please check your connection and try again.',
            'UNKNOWN_ERROR' => 'An unexpected error occurred. Please try again or contact support.'
        ];

        foreach ($errorMessages as $errorCode => $expectedMessage) {
            $message = $this->razorpayService->getUserFriendlyErrorMessage($errorCode);
            $this->assertEquals($expectedMessage, $message);
        }
    }

    /** @test */
    public function it_logs_errors_appropriately()
    {
        // Test error logging for different error types
        Log::shouldReceive('error')->with(
            'Razorpay API error',
            \Mockery::type('array')
        );

        Log::shouldReceive('warning')->with(
            'Razorpay payment warning',
            \Mockery::type('array')
        );

        Log::shouldReceive('info')->with(
            'Razorpay payment info',
            \Mockery::type('array')
        );

        // Trigger different error scenarios
        $this->razorpayService->logError('API error', ['details' => 'test']);
        $this->razorpayService->logWarning('Payment warning', ['details' => 'test']);
        $this->razorpayService->logInfo('Payment info', ['details' => 'test']);
    }
} 