# Wbcom CAPTCHA Manager — Code Flow Maps

**Generated:** 2026-05-06
**Source:** [`audit/manifest.json`](manifest.json)

---

## Flow A: Render-and-verify a captcha on a 3rd-party form (the core flow)

**Entry point:** any form-rendering hook (e.g., `login_form` for `wp_login`, `bp_signup_validate` for `bp_register`, `woocommerce_review_order_after_submit` for `woo_checkout`).

### Code path

```
WP/WC/BP form-render hook
        │
        ▼
WBC_Captcha_Service_Manager::render( $context, $args )      ← includes/class-captcha-service-manager.php:213
        │
        ├──▶ is_ip_whitelisted()                              ← if user IP matches wbc_recaptcha_ip_to_skip_captcha,
        │                                                       skip render entirely.
        │       └─ wb_recaptcha_ip_matches_entry()            ← exact / range / CIDR (since 2.1.0)
        │
        ├──▶ get_active_service()                             ← reads wbc_captcha_service option
        │
        ├──▶ $service->is_configured()                        ← if not configured:
        │                                                       set transient + show admin notice (since 2.1.0)
        │
        ├──▶ $service->is_enabled_for_context( $context )     ← reads wbc_recaptcha_enable_on_<ctx>
        │
        ├──▶ $service->enqueue_scripts( $context )            ← provider-specific api.js
        │       └─ hCaptcha: ?hl=<language> appended (2.1.0)
        │
        └──▶ $service->render( $context, $args )              ← outputs HTML + per-render <style> + <script>
                  │
                  └─ hCaptcha render emits:
                       - hidden nonce field
                       - <div class="h-captcha" data-sitekey data-theme data-size data-callback>
                       - <style>: .wbc_hcaptcha_field{ text-align:center }
                                  + #h-captcha-<ctx>-wbc{ scale } if non-compact
                       - <script>: window.hcaptchaCallback_<ctx> = function(token) { ... }
```

**Then on form submission**, the same form's validation hook fires:

```
WP/WC/BP form-validate hook
        │
        ▼
WBC_Captcha_Service_Manager::verify( $context, $response, $args )   ← class-captcha-service-manager.php:264
        │
        ├──▶ get_active_service()
        │
        ├──▶ if !$service || !$service->is_configured():
        │       - set transient wbc_captcha_not_configured = 1
        │       - log error
        │       - apply_filters('wbc_captcha_fail_closed', option, $context)
        │           └─ true:  return false  (block)
        │           └─ false: return true   (legacy fail-open)
        │
        ├──▶ if !$service->is_enabled_for_context( $context ): return true
        │
        ├──▶ $response = $_POST[ $service->get_response_field_name() ]   ← g-recaptcha-response,
        │                                                                  h-captcha-response, etc.
        │
        └──▶ try { $service->verify( $response, $args ) } catch:
                  ├─ wbc_captcha_fail_closed filter ⇒ false / true
                  │
                  └─ Inside service::verify():
                       1. nonce check (advisory or strict per wbc_captcha_strict_nonce)
                       2. POST $response to provider's /siteverify endpoint
                       3. apply_filters('wbc_captcha_verified', $verified, $api_result, $response, $service_id)
                       4. return bool
```

### Key files

| File | Role |
|---|---|
| `includes/class-captcha-service-manager.php` | Singleton dispatcher. |
| `includes/class-captcha-service-base.php` | Abstract base — context maps (nonce/selector/option), no-conflict, IP gate. |
| `includes/services/class-{recaptcha-v2,recaptcha-v3,hcaptcha,turnstile,altcha}-service.php` | Per-provider render/verify. |
| `includes/recaptcha-helper-functions.php` | `wb_recaptcha_ip_matches_entry()`, `wb_recaptcha_get_the_user_ip()`, legacy back-compat shims. |

### Permissions
- **Render:** none (any visitor seeing the form gets the captcha, modulo IP whitelist).
- **Verify:** none (verification is pre-auth; the caller decides what to do with the result).

### Required settings

- `wbc_captcha_service` (or auto-detected from configured keys)
- Active provider's site + secret key
- `wbc_recaptcha_enable_on_<context> = 'yes'`

---

## Flow B: AJAX login widget submit

**Entry point:** user clicks the submit button inside the `wbc_login` block / classic widget on a frontend page.

