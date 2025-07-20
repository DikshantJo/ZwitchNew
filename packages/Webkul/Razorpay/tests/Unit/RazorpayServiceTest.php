<?php

namespace Webkul\Razorpay\Tests\Unit;

use Tests\TestCase;
use Webkul\Razorpay\Services\RazorpayService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Config;
use Mockery;

class RazorpayServiceTest extends TestCase
{
    protected $razorpayService;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Set up test configuration
        Config::set('paymentmethods.razorpay', [
            'active' => true,
            'title' => 'Pay with Razorpay',
            'description' => 'Pay securely with cards, UPI, net banking, and wallets',
            'sort' => 5,
            'key_id' => 'rzp_test_test123',
            'key_secret' => 'test_secret_123',
            'webhook_secret' => 'webhook_secret_123',
            'sandbox' => true,
            'accepted_currencies' => 'INR,USD,EUR,GBP,AED,SGD,AUD,CAD',
            'prefill' => true,
            'theme' => '#3399cc',
            'modal' => true,
            'remember_customer' => true,
            'send_sms_link' => false,
            'send_email_link' => false,
        ]);
        
        // Create RazorpayService instance
        $this->razorpayService = new RazorpayService();
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /** @test */
    public function it_can_create_razorpay_order()
    {
        $expectedResponse = [
            'id' => 'order_test123',
            'entity' => 'order',
            'amount' => 100000,
            'currency' => 'INR',
            'receipt' => 'receipt_test123',
            'status' => 'created'
        ];

        Http::fake([
            'api.razorpay.com/v1/orders' => Http::response($expectedResponse, 200)
        ]);

        $result = $this->razorpayService->createOrder([
            'amount' => 1000.00,
            'currency' => 'INR',
            'receipt' => 'receipt_test123'
        ]);

        $this->assertEquals($expectedResponse, $result);
        $this->assertEquals('order_test123', $result['id']);
        $this->assertEquals(100000, $result['amount']);
        $this->assertEquals('INR', $result['currency']);
    }

    /** @test */
    public function it_can_fetch_payment_details()
    {
        $expectedResponse = [
            'id' => 'pay_test123',
            'entity' => 'payment',
            'amount' => 100000,
            'currency' => 'INR',
            'status' => 'captured',
            'method' => 'card',
            'order_id' => 'order_test123'
        ];

        Http::fake([
            'api.razorpay.com/v1/payments/pay_test123' => Http::response($expectedResponse, 200)
        ]);

        $result = $this->razorpayService->getPaymentDetails('pay_test123');

        $this->assertEquals($expectedResponse, $result);
        $this->assertEquals('pay_test123', $result['id']);
        $this->assertEquals('captured', $result['status']);
    }

    /** @test */
    public function it_can_fetch_order_details()
    {
        $expectedResponse = [
            'id' => 'order_test123',
            'entity' => 'order',
            'amount' => 100000,
            'currency' => 'INR',
            'status' => 'paid',
            'receipt' => 'receipt_test123'
        ];

        Http::fake([
            'api.razorpay.com/v1/orders/order_test123' => Http::response($expectedResponse, 200)
        ]);

        $result = $this->razorpayService->getOrderDetails('order_test123');

        $this->assertEquals($expectedResponse, $result);
        $this->assertEquals('order_test123', $result['id']);
        $this->assertEquals('paid', $result['status']);
    }

    /** @test */
    public function it_can_process_refund()
    {
        $expectedResponse = [
            'id' => 'rfnd_test123',
            'entity' => 'refund',
            'amount' => 100000,
            'currency' => 'INR',
            'payment_id' => 'pay_test123',
            'status' => 'processed'
        ];

        Http::fake([
            'api.razorpay.com/v1/payments/pay_test123/refund' => Http::response($expectedResponse, 200)
        ]);

        $result = $this->razorpayService->processRefund('pay_test123', [
            'amount' => 1000.00,
            'notes' => ['reason' => 'Test refund']
        ]);

        $this->assertEquals($expectedResponse, $result);
        $this->assertEquals('rfnd_test123', $result['id']);
        $this->assertEquals('processed', $result['status']);
    }

