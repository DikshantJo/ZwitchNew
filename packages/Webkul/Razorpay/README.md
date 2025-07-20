# Razorpay Payment Integration for Bagisto

A comprehensive Razorpay payment gateway integration for Bagisto e-commerce platform. This package provides secure, reliable, and feature-rich payment processing with support for multiple payment methods including UPI, cards, netbanking, wallets, and EMI.

## Features

- ✅ **Multiple Payment Methods**: UPI, Credit/Debit Cards, Net Banking, Wallets, EMI
- ✅ **Multi-Currency Support**: INR, USD, EUR, GBP, AED, SGD, AUD, CAD
- ✅ **Secure Integration**: Custom signature verification, webhook security
- ✅ **Comprehensive Testing**: Unit tests, integration tests, security tests
- ✅ **Admin Panel Integration**: Full admin management interface
- ✅ **Error Handling**: Robust error handling with user-friendly messages
- ✅ **Performance Optimized**: Caching, database optimization, efficient queries
- ✅ **Webhook Support**: Real-time payment status updates
- ✅ **Refund Processing**: Full and partial refund support
- ✅ **Guest Checkout**: Support for guest customers
- ✅ **Mobile Responsive**: Optimized for mobile devices

## Requirements

- PHP 8.0 or higher
- Laravel 9.x or higher
- Bagisto 1.x
- Razorpay merchant account
- SSL certificate (for production)

## Installation

### 1. Install the Package

```bash
composer require webkul/razorpay
```

### 2. Publish Configuration

```bash
php artisan vendor:publish --provider="Webkul\Razorpay\Providers\RazorpayServiceProvider"
```

### 3. Run Migrations

```bash
php artisan migrate
```

### 4. Register in Bagisto

Add the following to your `config/concord.php` file:

```php
'modules' => [
    // ... other modules
    'Webkul\Razorpay\Providers\RazorpayServiceProvider',
],
```

## Configuration

### 1. Razorpay Account Setup

