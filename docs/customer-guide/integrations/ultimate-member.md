# Ultimate Member Integration

Protect your Ultimate Member community from spam registrations and unauthorized access with CAPTCHA on login and registration forms.

## 📋 Overview

Protects Ultimate Member forms:
- **Registration Form** - Stop spam member accounts
- **Login Form** - Secure member access

---

## 🛡️ Why Protect Ultimate Member

**Without CAPTCHA:**
- Spam member registrations
- Fake profile creation
- Bot account generation
- Brute force login attempts
- Community quality degradation

**With CAPTCHA:**
- Only genuine members
- Quality community
- Protected member data
- Reduced moderation workload

**Recommendation:** ✅ Essential for public communities

---

## ⚙️ Quick Setup

### Step 1: Prerequisites

1. **Install Ultimate Member** - Free from [WordPress.org](https://wordpress.org/plugins/ultimate-member/)
2. **Configure CAPTCHA** - [Service guides](../captcha-services/README.md)

### Step 2: Enable Protection

1. **Settings → Wbcom CAPTCHA Manager**
2. Configure CAPTCHA service
3. Scroll to **"Ultimate Member"** section
4. Check forms:

```
☑ Registration Form ← Essential
☑ Login Form
```

5. **Save Changes**

### Step 3: Test

1. **Test Registration:**
   - Go to UM registration page
   - Verify CAPTCHA appears
   - Test with and without CAPTCHA

2. **Test Login:**
   - Log out
   - Visit login page
   - Verify CAPTCHA required

---

## 🎨 Customization

### Custom Error Messages

```php
// Registration error
add_filter( 'wbc_um_register_error_message', function( $message ) {
    return 'Please verify you are human to join our community.';
});

// Login error
add_filter( 'wbc_um_login_error_message', function( $message ) {
    return 'Please complete the security check to access your account.';
});
```

---

### Skip CAPTCHA for Specific Registration Forms

If you have multiple UM registration forms:

```php
add_filter( 'wbc_um_exclude_forms', function( $excluded ) {
    $excluded[] = 123; // Form ID
    return $excluded;
});
```

---

### CAPTCHA Position

```php
add_filter( 'wbc_um_captcha_position', function() {
    return 'before_submit'; // or 'after_fields'
});
```

---

## 🔧 Troubleshooting

### CAPTCHA Not Appearing

**Check:**
1. Ultimate Member is active
2. Using UM forms (not WP default login/registration)
3. Plugin settings enabled
4. CAPTCHA service configured
5. Clear UM cache and page cache

---

### Registration Issues

**Problem:** Users can't register

**Solutions:**
1. Verify API keys correct
2. Check domain registered in CAPTCHA dashboard
3. Test UM registration without other plugins
4. Check for JavaScript errors (F12)
5. Verify UM form settings

---

### Login Problems

**Problem:** Members can't log in

**Solutions:**
1. Test CAPTCHA validation separately
2. Check for theme conflicts
3. Try default WordPress theme
4. Temporarily disable to isolate issue
5. Check UM login settings

---

### Multiple Forms Conflict

**Problem:** CAPTCHA appears on wrong UM form

**Solutions:**
1. UM allows multiple registration/login forms
2. Use exclude filter for specific forms
3. Test each form separately
4. Check form IDs in UM settings

---

## 🚀 Best Practices

### 1. Always Protect Public Registration

**Public communities are spam targets:**
- Open registration invites bots
- Spam profiles damage community
- Fake accounts clutter member directory

**Must-do:**
```
☑ Registration Form ← Essential
```

---

### 2. Choose Right CAPTCHA for Community

**Large Public Community:**
- reCAPTCHA v2 (visible, stronger)
- Shows members you take security seriously

**Professional Community:**
- Turnstile (invisible, professional)
- reCAPTCHA v3 (seamless)

**Privacy-Focused Community:**
- hCaptcha (privacy-respecting)
- ALTCHA (maximum privacy)

---

### 3. Combine with UM Features

**Comprehensive Protection:**
1. CAPTCHA (stops bots)
2. Email activation (UM built-in)
3. Admin approval (for sensitive communities)
4. Profile completion requirements
5. Minimum account age for posting

---

### 4. Monitor New Members

**Daily (High-Activity):**
- Review new member list
- Check for spam usernames/profiles
- Remove fake accounts immediately
- Look for patterns

**Weekly (Small Communities):**
- Review past week's registrations
- Check member engagement
- Adjust security if needed

---

## 📊 Recommended Settings

### Public Community
```
☑ Registration Form ← Critical
☑ Login Form ← Recommended
```
**CAPTCHA:** reCAPTCHA v2 or hCaptcha
**Extra:** Email activation + profile moderation

---

### Professional Network
```
☑ Registration Form ← Essential
☑ Login Form
```
**CAPTCHA:** Turnstile (invisible, professional)
**Extra:** Email activation + manual approval

---

### Membership Site
```
☑ Registration Form ← Essential
☐ Login Form (optional if paid)
```
**CAPTCHA:** reCAPTCHA v3 or Turnstile
**Extra:** Payment verification

---

### Private Community
```
☑ Registration Form ← Critical
☑ Login Form ← Essential
```
**CAPTCHA:** reCAPTCHA v2 (visible security)
**Extra:** Invitation-only + manual approval

---

## 🔒 Security Considerations

### Profile Spam is Serious

**Problems:**
- Spam user profiles in directory
- Fake profile pictures and bios
- Links to spam/malicious sites
- Degraded member experience
- SEO penalties

**Protection:**
1. CAPTCHA on registration
2. Profile field validation
3. Profile moderation
4. Limit profile editing for new users
5. Report/flag system

---

### Member Directory Protection

**UM creates public member directories:**
- Indexed by search engines
- Visible to visitors
- Spam profiles appear here

**Essential:**
- CAPTCHA prevents spam profiles
- Moderate new profiles
- Hide directory until profiles verified
- Use "noindex" for unverified profiles

---

## ♿ Accessibility

**UM is Accessibility-Ready:**
- Forms are WCAG compliant
- CAPTCHA maintains accessibility

**Recommended:**
- Turnstile (invisible, most accessible)
- reCAPTCHA with audio alternatives

---

## 🔗 Compatible Features

Works with all UM features:
- ✅ Multiple registration/login forms
- ✅ Custom profile fields
- ✅ User roles
- ✅ Profile privacy settings
- ✅ Member directories
- ✅ Social connect
- ✅ UM extensions (all)

---

## 🌍 Multilingual Communities

Compatible with:
- **WPML** - Full support
- **Polylang** - Full support
- UM translations work with CAPTCHA

---

## 📈 Community Quality Impact

### Metrics to Track

**Before CAPTCHA:**
- Total new registrations
- Spam account percentage
- Moderation time spent
- Community engagement

**After CAPTCHA:**
- Real member increase
- Spam reduction (expect 95%+)
- Less moderation time
- Better engagement (real members only)

---

## 📚 Related Guides

**CAPTCHA Services:**
- [reCAPTCHA v2](../captcha-services/recaptcha-v2.md) - For public communities
- [Turnstile](../captcha-services/turnstile.md) - Professional option
- [hCaptcha](../captcha-services/hcaptcha.md) - Privacy-focused

**Similar Integrations:**
- [BuddyPress](buddypress.md) - Alternative community plugin
- [MemberPress](memberpress.md) - Membership management
- [bbPress](bbpress.md) - Forum integration

**Core Protection:**
- [WordPress Core](wordpress-core.md) - Login/registration
- [WooCommerce](woocommerce.md) - If selling memberships

---

## 🔄 Next Steps

1. [Choose CAPTCHA](../captcha-services/README.md) - reCAPTCHA v2 or Turnstile recommended
2. Enable UM registration protection (essential)
3. Test complete registration flow
4. Enable email activation in UM
5. Set up profile moderation
6. Monitor community quality

---

**Need Help?** [FAQ](../README.md#frequently-asked-questions) | [Support](../README.md#getting-help)
