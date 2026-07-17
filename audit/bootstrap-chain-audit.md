# Bootstrap-chain audit — buddypress-recaptcha 2.1.0

**Audit date:** 2026-05-06
**Mode:** audit-only (no fixes applied)
**Scope:** what each companion skill in the wp-plugin-onboard bootstrap chain expects vs what currently exists in this plugin

The wp-plugin-onboard skill describes the plugin. The four skills in the bootstrap chain own different infrastructure surfaces. This document grades the plugin against each skill's expectations so the team can pick which gaps to close in follow-up work.

| Skill | Status | Critical gaps | Nice-to-have gaps |
|---|---|---|---|
| `/wp-plugin-development` | ⚠️ partial | phpcs.xml ruleset, pre-commit hook | inline-SVG → Lucide migration, dashicons → Lucide migration |
| `/wp-plugin-ci-setup` | ⚠️ partial (✅ PHPStan + Actions) | PHPUnit dev dep, WPCS dev dep, `tests/` directory | PHP 8.4 in CI matrix, WPCS job in CI |
| `/wp-plugin-release` | ⚠️ partial (✅ version sync) | `.distignore`, `bin/build-release.sh` (currently grunt-only) | pre-build version-sync check |
| `/wp-plugin-release-qa` | ❌ missing | `audit/qa/` checklists, `audit/journeys/` smoke flows, `bin/run-journeys.sh` | none |

Detailed findings follow.

---

## 1. `/wp-plugin-development` — code hygiene

**What this skill owns:** WPCS sniff config, pre-commit hook, escaping rules, security patterns, admin UI conventions, Lucide icon rule, inline-SVG ban.

### What exists
- PHPStan config (`phpstan.neon`) — owned by `/wp-plugin-ci-setup`, but lives here too; level 5 with sensible excludes (vendor, dist, ALTCHA library).

### Critical gaps

#### 1.1 `phpcs.xml` (or `.dist`) — MISSING

The plugin has no PHPCS ruleset. WPCS rules are not machine-enforced locally or in CI. Existing code uses an informal "WPCS-ish" style but there's no gate.

**Impact:** Any contributor can land code that violates WPCS without notice. We've worked around this in the 2.1.0 review by running the `wpcs` MCP manually — that's a stop-gap, not a substitute.

**Suggested fix (out of scope here):** generate a standard `phpcs.xml.dist` with the WordPress + WordPress-Extra rulesets, exclude `vendor/`, `node_modules/`, `dist/`, `includes/lib/altcha/`, `assets/`. Add `wp-coding-standards/wpcs` + `dealerdirect/phpcodesniffer-composer-installer` as dev deps.

#### 1.2 Pre-commit hook — MISSING

Neither `.githooks/pre-commit` nor `bin/git-hooks/pre-commit` exists. No staged-file gate runs before commits.

**Impact:** Issues that should be blocked locally (PHP syntax errors, WPCS violations, missed nonce, etc.) reach CI. Slows feedback loop and increases the chance of merging dirty commits to `main`.

**Suggested fix:** activate the tracked git-hooks pattern (`bin/git-hooks/pre-commit`) which runs `composer lint` + WPCS on staged files. Activatable via `composer install-hooks` (one-time per clone).

### Nice-to-have gaps

#### 1.3 Inline `<svg>` in `admin/wbcom/wbcom-admin-settings.php` (3 hits)

Lines 294, 300, 306 emit hardcoded inline SVG paths for the WB Plugins menu (move/grid/help icons).

**Impact:** Wbcom-shared admin file (drop-in across all Wbcom plugins). Migration to Lucide / a shared SVG sprite is a Wbcom-org-wide refactor, not a per-plugin one. Defer.

#### 1.4 Dashicons in `admin/js/recaptcha-for-buddypress-admin.js`

Three uses of `dashicons-arrow-down-alt2` / `dashicons-arrow-right-alt2` for the settings accordion.

**Impact:** Dashicons are still WordPress-blessed. The Lucide rule in `/wp-plugin-development` is a preference, not a hard requirement. Low priority.

---

## 2. `/wp-plugin-ci-setup` — CI infrastructure

**What this skill owns:** PHPUnit matrix, PHPStan baseline, WPCS workflow, GitHub Actions, branch protection.

### What exists ✅
- **`phpstan.neon`** — level 5, scans plugin root with sensible excludes (vendor, node_modules, dist, assets, ALTCHA library). Includes WooCommerce + WP-CLI stubs. Has a thoughtful `ignoreErrors` block for legitimate WP/WC patterns and 3rd-party plugin functions.
- **`.github/workflows/ci.yml`** — runs on push + PR to `main`. Two jobs:
  - `php-lint` — matrix PHP 8.1 / 8.2 / 8.3 via `composer lint` (parallel-lint).
  - `phpstan` — single PHP 8.2 job via `composer phpstan`.
