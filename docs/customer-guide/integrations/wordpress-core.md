# WordPress Core Forms Integration

Protect your WordPress core forms with CAPTCHA to prevent spam registrations, brute force attacks, and comment spam.

## 📋 Overview

This guide covers CAPTCHA protection for:

- **Login Form** - Prevent brute force attacks
- **Registration Form** - Stop spam user accounts
- **Lost Password Form** - Block password reset spam
- **Comment Form** - Eliminate comment spam

---

## 🛡️ Supported Forms

### 1. Login Form (`wp-login.php`)

**Location:** `/wp-login.php` or WordPress admin login page

**Why Protect:**
- Prevents brute force password attacks
- Blocks automated login attempts
- Secures admin access

**Recommendation:** ✅ Enable this (essential security)

---

### 2. Registration Form

**Location:** `/wp-login.php?action=register`

**Why Protect:**
- Stops bot registrations
- Prevents fake user accounts
- Reduces spam

**Recommendation:** ✅ Enable this if registration is open

**Note:** Only visible if you allow user registration in **Settings → General → Membership**

---

### 3. Lost Password Form

**Location:** `/wp-login.php?action=lostpassword`

**Why Protect:**
- Prevents password reset spam
- Blocks email flooding attacks
- Protects user accounts

**Recommendation:** ✅ Enable this (highly recommended)

---

### 4. Comment Form

**Location:** Individual posts/pages with comments enabled

**Why Protect:**
- Eliminates comment spam
- Blocks bot comments
- Keeps discussions genuine

**Recommendation:** ✅ Enable this if comments are enabled

**Note:** Only shows on posts/pages with comments enabled

---

## ⚙️ Setup Instructions

### Step 1: Configure Your CAPTCHA Service

Before enabling protection, set up your CAPTCHA service:

1. Go to **Settings → Wbcom CAPTCHA Manager**
2. Select your preferred CAPTCHA service
3. Enter API keys (if required)
4. Save settings

**Need help?** See our [CAPTCHA service guides](../captcha-services/README.md).

---

### Step 2: Enable WordPress Core Protection

1. In **Settings → Wbcom CAPTCHA Manager**
2. Scroll to **"WordPress Core Forms"** section
3. Check the boxes for forms you want to protect:

**Recommended Settings:**
```
☑ Login Form
☑ Registration Form
☑ Lost Password Form
☑ Comment Form
```

4. Click **"Save Changes"**

---

### Step 3: Test Each Form

It's important to test that CAPTCHA works correctly on each form.

#### Test Login Form:
1. Log out of WordPress
2. Go to `/wp-login.php`
3. Verify CAPTCHA widget appears
4. Try logging in:
   - **Without completing CAPTCHA:** Should show error
   - **With CAPTCHA completed:** Should log in successfully

#### Test Registration Form:
1. Log out (if needed)
2. Go to `/wp-login.php?action=register`
3. Verify CAPTCHA appears
4. Try registering a test account:
   - **Without CAPTCHA:** Should show error
   - **With CAPTCHA:** Should register successfully
5. Delete test account after testing

#### Test Lost Password Form:
1. Go to `/wp-login.php?action=lostpassword`
2. Verify CAPTCHA appears
3. Try requesting password reset:
   - **Without CAPTCHA:** Should show error
   - **With CAPTCHA:** Should send reset email

#### Test Comment Form:
1. Go to any post with comments enabled
2. Scroll to comment form
3. Verify CAPTCHA appears
4. Try posting a comment:
   - **Without CAPTCHA:** Should show error
   - **With CAPTCHA:** Should post successfully

---

## 🎨 Customization

### CAPTCHA Position on Comment Form

By default, CAPTCHA appears at the bottom of the comment form. To change position:

**Add to your theme's `functions.php`:**

```php
// Move CAPTCHA above submit button
add_filter( 'wbc_comment_form_captcha_position', function() {
    return 'before_submit';
});
```

---

### Custom Error Messages

Customize the error message shown when CAPTCHA fails:

```php
// Custom error for failed CAPTCHA
add_filter( 'wbc_captcha_error_message', function( $message ) {
    return 'Please verify you are human before submitting.';
});
```

---

### Exclude Logged-in Users from Comment CAPTCHA

Don't show CAPTCHA for logged-in users on comments:

**This is the default behavior**, but you can customize:

