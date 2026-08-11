# Wbcom CAPTCHA Manager - what it can and cannot do

Buyer-language answers to "can it do X?". Refresh this in the same pass as
`audit/manifest.json` so store and docs copy can never over- or under-claim.

**Version 2.2.0.** Requires WordPress 6.3+, PHP 7.4+. Free plugin, no Pro counterpart.

## Which CAPTCHA services can I use?

One at a time, chosen on the Quick Setup tab.

| Service | Status | Notes |
|---|---|---|
| Google reCAPTCHA v2 | **Yes** | The "I'm not a robot" checkbox |
| Google reCAPTCHA v3 | **Yes** | Invisible, scores each request against your threshold |
| Cloudflare Turnstile | **Yes** | |
| hCaptcha | **Yes** | Privacy-focused alternative |
| ALTCHA | **Yes** | Self-hosted proof-of-work, no third-party service. **Needs HTTPS** - see below |

Switching services is safe: only the selected one loads its script, so you never
end up with two CAPTCHAs on the same form.

## Which forms can it protect?

29 form contexts across 14 integrations. Each has its own on/off toggle on the
Protection tab, and a service you have not configured protects nothing.

| Area | Forms |
|---|---|
| WordPress core | Login, registration, lost password, comments |
| Login blocks and widgets | The core Login/Logout block, `wp_login_form()` theme forms, the plugin's own login widget and its AJAX login block |
| BuddyPress | Registration, group creation |
| bbPress | New topic, new reply |
| WooCommerce | Login, registration, lost password, guest checkout, logged-in checkout, pay-order, order tracking, product reviews |
| Easy Digital Downloads | Checkout, login, registration |
| MemberPress | Login, registration |
| Ultimate Member | Login, registration, password reset |
| Form builders | Contact Form 7, WPForms, Gravity Forms, Ninja Forms, Forminator, Elementor Pro, Divi contact forms |

## Does it work with my theme's login form?

**Yes.** Anything built with `wp_login_form()` is covered, which includes the core
Login/Logout block, most theme login modals, and the plugin's own widget and block.
Reign and BuddyX Pro login and registration forms are supported through their own
hooks.

## What it does NOT integrate with

Fluent Forms, Formidable Forms, WS Form and FluentCart. There is no code for any of
them. If you need one of these, this plugin will not protect it.

## Can I exempt my own IP?

**Yes.** The Advanced tab takes single addresses, ranges and CIDR blocks. An
allowlisted visitor sees no CAPTCHA and is not checked for one, on every service.

## Does ALTCHA work on any site?

**No - ALTCHA requires HTTPS.** It solves a proof-of-work in the browser using the
Web Crypto API, which browsers only expose in a secure context. On a plain HTTP site
the widget shows "Verifying..." forever and the form cannot be submitted. This is a
browser rule, not a plugin limit. The other four services work over HTTP.

## What happens if my keys are wrong or the service is down?

By default the form still submits - a misconfigured CAPTCHA does not lock people out
of your site - and an admin notice tells you the service is not configured. If you
would rather block submissions when verification cannot complete, turn on
fail-closed (`wbc_captcha_fail_closed`).

## Can I translate it?

**Yes.** German, Spanish, French, Italian and Portuguese (Brazil) ship with the
plugin, including the block editor sidebar. The CAPTCHA widget itself follows the
language setting on the Advanced tab for reCAPTCHA v2 and hCaptcha.

## Is it accessible and does it work on phones?

The admin screens are responsive down to 390px and follow the shared Wbcom admin
design tokens. The CAPTCHA widgets themselves are rendered by the upstream service,
so their accessibility is whatever that provider ships.

## What it does NOT do

- **No statistics or logging of blocked attempts.** There is no dashboard of spam
  caught; verification failures go to the WordPress debug log only.
- **No per-role rules.** Protection is per form, not per user role.
- **No reCAPTCHA v3 badge position control.** No badge rendering code has ever
  existed. Hiding or repositioning the badge would need Google's mandatory
  attribution text, so the control was removed rather than left doing nothing.
- **No multiple services at once.** One active service per site by design.
