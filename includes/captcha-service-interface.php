<?php
/**
 * Captcha Service Interface
 *
 * @package    Recaptcha_For_BuddyPress
 * @since      1.0.0
 */

/**
 * Interface for captcha services
 */
interface WBC_Captcha_Service_Interface {

	/**
	 * Get the service identifier
	 *
	 * @return string
	 */
	public function get_service_id();

	/**
	 * Get the service display name
	 *
	 * @return string
	 */
	public function get_service_name();

	/**
	 * Get the site key
	 *
	 * @return string
	 */
	public function get_site_key();

	/**
	 * Get the secret key
	 *
	 * @return string
	 */
	public function get_secret_key();

	/**
	 * Check if the service is properly configured
	 *
	 * @return bool
	 */
	public function is_configured();

	/**
	 * Get the script handle for this service
	 *
	 * @param string $context The context where the script is used.
	 * @return string
	 */
	public function get_script_handle( $context = 'default' );

	/**
	 * Get the script URL for this service
	 *
	 * @return string
	 */
	public function get_script_url();

	/**
	 * Render the captcha field
	 *
	 * @param string $context The context where captcha is rendered.
	 * @param array  $args    Additional arguments.
	 * @return void
	 */
	public function render( $context, $args = array() );

	/**
	 * Verify the captcha response
	 *
	 * @param string $response The captcha response.
	 * @param array  $args     Additional arguments.
	 * @return bool
	 */
	public function verify( $response, $args = array() );

	/**
	 * Get service-specific options
	 *
	 * @param string $option_name Option name.
	 * @param mixed  $default     Default value.
	 * @return mixed
	 */
	public function get_option( $option_name, $default = null );

	/**
	 * Enqueue necessary scripts and styles
	 *
	 * @param string $context The context where scripts are enqueued.
	 * @return void
	 */
	public function enqueue_scripts( $context );

	/**
	 * Get the verification endpoint URL
	 *
	 * @return string
	 */
	public function get_verify_endpoint();

	/**
	 * Get form field name for the response
	 *
	 * @return string
	 */
	public function get_response_field_name();

	/**
	 * Check if this service requires no-conflict mode
	 *
	 * @return bool
	 */
	public function requires_no_conflict();

	/**
	 * Get service-specific attributes for the captcha container
	 *
	 * @param string $context The context.
	 * @return array
	 */
	public function get_container_attributes( $context );

	/**
	 * Check if the service is enabled for a specific context
	 *
	 * @param string $context The context to check.
	 * @return bool
	 */
	public function is_enabled_for_context( $context );
}
