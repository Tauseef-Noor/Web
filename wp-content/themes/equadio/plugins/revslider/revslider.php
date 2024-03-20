<?php
/* Revolution Slider support functions
------------------------------------------------------------------------------- */

// Theme init priorities:
// 9 - register other filters (for installer, etc.)
if ( ! function_exists( 'equadio_revslider_theme_setup9' ) ) {
	add_action( 'after_setup_theme', 'equadio_revslider_theme_setup9', 9 );
	function equadio_revslider_theme_setup9() {
		if ( is_admin() ) {
			add_filter( 'equadio_filter_tgmpa_required_plugins', 'equadio_revslider_tgmpa_required_plugins' );
		}
	}
}

// Filter to add in the required plugins list
if ( ! function_exists( 'equadio_revslider_tgmpa_required_plugins' ) ) {	
	function equadio_revslider_tgmpa_required_plugins( $list = array() ) {
		if ( equadio_storage_isset( 'required_plugins', 'revslider' ) && equadio_storage_get_array( 'required_plugins', 'revslider', 'install' ) !== false && equadio_is_theme_activated() ) {
			$path = equadio_get_plugin_source_path( 'plugins/revslider/revslider.zip' );
			if ( ! empty( $path ) || equadio_get_theme_setting( 'tgmpa_upload' ) ) {
				$list[] = array(
					'name'     => equadio_storage_get_array( 'required_plugins', 'revslider', 'title' ),
					'slug'     => 'revslider',
					'source'   => ! empty( $path ) ? $path : 'upload://revslider.zip',
					'required' => false,
				);
			}
		}
		return $list;
	}
}

// Check if RevSlider installed and activated
if ( ! function_exists( 'equadio_exists_revslider' ) ) {
	function equadio_exists_revslider() {
		return function_exists( 'rev_slider_shortcode' );
	}
}
