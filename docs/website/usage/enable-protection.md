# Enable Protection

The **Protection** tab is where you turn the CAPTCHA on for each form. Every form is an independent toggle.

## Steps

1. Open the **Protection** tab.
2. Turn on the toggle for each form you want to protect.
3. Save.

Only enable protection on forms that actually get spam. Every extra widget adds page weight.

## What appears

Toggles for an integration show only when that plugin is active. On a plain WordPress site you will see the WordPress core forms (login, registration, lost password, comments) and the login widget. Activating WooCommerce, BuddyPress, bbPress, a form builder, or a membership plugin adds its toggles.

## Three conditions for a widget to render

A form shows the CAPTCHA only when all three are true:

1. The active provider has valid keys saved (ALTCHA excepted).
2. The toggle for that specific form is on.
3. The request is not from a whitelisted IP.

## Verify it works

Submit each protected form once as a logged-out visitor:

- Confirm the widget renders.
- Confirm an empty or unsolved submission is blocked with the security message.

Admins and editors do not see the comment CAPTCHA. Use a logged-out session, or a private window, to test comment protection.

For threshold, IP whitelist, fail-closed, and appearance, see [Advanced settings](advanced-settings.md).
