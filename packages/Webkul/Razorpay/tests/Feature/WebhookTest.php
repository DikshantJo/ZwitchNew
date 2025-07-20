<?php

namespace Webkul\Razorpay\Tests\Feature;

use Tests\TestCase;
use Webkul\Razorpay\Services\RazorpayService;
use Webkul\Sales\Models\Order;
use Webkul\Sales\Models\OrderPayment;
use Webkul\Sales\Models\OrderTransaction;
use Webkul\Customer\Models\Customer;
use Webkul\Customer\Models\CustomerGroup;
use Webkul\Core\Models\Channel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;

class WebhookTest extends TestCase
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
        Config::set('paymentmethods.razorpay.sandbox', true);
    }

    /** @test */
    public function it_handles_payment_captured_webhook()
    {
        Event::fake();

        $webhookPayload = json_encode([
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
            'created_at' => 1640995200
        ]);

        $signature = hash_hmac('sha256', $webhookPayload, 'test_webhook_secret');

        $response = $this->post(route('razorpay.webhook'), 
            json_decode($webhookPayload, true),
            [
                'X-Razorpay-Signature' => $signature,
                'Content-Type' => 'application/json'
            ]
        );

        $response->assertStatus(200);
        
        // Verify order status updated
        $this->order->refresh();
        $this->assertEquals('processing', $this->order->status);
        
        // Verify payment recorded
        $payment = OrderPayment::where('order_id', $this->order->id)->first();
        $this->assertNotNull($payment);
        $this->assertEquals('razorpay', $payment->method);
        $this->assertEquals('pay_test123', $payment->additional['razorpay_payment_id']);
        $this->assertEquals('card', $payment->additional['payment_method']);
    }

    /** @test */
    public function it_handles_payment_authorized_webhook()
    {
        Event::fake();

        $webhookPayload = json_encode([
            'entity' => 'payment',
            'id' => 'pay_test123',
            'order_id' => 'order_test123',
            'status' => 'authorized',
            'amount' => 100000,
            'currency' => 'INR',
            'method' => 'card'
        ]);

        $signature = hash_hmac('sha256', $webhookPayload, 'test_webhook_secret');

        $response = $this->post(route('razorpay.webhook'), 
            json_decode($webhookPayload, true),
            [
                'X-Razorpay-Signature' => $signature,
                'Content-Type' => 'application/json'
            ]
        );

        $response->assertStatus(200);
        
        // Verify order status updated
        $this->order->refresh();
        $this->assertEquals('processing', $this->order->status);
    }

    /** @test */
    public function it_handles_payment_failed_webhook()
    {
        Event::fake();

        $webhookPayload = json_encode([
            'entity' => 'payment',
            'id' => 'pay_test123',
            'order_id' => 'order_test123',
            'status' => 'failed',
            'amount' => 100000,
            'currency' => 'INR',
            'method' => 'card',
            'error_code' => 'PAYMENT_DECLINED',
            'error_description' => 'Payment was declined by the bank'
        ]);

        $signature = hash_hmac('sha256', $webhookPayload, 'test_webhook_secret');

        $response = $this->post(route('razorpay.webhook'), 
            json_decode($webhookPayload, true),
            [
                'X-Razorpay-Signature' => $signature,
                'Content-Type' => 'application/json'
            ]
        );

        $response->assertStatus(200);
        
        // Verify order status updated
        $this->order->refresh();
        $this->assertEquals('canceled', $this->order->status);
    }

    /** @test */
    public function it_handles_upi_payment_webhook()
    {
        Event::fake();

        $webhookPayload = json_encode([
            'entity' => 'payment',
            'id' => 'pay_test123',
            'order_id' => 'order_test123',
            'status' => 'captured',
            'amount' => 100000,
            'currency' => 'INR',
            'method' => 'upi',
            'vpa' => 'test@upi',
            'card_id' => null,
            'bank' => null,
            'wallet' => null
        ]);

        $signature = hash_hmac('sha256', $webhookPayload, 'test_webhook_secret');

        $response = $this->post(route('razorpay.webhook'), 
            json_decode($webhookPayload, true),
            [
                'X-Razorpay-Signature' => $signature,
                'Content-Type' => 'application/json'
            ]
        );

        $response->assertStatus(200);
        
        // Verify payment method recorded
        $payment = OrderPayment::where('order_id', $this->order->id)->first();
        $this->assertNotNull($payment);
        $this->assertEquals('upi', $payment->additional['payment_method']);
        $this->assertEquals('test@upi', $payment->additional['vpa']);
    }

    /** @test */
    public function it_handles_netbanking_payment_webhook()
    {
        Event::fake();

        $webhookPayload = json_encode([
            'entity' => 'payment',
            'id' => 'pay_test123',
            'order_id' => 'order_test123',
            'status' => 'captured',
            'amount' => 100000,
            'currency' => 'INR',
            'method' => 'netbanking',
            'bank' => 'HDFC',
            'card_id' => null,
            'wallet' => null,
            'vpa' => null
        ]);

        $signature = hash_hmac('sha256', $webhookPayload, 'test_webhook_secret');

        $response = $this->post(route('razorpay.webhook'), 
            json_decode($webhookPayload, true),
            [
                'X-Razorpay-Signature' => $signature,
                'Content-Type' => 'application/json'
            ]
        );

        $response->assertStatus(200);
        
        // Verify payment method recorded
        $payment = OrderPayment::where('order_id', $this->order->id)->first();
        $this->assertNotNull($payment);
        $this->assertEquals('netbanking', $payment->additional['payment_method']);
        $this->assertEquals('HDFC', $payment->additional['bank']);
    }

    /** @test */
    public function it_handles_wallet_payment_webhook()
    {
        Event::fake();

        $webhookPayload = json_encode([
            'entity' => 'payment',
            'id' => 'pay_test123',
            'order_id' => 'order_test123',
            'status' => 'captured',
            'amount' => 100000,
            'currency' => 'INR',
            'method' => 'wallet',
            'wallet' => 'paytm',
            'card_id' => null,
            'bank' => null,
            'vpa' => null
        ]);

        $signature = hash_hmac('sha256', $webhookPayload, 'test_webhook_secret');

        $response = $this->post(route('razorpay.webhook'), 
            json_decode($webhookPayload, true),
            [
                'X-Razorpay-Signature' => $signature,
                'Content-Type' => 'application/json'
            ]
        );

        $response->assertStatus(200);
        
        // Verify payment method recorded
        $payment = OrderPayment::where('order_id', $this->order->id)->first();
        $this->assertNotNull($payment);
        $this->assertEquals('wallet', $payment->additional['payment_method']);
        $this->assertEquals('paytm', $payment->additional['wallet']);
    }

    /** @test */
    public function it_handles_emi_payment_webhook()
    {
        Event::fake();

        $webhookPayload = json_encode([
            'entity' => 'payment',
            'id' => 'pay_test123',
            'order_id' => 'order_test123',
            'status' => 'captured',
            'amount' => 100000,
            'currency' => 'INR',
            'method' => 'emi',
            'emi_month' => 6,
            'card_id' => 'card_test123',
            'bank' => 'HDFC',
            'wallet' => null,
            'vpa' => null
        ]);

        $signature = hash_hmac('sha256', $webhookPayload, 'test_webhook_secret');

        $response = $this->post(route('razorpay.webhook'), 
            json_decode($webhookPayload, true),
            [
                'X-Razorpay-Signature' => $signature,
                'Content-Type' => 'application/json'
            ]
        );

        $response->assertStatus(200);
        
        // Verify payment method recorded
        $payment = OrderPayment::where('order_id', $this->order->id)->first();
        $this->assertNotNull($payment);
        $this->assertEquals('emi', $payment->additional['payment_method']);
        $this->assertEquals(6, $payment->additional['emi_month']);
    }

    /** @test */
    public function it_handles_refund_processed_webhook()
    {
        // Create a paid order first
        $paidOrder = Order::factory()->create([
            'customer_id' => $this->customer->id,
            'payment_method' => 'razorpay',
            'grand_total' => 1000.00,
            'status' => 'processing'
        ]);

        // Create payment record
        OrderPayment::factory()->create([
            'order_id' => $paidOrder->id,
            'method' => 'razorpay',
            'amount' => 1000.00,
            'additional' => [
                'razorpay_payment_id' => 'pay_test123'
            ]
        ]);

        Event::fake();

        $webhookPayload = json_encode([
            'entity' => 'refund',
            'id' => 'rfnd_test123',
            'payment_id' => 'pay_test123',
            'status' => 'processed',
            'amount' => 100000,
            'currency' => 'INR',
            'notes' => 'Customer requested refund'
        ]);

        $signature = hash_hmac('sha256', $webhookPayload, 'test_webhook_secret');

        $response = $this->post(route('razorpay.webhook'), 
            json_decode($webhookPayload, true),
            [
                'X-Razorpay-Signature' => $signature,
                'Content-Type' => 'application/json'
            ]
        );

        $response->assertStatus(200);
        
        // Verify refund recorded
        $refund = OrderTransaction::where('order_id', $paidOrder->id)
            ->where('type', 'refund')
            ->first();
        $this->assertNotNull($refund);
        $this->assertEquals('rfnd_test123', $refund->additional['razorpay_refund_id']);
    }

    /** @test */
    public function it_handles_partial_refund_webhook()
    {
        // Create a paid order first
        $paidOrder = Order::factory()->create([
            'customer_id' => $this->customer->id,
            'payment_method' => 'razorpay',
            'grand_total' => 1000.00,
            'status' => 'processing'
        ]);

        // Create payment record
        OrderPayment::factory()->create([
            'order_id' => $paidOrder->id,
            'method' => 'razorpay',
            'amount' => 1000.00,
            'additional' => [
                'razorpay_payment_id' => 'pay_test123'
            ]
        ]);

        Event::fake();

        $webhookPayload = json_encode([
            'entity' => 'refund',
            'id' => 'rfnd_test123',
            'payment_id' => 'pay_test123',
            'status' => 'processed',
            'amount' => 50000, // Partial refund of 500.00
            'currency' => 'INR',
            'notes' => 'Partial refund for damaged item'
        ]);

        $signature = hash_hmac('sha256', $webhookPayload, 'test_webhook_secret');

        $response = $this->post(route('razorpay.webhook'), 
            json_decode($webhookPayload, true),
            [
                'X-Razorpay-Signature' => $signature,
                'Content-Type' => 'application/json'
            ]
        );

        $response->assertStatus(200);
        
        // Verify partial refund recorded
        $refund = OrderTransaction::where('order_id', $paidOrder->id)
            ->where('type', 'refund')
            ->first();
        $this->assertNotNull($refund);
        $this->assertEquals(500.00, $refund->amount);
    }

    /** @test */
    public function it_handles_invalid_webhook_signature()
    {
        $webhookPayload = json_encode([
            'entity' => 'payment',
            'id' => 'pay_test123',
            'order_id' => 'order_test123',
            'status' => 'captured'
        ]);

        $invalidSignature = 'invalid_signature';

        $response = $this->post(route('razorpay.webhook'), 
            json_decode($webhookPayload, true),
            [
                'X-Razorpay-Signature' => $invalidSignature,
                'Content-Type' => 'application/json'
            ]
        );

        $response->assertStatus(400);
        $response->assertJson(['error' => 'Invalid webhook signature']);
        
        // Verify order status unchanged
        $this->order->refresh();
        $this->assertEquals('pending', $this->order->status);
    }

    /** @test */
    public function it_handles_missing_webhook_signature()
    {
        $webhookPayload = json_encode([
            'entity' => 'payment',
            'id' => 'pay_test123',
            'order_id' => 'order_test123',
            'status' => 'captured'
        ]);

        $response = $this->post(route('razorpay.webhook'), 
            json_decode($webhookPayload, true),
            [
                'Content-Type' => 'application/json'
            ]
        );

        $response->assertStatus(400);
        $response->assertJson(['error' => 'Webhook signature is required']);
    }

    /** @test */
    public function it_handles_empty_webhook_payload()
    {
        $signature = hash_hmac('sha256', '', 'test_webhook_secret');

        $response = $this->post(route('razorpay.webhook'), 
            [],
            [
                'X-Razorpay-Signature' => $signature,
                'Content-Type' => 'application/json'
            ]
        );

        $response->assertStatus(400);
        $response->assertJson(['error' => 'Webhook payload is required']);
    }

    /** @test */
    public function it_handles_invalid_webhook_payload()
    {
        $invalidPayload = 'invalid_json_payload';
        $signature = hash_hmac('sha256', $invalidPayload, 'test_webhook_secret');

        $response = $this->post(route('razorpay.webhook'), 
            $invalidPayload,
            [
                'X-Razorpay-Signature' => $signature,
                'Content-Type' => 'application/json'
            ]
        );

        $response->assertStatus(400);
        $response->assertJson(['error' => 'Invalid webhook payload']);
    }

    /** @test */
    public function it_handles_unknown_webhook_entity()
    {
        $webhookPayload = json_encode([
            'entity' => 'unknown_entity',
            'id' => 'test123'
        ]);

        $signature = hash_hmac('sha256', $webhookPayload, 'test_webhook_secret');

        $response = $this->post(route('razorpay.webhook'), 
            json_decode($webhookPayload, true),
            [
                'X-Razorpay-Signature' => $signature,
                'Content-Type' => 'application/json'
            ]
        );

        $response->assertStatus(400);
        $response->assertJson(['error' => 'Unknown webhook entity']);
    }

    /** @test */
    public function it_handles_webhook_retry()
    {
        Event::fake();

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

        // First webhook call
        $response1 = $this->post(route('razorpay.webhook'), 
            json_decode($webhookPayload, true),
            [
                'X-Razorpay-Signature' => $signature,
                'Content-Type' => 'application/json'
            ]
        );

        $response1->assertStatus(200);

        // Retry webhook call (should be idempotent)
        $response2 = $this->post(route('razorpay.webhook'), 
            json_decode($webhookPayload, true),
            [
                'X-Razorpay-Signature' => $signature,
                'Content-Type' => 'application/json'
            ]
        );

        $response2->assertStatus(200);
        
        // Verify only one payment record exists
        $payments = OrderPayment::where('order_id', $this->order->id)->get();
        $this->assertEquals(1, $payments->count());
    }

    /** @test */
    public function it_handles_webhook_with_missing_order()
    {
        $webhookPayload = json_encode([
            'entity' => 'payment',
            'id' => 'pay_test123',
            'order_id' => 'non_existent_order',
            'status' => 'captured',
            'amount' => 100000,
            'currency' => 'INR',
            'method' => 'card'
        ]);

        $signature = hash_hmac('sha256', $webhookPayload, 'test_webhook_secret');

        $response = $this->post(route('razorpay.webhook'), 
            json_decode($webhookPayload, true),
            [
                'X-Razorpay-Signature' => $signature,
                'Content-Type' => 'application/json'
            ]
        );

        $response->assertStatus(404);
        $response->assertJson(['error' => 'Order not found']);
    }

    /** @test */
    public function it_handles_webhook_with_different_currencies()
    {
        // Create order with USD currency
        $usdOrder = Order::factory()->create([
            'customer_id' => $this->customer->id,
            'payment_method' => 'razorpay',
            'grand_total' => 10.00,
            'currency_code' => 'USD',
            'status' => 'pending'
        ]);

        Event::fake();

        $webhookPayload = json_encode([
            'entity' => 'payment',
            'id' => 'pay_test123',
            'order_id' => 'order_test123',
            'status' => 'captured',
            'amount' => 1000, // 10.00 USD in cents
            'currency' => 'USD',
            'method' => 'card'
        ]);

        $signature = hash_hmac('sha256', $webhookPayload, 'test_webhook_secret');

        $response = $this->post(route('razorpay.webhook'), 
            json_decode($webhookPayload, true),
            [
                'X-Razorpay-Signature' => $signature,
                'Content-Type' => 'application/json'
            ]
        );

        $response->assertStatus(200);
        
        // Verify payment recorded with correct currency
        $payment = OrderPayment::where('order_id', $usdOrder->id)->first();
        $this->assertNotNull($payment);
        $this->assertEquals('USD', $payment->currency);
    }

    /** @test */
    public function it_handles_webhook_with_amount_mismatch()
    {
        $webhookPayload = json_encode([
            'entity' => 'payment',
            'id' => 'pay_test123',
            'order_id' => 'order_test123',
            'status' => 'captured',
            'amount' => 50000, // 500.00 in paise (mismatch with order amount)
            'currency' => 'INR',
            'method' => 'card'
        ]);

        $signature = hash_hmac('sha256', $webhookPayload, 'test_webhook_secret');

        $response = $this->post(route('razorpay.webhook'), 
            json_decode($webhookPayload, true),
            [
                'X-Razorpay-Signature' => $signature,
                'Content-Type' => 'application/json'
            ]
        );

        $response->assertStatus(400);
        $response->assertJson(['error' => 'Payment amount mismatch']);
        
        // Verify order status unchanged
        $this->order->refresh();
        $this->assertEquals('pending', $this->order->status);
    }

    /** @test */
    public function it_handles_webhook_with_currency_mismatch()
    {
        $webhookPayload = json_encode([
            'entity' => 'payment',
            'id' => 'pay_test123',
            'order_id' => 'order_test123',
            'status' => 'captured',
            'amount' => 100000,
            'currency' => 'USD', // Mismatch with order currency (INR)
            'method' => 'card'
        ]);

        $signature = hash_hmac('sha256', $webhookPayload, 'test_webhook_secret');

        $response = $this->post(route('razorpay.webhook'), 
            json_decode($webhookPayload, true),
            [
                'X-Razorpay-Signature' => $signature,
                'Content-Type' => 'application/json'
            ]
        );

        $response->assertStatus(400);
        $response->assertJson(['error' => 'Payment currency mismatch']);
    }

    /** @test */
    public function it_handles_webhook_rate_limiting()
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

        // Send multiple webhooks rapidly
        for ($i = 0; $i < 10; $i++) {
            $response = $this->post(route('razorpay.webhook'), 
                json_decode($webhookPayload, true),
                [
                    'X-Razorpay-Signature' => $signature,
                    'Content-Type' => 'application/json'
                ]
            );

            if ($i < 5) {
                $response->assertStatus(200);
            } else {
                $response->assertStatus(429); // Too Many Requests
            }
        }
    }
} 