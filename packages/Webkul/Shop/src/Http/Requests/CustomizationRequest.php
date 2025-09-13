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
     * Determine if the request should be validated.
     *
     * @return bool
     */
    public function shouldValidate()
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
            'input_data' => $this->all(),
            'csrf_token' => $this->input('_token'),
            'session_token' => session()->token(),
            'csrf_match' => $this->input('_token') === session()->token(),
            'has_csrf' => $this->has('_token'),
            'session_id' => session()->getId()
        ]);
        
        return [
            'name' => 'required|string|max:255',
            'phone' => 'required|string|min:10|max:20',
            'email' => 'required|email|max:255',
            'best_time_to_contact' => 'required|string|in:morning,afternoon,evening,anytime',
            'preferred_contact' => 'required|string|in:phone,email,whatsapp,sms',
            'customization_description' => 'required|string|min:10|max:2000',
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
            
            'best_time_to_contact.required' => 'Please select the best time to contact you.',
            'best_time_to_contact.in' => 'Please select a valid contact time.',
            
            'preferred_contact.required' => 'Please select your preferred contact method.',
            'preferred_contact.in' => 'Please select a valid contact method.',
            
            'customization_description.required' => 'Please describe your customization requirements.',
            'customization_description.min' => 'Description must be at least 10 characters long.',
            'customization_description.max' => 'Description cannot exceed 2000 characters.',
            
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
            'best_time_to_contact' => 'Best Time to Contact',
            'preferred_contact' => 'Preferred Contact Method',
            'customization_description' => 'Customization Description',
            'files.*' => 'File',
        ];
    }
}
