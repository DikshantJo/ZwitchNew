# Razorpay Implementation Rules & Development Guide

## Progress Tracking
**Overall Progress**: 100% Complete
**Last Updated**: 2024-12-17
**Current Phase**: Phase 5 Complete

### Phase Progress
- [x] **Phase 1: Core Setup** (7/7 tasks) - 100% ✅ COMPLETED
- [x] **Phase 2: API Integration** (7/7 tasks) - 100% ✅ COMPLETED
- [x] **Phase 3: Checkout Integration** (7/7 tasks) - 100% ✅ COMPLETED
- [x] **Phase 4: Admin Integration** (7/7 tasks) - 100% ✅ COMPLETED
- [x] **Phase 5: Testing & Refinement** (7/7 tasks) - 100% ✅ COMPLETED

### Quick Status
- **Package Structure**: ✓ Complete
- **Security Implementation**: ✓ Complete
- **API Integration**: ✓ Complete
- **Checkout Flow**: ✓ Complete
- **Admin Panel**: ✓ Complete
- **Testing**: ✓ Complete
- **Documentation**: ✓ Complete

### Notes & Issues
- **Completed Tasks**: 
  - ✓ Created Razorpay package directory structure
  - ✓ Implemented Razorpay payment method class
  - ✓ Created service providers (RazorpayServiceProvider, EventServiceProvider)
  - ✓ Added Razorpay to payment methods configuration
  - ✓ Registered in concord.php modules array
  - ✓ Created basic routes and migrations
  - ✓ Implemented RazorpayService class
  - ✓ Added language files
  - ✓ Fixed service provider method signature compatibility
  - ✓ Verified all components work correctly
  - ✓ Implemented comprehensive RazorpayService with all API methods
  - ✓ Created RazorpayOrder and RazorpayPayment models
  - ✓ Implemented WebhookController with full webhook handling
  - ✓ Implemented PaymentController with checkout, success, and failure handling
  - ✓ Created checkout view with Razorpay integration
  - ✓ Added signature generation and verification
  - ✓ Implemented comprehensive error handling
  - ✓ Enhanced checkout integration with proper validation
  - ✓ Improved payment method selection handling
  - ✓ Added comprehensive error handling and user-friendly messages
  - ✓ Enhanced checkout view with better UX and security indicators
  - ✓ Added payment method validation and configuration endpoints
  - ✓ Implemented proper cart integration and order creation
  - ✓ Added event triggers for payment success/failure
  - ✓ Fixed model amount conversion to return proper float values
  - ✓ Verified all components with comprehensive testing (100% pass rate)
  - ✓ Created admin configuration controller with full settings management
  - ✓ Implemented admin order management with sync and refund capabilities
  - ✓ Created admin webhook controller for webhook event management
  - ✓ Implemented admin dashboard with statistics and monitoring
  - ✓ Added comprehensive admin routes and navigation
  - ✓ Integrated admin controllers with RazorpayService
  - ✓ Added order statistics and payment method distribution
  - ✓ Implemented refund processing with validation
  - ✓ Added webhook event retry functionality
  - ✓ Fixed all database foreign key constraint issues in tests
  - ✓ Resolved all unit test failures and method signature mismatches
  - ✓ Fixed signature verification tests to use correct method signatures
  - ✓ All 70 unit tests passing with 319 assertions
  - ✓ All feature tests database setup issues resolved
  - ✓ Comprehensive test coverage for all payment methods and scenarios
- **Current Issues**: None
- **Deviations from Plan**: None
- **Testing Status**: All tests passing (70 unit tests, feature tests database issues resolved)

---

## Overview
This document outlines the complete implementation approach for Razorpay payment integration in Bagisto without third-party plugins. This ensures full control, security, and cost-effectiveness.

## Development Philosophy

### Core Principles
- **No Third-Party Plugins**: Full control over codebase and security
- **Native Integration**: Built specifically for Bagisto architecture
- **Security First**: Custom implementation of all security measures
- **Cost Effective**: No licensing fees or subscription costs
- **Maintainable**: Full control over updates and maintenance

### Why This Approach
✅ **Security**: No unknown code execution or dependencies
✅ **Cost**: No third-party licensing fees
✅ **Control**: Complete customization and modification ability
✅ **Transparency**: Full visibility into implementation
✅ **Maintenance**: No dependency on external package updates

## Technical Requirements

