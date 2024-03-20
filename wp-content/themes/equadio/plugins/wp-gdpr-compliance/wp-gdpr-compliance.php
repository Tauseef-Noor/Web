<?php
/* WP GDPR Compliance support functions
------------------------------------------------------------------------------- */

// Theme init priorities:
// 9 - register other filters (for installer, etc.)
if ( ! function_exists( 'equadio_wp_gdpr_compliance_theme_setup9' ) ) {
	add_action( 'after_setup_theme', 'equadio_wp_gdpr_compliance_theme_setup9', 9 );
	function equadio_wp_gdpr_compliance_theme_setup9() {
		if ( is_admin() ) {
			add_filter( 'equadio_filter_tgmpa_required_plugins', 'equadio_wp_gdpr_compliance_tgmpa_required_plugins' );
		}
	}
}

// Filter to add in the required plugins list
if ( ! function_exists( 'equadio_wp_gdpr_compliance_tgmpa_required_plugins' ) ) {	
	function equadio_wp_gdpr_compliance_tgmpa_required_plugins( $list = array() ) {
		if ( equadio_storage_isset( 'required_plugins', 'wp-gdpr-compliance' ) && equadio_storage_get_array( 'required_plugins', 'wp-gdpr-compliance', 'install' ) !== false ) {
			$list[] = array(
				'name'     => equadio_storage_get_array( 'required_plugins', 'wp-gdpr-compliance', 'title' ),
				'slug'     => 'wp-gdpr-compliance',
				'required' => false,
			);
		}
		return $list;
	}
}

// Check if this plugin installed and activated
if ( ! function_exists( 'equadio_exists_wp_gdpr_compliance' ) ) {
	function equadio_exists_wp_gdpr_compliance() {
		return defined( 'WP_GDPR_C_ROOT_FILE' ) || defined( 'WPGDPRC_ROOT_FILE' );
	}
}
