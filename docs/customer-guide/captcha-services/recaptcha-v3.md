# reCAPTCHA v3 Setup Guide

reCAPTCHA v3 is Google's invisible CAPTCHA solution that protects your site without any user interaction. It runs in the background and assigns a risk score to each visitor.

## 📋 What You'll Need

- A Google account
- Your website domain name
- Admin access to your WordPress site
- 10-15 minutes

---

## ✨ What is reCAPTCHA v3?

reCAPTCHA v3 is completely invisible. Instead of challenging users, it:

1. **Monitors user behavior** as they browse your site
2. **Assigns a risk score** from 0.0 (bot) to 1.0 (human)
3. **Makes decisions automatically** based on the score
4. **Never interrupts** legitimate users

Users never see a checkbox or solve puzzles. It just works silently in the background.

---

## 🎯 When to Use reCAPTCHA v3

**Choose reCAPTCHA v3 if:**
- ✅ User experience is your top priority
- ✅ You want invisible, frictionless protection
- ✅ You have high-traffic forms (checkout, registration)
- ✅ You're okay with Google tracking
- ✅ You can handle occasional false positives

**Consider alternatives if:**
- ❌ Privacy is critical (use Turnstile or ALTCHA instead)
- ❌ You need GDPR compliance without disclosure (use hCaptcha or Turnstile)
- ❌ You want visible challenges (use reCAPTCHA v2 instead)
- ❌ You want fallback challenges for suspicious users

---

## 🔑 Getting API Keys

### Step 1: Go to Google reCAPTCHA Admin Console

