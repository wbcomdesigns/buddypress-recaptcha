# Advanced Settings

The **Advanced** tab carries the tuning and hardening controls. All of them are optional.

## reCAPTCHA v3 score threshold

Shown when reCAPTCHA v3 is the active provider. Sets how strict the invisible score check is. A request scored below the threshold is rejected.

- Start at the default (0.5) and lower it only if real users get rejected.
- Raise it toward 1.0 for stricter filtering.
- One global value applies to every form.

## IP whitelist

Add trusted IP addresses that should skip the CAPTCHA. Listed addresses do not see the widget and are not asked to solve it. Accepts single addresses, ranges, and CIDR notation, comma separated. Matching uses `REMOTE_ADDR` only.

## Fail-closed mode

Off by default (the plugin fails open, allowing submissions through when verification cannot complete). Turn it on to block submissions when the provider is unreachable or unconfigured. Enable this only once you are confident the provider is set up correctly, or you can block your own users during an outage.

## No-conflict mode

Turn this on if another plugin or theme also loads a CAPTCHA script. It dequeues the competing scripts on the affected page so they do not collide with the active provider.

## Strict nonce mode

Off by default. When on, a submission must carry a valid plugin nonce or it is rejected. Leave it off unless you specifically need the stricter check.

## Appearance and language

For reCAPTCHA v2 and hCaptcha:

- Theme: light or dark.
- Size.
- Language: pick a language so the widget matches your site.

## Recommended setup order

1. Enable protection only on the forms that get spam (Protection tab).
2. Test each form logged out.
3. If you use reCAPTCHA v3, tune the score threshold here.
4. If a competing CAPTCHA script exists, enable no-conflict mode.
