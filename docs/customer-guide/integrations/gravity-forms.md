# Gravity Forms Integration

Protect your Gravity Forms from spam with automatic CAPTCHA protection. Works on all form types including advanced conditional logic forms.

## 📋 Overview

One-click protection for **all Gravity Forms** including:
- Contact forms
- Multi-page forms
- Conditional forms
- Payment forms (Stripe, PayPal)
- User registration forms
- Survey and quiz forms
- File upload forms

---

## 🛡️ Why Protect Gravity Forms

**Common Issues:**
- Spam submissions wasting time
- Fake registrations
- Bot-generated surveys skewing results
- Fraudulent payment attempts
- Server resources consumed by spam

**With CAPTCHA:**
- Eliminates automated spam
- Protects payment processing
- Accurate survey results
- Genuine user registrations only

**Recommendation:** ✅ Enable for all public Gravity Forms

---

## ⚙️ Quick Setup

### Step 1: Prerequisites

1. **Install Gravity Forms:**
   - Commercial plugin (license required)
   - Purchase from [gravityforms.com](https://www.gravityforms.com/)

2. **Configure CAPTCHA Service:**
   - Choose your service
   - Get API keys
   - See [CAPTCHA guides](../captcha-services/README.md)

---

### Step 2: Enable Protection

1. Go to **Settings → Wbcom CAPTCHA Manager**
2. Configure CAPTCHA service with API keys
3. Scroll to **"Gravity Forms"** section
4. Check:

```
☑ Enable CAPTCHA on Gravity Forms
```

5. Click **"Save Changes"**

**Done!** All Gravity Forms now protected automatically.

---

### Step 3: Test Your Forms

1. Visit page with Gravity Form
2. Verify CAPTCHA appears (usually before submit)
3. Test submission:
   - Without CAPTCHA: Should fail with error
   - With CAPTCHA completed: Should submit successfully

**Important:** Test multi-page forms on the last page.

---

## 🎨 Customization

### Exclude Specific Forms

```php
// Add to theme's functions.php
add_filter( 'wbc_gravity_exclude_forms', function( $excluded ) {
    $excluded[] = 5; // Gravity Form ID
    return $excluded;
});
```

**Find Form ID:**
- **Forms → All Forms** - ID in list

---

### Custom Error Message

```php
add_filter( 'wbc_gravity_error_message', function( $message ) {
    return 'Please complete the security verification.';
});
```

---

### CAPTCHA on Multi-Page Forms

By default, CAPTCHA appears on the last page (before final submit):

```php
// Show on first page instead
add_filter( 'wbc_gravity_captcha_page', function() {
    return 1; // Page number
});
```

---

### Skip for Logged-In Users

```php
add_filter( 'wbc_gravity_skip_logged_in', '__return_true' );
```

---

## 🔧 Troubleshooting

### CAPTCHA Not Appearing

**Check:**
1. Gravity Forms is active and licensed
2. Plugin setting "Enable CAPTCHA on Gravity Forms" is checked
3. CAPTCHA service configured correctly
4. Clear all caches
5. Check Gravity Forms version (2.5+ required)

---

### Conflicts with Gravity Forms' Built-in reCAPTCHA

**Issue:** Both CAPTCHAs showing

**Solution:**
- Remove Gravity Forms' CAPTCHA field from form
- OR disable our plugin's Gravity Forms integration
- Use one or the other, not both

**Recommendation:** Use our plugin for centralized management.

---

### Multi-Page Form Issues

**Problem:** CAPTCHA not showing on last page

**Solutions:**
1. Check form has multiple pages configured
2. CAPTCHA should appear before final submit
3. Test by going through all pages
4. Clear form cache

---

### Conditional Logic Conflicts

**Problem:** CAPTCHA affected by conditional logic

**Solution:**
- CAPTCHA validation happens server-side
- Conditional logic should not affect CAPTCHA
- If issues occur, contact support

---

### Payment Form Issues

**Problem:** CAPTCHA interfering with payment

**Solutions:**
1. Verify CAPTCHA validates before payment step
2. Test with Stripe/PayPal in test mode
3. Check order: Form validation → CAPTCHA → Payment
4. Use reCAPTCHA v3 for seamless checkout

---

## 🚀 Best Practices

### 1. Protect All Public Forms

Especially critical for:
- ✅ Contact forms
- ✅ Registration forms
- ✅ Payment forms
- ✅ Survey forms (prevents skewed results)
- ✅ Contest entry forms

### 2. Choose Right CAPTCHA by Form Type

**Simple Contact Forms:**
- Turnstile or reCAPTCHA v3 (invisible)

**Payment/Registration:**
- reCAPTCHA v2 (visible checkbox)
- Shows customers you take security seriously

**Surveys/Quizzes:**
- reCAPTCHA v3 (invisible, better completion rates)

**File Upload Forms:**
- reCAPTCHA v2 (prevents large file spam)

### 3. Multi-Page Form Strategy

- Place CAPTCHA on last page only (default)
- Don't frustrate users on early pages
- Validate all other fields first
- CAPTCHA as final check before submission

### 4. Test Thoroughly

**Gravity Forms is complex, so test:**
- All form types you use
- Multi-page forms (all pages)
- Conditional logic scenarios
- Payment processing
- User registration flows
- File uploads

---

## 📊 Recommended Settings

### Contact/Support Forms
```
☑ Enable Gravity Forms CAPTCHA
```
**CAPTCHA:** Turnstile (professional, invisible)

### Payment Forms
```
☑ Enable Gravity Forms CAPTCHA
```
**CAPTCHA:** reCAPTCHA v2 (visible security)
**Extra:** Fraud detection

### Registration Forms
```
☑ Enable Gravity Forms CAPTCHA
```
**CAPTCHA:** reCAPTCHA v2 or hCaptcha
**Extra:** Email verification + manual approval

### Survey/Quiz Forms
```
☑ Enable Gravity Forms CAPTCHA
```
**CAPTCHA:** reCAPTCHA v3 (invisible, better completion)
**Why:** Accurate results without friction

---

## 🔒 Security Considerations

### Payment Protection is Critical

Gravity Forms supports:
- Stripe
- PayPal
- Square
- Authorize.net

**CAPTCHA prevents:**
- Card testing (validating stolen cards)
- Fake transactions
- Payment gateway abuse
- Inventory locking

**Must-do:**
- Always enable CAPTCHA on payment forms
- Use visible CAPTCHA (reCAPTCHA v2)
- Combine with fraud detection
- Monitor failed payment attempts

---

### Survey Data Integrity

**Without CAPTCHA:**
- Bots can submit fake responses
- Results get skewed
- Decisions based on bad data

**With CAPTCHA:**
- Only human responses
- Accurate data for analysis
- Trustworthy results

---

### File Upload Protection

**Risks:**
- Bots uploading spam files
- Server storage consumed
- Malicious file uploads
- Server performance degradation

**Solution:**
- CAPTCHA on file upload forms
- Limit file types in Gravity Forms
- Set file size limits
- Scan uploads with security plugins

---

## ♿ Accessibility

All CAPTCHA services provide accessible options:

**reCAPTCHA v2:**
- Audio challenges
- Keyboard navigation
- Screen reader support

**Turnstile:**
- Usually invisible (no interaction)
- Accessible fallbacks when needed

**hCaptcha:**
- Audio alternatives
- Keyboard accessible

**Recommendation:** Use Turnstile or reCAPTCHA v3 for best accessibility.

---

## 🔗 Compatible Features

**Gravity Forms Features:**
- ✅ Multi-page forms
- ✅ Conditional logic
- ✅ User registration
- ✅ Payment addons (Stripe, PayPal, etc.)
- ✅ Post creation
- ✅ Surveys & polls
- ✅ Quiz scoring
- ✅ File uploads
- ✅ Save and continue
- ✅ Partial entries

**All features work with CAPTCHA enabled.**

---

## 🌍 Multilingual Forms

Works with:
- **WPML** - Full compatibility
- **Polylang** - Full compatibility
- **Gravity Forms translations**

CAPTCHA appears in user's language automatically.

---

## 📚 Related Guides

**CAPTCHA Services:**
- [Turnstile](../captcha-services/turnstile.md) - Best for professional forms
- [reCAPTCHA v2](../captcha-services/recaptcha-v2.md) - Payment forms
- [reCAPTCHA v3](../captcha-services/recaptcha-v3.md) - Surveys

**Other Form Builders:**
- [WPForms](wpforms.md)
- [Ninja Forms](ninja-forms.md)
- [Forminator](forminator.md)
- [Contact Form 7](contact-form-7.md)

**Related Integrations:**
- [WooCommerce](woocommerce.md) - E-commerce
- [Easy Digital Downloads](easy-digital-downloads.md) - Digital products

---

## 🔄 Next Steps

1. [Choose CAPTCHA service](../captcha-services/README.md) - Turnstile recommended
2. Enable Gravity Forms protection
3. Test all your forms (especially multi-page and payment)
4. Monitor spam reduction
5. Adjust settings based on results

---

**Need Help?** [FAQ](../README.md#frequently-asked-questions) | [Support](../README.md#getting-help)
