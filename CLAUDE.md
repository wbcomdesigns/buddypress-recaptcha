# Plugin: Wbcom CAPTCHA Manager (`buddypress-recaptcha`)

> **READ FIRST:** [`audit/manifest.json`](audit/manifest.json) is the canonical inventory — 1 REST endpoint, 2 AJAX actions, 7 admin pages, 1 block, 13 services, 42 unique hooks fired, 5 CAPTCHA providers (reCAPTCHA v2 / v3 / hCaptcha / Turnstile / ALTCHA), 0 tables, 0 CPTs. Use this before grepping. See also [`audit/FEATURE_AUDIT.md`](audit/FEATURE_AUDIT.md), [`audit/CODE_FLOWS.md`](audit/CODE_FLOWS.md), [`audit/ROLE_MATRIX.md`](audit/ROLE_MATRIX.md), [`audit/wppqa-baseline-2026-05-06/SUMMARY.md`](audit/wppqa-baseline-2026-05-06/SUMMARY.md). Refresh via `/wp-plugin-onboard --refresh` after non-trivial changes.

## Quick reference

- **Main file**: `recaptcha-for-buddypress.php`
- **Version**: `2.1.0`
- **Class prefixes**: `WBC_*`, `Recaptcha_For_BuddyPress*` (no PSR-4 namespace)
- **Text domain**: `buddypress-recaptcha`
- **Repo**: https://github.com/wbcomdesigns/buddypress-recaptcha
- **Distribution**: wbcomdesigns.com via EDD SL SDK (keyless, free download with EDD update channel)
- **Extends**: nothing (standalone — no Pro counterpart)

## Key entry points

- **Plugin bootstrap**: `recaptcha-for-buddypress.php` → `includes/class-recaptcha-for-buddypress.php` (the `Recaptcha_For_BuddyPress` core class wires admin + public hooks via the `Recaptcha_For_BuddyPress_Loader` registry).
- **Service manager** (the heart of the plugin): `includes/class-captcha-service-manager.php` (`WBC_Captcha_Service_Manager` singleton). Registry + dispatcher for all 5 providers. Resolves the active provider from the `wbc_captcha_service` option.
- **Service base class**: `includes/class-captcha-service-base.php` (`WBC_Captcha_Service_Base`). Owns the context→nonce / context→form-selector / context→submit-selector / context→enable-option maps. **Read this before adding a new context.**
- **Per-provider services**: `includes/services/class-{recaptcha-v2,recaptcha-v3,hcaptcha,turnstile,altcha}-service.php`.
- **AJAX login widget**: `includes/class-wbc-ajax-login-handler.php` (server) + `public/js/wbc-ajax-login.js` (client) + `includes/class-wbc-login-block.php` / `includes/widgets/class-wbc-login-widget.php` (UI).
- **Admin settings page**: `admin/includes/class-wbc-buddypress-settings-page.php` (large file — uses WooCommerce-style settings even when WC is absent).

## Provider model — every captcha shares this shape

Every CAPTCHA provider extends `WBC_Captcha_Service_Base` and implements:

```
public function get_service_id();           // e.g., 'hcaptcha'
public function get_site_key();             // reads wbc_<provider>_site_key
public function get_secret_key();           // reads wbc_<provider>_secret_key
public function get_script_url();           // provider api.js (?hl=lang for hCaptcha + reCAPTCHA v2 since 2.1.0)
public function get_script_handle( $context );
public function render( $context, $args );  // emits HTML + per-render <style> + <script>
public function verify( $response, $args ); // calls /siteverify, applies wbc_captcha_verified filter
public function get_verify_endpoint();
public function get_response_field_name();  // e.g., 'h-captcha-response'
public function requires_no_conflict();     // dequeue conflicting scripts?
```

Adding a new provider = create `includes/services/class-<id>-service.php` + register it in `WBC_Captcha_Service_Manager::register_default_services()` OR via the `wbc_register_captcha_services` action (3rd-party).

## Per-render rendering pattern (since 2.1.0)

The hCaptcha / reCAPTCHA v2 services emit a per-render `<style>` block instead of inline-styled wrapper:

```html
<input type="hidden" name="<context>-nonce" value="<nonce>" />
<div class="wbc_<provider>_field"><div id="<provider>-<context>-wbc" class="..." data-callback="<cb>" ... /></div>
<style>
  .wbc_<provider>_field { text-align: center; }
  /* scaling only at non-compact size */
  #<provider>-<context>-wbc { transform: scale(.89); transform-origin: 0 0; ... }
</style>
<script>
  window.<provider>Callback_<context> = function(token) { /* re-enable submit + fire woo_<context>_captcha_verified */ };
</script>
```

If you add a new provider with a checkbox-style widget, mirror this pattern — don't add inline `style="..."` to the wrapper.

## Important patterns

