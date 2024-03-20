<?php
/* Essential Grid support functions
------------------------------------------------------------------------------- */


// Theme init priorities:
// 9 - register other filters (for installer, etc.)
if ( ! function_exists( 'equadio_essential_grid_theme_setup9' ) ) {
	add_action( 'after_setup_theme', 'equadio_essential_grid_theme_setup9', 9 );
	function equadio_essential_grid_theme_setup9() {
		if ( equadio_exists_essential_grid() ) {
			add_action( 'wp_enqueue_scripts', 'equadio_essential_grid_frontend_scripts', 1100 );
			add_filter( 'equadio_filter_merge_styles', 'equadio_essential_grid_merge_styles' );
		}
		if ( is_admin() ) {
			add_filter( 'equadio_filter_tgmpa_required_plugins', 'equadio_essential_grid_tgmpa_required_plugins' );
		}
	}
}

// Filter to add in the required plugins list
if ( ! function_exists( 'equadio_essential_grid_tgmpa_required_plugins' ) ) {	
	function equadio_essential_grid_tgmpa_required_plugins( $list = array() ) {
		if ( equadio_storage_isset( 'required_plugins', 'essential-grid' ) && equadio_storage_get_array( 'required_plugins', 'essential-grid', 'install' ) !== false && equadio_is_theme_activated() ) {
			$path = equadio_get_plugin_source_path( 'plugins/essential-grid/essential-grid.zip' );
			if ( ! empty( $path ) || equadio_get_theme_setting( 'tgmpa_upload' ) ) {
				$list[] = array(
					'name'     => equadio_storage_get_array( 'required_plugins', 'essential-grid', 'title' ),
					'slug'     => 'essential-grid',
					'source'   => ! empty( $path ) ? $path : 'upload://essential-grid.zip',
					'required' => false,
				);
			}
		}
		return $list;
	}
}

// Check if plugin installed and activated
if ( ! function_exists( 'equadio_exists_essential_grid' ) ) {
	function equadio_exists_essential_grid() {
		return defined('EG_PLUGIN_PATH') || defined( 'ESG_PLUGIN_PATH' );
	}
}

// Enqueue styles for frontend
if ( ! function_exists( 'equadio_essential_grid_frontend_scripts' ) ) {	
	function equadio_essential_grid_frontend_scripts() {
		if ( equadio_is_on( equadio_get_theme_option( 'debug_mode' ) ) ) {
			$equadio_url = equadio_get_file_url( 'plugins/essential-grid/essential-grid.css' );
			if ( '' != $equadio_url ) {
				wp_enqueue_style( 'equadio-essential-grid', $equadio_url, array(), null );
			}
		}
	}
}

// Merge custom styles
if ( ! function_exists( 'equadio_essential_grid_merge_styles' ) ) {	
	function equadio_essential_grid_merge_styles( $list ) {
		$list[] = 'plugins/essential-grid/essential-grid.css';
		return $list;
	}
}

