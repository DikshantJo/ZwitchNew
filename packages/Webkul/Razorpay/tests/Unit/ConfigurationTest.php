<?php

namespace Webkul\Razorpay\Tests\Unit;

use Tests\TestCase;
use Webkul\Razorpay\Payment\Razorpay;
use Illuminate\Support\Facades\Config;

class ConfigurationTest extends TestCase
{
    protected $razorpayPayment;

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
        
        $this->razorpayPayment = new Razorpay();
    }

    /** @test */
    public function it_has_form_fields()
    {
        $formFields = $this->razorpayPayment->getFormFields();

        $this->assertIsArray($formFields);
        $this->assertArrayHasKey('title', $formFields);
        $this->assertArrayHasKey('description', $formFields);
        $this->assertArrayHasKey('active', $formFields);
        $this->assertArrayHasKey('sort', $formFields);
        $this->assertArrayHasKey('key_id', $formFields);
        $this->assertArrayHasKey('key_secret', $formFields);
        $this->assertArrayHasKey('webhook_secret', $formFields);
        $this->assertArrayHasKey('sandbox', $formFields);
        $this->assertArrayHasKey('accepted_currencies', $formFields);
        $this->assertArrayHasKey('instructions', $formFields);
        $this->assertArrayHasKey('generate_invoice', $formFields);
        $this->assertArrayHasKey('invoice_status', $formFields);
        $this->assertArrayHasKey('order_status', $formFields);
    }

    /** @test */
    public function it_validates_required_form_fields()
    {
        $formFields = $this->razorpayPayment->getFormFields();

        $requiredFields = [
            'title', 'description', 'active', 'sort', 'key_id', 
            'key_secret', 'webhook_secret', 'sandbox'
        ];

        foreach ($requiredFields as $field) {
            $this->assertArrayHasKey($field, $formFields, "Required field '{$field}' is missing from form fields");
        }
    }

    /** @test */
    public function it_validates_form_field_data_types()
    {
        $formFields = $this->razorpayPayment->getFormFields();

        $this->assertEquals('text', $formFields['title']['type']);
        $this->assertEquals('textarea', $formFields['description']['type']);
        $this->assertEquals('boolean', $formFields['active']['type']);
        $this->assertEquals('number', $formFields['sort']['type']);
        $this->assertEquals('text', $formFields['key_id']['type']);
        $this->assertEquals('password', $formFields['key_secret']['type']);
        $this->assertEquals('password', $formFields['webhook_secret']['type']);
        $this->assertEquals('boolean', $formFields['sandbox']['type']);
        $this->assertEquals('multiselect', $formFields['accepted_currencies']['type']);
    }

    /** @test */
    public function it_validates_form_field_default_values()
    {
        $formFields = $this->razorpayPayment->getFormFields();

        $this->assertEquals('Razorpay', $formFields['title']['default']);
        $this->assertStringContainsString('Pay securely with cards, UPI, net banking, and wallets', $formFields['description']['default']);
        $this->assertFalse($formFields['active']['default']);
        $this->assertEquals(5, $formFields['sort']['default']);
        $this->assertTrue($formFields['sandbox']['default']);
        $this->assertEquals('', $formFields['key_id']['default']);
        $this->assertEquals('', $formFields['key_secret']['default']);
        $this->assertEquals('', $formFields['webhook_secret']['default']);
    }

    /** @test */
    public function it_validates_currency_options()
    {
        $formFields = $this->razorpayPayment->getFormFields();
        $currencyOptions = $formFields['accepted_currencies']['options'];

        $expectedCurrencies = [
            'INR' => 'Indian Rupee (INR)',
            'USD' => 'US Dollar (USD)',
            'EUR' => 'Euro (EUR)',
            'GBP' => 'British Pound (GBP)',
            'AED' => 'UAE Dirham (AED)',
            'SGD' => 'Singapore Dollar (SGD)',
            'AUD' => 'Australian Dollar (AUD)',
            'CAD' => 'Canadian Dollar (CAD)'
        ];

        foreach ($expectedCurrencies as $code => $name) {
            $this->assertArrayHasKey($code, $currencyOptions, "Currency '{$code}' should be available");
            $this->assertEquals($name, $currencyOptions[$code], "Currency '{$code}' should have correct name");
        }
    }

    /** @test */
    public function it_validates_invoice_status_options()
    {
        $formFields = $this->razorpayPayment->getFormFields();
        $invoiceStatusOptions = $formFields['invoice_status']['options'];

        $this->assertArrayHasKey('pending', $invoiceStatusOptions);
        $this->assertArrayHasKey('paid', $invoiceStatusOptions);
        $this->assertEquals('Pending', $invoiceStatusOptions['pending']);
        $this->assertEquals('Paid', $invoiceStatusOptions['paid']);
    }

    /** @test */
    public function it_validates_order_status_options()
    {
        $formFields = $this->razorpayPayment->getFormFields();
        $orderStatusOptions = $formFields['order_status']['options'];

        $this->assertArrayHasKey('pending', $orderStatusOptions);
        $this->assertArrayHasKey('processing', $orderStatusOptions);
        $this->assertArrayHasKey('completed', $orderStatusOptions);
        $this->assertEquals('Pending', $orderStatusOptions['pending']);
        $this->assertEquals('Processing', $orderStatusOptions['processing']);
        $this->assertEquals('Completed', $orderStatusOptions['completed']);
    }

    /** @test */
    public function it_validates_form_field_labels()
    {
        $formFields = $this->razorpayPayment->getFormFields();

        $this->assertArrayHasKey('label', $formFields['title']);
        $this->assertArrayHasKey('label', $formFields['description']);
        $this->assertArrayHasKey('label', $formFields['active']);
        $this->assertArrayHasKey('label', $formFields['sort']);
        $this->assertArrayHasKey('label', $formFields['key_id']);
        $this->assertArrayHasKey('label', $formFields['key_secret']);
        $this->assertArrayHasKey('label', $formFields['webhook_secret']);
        $this->assertArrayHasKey('label', $formFields['sandbox']);
    }

    /** @test */
    public function it_validates_form_field_requirements()
    {
        $formFields = $this->razorpayPayment->getFormFields();

        $requiredFields = ['title', 'description', 'active', 'sort', 'key_id', 'key_secret', 'sandbox'];

        foreach ($requiredFields as $field) {
            $this->assertTrue($formFields[$field]['required'], "Field '{$field}' should be required");
        }

        // Webhook secret is optional
        $this->assertFalse($formFields['webhook_secret']['required'], 'Webhook secret should be optional');
    }

    /** @test */
    public function it_validates_form_field_structure()
    {
        $formFields = $this->razorpayPayment->getFormFields();

        foreach ($formFields as $fieldName => $fieldConfig) {
            $this->assertIsString($fieldName, "Field name should be string");
            $this->assertIsArray($fieldConfig, "Field config should be array");
            $this->assertArrayHasKey('type', $fieldConfig, "Field '{$fieldName}' should have type");
            $this->assertArrayHasKey('name', $fieldConfig, "Field '{$fieldName}' should have name");
            $this->assertArrayHasKey('label', $fieldConfig, "Field '{$fieldName}' should have label");
            $this->assertArrayHasKey('required', $fieldConfig, "Field '{$fieldName}' should have required flag");
        }
    }

    /** @test */
    public function it_validates_form_field_names()
    {
        $formFields = $this->razorpayPayment->getFormFields();

        foreach ($formFields as $fieldName => $fieldConfig) {
            $this->assertEquals($fieldName, $fieldConfig['name'], "Field name should match config name");
        }
    }

    /** @test */
    public function it_validates_default_currency_configuration()
    {
        $formFields = $this->razorpayPayment->getFormFields();
        $defaultCurrencies = $formFields['accepted_currencies']['default'];

        $this->assertIsArray($defaultCurrencies);
        $this->assertContains('INR', $defaultCurrencies, 'INR should be in default currencies');
        $this->assertCount(1, $defaultCurrencies, 'Should have only INR as default currency');
    }

    /** @test */
    public function it_validates_default_status_configurations()
    {
        $formFields = $this->razorpayPayment->getFormFields();

        $this->assertEquals('pending', $formFields['invoice_status']['default']);
        $this->assertEquals('pending', $formFields['order_status']['default']);
    }

    /** @test */
    public function it_validates_form_field_types()
    {
        $formFields = $this->razorpayPayment->getFormFields();

        $expectedTypes = [
            'title' => 'text',
            'description' => 'textarea',
            'active' => 'boolean',
            'sort' => 'number',
            'key_id' => 'text',
            'key_secret' => 'password',
            'webhook_secret' => 'password',
            'sandbox' => 'boolean',
            'accepted_currencies' => 'multiselect',
            'instructions' => 'textarea',
            'generate_invoice' => 'boolean',
            'invoice_status' => 'select',
            'order_status' => 'select'
        ];

        foreach ($expectedTypes as $field => $expectedType) {
            $this->assertEquals($expectedType, $formFields[$field]['type'], "Field '{$field}' should have type '{$expectedType}'");
        }
    }
} 