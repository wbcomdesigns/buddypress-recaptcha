# Wbcom CAPTCHA Manager — Feature Audit Report

**Generated:** 2026-05-06
**Version:** 2.1.0 (branch `release/2.1.0`)
**Source:** [`audit/manifest.json`](manifest.json)
**Counts:** 1 REST endpoint · 2 AJAX actions · 7 admin pages · 1 block · 1 shortcode · 13 services · 42 unique hooks fired · 5 CAPTCHA providers · 0 tables · 0 CPTs

---

## 1. Frontend features

The plugin has **no end-user frontend pages** of its own. Its job is to inject CAPTCHA challenges into other plugins' / WordPress core's existing forms. The closest thing to a frontend feature is the **AJAX login widget**.

### 1.1 AJAX Login widget (Gutenberg block + classic widget)

- **Block name:** `buddypress-recaptcha/login` (`blocks/login-widget/block.json`)
- **Classic widget class:** `WBC_Login_Widget` (`includes/widgets/class-wbc-login-widget.php`)
- **Form selector:** `#wbc-ajax-login-form`
- **Submit selector:** `.wbc-login-button`
- **AJAX action:** `wbc_ajax_login`
- **Nonce:** `wbc_ajax_login_nonce` (field name: `wbc_login_nonce`)
- **JS module:** `public/js/wbc-ajax-login.js`
- **CSS:** `public/css/wbc-ajax-login.css`
- **CAPTCHA context:** `widget_login` (per-context option: `wbc_recaptcha_enable_on_widget_login`)
- **Provider hooks:** `widget_login` triggers the active service's `render()` and `verify()` paths.
- **Successful response (2.1.0):** `{ success: true, data: { message, redirect_to, user: { id, display_name } } }` — `email` deliberately omitted (PII fix in 2.1.0).

### 1.2 CAPTCHA injection points (28 enabled-on contexts)

The plugin enables itself per form via `wbc_recaptcha_enable_on_<context>` options. Each context resolves to a specific render-and-verify pair via `WBC_Captcha_Service_Base`'s context maps.

| Context group | Contexts |
|---|---|
| WordPress core | `wp_login`, `wp_register`, `wp_lostpassword`, `comment` |
| WooCommerce | `woo_login`, `woo_register`, `woo_lostpassword`, `woo_checkout_guest`, `woo_checkout_login` |
| BuddyPress | `bp_register`, `bp_group_create` |
| bbPress | `bbpress_topic`, `bbpress_reply` |
| Form builders | `cf7`, `wpforms`, `gravityforms`, `ninjaforms`, `forminator`, `elementorpro`, `divi` |
| EDD | `edd_checkout`, `edd_login`, `edd_register` |
| MemberPress | `memberpress_login`, `memberpress_register` |
| Ultimate Member | `um_login`, `um_register`, `um_password` |
| Plugin's own | `widget_login` (the AJAX login widget above) |

---

## 2. AJAX handlers

| Action | Scope | Handler | Nonce | Capability | Purpose |
|---|---|---|---|---|---|
| `wbc_ajax_login` | priv + nopriv | `WBC_AJAX_Login_Handler::handle_ajax_login` | `wbc_ajax_login_nonce` | none (public login) | AJAX login flow — verify nonce, verify active CAPTCHA, `wp_signon`, return JSON. |
| `wbcom_addons_cards` | priv | `Wbcom_Admin_Settings::wbcom_addons_cards_links` | implicit | (none, but only on admin pages) | Returns the addon-cards markup for the WB Plugins admin menu. Shared Wbcom helper. |

**Note:** the plugin does not register any `wp_ajax_*` actions for CAPTCHA verification itself. Verification happens inline within whichever framework's form-validation hook the captcha is hooked into (e.g., `bp_signup_validate`, `woocommerce_checkout_process`, `comment_form_validate`).

---

## 3. REST endpoints

| Route | Method | Handler | Permission | Source | Purpose |
|---|---|---|---|---|---|
| `/altcha/v1/challenge` | GET | `Altcha_Lib::register_rest_route` closure | `__return_true` | `includes/lib/altcha/class-altcha-lib.php:622` | Issues an ALTCHA proof-of-work challenge. Required for the ALTCHA client widget. |

