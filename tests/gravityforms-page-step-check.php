<?php
// Self-check: php tests/gravityforms-page-step-check.php
// Stubs GF + WP to prove forged source/target posts never skip verification on a final submit.
$GLOBALS['verified'] = 0;
$GLOBALS['gf_target'] = 0; // what GF itself computes for this POST.
function get_option( $k ) { return 'yes'; }
function absint( $v ) { return abs( (int) $v ); }
function wp_unslash( $v ) { return $v; }
function sanitize_text_field( $v ) { return $v; }
function wp_parse_str( $s, &$o ) { parse_str( $s, $o ); }
function wbc_verify_captcha() { ++$GLOBALS['verified']; return false; }
function wbc_get_captcha_error_message() { return 'blocked'; }
class GFFormDisplay { public static function get_target_page( $form, $source, $fv ) { return $GLOBALS['gf_target']; } }
require dirname( __DIR__ ) . '/public/gravityforms-classes/GravityForms_Form.php';

$form = array( 'id' => 7, 'fields' => array( array( 'type' => 'text' ), array( 'type' => 'page' ), array( 'type' => 'text' ) ) );
$run  = function ( $source, $target_post, $gf_target ) use ( $form ) {
	$_POST = array( 'gform_source_page_number_7' => $source, 'gform_target_page_number_7' => $target_post );
	$GLOBALS['gf_target'] = $gf_target;
	$GLOBALS['verified']  = 0;
	$r = ( new GravityForms_Form() )->validate_gravityforms_captcha( array( 'is_valid' => true, 'form' => $form ) );
	return array( $GLOBALS['verified'], $r['is_valid'] );
};
$fail = 0;
$check = function ( $label, $got, $want ) use ( &$fail ) { if ( $got !== $want ) { echo "FAIL $label\n"; ++$fail; } else { echo "ok   $label\n"; } };
// Final submit (GF says target 0): always verified, whatever is posted.
foreach ( array( array( 1, 0 ), array( 2, 0 ), array( 99, 0 ), array( 1, 5 ), array( 0, 0 ), array( 2, 9 ) ) as $c ) {
	$check( "final submit source={$c[0]} posted target={$c[1]}", $run( $c[0], $c[1], 0 ), array( 1, false ) );
}
// Genuine page step (GF says target 2): skipped.
$check( 'page step skipped', $run( 1, 2, 2 ), array( 0, true ) );
// GF method missing: handled by class check below.
$GLOBALS['gf_target'] = 0;
$check( 'is_page_step false when GF target 0', GravityForms_Form::is_page_step( $form ), false );
exit( $fail ? 1 : 0 );
