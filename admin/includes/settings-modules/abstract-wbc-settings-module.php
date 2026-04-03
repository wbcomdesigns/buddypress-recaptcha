<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName -- Abstract class uses 'abstract-' prefix.
/**
 * Abstract Settings Module Base Class
 *
 * Provides common functionality for all settings modules.
 *
 * @link       https://wbcomdesigns.com/
 * @since      2.1.0
 *
 * @package    Recaptcha_For_BuddyPress
 * @subpackage bp_recaptcha/admin/includes/settings-modules
 */

/**
 * Abstract base class for settings modules
 *
 * Implements common functionality shared across all modules:
 * - Checkbox group HTML generation
 * - Standard settings array structure
 * - Active check integration
 *
 * @package    Recaptcha_For_BuddyPress
 * @subpackage bp_recaptcha/admin/includes/settings-modules
 * @author     Wbcom Designs <admin@wbcomdesigns.com>
 */
//phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedClassFound
abstract class WBC_Settings_Module_Abstract implements WBC_Settings_Module_Interface {

	/**
	 * Module ID
	 *
	 * @var string
	 */
	protected $module_id = '';

	/**
	 * Module name
	 *
	 * @var string
	 */
	protected $module_name = '';

	/**
	 * Get module ID
	 *
	 * @return string
	 */
	public function get_module_id() {
		return $this->module_id;
	}

	/**
	 * Get module name
	 *
	 * @return string
	 */
	public function get_module_name() {
		return $this->module_name;
	}

	/**
	 * Get protection settings
	 *
	 * Returns empty array if module is not active.
	 *
	 * @return array
	 */
	public function get_protection_settings() {
		if ( ! $this->is_active() ) {
			return array();
		}

		return $this->get_settings_array();
	}

	/**
	 * Get settings array
	 *
	 * Child classes must implement this to return their specific settings.
	 *
	 * @return array
	 */
	abstract protected function get_settings_array();

	/**
	 * Generate grouped checkbox HTML for protection settings
	 *
	 * @param array $checkboxes Array of checkbox configurations.
	 * @return string HTML for grouped checkboxes.
	 */
	protected function get_protection_checkbox_group( $checkboxes ) {
		$html = '<div class="wbc-protection-group">';

		foreach ( $checkboxes as $checkbox ) {
			$value   = get_option( $checkbox['id'], $checkbox['default'] );
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
	 * Create settings section
	 *
	 * Helper to create a complete settings section with title, checkboxes, and end.
	 *
	 * @param string $section_id   Section ID.
	 * @param string $section_name Section display name.
	 * @param array  $checkboxes   Array of checkbox configurations.
	 * @return array Settings array.
	 */
	protected function create_settings_section( $section_id, $section_name, $checkboxes ) {
		return array(
			array(
				'name' => esc_html( $section_name ),
				'type' => 'title',
				'id'   => $section_id,
			),
			array(
				'name'    => '',
				'type'    => 'custom',
				'id'      => $section_id . '_group',
				'default' => $this->get_protection_checkbox_group( $checkboxes ),
			),
			array(
				'type' => 'sectionend',
				'id'   => $section_id,
			),
		);
	}
}
