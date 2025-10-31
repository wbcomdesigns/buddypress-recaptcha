# WooCommerce Integration

Protect your WooCommerce store from spam registrations, fake orders, and bot attacks with CAPTCHA protection.

## 📋 Overview

This guide covers CAPTCHA protection for:

- **Checkout Form** - Prevent fake orders and payment spam
- **Registration Form** - Stop spam customer accounts
- **Login Form** - Secure customer account access

---

## 🛡️ Supported Forms

### 1. Checkout Form

**Location:** `/checkout` page

**Why Protect:**
- Prevents fake order submissions
- Blocks payment processing spam
- Reduces fraudulent checkout attempts
- Protects payment gateway from abuse
- Prevents inventory locking by bots

**Recommendation:** ✅ **Highly recommended** for all stores

**Important:** CAPTCHA appears before payment processing, so legitimate customers complete it once during checkout.

---

### 2. Registration Form

**Location:** Account registration page and checkout registration

**Why Protect:**
- Stops spam customer accounts
- Prevents fake account creation
- Maintains customer database quality
- Reduces email bounce rates from fake addresses

**Recommendation:** ✅ Enable if customer registration is open

**Note:** Can appear on dedicated "My Account" registration page or during checkout if guest checkout is disabled.

---

### 3. Login Form

**Location:** `/my-account` login page

**Why Protect:**
- Prevents brute force attacks on customer accounts
- Protects customer data and order history
- Secures payment methods saved in accounts
- Blocks automated login attempts

**Recommendation:** ✅ Enable for stores with saved payment methods or sensitive data

---

## ⚙️ Setup Instructions

### Prerequisites

**Before You Begin:**

