<?php
/* Instagram Feed support functions
------------------------------------------------------------------------------- */

// Theme init priorities:
// 9 - register other filters (for installer, etc.)
if ( ! function_exists( 'equadio_instagram_feed_theme_setup9' ) ) {
	add_action( 'after_setup_theme', 'equadio_instagram_feed_theme_setup9', 9 );
	function equadio_instagram_feed_theme_setup9() {
		if ( equadio_exists_instagram_feed() ) {
			add_action( 'wp_enqueue_scripts', 'equadio_instagram_responsive_styles', 2000 );
			add_filter( 'equadio_filter_merge_styles_responsive', 'equadio_instagram_merge_styles_responsive' );
		}
		if ( is_admin() ) {
			add_filter( 'equadio_filter_tgmpa_required_plugins', 'equadio_instagram_feed_tgmpa_required_plugins' );
		}
	}
}

// Filter to add in the required plugins list
if ( ! function_exists( 'equadio_instagram_feed_tgmpa_required_plugins' ) ) {	
	function equadio_instagram_feed_tgmpa_required_plugins( $list = array() ) {
		if ( equadio_storage_isset( 'required_plugins', 'instagram-feed' ) && equadio_storage_get_array( 'required_plugins', 'instagram-feed', 'install' ) !== false ) {
			$list[] = array(
				'name'     => equadio_storage_get_array( 'required_plugins', 'instagram-feed', 'title' ),
				'slug'     => 'instagram-feed',
				'required' => false,
			);
		}
		return $list;
	}
}

// Check if Instagram Feed installed and activated
if ( ! function_exists( 'equadio_exists_instagram_feed' ) ) {
	function equadio_exists_instagram_feed() {
		return defined( 'SBIVER' );
	}
}

// Enqueue responsive styles for frontend
if ( ! function_exists( 'equadio_instagram_responsive_styles' ) ) {	
	function equadio_instagram_responsive_styles() {
		if ( equadio_is_on( equadio_get_theme_option( 'debug_mode' ) ) ) {
			$equadio_url = equadio_get_file_url( 'plugins/instagram/instagram-responsive.css' );
			if ( '' != $equadio_url ) {
				wp_enqueue_style( 'equadio-instagram-responsive', $equadio_url, array(), null );
			}
		}
	}
}

// Merge responsive styles
if ( ! function_exists( 'equadio_instagram_merge_styles_responsive' ) ) {	
	function equadio_instagram_merge_styles_responsive( $list ) {
		$list[] = 'plugins/instagram/instagram-responsive.css';
		return $list;
	}
}