    /** @test */
    public function it_can_fetch_refund_details()
    {
        $expectedResponse = [
            'id' => 'rfnd_test123',
            'entity' => 'refund',
            'amount' => 100000,
            'currency' => 'INR',
            'payment_id' => 'pay_test123',
            'status' => 'processed'
        ];

        Http::fake([
            'api.razorpay.com/v1/refunds/rfnd_test123' => Http::response($expectedResponse, 200)
        ]);

        $result = $this->razorpayService->getRefundDetails('rfnd_test123');

        $this->assertEquals($expectedResponse, $result);
        $this->assertEquals('rfnd_test123', $result['id']);
    }

    /** @test */
    public function it_can_verify_payment_signature()
    {
        $orderId = 'order_test123';
        $paymentId = 'pay_test123';
        $secret = 'test_secret_123';
        
        $payload = $orderId . '|' . $paymentId;
        $expectedSignature = hash_hmac('sha256', $payload, $secret);

        $this->assertTrue($this->razorpayService->verifyPaymentSignature($orderId, $paymentId, $expectedSignature));
    }

    /** @test */
    public function it_rejects_invalid_payment_signature()
    {
        $orderId = 'order_test123';
        $paymentId = 'pay_test123';
        $invalidSignature = 'invalid_signature';

        $this->assertFalse($this->razorpayService->verifyPaymentSignature($orderId, $paymentId, $invalidSignature));
    }

    /** @test */
    public function it_can_verify_webhook_signature()
    {
        $payload = '{"order_id":"order_test123","payment_id":"pay_test123","status":"captured"}';
        $secret = 'webhook_secret_123';

        $expectedSignature = hash_hmac('sha256', $payload, $secret);

        $this->assertTrue($this->razorpayService->verifyWebhookSignature($payload, $expectedSignature));
    }

    /** @test */
    public function it_rejects_invalid_webhook_signature()
    {
        $payload = '{"order_id":"order_test123","payment_id":"pay_test123","status":"captured"}';
        $invalidSignature = 'invalid_webhook_signature';

        $this->assertFalse($this->razorpayService->verifyWebhookSignature($payload, $invalidSignature));
    }

    /** @test */
    public function it_handles_api_errors_gracefully()
    {
        $errorResponse = [
            'error' => [
                'code' => 'BAD_REQUEST_ERROR',
                'description' => 'Invalid amount'
            ]
        ];

        Http::fake([
            'api.razorpay.com/v1/orders' => Http::response($errorResponse, 400)
        ]);

        $result = $this->razorpayService->createOrder([
            'amount' => -1000.00,
            'currency' => 'INR'
        ]);

        $this->assertArrayHasKey('error', $result);
        $this->assertStringContainsString('Order creation failed', $result['error']);
    }

    /** @test */
    public function it_handles_network_errors()
    {
        Http::fake([
            'api.razorpay.com/v1/orders' => Http::response('', 500)
        ]);

        $result = $this->razorpayService->createOrder([
            'amount' => 1000.00,
            'currency' => 'INR'
        ]);

        $this->assertArrayHasKey('error', $result);
    }

    /** @test */
    public function it_returns_credentials()
    {
        $credentials = $this->razorpayService->getCredentials();

        $this->assertIsArray($credentials);
        $this->assertArrayHasKey('key_id', $credentials);
        $this->assertArrayHasKey('key_secret', $credentials);
        $this->assertArrayHasKey('sandbox', $credentials);
        $this->assertEquals('rzp_test_test123', $credentials['key_id']);
        $this->assertEquals('test_secret_123', $credentials['key_secret']);
        $this->assertTrue($credentials['sandbox']);
    }

    /** @test */
    public function it_checks_if_configured()
    {
        $this->assertTrue($this->razorpayService->isConfigured());

        // Test with empty credentials
        Config::set('paymentmethods.razorpay.key_id', '');
        Config::set('paymentmethods.razorpay.key_secret', '');
        
        $service = new RazorpayService();
        $this->assertFalse($service->isConfigured());
    }