```php
// Show CAPTCHA even for logged-in users on comments
add_filter( 'wbc_comment_form_show_for_logged_in', '__return_true' );
```

---

## 🔧 Troubleshooting

### Problem: CAPTCHA Not Appearing

**On Login Form:**

1. **Clear browser cache**
2. **Check plugin is active:**
   - Go to **Plugins** menu
   - Verify "Wbcom CAPTCHA Manager" is active

3. **Verify settings:**
   - Check "Login Form" is enabled in settings
   - Verify CAPTCHA service is configured with valid keys

4. **Check for conflicts:**
   - Temporarily disable other security plugins
   - Disable custom login page plugins
   - Test with default WordPress login

**On Registration Form:**

1. **Verify registration is enabled:**
   - Go to **Settings → General**
   - Check "Anyone can register" is enabled

2. **Follow same steps as Login Form above**

**On Comment Form:**

1. **Verify comments are enabled:**
   - Go to **Settings → Discussion**
   - Check "Allow people to submit comments" is enabled

2. **Check post/page settings:**
   - Individual posts can disable comments
   - Verify comments are enabled on the specific post

3. **Theme compatibility:**
   - Some custom themes override comment form
   - May need theme support

---

### Problem: CAPTCHA Shows But Validation Fails

**Symptoms:**
- CAPTCHA widget appears
- You complete it correctly
- Form still says "CAPTCHA validation failed"

**Solutions:**

1. **Check API Keys:**
   - Go to **Settings → Wbcom CAPTCHA Manager**
   - Verify Site Key and Secret Key are correct
   - Try regenerating keys in your CAPTCHA service dashboard

2. **Check Domain:**
   - In your CAPTCHA service dashboard (reCAPTCHA, hCaptcha, etc.)
   - Verify your domain is registered
   - Include both www and non-www versions

3. **Server Connection:**
   - Your server must be able to connect to CAPTCHA API
   - Check firewall settings
   - Contact hosting support if needed

4. **Time Synchronization:**
   - Ensure server time is accurate
   - CAPTCHA verification checks timestamps

---

### Problem: Too Many Failed Login Attempts

Even with CAPTCHA, users can't log in:

**Possible Causes:**

1. **Login Security Plugin:**
   - You may have a plugin like "Limit Login Attempts"
   - It might be blocking before CAPTCHA
   - Check that plugin's settings

2. **Firewall Rules:**
   - Server firewall may be blocking
   - Check with hosting support

3. **CAPTCHA Too Strict:**
   - If using reCAPTCHA v3, lower the threshold
   - Try switching to reCAPTCHA v2 for login

---

### Problem: Comment Spam Still Getting Through

CAPTCHA is enabled but spam comments still appear:

**Solutions:**

1. **Verify CAPTCHA is Actually Showing:**
   - Log out and view comment form
   - Confirm CAPTCHA widget is visible
   - Test submitting without CAPTCHA

2. **Check for Akismet:**
   - CAPTCHA prevents automated bots
   - Akismet catches manual spam
   - Use both for best results

3. **Enable Comment Moderation:**
   - Go to **Settings → Discussion**
   - Enable "Comment must be manually approved"
   - Require email and name

4. **Close Old Post Comments:**
   - Go to **Settings → Discussion**
   - Enable "Automatically close comments on posts older than X days"
   - Spam often targets old posts

---

## 🚀 Best Practices

### 1. Always Protect Login Form

**Why:** This is the #1 target for brute force attacks.

**How:**
- Enable Login Form protection
- Use strong admin passwords
- Consider two-factor authentication

---

### 2. Balance Security and User Experience

**High Security** (more strict):
- reCAPTCHA v2 on all forms
- Show CAPTCHA to all users (even logged-in)

**Balanced** (recommended):
- reCAPTCHA v3 or Turnstile on login/registration
- reCAPTCHA v2 on comments
- Skip CAPTCHA for logged-in users on comments

**User-Friendly** (less strict):
- Turnstile on all forms (invisible)
- Only show CAPTCHA on registration and lost password
- Skip CAPTCHA for logged-in users everywhere

---

### 3. Combine with Other Security Measures

CAPTCHA alone is not enough:

**Additional Security:**
- ✅ Strong passwords
- ✅ Two-factor authentication
- ✅ Login attempt limiting
- ✅ Security plugins (Wordfence, Sucuri)
- ✅ Regular WordPress updates
- ✅ Secure hosting

---

### 4. Monitor Your Forms

