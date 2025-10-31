# hCaptcha Setup Guide

hCaptcha is a privacy-focused CAPTCHA alternative to Google reCAPTCHA. It provides strong bot protection while respecting user privacy and is fully GDPR compliant.

## 📋 What You'll Need

- An hCaptcha account (free)
- Your website domain name
- Admin access to your WordPress site
- 10-15 minutes

---

## ✨ What is hCaptcha?

hCaptcha is a CAPTCHA service that:

- Shows users image-based challenges (like reCAPTCHA v2)
- **Doesn't sell or share user data** (unlike Google)
- Is fully GDPR compliant out of the box
- Can actually **pay you** for solving challenges (optional)
- Provides strong bot detection

**Key Difference from reCAPTCHA:** hCaptcha prioritizes privacy and doesn't use your users' data for advertising purposes.

---

## 🎯 When to Use hCaptcha

**Choose hCaptcha if:**
- ✅ Privacy is important to you
- ✅ You need GDPR compliance without complex setup
- ✅ You want to avoid Google services
- ✅ You want strong bot protection
- ✅ You'd like to earn revenue (optional)

**Consider alternatives if:**
- ❌ You want completely invisible CAPTCHA (use Turnstile or reCAPTCHA v3)
- ❌ You want the most familiar user experience (use reCAPTCHA v2)
- ❌ You need fastest setup (Turnstile is slightly easier)

---

## 🔑 Getting API Keys

### Step 1: Create an hCaptcha Account

