=== Wbcom CAPTCHA Manager ===
Contributors: wbcomdesigns, vapvarun
Donate link: https://wbcomdesigns.com/donate/
Tags: captcha, recaptcha, spam protection, security, woocommerce
Requires at least: 6.3
Tested up to: 7.0
Stable tag: 2.1.0
Requires PHP: 7.4
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Text Domain: buddypress-recaptcha
Domain Path: /languages

Protect WordPress, WooCommerce, BuddyPress, bbPress and popular form builders with reCAPTCHA, Turnstile, hCaptcha or ALTCHA.

== Description ==

**Wbcom CAPTCHA Manager** puts every CAPTCHA on your site behind one settings screen. Pick a provider once (reCAPTCHA v2, reCAPTCHA v3, Cloudflare Turnstile, hCaptcha, or ALTCHA), then switch protection on per form with a toggle. There is no per-form key juggling and no second CAPTCHA plugin to install.

Protection covers 14 integrations: WordPress core forms and comments, WooCommerce, BuddyPress, bbPress, seven form builders, and three e-commerce or membership plugins. Settings for an integration only appear when that plugin is actually active, so the screen stays short on a simple site and grows with a complex one.

Four of the five providers need free API keys from the provider. ALTCHA does not: it is self-hosted, generates its own key when you select it, and calls no third-party service.

= What you get =

**Five CAPTCHA providers, one active at a time**
* reCAPTCHA v2 (checkbox) and reCAPTCHA v3 (invisible, score-based).
* Cloudflare Turnstile.
* hCaptcha.
* ALTCHA: self-hosted proof-of-work, no third-party account and no API keys. The HMAC key is generated for you.
* Switch providers from one dropdown; every protected form follows.

**Protects 14 integrations**
* WordPress core: login, registration, lost password, and comments.
* WooCommerce: customer login, registration, lost password, product reviews, guest checkout, login at checkout, order tracking, and pay for order.
* BuddyPress: member registration and group creation.
* bbPress: new topics and replies.
* Form builders: Contact Form 7, WPForms, Gravity Forms, Ninja Forms, Forminator, Elementor Pro Forms, and Divi Builder contact forms.
* E-commerce and membership: Easy Digital Downloads (checkout, login, registration), MemberPress (login, registration), and Ultimate Member (login, registration, password reset).
* An AJAX login widget and a Gutenberg login block, both CAPTCHA protected.

**Security controls beyond on and off**
* Fail-closed mode: if the provider's API is unreachable, block the submission instead of letting it through. Off by default, so a provider outage does not lock out your users until you choose otherwise.
* IP whitelist: skip the CAPTCHA for trusted IPs. Accepts single addresses, ranges, and CIDR notation.
* reCAPTCHA v3 score threshold: set how strict the invisible score check is.
* No-conflict mode: dequeue CAPTCHA scripts from other plugins and themes that fight with yours on the same page.

**Setup and appearance**
* Theme (light or dark) and size for reCAPTCHA v2 and hCaptcha.
* A language selector for reCAPTCHA v2 and hCaptcha, so the widget matches your site's language.
* The AJAX login widget follows BuddyX and Reign dark mode.

**Developer-friendly**
* Register your own provider with the `wbc_register_captcha_services` action: implement the service interface and it appears in the picker alongside the built-in five.
* Filters for render and verify decisions (`wbc_should_render_captcha`, `wbc_should_verify_captcha`, `wbc_captcha_verified`, `wbc_captcha_fail_closed`).
* Translation ready with an included POT file, plus a WPML config.

= Perfect For =

* WordPress sites drowning in comment or registration spam
* WooCommerce stores hit by fake accounts, carding, or order-tracking probes
* BuddyPress and BuddyBoss communities that need clean signups
* Any site running several form plugins that would otherwise need a CAPTCHA plugin each
* Privacy-conscious sites that want CAPTCHA without a third-party service (use ALTCHA)

= Premium Support =

Our support team can help with setup, provider keys, and troubleshooting a form that is not behaving.

= Documentation =

