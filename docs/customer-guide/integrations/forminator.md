# Forminator Integration

Automatic CAPTCHA protection for all Forminator forms, polls, and quizzes. One-click setup for complete spam protection.

## 📋 Overview

Protects all Forminator features:
- Contact forms
- Registration forms
- Quote forms
- Polls
- Quizzes

---

## ⚙️ Quick Setup

### Step 1: Prerequisites

1. **Install Forminator** (free by WPMU DEV)
2. **Configure CAPTCHA** - [Service guides](../captcha-services/README.md)

### Step 2: Enable Protection

1. **Settings → Wbcom CAPTCHA Manager**
2. Scroll to **"Forminator"**
3. Check: ☑ Enable CAPTCHA on Forminator Forms
4. **Save Changes**

### Step 3: Test

1. Visit form/poll/quiz
2. Verify CAPTCHA appears
3. Test submission

---

## 🎨 Customization

### Exclude Forms

```php
add_filter( 'wbc_forminator_exclude_forms', function( $excluded ) {
    $excluded[] = 123; // Form ID
    return $excluded;
});
```

### Custom Error

```php
add_filter( 'wbc_forminator_error_message', function( $message ) {
    return 'Please complete the security check.';
});
```

---

## 🔧 Troubleshooting

**CAPTCHA Not Showing:**
- Forminator is active
- Plugin setting enabled
- CAPTCHA configured
- Clear caches

**Poll/Quiz Issues:**
- CAPTCHA works on all Forminator types
- Test each separately
- Check browser console for errors

---

## 🚀 Best Practices

**Enable for:**
- ✅ Contact forms
- ✅ Registration forms
- ✅ Polls (prevents vote manipulation)
- ✅ Quizzes (ensures genuine responses)

**CAPTCHA by Type:**
- Forms: Turnstile or reCAPTCHA v3
- Polls: reCAPTCHA v3 (better participation)
- Quizzes: Invisible CAPTCHA (don't distract)

---

## 🔒 Poll/Quiz Protection

**Why protect polls:**
- Prevents vote manipulation
- Ensures accurate results
- Stops bot voting

**Why protect quizzes:**
- Genuine responses only
- Accurate skill assessment
- Prevents automated completion

---

## 📚 Related

- [Turnstile Setup](../captcha-services/turnstile.md)
- [reCAPTCHA v3](../captcha-services/recaptcha-v3.md)
- [Contact Form 7](contact-form-7.md)

---

**Need Help?** [FAQ](../README.md#frequently-asked-questions) | [Support](../README.md#getting-help)