1. Create a Razorpay merchant account at [razorpay.com](https://razorpay.com)
2. Get your API credentials from the Razorpay Dashboard
3. Configure webhook endpoints

### 2. Admin Configuration

Navigate to **Admin Panel > Configuration > Sales > Payment Methods > Razorpay** and configure:

#### Basic Settings
- **Title**: Payment method title (e.g., "Pay with Razorpay")
- **Description**: Payment method description
- **Active**: Enable/disable the payment method
- **Sort Order**: Display order in checkout

#### API Credentials
- **Key ID**: Your Razorpay Key ID
- **Key Secret**: Your Razorpay Key Secret
- **Webhook Secret**: Your webhook secret for verification

#### Environment Settings
- **Sandbox Mode**: Enable for testing, disable for production
- **Accepted Currencies**: Supported currencies (INR, USD, EUR, etc.)

#### Checkout Settings
- **Prefill Customer Details**: Auto-fill customer information
- **Theme Color**: Customize checkout theme
- **Modal Checkout**: Enable modal checkout experience
- **Remember Customer**: Remember customer for future payments

### 3. Webhook Configuration

Configure the following webhook URL in your Razorpay Dashboard:

```
https://yourdomain.com/razorpay/webhook
```

**Required Events:**
- `payment.captured`
- `payment.failed`
- `refund.processed`
- `refund.failed`

## Usage

### 1. Customer Checkout

The Razorpay payment method will automatically appear in the checkout process for supported currencies and amounts.

#### Supported Payment Methods

**UPI Payments:**
- UPI ID (VPA) payments
- QR code scanning
- UPI apps integration

**Card Payments:**
- Credit cards
- Debit cards
- International cards
- Saved cards

**Net Banking:**
- All major Indian banks
- Secure bank authentication

**Digital Wallets:**
- Paytm
- PhonePe
- Amazon Pay
- Other popular wallets

**EMI Options:**
- No-cost EMI
- Standard EMI
- Card-based EMI

### 2. Admin Management

#### Order Management
- View Razorpay payment details
- Process refunds
- Sync payment status
- Monitor transactions

#### Configuration Management
- Update API credentials
- Configure webhook settings
- Manage payment methods
- Set up sandbox/live modes

#### Transaction Monitoring
- Real-time transaction status
- Payment success/failure rates
- Error monitoring
- Performance metrics

## API Reference

### Payment Processing

#### Create Order
```php
use Webkul\Razorpay\Services\RazorpayService;

$razorpayService = new RazorpayService();
$order = $razorpayService->createOrder(1000.00, 'INR', 'order_123');
```

#### Process Payment
```php
$payment = $razorpayService->fetchPayment('pay_123456789');
```

#### Verify Payment
```php
$isValid = $razorpayService->verifySignature($orderId, $paymentId, $signature, $secret);
```

### Refund Processing

#### Process Refund
```php
$refund = $razorpayService->processRefund('pay_123456789', 500.00, 'Partial refund');
```

#### Fetch Refund
```php
$refund = $razorpayService->fetchRefund('rfnd_123456789');
```

### Webhook Handling

#### Verify Webhook
```php
$isValid = $razorpayService->verifyWebhookSignature($payload, $signature, $secret);
```

## Testing

### Running Tests

```bash
# Run all tests
php artisan test --filter=Razorpay

# Run specific test suites
php artisan test packages/Webkul/Razorpay/tests/Unit
php artisan test packages/Webkul/Razorpay/tests/Feature
```

### Test Coverage

The package includes comprehensive tests for:

- **Unit Tests**: Payment methods, services, signature verification
- **Integration Tests**: Checkout flow, webhook handling, admin integration
- **Security Tests**: Signature verification, input validation, CSRF protection
- **Performance Tests**: Database optimization, caching, response times
- **Error Handling Tests**: Network errors, API errors, validation errors

### Sandbox Testing

Use Razorpay's sandbox environment for testing:

1. Enable sandbox mode in admin configuration
2. Use test API credentials
3. Test with sandbox payment methods

**Test Cards:**
- Success: `4111 1111 1111 1111`
- Failure: `4000 0000 0000 0002`

**Test UPI:**
- Success: `success@razorpay`
- Failure: `failure@razorpay`

## Security

### Signature Verification

All payments are verified using Razorpay's signature verification:

```php
$signature = $orderId . '|' . $paymentId;
$expectedSignature = hash_hmac('sha256', $signature, $secret);
```

### Webhook Security

Webhook signatures are verified to ensure authenticity:

```php
$expectedSignature = hash_hmac('sha256', $payload, $webhookSecret);
```

### Input Validation

All inputs are validated and sanitized to prevent:
- SQL injection
- XSS attacks
- CSRF attacks
- Parameter pollution

## Troubleshooting

### Common Issues

#### 1. Payment Method Not Showing
- Check if payment method is active
- Verify currency support
- Ensure minimum amount requirements
- Check API credentials

#### 2. Payment Failures
- Verify signature verification
- Check webhook configuration
- Review error logs
- Validate payment data

#### 3. Webhook Issues
- Verify webhook URL
- Check webhook secret
- Ensure HTTPS is enabled
- Review webhook logs

#### 4. API Errors
- Verify API credentials
- Check sandbox/live mode
- Review rate limits
- Validate request format

### Error Codes

| Error Code | Description | Solution |
|------------|-------------|----------|
| `CARD_DECLINED` | Card was declined | Try different card |
| `INSUFFICIENT_FUNDS` | Insufficient balance | Use different payment method |
| `UPI_PAYMENT_FAILED` | UPI payment failed | Check VPA and try again |
| `NETBANKING_FAILED` | Netbanking failed | Try again or use different method |
| `WALLET_INSUFFICIENT_BALANCE` | Low wallet balance | Use different payment method |
| `EMI_NOT_AVAILABLE` | EMI not available | Use different payment method |

### Debug Mode

Enable debug mode for detailed error information:

```php
// In config/paymentmethods.php
'razorpay' => [
    'debug' => true,
    // ... other settings
],
```

## Performance Optimization

### Caching

The package implements caching for:
- Configuration settings
- Currency lists
- Payment method availability
- API responses

### Database Optimization

- Optimized queries with proper indexes
- Efficient relationship loading
- Minimal database calls
- Connection pooling

### Response Times

Target response times:
- Payment processing: < 500ms
- Webhook handling: < 200ms
- API responses: < 100ms
- Database queries: < 50ms

## Support

### Documentation
- [Razorpay API Documentation](https://razorpay.com/docs/)
- [Bagisto Documentation](https://bagisto.com/docs/)
- [Package Documentation](https://github.com/webkul/razorpay-bagisto)

### Community Support
- [Bagisto Community](https://bagisto.com/community/)
- [GitHub Issues](https://github.com/webkul/razorpay-bagisto/issues)
- [Stack Overflow](https://stackoverflow.com/questions/tagged/bagisto)

### Professional Support
- [Webkul Support](https://webkul.com/support/)
- [Razorpay Support](https://razorpay.com/support/)

## Contributing

We welcome contributions! Please see our [Contributing Guide](CONTRIBUTING.md) for details.

### Development Setup

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Add tests
5. Submit a pull request

### Code Standards

- Follow PSR-12 coding standards
- Add comprehensive tests
- Update documentation
- Follow security best practices

## License

This package is licensed under the [MIT License](LICENSE).

## Changelog

### Version 1.0.0
- Initial release
- Basic payment processing
- UPI, card, netbanking support
- Admin integration
- Comprehensive testing

### Version 1.1.0
- Added wallet and EMI support
- Enhanced error handling
- Performance optimizations
- Security improvements

### Version 1.2.0
- Multi-currency support
- Advanced webhook handling
- Refund processing
- Mobile optimization

## Credits

- [Razorpay](https://razorpay.com) - Payment gateway
- [Bagisto](https://bagisto.com) - E-commerce platform
- [Webkul](https://webkul.com) - Package development

---

**Note**: This package is designed for Bagisto e-commerce platform. For other platforms, please refer to the appropriate integration guide. 