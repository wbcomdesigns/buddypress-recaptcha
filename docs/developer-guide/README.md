# Developer Guide - Wbcom CAPTCHA Manager

Technical documentation for developers extending or integrating with Wbcom CAPTCHA Manager.

## 📚 Table of Contents

- [Architecture Overview](#architecture-overview)
- [Creating Custom Integrations](#creating-custom-integrations)
- [Hooks & Filters Reference](#hooks--filters-reference)
- [CAPTCHA Service API](#captcha-service-api)
- [Testing & Debugging](#testing--debugging)
- [Best Practices](#best-practices)

---

## Architecture Overview

### Plugin Structure

```
buddypress-recaptcha/
├── admin/
│   ├── class-recaptcha-for-bp-admin.php          # Admin functionality
│   └── includes/
│       ├── settings-modules/                      # Modular settings system
│       │   ├── interface-wbc-settings-module.php
│       │   ├── abstract-wbc-settings-module.php
│       │   ├── class-wbc-settings-module-loader.php
│       │   └── class-wbc-*-settings.php           # Individual modules
│       └── class-recaptcha-for-bp-admin-settings.php
├── public/
│   ├── class-recaptcha-for-bp-public.php          # Frontend functionality
│   └── */                                         # Integration handlers
├── includes/
│   ├── class-recaptcha-for-bp.php                 # Main plugin class
│   ├── services/                                  # CAPTCHA service managers
│   │   ├── interface-wbc-captcha-service.php
│   │   ├── abstract-wbc-captcha-service.php
│   │   └── class-wbc-*-service.php                # Service implementations
│   └── class-wbc-service-manager.php              # Service factory
└── blocks/                                        # Gutenberg blocks
```

---

## Creating Custom Integrations

### Step 1: Create Integration Class

Create a new file in `public/your-plugin/`:

```php
<?php
/**
 * Your Plugin CAPTCHA Integration
 *
 * @package Recaptcha_For_BuddyPress
 */

class WBC_YourPlugin_Integration {

    /**
     * Constructor
     */
    public function __construct() {
        // Hook into plugin's form rendering
        add_action( 'yourplugin_form_render', array( $this, 'render_captcha' ) );

        // Hook into form validation
        add_filter( 'yourplugin_validate_form', array( $this, 'validate_captcha' ), 10, 2 );
    }

    /**
     * Render CAPTCHA on form
     */
    public function render_captcha() {
        // Check if CAPTCHA enabled for this integration
        if ( ! $this->is_captcha_enabled() ) {
            return;
        }

        // Get service manager
        $service = wbc_get_captcha_service();

        if ( $service ) {
            // Render CAPTCHA widget
            echo '<div class="wbc-captcha-container">';
            $service->render_captcha( 'yourplugin_form' );
            echo '</div>';
        }
    }

    /**
     * Validate CAPTCHA on submission
     *
     * @param bool  $is_valid Current validation status
     * @param array $form_data Form data
     * @return bool
     */
    public function validate_captcha( $is_valid, $form_data ) {
        // If already invalid, return
        if ( ! $is_valid ) {
            return $is_valid;
        }

        // Check if CAPTCHA enabled
        if ( ! $this->is_captcha_enabled() ) {
            return $is_valid;
        }

        // Get service manager
        $service = wbc_get_captcha_service();

        if ( ! $service ) {
            return $is_valid;
        }

        // Validate CAPTCHA
        $validation = $service->validate_captcha();

        if ( ! $validation['success'] ) {
            // Add error
            $this->add_error( $validation['message'] );
            return false;
        }

        return $is_valid;
    }

    /**
     * Check if CAPTCHA enabled for this integration
     *
     * @return bool
     */
    private function is_captcha_enabled() {
        return get_option( 'wbc_recaptcha_enable_on_yourplugin', 'no' ) === 'yes';
    }

    /**
     * Add validation error
     *
     * @param string $message Error message
     */
    private function add_error( $message ) {
        // Use plugin's error handling system
        yourplugin_add_error( $message );
    }
}

// Initialize
if ( class_exists( 'YourPlugin' ) ) {
    new WBC_YourPlugin_Integration();
}
```

---

### Step 2: Create Settings Module

Create settings in `admin/includes/settings-modules/`:

```php
<?php
/**
 * YourPlugin Settings Module
 *
 * @package Recaptcha_For_BuddyPress
 */

class WBC_YourPlugin_Settings extends WBC_Settings_Module_Abstract {

    /**
     * Constructor
     */
    public function __construct() {
        $this->module_id   = 'yourplugin';
        $this->module_name = __( 'YourPlugin Forms', 'buddypress-recaptcha' );
    }

    /**
     * Check if module should be active
     *
     * @return bool
     */
    public function is_active() {
        return class_exists( 'YourPlugin' );
    }

    /**
     * Get settings array
     *
     * @return array
     */
    protected function get_settings_array() {
        return $this->create_settings_section(
            'wbc_yourplugin_protection',
            __( 'YourPlugin Forms', 'buddypress-recaptcha' ),
            array(
                array(
                    'id'      => 'wbc_recaptcha_enable_on_yourplugin',
                    'label'   => __( 'Contact Form', 'buddypress-recaptcha' ),
                    'desc'    => __( 'Enable CAPTCHA on YourPlugin contact forms', 'buddypress-recaptcha' ),
                    'default' => 'yes',
                ),
                array(
                    'id'      => 'wbc_recaptcha_enable_on_yourplugin_registration',
                    'label'   => __( 'Registration Form', 'buddypress-recaptcha' ),
                    'desc'    => __( 'Enable CAPTCHA on registration', 'buddypress-recaptcha' ),
                    'default' => 'yes',
                ),
            )
        );
    }

    /**
     * Get checkbox IDs for saving
     *
     * @return array
     */
    public function get_checkbox_ids() {
        return array(
            'wbc_recaptcha_enable_on_yourplugin',
            'wbc_recaptcha_enable_on_yourplugin_registration',
        );
    }
}
```

---

### Step 3: Register Module

In `admin/includes/settings-modules/class-wbc-settings-module-loader.php`:

```php
// Add to register_modules() method
require_once plugin_dir_path( __FILE__ ) . 'class-wbc-yourplugin-settings.php';
$this->register_module( new WBC_YourPlugin_Settings() );
```

---

## Hooks & Filters Reference

### Rendering Hooks

**Modify CAPTCHA HTML:**
```php
add_filter( 'wbc_captcha_html', function( $html, $context ) {
    // $context = 'login', 'registration', 'contact_form', etc.
    // Add custom wrapper or modify HTML
    return '<div class="my-wrapper">' . $html . '</div>';
}, 10, 2 );
```

**Skip CAPTCHA for Specific Users:**
```php
add_filter( 'wbc_skip_captcha', function( $skip, $context ) {
    // Skip for administrators
    if ( current_user_can( 'manage_options' ) ) {
        return true;
    }
    return $skip;
}, 10, 2 );
```

**Customize Error Messages:**
```php
add_filter( 'wbc_captcha_error_message', function( $message, $context ) {
    // Context-specific messages
    if ( $context === 'checkout' ) {
        return __( 'Please verify you are human to complete your purchase.', 'your-textdomain' );
    }
    return $message;
}, 10, 2 );
```

---

### Validation Hooks

**Custom Validation Logic:**
```php
add_filter( 'wbc_captcha_validation_result', function( $result, $context ) {
    // $result = array( 'success' => bool, 'message' => string )

    // Add custom validation
    if ( $context === 'registration' && custom_check_failed() ) {
        return array(
            'success' => false,
            'message' => 'Custom validation failed',
        );
    }

    return $result;
}, 10, 2 );
```

**Modify Threshold (reCAPTCHA v3):**
```php
add_filter( 'wbc_recaptcha_v3_threshold', function( $threshold, $context ) {
    // More strict for registration
    if ( $context === 'registration' ) {
        return 0.7;
    }
    return $threshold;
}, 10, 2 );
```

---

### Service Selection

**Override CAPTCHA Service:**
```php
add_filter( 'wbc_active_captcha_service', function( $service_id, $context ) {
    // Use different service for specific forms
    if ( $context === 'checkout' ) {
        return 'turnstile'; // Options: 'recaptcha_v2', 'recaptcha_v3', 'hcaptcha', 'turnstile', 'altcha'
    }
    return $service_id;
}, 10, 2 );
```

---

### Settings Hooks

**Add Custom Settings:**
```php
add_filter( 'wbc_settings_sections', function( $sections ) {
    $sections['my_custom_section'] = array(
        'id'    => 'my_custom_section',
        'title' => __( 'My Custom Settings', 'your-textdomain' ),
        'desc'  => __( 'Custom CAPTCHA settings', 'your-textdomain' ),
    );
    return $sections;
});

add_filter( 'wbc_settings_fields', function( $fields ) {
    $fields['my_custom_section'] = array(
        array(
            'id'      => 'my_custom_option',
            'label'   => __( 'Custom Option', 'your-textdomain' ),
            'desc'    => __( 'Enable custom feature', 'your-textdomain' ),
            'type'    => 'checkbox',
            'default' => 'no',
        ),
    );
    return $fields;
});
```

---

## CAPTCHA Service API

### Get Active Service

```php
// Get current service manager
$service = wbc_get_captcha_service();

if ( $service ) {
    // Service is configured and active
}
```

---

### Render CAPTCHA

```php
// Render CAPTCHA widget
$service->render_captcha( 'my_form_context' );

// With custom attributes
$service->render_captcha( 'my_form', array(
    'theme' => 'dark',
    'size'  => 'compact',
));
```

---

### Validate CAPTCHA

```php
// Validate submission
$result = $service->validate_captcha();

if ( $result['success'] ) {
    // CAPTCHA validation passed
} else {
    // Failed - show error
    echo $result['message'];
}
```

---

### Creating Custom Service

Implement the service interface:

```php
<?php
class WBC_MyService_Service extends WBC_Captcha_Service_Abstract {

    /**
     * Service ID
     */
    protected $service_id = 'myservice';

    /**
     * Service name
     */
    protected $service_name = 'My CAPTCHA Service';

    /**
     * Render CAPTCHA widget
     *
     * @param string $context Form context
     * @param array  $args    Additional arguments
     */
    public function render_captcha( $context = '', $args = array() ) {
        // Output CAPTCHA HTML
        echo '<div class="myservice-captcha" data-sitekey="' . esc_attr( $this->get_site_key() ) . '"></div>';

        // Enqueue scripts
        $this->enqueue_scripts();
    }

    /**
     * Validate CAPTCHA response
     *
     * @return array Array with 'success' and 'message' keys
     */
    public function validate_captcha() {
        // Get response from POST data
        $response = isset( $_POST['myservice-response'] ) ? sanitize_text_field( $_POST['myservice-response'] ) : '';

        if ( empty( $response ) ) {
            return array(
                'success' => false,
                'message' => __( 'Please complete the CAPTCHA.', 'buddypress-recaptcha' ),
            );
        }

        // Verify with service API
        $is_valid = $this->verify_with_api( $response );

        return array(
            'success' => $is_valid,
            'message' => $is_valid ? '' : __( 'CAPTCHA verification failed.', 'buddypress-recaptcha' ),
        );
    }

    /**
     * Enqueue scripts and styles
     */
    protected function enqueue_scripts() {
        wp_enqueue_script(
            'myservice-captcha',
            'https://api.myservice.com/captcha.js',
            array(),
            null,
            true
        );
    }

    /**
     * Verify response with API
     *
     * @param string $response CAPTCHA response
     * @return bool
     */
    private function verify_with_api( $response ) {
        $api_url = 'https://api.myservice.com/verify';

        $response = wp_remote_post( $api_url, array(
            'body' => array(
                'secret'   => $this->get_secret_key(),
                'response' => $response,
            ),
        ));

        if ( is_wp_error( $response ) ) {
            return false;
        }

        $body = json_decode( wp_remote_retrieve_body( $response ), true );

        return isset( $body['success'] ) && $body['success'] === true;
    }
}
```

---

## Testing & Debugging

### Enable Debug Mode

```php
// In wp-config.php
define( 'WBC_CAPTCHA_DEBUG', true );
```

This will:
- Log CAPTCHA validation attempts
- Show detailed error messages
- Log API calls to debug.log

---

### Test CAPTCHA Without Verification

For development/testing:

```php
// Skip validation in development
add_filter( 'wbc_skip_captcha_validation', function( $skip ) {
    if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
        return true; // Skip in debug mode
    }
    return $skip;
});
```

---

### Debug Validation

```php
add_action( 'wbc_captcha_validation_start', function( $context ) {
    error_log( "CAPTCHA validation starting for context: {$context}" );
});

add_action( 'wbc_captcha_validation_complete', function( $result, $context ) {
    error_log( "CAPTCHA validation result for {$context}: " . print_r( $result, true ) );
}, 10, 2 );
```

---

### Test Different Services

```php
// Force specific service for testing
add_filter( 'wbc_active_captcha_service', function() {
    return 'turnstile'; // Test Turnstile
});
```

---

## Best Practices

### 1. Always Check if Service Exists

```php
$service = wbc_get_captcha_service();
if ( ! $service ) {
    // CAPTCHA not configured - handle gracefully
    return;
}
```

### 2. Use Proper Context

```php
// Always pass context for proper filtering
$service->render_captcha( 'my_plugin_contact_form' );
$result = $service->validate_captcha( 'my_plugin_contact_form' );
```

### 3. Sanitize and Validate

```php
// Always sanitize POST data
$response = isset( $_POST['captcha-response'] )
    ? sanitize_text_field( $_POST['captcha-response'] )
    : '';
```

### 4. Handle Errors Gracefully

```php
$result = $service->validate_captcha();

if ( ! $result['success'] ) {
    // Don't expose technical details to users
    $message = __( 'Security verification failed. Please try again.', 'your-textdomain' );

    // Log technical details for debugging
    if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
        error_log( 'CAPTCHA Error: ' . $result['message'] );
    }
}
```

### 5. Provide Filters

Allow customization of your integration:

```php
// Allow users to customize
$show_captcha = apply_filters( 'myplugin_show_captcha', true, $form_id );
$error_message = apply_filters( 'myplugin_captcha_error', $default_message, $context );
```

### 6. Test All Scenarios

- With CAPTCHA enabled/disabled
- With different CAPTCHA services
- With JavaScript disabled
- With ad blockers
- On mobile devices
- With screen readers

---

## Utility Functions

### Check if CAPTCHA is Active

```php
if ( wbc_is_captcha_active() ) {
    // CAPTCHA service is configured and active
}
```

### Get Service Configuration

```php
$config = wbc_get_service_config();
// Returns: array( 'service' => 'recaptcha_v2', 'site_key' => '...', ... )
```

### Log Debug Message

```php
wbc_debug_log( 'Custom integration validation started', array(
    'context' => $context,
    'user_id' => get_current_user_id(),
));
```

---

## Code Examples

### Example: Simple Contact Form Integration

```php
<?php
/**
 * Simple contact form with CAPTCHA
 */
function my_contact_form() {
    ?>
    <form method="post" action="">
        <input type="text" name="name" required>
        <input type="email" name="email" required>
        <textarea name="message" required></textarea>

        <?php
        // Render CAPTCHA
        $service = wbc_get_captcha_service();
        if ( $service ) {
            $service->render_captcha( 'my_contact_form' );
        }
        ?>

        <button type="submit">Send</button>
    </form>
    <?php
}

// Handle submission
add_action( 'init', function() {
    if ( isset( $_POST['message'] ) ) {
        // Validate CAPTCHA
        $service = wbc_get_captcha_service();
        if ( $service ) {
            $result = $service->validate_captcha( 'my_contact_form' );

            if ( ! $result['success'] ) {
                wp_die( $result['message'] );
            }
        }

        // Process form...
    }
});
```

---

### Example: Conditional CAPTCHA

```php
<?php
// Show CAPTCHA only for guests
function render_conditional_captcha() {
    // Skip for logged-in users
    if ( is_user_logged_in() ) {
        return;
    }

    $service = wbc_get_captcha_service();
    if ( $service ) {
        $service->render_captcha( 'my_form' );
    }
}

// Validate only if rendered
function validate_conditional_captcha() {
    // Skip for logged-in users
    if ( is_user_logged_in() ) {
        return true;
    }

    $service = wbc_get_captcha_service();
    if ( ! $service ) {
        return true;
    }

    $result = $service->validate_captcha( 'my_form' );
    return $result['success'];
}
```

---

## Resources

- **Main README:** Plugin overview and features
- **Customer Guide:** User-friendly documentation
- **CAPTCHA Services:** Service-specific guides
- **Integration Guides:** Plugin-specific integration docs
- **GitHub Issues:** Bug reports and feature requests
- **Support Forum:** Community support

---

## Contributing

Interested in contributing? See our [Contributing Guidelines](../../CONTRIBUTING.md).

**Areas for contribution:**
- New CAPTCHA service integrations
- New plugin integrations
- Bug fixes and improvements
- Documentation enhancements
- Translations

---

**Questions?** Contact us at [admin@wbcomdesigns.com](mailto:admin@wbcomdesigns.com)
