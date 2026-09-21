# CAPTCHA Providers

The plugin ships five providers. One is active site-wide at a time, selected on the Quick Setup tab and stored in the `wbc_captcha_service` option. Every protected form uses the active provider. Switching providers is a single dropdown change and does not reset your per-form toggles.

Each provider reads its own site and secret key. Where a provider has no keys saved it reports as not configured, and forms stay unprotected until the keys are added.

## Google reCAPTCHA v2 (checkbox)

The familiar "I'm not a robot" checkbox. Loads Google's `api.js`, verifies against Google's `siteverify` endpoint. Supports a theme (light or dark), a size, and a language for the widget. Uses no-conflict mode to dequeue competing reCAPTCHA scripts.

- Site key option: `wbc_recaptcha_v2_site_key`
- Secret key option: `wbc_recaptcha_v2_secret_key`

## Google reCAPTCHA v3 (invisible)

Invisible verification that scores each request from 0.0 (likely bot) to 1.0 (likely human) instead of challenging the visitor. A submission below your score threshold is rejected. See [Advanced settings](../usage/advanced-settings.md) for the threshold.

- Site key option: `wbc_recaptcha_v3_site_key`
- Secret key option: `wbc_recaptcha_v3_secret_key`
- Score threshold option: `wbc_recaptcha_v3_score_threshold` (default 0.5 when unset)

## Cloudflare Turnstile

A privacy-friendlier alternative to Google. Loads Cloudflare's Turnstile `api.js` and verifies against Cloudflare's `siteverify` endpoint.

- Site key option: `wbc_turnstile_site_key`
- Secret key option: `wbc_turnstile_secret_key`

## hCaptcha

A privacy-focused checkbox alternative. Loads hCaptcha's `api.js` and verifies against `hcaptcha.com/siteverify`. Supports a theme, a size, and a language, the same way reCAPTCHA v2 does. The response field is `h-captcha-response`.

- Site key option: `wbc_hcaptcha_site_key`
- Secret key option: `wbc_hcaptcha_secret_key`

## ALTCHA (self-hosted)

Self-hosted proof of work. ALTCHA needs no third-party account and no API keys. It generates its own HMAC key when you select it and issues its challenge from a local REST endpoint (`/altcha/v1/challenge`), so it never contacts an external server.

- HMAC key option: `wbc_altcha_hmac_key` (generated for you)
- Challenge complexity option: `wbc_altcha_max_number` (default 100000)
- Challenge expiry in seconds: `wbc_altcha_expires` (default 3600)
- Auto-verify option: `wbc_altcha_auto_verify`
- Hide logo option: `wbc_altcha_hide_logo`

## Add your own provider

A theme or plugin can register an additional provider through the `wbc_register_captcha_services` action. It then appears in the provider picker next to the built-in five. See [Add a custom provider](../developer-guide/add-a-custom-provider.md).
