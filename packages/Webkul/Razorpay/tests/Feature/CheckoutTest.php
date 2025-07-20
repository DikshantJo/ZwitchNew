<?php

namespace Webkul\Razorpay\Tests\Feature;

use Tests\TestCase;
use Webkul\Razorpay\Payment\Razorpay;
use Webkul\Checkout\Models\Cart;
use Webkul\Checkout\Models\CartAddress;
use Webkul\Checkout\Models\CartItem;
use Webkul\Customer\Models\Customer;
use Webkul\Product\Models\Product;
use Webkul\Core\Models\Currency;
use Webkul\Sales\Models\Order;
use Webkul\Sales\Models\OrderPayment;
use Webkul\Sales\Models\OrderTransaction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Config;

class CheckoutTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $customer;
    protected $cart;
    protected $product;
    protected $razorpayPayment;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->razorpayPayment = new Razorpay();
        
        // Create test customer
        $this->customer = Customer::factory()->create([
            'email' => 'test@example.com',
            'first_name' => 'Test',
            'last_name' => 'Customer',
            'phone' => '9876543210'
        ]);

        // Create test product
        $this->product = Product::factory()->create([
            'name' => 'Test Product',
            'price' => 1000.00,
            'type' => 'simple'
        ]);

        // Create test cart
        $this->cart = Cart::factory()->create([
            'customer_id' => $this->customer->id,
            'customer_email' => $this->customer->email,
            'currency_code' => 'INR'
        ]);

        // Add cart item
        CartItem::factory()->create([
            'cart_id' => $this->cart->id,
            'product_id' => $this->product->id,
            'quantity' => 1,
            'price' => 1000.00,
            'total' => 1000.00
        ]);

        // Add billing address
        CartAddress::factory()->create([
            'cart_id' => $this->cart->id,
            'address_type' => 'billing',
            'first_name' => 'Test',
            'last_name' => 'Customer',
            'email' => 'test@example.com',
            'phone' => '9876543210',
            'address1' => 'Test Address',
            'city' => 'Test City',
            'state' => 'Test State',
            'country' => 'IN',
            'postcode' => '123456'
        ]);

        // Add shipping address
        CartAddress::factory()->create([
            'cart_id' => $this->cart->id,
            'address_type' => 'shipping',
            'first_name' => 'Test',
            'last_name' => 'Customer',
            'email' => 'test@example.com',
            'phone' => '9876543210',
            'address1' => 'Test Address',
            'city' => 'Test City',
            'state' => 'Test State',
            'country' => 'IN',
            'postcode' => '123456'
        ]);

        // Configure Razorpay for testing
        Config::set('paymentmethods.razorpay.active', true);
        Config::set('paymentmethods.razorpay.key_id', 'test_key_id');
        Config::set('paymentmethods.razorpay.key_secret', 'test_key_secret');
        Config::set('paymentmethods.razorpay.webhook_secret', 'test_webhook_secret');
        Config::set('paymentmethods.razorpay.sandbox', true);
    }

    /** @test */
    public function it_displays_razorpay_payment_method_in_checkout()
    {
        $response = $this->actingAs($this->customer, 'customer')
            ->get(route('shop.checkout.onepage.index'));

        $response->assertStatus(200);
        $response->assertSee('Pay with Razorpay');
        $response->assertSee('Pay securely with cards, UPI, net banking, and wallets');
    }

    /** @test */
    public function it_validates_payment_method_availability()
    {
        $this->assertTrue($this->razorpayPayment->isAvailable($this->cart));
    }

    /** @test */
    public function it_handles_payment_method_selection()
    {
        $response = $this->actingAs($this->customer, 'customer')
            ->post(route('shop.checkout.payment-method.store'), [
                'payment_method' => 'razorpay'
            ]);

        $response->assertStatus(302);
        $this->assertEquals('razorpay', $this->cart->fresh()->payment_method);
    }

    /** @test */
    public function it_creates_razorpay_order_on_checkout()
    {
        Event::fake();

        $response = $this->actingAs($this->customer, 'customer')
            ->post(route('shop.checkout.onepage.orders.store'), [
                'payment_method' => 'razorpay',
                'billing_address' => [
                    'first_name' => 'Test',
                    'last_name' => 'Customer',
                    'email' => 'test@example.com',
                    'phone' => '9876543210',
                    'address1' => 'Test Address',
                    'city' => 'Test City',
                    'state' => 'Test State',
                    'country' => 'IN',
                    'postcode' => '123456'
                ],
                'shipping_address' => [
                    'first_name' => 'Test',
                    'last_name' => 'Customer',
                    'email' => 'test@example.com',
                    'phone' => '9876543210',
                    'address1' => 'Test Address',
                    'city' => 'Test City',
                    'state' => 'Test State',
                    'country' => 'IN',
                    'postcode' => '123456'
                ]
            ]);

        $response->assertStatus(302);
        
        // Verify order was created
        $order = Order::where('customer_id', $this->customer->id)->first();
        $this->assertNotNull($order);
        $this->assertEquals('razorpay', $order->payment_method);
        $this->assertEquals(1000.00, $order->grand_total);
    }

    /** @test */
    public function it_handles_payment_success()
    {
        // Create order first
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
            'amount' => 100000, // in paise
            'currency' => 'INR'
        ];

        $response = $this->actingAs($this->customer, 'customer')
            ->post(route('razorpay.payment.success'), $paymentData);

        $response->assertStatus(302);
        
        // Verify order status updated
        $order->refresh();
        $this->assertEquals('processing', $order->status);
        
        // Verify payment recorded
        $payment = OrderPayment::where('order_id', $order->id)->first();
        $this->assertNotNull($payment);
        $this->assertEquals('razorpay', $payment->method);
        $this->assertEquals('pay_test123', $payment->additional['razorpay_payment_id']);
    }

    /** @test */
    public function it_handles_payment_failure()
    {
        // Create order first
        $order = Order::factory()->create([
            'customer_id' => $this->customer->id,
            'payment_method' => 'razorpay',
            'grand_total' => 1000.00,
            'status' => 'pending'
        ]);

        $paymentData = [
            'razorpay_payment_id' => 'pay_test123',
            'razorpay_order_id' => 'order_test123',
            'error_code' => 'PAYMENT_DECLINED',
            'error_description' => 'Payment was declined by the bank'
        ];

        $response = $this->actingAs($this->customer, 'customer')
            ->post(route('razorpay.payment.failure'), $paymentData);

        $response->assertStatus(302);
        
        // Verify order status updated
        $order->refresh();
        $this->assertEquals('canceled', $order->status);
    }

    /** @test */
    public function it_validates_payment_signature()
    {
        $order = Order::factory()->create([
            'customer_id' => $this->customer->id,
            'payment_method' => 'razorpay',
            'grand_total' => 1000.00,
            'status' => 'pending'
        ]);

        $invalidPaymentData = [
            'razorpay_payment_id' => 'pay_test123',
            'razorpay_order_id' => 'order_test123',
            'razorpay_signature' => 'invalid_signature',
            'payment_method' => 'card',
            'amount' => 100000,
            'currency' => 'INR'
        ];

        $response = $this->actingAs($this->customer, 'customer')
            ->post(route('razorpay.payment.success'), $invalidPaymentData);

        $response->assertStatus(400);
        
        // Verify order status unchanged
        $order->refresh();
        $this->assertEquals('pending', $order->status);
    }

    /** @test */
    public function it_handles_webhook_payment_confirmation()
    {
        $order = Order::factory()->create([
            'customer_id' => $this->customer->id,
            'payment_method' => 'razorpay',
            'grand_total' => 1000.00,
            'status' => 'pending'
        ]);

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

        $response = $this->post(route('razorpay.webhook'), [], [
            'X-Razorpay-Signature' => $signature,
            'Content-Type' => 'application/json'
        ]);

        $response->assertStatus(200);
        
        // Verify order status updated
        $order->refresh();
        $this->assertEquals('processing', $order->status);
    }

    /** @test */
    public function it_handles_invalid_webhook_signature()
    {
        $order = Order::factory()->create([
            'customer_id' => $this->customer->id,
            'payment_method' => 'razorpay',
            'grand_total' => 1000.00,
            'status' => 'pending'
        ]);

        $webhookPayload = json_encode([
            'entity' => 'payment',
            'id' => 'pay_test123',
            'order_id' => 'order_test123',
            'status' => 'captured'
        ]);

        $invalidSignature = 'invalid_signature';

        $response = $this->post(route('razorpay.webhook'), [], [
            'X-Razorpay-Signature' => $invalidSignature,
            'Content-Type' => 'application/json'
        ]);

        $response->assertStatus(400);
        
        // Verify order status unchanged
        $order->refresh();
        $this->assertEquals('pending', $order->status);
    }

    /** @test */
    public function it_handles_different_payment_methods()
    {
        $paymentMethods = ['card', 'upi', 'netbanking', 'wallet', 'emi'];

        foreach ($paymentMethods as $method) {
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
                'payment_method' => $method,
                'amount' => 100000,
                'currency' => 'INR'
            ];

            $response = $this->actingAs($this->customer, 'customer')
                ->post(route('razorpay.payment.success'), $paymentData);

            $response->assertStatus(302);
            
            // Verify payment method recorded
            $payment = OrderPayment::where('order_id', $order->id)->first();
            $this->assertNotNull($payment);
            $this->assertEquals($method, $payment->additional['payment_method']);
        }
    }

    /** @test */
    public function it_handles_currency_conversion()
    {
        // Test with USD currency
        $this->cart->update(['currency_code' => 'USD']);
        
        $order = Order::factory()->create([
            'customer_id' => $this->customer->id,
            'payment_method' => 'razorpay',
            'grand_total' => 10.00,
            'currency_code' => 'USD',
            'status' => 'pending'
        ]);

        $paymentData = [
            'razorpay_payment_id' => 'pay_test123',
            'razorpay_order_id' => 'order_test123',
            'razorpay_signature' => 'valid_signature',
            'payment_method' => 'card',
            'amount' => 1000, // 10.00 USD in cents
            'currency' => 'USD'
        ];

        $response = $this->actingAs($this->customer, 'customer')
            ->post(route('razorpay.payment.success'), $paymentData);

        $response->assertStatus(302);
        
        // Verify payment recorded with correct currency
        $payment = OrderPayment::where('order_id', $order->id)->first();
        $this->assertNotNull($payment);
        $this->assertEquals('USD', $payment->currency);
    }

    /** @test */
    public function it_handles_partial_payments()
    {
        $order = Order::factory()->create([
            'customer_id' => $this->customer->id,
            'payment_method' => 'razorpay',
            'grand_total' => 1000.00,
            'status' => 'pending'
        ]);

        // First partial payment
        $paymentData1 = [
            'razorpay_payment_id' => 'pay_test123',
            'razorpay_order_id' => 'order_test123',
            'razorpay_signature' => 'valid_signature',
            'payment_method' => 'card',
            'amount' => 50000, // 500.00 in paise
            'currency' => 'INR'
        ];

        $response1 = $this->actingAs($this->customer, 'customer')
            ->post(route('razorpay.payment.success'), $paymentData1);

        $response1->assertStatus(302);
        
        // Second partial payment
        $paymentData2 = [
            'razorpay_payment_id' => 'pay_test456',
            'razorpay_order_id' => 'order_test123',
            'razorpay_signature' => 'valid_signature',
            'payment_method' => 'upi',
            'amount' => 50000, // 500.00 in paise
            'currency' => 'INR'
        ];

        $response2 = $this->actingAs($this->customer, 'customer')
            ->post(route('razorpay.payment.success'), $paymentData2);

        $response2->assertStatus(302);
        
        // Verify both payments recorded
        $payments = OrderPayment::where('order_id', $order->id)->get();
        $this->assertEquals(2, $payments->count());
        
        $totalPaid = $payments->sum('amount');
        $this->assertEquals(1000.00, $totalPaid);
    }

    /** @test */
    public function it_handles_guest_checkout()
    {
        $guestCart = Cart::factory()->create([
            'customer_id' => null,
            'customer_email' => 'guest@example.com',
            'currency_code' => 'INR'
        ]);

        // Add cart item
        CartItem::factory()->create([
            'cart_id' => $guestCart->id,
            'product_id' => $this->product->id,
            'quantity' => 1,
            'price' => 1000.00,
            'total' => 1000.00
        ]);

        // Add addresses
        CartAddress::factory()->create([
            'cart_id' => $guestCart->id,
            'address_type' => 'billing',
            'first_name' => 'Guest',
            'last_name' => 'Customer',
            'email' => 'guest@example.com',
            'phone' => '9876543210',
            'address1' => 'Guest Address',
            'city' => 'Guest City',
            'state' => 'Guest State',
            'country' => 'IN',
            'postcode' => '123456'
        ]);

        $response = $this->post(route('shop.checkout.onepage.orders.store'), [
            'payment_method' => 'razorpay',
            'billing_address' => [
                'first_name' => 'Guest',
                'last_name' => 'Customer',
                'email' => 'guest@example.com',
                'phone' => '9876543210',
                'address1' => 'Guest Address',
                'city' => 'Guest City',
                'state' => 'Guest State',
                'country' => 'IN',
                'postcode' => '123456'
            ]
        ]);

        $response->assertStatus(302);
        
        // Verify guest order created
        $order = Order::where('customer_email', 'guest@example.com')->first();
        $this->assertNotNull($order);
        $this->assertEquals('razorpay', $order->payment_method);
    }

    /** @test */
    public function it_handles_checkout_validation_errors()
    {
        $response = $this->actingAs($this->customer, 'customer')
            ->post(route('shop.checkout.onepage.orders.store'), [
                'payment_method' => 'razorpay'
                // Missing required address fields
            ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['billing_address', 'shipping_address']);
    }

    /** @test */
    public function it_handles_payment_method_validation()
    {
        $response = $this->actingAs($this->customer, 'customer')
            ->post(route('shop.checkout.payment-method.store'), [
                'payment_method' => 'invalid_method'
            ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['payment_method']);
    }

    /** @test */
    public function it_handles_cart_validation()
    {
        // Empty cart
        $emptyCart = Cart::factory()->create([
            'customer_id' => $this->customer->id,
            'customer_email' => $this->customer->email,
            'currency_code' => 'INR'
        ]);

        $response = $this->actingAs($this->customer, 'customer')
            ->post(route('shop.checkout.onepage.orders.store'), [
                'payment_method' => 'razorpay'
            ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['cart']);
    }

    /** @test */
    public function it_handles_currency_validation()
    {
        // Unsupported currency
        $this->cart->update(['currency_code' => 'XYZ']);

        $response = $this->actingAs($this->customer, 'customer')
            ->post(route('shop.checkout.onepage.orders.store'), [
                'payment_method' => 'razorpay'
            ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['currency']);
    }

    /** @test */
    public function it_handles_amount_validation()
    {
        // Zero amount cart
        $this->cart->items()->update(['price' => 0, 'total' => 0]);

        $response = $this->actingAs($this->customer, 'customer')
            ->post(route('shop.checkout.onepage.orders.store'), [
                'payment_method' => 'razorpay'
            ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['amount']);
    }

    /** @test */
    public function it_handles_duplicate_payment_processing()
    {
        $order = Order::factory()->create([
            'customer_id' => $this->customer->id,
            'payment_method' => 'razorpay',
            'grand_total' => 1000.00,
            'status' => 'processing' // Already processed
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
    }

    /** @test */
    public function it_handles_expired_orders()
    {
        $order = Order::factory()->create([
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
    }
} 