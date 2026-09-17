# Quick Start for Developers

Get started with Wbcom CAPTCHA Manager integration in 5 minutes.

## 🚀 Quick Examples

### Example 1: Add CAPTCHA to Your Custom Form

**Simplest implementation:**

```php
<?php
/**
 * Add CAPTCHA to custom form
 */

// 1. Render CAPTCHA on your form
function my_custom_form() {
    ?>
    <form method="post" action="">
        <input type="text" name="name" required>
        <input type="email" name="email" required>

        <?php
        // Add CAPTCHA
        $service = wbc_get_captcha_service();
        if ( $service ) {
            echo '<div class="captcha-wrapper">';
            $service->render_captcha( 'my_custom_form' );
            echo '</div>';
        }
        ?>

        <button type="submit">Submit</button>
    </form>
    <?php
}

// 2. Validate on submission
add_action( 'init', function() {
    if ( isset( $_POST['email'] ) && ! empty( $_POST['email'] ) ) {
        // Validate CAPTCHA
        $service = wbc_get_captcha_service();
        if ( $service ) {
            $result = $service->validate_captcha( 'my_custom_form' );

            if ( ! $result['success'] ) {
                wp_die( $result['message'] );
            }
        }

        // Process your form...
        $name = sanitize_text_field( $_POST['name'] );
        $email = sanitize_email( $_POST['email'] );

        // Your logic here...
    }
} );
```

**That's it!** CAPTCHA is now protecting your form.

---

### Example 2: Skip CAPTCHA for Logged-In Users

```php
// Method 1: Check before rendering
function my_custom_form() {
    ?>
    <form method="post">
        <input type="text" name="message" required>

        <?php
        // Only show CAPTCHA for guests
        if ( ! is_user_logged_in() ) {
            $service = wbc_get_captcha_service();
            if ( $service ) {
                $service->render_captcha( 'my_form' );
            }
        }
        ?>

        <button type="submit">Submit</button>
    </form>
    <?php
}

// Method 2: Use filters (applies globally). Pair should_render + should_verify
// so the widget and the server-side check agree.
function my_skip_for_logged_in( $value, $context, $service_id ) {
    return is_user_logged_in() ? false : $value;
}
add_filter( 'wbc_should_render_captcha', 'my_skip_for_logged_in', 10, 3 );
add_filter( 'wbc_should_verify_captcha', 'my_skip_for_logged_in', 10, 3 );
```

---

### Example 3: Custom Error Messages

```php
add_filter( 'wbc_captcha_error_message', function( $message, $context, $service_id, $error_type ) {
    $custom_messages = array(
        'woo_checkout_guest' => __( 'Please complete verification to finish your purchase.', 'textdomain' ),
        'wp_register'        => __( 'Please verify you are human to create an account.', 'textdomain' ),
        'cf7'                => __( 'Please complete the security check to send your message.', 'textdomain' ),
    );

    return $custom_messages[ $context ] ?? $message;
}, 10, 4 );
```

---

### Example 4: Adjust reCAPTCHA v3 Threshold

```php
add_filter( 'wbc_recaptcha_v3_score_threshold_value', function( $threshold, $context ) {
    // Stricter for registration, lenient for comments
    $thresholds = array(
        'wp_register' => 0.7, // Strict
        'woo_checkout_guest' => 0.6,
        'comment'     => 0.4, // Lenient
    );

    return $thresholds[ $context ] ?? $threshold;
}, 10, 2 );
```

---

## 📦 Create a Plugin Integration

### Step 1: Create Integration File

Create `wp-content/plugins/my-plugin/captcha-integration.php`:

