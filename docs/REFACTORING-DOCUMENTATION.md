# BuddyPress reCAPTCHA Plugin - Service Model Refactoring Documentation

## Overview
This document outlines the complete refactoring of the BuddyPress reCAPTCHA plugin to implement a modern service-based architecture, enabling support for multiple captcha providers (Google reCAPTCHA v2, v3, and Cloudflare Turnstile).

## Completed Work

### 1. Core Service Architecture

#### 1.1 Service Interface (`includes/captcha-service-interface.php`)
✅ **Status: COMPLETE**
- Defines the contract for all captcha service implementations
- Key methods:
  - `get_service_id()` - Unique identifier for the service
  - `get_service_name()` - Display name for admin UI
  - `get_site_key()` / `get_secret_key()` - API credentials
  - `is_configured()` - Configuration validation
  - `render()` - Display captcha on forms
  - `verify()` - Server-side validation
  - `enqueue_scripts()` - Load necessary JavaScript
  - `get_container_attributes()` - HTML attributes for captcha container

#### 1.2 Service Base Class (`includes/class-captcha-service-base.php`)
✅ **Status: COMPLETE**
- Abstract base class implementing common functionality
- Provides default implementations for standard methods
- Handles context-based configuration
- Manages script enqueueing
- Implements helper methods for IP retrieval and API requests

#### 1.3 Service Manager (`includes/class-captcha-service-manager.php`)
✅ **Status: COMPLETE**
- Singleton pattern for global access
- Features:
  - Service registration and management
  - Active service selection
  - Centralized render/verify methods
  - Context-based enabling/disabling
  - Error logging and admin notices
  - IP whitelist support
  - Backward compatibility handling

### 2. Service Implementations

#### 2.1 Google reCAPTCHA v2 (`includes/services/class-recaptcha-v2-service.php`)
✅ **Status: COMPLETE**
- Full implementation of reCAPTCHA v2
- Supports all existing v2 configurations
- Theme and size customization
- Submit button disable/enable functionality
- JavaScript callbacks for form integration

#### 2.2 Google reCAPTCHA v3 (`includes/services/class-recaptcha-v3-service.php`)
✅ **Status: COMPLETE**
- Full implementation of reCAPTCHA v3
- Score threshold configuration
- Action-based verification
- Invisible operation
- Token generation and validation

#### 2.3 Cloudflare Turnstile (`includes/services/class-turnstile-service.php`)
✅ **Status: COMPLETE (Needs Registration)**
- Full implementation of Turnstile API
- Theme and size configuration
- Simplified verification process
- No-conflict mode support

### 3. Helper Functions

#### 3.1 Verification Helper (`includes/captcha-verification-helper.php`)
✅ **Status: COMPLETE**
- Unified `wbc_verify_captcha()` function
- Context-based verification
- Legacy fallback support
- Error message management
- Score threshold helpers for v3
- Action name mapping

#### 3.2 General Helper Functions (`includes/recaptcha-helper-functions.php`)
✅ **Status: COMPLETE**
- `wbc_get_recaptcha_version()` - Get active version
- `wbc_get_recaptcha_site_key()` - Get site key
- `wbc_get_recaptcha_secret_key()` - Get secret key
- `wbc_is_recaptcha_enabled()` - Check if enabled
- Backward compatibility wrappers

#### 3.3 Settings Integration (`includes/class-settings-integration.php`)
✅ **Status: COMPLETE**
- Bridges settings with service architecture
- Option name mapping
- Configuration validation
- Admin UI integration helpers

### 4. Integration Points Updated

#### 4.1 BuddyPress Integration
✅ **Status: COMPLETE**
- `public/bp-classes/Registrationbp.php` - Updated to use service manager
- Fallback to legacy implementation for compatibility

#### 4.2 WordPress Core Integration
⚠️ **Status: PARTIAL - Uses Legacy with Fallback**
- `public/lrl-classes/Login.php` - Has service manager check
- `public/lrl-classes/Registration.php` - Has service manager check
- `public/lrl-classes/Lostpassword.php` - Has service manager check

#### 4.3 WooCommerce Integration
⚠️ **Status: PARTIAL - Uses Legacy with Fallback**
- `public/woocommerce-lrl-classes/WoocommerceLogin.php`
- `public/woocommerce-lrl-classes/WoocommerceRegister.php`
- `public/woocommerce-lrl-classes/WoocommerceLostpassword.php`
- Various checkout and order forms

#### 4.4 bbPress Integration
⚠️ **Status: PARTIAL - Uses Legacy with Fallback**
- `public/bbPress/class-wbc-bbpress-reply-recaptcha.php`
- `public/bbPress/class-wbc-bbpress-topic-recaptcha.php`

### 5. Main Plugin File Updates
✅ **Status: COMPLETE**
- `includes/class-recaptcha-for-buddypress.php` - Loads all service files
- Proper dependency loading order
- Service manager initialization

## Pending Tasks

### Critical Tasks (Must Complete)

1. **Register Turnstile Service**
   - [ ] Add Turnstile registration in `class-captcha-service-manager.php` line 68
   ```php
   // Register Turnstile
   require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/services/class-turnstile-service.php';
   $this->register_service( new WBC_Turnstile_Service() );
   ```

2. **Update Admin Settings**
   - [ ] Add service selector in admin settings
   - [ ] Add Turnstile configuration fields (site key, secret key)
   - [ ] Update settings save handlers

### Legacy Code Removal Checklist