Visit: [https://www.google.com/recaptcha/admin/create](https://www.google.com/recaptcha/admin/create)

**Note:** You'll need to sign in with your Google account.

---

### Step 2: Fill Out the Registration Form

#### **1. Label**
- Enter a name to identify your site
- Example: "My WordPress Site" or "Main Website"
- This is only for your reference

#### **2. reCAPTCHA type**
- Select **"reCAPTCHA v3"** ← Important!

**Note:** Don't confuse this with reCAPTCHA v2. Make sure you select v3.

#### **3. Domains**
Enter your website domain(s), one per line:
```
example.com
www.example.com
```

**Important Notes:**
- Don't include `http://` or `https://`
- Don't include paths (like `/checkout`)
- Include both `www` and non-`www` versions
- For local testing: `localhost` or `127.0.0.1`

**Example:**
```
mywebsite.com
www.mywebsite.com
staging.mywebsite.com
localhost
```

#### **4. Owners**
- Your Google email is automatically added
- Add additional admins if needed (optional)

#### **5. Accept Terms**
- Check the box to accept the reCAPTCHA Terms of Service
- Optionally receive alerts about your sites

---

### Step 3: Click "Submit"

After submitting, you'll see a page with your keys.

---

### Step 4: Copy Your Keys

You'll see two keys:

#### **Site Key** (Public Key)
```
Example: 6LdRcP0SAAAAAOxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
```
- Shown to users in your website code
- Safe to be public

#### **Secret Key** (Private Key)
```
Example: 6LdRcP0SAAAAAPxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
```
- Used on your server to verify scores
- **Keep this secret!** Never expose it

**Important:** Keep this page open or save both keys. You'll need them next.

---

## ⚙️ Configuring the Plugin

Let's add your reCAPTCHA v3 keys to WordPress.

### Step 1: Go to Plugin Settings

In your WordPress admin:

1. Navigate to **Settings → Wbcom CAPTCHA Manager**
2. Find the "CAPTCHA Configuration" section

---

### Step 2: Select reCAPTCHA v3

1. Find the **"Select CAPTCHA Type"** dropdown
2. Select **"reCAPTCHA v3"** ← Make sure it's v3, not v2!

---

### Step 3: Enter Your Keys

Paste your keys into these fields:

1. **Site Key:** Paste your Site Key
2. **Secret Key:** Paste your Secret Key

**Double-check:** Make sure no extra spaces or characters were copied.

---

### Step 4: Set Score Threshold

This is the most important reCAPTCHA v3 setting!

**Score Threshold** determines how strict the CAPTCHA is:

| Threshold | Security | User Experience | Recommended For |
|-----------|----------|-----------------|-----------------|
| **0.9** | Very Strict | May block legitimate users | Not recommended |
| **0.7** | Strict | Good balance | High-security sites |
| **0.5** | Moderate | Best for most sites | **Default (recommended)** |
| **0.3** | Lenient | Blocks only obvious bots | High-traffic sites |
| **0.1** | Very Lenient | May let some bots through | Testing only |

**Our Recommendation:** Start with **0.5** (the default)

**How Scores Work:**
- Score 1.0 = Definitely human
- Score 0.5 = Uncertain
- Score 0.0 = Definitely bot

If a user's score is below your threshold, they're blocked.

---

### Step 5: Save Settings

Click **"Save Changes"** at the bottom of the page.

You should see: "Settings saved successfully."

---

## 🛡️ Enabling Protection on Forms

Choose which forms to protect with reCAPTCHA v3.

### Step 1: Scroll to "Protection Settings"

You'll see sections for:
- WordPress Core Forms
- WooCommerce
- BuddyPress
- Contact Forms
- And more...

---

### Step 2: Enable Protection

Check the boxes for forms you want to protect:

**Recommended for All Sites:**
- ✅ WordPress Login Form
- ✅ WordPress Registration Form
- ✅ Lost Password Form
- ✅ Comment Form

**For E-commerce:**
- ✅ WooCommerce Checkout ← Very important!
- ✅ WooCommerce Registration
- ✅ WooCommerce Login

**For Community Sites:**
- ✅ BuddyPress Registration
- ✅ BuddyPress Group Creation
- ✅ bbPress Topic/Reply Forms

---

### Step 3: Save Protection Settings

Click **"Save Changes"** again.

---

## ✅ Testing Your Setup

Since reCAPTCHA v3 is invisible, testing requires a different approach.

### Test 1: Check for the Badge

1. **Visit a protected page** (like login or registration)
2. **Look for the reCAPTCHA badge** in the bottom-right corner
3. **You should see:** A small badge that says "protected by reCAPTCHA"

**If you don't see the badge:**
- Clear browser cache
- Check that settings were saved
- Look for JavaScript errors (press F12)

---

### Test 2: Normal User Test

1. **Fill out a protected form normally**
2. **Submit it**
3. **Expected result:** Should work without any interruption

**If it fails:**
- Your score threshold might be too high
- Try lowering it to 0.5 or 0.3

---

### Test 3: Bot-like Behavior Test

To verify it's actually working, try acting like a bot:

1. **Use a VPN** (bots often use VPNs)
2. **Submit forms very quickly** (within 1-2 seconds)
3. **Submit multiple times in a row**

**Expected result:** Eventually you should be blocked

**Note:** This test is optional and may not always work (reCAPTCHA is smart!).

---

## 🎨 Customization Options

### Badge Position

The reCAPTCHA v3 badge can appear in three positions:

1. **Bottom Right** (default) - Most common
2. **Bottom Left** - Alternative position
3. **Inline** - Inside your form

To change position:
1. Go to **Settings → Wbcom CAPTCHA Manager**
2. Find **"Badge Position"** setting
3. Select your preferred position
4. Save changes

---

### Hiding the Badge (Advanced)

Google's terms technically require showing the badge, but you can hide it if you:

1. **Add this to your Privacy Policy:**
```
This site is protected by reCAPTCHA and the Google
Privacy Policy and Terms of Service apply.
```

2. **Add this CSS to your site:**
```css
.grecaptcha-badge {
    visibility: hidden;
}
```

**Important:** Check Google's current terms before hiding the badge.

---

## 🔧 Troubleshooting

### Problem: Too Many False Positives (Blocking Real Users)

**Symptoms:**
- Legitimate users can't submit forms
- Error: "CAPTCHA verification failed"
- Users with VPNs are blocked

**Solution:**
1. Lower your score threshold:
   - Try **0.5** first (default)
   - If still blocking, try **0.3**
2. Check your reCAPTCHA dashboard for patterns
3. Consider allowing VPN users

---

### Problem: Still Getting Spam

**Symptoms:**
- Bots are getting through
- Spam registrations or comments

**Solution:**
1. Raise your score threshold:
   - Try **0.7** (more strict)
2. Enable protection on more forms
3. Check that CAPTCHA is actually loading
4. Consider adding reCAPTCHA v2 to critical forms

---

### Problem: Badge Not Appearing

**Possible Causes:**

1. **JavaScript Not Loading:**
   - Check browser console (F12) for errors
   - Disable other plugins temporarily

2. **Cache Issue:**
   - Clear WordPress cache
   - Clear browser cache
   - Clear CDN cache

3. **Ad Blocker:**
   - Some ad blockers block reCAPTCHA
   - Test in incognito mode

4. **Wrong Domain:**
   - Verify domain is registered in Google admin
   - Check both www and non-www versions

---

### Problem: All Submissions Failing

**Possible Causes:**

1. **Wrong Secret Key:**
   - Verify you copied the correct key
   - Check for extra spaces

2. **Threshold Too High:**
   - Try lowering to 0.3 temporarily
   - See if submissions start working

3. **Server Time Wrong:**
   - reCAPTCHA checks timestamps
   - Ensure server clock is accurate

4. **API Quota Exceeded:**
   - Check reCAPTCHA dashboard
   - Free tier: 1 million calls/month

---

### Problem: Interfering with AJAX Forms

Some themes or plugins use AJAX forms that might conflict.

**Solution:**
1. Check if the plugin supports reCAPTCHA v3
2. Contact the form plugin author
3. Consider using reCAPTCHA v2 for those specific forms

---

## 📊 Understanding Scores

### What Do Scores Mean?

reCAPTCHA v3 assigns each request a score:

| Score Range | Meaning | Action |
|-------------|---------|--------|
| **0.9 - 1.0** | Definitely human | Always allow |
| **0.7 - 0.8** | Probably human | Allow with 0.5 threshold |
| **0.5 - 0.6** | Uncertain | Allow with 0.5, block with 0.7 |
| **0.3 - 0.4** | Probably bot | Block with default settings |
| **0.0 - 0.2** | Definitely bot | Always block |

### Factors That Affect Scores

**Higher Scores (More Human):**
- Normal browsing patterns
- Mouse movements
- Time spent on page
- Came from search engine
- Clean IP address

**Lower Scores (More Bot-Like):**
- VPN or proxy usage
- Automated tools
- Suspicious IP address
- No cookies
- Very fast form submission
- No mouse movements

---

## 📈 Monitoring & Analytics

### View Your reCAPTCHA Dashboard

1. Go to [Google reCAPTCHA Admin](https://www.google.com/recaptcha/admin)
2. Click on your site
3. View metrics:
   - Total requests
   - Score distribution
   - Suspicious activity
   - Top actions (forms)

### Interpreting the Data

**Score Distribution:**
- Most scores should be 0.7+ (humans)
- Scores below 0.3 are likely bots
- Many 0.5-0.6 scores? You might need to adjust threshold

**Request Volume:**
- Sudden spike? Could be an attack
- Gradual increase? Your site is growing!

### Using Insights to Adjust

- **Lots of scores near your threshold?** Adjust up or down
- **High spam rate?** Increase threshold
- **High false positive rate?** Decrease threshold

---

## 🔒 Privacy & GDPR Compliance

### Data Collection

reCAPTCHA v3 collects extensive data:
- Full page browsing behavior
- Mouse movements and clicks
- Browser fingerprints
- IP address and cookies
- Time spent on pages

**This data goes to Google** for analysis.

### GDPR Requirements

For EU users, you must:

1. **Update Privacy Policy:**
```
This site is protected by reCAPTCHA and the Google
Privacy Policy (https://policies.google.com/privacy)
and Terms of Service (https://policies.google.com/terms) apply.
```

2. **Cookie Consent (Recommended):**
- reCAPTCHA sets cookies
- Get user consent before loading
- Provide opt-out option

3. **Data Processing Agreement:**
- Consider signing Google's DPA
- Required for some EU countries

### Privacy-Friendly Alternatives

If GDPR compliance is difficult, consider:
- **Cloudflare Turnstile** - Privacy-first, minimal data
- **ALTCHA** - No third-party tracking
- **hCaptcha** - GDPR-compliant alternative

---

## 🆚 reCAPTCHA v3 vs v2

Not sure you chose the right version?

| Feature | reCAPTCHA v3 | reCAPTCHA v2 |
|---------|--------------|--------------|
| Visibility | Invisible | Visible checkbox |
| User Interaction | None | Required |
| User Experience | Excellent | Good |
| False Positives | Can happen | Rare |
| Requires Tuning | Yes (threshold) | No |
| Bot Detection | Excellent | Excellent |
| Privacy Impact | High | High |

**Switch to v2 if:**
- You're getting too many false positives
- You want users to know they're protected
- You want a fallback challenge for suspicious users

---

## 🚀 Best Practices

### 1. Start with Default Threshold (0.5)
- Don't make it too strict initially
- Monitor for a week
- Adjust based on data

### 2. Monitor Your Dashboard Regularly
- Check score distribution weekly
- Look for unusual patterns
- Adjust threshold if needed

### 3. Don't Overprotect
- v3 works best on important forms
- Don't add to every single form
- Focus on registration, login, checkout

### 4. Combine with Other Security
- Use strong passwords
- Enable two-factor authentication
- Keep WordPress updated
- Use security plugins

### 5. Have a Backup Plan
- Provide alternative contact method
- Monitor for false positives
- Be ready to adjust quickly

### 6. Test Different User Scenarios
- Mobile devices
- VPN users
- International users
- Different browsers

---

## 🔄 Tuning Your Threshold

Over time, you may need to adjust your threshold:

### Too Much Spam Getting Through?

**Increase threshold to 0.7:**
```
More strict → Fewer bots → Some false positives
```

### Too Many False Positives?

**Decrease threshold to 0.3:**
```
More lenient → Fewer false positives → Some bots may get through
```

### Finding the Sweet Spot

1. Start at 0.5 (default)
2. Monitor for 1-2 weeks
3. Check spam rate and complaints
4. Adjust in 0.1 increments
5. Monitor again

**Goal:** Balance security and user experience

---

## 📚 Additional Resources

- **Google reCAPTCHA Homepage:** [https://www.google.com/recaptcha](https://www.google.com/recaptcha)
- **reCAPTCHA v3 Docs:** [https://developers.google.com/recaptcha/docs/v3](https://developers.google.com/recaptcha/docs/v3)
- **reCAPTCHA Admin Console:** [https://www.google.com/recaptcha/admin](https://www.google.com/recaptcha/admin)
- **Google Privacy Policy:** [https://policies.google.com/privacy](https://policies.google.com/privacy)

---

## 🔄 Next Steps

- **[Compare All CAPTCHA Services](README.md)** - See alternatives to reCAPTCHA v3
- **[Integration Guides](../integrations/wordpress-core.md)** - Learn about specific form protections
- **[Customer Guide Home](../README.md)** - Back to main documentation

---

**Need Help?** Check our [FAQ section](../README.md#frequently-asked-questions) or [contact support](../README.md#getting-help).
