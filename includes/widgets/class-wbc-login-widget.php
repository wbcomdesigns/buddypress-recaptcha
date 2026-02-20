<?php
/**
 * AJAX Login Widget with CAPTCHA Protection
 *
 * @link       https://wbcomdesigns.com/
 * @since      2.0.0
 *
 * @package    Recaptcha_For_BuddyPress
 * @subpackage bp_recaptcha/includes/widgets
 */

/**
 * AJAX Login Widget Class
 *
 * Provides a secure AJAX login widget with CAPTCHA protection.
 *
 * @package    Recaptcha_For_BuddyPress
 * @subpackage bp_recaptcha/includes/widgets
 * @author     Wbcom Designs <admin@wbcomdesigns.com>
 */
class WBC_Login_Widget extends WP_Widget {

	/**
	 * Constructor
	 */
	public function __construct() {
		parent::__construct(
			'wbc_login_widget',
			__( 'CAPTCHA Login Widget', 'buddypress-recaptcha' ),
			array(
				'description' => __( 'Secure AJAX login form with CAPTCHA protection', 'buddypress-recaptcha' ),
			)
		);
	}

	/**
	 * Front-end display of widget
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget( $args, $instance ) {
		echo wp_kses_post( $args['before_widget'] );

		if ( ! empty( $instance['title'] ) ) {
			echo wp_kses_post( $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'] );
		}

		if ( is_user_logged_in() ) {
			$this->render_logged_in_view( $instance );
		} else {
			$this->render_login_form( $instance );
		}

		echo wp_kses_post( $args['after_widget'] );
	}

	/**
	 * Render logged in view
	 *
	 * @param array $instance Widget instance.
	 */
	private function render_logged_in_view( $instance ) {
		$current_user    = wp_get_current_user();
		$welcome_message = ! empty( $instance['welcome_message'] ) ? $instance['welcome_message'] : __( 'Welcome back, {username}!', 'buddypress-recaptcha' );
		$welcome_message = str_replace( '{username}', '<strong>' . esc_html( $current_user->display_name ) . '</strong>', $welcome_message );

		?>
		<div class="wbc-login-widget-logged-in">
			<p class="wbc-welcome-message"><?php echo wp_kses_post( $welcome_message ); ?></p>
			<div class="wbc-user-links">
				<?php if ( ! empty( $instance['show_profile_link'] ) && 'yes' === $instance['show_profile_link'] ) : ?>
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
	 * @param array $instance Widget instance.
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

				<?php if ( ! empty( $instance['show_lost_password'] ) && 'yes' === $instance['show_lost_password'] ) : ?>
					<p class="wbc-lost-password">
						<a href="<?php echo esc_url( wp_lostpassword_url() ); ?>">
							<?php esc_html_e( 'Lost your password?', 'buddypress-recaptcha' ); ?>
						</a>
					</p>
				<?php endif; ?>

				<?php if ( get_option( 'users_can_register' ) && ! empty( $instance['show_register_link'] ) && 'yes' === $instance['show_register_link'] ) : ?>
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

	/**
	 * Back-end widget form
	 *
	 * @param array $instance Previously saved values from database.
	 */
	public function form( $instance ) {
		$title              = ! empty( $instance['title'] ) ? $instance['title'] : __( 'Login', 'buddypress-recaptcha' );
		$welcome_message    = ! empty( $instance['welcome_message'] ) ? $instance['welcome_message'] : __( 'Welcome back, {username}!', 'buddypress-recaptcha' );
		$redirect_url       = ! empty( $instance['redirect_url'] ) ? $instance['redirect_url'] : home_url();
		$show_lost_password = ! empty( $instance['show_lost_password'] ) ? $instance['show_lost_password'] : 'yes';
		$show_register_link = ! empty( $instance['show_register_link'] ) ? $instance['show_register_link'] : 'yes';
		$show_profile_link  = ! empty( $instance['show_profile_link'] ) ? $instance['show_profile_link'] : 'yes';
		?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>">
				<?php esc_html_e( 'Title:', 'buddypress-recaptcha' ); ?>
			</label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"
				name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text"
				value="<?php echo esc_attr( $title ); ?>" />
		</p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'welcome_message' ) ); ?>">
				<?php esc_html_e( 'Welcome Message:', 'buddypress-recaptcha' ); ?>
			</label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'welcome_message' ) ); ?>"
				name="<?php echo esc_attr( $this->get_field_name( 'welcome_message' ) ); ?>" type="text"
				value="<?php echo esc_attr( $welcome_message ); ?>" />
			<small><?php esc_html_e( 'Use {username} for user display name', 'buddypress-recaptcha' ); ?></small>
		</p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'redirect_url' ) ); ?>">
				<?php esc_html_e( 'Redirect URL after login:', 'buddypress-recaptcha' ); ?>
			</label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'redirect_url' ) ); ?>"
				name="<?php echo esc_attr( $this->get_field_name( 'redirect_url' ) ); ?>" type="text"
				value="<?php echo esc_url( $redirect_url ); ?>" />
		</p>

		<p>
			<input class="checkbox" type="checkbox" <?php checked( $show_lost_password, 'yes' ); ?>
				id="<?php echo esc_attr( $this->get_field_id( 'show_lost_password' ) ); ?>"
				name="<?php echo esc_attr( $this->get_field_name( 'show_lost_password' ) ); ?>" value="yes" />
			<label for="<?php echo esc_attr( $this->get_field_id( 'show_lost_password' ) ); ?>">
				<?php esc_html_e( 'Show "Lost Password" link', 'buddypress-recaptcha' ); ?>
			</label>
		</p>

		<p>
			<input class="checkbox" type="checkbox" <?php checked( $show_register_link, 'yes' ); ?>
				id="<?php echo esc_attr( $this->get_field_id( 'show_register_link' ) ); ?>"
				name="<?php echo esc_attr( $this->get_field_name( 'show_register_link' ) ); ?>" value="yes" />
			<label for="<?php echo esc_attr( $this->get_field_id( 'show_register_link' ) ); ?>">
				<?php esc_html_e( 'Show "Register" link', 'buddypress-recaptcha' ); ?>
			</label>
		</p>

		<p>
			<input class="checkbox" type="checkbox" <?php checked( $show_profile_link, 'yes' ); ?>
				id="<?php echo esc_attr( $this->get_field_id( 'show_profile_link' ) ); ?>"
				name="<?php echo esc_attr( $this->get_field_name( 'show_profile_link' ) ); ?>" value="yes" />
			<label for="<?php echo esc_attr( $this->get_field_id( 'show_profile_link' ) ); ?>">
				<?php esc_html_e( 'Show "My Profile" link when logged in', 'buddypress-recaptcha' ); ?>
			</label>
		</p>
		<?php
	}

	/**
	 * Sanitize widget form values as they are saved
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 * @return array Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance                       = array();
		$instance['title']              = ! empty( $new_instance['title'] ) ? sanitize_text_field( $new_instance['title'] ) : '';
		$instance['welcome_message']    = ! empty( $new_instance['welcome_message'] ) ? sanitize_text_field( $new_instance['welcome_message'] ) : '';
		$instance['redirect_url']       = ! empty( $new_instance['redirect_url'] ) ? esc_url_raw( $new_instance['redirect_url'] ) : home_url();
		$instance['show_lost_password'] = ! empty( $new_instance['show_lost_password'] ) ? 'yes' : 'no';
		$instance['show_register_link'] = ! empty( $new_instance['show_register_link'] ) ? 'yes' : 'no';
		$instance['show_profile_link']  = ! empty( $new_instance['show_profile_link'] ) ? 'yes' : 'no';

		return $instance;
	}
}
