<?php

namespace Webkul\Shop\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Webkul\Category\Repositories\CategoryRepository;
use Webkul\Product\Repositories\ProductRepository;
use Webkul\Shop\Http\Requests\CustomizationRequest;

class CustomizationController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(
        protected CategoryRepository $categoryRepository,
        protected ProductRepository $productRepository
    ) {}

    /**
     * Display the customization form.
     *
     * @return \Illuminate\View\View
     */
    public function index(): View
    {
        // Get active categories for dropdown
        $categories = $this->categoryRepository->getAll([
            'status' => 1,
            'locale' => app()->getLocale(),
        ]);

        // Budget options for dropdown (in Rupees)
        $budgetOptions = [
            'under_1000' => 'Under ₹1,000',
            '1000_2500' => '₹1,000 - ₹2,500',
            '2500_5000' => '₹2,500 - ₹5,000',
            '5000_10000' => '₹5,000 - ₹10,000',
            '10000_15000' => '₹10,000 - ₹15,000',
            '15000_25000' => '₹15,000 - ₹25,000',
            '25000_50000' => '₹25,000 - ₹50,000',
            '50000_100000' => '₹50,000 - ₹1,00,000',
            '100000_plus' => '₹1,00,000+',
            'custom' => 'Custom Budget (Specify in message)'
        ];

        return view('shop::customization.index', compact('categories', 'budgetOptions'));
    }

    /**
     * Handle form submission.
     *
     * @param \Webkul\Shop\Http\Requests\CustomizationRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function submit(CustomizationRequest $request): RedirectResponse
    {
        // Debug: Log that we reached the controller
        \Log::info('CustomizationController::submit called', [
            'request_data' => $request->all(),
            'validated_data' => $request->validated()
        ]);
        
        $uploadedFiles = [];
        
        try {
            // Get form data
            $formData = $request->validated();
            
            // Handle file uploads
            $uploadedFiles = $this->handleFileUploads($request);
            
            // Send emails
            $this->sendEmails($formData, $uploadedFiles);
            
            // Clean up uploaded files after successful email sending
            $this->cleanupFiles($uploadedFiles);
            
            // Redirect to thank you page
            return redirect()->route('shop.customisation.thank_you');
            
        } catch (\Exception $e) {
            // Clean up files even if email sending fails
            if (!empty($uploadedFiles)) {
                $this->cleanupFiles($uploadedFiles);
            }
            
            // Log error and show user-friendly message
            report($e);
            
            return back()->with('error', 'Something went wrong. Please try again.')
                        ->withInput();
        }
    }

    /**
     * Get products by category ID (AJAX endpoint).
     *
     * @param int $categoryId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getProducts(int $categoryId): JsonResponse
    {
        try {
            // Validate category exists and is active
            $category = $this->categoryRepository->find($categoryId);
            
            if (!$category || $category->status != 1) {
                return response()->json([
                    'success' => false,
                    'message' => 'Category not found or inactive'
                ], 404);
            }

            // Get products for the category
            $products = $this->productRepository->getAll([
                'category_id' => $categoryId,
                'status' => 1,
                'locale' => app()->getLocale(),
            ]);

            // Transform products for frontend
            $transformedProducts = $products->map(function ($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'sku' => $product->sku ?? '',
                ];
            });

            return response()->json([
                'success' => true,
                'products' => $transformedProducts,
                'count' => $transformedProducts->count(),
                'category_name' => $category->name
            ]);
            
        } catch (\Exception $e) {
            // Log the error for debugging
            report($e);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to load products. Please try again.'
            ], 500);
        }
    }

    /**
     * Display thank you page.
     *
     * @return \Illuminate\View\View
     */
    public function thankYou(): View
    {
        return view('shop::customization.thank-you');
    }

    /**
     * Handle file uploads.
     *
     * @param \Webkul\Shop\Http\Requests\CustomizationRequest $request
     * @return array
     */
    private function handleFileUploads(CustomizationRequest $request): array
    {
        $uploadedFiles = [];
        
        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                // Generate secure filename: timestamp_randomstring_originalname
                $timestamp = time();
                $randomString = uniqid();
                $originalName = $file->getClientOriginalName();
                
                // Sanitize original filename to prevent path traversal
                $sanitizedOriginalName = preg_replace('/[^a-zA-Z0-9._-]/', '_', $originalName);
                
                $filename = $timestamp . '_' . $randomString . '_' . $sanitizedOriginalName;
                $path = $file->storeAs('customization-requests', $filename, 'public');
                
                $uploadedFiles[] = [
                    'path' => $path,
                    'original_name' => $originalName,
                    'size' => $file->getSize(),
                    'mime_type' => $file->getMimeType(),
                ];
            }
        }
        
        return $uploadedFiles;
    }

    /**
     * Send emails to admin and customer.
     *
     * @param array $formData
     * @param array $uploadedFiles
     * @return void
     */
    private function sendEmails(array $formData, array $uploadedFiles): void
    {
        // Get category and product names
        $category = $this->categoryRepository->find($formData['category_id']);
        $product = $this->productRepository->find($formData['product_id']);
        
        $formData['category_name'] = $category->name ?? 'Unknown Category';
        $formData['product_name'] = $product->name ?? 'Unknown Product';
        
        // Validate and prepare file data for email attachments
        $formData['files'] = $this->prepareFileDataForEmail($uploadedFiles);
        
        // Send admin email
        Mail::queue(new \Webkul\Shop\Mail\CustomizationRequestAdmin($formData));
        
        // Send customer email
        Mail::queue(new \Webkul\Shop\Mail\CustomizationRequestCustomer($formData));
    }

    /**
     * Prepare file data for email attachments.
     *
     * @param array $uploadedFiles
     * @return array
     */
    private function prepareFileDataForEmail(array $uploadedFiles): array
    {
        $validFiles = [];
        
        foreach ($uploadedFiles as $file) {
            // Validate file data structure
            if (isset($file['path']) && isset($file['original_name']) && isset($file['mime_type'])) {
                // Verify file exists before including in email
                if (Storage::disk('public')->exists($file['path'])) {
                    $validFiles[] = [
                        'path' => $file['path'],
                        'original_name' => $file['original_name'],
                        'size' => $file['size'] ?? 0,
                        'mime_type' => $file['mime_type'],
                    ];
                }
            }
        }
        
        return $validFiles;
    }

    /**
     * Clean up uploaded files.
     *
     * @param array $uploadedFiles
     * @return void
     */
    private function cleanupFiles(array $uploadedFiles): void
    {
        foreach ($uploadedFiles as $file) {
            try {
                // Only delete if file exists
                if (Storage::disk('public')->exists($file['path'])) {
                    Storage::disk('public')->delete($file['path']);
                }
            } catch (\Exception $e) {
                // Log cleanup errors but don't fail the request
                report($e);
            }
        }
    }
}
