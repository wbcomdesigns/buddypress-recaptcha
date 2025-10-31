# reCAPTCHA v2 Setup Guide

reCAPTCHA v2 is Google's classic "I'm not a robot" checkbox CAPTCHA. It's the most recognizable CAPTCHA service and provides excellent bot protection.

## 📋 What You'll Need

- A Google account
- Your website domain name
- Admin access to your WordPress site
- 10-15 minutes

---

## ✨ What is reCAPTCHA v2?

reCAPTCHA v2 shows users a checkbox with the text "I'm not a robot". When clicked:

- **If you appear human:** The checkbox gets checked instantly ✅
- **If suspicious:** You'll see an image challenge (select traffic lights, crosswalks, etc.)

This provides strong security while keeping the process simple for legitimate users.

---

## 🎯 When to Use reCAPTCHA v2

**Choose reCAPTCHA v2 if:**
- ✅ Security is your top priority
- ✅ You handle sensitive information
- ✅ You have a serious spam problem
- ✅ Users are already familiar with the checkbox
- ✅ You want strong bot detection

**Consider alternatives if:**
- ❌ You want invisible CAPTCHA (use reCAPTCHA v3 instead)
- ❌ Privacy is critical (use Turnstile or ALTCHA instead)
- ❌ You need GDPR compliance (use hCaptcha or Turnstile instead)

---

## 🔑 Getting API Keys

### Step 1: Go to Google reCAPTCHA Admin Console

