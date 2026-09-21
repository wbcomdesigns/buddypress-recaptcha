# Login Widget and Block

The plugin includes two ready-made login surfaces, both CAPTCHA protected with the active provider.

## AJAX login widget

A login form widget you can place in any widget area. It submits over AJAX and shows inline messages without a full page reload. After a failed login the CAPTCHA resets, so a token cannot be reused.

- Form selector: `#wbc-ajax-login-form`
- Submit button: `.wbc-login-button`
- Messages container: `.wbc-form-messages`
- Registered widget class: `WBC_Login_Widget`

The widget follows BuddyX and Reign dark mode.

## Gutenberg login block

A block editor version of the same login form. Add the **CAPTCHA Login** block to any page or post.

- Block name: `wbc/login-widget`

## Enabling protection

Both surfaces use the `widget_login` context. Turn it on from the Protection tab (option `wbc_recaptcha_enable_on_widget_login`). The active provider's widget then renders inside the login form.

## Behind the scenes

The login request is handled by the `wbc_ajax_login` AJAX action, which verifies the CAPTCHA before authenticating. With reCAPTCHA v3 the form requests a fresh single-use token right before it sends, so the login is verified and sent once.
