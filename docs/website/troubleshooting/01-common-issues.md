# Common Issues

## The CAPTCHA does not show

1. Check the Overview tab for a key or provider warning.
2. Check the form's switch on the Protection tab.
3. With ALTCHA, the site address must be HTTPS (or localhost). On any other address ALTCHA stays off, forms are not locked, and the Overview tab says why.
4. Turn off No-conflict mode if another script is being removed.

## The Overview tab says the provider rejected the keys

The provider refused this site's keys. Until you fix them, CAPTCHA checks are let through so nobody is locked out. The site key and secret key may not belong together, or may not be valid for this domain. Create a new key pair with the provider and save both again.

## Visitors see "Security verification failed"

The token was rejected or already used. Reload the form and solve it again. The Overview tab lists rejections from the last 7 days, naming each form as the Protection tab does, with the reason.

## Visitors see "Please complete the security check"

They submitted before solving the widget. This is expected.

## I am locked out of wp-login.php

Add `define( 'WBC_CAPTCHA_DISABLE', true );` to wp-config.php, fix the settings, then remove it.

## Another plugin also checks the same CAPTCHA

A token can be verified once. Protect each form with one plugin only.
