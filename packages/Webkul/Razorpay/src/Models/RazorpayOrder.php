<?php

namespace Webkul\Razorpay\Models;

class RazorpayOrder
{
    /**
     * Order ID from Razorpay.
     *
     * @var string
     */
    public $id;

    /**
     * Order amount in paise.
     *
     * @var int
     */
    public $amount;

    /**
     * Order currency.
     *
     * @var string
     */
    public $currency;

    /**
     * Order receipt.
     *
     * @var string
     */
    public $receipt;

    /**
     * Order status.
     *
     * @var string
     */
    public $status;

    /**
     * Order notes.
     *
     * @var array
     */
    public $notes;

    /**
     * Order created at timestamp.
     *
     * @var int
     */
    public $created_at;

    /**
     * Create a new RazorpayOrder instance.
     *
     * @param  array  $data
     * @return void
     */
    public function __construct(array $data = [])
    {
        $this->id = $data['id'] ?? null;
        $this->amount = $data['amount'] ?? 0;
        $this->currency = $data['currency'] ?? 'INR';
        $this->receipt = $data['receipt'] ?? null;
        $this->status = $data['status'] ?? 'created';
        $this->notes = $data['notes'] ?? [];
        $this->created_at = $data['created_at'] ?? time();
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
     * Check if order is paid.
     *
     * @return bool
     */
    public function isPaid()
    {
        return $this->status === 'paid';
    }

    /**
     * Check if order is attempted.
     *
     * @return bool
     */
    public function isAttempted()
    {
        return $this->status === 'attempted';
    }

    /**
     * Check if order is created.
     *
     * @return bool
     */
    public function isCreated()
    {
        return $this->status === 'created';
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
            'amount' => $this->amount,
            'amount_in_rupees' => $this->getAmountInRupees(),
            'currency' => $this->currency,
            'receipt' => $this->receipt,
            'status' => $this->status,
            'notes' => $this->notes,
            'created_at' => $this->created_at,
            'is_paid' => $this->isPaid(),
            'is_attempted' => $this->isAttempted(),
            'is_created' => $this->isCreated(),
        ];
    }
} 