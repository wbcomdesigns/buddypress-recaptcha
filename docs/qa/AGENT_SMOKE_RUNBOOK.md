# Agent Smoke Runbook - Wbcom CAPTCHA Manager

**Audience:** a browser-capable agent (Claude Sonnet or equivalent) with Playwright MCP + WP-CLI Bash access, OR a human QA person with the same access. Both should be able to execute every step of this runbook.

## How to read this runbook

Each C and E step describes a **customer contract**: what the feature promises, why it matters, the surfaces it touches, and what "working" looks like in customer terms. It does NOT prescribe the exact Playwright calls, selectors, REST paths, or DB queries. Read the relevant plugin code, pick the right mechanism, and verify the contract. This freedom is the point: the verifier is expected to notice bugs we did not pre-imagine.

D (regression guards) stays specific - those are repros of past incidents; the exact fixture IS the contract.

Infrastructure sections (preconditions, output contract, debug-log protocol, fixture cleanup, failure protocol) stay specific because they are the stable machinery the walk rides on.

## Global preconditions

- Working directory: `/Users/varundubey/Local Sites/wbcom-free/app/public/wp-content/plugins/buddypress-recaptcha`
- Site URL: `https://wbcom-free.local` (default `http://localhost`)
- WP-CLI: `wp --path="/Users/varundubey/Local Sites/wbcom-free/app/public" <cmd>`
- Admin auto-login: `?autologin=1` on any front-end URL
- Per-user auto-login: `?autologin=<user_login>`
- Playwright: one Chromium session throughout; restart with `browser_close` + `browser_navigate` if it dies.
- Plugin version constant: `RFB_PLUGIN_VERSION`
- Front-end base slug (if applicable): `(none - this plugin adds no front-end routes)`
- Pair plugin: `` (empty if standalone)

## Output contract

At the end of the walk, write exactly one JSON file to
`/Users/varundubey/Local Sites/wbcom-free/app/public/wp-content/plugins/buddypress-recaptcha/docs/qa/.last-smoke-pass.json`:

```json
{
  "mode": "free|combo",
  "release_version": "<from RFB_PLUGIN_VERSION>",
  "ran_at": "<ISO 8601 UTC>",
  "sections": {
    "A_fresh_install":     { "pass": N, "fail": N, "skipped": N },
    "B_upgrade":           { "pass": N, "fail": N, "skipped": N },
    "C_core_flows":        { "pass": N, "fail": N, "skipped": N },
    "D_regression_guards": { "pass": N, "fail": N, "skipped": N },
    "E_extensions":        { "pass": N, "fail": N, "skipped": N },
    "F_cross_browser":     { "pass": N, "fail": N, "skipped": N }
  },
  "failures": [
    { "id": "...", "origin": "from|for", "triage_note": "...", "expected": "...", "actual": "...", "url": "...", "screenshot": "..." }
  ],
  "debug_log_issues": [
    { "section": "...", "level": "fatal|warning|notice|deprecated", "line": "...", "file": "..." }
  ],
  "manual_required": []
}
```

Emit a Basecamp draft per failure using the plugin's own board (project id `37595344`).

## Fixture cleanup (before every walk)

Delete any leftover test data from prior runs. Exact WP-CLI eval here is permitted because this is infrastructure, not a feature check.

This plugin owns no tables. Its fixtures are the test user, the BuddyPress signup row and the pages a walk publishes. Track IDs as you create them and delete by ID - never by title match, which has eaten real seed rows before.

```bash
WP="wp --path=/Users/varundubey/Local Sites/wbcom-free/app/public"
$WP user delete "$QA_USER_ID" --yes 2>/dev/null
$WP post delete $QA_PAGE_IDS --force 2>/dev/null
$WP eval 'global $wpdb; $wpdb->query( $wpdb->prepare(
    "DELETE FROM {$wpdb->prefix}signups WHERE user_login = %s", "qaregv3" ) );'
$WP option update users_can_register 0
echo "fixtures cleaned"
```

