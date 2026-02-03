=== Wbcom CAPTCHA Manager ===
Contributors: vapvarun,wbcomdesigns
Donate link: https://wbcomdesigns.com/
Tags: captcha, recaptcha, spam protection, security, woocommerce, buddypress, bbpress, fluentcart, turnstile, hcaptcha
Requires at least: 3.0.1
Tested up to: 6.7.2
Stable tag: 2.0.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

== Description ==

Complete CAPTCHA solution with support for multiple CAPTCHA services. Protect your WordPress, WooCommerce, BuddyPress, bbPress, and 10+ popular form builders from spam and bots with an easy-to-manage, modular interface.

== Key Features ==

**Multiple CAPTCHA Services:**
- reCAPTCHA v2 (Checkbox)
- reCAPTCHA v3 (Invisible)
- Cloudflare Turnstile
- hCaptcha
- ALTCHA (Self-hosted, no external dependencies)

**Comprehensive Form Protection:**
- WordPress core forms (login, registration, lost password, comments)
- WooCommerce (customer login, registration, checkout, product reviews)
- BuddyPress (member registration, group creation)
- bbPress (new topics, replies)
- 10+ popular form builders (Contact Form 7, WPForms, Gravity Forms, Ninja Forms, Forminator, Elementor Pro, Divi Builder, Fluent Forms, Formidable Forms, WS Form)
- E-commerce & Membership (Easy Digital Downloads, MemberPress, Ultimate Member)
- AJAX Login Widget & Gutenberg Login Block

== Free Add-ons to Enhance Your BuddyPress or BuddyBoss Platform ==

- **[BuddyPress Member Reviews](https://wordpress.org/plugins/bp-user-profile-reviews/)**: Allow members to add ratings or feedback for other community members.
- **[BuddyPress Group Reviews](https://wordpress.org/plugins/review-buddypress-groups/)**: Enable group ratings and reviews.
- **[BuddyPress Activity Social Share](https://wordpress.org/plugins/bp-activity-social-share/)**: Share activities on social platforms like Facebook, Twitter, and LinkedIn.
- **[Private Community with BP Lock](https://wordpress.org/plugins/lock-my-bp/)**: Make your community private, only accessible to logged-in users, while keeping selected pages public.
- **[BuddyPress Job Manager](https://wordpress.org/plugins/bp-job-manager/)**: Integrate WP Job Manager with BuddyPress.
- **[Check-ins for BuddyPress Activity](https://wordpress.org/plugins/bp-check-in/)**: Let members add their location or check-ins to their BuddyPress activities.
- **[BuddyPress Favorite Notification](https://wordpress.org/plugins/bp-favorite-notification/)**: Notify members when their activities receive likes or favorites.
- **[Shortcodes & Elementor Widgets for BuddyPress](https://wordpress.org/plugins/shortcodes-for-buddypress/)**: Use shortcodes and Elementor widgets for displaying BuddyPress activities, member directories, and groups.

== Premium Add-ons ==

- **[BuddyPress Hashtags](https://wbcomdesigns.com/downloads/buddypress-hashtags/)**: Use hashtags in BuddyPress activities and bbPress topics.
- **[BuddyPress Polls](https://wbcomdesigns.com/downloads/buddypress-polls/)**: Let members publish polls in BuddyPress or BuddyBoss activities and groups.
- **[BuddyPress Quotes](https://wbcomdesigns.com/downloads/buddypress-quotes/)**: Enable members to post updates with colorful and interactive backgrounds.
- **[BuddyPress Status & Reaction](https://wbcomdesigns.com/downloads/buddypress-status/)**: Let members set a status and offer reactions to activities.
- **[BuddyPress Profanity](https://wbcomdesigns.com/downloads/buddypress-profanity/)**: Filter and censor inappropriate content in activities and messages.
- **[BuddyPress Sticky Post](https://wbcomdesigns.com/downloads/buddypress-sticky-post/)**: Pin important activities to the top of the activity stream.
- **[BuddyPress Auto Friends](https://wbcomdesigns.com/downloads/buddypress-auto-friends/)**: Automatically assign global friends to new members.
- **[Shortcodes & Elementor Widgets for BuddyPress Pro](https://wbcomdesigns.com/downloads/shortcodes-for-buddypress-pro/)**: Use advanced shortcodes and Elementor widgets for BuddyPress content.




== Installation ==

1. Upload the entire `buddypress-recaptcha` folder to the /wp-content/plugins/ directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.

== Changelog ==

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
* Added: Fluent Forms integration for modern form builder
* Added: Formidable Forms integration for professional forms
* Added: WS Form integration for advanced form builder
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
