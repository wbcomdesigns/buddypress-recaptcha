# Ninja Forms Integration

Automatic CAPTCHA protection for all your Ninja Forms. Simple one-click setup protects contact forms, registration forms, and all custom forms.

## 📋 Overview

Protects all Ninja Forms types:
- Contact forms
- Multi-step forms
- Payment forms
- Registration forms
- Survey forms
- Calculation forms

---

## ⚙️ Quick Setup

### Step 1: Prerequisites

1. **Install Ninja Forms** (free or premium)
2. **Configure CAPTCHA** - [Service guides](../captcha-services/README.md)

### Step 2: Enable Protection

1. **Settings → Wbcom CAPTCHA Manager**
2. Scroll to **"Ninja Forms"**
3. Check: ☑ Enable CAPTCHA on Ninja Forms
4. **Save Changes**

### Step 3: Test

1. Visit form page
2. Verify CAPTCHA appears
3. Test submission with and without CAPTCHA

---

## 🎨 Customization

Excluding a single Ninja Form by ID is not supported - the CAPTCHA toggle applies to the whole Ninja Forms integration, not individual forms.

### Custom Error

```php
add_filter( 'wbc_captcha_error_message', function( $message, $context ) {
    return 'ninjaforms' === $context ? 'Please verify you are human.' : $message;
}, 10, 2 );
```

The filter receives `( $message, $context, $service_id, $error_type )` and covers every form the plugin protects. To change the message for all forms without code, use the fields on the **Advanced** tab.

---

## 🔧 Troubleshooting

**CAPTCHA Not Showing:**
- Ninja Forms is active
- Setting is enabled
- CAPTCHA service configured
- Clear caches

**Validation Fails:**
- Check API keys
- Verify domain registered
- Test server connection

---

## 🚀 Best Practices

**Enable for:**
- ✅ Contact forms
- ✅ Payment forms
- ✅ Registration forms

**CAPTCHA Choice:**
- Contact: Turnstile (invisible)
- Payment: reCAPTCHA v2 (visible)
- Registration: hCaptcha (privacy-focused)

---

## 📚 Related

- [Turnstile Setup](../captcha-services/turnstile.md)
- [WPForms](wpforms.md)
- [Gravity Forms](gravity-forms.md)

---

**Need Help?** [FAQ](../README.md#frequently-asked-questions) | [Support](../README.md#getting-help)
