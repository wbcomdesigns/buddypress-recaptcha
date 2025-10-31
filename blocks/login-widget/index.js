/**
 * WordPress dependencies
 */
( function( wp ) {
	const { registerBlockType } = wp.blocks;
	const { createElement: el } = wp.element;
	const { InspectorControls } = wp.blockEditor;
	const { PanelBody, TextControl, ToggleControl, Placeholder, Spinner } = wp.components;
	const { __ } = wp.i18n;
	const { ServerSideRender } = wp.editor;

	/**
	 * Register: CAPTCHA Login Block
	 */
	registerBlockType( 'wbc/login-widget', {
		edit: function( props ) {
			const { attributes, setAttributes, className } = props;

			return el(
				'div',
				{},
				el(
					InspectorControls,
					{},
					el(
						PanelBody,
						{ title: __( 'Login Settings', 'buddypress-recaptcha' ) },
						el( TextControl, {
							label: __( 'Welcome Message', 'buddypress-recaptcha' ),
							value: attributes.welcomeMessage,
							onChange: function( value ) {
								setAttributes( { welcomeMessage: value } );
							},
							help: __( 'Use {username} as placeholder for the username', 'buddypress-recaptcha' )
						} ),
						el( TextControl, {
							label: __( 'Redirect URL', 'buddypress-recaptcha' ),
							value: attributes.redirectUrl,
							onChange: function( value ) {
								setAttributes( { redirectUrl: value } );
							},
							help: __( 'Leave empty for homepage', 'buddypress-recaptcha' )
						} ),
						el( ToggleControl, {
							label: __( 'Show Lost Password Link', 'buddypress-recaptcha' ),
							checked: attributes.showLostPassword,
							onChange: function( value ) {
								setAttributes( { showLostPassword: value } );
							}
						} ),
						el( ToggleControl, {
							label: __( 'Show Register Link', 'buddypress-recaptcha' ),
							checked: attributes.showRegisterLink,
							onChange: function( value ) {
								setAttributes( { showRegisterLink: value } );
							}
						} ),
						el( ToggleControl, {
							label: __( 'Show Profile Link', 'buddypress-recaptcha' ),
							checked: attributes.showProfileLink,
							onChange: function( value ) {
								setAttributes( { showProfileLink: value } );
							}
						} )
					)
				),
				el(
					'div',
					{ className: className },
					el( ServerSideRender, {
						block: 'wbc/login-widget',
						attributes: attributes
					} )
				)
			);
		},

		save: function() {
			return null;
		}
	} );
} )( window.wp );
