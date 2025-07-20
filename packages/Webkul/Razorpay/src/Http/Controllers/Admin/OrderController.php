<?php

namespace Webkul\Razorpay\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Webkul\Razorpay\Services\RazorpayService;
use Webkul\Sales\Repositories\OrderRepository;

class OrderController extends Controller
{
    /**
     * Razorpay service instance.
     *
     * @var \Webkul\Razorpay\Services\RazorpayService
     */
    protected $razorpayService;

    /**
     * Order repository instance.
     *
     * @var \Webkul\Sales\Repositories\OrderRepository
     */
    protected $orderRepository;

    /**
     * Create a new controller instance.
     *
     * @param  \Webkul\Razorpay\Services\RazorpayService  $razorpayService
     * @param  \Webkul\Sales\Repositories\OrderRepository  $orderRepository
     * @return void
     */
    public function __construct(RazorpayService $razorpayService, OrderRepository $orderRepository)
    {
        $this->razorpayService = $razorpayService;
        $this->orderRepository = $orderRepository;
    }

    /**
     * Display a listing of Razorpay orders.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $query = $this->orderRepository->getModel()
            ->whereNotNull('razorpay_payment_id')
            ->orWhereNotNull('razorpay_order_id');

        // Apply filters
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        if ($request->has('payment_method') && $request->payment_method) {
            $query->where('payment->method', $request->payment_method);
        }

        if ($request->has('date_from') && $request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->has('date_to') && $request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $orders = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('razorpay::admin.orders.index', compact('orders'));
    }

    /**
     * Display the specified order.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        $order = $this->orderRepository->findOrFail($id);

        // Get Razorpay payment details if available
        $razorpayPaymentDetails = null;
        if ($order->razorpay_payment_id) {
            $razorpayPaymentDetails = $this->razorpayService->getPaymentDetails($order->razorpay_payment_id);
        }

        // Get Razorpay order details if available
        $razorpayOrderDetails = null;
        if ($order->razorpay_order_id) {
            $razorpayOrderDetails = $this->razorpayService->getOrderDetails($order->razorpay_order_id);
        }

        return view('razorpay::admin.orders.show', compact('order', 'razorpayPaymentDetails', 'razorpayOrderDetails'));
    }

    /**
     * Sync order with Razorpay.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function sync($id)
    {
        try {
            $order = $this->orderRepository->findOrFail($id);

            if (!$order->razorpay_payment_id) {
                session()->flash('error', 'Order does not have Razorpay payment ID.');
                return redirect()->back();
            }

            // Get latest payment details from Razorpay
            $paymentDetails = $this->razorpayService->getPaymentDetails($order->razorpay_payment_id);

            if (isset($paymentDetails['error'])) {
                session()->flash('error', 'Failed to sync with Razorpay: ' . $paymentDetails['error']);
                return redirect()->back();
            }

            // Update order payment details
            $paymentData = $order->payment->additional ?? [];
            $paymentData['razorpay_payment_status'] = $paymentDetails['status'];
            $paymentData['razorpay_payment_method'] = $paymentDetails['method'] ?? null;
            $paymentData['razorpay_bank'] = $paymentDetails['bank'] ?? null;
            $paymentData['razorpay_wallet'] = $paymentDetails['wallet'] ?? null;
            $paymentData['razorpay_vpa'] = $paymentDetails['vpa'] ?? null;
            $paymentData['razorpay_card_id'] = $paymentDetails['card_id'] ?? null;
            $paymentData['razorpay_emi_month'] = $paymentDetails['emi_month'] ?? null;
            $paymentData['last_synced_at'] = now();

            $order->payment->update(['additional' => $paymentData]);

            // Update order status based on payment status
            if ($paymentDetails['status'] === 'captured' && $order->status === 'pending') {
                $order->update(['status' => 'processing']);
            }

            session()->flash('success', 'Order synced successfully with Razorpay.');

            return redirect()->route('admin.razorpay.orders.show', $order->id);

        } catch (\Exception $e) {
            Log::error('Razorpay order sync failed: ' . $e->getMessage());
            session()->flash('error', 'Order sync failed: ' . $e->getMessage());
            return redirect()->back();
        }
    }

    /**
     * Process refund for the order.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function refund(Request $request, $id)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'reason' => 'required|string|max:500',
        ]);

        try {
            $order = $this->orderRepository->findOrFail($id);

            if (!$order->razorpay_payment_id) {
                session()->flash('error', 'Order does not have Razorpay payment ID.');
                return redirect()->back();
            }

            // Validate refund amount
            $paymentDetails = $this->razorpayService->getPaymentDetails($order->razorpay_payment_id);
            if (isset($paymentDetails['error'])) {
                session()->flash('error', 'Failed to get payment details: ' . $paymentDetails['error']);
                return redirect()->back();
            }

            $paymentAmount = $paymentDetails['amount'] / 100; // Convert from paise
            if ($request->amount > $paymentAmount) {
                session()->flash('error', 'Refund amount cannot exceed payment amount.');
                return redirect()->back();
            }

            // Process refund
            $refundData = [
                'amount' => $request->amount,
                'notes' => [
                    'reason' => $request->reason,
                    'order_id' => $order->id,
                    'admin_user_id' => auth()->id(),
                ],
            ];

            $refundResult = $this->razorpayService->processRefund($order->razorpay_payment_id, $refundData);

            if (isset($refundResult['error'])) {
                session()->flash('error', 'Refund failed: ' . $refundResult['error']);
                return redirect()->back();
            }

            // Create refund record in Bagisto
            $refund = $order->refunds()->create([
                'razorpay_payment_id' => $order->razorpay_payment_id,
                'razorpay_refund_id' => $refundResult['id'],
                'amount' => $request->amount,
                'reason' => $request->reason,
                'status' => $refundResult['status'],
                'admin_user_id' => auth()->id(),
            ]);

            session()->flash('success', 'Refund processed successfully. Refund ID: ' . $refundResult['id']);

            return redirect()->route('admin.razorpay.orders.show', $order->id);

        } catch (\Exception $e) {
            Log::error('Razorpay refund failed: ' . $e->getMessage());
            session()->flash('error', 'Refund failed: ' . $e->getMessage());
            return redirect()->back();
        }
    }

    /**
     * Get order statistics.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function statistics()
    {
        try {
            $totalOrders = $this->orderRepository->getModel()
                ->whereNotNull('razorpay_payment_id')
                ->count();

            $totalAmount = $this->orderRepository->getModel()
                ->whereNotNull('razorpay_payment_id')
                ->sum('grand_total');

            $pendingOrders = $this->orderRepository->getModel()
                ->whereNotNull('razorpay_payment_id')
                ->where('status', 'pending')
                ->count();

            $completedOrders = $this->orderRepository->getModel()
                ->whereNotNull('razorpay_payment_id')
                ->where('status', 'completed')
                ->count();

            return response()->json([
                'success' => true,
                'statistics' => [
                    'total_orders' => $totalOrders,
                    'total_amount' => $totalAmount,
                    'pending_orders' => $pendingOrders,
                    'completed_orders' => $completedOrders,
                ],
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get statistics: ' . $e->getMessage(),
            ]);
        }
    }
} 