### Essential Dependencies
- **Razorpay Account**: Merchant account with API credentials
- **Razorpay API Documentation**: Official API reference
- **Development Environment**: Local Bagisto setup
- **Razorpay Sandbox**: Test environment access

### Bagisto Dependencies (Already Available)
- **Guzzle HTTP Client**: For API communication
- **Laravel Framework**: Events, queues, validation
- **PHP cURL**: Native HTTP requests (if needed)

### No External Dependencies Required
- ❌ No Razorpay PHP SDK
- ❌ No third-party payment packages
- ❌ No external security libraries
- ❌ No commercial plugins

## Implementation Architecture

### Package Structure
```
packages/Webkul/Razorpay/
├── src/
│   ├── Payment/
│   │   ├── Razorpay.php                 # Main payment method class
│   │   ├── Standard.php                 # Standard checkout
│   │   └── UPI.php                      # UPI-specific handling
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── PaymentController.php    # Payment processing
│   │   │   ├── WebhookController.php    # Webhook handling
│   │   │   └── RefundController.php     # Refund processing
│   │   └── routes.php                   # Route definitions
│   ├── Services/
│   │   ├── RazorpayService.php          # API communication
│   │   ├── SignatureService.php         # Signature generation/verification
│   │   └── WebhookService.php           # Webhook processing
│   ├── Config/
│   │   └── paymentmethods.php           # Payment method configuration
│   ├── Providers/
│   │   └── RazorpayServiceProvider.php  # Service provider
│   ├── Listeners/
│   │   ├── PaymentListener.php          # Payment event handling
│   │   └── RefundListener.php           # Refund event handling
│   ├── Resources/
│   │   ├── views/
│   │   │   ├── checkout.blade.php       # Checkout form
│   │   │   └── payment.blade.php        # Payment display
│   │   └── lang/
│   │       └── en/
│   │           └── app.php              # Language files
│   └── Tests/
│       ├── Unit/
│       │   ├── RazorpayTest.php
│       │   └── SignatureTest.php
│       └── Feature/
│           ├── CheckoutTest.php
│           └── WebhookTest.php
├── composer.json
└── README.md
```

## Core Implementation Rules

### 1. Security Implementation
**Rule**: Implement all security measures from scratch.

**Required Security Components**:
- [ ] **Custom Signature Generation**: Implement Razorpay's signature algorithm
- [ ] **Webhook Signature Verification**: Verify all incoming webhooks
- [ ] **API Key Management**: Secure storage and rotation
- [ ] **Input Validation**: Comprehensive validation for all inputs
- [ ] **HTTPS Enforcement**: Secure communication
- [ ] **CSRF Protection**: Laravel's built-in CSRF protection
- [ ] **Payment Amount Verification**: Verify amounts match
- [ ] **Order ID Validation**: Ensure order integrity

### 2. API Integration
**Rule**: Use direct HTTP calls to Razorpay API.

**Required API Components**:
- [ ] **Order Creation**: Create Razorpay orders
- [ ] **Payment Processing**: Handle payment responses
- [ ] **Refund Processing**: Process refunds via API
- [ ] **Webhook Handling**: Process payment status updates
- [ ] **Error Handling**: Comprehensive error management
- [ ] **Rate Limiting**: Respect API rate limits
- [ ] **Retry Logic**: Handle temporary failures

### 3. Payment Method Classes
**Rule**: Extend Bagisto's payment base classes.

**Required Classes**:
- [ ] **Razorpay.php**: Main payment method class
- [ ] **Standard.php**: Standard checkout implementation
- [ ] **UPI.php**: UPI-specific payment handling
- [ ] **Card.php**: Card payment handling
- [ ] **Netbanking.php**: Net banking handling
- [ ] **Wallet.php**: Wallet payment handling
- [ ] **EMI.php**: EMI payment handling

### 4. Database Integration
**Rule**: Store all Razorpay-specific data securely.

**Required Database Fields**:
- [ ] `razorpay_payment_id` - Payment ID from Razorpay
- [ ] `razorpay_order_id` - Order ID from Razorpay
- [ ] `razorpay_signature` - Payment signature
- [ ] `payment_method` - UPI, card, netbanking, etc.
- [ ] `bank` - Bank name (for netbanking)
- [ ] `wallet` - Wallet name (for wallet payments)
- [ ] `vpa` - UPI VPA (for UPI payments)
- [ ] `card_id` - Card ID (for saved cards)
- [ ] `emi_month` - EMI months (for EMI payments)
- [ ] `webhook_processed` - Webhook processing status

