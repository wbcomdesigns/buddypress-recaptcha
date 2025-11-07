# Contact Form 7 Integration

Protect your Contact Form 7 forms from spam submissions with CAPTCHA. Works automatically on all CF7 forms across your site.

## 📋 Overview

This guide covers CAPTCHA protection for:

- **All Contact Form 7 Forms** - One-click protection for every CF7 form on your site

---

## 🛡️ Why Protect Contact Form 7

**Common Issues Without Protection:**
- Spam form submissions flooding your inbox
- Bot-generated messages wasting time
- Server resources consumed by spam
- Email deliverability issues from spam reports
- Time wasted reviewing fake inquiries

**With CAPTCHA Protection:**
- Eliminates 99% of automated spam
- Only genuine inquiries reach your inbox
- Saves hours of manual spam filtering
- Protects email reputation

**Recommendation:** ✅ **Always enable** for public-facing contact forms

---

## ⚙️ Setup Instructions

### Prerequisites

**Before You Begin:**

1. **Install Contact Form 7:**
   - Download from [WordPress.org](https://wordpress.org/plugins/contact-form-7/)
   - Activate the plugin

2. **Create Your Forms:**
   - Go to **Contact → Contact Forms**
   - Create or edit forms as needed
   - **No need to add CAPTCHA fields manually** - our plugin does it automatically!

3. **Configure CAPTCHA Service:**
   - Set up your preferred CAPTCHA service
   - Get API keys if required
   - See our [CAPTCHA service guides](../captcha-services/README.md)

---

### Step 1: Enable CAPTCHA Protection

1. Go to **Settings → Wbcom CAPTCHA Manager**
2. Ensure your CAPTCHA service is configured
3. Scroll to **"Contact Form 7"** section
4. Check the box:

```
☑ Enable CAPTCHA on Contact Form 7 Forms
```

5. Click **"Save Changes"**

**That's it!** CAPTCHA is now automatically added to ALL Contact Form 7 forms on your site.

---

### Step 2: Test Your Forms

#### Test Each Contact Form:

1. **Visit a page** with a Contact Form 7 form
2. **Verify CAPTCHA appears:**
   - Should see CAPTCHA widget on the form
   - Usually appears above the submit button

3. **Test without CAPTCHA:**
   - Fill out the form
   - Don't complete CAPTCHA
   - Try to submit
   - **Expected:** Error message "Please complete the CAPTCHA"

4. **Test with CAPTCHA:**
   - Fill out the form
   - Complete CAPTCHA challenge
   - Submit form
   - **Expected:** Form submits successfully, you receive the email

5. **Test multiple forms:**
   - If you have several CF7 forms, test each one
   - CAPTCHA should appear on all of them

---

## 🎨 Customization

### CAPTCHA Position

By default, CAPTCHA appears above the submit button. To customize:

```php
// Add to your theme's functions.php
add_filter( 'wbc_cf7_captcha_position', function() {
    return 'before_submit'; // Options: 'before_submit', 'after_form'
});
```

---

### Exclude Specific Forms

Don't want CAPTCHA on certain forms? Exclude by form ID:

```php
// Skip CAPTCHA on specific CF7 forms
add_filter( 'wbc_cf7_exclude_forms', function( $excluded ) {
    $excluded[] = 123; // CF7 form ID to exclude
    $excluded[] = 456; // Another form ID
    return $excluded;
});
```

**Finding Form ID:**
1. Go to **Contact → Contact Forms**
2. Form ID is shown in the list (the number in URL or shortcode)

---

### Custom Error Message

Customize the CAPTCHA error message:

```php
// Custom CF7 CAPTCHA error
add_filter( 'wbc_cf7_error_message', function( $message ) {
    return 'Please verify you are human before sending your message.';
});
```

---

### Enable Only on Specific Forms

Enable CAPTCHA only on certain forms, not all:

```php
// Only show CAPTCHA on specific forms
add_filter( 'wbc_cf7_include_only', function( $form_ids ) {
    return array( 123, 456 ); // Only these form IDs
});
```

---

## 🔧 Troubleshooting

### Problem: CAPTCHA Not Appearing

**Possible Causes:**

1. **Contact Form 7 Not Active:**
   - Go to **Plugins**
   - Verify Contact Form 7 is activated

2. **Plugin Settings:**
   - Go to **Settings → Wbcom CAPTCHA Manager**
   - Verify "Enable CAPTCHA on Contact Form 7" is checked
   - Verify CAPTCHA service is configured with valid keys

3. **Cache Issue:**
   - Clear WordPress cache
   - Clear browser cache
   - Clear page cache/CDN
   - Test in incognito mode

4. **Theme Conflict:**
   - Some themes heavily customize CF7
   - Try switching to default theme temporarily
   - Contact theme developer if issue persists

5. **CF7 Version:**
   - Update to latest Contact Form 7 version
   - Our plugin supports CF7 5.0+

---

### Problem: CAPTCHA Validation Fails

**Symptoms:**
- CAPTCHA widget appears
- User completes it
- Form still shows error

**Solutions:**

1. **Check API Keys:**
   - Verify Site Key and Secret Key in plugin settings
   - Make sure keys match your CAPTCHA service

2. **Check Domain:**
   - In CAPTCHA service dashboard
   - Verify domain is registered
   - Include www and non-www versions

3. **Server Connection:**
   - Server must reach CAPTCHA API
   - Check firewall settings
   - Contact hosting support

4. **CF7 Validation:**
   - Check if CF7 itself has validation errors
   - Fix field validation first
   - Then test CAPTCHA

---

### Problem: CAPTCHA Breaks Form Layout

**Symptoms:**
- Form looks misaligned
- CAPTCHA overlaps other fields
- Submit button moved

**Solutions:**

1. **Check Form Template:**
   - CF7 form editor may have custom HTML
   - CAPTCHA adds automatically
   - Adjust form template if needed

2. **Add Custom CSS:**
```css
/* Adjust CAPTCHA spacing */
.wpcf7-form .wbc-captcha-container {
    margin: 20px 0;
    clear: both;
}
```

3. **Change Position:**
   - Use filter to change CAPTCHA position (see customization section)

4. **Responsive Issues:**
   - Test on mobile
   - Use compact CAPTCHA size if available
   - Add mobile-specific CSS

---

### Problem: Spam Still Getting Through

**If spam submissions continue:**

1. **Verify CAPTCHA is Working:**
   - Test form submission yourself
   - Confirm CAPTCHA is required
   - Check validation blocks empty CAPTCHA

2. **Check Spam Type:**
   - **Automated bots:** CAPTCHA should block these
   - **Manual spam:** Humans can bypass CAPTCHA
   - Consider additional measures for manual spam

3. **Strengthen CAPTCHA:**
   - Switch from reCAPTCHA v3 to v2 (visible)
   - Or increase threshold (if using v3)
   - Or use hCaptcha (image challenges)

4. **Additional Protection:**
   - Use Akismet for CF7
   - Add honeypot fields
   - Enable form logging to track spam patterns
   - Use CF7 anti-spam plugins

---

### Problem: Multiple CAPTCHAs Appearing

**If you see two CAPTCHAs on one form:**

**Cause:** You may have:
- CF7's built-in reCAPTCHA enabled
- AND our plugin's CAPTCHA enabled

**Solution:**
1. **Remove CF7's CAPTCHA:**
   - Go to **Contact → Integration**
   - Remove reCAPTCHA API keys from CF7
   - Use only our plugin's CAPTCHA

2. **Or use CF7's CAPTCHA instead:**
   - Disable our plugin's CF7 integration
   - Use CF7's built-in reCAPTCHA

**Recommendation:** Use our plugin for centralized CAPTCHA management across all plugins.

---

## 🚀 Best Practices

### 1. Enable on All Public Forms

**Why:**
- Contact forms are prime spam targets
- Spam floods inbox and wastes time
- May affect email deliverability

**Recommended:**
```
☑ Enable CAPTCHA on Contact Form 7
```

Apply to all public-facing contact forms.

---

### 2. Choose Right CAPTCHA for Contact Forms

**Best User Experience:**
- **Turnstile** - Usually invisible, professional
- **reCAPTCHA v3** - Invisible, seamless

**More Security (if spam is severe):**
- **reCAPTCHA v2** - Visible checkbox
- **hCaptcha** - Image challenges

**Privacy-Focused:**
- **ALTCHA** - No third-party services
- **hCaptcha** - Privacy-respecting

---

### 3. Test All Your Forms

If you have multiple CF7 forms:
- Test each one individually
- Verify CAPTCHA appears on all
- Check mobile and desktop
- Test with different CAPTCHA services

---

### 4. Monitor Form Submissions

**Weekly:**
- Check email inbox for spam
- Review form submission patterns
- Adjust CAPTCHA if spam increases

**Metrics to Track:**
- Form submission count
- Spam vs legitimate ratio
- User complaints about CAPTCHA

---

### 5. Combine with Other Anti-Spam

**Layered Protection:**

1. **CAPTCHA** - Stops bots (our plugin)
2. **Akismet** - Catches sophisticated spam
3. **Honeypot** - Hidden field trap
4. **Email Validation** - Verify email format
5. **Form Logging** - Track submissions

---

## 📊 Recommended Settings by Form Type

### Simple Contact Form

**Basic "Contact Us" page:**
```
☑ Enable CF7 CAPTCHA
```
**CAPTCHA:** Turnstile or reCAPTCHA v3 (invisible)
**Why:** Professional, doesn't scare away customers

---

### Quote Request Form

**Forms collecting customer info:**
```
☑ Enable CF7 CAPTCHA
```
**CAPTCHA:** reCAPTCHA v2 or hCaptcha (visible)
**Why:** Shows customers you take security seriously

---

### Support/Help Form

**Customer support inquiries:**
```
☑ Enable CF7 CAPTCHA
```
**CAPTCHA:** Turnstile (invisible, professional)
**Extra:** Integrate with support ticket system

---

### Newsletter Signup

**Email list subscription:**
```
☑ Enable CF7 CAPTCHA
```
**CAPTCHA:** reCAPTCHA v3 (invisible, better conversion)
**Why:** Don't add friction to signup process

---

### Job Application Form

**Employment applications:**
```
☑ Enable CF7 CAPTCHA
```
**CAPTCHA:** reCAPTCHA v2 (visible, professional)
**Extra:** Save to database, not just email

---

## 🔒 Security Considerations

### Contact Forms Are Major Spam Targets

**Why spammers target contact forms:**
- Easy to find on websites
- Direct line to business email
- Can flood inbox with spam
- Used for phishing attempts
- SEO spam (backlink requests)

**Impact without protection:**
- Time wasted on spam
- Important emails buried
- Email server flagged as spam source
- Business disruption

---

### Form Spam Can Affect Email Deliverability

**Problem:**
- Spam submissions trigger your email sending
- Your server sends spam emails
- Email providers flag your domain
- Legitimate emails go to spam

**Solution:**
- CAPTCHA prevents this entirely
- Validates sender before email is triggered

---

## 🌍 Multilingual Forms

Contact Form 7 works with multilingual plugins:

**WPML:**
- Translate CF7 forms
- CAPTCHA works on all languages
- Error messages translated automatically

**Polylang:**
- Create forms per language
- CAPTCHA works on all versions

**Translation:**
- CAPTCHA widget auto-detects user language
- Error messages can be translated via .po files

---

## ♿ Accessibility

CAPTCHA services provide accessible options:

**For Visually Impaired:**
- reCAPTCHA: Audio challenges
- hCaptcha: Audio challenges
- Turnstile: Usually invisible (no interaction)
- ALTCHA: Screen reader friendly

**Best Practices:**
- Use invisible CAPTCHA when possible
- Provide clear labels
- Test with screen readers
- Ensure keyboard navigation works

---

## 🔗 Integration with Other Plugins

### Flamingo (CF7 Database)

Works together:
- CAPTCHA validates first
- Then Flamingo saves submission
- No conflicts

### Contact Form 7 Database Addon

Compatible:
- CAPTCHA validates before saving
- All validated submissions saved

### CF7 to Any API

Works seamlessly:
- CAPTCHA validates first
- Then data sent to API
- Prevents spam API calls

---

## 📈 Form Conversion Impact

### Measuring CAPTCHA Impact

**Before Enabling:**
- Track form completion rate
- Count spam vs real submissions

**After Enabling:**
- Monitor completion rate (should stay same or improve)
- Count spam (should drop dramatically)

**Expected Results:**
- Invisible CAPTCHA: No impact on conversion
- Visible CAPTCHA: <2% impact (offset by time saved)

---

## 📚 Related Guides

**CAPTCHA Service Setup:**
- [Turnstile Setup](../captcha-services/turnstile.md) - Best for contact forms
- [reCAPTCHA v3 Setup](../captcha-services/recaptcha-v3.md) - Invisible option
- [hCaptcha Setup](../captcha-services/hcaptcha.md) - Privacy-focused

**Other Form Builders:**
- [WPForms](wpforms.md) - Alternative form builder
- [Gravity Forms](gravity-forms.md) - Advanced form builder
- [Ninja Forms](ninja-forms.md) - Flexible form builder

**Core Integrations:**
- [WordPress Core](wordpress-core.md) - Comments, login, registration
- [WooCommerce](woocommerce.md) - E-commerce forms

---

## 🔄 Next Steps

1. **[Choose your CAPTCHA service](../captcha-services/README.md)** - Turnstile recommended
2. **Enable CF7 protection** with one checkbox
3. **Test all your contact forms** to verify CAPTCHA appears
4. **Monitor spam levels** - should drop dramatically
5. **Consider Akismet** for additional protection

---

**Need Help?** Check our [FAQ section](../README.md#frequently-asked-questions) or [contact support](../README.md#getting-help).
