<?php

namespace Webkul\Razorpay\Tests\Feature;

use Tests\TestCase;
use Webkul\Razorpay\Payment\Razorpay;
use Webkul\Checkout\Models\Cart;
use Webkul\Checkout\Models\CartAddress;
use Webkul\Checkout\Models\CartItem;
use Webkul\Customer\Models\Customer;
use Webkul\Product\Models\Product;
use Webkul\Sales\Models\Order;
use Webkul\Sales\Models\OrderPayment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Event;

class PaymentMethodsTest extends TestCase
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

        // Add addresses
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

        // Configure Razorpay for testing
        Config::set('paymentmethods.razorpay.active', true);
        Config::set('paymentmethods.razorpay.key_id', 'test_key_id');
        Config::set('paymentmethods.razorpay.key_secret', 'test_key_secret');
        Config::set('paymentmethods.razorpay.webhook_secret', 'test_webhook_secret');
        Config::set('paymentmethods.razorpay.sandbox', true);
    }

    /** @test */
    public function it_supports_card_payments()
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
            'card_id' => 'card_test123',
            'bank' => 'HDFC',
            'amount' => 100000,
            'currency' => 'INR'
        ];

        $response = $this->actingAs($this->customer, 'customer')
            ->post(route('razorpay.payment.success'), $paymentData);

        $response->assertStatus(302);
        
        // Verify card payment details
        $payment = OrderPayment::where('order_id', $order->id)->first();
        $this->assertNotNull($payment);
        $this->assertEquals('card', $payment->additional['payment_method']);
        $this->assertEquals('card_test123', $payment->additional['card_id']);
        $this->assertEquals('HDFC', $payment->additional['bank']);
    }

    /** @test */
    public function it_supports_upi_payments()
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
            'vpa' => 'test@upi',
            'amount' => 100000,
            'currency' => 'INR'
        ];

        $response = $this->actingAs($this->customer, 'customer')
            ->post(route('razorpay.payment.success'), $paymentData);

        $response->assertStatus(302);
        
        // Verify UPI payment details
        $payment = OrderPayment::where('order_id', $order->id)->first();
        $this->assertNotNull($payment);
        $this->assertEquals('upi', $payment->additional['payment_method']);
        $this->assertEquals('test@upi', $payment->additional['vpa']);
    }

    /** @test */
    public function it_supports_netbanking_payments()
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
            'bank' => 'HDFC',
            'amount' => 100000,
            'currency' => 'INR'
        ];

        $response = $this->actingAs($this->customer, 'customer')
            ->post(route('razorpay.payment.success'), $paymentData);

        $response->assertStatus(302);
        
        // Verify netbanking payment details
        $payment = OrderPayment::where('order_id', $order->id)->first();
        $this->assertNotNull($payment);
        $this->assertEquals('netbanking', $payment->additional['payment_method']);
        $this->assertEquals('HDFC', $payment->additional['bank']);
    }

    /** @test */
    public function it_supports_wallet_payments()
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
            'wallet' => 'paytm',
            'amount' => 100000,
            'currency' => 'INR'
        ];

        $response = $this->actingAs($this->customer, 'customer')
            ->post(route('razorpay.payment.success'), $paymentData);

        $response->assertStatus(302);
        
        // Verify wallet payment details
        $payment = OrderPayment::where('order_id', $order->id)->first();
        $this->assertNotNull($payment);
        $this->assertEquals('wallet', $payment->additional['payment_method']);
        $this->assertEquals('paytm', $payment->additional['wallet']);
    }

    /** @test */
    public function it_supports_emi_payments()
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
            'emi_month' => 6,
            'card_id' => 'card_test123',
            'bank' => 'HDFC',
            'amount' => 100000,
            'currency' => 'INR'
        ];

        $response = $this->actingAs($this->customer, 'customer')
            ->post(route('razorpay.payment.success'), $paymentData);

        $response->assertStatus(302);
        
        // Verify EMI payment details
        $payment = OrderPayment::where('order_id', $order->id)->first();
        $this->assertNotNull($payment);
        $this->assertEquals('emi', $payment->additional['payment_method']);
        $this->assertEquals(6, $payment->additional['emi_month']);
        $this->assertEquals('card_test123', $payment->additional['card_id']);
        $this->assertEquals('HDFC', $payment->additional['bank']);
    }

    /** @test */
    public function it_validates_upi_vpa_format()
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
            'vpa' => 'invalid_vpa_format', // Invalid VPA format
            'amount' => 100000,
            'currency' => 'INR'
        ];

        $response = $this->actingAs($this->customer, 'customer')
            ->post(route('razorpay.payment.success'), $paymentData);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['vpa']);
    }

    /** @test */
    public function it_validates_card_payment_details()
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
            'card_id' => '', // Empty card ID
            'amount' => 100000,
            'currency' => 'INR'
        ];

        $response = $this->actingAs($this->customer, 'customer')
            ->post(route('razorpay.payment.success'), $paymentData);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['card_id']);
    }

    /** @test */
    public function it_validates_netbanking_bank_details()
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
            'bank' => '', // Empty bank name
            'amount' => 100000,
            'currency' => 'INR'
        ];

        $response = $this->actingAs($this->customer, 'customer')
            ->post(route('razorpay.payment.success'), $paymentData);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['bank']);
    }

    /** @test */
    public function it_validates_wallet_details()
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
            'wallet' => '', // Empty wallet name
            'amount' => 100000,
            'currency' => 'INR'
        ];

        $response = $this->actingAs($this->customer, 'customer')
            ->post(route('razorpay.payment.success'), $paymentData);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['wallet']);
    }

    /** @test */
    public function it_validates_emi_details()
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
            'emi_month' => 0, // Invalid EMI months
            'amount' => 100000,
            'currency' => 'INR'
        ];

        $response = $this->actingAs($this->customer, 'customer')
            ->post(route('razorpay.payment.success'), $paymentData);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['emi_month']);
    }

    /** @test */
    public function it_handles_payment_method_specific_failures()
    {
        $order = Order::factory()->create([
            'customer_id' => $this->customer->id,
            'payment_method' => 'razorpay',
            'grand_total' => 1000.00,
            'status' => 'pending'
        ]);

        // Test card payment failure
        $cardPaymentData = [
            'razorpay_payment_id' => 'pay_test123',
            'razorpay_order_id' => 'order_test123',
            'razorpay_signature' => 'valid_signature',
            'payment_method' => 'card',
            'error_code' => 'CARD_DECLINED',
            'error_description' => 'Card was declined by the bank'
        ];

        $response = $this->actingAs($this->customer, 'customer')
            ->post(route('razorpay.payment.failure'), $cardPaymentData);

        $response->assertStatus(302);
        
        // Verify order status updated
        $order->refresh();
        $this->assertEquals('canceled', $order->status);
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

        $upiPaymentData = [
            'razorpay_payment_id' => 'pay_test123',
            'razorpay_order_id' => 'order_test123',
            'razorpay_signature' => 'valid_signature',
            'payment_method' => 'upi',
            'error_code' => 'UPI_PAYMENT_FAILED',
            'error_description' => 'UPI payment failed'
        ];

        $response = $this->actingAs($this->customer, 'customer')
            ->post(route('razorpay.payment.failure'), $upiPaymentData);

        $response->assertStatus(302);
        
        // Verify order status updated
        $order->refresh();
        $this->assertEquals('canceled', $order->status);
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

        $netbankingPaymentData = [
            'razorpay_payment_id' => 'pay_test123',
            'razorpay_order_id' => 'order_test123',
            'razorpay_signature' => 'valid_signature',
            'payment_method' => 'netbanking',
            'error_code' => 'NETBANKING_FAILED',
            'error_description' => 'Netbanking payment failed'
        ];

        $response = $this->actingAs($this->customer, 'customer')
            ->post(route('razorpay.payment.failure'), $netbankingPaymentData);

        $response->assertStatus(302);
        
        // Verify order status updated
        $order->refresh();
        $this->assertEquals('canceled', $order->status);
    }

    /** @test */
    public function it_handles_wallet_failures()
    {
        $order = Order::factory()->create([
            'customer_id' => $this->customer->id,
            'payment_method' => 'razorpay',
            'grand_total' => 1000.00,
            'status' => 'pending'
        ]);

        $walletPaymentData = [
            'razorpay_payment_id' => 'pay_test123',
            'razorpay_order_id' => 'order_test123',
            'razorpay_signature' => 'valid_signature',
            'payment_method' => 'wallet',
            'error_code' => 'WALLET_INSUFFICIENT_BALANCE',
            'error_description' => 'Insufficient wallet balance'
        ];

        $response = $this->actingAs($this->customer, 'customer')
            ->post(route('razorpay.payment.failure'), $walletPaymentData);

        $response->assertStatus(302);
        
        // Verify order status updated
        $order->refresh();
        $this->assertEquals('canceled', $order->status);
    }

    /** @test */
    public function it_handles_emi_failures()
    {
        $order = Order::factory()->create([
            'customer_id' => $this->customer->id,
            'payment_method' => 'razorpay',
            'grand_total' => 1000.00,
            'status' => 'pending'
        ]);

        $emiPaymentData = [
            'razorpay_payment_id' => 'pay_test123',
            'razorpay_order_id' => 'order_test123',
            'razorpay_signature' => 'valid_signature',
            'payment_method' => 'emi',
            'error_code' => 'EMI_NOT_AVAILABLE',
            'error_description' => 'EMI not available for this card'
        ];

        $response = $this->actingAs($this->customer, 'customer')
            ->post(route('razorpay.payment.failure'), $emiPaymentData);

        $response->assertStatus(302);
        
        // Verify order status updated
        $order->refresh();
        $this->assertEquals('canceled', $order->status);
    }

    /** @test */
    public function it_supports_multiple_payment_methods_in_single_order()
    {
        $order = Order::factory()->create([
            'customer_id' => $this->customer->id,
            'payment_method' => 'razorpay',
            'grand_total' => 2000.00,
            'status' => 'pending'
        ]);

        // First payment with card
        $cardPaymentData = [
            'razorpay_payment_id' => 'pay_test123',
            'razorpay_order_id' => 'order_test123',
            'razorpay_signature' => 'valid_signature',
            'payment_method' => 'card',
            'amount' => 100000, // 1000.00 in paise
            'currency' => 'INR'
        ];

        $response1 = $this->actingAs($this->customer, 'customer')
            ->post(route('razorpay.payment.success'), $cardPaymentData);

        $response1->assertStatus(302);

        // Second payment with UPI
        $upiPaymentData = [
            'razorpay_payment_id' => 'pay_test456',
            'razorpay_order_id' => 'order_test123',
            'razorpay_signature' => 'valid_signature',
            'payment_method' => 'upi',
            'vpa' => 'test@upi',
            'amount' => 100000, // 1000.00 in paise
            'currency' => 'INR'
        ];

        $response2 = $this->actingAs($this->customer, 'customer')
            ->post(route('razorpay.payment.success'), $upiPaymentData);

        $response2->assertStatus(302);
        
        // Verify both payments recorded
        $payments = OrderPayment::where('order_id', $order->id)->get();
        $this->assertEquals(2, $payments->count());
        
        $cardPayment = $payments->where('additional->payment_method', 'card')->first();
        $upiPayment = $payments->where('additional->payment_method', 'upi')->first();
        
        $this->assertNotNull($cardPayment);
        $this->assertNotNull($upiPayment);
        $this->assertEquals('test@upi', $upiPayment->additional['vpa']);
    }

    /** @test */
    public function it_validates_payment_method_availability_by_currency()
    {
        // Test INR currency (all methods available)
        $this->cart->update(['currency_code' => 'INR']);
        $this->assertTrue($this->razorpayPayment->isAvailable($this->cart));

        // Test USD currency (limited methods)
        $this->cart->update(['currency_code' => 'USD']);
        $this->assertTrue($this->razorpayPayment->isAvailable($this->cart));

        // Test unsupported currency
        $this->cart->update(['currency_code' => 'XYZ']);
        $this->assertFalse($this->razorpayPayment->isAvailable($this->cart));
    }

    /** @test */
    public function it_validates_payment_method_availability_by_amount()
    {
        // Test minimum amount
        $this->cart->items()->update(['price' => 0.50, 'total' => 0.50]);
        $this->assertFalse($this->razorpayPayment->isAvailable($this->cart));

        // Test valid amount
        $this->cart->items()->update(['price' => 1000.00, 'total' => 1000.00]);
        $this->assertTrue($this->razorpayPayment->isAvailable($this->cart));

        // Test maximum amount
        $this->cart->items()->update(['price' => 1000000.00, 'total' => 1000000.00]);
        $this->assertTrue($this->razorpayPayment->isAvailable($this->cart));
    }

    /** @test */
    public function it_handles_payment_method_specific_configurations()
    {
        // Test UPI-specific configuration
        Config::set('paymentmethods.razorpay.upi_enabled', true);
        $this->assertTrue($this->razorpayPayment->isUpiEnabled());

        Config::set('paymentmethods.razorpay.upi_enabled', false);
        $this->assertFalse($this->razorpayPayment->isUpiEnabled());

        // Test card-specific configuration
        Config::set('paymentmethods.razorpay.card_enabled', true);
        $this->assertTrue($this->razorpayPayment->isCardEnabled());

        Config::set('paymentmethods.razorpay.card_enabled', false);
        $this->assertFalse($this->razorpayPayment->isCardEnabled());
    }

    /** @test */
    public function it_handles_payment_method_specific_validation()
    {
        // Test UPI validation
        $this->assertTrue($this->razorpayPayment->isValidUpiVpa('test@upi'));
        $this->assertFalse($this->razorpayPayment->isValidUpiVpa('invalid_vpa'));

        // Test card validation
        $this->assertTrue($this->razorpayPayment->isValidCardNumber('4111111111111111'));
        $this->assertFalse($this->razorpayPayment->isValidCardNumber('1234'));

        // Test EMI validation
        $this->assertTrue($this->razorpayPayment->isValidEmiMonths(6));
        $this->assertFalse($this->razorpayPayment->isValidEmiMonths(0));
        $this->assertFalse($this->razorpayPayment->isValidEmiMonths(25));
    }
} 