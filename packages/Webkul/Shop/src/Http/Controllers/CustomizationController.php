<?php

namespace Webkul\Shop\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Webkul\Category\Repositories\CategoryRepository;
use Webkul\Product\Repositories\ProductRepository;
use Webkul\Shop\Http\Requests\CustomizationFormRequest;

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
        return view('shop::customization.index');
    }

    /**
     * Handle form submission.
     *
     * @param \Webkul\Shop\Http\Requests\CustomizationRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function submit(CustomizationFormRequest $request): RedirectResponse
    {
        // Get validated data
        $validated = $request->validated();
        
        $uploadedFiles = [];
        
        try {
            // Handle file uploads
            $uploadedFiles = $this->handleFileUploads($request);
            
            // Send emails
            $this->sendEmails($validated, $uploadedFiles);
            
            // Clean up uploaded files after successful email sending
            $this->cleanupFiles($uploadedFiles);
            
            // Redirect to thank you page
            return redirect()->route('shop.customisation.thank_you')
                ->with('success', 'Customization request submitted successfully!');
            
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
     * @param string $categoryId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getProducts(string $categoryId): JsonResponse
    {
        try {
            \Log::info('getProducts called', ['categoryId' => $categoryId]);
            
            if ($categoryId === 'all') {
                // Return all products for "All Products" category
                $products = $this->productRepository->getAll([
                    'locale' => app()->getLocale(),
                ]);
                
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
                    'category_name' => 'All Products'
                ]);
            }
            
            // For specific categories, check if category exists in pivot table
            $categoryExists = DB::table('product_categories')
                ->where('category_id', $categoryId)
                ->exists();
                
            if (!$categoryExists) {
                return response()->json([
                    'success' => false,
                    'message' => 'Category not found or has no products'
                ], 404);
            }
            
            // Get product IDs for this category from pivot table
            $productIds = DB::table('product_categories')
                ->where('category_id', $categoryId)
                ->pluck('product_id');
                
            \Log::info('Product IDs for category', [
                'categoryId' => $categoryId,
                'productIds' => $productIds->toArray()
            ]);
                
            // Get products using the product IDs - try different approach
            $products = collect();
            if ($productIds->isNotEmpty()) {
                $products = $this->productRepository->getAll([
                    'locale' => app()->getLocale(),
                ])->filter(function($product) use ($productIds) {
                    return $productIds->contains($product->id);
                });
            }
            
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
                'category_name' => "Category {$categoryId}"
            ]);
            
        } catch (\Exception $e) {
            // Log the error for debugging
            \Log::error('getProducts error', [
                'categoryId' => $categoryId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to load products. Please try again.'
            ], 500);
        }
    }

    /**
     * Test form submission without validation.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function testSubmit(Request $request): RedirectResponse
    {
        \Log::info('Test submit called', [
            'request_data' => $request->all(),
            'csrf_token' => $request->input('_token'),
            'session_token' => session()->token(),
            'csrf_match' => $request->input('_token') === session()->token()
        ]);
        
        // Basic validation
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|min:10|max:20',
            'email' => 'required|email|max:255',
            'best_time_to_contact' => 'required|string|in:morning,afternoon,evening,anytime',
            'preferred_contact' => 'required|string|in:phone,email,whatsapp,sms',
            'customization_description' => 'required|string|min:10|max:2000',
        ]);
        
        \Log::info('Test submit validation passed', ['validated_data' => $validated]);
        
        return redirect()->route('shop.customisation.thank_you')
            ->with('success', 'Test submission successful!');
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
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    private function handleFileUploads(Request $request): array
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
        // Validate and prepare file data for email attachments
        $formData['files'] = $this->prepareFileDataForEmail($uploadedFiles);

        // Send admin emails to both addresses
        $adminEmails = ['maildikshantjoshi@gmail.com', 'zwitchcustoms@gmail.com'];
        
        foreach ($adminEmails as $email) {
            Mail::send(new \Webkul\Shop\Mail\CustomizationRequestAdmin($formData, $email));
        }

        // Send customer email
        Mail::send(new \Webkul\Shop\Mail\CustomizationRequestCustomer($formData));
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
