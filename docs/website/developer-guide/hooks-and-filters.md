# Hooks and Filters

All hooks below are present in the plugin source. Signatures are taken from the code.

## Actions

### `wbc_register_captcha_services`

Fired after the five built-in providers register, so a plugin or theme can register its own. The service manager instance is passed.

```php
add_action( 'wbc_register_captcha_services', function ( $manager ) {
    $manager->register_service( new My_Captcha_Service() );
} );
```

See [Add a custom provider](add-a-custom-provider.md).

## Render and verify filters

### `wbc_should_render_captcha`

Return false to skip rendering the widget for a context. Honoured by every provider.

`apply_filters( 'wbc_should_render_captcha', bool $render, string $context, string $service_id )`

### `wbc_should_verify_captcha`

Return false to skip the verification for a context. Honoured by every provider. Pair it with `wbc_should_render_captcha` so the widget is hidden and the check is skipped together, otherwise a form can ask for a CAPTCHA that was never shown.

`apply_filters( 'wbc_should_verify_captcha', bool $verify, string $context, string $service_id )`

### `wbc_captcha_verified`

Filter the final verified result for reCAPTCHA v2, hCaptcha, Turnstile, and ALTCHA, with the raw provider response.

`apply_filters( 'wbc_captcha_verified', bool $verified, array $result, string $response, string $service_id )`

### `wbc_recaptcha_v3_verify`

Filter the reCAPTCHA v3 verified result after score and action checks.

`apply_filters( 'wbc_recaptcha_v3_verify', bool $verified, array $result, string $context )`

## Security and behaviour filters

### `wbc_captcha_fail_closed`

Whether to block a submission when the provider is unreachable or unconfigured. Defaults to the `wbc_captcha_fail_closed` option (off).

`apply_filters( 'wbc_captcha_fail_closed', bool $fail_closed, string $context )`

### `wbc_captcha_strict_nonce`

Whether the plugin nonce must be present and valid. Defaults to the `wbc_captcha_strict_nonce` option (off).

`apply_filters( 'wbc_captcha_strict_nonce', bool $strict, string $context, string $service_id )`

### `wbc_captcha_error_message`

Change the CAPTCHA error text for any protected form, for example a different message on checkout than on comments.

### `wbc_recaptcha_v3_score_threshold_value`

Override the reCAPTCHA v3 score threshold for a context.

`apply_filters( 'wbc_recaptcha_v3_score_threshold_value', float $threshold, string $context )`

### `wbc_recaptcha_v3_is_captcha_page`

Return true to pre-load the reCAPTCHA v3 script on a page the plugin cannot detect, for a form that renders very late.

`apply_filters( 'wbc_recaptcha_v3_is_captcha_page', bool $is_captcha_page )`

## Admin filter

### `bprc_admin_tabs`

Filter the admin tab definitions (Overview, Quick Setup, Protection, Advanced, Updates, Discover).

`apply_filters( 'bprc_admin_tabs', array $tabs )`
