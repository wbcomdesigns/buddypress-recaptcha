# CAPTCHA Providers

> **Included in Free**

One provider is active at a time. Switching it on Quick Setup changes every protected form.

| Provider | Account needed | Notes |
|---|---|---|
| reCAPTCHA v2 | Yes, free Google keys | Checkbox. Theme, size and language options. |
| reCAPTCHA v3 | Yes, free Google keys | Invisible, score based. Set a score threshold. |
| Cloudflare Turnstile | Yes, free Cloudflare keys | Theme and size options. |
| hCaptcha | Yes, free hCaptcha keys | Theme, size and language options. |
| ALTCHA | No | Self-hosted proof of work. Needs a site on HTTPS (or localhost). |

v2 and v3 keys are not interchangeable: create the type you selected.

## ALTCHA on a site without HTTPS

ALTCHA cannot run in a browser on a plain HTTP address. The plugin leaves it off there instead of locking visitors out, and the Overview tab says why.

## If the provider cannot be reached

By default a visitor who solved the CAPTCHA is let through when the provider is down. Turn on Block when the provider is down (Advanced tab) to refuse instead.
