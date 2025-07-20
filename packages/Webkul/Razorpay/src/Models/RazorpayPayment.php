<?php

namespace Webkul\Razorpay\Models;

class RazorpayPayment
{
    /**
     * Payment ID from Razorpay.
     *
     * @var string
     */
    public $id;

    /**
     * Order ID associated with payment.
     *
     * @var string
     */
    public $order_id;

    /**
     * Payment amount in paise.
     *
     * @var int
     */
    public $amount;

    /**
     * Payment currency.
     *
     * @var string
     */
    public $currency;

    /**
     * Payment method used.
     *
     * @var string
     */
    public $method;

    /**
     * Payment status.
     *
     * @var string
     */
    public $status;

    /**
     * Payment description.
     *
     * @var string
     */
    public $description;

    /**
     * Payment email.
     *
     * @var string
     */
    public $email;

    /**
     * Payment contact.
     *
     * @var string
     */
    public $contact;

    /**
     * Payment name.
     *
     * @var string
     */
    public $name;

    /**
     * Payment notes.
     *
     * @var array
     */
    public $notes;

    /**
     * Payment created at timestamp.
     *
     * @var int
     */
    public $created_at;

    /**
     * Payment captured at timestamp.
     *
     * @var int
     */
    public $captured_at;

    /**
     * Payment method details.
     *
     * @var array
     */
    public $method_details;

    /**
     * Create a new RazorpayPayment instance.
     *
     * @param  array  $data
     * @return void
     */
    public function __construct(array $data = [])
    {
        $this->id = $data['id'] ?? null;
        $this->order_id = $data['order_id'] ?? null;
        $this->amount = $data['amount'] ?? 0;
        $this->currency = $data['currency'] ?? 'INR';
        $this->method = $data['method'] ?? null;
        $this->status = $data['status'] ?? 'created';
        $this->description = $data['description'] ?? '';
        $this->email = $data['email'] ?? '';
        $this->contact = $data['contact'] ?? '';
        $this->name = $data['name'] ?? '';
        $this->notes = $data['notes'] ?? [];
        $this->created_at = $data['created_at'] ?? time();
        $this->captured_at = $data['captured_at'] ?? null;
        $this->method_details = $data['method_details'] ?? [];
    }

    /**
     * Get amount in rupees.
     *
     * @return float
     */
    public function getAmountInRupees()
    {
        return (float) ($this->amount / 100);
    }

    /**
     * Check if payment is captured.
     *
     * @return bool
     */
    public function isCaptured()
    {
        return $this->status === 'captured';
    }

    /**
     * Check if payment is authorized.
     *
     * @return bool
     */
    public function isAuthorized()
    {
        return $this->status === 'authorized';
    }

    /**
     * Check if payment is failed.
     *
     * @return bool
     */
    public function isFailed()
    {
        return $this->status === 'failed';
    }

    /**
     * Check if payment is pending.
     *
     * @return bool
     */
    public function isPending()
    {
        return $this->status === 'pending';
    }

    /**
     * Get payment method display name.
     *
     * @return string
     */
    public function getMethodDisplayName()
    {
        $methodNames = [
            'card' => 'Credit/Debit Card',
            'netbanking' => 'Net Banking',
            'wallet' => 'Digital Wallet',
            'upi' => 'UPI',
            'emi' => 'EMI',
        ];

        return $methodNames[$this->method] ?? ucfirst($this->method);
    }

    /**
     * Get bank name for netbanking payments.
     *
     * @return string|null
     */
    public function getBankName()
    {
        return $this->method_details['bank'] ?? null;
    }

    /**
     * Get wallet name for wallet payments.
     *
     * @return string|null
     */
    public function getWalletName()
    {
        return $this->method_details['wallet'] ?? null;
    }

    /**
     * Get UPI VPA for UPI payments.
     *
     * @return string|null
     */
    public function getUpiVpa()
    {
        return $this->method_details['vpa'] ?? null;
    }

    /**
     * Convert to array.
     *
     * @return array
     */
    public function toArray()
    {
        return [
            'id' => $this->id,
            'order_id' => $this->order_id,
            'amount' => $this->amount,
            'amount_in_rupees' => $this->getAmountInRupees(),
            'currency' => $this->currency,
            'method' => $this->method,
            'method_display_name' => $this->getMethodDisplayName(),
            'status' => $this->status,
            'description' => $this->description,
            'email' => $this->email,
            'contact' => $this->contact,
            'name' => $this->name,
            'notes' => $this->notes,
            'created_at' => $this->created_at,
            'captured_at' => $this->captured_at,
            'method_details' => $this->method_details,
            'is_captured' => $this->isCaptured(),
            'is_authorized' => $this->isAuthorized(),
            'is_failed' => $this->isFailed(),
            'is_pending' => $this->isPending(),
            'bank_name' => $this->getBankName(),
            'wallet_name' => $this->getWalletName(),
            'upi_vpa' => $this->getUpiVpa(),
        ];
    }
} 