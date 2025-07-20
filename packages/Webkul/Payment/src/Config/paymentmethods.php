<?php

return [
    'cashondelivery'  => [
        'code'        => 'cashondelivery',
        'title'       => 'Cash On Delivery',
        'description' => 'Cash On Delivery',
        'class'       => 'Webkul\Payment\Payment\CashOnDelivery',
        'active'      => true,
        'sort'        => 1,
    ],

    'moneytransfer'   => [
        'code'        => 'moneytransfer',
        'title'       => 'Money Transfer',
        'description' => 'Money Transfer',
        'class'       => 'Webkul\Payment\Payment\MoneyTransfer',
        'active'      => true,
        'sort'        => 2,
    ],

    'razorpay'   => [
        'code'        => 'razorpay',
        'title'       => 'Razorpay',
        'description' => 'Pay securely with cards, UPI, net banking, and wallets',
        'class'       => 'Webkul\Razorpay\Payment\Razorpay',
        'active'      => true,
        'sort'        => 3,
    ],
];
