<?php

namespace Webkul\Razorpay\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Routing\Controller;
use Webkul\Razorpay\Services\RazorpayService;
use Webkul\Razorpay\Models\RazorpayOrder;
use Webkul\Razorpay\Models\RazorpayPayment;

class PaymentController extends Controller
{
    /**
     * Razorpay service instance.
     *
     * @var \Webkul\Razorpay\Services\RazorpayService
     */
    protected $razorpayService;

    /**
     * Create a new controller instance.
     *
     * @param  \Webkul\Razorpay\Services\RazorpayService  $razorpayService
     * @return void
     */
    public function __construct(RazorpayService $razorpayService)
    {
        $this->razorpayService = $razorpayService;
    }

    /**
     * Show Razorpay checkout page.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function checkout(Request $request)
    {
        try {
            Log::info('Razorpay checkout started');
            
            // Get cart
            $cart = \Webkul\Checkout\Facades\Cart::getCart();
            Log::info('Cart retrieved', ['cart_id' => $cart->id ?? 'null', 'items_count' => $cart->items->count() ?? 0]);

            if (! $cart || ! $cart->items->count()) {
                Log::warning('Cart is empty or has no items');
                return redirect()->route('shop.home.index')->with('error', 'Cart is empty');
            }

            // Check if payment method is set
            if (! $cart->payment || $cart->payment->method !== 'razorpay') {
                Log::info('Setting Razorpay as payment method');
                
                // Set Razorpay as the payment method
                $cart->savePaymentMethod([
                    'method' => 'razorpay',
                ]);
                
                // Refresh cart to get updated payment method
                $cart = \Webkul\Checkout\Facades\Cart::getCart();
            }

            // Validate cart currency
            $supportedCurrenciesConfig = core()->getConfigData('sales.payment_methods.razorpay.accepted_currencies') ?? ['INR'];
            
            // Convert to array if it's a string (comma-separated)
            if (is_string($supportedCurrenciesConfig)) {
                $supportedCurrencies = array_map('trim', explode(',', $supportedCurrenciesConfig));
            } else {
                $supportedCurrencies = $supportedCurrenciesConfig;
            }
            
            $cartCurrency = $cart->cart_currency_code;
            
            if (! in_array($cartCurrency, $supportedCurrencies)) {
                return redirect()->route('shop.checkout.onepage.index')->with('error', 'Currency not supported by Razorpay');
            }

            // Validate minimum order amount
            $minimumOrderAmount = core()->getConfigData('sales.order_settings.minimum_order.minimum_order_amount') ?: 0;
            if ($cart->grand_total < $minimumOrderAmount) {
                return redirect()->route('shop.checkout.onepage.index')->with('error', 'Order amount is below minimum requirement');
            }

            // Create Razorpay order
            $orderData = [
                'amount' => $cart->grand_total,
                'currency' => $cart->cart_currency_code,
                'receipt' => 'order_' . $cart->id,
                'notes' => [
                    'cart_id' => $cart->id,
                    'customer_id' => $cart->customer_id,
                ],
            ];

            Log::info('Creating Razorpay order', $orderData);
            $razorpayOrder = $this->razorpayService->createOrder($orderData);
            Log::info('Razorpay order response', $razorpayOrder);

            if (isset($razorpayOrder['error'])) {
                Log::error('Razorpay order creation failed: ' . $razorpayOrder['error']);
                return redirect()->route('shop.checkout.onepage.index')->with('error', 'Payment initialization failed');
            }

            // Store Razorpay order ID in cart
            $cart->payment->additional = array_merge($cart->payment->additional ?? [], [
                'razorpay_order_id' => $razorpayOrder['id'],
            ]);
            $cart->payment->save();

            // Get customer details
            $customer = $cart->customer;
            $billingAddress = $cart->billing_address;

            // Build Razorpay checkout URL with parameters
            $razorpayUrl = 'https://checkout.razorpay.com/v1/checkout.html';
            
            // Handle guest users
            $customerName = '';
            $customerEmail = '';
            $customerPhone = '';
            
            if ($customer) {
                $customerName = $customer->first_name . ' ' . $customer->last_name;
                $customerEmail = $customer->email;
            } else {
                // For guest users, use cart customer details
                $customerName = $cart->customer_first_name . ' ' . $cart->customer_last_name;
                $customerEmail = $cart->customer_email;
            }
            
            if ($billingAddress) {
                $customerPhone = $billingAddress->phone ?? '';
            }
            
            // Create a simple checkout form that auto-submits to Razorpay
            $checkoutHtml = '
            <!DOCTYPE html>
            <html>
            <head>
                <title>Redirecting to Razorpay...</title>
                <meta charset="utf-8">
                <meta name="viewport" content="width=device-width, initial-scale=1">
            </head>
            <body>
                <div style="text-align: center; padding: 50px; font-family: Arial, sans-serif;">
                    <h2>Redirecting to Razorpay...</h2>
                    <p>Please wait while we redirect you to the payment gateway.</p>
                    <div style="margin: 20px 0;">
                        <div style="display: inline-block; width: 20px; height: 20px; border: 3px solid #f3f3f3; border-top: 3px solid #3498db; border-radius: 50%; animation: spin 1s linear infinite;"></div>
                    </div>
                </div>
                
                <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
                <script>
                    var options = {
                        "key": "' . core()->getConfigData('sales.payment_methods.razorpay.key_id') . '",
                        "amount": "' . $razorpayOrder['amount'] . '",
                        "currency": "' . $razorpayOrder['currency'] . '",
                        "name": "' . (core()->getConfigData('general.store_name') ?: 'Your Store') . '",
                        "description": "Order #' . $cart->id . '",
                        "order_id": "' . $razorpayOrder['id'] . '",
                        "prefill": {
                            "name": "' . addslashes($customerName) . '",
                            "email": "' . $customerEmail . '",
                            "contact": "' . $customerPhone . '"
                        },
                        "notes": {
                            "cart_id": "' . $cart->id . '",
                            "customer_id": "' . ($customer ? $customer->id : '') . '"
                        },
                        "theme": {
                            "color": "' . (core()->getConfigData('sales.payment_methods.razorpay.theme') ?? '#3399cc') . '"
                        },
                        "modal": {
                            "confirm_close": true,
                            "escape": false
                        },

                        "handler": function (response) {
                            console.log("Payment successful:", response);
                            // Force redirect to success page
                            window.location.replace(\'' . route('razorpay.success') . '?razorpay_payment_id=\' + response.razorpay_payment_id + \'&razorpay_order_id=\' + response.razorpay_order_id + \'&razorpay_signature=\' + response.razorpay_signature);
                        },
                        "modal": {
                            "ondismiss": function() {
                                console.log("Payment cancelled by user");
                                window.location.replace(\'' . route('razorpay.failure') . '?error_code=PAYMENT_CANCELLED&error_description=Payment was cancelled by user\');
                            }
                        }
                    };
                    
                    console.log("Razorpay options:", options);
                    var rzp = new Razorpay(options);
                    
                    rzp.on(\'payment.failed\', function (resp) {
                        console.log("Payment failed:", resp.error);
                        // Force redirect to failure page
                        window.location.replace(\'' . route('razorpay.failure') . '?error_code=\' + resp.error.code + \'&error_description=\' + resp.error.description);
                    });
                    
                    rzp.open();
                    
                    // Fallback redirect after 30 seconds if no response
                    setTimeout(function() {
                        if (window.location.href.indexOf(\'razorpay\') === -1) {
                            console.log("Fallback redirect - no response received");
                            window.location.replace(\'' . route('razorpay.failure') . '?error_code=TIMEOUT&error_description=Payment timeout - no response received\');
                        }
                    }, 30000);
                </script>
                
                <style>
                    @keyframes spin {
                        0% { transform: rotate(0deg); }
                        100% { transform: rotate(360deg); }
                    }
                </style>
            </body>
            </html>';
            
            Log::info('Razorpay checkout page generated', ['order_id' => $razorpayOrder['id']]);
            
            return response($checkoutHtml);

        } catch (\Exception $e) {
            Log::error('Razorpay checkout error: ' . $e->getMessage());
            return redirect()->route('shop.checkout.onepage.index')->with('error', 'Payment initialization failed');
        }
    }

    /**
     * Handle successful payment.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function success(Request $request)
    {
        try {
            Log::info('Razorpay success callback received', $request->all());
            
            $razorpayPaymentId = $request->input('razorpay_payment_id');
            $razorpayOrderId = $request->input('razorpay_order_id');
            $razorpaySignature = $request->input('razorpay_signature');

            // Validate required parameters
            if (! $razorpayPaymentId || ! $razorpayOrderId || ! $razorpaySignature) {
                Log::error('Razorpay success: Missing required parameters');
                return redirect()->route('shop.checkout.onepage.index')->with('error', 'Invalid payment response');
            }

            // Verify payment signature
            if (! $this->razorpayService->verifyPaymentSignature($razorpayOrderId, $razorpayPaymentId, $razorpaySignature)) {
                Log::error('Razorpay payment signature verification failed');
                return redirect()->route('shop.checkout.onepage.index')->with('error', 'Payment verification failed');
            }

            // Get payment details from Razorpay
            $paymentDetails = $this->razorpayService->getPaymentDetails($razorpayPaymentId);

            if (isset($paymentDetails['error'])) {
                Log::error('Razorpay payment details failed: ' . $paymentDetails['error']);
                return redirect()->route('shop.checkout.onepage.index')->with('error', 'Payment verification failed');
            }

            // Verify payment status
            if ($paymentDetails['status'] !== 'captured') {
                Log::error('Razorpay payment not captured: ' . $paymentDetails['status']);
                return redirect()->route('shop.checkout.onepage.index')->with('error', 'Payment not completed');
            }

            // Get cart
            $cart = \Webkul\Checkout\Facades\Cart::getCart();

            if (! $cart) {
                return redirect()->route('shop.home.index')->with('error', 'Cart not found');
            }

            // Check if cart has required addresses
            if (! $cart->billing_address) {
                Log::error('Cart missing billing address', ['cart_id' => $cart->id]);
                return redirect()->route('shop.checkout.onepage.index')->with('error', 'Billing address is required');
            }

            if (! $cart->shipping_address) {
                Log::error('Cart missing shipping address', ['cart_id' => $cart->id]);
                return redirect()->route('shop.checkout.onepage.index')->with('error', 'Shipping address is required');
            }

            // Verify cart amount matches payment amount
            $paymentAmount = $paymentDetails['amount'] / 100; // Convert from paise
            if (abs($paymentAmount - $cart->grand_total) > 0.01) {
                Log::error('Razorpay payment amount mismatch: expected ' . $cart->grand_total . ', got ' . $paymentAmount);
                return redirect()->route('shop.checkout.onepage.index')->with('error', 'Payment amount mismatch');
            }

            // Update cart payment with Razorpay details
            $cart->payment->additional = array_merge($cart->payment->additional ?? [], [
                'razorpay_payment_id' => $razorpayPaymentId,
                'razorpay_order_id' => $razorpayOrderId,
                'razorpay_signature' => $razorpaySignature,
                'payment_method' => $paymentDetails['method'] ?? null,
                'bank' => $paymentDetails['bank'] ?? null,
                'wallet' => $paymentDetails['wallet'] ?? null,
                'vpa' => $paymentDetails['vpa'] ?? null,
                'card_id' => $paymentDetails['card_id'] ?? null,
                'emi_month' => $paymentDetails['emi_month'] ?? null,
            ]);
            $cart->payment->save();

            // Prepare order data using Bagisto's OrderResource transformer
            $orderData = (new \Webkul\Sales\Transformers\OrderResource($cart))->jsonSerialize();
            
            // Create order
            $order = app(\Webkul\Sales\Repositories\OrderRepository::class)->create($orderData);

            if (! $order) {
                return redirect()->route('shop.checkout.onepage.index')->with('error', 'Order creation failed');
            }

            // Update order with Razorpay details
            $order->update([
                'razorpay_payment_id' => $razorpayPaymentId,
                'razorpay_order_id' => $razorpayOrderId,
                'razorpay_signature' => $razorpaySignature,
            ]);

            // Clear cart
            \Webkul\Checkout\Facades\Cart::deActivateCart();

            // Trigger success event
            event('razorpay.payment.success', ['order' => $order, 'payment_details' => $paymentDetails]);

            // Set order ID in session for Bagisto success page
            session()->flash('order_id', $order->id);

            // Redirect to success page
            return redirect()->route('shop.checkout.onepage.success');

        } catch (\Exception $e) {
            Log::error('Razorpay payment success error: ' . $e->getMessage());
            return redirect()->route('shop.checkout.onepage.index')->with('error', 'Payment processing failed');
        }
    }

    /**
     * Handle failed payment.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function failure(Request $request)
    {
        try {
            Log::info('Razorpay failure callback received', $request->all());
            
            $errorCode = $request->input('error_code');
            $errorDescription = $request->input('error_description');
            $razorpayPaymentId = $request->input('razorpay_payment_id');
            $razorpayOrderId = $request->input('razorpay_order_id');

            Log::error('Razorpay payment failed: ' . $errorCode . ' - ' . $errorDescription . ' (Payment ID: ' . $razorpayPaymentId . ', Order ID: ' . $razorpayOrderId . ')');

            // Get user-friendly error message
            $errorMessage = $this->getErrorMessage($errorCode, $errorDescription);

            // Trigger failure event
            event('razorpay.payment.failed', [
                'error_code' => $errorCode,
                'error_description' => $errorDescription,
                'razorpay_payment_id' => $razorpayPaymentId,
                'razorpay_order_id' => $razorpayOrderId,
            ]);

            return redirect()->route('shop.checkout.onepage.index')->with('error', $errorMessage);

        } catch (\Exception $e) {
            Log::error('Razorpay payment failure handling error: ' . $e->getMessage());
            return redirect()->route('shop.checkout.onepage.index')->with('error', 'Payment failed');
        }
    }

    /**
     * Get user-friendly error message.
     *
     * @param  string  $errorCode
     * @param  string  $errorDescription
     * @return string
     */
    protected function getErrorMessage($errorCode, $errorDescription)
    {
        $errorMessages = [
            'PAYMENT_CANCELLED' => 'Payment was cancelled by you',
            'PAYMENT_DECLINED' => 'Payment was declined by the bank',
            'INSUFFICIENT_FUNDS' => 'Insufficient funds in your account',
            'CARD_DECLINED' => 'Your card was declined',
            'NETWORK_ERROR' => 'Network error occurred. Please try again',
            'TIMEOUT' => 'Payment request timed out. Please try again',
            'INVALID_AMOUNT' => 'Invalid payment amount',
            'INVALID_CURRENCY' => 'Invalid currency',
            'INVALID_ORDER' => 'Invalid order details',
            'INVALID_PAYMENT' => 'Invalid payment details',
            'AUTHENTICATION_FAILED' => 'Payment authentication failed',
            'BAD_REQUEST_ERROR' => 'Invalid payment request',
            'GATEWAY_ERROR' => 'Payment gateway error occurred',
            'SERVER_ERROR' => 'Server error occurred. Please try again later',
            'UPI_PAYMENT_FAILED' => 'UPI payment failed. Please try again',
            'NETBANKING_PAYMENT_FAILED' => 'Net banking payment failed',
            'WALLET_PAYMENT_FAILED' => 'Wallet payment failed',
            'EMI_PAYMENT_FAILED' => 'EMI payment failed',
            'CARD_EXPIRED' => 'Your card has expired',
            'CARD_INVALID' => 'Invalid card details',
            'CARD_LOST' => 'Card reported as lost or stolen',
            'CARD_STOLEN' => 'Card reported as lost or stolen',
            'CARD_PICK_UP' => 'Card has been picked up',
            'CARD_DO_NOT_HONOR' => 'Card declined by bank',
            'CARD_INSUFFICIENT_FUNDS' => 'Insufficient funds on card',
            'CARD_INVALID_EXPIRY' => 'Invalid card expiry date',
            'CARD_INVALID_CVV' => 'Invalid CVV',
            'CARD_INVALID_PIN' => 'Invalid PIN',
            'CARD_INVALID_OTP' => 'Invalid OTP',
            'CARD_INVALID_UPI_PIN' => 'Invalid UPI PIN',
            'CARD_INVALID_UPI_VPA' => 'Invalid UPI VPA',
        ];

        return $errorMessages[$errorCode] ?? $errorDescription ?? 'Payment failed. Please try again.';
    }

