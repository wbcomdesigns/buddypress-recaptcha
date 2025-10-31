# bbPress Integration

Protect your bbPress forums from spam topics, spam replies, and bot activity with CAPTCHA protection.

## 📋 Overview

This guide covers CAPTCHA protection for:

- **New Topic Form** - Stop spam forum topics
- **Reply Form** - Prevent spam replies
- **Forum Registration** - Block spam forum accounts (if separate from WordPress)

---

## 🛡️ Supported Forms

### 1. New Topic Form

**Location:** Forum pages where users can create new topics

**Why Protect:**
- Prevents spam topic creation
- Stops automated forum spam
- Maintains forum quality
- Reduces moderation workload
- Protects forum from SEO spam

**Recommendation:** ✅ **Always enable** for public forums

---

### 2. Reply Form

**Location:** Topic pages where users can post replies

**Why Protect:**
- Blocks spam replies
- Prevents bot conversations
- Keeps discussions genuine
- Reduces cleanup time

**Recommendation:** ✅ Enable for public forums, optional for private forums

**Note:** You may choose to skip CAPTCHA for logged-in users on replies (they're already verified).

---

## ⚙️ Setup Instructions

### Prerequisites

**Before You Begin:**

1. **Install bbPress:**
   - bbPress plugin must be installed and active
   - Download from [WordPress.org](https://wordpress.org/plugins/bbpress/)

2. **Configure CAPTCHA Service:**
   - Set up your preferred CAPTCHA service
   - Get API keys if required
   - See our [CAPTCHA service guides](../captcha-services/README.md)

---

### Step 1: Enable bbPress in CAPTCHA Settings

1. Go to **Settings → Wbcom CAPTCHA Manager**
2. Ensure your CAPTCHA service is configured with valid keys
3. Save settings if you made changes

---

### Step 2: Enable bbPress Form Protection

1. In **Settings → Wbcom CAPTCHA Manager**
2. Scroll to **"bbPress Forms"** section
3. Check the boxes for forms you want to protect:

**Recommended Settings:**
```
☑ New Topic Form
☑ Reply Form
```

4. Click **"Save Changes"**

---

### Step 3: Configure bbPress Settings

**Forum Visibility:**

1. Go to **Settings → Forums**
2. Configure who can participate:
   - Anyone (requires CAPTCHA protection)
   - Registered users only (less spam risk)

**Moderation Settings:**

1. Enable post moderation if needed
2. Set up spam filtering
3. Configure user roles and capabilities

---

### Step 4: Test Your Protection

#### Test New Topic Form:

1. **Navigate to a forum** where you can create topics
2. **Click "New Topic"** button
3. **Verify CAPTCHA appears:**
   - Should see CAPTCHA widget on the form
   - Position depends on theme

4. **Test without CAPTCHA:**
   - Fill out topic title and content
   - Don't complete CAPTCHA
   - Try to submit
   - **Expected:** Error message "Please complete the CAPTCHA"

5. **Test with CAPTCHA:**
   - Fill out topic form
   - Complete CAPTCHA challenge
   - Submit topic
   - **Expected:** Topic created successfully
   - Delete test topic after verification

#### Test Reply Form:

1. **Open any forum topic**
2. **Scroll to reply form** at bottom
3. **Verify CAPTCHA appears**
4. **Test submission:**
   - Without CAPTCHA: Should show error
   - With CAPTCHA: Should post reply successfully
5. Delete test reply after verification

---

## 🎨 Customization

### Skip CAPTCHA for Logged-In Users

Don't show CAPTCHA for replies from logged-in users:

```php
// Add to your theme's functions.php
add_filter( 'wbc_bbpress_reply_skip_logged_in', '__return_true' );
```

**Recommendation:** Enable this for better user experience. Logged-in users are already verified through registration.

---

### CAPTCHA Position

Customize where CAPTCHA appears:

```php
// Move CAPTCHA on new topic form
add_filter( 'wbc_bbpress_topic_captcha_position', function() {
    return 'before_submit'; // Options: 'before_submit', 'after_content'
});

// Move CAPTCHA on reply form
add_filter( 'wbc_bbpress_reply_captcha_position', function() {
    return 'before_submit';
});
```

---

### Custom Error Messages

Customize error messages for bbPress forms:

```php
// Custom new topic error
add_filter( 'wbc_bbpress_topic_error_message', function( $message ) {
    return 'Please verify you are human before posting to the forum.';
});

// Custom reply error
add_filter( 'wbc_bbpress_reply_error_message', function( $message ) {
    return 'Please complete the security check to post your reply.';
});
```

---

### Different CAPTCHA for Topics vs Replies

Use stricter CAPTCHA for new topics:

```php
// Stricter for topics (reCAPTCHA v3 only)
add_filter( 'wbc_bbpress_topic_captcha_threshold', function( $threshold ) {
    return 0.7; // More strict
});

// Normal for replies
add_filter( 'wbc_bbpress_reply_captcha_threshold', function( $threshold ) {
    return 0.5; // Standard
});
```

---

## 🔧 Troubleshooting

### Problem: CAPTCHA Not Appearing on Forms

**Possible Causes:**

1. **bbPress Not Active:**
   - Go to **Plugins**
   - Verify bbPress is activated

2. **Wrong bbPress Version:**
   - Update to latest bbPress version
   - Our plugin supports bbPress 2.6+

3. **Theme Conflict:**
   - Some themes heavily customize bbPress templates
   - Try switching to a default theme temporarily
   - Contact theme developer

4. **Template Overrides:**
   - Theme may have bbPress template overrides
   - Check `your-theme/bbpress/` folder
   - May need to update templates

5. **Plugin Settings:**
   - Verify bbPress forms are checked in plugin settings
   - Verify CAPTCHA service is configured

---

### Problem: CAPTCHA Shows But Validation Fails

**Symptoms:**
- CAPTCHA widget appears
- User completes it
- Still shows validation error

**Solutions:**

1. **Check API Keys:**
   - Verify Site Key and Secret Key are correct
   - Try regenerating keys

2. **Check Domain:**
   - Verify your domain is registered in CAPTCHA service
   - Include www and non-www versions

3. **Server Connection:**
   - Server must reach CAPTCHA API
   - Check firewall settings

4. **AJAX Issues:**
   - bbPress uses AJAX for some forms
   - Check browser console for JavaScript errors

---

### Problem: Forum Spam Still Getting Through

**If spam still appears:**

1. **Verify CAPTCHA is Working:**
   - Log out and test forms
   - Confirm CAPTCHA is required
   - Check validation actually blocks submission

2. **Check User Roles:**
   - CAPTCHA may not show for certain roles
   - Verify it shows for anonymous users

3. **Strengthen CAPTCHA:**
   - Switch from reCAPTCHA v3 to v2 (visible)
   - Or increase threshold (v3)

4. **Enable Moderation:**
   - Go to **Settings → Forums**
   - Enable "Moderate new topics"
   - Require manual approval

5. **Additional Security:**
   - Use Akismet for bbPress
   - Install forum anti-spam plugins
   - Monitor new posts daily

---

### Problem: Users Complain About CAPTCHA

**Common complaints:**

**"I have to solve CAPTCHA every time I reply"**
- Enable skip for logged-in users (see customization)
- Use invisible CAPTCHA (Turnstile, reCAPTCHA v3)

**"CAPTCHA is too hard on mobile"**
- Use Turnstile (usually invisible)
- Use compact size if available
- Test on real mobile devices

**"CAPTCHA breaks my theme"**
- Theme may have custom bbPress styling
- Add custom CSS to fix layout
- Contact theme developer

---

## 🚀 Best Practices

### 1. Always Protect Public Forums

**Why:**
- Public forums are major spam targets
- Forum spam hurts SEO
- Cleanup is extremely time-consuming

**Recommended Protection:**
```
☑ New Topic Form ← Essential
☑ Reply Form ← Essential
```

---

### 2. Skip CAPTCHA for Trusted Users

**Smart Approach:**
- Show CAPTCHA on registration (covered by WordPress Core or BuddyPress)
- Skip CAPTCHA on replies for logged-in users
- Only guests and new topics require CAPTCHA

**Implementation:**
```php
// Skip replies for logged-in users
add_filter( 'wbc_bbpress_reply_skip_logged_in', '__return_true' );
```

---

### 3. Choose Right CAPTCHA for Forums

**High-Activity Forum:**
- Use Turnstile or reCAPTCHA v3 (invisible)
- Better UX for frequent posters
- Less friction

**Spam-Prone Forum:**
- Use reCAPTCHA v2 (visible checkbox)
- Or hCaptcha (visible challenges)
- Stronger protection

**Privacy-Focused Forum:**
- Use ALTCHA or Turnstile
- Maximum privacy
- Good for niche communities

---

### 4. Combine with Other Anti-Spam Measures

**Layered Protection:**

1. **CAPTCHA** - Stops automated bots
2. **Akismet** - Catches manual spam
3. **Moderation** - Human review for new users
4. **User Reputation** - Trust established users
5. **IP Blocking** - Block known spam sources

---

### 5. Monitor Forum Activity

**Daily (High-Activity Forums):**
- Review new topics
- Check flagged content
- Remove spam immediately
- Update blocked keywords

**Weekly (Small Forums):**
- Review past week's posts
- Check for spam patterns
- Adjust CAPTCHA settings if needed

---

## 📊 Recommended Settings by Forum Type

### Small Community Forum

**Low traffic, known members:**
```
☑ New Topic Form
☐ Reply Form (skip for logged-in users)
```
**CAPTCHA:** Turnstile or reCAPTCHA v3
**Extra:** Email activation on registration

---

### Medium Community Forum

**Growing forum, moderate activity:**
```
☑ New Topic Form
☑ Reply Form
```
**CAPTCHA:** reCAPTCHA v3 or hCaptcha
**Extra:** Akismet + first post moderation

---

### Large Public Forum

**High traffic, open participation:**
```
☑ New Topic Form ← Critical
☑ Reply Form ← Critical
```
**CAPTCHA:** reCAPTCHA v2 (visible) or hCaptcha
**Extra:** Akismet + moderation + reputation system

---

### Support Forum

**Customer support, some spam:**
```
☑ New Topic Form ← Essential
☑ Reply Form (guests only)
```
**CAPTCHA:** Turnstile (invisible, professional)
**Extra:** Customer authentication integration

---

## 🔒 Security Considerations

### Forum Spam Has Multiple Consequences

**Problems with forum spam:**
- Clutters forum directory
- Buries legitimate topics
- Hurts search engine rankings
- Damages community trust
- Links to malicious sites
- Time-consuming to clean up

**Solution:**
CAPTCHA is your first line of defense, but not sufficient alone. Combine with moderation and spam filtering.

---

### SEO Impact of Forum Spam

**Negative SEO Effects:**
- Google may penalize your site
- Spam links harm domain authority
- Duplicate content issues
- User-generated spam pages indexed

**Protection:**
- CAPTCHA on all public-facing forms
- Regular spam cleanup
- Use robots.txt to control crawling
- Monitor Google Search Console

---

## 🌍 Multilingual Forums

bbPress works with multilingual plugins:

**WPML:**
- CAPTCHA works on all forum languages
- Error messages translated automatically
- No special configuration

**Polylang:**
- Full compatibility
- Works on translated forums

**Translation:**
- Plugin strings are translatable
- Use .po/.mo files
- Or translation plugins like Loco Translate

---

## ♿ Accessibility

CAPTCHA services provide accessible alternatives:

**For Visually Impaired:**
- reCAPTCHA: Audio challenges
- hCaptcha: Audio challenges
- Turnstile: Usually no challenge needed
- ALTCHA: Screen reader friendly

**Forum Accessibility:**
- Keyboard navigation supported
- Screen reader compatible
- High contrast modes available

**Best Practice:**
- Use invisible CAPTCHA when possible (Turnstile)
- Provide audio alternatives (reCAPTCHA v2, hCaptcha)
- Test with screen readers

---

## 🔗 Integration with Other Plugins

### BuddyPress + bbPress

Common combination:
- BuddyPress for social features
- bbPress for forums
- CAPTCHA protects both

**Setup:**
- Enable BuddyPress integration
- Enable bbPress integration
- Both work together seamlessly

### GamiPress

Gamification for forums:
- CAPTCHA validates before post
- Then GamiPress awards points
- No conflict

### bbPress Moderation Suite

Works together:
- CAPTCHA stops bots
- Moderation catches manual spam
- Comprehensive protection

---

## 📚 Related Guides

**CAPTCHA Service Setup:**
- [Turnstile Setup](../captcha-services/turnstile.md) - Best for forum UX
- [reCAPTCHA v2 Setup](../captcha-services/recaptcha-v2.md) - Visible security
- [hCaptcha Setup](../captcha-services/hcaptcha.md) - Privacy-focused

**Other Integrations:**
- [BuddyPress](buddypress.md) - Community features
- [WordPress Core](wordpress-core.md) - Registration, login
- [Contact Forms](contact-form-7.md) - Support forms

---

## 🔄 Next Steps

1. **[Choose your CAPTCHA service](../captcha-services/README.md)** - Turnstile recommended for forums
2. **Enable bbPress protection** on new topics and replies
3. **Test thoroughly** as logged-in and logged-out user
4. **Configure smart skipping** for logged-in users on replies
5. **Add Akismet** for comprehensive spam protection

---

**Need Help?** Check our [FAQ section](../README.md#frequently-asked-questions) or [contact support](../README.md#getting-help).
