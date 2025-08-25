# Legacy Code Removal Guide

## Overview
This guide provides specific instructions for removing all legacy reCAPTCHA code after the service model implementation is fully tested and deployed.

## Pre-Removal Checklist
- [ ] All forms tested with new service manager
- [ ] Turnstile service registered and tested
- [ ] All integration points updated to use service manager
- [ ] Backup created of current working state
- [ ] Migration script for settings completed

## Detailed Removal Instructions

### 1. BuddyPress Registration Form
**File:** `public/bp-classes/Registrationbp.php`

**Remove Lines: 36-186** (Legacy fallback implementation)
```php
// Remove everything from line 36:
// Fallback to original implementation for backward compatibility
// Through line 186 (end of v3 implementation)
```

**Keep only:**
```php
public function woo_extra_bp_register_form() {
    if ( function_exists( 'wbc_captcha_service_manager' ) ) {
        wbc_captcha_service_manager()->render( 'bp_register' );
        do_action( 'bp_accept_tos_errors' );
    }
}
```

### 2. WordPress Login
**File:** `public/lrl-classes/Login.php`

**Current Structure to Remove:**
- Version checking code: `$re_capcha_version = get_option( 'wbc_recapcha_version' );`
- Conditional blocks: `if ( 'v2' === strtolower( $re_capcha_version ) )`
- Direct Google API calls in verification

**Replace render method with:**
```php
public function woo_extra_wp_login_form() {
    if ( function_exists( 'wbc_captcha_service_manager' ) ) {
        wbc_captcha_service_manager()->render( 'wp_login' );
    }
}
```

**Replace verification with:**
```php
public function woo_extra_check_for_wp_login( $user, $password ) {
    if ( function_exists( 'wbc_verify_captcha' ) ) {
        if ( ! wbc_verify_captcha( 'wp_login' ) ) {
            return new WP_Error( 'captcha_error', wbc_get_captcha_error_message( 'wp_login', 'invalid' ) );
        }
    }
    return $user;
}
```

### 3. WordPress Registration
**File:** `public/lrl-classes/Registration.php`

**Remove:**
- All version checking code
- Direct script enqueueing
- Inline JavaScript
- Direct API verification

**Replace with service manager calls** (similar to login)

### 4. WordPress Lost Password
**File:** `public/lrl-classes/Lostpassword.php`

**Remove:**
- Version-specific implementations
- Direct API calls
- Duplicate nonce handling

### 5. WooCommerce Forms
**Files to Update:**
- `public/woocommerce-lrl-classes/WoocommerceLogin.php`
- `public/woocommerce-lrl-classes/WoocommerceRegister.php`
- `public/woocommerce-lrl-classes/WoocommerceLostpassword.php`

**Common Patterns to Remove:**
```php
// Remove all instances of:
$re_capcha_version = get_option( 'wbc_recapcha_version' );
if ( '' === $re_capcha_version ) {
    $re_capcha_version = 'v2';
}
if ( 'v2' === strtolower( $re_capcha_version ) ) {
    // v2 specific code
} else {
    // v3 specific code
}
```

### 6. WooCommerce Verification Methods
**Files:**
- `public/woocommerce-extra/WoocommerceRegisterPost.php`
- `public/woocommerce-extra/LostpasswordPost.php`
- `public/woocommerce-extra/WoocommerceProcessLoginErrors.php`

**Remove Direct Verification Code:**
```php
// Remove patterns like:
$captcha_resp = isset( $_POST['g-recaptcha-response'] ) ? sanitize_text_field( $_POST['g-recaptcha-response'] ) : '';
$secret_key = trim( get_option( 'wc_settings_tab_recapcha_secret_key' ) );
// Direct API calls to Google
```

**Replace with:**
```php
if ( ! wbc_verify_captcha( 'context_name' ) ) {
    // Handle error
}
```

### 7. bbPress Integration
**Files:**
- `public/bbPress/class-wbc-bbpress-reply-recaptcha.php`
- `public/bbPress/class-wbc-bbpress-topic-recaptcha.php`

**Remove:**
- Manual script registration
- Version-specific rendering
- Direct verification

### 8. Helper Functions to Remove
**File:** `includes/captcha-verification-helper.php`

**After all integrations updated, remove:**
- `wbc_verify_captcha_legacy()` (lines 36-44)
- `wbc_verify_recaptcha_v2_legacy()` (lines 53-97)
- `wbc_verify_recaptcha_v3_legacy()` (lines 106-189)
- `wbc_get_score_threshold_for_context()` (lines 197-216) - Move to service classes
- `wbc_get_action_for_context()` (lines 224-242) - Move to service classes

### 9. JavaScript and CSS Cleanup

**Remove Inline JavaScript Patterns:**
```javascript
// Remove all instances of:
var verifyCallback_[context] = function(response) {
    // Legacy callback code
}

// Remove all grecaptcha.ready() calls for v3
// Remove all manual button disable/enable code
```

