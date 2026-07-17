# Audit Verdict: buddypress-recaptcha (Wbcom CAPTCHA Manager v2.1.0)

## Shippable? NO

Three provider contexts (WP lost password, WordPress comments, BP group creation) have broken verification paths that survive in the codebase right now. A paying customer enabling those contexts gets a CAPTCHA widget that renders but enforces nothing server-side. That is a silent security failure, not a cosmetic gap.

## Sellable? NO — fix 3 critical + 3 high issues first

The core promise ("add keys, enable forms, done") **holds for most contexts** — WP login/register, WooCommerce login/register/checkout, BuddyPress member registration, bbPress topics/replies, and all third-party form builder integrations are correctly wired (render + verify). The admin settings key contract is clean: every option key the admin saves is the same key the service reads for all five providers.

The promise **silently breaks** for:
1. WP lost password on any site without WooCommerce (render: yes, verify: never)
2. WordPress comment forms on any site without WooCommerce (render: never, verify: never)
3. BuddyPress group creation (render: yes, verify: yes, but the verified error never blocks the save)
4. AJAX login widget CAPTCHA reset on failure (CAPTCHA stuck in solved state after wrong password — token reuse window open)

---

## Findings (ranked: Critical → High → Medium → Low)

| # | Severity | Lens | Finding | File:line | Fix journey |
|---|----------|------|---------|-----------|-------------|
| 1 | Critical | Promise / Security | **WP Lost Password: renders but NEVER verifies on non-WooCommerce sites.** `lostpassword_post` hook is inside `register_woocommerce_hooks()` which bails early when `class_exists('WooCommerce')` is false. Admin UI shows "Lost Password Form" under WordPress Core Forms (always visible, not WC-gated). Bot can POST without any CAPTCHA token and receive a password reset email. | `includes/class-recaptcha-for-buddypress.php:555` | fix |
| 2 | Critical | Promise / Security | **Comment form CAPTCHA is an advertised WordPress Core feature that only works when WooCommerce is installed.** Both `comment_form_fields` (render) and `preprocess_comment` (verify) are registered inside `register_woocommerce_hooks()`. On non-WooCommerce sites the setting is a no-op. | `includes/class-recaptcha-for-buddypress.php:568-569` | fix |
| 3 | Critical | Promise / Security | **BuddyPress group creation CAPTCHA verifies but never blocks the save.** Verify is hooked to `groups_group_before_save` which fires inside `BP_Groups_Group::save()`. The hook is a `do_action` — return value and errors on `$bp->groups->current_group->errors` are both silently ignored by BP. Group is created regardless of CAPTCHA result. | `public/bp-classes/Registrationbp.php:73-86` | fix |
| 4 | High | Promise / Security | **AJAX Login Widget: CAPTCHA reset broken for ALL five providers after a failed login attempt.** `enqueue_login_widget_assets()` reads option `wbc_recaptcha_service` (line 810) but the service manager writes to and reads from `wbc_captcha_service` — different keys. The JS therefore always receives the stale default `'recaptcha_v2_checkbox'`. Separately, even if the option key were correct, the service manager stores IDs as `recaptcha-v2` (not `recaptcha_v2_checkbox`) so the JS `type === 'recaptcha_v2_checkbox'` branch never fires. After a failed login the widget shows as solved but is never reset; the old token remains in the POST field. | `includes/class-recaptcha-for-buddypress.php:810`, `public/js/wbc-ajax-login.js:19` | fix |
| 5 | High | Stability | **Generic LRL class names (`Login`, `Registration`, `Lostpassword`) with no namespace or prefix.** Any other plugin or theme defining a class with any of these names causes a PHP fatal on load, silently disabling all protection across every context. The guard at line 306 (`class_exists`) does not prevent the fatal — it triggers in `load_dependencies()` before that line is reached. Affected: whole plugin on any site where a naming collision exists. | `public/lrl-classes/Login.php:22`, `Registration.php:22`, `Lostpassword.php:22` | fix |
| 6 | High | Stability | **ALTCHA: `is_plugin_active()` called unconditionally at file-load time without a `function_exists` guard.** On the frontend, `wp-admin/includes/plugin.php` is not loaded by WP core. If no other plugin (e.g., WooCommerce) has included it first, the result is a fatal: "Call to undefined function is_plugin_active()". This fires during the singleton constructor for every page load when ALTCHA is the active provider. | `includes/services/class-altcha-service.php:13` | fix |
| 7 | Medium | Promise | **WP Lost Password + WooCommerce together: render/verify context mismatch.** On sites WITH WooCommerce, the `lostpassword_post` validator always uses context `woo_lostpassword` (maps to `wbc_recaptcha_enable_on_lostpassword`). If the admin enables only "WP Core Lost Password Form" (`wbc_recaptcha_enable_on_wplostpassword`) but not the WooCommerce lost password toggle, the widget renders correctly but the verify sees the context as disabled and returns `true` — no enforcement. The admin enabling the WP form gets false assurance. | `public/woocommerce-extra/LostpasswordPost.php:33` | fix |
| 8 | Medium | Promise | **reCAPTCHA v3: admin-configured score threshold is written-but-never-read.** The admin saves a global threshold to `wbc_recaptcha_v3_score_threshold`. The v3 service reads per-context legacy options `wbc_recapcha_login_score_threshold_v3`, `wbc_recapcha_wp_register_score_threshold_v3`, etc. — different option keys with the old typo-spelling. None of these are saved by the current admin UI. The threshold setting in the admin panel has zero effect; the service always defaults to `0.5`. | `includes/services/class-recaptcha-v3-service.php:349-360`, `admin/includes/class-wbc-buddypress-settings-page.php:260` | fix |
| 9 | Medium | Promise | **reCAPTCHA v3: token regeneration on form submit is async-without-await.** The inline script adds a `submit` event listener that calls `generateToken()` (a Promise chain) without calling `e.preventDefault()` or awaiting the result. The form POSTs before the new token lands in the hidden field. The 110-second interval refresh is the real mechanism keeping the token fresh; the submit listener is dead code that could cause a stale-token failure for a user whose browser tab has been open more than 110 seconds and then submits immediately. | `includes/services/class-recaptcha-v3-service.php:509-514` | improve |
| 10 | Medium | Standards | **1311 WPCS errors across 110 files** (predominantly `gruntfile.js` inline-comment and brace-spacing auto-fixable issues, plus 5 missing doc comments in the main bootstrap file). The PHP source errors in the bootstrap file are real. The Gruntfile volume inflates the count but 1,646 can be auto-fixed with `phpcbf`. | Multiple files | improve |
| 11 | Low | Promise | **Cloudflare Turnstile: per-context theme and size options never exposed in admin UI.** The Turnstile service reads `wbc_turnstile_theme_{context}` and `wbc_turnstile_size_{context}` but the admin only exposes global site key and secret key. Theme and size always fall back to 'light' and 'normal'. Admins cannot customise Turnstile appearance. | `includes/services/class-turnstile-service.php:99-100` | improve |
| 12 | Low | Promise | **`captcha-verification-helper.php` error-message fallback reads legacy typo option `wbc_recapcha_version`** (line 64). The migration maps this to `wbc_recaptcha_version` but does not guarantee the value is present on fresh installs. On a fresh install the version check defaults to `'v2'`, which is harmless, but the legacy key path is never exercised by any writer after migration. | `includes/captcha-verification-helper.php:64` | cleanup |
| 13 | Low | Promise | **ALTCHA hardcoded inline `style="…"` in the HTTPS-warning markup.** Violates the Wbcom token-first convention; the warning notice background (`#fff3cd`) and border (`#ffc107`) will not adapt to dark mode. | `includes/services/class-altcha-service.php:253` | improve |
| 14 | Low | Standards | **Non-prefixed global function names** `activate_recaptcha_for_woocommerce`, `deactivate_recaptcha_for_woocommerce`, `run_recaptcha_for_woocommerce`, `wb_recaptcha_activation_redirect_settings`. Collision risk on busy multisites. WPCS flags these; easy to rename with `wbc_` prefix. | `recaptcha-for-buddypress.php:65,75,121,142` | improve |

