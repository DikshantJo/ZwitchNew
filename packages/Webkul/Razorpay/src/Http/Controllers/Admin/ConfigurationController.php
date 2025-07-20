<?php

namespace Webkul\Razorpay\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Event;
use Webkul\Razorpay\Services\RazorpayService;

class ConfigurationController extends Controller
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
     * Display the configuration page.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $config = [
            'title' => core()->getConfigData('sales.payment_methods.razorpay.title'),
            'description' => core()->getConfigData('sales.payment_methods.razorpay.description'),
            'active' => core()->getConfigData('sales.payment_methods.razorpay.active'),
            'sort' => core()->getConfigData('sales.payment_methods.razorpay.sort'),
            'key_id' => core()->getConfigData('sales.payment_methods.razorpay.key_id'),
            'key_secret' => core()->getConfigData('sales.payment_methods.razorpay.key_secret'),
            'webhook_secret' => core()->getConfigData('sales.payment_methods.razorpay.webhook_secret'),
            'sandbox' => core()->getConfigData('sales.payment_methods.razorpay.sandbox'),
            'accepted_currencies' => core()->getConfigData('sales.payment_methods.razorpay.accepted_currencies'),
            'instructions' => core()->getConfigData('sales.payment_methods.razorpay.instructions'),
            'theme' => core()->getConfigData('sales.payment_methods.razorpay.theme'),
            'generate_invoice' => core()->getConfigData('sales.payment_methods.razorpay.generate_invoice'),
            'invoice_status' => core()->getConfigData('sales.payment_methods.razorpay.invoice_status'),
            'order_status' => core()->getConfigData('sales.payment_methods.razorpay.order_status'),
        ];

        return view('razorpay::admin.configuration.index', compact('config'));
    }

    /**
     * Store the configuration.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:500',
            'active' => 'boolean',
            'sort' => 'required|integer|min:1',
            'key_id' => 'required|string|max:255',
            'key_secret' => 'required|string|max:255',
            'webhook_secret' => 'nullable|string|max:255',
            'sandbox' => 'boolean',
            'accepted_currencies' => 'required|array|min:1',
            'instructions' => 'nullable|string|max:1000',
            'theme' => 'nullable|string|max:7',
            'generate_invoice' => 'boolean',
            'invoice_status' => 'required|string|in:pending,paid',
            'order_status' => 'required|string|in:pending,processing,completed',
        ]);

        try {
            // Store configuration
            core()->setConfigData('sales.payment_methods.razorpay.title', $request->input('title'));
            core()->setConfigData('sales.payment_methods.razorpay.description', $request->input('description'));
            core()->setConfigData('sales.payment_methods.razorpay.active', $request->input('active', false));
            core()->setConfigData('sales.payment_methods.razorpay.sort', $request->input('sort'));
            core()->setConfigData('sales.payment_methods.razorpay.key_id', $request->input('key_id'));
            core()->setConfigData('sales.payment_methods.razorpay.key_secret', $request->input('key_secret'));
            core()->setConfigData('sales.payment_methods.razorpay.webhook_secret', $request->input('webhook_secret'));
            core()->setConfigData('sales.payment_methods.razorpay.sandbox', $request->input('sandbox', false));
            core()->setConfigData('sales.payment_methods.razorpay.accepted_currencies', $request->input('accepted_currencies'));
            core()->setConfigData('sales.payment_methods.razorpay.instructions', $request->input('instructions'));
            core()->setConfigData('sales.payment_methods.razorpay.theme', $request->input('theme'));
            core()->setConfigData('sales.payment_methods.razorpay.generate_invoice', $request->input('generate_invoice', false));
            core()->setConfigData('sales.payment_methods.razorpay.invoice_status', $request->input('invoice_status'));
            core()->setConfigData('sales.payment_methods.razorpay.order_status', $request->input('order_status'));

            // Test API connectivity if credentials are provided
            if ($request->input('key_id') && $request->input('key_secret')) {
                $apiTest = $this->razorpayService->testApiConnectivity();
                
                if (!$apiTest['success']) {
                    session()->flash('warning', 'Configuration saved but API connection failed: ' . $apiTest['message']);
                } else {
                    session()->flash('success', 'Configuration saved successfully and API connection verified.');
                }
            } else {
                session()->flash('success', 'Configuration saved successfully.');
            }

            Event::dispatch('razorpay.configuration.updated');

            return redirect()->route('admin.razorpay.configuration.index');

        } catch (\Exception $e) {
            session()->flash('error', 'Configuration save failed: ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
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
     * Get supported payment methods.
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
} 