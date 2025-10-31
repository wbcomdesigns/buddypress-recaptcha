# MemberPress Integration

Protect your membership site from spam registrations and fake accounts with CAPTCHA on MemberPress login and registration forms.

## 📋 Overview

Protects MemberPress forms:
- **Registration Form** - Stop spam member accounts
- **Login Form** - Secure member account access

---

## 🛡️ Why Protect MemberPress

**Without CAPTCHA:**
- Spam membership signups
- Fake trial account abuse
- Bot registrations
- Unauthorized access attempts
- Database bloat

**With CAPTCHA:**
- Only genuine members
- Protected membership tiers
- Clean member database
- Reduced fraud

**Recommendation:** ✅ Essential for open membership sites

---

## ⚙️ Quick Setup

### Step 1: Prerequisites

1. **Install MemberPress** (premium plugin)
2. **Configure CAPTCHA** - [Service guides](../captcha-services/README.md)

### Step 2: Enable Protection

1. **Settings → Wbcom CAPTCHA Manager**
2. Configure CAPTCHA service
3. Scroll to **"MemberPress"** section
4. Check forms:

```
☑ Registration Form ← Essential
☑ Login Form
```

5. **Save Changes**

### Step 3: Test

1. **Test Registration:**
   - Go to membership registration page
   - Verify CAPTCHA appears
   - Test signup with and without CAPTCHA

2. **Test Login:**
   - Log out
   - Try login
   - Verify CAPTCHA required

---

## 🎨 Customization

### Skip Login CAPTCHA for Members

Don't show CAPTCHA to already-logged-in users:

```php
// This is default behavior - no code needed
// To show CAPTCHA even for logged-in users:
add_filter( 'wbc_memberpress_show_login_for_logged_in', '__return_true' );
```

---

### Custom Error Messages

```php
// Registration error
add_filter( 'wbc_memberpress_register_error_message', function( $message ) {
    return 'Please verify you are human to join our membership.';
});

// Login error
add_filter( 'wbc_memberpress_login_error_message', function( $message ) {
    return 'Please complete the security check to log in.';
});
```

---

### Different CAPTCHA by Membership Level

More strict for free trials:

```php
add_filter( 'wbc_memberpress_captcha_threshold', function( $threshold, $membership_id ) {
    // Free tier - stricter
    if ( $membership_id == 123 ) {
        return 0.7; // More strict (reCAPTCHA v3 only)
    }
    return $threshold;
}, 10, 2 );
```

---

## 🔧 Troubleshooting

### CAPTCHA Not Appearing

**Check:**
1. MemberPress is active and licensed
2. Plugin settings enabled for both forms
3. Using MemberPress registration (not WP default)
4. CAPTCHA service configured
5. Clear all caches

---

### Registration Blocked

**Problem:** Legitimate users can't register

**Solutions:**
1. Verify API keys correct
2. Check domain in CAPTCHA service
3. Lower threshold (if reCAPTCHA v3)
4. Try different CAPTCHA service
5. Test in incognito mode

---

### Login Issues

**Problem:** Members can't log in

**Solutions:**
1. Verify CAPTCHA validates before login
2. Check for JavaScript errors
3. Test with different browsers
4. Temporarily disable to isolate issue

---

### Trial Abuse Still Happening

**Problem:** Fake trial signups continue

**Solutions:**
1. Switch to reCAPTCHA v2 (visible, stronger)
2. Increase threshold (if using v3)
3. Enable email verification
4. Require payment method for trials
5. Manual approval for free tiers

---

## 🚀 Best Practices

### 1. Always Protect Free Tiers

**Free memberships attract spam:**
- Bots target free trials
- Spam accounts access content
- Database fills with fake users

**Must-do:**
```
☑ Registration Form ← Essential for free tiers
```

---

### 2. Choose Right CAPTCHA

**Free/Trial Memberships:**
- reCAPTCHA v2 (visible, stronger)
- hCaptcha (visible challenges)
- Shows security to paying members

**Paid Memberships:**
- Turnstile (invisible, professional)
- reCAPTCHA v3 (seamless)
- Better UX for paying customers

