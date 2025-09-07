<?php

namespace Webkul\Shop\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Webkul\Core\Rules\PhoneNumber;

class CustomizationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        // Debug: Log validation attempt
        \Log::info('CustomizationRequest validation started', [
            'input_data' => $this->all()
        ]);
        
        return [
            'name' => 'required|string|max:255',
            'phone' => ['required', new PhoneNumber],
            'email' => 'required|email|max:255',
            'category_id' => 'required|exists:categories,id',
            'product_id' => 'nullable|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'budget' => 'required|string|in:under_1000,1000_2500,2500_5000,5000_10000,10000_15000,15000_25000,25000_50000,50000_100000,100000_plus,custom',
            'timeline' => 'required|date|after:today',
            'files.*' => 'nullable|file|max:5120|mimes:jpeg,jpg,png,pdf',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'name.required' => 'Please enter your name.',
            'name.max' => 'Name cannot exceed 255 characters.',
            
            'phone.required' => 'Please enter your phone number.',
            
            'email.required' => 'Please enter your email address.',
            'email.email' => 'Please enter a valid email address.',
            'email.max' => 'Email cannot exceed 255 characters.',
            
            'category_id.required' => 'Please select a category.',
            'category_id.exists' => 'Selected category is invalid.',
            
            'product_id.exists' => 'Selected product is invalid.',
            
            'quantity.required' => 'Please enter the quantity.',
            'quantity.integer' => 'Quantity must be a whole number.',
            'quantity.min' => 'Quantity must be at least 1.',
            
            'budget.required' => 'Please select a budget range.',
            'budget.in' => 'Please select a valid budget range.',
            
            'timeline.required' => 'Please select a timeline.',
            'timeline.date' => 'Please enter a valid date.',
            'timeline.after' => 'Timeline must be a future date.',
            
            'files.*.file' => 'Each file must be a valid file.',
            'files.*.max' => 'Each file cannot exceed 5MB.',
            'files.*.mimes' => 'Files must be images (JPEG, JPG, PNG) or PDF documents.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            'name' => 'Name',
            'phone' => 'Phone Number',
            'email' => 'Email Address',
            'category_id' => 'Category',
            'product_id' => 'Product',
            'quantity' => 'Quantity',
            'budget' => 'Budget Range',
            'timeline' => 'Timeline',
            'files.*' => 'File',
        ];
    }
}
