# Overview

reCaptcha for BuddyPress is a single CAPTCHA manager for WordPress. You pick one provider once, add its keys, and then switch protection on for each form with a toggle. There is no per-form key juggling and no second CAPTCHA plugin to install.

In the WordPress admin the plugin appears under **WB Plugins > CAPTCHA** and is labelled **Wbcom CAPTCHA Manager**.

## What it does

- Supports five CAPTCHA providers, with one active site-wide at a time:
  - Google reCAPTCHA v2 (checkbox)
  - Google reCAPTCHA v3 (invisible, score based)
  - Cloudflare Turnstile
  - hCaptcha
  - ALTCHA (self-hosted proof of work, no third-party account)
- Protects 14 integrations across WordPress core, WooCommerce, BuddyPress, bbPress, seven form builders, and three e-commerce or membership plugins.
- Shows an integration's settings only when that plugin is active, so the settings screen stays short on a simple site and grows with a complex one.
- Ships an AJAX login widget and a Gutenberg login block, both CAPTCHA protected.

## How protection works

- A form renders the active provider's widget only when three conditions are met: the provider's keys are saved, the toggle for that form is on, and the request is not from a whitelisted IP.
- On submit, the plugin verifies the response with the provider before the form is allowed through.
- Four of the five providers need free API keys from the provider. ALTCHA does not: it generates its own HMAC key and calls no external service.

## Who it is for

- Sites fighting comment, registration, or checkout spam.
- Communities that need clean signups on BuddyPress, bbPress, or membership plugins.
- Sites running several form plugins that would otherwise need a CAPTCHA plugin each.
- Privacy-conscious sites that want CAPTCHA with no third-party service, using ALTCHA.

For what you need before installing, see [Requirements](requirements.md). To install and reach the settings screen, see [Installation](installation.md).
