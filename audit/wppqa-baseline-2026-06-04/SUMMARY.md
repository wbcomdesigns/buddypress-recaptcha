# wppqa Baseline — buddypress-recaptcha 2.1.0 (post card-panel migration)

**Date:** 2026-06-04
**Branch:** `release/2.1.0` (HEAD: `b8693af` — "Migrate admin to Wbcom card-panel (UX-only) for 2.1.0")
**Tool:** `wp-plugin-qa` MCP — `wppqa_check_plugin_dev_rules`
**Scope of this pass:** code-quality Plugin Check + onboarding refresh. Admin UX migration is DONE and out of scope; EDD SL SDK (vendored, keyless, item_id 1246648) untouched.

## Result

| Check | Passed | Failed | Skipped | Duration |
|---|---|---|---|---|
| `plugin_dev_rules` | 0 | **15** (high) + 7 (warning) | 0 | 23 ms |

Compared with the 2026-05-06 baseline (16 high): the legacy `admin/class-recaptcha-for-buddypress-admin.php:221` and `admin/wbcom/wbcom-admin-settings.php:43` hits are gone (those admin entry points are no longer the menu/save owner after the migration), and the migrated card-panel adds exactly one new flagged location — `includes/admin/class-bprc-admin.php:318`.

## New admin code (migration target) — confirmation: 0 real high-sev

The migration introduced `includes/admin/class-bprc-admin.php` (`BPRC_Admin`, prefix `bprc`) + views under `includes/admin/views/`.

- **`includes/admin/class-bprc-admin.php:318`** — `nonce-no-cap`. **False positive (layered defense).**
  `render_page()` is the callback of a menu page registered at lines 118-140 with `$cap = 'manage_options'`. WordPress enforces that capability *before* `render_page()` is ever invoked, so by the time the in-page nonce save at line 318 runs, `current_user_can('manage_options')` is already guaranteed true. The nonce here is the second layer (CSRF) on top of the menu cap gate (authZ). This is the **same pattern, same option keys, same nonce field/action** as the legacy admin save it replaced — the migration is UX-only and did not weaken authorization.

**Conclusion for new admin code: 0 real high-severity findings.** The card-panel views (`shell.php`, `hub.php`, `overview.php`, `updates.php`) produce no high-sev findings (the `WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound` notices Plugin Check emits on them are themselves false positives — every variable IS `bprc_`-prefixed).

## High-severity findings (15) — all `nonce-no-cap`, all false positives / pre-existing

| File:Line | Class | Triage |
|---|---|---|
| `includes/admin/class-bprc-admin.php:318` | **NEW (migrated admin)** | False positive — menu `manage_options` cap gate runs before render. See above. |
| `admin/class-wbc-setup-wizard.php:629,778,895` | pre-existing | Setup-wizard step handlers — admin-side, cap-gated by wizard menu. Pre-existing legacy debt, not in scope. |
| `admin/includes/class-settings-renderer.php:766` | pre-existing | Settings renderer save path — pre-existing legacy debt. |
| `includes/class-wbc-ajax-login-handler.php:28` | pre-existing | Public AJAX login endpoint — intentionally unauthenticated. False positive. |
| `includes/services/class-altcha-service.php:317` | pre-existing | CAPTCHA verify — pre-auth by design. False positive. |
| `includes/services/class-hcaptcha-service.php:257,261` | pre-existing | CAPTCHA verify — pre-auth by design. False positive. |
| `includes/services/class-recaptcha-v2-service.php:201,205` | pre-existing | CAPTCHA verify — pre-auth by design. False positive. |
| `includes/services/class-recaptcha-v3-service.php:223` | pre-existing | CAPTCHA verify — pre-auth by design. False positive. |
| `includes/services/class-turnstile-service.php:161,165` | pre-existing | CAPTCHA verify — pre-auth by design. False positive. |
| `public/woocommerce-extra/Woocommerce_After_Checkout_Validation.php:52` | pre-existing | WC guest-checkout validation — pre-auth by design. False positive. |

**Triage:** 11 of 15 are pre-auth CAPTCHA-verify / public-login false positives (capability checks would defeat the protection). The remaining 4 (`setup-wizard` x3 + `settings-renderer`) are pre-existing admin-side hardening opportunities (cap-gated by their menu pages; layered `current_user_can()` would be belt-and-braces) — pre-existing legacy debt, left as-is per the scope of this pass.

## Medium warnings (7) — pre-existing legacy

- `recaptcha-for-buddypress.php:127` — `activation-nested` (dead nested `register_activation_hook`; harmless, pre-existing).
- `admin/class-wbc-setup-wizard.php:685,724` + `admin/includes/class-wbc-buddypress-settings-page.php:1135,1190,1246,1300` — `inline-onclick`, legacy Wbcom admin pattern.

None are in the migrated card-panel code.

## WordPress Plugin Check (code-quality, paid/EDD — wp.org cosmetics ignored)

`wp plugin check buddypress-recaptcha` via Local wp-cli.

- **`wp_functions_compatibility` (the WP-floor check): 0 findings at `Requires at least: 5.9`.** The highest WP version any function requires is **5.9.0** (`str_starts_with()`, polyfilled by WP since 5.9). At floor 5.8 this is flagged; at 5.9 it passes. The header previously declared `6.0` (inflated). **Fixed: header + readme floor set to the truthful `5.9`; `Requires PHP: 7.4` confirmed present in the header and added to README.txt.**
- **0 high-severity ERRORs in code.** The only ERROR-level Plugin Check rows are wp.org-cosmetic / packaging items, explicitly out of scope for a paid EDD plugin: `outdated_tested_upto_header` (Tested up to 6.9) and `compressed_files` (the tracked `dist/buddypress-recaptcha-2.1.0.zip` build artifact).
- Remaining warnings (`trademarked_term`, `unexpected_markdown_file`, `hidden_files`, `github_directory`, readme tag/short-description, `PrefixAllGlobals` on already-`bprc_`-prefixed vars) are wp.org cosmetics / false positives — ignored per portfolio policy (Plugin Check = code-quality only for paid plugins).
- **Vendored EDD SL SDK under `vendor/` not modified** — its findings are out of scope.

## Conclusion

- Requires-at-least corrected to the truthful floor **5.9** (was 6.0); `Requires PHP: 7.4` present in both header and README.
- **0 real high-severity findings in the new card-panel admin code** (the single flag is a layered-defense false positive).
- EDD SL SDK untouched.
- No code-quality regressions introduced by the migration. Pre-existing legacy `nonce-no-cap` / `inline-onclick` debt left as-is (out of scope; track in a follow-up card).
