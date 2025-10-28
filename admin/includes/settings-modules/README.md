# Modular Settings System

## Overview

The Wbcom CAPTCHA Manager plugin uses a modular settings architecture where each integration (WooCommerce, FluentCart, BuddyPress, bbPress) has its own dedicated settings module.

**Benefits:**
- ✅ Settings only appear when the dependent plugin is active
- ✅ Each integration is independently maintainable
- ✅ Easy to add new integrations
- ✅ Follows separation of concerns principle
- ✅ Reduced code duplication

## Architecture

```
admin/includes/settings-modules/
├── interface-wbc-settings-module.php           # Interface contract
├── abstract-wbc-settings-module.php            # Base class with shared functionality
├── class-wbc-settings-module-loader.php        # Singleton loader/manager
├── class-wbc-wordpress-settings.php            # WordPress core forms
├── class-wbc-woocommerce-settings.php          # WooCommerce forms
├── class-wbc-fluentcart-settings.php           # FluentCart forms
├── class-wbc-buddypress-settings.php           # BuddyPress forms
├── class-wbc-bbpress-settings.php              # bbPress forms
└── README.md                                    # This file
```

## Creating a New Settings Module

### Step 1: Create Module Class

Create a new file: `class-wbc-yourplugin-settings.php`

```php
<?php
class WBC_YourPlugin_Settings extends WBC_Settings_Module_Abstract {

    public function __construct() {
        $this->module_id   = 'yourplugin';
        $this->module_name = __( 'Your Plugin Forms', 'buddypress-recaptcha' );
    }

    public function is_active() {
        return class_exists( 'YourPlugin' );
    }

    protected function get_settings_array() {
        return $this->create_settings_section(
            'wbc_yourplugin_protection',
            __( 'Your Plugin Forms', 'buddypress-recaptcha' ),
            array(
                array(
                    'id'      => 'wbc_recaptcha_enable_on_yourplugin_form',
                    'label'   => __( 'Form Name', 'buddypress-recaptcha' ),
                    'desc'    => __( 'Description of what this protects', 'buddypress-recaptcha' ),
                    'default' => 'yes',
                ),
            )
        );
    }

    public function get_checkbox_ids() {
        return array(
            'wbc_recaptcha_enable_on_yourplugin_form',
        );
    }
}
```

### Step 2: Register Module

Add to `class-wbc-settings-module-loader.php` in the `register_modules()` method:

```php
// Your Plugin (conditional)
$this->register_module( new WBC_YourPlugin_Settings() );
```

### Step 3: Load Module File

Add to `load_dependencies()` method in loader:

```php
require_once $modules_dir . 'class-wbc-yourplugin-settings.php';
```

### Step 4: Add Integration Code

Create your integration files in:
- `public/yourplugin-extra/YourPluginForm.php`

Register hooks in:
- `includes/class-recaptcha-for-buddypress.php`

Add context mappings in:
- `includes/class-captcha-service-base.php`

That's it! Your module will automatically:
- ✅ Only show when your plugin is active
- ✅ Integrate with the Protection settings tab
- ✅ Save settings correctly
- ✅ Use the toggle switch UI

## Usage in Main Settings Page

The main settings page (`class-wbc-buddypress-settings-page.php`) uses the loader:

```php
// Load the module system
require_once plugin_dir_path( __FILE__ ) . 'settings-modules/class-wbc-settings-module-loader.php';

// Get all protection settings from active modules
$loader = wbc_settings_module_loader();
$protection_settings = $loader->get_all_protection_settings();

// Get all checkbox IDs for saving
$checkbox_ids = $loader->get_all_checkbox_ids();
```

## Module Interface

All modules must implement `WBC_Settings_Module_Interface`:

```php
interface WBC_Settings_Module_Interface {
    public function is_active();                    // Check if plugin is active
    public function get_module_id();                // Get unique ID
    public function get_module_name();              // Get display name
    public function get_protection_settings();      // Get settings array
    public function get_checkbox_ids();             // Get checkbox field IDs
}
```

## Base Class Helper Methods

`WBC_Settings_Module_Abstract` provides:

### `create_settings_section( $id, $name, $checkboxes )`

Creates a complete settings section with title, checkboxes, and end.

**Parameters:**
- `$id` (string) - Section ID
- `$name` (string) - Section display name
- `$checkboxes` (array) - Array of checkbox configurations

