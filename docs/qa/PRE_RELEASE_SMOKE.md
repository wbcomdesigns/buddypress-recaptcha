# Wbcom CAPTCHA Manager — Pre-Release Smoke Checklist

> **Run this before every tagged release. Every row must pass.**
> Any failure → file a Basecamp card in Bugs and **halt the release**.
> Target time: 90 minutes end-to-end.

**Matrix:** 3 personas × 3 browsers × 2 viewports × 2 theme modes (where applicable).

- Personas: Anonymous visitor, Member (lowest authed role), Admin
- Browsers: Chrome Desktop, Firefox Desktop, Safari iOS (sim or real)
- Viewports: 1440px desktop, 390px mobile
- Theme modes: Light, Dark

**Environment:**
- Clean Local site with `buddypress-recaptcha` **already on the previous stable version** for the upgrade test
- A second clean Local site for the fresh-install test
- Access to `wp-content/debug.log` and DevTools Network tab
- Mailpit/Mailhog open for email rows

---

## A — Fresh install (10 min)

- [ ] Activate `buddypress-recaptcha` → no fatal, no PHP warning in `debug.log`
- [ ] Custom DB tables created (if any): `wp db tables --all-tables | grep ^wp_(none - this plugin creates no tables)`
- [ ] `wp option get rfb_plugin_version_db_version` equals the constant value (if plugin has a db version)
- [ ] Front-end route loads as the very first request after activation → HTTP 200 (regression guard against rewrite-flush 404)
- [ ] Deactivate → reactivate → no duplicate tables, no re-run migrations

## B — Upgrade from previous release (5 min)

- [ ] Drop the new zip → update via WP → no fatal
- [ ] `wp option get rfb_plugin_version_db_version` updates to new constant
- [ ] Pre-existing data still renders correctly (posts, settings, user profiles, etc.)
- [ ] No new warnings in debug.log

## C — Core user flows (25 min)

### C1 — Anonymous visitor
- [ ] Home / landing page renders, no console errors
- [ ] Click through primary navigation → all public pages render
- [ ] Auth-gated actions redirect to login (never silent 403)

### C2 — Member (lowest authed role)
- [ ] Register or log in via Wbcom CAPTCHA Manager's login surface
- [ ] Complete the primary creation flow (complete a BuddyPress registration at /register/ with the CAPTCHA solved) → item created, Network 2xx
- [ ] Complete the primary interaction flow (log in through wp-login.php, the core Login/Logout block and the plugin AJAX login widget) → state updates
- [ ] Mobile 390px: every flow still usable, no horizontal overflow

### C3 — Admin
- [ ] Navigate to plugin admin pages → all render without PHP warnings
- [ ] Settings save flow works — change a setting, save, reload, persists
- [ ] List pages (if any): filter, paginate, bulk actions
- [ ] User management / capability checks — permissions enforced


## D — Known-regression guards (15 min)

Fill this section from the plugin's fixed-bug history. Every row here is a bug that caused pain in production and must never regress.

- [ ] (placeholder) D.v3-token: v3 active, load /register/ → hidden token field is non-empty and no grecaptcha console error
- [ ] (placeholder) D.loginform-render: log in from a core Login/Logout block → CAPTCHA renders inside that form and login succeeds

**Rule:** every customer-visible fix that ships after this document adds a new row here in the same PR.

## E — Extensions / addons / premium features (if applicable)

Walk each feature per `docs/qa/UX_AUDIT.md` or a dedicated extension checklist. At minimum:

- [ ] Admin settings tab for each feature renders
- [ ] Primary REST/AJAX endpoint for each feature returns 2xx to an authenticated user with the required cap
- [ ] Feature toggle off → feature hidden from front-end; no dead buttons, no 404s

## F — Cross-browser quick pass (10 min)

Run these 5 pages on **Chrome + Firefox + Safari iOS**:

1. / — landing page
2. /wp-login.php — primary content view
3. /register/ — creation form
4. /wp-admin/admin.php?page=buddypress-recaptcha — plugin admin page
5. /wp-admin/admin.php?page=buddypress-recaptcha — plugin settings

Expectations: no JS errors, no layout breaks, interactive elements work.

## G — Post-release verification (first 24h)

- [ ] `wp-content/debug.log` clean of new warnings/notices/fatals
- [ ] `wp cron event list | grep (none - this plugin schedules no cron)` — expected events scheduled, no orphans
- [ ] Zoho Desk / Slack #support — no "broke after update" tickets in first 24h
- [ ] Analytics / activity signal continues (no "zero events" sign of breakage)

---

## Failure protocol

1. **Stop.** Do not merge the release branch.
2. File a Basecamp card in **Bugs** with the failed row verbatim, environment, browser, user persona.
3. Fix + push to the release branch.
4. Re-walk the failed row AND the section that contains it.
5. Resume only after the failure is resolved.

## Version-specific additions

Append a section below for every release with the specific regression guards added that cycle. After 2 clean releases of a row → graduate it into the main flow.
