<?php
/**
 * Exit if accessed directly.
 *
 * @package Exit if accessed directly.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Wooaction' ) ) :

	/**
	 * Exit if accessed directly.
	 *
	 * @package Exit if accessed directly.
	 */
	class Wooaction {
		/**
		 * Template Class Doc Comment
		 *
		 * Template Class.
		 */
		public function __construct() {

		}

		/**
		 * Template Class Doc Comment.
		 *
		 * @param array $options The position of the current token
		 * Template Class.
		 */
		public static function output_fields( $options ) {
			foreach ( $options as $value ) {
				if ( ! isset( $value['type'] ) ) {
					continue;
				}
				if ( ! isset( $value['id'] ) ) {
					$value['id'] = '';
				}
				if ( ! isset( $value['title'] ) ) {
					$value['title'] = isset( $value['name'] ) ? $value['name'] : '';
				}
				if ( ! isset( $value['class'] ) ) {
					$value['class'] = '';
				}
				if ( ! isset( $value['css'] ) ) {
					$value['css'] = '';
				}
				if ( ! isset( $value['default'] ) ) {
					$value['default'] = '';
				}
				if ( ! isset( $value['desc'] ) ) {
					$value['desc'] = '';
				}
				if ( ! isset( $value['desc_tip'] ) ) {
					$value['desc_tip'] = false;
				}
				if ( ! isset( $value['placeholder'] ) ) {
					$value['placeholder'] = '';
				}
				if ( ! isset( $value['suffix'] ) ) {
					$value['suffix'] = '';
				}
				if ( ! isset( $value['value'] ) ) {
					$value['value'] = self::get_option( $value['id'], $value['default'] );
				}

				// Custom attribute handling.
				$custom_attributes = array();

				if ( ! empty( $value['custom_attributes'] ) && is_array( $value['custom_attributes'] ) ) {
					foreach ( $value['custom_attributes'] as $attribute => $attribute_value ) {
						$custom_attributes[] = esc_attr( $attribute ) . '="' . esc_attr( $attribute_value ) . '"';
					}
				}

				// Description handling.
				$field_description = self::get_field_description( $value );
				$description       = $field_description['description'];
				$tooltip_html      = $field_description['tooltip_html'];

				// Switch based on type.
				switch ( $value['type'] ) {

					// Section Titles.
					case 'title':
						if ( ! empty( $value['title'] ) ) {
							echo '<h2>' . esc_html( $value['title'] ) . '</h2>';
						}
						if ( ! empty( $value['desc'] ) ) {
							echo '<div id="' . esc_attr( sanitize_title( $value['id'] ) ) . '-description">';
							echo wp_kses_post( wpautop( wptexturize( $value['desc'] ) ) );
							echo '</div>';
						}
						echo '<table class="form-table">' . "\n\n";
						if ( ! empty( $value['id'] ) ) {
							do_action( 'woocommerce_settings_' . sanitize_title( $value['id'] ) );
						}
						break;

					// Section Ends.
					case 'sectionend':
						if ( ! empty( $value['id'] ) ) {
							do_action( 'woocommerce_settings_' . sanitize_title( $value['id'] ) . '_end' );
						}
						echo '</table>';
						if ( ! empty( $value['id'] ) ) {
							do_action( 'woocommerce_settings_' . sanitize_title( $value['id'] ) . '_after' );
						}
						break;

					// Standard text inputs and subtypes like 'number'.
					case 'text':
					case 'password':
					case 'datetime':
					case 'datetime-local':
					case 'date':
					case 'month':
					case 'time':
					case 'week':
					case 'number':
					case 'email':
					case 'url':
					case 'tel':
						$option_value = $value['value'];

						?><tr valign="top" class="
						<?php
						if ( 'wbc_recapcha_wplogin_title' === $value['id'] ) {
							echo esc_attr( $hide_wplogin_label_class );
						}
						?>
						<?php
						if ( 'wbc_recapcha_wpregister_title' === $value['id'] ) {
							echo esc_attr( $hide_wpregister_label_class );
						}
						?>
						<?php
						if ( 'wbc_recapcha_wplostpassword_title' === $value['id'] ) {
							echo esc_attr( $hide_wplostpassword_label_class );
						}
						?>
						<?php
						if ( 'wbc_recapcha_woo_comment_title' === $value['id'] ) {
							echo esc_attr( $hide_woo_comment_label_class );
						}
						?>
						<?php
						if ( 'wbc_recapcha_signup_title_bp' === $value['id'] ) {
							echo esc_attr( $hide_sign_up_bp_title_label_class );
						}
						?>
						<?php
						if ( 'recapcha_bbpress_topic_title' === $value['id'] ) {
							echo esc_attr( $hide_bbpress_topic_label_class );
						}
						?>
						<?php
						if ( 'recapcha_bbpress_reply_title' === $value['id'] ) {
							echo esc_attr( $hide_bbpress_reply_label_class );
						}
						?>
						<?php
						if ( 'wbc_recapcha_signup_title' === $value['id'] ) {
							echo esc_attr( $hide_signup_label_class );
						}
						?>
						<?php
						if ( 'wbc_recapcha_login_title' === $value['id'] ) {
							echo esc_attr( $hide_login_label_class );
						}
						?>
						<?php
						if ( 'wbc_recapcha_lostpassword_title' === $value['id'] ) {
							echo esc_attr( $hide_lostpassword_label_class );
						}
						?>
						<?php
						if ( 'wbc_recapcha_guestcheckout_title' === $value['id'] ) {
							echo esc_attr( $hide_checkout_label_class );
						}
						?>
						">
							<th scope="row" class="titledesc">
								<label for="<?php echo esc_attr( $value['id'] ); ?>"><?php echo esc_html( $value['title'] ); ?><?php echo wp_kses_post( $tooltip_html ); ?></label>
							</th>
							<td class="forminp forminp-<?php echo esc_attr( sanitize_title( $value['type'] ) ); ?>">
								<input
									name="<?php echo esc_attr( $value['id'] ); ?>"
									id="<?php echo esc_attr( $value['id'] ); ?>"
									type="<?php echo esc_attr( $value['type'] ); ?>"
									style="<?php echo esc_attr( $value['css'] ); ?>"
									value="<?php echo esc_attr( $option_value ); ?>"
									class="<?php echo esc_attr( $value['class'] ); ?>"
									placeholder="<?php echo esc_attr( $value['placeholder'] ); ?>"
									<?php echo esc_attr( implode( ' ', $custom_attributes ) ); ?>
									/><?php echo esc_html( $value['suffix'] ); ?><?php echo wp_kses_post( $description ); ?>
							</td>
						</tr>
						<?php
						break;

					// Color picker.
					case 'color':
						$option_value = $value['value'];

						?>
						<tr valign="top">
							<th scope="row" class="titledesc">
								<label for="<?php echo esc_attr( $value['id'] ); ?>"><?php echo esc_html( $value['title'] ); ?> <?php echo wp_kses_post( $tooltip_html ); ?></label>
							</th>
							<td class="forminp forminp-<?php echo esc_attr( sanitize_title( $value['type'] ) ); ?>">&lrm;
								<span class="colorpickpreview" style="background: <?php echo esc_attr( $option_value ); ?>">&nbsp;</span>
								<input
									name="<?php echo esc_attr( $value['id'] ); ?>"
									id="<?php echo esc_attr( $value['id'] ); ?>"
									type="text"
									dir="ltr"
									style="<?php echo esc_attr( $value['css'] ); ?>"
									value="<?php echo esc_attr( $option_value ); ?>"
									class="<?php echo esc_attr( $value['class'] ); ?>colorpick"
									placeholder="<?php echo esc_attr( $value['placeholder'] ); ?>"
									<?php echo esc_attr( implode( ' ', $custom_attributes ) ); ?>
									/>&lrm; <?php echo wp_kses_post( $description ); ?>
									<div id="colorPickerDiv_<?php echo esc_attr( $value['id'] ); ?>" class="colorpickdiv" style="z-index: 100;background:#eee;border:1px solid #ccc;position:absolute;display:none;"></div>
							</td>
						</tr>
						<?php
						break;

					// Textarea.
					case 'textarea':
						$option_value = $value['value'];

						?>
						<tr valign="top">
							<th scope="row" class="titledesc">
								<label for="<?php echo esc_attr( $value['id'] ); ?>"><?php echo esc_html( $value['title'] ); ?> <?php echo wp_kses_post( $tooltip_html ); ?></label>
							</th>
							<td class="forminp forminp-<?php echo esc_attr( sanitize_title( $value['type'] ) ); ?>">
								<?php echo wp_kses_post( $description ); ?>

								<textarea
									name="<?php echo esc_attr( $value['id'] ); ?>"
									id="<?php echo esc_attr( $value['id'] ); ?>"
									style="<?php echo esc_attr( $value['css'] ); ?>"
									class="<?php echo esc_attr( $value['class'] ); ?>"
									placeholder="<?php echo esc_attr( $value['placeholder'] ); ?>"
									<?php echo esc_attr( implode( ' ', $custom_attributes ) ); ?>
									><?php echo esc_textarea( $option_value ); ?></textarea>
							</td>
						</tr>
						<?php
						break;

					// Select boxes.
					case 'select':
					case 'multiselect':
						$option_value = $value['value'];

						?>
						<tr valign="top">
							<th scope="row" class="titledesc">
								<label for="<?php echo esc_attr( $value['id'] ); ?>"><?php echo esc_html( $value['title'] ); ?> <?php echo wp_kses_post( $tooltip_html ); ?></label>
							</th>
							<td class="forminp forminp-<?php echo esc_attr( sanitize_title( $value['type'] ) ); ?>">
								<select
									name="<?php echo esc_attr( $value['id'] ); ?><?php echo ( 'multiselect' === $value['type'] ) ? '[]' : ''; ?>"
									id="<?php echo esc_attr( $value['id'] ); ?>"
									style="<?php echo esc_attr( $value['css'] ); ?>"
									class="<?php echo esc_attr( $value['class'] ); ?>"
									<?php echo esc_attr( implode( ' ', $custom_attributes ) ); ?>
									<?php echo 'multiselect' === $value['type'] ? 'multiple="multiple"' : ''; ?>
									>
									<?php
									foreach ( $value['options'] as $key => $val ) {
										?>
										<option value="<?php echo esc_attr( $key ); ?>"
											<?php

											if ( is_array( $option_value ) ) {
												selected( in_array( (string) $key, $option_value, true ), true );
											} else {
												selected( $option_value, (string) $key );
											}

											?>
										><?php echo wp_kses_post( $val ); ?></option>
										<?php
									}
									?>
								</select> <?php echo wp_kses_post( $description ); ?>
							</td>
						</tr>
						<?php
						break;

					// Radio inputs.
					case 'radio':
						$option_value = $value['value'];

						?>
						<tr valign="top">
							<th scope="row" class="titledesc">
								<label for="<?php echo esc_attr( $value['id'] ); ?>"><?php echo esc_html( $value['title'] ); ?> <?php echo wp_kses_post( $tooltip_html ); ?></label>
							</th>
							<td class="forminp forminp-<?php echo esc_attr( sanitize_title( $value['type'] ) ); ?>">
								<fieldset>
									<?php echo wp_kses_post( $description ); ?>
									<ul>
									<?php
									foreach ( $value['options'] as $key => $val ) {
										?>
										<li>
											<label><input
												name="<?php echo esc_attr( $value['id'] ); ?>"
												value="<?php echo esc_attr( $key ); ?>"
												type="radio"
												style="<?php echo esc_attr( $value['css'] ); ?>"
												class="<?php echo esc_attr( $value['class'] ); ?>"
												<?php echo esc_attr( implode( ' ', $custom_attributes ) ); ?>
												<?php checked( $key, $option_value ); ?>
												/> <?php echo wp_kses_post( $val ); ?></label>
										</li>
										<?php
									}
									?>
									</ul>
								</fieldset>
							</td>
						</tr>
						<?php
						break;

					// Checkbox input.
					case 'checkbox':
						$option_value     = $value['value'];
						$visibility_class = array();
						if ( 'wbc_recapcha_enable_on_guestcheckout' === $value['id'] ) {
							$guest_checkout_value = $value['value'];
							$guest_checkout_class = '';
							if ( 'yes' !== $guest_checkout_value ) {
								$guest_checkout_class = 'hide';
							}
						}
						if ( 'wbc_recapcha_enable_on_logincheckout' === $value['id'] ) {
							$login_checkout_value = $value['value'];
							$login_checkout_class = '';
							if ( 'yes' !== $login_checkout_value ) {
								$login_checkout_class = 'hide';
							}
						}
						if ( 'wbc_recapcha_enable_on_payfororder' === $value['id'] ) {
							$pay_order_value = $value['value'];
							$pay_order_class = '';
							if ( 'yes' !== $pay_order_value ) {
								$pay_order_class = 'hide';
							}
						}
						if ( 'wbc_recapcha_hide_label_wplogin' === $value['id'] ) {
							$hide_wplogin_label_value = $value['value'];
							$hide_wplogin_label_class = '';
							if ( 'yes' !== $hide_wplogin_label_value ) {
								$hide_wplogin_label_class = 'hide';
							}
						}
						if ( 'wbc_recapcha_hide_label_wpregister' === $value['id'] ) {
							$hide_wpregister_label_value = $value['value'];
							$hide_wpregister_label_class = '';
							if ( 'yes' !== $hide_wpregister_label_value ) {
								$hide_wpregister_label_class = 'hide';
							}
						}
						if ( 'wbc_recapcha_hide_label_wplostpassword' === $value['id'] ) {
							$hide_wplostpassword_label_value = $value['value'];
							$hide_wplostpassword_label_class = '';
							if ( 'yes' !== $hide_wplostpassword_label_value ) {
								$hide_wplostpassword_label_class = 'hide';
							}
						}
						if ( 'wbc_recapcha_hide_label_woo_comment' === $value['id'] ) {
							$hide_woo_comment_label_value = $value['value'];
							$hide_woo_comment_label_class = '';
							if ( 'yes' !== $hide_woo_comment_label_value ) {
								$hide_woo_comment_label_class = 'hide';
							}
						}
						if ( 'wbc_recapcha_hide_label_signup_bp' === $value['id'] ) {
							$hide_sign_up_bp_title_label_value = $value['value'];
							$hide_sign_up_bp_title_label_class = '';
							if ( 'yes' !== $hide_sign_up_bp_title_label_value ) {
								$hide_sign_up_bp_title_label_class = 'hide';
							}
						}
						if ( 'recapcha_hide_label_bbpress_topic' === $value['id'] ) {
							$hide_bbpress_topic_label_value = $value['value'];
							$hide_bbpress_topic_label_class = '';
							if ( 'yes' !== $hide_bbpress_topic_label_value ) {
								$hide_bbpress_topic_label_class = 'hide';
							}
						}
						if ( 'recapcha_hide_label_bbpress_reply' === $value['id'] ) {
							$hide_bbpress_reply_label_value = $value['value'];
							$hide_bbpress_reply_label_class = '';
							if ( 'yes' !== $hide_bbpress_reply_label_value ) {
								$hide_bbpress_reply_label_class = 'hide';
							}
						}
						if ( 'wbc_recapcha_hide_label_signup' === $value['id'] ) {
							$hide_signup_label_value = $value['value'];
							$hide_signup_label_class = '';
							if ( 'yes' !== $hide_signup_label_value ) {
								$hide_signup_label_class = 'hide';
							}
						}
						if ( 'wbc_recapcha_hide_label_login' === $value['id'] ) {
							$hide_login_label_value = $value['value'];
							$hide_login_label_class = '';
							if ( 'yes' !== $hide_login_label_value ) {
								$hide_login_label_class = 'hide';
							}
						}
						if ( 'wbc_recapcha_hide_label_lostpassword' === $value['id'] ) {
							$hide_lostpassword_label_value = $value['value'];
							$hide_lostpassword_label_class = '';
							if ( 'yes' !== $hide_lostpassword_label_value ) {
								$hide_lostpassword_label_class = 'hide';
							}
						}
						if ( 'wbc_recapcha_hide_label_checkout' === $value['id'] ) {
							$hide_checkout_label_value = $value['value'];
							$hide_checkout_label_class = '';
							if ( 'yes' !== $hide_checkout_label_value ) {
								$hide_checkout_label_class = 'hide';
							}
						}
						if ( ! isset( $value['hide_if_checked'] ) ) {
							$value['hide_if_checked'] = false;
						}
						if ( ! isset( $value['show_if_checked'] ) ) {
							$value['show_if_checked'] = false;
						}
						if ( 'yes' === $value['hide_if_checked'] || 'yes' === $value['show_if_checked'] ) {
							$visibility_class[] = 'hidden_option';
						}
						if ( 'option' === $value['hide_if_checked'] ) {
							$visibility_class[] = 'hide_options_if_checked';
						}
						if ( 'option' === $value['show_if_checked'] ) {
							$visibility_class[] = 'show_options_if_checked';
						}

						if ( ! isset( $value['checkboxgroup'] ) || 'start' === $value['checkboxgroup'] ) {

							?>
								<tr valign="top" class="
								<?php
								if ( 'wbc_recapcha_disable_submitbtn_guestcheckout' === $value['id'] || 'wbc_recapcha_guest_recpacha_refersh_on_error' === $value['id'] ) {
									echo esc_attr( $guest_checkout_class );
								}
								?>
								<?php
								if ( 'wbc_recapcha_disable_submitbtn_logincheckout' === $value['id'] || 'wbc_recapcha_login_recpacha_refersh_on_error' === $value['id'] ) {
									echo esc_attr( $login_checkout_class );
								}
								?>
								<?php
								if ( 'wbc_recapcha_disable_submitbtn_payfororder' === $value['id'] || 'wbc_recaptcha_login_recpacha_for_req_btn' === $value['id'] ) {
									echo esc_attr( $pay_order_class );
								}
								?>
								<?php echo esc_attr( implode( ' ', $visibility_class ) ); ?>">
									<th scope="row" class="titledesc"><?php echo esc_html( $value['title'] ); ?></th>
									<td class="forminp forminp-checkbox">
										<fieldset>
							<?php
						} else {
							?>
								<fieldset class="<?php echo esc_attr( implode( ' ', $visibility_class ) ); ?>">
							<?php
						}

						if ( ! empty( $value['title'] ) ) {
							?>
								<legend class="screen-reader-text"><span><?php echo esc_html( $value['title'] ); ?></span></legend>
							<?php
						}

						?>
							<label for="<?php echo esc_attr( $value['id'] ); ?>">
								<input
									name="<?php echo esc_attr( $value['id'] ); ?>"
									id="<?php echo esc_attr( $value['id'] ); ?>"
									type="checkbox"
									class="<?php echo esc_attr( isset( $value['class'] ) ? $value['class'] : '' ); ?>"
									value="1"
									<?php checked( $option_value, 'yes' ); ?>
									<?php echo esc_attr( implode( ' ', $custom_attributes ) ); ?>
																		/> <p class="description"><?php echo wp_kses_post( $description ); ?></p>
							</label> <?php echo wp_kses_post( $tooltip_html ); ?>
						<?php

						if ( ! isset( $value['checkboxgroup'] ) || 'end' === $value['checkboxgroup'] ) {
							?>
										</fieldset>
									</td>
								</tr>
							<?php
						} else {
							?>
								</fieldset>
							<?php
						}
						break;

					// Image width settings. @todo deprecate and remove in 4.0. No longer needed by core.
					case 'image_width':
						$image_size       = str_replace( '_image_size', '', $value['id'] );
						$size             = wc_get_image_size( $image_size );
						$width            = isset( $size['width'] ) ? $size['width'] : $value['default']['width'];
						$height           = isset( $size['height'] ) ? $size['height'] : $value['default']['height'];
						$crop             = isset( $size['crop'] ) ? $size['crop'] : $value['default']['crop'];
						$disabled_attr    = '';
						$disabled_message = '';

						if ( has_filter( 'woocommerce_get_image_size_' . $image_size ) ) {
							$disabled_attr    = 'disabled="disabled"';
							$disabled_message = '<p><small>' . esc_html__( 'The settings of this image size have been disabled because its values are being overwritten by a filter.', 'buddypress-recaptcha' ) . '</small></p>';
						}

						?>
						<tr valign="top">
							<th scope="row" class="titledesc">
							<label><?php echo esc_html( $value['title'] ); ?> <?php echo esc_html( $tooltip_html . $disabled_message ); ?></label>
						</th>
							<td class="forminp image_width_settings">

								<input name="<?php echo esc_attr( $value['id'] ); ?>[width]" <?php echo esc_attr( $disabled_attr ); ?> id="<?php echo esc_attr( $value['id'] ); ?>-width" type="text" size="3" value="<?php echo esc_attr( $width ); ?>" /> &times; <input name="<?php echo esc_attr( $value['id'] ); ?>[height]" <?php echo esc_html( $disabled_attr ); ?> id="<?php echo esc_attr( $value['id'] ); ?>-height" type="text" size="3" value="<?php echo esc_attr( $height ); ?>" />

								<label><input name="<?php echo esc_attr( $value['id'] ); ?>[crop]" <?php echo esc_attr( $disabled_attr ); ?> id="<?php echo esc_attr( $value['id'] ); ?>-crop" type="checkbox" value="1" <?php checked( 1, $crop ); ?> /> <?php esc_html_e( 'Hard crop?', 'buddypress-recaptcha' ); ?></label>

								</td>
						</tr>
						<?php
						break;

					// Single page selects.
					case 'single_select_page':
						$args = array(
							'name'             => $value['id'],
							'id'               => $value['id'],
							'sort_column'      => 'menu_order',
							'sort_order'       => 'ASC',
							'show_option_none' => ' ',
							'class'            => $value['class'],
							'echo'             => false,
							'selected'         => absint( $value['value'] ),
							'post_status'      => 'publish,private,draft',
						);

						if ( isset( $value['args'] ) ) {
							$args = wp_parse_args( $value['args'], $args );
						}

						?>
						<tr valign="top" class="single_select_page">
							<th scope="row" class="titledesc">
								<label><?php echo esc_html( $value['title'] ); ?> <?php echo wp_kses_post( $tooltip_html ); ?></label>
							</th>
							<td class="forminp">
								<?php echo str_replace( ' id=', " data-placeholder='" . esc_attr__( 'Select a page&hellip;', 'buddypress-recaptcha' ) . "' style='" . $value['css'] . "' class='" . $value['class'] . "' id=", wp_dropdown_pages( $args ) ); ?> <?php echo wp_kses_post( $description ); ?>
							</td>
						</tr>
						<?php
						break;

					case 'single_select_page_with_search':
						$option_value = $value['value'];
						$page         = get_post( $option_value );

						if ( ! is_null( $page ) ) {
							$page                = get_post( $option_value );
							$option_display_name = sprintf(
								/* translators: 1: page name 2: page ID */
								__( '%1$s (ID: %2$s)', 'buddypress-recaptcha' ),
								$page->post_title,
								$option_value
							);
						}
						?>
						<tr valign="top" class="single_select_page">
							<th scope="row" class="titledesc">
								<label for="<?php echo esc_attr( $value['id'] ); ?>"><?php echo esc_html( $value['title'] ); ?> <?php echo wp_kses_post( $tooltip_html ); ?></label>
							</th>
							<td class="forminp forminp-<?php echo esc_attr( sanitize_title( $value['type'] ) ); ?>">
								<select
									name="<?php echo esc_attr( $value['id'] ); ?>"
									id="<?php echo esc_attr( $value['id'] ); ?>"
									style="<?php echo esc_attr( $value['css'] ); ?>"
									class="<?php echo esc_attr( $value['class'] ); ?>"
									<?php echo esc_attr( implode( ' ', $custom_attributes ) ); ?>
									data-placeholder="<?php esc_attr_e( 'Search for a page&hellip;', 'buddypress-recaptcha' ); ?>"
									data-allow_clear="true"
									data-exclude="<?php echo esc_attr( wc_esc_json( wp_json_encode( $value['args']['exclude'] ) ) ); ?>"
									>
									<option value=""></option>
									<?php if ( ! is_null( $page ) ) { ?>
										<option value="<?php echo esc_attr( $option_value ); ?>" selected="selected">
										<?php echo esc_html( wp_strip_all_tags( $option_display_name ) ); ?>
										</option>
									<?php } ?>
								</select> <?php echo wp_kses_post( $description ); ?>
							</td>
						</tr>
						<?php
						break;

					// Single country selects.
					case 'single_select_country':
						$country_setting = (string) $value['value'];

						if ( strstr( $country_setting, ':' ) ) {
							$country_setting = explode( ':', $country_setting );
							$country         = current( $country_setting );
							$state           = end( $country_setting );
						} else {
							$country = $country_setting;
							$state   = '*';
						}
						?>
						<tr valign="top">
							<th scope="row" class="titledesc">
								<label for="<?php echo esc_attr( $value['id'] ); ?>"><?php echo esc_html( $value['title'] ); ?> <?php echo wp_kses_post( $tooltip_html ); ?></label>
							</th>
							<td class="forminp"><select name="<?php echo esc_attr( $value['id'] ); ?>" style="<?php echo esc_attr( $value['css'] ); ?>" data-placeholder="<?php esc_attr_e( 'Choose a country / region&hellip;', 'buddypress-recaptcha' ); ?>" aria-label="<?php esc_attr_e( 'Country / Region', 'buddypress-recaptcha' ); ?>" class="wc-enhanced-select">
								<?php WC()->countries->country_dropdown_options( $country, $state ); ?>
							</select> <?php echo wp_kses_post( $description ); ?>
							</td>
						</tr>
						<?php
						break;

					// Country multiselects.
					case 'multi_select_countries':
						$selections = (array) $value['value'];

						if ( ! empty( $value['options'] ) ) {
							$countries = $value['options'];
						} else {
							$countries = WC()->countries->countries;
						}

						asort( $countries );
						?>
						<tr valign="top">
							<th scope="row" class="titledesc">
								<label for="<?php echo esc_attr( $value['id'] ); ?>"><?php echo esc_html( $value['title'] ); ?> <?php echo wp_kses_post( $tooltip_html ); ?></label>
							</th>
							<td class="forminp">
								<select multiple="multiple" name="<?php echo esc_attr( $value['id'] ); ?>[]" style="width:350px" data-placeholder="<?php esc_attr_e( 'Choose countries / regions&hellip;', 'buddypress-recaptcha' ); ?>" aria-label="<?php esc_attr_e( 'Country / Region', 'buddypress-recaptcha' ); ?>" class="wc-enhanced-select">
									<?php
									if ( ! empty( $countries ) ) {
										foreach ( $countries as $key => $val ) {
											echo '<option value="' . esc_attr( $key ) . '"' . esc_attr( wc_selected( $key, $selections ) ) . '>' . esc_html( $val ) . '</option>';
										}
									}
									?>
								</select> <?php echo ( $description ) ? wp_kses_post( $description ) : ''; ?> <br /><a class="select_all button" href="#"><?php esc_html_e( 'Select all', 'buddypress-recaptcha' ); ?></a> <a class="select_none button" href="#"><?php esc_html_e( 'Select none', 'buddypress-recaptcha' ); ?></a>
							</td>
						</tr>
						<?php
						break;

					// Days/months/years selector.
					case 'relative_date_selector':
						$periods      = array(
							'days'   => __( 'Day(s)', 'buddypress-recaptcha' ),
							'weeks'  => __( 'Week(s)', 'buddypress-recaptcha' ),
							'months' => __( 'Month(s)', 'buddypress-recaptcha' ),
							'years'  => __( 'Year(s)', 'buddypress-recaptcha' ),
						);
						$option_value = wc_parse_relative_date_option( $value['value'] );
						?>
						<tr valign="top">
							<th scope="row" class="titledesc">
								<label for="<?php echo esc_attr( $value['id'] ); ?>"><?php echo esc_html( $value['title'] ); ?> <?php echo wp_kses_post( $tooltip_html ); ?></label>
							</th>
							<td class="forminp">
							<input
									name="<?php echo esc_attr( $value['id'] ); ?>[number]"
									id="<?php echo esc_attr( $value['id'] ); ?>"
									type="number"
									style="width: 80px;"
									value="<?php echo esc_attr( $option_value['number'] ); ?>"
									class="<?php echo esc_attr( $value['class'] ); ?>"
									placeholder="<?php echo esc_attr( $value['placeholder'] ); ?>"
									step="1"
									min="1"
									<?php echo esc_attr( implode( ' ', $custom_attributes ) ); ?>
								/>&nbsp;
								<select name="<?php echo esc_attr( $value['id'] ); ?>[unit]" style="width: auto;">
									<?php
									foreach ( $periods as $value => $label ) {
										echo '<option value="' . esc_attr( $value ) . '"' . selected( $option_value['unit'], $value, false ) . '>' . esc_html( $label ) . '</option>';
									}
									?>
								</select> <?php echo ( $description ) ? wp_kses_post( $description ) : ''; ?>
							</td>
						</tr>
						<?php
						break;

					// Default: run an action.
					default:
						do_action( 'woocommerce_admin_field_' . $value['type'], $value );
						break;
				}
			}
		}

		/**
		 * Get a setting from the settings API.
		 *
		 * @param string $option_name Option name.
		 * @param mixed  $default     Default value.
		 * @return mixed
		 */
		public static function get_option( $option_name, $default = '' ) {
			if ( ! $option_name ) {
				return $default;
			}

			// Array value.
			if ( strstr( $option_name, '[' ) ) {

				parse_str( $option_name, $option_array );

				// Option name is first key.
				$option_name = current( array_keys( $option_array ) );

				// Get value.
				$option_values = get_option( $option_name, '' );

				$key = key( $option_array[ $option_name ] );

				if ( isset( $option_values[ $key ] ) ) {
					$option_value = $option_values[ $key ];
				} else {
					$option_value = null;
				}
			} else {
				// Single value.
				$option_value = get_option( $option_name, null );
			}

			if ( is_array( $option_value ) ) {
				$option_value = wp_unslash( $option_value );
			} elseif ( ! is_null( $option_value ) ) {
				$option_value = stripslashes( $option_value );
			}

			return ( null === $option_value ) ? $default : $option_value;
		}

		/**
		 * Template Class Doc Comment
		 *
		 * @param array $value The position of the current token
		 * Template Class.
		 */
		public static function get_field_description( $value ) {
			$description  = '';
			$tooltip_html = '';

			if ( true === $value['desc_tip'] ) {
				$tooltip_html = $value['desc'];
			} elseif ( ! empty( $value['desc_tip'] ) ) {
				$description  = $value['desc'];
				$tooltip_html = $value['desc_tip'];
			} elseif ( ! empty( $value['desc'] ) ) {
				$description = $value['desc'];
			}

			if ( $description && in_array( $value['type'], array( 'textarea', 'radio' ), true ) ) {
				$description = '<p style="margin-top:0">' . wp_kses_post( $description ) . '</p>';
			} elseif ( $description && in_array( $value['type'], array( 'checkbox' ), true ) ) {
				$description = wp_kses_post( $description );
			} elseif ( $description ) {
				$description = '<p class="description">' . wp_kses_post( $description ) . '</p>';
			}

			if ( $tooltip_html && in_array( $value['type'], array( 'checkbox' ), true ) ) {
				$tooltip_html = '<p class="description">' . $tooltip_html . '</p>';
			} elseif ( $tooltip_html ) {
				$tooltip_html = wc_help_tip( $tooltip_html );
			}

			return array(
				'description'  => $description,
				'tooltip_html' => $tooltip_html,
			);
		}

		/**
		 * Template Class Doc Comment
		 *
		 * @param array $options The position of the current token.
		 * @param array $data The position of the current token.
		 * Template Class.
		 */
		public static function save_fields( $options, $data = null ) {
			if ( is_null( $data ) ) {
				$data = $_POST;
			}
			if ( empty( $data ) ) {
				return false;
			}

			// Options to update will be stored here and saved later.
			$update_options   = array();
			$autoload_options = array();

			// Loop options and get values to save.
			foreach ( $options as $option ) {
				if ( ! isset( $option['id'] ) || ! isset( $option['type'] ) || ( isset( $option['is_option'] ) && false === $option['is_option'] ) ) {
					continue;
				}

				// Get posted value.
				if ( strstr( $option['id'], '[' ) ) {
					parse_str( $option['id'], $option_name_array );
					$option_name  = current( array_keys( $option_name_array ) );
					$setting_name = key( $option_name_array[ $option_name ] );
					$raw_value    = isset( $data[ $option_name ][ $setting_name ] ) ? wp_unslash( $data[ $option_name ][ $setting_name ] ) : null;
				} else {
					$option_name  = $option['id'];
					$setting_name = '';
					$raw_value    = isset( $data[ $option['id'] ] ) ? wp_unslash( $data[ $option['id'] ] ) : null;
				}

				// Format the value based on option type.
				switch ( $option['type'] ) {
					case 'checkbox':
						$value = '1' === $raw_value || 'yes' === $raw_value ? 'yes' : 'no';
						break;
					case 'textarea':
						$value = wp_kses_post( trim( $raw_value ) );
						break;
					case 'multiselect':
					case 'multi_select_countries':
						$value = array_filter( array_map( 'wc_clean', (array) $raw_value ) );
						break;
					case 'image_width':
						$value = array();
						if ( isset( $raw_value['width'] ) ) {
							$value['width']  = $raw_value['width'];
							$value['height'] = $raw_value['height'];
							$value['crop']   = isset( $raw_value['crop'] ) ? 1 : 0;
						} else {
							$value['width']  = $option['default']['width'];
							$value['height'] = $option['default']['height'];
							$value['crop']   = $option['default']['crop'];
						}
						break;
					case 'select':
						$allowed_values = empty( $option['options'] ) ? array() : array_map( 'strval', array_keys( $option['options'] ) );
						if ( empty( $option['default'] ) && empty( $allowed_values ) ) {
							$value = null;
							break;
						}
						$default = ( empty( $option['default'] ) ? $allowed_values[0] : $option['default'] );
						$value   = in_array( $raw_value, $allowed_values, true ) ? $raw_value : $default;
						break;
					case 'relative_date_selector':
						$value = wc_parse_relative_date_option( $raw_value );
						break;
					default:
						$value = $raw_value;
						break;
				}

				/**
				 * Fire an action when a certain 'type' of field is being saved.
				 *
				 * @deprecated 2.4.0 - doesn't allow manipulation of values!
				 */
				if ( has_action( 'woocommerce_update_option_' . sanitize_title( $option['type'] ) ) ) {
					wc_deprecated_function( 'The woocommerce_update_option_X action', '2.4.0', 'woocommerce_admin_settings_sanitize_option filter' );
					do_action( 'woocommerce_update_option_' . sanitize_title( $option['type'] ), $option );
					continue;
				}

				/**
				 * Sanitize the value of an option.
				 *
				 * @since 2.4.0
				 */
				$value = apply_filters( 'woocommerce_admin_settings_sanitize_option', $value, $option, $raw_value );

				/**
				 * Sanitize the value of an option by option name.
				 *
				 * @since 2.4.0
				 */
				$value = apply_filters( "woocommerce_admin_settings_sanitize_option_$option_name", $value, $option, $raw_value );

				if ( is_null( $value ) ) {
					continue;
				}

				// Check if option is an array and handle that differently to single values.
				if ( $option_name && $setting_name ) {
					if ( ! isset( $update_options[ $option_name ] ) ) {
						$update_options[ $option_name ] = get_option( $option_name, array() );
					}
					if ( ! is_array( $update_options[ $option_name ] ) ) {
						$update_options[ $option_name ] = array();
					}
					$update_options[ $option_name ][ $setting_name ] = $value;
				} else {
					$update_options[ $option_name ] = $value;
				}

				$autoload_options[ $option_name ] = isset( $option['autoload'] ) ? (bool) $option['autoload'] : true;

				/**
				 * Fire an action before saved.
				 *
				 * @deprecated 2.4.0 - doesn't allow manipulation of values!
				 */
				do_action( 'woocommerce_update_option', $option );
			}

			// Save all options in our array.
			foreach ( $update_options as $name => $value ) {
				update_option( $name, $value, $autoload_options[ $name ] ? 'yes' : 'no' );
			}

			return true;
		}

		/**
		 * Template Class Doc Comment
		 *
		 * @param array $var The position of the current token
		 * Template Class.
		 */
		public function wc_clean( $var ) {
			if ( is_array( $var ) ) {
				return array_map( 'wc_clean', $var );
			} else {
				return is_scalar( $var ) ? sanitize_text_field( $var ) : $var;
			}
		}

	}
endif;