Also record and restore the CAPTCHA settings the walk changes - `wbc_captcha_service`, the per-provider keys, `wbc_recaptcha_ip_to_skip_captcha`, and every `wbc_recaptcha_enable_on_*` you flip. Leaving the site on a different provider silently changes what the next walk tests.

## Debug log protocol

Enable WP_DEBUG + WP_DEBUG_LOG + WP_DEBUG_DISPLAY=false before Section A. Baseline `wp-content/debug.log` byte count. After every section, diff new lines into `debug_log_issues[]` classified by level. Any new fatal or warning is a failure unless explicitly whitelisted.

---

## A - Fresh install

### A1 - Activation is clean and nothing is protected yet
**What to verify:** the plugin activates with no fatal, the admin menu appears under WB Plugins > CAPTCHA, and with no keys entered **no form is blocked**. A freshly installed CAPTCHA plugin must never lock anyone out before it is configured.
**Acceptance:** `wp plugin list` shows the new version active; the front page and `/wp-login.php` return 200; logging in works with no CAPTCHA present; the admin shows the "not configured" notice rather than silently enforcing.

### A2 - Settings storage
**What to verify:** this plugin creates **no database tables**. All state lives in `wp_options` under the `wbc_*` prefix. Confirm activation adds no table and that `RFB_PLUGIN_VERSION` matches the header, `readme.txt` Stable tag and `package.json`.

### A3 - REST route registers
**What to verify:** `GET /wp-json/altcha/v1/challenge` returns 200 with a challenge body. If it 5xxs, every ALTCHA-protected form on the site becomes unsubmittable.
**Mechanism:** `wp eval-file tests/audit/rest-reachability.php` covers this deterministically.

---

## B - Upgrade from previous version

### B1 - Migration is silent and existing protection survives
**What to verify:** upgrading from the prior stable version completes with no debug.log entries, and a site that was already configured stays configured - same active provider, same keys, same per-form toggles, still enforcing after the upgrade.
**Why it matters:** `WBC_Settings_Migration` rewrites option keys (underscore/hyphen spellings, `*_v3` consolidation). A migration that drops a key silently turns protection OFF on a live site, or - worse - leaves a verifier armed with no widget, which locks customers out.
**Acceptance:** before/after snapshot of every `wbc_*` option matches or is explainably migrated; a login and a registration still behave identically after the upgrade.

---

## C - Core customer flows

The persona that matters most here is the **anonymous visitor** - the person trying to register or log in. Almost every bug this plugin has shipped hurt exactly that person, and an admin testing while logged in cannot see it. Log out properly between checks; do not assume.

Each step is a contract, not a script. Verify what the visitor sees AND the server-side effect (was the user really created? was an auth cookie really set?), because "the form looked fine" and "the submission was accepted" are different claims.

**Run each of these once per service** - see Section E for the matrix and test keys.

### C.anon.wp-login
**What to verify:** the CAPTCHA renders on `/wp-login.php`, an unsolved submit is refused with the configured message, and a solved submit logs the user in.

### C.anon.login-form-surfaces
**What to verify:** the same for every form built by `wp_login_form()` - the core Login/Logout block, the plugin's own login widget and block, and a theme login form. The widget must be rendered INSIDE the `<form>` element; markup emitted before the opening tag is never submitted and reads as "no CAPTCHA supplied" server-side.
**Why it matters:** this is the exact surface that was unusable in 2.1.0. See D.loginform-render.

### C.anon.ajax-login
**What to verify:** the plugin's AJAX login widget (`#wbc-ajax-login-form`, context `widget_login`) renders its CAPTCHA, refuses an unsolved attempt with a visible message, succeeds when solved, and **resets the widget after a failed attempt** so the visitor can retry without reloading.

### C.anon.register
**What to verify:** BuddyPress registration at `/register/` and, where enabled, WordPress registration. Solved submit completes in ONE pass. On a rejected submit, the error must be specific to the CAPTCHA and the form must retain what the visitor typed - a full re-render that clears an agreement checkbox is a failure, not cosmetic.

