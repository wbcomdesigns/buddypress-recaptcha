# Common Issues

## The CAPTCHA does not show

1. Check the Overview tab for a key or provider warning.
2. Check the form's switch on the Protection tab.
3. With ALTCHA, the site address must be HTTPS.
4. Turn off No-conflict mode if another script is being removed.

## Visitors see "Security verification failed"

The token was rejected or already used. Reload the form and solve it again. The Overview tab lists recent rejections with the reason.

## Visitors see "Please complete the security check"

They submitted before solving the widget. This is expected.

## I am locked out of wp-login.php

Add `define( 'WBC_CAPTCHA_DISABLE', true );` to wp-config.php, fix the settings, then remove it.

## Another plugin also checks the same CAPTCHA

A token can be verified once. Protect each form with one plugin only.