**Checkbox Configuration:**
```php
array(
    'id'      => 'option_name',      // WordPress option name
    'label'   => 'Display Label',     // Label shown to user
    'desc'    => 'Description text',  // Tooltip description
    'default' => 'yes',               // Default value (yes/no)
)
```

### `get_protection_checkbox_group( $checkboxes )`

Generates the HTML for the toggle switch UI.

## Example: Complete Module

```php
<?php
class WBC_LearnDash_Settings extends WBC_Settings_Module_Abstract {

    public function __construct() {
        $this->module_id   = 'learndash';
        $this->module_name = __( 'LearnDash Forms', 'buddypress-recaptcha' );
    }

    public function is_active() {
        return class_exists( 'SFWD_LMS' );
    }

    protected function get_settings_array() {
        return $this->create_settings_section(
            'wbc_learndash_protection',
            __( 'LearnDash Forms', 'buddypress-recaptcha' ),
            array(
                array(
                    'id'      => 'wbc_recaptcha_enable_on_learndash_login',
                    'label'   => __( 'LearnDash Login', 'buddypress-recaptcha' ),
                    'desc'    => __( 'Protect LearnDash login form', 'buddypress-recaptcha' ),
                    'default' => 'no',
                ),
                array(
                    'id'      => 'wbc_recaptcha_enable_on_learndash_register',
                    'label'   => __( 'LearnDash Registration', 'buddypress-recaptcha' ),
                    'desc'    => __( 'Prevent spam learner accounts', 'buddypress-recaptcha' ),
                    'default' => 'yes',
                ),
            )
        );
    }

    public function get_checkbox_ids() {
        return array(
            'wbc_recaptcha_enable_on_learndash_login',
            'wbc_recaptcha_enable_on_learndash_register',
        );
    }
}
```

## Third-Party Module Registration

Third-party plugins can register custom modules via action hook:

```php
add_action( 'wbc_recaptcha_register_settings_modules', function( $loader ) {
    $loader->register_module( new My_Custom_Settings_Module() );
});
```

## Filters Available

### `wbc_recaptcha_all_protection_settings`

Filter all combined protection settings:

```php
add_filter( 'wbc_recaptcha_all_protection_settings', function( $settings ) {
    // Modify or add settings
    return $settings;
});
```

### `wbc_recaptcha_all_checkbox_ids`

Filter all combined checkbox IDs:

```php
add_filter( 'wbc_recaptcha_all_checkbox_ids', function( $ids ) {
    // Add custom IDs
    $ids[] = 'my_custom_checkbox_id';
    return $ids;
});
```

## Testing a Module

```php
// Check if module is loaded
$loader = wbc_settings_module_loader();
$module = $loader->get_module( 'fluentcart' );

if ( $module ) {
    echo 'Module ID: ' . $module->get_module_id();
    echo 'Module Name: ' . $module->get_module_name();
    echo 'Is Active: ' . ( $module->is_active() ? 'Yes' : 'No' );

    if ( $module->is_active() ) {
        print_r( $module->get_protection_settings() );
        print_r( $module->get_checkbox_ids() );
    }
}

// Get all active modules
$active_modules = $loader->get_active_modules();
echo 'Active Modules: ' . count( $active_modules );

// Get combined settings
$all_settings = $loader->get_all_protection_settings();
$all_checkboxes = $loader->get_all_checkbox_ids();
```

## Benefits of This Architecture

1. **Conditional Loading**: Settings only appear when needed
2. **Maintainability**: Each integration is self-contained
3. **Extensibility**: Easy to add new integrations
4. **Testability**: Each module can be tested independently
5. **Performance**: Only active modules are processed
6. **Clean Code**: Follows SOLID principles

## File Size Comparison

**Before (Monolithic):**
- `class-wbc-buddypress-settings-page.php`: 2,528 lines

**After (Modular):**
- Main settings page: ~500 lines (estimated)
- WordPress module: ~80 lines
- WooCommerce module: ~80 lines
- FluentCart module: ~75 lines
- BuddyPress module: ~70 lines
- bbPress module: ~75 lines
- Base abstract class: ~140 lines
- Interface: ~70 lines
- Loader: ~200 lines

**Total Lines**: ~1,290 lines across 9 organized files
**Reduction**: ~50% fewer lines, infinitely more maintainable!

---

**Version**: 2.1.0
**Author**: Wbcom Designs
**Date**: January 2025
