<?php
/**
 * Simplified Settings Page for BuddyPress reCAPTCHA
 *
 * @package    Recaptcha_For_BuddyPress
 * @since      2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WBC_BuddyPress_Settings_Page' ) ) :

	/**
	 * Settings Page Class for BuddyPress reCAPTCHA
	 */
	class WBC_BuddyPress_Settings_Page {
	
		/**
		 * Settings page ID
		 *
		 * @var string
		 */
		public $id;

		/**
		 * Settings page label
		 *
		 * @var string
		 */
		public $label;

		/**
		 * Constructor
		 */
		public function __construct() {
			$this->id    = 'wbc_woo_recaptcha';
			$this->label = 'reCaptcha';

			include 'class-settings-renderer.php';
			
			// Initialize settings
			add_action( 'admin_init', array( $this, 'wbc_initialize_settings' ) );
		}

		/**
		 * Initialize settings
		 */
		public function wbc_initialize_settings() {
			// Initialize settings integration if needed
			if ( class_exists( 'WBC_Settings_Integration' ) ) {
				WBC_Settings_Integration::init();
			}
		}

		/**
		 * Get Quick Setup settings - clean 3-section structure
		 *
		 * @return array
		 */
		public function wbc_quick_setup_settings() {
			// Default to recaptcha-v2 if nothing is selected
			$active_service = get_option( 'wbc_captcha_service', 'recaptcha-v2' );
			$has_service = ! empty( $active_service );

			$settings = array(
				// SECTION 1: Service Selection
				array(
					'name' => esc_html__( 'Step 1: Choose Your CAPTCHA Service', 'buddypress-recaptcha' ),
					'type' => 'title',
					'desc' => esc_html__( 'Select which CAPTCHA service you want to use, then click "Save Selection" to continue.', 'buddypress-recaptcha' ),
					'id'   => 'wbc_service_selection',
				),

				array(
					'name'    => '',
					'type'    => 'custom',
					'id'      => 'wbc_captcha_service_selector',
					'default' => $this->wbc_service_selector_html( $active_service ),
				),

				array(
					'type' => 'sectionend',
					'id'   => 'wbc_service_selection',
				),
			);

			// Only show API keys and guide if a service is already selected
			if ( $has_service ) {
				// SECTION 2: API Keys (only for selected service)
				$settings[] = array(
					'name' => __( 'Step 2: Add Your API Keys', 'buddypress-recaptcha' ),
					'type' => 'title',
					'desc' => sprintf(
						/* translators: %s: Service name */
						__( 'Enter the API keys for %s.', 'buddypress-recaptcha' ),
						'<strong id="wbc-current-service-name">' . $this->wbc_service_display_name( $active_service ) . '</strong>'
					),
					'id'   => 'wbc_api_keys_section',
				);

				// Add API key fields for selected service only (as custom HTML)
				$settings[] = array(
					'name'    => '',
					'type'    => 'custom',
					'id'      => 'wbc_service_api_keys',
					'default' => $this->wbc_all_service_key_fields_html( $active_service ),
				);

				$settings[] = array(
					'type' => 'sectionend',
					'id'   => 'wbc_api_keys_section',
				);

				// SECTION 3: Setup Guide (only for selected service)
				$settings[] = array(
					'name' => esc_html__( 'Step 3: How to Get Your Keys', 'buddypress-recaptcha' ),
					'type' => 'title',
					'desc' => esc_html__( 'Follow these instructions to obtain your API keys.', 'buddypress-recaptcha' ),
					'id'   => 'wbc_setup_guide',
				);

				$settings[] = array(
					'name'    => '',
					'type'    => 'custom',
					'id'      => 'wbc_service_documentation',
					'default' => $this->wbc_simple_documentation( $active_service ),
				);

				$settings[] = array(
					'type' => 'sectionend',
					'id'   => 'wbc_setup_guide',
				);
			} else {
				// Show message to select a service first
				$settings[] = array(
					'name' => '',
					'type' => 'title',
					'id'   => 'wbc_no_service_selected',
				);

				$settings[] = array(
					'name'    => '',
					'type'    => 'custom',
					'id'      => 'wbc_select_service_message',
					'default' => '<div class="wbc-info-box"><p>👆 ' . esc_html__( 'Please select a CAPTCHA service above and click "Save Selection" to continue with the setup.', 'buddypress-recaptcha' ) . '</p></div>',
				);

				$settings[] = array(
					'type' => 'sectionend',
					'id'   => 'wbc_no_service_selected',
				);
			}

			return apply_filters( 'wbc_recaptcha_quick_setup_settings', $settings );
		}

		/**
		 * Get service display name
		 */
		private function wbc_service_display_name( $service ) {
			$names = array(
				'recaptcha-v2' => __( 'Google reCAPTCHA v2', 'buddypress-recaptcha' ),
				'recaptcha-v3' => __( 'Google reCAPTCHA v3', 'buddypress-recaptcha' ),
				'turnstile'    => __( 'Cloudflare Turnstile', 'buddypress-recaptcha' ),
				'hcaptcha'     => __( 'hCaptcha', 'buddypress-recaptcha' ),
				'altcha'       => __( 'ALTCHA', 'buddypress-recaptcha' ),
			);
			return isset( $names[ $service ] ) ? $names[ $service ] : $service;
		}

		/**
		 * Generate HTML for all service key fields
		 *
		 * @param string $active_service Currently selected service
		 * @return string Complete HTML for all service fields
		 */
		private function wbc_all_service_key_fields_html( $active_service ) {
			$services = array( 'recaptcha-v2', 'recaptcha-v3', 'turnstile', 'hcaptcha', 'altcha' );
			$html = '';

			foreach ( $services as $service ) {
				$is_active = ( $service === $active_service );
				$wrapper_class = 'wbc-service-keys wbc-service-keys-' . $service;
				$wrapper_class .= $is_active ? ' wbc-active' : ' wbc-hidden';

				$html .= '<div class="' . esc_attr( $wrapper_class ) . '">';
				$html .= $this->wbc_service_key_fields_html( $service );
				$html .= '</div>';
			}

			return $html;
		}

		/**
		 * Generate HTML for a specific service's key fields
		 *
		 * @param string $service Service identifier
		 * @return string HTML for service fields
		 */
		private function wbc_service_key_fields_html( $service ) {
			$html = '';

			switch ( $service ) {
				case 'recaptcha-v2':
					$site_key = get_option( 'wbc_recaptcha_v2_site_key', '' );
					$secret_key = get_option( 'wbc_recaptcha_v2_secret_key', '' );

					$html .= $this->wbc_text_field_html(
						'wbc_recaptcha_v2_site_key',
						__( 'Google reCAPTCHA v2 - Site Key', 'buddypress-recaptcha' ),
						$site_key,
						__( 'Enter your site key', 'buddypress-recaptcha' ),
						sprintf(
							/* translators: %s: link */
							__( 'Get your keys from %s', 'buddypress-recaptcha' ),
							'<a href="https://www.google.com/recaptcha/admin" target="_blank">Google reCAPTCHA Admin</a>'
						)
					);

					$html .= $this->wbc_password_field_html(
						'wbc_recaptcha_v2_secret_key',
						__( 'Google reCAPTCHA v2 - Secret Key', 'buddypress-recaptcha' ),
						$secret_key,
						__( 'Enter your secret key', 'buddypress-recaptcha' ),
						__( 'Your private secret key (keep this confidential)', 'buddypress-recaptcha' )
					);
					break;

				case 'recaptcha-v3':
					$site_key = get_option( 'wbc_recaptcha_v3_site_key', '' );
					$secret_key = get_option( 'wbc_recaptcha_v3_secret_key', '' );
					$threshold = get_option( 'wbc_recaptcha_v3_score_threshold', '0.5' );

					$html .= $this->wbc_text_field_html(
						'wbc_recaptcha_v3_site_key',
						__( 'Google reCAPTCHA v3 - Site Key', 'buddypress-recaptcha' ),
						$site_key,
						__( 'Enter your v3 site key', 'buddypress-recaptcha' ),
						sprintf(
							/* translators: %s: link */
							__( 'Get your keys from %s', 'buddypress-recaptcha' ),
							'<a href="https://www.google.com/recaptcha/admin" target="_blank">Google reCAPTCHA Admin</a>'
						)
					);

					$html .= $this->wbc_password_field_html(
						'wbc_recaptcha_v3_secret_key',
						__( 'Google reCAPTCHA v3 - Secret Key', 'buddypress-recaptcha' ),
						$secret_key,
						__( 'Enter your v3 secret key', 'buddypress-recaptcha' ),
						__( 'Your private secret key (keep this confidential)', 'buddypress-recaptcha' )
					);

					$html .= $this->wbc_number_field_html(
						'wbc_recaptcha_v3_score_threshold',
						__( 'Score Threshold', 'buddypress-recaptcha' ),
						$threshold,
						__( 'Minimum score (0.0 to 1.0) to pass verification. Default: 0.5', 'buddypress-recaptcha' ),
						array( 'min' => '0', 'max' => '1', 'step' => '0.1' )
					);
					break;

				case 'turnstile':
					$site_key = get_option( 'wbc_turnstile_site_key', '' );
					$secret_key = get_option( 'wbc_turnstile_secret_key', '' );

					$html .= $this->wbc_text_field_html(
						'wbc_turnstile_site_key',
						__( 'Cloudflare Turnstile - Site Key', 'buddypress-recaptcha' ),
						$site_key,
						__( 'Enter your Turnstile site key', 'buddypress-recaptcha' ),
						sprintf(
							/* translators: %s: link */
							__( 'Get your keys from %s', 'buddypress-recaptcha' ),
							'<a href="https://dash.cloudflare.com/?to=/:account/turnstile" target="_blank">Cloudflare Dashboard</a>'
						)
					);

					$html .= $this->wbc_password_field_html(
						'wbc_turnstile_secret_key',
						__( 'Cloudflare Turnstile - Secret Key', 'buddypress-recaptcha' ),
						$secret_key,
						__( 'Enter your Turnstile secret key', 'buddypress-recaptcha' ),
						__( 'Your private secret key (keep this confidential)', 'buddypress-recaptcha' )
					);
					break;

				case 'hcaptcha':
					$site_key = get_option( 'wbc_hcaptcha_site_key', '' );
					$secret_key = get_option( 'wbc_hcaptcha_secret_key', '' );

					$html .= $this->wbc_text_field_html(
						'wbc_hcaptcha_site_key',
						__( 'hCaptcha - Site Key', 'buddypress-recaptcha' ),
						$site_key,
						__( 'Enter your hCaptcha site key', 'buddypress-recaptcha' ),
						sprintf(
							/* translators: %s: link */
							__( 'Get your keys from %s', 'buddypress-recaptcha' ),
							'<a href="https://dashboard.hcaptcha.com/sites" target="_blank">hCaptcha Dashboard</a>'
						)
					);

					$html .= $this->wbc_password_field_html(
						'wbc_hcaptcha_secret_key',
						__( 'hCaptcha - Secret Key', 'buddypress-recaptcha' ),
						$secret_key,
						__( 'Enter your hCaptcha secret key', 'buddypress-recaptcha' ),
						__( 'Your private secret key (keep this confidential)', 'buddypress-recaptcha' )
					);
					break;

				case 'altcha':
					$hmac_key = get_option( 'wbc_altcha_hmac_key', '' );

					// Auto-generate HMAC key if empty (better security practice)
					if ( empty( $hmac_key ) ) {
						$hmac_key = bin2hex( random_bytes( 32 ) ); // Generate 64-character hex string
						update_option( 'wbc_altcha_hmac_key', $hmac_key );
					}

					$complexity = get_option( 'wbc_altcha_complexity', '100000' );

					$html .= '<div class="wbcom-settings-section-wrap">';
					$html .= '<div valign="top" class="">';
					$html .= '<div scope="row" class="wbcom-settings-section-options-heading titledesc">';
					$html .= '<label for="wbc_altcha_hmac_key">' . esc_html__( 'ALTCHA - HMAC Secret Key', 'buddypress-recaptcha' ) . '</label>';
					$html .= '<p class="description">' . esc_html__( 'Generate a random secret key for challenge signing. ALTCHA is self-hosted and requires HTTPS.', 'buddypress-recaptcha' ) . ' <button type="button" class="button button-secondary wbc-generate-hmac-key" style="margin-left: 10px">' . esc_html__( 'Generate Random Key', 'buddypress-recaptcha' ) . '</button></p>';
					$html .= '</div>';
					$html .= '<div class="wbcom-settings-section-options">';
					$html .= '<div class="forminp forminp-text">';
					$html .= '<input name="wbc_altcha_hmac_key" id="wbc_altcha_hmac_key" type="text" value="' . esc_attr( $hmac_key ) . '" class="" placeholder="' . esc_attr__( 'Your HMAC secret key (32+ characters)', 'buddypress-recaptcha' ) . '">';
					$html .= '</div>';
					$html .= '</div>';
					$html .= '</div>';
					$html .= '</div>';

					$html .= '<div class="wbcom-settings-section-wrap">';
					$html .= '<div valign="top">';
					$html .= '<div class="wbcom-settings-section-options">';
					$html .= '<div scope="row" class="wbcom-settings-section-options-heading titledesc">';
					$html .= '<label for="wbc_altcha_complexity">' . esc_html__( 'Complexity Level', 'buddypress-recaptcha' ) . '</label>';
					$html .= '<p class="description">' . esc_html__( 'Higher numbers mean harder challenges', 'buddypress-recaptcha' ) . '</p>';
					$html .= '</div>';
					$html .= '</div>';
					$html .= '<div class="forminp forminp-select">';
					$html .= '<select name="wbc_altcha_complexity" id="wbc_altcha_complexity" class="">';
					$html .= '<option value="50000"' . selected( $complexity, '50000', false ) . '>' . esc_html__( 'Easy (50,000)', 'buddypress-recaptcha' ) . '</option>';
					$html .= '<option value="100000"' . selected( $complexity, '100000', false ) . '>' . esc_html__( 'Medium (100,000)', 'buddypress-recaptcha' ) . '</option>';
					$html .= '<option value="200000"' . selected( $complexity, '200000', false ) . '>' . esc_html__( 'Hard (200,000)', 'buddypress-recaptcha' ) . '</option>';
					$html .= '</select>';
					$html .= '</div>';
					$html .= '</div>';
					$html .= '</div>';
					break;
			}

			return $html;
		}

		/**
		 * Generate HTML for a text input field
		 */
		private function wbc_text_field_html( $id, $label, $value, $placeholder, $description = '' ) {
			$html = '<div class="wbcom-settings-section-wrap">';
			$html .= '<div valign="top" class="">';
			$html .= '<div scope="row" class="wbcom-settings-section-options-heading titledesc">';
			$html .= '<label for="' . esc_attr( $id ) . '">' . esc_html( $label ) . '</label>';
			if ( $description ) {
				$html .= '<p class="description">' . wp_kses_post( $description ) . '</p>';
			}
			$html .= '</div>';
			$html .= '<div class="wbcom-settings-section-options">';
			$html .= '<div class="forminp forminp-text">';
			$html .= '<input name="' . esc_attr( $id ) . '" id="' . esc_attr( $id ) . '" type="text" value="' . esc_attr( $value ) . '" class="" placeholder="' . esc_attr( $placeholder ) . '">';
			$html .= '</div>';
			$html .= '</div>';
			$html .= '</div>';
			$html .= '</div>';
			return $html;
		}

		/**
		 * Generate HTML for a password input field
		 */
		private function wbc_password_field_html( $id, $label, $value, $placeholder, $description = '' ) {
			$html = '<div class="wbcom-settings-section-wrap">';
			$html .= '<div valign="top" class="">';
			$html .= '<div scope="row" class="wbcom-settings-section-options-heading titledesc">';
			$html .= '<label for="' . esc_attr( $id ) . '">' . esc_html( $label ) . '</label>';
			if ( $description ) {
				$html .= '<p class="description">' . esc_html( $description ) . '</p>';
			}
			$html .= '</div>';
			$html .= '<div class="wbcom-settings-section-options">';
			$html .= '<div class="forminp forminp-password">';
			$html .= '<input name="' . esc_attr( $id ) . '" id="' . esc_attr( $id ) . '" type="text" value="' . esc_attr( $value ) . '" class="wbc-secret-key-input" placeholder="' . esc_attr( $placeholder ) . '">';
			$html .= '</div>';
			$html .= '</div>';
			$html .= '</div>';
			$html .= '</div>';
			return $html;
		}

		/**
		 * Generate HTML for a number input field
		 */
		private function wbc_number_field_html( $id, $label, $value, $description = '', $attrs = array() ) {
			$html = '<div class="wbcom-settings-section-wrap">';
			$html .= '<div valign="top" class="">';
			$html .= '<div scope="row" class="wbcom-settings-section-options-heading titledesc">';
			$html .= '<label for="' . esc_attr( $id ) . '">' . esc_html( $label ) . '</label>';
			if ( $description ) {
				$html .= '<p class="description">' . esc_html( $description ) . '</p>';
			}
			$html .= '</div>';
			$html .= '<div class="wbcom-settings-section-options">';
			$html .= '<div class="forminp forminp-number">';
			$html .= '<input name="' . esc_attr( $id ) . '" id="' . esc_attr( $id ) . '" type="number" value="' . esc_attr( $value ) . '" class=""';
			foreach ( $attrs as $attr => $attr_value ) {
				$html .= ' ' . esc_attr( $attr ) . '="' . esc_attr( $attr_value ) . '"';
			}
			$html .= '>';
			$html .= '</div>';
			$html .= '</div>';
			$html .= '</div>';
			$html .= '</div>';
			return $html;
		}

		/**
		 * Generate grouped checkbox HTML for protection settings
		 *
		 * @param array $checkboxes Array of checkbox configurations
		 * @return string HTML for grouped checkboxes
		 */
		private function wbc_protection_checkbox_group( $checkboxes ) {
			$html = '<div class="wbc-protection-group">';

			foreach ( $checkboxes as $checkbox ) {
				$value = get_option( $checkbox['id'], $checkbox['default'] );
				$checked = ( 'yes' === $value ) ? 'checked="checked"' : '';

				$html .= '<div class="wbc-protection-item">';
				$html .= '<label for="' . esc_attr( $checkbox['id'] ) . '" class="wbc-protection-label">';
				$html .= '<span class="wbc-protection-title-wrapper">';
				$html .= '<span class="wbc-protection-title">' . esc_html( $checkbox['label'] ) . '</span>';
				$html .= '<span class="wbc-tooltip">';
				$html .= '<span class="wbc-tooltip-icon">?</span>';
				$html .= '<span class="wbc-tooltip-text">' . esc_html( $checkbox['desc'] ) . '</span>';
				$html .= '</span>';
				$html .= '</span>';
				$html .= '<div class="wbc-toggle-wrapper">';
				$html .= '<input type="checkbox" name="' . esc_attr( $checkbox['id'] ) . '" id="' . esc_attr( $checkbox['id'] ) . '" value="1" ' . $checked . ' class="wbc-toggle-input">';
				$html .= '<span class="wbc-toggle-slider"></span>';
				$html .= '</div>';
				$html .= '</label>';
				$html .= '</div>';
			}

			$html .= '</div>';
			return $html;
		}

		/**
		 * Get service-specific key fields
		 *
		 * @param string $service   Service identifier.
		 * @param bool   $is_active Whether this is the active service.
		 * @return array Field configuration array.
		 */
		private function wbc_service_key_fields( $service, $is_active ) {
			$fields = array();

			// Wrapper for each service's fields
			$wrapper_class = 'wbc-service-keys wbc-service-keys-' . $service;
			$wrapper_class .= $is_active ? ' wbc-active' : ' wbc-hidden';

			switch ( $service ) {
				case 'recaptcha-v2':
					$fields[] = array(
						'type' => 'custom',
						'id'   => 'wbc_recaptcha_v2_wrapper_start',
						'default' => '<div class="' . esc_attr( $wrapper_class ) . '">',
					);

					$fields[] = array(
						'name'        => __( 'Google reCAPTCHA v2 - Site Key', 'buddypress-recaptcha' ),
						'type'        => 'text',
						'id'          => 'wbc_recaptcha_v2_site_key',
						'desc'        => sprintf(
							/* translators: %s: link to Google reCAPTCHA admin */
							__( 'Get your keys from %s', 'buddypress-recaptcha' ),
							'<a href="https://www.google.com/recaptcha/admin" target="_blank">Google reCAPTCHA Admin</a>'
						),
						'placeholder' => __( 'Enter your site key', 'buddypress-recaptcha' ),
					);

					$fields[] = array(
						'name'        => __( 'Google reCAPTCHA v2 - Secret Key', 'buddypress-recaptcha' ),
						'type'        => 'password',
						'id'          => 'wbc_recaptcha_v2_secret_key',
						'desc'        => __( 'Your private secret key (keep this confidential)', 'buddypress-recaptcha' ),
						'placeholder' => __( 'Enter your secret key', 'buddypress-recaptcha' ),
					);

					$fields[] = array(
						'type' => 'custom',
						'id'   => 'wbc_recaptcha_v2_wrapper_end',
						'default' => '</div>',
					);
					break;

				case 'recaptcha-v3':
					$fields[] = array(
						'type' => 'custom',
						'id'   => 'wbc_recaptcha_v3_wrapper_start',
						'default' => '<div class="' . esc_attr( $wrapper_class ) . '">',
					);

					$fields[] = array(
						'name'        => __( 'Google reCAPTCHA v3 - Site Key', 'buddypress-recaptcha' ),
						'type'        => 'text',
						'id'          => 'wbc_recaptcha_v3_site_key',
						'desc'        => sprintf(
							/* translators: %s: link to Google reCAPTCHA admin */
							__( 'Get your keys from %s', 'buddypress-recaptcha' ),
							'<a href="https://www.google.com/recaptcha/admin" target="_blank">Google reCAPTCHA Admin</a>'
						),
						'placeholder' => __( 'Enter your v3 site key', 'buddypress-recaptcha' ),
					);

					$fields[] = array(
						'name'        => __( 'Google reCAPTCHA v3 - Secret Key', 'buddypress-recaptcha' ),
						'type'        => 'password',
						'id'          => 'wbc_recaptcha_v3_secret_key',
						'desc'        => __( 'Your private secret key (keep this confidential)', 'buddypress-recaptcha' ),
						'placeholder' => __( 'Enter your v3 secret key', 'buddypress-recaptcha' ),
					);

					$fields[] = array(
						'name'    => __( 'Score Threshold', 'buddypress-recaptcha' ),
						'type'    => 'number',
						'id'      => 'wbc_recaptcha_v3_score_threshold',
						'desc'    => __( 'Minimum score (0.0 to 1.0) to pass verification. Default: 0.5', 'buddypress-recaptcha' ),
						'default' => '0.5',
						'custom_attributes' => array(
							'min'  => '0',
							'max'  => '1',
							'step' => '0.1',
						),
					);

					$fields[] = array(
						'type' => 'custom',
						'id'   => 'wbc_recaptcha_v3_wrapper_end',
						'default' => '</div>',
					);
					break;

				case 'turnstile':
					$fields[] = array(
						'type' => 'custom',
						'id'   => 'wbc_turnstile_wrapper_start',
						'default' => '<div class="' . esc_attr( $wrapper_class ) . '">',
					);

					$fields[] = array(
						'name'        => __( 'Cloudflare Turnstile - Site Key', 'buddypress-recaptcha' ),
						'type'        => 'text',
						'id'          => 'wbc_turnstile_site_key',
						'desc'        => sprintf(
							/* translators: %s: link to Cloudflare dashboard */
							__( 'Get your keys from %s', 'buddypress-recaptcha' ),
							'<a href="https://dash.cloudflare.com/?to=/:account/turnstile" target="_blank">Cloudflare Dashboard</a>'
						),
						'placeholder' => __( 'Enter your Turnstile site key', 'buddypress-recaptcha' ),
					);

					$fields[] = array(
						'name'        => __( 'Cloudflare Turnstile - Secret Key', 'buddypress-recaptcha' ),
						'type'        => 'password',
						'id'          => 'wbc_turnstile_secret_key',
						'desc'        => __( 'Your private secret key (keep this confidential)', 'buddypress-recaptcha' ),
						'placeholder' => __( 'Enter your Turnstile secret key', 'buddypress-recaptcha' ),
					);

					$fields[] = array(
						'type' => 'custom',
						'id'   => 'wbc_turnstile_wrapper_end',
						'default' => '</div>',
					);
					break;

				case 'hcaptcha':
					$fields[] = array(
						'type' => 'custom',
						'id'   => 'wbc_hcaptcha_wrapper_start',
						'default' => '<div class="' . esc_attr( $wrapper_class ) . '">',
					);

					$fields[] = array(
						'name'        => __( 'hCaptcha - Site Key', 'buddypress-recaptcha' ),
						'type'        => 'text',
						'id'          => 'wbc_hcaptcha_site_key',
						'desc'        => sprintf(
							/* translators: %s: link to hCaptcha dashboard */
							__( 'Get your keys from %s', 'buddypress-recaptcha' ),
							'<a href="https://dashboard.hcaptcha.com/sites" target="_blank">hCaptcha Dashboard</a>'
						),
						'placeholder' => __( 'Enter your hCaptcha site key', 'buddypress-recaptcha' ),
					);

					$fields[] = array(
						'name'        => __( 'hCaptcha - Secret Key', 'buddypress-recaptcha' ),
						'type'        => 'password',
						'id'          => 'wbc_hcaptcha_secret_key',
						'desc'        => __( 'Your private secret key (keep this confidential)', 'buddypress-recaptcha' ),
						'placeholder' => __( 'Enter your hCaptcha secret key', 'buddypress-recaptcha' ),
					);

					$fields[] = array(
						'type' => 'custom',
						'id'   => 'wbc_hcaptcha_wrapper_end',
						'default' => '</div>',
					);
					break;

				case 'altcha':
					$fields[] = array(
						'type' => 'custom',
						'id'   => 'wbc_altcha_wrapper_start',
						'default' => '<div class="' . esc_attr( $wrapper_class ) . '">',
					);

					$fields[] = array(
						'name'        => __( 'ALTCHA - HMAC Secret Key', 'buddypress-recaptcha' ),
						'type'        => 'text',
						'id'          => 'wbc_altcha_hmac_key',
						'desc'        => __( 'Generate a random secret key for challenge signing. ALTCHA is self-hosted and requires HTTPS.', 'buddypress-recaptcha' ) .
						                 ' <button type="button" class="button button-secondary wbc-generate-altcha-key" style="margin-left: 10px;">' .
						                 __( 'Generate Random Key', 'buddypress-recaptcha' ) . '</button>',
						'placeholder' => __( 'Your HMAC secret key (32+ characters)', 'buddypress-recaptcha' ),
					);

					$fields[] = array(
						'name'    => __( 'Complexity Level', 'buddypress-recaptcha' ),
						'type'    => 'select',
						'id'      => 'wbc_altcha_complexity',
						'options' => array(
							'50000'  => __( 'Easy (50,000)', 'buddypress-recaptcha' ),
							'100000' => __( 'Medium (100,000)', 'buddypress-recaptcha' ),
							'200000' => __( 'Hard (200,000)', 'buddypress-recaptcha' ),
						),
						'default' => '100000',
						'desc'    => __( 'Higher numbers mean harder challenges', 'buddypress-recaptcha' ),
					);

					$fields[] = array(
						'type' => 'custom',
						'id'   => 'wbc_altcha_wrapper_end',
						'default' => '</div>',
					);
					break;
			}

			return $fields;
		}

		/**
		 * Get Protection settings (all forms in one organized place)
		 *
		 * @return array
		 */
	public function wbc_protection_settings() {
		$settings = array(
			array(
				'name' => esc_html__( 'Form Protection Settings', 'buddypress-recaptcha' ),
				'type' => 'title',
				'desc' => esc_html__( 'Choose which forms to protect from spam and bots. Enable protection where you need it most.', 'buddypress-recaptcha' ),
				'id'   => 'wbc_protection_main',
			),
		);

		// Load modular settings system
		require_once plugin_dir_path( __FILE__ ) . 'settings-modules/class-wbc-settings-module-loader.php';

		// Get all protection settings from active modules only
		$module_settings = wbc_settings_module_loader()->get_all_protection_settings();

		// Merge module settings into main settings array
		if ( ! empty( $module_settings ) ) {
			$settings = array_merge( $settings, $module_settings );
		}

		return apply_filters( 'wbc_recaptcha_protection_settings', $settings );
	}

		/**
		 * Get combined advanced settings (appearance + advanced)
		 *
		 * @return array
		 */
		public function wbc_combined_advanced_settings() {
			$settings = array(
				array(
					'name' => esc_html__( 'Advanced Settings', 'buddypress-recaptcha' ),
					'type' => 'title',
					'desc' => esc_html__( 'Fine-tune appearance and behavior settings.', 'buddypress-recaptcha' ),
					'id'   => 'wbc_combined_advanced',
				),
			);

			// Appearance settings
			// $settings[] = array(
			// 	'name' => esc_html__( 'Appearanceee', 'buddypress-recaptcha' ),
			// 	'type' => 'title',
			// 	'id'   => 'wbc_appearance_section',
			// );

			$service = get_option( 'wbc_captcha_service', 'recaptcha-v2' );

			if ( in_array( $service, array( 'recaptcha-v2', 'hcaptcha' ) ) ) {
				$settings[] = array(
					'name'    => __( 'Theme', 'buddypress-recaptcha' ),
					'type'    => 'select',
					'id'      => 'wbc_recaptcha_theme',
					'options' => array(
						'light' => __( 'Light', 'buddypress-recaptcha' ),
						'dark'  => __( 'Dark', 'buddypress-recaptcha' ),
					),
					'default' => 'light',
					'desc'    => __( 'Widget color theme', 'buddypress-recaptcha' ),
				);

				$settings[] = array(
					'name'    => __( 'Size', 'buddypress-recaptcha' ),
					'type'    => 'select',
					'id'      => 'wbc_recaptcha_size',
					'options' => array(
						'normal'  => __( 'Normal', 'buddypress-recaptcha' ),
						'compact' => __( 'Compact', 'buddypress-recaptcha' ),
					),
					'default' => 'normal',
					'desc'    => __( 'Widget size', 'buddypress-recaptcha' ),
				);
			}

			// $settings[] = array(
			// 	'type' => 'sectionend',
			// 	'id'   => 'wbc_appearance_section',
			// );

			// Advanced options
			$settings[] = array(
				'name' => esc_html__( 'Advanced Options', 'buddypress-recaptcha' ),
				'type' => 'title',
				'id'   => 'wbc_advanced_options',
			);

			$settings[] = array(
				'name'    => __( 'IP Whitelist', 'buddypress-recaptcha' ),
				'type'    => 'textarea',
				'id'      => 'wbc_recaptcha_ip_to_skip_captcha',
				'desc'    => __( 'IP addresses that skip captcha (comma-separated)', 'buddypress-recaptcha' ),
				'placeholder' => '192.168.1.1, 10.0.0.1',
			);

			$settings[] = array(
				'name'        => __( 'Error Message', 'buddypress-recaptcha' ),
				'type'        => 'text',
				'id'          => 'wbc_recaptcha_error_msg_captcha_blank',
				'desc'        => __( 'Message shown when captcha is not completed', 'buddypress-recaptcha' ),
				'default'     => __( 'Please complete the security check.', 'buddypress-recaptcha' ),
			);

			$settings[] = array(
				'type' => 'sectionend',
				'id'   => 'wbc_advanced_options',
			);

			return apply_filters( 'wbc_recaptcha_combined_advanced_settings', $settings );
		}

		/**
		 * Get current site key based on active service
		 */
		private function wbc_current_site_key() {
			$service = get_option( 'wbc_captcha_service', 'recaptcha-v2' );
			$key_map = array(
				'recaptcha-v2' => 'wbc_recaptcha_v2_site_key',
				'recaptcha-v3' => 'wbc_recaptcha_v3_site_key',
				'turnstile' => 'wbc_turnstile_site_key',
				'hcaptcha' => 'wbc_hcaptcha_site_key',
			);
			return isset( $key_map[$service] ) ? get_option( $key_map[$service] ) : '';
		}

		/**
		 * Get current secret key based on active service
		 */
		private function wbc_current_secret_key() {
			$service = get_option( 'wbc_captcha_service', 'recaptcha-v2' );
			$key_map = array(
				'recaptcha-v2' => 'wbc_recaptcha_v2_secret_key',
				'recaptcha-v3' => 'wbc_recaptcha_v3_secret_key',
				'turnstile' => 'wbc_turnstile_secret_key',
				'hcaptcha' => 'wbc_hcaptcha_secret_key',
				'altcha' => 'wbc_altcha_hmac_key',
			);
			return isset( $key_map[$service] ) ? get_option( $key_map[$service] ) : '';
		}

		/**
		 * Get service signup links HTML
		 */
		private function wbc_service_signup_links() {
			$links = array(
				'recaptcha-v2' => array( 'Google reCAPTCHA', 'https://www.google.com/recaptcha/admin' ),
				'recaptcha-v3' => array( 'Google reCAPTCHA', 'https://www.google.com/recaptcha/admin' ),
				'turnstile' => array( 'Cloudflare Turnstile', 'https://dash.cloudflare.com/sign-up' ),
				'hcaptcha' => array( 'hCaptcha', 'https://www.hcaptcha.com/signup-interstitial' ),
			);

			$html = '<div style="margin-top: 10px;">';
			$html .= __( 'Get your API keys: ', 'buddypress-recaptcha' );
			foreach ( $links as $service => $data ) {
				$html .= sprintf(
					'<a href="%s" target="_blank" style="margin: 0 5px;">%s</a> | ',
					esc_url( $data[1] ),
					esc_html( $data[0] )
				);
			}
			$html = rtrim( $html, ' | ' );
			$html .= '</div>';

			return $html;
		}

		/**
		 * Render service selector with radio buttons
		 *
		 * @param string $active_service Currently active service.
		 * @return string HTML output for service selector.
		 */
		private function wbc_service_selector_html( $active_service ) {
			$services = array(
				'recaptcha-v2' => array(
					'name' => __( 'Google reCAPTCHA v2', 'buddypress-recaptcha' ),
					'desc' => __( 'Shows the familiar "I\'m not a robot" checkbox', 'buddypress-recaptcha' ),
					'badge' => __( 'Most Popular', 'buddypress-recaptcha' ),
				),
				'recaptcha-v3' => array(
					'name' => __( 'Google reCAPTCHA v3', 'buddypress-recaptcha' ),
					'desc' => __( 'Invisible verification with score-based detection', 'buddypress-recaptcha' ),
				),
				'turnstile' => array(
					'name' => __( 'Cloudflare Turnstile', 'buddypress-recaptcha' ),
					'desc' => __( 'Privacy-friendly CAPTCHA alternative from Cloudflare', 'buddypress-recaptcha' ),
				),
				'hcaptcha' => array(
					'name' => __( 'hCaptcha', 'buddypress-recaptcha' ),
					'desc' => __( 'Privacy-focused with rewards system', 'buddypress-recaptcha' ),
				),
				'altcha' => array(
					'name' => __( 'ALTCHA', 'buddypress-recaptcha' ),
					'desc' => __( 'Self-hosted solution, no external API required', 'buddypress-recaptcha' ),
					'badge' => __( 'Privacy First', 'buddypress-recaptcha' ),
				),
			);

			$html = '<div class="wbc-service-selector">';

			foreach ( $services as $service_id => $service ) {
				$checked = checked( $active_service, $service_id, false );
				$badge_html = isset( $service['badge'] ) ? '<span class="wbc-service-badge">' . esc_html( $service['badge'] ) . '</span>' : '';

				$html .= '<label class="wbc-service-option">';
				$html .= '<input type="radio" name="wbc_captcha_service" value="' . esc_attr( $service_id ) . '" ' . $checked . '>';
				$html .= '<strong class="wbc-service-name">' . esc_html( $service['name'] ) . '</strong> ' . $badge_html . '<br>';
				$html .= '<span class="wbc-service-desc">' . esc_html( $service['desc'] ) . '</span>';
				$html .= '</label>';
			}

			$html .= '</div>';

			return $html;
		}

		/**
		 * Get simple documentation for getting API keys
		 *
		 * @param string $service Active service identifier
		 * @return string HTML content for service documentation
		 */
		private function wbc_simple_documentation( $service ) {
			// Simple guide for each service
			$guides = array(
				'recaptcha-v2' => $this->wbc_simple_recaptcha_v2_guide(),
				'recaptcha-v3' => $this->wbc_simple_recaptcha_v3_guide(),
				'turnstile' => $this->wbc_simple_turnstile_guide(),
				'hcaptcha' => $this->wbc_simple_hcaptcha_guide(),
				'altcha' => $this->wbc_simple_altcha_guide(),
			);

			$html = '<div class="wbc-service-docs-container">';

			// Show guide for each service with dynamic visibility
			foreach ( $guides as $service_id => $guide_content ) {
				$active_class = ( $service_id === $service ) ? 'wbc-active' : 'wbc-hidden';
				$html .= '<div class="wbc-service-docs wbc-service-docs-' . esc_attr( $service_id ) . ' ' . esc_attr( $active_class ) . '">';
				$html .= $guide_content;
				$html .= '</div>';
			}

			$html .= '</div>';
			return $html;
		}

		/**
		 * Simple guide for reCAPTCHA v2
		 */
		private function wbc_simple_recaptcha_v2_guide() {
			$html = '<div class="wbc-guide-box wbc-guide-google">';
			$html .= '<h3>🔑 ' . esc_html__( 'How to Get Google reCAPTCHA v2 Keys', 'buddypress-recaptcha' ) . '</h3>';
			$html .= '<ol class="wbc-guide-steps">';
			$html .= '<li>' . esc_html__( 'Go to', 'buddypress-recaptcha' ) . ' <a href="https://www.google.com/recaptcha/admin/create" target="_blank">Google reCAPTCHA Admin Console</a></li>';
			$html .= '<li>' . esc_html__( 'Sign in with your Google account', 'buddypress-recaptcha' ) . '</li>';
			$html .= '<li>' . esc_html__( 'Enter a label for your site (e.g., "My WordPress Site")', 'buddypress-recaptcha' ) . '</li>';
			$html .= '<li>' . esc_html__( 'Select "reCAPTCHA v2" → "I\'m not a robot" Checkbox', 'buddypress-recaptcha' ) . '</li>';
			$html .= '<li>' . esc_html__( 'Add your domain(s) - both with and without www', 'buddypress-recaptcha' ) . '</li>';
			$html .= '<li>' . esc_html__( 'Accept the Terms of Service', 'buddypress-recaptcha' ) . '</li>';
			$html .= '<li>' . esc_html__( 'Click "Submit"', 'buddypress-recaptcha' ) . '</li>';
			$html .= '<li>' . esc_html__( 'Copy your Site Key and Secret Key', 'buddypress-recaptcha' ) . '</li>';
			$html .= '<li>' . esc_html__( 'Paste them in the fields above and save', 'buddypress-recaptcha' ) . '</li>';
			$html .= '</ol>';
			$html .= '</div>';
			return $html;
		}

		/**
		 * Simple guide for reCAPTCHA v3
		 */
		private function wbc_simple_recaptcha_v3_guide() {
			$html = '<div class="wbc-guide-box wbc-guide-google">';
			$html .= '<h3>🔑 ' . esc_html__( 'How to Get Google reCAPTCHA v3 Keys', 'buddypress-recaptcha' ) . '</h3>';
			$html .= '<ol class="wbc-guide-steps">';
			$html .= '<li>' . esc_html__( 'Go to', 'buddypress-recaptcha' ) . ' <a href="https://www.google.com/recaptcha/admin/create" target="_blank">Google reCAPTCHA Admin Console</a></li>';
			$html .= '<li>' . esc_html__( 'Sign in with your Google account', 'buddypress-recaptcha' ) . '</li>';
			$html .= '<li>' . esc_html__( 'Enter a label for your site', 'buddypress-recaptcha' ) . '</li>';
			$html .= '<li>' . esc_html__( 'Select "reCAPTCHA v3"', 'buddypress-recaptcha' ) . '</li>';
			$html .= '<li>' . esc_html__( 'Add your domain(s)', 'buddypress-recaptcha' ) . '</li>';
			$html .= '<li>' . esc_html__( 'Accept the Terms of Service', 'buddypress-recaptcha' ) . '</li>';
			$html .= '<li>' . esc_html__( 'Click "Submit"', 'buddypress-recaptcha' ) . '</li>';
			$html .= '<li>' . esc_html__( 'Copy your Site Key and Secret Key', 'buddypress-recaptcha' ) . '</li>';
			$html .= '<li>' . esc_html__( 'Paste them in the fields above and save', 'buddypress-recaptcha' ) . '</li>';
			$html .= '</ol>';
			$html .= '<p class="wbc-guide-note">💡 ' . esc_html__( 'Note: reCAPTCHA v3 runs invisibly in the background and uses a score system (0.0 to 1.0) to detect bots.', 'buddypress-recaptcha' ) . '</p>';
			$html .= '</div>';
			return $html;
		}

		/**
		 * Simple guide for Turnstile
		 */
		private function wbc_simple_turnstile_guide() {
			$html = '<div class="wbc-guide-box wbc-guide-cloudflare">';
			$html .= '<h3>🔑 ' . esc_html__( 'How to Get Cloudflare Turnstile Keys', 'buddypress-recaptcha' ) . '</h3>';
			$html .= '<ol class="wbc-guide-steps">';
			$html .= '<li>' . esc_html__( 'Go to', 'buddypress-recaptcha' ) . ' <a href="https://dash.cloudflare.com/?to=/:account/turnstile" target="_blank">Cloudflare Dashboard</a></li>';
			$html .= '<li>' . esc_html__( 'Sign in or create a free Cloudflare account', 'buddypress-recaptcha' ) . '</li>';
			$html .= '<li>' . esc_html__( 'Navigate to "Turnstile" in the sidebar', 'buddypress-recaptcha' ) . '</li>';
			$html .= '<li>' . esc_html__( 'Click "Add Site"', 'buddypress-recaptcha' ) . '</li>';
			$html .= '<li>' . esc_html__( 'Enter your site name', 'buddypress-recaptcha' ) . '</li>';
			$html .= '<li>' . esc_html__( 'Add your domain(s)', 'buddypress-recaptcha' ) . '</li>';
			$html .= '<li>' . esc_html__( 'Select widget mode (Managed recommended)', 'buddypress-recaptcha' ) . '</li>';
			$html .= '<li>' . esc_html__( 'Click "Create"', 'buddypress-recaptcha' ) . '</li>';
			$html .= '<li>' . esc_html__( 'Copy your Site Key and Secret Key', 'buddypress-recaptcha' ) . '</li>';
			$html .= '<li>' . esc_html__( 'Paste them in the fields above and save', 'buddypress-recaptcha' ) . '</li>';
			$html .= '</ol>';
			$html .= '</div>';
			return $html;
		}

		/**
		 * Simple guide for hCaptcha
		 */
		private function wbc_simple_hcaptcha_guide() {
			$html = '<div class="wbc-guide-box wbc-guide-hcaptcha">';
			$html .= '<h3>🔑 ' . esc_html__( 'How to Get hCaptcha Keys', 'buddypress-recaptcha' ) . '</h3>';
			$html .= '<ol class="wbc-guide-steps">';
			$html .= '<li>' . esc_html__( 'Go to', 'buddypress-recaptcha' ) . ' <a href="https://www.hcaptcha.com/signup-interstitial" target="_blank">hCaptcha Dashboard</a></li>';
			$html .= '<li>' . esc_html__( 'Sign up for a free account', 'buddypress-recaptcha' ) . '</li>';
			$html .= '<li>' . esc_html__( 'Verify your email address', 'buddypress-recaptcha' ) . '</li>';
			$html .= '<li>' . esc_html__( 'Go to "Sites" → "New Site"', 'buddypress-recaptcha' ) . '</li>';
			$html .= '<li>' . esc_html__( 'Enter your domain', 'buddypress-recaptcha' ) . '</li>';
			$html .= '<li>' . esc_html__( 'Click "Add"', 'buddypress-recaptcha' ) . '</li>';
			$html .= '<li>' . esc_html__( 'Copy your Site Key', 'buddypress-recaptcha' ) . '</li>';
			$html .= '<li>' . esc_html__( 'Go to "Settings" to find your Secret Key', 'buddypress-recaptcha' ) . '</li>';
			$html .= '<li>' . esc_html__( 'Paste them in the fields above and save', 'buddypress-recaptcha' ) . '</li>';
			$html .= '</ol>';
			$html .= '</div>';
			return $html;
		}

		/**
		 * Simple guide for ALTCHA
		 */
		private function wbc_simple_altcha_guide() {
			$html = '<div class="wbc-guide-box wbc-guide-altcha">';
			$html .= '<h3>🔑 ' . esc_html__( 'How to Configure ALTCHA', 'buddypress-recaptcha' ) . '</h3>';
			$html .= '<p>' . esc_html__( 'ALTCHA is a self-hosted solution that doesn\'t require external API keys.', 'buddypress-recaptcha' ) . '</p>';
			$html .= '<ol class="wbc-guide-steps">';
			$html .= '<li>' . esc_html__( 'Click the "Generate HMAC Key" button above', 'buddypress-recaptcha' ) . '</li>';
			$html .= '<li>' . esc_html__( 'The key will be automatically generated and filled', 'buddypress-recaptcha' ) . '</li>';
			$html .= '<li>' . esc_html__( 'Save the settings', 'buddypress-recaptcha' ) . '</li>';
			$html .= '<li>' . esc_html__( 'ALTCHA will work immediately without external services', 'buddypress-recaptcha' ) . '</li>';
			$html .= '</ol>';
			$html .= '<p class="wbc-guide-warning">⚠️ ' . esc_html__( 'Important: ALTCHA requires HTTPS to function properly. Make sure your site uses SSL.', 'buddypress-recaptcha' ) . '</p>';
			$html .= '</div>';
			return $html;
		}

		/**
		 * Get service-specific documentation HTML
		 *
		 * @param string $service Active service identifier
		 * @return string HTML content for service documentation
		 */
		private function wbc_service_documentation( $service ) {
			// Common container for all services
			$html = '<div class="wbc-service-docs-container" style="margin: 20px 0;">';

			// Service-specific documentation
			$docs = array(
				'recaptcha-v2' => $this->wbc_recaptcha_v2_docs(),
				'recaptcha-v3' => $this->wbc_recaptcha_v3_docs(),
				'turnstile' => $this->wbc_turnstile_docs(),
				'hcaptcha' => $this->wbc_hcaptcha_docs(),
				'altcha' => $this->wbc_altcha_docs(),
			);

			// Show documentation for each service with dynamic visibility
			foreach ( $docs as $service_id => $doc_content ) {
				$style = ( $service_id === $service ) ? '' : 'display: none;';
				$html .= '<div class="wbc-service-docs wbc-service-docs-' . esc_attr( $service_id ) . '" style="' . esc_attr( $style ) . '">';
				$html .= $doc_content;
				$html .= '</div>';
			}

			$html .= '</div>';

			return $html;
		}

		/**
		 * Get Google reCAPTCHA v2 documentation
		 */
		private function wbc_recaptcha_v2_docs() {
			$html = '<div class="wbc-docs-content">';

			// Setup instructions
			$html .= '<h3>' . esc_html__( 'Google reCAPTCHA v2 Setup Instructions', 'buddypress-recaptcha' ) . '</h3>';
			$html .= '<ol style="margin: 15px 0; padding-left: 20px;">';
			$html .= '<li>' . sprintf(
				/* translators: %s: link to Google reCAPTCHA */
				esc_html__( 'Visit %s and sign in with your Google account', 'buddypress-recaptcha' ),
				'<a href="https://www.google.com/recaptcha/admin" target="_blank">Google reCAPTCHA Admin Console</a>'
			) . '</li>';
			$html .= '<li>' . esc_html__( 'Click the "+" button to create a new site', 'buddypress-recaptcha' ) . '</li>';
			$html .= '<li>' . esc_html__( 'Enter a label for your site (e.g., "My WordPress Site")', 'buddypress-recaptcha' ) . '</li>';
			$html .= '<li>' . esc_html__( 'Select "reCAPTCHA v2" and choose "I\'m not a robot Checkbox"', 'buddypress-recaptcha' ) . '</li>';
			$html .= '<li>' . sprintf(
				/* translators: %s: current domain */
				esc_html__( 'Add your domain: %s', 'buddypress-recaptcha' ),
				'<code>' . esc_html( wp_parse_url( home_url(), PHP_URL_HOST ) ) . '</code>'
			) . '</li>';
			$html .= '<li>' . esc_html__( 'Accept the Terms of Service and click "Submit"', 'buddypress-recaptcha' ) . '</li>';
			$html .= '<li>' . esc_html__( 'Copy your Site Key and Secret Key to the fields above', 'buddypress-recaptcha' ) . '</li>';
			$html .= '<li>' . esc_html__( 'Save your settings and test the integration', 'buddypress-recaptcha' ) . '</li>';
			$html .= '</ol>';

			// Features info
			$html .= '<div style="background: #f0f8ff; padding: 15px; border-radius: 5px; margin: 20px 0;">';
			$html .= '<h4 style="margin-top: 0;">' . esc_html__( 'Key Features:', 'buddypress-recaptcha' ) . '</h4>';
			$html .= '<ul style="margin: 10px 0; padding-left: 20px;">';
			$html .= '<li>' . esc_html__( 'User-friendly checkbox interface', 'buddypress-recaptcha' ) . '</li>';
			$html .= '<li>' . esc_html__( 'Image challenges only when needed', 'buddypress-recaptcha' ) . '</li>';
			$html .= '<li>' . esc_html__( 'High accessibility with audio challenges', 'buddypress-recaptcha' ) . '</li>';
			$html .= '<li>' . esc_html__( 'Mobile-responsive design', 'buddypress-recaptcha' ) . '</li>';
			$html .= '</ul>';
			$html .= '</div>';

			// Action buttons
			$html .= '<div class="wbc-doc-actions" style="margin-top: 20px;">';
			$html .= '<button type="button" class="button button-primary" onclick="wbc_test_captcha_connection()">' .
				esc_html__( 'Test Connection', 'buddypress-recaptcha' ) . '</button> ';
			$html .= sprintf(
				'<a href="%s" class="button button-secondary">%s</a> ',
				esc_url( admin_url( 'admin.php?page=buddypress-recaptcha&tab=protection' ) ),
				esc_html__( 'Configure Protected Forms', 'buddypress-recaptcha' )
			);
			$html .= sprintf(
				'<a href="%s" target="_blank" class="button button-secondary">%s</a>',
				esc_url( 'https://developers.google.com/recaptcha/docs/display' ),
				esc_html__( 'View Official Documentation', 'buddypress-recaptcha' )
			);
			$html .= '</div>';

			$html .= '</div>';
			return $html;
		}

		/**
		 * Get Google reCAPTCHA v3 documentation
		 */
		private function wbc_recaptcha_v3_docs() {
			$html = '<div class="wbc-docs-content">';

			$html .= '<h3>' . esc_html__( 'Google reCAPTCHA v3 Setup Instructions', 'buddypress-recaptcha' ) . '</h3>';
			$html .= '<ol style="margin: 15px 0; padding-left: 20px;">';
			$html .= '<li>' . sprintf(
				/* translators: %s: link to Google reCAPTCHA */
				esc_html__( 'Visit %s and sign in with your Google account', 'buddypress-recaptcha' ),
				'<a href="https://www.google.com/recaptcha/admin" target="_blank">Google reCAPTCHA Admin Console</a>'
			) . '</li>';
			$html .= '<li>' . esc_html__( 'Click the "+" button to create a new site', 'buddypress-recaptcha' ) . '</li>';
			$html .= '<li>' . esc_html__( 'Select "reCAPTCHA v3" for invisible protection', 'buddypress-recaptcha' ) . '</li>';
			$html .= '<li>' . sprintf(
				/* translators: %s: current domain */
				esc_html__( 'Add your domain: %s', 'buddypress-recaptcha' ),
				'<code>' . esc_html( wp_parse_url( home_url(), PHP_URL_HOST ) ) . '</code>'
			) . '</li>';
			$html .= '<li>' . esc_html__( 'Copy your Site Key and Secret Key to the fields above', 'buddypress-recaptcha' ) . '</li>';
			$html .= '<li>' . esc_html__( 'Set your Score Threshold (0.5 is recommended to start)', 'buddypress-recaptcha' ) . '</li>';
			$html .= '</ol>';

			// Score explanation
			$html .= '<div style="background: #fff3cd; padding: 15px; border-radius: 5px; margin: 20px 0;">';
			$html .= '<h4 style="margin-top: 0;">' . esc_html__( 'Understanding Score Thresholds:', 'buddypress-recaptcha' ) . '</h4>';
			$html .= '<ul style="margin: 10px 0; padding-left: 20px;">';
			$html .= '<li><strong>1.0</strong> - ' . esc_html__( 'Very likely a good interaction', 'buddypress-recaptcha' ) . '</li>';
			$html .= '<li><strong>0.5</strong> - ' . esc_html__( 'Balanced threshold (recommended)', 'buddypress-recaptcha' ) . '</li>';
			$html .= '<li><strong>0.0</strong> - ' . esc_html__( 'Very likely a bot', 'buddypress-recaptcha' ) . '</li>';
			$html .= '</ul>';
			$html .= '<p style="margin-bottom: 0;">' . esc_html__( 'Lower thresholds = stricter validation. Start with 0.5 and adjust based on your needs.', 'buddypress-recaptcha' ) . '</p>';
			$html .= '</div>';

			// Action buttons
			$html .= '<div class="wbc-doc-actions" style="margin-top: 20px;">';
			$html .= '<button type="button" class="button button-primary" onclick="wbc_test_captcha_connection()">' .
				esc_html__( 'Test Connection', 'buddypress-recaptcha' ) . '</button> ';
			$html .= sprintf(
				'<a href="%s" class="button button-secondary">%s</a> ',
				esc_url( admin_url( 'admin.php?page=buddypress-recaptcha&tab=protection' ) ),
				esc_html__( 'Configure Protected Forms', 'buddypress-recaptcha' )
			);
			$html .= sprintf(
				'<a href="%s" target="_blank" class="button button-secondary">%s</a>',
				esc_url( 'https://developers.google.com/recaptcha/docs/v3' ),
				esc_html__( 'View Official Documentation', 'buddypress-recaptcha' )
			);
			$html .= '</div>';

			$html .= '</div>';
			return $html;
		}

		/**
		 * Get Cloudflare Turnstile documentation
		 */
		private function wbc_turnstile_docs() {
			$html = '<div class="wbc-docs-content">';

			$html .= '<h3>' . esc_html__( 'Cloudflare Turnstile Setup Instructions', 'buddypress-recaptcha' ) . '</h3>';
			$html .= '<ol style="margin: 15px 0; padding-left: 20px;">';
			$html .= '<li>' . sprintf(
				/* translators: %s: link to Cloudflare Dashboard */
				esc_html__( 'Visit %s and sign in or create account', 'buddypress-recaptcha' ),
				'<a href="https://dash.cloudflare.com/?to=/:account/turnstile" target="_blank">Cloudflare Dashboard</a>'
			) . '</li>';
			$html .= '<li>' . esc_html__( 'Navigate to Turnstile in the sidebar', 'buddypress-recaptcha' ) . '</li>';
			$html .= '<li>' . esc_html__( 'Click "Add Site" to create a new widget', 'buddypress-recaptcha' ) . '</li>';
			$html .= '<li>' . esc_html__( 'Enter a site name and select "Managed" widget mode', 'buddypress-recaptcha' ) . '</li>';
			$html .= '<li>' . sprintf(
				/* translators: %s: current domain */
				esc_html__( 'Add your domain: %s', 'buddypress-recaptcha' ),
				'<code>' . esc_html( wp_parse_url( home_url(), PHP_URL_HOST ) ) . '</code>'
			) . '</li>';
			$html .= '<li>' . esc_html__( 'Copy the Site Key and Secret Key to the fields above', 'buddypress-recaptcha' ) . '</li>';
			$html .= '</ol>';

			// Features
			$html .= '<div style="background: #e8f5e9; padding: 15px; border-radius: 5px; margin: 20px 0;">';
			$html .= '<h4 style="margin-top: 0;">' . esc_html__( 'Why Choose Turnstile?', 'buddypress-recaptcha' ) . '</h4>';
			$html .= '<ul style="margin: 10px 0; padding-left: 20px;">';
			$html .= '<li>' . esc_html__( 'Privacy-focused: No user tracking', 'buddypress-recaptcha' ) . '</li>';
			$html .= '<li>' . esc_html__( 'GDPR compliant by design', 'buddypress-recaptcha' ) . '</li>';
			$html .= '<li>' . esc_html__( 'Free tier with generous limits', 'buddypress-recaptcha' ) . '</li>';
			$html .= '<li>' . esc_html__( 'Fast, non-intrusive challenges', 'buddypress-recaptcha' ) . '</li>';
			$html .= '<li>' . esc_html__( 'Works globally without CAPTCHAs', 'buddypress-recaptcha' ) . '</li>';
			$html .= '</ul>';
			$html .= '</div>';

			// Action buttons
			$html .= '<div class="wbc-doc-actions" style="margin-top: 20px;">';
			$html .= '<button type="button" class="button button-primary" onclick="wbc_test_captcha_connection()">' .
				esc_html__( 'Test Connection', 'buddypress-recaptcha' ) . '</button> ';
			$html .= sprintf(
				'<a href="%s" class="button button-secondary">%s</a> ',
				esc_url( admin_url( 'admin.php?page=buddypress-recaptcha&tab=protection' ) ),
				esc_html__( 'Configure Protected Forms', 'buddypress-recaptcha' )
			);
			$html .= sprintf(
				'<a href="%s" target="_blank" class="button button-secondary">%s</a>',
				esc_url( 'https://developers.cloudflare.com/turnstile/' ),
				esc_html__( 'View Official Documentation', 'buddypress-recaptcha' )
			);
			$html .= '</div>';

			$html .= '</div>';
			return $html;
		}

		/**
		 * Get hCaptcha documentation
		 */
		private function wbc_hcaptcha_docs() {
			$html = '<div class="wbc-docs-content">';

			$html .= '<h3>' . esc_html__( 'hCaptcha Setup Instructions', 'buddypress-recaptcha' ) . '</h3>';
			$html .= '<ol style="margin: 15px 0; padding-left: 20px;">';
			$html .= '<li>' . sprintf(
				/* translators: %s: link to hCaptcha */
				esc_html__( 'Visit %s and create an account', 'buddypress-recaptcha' ),
				'<a href="https://dashboard.hcaptcha.com/signup" target="_blank">hCaptcha Dashboard</a>'
			) . '</li>';
			$html .= '<li>' . esc_html__( 'Click "New Site" in your dashboard', 'buddypress-recaptcha' ) . '</li>';
			$html .= '<li>' . sprintf(
				/* translators: %s: current domain */
				esc_html__( 'Add your domain: %s', 'buddypress-recaptcha' ),
				'<code>' . esc_html( wp_parse_url( home_url(), PHP_URL_HOST ) ) . '</code>'
			) . '</li>';
			$html .= '<li>' . esc_html__( 'Choose your difficulty level (Moderate recommended)', 'buddypress-recaptcha' ) . '</li>';
			$html .= '<li>' . esc_html__( 'Copy the Site Key and Secret Key to the fields above', 'buddypress-recaptcha' ) . '</li>';
			$html .= '</ol>';

			// Features
			$html .= '<div style="background: #fce4ec; padding: 15px; border-radius: 5px; margin: 20px 0;">';
			$html .= '<h4 style="margin-top: 0;">' . esc_html__( 'hCaptcha Benefits:', 'buddypress-recaptcha' ) . '</h4>';
			$html .= '<ul style="margin: 10px 0; padding-left: 20px;">';
			$html .= '<li>' . esc_html__( 'Privacy-focused alternative to Google', 'buddypress-recaptcha' ) . '</li>';
			$html .= '<li>' . esc_html__( 'Earn rewards for your website traffic', 'buddypress-recaptcha' ) . '</li>';
			$html .= '<li>' . esc_html__( 'GDPR and CCPA compliant', 'buddypress-recaptcha' ) . '</li>';
			$html .= '<li>' . esc_html__( 'Accessibility features included', 'buddypress-recaptcha' ) . '</li>';
			$html .= '</ul>';
			$html .= '</div>';

			// Action buttons
			$html .= '<div class="wbc-doc-actions" style="margin-top: 20px;">';
			$html .= '<button type="button" class="button button-primary" onclick="wbc_test_captcha_connection()">' .
				esc_html__( 'Test Connection', 'buddypress-recaptcha' ) . '</button> ';
			$html .= sprintf(
				'<a href="%s" class="button button-secondary">%s</a> ',
				esc_url( admin_url( 'admin.php?page=buddypress-recaptcha&tab=protection' ) ),
				esc_html__( 'Configure Protected Forms', 'buddypress-recaptcha' )
			);
			$html .= sprintf(
				'<a href="%s" target="_blank" class="button button-secondary">%s</a>',
				esc_url( 'https://docs.hcaptcha.com/' ),
				esc_html__( 'View Official Documentation', 'buddypress-recaptcha' )
			);
			$html .= '</div>';

			$html .= '</div>';
			return $html;
		}

		/**
		 * Get ALTCHA documentation
		 */
		private function wbc_altcha_docs() {
			$html = '<div class="wbc-docs-content">';

			$html .= '<h3>' . esc_html__( 'ALTCHA Setup Instructions', 'buddypress-recaptcha' ) . '</h3>';

			// HTTPS warning
			$html .= '<div style="background: #ffebee; padding: 15px; border-radius: 5px; margin: 15px 0; border-left: 4px solid #f44336;">';
			$html .= '<strong>' . esc_html__( '⚠️ HTTPS Required:', 'buddypress-recaptcha' ) . '</strong> ';
			$html .= esc_html__( 'ALTCHA requires a secure HTTPS connection to work properly due to Web Crypto API requirements.', 'buddypress-recaptcha' );
			$html .= '</div>';

			$html .= '<ol style="margin: 15px 0; padding-left: 20px;">';
			$html .= '<li>' . esc_html__( 'Click "Generate Random Key" button above to create a secure HMAC key', 'buddypress-recaptcha' ) . '</li>';
			$html .= '<li>' . esc_html__( 'Choose your complexity level (Medium recommended for most sites)', 'buddypress-recaptcha' ) . '</li>';
			$html .= '<li>' . esc_html__( 'Save your settings', 'buddypress-recaptcha' ) . '</li>';
			$html .= '<li>' . esc_html__( 'No external registration required!', 'buddypress-recaptcha' ) . '</li>';
			$html .= '</ol>';

			// Features
			$html .= '<div style="background: #f3e5f5; padding: 15px; border-radius: 5px; margin: 20px 0;">';
			$html .= '<h4 style="margin-top: 0;">' . esc_html__( 'Why ALTCHA?', 'buddypress-recaptcha' ) . '</h4>';
			$html .= '<ul style="margin: 10px 0; padding-left: 20px;">';
			$html .= '<li>' . esc_html__( 'Complete privacy: No external API calls', 'buddypress-recaptcha' ) . '</li>';
			$html .= '<li>' . esc_html__( 'Self-hosted: All processing on your server', 'buddypress-recaptcha' ) . '</li>';
			$html .= '<li>' . esc_html__( 'No user tracking or data collection', 'buddypress-recaptcha' ) . '</li>';
			$html .= '<li>' . esc_html__( 'Proof-of-work based protection', 'buddypress-recaptcha' ) . '</li>';
			$html .= '<li>' . esc_html__( 'Free forever - no API limits', 'buddypress-recaptcha' ) . '</li>';
			$html .= '</ul>';

			// Complexity explanation
			$html .= '<h4>' . esc_html__( 'Complexity Levels:', 'buddypress-recaptcha' ) . '</h4>';
			$html .= '<ul style="margin: 10px 0; padding-left: 20px;">';
			$html .= '<li><strong>' . esc_html__( 'Easy (50,000):', 'buddypress-recaptcha' ) . '</strong> ' .
				esc_html__( 'Quick solve, suitable for low-risk forms', 'buddypress-recaptcha' ) . '</li>';
			$html .= '<li><strong>' . esc_html__( 'Medium (100,000):', 'buddypress-recaptcha' ) . '</strong> ' .
				esc_html__( 'Balanced protection (recommended)', 'buddypress-recaptcha' ) . '</li>';
			$html .= '<li><strong>' . esc_html__( 'Hard (200,000):', 'buddypress-recaptcha' ) . '</strong> ' .
				esc_html__( 'Strong protection, longer solve time', 'buddypress-recaptcha' ) . '</li>';
			$html .= '</ul>';
			$html .= '</div>';

			// Action buttons
			$html .= '<div class="wbc-doc-actions" style="margin-top: 20px;">';
			$html .= sprintf(
				'<a href="%s" class="button button-primary">%s</a> ',
				esc_url( admin_url( 'admin.php?page=buddypress-recaptcha&tab=protection' ) ),
				esc_html__( 'Configure Protected Forms', 'buddypress-recaptcha' )
			);
			$html .= sprintf(
				'<a href="%s" target="_blank" class="button button-secondary">%s</a>',
				esc_url( 'https://altcha.org/docs/' ),
				esc_html__( 'View ALTCHA Documentation', 'buddypress-recaptcha' )
			);
			$html .= '</div>';

			$html .= '</div>';
			return $html;
		}

		/**
		 * Get available captcha services dynamically
		 *
		 * @return array Service ID => Service Name
		 */
		private function wbc_available_services() {
			$services = array();

			if ( function_exists( 'wbc_captcha_service_manager' ) ) {
				$service_manager = wbc_captcha_service_manager();
				$registered_services = $service_manager->get_services();

				foreach ( $registered_services as $service_id => $service ) {
					$services[ $service_id ] = $service->get_service_name();
				}
			}

			// Fallback if service manager is not available
			if ( empty( $services ) ) {
				$services = array(
					'recaptcha-v2' => __( 'Google reCAPTCHA v2 (Checkbox)', 'buddypress-recaptcha' ),
					'recaptcha-v3' => __( 'Google reCAPTCHA v3 (Invisible)', 'buddypress-recaptcha' ),
					'turnstile'    => __( 'Cloudflare Turnstile', 'buddypress-recaptcha' ),
				);
			}

			return $services;
		}

		/**
		 * Get sections
		 *
		 * @return array
		 */
		public function wbc_sections() {
			$sections = array(
				''                => __( 'Service Configuration', 'buddypress-recaptcha' ),
			);
			
			// Add plugin-specific integration sections
			$sections['wordpress'] = __( 'WordPress', 'buddypress-recaptcha' );
			
			if ( class_exists( 'WooCommerce' ) ) {
				$sections['woocommerce'] = __( 'WooCommerce', 'buddypress-recaptcha' );
			}
			
			if ( class_exists( 'BuddyPress' ) ) {
				$sections['buddypress'] = __( 'BuddyPress', 'buddypress-recaptcha' );
			}
			
			if ( class_exists( 'bbPress' ) ) {
				$sections['bbpress'] = __( 'bbPress', 'buddypress-recaptcha' );
			}
			
			$sections['appearance'] = __( 'Appearance', 'buddypress-recaptcha' );
			$sections['advanced'] = __( 'Advanced', 'buddypress-recaptcha' );

			return apply_filters( 'wbc_recaptcha_settings_sections', $sections );
		}

		/**
		 * Output sections
		 */
		public function wbc_output_sections() {
			global $current_section;

			$sections = $this->wbc_sections();

			if ( empty( $sections ) || 1 === count( $sections ) ) {
				return;
			}

			echo '<ul class="subsubsub">';

			$array_keys = array_keys( $sections );

			foreach ( $sections as $id => $label ) {
				echo '<li>';
				echo '<a href="' . esc_url( admin_url( 'admin.php?page=wbc-recaptcha-page&tab=' . $this->id . '&section=' . sanitize_title( $id ) ) ) . '" 
					class="' . ( $current_section === $id ? 'current' : '' ) . '">' . esc_html( $label ) . '</a>';
				echo ( end( $array_keys ) === $id ? '' : ' | ' );
				echo '</li>';
			}

			echo '</ul><br class="clear" />';
		}

		/**
		 * Get settings for the default section (Service Configuration)
		 *
		 * @return array
		 */
		public function wbc_service_settings() {
			// Get active service for dynamic settings
			$active_service = '';
			if ( function_exists( 'wbc_captcha_service_manager' ) ) {
				$service = wbc_captcha_service_manager()->get_active_service();
				if ( $service ) {
					$active_service = $service->get_service_id();
				}
			}

			$settings = array(
				array(
					'name' => __( 'Captcha Service Selection', 'buddypress-recaptcha' ),
					'type' => 'title',
					'id'   => 'wbc_captcha_service_selection',
				),

				array(
					'name'    => __( 'Active Captcha Service', 'buddypress-recaptcha' ),
					'type'    => 'select',
					'id'      => 'wbc_captcha_service',
					'options' => $this->wbc_available_services(),
					'default' => 'recaptcha-v2',
					'desc'    => __( 'Select which captcha service to use across your site', 'buddypress-recaptcha' ),
				),

				array(
					'type' => 'sectionend',
					'id'   => 'wbc_captcha_service_selection',
				),

				// reCAPTCHA v2 Settings
				array(
					'name'  => __( 'Google reCAPTCHA v2 Settings', 'buddypress-recaptcha' ),
					'type'  => 'title',
					'id'    => 'wbc_recaptcha_v2_settings',
					'class' => 'wbc-service-settings wbc-service-recaptcha-v2',
				),

				array(
					'name'        => __( 'Site Key', 'buddypress-recaptcha' ),
					'type'        => 'text',
					'id'          => 'wbc_recaptcha_v2_site_key',
					'desc'        => __( 'Enter your Google reCAPTCHA v2 site key', 'buddypress-recaptcha' ),
					'placeholder' => __( 'Your site key', 'buddypress-recaptcha' ),
					'class'       => 'wbc-service-field wbc-service-recaptcha-v2',
				),

				array(
					'name'        => __( 'Secret Key', 'buddypress-recaptcha' ),
					'type'        => 'password',
					'id'          => 'wbc_recaptcha_v2_secret_key',
					'desc'        => __( 'Enter your Google reCAPTCHA v2 secret key', 'buddypress-recaptcha' ),
					'placeholder' => __( 'Your secret key', 'buddypress-recaptcha' ),
					'class'       => 'wbc-service-field wbc-service-recaptcha-v2',
				),

				array(
					'type'  => 'sectionend',
					'id'    => 'wbc_recaptcha_v2_settings',
					'class' => 'wbc-service-settings wbc-service-recaptcha-v2',
				),

				// reCAPTCHA v3 Settings
				array(
					'name'  => __( 'Google reCAPTCHA v3 Settings', 'buddypress-recaptcha' ),
					'type'  => 'title',
					'id'    => 'wbc_recaptcha_v3_settings',
					'class' => 'wbc-service-settings wbc-service-recaptcha-v3',
				),

				array(
					'name'        => __( 'Site Key', 'buddypress-recaptcha' ),
					'type'        => 'text',
					'id'          => 'wbc_recaptcha_v3_site_key',
					'desc'        => __( 'Enter your Google reCAPTCHA v3 site key', 'buddypress-recaptcha' ),
					'placeholder' => __( 'Your site key', 'buddypress-recaptcha' ),
					'class'       => 'wbc-service-field wbc-service-recaptcha-v3',
				),

				array(
					'name'        => __( 'Secret Key', 'buddypress-recaptcha' ),
					'type'        => 'password',
					'id'          => 'wbc_recaptcha_v3_secret_key',
					'desc'        => __( 'Enter your Google reCAPTCHA v3 secret key', 'buddypress-recaptcha' ),
					'placeholder' => __( 'Your secret key', 'buddypress-recaptcha' ),
					'class'       => 'wbc-service-field wbc-service-recaptcha-v3',
				),

				array(
					'name'    => __( 'Score Threshold', 'buddypress-recaptcha' ),
					'type'    => 'number',
					'id'      => 'wbc_recaptcha_v3_score_threshold',
					'desc'    => __( 'Set the minimum score (0.0 to 1.0) to pass verification. Higher scores mean stricter validation.', 'buddypress-recaptcha' ),
					'default' => '0.5',
					'custom_attributes' => array(
						'min'  => '0',
						'max'  => '1',
						'step' => '0.1',
					),
					'class'   => 'wbc-service-field wbc-service-recaptcha-v3',
				),

				array(
					'type'  => 'sectionend',
					'id'    => 'wbc_recaptcha_v3_settings',
					'class' => 'wbc-service-settings wbc-service-recaptcha-v3',
				),

				// Turnstile Settings
				array(
					'name'  => __( 'Cloudflare Turnstile Settings', 'buddypress-recaptcha' ),
					'type'  => 'title',
					'id'    => 'wbc_turnstile_settings',
					'class' => 'wbc-service-settings wbc-service-turnstile',
				),

				array(
					'name'        => __( 'Site Key', 'buddypress-recaptcha' ),
					'type'        => 'text',
					'id'          => 'wbc_turnstile_site_key',
					'desc'        => __( 'Enter your Cloudflare Turnstile site key', 'buddypress-recaptcha' ),
					'placeholder' => __( 'Your site key', 'buddypress-recaptcha' ),
					'class'       => 'wbc-service-field wbc-service-turnstile',
				),

				array(
					'name'        => __( 'Secret Key', 'buddypress-recaptcha' ),
					'type'        => 'password',
					'id'          => 'wbc_turnstile_secret_key',
					'desc'        => __( 'Enter your Cloudflare Turnstile secret key', 'buddypress-recaptcha' ),
					'placeholder' => __( 'Your secret key', 'buddypress-recaptcha' ),
					'class'       => 'wbc-service-field wbc-service-turnstile',
				),

				array(
					'type'  => 'sectionend',
					'id'    => 'wbc_turnstile_settings',
					'class' => 'wbc-service-settings wbc-service-turnstile',
				),

				// ALTCHA Settings
				array(
					'name'  => __( 'ALTCHA Settings', 'buddypress-recaptcha' ),
					'type'  => 'title',
					'id'    => 'wbc_altcha_settings',
					'desc'  => __( 'Configure ALTCHA - Privacy-first, self-hosted captcha. No external API required.<br><strong>⚠️ HTTPS Required:</strong> ALTCHA requires a secure context (HTTPS) to work due to Web Crypto API requirements.', 'buddypress-recaptcha' ),
					'class' => 'wbc-service-settings wbc-service-altcha',
				),

				array(
					'name'        => __( 'HMAC Key', 'buddypress-recaptcha' ),
					'type'        => 'text',
					'id'          => 'wbc_altcha_hmac_key',
					'desc'        => __( 'Enter a secret HMAC key for challenge signing. <button type="button" class="button button-secondary wbc-generate-hmac-key" style="margin-left: 10px;">Generate Random Key</button>', 'buddypress-recaptcha' ),
					'placeholder' => __( 'Your HMAC secret key (32+ characters recommended)', 'buddypress-recaptcha' ),
					'class'       => 'wbc-service-field wbc-service-altcha wbc-altcha-hmac-input',
				),

				array(
					'name'    => __( 'Complexity (Max Number)', 'buddypress-recaptcha' ),
					'type'    => 'number',
					'id'      => 'wbc_altcha_max_number',
					'desc'    => __( 'Maximum number for proof-of-work challenge (higher = harder, recommended: 50000-100000)', 'buddypress-recaptcha' ),
					'default' => '100000',
					'class'   => 'wbc-service-field wbc-service-altcha',
				),

				array(
					'name'    => __( 'Challenge Expiration', 'buddypress-recaptcha' ),
					'type'    => 'number',
					'id'      => 'wbc_altcha_expires',
					'desc'    => __( 'Time in seconds before challenge expires (recommended: 3600)', 'buddypress-recaptcha' ),
					'default' => '3600',
					'class'   => 'wbc-service-field wbc-service-altcha',
				),

				array(
					'name'    => __( 'Auto Verify', 'buddypress-recaptcha' ),
					'type'    => 'select',
					'id'      => 'wbc_altcha_auto_verify',
					'desc'    => __( 'When to automatically start verification', 'buddypress-recaptcha' ),
					'options' => array(
						'off'      => __( 'Manual (User clicks checkbox)', 'buddypress-recaptcha' ),
						'onload'   => __( 'On page load', 'buddypress-recaptcha' ),
						'onfocus'  => __( 'On form focus', 'buddypress-recaptcha' ),
						'onsubmit' => __( 'On form submit', 'buddypress-recaptcha' ),
					),
					'default' => 'off',
					'class'   => 'wbc-service-field wbc-service-altcha',
				),

				array(
					'name'    => __( 'Hide Logo', 'buddypress-recaptcha' ),
					'type'    => 'select',
					'id'      => 'wbc_altcha_hide_logo',
					'desc'    => __( 'Hide the ALTCHA logo from the widget', 'buddypress-recaptcha' ),
					'options' => array(
						'no'  => __( 'No', 'buddypress-recaptcha' ),
						'yes' => __( 'Yes', 'buddypress-recaptcha' ),
					),
					'default' => 'no',
					'class'   => 'wbc-service-field wbc-service-altcha',
				),

				array(
					'type'  => 'sectionend',
					'id'    => 'wbc_altcha_settings',
					'class' => 'wbc-service-settings wbc-service-altcha',
				),

				// hCaptcha Settings
				array(
					'name'  => __( 'hCaptcha Settings', 'buddypress-recaptcha' ),
					'type'  => 'title',
					'id'    => 'wbc_hcaptcha_settings',
					'desc'  => __( 'Configure hCaptcha - Privacy-focused alternative to reCAPTCHA. Get your keys from <a href="https://www.hcaptcha.com/" target="_blank">hCaptcha.com</a>', 'buddypress-recaptcha' ),
					'class' => 'wbc-service-settings wbc-service-hcaptcha',
				),

				array(
					'name'        => __( 'Site Key', 'buddypress-recaptcha' ),
					'type'        => 'text',
					'id'          => 'wbc_hcaptcha_site_key',
					'desc'        => __( 'Enter your hCaptcha site key (automatically uses hCaptcha plugin settings if installed)', 'buddypress-recaptcha' ),
					'placeholder' => __( 'Your site key', 'buddypress-recaptcha' ),
					'class'       => 'wbc-service-field wbc-service-hcaptcha',
				),

				array(
					'name'        => __( 'Secret Key', 'buddypress-recaptcha' ),
					'type'        => 'password',
					'id'          => 'wbc_hcaptcha_secret_key',
					'desc'        => __( 'Enter your hCaptcha secret key (automatically uses hCaptcha plugin settings if installed)', 'buddypress-recaptcha' ),
					'placeholder' => __( 'Your secret key', 'buddypress-recaptcha' ),
					'class'       => 'wbc-service-field wbc-service-hcaptcha',
				),

				array(
					'name'    => __( 'Theme', 'buddypress-recaptcha' ),
					'type'    => 'select',
					'id'      => 'wbc_hcaptcha_theme',
					'desc'    => __( 'Widget theme', 'buddypress-recaptcha' ),
					'options' => array(
						'light' => __( 'Light', 'buddypress-recaptcha' ),
						'dark'  => __( 'Dark', 'buddypress-recaptcha' ),
					),
					'default' => 'light',
					'class'   => 'wbc-service-field wbc-service-hcaptcha',
				),

				array(
					'name'    => __( 'Size', 'buddypress-recaptcha' ),
					'type'    => 'select',
					'id'      => 'wbc_hcaptcha_size',
					'desc'    => __( 'Widget size', 'buddypress-recaptcha' ),
					'options' => array(
						'normal'  => __( 'Normal', 'buddypress-recaptcha' ),
						'compact' => __( 'Compact', 'buddypress-recaptcha' ),
					),
					'default' => 'normal',
					'class'   => 'wbc-service-field wbc-service-hcaptcha',
				),

				array(
					'type'  => 'sectionend',
					'id'    => 'wbc_hcaptcha_settings',
					'class' => 'wbc-service-settings wbc-service-hcaptcha',
				),
			);

			return apply_filters( 'wbc_recaptcha_service_settings', $settings );
		}

		/**
		 * Get settings for WordPress integration
		 *
		 * @return array
		 */
		public function wbc_wordpress_settings() {
			$settings = array(
				array(
					'name' => __( 'WordPress Core Forms', 'buddypress-recaptcha' ),
					'type' => 'title',
					'id'   => 'wbc_wp_forms_integration',
					'desc' => __( 'Enable reCAPTCHA protection for WordPress native forms', 'buddypress-recaptcha' ),
				),

				array(
					'name'    => __( 'Login Form', 'buddypress-recaptcha' ),
					'type'    => 'checkbox',
					'id'      => 'wbc_recapcha_enable_on_wplogin',
					'desc'    => __( 'Protect WordPress login form from brute force attacks', 'buddypress-recaptcha' ),
					'default' => 'no',
				),

				array(
					'name'    => __( 'Registration Form', 'buddypress-recaptcha' ),
					'type'    => 'checkbox',
					'id'      => 'wbc_recapcha_enable_on_wpregister',
					'desc'    => __( 'Prevent spam registrations on your site', 'buddypress-recaptcha' ),
					'default' => 'no',
				),

				array(
					'name'    => __( 'Lost Password Form', 'buddypress-recaptcha' ),
					'type'    => 'checkbox',
					'id'      => 'wbc_recapcha_enable_on_wplostpassword',
					'desc'    => __( 'Secure password reset requests', 'buddypress-recaptcha' ),
					'default' => 'no',
				),

				array(
					'name'    => __( 'Comment Forms', 'buddypress-recaptcha' ),
					'type'    => 'checkbox',
					'id'      => 'wbc_recapcha_enable_on_comment',
					'desc'    => __( 'Stop spam comments on posts and pages', 'buddypress-recaptcha' ),
					'default' => 'no',
				),

				array(
					'type' => 'sectionend',
					'id'   => 'wbc_wp_forms_integration',
				),
			);

			return apply_filters( 'wbc_recaptcha_wordpress_settings', $settings );
		}

		/**
		 * Get settings for WooCommerce integration
		 *
		 * @return array
		 */
		public function wbc_woocommerce_settings() {
			$settings = array(
				array(
					'name' => __( 'WooCommerce Forms', 'buddypress-recaptcha' ),
					'type' => 'title',
					'id'   => 'wbc_woo_forms_integration',
					'desc' => __( 'Enable reCAPTCHA protection for WooCommerce forms', 'buddypress-recaptcha' ),
				),

				array(
					'name'    => __( 'Login Form', 'buddypress-recaptcha' ),
					'type'    => 'checkbox',
					'id'      => 'wbc_recapcha_enable_on_login',
					'desc'    => __( 'Protect customer login from unauthorized access', 'buddypress-recaptcha' ),
					'default' => 'no',
				),

				array(
					'name'    => __( 'Registration Form', 'buddypress-recaptcha' ),
					'type'    => 'checkbox',
					'id'      => 'wbc_recapcha_enable_on_signup',
					'desc'    => __( 'Prevent fake customer account creation', 'buddypress-recaptcha' ),
					'default' => 'no',
				),

				array(
					'name'    => __( 'Lost Password Form', 'buddypress-recaptcha' ),
					'type'    => 'checkbox',
					'id'      => 'wbc_recapcha_enable_on_lostpassword',
					'desc'    => __( 'Secure password reset for customers', 'buddypress-recaptcha' ),
					'default' => 'no',
				),

				array(
					'name'    => __( 'Guest Checkout', 'buddypress-recaptcha' ),
					'type'    => 'checkbox',
					'id'      => 'wbc_recapcha_enable_on_guestcheckout',
					'desc'    => __( 'Protect checkout from automated bots', 'buddypress-recaptcha' ),
					'default' => 'no',
				),

				array(
					'name'    => __( 'Logged-in User Checkout', 'buddypress-recaptcha' ),
					'type'    => 'checkbox',
					'id'      => 'wbc_recapcha_enable_on_logincheckout',
					'desc'    => __( 'Add extra security for logged-in user checkouts', 'buddypress-recaptcha' ),
					'default' => 'no',
				),

				array(
					'name'    => __( 'Pay for Order', 'buddypress-recaptcha' ),
					'type'    => 'checkbox',
					'id'      => 'wbc_recapcha_enable_on_payfororder',
					'desc'    => __( 'Secure payment forms for existing orders', 'buddypress-recaptcha' ),
					'default' => 'no',
				),

				array(
					'name'    => __( 'Order Tracking', 'buddypress-recaptcha' ),
					'type'    => 'checkbox',
					'id'      => 'wbc_recapcha_enable_on_order_tracking',
					'desc'    => __( 'Protect order tracking from automated queries', 'buddypress-recaptcha' ),
					'default' => 'no',
				),

				array(
					'type' => 'sectionend',
					'id'   => 'wbc_woo_forms_integration',
				),
			);

			return apply_filters( 'wbc_recaptcha_woocommerce_settings', $settings );
		}

		/**
		 * Get settings for BuddyPress integration
		 *
		 * @return array
		 */
		public function wbc_buddypress_settings() {
			$settings = array(
				array(
					'name' => __( 'BuddyPress Forms', 'buddypress-recaptcha' ),
					'type' => 'title',
					'id'   => 'wbc_bp_forms_integration',
					'desc' => __( 'Enable reCAPTCHA protection for BuddyPress community features', 'buddypress-recaptcha' ),
				),

				array(
					'name'    => __( 'Member Registration', 'buddypress-recaptcha' ),
					'type'    => 'checkbox',
					'id'      => 'wbc_recapcha_enable_on_buddypress',
					'desc'    => __( 'Protect community registration from spam accounts', 'buddypress-recaptcha' ),
					'default' => 'no',
				),

				array(
					'type' => 'sectionend',
					'id'   => 'wbc_bp_forms_integration',
				),
			);

			return apply_filters( 'wbc_recaptcha_buddypress_settings', $settings );
		}

		/**
		 * Get settings for bbPress integration
		 *
		 * @return array
		 */
		public function wbc_bbpress_settings() {
			$settings = array(
				array(
					'name' => __( 'bbPress Forum Protection', 'buddypress-recaptcha' ),
					'type' => 'title',
					'id'   => 'wbc_bbpress_forms_integration',
					'desc' => __( 'Enable reCAPTCHA protection for forum activities', 'buddypress-recaptcha' ),
				),

				array(
					'name'    => __( 'New Topic Creation', 'buddypress-recaptcha' ),
					'type'    => 'checkbox',
					'id'      => 'wbc_recapcha_enable_on_bbpress_topic',
					'desc'    => __( 'Prevent spam topics in your forums', 'buddypress-recaptcha' ),
					'default' => 'no',
				),

				array(
					'name'    => __( 'Topic Replies', 'buddypress-recaptcha' ),
					'type'    => 'checkbox',
					'id'      => 'wbc_recapcha_enable_on_bbpress_reply',
					'desc'    => __( 'Stop automated spam replies', 'buddypress-recaptcha' ),
					'default' => 'no',
				),

				array(
					'type' => 'sectionend',
					'id'   => 'wbc_bbpress_forms_integration',
				),
			);

			return apply_filters( 'wbc_recaptcha_bbpress_settings', $settings );
		}

		/**
		 * Get appearance settings
		 *
		 * @return array
		 */
		public function wbc_appearance_settings() {
			$settings = array(
				array(
					'name' => __( 'Global Appearance Settings', 'buddypress-recaptcha' ),
					'type' => 'title',
					'id'   => 'wbc_appearance_settings',
					'desc' => __( 'These settings apply to all enabled forms', 'buddypress-recaptcha' ),
				),

				// For reCAPTCHA v2
				array(
					'name'    => __( 'Theme (reCAPTCHA v2)', 'buddypress-recaptcha' ),
					'type'    => 'select',
					'id'      => 'wbc_recaptcha_theme',
					'options' => array(
						'light' => __( 'Light', 'buddypress-recaptcha' ),
						'dark'  => __( 'Dark', 'buddypress-recaptcha' ),
					),
					'default' => 'light',
					'desc'    => __( 'Color theme for reCAPTCHA v2 widget', 'buddypress-recaptcha' ),
					'class'   => 'wbc-service-specific wbc-service-recaptcha-v2',
				),

				array(
					'name'    => __( 'Size (reCAPTCHA v2)', 'buddypress-recaptcha' ),
					'type'    => 'select',
					'id'      => 'wbc_recaptcha_size',
					'options' => array(
						'normal'  => __( 'Normal', 'buddypress-recaptcha' ),
						'compact' => __( 'Compact', 'buddypress-recaptcha' ),
					),
					'default' => 'normal',
					'desc'    => __( 'Size of the reCAPTCHA v2 widget', 'buddypress-recaptcha' ),
					'class'   => 'wbc-service-specific wbc-service-recaptcha-v2',
				),

				array(
					'name'    => __( 'Badge Position (reCAPTCHA v3)', 'buddypress-recaptcha' ),
					'type'    => 'select',
					'id'      => 'wbc_recaptcha_v3_badge',
					'options' => array(
						'bottomright' => __( 'Bottom Right', 'buddypress-recaptcha' ),
						'bottomleft'  => __( 'Bottom Left', 'buddypress-recaptcha' ),
						'inline'      => __( 'Inline', 'buddypress-recaptcha' ),
					),
					'default' => 'bottomright',
					'desc'    => __( 'Position of the reCAPTCHA v3 badge', 'buddypress-recaptcha' ),
					'class'   => 'wbc-service-specific wbc-service-recaptcha-v3',
				),

				// For Turnstile
				array(
					'name'    => __( 'Theme (Turnstile)', 'buddypress-recaptcha' ),
					'type'    => 'select',
					'id'      => 'wbc_turnstile_theme',
					'options' => array(
						'light' => __( 'Light', 'buddypress-recaptcha' ),
						'dark'  => __( 'Dark', 'buddypress-recaptcha' ),
						'auto'  => __( 'Auto', 'buddypress-recaptcha' ),
					),
					'default' => 'auto',
					'desc'    => __( 'Color theme for Turnstile widget', 'buddypress-recaptcha' ),
					'class'   => 'wbc-service-specific wbc-service-turnstile',
				),

				array(
					'name'    => __( 'Size (Turnstile)', 'buddypress-recaptcha' ),
					'type'    => 'select',
					'id'      => 'wbc_turnstile_size',
					'options' => array(
						'normal'  => __( 'Normal', 'buddypress-recaptcha' ),
						'compact' => __( 'Compact', 'buddypress-recaptcha' ),
					),
					'default' => 'normal',
					'desc'    => __( 'Size of the Turnstile widget', 'buddypress-recaptcha' ),
					'class'   => 'wbc-service-specific wbc-service-turnstile',
				),

				array(
					'name'    => __( 'Language', 'buddypress-recaptcha' ),
					'type'    => 'select',
					'id'      => 'wbc_recaptcha_language',
					'options' => $this->wbc_language_options(),
					'default' => '',
					'desc'    => __( 'Language for captcha widget (leave empty for auto-detect)', 'buddypress-recaptcha' ),
				),

				array(
					'type' => 'sectionend',
					'id'   => 'wbc_appearance_settings',
				),
			);

			return apply_filters( 'wbc_recaptcha_appearance_settings', $settings );
		}

		/**
		 * Get advanced settings
		 *
		 * @return array
		 */
		public function wbc_advanced_settings() {
			$settings = array(
				array(
					'name' => __( 'Advanced Settings', 'buddypress-recaptcha' ),
					'type' => 'title',
					'id'   => 'wbc_advanced_settings',
				),

				array(
					'name'    => __( 'IP Whitelist', 'buddypress-recaptcha' ),
					'type'    => 'textarea',
					'id'      => 'wbc_recaptcha_ip_to_skip_captcha',
					'desc'    => __( 'Enter IP addresses to skip captcha verification (comma-separated)', 'buddypress-recaptcha' ),
					'placeholder' => __( '192.168.1.1, 10.0.0.1', 'buddypress-recaptcha' ),
				),

				array(
					'name'    => __( 'No Conflict Mode', 'buddypress-recaptcha' ),
					'type'    => 'checkbox',
					'id'      => 'wbc_recapcha_no_conflict',
					'desc'    => __( 'Prevent conflicts with other captcha plugins', 'buddypress-recaptcha' ),
					'default' => 'no',
				),

				array(
					'name'    => __( 'Disable Submit Button', 'buddypress-recaptcha' ),
					'type'    => 'checkbox',
					'id'      => 'wbc_recapcha_disable_submitbtn',
					'desc'    => __( 'Disable submit button until captcha is completed (reCAPTCHA v2 only)', 'buddypress-recaptcha' ),
					'default' => 'no',
					'class'   => 'wbc-service-specific wbc-service-recaptcha-v2',
				),

				array(
					'name'    => __( 'Checkout Timeout (minutes)', 'buddypress-recaptcha' ),
					'type'    => 'number',
					'id'      => 'wbc_recapcha_checkout_timeout',
					'desc'    => __( 'Time before checkout captcha revalidation is required (0 to disable)', 'buddypress-recaptcha' ),
					'default' => '3',
					'custom_attributes' => array(
						'min' => '0',
						'max' => '60',
					),
				),

				array(
					'name' => __( 'Error Messages', 'buddypress-recaptcha' ),
					'type' => 'title',
					'id'   => 'wbc_error_messages',
				),

				array(
					'name'        => __( 'Blank Captcha Error', 'buddypress-recaptcha' ),
					'type'        => 'text',
					'id'          => 'wbc_recapcha_error_msg_captcha_blank',
					'desc'        => __( 'Error message when captcha is not completed', 'buddypress-recaptcha' ),
					'default'     => __( 'Please complete the security check.', 'buddypress-recaptcha' ),
					'placeholder' => __( 'Please complete the security check.', 'buddypress-recaptcha' ),
				),

				array(
					'name'        => __( 'Invalid Captcha Error', 'buddypress-recaptcha' ),
					'type'        => 'text',
					'id'          => 'wbc_recapcha_error_msg_captcha_invalid',
					'desc'        => __( 'Error message when captcha verification fails', 'buddypress-recaptcha' ),
					'default'     => __( 'Security verification failed. Please try again.', 'buddypress-recaptcha' ),
					'placeholder' => __( 'Security verification failed. Please try again.', 'buddypress-recaptcha' ),
				),

				array(
					'name'        => __( 'No Response Error', 'buddypress-recaptcha' ),
					'type'        => 'text',
					'id'          => 'wbc_recapcha_error_msg_captcha_no_response',
					'desc'        => __( 'Error message when captcha server doesn\'t respond', 'buddypress-recaptcha' ),
					'default'     => __( 'Could not verify security check. Please refresh and try again.', 'buddypress-recaptcha' ),
					'placeholder' => __( 'Could not verify security check. Please refresh and try again.', 'buddypress-recaptcha' ),
				),

				array(
					'type' => 'sectionend',
					'id'   => 'wbc_error_messages',
				),

				array(
					'type' => 'sectionend',
					'id'   => 'wbc_advanced_settings',
				),
			);

			return apply_filters( 'wbc_recaptcha_advanced_settings', $settings );
		}

		/**
		 * Get language options
		 *
		 * @return array
		 */
		private function wbc_language_options() {
			return array(
				''      => __( 'Auto-detect', 'buddypress-recaptcha' ),
				'ar'    => __( 'Arabic', 'buddypress-recaptcha' ),
				'bg'    => __( 'Bulgarian', 'buddypress-recaptcha' ),
				'ca'    => __( 'Catalan', 'buddypress-recaptcha' ),
				'zh-CN' => __( 'Chinese (Simplified)', 'buddypress-recaptcha' ),
				'zh-TW' => __( 'Chinese (Traditional)', 'buddypress-recaptcha' ),
				'hr'    => __( 'Croatian', 'buddypress-recaptcha' ),
				'cs'    => __( 'Czech', 'buddypress-recaptcha' ),
				'da'    => __( 'Danish', 'buddypress-recaptcha' ),
				'nl'    => __( 'Dutch', 'buddypress-recaptcha' ),
				'en'    => __( 'English', 'buddypress-recaptcha' ),
				'et'    => __( 'Estonian', 'buddypress-recaptcha' ),
				'fil'   => __( 'Filipino', 'buddypress-recaptcha' ),
				'fi'    => __( 'Finnish', 'buddypress-recaptcha' ),
				'fr'    => __( 'French', 'buddypress-recaptcha' ),
				'de'    => __( 'German', 'buddypress-recaptcha' ),
				'el'    => __( 'Greek', 'buddypress-recaptcha' ),
				'iw'    => __( 'Hebrew', 'buddypress-recaptcha' ),
				'hi'    => __( 'Hindi', 'buddypress-recaptcha' ),
				'hu'    => __( 'Hungarian', 'buddypress-recaptcha' ),
				'id'    => __( 'Indonesian', 'buddypress-recaptcha' ),
				'it'    => __( 'Italian', 'buddypress-recaptcha' ),
				'ja'    => __( 'Japanese', 'buddypress-recaptcha' ),
				'ko'    => __( 'Korean', 'buddypress-recaptcha' ),
				'lv'    => __( 'Latvian', 'buddypress-recaptcha' ),
				'lt'    => __( 'Lithuanian', 'buddypress-recaptcha' ),
				'no'    => __( 'Norwegian', 'buddypress-recaptcha' ),
				'fa'    => __( 'Persian', 'buddypress-recaptcha' ),
				'pl'    => __( 'Polish', 'buddypress-recaptcha' ),
				'pt'    => __( 'Portuguese', 'buddypress-recaptcha' ),
				'pt-BR' => __( 'Portuguese (Brazil)', 'buddypress-recaptcha' ),
				'ro'    => __( 'Romanian', 'buddypress-recaptcha' ),
				'ru'    => __( 'Russian', 'buddypress-recaptcha' ),
				'sr'    => __( 'Serbian', 'buddypress-recaptcha' ),
				'sk'    => __( 'Slovak', 'buddypress-recaptcha' ),
				'sl'    => __( 'Slovenian', 'buddypress-recaptcha' ),
				'es'    => __( 'Spanish', 'buddypress-recaptcha' ),
				'sv'    => __( 'Swedish', 'buddypress-recaptcha' ),
				'th'    => __( 'Thai', 'buddypress-recaptcha' ),
				'tr'    => __( 'Turkish', 'buddypress-recaptcha' ),
				'uk'    => __( 'Ukrainian', 'buddypress-recaptcha' ),
				'vi'    => __( 'Vietnamese', 'buddypress-recaptcha' ),
			);
		}

		/**
		 * Output the settings
		 * 
		 * @param string $current Current tab/section
		 */
		public function wbc_output( $current = '' ) {
			// Use the passed parameter or fall back to global
			global $current_section;
			$section = ! empty( $current ) ? $current : $current_section;

			// Get the appropriate settings based on section
			switch ( $section ) {
				case 'rfw-general':
					// Quick Setup tab - combines service selection and API keys
					$settings = $this->wbc_quick_setup_settings();
					break;
				case 'protection':
					// Protection tab - all forms in one place
					$settings = $this->wbc_protection_settings();
					break;
				case 'advanced':
					// Advanced tab - appearance + advanced settings
					$settings = $this->wbc_combined_advanced_settings();
					break;
				// Keep backward compatibility
				case 'wordpress':
					$settings = $this->wbc_wordpress_settings();
					break;
				case 'woocommerce':
					$settings = $this->wbc_woocommerce_settings();
					break;
				case 'buddypress':
					$settings = $this->wbc_buddypress_settings();
					break;
				case 'bbpress':
					$settings = $this->wbc_bbpress_settings();
					break;
				case 'appearance':
					$settings = $this->wbc_appearance_settings();
					break;
				default:
					$settings = $this->wbc_service_settings();
					break;
			}

			// Output the settings
			WBC_Settings_Renderer::output_fields( $settings );
		}

		/**
		 * Save settings
		 * 
		 * @param string $current Current tab/section
		 */
		/**
		 * Manually save API key fields from custom HTML
		 *
		 * Since we're using custom HTML for the API key fields,
		 * they won't be saved by the standard save_fields() method.
		 */
		private function wbc_save_api_key_fields() {
			// Define API key pairs that must be validated together
			$api_key_pairs = array(
				'recaptcha-v2' => array(
					'site'   => 'wbc_recaptcha_v2_site_key',
					'secret' => 'wbc_recaptcha_v2_secret_key',
					'name'   => 'reCAPTCHA v2',
				),
				'recaptcha-v3' => array(
					'site'   => 'wbc_recaptcha_v3_site_key',
					'secret' => 'wbc_recaptcha_v3_secret_key',
					'name'   => 'reCAPTCHA v3',
				),
				'turnstile' => array(
					'site'   => 'wbc_turnstile_site_key',
					'secret' => 'wbc_turnstile_secret_key',
					'name'   => 'Cloudflare Turnstile',
				),
				'hcaptcha' => array(
					'site'   => 'wbc_hcaptcha_site_key',
					'secret' => 'wbc_hcaptcha_secret_key',
					'name'   => 'hCaptcha',
				),
			);

			// Validate API key pairs
			$validation_errors = array();
			foreach ( $api_key_pairs as $service => $keys ) {
				$site_key   = isset( $_POST[ $keys['site'] ] ) ? sanitize_text_field( wp_unslash( $_POST[ $keys['site'] ] ) ) : '';
				$secret_key = isset( $_POST[ $keys['secret'] ] ) ? sanitize_text_field( wp_unslash( $_POST[ $keys['secret'] ] ) ) : '';

				// If either key is provided, both must be provided
				if ( ( ! empty( $site_key ) && empty( $secret_key ) ) || ( empty( $site_key ) && ! empty( $secret_key ) ) ) {
					$validation_errors[] = sprintf(
						/* translators: %s: Service name */
						__( 'Both Site Key and Secret Key are required for %s. Please enter valid API keys or leave both fields empty.', 'buddypress-recaptcha' ),
						$keys['name']
					);
				}
			}

			// Validate ALTCHA HMAC key if it's being submitted as empty
			if ( isset( $_POST['wbc_altcha_hmac_key'] ) ) {
				$altcha_key = sanitize_text_field( wp_unslash( $_POST['wbc_altcha_hmac_key'] ) );
				$current_altcha_key = get_option( 'wbc_altcha_hmac_key', '' );

				// Only validate if user is trying to clear an existing key
				if ( empty( $altcha_key ) && ! empty( $current_altcha_key ) ) {
					$validation_errors[] = __( 'ALTCHA HMAC Key cannot be empty. Please enter a valid key or leave the field unchanged.', 'buddypress-recaptcha' );
				}
			}

			// If validation failed, show errors and return
			if ( ! empty( $validation_errors ) ) {
				foreach ( $validation_errors as $error ) {
					add_settings_error(
						'wbc_recaptcha_messages',
						'wbc_recaptcha_validation_error',
						$error,
						'error'
					);
				}
				return;
			}

			// Define all field IDs that need to be saved
			$field_ids = array(
				// Service selection
				'wbc_captcha_service',
				// reCAPTCHA v2
				'wbc_recaptcha_v2_site_key',
				'wbc_recaptcha_v2_secret_key',
				// reCAPTCHA v3
				'wbc_recaptcha_v3_site_key',
				'wbc_recaptcha_v3_secret_key',
				'wbc_recaptcha_v3_score_threshold',
				// Turnstile
				'wbc_turnstile_site_key',
				'wbc_turnstile_secret_key',
				// hCaptcha
				'wbc_hcaptcha_site_key',
				'wbc_hcaptcha_secret_key',
				// ALTCHA
				'wbc_altcha_hmac_key',
				'wbc_altcha_complexity',
			);

			// Save each field
			foreach ( $field_ids as $field_id ) {
				if ( isset( $_POST[ $field_id ] ) ) {
					$value = sanitize_text_field( wp_unslash( $_POST[ $field_id ] ) );
					update_option( $field_id, $value );
				}
			}

			// Show success message
			add_settings_error(
				'wbc_recaptcha_messages',
				'wbc_recaptcha_message',
				__( 'Settings saved successfully.', 'buddypress-recaptcha' ),
				'updated'
			);
		}

		/**
		 * Save protection checkbox fields from custom HTML
		 */
	private function wbc_save_protection_fields() {
		// Load modular settings system
		require_once plugin_dir_path( __FILE__ ) . 'settings-modules/class-wbc-settings-module-loader.php';

		// Get all checkbox IDs from active modules only
		$checkbox_ids = wbc_settings_module_loader()->get_all_checkbox_ids();

		// Save each checkbox (yes if checked, no if not)
		foreach ( $checkbox_ids as $checkbox_id ) {
			$value = isset( $_POST[ $checkbox_id ] ) ? 'yes' : 'no';
			update_option( $checkbox_id, $value );
		}

		// Show success message
		add_settings_error(
			'wbc_recaptcha_messages',
			'wbc_recaptcha_message',
			__( 'Protection settings saved successfully.', 'buddypress-recaptcha' ),
			'updated'
		);
	}

		public function wbc_save( $current = '' ) {
			// Use the passed parameter or fall back to global
			global $current_section;
			$section = ! empty( $current ) ? $current : $current_section;

			// Get the appropriate settings based on section
			switch ( $section ) {
				case 'rfw-general':
					// Quick Setup tab
					$settings = $this->wbc_quick_setup_settings();
					break;
				case 'protection':
					// Protection tab
					$settings = $this->wbc_protection_settings();
					break;
				case 'advanced':
					// Advanced tab
					$settings = $this->wbc_combined_advanced_settings();
					break;
				// Keep backward compatibility
				case 'wordpress':
					$settings = $this->wbc_wordpress_settings();
					break;
				case 'woocommerce':
					$settings = $this->wbc_woocommerce_settings();
					break;
				case 'buddypress':
					$settings = $this->wbc_buddypress_settings();
					break;
				case 'bbpress':
					$settings = $this->wbc_bbpress_settings();
					break;
				case 'appearance':
					$settings = $this->wbc_appearance_settings();
					break;
				default:
					$settings = $this->wbc_service_settings();
					break;
			}

			// Manually save custom HTML fields based on tab
			if ( 'rfw-general' === $section ) {
				// Quick Setup tab - API keys and service selection
				$this->wbc_save_api_key_fields();
			} elseif ( 'protection' === $section ) {
				// Protection tab - checkbox fields
				$this->wbc_save_protection_fields();
			}

			// Save the settings
			WBC_Settings_Renderer::save_fields( $settings );

			// Trigger settings saved action
			do_action( 'wbc_recaptcha_settings_saved', $section );
		}
	}

endif;