- **All option keys prefixed `wbc_`** (or legacy `wbc_recapcha_` for original-typo back-compat). `WBC_Settings_Migration` (`includes/class-settings-migration.php`) handles the underscore/hyphen migration.
- **No PSR-4 namespace**; no Composer-autoloaded plugin code. The vendored EDD SL SDK and ALTCHA library are the only Composer dependencies.
- **ALTCHA library at `includes/lib/altcha/` is vendored** and treated as plugin-owned. Modifications there are deliberate forks (e.g., 2.1.0's REMOTE_ADDR-only IP detection fix).
- **No raw `$wpdb` use** — the plugin doesn't touch tables. Everything lives in `wp_options`.
- **Pre-auth verification by design**: every CAPTCHA service's `verify()` runs before the user is authenticated. This is why `wppqa_check_plugin_dev_rules` flags 10 nonce-no-cap "issues" in the service layer — those are false positives. Only the 6 admin-side findings are real.
- **Settings UI uses WooCommerce-style hooks** (`woocommerce_admin_field_*`, `woocommerce_settings_*`) even when WC is absent. This is a legacy choice — don't ADD new dependencies on this pattern.
- **Per-context behavior dispatched via string maps**, not OOP polymorphism. To add a new form context, extend the maps in `WBC_Captcha_Service_Base` (nonce-action, form selector, submit selector, enable-option) — adding a new method on each service class is NOT the pattern.

## Build / release

- **Build**: `npm run build` (= `grunt build`) → `dist/buddypress-recaptcha-<version>.zip`. Reads version from `package.json` (NOT from the plugin header — these must stay in sync).
- **Version sync points** (when bumping a release):
  1. `recaptcha-for-buddypress.php` plugin header `Version:`
  2. `recaptcha-for-buddypress.php` `RFB_PLUGIN_VERSION` constant
  3. `package.json` `"version"`
  4. `README.txt` `Stable tag:`
- **CI**: `.github/workflows/ci.yml` runs PHP Lint + PHPStan level 5.

## CSS selectors (for testing / dev)

- AJAX login form: `#wbc-ajax-login-form`, `.wbc-login-button`, `.wbc-form-messages`
- hCaptcha wrapper: `.wbc_hcaptcha_field`, captcha id `#h-captcha-<context>-wbc`
- reCAPTCHA v2 wrapper: `.wbc_recaptcha_field`, captcha id-attribute `[name="g-recaptcha-<context>-wbc"]`
- Each context has a stable nonce field name: `<context>-nonce` (e.g., `wp-login-nonce`, `widget-login-nonce`).

## Bootstrap chain — companion skills to invoke after this onboarding

Detected during onboarding (Phase 1.1) — out of scope for this skill, route to the listed skill:

- ⏳ Pre-commit hook missing → `/wp-plugin-development` (Part 1.4)
- ⏳ WPCS ruleset (`phpcs.xml`) missing → `/wp-plugin-development` (Part 8)
- ⏳ PHPUnit dev deps missing in `composer.json` → `/wp-plugin-ci-setup` (Step 2)
- ⏳ Release build script (`bin/build-release.sh`) missing → `/wp-plugin-release` (build relies on grunt; consider a thin shell wrapper)
- ⏳ `.distignore` missing → `/wp-plugin-release` (currently rely on Gruntfile copy:dist)
- ⏳ QA checklists (`audit/qa/` or `plan/qa/`) missing → `/wp-plugin-release-qa`
- ✅ PHPStan config exists (`phpstan.neon`) — no action needed
- ✅ GitHub Actions exists (`.github/workflows/ci.yml`) — no action needed

## Recent changes

| Date | Type | Description | Files |
|---|---|---|---|
| 2026-05-06 | Release | **2.1.0** — Reciprocate Technologies bug report (10 fixes across 2 cards). hCaptcha parity (data-callback, AJAX reset, language, conditional CSS, centered alignment), security hardening (no email leak, admin notice + opt-in fail-closed, opt-in strict-nonce, IP range/CIDR parsing, ALTCHA REMOTE_ADDR-only, strict comparison). | `class-hcaptcha-service.php`, `class-{recaptcha-v2,turnstile,altcha}-service.php`, `class-captcha-service-manager.php`, `class-wbc-ajax-login-handler.php`, `class-recaptcha-for-buddypress.php`, `recaptcha-helper-functions.php`, `lib/altcha/class-altcha-lib.php`, `recaptcha-for-buddypress.php`, `wbc-ajax-login.js`, `package.json`, `README.txt` |
| 2026-04-03 | Build | 2.0.2 build — minified assets, RTL CSS, POT, dist zip. | `dist/`, `languages/`, `public/css/min/`, `public/js/min/` |
| 2026-05-06 | Onboarding | First-time wp-plugin-onboard run — added `audit/` (manifest + reports + graph) + this CLAUDE.md. | `audit/`, `CLAUDE.md` |
