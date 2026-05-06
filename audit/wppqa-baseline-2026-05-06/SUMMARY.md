# wppqa Baseline — buddypress-recaptcha 2.1.0

**Date:** 2026-05-06
**Branch:** `release/2.1.0` (HEAD: `c29e791`)
**Tool:** `wp-plugin-qa` MCP (3 checks)

## Per-check pass/fail

| Check | Passed | Failed | Skipped | Duration |
|---|---|---|---|---|
| `plugin_dev_rules` | 0 | **16** (high) + 7 (warning) | 0 | 28 ms |
| `rest_js_contract` | 0 | 0 | 1 (no `register_rest_route` calls) | 4 ms |
| `wiring_completeness` | 0 | 0 | 1 (no `templates/` dir) | 0 ms |

## High-severity findings (16) — all `nonce-no-cap`

The `wp-plugin-development` skill's security rule says nonces prevent CSRF but do **not** authorize. Every `wp_verify_nonce()` should be paired with `current_user_can()` (or a more specific cap check) for handlers that mutate state. Failing locations:

| File:Line | Context |
|---|---|
| `admin/class-recaptcha-for-buddypress-admin.php:221` | Admin settings save / form handler |
| `admin/class-wbc-setup-wizard.php:629,778,895` | Setup wizard step handlers |
| `admin/includes/class-settings-renderer.php:766` | Settings renderer save path |
| `admin/wbcom/wbcom-admin-settings.php:43` | Wbcom shared admin settings boilerplate |
| `includes/class-wbc-ajax-login-handler.php:28` | **AJAX login handler** — public, intentionally unauthenticated. Likely false positive (this is a public login endpoint; capability checks would defeat the purpose). |
| `includes/services/class-altcha-service.php:317` | ALTCHA verify path — runtime, no auth context. **False positive** (CAPTCHA verify is by definition pre-auth.) |
| `includes/services/class-hcaptcha-service.php:252,256` | hCaptcha verify path — same false-positive class. |
| `includes/services/class-recaptcha-v2-service.php:199,203` | reCAPTCHA v2 verify — same. |
| `includes/services/class-recaptcha-v3-service.php:223` | reCAPTCHA v3 verify — same. |
| `includes/services/class-turnstile-service.php:161,165` | Turnstile verify — same. |
| `public/woocommerce-extra/Woocommerce_After_Checkout_Validation.php:52` | WC checkout validation — runs pre-auth on guest checkout, **false positive**. |

**Real-vs-false-positive triage:**

- **8 service-layer hits (alt/hcap/v2/v3/turnstile) + AJAX login + WC checkout = 10 false positives.** These are CAPTCHA verification paths that run pre-authentication by design. Capability checks would defeat the protection (logged-in users would bypass CAPTCHA, anonymous users would never reach the endpoint). The wppqa rule is correct in general but doesn't model "intentionally pre-auth" handlers.
- **6 real findings:** the admin-side handlers (admin-class:221, setup-wizard ×3, settings-renderer, wbcom-admin-settings) all process admin form submissions where a `current_user_can('manage_options')` (or similar) check should accompany the nonce. These are legitimate hardening opportunities — currently rely on the menu-page capability gate ahead of the nonce, which is sandboxed but layered defense is the standard pattern.

**Recommendation:** address the 6 admin-side findings in a follow-up release (not blocking 2.1.0). Document the 10 service-layer false positives in a `wppqa-suppressions.json` so future runs surface only real issues.

## Medium warnings (7) — `inline-onclick` + 1 `activation-nested`

| File:Line | Code | Note |
|---|---|---|
| `recaptcha-for-buddypress.php:127` | `activation-nested` | `wb_recaptcha_plugin_activation()` registers `register_activation_hook` inside a `plugins_loaded` callback — the outer `register_activation_hook` at line 80 is the real one, this nested call is a no-op. **Dead code, safe to remove.** |
| `admin/class-wbc-setup-wizard.php:685,724` | `inline-onclick` | Setup wizard inline handlers — Wbcom legacy admin pattern. |
| `admin/includes/class-wbc-buddypress-settings-page.php:1126,1181,1237,1291` | `inline-onclick` | Settings page inline handlers — same legacy pattern. |

## Other categories

- **REST-JS contract drift:** N/A — plugin has no `register_rest_route` calls.
- **Wiring completeness:** N/A — plugin uses no `templates/` directory (settings rendered directly via PHP class methods).

## Conclusion

- 0 release-blocking issues for 2.1.0.
- 6 admin-side `nonce-no-cap` hardening opportunities for the next release.
- 10 service-layer findings are false positives (pre-auth CAPTCHA verification paths).
- 1 dead-code activation hook (`recaptcha-for-buddypress.php:127`) can be removed in a follow-up.
- 6 inline-onclick warnings are legacy Wbcom admin pattern; non-blocking but worth migrating to `data-wp-on--click` over time.

The release/2.1.0 branch is **release-ready** from the perspective of this baseline; follow-ups go in a new card.
