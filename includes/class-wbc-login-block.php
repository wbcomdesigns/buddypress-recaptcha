<?php
/**
 * AJAX Login Gutenberg Block
 *
 * @link       https://wbcomdesigns.com/
 * @since      2.0.0
 *
 * @package    Recaptcha_For_BuddyPress
 * @subpackage bp_recaptcha/includes
 */

/**
 * AJAX Login Block Class
 *
 * Registers and renders the login block for Gutenberg.
 *
 * @package    Recaptcha_For_BuddyPress
 * @subpackage bp_recaptcha/includes
 * @author     Wbcom Designs <admin@wbcomdesigns.com>
 */
class WBC_Login_Block {

	/**
	 * Register the block
	 */
	public function register_block() {
		// Register the block
		register_block_type(
			'wbc/login-widget',
			array(
				'attributes'      => array(
					'welcomeMessage' => array(
						'type'    => 'string',
						'default' => __( 'Welcome back, {username}!', 'buddypress-recaptcha' ),
					),
					'redirectUrl' => array(
						'type'    => 'string',
						'default' => home_url(),
					),
					'showLostPassword' => array(
						'type'    => 'boolean',
						'default' => true,
					),
					'showRegisterLink' => array(
						'type'    => 'boolean',
						'default' => true,
					),
					'showProfileLink' => array(
						'type'    => 'boolean',
						'default' => true,
					),
				),
				'render_callback' => array( $this, 'render_block' ),
			)
		);
	}

	/**
	 * Render the block
	 *
	 * @param array $attributes Block attributes.
	 * @return string Block HTML.
	 */
	public function render_block( $attributes ) {
		// Convert attributes to widget instance format
		$instance = array(
			'welcome_message'     => ! empty( $attributes['welcomeMessage'] ) ? $attributes['welcomeMessage'] : __( 'Welcome back, {username}!', 'buddypress-recaptcha' ),
			'redirect_url'        => ! empty( $attributes['redirectUrl'] ) ? $attributes['redirectUrl'] : home_url(),
			'show_lost_password'  => ! empty( $attributes['showLostPassword'] ) ? 'yes' : 'no',
			'show_register_link'  => ! empty( $attributes['showRegisterLink'] ) ? 'yes' : 'no',
			'show_profile_link'   => ! empty( $attributes['showProfileLink'] ) ? 'yes' : 'no',
		);

		// Start output buffering
		ob_start();

		echo '<div class="wp-block-wbc-login-widget">';

		if ( is_user_logged_in() ) {
			$this->render_logged_in_view( $instance );
		} else {
			$this->render_login_form( $instance );
		}

		echo '</div>';

		return ob_get_clean();
	}

	/**
	 * Render logged in view
	 *
	 * @param array $instance Block instance.
	 */
	private function render_logged_in_view( $instance ) {
		$current_user = wp_get_current_user();
		$welcome_message = ! empty( $instance['welcome_message'] ) ? $instance['welcome_message'] : __( 'Welcome back, {username}!', 'buddypress-recaptcha' );
		$welcome_message = str_replace( '{username}', '<strong>' . esc_html( $current_user->display_name ) . '</strong>', $welcome_message );

		?>
		<div class="wbc-login-widget-logged-in">
			<p class="wbc-welcome-message"><?php echo wp_kses_post( $welcome_message ); ?></p>
			<div class="wbc-user-links">
				<?php if ( ! empty( $instance['show_profile_link'] ) && $instance['show_profile_link'] === 'yes' ) : ?>
					<a href="<?php echo esc_url( get_edit_profile_url( $current_user->ID ) ); ?>" class="wbc-profile-link">
						<?php esc_html_e( 'My Profile', 'buddypress-recaptcha' ); ?>
					</a>
				<?php endif; ?>
				<a href="<?php echo esc_url( wp_logout_url( home_url() ) ); ?>" class="wbc-logout-link">
					<?php esc_html_e( 'Logout', 'buddypress-recaptcha' ); ?>
				</a>
			</div>
		</div>
		<?php
	}

	/**
	 * Render login form
	 *
	 * @param array $instance Block instance.
	 */
	private function render_login_form( $instance ) {
		$redirect_url = ! empty( $instance['redirect_url'] ) ? $instance['redirect_url'] : home_url();
		?>
		<div class="wbc-login-widget-form">
			<form id="wbc-ajax-login-form" method="post">
				<div class="wbc-form-messages"></div>

				<p class="wbc-login-username">
					<label for="wbc-username"><?php esc_html_e( 'Username or Email', 'buddypress-recaptcha' ); ?></label>
					<input type="text" name="username" id="wbc-username" class="wbc-input" required />
				</p>

				<p class="wbc-login-password">
					<label for="wbc-password"><?php esc_html_e( 'Password', 'buddypress-recaptcha' ); ?></label>
					<input type="password" name="password" id="wbc-password" class="wbc-input" required />
				</p>

				<p class="wbc-login-remember">
					<label>
						<input type="checkbox" name="remember" id="wbc-remember" value="yes" />
						<?php esc_html_e( 'Remember Me', 'buddypress-recaptcha' ); ?>
					</label>
				</p>

				<div class="wbc-login-captcha">
					<?php
					if ( function_exists( 'wbc_captcha_service_manager' ) ) {
						wbc_captcha_service_manager()->render( 'widget_login' );
					}
					?>
				</div>

				<p class="wbc-login-submit">
					<button type="submit" class="wbc-login-button">
						<span class="wbc-button-text"><?php esc_html_e( 'Login', 'buddypress-recaptcha' ); ?></span>
						<span class="wbc-button-loader" style="display:none;"><?php esc_html_e( 'Please wait...', 'buddypress-recaptcha' ); ?></span>
					</button>
				</p>

				<?php if ( ! empty( $instance['show_lost_password'] ) && $instance['show_lost_password'] === 'yes' ) : ?>
					<p class="wbc-lost-password">
						<a href="<?php echo esc_url( wp_lostpassword_url() ); ?>">
							<?php esc_html_e( 'Lost your password?', 'buddypress-recaptcha' ); ?>
						</a>
					</p>
				<?php endif; ?>

				<?php if ( get_option( 'users_can_register' ) && ! empty( $instance['show_register_link'] ) && $instance['show_register_link'] === 'yes' ) : ?>
					<p class="wbc-register-link">
						<a href="<?php echo esc_url( wp_registration_url() ); ?>">
							<?php esc_html_e( 'Register', 'buddypress-recaptcha' ); ?>
						</a>
					</p>
				<?php endif; ?>

				<input type="hidden" name="redirect_to" value="<?php echo esc_url( $redirect_url ); ?>" />
				<input type="hidden" name="action" value="wbc_ajax_login" />
				<?php wp_nonce_field( 'wbc_ajax_login_nonce', 'wbc_login_nonce' ); ?>
			</form>
		</div>
		<?php
	}
}
