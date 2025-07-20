<?php

namespace Webkul\Razorpay\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;

class RazorpayService
{
    /**
     * Razorpay API base URL.
     *
     * @var string
     */
    protected $baseUrl = 'https://api.razorpay.com/v1';

    /**
     * Razorpay key ID.
     *
     * @var string
     */
    protected $keyId;

    /**
     * Razorpay key secret.
     *
     * @var string
     */
    protected $keySecret;

    /**
     * Is sandbox mode.
     *
     * @var bool
     */
    protected $sandbox;

    /**
     * Constructor.
     */
    public function __construct()
    {
        try {
            // Try to get configuration using core() helper
            if (function_exists('core')) {
                $this->keyId = core()->getConfigData('sales.payment_methods.razorpay.key_id');
                $this->keySecret = core()->getConfigData('sales.payment_methods.razorpay.key_secret');
                $this->sandbox = core()->getConfigData('sales.payment_methods.razorpay.sandbox', true);
            } else {
                // Fallback to direct database query
                $this->keyId = \Webkul\Core\Models\CoreConfig::where('code', 'sales.payment_methods.razorpay.key_id')->value('value');
                $this->keySecret = \Webkul\Core\Models\CoreConfig::where('code', 'sales.payment_methods.razorpay.key_secret')->value('value');
                $this->sandbox = \Webkul\Core\Models\CoreConfig::where('code', 'sales.payment_methods.razorpay.sandbox')->value('value') ?? true;
            }
            
            // Set sandbox URL if in sandbox mode
            if ($this->sandbox) {
                $this->baseUrl = 'https://api.razorpay.com/v1';
            }
            
            // Ensure sandbox is boolean
            $this->sandbox = (bool) $this->sandbox;
            
            // Log configuration status
            Log::info('RazorpayService initialized', [
                'key_id_set' => !empty($this->keyId),
                'key_secret_set' => !empty($this->keySecret),
                'sandbox' => $this->sandbox,
            ]);
        } catch (\Exception $e) {
            Log::error('RazorpayService initialization failed: ' . $e->getMessage());
        }
    }

