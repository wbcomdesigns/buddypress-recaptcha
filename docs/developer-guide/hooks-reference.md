# Hooks & Filters Reference

Complete reference of all hooks and filters available in Wbcom CAPTCHA Manager.

## 📚 Table of Contents

- [Action Hooks](#action-hooks)
- [Filter Hooks](#filter-hooks)
- [Common Use Cases](#common-use-cases)
- [Integration-Specific Hooks](#integration-specific-hooks)

---

## Action Hooks

### `wbc_register_captcha_services`

**Description:** The primary extension point. Fires once while the service manager boots, so a third-party plugin can register a custom CAPTCHA provider alongside the 5 built-in ones.

**Parameters:**
- `WBC_Captcha_Service_Manager $manager` - The service manager instance

**Example:**
```php
add_action( 'wbc_register_captcha_services', function( $manager ) {
    // $manager->register_service( new My_Custom_Captcha_Service() );
    // The custom service must implement WBC_Captcha_Service_Interface.
} );
```

---

## Filter Hooks

### Rendering & Verification Filters

#### `wbc_should_render_captcha`

**Since:** 2.2.1 (applied for all 5 providers; before that only reCAPTCHA v3 consulted it)

**Description:** Return `false` to hide the CAPTCHA widget for a specific context/request.

**Parameters:**
- `bool $should_render` - Whether to render the widget
- `string $context` - Form context, for example `wp_login`, `comment`, `bp_register`, `woo_checkout_guest`
- `string $service_id` - Active provider: `recaptcha-v2`, `recaptcha-v3`, `hcaptcha`, `turnstile`, `altcha`

**Example:**
```php
add_filter( 'wbc_should_render_captcha', function( $should_render, $context, $service_id ) {
    if ( 'comment' === $context && current_user_can( 'manage_options' ) ) {
        return false;
    }
    return $should_render;
}, 10, 3 );
```

**Always pair this with `wbc_should_verify_captcha` using the same condition.** If you hide the widget without also skipping verification, the form still requires a response the visitor was never shown.

---

#### `wbc_should_verify_captcha`

**Since:** 2.2.0 (honoured by all 5 providers; before that only reCAPTCHA v3 consulted it)

**Description:** Return `false` to skip CAPTCHA verification for a specific context/request. This filter can add exemptions, but it cannot re-require a CAPTCHA for users the Protection tab's "Comments: Skip for Logged-in Users" toggle already exempts.

**Parameters:**
- `bool $should_verify` - Whether to verify the response
- `string $context` - Form context
- `string $service_id` - Active provider ID

**Example (paired with the filter above):**
```php
add_filter( 'wbc_should_verify_captcha', function( $should_verify, $context, $service_id ) {
    if ( 'comment' === $context && current_user_can( 'manage_options' ) ) {
        return false;
    }
    return $should_verify;
}, 10, 3 );
```

---

### Error Message Filter

#### `wbc_captcha_error_message`

**Since:** 2.2.1

**Description:** Filters the CAPTCHA error message shown to the visitor. It runs after the provider's message, the admin messages on the **Advanced** tab and the built-in default are resolved, so it can override any of them for one form. Every integration builds its error through `wbc_get_captcha_error_message()`, so this one filter covers all protected forms, including the WooCommerce checkout error and the tooltip on a disabled submit button.

**Parameters:**
- `string $message` - The resolved error message
- `string $context` - Form context, for example `wp_login`, `wp_register`, `wp_lostpassword`, `comment`, `woo_login`, `woo_register`, `woo_checkout_guest`, `woo_checkout_login`, `bp_register`, `bp_group_create`, `bbpress_topic`, `bbpress_reply`, `cf7`, `wpforms`, `gravityforms`, `ninjaforms`, `forminator`, `elementorpro`, `divi`, `edd_checkout`, `edd_login`, `edd_register`, `memberpress_login`, `memberpress_register`, `um_login`, `um_register`, `um_password`, `widget_login`
- `string $service_id` - Active provider: `recaptcha-v2`, `recaptcha-v3`, `hcaptcha`, `turnstile`, `altcha`, or `''` when none is set up
- `string $error_type` - `blank` (CAPTCHA not completed), `invalid` (verification failed) or `no_response` (provider unreachable)

**Example:**
```php
add_filter( 'wbc_captcha_error_message', function( $message, $context, $service_id, $error_type ) {
    $messages = array(
        'wp_login'           => __( 'Please complete the security check to log in.', 'textdomain' ),
        'wp_register'        => __( 'Please verify you are human to create an account.', 'textdomain' ),
        'woo_checkout_guest' => __( 'Please complete verification to finalize your purchase.', 'textdomain' ),
        'cf7'                => __( 'Please verify you are human to send your message.', 'textdomain' ),
    );

    return $messages[ $context ] ?? $message;
}, 10, 4 );
```

---

### Verification Result Filters

#### `wbc_captcha_verified`

**Description:** Overrides the verification result after the provider's API call, for reCAPTCHA v2, hCaptcha, Turnstile and ALTCHA.

**Parameters:**
- `bool $verified` - The result from the provider
- `array $api_result` - The raw API response
- `string $response` - The response token submitted by the visitor
- `string $service_id` - Active provider ID

**Note:** No `$context` is passed. reCAPTCHA v3 does **not** fire this filter - use `wbc_recaptcha_v3_verify` for v3.

**Example:**
```php
add_filter( 'wbc_captcha_verified', function( $verified, $api_result, $response, $service_id ) {
    return $verified;
}, 10, 4 );
```

---

#### `wbc_recaptcha_v3_verify`

**Description:** reCAPTCHA v3's own verification filter (it operates on the score-bearing API result instead of a pass/fail response). This is v3's replacement for `wbc_captcha_verified`.

**Parameters:**
- `bool $verified` - The result from the score check
- `array $result` - The raw API response (includes the `score`)
- `string $context` - Form context

**Example:**
```php
add_filter( 'wbc_recaptcha_v3_verify', function( $verified, $result, $context ) {
    return $verified;
}, 10, 3 );
```

---

#### `wbc_recaptcha_v3_score_threshold_value`

**Description:** Overrides the reCAPTCHA v3 score threshold per context.

**Parameters:**
- `float $threshold` - Score threshold (0.0 to 1.0)
- `string $context` - Form context

**Example:**
```php
add_filter( 'wbc_recaptcha_v3_score_threshold_value', function( $threshold, $context ) {
    $thresholds = array(
        'wp_register' => 0.7, // More strict
        'wp_login'    => 0.6,
        'comment'     => 0.4, // More lenient
    );

    return $thresholds[ $context ] ?? $threshold;
}, 10, 2 );
```

---

### Security Filters

#### `wbc_captcha_fail_closed`

**Since:** 2.1.0

**Description:** Overrides the `wbc_captcha_fail_closed` option per request. When true, a missing/unconfigured provider or a `verify()` exception blocks the form instead of failing open.

**Parameters:**
- `bool $fail_closed`
- `string $context`

**Example:**
```php
add_filter( 'wbc_captcha_fail_closed', function( $fail_closed, $context ) {
    if ( 'woo_checkout_guest' === $context ) {
        return true;
    }
    return $fail_closed;
}, 10, 2 );
```

---

#### `wbc_captcha_strict_nonce`

**Since:** 2.1.0

**Description:** Overrides the `wbc_captcha_strict_nonce` option per request - when true, the per-context nonce is required. Wired in reCAPTCHA v2, hCaptcha and Turnstile only; it has no effect when reCAPTCHA v3 or ALTCHA is the active service.

**Parameters:**
- `bool $strict`
- `string $context`
- `string $service_id`

**Example:**
```php
add_filter( 'wbc_captcha_strict_nonce', function( $strict, $context, $service_id ) {
    return true;
}, 10, 3 );
```

---

## Common Use Cases

### Use Case 1: Hide and Skip CAPTCHA for Trusted Users

```php
function my_captcha_exemption( $value, $context, $service_id ) {
    if ( current_user_can( 'manage_options' ) ) {
        return false; // false = hide (should_render) / skip (should_verify)
    }
    return $value;
}
add_filter( 'wbc_should_render_captcha', 'my_captcha_exemption', 10, 3 );
add_filter( 'wbc_should_verify_captcha', 'my_captcha_exemption', 10, 3 );
```

---

### Use Case 2: Rate Limiting Based on Failed Attempts

```php
// Count failures via the post-API-call result (no $context available here).
add_filter( 'wbc_captcha_verified', function( $verified, $api_result, $response, $service_id ) {
    if ( ! $verified ) {
        $ip = $_SERVER['REMOTE_ADDR'] ?? '';
        $key = "captcha_fails_{$ip}";
        $attempts = (int) get_transient( $key );
        set_transient( $key, $attempts + 1, HOUR_IN_SECONDS );
    }
    return $verified;
}, 10, 4 );

// Block before verification runs once an IP is over the threshold.
add_filter( 'wbc_should_verify_captcha', function( $should_verify, $context, $service_id ) {
    $ip = $_SERVER['REMOTE_ADDR'] ?? '';
    $attempts = (int) get_transient( "captcha_fails_{$ip}" );

    if ( $attempts >= 10 ) {
        wp_die( 'Access denied. Too many failed CAPTCHA attempts.' );
    }

    return $should_verify;
}, 5, 3 );
```

---

### Use Case 3: Custom Error Messages Per Context

```php
add_filter( 'wbc_captcha_error_message', function( $message, $context, $service_id, $error_type ) {
    $messages = array(
        'wp_login'    => __( 'Please complete the security check to log in.', 'textdomain' ),
        'bp_register' => __( 'Please verify you are human to join our community.', 'textdomain' ),
    );

    return $messages[ $context ] ?? $message;
}, 10, 4 );
```

---

## Integration-Specific Hooks

### WordPress Core

```php
// Skip CAPTCHA on wp-login.php for a specific username.
function my_wp_login_exemption( $value, $context, $service_id ) {
    if ( 'wp_login' !== $context ) {
        return $value;
    }
    $whitelist = array( 'support' );
    $username  = isset( $_POST['log'] ) ? sanitize_user( wp_unslash( $_POST['log'] ) ) : '';
    return in_array( $username, $whitelist, true ) ? false : $value;
}
add_filter( 'wbc_should_render_captcha', 'my_wp_login_exemption', 10, 3 );
add_filter( 'wbc_should_verify_captcha', 'my_wp_login_exemption', 10, 3 );
```

For comments, use the built-in **Protection > WordPress Forms > Comments: Skip for Logged-in Users** toggle instead of a filter.

---

### WooCommerce

```php
// Skip checkout CAPTCHA for logged-in customers (context: woo_checkout_login).
function my_woo_checkout_login_exemption( $value, $context, $service_id ) {
    return 'woo_checkout_login' === $context ? false : $value;
}
add_filter( 'wbc_should_render_captcha', 'my_woo_checkout_login_exemption', 10, 3 );
add_filter( 'wbc_should_verify_captcha', 'my_woo_checkout_login_exemption', 10, 3 );

// Custom error on checkout (guest and logged-in).
add_filter( 'wbc_captcha_error_message', function( $message, $context ) {
    return in_array( $context, array( 'woo_checkout_guest', 'woo_checkout_login' ), true )
        ? __( 'Please complete security verification to place your order.', 'textdomain' )
        : $message;
}, 10, 2 );
```

---

### BuddyPress

```php
// Skip CAPTCHA for invited members (context: bp_register).
function my_bp_register_exemption( $value, $context, $service_id ) {
    if ( 'bp_register' !== $context ) {
        return $value;
    }
    return isset( $_GET['invite'] ) ? false : $value;
}
add_filter( 'wbc_should_render_captcha', 'my_bp_register_exemption', 10, 3 );
add_filter( 'wbc_should_verify_captcha', 'my_bp_register_exemption', 10, 3 );

// Custom registration error.
add_filter( 'wbc_captcha_error_message', function( $message, $context ) {
    return 'bp_register' === $context
        ? __( 'Please verify you are human to join our community.', 'textdomain' )
        : $message;
}, 10, 2 );
```

---

### Contact Form 7

There is no filter to exclude a specific CF7 form ID - `wbc_should_render_captcha` / `wbc_should_verify_captcha` only receive the `cf7` context, not a form ID. To skip CAPTCHA on one form, disable it for the CF7 integration entirely on the Protection tab, or add the field to individual forms manually instead of using the integration's auto-injection.

```php
// Custom error message for CF7.
add_filter( 'wbc_captcha_error_message', function( $message, $context ) {
    return 'cf7' === $context
        ? __( 'Please verify you are human to send your message.', 'textdomain' )
        : $message;
}, 10, 2 );
```

---

## Debug Hooks

### Enable Detailed Logging

```php
add_filter( 'wbc_should_verify_captcha', function( $should_verify, $context, $service_id ) {
    if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
        error_log( sprintf( '[CAPTCHA] Verifying - Context: %s, Service: %s', $context, $service_id ) );
    }
    return $should_verify;
}, 5, 3 );

add_filter( 'wbc_captcha_verified', function( $verified, $api_result, $response, $service_id ) {
    if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
        error_log( sprintf( '[CAPTCHA] Result - Service: %s, Success: %s', $service_id, $verified ? 'YES' : 'NO' ) );
    }
    return $verified;
}, 10, 4 );
```

`wbc_captcha_verified` does not receive `$context`, and reCAPTCHA v3 does not fire it at all - hook `wbc_recaptcha_v3_verify` separately to log v3 results with `$context`.

---

## Hook Priority Guidelines

**Rendering/verification:**
- Early (5): Blanket exemptions that should win over everything else
- Normal (10): Standard customization
- Late (15+): Final overrides

**Example:**
```php
// Run before the default check
add_filter( 'wbc_should_verify_captcha', 'my_early_exemption', 5, 3 );

// Run after (e.g. to log the final decision)
add_filter( 'wbc_should_verify_captcha', 'my_logging', 15, 3 );
```

---

## More Resources

- [Main Developer Guide](README.md) - Complete technical documentation
- [Creating Custom Integrations](README.md#creating-custom-integrations)
- [CAPTCHA Service API](README.md#captcha-service-api)
- [Testing & Debugging](README.md#testing--debugging)

---

**Need a hook that doesn't exist?** [Request it on GitHub](https://github.com/wbcomdesigns/buddypress-recaptcha/issues) or contact support.
