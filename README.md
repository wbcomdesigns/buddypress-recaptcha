# Wbcom CAPTCHA Manager

Protect WordPress, WooCommerce, BuddyPress, bbPress and popular form builders from spam and bots with reCAPTCHA, Cloudflare Turnstile, hCaptcha or ALTCHA, from one settings screen.

Current release: 2.2.4. Free, GPLv2 or later.

## What it does

Pick one CAPTCHA provider, add its keys, and switch protection on per form. Every protected form follows the active provider. A form's settings only appear when its plugin is active.

**Providers** (one active at a time)

- reCAPTCHA v2 (checkbox) and reCAPTCHA v3 (invisible, score based)
- Cloudflare Turnstile
- hCaptcha
- ALTCHA: self-hosted proof of work, no account, no API keys, no third-party call. Needs a site on HTTPS (or localhost).

**Protected forms** (14 integrations)

| Integration | Forms |
|---|---|
| WordPress | Login, registration, lost password, comments |
| WooCommerce | Login, registration, lost password, product reviews, checkout (block and classic) |
| BuddyPress | Member registration, group creation, Contact Me |
| bbPress | New topics, replies |
| Form builders | Contact Form 7, WPForms, Gravity Forms, Ninja Forms, Forminator, Elementor Pro Forms, Divi contact forms |
| Stores and memberships | Easy Digital Downloads, MemberPress, Ultimate Member |

Also included: an AJAX login widget and Login block, per-form reCAPTCHA v3 score thresholds, an IP whitelist, optional fail-closed mode when the provider is down, editable error messages, and an Overview tab with key warnings and recent rejections.

## Who it is for

Site owners and agencies running membership, community, store or forum sites who want one CAPTCHA plugin instead of one per form plugin.

## Get it

- Store page: https://wbcomdesigns.com/downloads/buddypress-recaptcha/
- Release notes: https://wbcomdesigns.com/release-notes/buddypress-recaptcha/

## Documentation

User guides and developer docs: https://docs.wbcomdesigns.com/woo-family/buddypress-recaptcha/. The same pages live in [docs/website](docs/website).

## Requirements

- WordPress 6.5 or higher (tested up to 7.1)
- PHP 8.1 or higher
- Free API keys from the provider (not needed for ALTCHA)
- BuddyPress, WooCommerce, bbPress and the form plugins are optional

## Installation

1. Upload the `buddypress-recaptcha` folder to `/wp-content/plugins/`, or install the zip from Plugins > Add New > Upload Plugin.
2. Activate the plugin.
3. Open WB Plugins > CAPTCHA and go to the Quick Setup tab.
4. Choose a provider and enter its keys.
5. On the Protection tab, switch on the forms you want checked.

Locked out? Add `define( 'WBC_CAPTCHA_DISABLE', true );` to `wp-config.php`, fix the settings, then remove the line.

## Developer information

- Text domain: `buddypress-recaptcha`
- Hook prefix: `wbc_` (for example `wbc_register_captcha_services`, `wbc_should_render_captcha`, `wbc_should_verify_captcha`, `wbc_captcha_error_message`, `wbc_captcha_fail_closed`, `wbc_captcha_strict_nonce`)
- Hooks reference: [docs/website/developer-guide/01-hooks-reference.md](docs/website/developer-guide/01-hooks-reference.md)
- Login block: `wbc/login-widget`

## Support and licence

Support: https://wbcomdesigns.com/contact/

Licensed under GPLv2 or later. See [LICENSE.txt](LICENSE.txt).
