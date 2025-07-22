# Bagisto Razorpay Payment Integration Implementation Rules

## Progress Tracking
**Overall Progress**: 12% Complete
**Last Updated**: 2024-12-17
**Current Phase**: Phase 1 Complete

### Phase Progress
- [x] **Phase 1: Core Setup** (8/8 tasks) - 100%
- [ ] **Phase 2: API Integration** (0/12 tasks) - 0%
- [ ] **Phase 3: Checkout Integration** (0/10 tasks) - 0%
- [ ] **Phase 4: Admin Integration** (0/8 tasks) - 0%
- [ ] **Phase 5: Testing & Refinement** (0/8 tasks) - 0%

### Quick Status
- **Package Structure**: ✓ Complete
- **API Integration**: Not Started
- **Checkout Flow**: Not Started
- **Admin Panel**: Not Started
- **Testing**: Not Started
- **Documentation**: Not Started

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
- **Current Issues**: None
- **Deviations from Plan**: None

---

## Overview
This document outlines the comprehensive rules and checklist for implementing Razorpay payment integration in Bagisto. Follow these rules strictly to ensure all components are properly integrated and no functionality is missed.

## Razorpay-Specific Requirements

### Razorpay Gateway Features
- **Payment Methods**: Credit/Debit Cards, UPI, Net Banking, Wallets, EMI
- **Currencies**: INR (Primary), USD, EUR, GBP, AED, SGD, AUD, CAD
- **Webhook Support**: Real-time payment status updates
- **Refund Support**: Full and partial refunds
- **Subscription Support**: Recurring payments
- **International Payments**: Cross-border transactions
- **UPI Support**: Native UPI integration for Indian market

## Core Implementation Rules

### 1. Razorpay Package Structure
**Rule**: Razorpay payment method must follow the established package structure.

**Required Components**:
- [ ] Create `packages/Webkul/Razorpay/` package directory
- [ ] Create payment method class extending `Webkul\Payment\Payment\Payment`
- [ ] Implement abstract method `getRedirectUrl()` for Razorpay checkout
- [ ] Define payment method code as protected property `$code = 'razorpay'`
- [ ] Implement `isAvailable()` method with currency validation (INR primary)
- [ ] Add Razorpay to `packages/Webkul/Payment/src/Config/paymentmethods.php`
- [ ] Create `Webkul\Razorpay\Providers\RazorpayServiceProvider`
- [ ] Register in `config/concord.php` modules array
- [ ] Create Razorpay-specific payment classes for different payment methods

### 2. Razorpay Configuration Management
**Rule**: Razorpay payment method must have comprehensive configuration options.

**Required Configuration Fields**:
- [ ] `title` - "Razorpay" or "Pay with Razorpay"
- [ ] `description` - "Pay securely with cards, UPI, net banking, and wallets"
- [ ] `active` - Enable/disable toggle
- [ ] `sort` - Display order (recommend 5)
- [ ] `image` - Razorpay logo (55px X 45px)
- [ ] `instructions` - Customer instructions for payment
- [ ] `generate_invoice` - Auto-invoice generation toggle
- [ ] `invoice_status` - Invoice status when auto-generated
- [ ] `order_status` - Order status when auto-generated

**Razorpay-Specific Fields**:
- [ ] `key_id` - Razorpay Key ID (required)
- [ ] `key_secret` - Razorpay Key Secret (required)
- [ ] `webhook_secret` - Webhook secret for verification
- [ ] `sandbox` - Test/Live mode toggle
- [ ] `accepted_currencies` - Supported currencies (INR, USD, etc.)
- [ ] `prefill` - Pre-fill customer details (name, email, contact)
- [ ] `theme` - Checkout theme color
- [ ] `modal` - Modal checkout option
- [ ] `remember_customer` - Remember customer for future payments
- [ ] `send_sms_link` - Send payment link via SMS
- [ ] `send_email_link` - Send payment link via email