Visit: [https://www.google.com/recaptcha/admin/create](https://www.google.com/recaptcha/admin/create)

**Note:** You'll need to sign in with your Google account.

---

### Step 2: Fill Out the Registration Form

You'll see a form with several fields:

#### **1. Label**
- Enter a name to identify your site
- Example: "My WordPress Site" or "Contact Forms"
- This is only for your reference in the Google dashboard

#### **2. reCAPTCHA type**
- Select **"reCAPTCHA v2"**
- Then choose **"I'm not a robot Checkbox"**

There are other v2 options:
- **"I'm not a robot Checkbox"** ← Choose this one (most common)
- "Invisible reCAPTCHA badge" (use reCAPTCHA v3 instead)
- "reCAPTCHA Android" (for mobile apps)

#### **3. Domains**
Enter your website domain(s), one per line:
```
example.com
www.example.com
```

**Important Notes:**
- Don't include `http://` or `https://`
- Don't include paths (like `/contact`)
- Include both `www` and non-`www` versions if you use both
- For local testing, you can add: `localhost` or `127.0.0.1`

**Example:**
```
mywebsite.com
www.mywebsite.com
localhost
```

#### **4. Owners**
- Your Google email is automatically added
- You can add additional Google accounts if needed (optional)

#### **5. Accept Terms**
- Check the box to accept the reCAPTCHA Terms of Service
- Optionally check the box to receive alerts about your sites

---

### Step 3: Click "Submit"

After clicking submit, you'll see a page with your keys.

---

### Step 4: Copy Your Keys

You'll see two keys:

#### **Site Key** (Public Key)
```
Example: 6LdRcP0SAAAAAOxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
```
- This is shown to users in your HTML
- It's safe to be public

#### **Secret Key** (Private Key)
```
Example: 6LdRcP0SAAAAAPxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
```
- This is used on your server to verify submissions
- **Keep this private!** Never share it publicly

**Important:** Keep this page open or copy both keys somewhere safe. You'll need them in the next step.

---

## ⚙️ Configuring the Plugin

Now let's add these keys to your WordPress site.

### Step 1: Go to Plugin Settings

In your WordPress admin dashboard:

1. Go to **Settings → Wbcom CAPTCHA Manager**
2. You'll see the "CAPTCHA Configuration" section

---

### Step 2: Select reCAPTCHA v2

1. Find the **"Select CAPTCHA Type"** dropdown
2. Select **"reCAPTCHA v2"**

---

### Step 3: Enter Your Keys

You'll see two fields:

1. **Site Key:** Paste your Site Key here
2. **Secret Key:** Paste your Secret Key here

**Triple-check:** Make sure you didn't accidentally include extra spaces or characters when copying.

---

### Step 4: Choose a Theme (Optional)

reCAPTCHA v2 offers two visual themes:

- **Light Theme** (default) - White background
- **Dark Theme** - Dark background

Choose the one that matches your site design.

---

### Step 5: Choose CAPTCHA Size (Optional)

- **Normal** (default) - Standard size checkbox
- **Compact** - Smaller version (good for mobile or tight spaces)

---

### Step 6: Save Your Settings

Click the **"Save Changes"** button at the bottom of the page.

You should see a success message: "Settings saved successfully."

---

## 🛡️ Enabling Protection on Forms

Now that reCAPTCHA v2 is configured, choose which forms to protect.

### Step 1: Scroll to "Protection Settings"

You'll see sections for different form types:

- WordPress Core Forms (Login, Registration, Comments, etc.)
- BuddyPress Forms
- WooCommerce Forms
- Contact Form 7
- And many more...

---

### Step 2: Enable Protection

Check the boxes next to the forms you want to protect:

**Recommended for Most Sites:**
- ✅ WordPress Login Form
- ✅ WordPress Registration Form
- ✅ Lost Password Form
- ✅ Comment Form
- ✅ Contact Forms (CF7, WPForms, etc.)

**For E-commerce Sites:**
- ✅ WooCommerce Checkout
- ✅ WooCommerce Registration

**For Community Sites:**
- ✅ BuddyPress Registration
- ✅ BuddyPress Group Creation

---

### Step 3: Save Protection Settings

Click **"Save Changes"** again.

---

## ✅ Testing Your Setup

It's important to test that reCAPTCHA is working correctly.

### Test 1: Visual Check

1. **Open a protected form** (like your login page)
2. **Look for the checkbox:**
   - You should see the "I'm not a robot" checkbox
   - Below it should say "protected by reCAPTCHA"

**If you don't see the checkbox:**
- Clear your browser cache
- Check that you saved your settings
- Check browser console for JavaScript errors (press F12)

---

### Test 2: Submission Test

1. **Fill out the form** but don't check the CAPTCHA box
2. **Try to submit**
3. **Expected result:** You should see an error message like "Please complete the CAPTCHA"

---

### Test 3: Success Test

1. **Fill out the form again**
2. **Check the CAPTCHA box** (you might need to solve an image challenge)
3. **Submit the form**
4. **Expected result:** Form should submit successfully

---

## 🎨 Customization Options

### Theme Selection

If the CAPTCHA doesn't match your site design:

1. Go back to **Settings → Wbcom CAPTCHA Manager**
2. Change the **Theme** setting:
   - **Light** - For light-colored backgrounds
   - **Dark** - For dark-colored backgrounds

---

### Size Options

If the CAPTCHA looks too big on mobile:

1. Change the **Size** setting to **"Compact"**
2. This makes the checkbox smaller

---

### Language

reCAPTCHA automatically detects the user's browser language. To force a specific language:

1. reCAPTCHA uses your WordPress site language setting
2. Go to **Settings → General**
3. Change **Site Language** if needed

---

## 🔧 Troubleshooting

### Problem: CAPTCHA Doesn't Appear

**Possible Causes:**

1. **JavaScript Conflict:**
   - Try disabling other plugins temporarily
   - Check browser console (F12) for errors

2. **Caching Issue:**
   - Clear your browser cache
   - Clear WordPress cache (if using a cache plugin)
   - Clear CDN cache (if using Cloudflare, etc.)

3. **Wrong Domain:**
   - Make sure your domain is added in Google reCAPTCHA admin
   - Include both `www` and non-`www` versions

4. **Incorrect API Keys:**
   - Double-check you copied the keys correctly
   - Make sure you selected reCAPTCHA v2 (not v3)

---

### Problem: CAPTCHA Shows But Always Fails

**Possible Causes:**

1. **Wrong Secret Key:**
   - Verify you copied the Secret Key (not the Site Key)
   - Check for extra spaces or missing characters

2. **Server Time Issue:**
   - reCAPTCHA checks timestamps
   - Ensure your server clock is accurate

3. **Domain Mismatch:**
   - The domain you're using must match what's registered
   - Check the URL in your browser matches your registered domain

---

### Problem: CAPTCHA Shows "Invalid Domain"

**Solution:**
1. Go to [Google reCAPTCHA Admin](https://www.google.com/recaptcha/admin)
2. Find your site key
3. Click the gear icon to edit
4. Add your domain to the list (don't forget `www` version)
5. Save changes
6. Wait a few minutes for changes to propagate

---

### Problem: Users Always Get Image Challenges

This is normal behavior for:
- VPN users
- Users with ad blockers
- Users with suspicious browsing patterns
- Bots pretending to be users

**Note:** This is actually reCAPTCHA working correctly! It's being extra cautious with suspicious traffic.

**To reduce false positives:**
- Consider using reCAPTCHA v3 instead (invisible, score-based)
- Or use Cloudflare Turnstile (more user-friendly)

---

### Problem: CAPTCHA Breaks Page Layout

**Solutions:**

1. **Try Compact Size:**
   - Go to plugin settings
   - Change Size to "Compact"

2. **CSS Conflict:**
   - Check if your theme has CSS that interferes
   - May need custom CSS to fix layout

3. **Mobile Issues:**
   - Compact size works better on mobile
   - Consider making CAPTCHA full-width on small screens

---

## 🔒 Privacy & GDPR Compliance

### Data Collection

reCAPTCHA v2 collects:
- IP address
- Cookies
- User interaction data
- Browser information

This data is sent to Google for analysis.

### GDPR Requirements

If you serve EU users, you must:

1. **Update Privacy Policy:**
   - Disclose that you use Google reCAPTCHA
   - Explain what data is collected
   - Link to Google's Privacy Policy

2. **Example Privacy Policy Text:**
```
This site is protected by reCAPTCHA and the Google
Privacy Policy and Terms of Service apply.
```

3. **Cookie Consent (Optional but Recommended):**
   - Use a cookie consent plugin
   - Get consent before loading reCAPTCHA
   - Provide opt-out option

---

## 📊 Monitoring & Analytics

### View reCAPTCHA Stats

1. Go to [Google reCAPTCHA Admin](https://www.google.com/recaptcha/admin)
2. Click on your site
3. View the dashboard with:
   - Total verification requests
   - Blocked requests
   - Success rate
   - Suspicious activity

### Understanding the Data

- **High block rate:** Good! reCAPTCHA is protecting you
- **Low block rate:** Also good! You don't have much spam
- **100% blocks:** Something might be wrong with your setup

---

## 🆚 reCAPTCHA v2 vs v3

Not sure if you chose the right version? Here's a quick comparison:

| Feature | reCAPTCHA v2 | reCAPTCHA v3 |
|---------|--------------|--------------|
| User Interaction | Yes (checkbox) | No (invisible) |
| Image Challenges | Sometimes | Never |
| Bot Detection | Excellent | Excellent |
| User Experience | Good | Excellent |
| False Positives | Low | Medium |
| Setup Difficulty | Easy | Moderate |

**Switch to v3 if:** You want completely invisible protection and better UX

---

## 🚀 Best Practices

### 1. Don't Overprotect
- Don't add CAPTCHA to every single form
- Only protect forms that attract spam
- Consider user experience

**Good:**
- Login, Registration, Contact Forms

**Maybe Overkill:**
- Newsletter signup (unless you have spam issues)
- Search forms
- Filter forms

### 2. Test Different Settings
- Try both Light and Dark themes
- Test Normal and Compact sizes
- See what works best for your users

### 3. Monitor Performance
- Check your reCAPTCHA dashboard monthly
- Look for unusual patterns
- Adjust protection settings as needed

### 4. Provide Help Text
- Add instructions near the CAPTCHA
- Explain why CAPTCHA is required
- Provide alternative contact method if CAPTCHA fails

### 5. Mobile Optimization
- Use Compact size for mobile
- Test on real mobile devices
- Make sure checkbox is easy to tap

---

## 📚 Additional Resources

- **Google reCAPTCHA Homepage:** [https://www.google.com/recaptcha](https://www.google.com/recaptcha)
- **reCAPTCHA Developer Docs:** [https://developers.google.com/recaptcha](https://developers.google.com/recaptcha)
- **reCAPTCHA Admin Console:** [https://www.google.com/recaptcha/admin](https://www.google.com/recaptcha/admin)

---

## 🔄 Next Steps

- **[Compare All CAPTCHA Services](README.md)** - See if reCAPTCHA v2 is right for you
- **[Integration Guides](../integrations/wordpress-core.md)** - Learn about protecting specific forms
- **[Customer Guide Home](../README.md)** - Back to main documentation

---

**Need Help?** Check our [FAQ section](../README.md#frequently-asked-questions) or [contact support](../README.md#getting-help).
