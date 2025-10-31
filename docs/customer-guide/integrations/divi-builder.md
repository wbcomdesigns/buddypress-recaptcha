# Divi Builder Contact Forms Integration

Automatic CAPTCHA protection for Divi contact form modules. Protects all Divi forms across your site with one click.

## 📋 Overview

Protects Divi contact form modules:
- Contact forms
- Newsletter signup forms
- Quote request forms
- Forms in Divi Builder
- Forms in Theme Builder

---

## ⚙️ Quick Setup

### Step 1: Prerequisites

1. **Install Divi Theme or Divi Builder Plugin**
2. **Configure CAPTCHA** - [Service guides](../captcha-services/README.md)

### Step 2: Enable Protection

1. **Settings → Wbcom CAPTCHA Manager**
2. Scroll to **"Divi Builder"**
3. Check: ☑ Enable CAPTCHA on Divi Contact Forms
4. **Save Changes**

### Step 3: Test

1. Visit page with Divi contact form
2. Verify CAPTCHA appears above submit button
3. Test submission with and without CAPTCHA

---

## 🎨 Customization

### Exclude Specific Forms

```php
add_filter( 'wbc_divi_exclude_forms', function( $excluded ) {
    // Exclude by page ID or form identifier
    $excluded[] = 123; // Page ID
    return $excluded;
});
```

### Custom Error Message

```php
add_filter( 'wbc_divi_error_message', function( $message ) {
    return 'Please complete the security verification.';
});
```

### CAPTCHA Position

```php
add_filter( 'wbc_divi_captcha_position', function() {
    return 'before_submit'; // or 'after_fields'
});
```

---

## 🔧 Troubleshooting

### CAPTCHA Not Appearing

**Check:**
1. Divi theme/plugin is active
2. Using Divi Contact Form module (not third-party)
3. Plugin setting enabled
4. Clear Divi static CSS cache
5. Clear all page caches

### Clear Divi Cache

**Important for Divi:**
1. **Divi → Theme Options → Builder → Advanced**
2. Click **"Clear Static CSS File Cache"**
3. Test form again

### Builder vs Frontend

**Problem:** Works in builder but not frontend

**Solutions:**
- Clear Divi cache (see above)
- Regenerate static CSS files
- Test in incognito mode
- Check for JavaScript errors

### Theme Builder Forms

**Problem:** CAPTCHA not on Theme Builder forms

**Solutions:**
1. Clear Divi cache
2. Regenerate CSS files
3. Test template separately

---

## 🚀 Best Practices

### 1. Test All Form Locations

Divi forms can appear in:
- Regular pages
- Landing pages
- Theme Builder headers/footers
- Popups
- Specialty sections

Test each location.

### 2. Choose Right CAPTCHA for Divi

**Landing Pages:**
- Turnstile or reCAPTCHA v3 (invisible)
- Maximizes conversion rates
- Professional appearance

**Contact Pages:**
- Turnstile (invisible, modern)
- reCAPTCHA v3 (seamless)

**Forms with CTA:**
- Use invisible CAPTCHA
- Don't add friction to conversions

### 3. Match Divi Design

**Divi is design-focused:**
- Use invisible CAPTCHA when possible
- If visible, ensure it matches color scheme
- Test responsive design on mobile
- Check dark/light theme versions

### 4. Performance

**Divi + CAPTCHA:**
- CAPTCHA loads only with forms
- Minimal impact on Divi performance
- Consider Divi speed optimization settings

---

## 📊 Recommended Settings

### Lead Generation
```
☑ Enable Divi CAPTCHA
```
**CAPTCHA:** Turnstile (invisible, best conversion)

### Contact Forms
```
☑ Enable Divi CAPTCHA
```
**CAPTCHA:** reCAPTCHA v3 or Turnstile

### Newsletter Signups
```
☑ Enable Divi CAPTCHA
```
**CAPTCHA:** reCAPTCHA v3 (invisible, better signups)

---

## 🔒 Security Notes

**Divi Forms + Email:**
- Forms send to your email
- Spam can flood inbox
- Affects email reputation

**CAPTCHA protects:**
- Email deliverability
- Server resources
- Your time (no spam review)

---

## ♿ Accessibility

**Divi Accessibility:**
- Divi forms are accessible
- CAPTCHA maintains accessibility

**Recommended:**
- Turnstile (invisible, most accessible)
- reCAPTCHA v3 (no user interaction)

---

## 🔗 Compatible Features

Works with:
- ✅ Divi Contact Form module
- ✅ Divi Theme Builder
- ✅ Divi Landing Pages
- ✅ Split testing (Divi Leads)
- ✅ Custom form fields
- ✅ Conditional display

---

## 🌍 Multilingual Sites

Compatible with:
- **WPML** - Full support
- **Polylang** - Full support
- CAPTCHA detects user language automatically

---

## 📚 Related Guides

**CAPTCHA Services:**
- [Turnstile](../captcha-services/turnstile.md) - Best for Divi
- [reCAPTCHA v3](../captcha-services/recaptcha-v3.md) - Invisible

**Other Page Builders:**
- [Elementor Pro](elementor-pro.md)

**Form Builders:**
- [Contact Form 7](contact-form-7.md)
- [WPForms](wpforms.md)

---

## 🔄 Next Steps

1. [Choose CAPTCHA](../captcha-services/README.md) - Turnstile recommended
2. Enable Divi protection
3. Clear Divi cache
4. Test all form locations
5. Monitor spam reduction

---

**Need Help?** [FAQ](../README.md#frequently-asked-questions) | [Support](../README.md#getting-help)
