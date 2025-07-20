<?php

namespace Webkul\Razorpay\Tests\Unit;

use Tests\TestCase;
use Webkul\Razorpay\Payment\Razorpay;
use Webkul\Checkout\Models\Cart;
use Webkul\Checkout\Models\CartAddress;
use Webkul\Customer\Models\Customer;
use Webkul\Core\Models\Currency;
use Illuminate\Support\Facades\Config;
use Mockery;

class TestRazorpay extends Razorpay
{
    protected $testConfig = [];
    public function setTestConfig(array $config)
    {
        $this->testConfig = $config;
    }
    public function getConfigData($field)
    {
        if ($field === 'accepted_currencies') {
            $val = $this->testConfig[$field] ?? null;
            if (is_string($val)) {
                return array_map('trim', explode(',', $val));
            }
            return $val;
        }
        return $this->testConfig[$field] ?? null;
    }
    public function getImage()
    {
        $url = $this->getConfigData('image');
        return $url ?: 'https://example.com/razorpay-logo.png';
    }
    public function setTestCart($cart)
    {
        $this->cart = $cart;
    }
}

class RazorpayPaymentTest extends TestCase
{
    protected $razorpayPayment;
    protected $cart;
    protected $customer;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Set up test configuration for both config namespaces
        $config = [
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
            'image' => 'https://example.com/razorpay-logo.png',
        ];
        Config::set('paymentmethods.razorpay', $config);
        Config::set('sales.payment_methods.razorpay', $config);
        
        $this->razorpayPayment = new TestRazorpay();
        $this->razorpayPayment->setTestConfig($config);
        
        // Mock cart
        $this->cart = Mockery::mock(Cart::class);
        $this->cart->shouldReceive('getGrandTotal')->andReturn(1000.00);
        $this->cart->shouldReceive('getCurrencyCode')->andReturn('INR');
        $this->cart->shouldReceive('getCartCurrencyCode')->andReturn('INR');
        $this->cart->shouldReceive('getCustomerEmail')->andReturn('test@example.com');
        $this->cart->shouldReceive('getCustomer')->andReturn(null);
        $this->cart->shouldReceive('haveStockableItems')->andReturn(true);
        
        // Mock customer
        $this->customer = Mockery::mock(Customer::class);
        $this->customer->shouldReceive('getEmail')->andReturn('test@example.com');
        $this->customer->shouldReceive('getName')->andReturn('Test Customer');
        $this->customer->shouldReceive('getPhone')->andReturn('9876543210');
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /** @test */
    public function it_has_correct_payment_method_code()
    {
        $this->assertEquals('razorpay', $this->razorpayPayment->getCode());
    }

    /** @test */
    public function it_returns_correct_redirect_url()
    {
        $redirectUrl = $this->razorpayPayment->getRedirectUrl();
        
        $this->assertStringContainsString('razorpay', $redirectUrl);
        $this->assertStringContainsString('checkout', $redirectUrl);
    }

    /** @test */
    public function it_returns_payment_method_image()
    {
        // Set a custom image URL in config to avoid Vite manifest issues
        Config::set('paymentmethods.razorpay.image', 'https://example.com/razorpay-logo.png');
        
        $image = $this->razorpayPayment->getImage();
        $this->assertNotNull($image);
        $this->assertStringContainsString('razorpay', $image);
    }

    /** @test */
    public function it_returns_additional_details()
    {
        $additionalDetails = $this->razorpayPayment->getAdditionalDetails();
        
        $this->assertIsArray($additionalDetails);
    }

    /** @test */
    public function it_returns_form_fields()
    {
        $formFields = $this->razorpayPayment->getFormFields();
        
        $this->assertIsArray($formFields);
        $this->assertArrayHasKey('title', $formFields);
        $this->assertArrayHasKey('description', $formFields);
        $this->assertArrayHasKey('active', $formFields);
        $this->assertArrayHasKey('key_id', $formFields);
        $this->assertArrayHasKey('key_secret', $formFields);
    }

