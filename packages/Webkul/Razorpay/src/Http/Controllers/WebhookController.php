<?php

namespace Webkul\Razorpay\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Routing\Controller;
use Webkul\Razorpay\Services\RazorpayService;

class WebhookController extends Controller
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
     * Handle Razorpay webhook events.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function handle(Request $request)
    {
        try {
            // Get the webhook payload
            $payload = $request->getContent();
            $signature = $request->header('X-Razorpay-Signature');

            // Verify webhook signature
            if (! $this->razorpayService->verifyWebhookSignature($payload, $signature)) {
                Log::error('Razorpay webhook signature verification failed');
                return response()->json(['error' => 'Invalid signature'], Response::HTTP_BAD_REQUEST);
            }

            // Parse the webhook payload
            $webhookData = json_decode($payload, true);

            if (! $webhookData) {
                Log::error('Razorpay webhook payload parsing failed');
                return response()->json(['error' => 'Invalid payload'], Response::HTTP_BAD_REQUEST);
            }

            // Get the event type
            $event = $webhookData['event'] ?? null;
            $entity = $webhookData['payload']['payment']['entity'] ?? null;

            if (! $event || ! $entity) {
                Log::error('Razorpay webhook missing event or entity data');
                return response()->json(['error' => 'Missing event data'], Response::HTTP_BAD_REQUEST);
            }

            // Handle different webhook events
            switch ($event) {
                case 'payment.captured':
                    return $this->handlePaymentCaptured($entity);

                case 'payment.failed':
                    return $this->handlePaymentFailed($entity);

                case 'payment.authorized':
                    return $this->handlePaymentAuthorized($entity);

                case 'order.paid':
                    return $this->handleOrderPaid($entity);

                case 'refund.processed':
                    return $this->handleRefundProcessed($entity);

                default:
                    Log::info('Razorpay webhook event not handled: ' . $event);
                    return response()->json(['status' => 'ignored'], Response::HTTP_OK);
            }

        } catch (\Exception $e) {
            Log::error('Razorpay webhook processing error: ' . $e->getMessage());
            return response()->json(['error' => 'Webhook processing failed'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Handle payment captured event.
     *
     * @param  array  $paymentData
     * @return \Illuminate\Http\Response
     */
    protected function handlePaymentCaptured($paymentData)
    {
        try {
            Log::info('Razorpay payment captured: ' . $paymentData['id']);

            // Update order status in Bagisto
            $this->updateOrderStatus($paymentData['order_id'], 'processing');

            // Trigger event for payment captured
            event('razorpay.payment.captured', $paymentData);

            return response()->json(['status' => 'success'], Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('Razorpay payment captured handling error: ' . $e->getMessage());
            return response()->json(['error' => 'Payment captured handling failed'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Handle payment failed event.
     *
     * @param  array  $paymentData
     * @return \Illuminate\Http\Response
     */
    protected function handlePaymentFailed($paymentData)
    {
        try {
            Log::info('Razorpay payment failed: ' . $paymentData['id']);

            // Update order status in Bagisto
            $this->updateOrderStatus($paymentData['order_id'], 'cancelled');

            // Trigger event for payment failed
            event('razorpay.payment.failed', $paymentData);

            return response()->json(['status' => 'success'], Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('Razorpay payment failed handling error: ' . $e->getMessage());
            return response()->json(['error' => 'Payment failed handling failed'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Handle payment authorized event.
     *
     * @param  array  $paymentData
     * @return \Illuminate\Http\Response
     */
    protected function handlePaymentAuthorized($paymentData)
    {
        try {
            Log::info('Razorpay payment authorized: ' . $paymentData['id']);

            // Trigger event for payment authorized
            event('razorpay.payment.authorized', $paymentData);

            return response()->json(['status' => 'success'], Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('Razorpay payment authorized handling error: ' . $e->getMessage());
            return response()->json(['error' => 'Payment authorized handling failed'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Handle order paid event.
     *
     * @param  array  $orderData
     * @return \Illuminate\Http\Response
     */
    protected function handleOrderPaid($orderData)
    {
        try {
            Log::info('Razorpay order paid: ' . $orderData['id']);

            // Update order status in Bagisto
            $this->updateOrderStatus($orderData['id'], 'completed');

            // Trigger event for order paid
            event('razorpay.order.paid', $orderData);

            return response()->json(['status' => 'success'], Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('Razorpay order paid handling error: ' . $e->getMessage());
            return response()->json(['error' => 'Order paid handling failed'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Handle refund processed event.
     *
     * @param  array  $refundData
     * @return \Illuminate\Http\Response
     */
    protected function handleRefundProcessed($refundData)
    {
        try {
            Log::info('Razorpay refund processed: ' . $refundData['id']);

            // Update refund status in Bagisto
            $this->updateRefundStatus($refundData['payment_id'], $refundData['id'], 'processed');

            // Trigger event for refund processed
            event('razorpay.refund.processed', $refundData);

            return response()->json(['status' => 'success'], Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('Razorpay refund processed handling error: ' . $e->getMessage());
            return response()->json(['error' => 'Refund processed handling failed'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Update order status in Bagisto.
     *
     * @param  string  $razorpayOrderId
     * @param  string  $status
     * @return void
     */
    protected function updateOrderStatus($razorpayOrderId, $status)
    {
        try {
            // Find the order by Razorpay order ID
            $order = \Webkul\Sales\Models\Order::where('razorpay_order_id', $razorpayOrderId)->first();

            if ($order) {
                $order->update(['status' => $status]);
                Log::info('Updated order status: ' . $order->id . ' to ' . $status);
            } else {
                Log::warning('Order not found for Razorpay order ID: ' . $razorpayOrderId);
            }
        } catch (\Exception $e) {
            Log::error('Error updating order status: ' . $e->getMessage());
        }
    }

    /**
     * Update refund status in Bagisto.
     *
     * @param  string  $razorpayPaymentId
     * @param  string  $razorpayRefundId
     * @param  string  $status
     * @return void
     */
    protected function updateRefundStatus($razorpayPaymentId, $razorpayRefundId, $status)
    {
        try {
            // Find the refund by Razorpay payment ID and refund ID
            $refund = \Webkul\Sales\Models\Refund::where('razorpay_payment_id', $razorpayPaymentId)
                ->where('razorpay_refund_id', $razorpayRefundId)
                ->first();

            if ($refund) {
                $refund->update(['status' => $status]);
                Log::info('Updated refund status: ' . $refund->id . ' to ' . $status);
            } else {
                Log::warning('Refund not found for Razorpay payment ID: ' . $razorpayPaymentId . ' and refund ID: ' . $razorpayRefundId);
            }
        } catch (\Exception $e) {
            Log::error('Error updating refund status: ' . $e->getMessage());
        }
    }
} 