### C.anon.lost-password
**What to verify:** the lost-password form renders the CAPTCHA and is verified exactly once. Double-verification spends the single-use token and rejects a legitimate visitor.

### C.anon.comment
**What to verify:** the comment form on a public post carries the CAPTCHA and an unsolved comment is refused. Check both a logged-out visitor and a logged-in one (see `wbc_recaptcha_skip_comment_for_logged_in`).

### C.member.mobile
**What to verify:** every flow above is usable at 390px - the widget is not clipped, the submit button is reachable, and the page does not scroll sideways.

### C.admin.settings
**What to verify:** all six tabs render with no PHP notice/warning/fatal; saving one section does not drop values in another (settings edits MERGE - hard contract); the active-service card and key-status card reflect reality after a save.

### C.admin.toggles-enforce
**What to verify:** the Protection tab toggles are real. Turn one OFF and confirm the widget disappears **and** the form still submits; turn it ON and confirm the widget returns **and** an unsolved submit is refused. A toggle that changes the UI but not enforcement (or vice versa) is the defect class this plugin keeps producing.

### C.admin.ip-allowlist
**What to verify:** an allowlisted address sees no CAPTCHA and is not required to supply one, on every service. See D.ip-allowlist.

### C.admin.unconfigured
**What to verify:** clearing the active provider's keys does not lock anyone out - forms still submit and the admin sees the "not configured" notice. Then enable fail-closed (`wbc_captcha_fail_closed`) and confirm submissions ARE refused. Both directions matter.

---

## CP - Presentation & product completeness (Tier 1 - the gate that decides RFT)

> Per the portfolio QA catalog (docs/standards/qa-catalog.md): presentation and
> functional flow are PRIMARY; static code checks never substitute for this
> section. Every step runs in a real browser (Playwright MCP).

### CP.theme-fit
**What to verify:** key surfaces x each theme in qa-config (`themes[]`, e.g. BuddyX, Reign, TT4) x light/dark x desktop/390px. Computed `font-family` inherits the theme (never a hardcoded stack); accent/color tokens resolve through the theme chain; UNSELECTED controls stay neutral (no theme button-bleed - only the selected/pressed control carries the accent); no raw hex bypassing tokens.

### CP.block-matrix
**What to verify:** every block in qa-config (`blocks[]`) placed on a scratch page renders non-empty DOM (a silently-empty block = FAIL) + screenshot. Then insert each block in the Gutenberg editor: no crash, preview renders, at least one inspector control demonstrably changes output.

### CP.legacy-surfaces
**What to verify:** every shortcode / widget / template override the plugin ships renders correctly (BP-era and Woo plugins are not block-only). Delete this step only if qa-config lists none.

### CP.ssr-js-parity
**What to verify:** for every surface with a client re-render (load more, filters, live search): the JS-built item has the SAME structure/classes as the server-rendered item (DOM-diff one of each). Drift here ships layouts that break only after the first interaction.

### CP.click-everything
**What to verify:** every visible tab, button, action link, and setting leads somewhere real. A tab that can never be populated (feature with no entry point), a button that 404s, or a setting whose effect is unreachable = FAIL.

### CP.states
**What to verify:** every async surface shows loading, empty (canonical empty-state primitive with guidance/CTA, not a bare sentence), and error states. Form validation: a 422 renders its message AT the offending field (invalid styling + aria-invalid) and non-field errors show their real message.

### CP.entry-points
**What to verify:** every data store/feature is reachable three ways - frontend UX, wp-admin view, REST API (cross-check the manifest). Fewer than three = FAIL unless the manifest documents the exception.

### CP.notifications
**What to verify:** trigger each outgoing notification once; confirm it fires EXACTLY once, respects its settings toggle, and the email renders (branded shell, placeholders resolved, links work).

### CP.console-and-assets
**What to verify:** zero JS console errors on every page visited during this walk; plugin CSS/JS are NOT enqueued on an unrelated page (network-tab probe).

---

## D - Known-regression guards

Each row is a repro of a past bug that caused customer pain. D rows stay specific - the fixture IS the contract.

