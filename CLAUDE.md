# Plugin: Wbcom CAPTCHA Manager (`buddypress-recaptcha`)

> **READ FIRST:** [`audit/manifest.json`](audit/manifest.json) is the canonical inventory (structurally re-scanned 2026-07-16) — 1 REST endpoint, 1 AJAX action, 2 admin pages (6 tabs), 1 block, 1 widget, **0 shortcodes**, 19 services, 35 unique hooks fired, 5 CAPTCHA providers (reCAPTCHA v2 / v3 / Turnstile / hCaptcha / ALTCHA), **14 integrations**, 29 contexts, 0 tables, 0 CPTs, **0 live `register_setting()` calls**. Use this before grepping. See also [`audit/FEATURE_AUDIT.md`](audit/FEATURE_AUDIT.md), [`audit/CODE_FLOWS.md`](audit/CODE_FLOWS.md), [`audit/ROLE_MATRIX.md`](audit/ROLE_MATRIX.md), [`audit/wppqa-baseline-2026-06-04/SUMMARY.md`](audit/wppqa-baseline-2026-06-04/SUMMARY.md) (latest, post card-panel migration; earlier: [`audit/wppqa-baseline-2026-05-06/SUMMARY.md`](audit/wppqa-baseline-2026-05-06/SUMMARY.md)). Refresh via `/wp-plugin-onboard --refresh` after non-trivial changes.
>
> **Known-dead surfaces (recorded 2026-07-16, NOT yet fixed — each needs a card).** Do not treat these as working:
> - `admin/class-wbc-setup-wizard.php` is **never required by any file** — the whole setup wizard is unreachable and `wbc_enable_setup_wizard` never fires.
> - `wbc_hcaptcha_theme/_size`, `wbc_turnstile_theme/_size`, `wbc_recaptcha_v3_badge` are **written but never read**. Only the shared `wbc_recaptcha_theme`/`wbc_recaptcha_size` are live (reCAPTCHA v2 + hCaptcha). Turnstile has no working appearance control.
> - **Custom error messages do nothing** — settings write `wbc_recapcha_error_msg_captcha_*`, readers use `wc_settings_tab_recapcha_*` / `*_v3`, and nothing bridges them.
> - Activation redirect is gated on Woo/BP/bbPress (`recaptcha-for-buddypress.php:123`) — a plain WordPress site gets no signpost.
> - `gruntfile.js` still points at the deleted `admin/wbcom/` asset tree.
>
> **Never re-add these as integrations:** Fluent Forms, Formidable Forms, WS Form, FluentCart. All four were falsely advertised with zero first-party code; the readme claims were removed 2026-07-16. Formidable appears only inside the never-loaded vendored ALTCHA lib.

## Quick reference

- **Main file**: `recaptcha-for-buddypress.php`
- **Version**: `2.1.0`
- **Class prefixes**: `WBC_*`, `Recaptcha_For_BuddyPress*`, `BPRC_*` (no PSR-4 namespace)
- **Requires at least**: `6.3` — set by `blocks/login-widget/block.json` `apiVersion 3`. This is the real code-justified floor; do not raise it without a code reason. (History: a truthful 5.9 was set 2026-06-04, then overwritten with an unjustified 6.9; corrected to 6.3 on 2026-07-16.)
- **Text domain**: `buddypress-recaptcha`
- **Repo**: https://github.com/wbcomdesigns/buddypress-recaptcha
- **Distribution**: wbcomdesigns.com via the official EDD SL SDK (keyless — no license check; free download with EDD update channel). `item_id 1246648` is already set in the SDK registration. **Do not touch the EDD SDK or its `item_id`.**
- **Extends**: nothing (standalone — no Pro counterpart)

## Key entry points