The plugin's main verify pipeline is **not** REST-based — it runs synchronously inside `wp_signon` / WC checkout / BP signup hooks via `WBC_Captcha_Service_Manager::verify()`.

---

## 4. Admin pages & menu

| Title | Slug | Parent | Source | Notes |
|---|---|---|---|---|
| WB Plugins | `wbcomplugins` | top-level | `admin/class-recaptcha-for-buddypress-admin.php:178` | Top-level menu — shared across all Wbcom plugins. |
| General | `wbcomplugins` | `wbcomplugins` | `:179` | First submenu (alias of top-level). |
| **Wbcom CAPTCHA Manager** | `buddypress-recaptcha` | `wbcomplugins` | `:182` | **The main settings page.** |
| Wbcom Plugins (catalog) | `wbcom-plugins-page` | `wbcomplugins` | `admin/wbcom/wbcom-admin-settings.php:208` | Wbcom shared admin — addon catalog. |
| Wbcom Support | `wbcom-support-page` | `wbcomplugins` | `:216` | Wbcom shared admin — support links. |
| Wbcom License | `wbcom-license-page` | `wbcomplugins` | `:224` | Wbcom shared admin — license codes (kept for legacy plugins; 2.1.0 uses keyless EDD). |
| ALTCHA Options | `altcha-options` | `options-general.php` | `includes/lib/altcha/admin.php:15` | Vendored ALTCHA library — separate page from main plugin settings. |

All 7 admin pages gate on `manage_options` (no custom capability).

---

## 5. Settings inventory

The plugin uses `register_setting` 34 times; settings live in `wp_options` under prefixes `wbc_*` (current) and `wbc_recapcha_*` (legacy back-compat with the original misspelling).

### 5.1 Per-provider credentials

| Provider | Site key | Secret key | Active option |
|---|---|---|---|
| Google reCAPTCHA v2 | `wbc_recaptcha_v2_site_key` | `wbc_recaptcha_v2_secret_key` | `wbc_captcha_service = 'recaptcha-v2'` |
| Google reCAPTCHA v3 | `wbc_recaptcha_v3_site_key` | `wbc_recaptcha_v3_secret_key` | `'recaptcha-v3'` |
| hCaptcha | `wbc_hcaptcha_site_key` | `wbc_hcaptcha_secret_key` | `'hcaptcha'` |
| Cloudflare Turnstile | `wbc_turnstile_site_key` | `wbc_turnstile_secret_key` | `'turnstile'` |
| ALTCHA | (no key — HMAC-based) | `wbc_altcha_hmac_key` | `'altcha'` |

`WBC_Settings_Migration` handles the legacy hyphen-form (`wbc_recaptcha-v2_site_key`) → underscore-form migration.

### 5.2 Per-context enable flags (28)

`wbc_recaptcha_enable_on_<context>` — `'yes'` enables CAPTCHA for the context. See section 1.2 for the full list of contexts.

### 5.3 Appearance

| Option | Type | Default | Notes |
|---|---|---|---|
| `wbc_recaptcha_theme` | `light\|dark` | `light` | Shared across reCAPTCHA v2 + hCaptcha. |
| `wbc_recaptcha_size` | `normal\|compact\|invisible` | `normal` | Shared. **Triggers conditional CSS in 2.1.0** (no scaling at compact). |

### 5.4 Disable-submit-until-verified (per context)

`wbc_recapcha_disable_submitbtn_<context>` — `'yes'` keeps the form submit button disabled until the user solves the captcha. Works for reCAPTCHA v2 and hCaptcha (added in 2.1.0). Contexts: wp_login, wp_register, wp_lost_password, woo_login, woo_signup, woo_lostpassword, woo_signup_bp, bbpress_topic, bbpress_reply, guestcheckout, logincheckout.

### 5.5 IP whitelist (since 2.1.0 supports ranges + CIDR)

