# CAPTCHA Wiring Contract

> Locked 2026-06-29 after a portfolio-wide "add keys -> it should just work" audit
> found several contexts where a toggle saved correctly but the CAPTCHA never
> rendered and/or never verified. This document is the contract every context
> must satisfy. Treat a violation as a release blocker.

## The promise

A site owner adds their provider keys, toggles ON the forms they want protected,
and the CAPTCHA then **renders** on those forms **and blocks submission** when it
is not solved - with no extra steps and **regardless of which other plugins
(WooCommerce, bbPress, etc.) are installed**.

## The three-part contract per context

For every context listed in `WBC_Captcha_Service_Base::get_context_option_map()`,
all three must be true and must use the **same context string**:

1. **Enable flag** - admin saves and runtime reads the *same* `wbc_recaptcha_enable_on_*`
   option key. (Verified clean for all contexts as of 2.1.0.)
2. **Render** - a hook emits `wbc_captcha_service_manager()->render( '<context>' )`
   on the target form. The render emits the `<context>-nonce` hidden field.
3. **Verify** - a submit hook calls `wbc_verify_captcha( '<context>' )` with the
   **identical** context string and blocks on `false`.

### Rules

- **Render context === verify context.** A render of `comment` verified as
  `comment_form` is a silent no-op (`is_enabled_for_context()` returns false for an
  unmapped context, and `verify()` then returns `true`). This was the comment bug.
- **Core/BP/comment contexts must NOT be gated behind unrelated integrations.**
  WP login/register/lostpassword and WordPress comments are core forms. Their
  render+verify hooks belong in `define_public_hooks()`, never inside
  `register_woocommerce_hooks()` (which early-returns when WooCommerce is absent).
  Only genuinely integration-specific contexts (woo_*, edd_*, cf7, etc.) may sit
  behind their own `class_exists`/`function_exists` guard.
- **Register each verify hook exactly once.** CAPTCHA tokens are single-use; a
  second `verify()` on the same submission re-checks the spent token and rejects a
  legitimate user. When one WP hook (e.g. `lostpassword_post`) serves two forms
  (WP core + WooCommerce), use ONE callback that selects the context by which
  `<context>-nonce` field is present in `$_POST`.
- **Aborting must use a hook whose result is honored.** `do_action` hooks like
  `groups_group_before_save` ignore return values and error bags; to block, call
  `bp_core_add_message()` + `bp_core_redirect()` (scoped to the exact creation
  step that renders the CAPTCHA) so later wizard steps / admin edits are not
  falsely blocked.
- **No fatals at load.** Internal classes must be uniquely prefixed (`WBC_*`);
  admin-only functions (`is_plugin_active()`) must be `function_exists`-guarded
  before use on the front end.

## Status snapshot (2.1.0, post-fix)

| Context | Enable flag | Render | Verify | Independent of Woo |
|---|---|---|---|---|
| wp_login | ok | ok | ok (`wp_authenticate_user`) | yes |
| wp_register | ok | ok | ok (`registration_errors`) | yes |
| wp_lostpassword | ok | ok | ok (`lostpassword_post`, context-by-nonce) | yes (fixed) |
| comment | ok | ok (fixed) | ok (`preprocess_comment`, fixed) | yes (fixed) |
| bp_register | ok | ok | ok | yes |
| bp_group_create | ok | ok | ok (abort via message+redirect, fixed) | n/a (BP) |
| bbpress_topic/reply | ok | ok | ok | n/a (bbPress-gated) |
| woo_* | ok | ok | ok | Woo-gated (correct) |
| edd_*, cf7, wpforms, gravityforms, ninjaforms, forminator, elementorpro, divi, memberpress_*, um_* | ok | ok | ok | integration-gated (correct) |

## How to re-verify

On a site WITHOUT WooCommerce, with provider keys set and the context enabled:
1. Render: load the form, confirm the widget HTML (`g-recaptcha` / `h-captcha` /
   `cf-turnstile` / `altcha-widget`) and the `<context>-nonce` field are present.
2. Verify: submit without solving -> must be blocked with the configured error.
3. Confirm exactly one verify callback is attached to the submit hook.
