/**
 * WordPress dependencies
 */
const { registerBlockType } = wp.blocks;
const { ServerSideRender } = wp.serverSideRender || wp.components;
const { InspectorControls } = wp.blockEditor || wp.editor;
const { PanelBody, TextControl, ToggleControl } = wp.components;
const { __ } = wp.i18n;

/**
 * Register: CAPTCHA Login Block
 *
 * @link https://wordpress.org/gutenberg/handbook/block-api/
 * @param  {string}   name     Block name.
 * @param  {Object}   settings Block settings.
 * @return {?WPBlock}          The block, if it has been successfully registered; otherwise `undefined`.
 */
registerBlockType('wbc/login-widget', {
	/**
	 * Edit component.
	 * See https://wordpress.org/gutenberg/handbook/designers-developers/developers/block-api/block-edit-save/#edit
	 *
	 * @param {Object} props Props.
	 * @returns {Mixed} JSX Component.
	 */
	edit: (props) => {
		const { attributes, setAttributes } = props;

		return [
			<InspectorControls key="inspector">
				<PanelBody title={__('Login Settings', 'buddypress-recaptcha')}>
					<TextControl
						label={__('Welcome Message', 'buddypress-recaptcha')}
						value={attributes.welcomeMessage}
						onChange={(value) => setAttributes({ welcomeMessage: value })}
						help={__('Use {username} as placeholder for the username', 'buddypress-recaptcha')}
					/>
					<TextControl
						label={__('Redirect URL', 'buddypress-recaptcha')}
						value={attributes.redirectUrl}
						onChange={(value) => setAttributes({ redirectUrl: value })}
						help={__('Where to redirect after successful login (leave empty for homepage)', 'buddypress-recaptcha')}
					/>
					<ToggleControl
						label={__('Show Lost Password Link', 'buddypress-recaptcha')}
						checked={attributes.showLostPassword}
						onChange={(value) => setAttributes({ showLostPassword: value })}
					/>
					<ToggleControl
						label={__('Show Register Link', 'buddypress-recaptcha')}
						checked={attributes.showRegisterLink}
						onChange={(value) => setAttributes({ showRegisterLink: value })}
					/>
					<ToggleControl
						label={__('Show Profile Link', 'buddypress-recaptcha')}
						checked={attributes.showProfileLink}
						onChange={(value) => setAttributes({ showProfileLink: value })}
					/>
				</PanelBody>
			</InspectorControls>,
			<div key="preview" className={props.className}>
				<ServerSideRender
					block="wbc/login-widget"
					attributes={attributes}
				/>
			</div>
		];
	},

	/**
	 * Save component.
	 * See https://wordpress.org/gutenberg/handbook/designers-developers/developers/block-api/block-edit-save/#save
	 *
	 * @returns {Mixed} JSX Component.
	 */
	save: () => {
		// Server-side rendered block
		return null;
	},
});
