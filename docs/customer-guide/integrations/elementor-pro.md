# Elementor Pro Forms Integration

Protect all Elementor Pro form widgets with automatic CAPTCHA. Works on all pages and popups built with Elementor Pro.

## 📋 Overview

Protects Elementor Pro form widgets:
- Contact forms
- Registration forms
- Login forms
- Subscription forms
- Forms in popups
- Forms in templates

---

## ⚙️ Quick Setup

### Step 1: Prerequisites

1. **Install Elementor Pro** (premium license required)
2. **Configure CAPTCHA** - [Service guides](../captcha-services/README.md)

### Step 2: Enable Protection

1. **Settings → Wbcom CAPTCHA Manager**
2. Scroll to **"Elementor Pro Forms"**
3. Check: ☑ Enable CAPTCHA on Elementor Pro Forms
4. **Save Changes**

### Step 3: Test

1. Visit page with Elementor form
2. Verify CAPTCHA appears
3. Test in editor and frontend
4. Test in popups if using

---

## 🎨 Customization

### Exclude Specific Forms

```php
add_filter( 'wbc_elementor_exclude_forms', function( $excluded ) {
    $excluded[] = 'form_id_123'; // Elementor form ID
    return $excluded;
});
```

**Find Form ID:**
- Edit page in Elementor
- Select form widget
- Check Form ID in settings

### Custom Error Message

```php
add_filter( 'wbc_elementor_error_message', function( $message ) {
    return 'Please verify you are human before submitting.';
});
```

### CAPTCHA in Popups

CAPTCHA works in Elementor popups automatically. No special configuration needed.

---

## 🔧 Troubleshooting

### CAPTCHA Not Appearing

**Check:**
1. Elementor Pro is active (not free Elementor)
2. Form widget is Elementor Pro form (not CF7/other)
3. Plugin setting enabled
4. Clear Elementor cache: **Elementor → Tools → Regenerate CSS**
5. Clear page cache

### Editor vs Frontend Issues

**Problem:** Works in editor but not frontend

**Solution:**
- Clear Elementor cache
- Regenerate CSS files
- Test in incognito mode

### Popup Form Issues

**Problem:** CAPTCHA not showing in popup

**Solutions:**
1. Regenerate Elementor files
2. Check popup trigger settings
3. Test popup animation timing
4. Ensure CAPTCHA loads before popup closes

### Theme Conflicts

**Problem:** CAPTCHA breaks Elementor form styling

**Solutions:**
1. Add custom CSS to Elementor
2. Adjust CAPTCHA container styling:
```css
.elementor-form .wbc-captcha-container {
    margin: 15px 0;
    width: 100%;
}
```

---

## 🚀 Best Practices

### 1. Test in All Contexts

Elementor forms appear in many places:
- Regular pages
- Landing pages
- Popups
- Sticky bars
- Flyouts

**Test each context** where you use forms.

### 2. Choose Right CAPTCHA for Elementor

**Landing Pages/Popups:**
- Use Turnstile or reCAPTCHA v3 (invisible)
- Don't add friction to conversion

**Contact Forms:**
- Turnstile (professional, invisible)

**Login/Registration:**
- reCAPTCHA v2 (visible security)

### 3. Optimize for Mobile

Elementor is responsive:
- Test forms on mobile devices
- Use compact CAPTCHA if visible
- Consider invisible CAPTCHA for better mobile UX

### 4. Performance

**Elementor + CAPTCHA:**
- CAPTCHA loads only on pages with forms
- Minimal performance impact
- Consider lazy loading for popups

---

## 📊 Recommended Settings

### Lead Generation Forms
```
☑ Enable Elementor Pro CAPTCHA
```
**CAPTCHA:** Turnstile (invisible, better conversion)
**Why:** Don't add friction to leads

### Contact Forms
```
☑ Enable Elementor Pro CAPTCHA
```
**CAPTCHA:** reCAPTCHA v3 or Turnstile
**Why:** Professional, seamless experience

### Login/Registration Forms
```
☑ Enable Elementor Pro CAPTCHA
```
**CAPTCHA:** reCAPTCHA v2 (visible)
**Why:** Shows users you take security seriously

---

## 🔒 Security Notes

**Elementor Pro Forms + Email:**
- Forms trigger email sending
- Spam can flood your inbox
- Can affect email deliverability

**CAPTCHA prevents:**
- Email spam via contact forms
- Fake registrations
- Bot submissions
- Server resource abuse

---

## ♿ Accessibility

**Elementor is WCAG compliant:**
- CAPTCHA maintains accessibility
- Keyboard navigation works
- Screen readers supported

**Best for Elementor:**
- Turnstile (invisible, most accessible)
- reCAPTCHA v3 (invisible)

---

## 🔗 Compatible Features

Works with all Elementor Pro features:
- ✅ Form Actions (Email, Webhook, etc.)
- ✅ Multi-step forms
- ✅ Conditional logic
- ✅ File uploads
- ✅ Popups and templates
- ✅ Theme Builder forms
- ✅ WooCommerce Builder forms

---

## 🌍 Multilingual Sites

Works with:
- **WPML** - Full compatibility
- **Polylang** - Full compatibility
- **Elementor multilingual** - Automatic language detection

---

## 📚 Related Guides

**CAPTCHA Services:**
- [Turnstile](../captcha-services/turnstile.md) - Best for Elementor
- [reCAPTCHA v3](../captcha-services/recaptcha-v3.md) - Invisible

**Other Page Builders:**
- [Divi Builder](divi-builder.md)

**Form Builders:**
- [Contact Form 7](contact-form-7.md)
- [WPForms](wpforms.md)
- [Gravity Forms](gravity-forms.md)

---

## 🔄 Next Steps

1. [Choose CAPTCHA](../captcha-services/README.md) - Turnstile recommended
2. Enable Elementor Pro protection
3. Test all form locations (pages, popups, templates)
4. Clear Elementor cache
5. Monitor spam reduction

---

**Need Help?** [FAQ](../README.md#frequently-asked-questions) | [Support](../README.md#getting-help)
