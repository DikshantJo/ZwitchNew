<?php

namespace Webkul\Razorpay\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Webkul\Razorpay\Services\RazorpayService;
use Webkul\Sales\Repositories\OrderRepository;

class DashboardController extends Controller
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
     * Display the Razorpay dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Get statistics
        $statistics = $this->getStatistics();

        // Get recent orders
        $recentOrders = $this->getRecentOrders();

        // Get configuration status
        $configStatus = $this->getConfigurationStatus();

        return view('razorpay::admin.dashboard.index', compact('statistics', 'recentOrders', 'configStatus'));
    }

    /**
     * Get dashboard statistics.
     *
     * @return array
     */
    protected function getStatistics()
    {
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

        $failedOrders = $this->orderRepository->getModel()
            ->whereNotNull('razorpay_payment_id')
            ->where('status', 'cancelled')
            ->count();

        // Today's statistics
        $todayOrders = $this->orderRepository->getModel()
            ->whereNotNull('razorpay_payment_id')
            ->whereDate('created_at', today())
            ->count();

        $todayAmount = $this->orderRepository->getModel()
            ->whereNotNull('razorpay_payment_id')
            ->whereDate('created_at', today())
            ->sum('grand_total');

        return [
            'total_orders' => $totalOrders,
            'total_amount' => $totalAmount,
            'pending_orders' => $pendingOrders,
            'completed_orders' => $completedOrders,
            'failed_orders' => $failedOrders,
            'today_orders' => $todayOrders,
            'today_amount' => $todayAmount,
        ];
    }

    /**
     * Get recent orders.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    protected function getRecentOrders()
    {
        return $this->orderRepository->getModel()
            ->whereNotNull('razorpay_payment_id')
            ->with(['customer', 'payment'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
    }

    /**
     * Get configuration status.
     *
     * @return array
     */
    protected function getConfigurationStatus()
    {
        $keyId = core()->getConfigData('sales.payment_methods.razorpay.key_id');
        $keySecret = core()->getConfigData('sales.payment_methods.razorpay.key_secret');
        $webhookSecret = core()->getConfigData('sales.payment_methods.razorpay.webhook_secret');
        $active = core()->getConfigData('sales.payment_methods.razorpay.active');

        $isConfigured = !empty($keyId) && !empty($keySecret);
        $hasWebhook = !empty($webhookSecret);
        $isActive = $active;

        return [
            'is_configured' => $isConfigured,
            'has_webhook' => $hasWebhook,
            'is_active' => $isActive,
            'key_id' => $keyId,
            'webhook_secret' => $webhookSecret,
        ];
    }

    /**
     * Get API connectivity status.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function apiStatus()
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
     * Get payment method distribution.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function paymentMethodDistribution()
    {
        try {
            $distribution = $this->orderRepository->getModel()
                ->whereNotNull('razorpay_payment_id')
                ->selectRaw('JSON_EXTRACT(payment->"$.additional", "$.payment_method") as payment_method, COUNT(*) as count')
                ->groupBy('payment_method')
                ->get()
                ->mapWithKeys(function ($item) {
                    return [$item->payment_method ?? 'unknown' => $item->count];
                });

            return response()->json([
                'success' => true,
                'distribution' => $distribution,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get payment method distribution: ' . $e->getMessage(),
            ]);
        }
    }

    /**
     * Get daily order trends.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function dailyTrends()
    {
        try {
            $trends = $this->orderRepository->getModel()
                ->whereNotNull('razorpay_payment_id')
                ->whereDate('created_at', '>=', now()->subDays(30))
                ->selectRaw('DATE(created_at) as date, COUNT(*) as orders, SUM(grand_total) as amount')
                ->groupBy('date')
                ->orderBy('date')
                ->get();

            return response()->json([
                'success' => true,
                'trends' => $trends,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get daily trends: ' . $e->getMessage(),
            ]);
        }
    }
} 