#### Phase 1: Update All Integration Points (Convert to Service Manager)

**WordPress Core Forms:**
- [ ] `public/lrl-classes/Login.php` - Remove version checks, use only service manager
- [ ] `public/lrl-classes/Registration.php` - Remove version checks, use only service manager
- [ ] `public/lrl-classes/Lostpassword.php` - Remove version checks, use only service manager

**WooCommerce Forms:**
- [ ] `public/woocommerce-lrl-classes/WoocommerceLogin.php` - Convert to service manager
- [ ] `public/woocommerce-lrl-classes/WoocommerceRegister.php` - Convert to service manager
- [ ] `public/woocommerce-lrl-classes/WoocommerceLostpassword.php` - Convert to service manager
- [ ] `public/woocommerce-extra/WoocommerceRegisterPost.php` - Convert verification
- [ ] `public/woocommerce-extra/LostpasswordPost.php` - Convert verification
- [ ] `public/woocommerce-extra/WoocommerceProcessLoginErrors.php` - Convert verification
- [ ] `public/woocommerce-extra/WoocommerceAfterCheckoutValidation.php` - Convert to service manager
- [ ] `public/woocommerce-order/WoocommerceOrder.php` - Convert to service manager

**BuddyPress Forms:**
- [ ] `public/bp-classes/Registrationbp.php` - Remove legacy fallback code (lines 36-186)

**bbPress Forms:**
- [ ] `public/bbPress/class-wbc-bbpress-reply-recaptcha.php` - Convert to service manager
- [ ] `public/bbPress/class-wbc-bbpress-topic-recaptcha.php` - Convert to service manager

#### Phase 2: Remove Legacy Verification Code

**From captcha-verification-helper.php:**
- [ ] Remove `wbc_verify_captcha_legacy()` function (lines 36-44)
- [ ] Remove `wbc_verify_recaptcha_v2_legacy()` function (lines 53-97)
- [ ] Remove `wbc_verify_recaptcha_v3_legacy()` function (lines 106-189)

**From each integration file:**
- [ ] Remove all direct Google API calls
- [ ] Remove version checking code (`if 'v2' === $version`)
- [ ] Remove duplicate script enqueueing
- [ ] Remove inline JavaScript for captcha initialization

#### Phase 3: Clean Up Options and Database

**Option Migration:**
- [ ] Create migration script to convert old options to new format
- [ ] Map `wbc_recapcha_version` to `wbc_captcha_service`
- [ ] Consolidate duplicate option names

**Remove Deprecated Options:**
- [ ] `wbc_recapcha_version` (after migration)
- [ ] Context-specific version options
- [ ] Duplicate enable/disable options

#### Phase 4: Remove Unused Files and Functions

**Files to Review for Removal:**
- [ ] Any temporary update files
- [ ] Backup files from refactoring
- [ ] Unused helper functions

**Functions to Remove:**
- [ ] Direct reCAPTCHA API verification functions
- [ ] Version-specific rendering functions
- [ ] Duplicate helper functions

## Testing Checklist

### Functional Testing
- [ ] Test reCAPTCHA v2 on all forms
- [ ] Test reCAPTCHA v3 on all forms
- [ ] Test Turnstile on all forms (after registration)
- [ ] Test service switching in admin
- [ ] Test IP whitelist functionality
- [ ] Test error handling and logging

### Integration Testing
- [ ] WordPress login/registration/lost password
- [ ] WooCommerce login/registration/checkout
- [ ] BuddyPress registration
- [ ] bbPress topic/reply creation
- [ ] Comment forms
- [ ] Order tracking forms

### Backward Compatibility Testing
- [ ] Test with existing configurations
- [ ] Verify option migration
- [ ] Check for JavaScript conflicts
- [ ] Validate with popular themes/plugins

## Migration Guide for Developers

### Using the New Service Manager

**Rendering Captcha:**
```php
// Old way
if ( 'v2' === get_option( 'wbc_recapcha_version' ) ) {
    // v2 specific code
} else {
    // v3 specific code
}

// New way
if ( function_exists( 'wbc_captcha_service_manager' ) ) {
    wbc_captcha_service_manager()->render( 'context_name' );
}
```

**Verifying Captcha:**
```php
// Old way
// Complex version checking and API calls

// New way
if ( function_exists( 'wbc_verify_captcha' ) ) {
    $is_valid = wbc_verify_captcha( 'context_name' );
}
```

### Adding Custom Captcha Service

```php
// Create your service class
class My_Custom_Captcha_Service extends WBC_Captcha_Service_Base {
    // Implement required methods
}

// Register with service manager
add_action( 'wbc_register_captcha_services', function( $manager ) {
    $manager->register_service( new My_Custom_Captcha_Service() );
});
```

## Benefits of the Refactoring

1. **Extensibility**: Easy to add new captcha providers
2. **Maintainability**: Clean separation of concerns
3. **Testability**: Each service can be tested independently
4. **Performance**: Reduced code duplication and optimized loading
5. **User Experience**: Consistent interface across all captcha types
6. **Developer Experience**: Simple API for integration

## Next Steps

1. Complete Turnstile registration
2. Update all integration points to use service manager
3. Remove legacy code following the checklist
4. Update documentation and readme files
5. Perform comprehensive testing
6. Create migration guide for users
7. Plan release strategy

## Notes

- Maintain backward compatibility during transition
- Keep legacy functions temporarily with deprecation notices
- Document all breaking changes
- Provide clear upgrade path for users
- Consider phased rollout of changes