**Remove CSS:**
```css
/* Remove scaling transforms for captcha containers */
[name="g-recaptcha-*"]{
    transform:scale(0.89);
}
```

### 10. Script Registration Cleanup

**In each file, remove:**
```php
// Remove all instances of:
wp_enqueue_script( 'wbc-woo-captcha' );
wp_enqueue_script( 'wbc-woo-captcha-v3' );

// Remove no-conflict code:
if ( 'yes' === $wbc_recapcha_no_conflict ) {
    global $wp_scripts;
    $urls = array( 'google.com/recaptcha', 'gstatic.com/recaptcha' );
    // Dequeue/deregister code
}
```

## Database Options Cleanup

### Options to Migrate and Remove
```sql
-- Options to migrate to new format:
wbc_recapcha_version -> wbc_captcha_service
wc_settings_tab_recapcha_site_key -> wbc_recaptcha_v2_site_key
wc_settings_tab_recapcha_secret_key -> wbc_recaptcha_v2_secret_key

-- Options to consolidate:
wbc_recapcha_enable_on_[context] -> wbc_captcha_enabled_contexts (array)
wbc_recapcha_[context]_theme -> wbc_captcha_[service]_theme_[context]
wbc_recapcha_[context]_size -> wbc_captcha_[service]_size_[context]
```

### Migration Script Template
```php
function wbc_migrate_legacy_options() {
    // Get old version
    $version = get_option( 'wbc_recapcha_version', 'v2' );
    
    // Map to new service
    $service_map = array(
        'v2' => 'recaptcha_v2',
        'v3' => 'recaptcha_v3',
    );
    
    // Set new service option
    update_option( 'wbc_captcha_service', $service_map[$version] );
    
    // Migrate keys based on version
    if ( 'v2' === $version ) {
        update_option( 'wbc_recaptcha_v2_site_key', get_option( 'wc_settings_tab_recapcha_site_key' ) );
        update_option( 'wbc_recaptcha_v2_secret_key', get_option( 'wc_settings_tab_recapcha_secret_key' ) );
    } else {
        update_option( 'wbc_recaptcha_v3_site_key', get_option( 'wc_settings_tab_recapcha_site_key_v3' ) );
        update_option( 'wbc_recaptcha_v3_secret_key', get_option( 'wc_settings_tab_recapcha_secret_key_v3' ) );
    }
    
    // Migrate enabled contexts
    $contexts = array();
    if ( 'yes' === get_option( 'wbc_recapcha_enable_on_login' ) ) {
        $contexts[] = 'wp_login';
    }
    // ... check all contexts
    update_option( 'wbc_captcha_enabled_contexts', $contexts );
    
    // Set migration flag
    update_option( 'wbc_captcha_migrated', '1.0' );
}
```

## Testing After Removal

### Critical Test Cases
1. **Service Switching**
   - [ ] Switch between v2, v3, and Turnstile in admin
   - [ ] Verify settings persist correctly
   - [ ] Check that forms update immediately

2. **Form Rendering**
   - [ ] Each form displays correct captcha type
   - [ ] No JavaScript errors in console
   - [ ] Proper styling and positioning

3. **Verification**
   - [ ] Valid captcha allows form submission
   - [ ] Invalid/missing captcha shows error
   - [ ] Error messages display correctly

4. **Edge Cases**
   - [ ] IP whitelist still works
   - [ ] No-conflict mode functions properly
   - [ ] Ajax forms work correctly

## Rollback Plan

If issues arise after legacy code removal:

1. **Immediate Rollback:**
   - Restore from backup
   - Revert git commits

2. **Gradual Rollback:**
   - Re-add legacy functions with deprecation notices
   - Log usage to identify problem areas
   - Fix issues and retry removal

## Timeline Recommendation

**Phase 1 (Week 1-2):**
- Complete Turnstile registration
- Update all integration points
- Internal testing

**Phase 2 (Week 3-4):**
- Beta testing with select users
- Fix any identified issues
- Prepare migration scripts

**Phase 3 (Week 5):**
- Remove legacy code in staging
- Comprehensive testing
- Documentation updates

**Phase 4 (Week 6):**
- Production deployment
- Monitor for issues
- User support ready

## Support Considerations

### Common Issues After Migration
1. **Settings not migrated:** Run migration script manually
2. **JavaScript conflicts:** Check for other captcha plugins
3. **Forms not showing captcha:** Verify service is configured
4. **Verification failures:** Check API keys and server connectivity

### Debug Mode
Add temporary debug logging during transition:
```php
if ( defined( 'WBC_CAPTCHA_DEBUG' ) && WBC_CAPTCHA_DEBUG ) {
    error_log( 'Captcha Service: ' . wbc_captcha_service_manager()->get_active_service()->get_service_id() );
    error_log( 'Context: ' . $context );
    error_log( 'Verification Result: ' . ( $result ? 'Success' : 'Failed' ) );
}
```

## Final Cleanup

After successful deployment and stabilization:
1. Remove debug code
2. Delete migration scripts
3. Update plugin version
4. Release changelog
5. Update public documentation