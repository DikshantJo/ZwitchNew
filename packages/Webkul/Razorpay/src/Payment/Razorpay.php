<?php

namespace Webkul\Razorpay\Payment;

use Webkul\Payment\Payment\Payment;
use Illuminate\Support\Facades\Storage;

class Razorpay extends Payment
{
    /**
     * Payment method code.
     *
     * @var string
     */
    protected $code = 'razorpay';

    /**
     * Get redirect url.
     *
     * @return string
     */
    public function getRedirectUrl()
    {
        // This will be implemented to redirect to Razorpay checkout
        return route('razorpay.checkout');
    }

    /**
     * Is available.
     *
     * @return bool
     */
    public function isAvailable()
    {
        // Check if payment method is active
        if (! $this->getConfigData('active')) {
            return false;
        }

        // If we have a cart, check if it has stockable items
        if ($this->cart) {
            return $this->cart->haveStockableItems();
        }

        // If no cart is set, just check if the payment method is active
        return true;
    }

    /**
     * Get payment method image.
     *
     * @return string
     */
    public function getImage()
    {
        $url = $this->getConfigData('image');

        return $url ? Storage::url($url) : bagisto_asset('images/razorpay-logo.png', 'shop');
    }

    /**
     * Get payment method additional information.
     *
     * @return array
     */
    public function getAdditionalDetails()
    {
        $instructions = $this->getConfigData('instructions');

        if (empty($instructions)) {
            return [];
        }

        return [
            'title' => trans('razorpay::app.payment.instructions'),
            'value' => $instructions,
        ];
    }

    /**
     * Get payment method form fields.
     *
     * @return array
     */
    public function getFormFields()
    {
        return [
            'title' => [
                'type' => 'text',
                'name' => 'title',
                'label' => trans('razorpay::app.configuration.title'),
                'required' => true,
                'default' => 'Razorpay',
            ],
            'description' => [
                'type' => 'textarea',
                'name' => 'description',
                'label' => trans('razorpay::app.configuration.description'),
                'required' => true,
                'default' => 'Pay securely with cards, UPI, net banking, and wallets',
            ],
            'active' => [
                'type' => 'boolean',
                'name' => 'active',
                'label' => trans('razorpay::app.configuration.active'),
                'required' => true,
                'default' => false,
            ],
            'sort' => [
                'type' => 'number',
                'name' => 'sort',
                'label' => trans('razorpay::app.configuration.sort'),
                'required' => true,
                'default' => 5,
            ],
            'key_id' => [
                'type' => 'text',
                'name' => 'key_id',
                'label' => trans('razorpay::app.configuration.key_id'),
                'required' => true,
                'default' => '',
            ],
            'key_secret' => [
                'type' => 'password',
                'name' => 'key_secret',
                'label' => trans('razorpay::app.configuration.key_secret'),
                'required' => true,
                'default' => '',
            ],
            'webhook_secret' => [
                'type' => 'password',
                'name' => 'webhook_secret',
                'label' => trans('razorpay::app.configuration.webhook_secret'),
                'required' => false,
                'default' => '',
            ],
            'sandbox' => [
                'type' => 'boolean',
                'name' => 'sandbox',
                'label' => trans('razorpay::app.configuration.sandbox'),
                'required' => true,
                'default' => true,
            ],
            'accepted_currencies' => [
                'type' => 'multiselect',
                'name' => 'accepted_currencies',
                'label' => trans('razorpay::app.configuration.accepted_currencies'),
                'required' => true,
                'default' => ['INR'],
                'options' => [
                    'INR' => 'Indian Rupee (INR)',
                    'USD' => 'US Dollar (USD)',
                    'EUR' => 'Euro (EUR)',
                    'GBP' => 'British Pound (GBP)',
                    'AED' => 'UAE Dirham (AED)',
                    'SGD' => 'Singapore Dollar (SGD)',
                    'AUD' => 'Australian Dollar (AUD)',
                    'CAD' => 'Canadian Dollar (CAD)',
                ],
            ],
            'instructions' => [
                'type' => 'textarea',
                'name' => 'instructions',
                'label' => trans('razorpay::app.configuration.instructions'),
                'required' => false,
                'default' => '',
            ],
            'generate_invoice' => [
                'type' => 'boolean',
                'name' => 'generate_invoice',
                'label' => trans('razorpay::app.configuration.generate_invoice'),
                'required' => true,
                'default' => false,
            ],
            'invoice_status' => [
                'type' => 'select',
                'name' => 'invoice_status',
                'label' => trans('razorpay::app.configuration.invoice_status'),
                'required' => true,
                'default' => 'pending',
                'options' => [
                    'pending' => 'Pending',
                    'paid' => 'Paid',
                ],
            ],
            'order_status' => [
                'type' => 'select',
                'name' => 'order_status',
                'label' => trans('razorpay::app.configuration.order_status'),
                'required' => true,
                'default' => 'pending',
                'options' => [
                    'pending' => 'Pending',
                    'processing' => 'Processing',
                    'completed' => 'Completed',
                ],
            ],
        ];
    }
} 