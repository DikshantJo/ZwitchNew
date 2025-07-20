<?php

return [
    'payment' => [
        'title' => 'Razorpay',
        'description' => 'Pay securely with cards, UPI, net banking, and wallets',
        'instructions' => 'Payment Instructions',
        'success' => 'Payment successful',
        'failed' => 'Payment failed',
        'cancelled' => 'Payment cancelled',
        'processing' => 'Payment processing',
    ],

    'configuration' => [
        'title' => 'Title',
        'description' => 'Description',
        'active' => 'Active',
        'sort' => 'Sort Order',
        'key_id' => 'Key ID',
        'key_secret' => 'Key Secret',
        'webhook_secret' => 'Webhook Secret',
        'sandbox' => 'Sandbox Mode',
        'accepted_currencies' => 'Accepted Currencies',
        'instructions' => 'Instructions',
        'generate_invoice' => 'Generate Invoice',
        'invoice_status' => 'Invoice Status',
        'order_status' => 'Order Status',
    ],

    'errors' => [
        'invalid_signature' => 'Invalid payment signature',
        'payment_failed' => 'Payment failed',
        'order_not_found' => 'Order not found',
        'invalid_amount' => 'Invalid amount',
        'currency_not_supported' => 'Currency not supported',
        'api_error' => 'API error occurred',
    ],

    'messages' => [
        'payment_successful' => 'Payment completed successfully',
        'payment_failed' => 'Payment failed. Please try again.',
        'payment_cancelled' => 'Payment was cancelled',
        'order_created' => 'Order created successfully',
        'refund_processed' => 'Refund processed successfully',
    ],
]; 