* **[Documentation and Support](https://docs.wbcomdesigns.com/)** - Setup walkthrough, settings reference, and provider key guides

= Translations =

* English (default)
* Ready for translation in your language with included POT file
* WPML configuration included

= Links =

* [Plugin Homepage](https://wbcomdesigns.com/downloads/buddypress-recaptcha/)
* [Documentation](https://docs.wbcomdesigns.com/)
* [Support](https://wbcomdesigns.com/support/)
* [Request Features](https://wbcomdesigns.com/contact/)

= Compatibility =

* WordPress 6.3 and higher
* PHP 7.4 and higher (8.0+ recommended)
* WooCommerce, BuddyPress, BuddyBoss Platform and bbPress are all optional - protection for each appears only when it is active
* Works with the block editor and classic editor
* Tested with popular themes including BuddyX and Reign

= What's New in 2.1.0 =

Version 2.1.0 closes several gaps where a CAPTCHA looked active but was not verifying. Comment forms, lost-password submissions and the login form are now protected on every site, not only on sites running WooCommerce. BuddyPress group creation is properly blocked without a completed CAPTCHA, and the login widget resets after a failed attempt. The admin gets a modern card-based layout with a Discover tab, and reCAPTCHA and Turnstile scripts now load deferred for better Core Web Vitals.

== More Free Tools from Wbcom Designs ==

Keeping spam and bots out is the first job on a community site, and the tools below are what your real members get to use once the noise is gone. All of them are free from Wbcom Designs, covering the theme and social network itself through forums, media, events, gamification, directories, jobs, and courses.

* **[BuddyX](https://wbcomdesigns.com/downloads/buddyx-theme/)** - A free, fast community theme for BuddyPress, BuddyBoss and PeepSo with a modern layout and dark mode.
* **[BuddyNext](https://wbcomdesigns.com/downloads/buddynext/)** - Stand up a complete WordPress community with activity streams, member spaces, profiles, direct messaging, and built-in moderation.
* **[Jetonomy](https://wbcomdesigns.com/downloads/jetonomy/)** - Add forums, question-and-answer boards, and idea spaces that stay tidy through trust-based auto-moderation even past 100,000 topics.
* **[Mediaverse](https://wbcomdesigns.com/downloads/mediaverse/)** - Let members build photo and video albums, react, follow each other, and message privately while AI moderation keeps things clean.
* **[Eventonomy](https://wbcomdesigns.com/downloads/eventonomy/)** - Run community events with RSVPs, calendars, and front-end submissions.
* **[WB Gamification](https://wbcomdesigns.com/downloads/wordpress-gamification-plugin/)** - Reward members with points, badges, and leaderboards to keep engagement high.
* **[Listora](https://wbcomdesigns.com/downloads/listora/)** - Publish searchable directories across ten listing types with reviews, maps, and member-submitted entries from the front end.
* **[WP Career Board](https://wbcomdesigns.com/downloads/wp-career-board/)** - Add a job board with front-end listings, applications, and employer profiles.
* **[Learnomy](https://wbcomdesigns.com/downloads/learnomy/)** - Build and sell online courses, auto-grade quizzes, collect payments, and award certificates when learners finish.

== Installation ==

= Automatic Installation (Recommended) =

1. Log in to your WordPress admin dashboard
2. Navigate to Plugins > Add New
3. Search for "Wbcom CAPTCHA Manager"
4. Click "Install Now", then "Activate"
5. Go to WB Plugins > CAPTCHA Manager and open the Quick Setup tab
6. Choose your CAPTCHA service and add its API keys (see below) - the plugin protects nothing until keys are saved, unless you choose ALTCHA
7. Open the Protection tab and toggle CAPTCHA on for each form you want covered

= Manual Installation =

1. Upload the entire `buddypress-recaptcha` folder to the /wp-content/plugins/ directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Continue from step 5 above

= Add Your API Keys (Required) =

Four of the five services need a free key pair from the provider. Add both the site key and the secret key on the plugin's settings screen, then save. **Until you do, no CAPTCHA renders and no form is protected.**

* reCAPTCHA v2 and reCAPTCHA v3 - get keys at https://www.google.com/recaptcha/admin (v2 and v3 keys are not interchangeable, so create the type you selected)
* Cloudflare Turnstile - get keys at https://dash.cloudflare.com/
* hCaptcha - get keys at https://dashboard.hcaptcha.com/

ALTCHA needs no keys and no account. It is self-hosted and generates its own HMAC key when you select it, so it works as soon as you save.

= Post-Installation Setup =

1. Open the Protection tab and enable CAPTCHA only on the forms that get spam. Every extra widget costs page weight.
2. Submit each protected form once as a logged-out visitor to confirm the CAPTCHA renders and blocks an empty submission.
3. If you use reCAPTCHA v3, tune the score threshold on the Advanced tab. Start at the default and lower it only if real users get rejected.
4. If another plugin or theme also loads a CAPTCHA script, enable no-conflict mode on the Advanced tab.
5. See the [documentation](https://docs.wbcomdesigns.com/) for detailed instructions.

= Requirements =

* WordPress 6.3 or higher
* PHP 7.4 or higher
* API keys from your chosen provider (not required for ALTCHA)

== Frequently Asked Questions ==

= Do I need BuddyPress to use this plugin? =

No. BuddyPress, BuddyBoss Platform, WooCommerce and bbPress are all optional. On a plain WordPress site the plugin still protects login, registration, lost password and comments. Settings for an integration appear only when that plugin is active.

= Which CAPTCHA service should I choose? =

reCAPTCHA v2 is the familiar checkbox and the safest default. reCAPTCHA v3 is invisible and scores traffic instead of challenging it, which is better for conversions but needs threshold tuning. Turnstile and hCaptcha are privacy-friendlier alternatives to Google. ALTCHA is the choice if you want no third-party service at all.

= Can I use more than one service at a time? =

No. One service is active site-wide, on every protected form. Switching services is a single dropdown change and does not reset your per-form toggles.

= Does ALTCHA really need no API keys? =

Correct. ALTCHA is self-hosted proof-of-work. It generates its own HMAC key when you select it and never contacts an external server. The other four services each need a free key pair from the provider.

= What happens if the CAPTCHA provider goes down? =

By default the plugin fails open: if the provider's API cannot be reached, the submission is allowed through, so an outage at Google or Cloudflare cannot lock your users out. If you would rather block submissions than risk letting spam through, enable fail-closed mode on the Advanced tab.

= Can I skip the CAPTCHA for my own IP? =

Yes. Add trusted addresses to the IP whitelist on the Advanced tab. It accepts single IPs, ranges, and CIDR notation.

= Real users are being blocked by reCAPTCHA v3. What do I do? =

Lower the score threshold on the Advanced tab. reCAPTCHA v3 scores each request from 0.0 (likely bot) to 1.0 (likely human), and anything below your threshold is rejected. If legitimate users fail, your threshold is too strict.

= The CAPTCHA is not appearing on my form. Why? =

Check three things in order: your API keys are saved, the toggle for that specific form is on in the Protection tab, and you are viewing the form logged out. Also confirm the integration's plugin is active - the Contact Form 7 toggle only exists when Contact Form 7 is.

= Can I add support for a CAPTCHA service you do not ship? =

Yes. Hook the `wbc_register_captcha_services` action and register a class implementing the service interface. It then appears in the provider picker next to the built-in five.

= Is the plugin translation ready? =

Yes. A POT file is included, along with a WPML configuration file.

= Where can I get support? =

* [Documentation](https://docs.wbcomdesigns.com/) - Free guides for every Wbcom plugin
* [Support](https://wbcomdesigns.com/support/) - Get help from our team
* [Contact us](https://wbcomdesigns.com/contact/) - Report bugs and request features

== Screenshots ==

1. Quick Setup - pick a CAPTCHA service (reCAPTCHA v2/v3, Turnstile, hCaptcha, or ALTCHA) and add your API keys.
2. Protection - toggle CAPTCHA on individual WordPress and BuddyPress forms.
3. Discover more free Wbcom Designs community plugins from the admin screen.

== Upgrade Notice ==

= 2.1.0 =
Security update: comment, lost-password and login forms are now verified on sites without WooCommerce, where they previously did nothing. Recommended for every site.

== Changelog ==

= 2.1.0 - June 2026 =

* New      - Added a Discover tab to the admin with curated free Wbcom Designs tools.
* Improve  - Refreshed the settings screen with a modern card-based layout.
* Improve  - reCAPTCHA and Turnstile scripts now load deferred on all browsers, improving page load speed and Core Web Vitals.
* Improve  - More reliable ALTCHA spam detection.
* Fix      - The CAPTCHA language setting now applies correctly for hCaptcha and reCAPTCHA v2.
* Fix      - Corrected the language selector display on the Advanced settings tab.
* Fix      - The reCAPTCHA v3 score threshold set in the admin now takes effect.
* Fix      - Removed unnecessary debug entries from the server error log.
* Fix      - Prevented a rare fatal error when another plugin or theme uses the same generic class names.
* Fix      - Prevented a fatal error on the front end when ALTCHA is the active provider.
* Security - Comment forms are now protected on every site, not only sites running WooCommerce.
* Security - Lost-password submissions are now verified on every site, not only sites running WooCommerce.
* Security - BuddyPress group creation is now blocked when the CAPTCHA is not completed.
* Security - The login widget now resets its CAPTCHA after a failed attempt, closing a token-reuse window.
* Security - The login form can no longer be submitted without completing the CAPTCHA on sites that do not use WooCommerce.
* Security - The setup wizard is now restricted to administrators.
* Dev      - Corrected the minimum supported WordPress version metadata.
* Compat   - Tested with WordPress 7.0.

= 2.0.2 =
* Fixed: WordPress Coding Standards compliance across all PHP files
* Fixed: Plugin Check compatibility improvements
* Fixed: Removed deprecated load_plugin_textdomain calls
* Fixed: Added direct file access protection to all files
* Fixed: Replaced external Font Awesome CDN with WordPress built-in dashicons
* Tested up to: WordPress 6.9

= 2.0.1 =
* Fixed: Fatal error caused by undefined WooCommerce hook callbacks (woo_verify_wp_register_captcha, woo_verify_wp_lostpassword_captcha, woo_remove_no_conflict)
* Fixed: PHP 8+ TypeError in settings migration when captcha version option is not set (strtolower on non-string)
* Tested up to: WordPress 6.7.2

= 2.0.0 =
* Major Release: Complete plugin revamp with extensive new integrations
* Added: Contact Form 7 integration with CAPTCHA protection
* Added: WPForms integration for drag & drop form builder
* Added: Gravity Forms integration for advanced form solution
* Added: Ninja Forms integration for flexible form creator
* Added: Forminator integration for versatile form builder
* Added: Elementor Pro Forms integration with native support
* Added: Divi Builder Contact Forms integration
* Added: Easy Digital Downloads integration for checkout and registration protection
* Added: MemberPress integration for membership signups and login
* Added: Ultimate Member integration for member registration and login
* Added: BuddyPress group creation form protection
* Added: AJAX Login Widget for secure login anywhere on your site
* Added: Gutenberg Login Block with CAPTCHA protection
* Added: ALTCHA service support - self-hosted CAPTCHA with no external dependencies
* Added: Plugin update system for automatic update notifications
* Improved: Complete UI/UX overhaul with modern, intuitive admin interface
* Improved: Quick Setup Wizard for 3-step configuration
* Improved: Unified Protection Tab with all form settings in one location
* Improved: Advanced Settings with consolidated appearance and security options
* Improved: Navigation with tab icons and reduced from 7+ tabs to 4 streamlined tabs
* Improved: Modular settings architecture - settings appear only when relevant plugins are active
* Improved: Code organization with 50% codebase reduction
* Enhanced: Security with strengthened nonce verification across all forms
* Enhanced: Input validation and sanitization throughout plugin
* Enhanced: CAPTCHA loading and validation performance
* Enhanced: IP whitelisting functionality for trusted IPs
* Fixed: Compatibility issues with latest WordPress, WooCommerce, and BuddyPress versions
* Updated: Modern card-based design layout throughout admin interface
* Updated: Better performance with faster load times
* Updated: Comprehensive developer documentation

= 1.7.0 =
* Fixed: Manage bbPress topic's reply button not disabling before captcha validation.
* Improved: Added filters and removed escaping functions.
* Fixed: Plugin content-related issues.
* Updated: Renamed the menu "Post Comment Form" for better usability.
* Resolved: reCAPTCHA issue with WooCommerce integration.

= 1.6.3 =
* Fix: (#73) Issue with BuddyBoss Registration button
* Fix: (#71) Added admin topic and reply icons
* Fix: (#70) Fatal error

= 1.6.2 =
* Fix: Fixed Plugin redirect issue when multi plugin activate the same time

= 1.6.1 =
* Fix: (#68)Fixed lost password captcha issue

= 1.6.0 =
* Fix: (#67) Fixed single group forum reply in captcha not showing
* Fix: Fixed buddyboss admin notice issue

= 1.5.0 =
* Fix: Fixed reCaptcha V3 admin UI
* Fix: Hide admin notices and update admin theme extension & support title
* Fix: Added faq section style
* Fix: Remove unused icons code and update prefix
* Fix: Update admin backend UI
* Fix: Added support for Bp Lock, Bp private community pro plugin
* Fix: Fixed ip resctriction warning issue

= 1.4.1 =
* Fix: (#56) Fixed ip restriction not working

= 1.4.0 =
* Fix: (#52)fixed admin setting UI issue
* Fix: (#55)Fixed need to add notice in default buddypress message
* Fix: (#53)Fixed string replace is not working in error message
* Fix: (#54)Fixed bbpress reply captcha is not working

= 1.3.0 =
* Fix: (#43,#44,#45,#46)Fixed v3 recaptcha issue
* Fix: Removed install plugin button from wrapper
* Fix: phpcs fixes

= 1.2.0 =
* Fix: Add recaptcha support for buddyx-pro and reign theme
* Fix: (#39)Fixed welcome page redirection issue

= 1.1.0 =
* Fix: Fixed phpcs errors
* Fix: (#27, #28) Managed UI with post comment form
* Fix: (#26) Fixed recaptcha field title not showing in post comment form
* Fix: (#23) Managed UI with twenty-twenty theme
* Fix: (#25)Fixed changed typo error on plugin welcome page

= 1.0.0 =
* first version.
</content>