| ID | Bug | Fixture + assertion |
|----|-----|---------------------|
| D.v3-token | 2.2.0 - reCAPTCHA v3 issued no token on any form. The bootstrap is attached to the deferred api.js handle via `wp_add_inline_script`, which is NOT deferred, so it ran first and threw `grecaptcha is not defined`, aborting the IIFE. | Set `wbc_captcha_service=recaptcha-v3` with REAL v3 keys. Load `/register/`. Assert: console has no `grecaptcha is not defined`; `#wbc_recaptcha_bp_register_token` exists inside `#signup-form`; **its value length is > 1000** after ~3s. A token field that exists but stays empty is the bug. |
| D.v3-register | 2.2.0 - BP registration bounced back to a re-rendered form with the agreement checkbox cleared, because the empty token failed verify. | With v3 active, complete `/register/` in one pass. Assert: "Check Your Email To Activate Your Account!" AND a row in `{prefix}signups`. Any return to a populated-but-reset form is a fail. |
| D.loginform-render | 2.2.0 - login from the core Login/Logout block, the login widget and theme login forms ALWAYS failed with "Security verification failed": `wp_login_form()` fires no render hook, but `wp_authenticate_user` verified every POST. | Publish a page with `<!-- wp:loginout {"displayLoginAsForm":true} /-->`. With `wbc_recaptcha_enable_on_wplogin=yes`, assert the CAPTCHA renders INSIDE that form (not before `<form>`), then log in successfully. Run for all five services. |
| D.loginform-nobypass | Guard for the above - the fix must not have opened a bypass. | POST valid credentials to `/wp-login.php` with NO captcha field at all. Assert: no auth cookie is set and the response is not a redirect to wp-admin. |
| D.ip-allowlist | 2.2.0 - the IP allowlist hid the widget but still demanded it on v2/hCaptcha/Turnstile/ALTCHA, locking out exactly the addresses it was meant to exempt (only v3 honoured it). | Set `wbc_recaptcha_ip_to_skip_captcha` to the test client IP. For EACH of the five services: assert no widget renders AND login still succeeds. Then clear the allowlist and assert an empty-token login is blocked again. |
| D.late-render | 2.2.0 - a form rendering after `wp_print_footer_scripts` lost its token silently, because `wp_enqueue_script`/`wp_add_inline_script` are no-ops that late; and the direct-print fallback then emitted api.js twice with a duplicate DOM id. | Render a captcha on `wp_footer` priority 100 on a page NOT in `is_captcha_page()` (comments closed). Assert exactly ONE `recaptcha/api.js` tag, ONE `#wbc-recaptcha-v3-js`, ONE bootstrap, and a non-empty token in the browser. |
| D.v3-no-conflict | 2.2.0 - v3 read no-conflict mode only as `wbc_recapcha_no_conflict_v3`, but the settings migration writes `wbc_recapcha_no_conflict`, so on migrated sites the mode was permanently off. | Set ONLY `wbc_recapcha_no_conflict=yes`; assert `requires_no_conflict()` is true. Repeat with ONLY the `_v3` spelling (back-compat). With neither set, assert false. |
| D.altcha-https | 2.2.0 investigation - ALTCHA cannot solve over HTTP; its proof-of-work needs Web Crypto, which browsers expose only in a secure context. | Run the ALTCHA login journey over **https**. If the widget sticks on "Verifying..." check the scheme before filing a bug - over http this is expected, not a regression. |
| D.it-IT-labels | 2.1.0 - the Italian catalogue was shifted by one, so admin labels read as the wrong thing ("Settings" showed as "Account"). | Set the admin user's locale to `it_IT`, open the Overview and Protection tabs, and assert in the RENDERED page: Impostazioni, Risorse, Supporto, Ottieni Supporto, and "Widget di accesso AJAX" (not "AJAX Login Widget"). Check the compiled `.l10n.php`, never the `.po`. |
| D.admin-390 | 2.1.0 - the settings screen scrolled sideways by 2px on phones (`.bprc-settings-sidebar` had `width:100%` plus a 1px border and no `box-sizing`). | At 390px on the Advanced tab, assert `document.documentElement.scrollWidth === clientWidth`. |

