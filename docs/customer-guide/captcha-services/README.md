# CAPTCHA Services Comparison

Wbcom CAPTCHA Manager supports 5 different CAPTCHA services, each with unique features and benefits. This guide will help you choose the right one for your website.

## 📊 Quick Comparison Table

| Feature | reCAPTCHA v2 | reCAPTCHA v3 | hCaptcha | Turnstile | ALTCHA |
|---------|--------------|--------------|----------|-----------|---------|
| **Free Tier** | Yes | Yes | Yes | Yes | Yes |
| **Visibility** | Visible Checkbox | Invisible | Visible Challenge | Invisible | Visible/Invisible |
| **User Experience** | One Click | Seamless | Image Selection | Seamless | Proof-of-Work |
| **Privacy** | ⚠️ Google Tracking | ⚠️ Google Tracking | ✅ Privacy-Focused | ✅ Privacy-First | ✅ Fully Private |
| **GDPR Compliant** | Requires Disclosure | Requires Disclosure | Yes | Yes | Yes |
| **Setup Difficulty** | Easy | Very Easy | Easy | Very Easy | Easy |
| **Accessibility** | Good | Excellent | Good | Excellent | Good |
| **Bot Detection** | Excellent | Excellent | Excellent | Excellent | Good |
| **Best For** | High Security | User Experience | Privacy Compliance | Modern Sites | Open Source |

---

## 🔍 Detailed Service Comparison

### 1. Google reCAPTCHA v2

**Overview:** The classic "I'm not a robot" checkbox that most users are familiar with.

**Pros:**
- ✅ Familiar to users worldwide
- ✅ Strong bot detection powered by Google
- ✅ Easy to implement
- ✅ Accessible with audio challenges
- ✅ Free for most websites

**Cons:**
- ❌ Requires user interaction (one click minimum)
- ❌ Google collects user data
- ❌ May require additional challenges (image selection)
- ❌ GDPR requires privacy policy disclosure
- ❌ May impact page load time slightly

**Best Use Cases:**
- High-security requirements
- Government or financial websites
- Sites with spam problems
- Any site prioritizing security over UX

**Learn More:** [reCAPTCHA v2 Setup Guide](recaptcha-v2.md)

---

### 2. Google reCAPTCHA v3

**Overview:** Invisible CAPTCHA that analyzes user behavior without any user interaction.

**Pros:**
- ✅ Completely invisible to users
- ✅ No interruption to user flow
- ✅ Risk-based scoring system
- ✅ Works on mobile and desktop
- ✅ Free for most websites

**Cons:**
- ❌ Google collects extensive user data
- ❌ No fallback challenge for suspicious users
- ❌ Requires score threshold tuning
- ❌ GDPR requires privacy policy disclosure
- ❌ May block legitimate users in rare cases

**Best Use Cases:**
- E-commerce checkout flows
- User registration forms
- Sites prioritizing seamless UX
- High-traffic websites

**Learn More:** [reCAPTCHA v3 Setup Guide](recaptcha-v3.md)

---

### 3. hCaptcha

**Overview:** Privacy-focused alternative to reCAPTCHA with similar functionality.

