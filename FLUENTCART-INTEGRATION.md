# FluentCart CAPTCHA Integration

This document describes the FluentCart integration added to Wbcom CAPTCHA Manager plugin (v2.1.0+).

## Overview

FluentCart login and registration forms are now protected with CAPTCHA to prevent spam accounts and brute-force attacks.

## Features Added

### 1. **FluentCart Customer Login Protection**
- Protects the `[fluent_cart_login_form]` shortcode
- Prevents brute-force login attempts
- Option: `wbc_recaptcha_enable_on_fluentcart_login`
- Context: `fluent_cart_login`

### 2. **FluentCart Customer Registration Protection**
- Protects the `[fluent_cart_registration_form]` shortcode
- Prevents fake account creation
- Option: `wbc_recaptcha_enable_on_fluentcart_register`
- Context: `fluent_cart_register`
- **Enabled by default**

## Files Created

```
buddypress-recaptcha/
└── public/
    └── fluentcart-extra/
        ├── FluentCartLogin.php         # Login form integration
        └── FluentCartRegistration.php  # Registration form integration
```

## Files Modified

### 1. **includes/class-recaptcha-for-buddypress.php**
- Added FluentCart detection: `class_exists( 'FluentCart\App\App' )`
- Loaded FluentCart integration classes
- Registered hooks for login and registration forms

**Lines Added:** 364-379

### 2. **admin/includes/class-wbc-buddypress-settings-page.php**
- Added "FluentCart Forms" section to Protection settings tab
- Two toggle options: Customer Login and Customer Registration
- Added options to registered settings array for saving

**Lines Added:** After line 769 and 2446

### 3. **includes/class-captcha-service-base.php**
- Added FluentCart contexts to `get_context_option_map()`
- Added FluentCart nonce actions to `get_nonce_action()`
- Added FluentCart form selectors to `get_form_selector()`

**Context Mappings Added:**
```php
'fluent_cart_login' => 'wbc_recaptcha_enable_on_fluentcart_login',
'fluent_cart_register' => 'wbc_recaptcha_enable_on_fluentcart_register',
```

## How It Works

### Registration Flow

1. User visits FluentCart registration form `[fluent_cart_registration_form]`
2. **Hook:** `fluent_cart/views/checkout_page_registration_form`
3. CAPTCHA field is rendered below registration fields
4. User submits form
5. **Hook:** `register_post` filter (priority 10)
6. CAPTCHA is verified before user account creation
7. If verification fails, registration is blocked with error message
8. If verification passes, user account is created normally

### Login Flow

1. User visits FluentCart login form `[fluent_cart_login_form]`
2. **Hook:** `fluent_cart/views/checkout_page_login_form`
3. CAPTCHA field is rendered below login fields
4. User submits credentials
5. **Hook:** `authenticate` filter (priority 20)
6. CAPTCHA is verified before authentication
7. If verification fails, login is blocked with error message
8. If verification passes, user is authenticated normally

## Admin Settings

**Location:** WordPress Admin → Settings → Wbcom CAPTCHA Manager → Protection Tab

**FluentCart Forms Section:**
- ✓ Customer Login (disabled by default)
- ✓ Customer Registration (enabled by default)

## Testing

### Enable FluentCart Registration CAPTCHA

```bash
# Via WP-CLI
wp option update wbc_recaptcha_enable_on_fluentcart_register yes

# Via PHP
update_option( 'wbc_recaptcha_enable_on_fluentcart_register', 'yes' );
```

### Enable FluentCart Login CAPTCHA

```bash
# Via WP-CLI
wp option update wbc_recaptcha_enable_on_fluentcart_login yes

# Via PHP
update_option( 'wbc_recaptcha_enable_on_fluentcart_login', 'yes' );
```

### Test Registration Form

1. Create a page with `[fluent_cart_registration_form]` shortcode
2. Enable FluentCart Registration CAPTCHA in settings
3. Visit the registration page
4. Verify CAPTCHA appears below form fields
5. Try submitting without solving CAPTCHA - should show error
6. Solve CAPTCHA and submit - should create account

### Test Login Form

1. Create a page with `[fluent_cart_login_form]` shortcode
2. Enable FluentCart Login CAPTCHA in settings
3. Visit the login page
4. Verify CAPTCHA appears below form fields
5. Try submitting without solving CAPTCHA - should show error
6. Solve CAPTCHA and submit - should log in successfully