    /**
     * Create a Razorpay order.
     *
     * @param  array  $data
     * @return array
     */
    public function createOrder($data)
    {
        try {
            $httpClient = Http::withBasicAuth($this->keyId, $this->keySecret);
            
            // Disable SSL verification in development environment
            if (app()->environment('local', 'development')) {
                $httpClient = $httpClient->withoutVerifying();
            }
            
            $response = $httpClient->post($this->baseUrl . '/orders', [
                'amount' => $data['amount'] * 100, // Convert to paise
                'currency' => $data['currency'] ?? 'INR',
                'receipt' => $data['receipt'] ?? 'order_' . time(),
                'notes' => $data['notes'] ?? [],
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('Razorpay order creation failed: ' . $response->body());
            return ['error' => 'Order creation failed'];
        } catch (\Exception $e) {
            Log::error('Razorpay order creation exception: ' . $e->getMessage());
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Verify payment signature.
     *
     * @param  string  $orderId
     * @param  string  $paymentId
     * @param  string  $signature
     * @return bool
     */
    public function verifyPaymentSignature($orderId, $paymentId, $signature)
    {
        try {
            $payload = $orderId . '|' . $paymentId;
            $expectedSignature = hash_hmac('sha256', $payload, $this->keySecret);

            return hash_equals($expectedSignature, $signature);
        } catch (\Exception $e) {
            Log::error('Razorpay signature verification failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get payment details.
     *
     * @param  string  $paymentId
     * @return array
     */
    public function getPaymentDetails($paymentId)
    {
        try {
            $httpClient = Http::withBasicAuth($this->keyId, $this->keySecret);
            
            // Disable SSL verification in development environment
            if (app()->environment('local', 'development')) {
                $httpClient = $httpClient->withoutVerifying();
            }
            
            $response = $httpClient->get($this->baseUrl . '/payments/' . $paymentId);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('Razorpay payment details failed: ' . $response->body());
            return ['error' => 'Payment details retrieval failed'];
        } catch (\Exception $e) {
            Log::error('Razorpay payment details exception: ' . $e->getMessage());
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Process refund.
     *
     * @param  string  $paymentId
     * @param  array   $data
     * @return array
     */
    public function processRefund($paymentId, $data)
    {
        try {
            $httpClient = Http::withBasicAuth($this->keyId, $this->keySecret);
            
            // Disable SSL verification in development environment
            if (app()->environment('local', 'development')) {
                $httpClient = $httpClient->withoutVerifying();
            }
            
            $response = $httpClient->post($this->baseUrl . '/payments/' . $paymentId . '/refund', [
                'amount' => $data['amount'] * 100, // Convert to paise
                'speed' => $data['speed'] ?? 'normal',
                'notes' => $data['notes'] ?? [],
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('Razorpay refund failed: ' . $response->body());
            return ['error' => 'Refund processing failed'];
        } catch (\Exception $e) {
            Log::error('Razorpay refund exception: ' . $e->getMessage());
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Verify webhook signature.
     *
     * @param  string  $payload
     * @param  string  $signature
     * @return bool
     */
    public function verifyWebhookSignature($payload, $signature)
    {
        try {
            $webhookSecret = core()->getConfigData('sales.payment_methods.razorpay.webhook_secret');
            $expectedSignature = hash_hmac('sha256', $payload, $webhookSecret);

            return hash_equals($expectedSignature, $signature);
        } catch (\Exception $e) {
            Log::error('Razorpay webhook signature verification failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get API credentials.
     *
     * @return array
     */
    public function getCredentials()
    {
        return [
            'key_id' => $this->keyId,
            'key_secret' => $this->keySecret,
            'sandbox' => $this->sandbox,
        ];
    }

    /**
     * Check if service is configured.
     *
     * @return bool
     */
    public function isConfigured()
    {
        return ! empty($this->keyId) && ! empty($this->keySecret);
    }

    /**
     * Get order details from Razorpay.
     *
     * @param  string  $orderId
     * @return array
     */
    public function getOrderDetails($orderId)
    {
        try {
            $httpClient = Http::withBasicAuth($this->keyId, $this->keySecret);
            
            // Disable SSL verification in development environment
            if (app()->environment('local', 'development')) {
                $httpClient = $httpClient->withoutVerifying();
            }
            
            $response = $httpClient->get($this->baseUrl . '/orders/' . $orderId);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('Razorpay order details failed: ' . $response->body());
            return ['error' => 'Order details retrieval failed'];
        } catch (\Exception $e) {
            Log::error('Razorpay order details exception: ' . $e->getMessage());
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Capture payment.
     *
     * @param  string  $paymentId
     * @param  array   $data
     * @return array
     */
    public function capturePayment($paymentId, $data)
    {
        try {
            $httpClient = Http::withBasicAuth($this->keyId, $this->keySecret);
            
            // Disable SSL verification in development environment
            if (app()->environment('local', 'development')) {
                $httpClient = $httpClient->withoutVerifying();
            }
            
            $response = $httpClient->post($this->baseUrl . '/payments/' . $paymentId . '/capture', [
                'amount' => $data['amount'] * 100, // Convert to paise
                'currency' => $data['currency'] ?? 'INR',
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('Razorpay payment capture failed: ' . $response->body());
            return ['error' => 'Payment capture failed'];
        } catch (\Exception $e) {
            Log::error('Razorpay payment capture exception: ' . $e->getMessage());
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Get refund details.
     *
     * @param  string  $refundId
     * @return array
     */
    public function getRefundDetails($refundId)
    {
        try {
            $response = Http::withBasicAuth($this->keyId, $this->keySecret)
                ->get($this->baseUrl . '/refunds/' . $refundId);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('Razorpay refund details failed: ' . $response->body());
            return ['error' => 'Refund details retrieval failed'];
        } catch (\Exception $e) {
            Log::error('Razorpay refund details exception: ' . $e->getMessage());
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Create payment link.
     *
     * @param  array  $data
     * @return array
     */
    public function createPaymentLink($data)
    {
        try {
            $response = Http::withBasicAuth($this->keyId, $this->keySecret)
                ->post($this->baseUrl . '/payment_links', [
                    'amount' => $data['amount'] * 100, // Convert to paise
                    'currency' => $data['currency'] ?? 'INR',
                    'accept_partial' => $data['accept_partial'] ?? false,
                    'reference_id' => $data['reference_id'] ?? 'link_' . time(),
                    'description' => $data['description'] ?? '',
                    'callback_url' => $data['callback_url'] ?? '',
                    'callback_method' => $data['callback_method'] ?? 'get',
                ]);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('Razorpay payment link creation failed: ' . $response->body());
            return ['error' => 'Payment link creation failed'];
        } catch (\Exception $e) {
            Log::error('Razorpay payment link creation exception: ' . $e->getMessage());
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Generate UPI QR code.
     *
     * @param  array  $data
     * @return array
     */
    public function generateUpiQrCode($data)
    {
        try {
            $response = Http::withBasicAuth($this->keyId, $this->keySecret)
                ->post($this->baseUrl . '/payments/qr_codes', [
                    'type' => 'upi_qr',
                    'name' => $data['name'] ?? 'QR Code',
                    'usage' => 'single_use',
                    'fixed_amount' => true,
                    'payment_amount' => $data['amount'] * 100, // Convert to paise
                    'description' => $data['description'] ?? '',
                    'customer_id' => $data['customer_id'] ?? null,
                ]);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('Razorpay UPI QR code generation failed: ' . $response->body());
            return ['error' => 'UPI QR code generation failed'];
        } catch (\Exception $e) {
            Log::error('Razorpay UPI QR code generation exception: ' . $e->getMessage());
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Validate payment amount.
     *
     * @param  float  $amount
     * @param  string  $currency
     * @return bool
     */
    public function validatePaymentAmount($amount, $currency = 'INR')
    {
        // Minimum amount validation (in paise)
        $minAmount = 100; // 1 INR
        
        // Maximum amount validation (in paise)
        $maxAmount = 9999999999; // 99,99,99,999 INR
        
        $amountInPaise = $amount * 100;
        
        return $amountInPaise >= $minAmount && $amountInPaise <= $maxAmount;
    }

    /**
     * Get supported payment methods.
     *
     * @return array
     */
    public function getSupportedPaymentMethods()
    {
        return [
            'card' => 'Credit/Debit Cards',
            'netbanking' => 'Net Banking',
            'wallet' => 'Digital Wallets',
            'upi' => 'UPI',
            'emi' => 'EMI',
        ];
    }

    /**
     * Get supported currencies.
     *
     * @return array
     */
    public function getSupportedCurrencies()
    {
        return [
            'INR' => 'Indian Rupee',
            'USD' => 'US Dollar',
            'EUR' => 'Euro',
            'GBP' => 'British Pound',
            'AED' => 'UAE Dirham',
            'SGD' => 'Singapore Dollar',
            'AUD' => 'Australian Dollar',
            'CAD' => 'Canadian Dollar',
        ];
    }

    /**
     * Test API connectivity.
     *
     * @return array
     */
    public function testApiConnectivity()
    {
        try {
            // Check if credentials are configured
            if (empty($this->keyId) || empty($this->keySecret)) {
                return ['success' => false, 'message' => 'API credentials not configured'];
            }

            $httpClient = Http::withBasicAuth($this->keyId, $this->keySecret);
            
            // Disable SSL verification in development environment
            if (app()->environment('local', 'development')) {
                $httpClient = $httpClient->withoutVerifying();
            }
            
            $response = $httpClient->get($this->baseUrl . '/payments');

            if ($response->successful()) {
                return ['success' => true, 'message' => 'API connection successful'];
            }

            return ['success' => false, 'message' => 'API connection failed: ' . $response->status()];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'API connection exception: ' . $e->getMessage()];
        }
    }
} 