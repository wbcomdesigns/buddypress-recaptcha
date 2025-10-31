# Easy Digital Downloads Integration

Protect your digital store from spam, fake accounts, and fraudulent checkout attempts with CAPTCHA on EDD forms.

## 📋 Overview

Protects Easy Digital Downloads forms:
- **Checkout Form** - Prevent fake purchases
- **Registration Form** - Stop spam accounts
- **Login Form** - Secure customer accounts

---

## 🛡️ Why Protect EDD

**Without CAPTCHA:**
- Fake purchase attempts
- Spam customer accounts
- Bot checkout submissions
- Payment gateway testing abuse
- Database bloat from fake accounts

**With CAPTCHA:**
- Only genuine customers
- Protected payment processing
- Clean customer database
- Reduced fraud attempts

**Recommendation:** ✅ Always enable for checkout and registration

---

## ⚙️ Quick Setup

### Step 1: Prerequisites

1. **Install Easy Digital Downloads**
   - Free from [WordPress.org](https://wordpress.org/plugins/easy-digital-downloads/)

2. **Configure CAPTCHA**
   - Choose service and get API keys
   - See [CAPTCHA guides](../captcha-services/README.md)

---

### Step 2: Enable Protection

1. **Settings → Wbcom CAPTCHA Manager**
2. Configure CAPTCHA service
3. Scroll to **"Easy Digital Downloads"** section
4. Check forms to protect:

```
☑ Checkout Form ← Essential
☑ Registration Form
☑ Login Form
```

5. **Save Changes**

---

### Step 3: Test Your Store

**Test Checkout:**
1. Add product to cart
2. Go to checkout
3. Verify CAPTCHA appears
4. Test purchase:
   - Without CAPTCHA: Should fail
   - With CAPTCHA: Should complete

**Test Registration:**
1. Go to EDD account page
2. Try registering new account
3. Verify CAPTCHA required

**Test Login:**
1. Log out
2. Try logging in
3. Verify CAPTCHA appears

---

## 🎨 Customization

### Skip CAPTCHA for Logged-In Customers

```php
// Skip checkout CAPTCHA if customer logged in
add_filter( 'wbc_edd_checkout_skip_logged_in', '__return_true' );
```

**Recommended:** Improves UX for returning customers.

---

### Custom Error Messages

```php
// Checkout error
add_filter( 'wbc_edd_checkout_error_message', function( $message ) {
    return 'Please verify you are human to complete your purchase.';
});

// Registration error
add_filter( 'wbc_edd_register_error_message', function( $message ) {
    return 'Please complete the security check to create your account.';
});
```

---

### CAPTCHA Position on Checkout

```php
add_filter( 'wbc_edd_checkout_captcha_position', function() {
    return 'before_payment'; // or 'after_customer_info'
});
```

---

## 🔧 Troubleshooting

### CAPTCHA Not Appearing on Checkout

**Check:**
1. EDD is active and configured
2. Checkout page has `[download_checkout]` shortcode
3. Plugin setting "Checkout Form" is checked
4. CAPTCHA service configured correctly
5. Clear all caches

---

### Checkout Fails with Valid CAPTCHA

**Solutions:**
1. Verify API keys are correct
2. Check domain registered in CAPTCHA service
3. Test with different payment gateway
4. Check for JavaScript errors (F12 console)
5. Verify server can reach CAPTCHA API

---

### Payment Gateway Conflicts

**Most gateways compatible:**
- PayPal Standard ✅
- Stripe ✅
- Manual Payment ✅

**If issues:**
1. Test with "Test Payment" gateway
2. Check gateway plugin compatibility
3. Try invisible CAPTCHA (Turnstile, reCAPTCHA v3)

---

### Guest Checkout Issues

**Problem:** Guests can't complete checkout

**Solutions:**
1. Verify guest checkout enabled in EDD settings
2. Test as logged-out user
3. Check CAPTCHA validates before payment
4. Try lowering threshold (if reCAPTCHA v3)

---

## 🚀 Best Practices

### 1. Always Protect Checkout

**Why Checkout is Critical:**
- Payment processing abuse
- Stolen card testing
- Fake purchase attempts
- Download link harvesting

**Recommended:**
```
☑ Checkout Form ← Essential
```

---

### 2. Choose Right CAPTCHA for E-commerce

**For Best Conversion:**
- **Turnstile** - Invisible, professional
- **reCAPTCHA v3** - Seamless, no friction

**For Maximum Security:**
- **reCAPTCHA v2** - Visible checkbox
- Shows customers you're secure

**Our Recommendation:** Turnstile for best balance

---

### 3. Skip for Logged-In Customers

```php
add_filter( 'wbc_edd_checkout_skip_logged_in', '__return_true' );
```

**Benefits:**
- Better UX for repeat customers
- Faster checkout for members
- Still protects guest checkout

---

### 4. Combine with Fraud Prevention

**Layered Security:**
1. CAPTCHA (stops bots)
2. EDD fraud detection
3. Payment gateway fraud tools
4. IP blocking for repeat offenders

---

## 📊 Recommended Settings

### Small Digital Store
```
☑ Checkout Form
☐ Registration (if guest checkout allowed)
☐ Login (optional)
```
**CAPTCHA:** Turnstile (invisible, professional)

### Medium Digital Store
```
☑ Checkout Form ← Essential
☑ Registration Form
☐ Login (optional)
```
**CAPTCHA:** reCAPTCHA v3 or Turnstile

### Large Digital Store
```
☑ Checkout Form ← Essential
☑ Registration Form ← Essential
☑ Login Form ← Recommended
```
**CAPTCHA:** reCAPTCHA v2 for registration/login, v3 for checkout

---

## 🔒 Security Considerations

### Checkout Protection is Critical

**Risks without CAPTCHA:**
- Card testing (validating stolen cards)
- Fake purchases to get download links
- Payment gateway abuse
- Server resources consumed

**CAPTCHA prevents:**
- Automated attacks
- Download link harvesting
- Fake account creation
- Payment processor flags

---

### Digital Products are Vulnerable

**Unlike physical goods:**
- Instant delivery after payment
- No shipping verification
- Easy to automate downloads
- High-value targets for bots

**Essential protections:**
- CAPTCHA on checkout
- Fraud detection plugins
- IP monitoring
- Download attempt limits

---

## ♿ Accessibility

All CAPTCHA services provide accessible options:

**Recommended for EDD:**
- **Turnstile** - Usually invisible, accessible
- **reCAPTCHA v3** - Invisible, no barriers

**With visible challenges:**
- Audio alternatives available
- Keyboard navigation supported

---

## 🔗 Compatible Features

Works with all EDD features:
- ✅ Simple downloads
- ✅ Variable pricing
- ✅ Recurring payments (EDD Recurring)
- ✅ Software licensing (EDD SL)
- ✅ All payment gateways
- ✅ FES (Frontend Submissions)
- ✅ Reviews extension

---

## 🌍 Multilingual Stores

Compatible with:
- **WPML** - Full support
- **Polylang** - Full support
- CAPTCHA auto-detects language

---

## 📈 Conversion Impact

### Measuring CAPTCHA Impact

**Track these metrics:**
- Checkout completion rate
- Cart abandonment rate
- Spam order count
- Customer complaints

**Expected results with invisible CAPTCHA:**
- Conversion rate: No change or slight improvement
- Spam orders: 95%+ reduction
- Support tickets: Decrease

---

## 📚 Related Guides

**CAPTCHA Services:**
- [Turnstile](../captcha-services/turnstile.md) - Best for EDD
- [reCAPTCHA v3](../captcha-services/recaptcha-v3.md) - Invisible option

**Similar Integrations:**
- [WooCommerce](woocommerce.md) - Physical products
- [MemberPress](memberpress.md) - Memberships

**Form Builders:**
- [Contact Form 7](contact-form-7.md) - Support forms
- [WPForms](wpforms.md) - Custom forms

---

## 🔄 Next Steps

1. [Choose CAPTCHA service](../captcha-services/README.md) - Turnstile recommended
2. Enable EDD checkout protection (minimum)
3. Test complete purchase flow
4. Monitor conversion rates
5. Add fraud detection for comprehensive security

---

**Need Help?** [FAQ](../README.md#frequently-asked-questions) | [Support](../README.md#getting-help)