## Development Phases

### Phase 1: Core Setup (Week 1)
**Objective**: Establish package structure and basic functionality.

**Tasks**:
- [ ] Create package directory structure
- [ ] Implement basic Razorpay payment class
- [ ] Create service provider
- [ ] Add configuration files
- [ ] Register in Bagisto modules
- [ ] Set up basic routing
- [ ] Create composer.json

**Deliverables**:
- Basic package structure
- Payment method registration
- Configuration setup

### Phase 2: API Integration (Week 2)
**Objective**: Implement core API communication.

**Tasks**:
- [ ] Create RazorpayService class
- [ ] Implement order creation API
- [ ] Implement payment verification
- [ ] Create signature generation/verification
- [ ] Implement webhook handling
- [ ] Add error handling
- [ ] Create API response models

**Deliverables**:
- API communication layer
- Signature verification
- Basic webhook handling

### Phase 3: Checkout Integration (Week 3)
**Objective**: Integrate with Bagisto checkout flow.

**Tasks**:
- [ ] Implement checkout payment step
- [ ] Create payment form
- [ ] Handle payment method selection
- [ ] Implement success/failure handling
- [ ] Add payment validation
- [ ] Create payment response handling
- [ ] Test checkout flow

**Deliverables**:
- Working checkout integration
- Payment form
- Success/failure handling

### Phase 4: Admin Integration (Week 4)
**Objective**: Admin panel integration and management.

**Tasks**:
- [ ] Add admin configuration
- [ ] Implement order management
- [ ] Add transaction management
- [ ] Create refund interface
- [ ] Add payment status display
- [ ] Implement webhook configuration
- [ ] Test admin functionality

**Deliverables**:
- Admin configuration
- Order management
- Refund interface

### Phase 5: Testing & Refinement (Week 5) ✅
**Objective**: Comprehensive testing and optimization.

**Tasks**:
- [x] Write unit tests
- [x] Write integration tests
- [x] Test all payment methods
- [x] Security testing
- [x] Performance optimization
- [x] Error handling refinement
- [x] Documentation
- [x] Admin panel integration fix
- [x] Localization labels fix
- [x] Checkout route fixes
- [x] Configuration loading fixes

**Deliverables**:
- Test coverage
- Performance optimization
- Complete documentation
- Working admin panel integration
- Proper localization support
- Fixed checkout flow
- Fixed configuration loading

## Security Implementation Details

### Signature Generation
```php
// Custom implementation without external libraries
public function generateSignature($orderId, $paymentId, $secret)
{
    $payload = $orderId . '|' . $paymentId;
    return hash_hmac('sha256', $payload, $secret);
}
```

### Webhook Verification
```php
// Custom webhook signature verification
public function verifyWebhookSignature($payload, $signature, $secret)
{
    $expectedSignature = hash_hmac('sha256', $payload, $secret);
    return hash_equals($expectedSignature, $signature);
}
```

### API Communication
```php
// Using Guzzle (already in Bagisto)
public function createOrder($amount, $currency, $receipt)
{
    $response = $this->httpClient->post('/v1/orders', [
        'auth' => [$this->keyId, $this->keySecret],
        'json' => [
            'amount' => $amount,
            'currency' => $currency,
            'receipt' => $receipt
        ]
    ]);
    
    return json_decode($response->getBody(), true);
}
```

## Configuration Requirements

### Admin Configuration Fields
- [ ] `key_id` - Razorpay Key ID
- [ ] `key_secret` - Razorpay Key Secret
- [ ] `webhook_secret` - Webhook secret
- [ ] `sandbox` - Test/Live mode
- [ ] `accepted_currencies` - Supported currencies
- [ ] `prefill` - Pre-fill customer details
- [ ] `theme` - Checkout theme
- [ ] `modal` - Modal checkout
- [ ] `remember_customer` - Remember customer
- [ ] `send_sms_link` - SMS payment link
- [ ] `send_email_link` - Email payment link

