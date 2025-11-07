# ALTCHA Setup Guide

ALTCHA is an open-source, privacy-first CAPTCHA alternative that uses proof-of-work challenges. It requires no third-party services and is 100% GDPR compliant.

## 📋 What You'll Need

- Admin access to your WordPress site
- 5 minutes

**That's it!** No external account or API keys needed.

---

## ✨ What is ALTCHA?

ALTCHA is a unique CAPTCHA solution that:

- **Fully open source** (inspect the code yourself)
- **No third-party services** (everything runs on your server)
- **100% GDPR compliant** (no data tracking at all)
- **Proof-of-work based** (uses computational challenges)
- **No external API keys** (works out of the box)

**Key Benefit:** Maximum privacy and transparency. Perfect for privacy-critical applications.

---

## 🎯 When to Use ALTCHA

**Choose ALTCHA if:**
- ✅ Privacy is your absolute top priority
- ✅ You want open-source, transparent technology
- ✅ You need 100% GDPR compliance with no tracking
- ✅ You want to avoid all third-party services
- ✅ You're in a highly regulated industry
- ✅ You want self-hosted solution

**Consider alternatives if:**
- ❌ You need the strongest bot detection (use reCAPTCHA)
- ❌ You want completely invisible CAPTCHA (use Turnstile)
- ❌ Your users have very old devices (proof-of-work may be slow)
- ❌ You want the most established solution (use reCAPTCHA or hCaptcha)

---

## 🎨 How ALTCHA Works

Unlike traditional CAPTCHAs, ALTCHA uses **proof-of-work**:

1. **Server generates a challenge** (mathematical puzzle)
2. **User's browser solves the challenge** (takes 1-3 seconds)
3. **Solution is verified by server**
4. **If valid, form submits**

**No tracking. No cookies. No external services.**

