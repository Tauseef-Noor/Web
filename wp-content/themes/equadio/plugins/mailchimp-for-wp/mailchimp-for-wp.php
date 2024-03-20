<?php
/* Mail Chimp support functions
------------------------------------------------------------------------------- */

// Theme init priorities:
// 9 - register other filters (for installer, etc.)
if ( ! function_exists( 'equadio_mailchimp_theme_setup9' ) ) {
	add_action( 'after_setup_theme', 'equadio_mailchimp_theme_setup9', 9 );
	function equadio_mailchimp_theme_setup9() {
		if ( equadio_exists_mailchimp() ) {
			add_action( 'wp_enqueue_scripts', 'equadio_mailchimp_frontend_scripts', 1100 );
			add_filter( 'equadio_filter_merge_styles', 'equadio_mailchimp_merge_styles' );
		}
		if ( is_admin() ) {
			add_filter( 'equadio_filter_tgmpa_required_plugins', 'equadio_mailchimp_tgmpa_required_plugins' );
		}
	}
}

// Filter to add in the required plugins list
if ( ! function_exists( 'equadio_mailchimp_tgmpa_required_plugins' ) ) {	
	function equadio_mailchimp_tgmpa_required_plugins( $list = array() ) {
		if ( equadio_storage_isset( 'required_plugins', 'mailchimp-for-wp' ) && equadio_storage_get_array( 'required_plugins', 'mailchimp-for-wp', 'install' ) !== false ) {
			$list[] = array(
				'name'     => equadio_storage_get_array( 'required_plugins', 'mailchimp-for-wp', 'title' ),
				'slug'     => 'mailchimp-for-wp',
				'required' => false,
			);
		}
		return $list;
	}
}

// Check if plugin installed and activated
if ( ! function_exists( 'equadio_exists_mailchimp' ) ) {
	function equadio_exists_mailchimp() {
		return function_exists( '__mc4wp_load_plugin' ) || defined( 'MC4WP_VERSION' );
	}
}



// Custom styles and scripts
//------------------------------------------------------------------------

// Enqueue styles for frontend
if ( ! function_exists( 'equadio_mailchimp_frontend_scripts' ) ) {	
	function equadio_mailchimp_frontend_scripts() {
		if ( equadio_is_on( equadio_get_theme_option( 'debug_mode' ) ) ) {
			$equadio_url = equadio_get_file_url( 'plugins/mailchimp-for-wp/mailchimp-for-wp.css' );
			if ( '' != $equadio_url ) {
				wp_enqueue_style( 'equadio-mailchimp-for-wp', $equadio_url, array(), null );
			}
		}
	}
}

// Merge custom styles
if ( ! function_exists( 'equadio_mailchimp_merge_styles' ) ) {	
	function equadio_mailchimp_merge_styles( $list ) {
		$list[] = 'plugins/mailchimp-for-wp/mailchimp-for-wp.css';
		return $list;
	}
}


// Add plugin-specific colors and fonts to the custom CSS
if ( equadio_exists_mailchimp() ) {
	require_once equadio_get_file_dir( 'plugins/mailchimp-for-wp/mailchimp-for-wp-style.php' );
}