### Test with Different CAPTCHA Services

Test all 5 supported CAPTCHA services:
- ✓ reCAPTCHA v2 (checkbox)
- ✓ reCAPTCHA v3 (invisible, score-based)
- ✓ Cloudflare Turnstile
- ✓ ALTCHA (self-hosted, proof-of-work)
- ✓ hCaptcha (privacy-focused)

## Integration Hooks Used

### FluentCart Registration

**Render Hook:**
```php
add_action( 'fluent_cart/views/checkout_page_registration_form',
    array( $instance, 'render_registration_captcha' ), 10, 1 );
```

**Validation Hook:**
```php
add_filter( 'register_post',
    array( $instance, 'validate_wp_registration_captcha' ), 10, 3 );
```

### FluentCart Login

**Render Hook:**
```php
add_action( 'fluent_cart/views/checkout_page_login_form',
    array( $instance, 'render_login_captcha' ), 10, 1 );
```

**Validation Hook:**
```php
add_filter( 'authenticate',
    array( $instance, 'validate_login_captcha' ), 20, 3 );
```

## Context Detection

Both integration classes detect FluentCart-specific form submissions by checking:
- `$_POST['fc_registration_nonce']` for registration
- `$_POST['fc_login_nonce']` for login
- `$_POST['fluent_cart_register']` for registration
- `$_POST['fluent_cart_login']` for login

This prevents CAPTCHA from being enforced on non-FluentCart forms.

## Error Messages

Error messages are retrieved via:
```php
wbc_get_captcha_error_message( $context, 'invalid' )
```

Default error message:
> "CAPTCHA verification failed. Please try again."

## IP Whitelist Support

FluentCart integration respects the IP whitelist setting:
- Option: `wbc_recaptcha_ip_to_skip_captcha`
- Function: `wb_recaptcha_restriction_recaptcha_by_ip()`
- If user IP is whitelisted, CAPTCHA is not rendered or validated

## Debug Logging

Enable WordPress debug mode to see CAPTCHA verification logs:

```php
// wp-config.php
define( 'WP_DEBUG', true );
define( 'WP_DEBUG_LOG', true );
```

**Log Format:**
```
[BuddyPress reCAPTCHA Debug] [fluent_cart_register] Rendering CAPTCHA
[BuddyPress reCAPTCHA Info] [fluent_cart_register] Verification failed
[BuddyPress reCAPTCHA Error] [fluent_cart_register] Error message
```

## Compatibility

- **WordPress:** 5.0+
- **PHP:** 7.4+
- **FluentCart:** 1.2.3+
- **Wbcom CAPTCHA Manager:** 2.1.0+

## Notes

1. **Registration is protected by default** - The `wbc_recaptcha_enable_on_fluentcart_register` option defaults to `yes` to prevent spam accounts immediately upon plugin activation.

2. **Login is not protected by default** - The `wbc_recaptcha_enable_on_fluentcart_login` option defaults to `no` to avoid friction for legitimate users. Enable it if you experience brute-force attacks.

3. **FluentCart must be active** - Integration classes only load if `FluentCart\App\App` class exists or `FLUENT_CART_VERSION` constant is defined.

4. **Form selectors** - Integration uses FluentCart's default CSS classes:
   - Login: `.fct-login-form`
   - Registration: `.fct-registration-form`

5. **AJAX compatibility** - Both forms may use AJAX. The integration handles both standard form submissions and AJAX requests.

## Future Enhancements

Possible future additions:
- FluentCart checkout form CAPTCHA (guest and logged-in)
- FluentCart password reset form
- Custom error message configuration per form
- Rate limiting integration
- Per-user CAPTCHA bypass (trusted users)

## Support

For issues or questions:
1. Check this documentation
2. Enable WP_DEBUG and check debug.log
3. Verify FluentCart is active and up to date
4. Test with default WordPress theme to rule out theme conflicts
5. Contact Wbcom Designs support

## Changelog

### Version 2.1.0 (2025-01-XX)
- ✨ Added FluentCart customer login form protection
- ✨ Added FluentCart customer registration form protection
- 📝 Added admin settings for FluentCart forms
- 🔧 Added context mappings for FluentCart integration
- 📚 Added FLUENTCART-INTEGRATION.md documentation

---

**Developed by:** Wbcom Designs
**Plugin:** Wbcom CAPTCHA Manager
**Version:** 2.1.0+
**Date:** January 2025
