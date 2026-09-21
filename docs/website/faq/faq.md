# Frequently Asked Questions

## Do I need BuddyPress to use this plugin?

No. BuddyPress, BuddyBoss Platform, WooCommerce, and bbPress are all optional. On a plain WordPress site the plugin still protects login, registration, lost password, and comments. An integration's settings appear only when that plugin is active.

## Which provider should I choose?

reCAPTCHA v2 is the familiar checkbox and the safest default. reCAPTCHA v3 is invisible and scores traffic instead of challenging it, which is better for conversions but needs threshold tuning. Turnstile and hCaptcha are privacy-friendlier alternatives to Google. ALTCHA is the choice if you want no third-party service at all.

## Can I use more than one provider at once?

No. One provider is active site-wide, on every protected form. Switching providers is a single dropdown change and does not reset your per-form toggles.

## Does ALTCHA really need no API keys?

Correct. ALTCHA is self-hosted proof of work. It generates its own HMAC key when you select it and never contacts an external server. The other four providers each need a free key pair from the provider.

## What happens if the provider goes down?

By default the plugin fails open: if the provider's API cannot be reached, the submission is allowed through, so an outage cannot lock your users out. If you would rather block submissions, enable fail-closed mode on the Advanced tab.

## Can I skip the CAPTCHA for my own IP?

Yes. Add trusted addresses to the IP whitelist on the Advanced tab. It accepts single IPs, ranges, and CIDR notation.

## Real users are being blocked by reCAPTCHA v3. What do I do?

Lower the score threshold on the Advanced tab. reCAPTCHA v3 scores each request from 0.0 (likely bot) to 1.0 (likely human), and anything below your threshold is rejected. If legitimate users fail, your threshold is too strict.

## Can I add a provider you do not ship?

Yes. Hook the `wbc_register_captcha_services` action and register a class implementing the service interface. See [Add a custom provider](../developer-guide/add-a-custom-provider.md).

## Is the plugin translation ready?

Yes. A POT file is included, along with a WPML configuration file. German, Spanish, French, Italian, and Brazilian Portuguese translations ship with the plugin.
