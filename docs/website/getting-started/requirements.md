# Requirements

## Environment

- WordPress 6.5 or higher.
- PHP 8.0 or higher.
- The block editor or the classic editor. Both are supported.

## API keys

Four of the five providers need a free key pair from the provider. Add the site key and the secret key on the plugin's settings screen and save. Until you do, no CAPTCHA renders and no form is protected.

- Google reCAPTCHA v2 and v3: keys from https://www.google.com/recaptcha/admin (v2 and v3 keys are not interchangeable, so create the type you selected).
- Cloudflare Turnstile: keys from https://dash.cloudflare.com/
- hCaptcha: keys from https://dashboard.hcaptcha.com/

ALTCHA needs no keys and no account. It is self-hosted and generates its own HMAC key when you select it, so it works as soon as you save.

## Optional integrations

BuddyPress is not required. On a plain WordPress site the plugin still protects login, registration, lost password, and comments.

Protection for these plugins appears only when the plugin is active:

- WooCommerce
- BuddyPress or BuddyBoss Platform
- bbPress
- Contact Form 7, WPForms, Gravity Forms, Ninja Forms, Forminator, Elementor Pro, Divi
- Easy Digital Downloads, MemberPress, Ultimate Member