1. **Install WooCommerce:**
   - WooCommerce plugin must be installed and active
   - Download from [WordPress.org](https://wordpress.org/plugins/woocommerce/)
   - Complete WooCommerce setup wizard

2. **Configure CAPTCHA Service:**
   - Set up your preferred CAPTCHA service
   - Get API keys if required
   - See our [CAPTCHA service guides](../captcha-services/README.md)

---

### Step 1: Enable WooCommerce in CAPTCHA Settings

1. Go to **Settings → Wbcom CAPTCHA Manager**
2. Ensure your CAPTCHA service is configured with valid keys
3. Save settings if you made changes

---

### Step 2: Enable WooCommerce Form Protection

1. In **Settings → Wbcom CAPTCHA Manager**
2. Scroll to **"WooCommerce Forms"** section
3. Check the boxes for forms you want to protect:

**Recommended Settings:**
```
☑ Checkout Form ← Most important!
☑ Registration Form
☑ Login Form
```

4. Click **"Save Changes"**

---

### Step 3: Configure WooCommerce Settings

**Enable Registration (if needed):**

1. Go to **WooCommerce → Settings → Accounts & Privacy**
2. Check "Allow customers to create an account on the 'My account' page"
3. Optionally: "Allow customers to create an account during checkout"
4. Save changes

**Checkout Settings:**

1. Go to **WooCommerce → Settings → Advanced → Page Setup**
2. Verify checkout page is set
3. Test checkout process

---

### Step 4: Test Your Protection

#### Test Checkout Form:

1. **Add product to cart**
2. **Go to checkout page**
3. **Verify CAPTCHA appears:**
   - Should be visible on checkout form
   - Usually appears before payment section

4. **Test without CAPTCHA:**
   - Fill out checkout details
   - Don't complete CAPTCHA
   - Try to place order
   - **Expected:** Error message "Please complete the CAPTCHA"

5. **Test with CAPTCHA:**
   - Fill out checkout details
   - Complete CAPTCHA challenge
   - Place order
   - **Expected:** Order processes successfully
   - Cancel test order after verification

**Important:** Test with different payment methods to ensure CAPTCHA doesn't interfere.

#### Test Registration Form:

1. **Go to My Account page** (`/my-account`)
2. **Go to registration tab/section**
3. **Verify CAPTCHA appears**
4. **Test registration:**
   - Without CAPTCHA: Should show error
   - With CAPTCHA: Should create account successfully
5. Delete test account after verification

#### Test Login Form:

1. **Log out of customer account**
2. **Go to My Account page**
3. **Try to log in:**
   - Verify CAPTCHA appears on login form
   - Test with and without completing CAPTCHA
4. **Expected behavior:**
   - Without CAPTCHA: Login fails with error
   - With CAPTCHA: Login successful

---

## 🎨 Customization

### CAPTCHA Position on Checkout

By default, CAPTCHA appears at a strategic point in the checkout form. To customize:

```php
// Move CAPTCHA after billing fields
add_filter( 'wbc_woocommerce_checkout_captcha_position', function() {
    // Options: 'before_order_notes', 'after_customer_details', 'before_payment'
    return 'before_payment';
});
```

---

### Skip CAPTCHA for Registered Customers

Don't show CAPTCHA on checkout for logged-in customers:

```php
// Skip CAPTCHA if customer is logged in
add_filter( 'wbc_woocommerce_checkout_skip_logged_in', '__return_true' );
```

**Recommendation:** This improves UX for returning customers while still protecting guest checkouts.

---

### Custom Error Messages

Customize error messages for WooCommerce forms:

```php
// Custom checkout error
add_filter( 'wbc_woocommerce_checkout_error_message', function( $message ) {
    return 'Please verify you are human to complete your purchase.';
});

// Custom registration error
add_filter( 'wbc_woocommerce_register_error_message', function( $message ) {
    return 'Please complete the security check to create your account.';
});
```

---

### Different CAPTCHA for Different Forms

Use stricter CAPTCHA for checkout than login:

```php
// Stricter threshold for checkout (reCAPTCHA v3 only)
add_filter( 'wbc_woocommerce_checkout_captcha_threshold', function( $threshold ) {
    return 0.7; // More strict for checkout
});

add_filter( 'wbc_woocommerce_login_captcha_threshold', function( $threshold ) {
    return 0.5; // Normal for login
});
```

---

## 🔧 Troubleshooting

### Problem: CAPTCHA Not Appearing on Checkout

**Possible Causes:**

1. **WooCommerce Not Active:**
   - Go to **Plugins**
   - Verify WooCommerce is activated

2. **Checkout Page Issue:**
   - Go to **WooCommerce → Settings → Advanced**
   - Verify checkout page is set correctly
   - Make sure page contains `[woocommerce_checkout]` shortcode

3. **Theme Conflict:**
   - Some themes heavily customize checkout
   - Try switching to Storefront or default theme
   - Contact theme developer if issue persists

4. **Page Builder Conflict:**
   - If using Elementor/Divi for checkout page
   - Custom checkout templates may not work
   - Use WooCommerce standard checkout

5. **Cache Issue:**
   - Clear WooCommerce cache
   - Clear page cache
   - Disable cache plugins temporarily
   - Test in incognito mode

---

### Problem: Checkout Fails Even with CAPTCHA Completed

**Symptoms:**
- CAPTCHA completed successfully
- Checkout still shows error
- Order not created

**Solutions:**

1. **Check Payment Gateway:**
   - CAPTCHA should validate before payment
   - Some gateways have their own security
   - Test with different payment method

2. **JavaScript Errors:**
   - Open browser console (F12)
   - Look for JavaScript errors
   - May indicate conflict with other plugins

3. **AJAX Checkout:**
   - Some themes use AJAX checkout
   - May need special compatibility
   - Contact plugin support

4. **API Key Issues:**
   - Verify CAPTCHA API keys are correct
   - Check domain is registered in CAPTCHA dashboard

---

### Problem: Payment Gateway Conflicts

**Some gateways have their own fraud prevention:**

**PayPal:**
- Usually compatible
- CAPTCHA validates before PayPal redirect

**Stripe:**
- Compatible with CAPTCHA
- Stripe's own fraud detection still works
- CAPTCHA adds extra layer

**Square:**
- Test thoroughly
- Some versions may conflict

**Solution if conflicts occur:**
- Skip CAPTCHA for specific payment methods
- Use only on guest checkout
- Contact gateway support

---

### Problem: Guest Checkout Blocked

**Symptoms:**
- Customers can't complete guest checkout
- CAPTCHA validation fails

**Solutions:**

1. **Verify Guest Checkout Enabled:**
   - Go to **WooCommerce → Settings → Accounts & Privacy**
   - Check "Allow customers to place orders without an account"

2. **Test as Guest:**
   - Log out completely
   - Clear cookies
   - Try checkout as guest

3. **Check CAPTCHA Service:**
   - Some CAPTCHA services stricter on guests
   - Try lowering threshold (reCAPTCHA v3)
   - Or switch to reCAPTCHA v2

---

### Problem: Mobile Checkout Issues

**CAPTCHA may be harder on mobile:**

**Solutions:**

1. **Use Responsive CAPTCHA:**
   - reCAPTCHA automatically responsive
   - hCaptcha has mobile support
   - Turnstile works well on mobile

2. **Test on Real Devices:**
   - iPhone and Android
   - Different screen sizes
   - Mobile and tablet

3. **Use Compact Size:**
   - If available for your CAPTCHA service
   - Fits better on small screens

4. **Consider Invisible CAPTCHA:**
   - Turnstile or reCAPTCHA v3
   - Better mobile experience
   - No interaction needed

---

## 🚀 Best Practices

### 1. Always Protect Checkout

**Why Checkout Protection is Critical:**
- Prevents inventory locking (bots add to cart)
- Stops payment processor abuse
- Reduces fraudulent orders
- Protects against carding attacks

**Recommended Approach:**
```
☑ CAPTCHA on checkout
☐ Skip CAPTCHA for logged-in customers (optional)
☑ Combine with fraud detection plugins
☑ Monitor failed checkout attempts
```

---

### 2. Choose Right CAPTCHA for E-commerce

**For Best Checkout Conversion:**
- **Turnstile** - Invisible, fast, best UX
- **reCAPTCHA v3** - Invisible, good for high-volume

**For Maximum Security:**
- **reCAPTCHA v2** - Visible checkbox, stronger
- **hCaptcha** - Privacy-focused, visible

**Balance Approach:**
- Use Turnstile/v3 for checkout (smooth UX)
- Use v2 for registration/login (visible security)

---

### 3. Don't Over-Protect

**Bad: CAPTCHA Everywhere**
```
☑ Checkout ← Good
☑ Login ← Good
☑ Registration ← Good
☑ Product pages ← NO!
☑ Add to cart ← NO!
☑ Search ← NO!
```

**Good: Strategic Protection**
```
☑ Checkout ← Essential
☑ Registration ← Recommended
☐ Login ← Optional (if saved payments)
```

**Why:** Too many CAPTCHAs frustrate customers and hurt conversions.

---

### 4. Monitor Your Store

**Daily (High-Volume Stores):**
- Check abandoned cart rate
- Review failed orders
- Monitor CAPTCHA blocking rate
- Look for unusual patterns

**Weekly (Small Stores):**
- Review past week's orders
- Check for spam accounts
- Analyze checkout completion rate
- Adjust CAPTCHA settings if needed

**Key Metrics:**
- Checkout completion rate (should stay same or improve)
- Spam order rate (should decrease)
- Customer complaints (should decrease)

---

### 5. Test Checkout Process Thoroughly

**Test Scenarios:**

1. **Guest Checkout:**
   - As new customer
   - With all payment methods
   - On mobile and desktop

2. **Registered Customer:**
   - Logged in checkout
   - Verify CAPTCHA behavior
   - Test saved payment methods

3. **Different CAPTCHA Services:**
   - Test each service you might use
   - Compare conversion rates
   - Choose best for your store

4. **Mobile Testing:**
   - iPhone Safari
   - Android Chrome
   - Tablet devices

---

## 📊 Recommended Settings by Store Type

### Small Store (Low Traffic)

**Focus on UX:**
```
☑ Checkout Form (use Turnstile)
☐ Registration (only if spam is an issue)
☐ Login (not necessary for small stores)
```

**CAPTCHA:** Turnstile or reCAPTCHA v3 (invisible)

---

### Medium Store (Growing Traffic)

**Balance security and UX:**
```
☑ Checkout Form (Turnstile or reCAPTCHA v3)
☑ Registration Form (if enabled)
☐ Login Form (optional)
```

**CAPTCHA:** reCAPTCHA v3 with 0.5 threshold

---

### Large Store (High Traffic)

**Maximum protection:**
```
☑ Checkout Form ← Essential
☑ Registration Form ← Essential
☑ Login Form ← Recommended
```

**CAPTCHA:** reCAPTCHA v2 for registration/login, v3 for checkout
**Extra:** Fraud detection plugins, rate limiting

---

### High-Risk Store (Frequent Attacks)

**Strict security:**
```
☑ Checkout Form (reCAPTCHA v2)
☑ Registration Form (reCAPTCHA v2)
☑ Login Form (reCAPTCHA v2)
```

**CAPTCHA:** reCAPTCHA v2 (visible) everywhere
**Extra:** IP blocking, manual order review, fraud plugins

---

## 🔒 Security Considerations

### Checkout Form is Prime Target

**Why:**
- Test stolen credit cards (carding)
- Lock up inventory (competitive attack)
- Abuse promotional codes
- Create fake orders

**Protection Stack:**
```
1. CAPTCHA (stops bots)
2. Fraud detection (WooCommerce Anti-Fraud, etc.)
3. Payment gateway security (Stripe Radar, etc.)
4. Manual review (high-value orders)
```

---

### Guest Checkout Requires Extra Protection

**Risk:**
- No account means no tracking
- Harder to block repeat offenders
- Can't use reputation systems

**Solutions:**
- Always use CAPTCHA on guest checkout
- Consider requiring account for high-value orders
- Use fraud detection plugins
- Monitor guest order patterns

---

### Protect Wholesale/B2B Stores Differently

**B2B Considerations:**
- Known customers (logged in)
- High order values
- Repeated orders

**Recommended Approach:**
```
☑ Registration Form ← Verify new B2B customers
☐ Checkout Form ← Skip for logged-in B2B customers
☑ Login Form ← Protect B2B accounts
```

---

## 🌍 International Stores

WooCommerce CAPTCHA works globally:

**WPML / Polylang:**
- CAPTCHA works on all language versions
- Automatically detects customer language
- Error messages can be translated

**Multi-Currency:**
- CAPTCHA works regardless of currency
- No impact on currency switchers

**Geolocation:**
- Some CAPTCHA services stricter on certain countries
- Monitor checkout completion rates by country
- Adjust if specific countries have issues

---

## ♿ Accessibility for E-commerce

**Critical for E-commerce:**
- Must not block legitimate customers
- Should be keyboard accessible
- Need screen reader support

**Best CAPTCHA for Accessibility:**

1. **Turnstile** - Usually invisible (best)
2. **reCAPTCHA v3** - Invisible (good)
3. **reCAPTCHA v2** - Audio alternative (acceptable)
4. **hCaptcha** - Audio alternative (acceptable)

**Testing:**
- Test with screen readers
- Test keyboard-only navigation
- Test high-contrast modes

---

## 🔗 Integration with Other Plugins

### WooCommerce Subscriptions

Full compatibility:
- CAPTCHA on initial subscription signup
- No CAPTCHA on recurring payments (automatic)
- CAPTCHA on subscription changes (optional)

### WooCommerce Memberships

Works together:
- CAPTCHA on membership registration
- Can skip for certain membership levels
- Protects membership checkout

### YITH WooCommerce Gift Cards

Compatible:
- CAPTCHA on gift card purchase
- CAPTCHA on gift card redemption (optional)

### WooCommerce Bookings

Works well:
- CAPTCHA on booking checkout
- Same as regular checkout
- No special configuration

---

## 📈 Conversion Rate Impact

### Measuring CAPTCHA Impact

**Before Enabling:**
- Measure baseline checkout completion rate
- Track average time on checkout page

**After Enabling:**
- Monitor checkout completion rate
- Should stay same or improve (less bots)
- Compare real orders vs spam orders

**Expected Results:**
- Invisible CAPTCHA: No negative impact
- Visible CAPTCHA: <1% impact (usually offset by reduced fraud)

---

### Optimizing for Conversions

**Best Practices:**
- Use invisible CAPTCHA (Turnstile, reCAPTCHA v3)
- Skip CAPTCHA for logged-in customers
- Place CAPTCHA early in checkout (not at payment step)
- Test mobile thoroughly
- Monitor and adjust

---

## 📚 Related Guides

**CAPTCHA Service Setup:**
- [Turnstile Setup](../captcha-services/turnstile.md) - Best for e-commerce UX
- [reCAPTCHA v3 Setup](../captcha-services/recaptcha-v3.md) - Invisible protection
- [reCAPTCHA v2 Setup](../captcha-services/recaptcha-v2.md) - Visible security

**Other Integrations:**
- [WordPress Core Forms](wordpress-core.md) - Login, registration
- [Easy Digital Downloads](easy-digital-downloads.md) - Digital products
- [Contact Forms](contact-form-7.md) - Customer support forms

---

## 🔄 Next Steps

1. **[Choose your CAPTCHA service](../captcha-services/README.md)** - Turnstile recommended for e-commerce
2. **Enable WooCommerce protection** on checkout (minimum)
3. **Test thoroughly** with real checkout process
4. **Monitor conversion rates** to ensure no negative impact
5. **Add fraud detection** for comprehensive security

---

**Need Help?** Check our [FAQ section](../README.md#frequently-asked-questions) or [contact support](../README.md#getting-help).