**Weekly Checks:**
- Review spam comment queue
- Check new user registrations
- Look for unusual login attempts
- Review security logs

**Monthly:**
- Review CAPTCHA dashboard (reCAPTCHA, hCaptcha, Turnstile)
- Check blocked attempts
- Adjust settings if needed

---

### 5. Test After Theme/Plugin Updates

After major updates:
- Test login form with CAPTCHA
- Test comment form
- Verify CAPTCHA still appears correctly
- Check that validation works

---

## 📊 Recommended Settings by Site Type

### Personal Blog

**Low traffic, minimal risk:**
```
☑ Comment Form
☐ Login Form (optional)
☐ Registration Form (if closed registration)
☑ Lost Password Form
```

**Recommended CAPTCHA:** Turnstile or reCAPTCHA v3

---

### Business Website

**Medium traffic, moderate risk:**
```
☑ Login Form
☑ Comment Form
☐ Registration Form (if closed registration)
☑ Lost Password Form
```

**Recommended CAPTCHA:** reCAPTCHA v3 or Turnstile

---

### Community Site / Forum

**High traffic, open registration:**
```
☑ Login Form
☑ Registration Form ← Critical!
☑ Comment Form
☑ Lost Password Form
```

**Recommended CAPTCHA:** reCAPTCHA v2 (visible security) or hCaptcha

---

### E-commerce / Membership Site

**High value, high risk:**
```
☑ Login Form ← Essential!
☑ Registration Form ← Essential!
☑ Comment Form
☑ Lost Password Form ← Essential!
```

**Recommended CAPTCHA:** reCAPTCHA v2 for registration/login, v3 for comments

---

## 🔒 Security Considerations

### Login Form Protection is Critical

**Without CAPTCHA:**
- Bots can attempt thousands of passwords per hour
- Eventually they may find weak passwords
- High server load from attacks

**With CAPTCHA:**
- Automated attacks stop immediately
- Only legitimate users can attempt login
- Server load reduced dramatically

---

### Registration Spam Can Be Dangerous

**Why it matters:**
- Spam accounts can post spam content
- SEO penalties for spam
- Database bloat
- Email spam from fake accounts

**Solution:**
- Always use CAPTCHA on registration
- Enable email verification
- Moderate new registrations

---

### Comment Spam Affects SEO

**Problems with spam comments:**
- Google may penalize your site
- Bad user experience
- Links to malicious sites
- Time wasted moderating

**Solution:**
- CAPTCHA + Akismet = Best defense
- Close comments on old posts
- Require manual approval

---

## 🌍 Multilingual Support

WordPress Core forms work with multilingual sites:

**WPML / Polylang:**
- CAPTCHA works on all language versions
- Widget appears in user's language automatically
- No special configuration needed

**Translation:**
- Error messages can be translated
- Use .po/.mo files in plugin's `/languages/` folder
- Or use translation plugins

---

## ♿ Accessibility

All CAPTCHA services provide accessible alternatives:

**reCAPTCHA:**
- Audio challenges for visually impaired
- Keyboard navigation
- Screen reader support

**hCaptcha:**
- Audio alternatives
- High contrast mode
- Keyboard accessible

**Turnstile:**
- Usually invisible (no interaction needed)
- Fallback challenges are accessible

**ALTCHA:**
- No visual puzzle required
- Screen reader friendly
- Keyboard accessible

---

## 📚 Related Guides

**CAPTCHA Service Setup:**
- [reCAPTCHA v2 Setup](../captcha-services/recaptcha-v2.md)
- [reCAPTCHA v3 Setup](../captcha-services/recaptcha-v3.md)
- [hCaptcha Setup](../captcha-services/hcaptcha.md)
- [Turnstile Setup](../captcha-services/turnstile.md)
- [ALTCHA Setup](../captcha-services/altcha.md)

**Other Integrations:**
- [BuddyPress Forms](buddypress.md)
- [WooCommerce Forms](woocommerce.md)
- [Contact Form 7](contact-form-7.md)

---

## 🔄 Next Steps

1. **[Choose Your CAPTCHA Service](../captcha-services/README.md)** if you haven't already
2. **Enable protection** on WordPress core forms
3. **Test thoroughly** to ensure everything works
4. **Explore other integrations** for additional protection

---

**Need Help?** Check our [FAQ section](../README.md#frequently-asked-questions) or [contact support](../README.md#getting-help).