```php
<?php
/**
 * Wbcom CAPTCHA Manager Integration
 */

// Exit if Wbcom CAPTCHA Manager not active
if ( ! function_exists( 'wbc_get_captcha_service' ) ) {
    return;
}

class MyPlugin_CAPTCHA_Integration {

    public function __construct() {
        // Render on your forms
        add_action( 'myplugin_form_footer', array( $this, 'render_captcha' ) );

        // Validate submissions
        add_filter( 'myplugin_validate_form', array( $this, 'validate' ), 10, 2 );
    }

    public function render_captcha() {
        if ( ! $this->is_enabled() ) {
            return;
        }

        $service = wbc_get_captcha_service();
        if ( $service ) {
            echo '<div class="myplugin-captcha-container">';
            $service->render_captcha( 'myplugin_form' );
            echo '</div>';
        }
    }

    public function validate( $is_valid, $form_data ) {
        if ( ! $is_valid || ! $this->is_enabled() ) {
            return $is_valid;
        }

        $service = wbc_get_captcha_service();
        if ( ! $service ) {
            return $is_valid;
        }

        $result = $service->validate_captcha( 'myplugin_form' );

        if ( ! $result['success'] ) {
            // Add error to your plugin's error handler
            $this->add_error( $result['message'] );
            return false;
        }

        return true;
    }

    private function is_enabled() {
        return get_option( 'myplugin_captcha_enabled', 'yes' ) === 'yes';
    }

    private function add_error( $message ) {
        // Use your plugin's error system
        MyPlugin::add_error( $message );
    }
}

new MyPlugin_CAPTCHA_Integration();
```

---

### Step 2: Load Integration

In your main plugin file:

```php
// Load CAPTCHA integration
require_once plugin_dir_path( __FILE__ ) . 'captcha-integration.php';
```

---

## 🎨 Styling CAPTCHA Widget

### Add Custom CSS

```php
add_action( 'wp_head', function() {
    ?>
    <style>
    /* CAPTCHA container */
    .wbc-captcha-container {
        margin: 20px 0;
        padding: 15px;
        background: #f9f9f9;
        border: 1px solid #ddd;
        border-radius: 4px;
    }

    /* Mobile responsive */
    @media (max-width: 768px) {
        .wbc-captcha-container {
            padding: 10px;
        }

        /* Scale down reCAPTCHA v2 on mobile */
        .g-recaptcha {
            transform: scale(0.85);
            transform-origin: 0 0;
        }
    }
    </style>
    <?php
} );
```

---

## 🔍 Debugging

### Enable Debug Mode

```php
// In wp-config.php
define( 'WP_DEBUG', true );
define( 'WP_DEBUG_LOG', true );
```

### Add Debug Logging

```php
add_filter( 'wbc_should_verify_captcha', function( $should_verify, $context, $service_id ) {
    error_log( "CAPTCHA validation starting for: {$context}" );
    return $should_verify;
}, 5, 3 );

add_filter( 'wbc_captcha_verified', function( $verified, $api_result, $response, $service_id ) {
    error_log( sprintf( 'CAPTCHA result for service %s: %s', $service_id, $verified ? 'SUCCESS' : 'FAIL' ) );
    return $verified;
}, 10, 4 );
```
`wbc_captcha_verified` has no `$context` and reCAPTCHA v3 never fires it - hook `wbc_recaptcha_v3_verify` separately for v3.

### Check if Service is Active

```php
$service = wbc_get_captcha_service();

if ( $service ) {
    echo "Active service: " . $service->get_service_id();
} else {
    echo "No CAPTCHA service configured";
}
```

---

## ⚡ Performance Tips

### 1. Load CAPTCHA Only When Needed

```php
// Don't enqueue scripts on all pages
function my_enqueue_captcha() {
    // Only on pages with forms
    if ( is_page( 'contact' ) || is_checkout() ) {
        // CAPTCHA scripts auto-enqueue when render is called
    }
}
add_action( 'wp_enqueue_scripts', 'my_enqueue_captcha' );
```

---

### 2. Cache-Friendly Implementation

```php
// CAPTCHA works with caching - it loads dynamically via JavaScript
// No special configuration needed for most cache plugins

// If using aggressive caching, exclude CAPTCHA validation endpoints:
// - Exclude POST requests
// - Exclude login/registration URLs
// - Exclude checkout pages
```

