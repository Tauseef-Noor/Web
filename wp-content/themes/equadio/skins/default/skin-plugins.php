<?php
/**
 * Required plugins
 *
 * @package EQUADIO
 * @since EQUADIO 1.76.0
 */

// THEME-SUPPORTED PLUGINS
// If plugin not need - remove its settings from next array
//----------------------------------------------------------
$equadio_theme_required_plugins_groups = array(
	'core'          => esc_html__( 'Core', 'equadio' ),
	'page_builders' => esc_html__( 'Page Builders', 'equadio' ),
	'ecommerce'     => esc_html__( 'E-Commerce & Donations', 'equadio' ),
	'socials'       => esc_html__( 'Socials and Communities', 'equadio' ),
	'events'        => esc_html__( 'Events and Appointments', 'equadio' ),
	'content'       => esc_html__( 'Content', 'equadio' ),
	'other'         => esc_html__( 'Other', 'equadio' ),
);
$equadio_theme_required_plugins        = array(
	'trx_addons'                 => array(
		'title'       => esc_html__( 'ThemeREX Addons', 'equadio' ),
		'description' => esc_html__( "Will allow you to install recommended plugins, demo content, and improve the theme's functionality overall with multiple theme options", 'equadio' ),
		'required'    => true,
		'logo'        => 'trx_addons.png',
		'group'       => $equadio_theme_required_plugins_groups['core'],
	),
	'elementor'                  => array(
		'title'       => esc_html__( 'Elementor', 'equadio' ),
		'description' => esc_html__( "Is a beautiful PageBuilder, even the free version of which allows you to create great pages using a variety of modules.", 'equadio' ),
		'required'    => false,
		'logo'        => 'elementor.png',
		'group'       => $equadio_theme_required_plugins_groups['page_builders'],
	),
    'essential-addons-for-elementor-lite'       => array(
        'title'       => esc_html__( 'Essential Addons for Elementor', 'equadio' ),
        'description' => esc_html__( "The ultimate elements library for Elementor PageBuilder", 'equadio' ),
        'required'    => false,
        'logo'        => equadio_get_file_url( 'plugins/essential-addons-for-elementor-lite/essential-addons-for-elementor-lite.png' ),
        'group'       => $equadio_theme_required_plugins_groups['page_builders'],
    ),
	'gutenberg'                  => array(
		'title'       => esc_html__( 'Gutenberg', 'equadio' ),
		'description' => esc_html__( "It's a posts editor coming in place of the classic TinyMCE. Can be installed and used in parallel with Elementor", 'equadio' ),
		'required'    => false,
		'install'     => false,          // Do not offer installation of the plugin in the Theme Dashboard and TGMPA
		'logo'        => 'gutenberg.png',
		'group'       => $equadio_theme_required_plugins_groups['page_builders'],
	),
	'js_composer'                => array(
		'title'       => esc_html__( 'WPBakery PageBuilder', 'equadio' ),
		'description' => esc_html__( "Popular PageBuilder which allows you to create excellent pages", 'equadio' ),
		'required'    => false,
		'install'     => false,          // Do not offer installation of the plugin in the Theme Dashboard and TGMPA
		'logo'        => 'js_composer.jpg',
		'group'       => $equadio_theme_required_plugins_groups['page_builders'],
	),
	'give'                       => array(
		'title'       => esc_html__( 'Give', 'equadio' ),
		'description' => '',
		'required'    => false,
		'logo'        => 'give.png',
		'group'       => $equadio_theme_required_plugins_groups['ecommerce'],
	),
    'speakout'                       => array(
        'title'       => esc_html__( 'SpeakOut! Email Petitions', 'equadio' ),
        'description' => '',
        'required'    => false,
        'logo'        => equadio_get_file_url( 'plugins/speakout/speakout.png' ),
        'group'       => $equadio_theme_required_plugins_groups['ecommerce'],
    ),
    'instagram-feed'             => array(
		'title'       => esc_html__( 'Instagram Feed', 'equadio' ),
		'description' => esc_html__( "Displays the latest photos from your profile on Instagram", 'equadio' ),
		'required'    => false,
		'logo'        => 'instagram-feed.png',
		'group'       => $equadio_theme_required_plugins_groups['socials'],
	),
	'mailchimp-for-wp'           => array(
		'title'       => esc_html__( 'MailChimp for WP', 'equadio' ),
		'description' => esc_html__( "Allows visitors to subscribe to newsletters", 'equadio' ),
		'required'    => false,
		'logo'        => 'mailchimp-for-wp.png',
		'group'       => $equadio_theme_required_plugins_groups['socials'],
	),
	'the-events-calendar'        => array(
		'title'       => esc_html__( 'The Events Calendar', 'equadio' ),
		'description' => '',
		'required'    => false,
		'logo'        => 'the-events-calendar.png',
		'group'       => $equadio_theme_required_plugins_groups['events'],
	),
	'contact-form-7'             => array(
		'title'       => esc_html__( 'Contact Form 7', 'equadio' ),
		'description' => esc_html__( "CF7 allows you to create an unlimited number of contact forms", 'equadio' ),
		'required'    => false,
		'logo'        => 'contact-form-7.png',
		'group'       => $equadio_theme_required_plugins_groups['content'],
	),
	'essential-grid'             => array(
		'title'       => esc_html__( 'Essential Grid', 'equadio' ),
		'description' => '',
		'required'    => false,
		'logo'        => 'essential-grid.png',
		'group'       => $equadio_theme_required_plugins_groups['content'],
	),
	'revslider'                  => array(
		'title'       => esc_html__( 'Revolution Slider', 'equadio' ),
		'description' => '',
		'required'    => false,
		'logo'        => 'revslider.png',
		'group'       => $equadio_theme_required_plugins_groups['content'],
	),
	'sitepress-multilingual-cms' => array(
		'title'       => esc_html__( 'WPML - Sitepress Multilingual CMS', 'equadio' ),
		'description' => esc_html__( "Allows you to make your website multilingual", 'equadio' ),
		'required'    => false,
		'install'     => false,      // Do not offer installation of the plugin in the Theme Dashboard and TGMPA
		'logo'        => 'sitepress-multilingual-cms.png',
		'group'       => $equadio_theme_required_plugins_groups['content'],
	),
	'wp-gdpr-compliance'         => array(
		'title'       => esc_html__( 'Cookie Information', 'equadio' ),
		'description' => esc_html__( "Allow visitors to decide for themselves what personal data they want to store on your site", 'equadio' ),
		'required'    => false,
		'logo'        => 'wp-gdpr-compliance.png',
		'group'       => $equadio_theme_required_plugins_groups['other'],
	),
	'trx_updater'                => array(
		'title'       => esc_html__( 'ThemeREX Updater', 'equadio' ),
		'description' => esc_html__( "Update theme and theme-specific plugins from developer's upgrade server.", 'equadio' ),
		'required'    => false,
		'logo'        => 'trx_updater.png',
		'group'       => $equadio_theme_required_plugins_groups['other'],
	),
	'trx_popup'                  => array(
		'title'       => esc_html__( 'ThemeREX Popup', 'equadio' ),
		'description' => esc_html__( "Add popup to your site.", 'equadio' ),
		'required'    => false,
		'logo'        => 'trx_popup.png',
		'group'       => $equadio_theme_required_plugins_groups['other'],
	),
);

if ( EQUADIO_THEME_FREE ) {
	unset( $equadio_theme_required_plugins['js_composer'] );
	unset( $equadio_theme_required_plugins['give'] );
	unset( $equadio_theme_required_plugins['the-events-calendar'] );
	unset( $equadio_theme_required_plugins['essential-grid'] );
	unset( $equadio_theme_required_plugins['revslider'] );
	unset( $equadio_theme_required_plugins['sitepress-multilingual-cms'] );
	unset( $equadio_theme_required_plugins['trx_updater'] );
	unset( $equadio_theme_required_plugins['trx_popup'] );
}

// Add plugins list to the global storage
equadio_storage_set( 'required_plugins', $equadio_theme_required_plugins );
