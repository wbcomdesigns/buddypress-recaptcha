<?php
/**
 * Regression guard: wp-admin actions that reuse a front-end hook must not be
 * blocked by a CAPTCHA the admin screen never renders - while the front-end
 * form that owns the hook stays protected.
 *
 * Two core admin tools fire the same hooks as a public form:
 *
 *   - Users > Send password reset / Edit User > Send Reset Link call
 *     retrieve_password(), which fires `lostpassword_post`. Until 2.2.1 the
 *     lost-password validator rejected them ("Password reset links sent to 0 users").
 *   - Comments > Reply (and the Dashboard Activity widget) run core's
 *     `replyto-comment` AJAX handler, which fires `preprocess_comment`. Until
 *     2.2.1 the comment validator rejected them ("Security verification failed").
 *
 * Each case is paired with its control: the same call as a logged-out visitor
 * with no CAPTCHA token must still be rejected, so an over-wide bypass fails
 * this script as surely as the original bug does.
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

$forced_options = array(
	'wbc_captcha_service'                      => 'turnstile',
	'wbc_turnstile_site_key'                   => '1x00000000000000000000AA',
	'wbc_turnstile_secret_key'                 => '1x0000000000000000000000000000000AA',
	'wbc_recaptcha_enable_on_wplostpassword'   => 'yes',
	'wbc_recaptcha_enable_on_comment'          => 'yes',
	'wbc_recaptcha_skip_comment_for_logged_in' => 'no',
	'wbc_recaptcha_ip_to_skip_captcha'         => '',
);
foreach ( $forced_options as $name => $value ) {
	add_filter( "pre_option_{$name}", static fn() => $value );
}

// The manager resolved its active provider at boot, before the filters above.
$manager = wbc_captcha_service_manager();
$init    = new \ReflectionMethod( $manager, 'init_active_service' );
$init->setAccessible( true );
$init->invoke( $manager );

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

$admins = get_users(
	array(
		'role'   => 'administrator',
		'number' => 1,
		'fields' => 'ID',
	)
);
$member = get_users(
	array(
		'role__not_in' => array( 'administrator' ),
		'number'       => 1,
	)
);
if ( ! $admins || ! $member ) {
	echo "FAILED - needs one administrator and one non-administrator user.\n";
	exit( 1 );
}
$admin_id     = (int) $admins[0];
$member_login = $member[0]->user_login;

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

$cases = array();

// 1. Admin sends a password reset from wp-admin.
wp_set_current_user( $admin_id );
$result  = retrieve_password( $member_login );
$cases[] = array( 'admin password reset is sent', true === $result );

// 2. Control: a logged-out lost-password submit with no token is rejected.
wp_set_current_user( 0 );
$result  = retrieve_password( $member_login );
$cases[] = array( 'anonymous lost-password without a token is rejected', is_wp_error( $result ) && in_array( 'captcha_error', $result->get_error_codes(), true ) );

// 3. Admin replies from Comments > Reply (core's replyto-comment AJAX handler).
wp_set_current_user( $admin_id );
$admin_reply_passed = null;
add_action(
	'wp_ajax_replyto-comment', // phpcs:ignore WordPress.NamingConventions.ValidHookName.UseUnderscores -- core's hook name.
	static function () use ( &$admin_reply_passed, $comment_passes ) {
		$admin_reply_passed = $comment_passes();
	}
);
do_action( 'wp_ajax_replyto-comment' ); // phpcs:ignore WordPress.NamingConventions.ValidHookName.UseUnderscores -- core's hook name.
$cases[] = array( 'admin reply from the Comments screen is accepted', true === $admin_reply_passed );

// 4. Control: a front-end comment with no token is rejected, even from an admin.
$cases[] = array( 'front-end comment without a token is rejected (admin)', false === $comment_passes() );
wp_set_current_user( 0 );
$cases[] = array( 'front-end comment without a token is rejected (anonymous)', false === $comment_passes() );

// phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped -- CLI report, not HTML.
$failed = 0;
foreach ( $cases as $case ) {
	list( $label, $ok ) = $case;
	echo ( $ok ? '  PASS  ' : '  FAIL  ' ) . $label . "\n";
	$failed += $ok ? 0 : 1;
}

echo "====================================\n";
echo 0 === $failed ? "OK - admin actions pass, front-end forms stay protected.\n" : "FAILED - {$failed} case(s) above.\n";
exit( $failed > 0 ? 1 : 0 );
