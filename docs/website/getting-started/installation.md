# Installation

## Automatic installation

1. Log in to your WordPress admin dashboard.
2. Go to **Plugins > Add New**.
3. Search for "Wbcom CAPTCHA Manager".
4. Click **Install Now**, then **Activate**.
5. On activation you are taken to the plugin settings. You can return any time from **WB Plugins > CAPTCHA**.

## Manual installation

1. Upload the entire `buddypress-recaptcha` folder to `/wp-content/plugins/`.
2. Activate the plugin through the **Plugins** menu in WordPress.

## First-run setup

1. Open the **Quick Setup** tab, choose a CAPTCHA provider, and add its API keys (ALTCHA needs none). The plugin protects nothing until keys are saved, unless you choose ALTCHA.
2. Open the **Protection** tab and toggle CAPTCHA on for each form you want covered.
3. Submit each protected form once as a logged-out visitor to confirm the widget renders and blocks an empty submission.

The settings screen is admin-only. The menu and every tab are gated by the `manage_options` capability.

## Automatic updates

Updates are delivered from wbcomdesigns.com and ship preactivated. There is no license key to enter and no account step. Update state only authorises update downloads and never gates CAPTCHA protection. If the bundled update SDK package is incomplete, updates turn off and an admin notice says so, while every other feature keeps working.

Next: pick and configure a provider in [Select a provider and add keys](../usage/select-provider-and-keys.md).
