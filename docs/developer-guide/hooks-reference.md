# Hooks & Filters Reference

Complete reference of all hooks and filters available in Wbcom CAPTCHA Manager.

## 📚 Table of Contents

- [Action Hooks](#action-hooks)
- [Filter Hooks](#filter-hooks)
- [Common Use Cases](#common-use-cases)
- [Integration-Specific Hooks](#integration-specific-hooks)

---

## Action Hooks

### Rendering Actions

#### `wbc_before_captcha_render`

**Description:** Fires before CAPTCHA widget is rendered.

**Parameters:**
- `string $context` - Form context (e.g., 'login', 'registration', 'checkout')
- `array $args` - Additional arguments passed to render function

**Example:**
```php
add_action( 'wbc_before_captcha_render', function( $context, $args ) {
    // Add custom HTML before CAPTCHA
    if ( $context === 'checkout' ) {
        echo '<div class="checkout-security-notice">';
        echo '<p>Please verify you are human to complete your purchase.</p>';
        echo '</div>';
    }
}, 10, 2 );
```

---

#### `wbc_after_captcha_render`

**Description:** Fires after CAPTCHA widget is rendered.

**Parameters:**
- `string $context` - Form context
- `array $args` - Additional arguments

**Example:**
```php
add_action( 'wbc_after_captcha_render', function( $context, $args ) {
    // Add privacy notice after CAPTCHA
    echo '<p class="captcha-privacy-notice">';
    echo 'Protected by reCAPTCHA. <a href="#">Privacy Policy</a>';
    echo '</p>';
}, 10, 2 );
```

---

#### `wbc_captcha_scripts_enqueued`

**Description:** Fires after CAPTCHA scripts are enqueued.

**Parameters:**
- `string $service_id` - Active CAPTCHA service ID

**Example:**
```php
add_action( 'wbc_captcha_scripts_enqueued', function( $service_id ) {
    // Add custom JavaScript after CAPTCHA scripts
    if ( $service_id === 'recaptcha_v3' ) {
        wp_add_inline_script( 'recaptcha-v3', '
            console.log("reCAPTCHA v3 loaded");
        ' );
    }
} );
```

---

### Validation Actions

#### `wbc_before_captcha_validation`

**Description:** Fires before CAPTCHA validation starts.

**Parameters:**
- `string $context` - Form context
- `array $post_data` - Submitted form data

**Example:**
```php
add_action( 'wbc_before_captcha_validation', function( $context, $post_data ) {
    // Log validation attempts
    error_log( "CAPTCHA validation starting for: {$context}" );

    // Track analytics
    if ( function_exists( 'track_event' ) ) {
        track_event( 'captcha_validation_start', array( 'context' => $context ) );
    }
}, 10, 2 );
```

---

#### `wbc_after_captcha_validation`

**Description:** Fires after CAPTCHA validation completes.

**Parameters:**
- `array $result` - Validation result (`success` and `message`)
- `string $context` - Form context
- `array $post_data` - Submitted form data

**Example:**
```php
add_action( 'wbc_after_captcha_validation', function( $result, $context, $post_data ) {
    // Log failed attempts
    if ( ! $result['success'] ) {
        error_log( "CAPTCHA failed for {$context}: {$result['message']}" );

        // Track failed attempts by IP
        $ip = $_SERVER['REMOTE_ADDR'];
        $attempts = get_transient( "captcha_fails_{$ip}" ) ?: 0;
        set_transient( "captcha_fails_{$ip}", $attempts + 1, HOUR_IN_SECONDS );
    }
}, 10, 3 );
```

---

#### `wbc_captcha_validation_failed`

**Description:** Fires when CAPTCHA validation fails.

**Parameters:**
- `string $context` - Form context
- `string $error_message` - Error message
- `string $service_id` - CAPTCHA service that failed

**Example:**
```php
add_action( 'wbc_captcha_validation_failed', function( $context, $error_message, $service_id ) {
    // Send alert for suspicious activity
    $ip = $_SERVER['REMOTE_ADDR'];
    $attempts = get_transient( "captcha_fails_{$ip}" ) ?: 0;

    if ( $attempts > 5 ) {
        // Alert admin or block IP
        wp_mail(
            get_option( 'admin_email' ),
            'Multiple CAPTCHA Failures',
            "IP {$ip} has failed CAPTCHA {$attempts} times"
        );
    }
}, 10, 3 );
```

---

#### `wbc_captcha_validation_success`

**Description:** Fires when CAPTCHA validation succeeds.

**Parameters:**
- `string $context` - Form context
- `string $service_id` - CAPTCHA service used

**Example:**
```php
add_action( 'wbc_captcha_validation_success', function( $context, $service_id ) {
    // Clear failed attempts on success
    $ip = $_SERVER['REMOTE_ADDR'];
    delete_transient( "captcha_fails_{$ip}" );

    // Track successful validations
    if ( function_exists( 'track_event' ) ) {
        track_event( 'captcha_success', array(
            'context' => $context,
            'service' => $service_id,
        ) );
    }
}, 10, 2 );
```

---

## Filter Hooks

### Rendering Filters

#### `wbc_captcha_html`

**Description:** Filters the CAPTCHA widget HTML.

**Parameters:**
- `string $html` - CAPTCHA HTML output
- `string $context` - Form context
- `string $service_id` - Active service ID

**Example:**
```php
add_filter( 'wbc_captcha_html', function( $html, $context, $service_id ) {
    // Add custom wrapper
    $wrapper = '<div class="custom-captcha-wrapper" data-context="' . esc_attr( $context ) . '">';
    $wrapper .= $html;
    $wrapper .= '</div>';

    return $wrapper;
}, 10, 3 );
```

---

#### `wbc_captcha_container_class`

**Description:** Filters the CAPTCHA container CSS classes.

**Parameters:**
- `array $classes` - Array of CSS classes
- `string $context` - Form context

**Example:**
```php
add_filter( 'wbc_captcha_container_class', function( $classes, $context ) {
    // Add context-specific class
    $classes[] = 'captcha-' . $context;

    // Add mobile class
    if ( wp_is_mobile() ) {
        $classes[] = 'captcha-mobile';
    }

    return $classes;
}, 10, 2 );
```

---

#### `wbc_captcha_position`

**Description:** Filters where CAPTCHA appears in form.

**Parameters:**
- `string $position` - Position ('before_submit', 'after_submit', 'custom')
- `string $context` - Form context

**Example:**
```php
add_filter( 'wbc_captcha_position', function( $position, $context ) {
    // Place checkout CAPTCHA before payment
    if ( $context === 'checkout' ) {
        return 'before_payment';
    }

    return $position;
}, 10, 2 );
```

---

### Validation Filters

#### `wbc_skip_captcha`

**Description:** Determines if CAPTCHA should be skipped.

**Parameters:**
- `bool $skip` - Whether to skip CAPTCHA
- `string $context` - Form context
- `int $user_id` - Current user ID (0 if not logged in)

**Example:**
```php
add_filter( 'wbc_skip_captcha', function( $skip, $context, $user_id ) {
    // Skip for administrators
    if ( user_can( $user_id, 'manage_options' ) ) {
        return true;
    }

    // Skip for trusted users (example: verified customers)
    if ( $context === 'checkout' && $user_id > 0 ) {
        $order_count = wc_get_customer_order_count( $user_id );
        if ( $order_count > 5 ) {
            return true; // Skip for repeat customers
        }
    }

    return $skip;
}, 10, 3 );
```

---

#### `wbc_captcha_validation_result`

**Description:** Filters the CAPTCHA validation result.

**Parameters:**
- `array $result` - Validation result with 'success' and 'message'
- `string $context` - Form context
- `string $response` - CAPTCHA response token

**Example:**
```php
add_filter( 'wbc_captcha_validation_result', function( $result, $context, $response ) {
    // Add custom validation layer
    if ( $result['success'] && $context === 'registration' ) {
        // Check if email domain is blacklisted
        $email = isset( $_POST['email'] ) ? sanitize_email( $_POST['email'] ) : '';
        $domain = substr( strrchr( $email, '@' ), 1 );

        $blacklist = array( 'tempmail.com', 'fakeemail.com' );
        if ( in_array( $domain, $blacklist, true ) ) {
            return array(
                'success' => false,
                'message' => 'Please use a valid email address.',
            );
        }
    }

    return $result;
}, 10, 3 );
```

---

#### `wbc_captcha_error_message`

**Description:** Filters the CAPTCHA error message.

**Parameters:**
- `string $message` - Error message
- `string $context` - Form context
- `string $service_id` - CAPTCHA service ID

**Example:**
```php
add_filter( 'wbc_captcha_error_message', function( $message, $context, $service_id ) {
    // Customize messages by context
    $messages = array(
        'login'        => __( 'Please complete the security check to log in.', 'textdomain' ),
        'registration' => __( 'Please verify you are human to create an account.', 'textdomain' ),
        'checkout'     => __( 'Please complete verification to finalize your purchase.', 'textdomain' ),
        'contact'      => __( 'Please verify you are human to send your message.', 'textdomain' ),
    );

    return $messages[ $context ] ?? $message;
}, 10, 3 );
```

---

### Service Configuration Filters

#### `wbc_active_captcha_service`

**Description:** Filters which CAPTCHA service to use.

**Parameters:**
- `string $service_id` - Current service ID
- `string $context` - Form context

**Example:**
```php
add_filter( 'wbc_active_captcha_service', function( $service_id, $context ) {
    // Use stricter service for checkout
    if ( $context === 'checkout' ) {
        return 'recaptcha_v2'; // Visible checkbox
    }

    // Use invisible for comments
    if ( $context === 'comment' ) {
        return 'turnstile';
    }

    return $service_id;
}, 10, 2 );
```

---

#### `wbc_captcha_service_args`

**Description:** Filters arguments passed to CAPTCHA service.

**Parameters:**
- `array $args` - Service arguments (theme, size, etc.)
- `string $service_id` - Service ID
- `string $context` - Form context

**Example:**
```php
add_filter( 'wbc_captcha_service_args', function( $args, $service_id, $context ) {
    // Use compact size on mobile
    if ( wp_is_mobile() && $service_id === 'recaptcha_v2' ) {
        $args['size'] = 'compact';
    }

    // Use dark theme for dark mode
    if ( isset( $_COOKIE['dark_mode'] ) && $_COOKIE['dark_mode'] === '1' ) {
        $args['theme'] = 'dark';
    }

    return $args;
}, 10, 3 );
```

---

#### `wbc_recaptcha_v3_threshold`

**Description:** Filters reCAPTCHA v3 score threshold.

**Parameters:**
- `float $threshold` - Score threshold (0.0 to 1.0)
- `string $context` - Form context

**Example:**
```php
add_filter( 'wbc_recaptcha_v3_threshold', function( $threshold, $context ) {
    // Stricter thresholds by context
    $thresholds = array(
        'registration' => 0.7, // More strict
        'login'        => 0.6,
        'checkout'     => 0.6,
        'comment'      => 0.5, // More lenient
        'contact'      => 0.5,
    );

    return $thresholds[ $context ] ?? $threshold;
}, 10, 2 );
```

---

### Settings Filters

#### `wbc_default_captcha_service`

**Description:** Filters the default CAPTCHA service selection.

**Parameters:**
- `string $service_id` - Default service ID

**Example:**
```php
add_filter( 'wbc_default_captcha_service', function( $service_id ) {
    // Default to Turnstile for new installations
    return 'turnstile';
} );
```

---

#### `wbc_captcha_enabled_forms`

**Description:** Filters which forms have CAPTCHA enabled by default.

**Parameters:**
- `array $forms` - Array of form IDs

**Example:**
```php
add_filter( 'wbc_captcha_enabled_forms', function( $forms ) {
    // Enable on specific forms by default
    $forms[] = 'my_custom_form';
    $forms[] = 'my_plugin_registration';

    return $forms;
} );
```

---

## Common Use Cases

### Use Case 1: Skip CAPTCHA for VIP Users

```php
add_filter( 'wbc_skip_captcha', function( $skip, $context, $user_id ) {
    if ( $user_id > 0 ) {
        // Check if user has VIP role
        $user = get_userdata( $user_id );
        if ( in_array( 'vip', $user->roles, true ) ) {
            return true; // Skip CAPTCHA for VIP users
        }
    }
    return $skip;
}, 10, 3 );
```

---

### Use Case 2: Different Services for Different Forms

```php
add_filter( 'wbc_active_captcha_service', function( $service_id, $context ) {
    $service_map = array(
        'login'        => 'recaptcha_v2', // Visible security
        'registration' => 'recaptcha_v2', // Visible security
        'checkout'     => 'turnstile',    // Invisible, better UX
        'contact'      => 'turnstile',    // Invisible
        'comment'      => 'recaptcha_v3', // Invisible, less friction
    );

    return $service_map[ $context ] ?? $service_id;
}, 10, 2 );
```

---

### Use Case 3: Rate Limiting Based on Failed Attempts

```php
add_action( 'wbc_captcha_validation_failed', function( $context, $error_message, $service_id ) {
    $ip = $_SERVER['REMOTE_ADDR'];
    $key = "captcha_fails_{$ip}";

    $attempts = get_transient( $key ) ?: 0;
    $attempts++;

    // Store for 1 hour
    set_transient( $key, $attempts, HOUR_IN_SECONDS );

    // Block after 10 failures
    if ( $attempts >= 10 ) {
        // Add to IP blacklist
        $blacklist = get_option( 'captcha_ip_blacklist', array() );
        if ( ! in_array( $ip, $blacklist, true ) ) {
            $blacklist[] = $ip;
            update_option( 'captcha_ip_blacklist', $blacklist );
        }

        // Alert admin
        wp_mail(
            get_option( 'admin_email' ),
            'IP Blocked - Too Many CAPTCHA Failures',
            "IP {$ip} has been blocked after {$attempts} failed CAPTCHA attempts."
        );
    }
}, 10, 3 );

// Check blacklist before rendering
add_filter( 'wbc_skip_captcha', function( $skip, $context, $user_id ) {
    $ip = $_SERVER['REMOTE_ADDR'];
    $blacklist = get_option( 'captcha_ip_blacklist', array() );

    if ( in_array( $ip, $blacklist, true ) ) {
        // Block completely
        wp_die( 'Access denied. Too many failed CAPTCHA attempts.' );
    }

    return $skip;
}, 5, 3 ); // Priority 5 to run early
```

---

### Use Case 4: Analytics Tracking

```php
// Track CAPTCHA performance
add_action( 'wbc_after_captcha_validation', function( $result, $context, $post_data ) {
    // Only track if analytics plugin exists
    if ( ! function_exists( 'track_event' ) ) {
        return;
    }

    track_event( 'captcha_validation', array(
        'context'    => $context,
        'success'    => $result['success'],
        'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
        'mobile'     => wp_is_mobile(),
    ) );
}, 10, 3 );
```

---

### Use Case 5: Custom CAPTCHA Widget Styling

```php
add_filter( 'wbc_captcha_html', function( $html, $context, $service_id ) {
    // Add custom styling wrapper
    $output = '<div class="custom-captcha-wrapper">';
    $output .= '<h4 class="captcha-title">' . __( 'Security Check', 'textdomain' ) . '</h4>';
    $output .= '<div class="captcha-inner">';
    $output .= $html;
    $output .= '</div>';
    $output .= '<p class="captcha-help">';
    $output .= __( 'This helps us prevent automated spam.', 'textdomain' );
    $output .= '</p>';
    $output .= '</div>';

    return $output;
}, 10, 3 );

// Add CSS for custom wrapper
add_action( 'wp_head', function() {
    ?>
    <style>
    .custom-captcha-wrapper {
        padding: 20px;
        border: 1px solid #ddd;
        border-radius: 8px;
        background: #f9f9f9;
        margin: 20px 0;
    }
    .captcha-title {
        margin: 0 0 15px;
        font-size: 16px;
        font-weight: 600;
    }
    .captcha-help {
        margin: 10px 0 0;
        font-size: 13px;
        color: #666;
    }
    </style>
    <?php
} );
```

---

### Use Case 6: Conditional Service Selection Based on Traffic

```php
add_filter( 'wbc_active_captcha_service', function( $service_id, $context ) {
    // Use stricter CAPTCHA during high traffic or attacks
    $current_load = get_transient( 'server_load_high' );

    if ( $current_load ) {
        // Switch to visible reCAPTCHA during attacks
        return 'recaptcha_v2';
    }

    // Normal operation - use invisible
    return 'turnstile';
}, 10, 2 );
```

---

## Integration-Specific Hooks

### WordPress Core

```php
// Skip CAPTCHA on login for specific users
add_filter( 'wbc_wp_login_skip_captcha', function( $skip, $username ) {
    // Skip for whitelisted usernames
    $whitelist = array( 'admin', 'support' );
    return in_array( $username, $whitelist, true );
}, 10, 2 );
```

### WooCommerce

```php
// Skip checkout CAPTCHA for logged-in customers
add_filter( 'wbc_woocommerce_checkout_skip_logged_in', '__return_true' );

// Custom error on checkout
add_filter( 'wbc_woocommerce_checkout_error', function( $message ) {
    return __( 'Please complete security verification to place your order.', 'textdomain' );
} );
```

### BuddyPress

```php
// Skip CAPTCHA for invited members
add_filter( 'wbc_buddypress_registration_skip_invited', '__return_true' );

// Custom registration error
add_filter( 'wbc_buddypress_registration_error', function( $message ) {
    return __( 'Please verify you are human to join our community.', 'textdomain' );
} );
```

### Contact Form 7

```php
// Exclude specific CF7 forms
add_filter( 'wbc_cf7_exclude_forms', function( $excluded ) {
    $excluded[] = 123; // Form ID
    $excluded[] = 456; // Another form ID
    return $excluded;
} );
```

---

## Debug Hooks

### Enable Detailed Logging

```php
add_action( 'wbc_before_captcha_validation', function( $context, $post_data ) {
    if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
        error_log( sprintf(
            '[CAPTCHA] Validation start - Context: %s, IP: %s, User-Agent: %s',
            $context,
            $_SERVER['REMOTE_ADDR'] ?? 'unknown',
            $_SERVER['HTTP_USER_AGENT'] ?? 'unknown'
        ) );
    }
}, 10, 2 );

add_action( 'wbc_after_captcha_validation', function( $result, $context, $post_data ) {
    if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
        error_log( sprintf(
            '[CAPTCHA] Validation result - Context: %s, Success: %s, Message: %s',
            $context,
            $result['success'] ? 'YES' : 'NO',
            $result['message']
        ) );
    }
}, 10, 3 );
```

---

## Hook Priority Guidelines

**Rendering:**
- Early (5): Override default behavior
- Normal (10): Standard customization
- Late (15+): Final modifications

**Validation:**
- Early (5): Pre-validation checks
- Normal (10): Standard validation
- Late (15+): Post-validation actions

**Example:**
```php
// Run before default validation
add_filter( 'wbc_captcha_validation_result', 'my_validation', 5, 3 );

// Run after default validation
add_action( 'wbc_after_captcha_validation', 'my_logging', 15, 3 );
```

---

## More Resources

- [Main Developer Guide](README.md) - Complete technical documentation
- [Creating Custom Integrations](README.md#creating-custom-integrations)
- [CAPTCHA Service API](README.md#captcha-service-api)
- [Testing & Debugging](README.md#testing--debugging)

---

**Need a hook that doesn't exist?** [Request it on GitHub](https://github.com/wbcomdesigns/buddypress-recaptcha/issues) or contact support.