**Our Recommendation:**
- Free tiers: reCAPTCHA v2
- Paid tiers: Turnstile or reCAPTCHA v3

---

### 3. Combine with Other Protection

**Layered Security:**
1. CAPTCHA (stops bots)
2. Email verification (confirms valid email)
3. Payment verification (requires valid card)
4. Manual approval (for suspicious accounts)

---

### 4. Monitor Member Signups

**Daily checks:**
- Review new member list
- Check for suspicious usernames
- Look for patterns (same email domain, etc.)
- Remove spam accounts quickly

---

## 📊 Recommended Settings by Membership Type

### Free Content Site
```
☑ Registration Form ← Essential
☑ Login Form ← Recommended
```
**CAPTCHA:** reCAPTCHA v2 (visible)
**Extra:** Email verification

---

### Free Trial + Paid
```
☑ Registration Form ← Critical
☑ Login Form
```
**CAPTCHA:** reCAPTCHA v2 for registration, v3 for login
**Extra:** Require payment method for trial

---

### Fully Paid Memberships
```
☑ Registration Form
☐ Login Form (optional - payment validates users)
```
**CAPTCHA:** Turnstile or reCAPTCHA v3 (professional)
**Extra:** Payment gateway fraud detection

---

### Course/LMS Membership
```
☑ Registration Form ← Essential
☑ Login Form
```
**CAPTCHA:** reCAPTCHA v2 or hCaptcha
**Extra:** Email verification + manual approval

---

## 🔒 Security Considerations

### Free Trials are Prime Targets

**Why spammers love free trials:**
- Access to premium content
- Test account functionality
- Resell access to others
- Data scraping

**Protection stack:**
1. CAPTCHA on registration
2. Email verification
3. Require payment method (even for $0)
4. Limit free trial duration
5. Monitor for abuse patterns

---

### Member Login Security

**Risks:**
- Brute force attacks
- Credential stuffing
- Account takeovers

**CAPTCHA helps:**
- Blocks automated login attempts
- Slows down attacks
- Protects member accounts

**Also use:**
- Strong password requirements
- Two-factor authentication
- Login attempt limiting

---

## ♿ Accessibility

**MemberPress Accessibility:**
- Forms are accessible
- CAPTCHA maintains accessibility

**Recommended:**
- Turnstile (invisible, most accessible)
- reCAPTCHA v2/v3 with audio alternatives

---

## 🔗 Compatible Features

Works with all MemberPress features:
- ✅ Multiple membership levels
- ✅ Free trials
- ✅ Coupons
- ✅ Corporate accounts
- ✅ Group memberships
- ✅ Drip content
- ✅ Payment gateways (Stripe, PayPal, etc.)

---

## 🌍 Multilingual Memberships

Compatible with:
- **WPML** - Full support
- **Polylang** - Full support
- CAPTCHA detects user language

---

## 📈 Impact on Signups

### Measuring CAPTCHA Impact

**Track:**
- Registration completion rate
- Spam signup count
- Member engagement (real vs fake)
- Support tickets about access

**Expected with invisible CAPTCHA:**
- Signup rate: No change
- Spam accounts: 90%+ reduction
- Engaged members: Increase (less dilution)

---

## 📚 Related Guides

**CAPTCHA Services:**
- [reCAPTCHA v2](../captcha-services/recaptcha-v2.md) - For free tiers
- [Turnstile](../captcha-services/turnstile.md) - For paid memberships
- [hCaptcha](../captcha-services/hcaptcha.md) - Privacy-focused

**Similar Integrations:**
- [Ultimate Member](ultimate-member.md) - Alternative membership
- [BuddyPress](buddypress.md) - Community features
- [Easy Digital Downloads](easy-digital-downloads.md) - Digital sales

---

## 🔄 Next Steps

1. [Choose CAPTCHA](../captcha-services/README.md) - reCAPTCHA v2 for free, Turnstile for paid
2. Enable MemberPress protection on registration
3. Test complete signup flow
4. Add email verification
5. Monitor member quality

---

**Need Help?** [FAQ](../README.md#frequently-asked-questions) | [Support](../README.md#getting-help)
