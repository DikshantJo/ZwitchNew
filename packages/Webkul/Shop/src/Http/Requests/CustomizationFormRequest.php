<?php

namespace Webkul\Shop\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CustomizationFormRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255|min:2',
            'phone' => 'required|string|min:10|max:15|regex:/^(\+91[\s\-]?)?[6-9]\d{9}$/',
            'email' => 'required|email|max:255',
            'best_time_to_contact' => 'required|string|in:morning,afternoon,evening,anytime',
            'preferred_contact' => 'required|string|in:phone,email,whatsapp,sms',
            'customization_description' => 'required|string|min:10|max:2000',
            'files.*' => 'nullable|file|max:5120|mimes:jpeg,jpg,png,pdf',
        ];
    }

    /**
     * Get custom validation messages.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Please enter your full name.',
            'name.min' => 'Name must be at least 2 characters long.',
            'name.max' => 'Name cannot exceed 255 characters.',
            
            'phone.required' => 'Please enter your phone number.',
            'phone.min' => 'Phone number must be at least 10 digits.',
            'phone.max' => 'Phone number cannot exceed 15 characters.',
            'phone.regex' => 'Please enter a valid Indian phone number (e.g., +91 98765 43210 or 9876543210).',
            
            'email.required' => 'Please enter your email address.',
            'email.email' => 'Please enter a valid email address.',
            'email.max' => 'Email address cannot exceed 255 characters.',
            
            'best_time_to_contact.required' => 'Please select your preferred contact time.',
            'best_time_to_contact.in' => 'Please select a valid contact time option.',
            
            'preferred_contact.required' => 'Please select your preferred contact method.',
            'preferred_contact.in' => 'Please select a valid contact method.',
            
            'customization_description.required' => 'Please describe your customization requirements.',
            'customization_description.min' => 'Description must be at least 10 characters long.',
            'customization_description.max' => 'Description cannot exceed 2000 characters.',
            
            'files.*.file' => 'Please upload valid files only.',
            'files.*.max' => 'Each file must not exceed 5MB.',
            'files.*.mimes' => 'Only JPEG, JPG, PNG, and PDF files are allowed.',
        ];
    }

    /**
     * Get custom attribute names for validation errors.
     */
    public function attributes(): array
    {
        return [
            'name' => 'full name',
            'phone' => 'phone number',
            'email' => 'email address',
            'best_time_to_contact' => 'preferred contact time',
            'preferred_contact' => 'preferred contact method',
            'customization_description' => 'customization description',
            'files.*' => 'uploaded file',
        ];
    }
}
