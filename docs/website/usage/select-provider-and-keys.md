# Select a Provider and Add Keys

Everything starts on the **Quick Setup** tab, at **WB Plugins > CAPTCHA**.

## Steps

1. Open the **Quick Setup** tab.
2. Choose one provider from the picker. The choice is saved to the `wbc_captcha_service` option and applies to every protected form.
3. Enter the site key and secret key for that provider (ALTCHA is the exception, below), then save.

Until valid keys are saved, no CAPTCHA renders and no form is protected. An admin notice appears when the active provider is enabled but not configured.

## Where to get keys

| Provider | Get keys at | Notes |
|---|---|---|
| reCAPTCHA v2 | https://www.google.com/recaptcha/admin | Create a v2 (checkbox) key |
| reCAPTCHA v3 | https://www.google.com/recaptcha/admin | Create a v3 key; v2 and v3 keys are not interchangeable |
| Cloudflare Turnstile | https://dash.cloudflare.com/ | |
| hCaptcha | https://dashboard.hcaptcha.com/ | |
| ALTCHA | not needed | Self-hosted; the HMAC key is generated for you |

## ALTCHA needs no keys

When you select ALTCHA and save, the plugin generates its own HMAC key (`wbc_altcha_hmac_key`) and serves its challenge from the local `/altcha/v1/challenge` REST endpoint. No external account is required and no third-party service is contacted.

## Switching providers later

Change the provider on the Quick Setup tab at any time. Your per-form toggles are preserved, so protection follows the new provider on every form that was already enabled.

Next: turn protection on per form in [Enable protection](enable-protection.md).