    /**
     * Test API connectivity.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function testApi()
    {
        try {
            $result = $this->razorpayService->testApiConnectivity();

            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'API test failed: ' . $e->getMessage(),
            ]);
        }
    }

    /**
     * Get payment methods.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getPaymentMethods()
    {
        try {
            $methods = $this->razorpayService->getSupportedPaymentMethods();

            return response()->json([
                'success' => true,
                'methods' => $methods,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get payment methods: ' . $e->getMessage(),
            ]);
        }
    }

    /**
     * Get supported currencies.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCurrencies()
    {
        try {
            $currencies = $this->razorpayService->getSupportedCurrencies();

            return response()->json([
                'success' => true,
                'currencies' => $currencies,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get currencies: ' . $e->getMessage(),
            ]);
        }
    }

    /**
     * Validate payment method availability.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function validatePaymentMethod(Request $request)
    {
        try {
            $cart = \Webkul\Checkout\Facades\Cart::getCart();

            if (! $cart || ! $cart->items->count()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cart is empty',
                ]);
            }

            // Check if Razorpay is configured
            if (! $this->razorpayService->isConfigured()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Razorpay is not configured',
                ]);
            }

            // Check currency support
            $supportedCurrenciesConfig = core()->getConfigData('sales.payment_methods.razorpay.accepted_currencies') ?? ['INR'];
            
            // Convert to array if it's a string (comma-separated)
            if (is_string($supportedCurrenciesConfig)) {
                $supportedCurrencies = array_map('trim', explode(',', $supportedCurrenciesConfig));
            } else {
                $supportedCurrencies = $supportedCurrenciesConfig;
            }
            
            $cartCurrency = $cart->cart_currency_code;

            if (! in_array($cartCurrency, $supportedCurrencies)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Currency ' . $cartCurrency . ' is not supported by Razorpay',
                ]);
            }

            // Check minimum order amount
            $minimumOrderAmount = core()->getConfigData('sales.order_settings.minimum_order.minimum_order_amount') ?: 0;
            if ($cart->grand_total < $minimumOrderAmount) {
                return response()->json([
                    'success' => false,
                    'message' => 'Order amount is below minimum requirement of ' . core()->currency($minimumOrderAmount),
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Payment method is available',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed: ' . $e->getMessage(),
            ]);
        }
    }

    /**
     * Get payment method configuration.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getConfiguration()
    {
        try {
            $acceptedCurrenciesConfig = core()->getConfigData('sales.payment_methods.razorpay.accepted_currencies') ?? ['INR'];
            
            // Convert to array if it's a string (comma-separated)
            if (is_string($acceptedCurrenciesConfig)) {
                $acceptedCurrencies = array_map('trim', explode(',', $acceptedCurrenciesConfig));
            } else {
                $acceptedCurrencies = $acceptedCurrenciesConfig;
            }
            
            $config = [
                'key_id' => core()->getConfigData('sales.payment_methods.razorpay.key_id'),
                'sandbox' => core()->getConfigData('sales.payment_methods.razorpay.sandbox'),
                'accepted_currencies' => $acceptedCurrencies,
                'theme' => core()->getConfigData('sales.payment_methods.razorpay.theme') ?? '#3399cc',
                'instructions' => core()->getConfigData('sales.payment_methods.razorpay.instructions'),
            ];

            return response()->json([
                'success' => true,
                'config' => $config,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get configuration: ' . $e->getMessage(),
            ]);
        }
    }
} 