### 3. Razorpay Database Integration
**Rule**: Razorpay payment method must properly integrate with all database tables.

**Required Database Integration**:
- [ ] `cart_payment` table - Cart payment information
- [ ] `order_payment` table - Order payment information
- [ ] `order_transactions` table - Payment transactions
- [ ] `refunds` table - Refund processing
- [ ] Ensure payment method code is stored as `'razorpay'` in `method` field
- [ ] Store Razorpay-specific data in `additional` JSON field:
  - [ ] `razorpay_payment_id` - Razorpay payment ID
  - [ ] `razorpay_order_id` - Razorpay order ID
  - [ ] `razorpay_signature` - Payment signature for verification
  - [ ] `payment_method` - UPI, card, netbanking, wallet, etc.
  - [ ] `bank` - Bank name (for netbanking)
  - [ ] `wallet` - Wallet name (for wallet payments)
  - [ ] `vpa` - UPI VPA (for UPI payments)
  - [ ] `card_id` - Card ID (for saved cards)
  - [ ] `emi_month` - EMI months (for EMI payments)

### 4. Razorpay Checkout Integration
**Rule**: Razorpay payment method must integrate seamlessly with checkout flow.

**Required Checkout Components**:
- [ ] Payment method appears in checkout payment step
- [ ] Payment method validation during checkout
- [ ] Payment method selection saves to cart
- [ ] Razorpay checkout modal/redirect handling
- [ ] Payment method availability based on cart contents
- [ ] Payment method availability based on shipping method
- [ ] Payment method availability based on customer type (guest/registered)
- [ ] Currency validation (INR primary, other supported currencies)
- [ ] Razorpay checkout form integration
- [ ] Pre-fill customer details (name, email, contact)
- [ ] Handle Razorpay payment response
- [ ] Verify payment signature
- [ ] Handle payment failures and retries
- [ ] Support for UPI, cards, netbanking, wallets, EMI
- [ ] Remember customer option for future payments

### 5. Razorpay Order Processing
**Rule**: Razorpay payment method must handle all order lifecycle events.

**Required Order Integration**:
- [ ] Order creation with payment method
- [ ] Payment method stored in order_payment table
- [ ] Order status updates based on payment
- [ ] Invoice generation (if applicable)
- [ ] Transaction recording
- [ ] Order validation before creation
- [ ] Create Razorpay order before payment
- [ ] Handle Razorpay payment confirmation
- [ ] Verify payment signature for security
- [ ] Update order status based on payment status
- [ ] Handle failed payments and order cancellation
- [ ] Store Razorpay payment details in order
- [ ] Support for partial payments (if enabled)
- [ ] Handle payment method-specific details (UPI, card, etc.)

### 6. Invoice System
**Rule**: Payment method must integrate with invoice generation and management.

**Required Invoice Integration**:
- [ ] Auto-invoice generation configuration
- [ ] Invoice status management
- [ ] Invoice email notifications
- [ ] Invoice PDF generation with payment method
- [ ] Invoice payment method display
- [ ] Invoice transaction recording

### 7. Razorpay Refund System
**Rule**: Razorpay payment method must support refund processing.

**Required Refund Integration**:
- [ ] Refund capability validation
- [ ] Razorpay refund API integration
- [ ] Refund transaction recording
- [ ] Refund email notifications
- [ ] Refund status management
- [ ] Handle full and partial refunds
- [ ] Store Razorpay refund ID
- [ ] Handle refund failures
- [ ] Support for refund speed (normal/instant)
- [ ] Handle refund notes and reasons
- [ ] Update order status after refund
- [ ] Handle refund webhooks

### 8. Razorpay Admin Panel Integration
**Rule**: Razorpay payment method must be fully manageable from admin panel.