Rule: every customer-visible fix adds a D row in the same PR. After 2 clean releases, a D row graduates into C/E.

---

## E - Provider x integration matrix

This plugin has no premium tier. Its equivalent of "extensions" is the **provider x context matrix**, and that is where its real risk lives: each of the five services has its own render path and its own `verify()`, so a journey passing on reCAPTCHA v2 says nothing about hCaptcha.

**Run every C journey once per service.** Switch with `wp option update wbc_captcha_service <id>`.

| Service | id | Test keys |
|---|---|---|
| reCAPTCHA v2 | `recaptcha-v2` | Google's public always-pass pair |
| reCAPTCHA v3 | `recaptcha-v3` | **No public test key exists** - needs a real site+secret registered for the test domain. Without one `grecaptcha.execute()` rejects and no token is produced; do not read that as a plugin bug. |
| Turnstile | `turnstile` | Cloudflare's always-pass pair (`1x00000000000000000000AA`) |
| hCaptcha | `hcaptcha` | hCaptcha's always-pass pair (`10000000-ffff-ffff-ffff-000000000001`) |
| ALTCHA | `altcha` | Any local HMAC secret. **HTTPS only** - see D.altcha-https |

> **Always-pass test keys accept ANY response string.** Google's test secret returns `success:true` for literal junk (`hostname: testkey.google.com`). So a "logged in with a junk token" result proves nothing about verification. To assert that verification actually blocks, submit with **no** captcha field at all (D.loginform-nobypass) - that path does not depend on the provider's verdict.

### E.contexts
**What to verify:** each enabled context renders its widget on the real form AND blocks submission when unsolved. Priority order, highest customer impact first: `wp_login` (wp-login.php AND `wp_login_form()` surfaces), `bp_register`, `comment`, `wp_lostpassword`, `widget_login`, `bp_group_create`, then the WooCommerce, EDD, MemberPress, Ultimate Member and form-builder contexts for whichever of those plugins is installed.

### E.render-verify-symmetry
**What to verify:** the standing invariant behind most of this plugin's bugs - **anything that suppresses the widget must also suppress the check, and any form that reaches a verifier must render.** For each context: if the widget is absent, submitting must still succeed; if the widget is present, submitting unsolved must fail. A context where the widget is absent AND submission is refused is a lockout, and is always a release blocker.

---

## F - Cross-browser, RTL, accessibility

### F.chromium
Already covered by Sections A-E.

### F.firefox-desktop and F.safari-ios
Chromium-only MCP cannot walk these. Populate `manual_required[]` with the critical flows a human must spot-check (controls that depend on browser-native behavior, iOS-specific scroll quirks, etc.).

### F.rtl
**What to verify:** on an RTL locale, primary templates render right-to-left without overflow; icons mirror where appropriate; brand glyphs stay untransformed.

### F.a11y
**What to verify:** primary interactive surfaces have visible focus rings; tab order is logical; icon-only buttons have `aria-label`; composers, voting controls, moderation actions have screen-reader-critical labels.

---

## G - Post-release monitoring (first 24h after tag)

Runs on the production host. Watch for new debug.log entries, orphaned cron events, support tickets reporting breakage, and activity-signal drops.

---

## Failure protocol

1. Screenshot on every failure: `browser_take_screenshot({ filename: "fail-<id>.png" })`.
2. **Triage: from vs for our plugin.**
   - `from` = our code is at fault.
   - `for` = failure surfaces while our plugin runs but root cause is elsewhere (theme / other plugin / browser limit / legacy data / hosting).
3. Record in `failures[]` with `{ id, origin, triage_note, expected, actual, url, screenshot }`.
4. Never halt. Collect all failures in one pass.
5. Emit a Basecamp draft per failure with the origin line populated.

Triage is Sonnet's job; fix-or-document is the calling session's job.

## Step ID format

`<Section>.<persona>.<feature>` e.g. `C.member.post-create`. D rows: `D.<descriptor>`. E rows: `E.<extension>`.