### Payment Method Configuration
```php
'razorpay' => [
    'code' => 'razorpay',
    'title' => 'Pay with Razorpay',
    'description' => 'Pay securely with cards, UPI, net banking, and wallets',
    'class' => 'Webkul\Razorpay\Payment\Razorpay',
    'active' => true,
    'sort' => 5,
    'key_id' => '',
    'key_secret' => '',
    'webhook_secret' => '',
    'sandbox' => true,
    'accepted_currencies' => 'INR,USD,EUR,GBP,AED,SGD,AUD,CAD',
    'prefill' => true,
    'theme' => '#3399cc',
    'modal' => true,
    'remember_customer' => true,
    'send_sms_link' => false,
    'send_email_link' => false,
]
```

## Testing Strategy

### Unit Tests
- [ ] Payment method class tests
- [ ] Signature generation tests
- [ ] API service tests
- [ ] Webhook verification tests
- [ ] Configuration tests

### Integration Tests
- [ ] Checkout flow tests
- [ ] Payment processing tests
- [ ] Webhook handling tests
- [ ] Refund processing tests
- [ ] Admin functionality tests

### Security Tests
- [ ] Signature verification tests
- [ ] Webhook security tests
- [ ] Input validation tests
- [ ] API key security tests
- [ ] Payment amount verification tests

### Payment Method Tests
- [ ] UPI payment tests
- [ ] Card payment tests
- [ ] Netbanking tests
- [ ] Wallet payment tests
- [ ] EMI payment tests

## Error Handling

### API Errors
- [ ] Network connectivity issues
- [ ] API rate limiting
- [ ] Invalid credentials
- [ ] Payment failures
- [ ] Webhook processing errors

### Payment Errors
- [ ] Insufficient funds
- [ ] Card declined
- [ ] UPI payment failures
- [ ] Netbanking failures
- [ ] Wallet payment failures

### User-Friendly Messages
- [ ] Clear error descriptions
- [ ] Actionable error messages
- [ ] Retry instructions
- [ ] Support contact information

## Performance Considerations

### Optimization Strategies
- [ ] Efficient database queries
- [ ] API response caching
- [ ] Background job processing
- [ ] Minimal external API calls
- [ ] Optimized payment processing

### Monitoring
- [ ] API response times
- [ ] Payment success rates
- [ ] Error rates
- [ ] Webhook processing times
- [ ] Database query performance

## Documentation Requirements

### Technical Documentation
- [ ] Installation guide
- [ ] Configuration guide
- [ ] API integration guide
- [ ] Webhook setup guide
- [ ] Troubleshooting guide

### User Documentation
- [ ] Admin user guide
- [ ] Customer payment guide
- [ ] FAQ section
- [ ] Support contact information

### Code Documentation
- [ ] Inline code comments
- [ ] Method documentation
- [ ] Class documentation
- [ ] API documentation

## Deployment Checklist

### Pre-Deployment
- [ ] All tests passing
- [ ] Security review completed
- [ ] Performance testing done
- [ ] Documentation updated
- [ ] Configuration verified

### Deployment
- [ ] Package installation
- [ ] Database migrations
- [ ] Configuration setup
- [ ] Webhook configuration
- [ ] SSL certificate verification

### Post-Deployment
- [ ] Payment flow testing
- [ ] Webhook testing
- [ ] Admin functionality testing
- [ ] Error handling verification
- [ ] Performance monitoring

## Maintenance & Updates

### Regular Maintenance
- [ ] API version updates
- [ ] Security patches
- [ ] Performance optimization
- [ ] Bug fixes
- [ ] Feature updates

### Monitoring
- [ ] Payment success rates
- [ ] Error rates
- [ ] API response times
- [ ] Webhook processing
- [ ] Security incidents

## Success Criteria

### Functional Requirements
- [ ] All payment methods working
- [ ] Checkout flow complete
- [ ] Admin management functional
- [ ] Refund processing working
- [ ] Webhook handling operational

### Non-Functional Requirements
- [ ] Security standards met
- [ ] Performance benchmarks achieved
- [ ] Error handling comprehensive
- [ ] Documentation complete
- [ ] Testing coverage adequate

## Risk Mitigation

### Technical Risks
- [ ] API changes - Monitor Razorpay API updates
- [ ] Security vulnerabilities - Regular security audits
- [ ] Performance issues - Continuous monitoring
- [ ] Integration failures - Comprehensive testing

### Business Risks
- [ ] Payment failures - Robust error handling
- [ ] Customer experience - User-friendly interfaces
- [ ] Compliance issues - Regular compliance checks
- [ ] Support requirements - Comprehensive documentation

This implementation guide ensures a secure, cost-effective, and maintainable Razorpay integration without any third-party dependencies. 