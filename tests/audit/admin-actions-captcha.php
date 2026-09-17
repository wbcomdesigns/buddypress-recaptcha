<?php
/**
 * Regression guard: who is exempt from a CAPTCHA, and who is not.
 *
 * 1. wp-admin actions that reuse a front-end hook must not be blocked by a
 *    CAPTCHA the admin screen never renders:
 *    - Users > Send password reset / Edit User > Send Reset Link call
 *      retrieve_password(), which fires `lostpassword_post`. Until 2.2.1 they
 *      were rejected ("Password reset links sent to 0 users").
 *    - Comments > Reply (and the Dashboard Activity widget) run core's
 *      `replyto-comment` AJAX handler, which fires `preprocess_comment`. Until
 *      2.2.1 they were rejected ("Security verification failed").
 * 2. The comment CAPTCHA for logged-in users (2.2.1):
 *    - users who can moderate comments never get it;
 *    - other members get it unless "Comments: Skip for Logged-in Users" is on;
 *    - logged-out visitors always get it.
 *    Rendering and verifying must agree for every one of those users - a widget
 *    that is not checked, or a check for a widget that was never shown, is a bug.
 *
 * Every exemption is paired with a control that must still be rejected, so an
 * over-wide bypass fails this script as surely as the original bug does.
 *
 * Provider, keys and toggles are forced through `pre_option_*` filters for this
 * process only - nothing is written to the database. Turnstile rejects an empty
 * token before any HTTP call, so the run needs no network.
 *
 * RUN:   wp eval-file wp-content/plugins/buddypress-recaptcha/tests/audit/admin-actions-captcha.php
 * EXIT:  non-zero if any case fails.
 *
 * @package Recaptcha_For_BuddyPress
 */

namespace WBC_Captcha\Tests\Audit;

if ( ! defined( 'ABSPATH' ) ) {
	exit( 1 );
}

if ( ! function_exists( 'wbc_captcha_service_manager' ) ) {
	echo "FAILED - Wbcom CAPTCHA Manager is not active.\n";
	exit( 1 );
}

// Every option below is read through this array for this process only, so cases
// can flip a value (provider, logged-in skip) without touching the database.
$forced_options = array(
	'wbc_captcha_service'                      => 'turnstile',
	'wbc_turnstile_site_key'                   => '1x00000000000000000000AA',
	'wbc_turnstile_secret_key'                 => '1x0000000000000000000000000000000AA',
	'wbc_recaptcha_v2_site_key'                => 'test-site-key',
	'wbc_recaptcha_v2_secret_key'              => 'test-secret-key',
	'wbc_recaptcha_v3_site_key'                => 'test-site-key',
	'wbc_recaptcha_v3_secret_key'              => 'test-secret-key',
	'wbc_hcaptcha_site_key'                    => 'test-site-key',
	'wbc_hcaptcha_secret_key'                  => 'test-secret-key',
	'wbc_altcha_hmac_key'                      => 'test-hmac-key',
	'wbc_recaptcha_enable_on_wplostpassword'   => 'yes',
	'wbc_recaptcha_enable_on_comment'          => 'yes',
	'wbc_recaptcha_skip_comment_for_logged_in' => 'no',
	'wbc_recaptcha_ip_to_skip_captcha'         => '',
);
foreach ( array_keys( $forced_options ) as $name ) {
	add_filter(
		"pre_option_{$name}",
		static function () use ( &$forced_options, $name ) {
			return $forced_options[ $name ];
		}
	);
}

// The manager resolved its active provider at boot, before the filters above.
$manager = wbc_captcha_service_manager();
$init    = new \ReflectionMethod( $manager, 'init_active_service' );
$init->setAccessible( true );
$use_provider = static function ( $service_id ) use ( &$forced_options, $manager, $init ) {
	$forced_options['wbc_captcha_service'] = $service_id;
	$init->invoke( $manager );
};
$use_provider( 'turnstile' );

// Never send mail; retrieve_password() succeeds once wp_mail() reports success.
add_filter( 'pre_wp_mail', '__return_true' );

// wp_die() would end the run; turn it into an exception the comment cases can catch.
add_filter(
	'wp_die_handler',
	static fn() => static function ( $message ) {
		throw new \RuntimeException( is_string( $message ) ? $message : 'wp_die' ); // phpcs:ignore WordPress.Security.EscapeOutput.ExceptionNotEscaped -- caught below, never rendered.
	},
	PHP_INT_MAX
);

$first_user = static function ( $role ) {
	$users = get_users(
		array(
			'role'   => $role,
			'number' => 1,
		)
	);
	return $users ? $users[0] : null;
};
$admin      = $first_user( 'administrator' );
$editor     = $first_user( 'editor' );
$subscriber = $first_user( 'subscriber' );
if ( ! $admin || ! $editor || ! $subscriber ) {
	echo "FAILED - needs one administrator, one editor and one subscriber.\n";
	exit( 1 );
}

$probe_comment = array(
	'comment_post_ID'      => 0,
	'comment_content'      => 'admin-actions-captcha probe',
	'comment_author'       => 'probe',
	'comment_author_email' => 'probe@example.com',
	'comment_type'         => 'comment',
);

