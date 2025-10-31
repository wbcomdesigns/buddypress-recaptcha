# WPForms Integration

Protect all your WPForms from spam with automatic CAPTCHA protection. Works on contact forms, surveys, payment forms, and all other WPForms types.

## 📋 Overview

One-click protection for **all WPForms** on your site including:
- Contact forms
- Registration forms
- Survey forms
- Payment forms
- Order forms
- Poll forms
- Newsletter signups

---

## 🛡️ Why Protect WPForms

**Without CAPTCHA:**
- Spam form submissions
- Fake survey responses
- Bot registrations
- Fraudulent payment attempts
- Email inbox flooding

**With CAPTCHA:**
- 99% spam reduction
- Genuine submissions only
- Protected payment forms
- Time saved on spam filtering

**Recommendation:** ✅ Enable for all public-facing WPForms

---

## ⚙️ Quick Setup

### Step 1: Prerequisites

1. **Install WPForms:**
   - Free or Pro version
   - Download from [WordPress.org](https://wordpress.org/plugins/wpforms-lite/)

2. **Configure CAPTCHA Service:**
   - Choose your CAPTCHA service
   - Get API keys
   - See [CAPTCHA service guides](../captcha-services/README.md)

---

### Step 2: Enable Protection

1. Go to **Settings → Wbcom CAPTCHA Manager**
2. Configure your CAPTCHA service
3. Scroll to **"WPForms"** section
4. Check the box:

```
☑ Enable CAPTCHA on WPForms
```

5. Click **"Save Changes"**

**Done!** CAPTCHA now protects all WPForms automatically.

---

### Step 3: Test Your Forms

1. **Visit a page** with a WPForms form
2. **Verify CAPTCHA appears** above submit button
3. **Test without CAPTCHA:**
   - Fill form, don't complete CAPTCHA
   - Try to submit
   - Should see error message

4. **Test with CAPTCHA:**
   - Complete CAPTCHA
   - Submit successfully

---

## 🎨 Customization

### Exclude Specific Forms

Skip CAPTCHA on certain forms:

```php
// Add to theme's functions.php
add_filter( 'wbc_wpforms_exclude_forms', function( $excluded ) {
    $excluded[] = 123; // WPForms form ID
    return $excluded;
});
```

**Find Form ID:**
- Go to **WPForms → All Forms**
- ID shown in form list

---

### Custom Error Message

```php
add_filter( 'wbc_wpforms_error_message', function( $message ) {
    return 'Please verify you are human before submitting.';
});
```

---

### CAPTCHA Position

```php
add_filter( 'wbc_wpforms_captcha_position', function() {
    return 'before_submit'; // or 'after_form'
});
```

---

## 🔧 Troubleshooting

### CAPTCHA Not Appearing

**Check:**
1. WPForms plugin is active
2. "Enable CAPTCHA on WPForms" is checked
3. CAPTCHA service configured correctly
4. Clear cache (browser + WordPress)
5. Test in incognito mode

---

### Validation Fails

**Solutions:**
1. Verify API keys are correct
2. Check domain registered in CAPTCHA service
3. Include www and non-www versions
4. Test server connection to CAPTCHA API

---

### Form Layout Issues

**Fix with CSS:**
```css
.wpforms-form .wbc-captcha-container {
    margin: 20px 0;
    clear: both;
}
```

---

### Spam Still Getting Through

1. Verify CAPTCHA is actually validating
2. Switch to reCAPTCHA v2 (visible) for stronger protection
3. Increase threshold if using reCAPTCHA v3
4. Enable WPForms' built-in spam prevention
5. Use Akismet integration

---

## 🚀 Best Practices

### 1. Enable on All Forms

WPForms used for many purposes:
- Contact forms ← Essential
- Payment forms ← Critical
- Registration forms ← Important
- Surveys ← Optional (but recommended)

### 2. Choose Right CAPTCHA

**Payment/Registration:**
- Use reCAPTCHA v2 (visible security)
- Or hCaptcha (privacy + visible)

**Contact/Survey Forms:**
- Use Turnstile (invisible, better UX)
- Or reCAPTCHA v3 (invisible)

### 3. Test Payment Forms Thoroughly

If using WPForms for payments:
- Test with Stripe/PayPal
- Verify CAPTCHA doesn't break payment flow
- Test on mobile devices
- Monitor conversion rates

---

## 📊 Recommended Settings

### Contact Forms
```
☑ Enable WPForms CAPTCHA
```
**CAPTCHA:** Turnstile or reCAPTCHA v3

### Payment Forms
```
☑ Enable WPForms CAPTCHA
```
**CAPTCHA:** reCAPTCHA v2 (visible)
**Extra:** Fraud detection plugins

### Registration Forms
```
☑ Enable WPForms CAPTCHA
```
**CAPTCHA:** reCAPTCHA v2 or hCaptcha
**Extra:** Email verification

---

## 🔒 Security Notes

**WPForms Payment Protection:**
- CAPTCHA validates before payment processing
- Prevents fake payment attempts
- Protects Stripe/PayPal from abuse
- Essential for e-commerce forms

**Registration Protection:**
- Stops bot registrations via WPForms
- Maintains user database quality
- Prevents email spam from fake accounts

---

## ♿ Accessibility

All supported CAPTCHA services provide:
- Keyboard navigation
- Screen reader support
- Audio alternatives (reCAPTCHA v2, hCaptcha)
- High contrast modes

**Recommended:** Use Turnstile for best accessibility (usually invisible).

---

## 🔗 Compatible With

- WPForms Lite (Free)
- WPForms Pro
- WPForms Payment Addons (Stripe, PayPal, Square)
- WPForms Registration Addon
- WPForms Survey & Polls
- WPForms Post Submissions

---

## 📚 Related Guides

**CAPTCHA Services:**
- [Turnstile](../captcha-services/turnstile.md) - Best for WPForms UX
- [reCAPTCHA v2](../captcha-services/recaptcha-v2.md) - Payment forms
- [reCAPTCHA v3](../captcha-services/recaptcha-v3.md) - Invisible

**Other Form Builders:**
- [Contact Form 7](contact-form-7.md)
- [Gravity Forms](gravity-forms.md)
- [Ninja Forms](ninja-forms.md)
- [Forminator](forminator.md)

---

## 🔄 Next Steps

1. [Choose CAPTCHA service](../captcha-services/README.md) - Turnstile recommended
2. Enable WPForms protection with one click
3. Test all your forms
4. Monitor spam reduction
5. Adjust settings if needed

---

**Need Help?** [FAQ](../README.md#frequently-asked-questions) | [Support](../README.md#getting-help)