---

### 3. Conditional Loading

```php
// Hide + skip CAPTCHA for admins; the built-in per-provider render() already
// only enqueues scripts when the widget actually renders, so there is no
// separate "should load" filter to hook.
function my_skip_for_admins( $value, $context, $service_id ) {
    return current_user_can( 'manage_options' ) ? false : $value;
}
add_filter( 'wbc_should_render_captcha', 'my_skip_for_admins', 10, 3 );
add_filter( 'wbc_should_verify_captcha', 'my_skip_for_admins', 10, 3 );
```

---

## 🔒 Security Best Practices

### 1. Always Validate Server-Side

```php
// NEVER trust client-side validation alone
// Always validate on server:

$service = wbc_get_captcha_service();
if ( $service ) {
    $result = $service->validate_captcha( 'my_form' );

    if ( ! $result['success'] ) {
        // Block submission
        wp_die( $result['message'] );
    }
}

// Then process form...
```

---

### 2. Use Nonces with CAPTCHA

```php
// Render form with nonce
function my_form() {
    ?>
    <form method="post">
        <?php wp_nonce_field( 'my_form_action', 'my_form_nonce' ); ?>

        <input type="text" name="data">

        <?php
        $service = wbc_get_captcha_service();
        if ( $service ) {
            $service->render_captcha( 'my_form' );
        }
        ?>

        <button type="submit">Submit</button>
    </form>
    <?php
}

// Validate nonce AND CAPTCHA
if ( isset( $_POST['my_form_nonce'] ) ) {
    // Verify nonce
    if ( ! wp_verify_nonce( $_POST['my_form_nonce'], 'my_form_action' ) ) {
        wp_die( 'Security check failed' );
    }

    // Validate CAPTCHA
    $service = wbc_get_captcha_service();
    if ( $service ) {
        $result = $service->validate_captcha( 'my_form' );
        if ( ! $result['success'] ) {
            wp_die( $result['message'] );
        }
    }

    // Process form...
}
```

---

### 3. Rate Limiting

```php
// Combine CAPTCHA with rate limiting. wbc_captcha_verified fires on failure
// too (it's a filter, not a validation-failed action), so count from there.
add_filter( 'wbc_captcha_verified', function( $verified, $api_result, $response, $service_id ) {
    if ( ! $verified ) {
        $ip  = $_SERVER['REMOTE_ADDR'] ?? '';
        $key = "failed_captcha_{$ip}";

        $attempts = (int) get_transient( $key );
        $attempts++;

        set_transient( $key, $attempts, HOUR_IN_SECONDS );

        if ( $attempts >= 5 ) {
            wp_die( 'Too many failed attempts. Please try again later.' );
        }
    }

    return $verified;
}, 10, 4 );
```
reCAPTCHA v3 does not fire `wbc_captcha_verified` - hook `wbc_recaptcha_v3_verify` separately if you also need to rate-limit v3.

---

## 📚 Learn More

- **[Complete Hooks Reference](hooks-reference.md)** - All hooks and filters
- **[Full Developer Guide](README.md)** - Comprehensive documentation
- **[Customer Guides](../customer-guide/README.md)** - User documentation
- **[GitHub Repository](https://github.com/wbcomdesigns/buddypress-recaptcha)** - Source code

---

## 💡 Need Help?

**Common Issues:**
1. CAPTCHA not showing? Check if service is configured in settings
2. Validation failing? Verify API keys and domain registration
3. Scripts not loading? Check for JavaScript conflicts

**Get Support:**
- [GitHub Issues](https://github.com/wbcomdesigns/buddypress-recaptcha/issues)
- [WordPress Support Forum](https://wordpress.org/support/plugin/buddypress-recaptcha/)
- Email: admin@wbcomdesigns.com

---

**Pro Tip:** Start with the simplest implementation, then customize as needed. The plugin is designed to work out-of-the-box with minimal code.