**Required Admin Components**:
- [ ] Payment method configuration in admin settings
- [ ] Payment method display in order views
- [ ] Payment method display in invoice views
- [ ] Payment method display in transaction views
- [ ] Payment method selection in admin order creation
- [ ] Payment method validation in admin
- [ ] Payment method status management
- [ ] Razorpay API credentials management
- [ ] Webhook configuration and testing
- [ ] Sandbox/Live mode toggle
- [ ] Currency configuration for Razorpay
- [ ] Payment method-specific details display (UPI, card, etc.)
- [ ] Razorpay payment ID display
- [ ] Refund management interface
- [ ] Payment status synchronization

### 9. Email System Integration
**Rule**: Payment method must appear in all relevant email templates.

**Required Email Integration**:
- [ ] Order confirmation emails (Shop & Admin)
- [ ] Invoice emails (Shop & Admin)
- [ ] Refund emails (Shop & Admin)
- [ ] Order canceled emails (Shop & Admin)
- [ ] Shipment emails (Shop & Admin)
- [ ] Payment method title display
- [ ] Payment method additional details display

### 10. Frontend Display
**Rule**: Payment method must display correctly in all customer-facing areas.

**Required Frontend Components**:
- [ ] Checkout payment step
- [ ] Customer order history
- [ ] Customer order details
- [ ] Customer invoice PDFs
- [ ] Payment method logos and descriptions
- [ ] Payment method instructions
- [ ] Mobile-responsive design

### 11. Event System Integration
**Rule**: Payment method must properly handle all relevant events.

**Required Event Listeners**:
- [ ] `checkout.order.save.after` - Order creation
- [ ] `sales.invoice.save.after` - Invoice creation
- [ ] `sales.refund.save.after` - Refund creation
- [ ] `sales.order.update-status.after` - Order status updates
- [ ] `sales.order.cancel.after` - Order cancellation

### 12. Razorpay API Integration
**Rule**: Razorpay payment method must work with API endpoints.

**Required API Integration**:
- [ ] Payment method listing API
- [ ] Payment method selection API
- [ ] Payment method validation API
- [ ] Order creation API
- [ ] Refund processing API
- [ ] Razorpay order creation API
- [ ] Razorpay payment verification API
- [ ] Razorpay refund API
- [ ] Webhook handling API
- [ ] Payment status check API
- [ ] Customer management API (for saved cards)
- [ ] UPI QR code generation API
- [ ] Payment link generation API

### 13. Razorpay Security & Validation
**Rule**: Razorpay payment method must implement proper security measures.

**Required Security Components**:
- [ ] Input validation and sanitization
- [ ] CSRF protection
- [ ] Payment method availability validation
- [ ] Amount validation
- [ ] Currency validation
- [ ] Razorpay signature verification
- [ ] Webhook signature verification
- [ ] API key validation
- [ ] Payment amount verification
- [ ] Order ID validation
- [ ] Payment status verification
- [ ] Secure storage of API credentials
- [ ] HTTPS enforcement for payment pages
- [ ] PCI DSS compliance considerations

### 14. Razorpay Error Handling
**Rule**: Razorpay payment method must handle errors gracefully.

**Required Error Handling**:
- [ ] Razorpay API errors
- [ ] Network connectivity issues
- [ ] Invalid payment data
- [ ] Insufficient funds
- [ ] Gateway timeout errors
- [ ] User-friendly error messages
- [ ] Error logging
- [ ] Payment failure handling
- [ ] Signature verification failures
- [ ] Webhook processing errors
- [ ] Refund processing errors
- [ ] Currency conversion errors
- [ ] UPI payment failures
- [ ] Card payment failures
- [ ] Netbanking failures
- [ ] Wallet payment failures
- [ ] EMI payment failures

### 15. Razorpay Testing Requirements
**Rule**: Razorpay payment method must have comprehensive test coverage.