```
public/js/wbc-ajax-login.js  (submit handler)
        │
        ▼  $.ajax POST /wp-admin/admin-ajax.php?action=wbc_ajax_login
        │
        ▼
admin-ajax.php → fires `wp_ajax_wbc_ajax_login` / `wp_ajax_nopriv_wbc_ajax_login`
        │
        ▼
Recaptcha_For_BuddyPress::handle_ajax_login()             ← shim: includes/class-recaptcha-for-buddypress.php:810
        │
        ▼
WBC_AJAX_Login_Handler::handle_ajax_login()                ← includes/class-wbc-ajax-login-handler.php:26
        │
        ├──▶ wp_verify_nonce( $_POST['wbc_login_nonce'], 'wbc_ajax_login_nonce' )    ← line 28
        │       └─ fail: wp_send_json_error
        │
        ├──▶ wbc_verify_captcha( 'widget_login' )                                      ← line 38
        │       └─ delegates to WBC_Captcha_Service_Manager::verify('widget_login')
        │       └─ fail: wp_send_json_error( wbc_get_captcha_error_message(...) )
        │
        ├──▶ sanitize $username, $password (NOT sanitized — passwords must be raw),
        │   $remember, $redirect_to
        │
        ├──▶ wp_signon($credentials, is_ssl())
        │       └─ on error: wp_send_json_error
        │
        └──▶ wp_send_json_success({
              message: "Welcome back, <name>!",
              redirect_to,
              user: { id, display_name }      ← email REMOVED in 2.1.0
            })
        │
        ▼  client receives JSON
        │
        ▼  on error: resetActiveCaptcha()         ← provider-aware (grecaptcha / hcaptcha / turnstile)
        ▼  on success: setTimeout 1s → window.location = redirect_to
```

### Key files

| File | Lines | Role |
|---|---|---|
| `public/js/wbc-ajax-login.js` | 1–117 | Frontend submit handler + provider-aware reset. |
| `includes/class-wbc-ajax-login-handler.php` | 1–98 | Server-side handler. |
| `includes/class-wbc-login-block.php` | 28+ | Gutenberg block render. |
| `includes/widgets/class-wbc-login-widget.php` | — | Classic widget render. |

### AJAX chain

| Step | What | Input | Output |
|---|---|---|---|
| 1 | Page load | (none) | Block/widget HTML with `wp_nonce_field('wbc_ajax_login_nonce', 'wbc_login_nonce')` + active CAPTCHA |
| 2 | User solves CAPTCHA | provider-specific | DOM token in `<input name="<provider-response-field>">` |
| 3 | User clicks submit | form data | jQuery `$.ajax` POST |
| 4 | Server handler | `$_POST` | JSON `{success, data}` |
| 5 | JS update | JSON response | redirect or error message + captcha reset |

---

## Flow C: ALTCHA challenge issuance (REST)

**Entry point:** ALTCHA-protected form's client-side widget calls `/wp-json/altcha/v1/challenge` on render.

```
GET /wp-json/altcha/v1/challenge
        │
        ▼
register_rest_route closure                ← includes/lib/altcha/class-altcha-lib.php:622
        │
        ├──▶ generate HMAC-signed challenge (random salt + difficulty)
        │
        └──▶ return { algorithm, challenge, salt, signature, maxnumber }
```

The client-side ALTCHA widget then proof-of-works the challenge and posts the solution to whichever form-validation hook the captcha is wired into.

---

## Flow D: Settings save (admin)

**Entry point:** `Save Changes` button on `Wbcom CAPTCHA Manager` settings page.

```
admin form POST → woocommerce_admin_settings_sanitize_option (filter)
        │
        ▼
WBC_BuddyPress_Settings_Page (settings save handler)
        │
        ├──▶ check_admin_referer('settings_save_nonce')                ← nonce
        │       └─ ⚠️  no current_user_can() pair (wppqa #1 — false positive masked by menu cap gate)
        │
        ├──▶ update_option('wbc_*', $sanitized_value)  × N
        │
        └──▶ do_action('wbc_recaptcha_settings_saved')                  ← extension point
```

---

## Flow E: Plugin activation

```
User clicks Activate → register_activation_hook fires
        │
        ▼
activate_recaptcha_for_woocommerce()                  ← recaptcha-for-buddypress.php:65
        │
        └──▶ Recaptcha_For_BuddyPress_Activator::activate()
                  └─ flush_rewrite_rules() etc.
        │
        ▼
activated_plugin action fires
        │
        ▼
wb_recaptcha_activation_redirect_settings($plugin)    ← recaptcha-for-buddypress.php:138
        │
        ├──▶ if $plugin === plugin_basename(__FILE__) AND (WC|BP|bbPress active):
        │       └─ wp_safe_redirect(admin.php?page=buddypress-recaptcha)
        │
        └─ NOTE: line 141 uses === (strict comparison) since 2.1.0 (was == before).
```