Visit: [https://www.hcaptcha.com/signup-interstitial](https://www.hcaptcha.com/signup-interstitial)

1. Enter your email address
2. Create a password
3. Click "Sign Up"
4. **Verify your email** (check your inbox)

---

### Step 2: Log In to Dashboard

After email verification:

1. Go to [https://dashboard.hcaptcha.com/](https://dashboard.hcaptcha.com/)
2. Log in with your credentials
3. You'll see the hCaptcha dashboard

---

### Step 3: Add Your Site

1. Click **"+ Add New Site"** button (or "Sites" in the menu)
2. Fill out the form:

#### **Site Name**
- Enter a name to identify your site
- Example: "My WordPress Site" or "Main Website"
- This is for your reference only

#### **Hostname**
Enter your domain (one per line):
```
example.com
www.example.com
```

**Important Notes:**
- Don't include `http://` or `https://`
- Include both `www` and non-`www` if you use both
- For testing: add `localhost` or `127.0.0.1`

**Example:**
```
mywebsite.com
www.mywebsite.com
staging.mywebsite.com
localhost
```

#### **Difficulty**
Choose challenge difficulty:
- **Easy** - Simple challenges, better UX (recommended for most sites)
- **Moderate** - Standard difficulty
- **Difficult** - Harder challenges, better security

**Recommendation:** Start with **Easy** for best user experience.

---

### Step 4: Save and Get Your Keys

1. Click **"Save"**
2. You'll see your site listed
3. Click on the site name to view details

You'll see two keys:

#### **Sitekey** (Public Key)
```
Example: 10000000-ffff-ffff-ffff-000000000001
```
- Shown in your website code
- Safe to be public

#### **Secret Key** (Private Key)
```
Example: 0x0000000000000000000000000000000000000000
```
- Used on your server for verification
- **Keep this secret!**

**Important:** Copy both keys. You'll need them next.

---

## ⚙️ Configuring the Plugin

Let's add hCaptcha to your WordPress site.

### Step 1: Go to Plugin Settings

In WordPress admin:

1. Navigate to **Settings → Wbcom CAPTCHA Manager**
2. Find the "CAPTCHA Configuration" section

---

### Step 2: Select hCaptcha

1. Find the **"Select CAPTCHA Type"** dropdown
2. Select **"hCaptcha"**

---

### Step 3: Enter Your Keys

Paste your keys:

1. **Site Key:** Paste your hCaptcha Sitekey
2. **Secret Key:** Paste your hCaptcha Secret Key

**Double-check:** Make sure no extra spaces or characters were copied.

---

### Step 4: Choose a Theme (Optional)

hCaptcha offers two visual themes:

- **Light Theme** (default) - White background
- **Dark Theme** - Dark background

Choose the one that matches your site design.

---

### Step 5: Choose Size (Optional)

- **Normal** (default) - Standard size
- **Compact** - Smaller (better for mobile or tight spaces)

---

### Step 6: Save Your Settings

Click **"Save Changes"** at the bottom.

You should see: "Settings saved successfully."

---

## 🛡️ Enabling Protection on Forms

Choose which forms to protect with hCaptcha.

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

**Recommended:**
- ✅ WordPress Login Form
- ✅ WordPress Registration Form
- ✅ Lost Password Form
- ✅ Comment Form
- ✅ Contact Forms

**For E-commerce:**
- ✅ WooCommerce Checkout
- ✅ WooCommerce Registration

**For Communities:**
- ✅ BuddyPress Registration
- ✅ BuddyPress Group Creation

---

### Step 3: Save Protection Settings

Click **"Save Changes"** again.

---

## ✅ Testing Your Setup

### Test 1: Visual Check

1. **Visit a protected form** (like your login page)
2. **Look for the hCaptcha widget**
3. **You should see:** A checkbox with "I am human"

**If you don't see it:**
- Clear browser cache
- Check that settings were saved
- Look for JavaScript errors (F12)

---

### Test 2: Challenge Test

1. **Click the checkbox**
2. **You'll see an image challenge:**
   - "Select all images with [object]"
   - Choose the correct images
   - Click "Verify"

3. **Expected result:** Checkbox gets checked ✅

---

### Test 3: Form Submission

1. **Fill out the form**
2. **Complete the hCaptcha challenge**
3. **Submit**
4. **Expected result:** Form submits successfully

---

### Test 4: Failure Test

1. **Fill out the form**
2. **Don't complete the hCaptcha**
3. **Try to submit**
4. **Expected result:** Error message - "Please complete the CAPTCHA"

---

## 🎨 Customization Options

### Theme Selection

Change the theme to match your site:

1. Go to **Settings → Wbcom CAPTCHA Manager**
2. Find **Theme** setting
3. Choose:
   - **Light** - For light backgrounds
   - **Dark** - For dark backgrounds

---

### Size Options

For mobile or tight spaces:

1. Change **Size** to **"Compact"**
2. Makes the widget smaller
3. Better for responsive designs

---

### Challenge Difficulty

Want harder or easier challenges?

1. Go to [hCaptcha Dashboard](https://dashboard.hcaptcha.com/)
2. Click on your site
3. Change **Difficulty** setting:
   - **Easy** - Simple challenges (better UX)
   - **Moderate** - Standard
   - **Difficult** - Harder (better security)

---

## 🔧 Troubleshooting

### Problem: hCaptcha Doesn't Appear

**Possible Causes:**

1. **JavaScript Conflict:**
   - Disable other plugins temporarily
   - Check browser console (F12) for errors

2. **Caching Issue:**
   - Clear browser cache
   - Clear WordPress cache
   - Clear CDN cache

3. **Wrong Domain:**
   - Verify domain in hCaptcha dashboard
   - Include both www and non-www versions

4. **Incorrect Keys:**
   - Double-check Sitekey and Secret Key
   - Make sure you selected "hCaptcha" (not reCAPTCHA)

---

### Problem: Challenge Always Fails

**Possible Causes:**

1. **Wrong Secret Key:**
   - Verify you copied the Secret Key correctly
   - Check for extra spaces

2. **Server Time Issue:**
   - Ensure server clock is accurate
   - Contact hosting support if needed

3. **Domain Mismatch:**
   - URL must match registered domain
   - Check both www and non-www

4. **IP Address Blocked:**
   - Some IPs may be flagged
   - Check hCaptcha dashboard for blocks

---

### Problem: Shows "Invalid Domain"

**Solution:**

1. Go to [hCaptcha Dashboard](https://dashboard.hcaptcha.com/)
2. Click on your site
3. Add your domain to the hostname list
4. Include www version if needed
5. Save changes
6. Wait 1-2 minutes for changes to apply

---

### Problem: Challenges Too Difficult

Users complaining about hard challenges?

**Solution:**

1. Go to hCaptcha Dashboard
2. Click on your site
3. Change **Difficulty** to **"Easy"**
4. Save changes

---

### Problem: Breaks Mobile Layout

**Solutions:**

1. **Use Compact Size:**
   - Change Size to "Compact" in plugin settings

2. **Add Custom CSS:**
```css
@media (max-width: 768px) {
    .h-captcha {
        transform: scale(0.85);
        transform-origin: 0 0;
    }
}
```

---

## 🔒 Privacy & GDPR Compliance

### Why hCaptcha is Privacy-Friendly

**hCaptcha's Privacy Promise:**
- ✅ Doesn't sell your users' data
- ✅ Doesn't use data for ad targeting
- ✅ GDPR compliant by default
- ✅ Transparent privacy policy
- ✅ EU data centers available

### Data Collection

hCaptcha collects:
- IP address (for bot detection)
- Browser information
- Mouse movements (during challenge)
- Challenge responses

**But unlike Google reCAPTCHA:**
- Data is NOT used for advertising
- Data is NOT sold to third parties
- Data is only used for CAPTCHA service

### GDPR Compliance

hCaptcha is GDPR compliant by default, but you should still:

1. **Update Privacy Policy:**
```
This site uses hCaptcha to protect against spam and abuse.
hCaptcha's privacy policy applies:
https://www.hcaptcha.com/privacy
```

2. **Cookie Consent (Optional):**
- hCaptcha uses cookies
- Consider cookie consent banner
- Many sites don't require explicit consent for security cookies

3. **No Data Processing Agreement Needed:**
- hCaptcha's terms cover GDPR requirements
- No separate agreement necessary for most sites

---

## 💰 Earning Revenue with hCaptcha (Optional)

One unique feature of hCaptcha: you can earn money!

### How It Works

- Users solve challenges on your site
- You earn a small amount per challenge
- Paid out when you reach minimum threshold

### Enabling Rewards

1. Go to [hCaptcha Dashboard](https://dashboard.hcaptcha.com/)
2. Navigate to **Settings → Account**
3. Enable **"Publisher Rewards"**
4. Add payment details (PayPal or crypto)

### Realistic Expectations

- Earnings are small (fractions of a cent per challenge)
- Good for high-traffic sites
- Don't expect significant revenue
- Nice bonus, not a primary income source

**Example:**
- 1 million challenges/month ≈ $10-50 USD

---

## 📊 Monitoring & Analytics

### View hCaptcha Dashboard

1. Go to [hCaptcha Dashboard](https://dashboard.hcaptcha.com/)
2. Click on your site
3. View statistics:
   - Total requests
   - Passed challenges
   - Failed attempts
   - Traffic sources

### Understanding the Data

- **High pass rate** (90%+) - Good! Normal users
- **High fail rate** (50%+) - Could indicate bot attacks
- **Unusual traffic spikes** - Investigate source

### Using Analytics

- Monitor for attack patterns
- Adjust difficulty if needed
- Track peak usage times
- Identify problematic sources

---

## 🆚 hCaptcha vs reCAPTCHA

Still wondering if you made the right choice?

| Feature | hCaptcha | reCAPTCHA v2 |
|---------|----------|--------------|
| Privacy | Excellent | Poor |
| GDPR Compliance | Easy | Requires disclosure |
| Data Selling | No | Yes (Google ads) |
| Bot Detection | Excellent | Excellent |
| User Experience | Good | Good |
| Revenue Sharing | Yes (optional) | No |
| Free Tier | Generous | Generous |
| Setup Difficulty | Easy | Easy |

**Use hCaptcha if:** Privacy matters
**Use reCAPTCHA if:** You trust Google and want maximum familiarity

---

## 🚀 Best Practices

### 1. Start with Easy Difficulty
- Better user experience
- Still blocks bots effectively
- Adjust if you see too much spam

### 2. Match Theme to Your Site
- Light theme for light sites
- Dark theme for dark sites
- Improves visual consistency

### 3. Use Compact Size on Mobile
- Better responsive design
- Easier to interact on small screens
- Set via plugin settings

### 4. Monitor Your Dashboard
- Check analytics weekly
- Look for unusual patterns
- Adjust settings based on data

### 5. Keep Domains Updated
- Add staging/dev domains
- Include all variations (www, non-www)
- Remove old domains you no longer use

### 6. Test from Different Locations
- VPN users
- International visitors
- Mobile devices
- Different browsers

---

## 🌍 Accessibility Features

hCaptcha provides accessibility options:

### Audio Challenges

1. Users can click the audio icon
2. Listen to spoken challenge
3. Type what they hear
4. Accessible for visually impaired users

### Keyboard Navigation

- All challenges support keyboard-only navigation
- Tab through options
- Spacebar to select
- Enter to submit

### Screen Reader Support

- ARIA labels on all elements
- Announced changes and updates
- Compatible with major screen readers

---

## 🔄 Advanced Settings

### Passive Mode

Less intrusive for trusted traffic:

1. Go to hCaptcha Dashboard
2. Click on your site
3. Enable **"Passive Mode"**
4. Challenges shown less frequently

### Custom Themes

Want to match your brand exactly?

1. hCaptcha Enterprise offers custom themes
2. Contact hCaptcha sales for pricing
3. Custom colors, logos, and styling

### Rate Limiting

Prevent abuse:

1. Configure in hCaptcha Dashboard
2. Set maximum attempts per IP
3. Automatic blocking for suspicious IPs

---

## 📚 Additional Resources

- **hCaptcha Homepage:** [https://www.hcaptcha.com/](https://www.hcaptcha.com/)
- **hCaptcha Dashboard:** [https://dashboard.hcaptcha.com/](https://dashboard.hcaptcha.com/)
- **hCaptcha Documentation:** [https://docs.hcaptcha.com/](https://docs.hcaptcha.com/)
- **hCaptcha Privacy Policy:** [https://www.hcaptcha.com/privacy](https://www.hcaptcha.com/privacy)
- **Support:** [https://www.hcaptcha.com/support](https://www.hcaptcha.com/support)

---

## 🔄 Next Steps

- **[Compare All CAPTCHA Services](README.md)** - See other options
- **[Integration Guides](../integrations/wordpress-core.md)** - Protect specific forms
- **[Customer Guide Home](../README.md)** - Back to main documentation

---

**Need Help?** Check our [FAQ section](../README.md#frequently-asked-questions) or [contact support](../README.md#getting-help).
