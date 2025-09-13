@component('shop::emails.layout')
    <!-- Email Header -->
    <div style="margin-bottom: 30px;">
        <h1 style="font-size: 24px; color: #1a1a1a; font-weight: 600; margin: 0 0 10px 0; line-height: 1.2;">
            🎨 Thank You for Your Customization Request!
        </h1>
        <p style="font-size: 16px; color: #676665; margin: 0; line-height: 1.4;">
            Hi {{ $data['name'] }}, we've received your customization request and will review it shortly. Our team will get back to you within 24 hours.
        </p>
    </div>

    <!-- Reference Number Card -->
    <div style="background: linear-gradient(135deg, #c2b4a3 0%, #ae9b84 100%); border-radius: 12px; padding: 24px; margin-bottom: 30px; text-align: center;">
        <h2 style="font-size: 18px; color: #0f0f0f; font-weight: 600; margin: 0 0 8px 0;">
            📋 Request Reference
        </h2>
        <p style="font-size: 24px; color: #0f0f0f; font-weight: 700; margin: 0; font-family: 'Courier New', monospace; letter-spacing: 2px;">
            {{ 'CUST-' . date('Ymd') . '-' . substr(md5($data['email'] . time()), 0, 6) }}
        </p>
        <p style="font-size: 14px; color: #1f1f1f; margin: 8px 0 0 0; opacity: 0.8;">
            Please keep this reference number for your records
        </p>
    </div>

    <!-- Request Details Card -->
    <div style="background: #f8f9fa; border: 1px solid #e9ecef; border-radius: 8px; padding: 24px; margin-bottom: 30px;">
        <h2 style="font-size: 20px; color: #1a1a1a; font-weight: 600; margin: 0 0 20px 0; border-bottom: 2px solid #c2b4a3; padding-bottom: 10px;">
            📋 @lang('shop::app.emails.customization-request.customer.request-details')
        </h2>
        
        <!-- Customer Information -->
        <div style="margin-bottom: 20px;">
            <h3 style="font-size: 16px; color: #1a1a1a; font-weight: 600; margin: 0 0 12px 0;">
                👤 Your Information
            </h3>
            <table style="width: 100%; border-collapse: collapse;">
                <tr>
                    <td style="padding: 8px 0; font-weight: 500; color: #384860; width: 120px;">Name:</td>
                    <td style="padding: 8px 0; color: #1a1a1a;">{{ $data['name'] }}</td>
                </tr>
                <tr>
                    <td style="padding: 8px 0; font-weight: 500; color: #384860;">Email:</td>
                    <td style="padding: 8px 0; color: #1a1a1a;">{{ $data['email'] }}</td>
                </tr>
                <tr>
                    <td style="padding: 8px 0; font-weight: 500; color: #384860;">Phone:</td>
                    <td style="padding: 8px 0; color: #1a1a1a;">{{ $data['phone'] }}</td>
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
                📝 Your Customization Description
            </h3>
            <div style="background: #ffffff; border: 1px solid #e9ecef; border-radius: 6px; padding: 16px;">
                <p style="font-size: 14px; color: #1a1a1a; margin: 0; line-height: 1.6; white-space: pre-wrap;">{{ $data['customization_description'] }}</p>
            </div>
        </div>

        <!-- File Attachments -->
        @if(!empty($data['files']))
        <div style="margin-bottom: 20px;">
            <h3 style="font-size: 16px; color: #1a1a1a; font-weight: 600; margin: 0 0 12px 0;">
                📎 Reference Files Submitted ({{ count($data['files']) }} file(s))
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
                ✅ @lang('shop::app.emails.customization-request.customer.files-received')
            </p>
            <p style="font-size: 12px; color: #676665; margin: 4px 0 0 0; font-style: italic;">
                📎 @lang('shop::app.emails.customization-request.customer.files-attached')
            </p>
        </div>
        @endif
    </div>

    <!-- Next Steps -->
    <div style="background: #e8f5e8; border: 1px solid #c3e6c3; border-radius: 8px; padding: 20px; margin-bottom: 30px;">
        <h3 style="font-size: 16px; color: #2d5a2d; font-weight: 600; margin: 0 0 15px 0;">
            🚀 @lang('shop::app.emails.customization-request.customer.next-steps')
        </h3>
        <div style="margin-bottom: 15px;">
            <div style="display: flex; align-items: flex-start; margin-bottom: 12px;">
                <div style="background: #28a745; color: white; border-radius: 50%; width: 24px; height: 24px; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 600; margin-right: 12px; flex-shrink: 0;">1</div>
                <div>
                    <h4 style="font-size: 14px; color: #2d5a2d; font-weight: 600; margin: 0 0 4px 0;">Review & Analysis</h4>
                    <p style="font-size: 13px; color: #2d5a2d; margin: 0; line-height: 1.4;">Our team will review your request and analyze your requirements.</p>
                </div>
            </div>
            <div style="display: flex; align-items: flex-start; margin-bottom: 12px;">
                <div style="background: #28a745; color: white; border-radius: 50%; width: 24px; height: 24px; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 600; margin-right: 12px; flex-shrink: 0;">2</div>
                <div>
                    <h4 style="font-size: 14px; color: #2d5a2d; font-weight: 600; margin: 0 0 4px 0;">Quote & Timeline</h4>
                    <p style="font-size: 13px; color: #2d5a2d; margin: 0; line-height: 1.4;">We'll provide you with a detailed quote and confirmed timeline.</p>
                </div>
            </div>
            <div style="display: flex; align-items: flex-start;">
                <div style="background: #28a745; color: white; border-radius: 50%; width: 24px; height: 24px; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 600; margin-right: 12px; flex-shrink: 0;">3</div>
                <div>
                    <h4 style="font-size: 14px; color: #2d5a2d; font-weight: 600; margin: 0 0 4px 0;">Project Start</h4>
                    <p style="font-size: 13px; color: #2d5a2d; margin: 0; line-height: 1.4;">Once approved, we'll begin working on your customization.</p>
                </div>
            </div>
        </div>
        <div style="background: #ffffff; border: 1px solid #c3e6c3; border-radius: 6px; padding: 12px; margin-top: 15px;">
            <p style="font-size: 14px; color: #2d5a2d; margin: 0; font-weight: 600;">
                ⏰ @lang('shop::app.emails.customization-request.customer.estimated-response')
            </p>
        </div>
    </div>

    <!-- Contact Information -->
    <div style="background: #f8f9fa; border-left: 4px solid #c2b4a3; padding: 20px; margin-bottom: 30px;">
        <h3 style="font-size: 16px; color: #1a1a1a; font-weight: 600; margin: 0 0 12px 0;">
            📞 @lang('shop::app.emails.customization-request.customer.contact-info')
        </h3>
        <p style="font-size: 14px; color: #676665; margin: 0 0 12px 0; line-height: 1.4;">
            If you have any questions or need to make changes to your request, please don't hesitate to contact us:
        </p>
        <div style="display: flex; gap: 20px; flex-wrap: wrap;">
            <div style="display: flex; align-items: center;">
                <span style="margin-right: 8px;">📧</span>
                <a href="mailto:{{ core()->getContactEmailDetails()['email'] }}" style="color: #2969FF; text-decoration: none; font-size: 14px;">
                    {{ core()->getContactEmailDetails()['email'] }}
                </a>
            </div>
            <div style="display: flex; align-items: center;">
                <span style="margin-right: 8px;">📱</span>
                <a href="tel:{{ core()->getContactEmailDetails()['phone'] ?? '+1 (555) 123-4567' }}" style="color: #2969FF; text-decoration: none; font-size: 14px;">
                    {{ core()->getContactEmailDetails()['phone'] ?? '+1 (555) 123-4567' }}
                </a>
            </div>
        </div>
    </div>

    <!-- Thank You Message -->
    <div style="text-align: center; margin-bottom: 30px;">
        <div style="background: #c2b4a3; border-radius: 50%; width: 80px; height: 80px; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px auto;">
            <span style="font-size: 32px;">🎨</span>
        </div>
        <h3 style="font-size: 18px; color: #1a1a1a; font-weight: 600; margin: 0 0 8px 0;">
            @lang('shop::app.emails.customization-request.customer.thank-you', ['app_name' => config('app.name')])
        </h3>
        <p style="font-size: 14px; color: #676665; margin: 0; line-height: 1.4;">
            We're excited to work on your customization project and bring your vision to life!
        </p>
    </div>

    <!-- Call to Action -->
    <div style="text-align: center; margin-bottom: 30px;">
        <a href="{{ route('shop.home.index') }}" 
           style="background: #c2b4a3; color: #0f0f0f; padding: 12px 24px; border-radius: 6px; text-decoration: none; font-weight: 600; font-size: 14px; display: inline-block;">
            🛍️ @lang('shop::app.emails.customization-request.customer.continue-shopping')
        </a>
    </div>

    <!-- Footer Note -->
    <div style="border-top: 1px solid #e9ecef; padding-top: 20px; margin-top: 30px;">
        <p style="font-size: 12px; color: #676665; margin: 0; line-height: 1.4;">
            @lang('shop::app.emails.customization-request.customer.confirmation-note')
        </p>
    </div>
@endcomponent