**Pros:**
- ✅ Privacy-focused (doesn't sell user data)
- ✅ GDPR compliant by default
- ✅ Pays website owners (optional)
- ✅ Good bot detection
- ✅ Accessible design
- ✅ Free tier is generous

**Cons:**
- ❌ Requires user interaction (challenges)
- ❌ Less familiar to users than reCAPTCHA
- ❌ Image challenges can be difficult
- ❌ May slow down form submissions slightly

**Best Use Cases:**
- Privacy-conscious websites
- European websites (GDPR)
- Sites wanting to monetize CAPTCHA
- Organizations avoiding Google services

**Learn More:** [hCaptcha Setup Guide](hcaptcha.md)

---

### 4. Cloudflare Turnstile

**Overview:** Modern, privacy-first CAPTCHA that's completely invisible to most users.

**Pros:**
- ✅ Privacy-first (minimal data collection)
- ✅ Usually invisible to users
- ✅ Fast and lightweight
- ✅ No cookies, no tracking
- ✅ Free unlimited usage
- ✅ Excellent user experience
- ✅ Modern technology

**Cons:**
- ❌ Relatively new (less battle-tested)
- ❌ Requires Cloudflare account
- ❌ Limited customization options
- ❌ May require challenges for VPN users

**Best Use Cases:**
- Modern web applications
- Sites already using Cloudflare
- Privacy-focused organizations
- Sites prioritizing both UX and privacy

**Learn More:** [Cloudflare Turnstile Setup Guide](turnstile.md)

---

### 5. ALTCHA

**Overview:** Open-source, self-hosted CAPTCHA using proof-of-work challenges.

**Pros:**
- ✅ Fully open source
- ✅ No third-party tracking at all
- ✅ 100% GDPR compliant
- ✅ Self-hosted option available
- ✅ No external dependencies
- ✅ Customizable design
- ✅ Free forever

**Cons:**
- ❌ Less powerful bot detection than Google
- ❌ Requires computational work on user device
- ❌ May not work on very old browsers
- ❌ Smaller community and support
- ❌ Not as widely tested as others

**Best Use Cases:**
- Privacy-critical applications
- Open-source projects
- Self-hosted platforms
- Organizations avoiding all third-party services
- European organizations with strict GDPR requirements

**Learn More:** [ALTCHA Setup Guide](altcha.md)

---

## 🤔 How to Choose the Right Service

### Choose **reCAPTCHA v2** if:
- You need maximum security
- You handle sensitive data (financial, medical, legal)
- You have a serious spam problem
- Users are familiar with "I'm not a robot" checkbox

### Choose **reCAPTCHA v3** if:
- User experience is your top priority
- You have high-traffic forms (registration, checkout)
- You want invisible protection
- You're okay with Google tracking

### Choose **hCaptcha** if:
- Privacy is important but you still want strong protection
- You need GDPR compliance
- You want to avoid Google services
- You want to potentially earn revenue from CAPTCHA

### Choose **Cloudflare Turnstile** if:
- You want both privacy AND great user experience
- You're building a modern web application
- You already use Cloudflare
- You want free unlimited usage

### Choose **ALTCHA** if:
- Privacy is your absolute top priority
- You want open-source, transparent technology
- You need 100% GDPR compliance with no tracking
- You want to self-host everything
- You're in a highly regulated industry

---

## 🔒 Privacy Comparison

### Most Private (No Tracking):
1. **ALTCHA** - No third-party services at all
2. **Cloudflare Turnstile** - Minimal data, no cookies

### Privacy-Focused (Limited Tracking):
3. **hCaptcha** - Collects data but doesn't sell it

### Less Private (Google Tracking):
4. **reCAPTCHA v2** - Google tracks users
5. **reCAPTCHA v3** - Google tracks users extensively

**Privacy Tip:** If you're in the EU or serve EU customers, we recommend Turnstile, hCaptcha, or ALTCHA for easier GDPR compliance.

---

## 💰 Cost Comparison

All services offer generous free tiers suitable for most websites:

| Service | Free Tier Limit | Paid Plans |
|---------|-----------------|------------|
| **reCAPTCHA v2/v3** | 1 million calls/month | Free (enterprise options available) |
| **hCaptcha** | Unlimited for most sites | Free (enterprise options available) |
| **Turnstile** | Unlimited | Free |
| **ALTCHA** | Unlimited | Free (open source) |

**Note:** Unless you're running a very high-traffic website, you'll likely never need to pay for any of these services.

---

## 🚀 Performance Comparison

Impact on page load time (approximate):

1. **ALTCHA** - Lightest (no external scripts)
2. **Turnstile** - Very light
3. **reCAPTCHA v3** - Light (invisible)
4. **hCaptcha** - Moderate (loads challenge images)
5. **reCAPTCHA v2** - Moderate (checkbox widget)

**Performance Tip:** All services are optimized and have minimal impact. Choose based on your features needs, not performance.

---

## 📱 Mobile Experience

All services work on mobile devices:

- **Best Mobile UX:** reCAPTCHA v3, Turnstile (invisible)
- **Good Mobile UX:** reCAPTCHA v2 (one tap)
- **Moderate Mobile UX:** hCaptcha (image challenges on small screen)
- **Good Mobile UX:** ALTCHA (computational challenge)

---

## ♿ Accessibility

All services provide accessible alternatives:

- **reCAPTCHA v2/v3:** Audio challenges for visually impaired
- **hCaptcha:** Audio challenges available
- **Turnstile:** Usually no interaction needed
- **ALTCHA:** Computational challenge (screen reader friendly)

---

## 🔄 Can I Switch Services Later?

**Yes!** You can easily switch between services:

1. Get API keys for the new service
2. Go to plugin settings
3. Select the new service
4. Enter the new API keys
5. Save changes

Your protection settings (which forms to protect) remain the same.

---

## 📚 Next Steps

Ready to set up your CAPTCHA service?

1. **[Choose your service](#how-to-choose-the-right-service)** based on your needs
2. **Get API keys** using our step-by-step guides:
   - [Get reCAPTCHA v2 Keys](recaptcha-v2.md#getting-api-keys)
   - [Get reCAPTCHA v3 Keys](recaptcha-v3.md#getting-api-keys)
   - [Get hCaptcha Keys](hcaptcha.md#getting-api-keys)
   - [Get Turnstile Keys](turnstile.md#getting-api-keys)
   - [Setup ALTCHA](altcha.md#setup)
3. **[Configure the plugin](../README.md#step-3-configure-the-plugin)** with your keys
4. **[Enable protection](../README.md#step-4-test-your-setup)** on your forms

---

**Still unsure which to choose?** Start with **Cloudflare Turnstile** - it offers the best balance of privacy, user experience, and security.

**Need help?** Check our [FAQ section](../README.md#frequently-asked-questions) or [contact support](../README.md#getting-help).
