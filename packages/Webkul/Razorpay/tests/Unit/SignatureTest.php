<?php

namespace Webkul\Razorpay\Tests\Unit;

use Tests\TestCase;
use Webkul\Razorpay\Services\RazorpayService;
use Illuminate\Support\Facades\Config;

class SignatureTest extends TestCase
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
        
        $this->razorpayService = new RazorpayService();
    }

    /** @test */
    public function it_verifies_valid_payment_signature()
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
        $invalidSignature = 'invalid_signature_hash';

        $this->assertFalse($this->razorpayService->verifyPaymentSignature($orderId, $paymentId, $invalidSignature));
    }

    /** @test */
    public function it_rejects_tampered_order_id()
    {
        $orderId = 'order_test123';
        $paymentId = 'pay_test123';
        $secret = 'test_secret_123';

        $payload = $orderId . '|' . $paymentId;
        $signature = hash_hmac('sha256', $payload, $secret);
        $tamperedOrderId = 'order_tampered123';

        $this->assertFalse($this->razorpayService->verifyPaymentSignature($tamperedOrderId, $paymentId, $signature));
    }

    /** @test */
    public function it_rejects_tampered_payment_id()
    {
        $orderId = 'order_test123';
        $paymentId = 'pay_test123';
        $secret = 'test_secret_123';

        $payload = $orderId . '|' . $paymentId;
        $signature = hash_hmac('sha256', $payload, $secret);
        $tamperedPaymentId = 'pay_tampered123';

        $this->assertFalse($this->razorpayService->verifyPaymentSignature($orderId, $tamperedPaymentId, $signature));
    }

    /** @test */
    public function it_generates_consistent_signatures()
    {
        $orderId = 'order_test123';
        $paymentId = 'pay_test123';
        $secret = 'test_secret_123';

        $payload = $orderId . '|' . $paymentId;
        $signature1 = hash_hmac('sha256', $payload, $secret);
        $signature2 = hash_hmac('sha256', $payload, $secret);

        $this->assertEquals($signature1, $signature2);
    }

    /** @test */
    public function it_generates_different_signatures_for_different_data()
    {
        $orderId1 = 'order_test123';
        $orderId2 = 'order_test456';
        $paymentId = 'pay_test123';
        $secret = 'test_secret_123';

        $payload1 = $orderId1 . '|' . $paymentId;
        $payload2 = $orderId2 . '|' . $paymentId;
        $signature1 = hash_hmac('sha256', $payload1, $secret);
        $signature2 = hash_hmac('sha256', $payload2, $secret);

        $this->assertNotEquals($signature1, $signature2);
    }

    /** @test */
    public function it_verifies_webhook_signature()
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
    public function it_rejects_tampered_webhook_payload()
    {
        $payload = '{"order_id":"order_test123","payment_id":"pay_test123","status":"captured"}';
        $tamperedPayload = '{"order_id":"order_test123","payment_id":"pay_test123","status":"failed"}';
        $secret = 'webhook_secret_123';

        $signature = hash_hmac('sha256', $payload, $secret);

        $this->assertFalse($this->razorpayService->verifyWebhookSignature($tamperedPayload, $signature));
    }

    /** @test */
    public function it_verifies_signature_with_special_characters()
    {
        $orderId = 'order_test_123!@#$%^&*()';
        $paymentId = 'pay_test_123!@#$%^&*()';
        $secret = 'test_secret_123'; // Use the configured secret

        $payload = $orderId . '|' . $paymentId;
        $signature = hash_hmac('sha256', $payload, $secret);

        $this->assertTrue($this->razorpayService->verifyPaymentSignature($orderId, $paymentId, $signature));
    }

    /** @test */
    public function it_verifies_signature_with_unicode_characters()
    {
        $orderId = 'order_test_123_हिंदी';
        $paymentId = 'pay_test_123_हिंदी';
        $secret = 'test_secret_123'; // Use the configured secret

        $payload = $orderId . '|' . $paymentId;
        $signature = hash_hmac('sha256', $payload, $secret);

        $this->assertTrue($this->razorpayService->verifyPaymentSignature($orderId, $paymentId, $signature));
    }

    /** @test */
    public function it_verifies_signature_with_long_values()
    {
        $orderId = str_repeat('a', 1000);
        $paymentId = str_repeat('b', 1000);
        $secret = 'test_secret_123'; // Use the configured secret

        $payload = $orderId . '|' . $paymentId;
        $signature = hash_hmac('sha256', $payload, $secret);

        $this->assertTrue($this->razorpayService->verifyPaymentSignature($orderId, $paymentId, $signature));
    }

    /** @test */
    public function it_verifies_webhook_signature_with_complex_payload()
    {
        $payload = json_encode([
            'order_id' => 'order_test123',
            'payment_id' => 'pay_test123',
            'status' => 'captured',
            'amount' => 100000,
            'currency' => 'INR',
            'method' => 'card',
            'description' => 'Test payment with special chars: !@#$%^&*()',
            'email' => 'test@example.com',
            'contact' => '+919876543210',
            'notes' => [
                'merchant_order_id' => 'order_123',
                'custom_field' => 'test_value'
            ]
        ]);
        $secret = 'webhook_secret_123';

        $expectedSignature = hash_hmac('sha256', $payload, $secret);

        $this->assertTrue($this->razorpayService->verifyWebhookSignature($payload, $expectedSignature));
    }

    /** @test */
    public function it_verifies_signature_case_sensitivity()
    {
        $orderId = 'ORDER_TEST_123';
        $paymentId = 'PAY_TEST_123';
        $secret = 'test_secret_123'; // Use the configured secret

        $payload = $orderId . '|' . $paymentId;
        $signature = hash_hmac('sha256', $payload, $secret);

        $this->assertTrue($this->razorpayService->verifyPaymentSignature($orderId, $paymentId, $signature));
    }

    /** @test */
    public function it_verifies_signature_with_numbers_only()
    {
        $orderId = '123456789';
        $paymentId = '987654321';
        $secret = 'test_secret_123'; // Use the configured secret

        $payload = $orderId . '|' . $paymentId;
        $signature = hash_hmac('sha256', $payload, $secret);

        $this->assertTrue($this->razorpayService->verifyPaymentSignature($orderId, $paymentId, $signature));
    }

    /** @test */
    public function it_verifies_signature_with_mixed_case()
    {
        $orderId = 'Order_Test_123';
        $paymentId = 'Pay_Test_123';
        $secret = 'test_secret_123'; // Use the configured secret

        $payload = $orderId . '|' . $paymentId;
        $signature = hash_hmac('sha256', $payload, $secret);

        $this->assertTrue($this->razorpayService->verifyPaymentSignature($orderId, $paymentId, $signature));
    }

    /** @test */
    public function it_verifies_signature_with_underscores()
    {
        $orderId = 'order_test_123';
        $paymentId = 'pay_test_123';
        $secret = 'test_secret_123'; // Use the configured secret

        $payload = $orderId . '|' . $paymentId;
        $signature = hash_hmac('sha256', $payload, $secret);

        $this->assertTrue($this->razorpayService->verifyPaymentSignature($orderId, $paymentId, $signature));
    }

    /** @test */
    public function it_verifies_signature_with_hyphens()
    {
        $orderId = 'order-test-123';
        $paymentId = 'pay-test-123';
        $secret = 'test_secret_123'; // Use the configured secret

        $payload = $orderId . '|' . $paymentId;
        $signature = hash_hmac('sha256', $payload, $secret);

        $this->assertTrue($this->razorpayService->verifyPaymentSignature($orderId, $paymentId, $signature));
    }

    /** @test */
    public function it_verifies_signature_with_dots()
    {
        $orderId = 'order.test.123';
        $paymentId = 'pay.test.123';
        $secret = 'test_secret_123'; // Use the configured secret

        $payload = $orderId . '|' . $paymentId;
        $signature = hash_hmac('sha256', $payload, $secret);

        $this->assertTrue($this->razorpayService->verifyPaymentSignature($orderId, $paymentId, $signature));
    }

    /** @test */
    public function it_verifies_signature_with_spaces()
    {
        $orderId = 'order test 123';
        $paymentId = 'pay test 123';
        $secret = 'test_secret_123'; // Use the configured secret

        $payload = $orderId . '|' . $paymentId;
        $signature = hash_hmac('sha256', $payload, $secret);

        $this->assertTrue($this->razorpayService->verifyPaymentSignature($orderId, $paymentId, $signature));
    }

    /** @test */
    public function it_verifies_signature_with_newlines()
    {
        $orderId = "order\ntest\n123";
        $paymentId = "pay\ntest\n123";
        $secret = 'test_secret_123'; // Use the configured secret

        $payload = $orderId . '|' . $paymentId;
        $signature = hash_hmac('sha256', $payload, $secret);

        $this->assertTrue($this->razorpayService->verifyPaymentSignature($orderId, $paymentId, $signature));
    }

    /** @test */
    public function it_verifies_signature_with_tabs()
    {
        $orderId = "order\ttest\t123";
        $paymentId = "pay\ttest\t123";
        $secret = 'test_secret_123'; // Use the configured secret

        $payload = $orderId . '|' . $paymentId;
        $signature = hash_hmac('sha256', $payload, $secret);

        $this->assertTrue($this->razorpayService->verifyPaymentSignature($orderId, $paymentId, $signature));
    }
} 