| Option | Format | Notes |
|---|---|---|
| `wbc_recaptcha_ip_to_skip_captcha` (or legacy `wbc_recapcha_ip_to_skip_captcha`) | comma-separated entries | Each entry can be: exact IPv4 (`1.2.3.4`), dash-range (`1.2.3.4-1.2.3.10`), or CIDR (`10.0.0.0/24`). Helper: `wb_recaptcha_ip_matches_entry()`. |

### 5.6 New options in 2.1.0

| Option | Type | Default | Filter | Behavior |
|---|---|---|---|---|
| `wbc_captcha_fail_closed` | bool | false | `wbc_captcha_fail_closed` | When true, missing config + verify exceptions BLOCK forms instead of fail-open. |
| `wbc_captcha_strict_nonce` | bool | false | `wbc_captcha_strict_nonce` | When true, the per-context nonce is REQUIRED on plugin-controlled forms. |

Neither has UI yet — set via `wp option update` or filter. UI toggles deferred to a follow-up release.

### 5.7 No-conflict mode

| Option | Notes |
|---|---|
| `wbc_recapcha_no_conflict` | Dequeue other plugins' reCAPTCHA v2 scripts on the page. |
| `wbc_recapcha_no_conflict_v3` | Same for v3. |

---

## 6. Database tables

_None._ The plugin stores everything in `wp_options`.

---

## 7. Content types

_None._ No CPTs, no taxonomies.

---

## 8. JavaScript modules

| File | Role |
|---|---|
| `public/js/recaptcha-for-buddypress-public.js` | Frontend boot — generic captcha lifecycle helpers across all providers. |
| `public/js/wbc-ajax-login.js` | AJAX login widget — submit handler + provider-aware reset (`resetActiveCaptcha()` since 2.1.0). |
| `public/js/altcha.min.js` | Vendored ALTCHA widget. |
| `admin/wbcom/assets/js/...` | Wbcom shared admin scripts. |

Each provider's API JS (Google, hCaptcha, Turnstile) is enqueued via the service's `get_script_url()` — **hCaptcha now appends `?hl=<language>` since 2.1.0**.

---

## 9. CSS modules

| File | Role |
|---|---|
| `public/css/recaptcha-for-buddypress-public.css` | Shared widget CSS. |
| `public/css/wbc-ajax-login.css` | AJAX login widget styles. |
| `admin/css/...` + `admin/wbcom/assets/css/...` | Admin settings page styles. |

**RTL variants** are auto-generated by `grunt rtlcss` into `public/css/rtl/`.

**hCaptcha widget styling (since 2.1.0):** scaling moved into a per-render `<style>` block, applied only at non-compact sizes. `text-align:center` on the wrapper centers the widget regardless of size. `transform-origin:0 0` replaces the legacy `margin-left:-20px` hack so the widget aligns with form fields without overflow.

---

## 10. Email templates

_None._ The plugin never sends email.

---

## 11. Cron jobs

_None._

---

## 12. WP-CLI commands

_None._

---

## 13. Integrations (third-party plugin compatibility)