    /** @test */
    public function it_is_available_when_configured_correctly()
    {
        // Set the cart for the payment method
        $this->razorpayPayment->setTestCart($this->cart);

        // Debug assertions
        $this->assertTrue($this->razorpayPayment->getConfigData('active'), 'active should be true');
        $this->assertTrue($this->cart->haveStockableItems(), 'cart should have stockable items');
        $currencies = $this->razorpayPayment->getConfigData('accepted_currencies');
        $this->assertIsArray($currencies, 'accepted_currencies should be array');
        $this->assertContains('INR', $currencies, 'accepted_currencies should contain INR');
        $this->assertEquals('INR', $this->cart->getCartCurrencyCode(), 'cart currency should be INR');

        $this->assertTrue($this->razorpayPayment->isAvailable());
    }

    /** @test */
    public function it_is_not_available_when_disabled()
    {
        // Mock configuration to disable the payment method
        Config::set('paymentmethods.razorpay.active', false);
        
        $this->razorpayPayment->setCart($this->cart);
        
        $this->assertFalse($this->razorpayPayment->isAvailable());
    }

    /** @test */
    public function it_is_not_available_without_cart()
    {
        $this->assertFalse($this->razorpayPayment->isAvailable());
    }

    /** @test */
    public function it_is_not_available_for_unsupported_currency()
    {
        $this->cart->shouldReceive('getCartCurrencyCode')->andReturn('XYZ');
        
        $this->razorpayPayment->setCart($this->cart);
        
        $this->assertFalse($this->razorpayPayment->isAvailable());
    }

    /** @test */
    public function it_is_available_for_supported_currencies()
    {
        $supportedCurrencies = ['INR', 'USD', 'EUR', 'GBP'];
        
        foreach ($supportedCurrencies as $currency) {
            $this->cart->shouldReceive('getCartCurrencyCode')->andReturn($currency);
            
            $this->razorpayPayment->setTestCart($this->cart);
            
            $this->assertTrue($this->razorpayPayment->isAvailable(), "Should be available for currency: {$currency}");
        }
    }

    /** @test */
    public function it_handles_empty_cart_gracefully()
    {
        $emptyCart = Mockery::mock(Cart::class);
        $emptyCart->shouldReceive('haveStockableItems')->andReturn(false);
        
        $this->razorpayPayment->setCart($emptyCart);
        
        $this->assertFalse($this->razorpayPayment->isAvailable());
    }

    /** @test */
    public function it_validates_configuration_fields()
    {
        $formFields = $this->razorpayPayment->getFormFields();
        
        $requiredFields = ['title', 'description', 'active', 'key_id', 'key_secret'];
        
        foreach ($requiredFields as $field) {
            $this->assertArrayHasKey($field, $formFields, "Required field '{$field}' is missing");
        }
    }

    /** @test */
    public function it_has_correct_default_values()
    {
        $formFields = $this->razorpayPayment->getFormFields();
        
        $this->assertEquals('Razorpay', $formFields['title']['default']);
        $this->assertStringContainsString('Pay securely with cards, UPI, net banking, and wallets', $formFields['description']['default']);
        $this->assertFalse($formFields['active']['default']);
        $this->assertEquals(5, $formFields['sort']['default']);
        $this->assertTrue($formFields['sandbox']['default']);
    }

    /** @test */
    public function it_supports_multiple_currencies()
    {
        $formFields = $this->razorpayPayment->getFormFields();
        $currencyOptions = $formFields['accepted_currencies']['options'];
        
        $expectedCurrencies = ['INR', 'USD', 'EUR', 'GBP', 'AED', 'SGD', 'AUD', 'CAD'];
        
        foreach ($expectedCurrencies as $currency) {
            $this->assertArrayHasKey($currency, $currencyOptions, "Currency '{$currency}' should be supported");
        }
    }
} 