- **Plugin bootstrap**: `recaptcha-for-buddypress.php` → `includes/class-recaptcha-for-buddypress.php` (the `Recaptcha_For_BuddyPress` core class wires admin + public hooks via the `Recaptcha_For_BuddyPress_Loader` registry).
- **Service manager** (the heart of the plugin): `includes/class-captcha-service-manager.php` (`WBC_Captcha_Service_Manager` singleton). Registry + dispatcher for all 5 providers. Resolves the active provider from the `wbc_captcha_service` option.
- **Service base class**: `includes/class-captcha-service-base.php` (`WBC_Captcha_Service_Base`). Owns the context→nonce / context→form-selector / context→submit-selector / context→enable-option maps. **Read this before adding a new context.**
- **Per-provider services**: `includes/services/class-{recaptcha-v2,recaptcha-v3,hcaptcha,turnstile,altcha}-service.php`.
- **AJAX login widget**: `includes/class-wbc-ajax-login-handler.php` (server) + `public/js/wbc-ajax-login.js` (client) + `includes/class-wbc-login-block.php` / `includes/widgets/class-wbc-login-widget.php` (UI).
- **Admin menu + page shell (card-panel, since 2.1.0)**: `includes/admin/class-bprc-admin.php` (`BPRC_Admin`, prefix `bprc`). Registers the shared `wbcomplugins` hub (only when no other Wbcom plugin created it yet) plus the `CAPTCHA` submenu, both with `manage_options` (the cap gate WP enforces before `render_page()` runs). Renders 6 tabs (Overview / Quick Setup / Protection / Advanced / Updates / Discover, filterable via `bprc_admin_tabs`) using `includes/admin/views/{shell,hub,overview,updates,discover}.php`, and on save **delegates to `WBC_BuddyPress_Settings_Page::wbc_save()`** using the *same* nonce field/action (`bp_recaptcha_submit_fields_nonce` / `bp_recaptcha_submit_nonce`) and option keys as the legacy admin. **This migration is UX-only** — no option key, nonce, field name, or save mechanism changed.
- **Admin settings fields (legacy renderer, still the field source)**: `admin/includes/class-wbc-buddypress-settings-page.php` (large file — uses WooCommerce-style settings even when WC is absent). The card-panel above calls into this for field output + save.

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
  4. `readme.txt` `Stable tag:` (lowercase — renamed from `README.txt` on 2026-07-16)
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
| 2026-07-16 | Docs (manifest refresh) | **Structural manifest re-scan against shipped code — no code changed.** Corrected drift from the 2026-06-04 manifest: shortcodes `1 → 0` (the recorded shortcode lived in the deleted `admin/wbcom/`); AJAX actions `2 → 1` (same cause); admin pages `7 → 2` (the "ALTCHA Options" page never existed at runtime — `lib/altcha/admin.php` is deliberately never required); hooks `42 → 35` (WP/WC re-dispatch split into `hooks_not_owned`; added `bprc_admin_tabs`, `wbc_recaptcha_v3_score_threshold_value`); `register_setting` `34 → 0` live (all 32 sit in the never-loaded `lib/altcha/settings.php`); block name corrected to `wbc/login-widget`; `wbc_captcha_verified` has 4 firers not 5 (v3 uses `wbc_recaptcha_v3_verify`). Recorded the 14 real integrations with dual evidence + the 4 false claims (Fluent Forms, Formidable, WS Form, FluentCart) as explicitly NOT integrated. **New finding: the setup wizard is unreachable dead code** (never required). Recorded — not fixed — the dead appearance options, dead error-message keys, the Woo/BP/bbPress-gated activation redirect, and gruntfile's dead `admin/wbcom/` paths. Each needs a card. | `audit/manifest.json`, `audit/manifest.summary.json`, `CLAUDE.md` |
| 2026-06-29 | Bug-fix (wiring/security) | **"Add keys -> it works" audit + 8 fixes (into unreleased 2.1.0).** Decoupled WP **comment** (display+verify) and **lost-password** (verify) from `register_woocommerce_hooks()` -> now in `define_public_hooks()`, so they work on non-WooCommerce sites (were silent no-ops); single `lostpassword_post` callback picks context by nonce field (`wp_lostpassword` vs `woo_lostpassword`), fixing the Woo context mismatch. **BP group create** now aborts via `bp_core_add_message()`+`bp_core_redirect()` scoped to the group-details step (`groups_group_before_save` is a `do_action` that ignored the old error bag). **AJAX login widget** reads the correct `wbc_captcha_service` key + matching `recaptcha-v2` id so the widget resets after a failed login (was token-reuse). Renamed generic `Login`/`Registration`/`Lostpassword` -> `WBC_*` (fatal-on-collision). Guarded `is_plugin_active()` in ALTCHA (front-end fatal). reCAPTCHA v3 score threshold now reads the global admin key `wbc_recaptcha_v3_score_threshold` (was reading unused typo keys). Earlier defer-filter IE-scoping fix folded in. Browser-verified comment+login render; locked `audit/CAPTCHA-WIRING-CONTRACT.md`; verdict in `audit/AUDIT-VERDICT-2026-06-29.md`. | `includes/class-recaptcha-for-buddypress.php`, `public/lrl-classes/{Login,Registration,Lostpassword}.php`, `public/woocommerce-extra/{LostpasswordPost,Woocommerce_Order}.php`, `public/bp-classes/Registrationbp.php`, `includes/services/{class-altcha-service,class-recaptcha-v3-service}.php`, `public/js/wbc-ajax-login.js`, `README.txt`, `audit/CAPTCHA-WIRING-CONTRACT.md` |
| 2026-06-04 | Code-quality + onboarding | **2.1.0 post-migration pass.** Corrected the inflated `Requires at least` floor `6.0` → truthful `5.9` (highest WP-version-flagged function is `str_starts_with()` @ WP 5.9; Plugin Check `wp_functions_compatibility` passes at 5.9), header + readme; added `Requires PHP: 7.4` to readme. Refreshed `audit/manifest.json` (card-panel `BPRC_Admin` admin page/service, updated wppqa findings + generated meta) and added `audit/wppqa-baseline-2026-06-04/SUMMARY.md`. Confirmed 0 real high-sev in the migrated card-panel admin code (the one `nonce-no-cap` flag at `class-bprc-admin.php:318` is a layered-defense false positive — menu `manage_options` gate runs before render). EDD SL SDK untouched. | `recaptcha-for-buddypress.php`, `README.txt`, `audit/manifest.json`, `audit/wppqa-baseline-2026-06-04/SUMMARY.md`, `CLAUDE.md` |
| 2026-05-06 | Release | **2.1.0** — Reciprocate Technologies bug report (10 fixes across 2 cards). hCaptcha parity (data-callback, AJAX reset, language, conditional CSS, centered alignment), security hardening (no email leak, admin notice + opt-in fail-closed, opt-in strict-nonce, IP range/CIDR parsing, ALTCHA REMOTE_ADDR-only, strict comparison). | `class-hcaptcha-service.php`, `class-{recaptcha-v2,turnstile,altcha}-service.php`, `class-captcha-service-manager.php`, `class-wbc-ajax-login-handler.php`, `class-recaptcha-for-buddypress.php`, `recaptcha-helper-functions.php`, `lib/altcha/class-altcha-lib.php`, `recaptcha-for-buddypress.php`, `wbc-ajax-login.js`, `package.json`, `README.txt` |
| 2026-04-03 | Build | 2.0.2 build — minified assets, RTL CSS, POT, dist zip. | `dist/`, `languages/`, `public/css/min/`, `public/js/min/` |
| 2026-05-06 | Onboarding | First-time wp-plugin-onboard run — added `audit/` (manifest + reports + graph) + this CLAUDE.md. | `audit/`, `CLAUDE.md` |
