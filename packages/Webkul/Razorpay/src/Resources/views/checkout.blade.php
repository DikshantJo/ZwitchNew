@extends('shop::layouts.master')

@section('page_title')
    {{ __('razorpay::app.payment.title') }}
@endsection

@section('content-wrapper')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h4>{{ __('razorpay::app.payment.title') }}</h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h5>Order Summary</h5>
                                <table class="table">
                                    <tr>
                                        <td>Order ID:</td>
                                        <td>{{ $cart->id }}</td>
                                    </tr>
                                    <tr>
                                        <td>Total Amount:</td>
                                        <td>{{ core()->currency($cart->grand_total) }}</td>
                                    </tr>
                                    <tr>
                                        <td>Currency:</td>
                                        <td>{{ $cart->getCartCurrencyCode() }}</td>
                                    </tr>
                                    <tr>
                                        <td>Items:</td>
                                        <td>{{ $cart->items->count() }} item(s)</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <h5>Payment Details</h5>
                                <p>{{ __('razorpay::app.payment.description') }}</p>
                                
                                <div class="alert alert-info">
                                    <strong>Supported Payment Methods:</strong><br>
                                    • Credit/Debit Cards<br>
                                    • UPI<br>
                                    • Net Banking<br>
                                    • Digital Wallets<br>
                                    • EMI
                                </div>
                                
                                @if (isset($checkoutData['instructions']))
                                    <div class="alert alert-warning">
                                        <strong>Instructions:</strong><br>
                                        {{ $checkoutData['instructions'] }}
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="text-center mt-4">
                            <button id="rzp-button" class="btn btn-primary btn-lg">
                                <i class="fas fa-lock"></i> Pay Securely {{ core()->currency($cart->grand_total) }}
                            </button>
                        </div>

                        <div class="text-center mt-3">
                            <small class="text-muted">
                                <i class="fas fa-shield-alt"></i> You will be redirected to Razorpay's secure payment gateway
                            </small>
                        </div>
                        
                        <div class="text-center mt-2">
                            <small class="text-muted">
                                <i class="fas fa-info-circle"></i> Your payment information is encrypted and secure
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Razorpay Script -->
    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var options = @json($checkoutData);
            var button = document.getElementById('rzp-button');
            var originalText = button.innerHTML;
            
            options.handler = function (response) {
                // Show loading state
                button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing Payment...';
                button.disabled = true;
                
                // Create a form to submit payment details
                var form = document.createElement('form');
                form.method = 'POST';
                form.action = '{{ route("razorpay.success") }}';
                
                // Add CSRF token
                var csrfToken = document.createElement('input');
                csrfToken.type = 'hidden';
                csrfToken.name = '_token';
                csrfToken.value = '{{ csrf_token() }}';
                form.appendChild(csrfToken);
                
                // Add payment details
                var paymentId = document.createElement('input');
                paymentId.type = 'hidden';
                paymentId.name = 'razorpay_payment_id';
                paymentId.value = response.razorpay_payment_id;
                form.appendChild(paymentId);
                
                var orderId = document.createElement('input');
                orderId.type = 'hidden';
                orderId.name = 'razorpay_order_id';
                orderId.value = response.razorpay_order_id;
                form.appendChild(orderId);
                
                var signature = document.createElement('input');
                signature.type = 'hidden';
                signature.name = 'razorpay_signature';
                signature.value = response.razorpay_signature;
                form.appendChild(signature);
                
                document.body.appendChild(form);
                form.submit();
            };
            
            options.modal = {
                ondismiss: function() {
                    // Reset button state
                    button.innerHTML = originalText;
                    button.disabled = false;
                    
                    // Show cancellation message
                    if (confirm('Payment was cancelled. Do you want to return to checkout?')) {
                        window.location.href = '{{ route("shop.checkout.onepage.index") }}';
                    }
                }
            };
            
            options.prefill = {
                name: '{{ $cart->customer->first_name }} {{ $cart->customer->last_name }}',
                email: '{{ $cart->customer->email }}',
                contact: '{{ $cart->billing_address->phone ?? "" }}'
            };
            
            options.theme = {
                color: '{{ core()->getConfigData("sales.payment_methods.razorpay.theme") ?? "#3399cc" }}'
            };
            
            var rzp = new Razorpay(options);
            
            button.onclick = function(e) {
                e.preventDefault();
                
                // Show loading state
                button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Opening Payment Gateway...';
                button.disabled = true;
                
                // Open Razorpay
                rzp.open();
            };
            
            // Handle page unload to show warning
            window.addEventListener('beforeunload', function(e) {
                if (button.disabled) {
                    e.preventDefault();
                    e.returnValue = 'Payment is in progress. Are you sure you want to leave?';
                    return e.returnValue;
                }
            });
        });
    </script>
@endsection 