**Required Testing**:
- [ ] Unit tests for Razorpay payment method class
- [ ] Integration tests for checkout flow
- [ ] API endpoint tests
- [ ] Admin panel tests
- [ ] Email template tests
- [ ] Error handling tests
- [ ] Razorpay API integration tests
- [ ] Signature verification tests
- [ ] Webhook handling tests
- [ ] Refund processing tests
- [ ] Currency conversion tests
- [ ] UPI payment tests
- [ ] Card payment tests
- [ ] Netbanking tests
- [ ] Wallet payment tests
- [ ] EMI payment tests
- [ ] Sandbox environment tests
- [ ] Live environment tests
- [ ] Payment failure scenario tests
- [ ] Webhook signature verification tests

### 16. Razorpay Documentation
**Rule**: Razorpay payment method must be properly documented.

**Required Documentation**:
- [ ] Installation instructions
- [ ] Configuration guide
- [ ] API documentation
- [ ] Troubleshooting guide
- [ ] Razorpay-specific documentation
- [ ] Code comments and inline documentation
- [ ] Razorpay account setup guide
- [ ] API credentials configuration
- [ ] Webhook setup instructions
- [ ] Sandbox testing guide
- [ ] Live environment setup
- [ ] Payment method-specific guides (UPI, cards, etc.)
- [ ] Refund processing guide
- [ ] Error code reference
- [ ] Security best practices
- [ ] PCI DSS compliance guide

### 17. Localization
**Rule**: Payment method must support multiple languages.

**Required Localization**:
- [ ] Payment method title translations
- [ ] Payment method description translations
- [ ] Error message translations
- [ ] Admin panel translations
- [ ] Email template translations

### 18. Performance Considerations
**Rule**: Payment method must not impact system performance.

**Required Performance Measures**:
- [ ] Efficient database queries
- [ ] Proper caching implementation
- [ ] Minimal external API calls
- [ ] Optimized payment processing
- [ ] Background job processing for heavy operations

### 19. Razorpay Compatibility
**Rule**: Razorpay payment method must be compatible with existing Bagisto features.

**Required Compatibility Checks**:
- [ ] Multi-currency support (INR, USD, EUR, GBP, AED, SGD, AUD, CAD)
- [ ] Multi-location support
- [ ] Guest checkout support
- [ ] Customer group support
- [ ] Tax calculation compatibility
- [ ] Discount compatibility
- [ ] Shipping method compatibility
- [ ] Indian market specific features (UPI, GST, etc.)
- [ ] International payment support
- [ ] Mobile payment compatibility
- [ ] UPI QR code compatibility
- [ ] EMI payment compatibility
- [ ] Saved card functionality
- [ ] Payment link compatibility

### 20. Maintenance & Updates
**Rule**: Payment method must be maintainable and updatable.

**Required Maintenance Features**:
- [ ] Version compatibility
- [ ] Upgrade procedures
- [ ] Configuration migration
- [ ] Data migration scripts
- [ ] Rollback procedures

## Razorpay Implementation Checklist

### Phase 1: Core Setup
- [ ] Create `packages/Webkul/Razorpay/` package structure
- [ ] Implement Razorpay payment method class
- [ ] Add Razorpay configuration files
- [ ] Register in system modules
- [ ] Install Razorpay PHP SDK
- [ ] Set up Razorpay API credentials

### Phase 2: Database & Models
- [ ] Ensure database table compatibility
- [ ] Implement model relationships
- [ ] Add migration files if needed
- [ ] Test database operations
- [ ] Set up Razorpay-specific data storage
- [ ] Configure JSON fields for payment details

### Phase 3: Razorpay Checkout Integration
- [ ] Implement Razorpay checkout payment step
- [ ] Add Razorpay payment method validation
- [ ] Test checkout flow
- [ ] Implement Razorpay modal/redirect handling
- [ ] Set up Razorpay checkout form
- [ ] Configure payment method options (UPI, cards, etc.)
- [ ] Test payment signature verification

### Phase 4: Razorpay Order Processing
- [ ] Implement Razorpay order creation
- [ ] Add invoice generation
- [ ] Implement transaction recording
- [ ] Test order lifecycle
- [ ] Set up Razorpay payment confirmation
- [ ] Configure webhook handling
- [ ] Test payment status updates