| Plugin | Detection | Where wired |
|---|---|---|
| WooCommerce | `class_exists('WooCommerce')` | settings page is WC-style; checkout / login / register / lost-pwd / reviews hooks |
| BuddyPress | `class_exists('BuddyPress')` | `bp_signup_validate`, `bp_after_group_details_creation_step` (manual-test flagged in card #9856913445) |
| bbPress | `class_exists('bbPress')` | `bbp_new_topic_pre_extras`, `bbp_new_reply_pre_extras` |
| Contact Form 7 | function/class detection | `wpcf7_validate_*` filters |
| WPForms | hook detection | `wpforms_process_*` |
| Gravity Forms | hook detection | `gform_validation` |
| Ninja Forms | hook detection | `ninja_forms_post_run_action_*` |
| Forminator | hook detection | `forminator_validate_*` |
| Elementor Pro | hook detection | `elementor_pro/forms/validation` |
| Divi | hook detection | `et_pb_contact_form_*` |
| EDD | hook detection | `edd_checkout_error_checks`, `edd_pre_process_purchase_form` |
| MemberPress | hook detection | login + signup forms |
| Ultimate Member | hook detection | login + register + lost-pwd forms |

---

## 14. Custom capabilities

_None._ All admin paths gate on `manage_options`. No custom roles are registered.

---

## 15. Extensibility (extension points for 3rd-party plugins)

| Hook | Type | Purpose |
|---|---|---|
| `wbc_register_captcha_services` | action | Register a custom service implementing `WBC_Captcha_Service_Interface`. |
| `wbc_should_render_captcha` | filter | Skip rendering for a specific context/request. |
| `wbc_should_verify_captcha` | filter | Skip verification. |
| `wbc_captcha_verified` | filter | Override verification result post-API-call. |
| `wbc_captcha_fail_closed` | filter (2.1.0) | Per-request override of the fail-closed mode. |
| `wbc_captcha_strict_nonce` | filter (2.1.0) | Per-request override of strict-nonce mode. |
| `wbc_recaptcha_v3_verify` | filter | v3-specific score-based override. |
| `wbc_recaptcha_*_settings` (10+) | filter | Settings page extension — inject sub-tabs / fields. |
| `anr_recaptcha_domain` | filter | Override the Google reCAPTCHA hostname (e.g., GFW workaround). |

---

## 16. Known issues surfaced by audit

### 16.1 Dead code (non-blocking)

- **`recaptcha-for-buddypress.php:127`** — `wb_recaptcha_plugin_activation()` registers `register_activation_hook` inside a `plugins_loaded` callback. The outer registration at line 80 is the real one; this nested call is a no-op. Safe to remove.

### 16.2 Inline event handlers (legacy Wbcom admin pattern)

`admin/class-wbc-setup-wizard.php` (2 locations) and `admin/includes/class-wbc-buddypress-settings-page.php` (4 locations) emit `onclick=""` attributes. The `wp-plugin-development` rule prefers `data-wp-on--click` or `addEventListener`. Non-blocking; gradual migration recommended.

### 16.3 wppqa nonce-no-cap findings (6 real, 10 false positives)

See [`audit/wppqa-baseline-2026-05-06/SUMMARY.md`](wppqa-baseline-2026-05-06/SUMMARY.md). The 6 real findings are admin-side handlers that should pair `wp_verify_nonce()` with `current_user_can('manage_options')`. The 10 false positives are CAPTCHA verify paths (pre-auth by design).

### 16.4 AJAX login has no client timeout

`public/js/wbc-ajax-login.js` uses `$.ajax` without a `timeout` option. A failed network leaves the form in a permanent spinner state. Low risk for short login round-trips but worth a 30s timeout in a future refactor.

### 16.5 Manual-test items from the original Reciprocate Technologies report

Both still need browser QA against a real BuddyPress / Reign install:

1. **BuddyPress AJAX group-creation modal** — does hCaptcha auto-discover `.h-captcha` divs injected after page load, or do we need an explicit `hcaptcha.render()` call?
2. **Multiple hCaptcha widgets on one page** — WooCommerce MyAccount login + register simultaneously.

---

## 17. 2.1.0 release scope (Reciprocate Technologies bug report)

**Card #9856913788 (hCaptcha gaps):**
- 1.1 `data-callback` + inline JS (disable-submit, post-verify callback)
- 1.2 Provider-aware AJAX login reset (`resetActiveCaptcha()`)
- 1.3 `?hl=<language>` in hCaptcha script URL
- 1.4 Conditional scaling CSS (no scaling at compact)
- UX bonus: widget centered at every size

**Card #9856914241 (security):**
- 2.1 Drop `email` from AJAX login response (PII)
- 2.2 Admin notice + opt-in `wbc_captcha_fail_closed`
- 2.3 Opt-in `wbc_captcha_strict_nonce` (hCaptcha + reCAPTCHA v2 + Turnstile)
- 2.4 IP whitelist range + CIDR parsing
- 2.5 ALTCHA `REMOTE_ADDR`-only IP detection
- 2.6 Strict comparison in activation redirect

All 10 items verified end-to-end (browser + wp-eval + Plugin Check). See branch `release/2.1.0` commits `c2b0bf8` (source) + `c29e791` (build).