The computational challenge is designed to be:
- Easy for legitimate users' devices
- Expensive for bots (they'd need to solve thousands)

---

## ⚙️ Setup (No API Keys Required!)

ALTCHA is the easiest CAPTCHA to set up because it requires no external services.

### Step 1: Go to Plugin Settings

In WordPress admin:

1. Navigate to **Settings → Wbcom CAPTCHA Manager**
2. Find the "CAPTCHA Configuration" section

---

### Step 2: Select ALTCHA

1. Find the **"Select CAPTCHA Type"** dropdown
2. Select **"ALTCHA"**

---

### Step 3: Save Settings

Click **"Save Changes"**.

**That's it!** ALTCHA is now configured. No API keys, no external accounts needed.

---

## 🛡️ Enabling Protection on Forms

Choose which forms to protect with ALTCHA.

### Step 1: Scroll to "Protection Settings"

You'll see sections for:
- WordPress Core Forms
- WooCommerce
- BuddyPress
- Contact Forms
- And more...

---

### Step 2: Enable Protection

Check boxes for forms you want to protect:

**Recommended:**
- ✅ WordPress Login Form
- ✅ WordPress Registration Form
- ✅ Lost Password Form
- ✅ Comment Form

**For E-commerce:**
- ✅ WooCommerce Checkout
- ✅ WooCommerce Registration

**For Communities:**
- ✅ BuddyPress Registration
- ✅ BuddyPress Group Creation

**For Contact Forms:**
- ✅ Contact Form 7
- ✅ WPForms
- ✅ Gravity Forms

---

### Step 3: Save Protection Settings

Click **"Save Changes"** again.

---

## ✅ Testing Your Setup

### Test 1: Visual Check

1. **Visit a protected form** (like your registration page)
2. **Look for the ALTCHA widget:**
   - You'll see a checkbox or loading indicator
   - Text says "Verify you are human"
   - Loading spinner while solving challenge

---

### Test 2: Normal Submission

1. **Fill out the form**
2. **Wait for ALTCHA to solve** (1-3 seconds)
3. **You'll see a checkmark** when done
4. **Submit the form**
5. **Expected result:** Form submits successfully

---

### Test 3: Without Solving

1. **Fill out the form**
2. **Immediately try to submit** (don't wait for checkmark)
3. **Expected result:** Error message - "Please complete the CAPTCHA"

---

### Test 4: Old Browser Test

1. **Test on an older device or browser**
2. **Challenge may take longer** (3-5 seconds)
3. **Should still work**, just slower

---

## 🎨 Customization Options

### Challenge Difficulty

Adjust how hard the proof-of-work challenge is:

**In plugin settings, you can adjust:**

1. **Easy** (default)
   - Solves in 1-2 seconds on modern devices
   - 2-3 seconds on older devices
   - Good for most sites

2. **Medium**
   - Solves in 2-3 seconds on modern devices
   - 4-5 seconds on older devices
   - Better bot protection

3. **Hard**
   - Solves in 3-5 seconds on modern devices
   - 6-10 seconds on older devices
   - Maximum bot protection, but slower

**Recommendation:** Start with **Easy** for best user experience.

---

### Visual Appearance

Customize how ALTCHA looks:

**Theme Options:**
- **Light** - Light background (default)
- **Dark** - Dark background
- **Auto** - Matches system theme

**Size Options:**
- **Normal** - Standard size
- **Compact** - Smaller widget
- **Floating** - Overlays form

---

## 🔧 Troubleshooting

### Problem: Widget Not Appearing

**Possible Causes:**

1. **JavaScript Not Loading:**
   - Check browser console (F12) for errors
   - Disable other plugins temporarily
   - Check theme compatibility

2. **Caching Issue:**
   - Clear WordPress cache
   - Clear browser cache
   - Disable cache plugins temporarily

3. **JavaScript Disabled:**
   - ALTCHA requires JavaScript
   - Verify JS is enabled in browser

---

### Problem: Challenge Takes Too Long

Users complaining challenge is slow?

**Solutions:**

1. **Lower Difficulty:**
   - Change to "Easy" in plugin settings
   - Reduces solving time

2. **Check Device Performance:**
   - Older devices naturally take longer
   - This is expected behavior

3. **Inform Users:**
   - Add text: "Verifying you're human (this may take a few seconds)"
   - Set expectations

---

### Problem: Challenge Never Completes

**Possible Causes:**

1. **JavaScript Error:**
   - Check browser console (F12)
   - Look for JavaScript conflicts

2. **Browser Too Old:**
   - Very old browsers may not support required features
   - Upgrade browser or use fallback

3. **Ad Blocker:**
   - Some aggressive ad blockers may interfere
   - Add exception for your site

4. **Low-Power Device:**
   - Extremely slow devices may timeout
   - Lower difficulty setting

---

### Problem: Form Submission Still Fails

**Possible Causes:**

1. **Verification Timeout:**
   - Challenge solution expired (5 minutes)
   - User took too long to submit
   - Have user re-solve challenge

2. **Server-Side Issue:**
   - Check WordPress error logs
   - Verify plugin is active
   - Check for server errors

---

## 🔒 Privacy & GDPR Compliance

### Why ALTCHA is the Most Private Option

**No Third-Party Services:**
- Everything runs on your server
- No external API calls
- No data sent to third parties

**No Tracking:**
- No cookies
- No user profiling
- No IP address collection
- No browser fingerprinting

**Open Source:**
- Fully auditable code
- Transparent operation
- Community-reviewed

### GDPR Compliance

ALTCHA is 100% GDPR compliant because:

1. **No Personal Data Collected:**
   - No user information stored
   - No tracking mechanisms
   - No data processing

2. **No Privacy Policy Update Needed:**
   - (But you can mention it for transparency)

3. **No Cookie Consent Required:**
   - Doesn't use cookies
   - Doesn't track users

4. **No Data Processing Agreement:**
   - No third-party data processing
   - You control everything

**ALTCHA is the gold standard for privacy compliance.**

---

## ⚡ Performance Considerations

### How Proof-of-Work Affects Users

**On Modern Devices (2018+):**
- Desktop: 1-2 seconds
- Laptop: 1-2 seconds
- Smartphone: 2-3 seconds
- Tablet: 2-3 seconds

**On Older Devices (2015-2017):**
- Desktop: 2-3 seconds
- Laptop: 3-4 seconds
- Smartphone: 4-5 seconds
- Tablet: 4-5 seconds

**On Very Old Devices (<2015):**
- May take 5-10 seconds
- Consider lowering difficulty
- Or use alternative CAPTCHA for these users

---

### Server Performance

**ALTCHA has minimal server impact:**

- No external API calls
- No complex processing
- Simple verification logic
- Scales well

**Better than:**
- reCAPTCHA (external API calls)
- hCaptcha (external API calls)
- Turnstile (external API calls)

---

## 🆚 ALTCHA vs Other Services

### ALTCHA vs Turnstile

| Feature | ALTCHA | Turnstile |
|---------|--------|-----------|
| Privacy | Absolute | Excellent |
| Third-Party | None | Cloudflare |
| Open Source | Yes | No |
| User Delay | 1-3 seconds | Usually none |
| Bot Detection | Good | Excellent |
| Old Browser Support | Moderate | Excellent |

**Choose ALTCHA if:** Privacy is non-negotiable
**Choose Turnstile if:** You want better UX and don't mind Cloudflare

---

### ALTCHA vs reCAPTCHA

| Feature | ALTCHA | reCAPTCHA |
|---------|--------|-----------|
| Privacy | Absolute | Poor |
| Tracking | None | Extensive |
| GDPR Compliance | Easy | Difficult |
| User Experience | Good | Varies |
| Bot Detection | Good | Excellent |
| Setup | Easiest | Easy |

**Choose ALTCHA if:** Privacy matters
**Choose reCAPTCHA if:** You need maximum bot detection

---

## 🚀 Best Practices

### 1. Set Appropriate Difficulty
- **Start with Easy** for best UX
- **Monitor spam rates**
- **Increase if needed**

### 2. Inform Users
Add helpful text:
```
"Verifying you're human... this may take a moment"
```

### 3. Test on Various Devices
- Desktop computers
- Laptops
- Smartphones
- Tablets
- Older devices

### 4. Combine with Other Security
ALTCHA alone may not stop all bots. Combine with:
- Rate limiting
- IP blocking
- Email verification
- Honeypot fields

### 5. Monitor Form Submissions
- Watch for spam increase
- Adjust difficulty if needed
- Consider hybrid approach (ALTCHA + other methods)

### 6. Provide Alternatives
For users with very old devices:
- Alternative contact method
- Email support
- Phone number

---

## 🌍 Accessibility

### How ALTCHA Handles Accessibility

**Screen Reader Support:**
- ARIA labels on all elements
- Announces challenge progress
- Clear status messages

**Keyboard Navigation:**
- Fully keyboard accessible
- No mouse required
- Tab through elements

**Low Vision:**
- High contrast options
- Scalable text
- Clear visual feedback

### Accessibility Limitations

**No Audio Alternative:**
- Unlike reCAPTCHA, ALTCHA has no audio challenge
- Relies on computational challenge
- Screen readers can announce status

**For visually impaired users:**
- Challenge is accessible via screen reader
- No visual puzzle to solve
- Purely computational

---

## 🔄 Advanced Configuration

### Custom Difficulty Levels

For developers, adjust difficulty in code:

```php
// Add to your theme's functions.php or custom plugin
add_filter( 'wbc_altcha_difficulty', function( $difficulty ) {
    // Options: 'easy', 'medium', 'hard'
    return 'medium';
});
```

### Custom Timeout

Adjust how long a challenge remains valid:

```php
// Challenge expires after X seconds
add_filter( 'wbc_altcha_timeout', function( $timeout ) {
    return 300; // 5 minutes (default)
});
```

### Custom Styling

Style the ALTCHA widget:

```css
/* Target ALTCHA widget */
.altcha-widget {
    border: 2px solid #0073aa;
    border-radius: 4px;
    padding: 10px;
}

.altcha-widget.verified {
    border-color: #46b450;
}
```

---

## 🛠️ Self-Hosting (Advanced)

ALTCHA can be fully self-hosted:

### Requirements:
- PHP 7.4 or higher
- No external dependencies
- Already included in the plugin!

### How It Works:
1. Plugin generates challenge on your server
2. User's browser solves challenge
3. Solution verified on your server
4. No external services involved

**This is the default behavior** - no configuration needed!

---

## 📊 Monitoring

### Track ALTCHA Performance

Unfortunately, ALTCHA doesn't provide a dashboard like other services. To monitor:

1. **Check Spam Rates:**
   - Monitor form submissions
   - Track spam comments/registrations
   - Compare before/after ALTCHA

2. **User Feedback:**
   - Ask users about experience
   - Monitor support tickets
   - Watch for complaints about slow challenges

3. **Server Logs:**
   - Check WordPress debug logs
   - Look for ALTCHA errors
   - Monitor verification failures

---

## 🔄 When to Use vs Not Use ALTCHA

### Perfect Use Cases for ALTCHA:

✅ **Healthcare Sites** - HIPAA compliance
✅ **Financial Services** - PCI compliance
✅ **Government Portals** - Maximum privacy
✅ **EU-based Sites** - GDPR compliance
✅ **Privacy-focused Businesses** - Brand alignment
✅ **Open Source Projects** - Philosophical match

### When to Consider Alternatives:

❌ **High spam sites** - May need stronger solution
❌ **Very old user base** - Slower devices struggle
❌ **Maximum UX priority** - Use Turnstile instead
❌ **Established solution needed** - Use reCAPTCHA

---

## 💡 Hybrid Approach

Consider combining ALTCHA with other methods:

### ALTCHA + Rate Limiting
```
First layer: ALTCHA (privacy-focused)
Second layer: IP-based rate limiting
Result: Strong protection, maximum privacy
```

### ALTCHA + Email Verification
```
Registration: ALTCHA prevents bot signups
Then: Email verification confirms legitimacy
Result: Very effective, still private
```

### ALTCHA + Honeypot
```
Hidden field: Bots fill it out
ALTCHA: Computational challenge
Result: Double protection
```

---

## 🎓 Understanding Proof-of-Work

### What is Proof-of-Work?

**Simple Explanation:**
Your browser solves a mathematical puzzle. It's easy for a human's device, but expensive for bots to solve thousands of times.

**Technical Explanation:**
The challenge requires finding a hash value that meets certain criteria. This is computationally intensive but verifiable quickly.

### Why It Stops Bots

**For legitimate users:**
- Solve one challenge per form submission
- Takes 1-3 seconds
- No problem

**For bots:**
- Need to solve hundreds or thousands
- Each takes 1-3 seconds
- Too expensive and slow
- Bot spam becomes unprofitable

---

## 📚 Additional Resources

- **ALTCHA Homepage:** [https://altcha.org/](https://altcha.org/)
- **ALTCHA GitHub:** [https://github.com/altcha-org/altcha](https://github.com/altcha-org/altcha)
- **Documentation:** [https://altcha.org/docs/](https://altcha.org/docs/)
- **Demo:** [https://altcha.org/demo/](https://altcha.org/demo/)

---

## 🔄 Next Steps

- **[Compare All CAPTCHA Services](README.md)** - See other options
- **[Integration Guides](../integrations/wordpress-core.md)** - Protect specific forms
- **[Customer Guide Home](../README.md)** - Back to main documentation
- **[Developer Guide](../../developer-guide/README.md)** - Technical implementation

---

**Need Help?** Check our [FAQ section](../README.md#frequently-asked-questions) or [contact support](../README.md#getting-help).
