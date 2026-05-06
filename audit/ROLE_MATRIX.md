# Wbcom CAPTCHA Manager — Role / Capability Matrix

**Generated:** 2026-05-06
**Source:** [`audit/manifest.json`](manifest.json) → `capabilities[]`

The plugin defines **no custom capabilities**. All admin gates use the WordPress core `manage_options` capability (administrator-only by default).

## Admin pages

| Page | `manage_options` (Admin) | Editor | Author | Subscriber | Anonymous |
|---|---|---|---|---|---|
| WB Plugins (top-level menu) | R | – | – | – | – |
| Wbcom CAPTCHA Manager (settings) | CRUD | – | – | – | – |
| Wbcom Plugins (catalog) | R | – | – | – | – |
| Wbcom Support | R | – | – | – | – |
| Wbcom License | R | – | – | – | – |
| ALTCHA Options | CRUD | – | – | – | – |

## Form interactions (all visitors / users equally)

| Action | Admin | Editor | Author | Subscriber | Anonymous |
|---|---|---|---|---|---|
| See CAPTCHA on `wp_login` | ✅ | ✅ | ✅ | ✅ | ✅ |
| See CAPTCHA on `wp_register` | – | – | – | – | ✅ |
| See CAPTCHA on Woo checkout | ✅ | ✅ | ✅ | ✅ | ✅ |
| See CAPTCHA on BP `signup` | – | – | – | – | ✅ |
| See CAPTCHA on `comment` form | ✅ | ✅ | ✅ | ✅ | ✅ |
| Submit AJAX login widget | ✅ | ✅ | ✅ | ✅ | ✅ |
| Skip CAPTCHA via IP whitelist | ✅ | ✅ | ✅ | ✅ | ✅ |

CAPTCHA rendering is **role-blind** by design — bots and humans of every role get the same challenge. Only the IP whitelist (`wbc_recaptcha_ip_to_skip_captcha`) bypasses rendering, and that's per-IP, not per-role.

## Settings options

| Option group | Read | Write |
|---|---|---|
| `wbc_*` (all plugin options) | all admins via settings page | `manage_options` only |

## REST endpoint

| Endpoint | Public? | Auth |
|---|---|---|
| `GET /altcha/v1/challenge` | ✅ public | `__return_true` (any visitor) |

This is **intentional** — the ALTCHA widget needs to fetch a challenge before the user submits a form. The endpoint is rate-limited only by HMAC validity (the secret key).

## AJAX endpoints

| Action | Auth | Capability |
|---|---|---|
| `wbc_ajax_login` | priv + nopriv | (none — pre-auth login flow) |
| `wbcom_addons_cards` | priv | (none — admin context implicit) |

## Custom capabilities

_None registered._

Legend: **C** = Create, **R** = Read, **U** = Update, **D** = Delete, **–** = No access, **✅** = Allowed
