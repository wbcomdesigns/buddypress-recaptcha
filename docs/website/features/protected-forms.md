# Protected Forms

The plugin protects 14 integrations. Each form is an independent toggle on the Protection tab, and an integration's toggles appear only when that plugin is active. The active provider is used on every enabled form.

## WordPress core

- Login (`#loginform`)
- Registration (`#registerform`)
- Lost password (`#lostpasswordform`)
- Comments (`#commentform`)

Comment and lost-password protection work on every site, including sites without WooCommerce. Admins and editors do not see the comment CAPTCHA, since they can approve any comment. A "Comments: Skip for Logged-in Users" setting controls whether logged-in members see the comment CAPTCHA; new installs start with it on.

## WooCommerce

- Customer login and registration
- Lost password
- Guest checkout and login at checkout
- Product reviews
- Order handling and order tracking

## BuddyPress

- Member registration (`#signup_form`)
- Group creation (`#create-group-form`), blocked when the CAPTCHA is not completed

Works with BuddyPress and BuddyBoss Platform.

## bbPress

- New topics (`#new-post`)
- Replies (`#new-post`)

## Form builders

- Contact Form 7
- WPForms
- Gravity Forms
- Ninja Forms
- Forminator
- Elementor Pro forms
- Divi Builder contact forms

## E-commerce and membership

- Easy Digital Downloads: checkout, login, registration
- MemberPress: login, registration
- Ultimate Member: login, registration, password reset

## Login widget and block

An AJAX login widget and a Gutenberg login block are both CAPTCHA protected. See [Login widget and block](login-widget-and-block.md).
