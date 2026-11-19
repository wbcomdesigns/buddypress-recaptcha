# Advanced

> **Included in Free**

| Option | What it does |
|---|---|
| Theme and Size | Look of the widget for reCAPTCHA v2, hCaptcha and Turnstile. |
| Language | Widget language for reCAPTCHA v2 and hCaptcha. Empty uses the site language. |
| IP Whitelist | IPs, ranges and CIDR blocks that skip the CAPTCHA. |
| Block when the provider is down | Refuse submissions when the provider cannot be reached. Off by default. |
| No-conflict mode | Remove CAPTCHA scripts loaded by other plugins and themes. |
| Error messages | Text shown for a missing answer, an invalid answer and a failed lookup. |

## Locked out?

Add `define( 'WBC_CAPTCHA_DISABLE', true );` to wp-config.php, log in, fix the keys, then remove the line.