- **Composer dev deps** — `php-parallel-lint`, `phpstan/phpstan` ^1.10, `szepeviktor/phpstan-wordpress`, `php-stubs/woocommerce-stubs`, `php-stubs/wp-cli-stubs`. Sensible.

### Critical gaps

#### 2.1 PHPUnit dev dep + `tests/` directory — MISSING

No `phpunit/phpunit` in `composer.json`. No `tests/` directory. The plugin has **zero automated unit / integration test coverage**.

**Impact:** All testing is manual. The 2.1.0 release was verified by manually exercising flows in a browser + `wp eval` + Plugin Check — none of that captured as a test that would catch regressions.

**Suggested fix (out of scope here):** add `phpunit/phpunit` + `yoast/phpunit-polyfills` + `wp-phpunit/wp-phpunit` as dev deps. Scaffold `tests/phpunit/` with at minimum:
- IP whitelist parser (`wb_recaptcha_ip_matches_entry`) — easiest to test, pure function with 6+ existing wp-eval cases.
- Strict-nonce verify branches per provider.
- Email-leak regression (assert AJAX login response shape).

#### 2.2 WPCS workflow in CI — MISSING

The CI workflow runs PHP Lint + PHPStan but no WPCS job. PR reviews don't get a sniff report.

**Impact:** Same as 1.1 — coding standards drift. Especially relevant since 1.1's local hook is also missing.

**Suggested fix:** add a `wpcs` job to `.github/workflows/ci.yml` that runs `composer wpcs` against the changed files (or full plugin on a release branch).

### Nice-to-have gaps

#### 2.3 PHP 8.4 missing from CI matrix

The skill recommends matrix PHP 8.1 / 8.2 / 8.3 / 8.4 × WP 6.7 / 6.8 / latest. Current matrix is 8.1 / 8.2 / 8.3 (no WP version axis at all because there's no PHPUnit job).

**Impact:** Low — 8.4 is recent (released Nov 2024). Add when next touching the workflow.

#### 2.4 No WP version matrix

Without PHPUnit, there's no place to test against multiple WordPress versions. PHP Lint alone passes on any WP version because it doesn't load WordPress.

**Impact:** Compatibility issues with new WP releases (e.g., 6.9 deprecations) only surface in customer reports. Tied to gap 2.1.

---

## 3. `/wp-plugin-release` — release packaging

**What this skill owns:** dist zip building, version triangulation, `bin/build-release.sh`, `.distignore`.

### What exists ✅
- **`gruntfile.js`** — well-structured `build` task that runs `clean:pre` → `rtlcss` → `clean:maps` → `cssmin` → `uglify` → `makepot` → `copy:dist` → `compress:dist` → `clean:post`. Produces `dist/buddypress-recaptcha-<version>.zip`.
- **`package.json`** — npm scripts: `build`, `rtl`, `minify`, `makepot`, `watch`, `clean`. Used by Grunt. Reads version which propagates into the dist zip filename.
- **Version triangulation** — all 4 version-bearing files currently agree at **2.1.0**:
  - `recaptcha-for-buddypress.php` plugin header `Version: 2.1.0`
  - `recaptcha-for-buddypress.php` `RFB_PLUGIN_VERSION` constant
  - `package.json` `"version": "2.1.0"`
  - `README.txt` `Stable tag: 2.1.0`

### Critical gaps

#### 3.1 `.distignore` — MISSING

No `.distignore` file. The dist scope is defined inline inside `gruntfile.js` `copy:dist` task (lines 195-225) using `'!path/**'` exclusions.

**Impact:** Two issues:
- The skill's Phase 1.5 distribution-scope detection falls through to "fallback_defaults" instead of reading a real `.distignore` because the Gruntfile is parsed but `.distignore` is the more standard signal.
- `wp.org svn` deploy patterns expect `.distignore`. If the plugin ever moves to wp.org, that file is required.

**Suggested fix:** extract the Gruntfile exclusions into `.distignore`, then update `gruntfile.js` `copy:dist` to read from it (or keep both in sync).

#### 3.2 `bin/build-release.sh` — MISSING

No shell wrapper around `npm run build`. Builds run via `npm run build` directly.

**Impact:** Two issues:
- No pre-build sanity check (e.g., "are all 4 version files in sync?"). If 2.1.1 is bumped in `package.json` but not in the PHP header, the build silently produces a 2.1.1 zip with a 2.1.0 plugin.
- Release humans / CI need to know the "build" entry-point. Currently the only way to learn this is to read `package.json` scripts. A standard shell entry-point makes the release workflow explicit.

**Suggested fix:** add `bin/build-release.sh` that:
1. Greps the 4 version sources, asserts they agree (fail fast otherwise).
2. Runs `npm ci` (clean install).
3. Runs `npm run build`.
4. Reports the output zip path + size.

### Nice-to-have gaps

None. Version triangulation works in practice; the gaps above are about making it harder to misuse, not about correctness today.

---

## 4. `/wp-plugin-release-qa` — manual + automated QA

**What this skill owns:** manual QA checklists, release-time smoke runs, journey framework, `bin/run-journeys.sh`.

### What exists
_Nothing._ The plugin has no QA infrastructure at all.

### Critical gaps

#### 4.1 `audit/qa/` or `plan/qa/` — MISSING

No release checklists. The 2.1.0 release was QA'd ad-hoc (via this conversation's manual verification). That's not repeatable.