---

## Scores

| Lens | Grade | Notes |
|---|---|---|
| Security | C+ | Admin save path: nonce + cap check correct, POST sanitized. Pre-auth verify by design (not a bug). Critical gap: WP lostpassword and comments unprotected without WooCommerce; BP group create bypass; AJAX widget token-reset broken. |
| Performance | A | No unbounded queries, no N+1, no heavy autoload. Service manager is a singleton, keys are `get_option` (cheap). 10-second `wp_remote_post` timeout on verify is appropriate. |
| UX / Design | B | Admin card-panel is clean. ALTCHA HTTPS-warning has hardcoded colours. Turnstile has no theme/size control. reCAPTCHA v3 inline script has a submit-race that does not affect most users. |
| QA / Testing | D | No PHPUnit tests. No QA runbook. No `.distignore`. WPCS at 1,311 errors (mostly auto-fixable). No pre-commit hook. |
| Standards | C | PHPStan config exists; CI exists. WPCS errors present but most are in `gruntfile.js`. PHP source files need a `phpcbf` pass. Non-prefixed global functions are the main real standard violation in PHP. |

---

## Fix priority for release

1. **Fix #1 + #2 together**: Move `comment_form_fields`, `preprocess_comment`, and `lostpassword_post` hooks out of `register_woocommerce_hooks()` into `define_public_hooks()` with their own `class_exists`/`function_exists` guards only where genuinely needed. Introduce a `wp_lostpassword` verify context that maps to `wbc_recaptcha_enable_on_wplostpassword` and hook it unconditionally to `lostpassword_post`.
2. **Fix #3**: Replace `groups_group_before_save` with `groups_group_before_save` having a `wp_die()` on captcha failure, OR hook into the BuddyPress group-creation step save logic (pre-`groups_create_group()` call) and use `bp_core_add_message()` + redirect to block progress.
3. **Fix #4**: Change `get_option( 'wbc_recaptcha_service', 'recaptcha_v2_checkbox' )` to `get_option( 'wbc_captcha_service', 'recaptcha-v2' )`. Update JS `resetActiveCaptcha()` to match service IDs `recaptcha-v2` → `grecaptcha.reset()`, `hcaptcha` → `hcaptcha.reset()`, `turnstile` → `turnstile.reset()`.
4. **Fix #5**: Rename `Login` → `WBC_Login`, `Registration` → `WBC_Registration`, `Lostpassword` → `WBC_Lostpassword`. Update all callers.
5. **Fix #6**: Wrap ALTCHA line 13 as `if ( ! class_exists( 'AltchaPlugin' ) && ( ! function_exists( 'is_plugin_active' ) || ! is_plugin_active( 'altcha-spam-protection/altcha.php' ) ) )`.
6. **Fix #7**: In `LostpasswordPost`, detect whether the POST originated from the WP core form (`isset($_POST['wp-login.php'])` or check `$_SERVER['REQUEST_URI']`) and use the correct context (`wp_lostpassword` vs `woo_lostpassword`). Simpler: split into two separate action callbacks with separate contexts.
7. **Fix #8**: Either expose per-context threshold fields in the admin UI, or in the v3 service read the single global `wbc_recaptcha_v3_score_threshold` option instead of the legacy per-context keys.