    /** @test */
    public function it_can_capture_payment()
    {
        $expectedResponse = [
            'id' => 'pay_test123',
            'entity' => 'payment',
            'amount' => 100000,
            'currency' => 'INR',
            'status' => 'captured'
        ];

        Http::fake([
            'api.razorpay.com/v1/payments/pay_test123/capture' => Http::response($expectedResponse, 200)
        ]);

        $result = $this->razorpayService->capturePayment('pay_test123', [
            'amount' => 1000.00,
            'currency' => 'INR'
        ]);

        $this->assertEquals($expectedResponse, $result);
        $this->assertEquals('captured', $result['status']);
    }

    /** @test */
    public function it_can_create_payment_link()
    {
        $expectedResponse = [
            'id' => 'plink_test123',
            'entity' => 'payment_link',
            'amount' => 100000,
            'currency' => 'INR',
            'status' => 'created'
        ];

        Http::fake([
            'api.razorpay.com/v1/payment_links' => Http::response($expectedResponse, 200)
        ]);

        $result = $this->razorpayService->createPaymentLink([
            'amount' => 1000.00,
            'currency' => 'INR',
            'reference_id' => 'ref_test123'
        ]);

        $this->assertEquals($expectedResponse, $result);
        $this->assertEquals('plink_test123', $result['id']);
    }

    /** @test */
    public function it_can_generate_upi_qr_code()
    {
        $expectedResponse = [
            'id' => 'qr_test123',
            'entity' => 'qr_code',
            'name' => 'Test QR Code',
            'usage' => 'single_use',
            'type' => 'upi_qr',
            'image_url' => 'https://example.com/qr.png'
        ];

        Http::fake([
            'api.razorpay.com/v1/payments/qr_codes' => Http::response($expectedResponse, 200)
        ]);

        $result = $this->razorpayService->generateUpiQrCode([
            'name' => 'Test QR Code',
            'amount' => 1000, // Add the required amount field
            'description' => 'Test QR code for payment',
            'fixed_amount' => true,
            'payment_amount' => 100000
        ]);

        $this->assertEquals($expectedResponse, $result);
        $this->assertEquals('qr_test123', $result['id']);
        $this->assertEquals('upi_qr', $result['type']);
    }

    /** @test */
    public function it_returns_supported_payment_methods()
    {
        $methods = $this->razorpayService->getSupportedPaymentMethods();

        $this->assertIsArray($methods);
        $this->assertArrayHasKey('card', $methods);
        $this->assertArrayHasKey('netbanking', $methods);
        $this->assertArrayHasKey('wallet', $methods);
        $this->assertArrayHasKey('upi', $methods);
        $this->assertEquals('Credit/Debit Cards', $methods['card']);
        $this->assertEquals('Net Banking', $methods['netbanking']);
        $this->assertEquals('Digital Wallets', $methods['wallet']);
        $this->assertEquals('UPI', $methods['upi']);
    }

    /** @test */
    public function it_returns_supported_currencies()
    {
        $currencies = $this->razorpayService->getSupportedCurrencies();

        $this->assertIsArray($currencies);
        $this->assertArrayHasKey('INR', $currencies);
        $this->assertArrayHasKey('USD', $currencies);
        $this->assertArrayHasKey('EUR', $currencies);
        $this->assertEquals('Indian Rupee', $currencies['INR']);
        $this->assertEquals('US Dollar', $currencies['USD']);
        $this->assertEquals('Euro', $currencies['EUR']);
    }

    /** @test */
    public function it_can_test_api_connectivity()
    {
        Http::fake([
            'api.razorpay.com/v1/payments' => Http::response(['count' => 0], 200)
        ]);

        $result = $this->razorpayService->testApiConnectivity();

        $this->assertIsArray($result);
        $this->assertArrayHasKey('success', $result);
        $this->assertArrayHasKey('message', $result);
        $this->assertTrue($result['success']);
        $this->assertEquals('API connection successful', $result['message']);
    }

    /** @test */
    public function it_validates_payment_amount()
    {
        $this->assertTrue($this->razorpayService->validatePaymentAmount(100.00, 'INR'));
        $this->assertTrue($this->razorpayService->validatePaymentAmount(10.00, 'USD'));
        $this->assertFalse($this->razorpayService->validatePaymentAmount(-100.00, 'INR'));
        $this->assertFalse($this->razorpayService->validatePaymentAmount(0, 'INR'));
    }
} 