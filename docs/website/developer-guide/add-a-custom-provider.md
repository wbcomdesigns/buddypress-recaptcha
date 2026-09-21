# Add a Custom Provider

You can register a CAPTCHA provider of your own. Once registered, it appears in the Quick Setup provider picker alongside the built-in five and can be made the active provider.

## The contract

A provider is a class that implements `WBC_Captcha_Service_Interface`. The abstract base class `WBC_Captcha_Service_Base` implements most of it for you (context maps, no-conflict handling, verify request helper), so extend the base and supply the provider-specific parts.

The interface requires these methods:

- `get_service_id()` returns a unique string id, for example `my-captcha`.
- `get_service_name()` returns the display name.
- `get_site_key()` and `get_secret_key()` read your stored keys.
- `is_configured()` returns whether the provider is ready.
- `get_script_url()` and `get_script_handle( $context )` describe the front-end script.
- `render( $context, $args )` emits the widget HTML.
- `verify( $response, $args )` calls your endpoint and returns a boolean.
- `get_verify_endpoint()` and `get_response_field_name()`.
- `requires_no_conflict()`, `get_container_attributes( $context )`, `is_enabled_for_context( $context )`.
- `get_option()` and `enqueue_scripts( $context )` are provided by the base class.

If your class defines `get_error_message()`, it must be `public`.

## Register it

Hook the `wbc_register_captcha_services` action and call `register_service()` on the manager instance passed to you.

```php
add_action( 'wbc_register_captcha_services', function ( $manager ) {
    require_once __DIR__ . '/class-my-captcha-service.php';
    $manager->register_service( new My_Captcha_Service() );
} );
```

## Honour the shared filters

So your provider behaves like the built-ins:

- Check `wbc_should_render_captcha` in your render path.
- Call the base class `should_skip_verification()` (or check `wbc_should_verify_captcha`) in your verify path, so a skipped check does not leave a form asking for a widget that was never shown.
- Apply `wbc_captcha_verified` to your final result.

See [Hooks and filters](hooks-and-filters.md) for signatures.
