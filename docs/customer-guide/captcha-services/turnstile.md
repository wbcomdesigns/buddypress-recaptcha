# Cloudflare Turnstile Setup Guide

Cloudflare Turnstile is a modern, privacy-first CAPTCHA alternative that's usually invisible to users. It's fast, free, and respects user privacy.

## 📋 What You'll Need

- A Cloudflare account (free)
- Your website domain name
- Admin access to your WordPress site
- 5-10 minutes

---

## ✨ What is Cloudflare Turnstile?

Turnstile is Cloudflare's next-generation CAPTCHA that:

- **Usually invisible** to users (no clicking or solving)
- **Privacy-first** (minimal data collection, no tracking)
- **Fast and lightweight** (doesn't slow down your site)
- **Free unlimited usage** (no quotas or limits)
- **Modern technology** (uses browser challenges)

**Key Benefit:** Best balance of security, privacy, and user experience.

---

## 🎯 When to Use Cloudflare Turnstile

**Choose Turnstile if:**
- ✅ You want invisible protection with great UX
- ✅ Privacy is important (but you still want strong security)
- ✅ You already use Cloudflare (easier setup)
- ✅ You want free unlimited usage
- ✅ You're building a modern web application

**Consider alternatives if:**
- ❌ You need the most battle-tested solution (use reCAPTCHA v2)
- ❌ You want absolutely no third-party services (use ALTCHA)
- ❌ You want visible challenges users can see (use reCAPTCHA v2 or hCaptcha)

---

## 🔑 Getting API Keys

### Step 1: Create a Cloudflare Account

If you don't have one:

Visit: [https://dash.cloudflare.com/sign-up](https://dash.cloudflare.com/sign-up)

1. Enter your email and password
2. Verify your email address
3. Log in to Cloudflare dashboard

**Note:** You don't need to add your domain to Cloudflare to use Turnstile!

---

### Step 2: Navigate to Turnstile

1. Log in to [Cloudflare Dashboard](https://dash.cloudflare.com/)
2. Click **"Turnstile"** in the left sidebar
   - If you don't see it, search for "Turnstile" in the dashboard
3. Click **"Add Site"** or **"Add Widget"**

**First Time?** You may need to accept Turnstile terms.

---

### Step 3: Configure Your Widget

Fill out the widget configuration form:

#### **1. Site Name**
- Enter a friendly name
- Example: "My WordPress Site"
- For your reference only

#### **2. Domain**
Enter your domain(s):
```
example.com
www.example.com
```

**Important Notes:**
- Don't include `http://` or `https://`
- One domain per line
- Include both www and non-www versions
- For testing, add: `localhost`

**Example:**
```
mywebsite.com
www.mywebsite.com
staging.mywebsite.com
localhost
```

#### **3. Widget Mode**

Choose a mode (you can change this later):

**Managed (Recommended)**
- Cloudflare decides when to show challenges
- Usually invisible
- Best balance of security and UX

**Non-Interactive**
- Always invisible
- No user interaction ever
- Best UX, slightly less secure

**Invisible**
- Invisible but runs JavaScript challenges
- Good security, no visible widget

**Recommendation:** Start with **Managed** for best results.

---

### Step 4: Create Widget

1. Click **"Create"**
2. You'll see your widget details page

---

### Step 5: Copy Your Keys

You'll see two keys:

#### **Site Key**
```
Example: 0x4AAAAAAA...
```
- Public key shown in your HTML
- Safe to expose

#### **Secret Key**
```
Example: 0x4AAAAAAA...
```
- Private key for server verification
- **Keep this secret!**

**Important:** Copy both keys - you'll need them next.

---

## ⚙️ Configuring the Plugin

Let's add Turnstile to your WordPress site.

### Step 1: Go to Plugin Settings

In WordPress admin:

1. Navigate to **Settings → Wbcom CAPTCHA Manager**
2. Find the "CAPTCHA Configuration" section

---

### Step 2: Select Cloudflare Turnstile

1. Find the **"Select CAPTCHA Type"** dropdown
2. Select **"Cloudflare Turnstile"**

---

### Step 3: Enter Your Keys

Paste your keys:

1. **Site Key:** Paste your Turnstile Site Key
2. **Secret Key:** Paste your Turnstile Secret Key

**Double-check:** No extra spaces or characters.

---

### Step 4: Choose Theme (Optional)

Turnstile themes (for the rare cases when widget is visible):

- **Auto** (default) - Matches user's system (light/dark mode)
- **Light** - Light theme
- **Dark** - Dark theme

**Recommendation:** Keep **Auto** for best experience.

---

### Step 5: Save Settings

Click **"Save Changes"** at the bottom.

You should see: "Settings saved successfully."

---

## 🛡️ Enabling Protection on Forms

Choose which forms to protect with Turnstile.

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
- ✅ WooCommerce Checkout ← Especially important!
- ✅ WooCommerce Registration
- ✅ WooCommerce Login

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

Since Turnstile is usually invisible, testing is simple.

### Test 1: Visual Check

1. **Visit a protected form** (like registration page)
2. **Look for:**
   - Usually nothing visible! That's normal
   - Or a small "Cloudflare" badge
   - Or a loading indicator briefly

**If you see a challenge box:**
- That's okay! Turnstile occasionally shows visible challenges
- More common for VPN users or suspicious behavior

---

### Test 2: Normal Submission

1. **Fill out the form normally**
2. **Submit it**
3. **Expected result:** Works seamlessly without interruption

**If it works:** Great! Turnstile is working invisibly.

---

### Test 3: Without JavaScript

1. **Disable JavaScript** in your browser
2. **Try to submit the form**
3. **Expected result:** Should show an error or fallback

This confirms Turnstile is actually protecting your forms.

---

### Test 4: Cloudflare Dashboard

1. Go to [Cloudflare Turnstile Dashboard](https://dash.cloudflare.com/)
2. Click on your widget
3. **Check analytics:**
   - Should show requests increasing
   - Confirms Turnstile is active

---

## 🎨 Customization Options

### Widget Modes

Change how Turnstile behaves:

1. Go to [Cloudflare Dashboard](https://dash.cloudflare.com/)
2. Find your Turnstile widget
3. Click **Edit**
4. Change **Widget Mode:**

**Managed (Recommended)**
```
✓ Usually invisible
✓ Shows challenges when needed
✓ Best balance
```

**Non-Interactive**
```
✓ Always invisible
✓ Never shows challenges
✓ Best UX, slightly less secure
```

**Invisible**
```
✓ Always invisible
✓ Runs JavaScript challenges
✓ Good security
```

---

### Theme Selection

Match your site design:

**Auto** (Recommended)
- Automatically matches user's system theme
- Light mode users see light theme
- Dark mode users see dark theme

**Light**
- Always shows light theme
- Good for light-colored sites

**Dark**
- Always shows dark theme
- Good for dark-colored sites

---

### Appearance

Customize widget appearance:

1. **Execution Mode:**
   - Render: Widget appears immediately
   - Execute: Widget runs on-demand

2. **Language:**
   - Auto-detected from user's browser
   - Or force specific language in Cloudflare settings

---

## 🔧 Troubleshooting

### Problem: Widget Doesn't Appear or Work

**Possible Causes:**

1. **JavaScript Not Loading:**
   - Check browser console (F12) for errors
   - Disable conflicting plugins temporarily

2. **Caching Issue:**
   - Clear WordPress cache
   - Clear browser cache
   - Clear CDN cache

3. **Wrong Domain:**
   - Verify domain in Cloudflare Turnstile settings
   - Include www and non-www versions
   - Check URL in browser matches registered domain

4. **Incorrect Keys:**
   - Double-check Site Key and Secret Key
   - Make sure you selected "Cloudflare Turnstile" in plugin

---

### Problem: All Submissions Failing

**Possible Causes:**

1. **Wrong Secret Key:**
   - Verify you copied Secret Key correctly
   - Check for extra spaces or missing characters

2. **Domain Mismatch:**
   - The domain you're on must be registered
   - Add domain in Cloudflare Turnstile dashboard

3. **Server Issue:**
   - Server can't reach Cloudflare API
   - Check firewall settings
   - Verify server has outbound internet access

4. **Rate Limiting:**
   - Check Cloudflare dashboard for blocks
   - May need to adjust settings

---

### Problem: Shows Challenges Too Often

Users seeing visible challenges frequently?

**Causes:**
- VPN usage
- Tor browser
- Ad blockers
- Suspicious behavior patterns

**Solutions:**

1. **Change to Non-Interactive Mode:**
   - Edit widget in Cloudflare Dashboard
   - Select "Non-Interactive" mode
   - Never shows challenges

2. **Adjust Security Level:**
   - Lower security threshold in dashboard
   - Reduces false positives

---

### Problem: Not Blocking Bots

**Solutions:**

1. **Switch to Managed Mode:**
   - More aggressive bot detection
   - Shows challenges when suspicious

2. **Check Analytics:**
   - View Turnstile dashboard
   - Verify requests are being processed
   - Look for blocked attempts

3. **Combine with Other Security:**
   - Use additional security plugins
   - Enable rate limiting
   - Use Cloudflare's other security features

---

## 🔒 Privacy & GDPR Compliance

### Why Turnstile is Privacy-Friendly

**Cloudflare's Privacy Approach:**
- ✅ Minimal data collection
- ✅ No cross-site tracking
- ✅ No selling user data
- ✅ No advertising use
- ✅ No persistent identifiers

### Data Collection

Turnstile collects minimal data:
- Browser characteristics (for challenge)
- IP address (for security)
- Timing information
- Challenge results

**What Turnstile DOESN'T collect:**
- No cookies for tracking
- No user identity
- No browsing history
- No personal information

### GDPR Compliance

Turnstile is GDPR-friendly:

1. **Privacy Policy Update:**
```
This site is protected by Cloudflare Turnstile.
Cloudflare's Privacy Policy applies:
https://www.cloudflare.com/privacypolicy/
```

2. **No Cookie Consent Required:**
   - Turnstile doesn't use tracking cookies
   - Security cookies are exempt from consent
   - Check local regulations to be sure

3. **Data Processing:**
   - Cloudflare is GDPR compliant
   - Has Data Processing Agreements available
   - EU data centers available

---

## 📊 Monitoring & Analytics

### View Turnstile Analytics

1. Go to [Cloudflare Dashboard](https://dash.cloudflare.com/)
2. Click **"Turnstile"**
3. Select your widget
4. View metrics:
   - Total requests
   - Solved challenges
   - Failed attempts
   - Traffic patterns

### Understanding the Data

**Metrics to Watch:**

- **Total Requests:** How many form submissions
- **Challenges Shown:** How often users see visible challenges
- **Failed Attempts:** Blocked bots
- **Success Rate:** Percentage passing

### Using Analytics

- **High challenge rate?** Consider Non-Interactive mode
- **High failure rate?** May indicate bot attack (good!)
- **Low activity?** Verify Turnstile is configured correctly

---

## 🆚 Turnstile vs reCAPTCHA

Comparing Turnstile and Google reCAPTCHA:

| Feature | Turnstile | reCAPTCHA v3 |
|---------|-----------|--------------|
| Privacy | Excellent | Poor |
| User Experience | Excellent | Excellent |
| Visibility | Usually invisible | Always invisible |
| Data Tracking | Minimal | Extensive |
| GDPR Compliance | Easy | Requires disclosure |
| Free Tier | Unlimited | 1M/month |
| Bot Detection | Excellent | Excellent |
| Setup Difficulty | Very Easy | Easy |

**Use Turnstile if:** You want modern, privacy-first protection
**Use reCAPTCHA if:** You need the most established solution

---

## 🚀 Best Practices

### 1. Start with Managed Mode
- Best balance of security and UX
- Adjust based on your needs
- Monitor analytics for patterns

### 2. Use Auto Theme
- Automatically matches user preferences
- Better accessibility
- Modern user experience

### 3. Monitor Regularly
- Check Cloudflare dashboard weekly
- Look for unusual patterns
- Adjust settings based on data

### 4. Combine with Cloudflare CDN
- Already using Cloudflare? Perfect synergy
- Better performance
- Additional security features

### 5. Test from Different Locations
- VPN users may see challenges
- International visitors
- Mobile devices
- Different browsers

### 6. Keep Domains Updated
- Add all domains (www, non-www)
- Include staging/dev environments
- Remove old domains

---

## 🌍 Performance & Speed

### Why Turnstile is Fast

**Optimizations:**
- Lightweight JavaScript (< 20KB)
- Loads asynchronously
- CDN-delivered globally
- No external dependencies

### Performance Tips

1. **Use Cloudflare CDN:**
   - Even faster loading
   - Better global performance

2. **Lazy Loading:**
   - Turnstile loads only when needed
   - Doesn't slow initial page load

3. **Browser Caching:**
   - Scripts cached locally
   - Faster on repeat visits

---

## 🔄 Advanced Settings

### Custom Error Handling

Handle failed challenges gracefully:

```javascript
// Listen for Turnstile errors
window.addEventListener('cf-challenge-error', function(e) {
    console.log('Turnstile error:', e.detail);
    // Show friendly error message
});
```

### Programmatic Reset

Reset challenge after failed submission:

```javascript
// Reset Turnstile widget
turnstile.reset();
```

### Pre-Clearance Mode

For Enterprise customers:

- Clear users before they fill forms
- Invisible pre-validation
- Reduces form abandonment

**Contact Cloudflare sales for Enterprise features.**

---

## 🎁 Turnstile vs Alternatives

Quick comparison with other services:

### Turnstile vs hCaptcha

**Choose Turnstile if:**
- You want invisible challenges
- Privacy with great UX
- Free unlimited usage

**Choose hCaptcha if:**
- You want revenue sharing
- More visible security
- Established GDPR compliance track record

### Turnstile vs ALTCHA

**Choose Turnstile if:**
- You want proven technology
- Better bot detection
- Professional support

**Choose ALTCHA if:**
- You want 100% open source
- No third-party services at all
- Self-hosted option

---

## 📚 Additional Resources

- **Cloudflare Turnstile:** [https://www.cloudflare.com/products/turnstile/](https://www.cloudflare.com/products/turnstile/)
- **Turnstile Docs:** [https://developers.cloudflare.com/turnstile/](https://developers.cloudflare.com/turnstile/)
- **Cloudflare Dashboard:** [https://dash.cloudflare.com/](https://dash.cloudflare.com/)
- **Cloudflare Privacy Policy:** [https://www.cloudflare.com/privacypolicy/](https://www.cloudflare.com/privacypolicy/)
- **Support:** [https://support.cloudflare.com/](https://support.cloudflare.com/)

---

## 🔄 Next Steps

- **[Compare All CAPTCHA Services](README.md)** - See other options
- **[Integration Guides](../integrations/wordpress-core.md)** - Protect specific forms
- **[Customer Guide Home](../README.md)** - Back to main documentation

---

**Need Help?** Check our [FAQ section](../README.md#frequently-asked-questions) or [contact support](../README.md#getting-help).
