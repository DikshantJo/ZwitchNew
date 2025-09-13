@component('shop::emails.layout')
    <!-- Email Header -->
    <div style="margin-bottom: 30px;">
        <h1 style="font-size: 24px; color: #1a1a1a; font-weight: 600; margin: 0 0 10px 0; line-height: 1.2;">
            🎨 New Customization Request
        </h1>
        <p style="font-size: 16px; color: #676665; margin: 0; line-height: 1.4;">
            A customer has submitted a new customization request. Please review the details below and respond promptly.
        </p>
    </div>

    <!-- Request Details Card -->
    <div style="background: #f8f9fa; border: 1px solid #e9ecef; border-radius: 8px; padding: 24px; margin-bottom: 30px;">
        <h2 style="font-size: 20px; color: #1a1a1a; font-weight: 600; margin: 0 0 20px 0; border-bottom: 2px solid #c2b4a3; padding-bottom: 10px;">
            📋 Request Details
        </h2>
        
        <!-- Customer Information -->
        <div style="margin-bottom: 20px;">
            <h3 style="font-size: 16px; color: #1a1a1a; font-weight: 600; margin: 0 0 12px 0;">
                👤 Customer Information
            </h3>
            <table style="width: 100%; border-collapse: collapse;">
                <tr>
                    <td style="padding: 8px 0; font-weight: 500; color: #384860; width: 120px;">Name:</td>
                    <td style="padding: 8px 0; color: #1a1a1a;">{{ $data['name'] }}</td>
                </tr>
                <tr>
                    <td style="padding: 8px 0; font-weight: 500; color: #384860;">Email:</td>
                    <td style="padding: 8px 0; color: #1a1a1a;">
                        <a href="mailto:{{ $data['email'] }}" style="color: #2969FF; text-decoration: none;">{{ $data['email'] }}</a>
                    </td>
                </tr>
                <tr>
                    <td style="padding: 8px 0; font-weight: 500; color: #384860;">Phone:</td>
                    <td style="padding: 8px 0; color: #1a1a1a;">
                        <a href="tel:{{ $data['phone'] }}" style="color: #2969FF; text-decoration: none;">{{ $data['phone'] }}</a>
                    </td>
                </tr>
            </table>
        </div>

        <!-- Project Details -->
        <div style="margin-bottom: 20px;">
            <h3 style="font-size: 16px; color: #1a1a1a; font-weight: 600; margin: 0 0 12px 0;">
                💼 Contact Preferences
            </h3>
            <table style="width: 100%; border-collapse: collapse;">
                <tr>
                    <td style="padding: 8px 0; font-weight: 500; color: #384860; width: 120px;">Best Time to Contact:</td>
                    <td style="padding: 8px 0; color: #1a1a1a;">
                        <span style="background: #c2b4a3; color: #0f0f0f; padding: 4px 8px; border-radius: 4px; font-weight: 600;">
                            {{ ucfirst(str_replace('_', ' ', $data['best_time_to_contact'])) }}
                        </span>
                    </td>
                </tr>
                <tr>
                    <td style="padding: 8px 0; font-weight: 500; color: #384860;">Preferred Contact:</td>
                    <td style="padding: 8px 0; color: #1a1a1a;">
                        <span style="background: #e3f2fd; color: #1976d2; padding: 4px 8px; border-radius: 4px; font-weight: 600;">
                            {{ ucfirst($data['preferred_contact']) }}
                        </span>
                    </td>
                </tr>
            </table>
        </div>

        <!-- Customization Description -->
        <div style="margin-bottom: 20px;">
            <h3 style="font-size: 16px; color: #1a1a1a; font-weight: 600; margin: 0 0 12px 0;">
                📝 Customization Description
            </h3>
            <div style="background: #ffffff; border: 1px solid #e9ecef; border-radius: 6px; padding: 16px;">
                <p style="font-size: 14px; color: #1a1a1a; margin: 0; line-height: 1.6; white-space: pre-wrap;">{{ $data['customization_description'] }}</p>
            </div>
        </div>

        <!-- File Attachments -->
        @if(!empty($data['files']))
        <div style="margin-bottom: 20px;">
            <h3 style="font-size: 16px; color: #1a1a1a; font-weight: 600; margin: 0 0 12px 0;">
                📎 @lang('shop::app.emails.customization-request.admin.reference-files') ({{ count($data['files']) }} file(s))
            </h3>
            <div style="background: #ffffff; border: 1px solid #e9ecef; border-radius: 6px; padding: 12px;">
                @foreach($data['files'] as $file)
                <div style="display: flex; align-items: center; padding: 6px 0; border-bottom: 1px solid #f1f3f4;">
                    <span style="margin-right: 8px;">
                        @if(str_contains($file['mime_type'], 'image'))
                            🖼️
                        @elseif(str_contains($file['mime_type'], 'pdf'))
                            📄
                        @else
                            📎
                        @endif
                    </span>
                    <span style="color: #1a1a1a; font-size: 14px;">{{ $file['original_name'] }}</span>
                    <span style="color: #676665; font-size: 12px; margin-left: auto;">
                        ({{ number_format($file['size'] / 1024, 1) }} KB)
                    </span>
                </div>
                @endforeach
            </div>
            <p style="font-size: 12px; color: #676665; margin: 8px 0 0 0;">
                💡 Files are attached to this email for your review.
            </p>
        </div>
        @endif
    </div>

    <!-- Action Required -->
    <div style="background: #fff3cd; border: 1px solid #ffeaa7; border-radius: 8px; padding: 20px; margin-bottom: 30px;">
        <h3 style="font-size: 16px; color: #856404; font-weight: 600; margin: 0 0 10px 0;">
            ⚡ @lang('shop::app.emails.customization-request.admin.action-required')
        </h3>
        <p style="font-size: 14px; color: #856404; margin: 0; line-height: 1.4;">
            Please review this customization request and respond to the customer within 24 hours. 
            You can contact them directly at <a href="mailto:{{ $data['email'] }}" style="color: #2969FF; text-decoration: none;">{{ $data['email'] }}</a> 
            or <a href="tel:{{ $data['phone'] }}" style="color: #2969FF; text-decoration: none;">{{ $data['phone'] }}</a>.
        </p>
    </div>

    <!-- Quick Actions -->
    <div style="margin-bottom: 30px;">
        <h3 style="font-size: 16px; color: #1a1a1a; font-weight: 600; margin: 0 0 15px 0;">
            🚀 @lang('shop::app.emails.customization-request.admin.quick-actions')
        </h3>
        <div style="display: flex; gap: 12px; flex-wrap: wrap;">
            <a href="mailto:{{ $data['email'] }}?subject=Re: Customization Request - {{ $data['name'] }}&body=Hi {{ $data['name'] }},%0D%0A%0D%0AThank you for your customization request. We have received your request and will review it shortly.%0D%0A%0D%0ABest regards,%0D%0A{{ config('app.name') }} Team" 
               style="background: #c2b4a3; color: #0f0f0f; padding: 10px 16px; border-radius: 6px; text-decoration: none; font-weight: 600; font-size: 14px;">
                📧 @lang('shop::app.emails.customization-request.admin.reply-customer')
            </a>
            <a href="tel:{{ $data['phone'] }}" 
               style="background: #28a745; color: white; padding: 10px 16px; border-radius: 6px; text-decoration: none; font-weight: 600; font-size: 14px;">
                📞 @lang('shop::app.emails.customization-request.admin.call-customer')
            </a>
        </div>
    </div>

    <!-- Request Summary -->
    <div style="background: #f8f9fa; border-left: 4px solid #c2b4a3; padding: 16px; margin-bottom: 30px;">
        <h4 style="font-size: 14px; color: #1a1a1a; font-weight: 600; margin: 0 0 8px 0;">
            📊 @lang('shop::app.emails.customization-request.admin.request-summary')
        </h4>
        <p style="font-size: 14px; color: #676665; margin: 0; line-height: 1.4;">
            <strong>{{ $data['name'] }}</strong> has submitted a customization request. 
            They prefer to be contacted via <strong>{{ ucfirst($data['preferred_contact']) }}</strong> 
            during <strong>{{ ucfirst(str_replace('_', ' ', $data['best_time_to_contact'])) }}</strong>.
            @if(!empty($data['files']))
                They have also provided <strong>{{ count($data['files']) }} reference file(s)</strong> for your review.
            @endif
        </p>
    </div>

    <!-- Footer Note -->
    <div style="border-top: 1px solid #e9ecef; padding-top: 20px; margin-top: 30px;">
        <p style="font-size: 12px; color: #676665; margin: 0; line-height: 1.4;">
            @lang('shop::app.emails.customization-request.admin.footer-note', ['app_name' => config('app.name')])
        </p>
    </div>
@endcomponent