**Suggested fix:** scaffold `audit/qa/` with:
- `checklist-backend.md` — admin settings save/load round-trip per provider, IP whitelist forms, language switcher, role gates.
- `checklist-frontend.md` — captcha render+verify on every supported context (28 contexts × 5 providers is a big matrix; prioritize the top 8 contexts × 5 providers = 40 cells).
- `release-runbook.md` — pre-release smoke test sequence.

#### 4.2 `audit/journeys/` — MISSING

No automated end-to-end browser flows. Bug fixes don't have regression sentinels.

**Impact:** When 2.1.0 ships and a 2.2.0 refactor is needed, there's no automated guard to ensure the email-leak fix isn't reintroduced. Or that the strict-nonce flag still rejects requests without the nonce. Or that hCaptcha still renders with `?hl=es`.

**Suggested fix:** scaffold `audit/journeys/` with at minimum 5 critical-priority Playwright journeys derived from the 2.1.0 verification:
1. `customer/ajax-login-response-shape.md` — submit AJAX login, assert response has no `email`.
2. `customer/hcaptcha-render-spanish.md` — load wp-login with `language=es`, assert script URL has `?hl=es`.
3. `customer/hcaptcha-compact-no-scaling.md` — load with `wbc_recaptcha_size=compact`, assert no inline scaling style.
4. `admin/missing-keys-shows-notice.md` — clear keys, trigger verify, load admin dashboard, assert notice present.
5. `security/strict-nonce-rejects-missing.md` — enable strict mode, post without nonce, assert 403/false.

#### 4.3 `bin/run-journeys.sh` — MISSING

No journey executor. (Implied by 4.2.)

### Nice-to-have gaps

None — once 4.1–4.3 are in place, the framework is foundational and grows naturally with each future fix.

---

## Recommended follow-up cards (in priority order)

1. **`/wp-plugin-development` — add phpcs.xml + pre-commit hook**
   _Effort: ~2 hours. Unblocks all later quality work._

2. **`/wp-plugin-ci-setup` — add WPCS job + PHPUnit dev dep + tests/ scaffold + first 3 unit tests**
   _Effort: ~4 hours. First tests should be: IP whitelist parser, strict-nonce branches, email-leak regression._

3. **`/wp-plugin-release-qa` — scaffold audit/qa/ + audit/journeys/ + the 5 critical journeys above**
   _Effort: ~3 hours. The 5 journeys are derived directly from 2.1.0's manual verification — copy from the conversation transcript._

4. **`/wp-plugin-release` — extract `.distignore` + add `bin/build-release.sh` with version-sync gate**
   _Effort: ~1 hour. Mostly mechanical._

5. **Address the 6 real `nonce-no-cap` admin-side findings** from the wppqa baseline.
   _Effort: ~1 hour. Add `current_user_can('manage_options')` next to each `wp_verify_nonce()` in admin handlers._

6. **(Lower priority) Lucide migration for `wbcom-admin-settings.php` inline SVGs and dashicons in admin JS.**
   _Effort: cross-plugin Wbcom refactor — coordinate with other Wbcom plugin maintainers._

Total estimated effort to close the bootstrap chain: ~10 hours for steps 1–4 (gating infrastructure), plus 1 hour for step 5 (real wppqa fixes). Steps 6+ are improvements, not gaps.

---

## What this audit deliberately did NOT do

- ❌ Did not write any code.
- ❌ Did not modify `composer.json`.
- ❌ Did not create `phpcs.xml`, `.distignore`, `bin/build-release.sh`, `tests/`, `audit/qa/`, `audit/journeys/`.
- ❌ Did not invoke any companion skill in its write/scaffold mode.
- ❌ Did not change any plugin behavior.

The plugin's current `release/2.1.0` branch is unchanged from commit `5a38661` (the onboarding commit). All findings above are observations to inform follow-up cards.
