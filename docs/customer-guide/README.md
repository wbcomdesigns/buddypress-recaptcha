# Wbcom CAPTCHA Manager - Customer Guide

Welcome to Wbcom CAPTCHA Manager! This guide will help you protect your WordPress site from spam and bot attacks using industry-leading CAPTCHA services.

## 📚 Table of Contents

- [What is CAPTCHA?](#what-is-captcha)
- [Quick Start Guide](#quick-start-guide)
- [CAPTCHA Services](#captcha-services)
- [Integrations](#integrations)
- [Frequently Asked Questions](#frequently-asked-questions)
- [Getting Help](#getting-help)

---

## What is CAPTCHA?

CAPTCHA (Completely Automated Public Turing test to tell Computers and Humans Apart) is a security feature that helps protect your website from:

- **Spam Registration:** Automated bots creating fake user accounts
- **Spam Comments:** Bots posting unwanted comments on your blog
- **Brute Force Attacks:** Automated login attempts to crack passwords
- **Form Spam:** Bots submitting contact forms and other forms repeatedly

### How It Works

When CAPTCHA is enabled on a form, users must complete a challenge to prove they're human before submitting. This can be:
- Solving a puzzle (checkbox, image selection)
- Invisible verification (runs automatically in the background)
- Accessibility-friendly challenges

---

## Quick Start Guide

### Step 1: Choose Your CAPTCHA Service

Wbcom CAPTCHA Manager supports 5 different CAPTCHA services. Choose the one that best fits your needs:

| Service | Best For | Free Tier | Difficulty |
|---------|----------|-----------|------------|
| **reCAPTCHA v2** | Maximum security with visible checkbox | Yes | Easy |
| **reCAPTCHA v3** | Invisible, seamless experience | Yes | Very Easy |
| **hCaptcha** | Privacy-focused alternative to reCAPTCHA | Yes | Easy |
| **Cloudflare Turnstile** | Modern, privacy-first solution | Yes | Very Easy |
| **ALTCHA** | Open-source, no third-party tracking | Yes | Easy |

📖 [Detailed Comparison of CAPTCHA Services](captcha-services/README.md)

### Step 2: Get Your API Keys

Each CAPTCHA service requires API keys. Follow our step-by-step guides:

- [Get reCAPTCHA v2 Keys](captcha-services/recaptcha-v2.md#getting-api-keys)
- [Get reCAPTCHA v3 Keys](captcha-services/recaptcha-v3.md#getting-api-keys)
- [Get hCaptcha Keys](captcha-services/hcaptcha.md#getting-api-keys)
- [Get Turnstile Keys](captcha-services/turnstile.md#getting-api-keys)
- [Setup ALTCHA](captcha-services/altcha.md#setup)

### Step 3: Configure the Plugin

1. **Go to Settings:**
   - Navigate to `Settings → Wbcom CAPTCHA Manager` in your WordPress admin

2. **Select Your Service:**
   - Choose your preferred CAPTCHA service from the dropdown

3. **Enter Your Keys:**
   - Paste your Site Key and Secret Key
   - Click "Save Changes"

4. **Enable Protection:**
   - Choose which forms you want to protect
   - Enable the checkboxes for each form type
   - Save your settings

### Step 4: Test Your Setup

1. **Visit a Protected Form:**
   - Go to your login page, registration form, or any protected form

2. **Verify CAPTCHA Appears:**
   - You should see the CAPTCHA widget on the form

3. **Test Submission:**
   - Complete the CAPTCHA and submit the form
   - Verify it works correctly

🎉 **That's it!** Your site is now protected from spam and bots.

---

## CAPTCHA Services

Detailed guides for each CAPTCHA service:

### Google reCAPTCHA

- **[reCAPTCHA v2 Checkbox](captcha-services/recaptcha-v2.md)** - Classic "I'm not a robot" checkbox
- **[reCAPTCHA v3](captcha-services/recaptcha-v3.md)** - Invisible, risk-based verification

### Alternatives to Google reCAPTCHA

- **[hCaptcha](captcha-services/hcaptcha.md)** - Privacy-focused, GDPR-compliant alternative
- **[Cloudflare Turnstile](captcha-services/turnstile.md)** - Modern, privacy-first CAPTCHA
- **[ALTCHA](captcha-services/altcha.md)** - Open-source, self-hosted solution

---

## Integrations

Wbcom CAPTCHA Manager protects forms from various plugins and systems:

### WordPress Core
- **[WordPress Core Forms](integrations/wordpress-core.md)** - Login, Registration, Lost Password, Comments

### Community & Membership

- **[BuddyPress](integrations/buddypress.md)** - Member Registration, Group Creation
- **[bbPress](integrations/bbpress.md)** - Topic & Reply Forms
- **[MemberPress](integrations/memberpress.md)** - Login & Registration
- **[Ultimate Member](integrations/ultimate-member.md)** - Login & Registration

### E-Commerce

- **[WooCommerce](integrations/woocommerce.md)** - Login, Registration, Checkout
- **[Easy Digital Downloads](integrations/easy-digital-downloads.md)** - Checkout, Login, Registration

### Form Builders

- **[Contact Form 7](integrations/contact-form-7.md)** - Contact Forms
- **[WPForms](integrations/wpforms.md)** - All Form Types
- **[Gravity Forms](integrations/gravity-forms.md)** - All Form Types
- **[Ninja Forms](integrations/ninja-forms.md)** - All Form Types
- **[Forminator](integrations/forminator.md)** - All Form Types

### Page Builders

- **[Elementor Pro](integrations/elementor-pro.md)** - Form Widgets
- **[Divi Builder](integrations/divi-builder.md)** - Contact Form Modules

---

## Frequently Asked Questions

### General Questions

**Q: Do I need to install all CAPTCHA services?**
No, you only need to choose one CAPTCHA service and get its API keys.

**Q: Can I use different CAPTCHA services for different forms?**
No, currently one CAPTCHA service is used across all forms. You can choose which service to use globally.

**Q: Is CAPTCHA free?**
Yes, all supported CAPTCHA services offer generous free tiers suitable for most websites.

**Q: Will CAPTCHA slow down my site?**
CAPTCHA services are loaded only on pages with forms, and the impact is minimal.

### Troubleshooting

**Q: CAPTCHA doesn't appear on my form**
- Verify the plugin is activated
- Check that CAPTCHA is enabled for that specific form type in settings
- Clear your browser cache and reload the page
- Check for JavaScript errors in browser console

**Q: CAPTCHA validation fails even when completed correctly**
- Verify your API keys are correct (Site Key and Secret Key)
- Check that your domain is allowed in the CAPTCHA service dashboard
- Ensure your server can connect to the CAPTCHA service API

**Q: CAPTCHA appears but looks broken or unstyled**
- Clear browser cache
- Check for JavaScript conflicts with other plugins
- Try a different CAPTCHA service to isolate the issue

### Privacy & Compliance

**Q: Is CAPTCHA GDPR compliant?**
It depends on the service:
- **ALTCHA:** Fully GDPR compliant (no third-party tracking)
- **Cloudflare Turnstile:** Privacy-focused, GDPR friendly
- **hCaptcha:** GDPR compliant with proper configuration
- **reCAPTCHA:** Requires privacy policy disclosure (Google collects data)

**Q: Which CAPTCHA service is most privacy-friendly?**
ALTCHA and Cloudflare Turnstile are the most privacy-friendly options.

---

## Getting Help

### Documentation

- 📖 [Customer Guides](../customer-guide/) - User-friendly guides for all features
- 🔧 [Developer Guides](../developer-guide/) - Technical documentation for developers

### Support Resources

- 🐛 **Bug Reports:** [GitHub Issues](https://github.com/wbcomdesigns/buddypress-recaptcha/issues)
- 💬 **Community Support:** [WordPress.org Forums](https://wordpress.org/support/plugin/buddypress-recaptcha/)
- 📧 **Premium Support:** Contact us at [admin@wbcomdesigns.com](mailto:admin@wbcomdesigns.com)

### Useful Links

- **Plugin Website:** [https://wbcomdesigns.com/](https://wbcomdesigns.com/)
- **Documentation:** [https://wbcomdesigns.com/docs/](https://wbcomdesigns.com/docs/)
- **Changelog:** [CHANGELOG.md](../../CHANGELOG.md)

---

## Next Steps

1. **[Choose Your CAPTCHA Service →](captcha-services/README.md)**
2. **[Configure Your First Integration →](integrations/wordpress-core.md)**
3. **[Explore Advanced Features →](../developer-guide/README.md)**

---

**Thank you for using Wbcom CAPTCHA Manager!** 🛡️

We're committed to keeping your WordPress site secure and spam-free.
