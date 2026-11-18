# Protected Forms

> **Included in Free**

Each form has its own switch on the Protection tab.

| Group | Forms |
|---|---|
| WordPress Core Forms | Login, Registration, Lost Password, Comments |
| WooCommerce Forms | Login, Registration, Lost Password, Guest Checkout, Logged-in User Checkout, product reviews |
| BuddyPress Forms | Member Registration, group creation, Contact Me |
| bbPress Forum Protection | New Topic Creation, Topic Replies |
| Form builders | Contact Form 7, WPForms, Gravity Forms, Ninja Forms, Forminator, Elementor Pro, Divi |
| Memberships and stores | Easy Digital Downloads, MemberPress, Ultimate Member |

A group only shows when its plugin is active.

## How a visitor is checked

The widget adds a token to the form. The server verifies it once on submit. A visitor who never solved the widget sees "Please complete the security check". A rejected token shows "Security verification failed". Both messages can be edited on the Advanced tab.

## XML-RPC

While login protection is on, password logins over XML-RPC are blocked. Application passwords keep working.

## Gravity Forms multi-page forms

The CAPTCHA sits on the last page. It is checked on every real submit; page steps are not checked.