### Phase 5: Razorpay Admin Integration
- [ ] Add Razorpay admin configuration
- [ ] Implement admin order management
- [ ] Add admin transaction management
- [ ] Test admin functionality
- [ ] Configure Razorpay API credentials in admin
- [ ] Set up webhook configuration
- [ ] Add refund management interface

### Phase 6: Razorpay Email & Notifications
- [ ] Update email templates with Razorpay details
- [ ] Implement email notifications
- [ ] Test email delivery
- [ ] Verify email content
- [ ] Add Razorpay payment method display in emails
- [ ] Include payment method-specific details (UPI, card, etc.)

### Phase 7: Razorpay Testing & Validation
- [ ] Write Razorpay unit tests
- [ ] Write Razorpay integration tests
- [ ] Perform security testing
- [ ] Test error scenarios
- [ ] Test Razorpay API integration
- [ ] Test signature verification
- [ ] Test webhook handling
- [ ] Test refund processing
- [ ] Test different payment methods (UPI, cards, etc.)
- [ ] Test sandbox environment
- [ ] Test live environment

### Phase 8: Razorpay Documentation & Deployment
- [ ] Write Razorpay documentation
- [ ] Create Razorpay installation guide
- [ ] Prepare deployment package
- [ ] Perform final testing
- [ ] Create Razorpay account setup guide
- [ ] Document API credentials configuration
- [ ] Create webhook setup guide
- [ ] Document testing procedures
- [ ] Create troubleshooting guide

## Quality Assurance Rules

### Code Quality
- [ ] Follow PSR-12 coding standards
- [ ] Use proper type hints
- [ ] Implement proper error handling
- [ ] Add comprehensive comments
- [ ] Use meaningful variable names

### Security
- [ ] Validate all inputs
- [ ] Sanitize all outputs
- [ ] Implement proper authentication
- [ ] Use secure communication protocols
- [ ] Follow OWASP guidelines

### Performance
- [ ] Optimize database queries
- [ ] Implement proper caching
- [ ] Minimize external API calls
- [ ] Use background jobs for heavy operations
- [ ] Monitor performance metrics

### Maintainability
- [ ] Write clean, readable code
- [ ] Use design patterns appropriately
- [ ] Implement proper separation of concerns
- [ ] Create reusable components
- [ ] Document complex logic

## Final Validation Checklist

Before marking a payment integration as complete, verify:

- [ ] All required components are implemented
- [ ] All tests pass
- [ ] All configurations work correctly
- [ ] All email templates display properly
- [ ] All admin functions work as expected
- [ ] All frontend displays are correct
- [ ] All error scenarios are handled
- [ ] All security measures are in place
- [ ] All documentation is complete
- [ ] All localization is implemented
- [ ] Performance is acceptable
- [ ] Code quality meets standards

## Razorpay-Specific Notes

- Always test in Razorpay sandbox environment first
- Follow the existing Bagisto patterns and conventions
- Ensure backward compatibility when possible
- Keep security as the top priority (especially signature verification)
- Document any deviations from standard patterns
- Consider the impact on existing functionality
- Plan for future maintenance and updates
- Ensure PCI DSS compliance for card payments
- Test with Indian market specific features (UPI, GST, etc.)
- Verify webhook signature for security
- Handle currency conversion properly
- Test with multiple payment methods (UPI, cards, netbanking, wallets, EMI)
- Ensure mobile responsiveness for UPI payments
- Test payment link functionality
- Verify refund processing with different payment methods

## Razorpay Account Requirements

- Razorpay merchant account
- API credentials (Key ID and Key Secret)
- Webhook endpoint configuration
- Sandbox and Live environment access
- Proper business verification
- Bank account integration
- GST registration (for Indian businesses)

This rule guide should be referenced throughout the Razorpay payment integration implementation process to ensure no components are missed and all requirements are met. 