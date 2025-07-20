<?php

namespace Webkul\Razorpay\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
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
     * Display a listing of webhook events.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        // Get webhook events from logs or database
        $webhookEvents = $this->getWebhookEvents($request);

        return view('razorpay::admin.webhooks.index', compact('webhookEvents'));
    }

    /**
     * Display the specified webhook event.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        $webhookEvent = $this->getWebhookEvent($id);

        if (!$webhookEvent) {
            session()->flash('error', 'Webhook event not found.');
            return redirect()->route('admin.razorpay.webhooks.index');
        }

        return view('razorpay::admin.webhooks.show', compact('webhookEvent'));
    }

    /**
     * Retry processing a webhook event.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function retry($id)
    {
        try {
            $webhookEvent = $this->getWebhookEvent($id);

            if (!$webhookEvent) {
                session()->flash('error', 'Webhook event not found.');
                return redirect()->back();
            }

            // Re-process the webhook event
            $result = $this->processWebhookEvent($webhookEvent);

            if ($result['success']) {
                session()->flash('success', 'Webhook event reprocessed successfully.');
            } else {
                session()->flash('error', 'Webhook event reprocessing failed: ' . $result['message']);
            }

            return redirect()->route('admin.razorpay.webhooks.show', $id);

        } catch (\Exception $e) {
            Log::error('Webhook retry failed: ' . $e->getMessage());
            session()->flash('error', 'Webhook retry failed: ' . $e->getMessage());
            return redirect()->back();
        }
    }

    /**
     * Get webhook events with filtering.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    protected function getWebhookEvents(Request $request)
    {
        // This would typically query a webhook_events table
        // For now, we'll return a mock structure
        $events = [
            [
                'id' => 1,
                'event_type' => 'payment.captured',
                'payment_id' => 'pay_test123',
                'order_id' => 'order_test123',
                'status' => 'processed',
                'created_at' => now()->subHours(2),
                'processed_at' => now()->subHours(2),
                'payload' => json_encode(['event' => 'payment.captured', 'payload' => ['payment' => ['entity' => ['id' => 'pay_test123']]]]),
            ],
            [
                'id' => 2,
                'event_type' => 'payment.failed',
                'payment_id' => 'pay_test456',
                'order_id' => 'order_test456',
                'status' => 'failed',
                'created_at' => now()->subHours(1),
                'processed_at' => null,
                'payload' => json_encode(['event' => 'payment.failed', 'payload' => ['payment' => ['entity' => ['id' => 'pay_test456']]]]),
            ],
        ];

        // Apply filters
        if ($request->has('status') && $request->status) {
            $events = array_filter($events, function($event) use ($request) {
                return $event['status'] === $request->status;
            });
        }

        if ($request->has('event_type') && $request->event_type) {
            $events = array_filter($events, function($event) use ($request) {
                return $event['event_type'] === $request->event_type;
            });
        }

        return $events;
    }

    /**
     * Get a specific webhook event.
     *
     * @param  int  $id
     * @return array|null
     */
    protected function getWebhookEvent($id)
    {
        $events = $this->getWebhookEvents(request());
        
        foreach ($events as $event) {
            if ($event['id'] == $id) {
                return $event;
            }
        }

        return null;
    }

    /**
     * Process a webhook event.
     *
     * @param  array  $webhookEvent
     * @return array
     */
    protected function processWebhookEvent($webhookEvent)
    {
        try {
            $payload = json_decode($webhookEvent['payload'], true);
            
            if (!$payload) {
                return ['success' => false, 'message' => 'Invalid payload'];
            }

            // Verify webhook signature if available
            if (isset($webhookEvent['signature'])) {
                $isValid = $this->razorpayService->verifyWebhookSignature(
                    $webhookEvent['payload'],
                    $webhookEvent['signature']
                );

                if (!$isValid) {
                    return ['success' => false, 'message' => 'Invalid signature'];
                }
            }

            // Process based on event type
            switch ($payload['event']) {
                case 'payment.captured':
                    return $this->handlePaymentCaptured($payload['payload']['payment']['entity']);
                
                case 'payment.failed':
                    return $this->handlePaymentFailed($payload['payload']['payment']['entity']);
                
                case 'order.paid':
                    return $this->handleOrderPaid($payload['payload']['order']['entity']);
                
                case 'refund.processed':
                    return $this->handleRefundProcessed($payload['payload']['refund']['entity']);
                
                default:
                    return ['success' => true, 'message' => 'Event ignored'];
            }

        } catch (\Exception $e) {
            Log::error('Webhook event processing failed: ' . $e->getMessage());
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Handle payment captured event.
     *
     * @param  array  $paymentData
     * @return array
     */
    protected function handlePaymentCaptured($paymentData)
    {
        try {
            // Find order by payment ID
            $order = \Webkul\Sales\Models\Order::where('razorpay_payment_id', $paymentData['id'])->first();

            if ($order) {
                $order->update(['status' => 'processing']);
                return ['success' => true, 'message' => 'Order status updated to processing'];
            }

            return ['success' => false, 'message' => 'Order not found'];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Handle payment failed event.
     *
     * @param  array  $paymentData
     * @return array
     */
    protected function handlePaymentFailed($paymentData)
    {
        try {
            // Find order by payment ID
            $order = \Webkul\Sales\Models\Order::where('razorpay_payment_id', $paymentData['id'])->first();

            if ($order) {
                $order->update(['status' => 'cancelled']);
                return ['success' => true, 'message' => 'Order status updated to cancelled'];
            }

            return ['success' => false, 'message' => 'Order not found'];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Handle order paid event.
     *
     * @param  array  $orderData
     * @return array
     */
    protected function handleOrderPaid($orderData)
    {
        try {
            // Find order by Razorpay order ID
            $order = \Webkul\Sales\Models\Order::where('razorpay_order_id', $orderData['id'])->first();

            if ($order) {
                $order->update(['status' => 'completed']);
                return ['success' => true, 'message' => 'Order status updated to completed'];
            }

            return ['success' => false, 'message' => 'Order not found'];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Handle refund processed event.
     *
     * @param  array  $refundData
     * @return array
     */
    protected function handleRefundProcessed($refundData)
    {
        try {
            // Find refund by Razorpay refund ID
            $refund = \Webkul\Sales\Models\Refund::where('razorpay_refund_id', $refundData['id'])->first();

            if ($refund) {
                $refund->update(['status' => 'processed']);
                return ['success' => true, 'message' => 'Refund status updated to processed'];
            }

            return ['success' => false, 'message' => 'Refund not found'];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
} 