<?php

if ( ! defined( 'ABSPATH' ) ) exit;

if (is_admin()) {
  add_action('admin_init', 'altcha_settings_init');

  function altcha_settings_init()
  {
    register_setting(
      'altcha_options',
      AltchaPlugin::$option_api
    );

    register_setting(
      'altcha_options',
      AltchaPlugin::$option_api_custom_url
    );

    register_setting(
      'altcha_options',
      AltchaPlugin::$option_api_key
    );

    register_setting(
      'altcha_options',
      AltchaPlugin::$option_secret
    );

    register_setting(
      'altcha_options',
      AltchaPlugin::$option_complexity
    );

    register_setting(
      'altcha_options',
      AltchaPlugin::$option_expires
    );

    register_setting(
      'altcha_options',
      AltchaPlugin::$option_hidefooter
    );

    register_setting(
      'altcha_options',
      AltchaPlugin::$option_hidelogo
    );

    register_setting(
      'altcha_options',
      AltchaPlugin::$option_blockspam
    );

    register_setting(
      'altcha_options',
      AltchaPlugin::$option_send_ip
    );

    register_setting(
      'altcha_options',
      AltchaPlugin::$option_auto
    );

    register_setting(
      'altcha_options',
      AltchaPlugin::$option_floating
    );

    register_setting(
      'altcha_options',
      AltchaPlugin::$option_delay
    );

    register_setting(
      'altcha_options',
      AltchaPlugin::$option_integration_coblocks
    );

    register_setting(
      'altcha_options',
      AltchaPlugin::$option_integration_contact_form_7
    );

    register_setting(
      'altcha_options',
      AltchaPlugin::$option_integration_custom
    );

    register_setting(
      'altcha_options',
      AltchaPlugin::$option_integration_elementor
    );

    register_setting(
      'altcha_options',
      AltchaPlugin::$option_integration_enfold_theme
    );

    register_setting(
      'altcha_options',
      AltchaPlugin::$option_integration_formidable
    );

    register_setting(
      'altcha_options',
      AltchaPlugin::$option_integration_forminator
    );

    register_setting(
      'altcha_options',
      AltchaPlugin::$option_integration_gravityforms
    );

    register_setting(
      'altcha_options',
      AltchaPlugin::$option_integration_woocommerce_login
    );

    register_setting(
      'altcha_options',
      AltchaPlugin::$option_integration_woocommerce_register
    );

    register_setting(
      'altcha_options',
      AltchaPlugin::$option_integration_woocommerce_reset_password
    );

    register_setting(
      'altcha_options',
      AltchaPlugin::$option_integration_html_forms
    );

    register_setting(
      'altcha_options',
      AltchaPlugin::$option_integration_wordpress_comments
    );

    register_setting(
      'altcha_options',
      AltchaPlugin::$option_integration_wordpress_login
    );

    register_setting(
      'altcha_options',
      AltchaPlugin::$option_integration_wordpress_register
    );

    register_setting(
      'altcha_options',
      AltchaPlugin::$option_integration_wordpress_reset_password
    );

    register_setting(
      'altcha_options',
      AltchaPlugin::$option_integration_wpdiscuz
    );

    register_setting(
      'altcha_options',
      AltchaPlugin::$option_integration_wpforms
    );

    // Section
    add_settings_section(
      'altcha_general_settings_section',
      __('General', 'buddypress-recaptcha'),
      'altcha_general_section_callback',
      'altcha_admin'
    );

    add_settings_field(
      'altcha_settings_api_field',
      __('API Region', 'buddypress-recaptcha'),
      'altcha_settings_select_callback',
      'altcha_admin',
      'altcha_general_settings_section',
      array(
        "name" => AltchaPlugin::$option_api,
        "hint" => __('Select the API region.', 'buddypress-recaptcha'),
        "options" => array(
          "selfhosted" => __('Self-hosted', 'buddypress-recaptcha'),
          "custom" => __('Custom', 'buddypress-recaptcha'),
          "eu" => __('EU (eu.altcha.org)', 'buddypress-recaptcha'),
          "us" => __('USA (us.altcha.org)', 'buddypress-recaptcha'),
        )
      )
    );

    add_settings_field(
      'altcha_settings_challenge_url_field',
      __('Challenge URL', 'buddypress-recaptcha'),
      'altcha_settings_field_callback',
      'altcha_admin',
      'altcha_general_settings_section',
      array(
        "custom" => true,
        "name" => AltchaPlugin::$option_api_custom_url,
        "hint" => __('Configure your custom Challenge URL. Include the API key in the URL, if required.', 'buddypress-recaptcha'),
        "type" => "text"
      )
    );

    add_settings_field(
      'altcha_settings_api_key_field',
      __('API Key', 'buddypress-recaptcha'),
      'altcha_settings_field_callback',
      'altcha_admin',
      'altcha_general_settings_section',
      array(
        "spamfilter" => true,
        "name" => AltchaPlugin::$option_api_key,
        "hint" => __('Configure your API Key. Only for API modes. Leave this field empty in self-hosted.', 'buddypress-recaptcha'),
        "type" => "text"
      )
    );

    add_settings_field(
      'altcha_settings_secret_field',
      __('Secret Key', 'buddypress-recaptcha'),
      'altcha_settings_field_callback',
      'altcha_admin',
      'altcha_general_settings_section',
      array(
        "name" => AltchaPlugin::$option_secret,
        "hint" => __('Configure your API Key secret or HMAC signing secret.', 'buddypress-recaptcha'),
        "type" => "text"
      )
    );

    add_settings_field(
      'altcha_settings_complexity_field',
      __('Complexity', 'buddypress-recaptcha'),
      'altcha_settings_select_callback',
      'altcha_admin',
      'altcha_general_settings_section',
      array(
        "name" => AltchaPlugin::$option_complexity,
        "hint" => __('Select the PoW complexity for the widget.', 'buddypress-recaptcha'),
        "options" => array(
          "low" => __('Low', 'buddypress-recaptcha'),
          "medium" => __('Medium', 'buddypress-recaptcha'),
          "high" => __('High', 'buddypress-recaptcha'),
        )
      )
    );

    add_settings_field(
      'altcha_settings_expires_field',
      __('Expiration', 'buddypress-recaptcha'),
      'altcha_settings_select_callback',
      'altcha_admin',
      'altcha_general_settings_section',
      array(
        "name" => AltchaPlugin::$option_expires,
        "hint" => __('Select the life-span of the challenge.', 'buddypress-recaptcha'),
        "options" => array(
          "3600" => __('1 hour', 'buddypress-recaptcha'),
          "14400" => __('4 hours', 'buddypress-recaptcha'),
          "0" => __('None', 'buddypress-recaptcha'),
        )
      )
    );

    // Section
    add_settings_section(
      'altcha_spamfilter_settings_section',
      __('Spam Filter', 'buddypress-recaptcha'),
      'altcha_spam_filter_section_callback',
      'altcha_admin'
    );

    add_settings_field(
      'altcha_settings_blockspam_field',
      __('Block Spam Submissions', 'buddypress-recaptcha'),
      'altcha_settings_field_callback',
      'altcha_admin',
      'altcha_spamfilter_settings_section',
      array(
        "spamfilter" => true,
        "name" => AltchaPlugin::$option_blockspam,
        "description" => __('Yes', 'buddypress-recaptcha'),
        "hint" => __('Don\'t allow form submissions if the Spam Filter detects potential spam.', 'buddypress-recaptcha'),
        "type" => "checkbox"
      )
    );

    add_settings_field(
      'altcha_settings_send_ip_field',
      __('Classify IP address', 'buddypress-recaptcha'),
      'altcha_settings_field_callback',
      'altcha_admin',
      'altcha_spamfilter_settings_section',
      array(
        "spamfilter" => true,
        "name" => AltchaPlugin::$option_send_ip,
        "description" => __('Yes', 'buddypress-recaptcha'),
        "hint" => __('Whether to send the user\'s IP address for classification.', 'buddypress-recaptcha'),
        "type" => "checkbox"
      )
    );

    // Section
    add_settings_section(
      'altcha_widget_settings_section',
      __('Widget Customization', 'buddypress-recaptcha'),
      'altcha_widget_section_callback',
      'altcha_admin'
    );

    add_settings_field(
      'altcha_settings_auto_field',
      __('Auto verification', 'buddypress-recaptcha'),
      'altcha_settings_select_callback',
      'altcha_admin',
      'altcha_widget_settings_section',
      array(
        "name" => AltchaPlugin::$option_auto,
        "hint" => __('Select auto-verification behaviour.', 'buddypress-recaptcha'),
        "options" => array(
          "" => __('Disabled', 'buddypress-recaptcha'),
          "onload" => __('On page load', 'buddypress-recaptcha'),
          "onfocus" => __('On form focus', 'buddypress-recaptcha'),
          "onsubmit" => __('On form submit', 'buddypress-recaptcha'),
        )
      )
    );

    add_settings_field(
      'altcha_settings_floating_field',
      __('Floating UI', 'buddypress-recaptcha'),
      'altcha_settings_field_callback',
      'altcha_admin',
      'altcha_widget_settings_section',
      array(
        "name" => AltchaPlugin::$option_floating,
        "description" => __('Yes', 'buddypress-recaptcha'),
        "hint" => __('Enable Floating UI.', 'buddypress-recaptcha'),
        "type" => "checkbox"
      )
    );

    add_settings_field(
      'altcha_settings_delay_field',
      __('Delay', 'buddypress-recaptcha'),
      'altcha_settings_field_callback',
      'altcha_admin',
      'altcha_widget_settings_section',
      array(
        "name" => AltchaPlugin::$option_delay,
        "description" => __('Yes', 'buddypress-recaptcha'),
        "hint" => __('Add a delay of 1.5 seconds to verification.', 'buddypress-recaptcha'),
        "type" => "checkbox"
      )
    );

    add_settings_field(
      'altcha_settings_hidelogo_field',
      __('Hide logo', 'buddypress-recaptcha'),
      'altcha_settings_field_callback',
      'altcha_admin',
      'altcha_widget_settings_section',
      array(
        "name" => AltchaPlugin::$option_hidelogo,
        "description" => __('Yes', 'buddypress-recaptcha'),
        "hint" => __('Not available with Free API Keys.', 'buddypress-recaptcha'),
        "type" => "checkbox"
      )
    );

    add_settings_field(
      'altcha_settings_hidefooter_field',
      __('Hide footer', 'buddypress-recaptcha'),
      'altcha_settings_field_callback',
      'altcha_admin',
      'altcha_widget_settings_section',
      array(
        "name" => AltchaPlugin::$option_hidefooter,
        "description" => __('Yes', 'buddypress-recaptcha'),
        "hint" => __('Hide Powered by ALTCHA. Not available with Free API Keys.', 'buddypress-recaptcha'),
        "type" => "checkbox"
      )
    );

    // Section
    add_settings_section(
      'altcha_integrations_settings_section',
      __('Integrations', 'buddypress-recaptcha'),
      'altcha_integrations_section_callback',
      'altcha_admin'
    );

    add_settings_field(
        'altcha_settings_coblocks_integration_field',
        __('CoBlocks', 'buddypress-recaptcha'),
        'altcha_settings_select_callback',
        'altcha_admin',
        'altcha_integrations_settings_section',
        array(
            "name" => AltchaPlugin::$option_integration_coblocks,
            "disabled" => !altcha_plugin_active('coblocks'),
            "spamfilter_options" => array(
              "spamfilter",
              "captcha_spamfilter",
            ),
            "options" => array(
              "" => __('Disable', 'buddypress-recaptcha'),
              "captcha" => __('Captcha', 'buddypress-recaptcha'),
              "captcha_spamfilter" => __('Captcha + Spam Filter', 'buddypress-recaptcha'),
            ),
        )
    );

    add_settings_field(
        'altcha_settings_contact_form_7_integration_field',
        __('Contact Form 7', 'buddypress-recaptcha'),
        'altcha_settings_select_callback',
        'altcha_admin',
        'altcha_integrations_settings_section',
        array(
            "name" => AltchaPlugin::$option_integration_contact_form_7,
            "disabled" => !altcha_plugin_active('contact-form-7'),
            "spamfilter_options" => array(
              "spamfilter",
              "captcha_spamfilter",
            ),
            "options" => array(
              "" => __('Disable', 'buddypress-recaptcha'),
              "captcha" => __('Captcha', 'buddypress-recaptcha'),
              "captcha_spamfilter" => __('Captcha + Spam Filter', 'buddypress-recaptcha'),
              "shortcode" => __('Shortcode', 'buddypress-recaptcha'),
            ),
        )
    );

    add_settings_field(
        'altcha_settings_elementor_integration_field',
        __('Elementor Pro Forms', 'buddypress-recaptcha'),
        'altcha_settings_select_callback',
        'altcha_admin',
        'altcha_integrations_settings_section',
        array(
            "name" => AltchaPlugin::$option_integration_elementor,
            "disabled" => !altcha_plugin_active('elementor'),
            "spamfilter_options" => array(
              "spamfilter",
              "captcha_spamfilter",
            ),
            "options" => array(
              "" => __('Disable', 'buddypress-recaptcha'),
              "captcha" => __('Captcha', 'buddypress-recaptcha'),
              "captcha_spamfilter" => __('Captcha + Spam Filter', 'buddypress-recaptcha'),
            ),
        )
    );

    add_settings_field(
      'altcha_settings_enfold_theme_integration_field',
      __('Enfold Theme', 'buddypress-recaptcha'),
      'altcha_settings_select_callback',
      'altcha_admin',
      'altcha_integrations_settings_section',
      array(
        "name" => AltchaPlugin::$option_integration_enfold_theme,
        "disabled" => empty(array_filter(wp_get_themes(), function($theme) { 
          return stripos($theme, 'enfold') !== false;
          })),
        "spamfilter_options" => array(
          "spamfilter",
          "captcha_spamfilter",
        ),
        "options" => array(
          "" => __('Disable', 'buddypress-recaptcha'),
          "captcha" => __('Captcha', 'buddypress-recaptcha'),
          "captcha_spamfilter" => __('Captcha + Spam Filter', 'buddypress-recaptcha'),
        ),
      )
    );

    add_settings_field(
        'altcha_settings_formidable_integration_field',
        __('Formidable Forms', 'buddypress-recaptcha'),
        'altcha_settings_select_callback',
        'altcha_admin',
        'altcha_integrations_settings_section',
        array(
            "name" => AltchaPlugin::$option_integration_formidable,
            "disabled" => !altcha_plugin_active('formidable'),
            "spamfilter_options" => array(
              "spamfilter",
              "captcha_spamfilter",
            ),
            "options" => array(
              "" => __('Disable', 'buddypress-recaptcha'),
              "captcha" => __('Captcha', 'buddypress-recaptcha'),
              "captcha_spamfilter" => __('Captcha + Spam Filter', 'buddypress-recaptcha'),
            ),
        )
    );

    add_settings_field(
        'altcha_settings_forminator_integration_field',
        __('Forminator', 'buddypress-recaptcha'),
        'altcha_settings_select_callback',
        'altcha_admin',
        'altcha_integrations_settings_section',
        array(
            "name" => AltchaPlugin::$option_integration_forminator,
            "disabled" => !altcha_plugin_active('forminator'),
            "spamfilter_options" => array(
              "spamfilter",
              "captcha_spamfilter",
            ),
            "options" => array(
              "" => __('Disable', 'buddypress-recaptcha'),
              "captcha" => __('Captcha', 'buddypress-recaptcha'),
              "captcha_spamfilter" => __('Captcha + Spam Filter', 'buddypress-recaptcha'),
            ),
        )
    );

    add_settings_field(
        'altcha_settings_gravityforms_integration_field',
        __('Gravity Forms', 'buddypress-recaptcha'),
        'altcha_settings_select_callback',
        'altcha_admin',
        'altcha_integrations_settings_section',
        array(
            "name" => AltchaPlugin::$option_integration_gravityforms,
            "disabled" => !altcha_plugin_active('gravityforms'),
            "spamfilter_options" => array(
              "spamfilter",
              "captcha_spamfilter",
            ),
            "options" => array(
              "" => __('Disable', 'buddypress-recaptcha'),
              "captcha" => __('Captcha', 'buddypress-recaptcha'),
              "captcha_spamfilter" => __('Captcha + Spam Filter', 'buddypress-recaptcha'),
            ),
        )
    );

    add_settings_field(
        'altcha_settings_html_forms_integration_field',
        __('HTML Forms', 'buddypress-recaptcha'),
        'altcha_settings_select_callback',
        'altcha_admin',
        'altcha_integrations_settings_section',
        array(
            "name" => AltchaPlugin::$option_integration_html_forms,
            "disabled" => !altcha_plugin_active('html-forms'),
            "spamfilter_options" => array(
              "spamfilter",
              "captcha_spamfilter",
            ),
            "options" => array(
              "" => __('Disable', 'buddypress-recaptcha'),
              "captcha" => __('Captcha', 'buddypress-recaptcha'),
              "captcha_spamfilter" => __('Captcha + Spam Filter', 'buddypress-recaptcha'),
              "shortcode" => __('Shortcode', 'buddypress-recaptcha'),
            ),
        )
    );

    add_settings_field(
        'altcha_settings_wpdiscuz_integration_field',
        __('WPDiscuz', 'buddypress-recaptcha'),
        'altcha_settings_select_callback',
        'altcha_admin',
        'altcha_integrations_settings_section',
        array(
            "name" => AltchaPlugin::$option_integration_wpdiscuz,
            "disabled" => !altcha_plugin_active('wpdiscuz'),
            "spamfilter_options" => array(
              "spamfilter",
              "captcha_spamfilter",
            ),
            "options" => array(
              "" => __('Disable', 'buddypress-recaptcha'),
              "captcha" => __('Captcha', 'buddypress-recaptcha'),
              "captcha_spamfilter" => __('Captcha + Spam Filter', 'buddypress-recaptcha'),
            ),
        )
    );

    add_settings_field(
        'altcha_settings_wpforms_integration_field',
        __('WP Forms', 'buddypress-recaptcha'),
        'altcha_settings_select_callback',
        'altcha_admin',
        'altcha_integrations_settings_section',
        array(
            "name" => AltchaPlugin::$option_integration_wpforms,
            "disabled" => !altcha_plugin_active('wpforms'),
            "spamfilter_options" => array(
              "spamfilter",
              "captcha_spamfilter",
            ),
            "options" => array(
              "" => __('Disable', 'buddypress-recaptcha'),
              "captcha" => __('Captcha', 'buddypress-recaptcha'),
              "captcha_spamfilter" => __('Captcha + Spam Filter', 'buddypress-recaptcha'),
            ),
        )
    );

    add_settings_field(
        'altcha_settings_woocommerce_register_integration_field',
        __('WooCommerce register page', 'buddypress-recaptcha'),
        'altcha_settings_select_callback',
        'altcha_admin',
        'altcha_integrations_settings_section',
        array(
            "name" => AltchaPlugin::$option_integration_woocommerce_register,
            "disabled" => !altcha_plugin_active('woocommerce'),
            "spamfilter_options" => array(
              "captcha_spamfilter",
            ),
            "options" => array(
              "" => __('Disable', 'buddypress-recaptcha'),
              "captcha" => __('Captcha', 'buddypress-recaptcha'),
              "captcha_spamfilter" => __('Captcha + Spam Filter', 'buddypress-recaptcha'),
            ),
        )
    );

    add_settings_field(
        'altcha_settings_woocommerce_reset_password_integration_field',
        __('WooCommerce reset password page', 'buddypress-recaptcha'),
        'altcha_settings_select_callback',
        'altcha_admin',
        'altcha_integrations_settings_section',
        array(
            "name" => AltchaPlugin::$option_integration_woocommerce_reset_password,
            "disabled" => !altcha_plugin_active('woocommerce'),
            "spamfilter_options" => array(
              "captcha_spamfilter",
            ),
            "options" => array(
              "" => __('Disable', 'buddypress-recaptcha'),
              "captcha" => __('Captcha', 'buddypress-recaptcha'),
              "captcha_spamfilter" => __('Captcha + Spam Filter', 'buddypress-recaptcha'),
            ),
        )
    );

    add_settings_field(
        'altcha_settings_woocommerce_login_integration_field',
        __('WooCommerce login page', 'buddypress-recaptcha'),
        'altcha_settings_select_callback',
        'altcha_admin',
        'altcha_integrations_settings_section',
        array(
            "name" => AltchaPlugin::$option_integration_woocommerce_login,
            "disabled" => !altcha_plugin_active('woocommerce'),
            "spamfilter_options" => array(
              "captcha_spamfilter",
            ),
            "options" => array(
              "" => __('Disable', 'buddypress-recaptcha'),
              "captcha" => __('Captcha', 'buddypress-recaptcha'),
              "captcha_spamfilter" => __('Captcha + Spam Filter', 'buddypress-recaptcha'),
            ),
        )
    );

    add_settings_field(
        'altcha_settings_custom_integration_field',
        __('Custom HTML', 'buddypress-recaptcha'),
        'altcha_settings_select_callback',
        'altcha_admin',
        'altcha_integrations_settings_section',
        array(
            "name" => AltchaPlugin::$option_integration_custom,
            "hint" => sprintf(
              /* translators: the placeholder will be replaced with the shortcode */
              __('Use %s shortcode anywhere in your HTML.', 'buddypress-recaptcha'), '[altcha]',
            ),
            "spamfilter_options" => array(
              "spamfilter",
              "captcha_spamfilter",
            ),
            "options" => array(
              "" => __('Disable', 'buddypress-recaptcha'),
              "captcha" => __('Captcha', 'buddypress-recaptcha'),
              "captcha_spamfilter" => __('Captcha + Spam Filter', 'buddypress-recaptcha'),
            ),
        )
    );

    do_action('altcha_settings_integrations');

    // Section
    add_settings_section(
      'altcha_wordpress_settings_section',
      __('Wordpress', 'buddypress-recaptcha'),
      'altcha_wordpress_section_callback',
      'altcha_admin'
    );

    add_settings_field(
        'altcha_settings_wordpress_register_integration_field',
        __('Register page', 'buddypress-recaptcha'),
        'altcha_settings_select_callback',
        'altcha_admin',
        'altcha_wordpress_settings_section',
        array(
            "name" => AltchaPlugin::$option_integration_wordpress_register,
            "spamfilter_options" => array(
              "captcha_spamfilter",
            ),
            "options" => array(
              "" => __('Disable', 'buddypress-recaptcha'),
              "captcha" => __('Captcha', 'buddypress-recaptcha'),
              "captcha_spamfilter" => __('Captcha + Spam Filter', 'buddypress-recaptcha'),
            ),
        )
    );

    add_settings_field(
        'altcha_settings_wordpress_reset_password_integration_field',
        __('Reset password page', 'buddypress-recaptcha'),
        'altcha_settings_select_callback',
        'altcha_admin',
        'altcha_wordpress_settings_section',
        array(
            "name" => AltchaPlugin::$option_integration_wordpress_reset_password,
            "spamfilter_options" => array(
              "captcha_spamfilter",
            ),
            "options" => array(
              "" => __('Disable', 'buddypress-recaptcha'),
              "captcha" => __('Captcha', 'buddypress-recaptcha'),
              "captcha_spamfilter" => __('Captcha + Spam Filter', 'buddypress-recaptcha'),
            ),
        )
    );

    add_settings_field(
        'altcha_settings_wordpress_login_integration_field',
        __('Login page', 'buddypress-recaptcha'),
        'altcha_settings_select_callback',
        'altcha_admin',
        'altcha_wordpress_settings_section',
        array(
            "name" => AltchaPlugin::$option_integration_wordpress_login,
            "spamfilter_options" => array(
              "captcha_spamfilter",
            ),
            "options" => array(
              "" => __('Disable', 'buddypress-recaptcha'),
              "captcha" => __('Captcha', 'buddypress-recaptcha'),
              "captcha_spamfilter" => __('Captcha + Spam Filter', 'buddypress-recaptcha'),
            ),
        )
    );

    add_settings_field(
        'altcha_settings_wordpress_comments_integration_field',
        __('Comments', 'buddypress-recaptcha'),
        'altcha_settings_select_callback',
        'altcha_admin',
        'altcha_wordpress_settings_section',
        array(
            "name" => AltchaPlugin::$option_integration_wordpress_comments,
            "spamfilter_options" => array(
              "captcha_spamfilter",
            ),
            "options" => array(
              "" => __('Disable', 'buddypress-recaptcha'),
              "captcha" => __('Captcha', 'buddypress-recaptcha'),
              "captcha_spamfilter" => __('Captcha + Spam Filter', 'buddypress-recaptcha'),
            ),
        )
    );
  }
}
