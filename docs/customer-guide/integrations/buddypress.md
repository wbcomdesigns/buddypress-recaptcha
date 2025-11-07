# BuddyPress Integration

Protect your BuddyPress community from spam members, fake groups, and bot activity with CAPTCHA protection.

## 📋 Overview

This guide covers CAPTCHA protection for:

- **Member Registration** - Stop spam accounts
- **Group Creation** - Prevent spam groups
- **Additional BuddyPress Forms** - Future integrations

---

## 🛡️ Supported Forms

### 1. Member Registration Form

**Location:** `/register` (BuddyPress registration page)

**Why Protect:**
- Prevents bot registrations
- Stops fake member accounts
- Protects your community from spam
- Maintains member quality

**Recommendation:** ✅ **Always enable this** for open communities

**Note:** This replaces WordPress core registration when BuddyPress is active.

---

### 2. Group Creation Form

**Location:** `/groups/create` (when creating new groups)

**Why Protect:**
- Prevents spam group creation
- Stops automated group spam
- Maintains group directory quality
- Reduces moderation workload

**Recommendation:** ✅ Enable if any members can create groups

**Note:** Only relevant if group creation is enabled in BuddyPress settings.

---

## ⚙️ Setup Instructions

### Prerequisites

**Before You Begin:**

1. **Install BuddyPress:**
   - BuddyPress plugin must be installed and active
   - Download from [WordPress.org](https://wordpress.org/plugins/buddypress/)

2. **Configure CAPTCHA Service:**
   - Set up your preferred CAPTCHA service
   - Get API keys if required
   - See our [CAPTCHA service guides](../captcha-services/README.md)

---

### Step 1: Enable BuddyPress in Your CAPTCHA Settings

1. Go to **Settings → Wbcom CAPTCHA Manager**
2. Ensure your CAPTCHA service is configured with valid keys
3. Save settings if you made changes

---

### Step 2: Enable BuddyPress Form Protection

1. In **Settings → Wbcom CAPTCHA Manager**
2. Scroll to **"BuddyPress Forms"** section
3. Check the boxes for forms you want to protect:

**Recommended Settings:**
```
☑ BuddyPress Registration Form
☑ Group Creation Form (if groups are enabled)
```

4. Click **"Save Changes"**

---

### Step 3: Configure BuddyPress Settings

**Enable Registration (if not already enabled):**

1. Go to **Settings → BuddyPress → Options**
2. Make sure member registration is enabled
3. Save changes

**Group Creation Settings:**

1. Go to **Settings → BuddyPress → Options**
2. Under "Groups" tab
3. Choose who can create groups:
   - All members
   - Site admins only
4. If "All members", CAPTCHA on group creation is recommended

---

### Step 4: Test Your Protection

#### Test Registration Form:

1. **Log out** of WordPress
2. **Visit registration page** (usually `/register`)
3. **Verify CAPTCHA appears:**
   - Should see CAPTCHA widget on the form
   - Position depends on theme (usually at bottom)

4. **Test without CAPTCHA:**
   - Fill out registration form
   - Don't complete CAPTCHA
   - Try to submit
   - **Expected:** Error message "Please complete the CAPTCHA"

5. **Test with CAPTCHA:**
   - Fill out registration form
   - Complete CAPTCHA challenge
   - Submit form
   - **Expected:** Registration successful
   - Delete test account after verification

#### Test Group Creation:

1. **Log in** as a regular member
2. **Go to groups** → **Create a Group**
3. **Fill out group details**
4. **On the final step**, verify CAPTCHA appears
5. **Test submission:**
   - Without CAPTCHA: Should show error
   - With CAPTCHA: Should create group successfully
6. Delete test group after verification

---

## 🎨 Customization

### CAPTCHA Position on Registration Form

The default position works with most BuddyPress themes, but you can customize:

**Move CAPTCHA to different position:**

```php
// Add to your theme's functions.php
add_filter( 'wbc_bp_register_captcha_position', function() {
    // Options: 'before_submit', 'after_submit', 'before_profile_fields'
    return 'before_submit';
});
```

---

### Custom Error Messages

Customize error messages for BuddyPress forms:

```php
// Custom registration error message
add_filter( 'wbc_bp_register_error_message', function( $message ) {
    return 'Please verify you are human to join our community.';
});

// Custom group creation error message
add_filter( 'wbc_bp_group_create_error_message', function( $message ) {
    return 'Please complete the CAPTCHA to create your group.';
});
```

---

### Skip CAPTCHA for Invited Members

If you use BuddyPress invitations, skip CAPTCHA for invited members:

```php
// Skip CAPTCHA if user was invited
add_filter( 'wbc_bp_register_skip_invited', '__return_true' );
```

---

### Different CAPTCHA for Group Creation

Use different settings for group creation vs registration:

```php
// More strict CAPTCHA for group creation
add_filter( 'wbc_bp_group_captcha_threshold', function( $threshold ) {
    return 0.7; // Stricter (only for reCAPTCHA v3)
});
```

---

## 🔧 Troubleshooting

### Problem: CAPTCHA Not Appearing on Registration

**Possible Causes:**

1. **BuddyPress Not Active:**
   - Go to **Plugins**
   - Verify BuddyPress is activated
   - Check for "BuddyPress" in active plugins

2. **Registration Not Enabled:**
   - Go to **Settings → BuddyPress → Options**
   - Make sure registration is enabled

3. **Theme Conflict:**
   - Some custom BuddyPress themes override registration
   - Try switching to a default theme temporarily
   - Contact theme developer if issue persists

4. **Plugin Settings:**
   - Go to **Settings → Wbcom CAPTCHA Manager**
   - Verify "BuddyPress Registration Form" is checked
   - Verify CAPTCHA service is configured

5. **Cache Issue:**
   - Clear WordPress cache
   - Clear browser cache
   - Disable cache plugins temporarily

---

### Problem: CAPTCHA Not Appearing on Group Creation

**Possible Causes:**

1. **Groups Not Enabled:**
   - Go to **Settings → BuddyPress → Components**
   - Verify "User Groups" component is active

2. **Group Creation Restricted:**
   - Go to **Settings → BuddyPress → Options → Groups**
   - Check who can create groups
   - If "Site admins only", CAPTCHA won't show (not needed)

3. **Multi-Step Form:**
   - BuddyPress group creation is multi-step
   - CAPTCHA appears on the last step (Review & Submit)
   - Make sure you're checking the final step

4. **Plugin Settings:**
   - Verify "Group Creation Form" is checked in plugin settings

---

### Problem: Registration Works Without CAPTCHA

**Debugging Steps:**

1. **Check Plugin Priority:**
   - Another plugin might be interfering
   - Temporarily disable other registration plugins

2. **Check Theme:**
   - Theme might have custom registration template
   - Try default WordPress theme

3. **Check Error Messages:**
   - Look for JavaScript errors in browser console (F12)
   - Check WordPress debug.log

4. **Verify Installation:**
   - Make sure plugin is properly installed
   - Try deactivating and reactivating

---

### Problem: CAPTCHA Validation Fails

**Symptoms:**
- CAPTCHA widget appears
- User completes it correctly
- Still shows "CAPTCHA verification failed"

**Solutions:**

1. **Check API Keys:**
   - Go to plugin settings
   - Verify Site Key and Secret Key are correct
   - Make sure you're using keys for the correct CAPTCHA service

2. **Check Domain Registration:**
   - In your CAPTCHA service dashboard
   - Verify your domain is registered
   - Include both www and non-www versions

3. **Server Connection:**
   - Server must reach CAPTCHA API
   - Check firewall settings
   - Contact hosting support

4. **SSL Certificate:**
   - Make sure your SSL is valid
   - Some CAPTCHA services require HTTPS

---

### Problem: Spam Still Getting Through

**If spam registrations still occur:**

1. **Verify CAPTCHA is Working:**
   - Test registration yourself
   - Confirm CAPTCHA is required
   - Check that validation actually blocks submission

2. **Check CAPTCHA Strength:**
   - reCAPTCHA v3: Increase threshold (0.5 → 0.7)
   - Consider switching to reCAPTCHA v2 (visible challenges)

3. **Enable Email Activation:**
   - Go to **Settings → BuddyPress → Options**
   - Enable "User email activation"
   - Requires email verification after registration

4. **Manual Approval:**
   - Use a membership plugin for manual approval
   - Combine CAPTCHA with human moderation

5. **Additional Security:**
   - Install security plugins
   - Use IP blocking
   - Monitor new registrations daily

---

## 🚀 Best Practices

### 1. Always Protect Registration for Public Communities

**Why:**
- Public communities are prime targets for spam
- Spam accounts can damage your reputation
- Cleanup is time-consuming

**How:**
- ✅ Enable CAPTCHA on registration
- ✅ Enable email activation
- ✅ Moderate first posts/activities
- ✅ Set up spam reporting

---

### 2. Protect Group Creation if Enabled

**Why:**
- Spam groups clutter group directory
- Can contain inappropriate content
- Hard to detect and remove

**How:**
- ✅ Enable CAPTCHA on group creation
- ✅ Require group moderation
- ✅ Limit who can create groups
- ✅ Monitor new groups

---

### 3. Choose Right CAPTCHA for Your Community

**Large Public Community:**
- Use reCAPTCHA v2 (visible security)
- Or hCaptcha (privacy-focused)
- More strict = fewer spam accounts

**Invite-Only Community:**
- Use Turnstile or reCAPTCHA v3 (invisible)
- Better user experience
- Lower spam risk anyway

**Privacy-Focused Community:**
- Use ALTCHA or Turnstile
- Maximum privacy compliance
- Good for EU communities

---

### 4. Combine Multiple Protection Methods

**Layered Security (Best Approach):**

1. **CAPTCHA** - Stops automated bots
2. **Email Activation** - Confirms valid email
3. **Moderation** - Human review of suspicious accounts
4. **Activity Monitoring** - Flag unusual behavior
5. **Spam Reporting** - Community helps moderate

**Configuration:**
- CAPTCHA: Always enabled
- Email Activation: Enabled in BP settings
- First Activity: Set to pending approval
- Community: Enable member reporting

---

### 5. Monitor New Registrations

**Daily (High-Activity Sites):**
- Review new member list
- Check for suspicious usernames
- Look for patterns (same email domain, etc.)
- Remove spam accounts immediately

**Weekly (Low-Activity Sites):**
- Review past week's registrations
- Check for inactive spam accounts
- Update security settings if needed

---

## 📊 Recommended Settings by Community Type

### Small Private Community

**Low risk, known members:**
```
☑ BuddyPress Registration (if open)
☐ Group Creation (members are trusted)
```
**CAPTCHA:** Turnstile or reCAPTCHA v3
**Extra:** Email activation enabled

---

### Medium Public Community

**Growing community, moderate risk:**
```
☑ BuddyPress Registration ← Essential
☑ Group Creation ← Recommended
```
**CAPTCHA:** reCAPTCHA v3 or hCaptcha
**Extra:** Email activation + first post moderation

---

### Large Public Community

**High traffic, high spam risk:**
```
☑ BuddyPress Registration ← Critical!
☑ Group Creation ← Critical!
```
**CAPTCHA:** reCAPTCHA v2 (visible) or hCaptcha
**Extra:** Email activation + moderation + security plugins

---

### Membership/Paid Community

**Premium members, low tolerance for spam:**
```
☑ BuddyPress Registration ← Essential
☑ Group Creation ← Essential
```
**CAPTCHA:** reCAPTCHA v2 or hCaptcha (visible security)
**Extra:** Manual approval + email activation

---

## 🔒 Security Considerations

### Registration Spam Can Destroy Communities

**Problems:**
- Spam profiles in member directory
- Spam in activity streams
- Spam private messages to members
- Database bloat
- SEO penalties

**Solution:**
- CAPTCHA is your first line of defense
- Must be combined with other methods
- Regular monitoring is essential

---

### Fake Groups Hurt Engagement

**Problems:**
- Real groups get buried
- Members lose trust
- Cleanup is manual and time-consuming
- Can contain illegal content

**Solution:**
- CAPTCHA on group creation
- Moderate new groups
- Limit group creation to trusted members
- Enable group reporting

---

## 🌍 Multilingual Communities

BuddyPress works great with multilingual plugins:

**WPML:**
- CAPTCHA works on all language versions
- Automatically detects user language
- Error messages can be translated

**Polylang:**
- Full compatibility
- Works on translated registration pages

**Translation:**
- Plugin is translation-ready
- Use .po/.mo files for custom translations
- Or translation plugins like Loco Translate

---

## ♿ Accessibility

CAPTCHA services provide accessible alternatives for BuddyPress forms:

**For Visually Impaired:**
- reCAPTCHA: Audio challenges
- hCaptcha: Audio challenges
- Turnstile: Usually no challenge (invisible)
- ALTCHA: Screen reader friendly

**Keyboard Navigation:**
- All CAPTCHA widgets support keyboard
- Tab through form fields
- No mouse required

**Screen Readers:**
- ARIA labels on all elements
- Status announcements
- Clear error messages

---

## 🔗 Integration with Other Plugins

### BuddyPress Profile Pro

CAPTCHA works with custom registration fields:
- Profile fields + CAPTCHA
- Custom field validation works normally
- CAPTCHA validates after field validation

### BuddyBoss Platform

Full compatibility:
- Works with BuddyBoss registration
- Works with BuddyBoss groups
- Same setup process

### Paid Memberships Pro

Combine with membership:
- CAPTCHA on free registration
- Optional: Skip CAPTCHA for paid tiers
- Reduces spam in trial accounts

---

## 📚 Related Guides

**CAPTCHA Service Setup:**
- [reCAPTCHA v2 Setup](../captcha-services/recaptcha-v2.md) - Best for high-security communities
- [hCaptcha Setup](../captcha-services/hcaptcha.md) - Privacy-focused alternative
- [Turnstile Setup](../captcha-services/turnstile.md) - Modern, invisible protection

**Other Integrations:**
- [WordPress Core Forms](wordpress-core.md) - Login, comments
- [bbPress](bbpress.md) - Forum protection
- [WooCommerce](woocommerce.md) - E-commerce forms

---

## 🔄 Next Steps

1. **[Set up your CAPTCHA service](../captcha-services/README.md)** if you haven't already
2. **Enable BuddyPress form protection** following this guide
3. **Test thoroughly** with test accounts
4. **Enable additional security** (email activation, moderation)
5. **Monitor registrations** regularly

---

**Need Help?** Check our [FAQ section](../README.md#frequently-asked-questions) or [contact support](../README.md#getting-help).
