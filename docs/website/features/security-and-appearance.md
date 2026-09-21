# Security and Appearance

Beyond on and off, the Advanced tab carries controls that change how strictly the CAPTCHA behaves and how it looks.

## Fail-closed mode

By default the plugin fails open: if the provider's API cannot be reached, or the active provider is not configured, the submission is allowed through, so a provider outage cannot lock your users out. Enable fail-closed mode to block submissions instead when verification cannot complete.

- Option: `wbc_captcha_fail_closed` (default off)
- Filter: `wbc_captcha_fail_closed`

## IP whitelist

Skip the CAPTCHA for trusted IP addresses. When a request comes from a listed IP, the widget is not rendered and the check is not required, so listed addresses are never asked to solve a CAPTCHA that was never shown. Accepts single addresses, ranges, and CIDR notation, comma separated. The check uses `REMOTE_ADDR` only, which cannot be spoofed through headers.

- Option: `wbc_recaptcha_ip_to_skip_captcha`

## reCAPTCHA v3 score threshold

Sets how strict the invisible v3 score check is. reCAPTCHA v3 scores each request from 0.0 (likely bot) to 1.0 (likely human), and anything below your threshold is rejected. A single global value applies to every form.

- Option: `wbc_recaptcha_v3_score_threshold` (default 0.5)
- Filter: `wbc_recaptcha_v3_score_threshold_value`

## No-conflict mode

If another plugin or theme also loads a CAPTCHA script, no-conflict mode dequeues the competing scripts on the affected page so they do not fight with the active provider.

## Strict nonce mode

By default the plugin validates its per-form nonce only when it is present. Strict nonce mode requires the nonce to be present and valid, and rejects the submission otherwise.

- Option: `wbc_captcha_strict_nonce` (default off)
- Filter: `wbc_captcha_strict_nonce`

## Appearance and language

For reCAPTCHA v2 and hCaptcha you can set a theme (light or dark) and a size, and a language so the widget matches your site.

- Theme and size resolve from the provider's own key and fall back to the shared `wbc_recaptcha_theme` and `wbc_recaptcha_size` keys.
- Language option: `wbc_recaptcha_language`

## Disable submit until solved

For reCAPTCHA v2 and hCaptcha, individual contexts can keep the submit button disabled until the check is completed. reCAPTCHA and Turnstile scripts load deferred for better page-load performance.