/** Run preprocess_comment and report whether the plugin let it through. */
$comment_passes = static function () use ( $probe_comment ) {
	try {
		apply_filters( 'preprocess_comment', $probe_comment );
		return true;
	} catch ( \RuntimeException $e ) {
		return false;
	}
};

/** Whether the comment form renders the CAPTCHA for the current user. */
$form_renders = static function () {
	ob_start();
	$fields = ( new \Woocommerce_Order() )->woo_comment_form_captcha_field( array() );
	ob_end_clean();
	return ! empty( $fields['captcha'] );
};

$cases = array();

/**
 * Record a comment case: the form must render the CAPTCHA exactly when the
 * validator demands it, and the validator must match the expectation.
 */
$comment_case = static function ( $label, $expect_exempt ) use ( &$cases, $comment_passes, $form_renders ) {
	$passes  = $comment_passes();
	$renders = $form_renders();
	$cases[] = array( $label, $expect_exempt === $passes && $passes !== $renders );
};

// Admin-initiated password reset.
wp_set_current_user( $admin->ID );
$result  = retrieve_password( $subscriber->user_login );
$cases[] = array( 'admin password reset from wp-admin is sent', true === $result );

wp_set_current_user( 0 );
$result  = retrieve_password( $subscriber->user_login );
$cases[] = array( 'CONTROL anonymous lost-password without a token is rejected', is_wp_error( $result ) && in_array( 'captcha_error', $result->get_error_codes(), true ) );

// Comment reply through wp-admin. Run as a user who cannot moderate, with the
// logged-in skip OFF, so only the replyto-comment exemption can let it through.
wp_set_current_user( $subscriber->ID );
$reply_passed = null;
add_action(
	'wp_ajax_replyto-comment', // phpcs:ignore WordPress.NamingConventions.ValidHookName.UseUnderscores -- core's hook name.
	static function () use ( &$reply_passed, $comment_passes ) {
		$reply_passed = $comment_passes();
	}
);
do_action( 'wp_ajax_replyto-comment' ); // phpcs:ignore WordPress.NamingConventions.ValidHookName.UseUnderscores -- core's hook name.
$cases[] = array( 'reply through wp-admin replyto-comment is accepted', true === $reply_passed );

// Logged-in comment rules, setting OFF (the state every existing site is in).
$forced_options['wbc_recaptcha_skip_comment_for_logged_in'] = 'no';
wp_set_current_user( $admin->ID );
$comment_case( 'skip OFF: administrator comments without a CAPTCHA', true );
wp_set_current_user( $editor->ID );
$comment_case( 'skip OFF: editor comments without a CAPTCHA', true );
wp_set_current_user( $subscriber->ID );
$comment_case( 'CONTROL skip OFF: subscriber gets the CAPTCHA and is rejected without a token', false );
wp_set_current_user( 0 );
$comment_case( 'CONTROL skip OFF: anonymous gets the CAPTCHA and is rejected without a token', false );

// Setting ON (the default for new installs).
$forced_options['wbc_recaptcha_skip_comment_for_logged_in'] = 'yes';
wp_set_current_user( $subscriber->ID );
$comment_case( 'skip ON: subscriber comments without a CAPTCHA', true );
wp_set_current_user( 0 );
$comment_case( 'CONTROL skip ON: anonymous still gets the CAPTCHA and is rejected without a token', false );

// The render and verify filters must agree on every provider: a callback that
// skips the check also hides the widget. Until 2.2.1 the render filter was only
// applied by reCAPTCHA v3, so the other four showed a widget nobody checked.
$skip_comment = static function ( $should, $context ) {
	return 'comment' === $context ? false : $should;
};
wp_set_current_user( 0 );
foreach ( array( 'recaptcha-v2', 'recaptcha-v3', 'hcaptcha', 'turnstile', 'altcha' ) as $service_id ) {
	$use_provider( $service_id );
	$comment_case( "CONTROL {$service_id}: anonymous gets the CAPTCHA and is rejected without a token", false );
	add_filter( 'wbc_should_render_captcha', $skip_comment, 10, 2 );
	add_filter( 'wbc_should_verify_captcha', $skip_comment, 10, 2 );
	$comment_case( "{$service_id}: skip filters hide the widget and skip the check together", true );
	remove_filter( 'wbc_should_render_captcha', $skip_comment, 10 );
	remove_filter( 'wbc_should_verify_captcha', $skip_comment, 10 );
}

// phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped -- CLI report, not HTML.
$failed = 0;
foreach ( $cases as $case ) {
	list( $label, $ok ) = $case;
	echo ( $ok ? '  PASS  ' : '  FAIL  ' ) . $label . "\n";
	$failed += $ok ? 0 : 1;
}

echo "====================================\n";
echo 0 === $failed ? "OK - exemptions hold and every control is still rejected.\n" : "FAILED - {$failed} case(s) above.\n";
exit( $failed > 0 ? 1 : 0 );
