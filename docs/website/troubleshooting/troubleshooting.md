# Troubleshooting

## The CAPTCHA is not appearing on my form

Check three things in order:

1. The active provider's API keys are saved (ALTCHA excepted).
2. The toggle for that specific form is on in the Protection tab.
3. You are viewing the form logged out. Admins and editors do not see the comment CAPTCHA.

Also confirm the integration's plugin is active. A form builder toggle only exists when that form builder is active.

## Admin notice: the service is not configured

The active provider is selected but has no keys saved, so forms are unprotected. Open the settings, add the site and secret keys, and save. This notice targets administrators only.

## Logins or submissions fail with "Security verification failed"

- With reCAPTCHA v3, this can happen if the form is submitted before the token has loaded. The plugin holds the submit briefly and sends once with a valid token, so retry after the page has fully loaded.
- Confirm the v3 keys are v3 keys, not v2 keys. The two are not interchangeable.
- If a competing CAPTCHA script from another plugin is on the page, enable no-conflict mode on the Advanced tab.

## Real users blocked by reCAPTCHA v3

Lower the score threshold on the Advanced tab. Anything below the threshold is rejected. Start at 0.5 and lower gradually.

## Listed IPs are still asked to solve the CAPTCHA

Confirm the address is in the IP whitelist on the Advanced tab and matches the visitor's real `REMOTE_ADDR`. The whitelist accepts single addresses, ranges, and CIDR notation, comma separated.

## Automatic updates are turned off

If the bundled update SDK package is incomplete, an admin notice reports that automatic updates are off. Reinstall the plugin from a complete package to restore them. CAPTCHA protection keeps working regardless.

## Sending a password reset or replying to a comment from the dashboard fails

Admin screens do not render the CAPTCHA, so admin-initiated password resets and comment replies are not subject to it. If you saw this on an older version, update to the current release. Front-end lost-password and comment forms stay protected.

## A form renders very late and loses its reCAPTCHA v3 token

Some page builders output forms after the footer scripts. Use the `wbc_recaptcha_v3_is_captcha_page` filter to pre-load the v3 script on that page. See [Hooks and filters](../developer-guide/hooks-and-filters.md).

## Where to get support

- Documentation: https://docs.wbcomdesigns.com/
- Support: https://wbcomdesigns.com/support/
- Report bugs and request features: https://wbcomdesigns.com/contact/
