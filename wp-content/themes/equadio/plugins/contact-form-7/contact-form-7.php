<?php
/* Contact Form 7 support functions
------------------------------------------------------------------------------- */

// Theme init priorities:
// 9 - register other filters (for installer, etc.)
if ( ! function_exists( 'equadio_cf7_theme_setup9' ) ) {
	add_action( 'after_setup_theme', 'equadio_cf7_theme_setup9', 9 );
	function equadio_cf7_theme_setup9() {
		if ( equadio_exists_cf7() ) {
			add_filter('wpcf7_autop_or_not', '__return_false');
			add_action( 'wp_enqueue_scripts', 'equadio_cf7_frontend_scripts', 1100 );
			add_filter( 'equadio_filter_merge_scripts', 'equadio_cf7_merge_scripts' );
		}
		if ( is_admin() ) {
			add_filter( 'equadio_filter_tgmpa_required_plugins', 'equadio_cf7_tgmpa_required_plugins' );
			add_filter( 'equadio_filter_theme_plugins', 'equadio_cf7_theme_plugins' );
		}
	}
}

// Filter to add in the required plugins list
if ( ! function_exists( 'equadio_cf7_tgmpa_required_plugins' ) ) {	
	function equadio_cf7_tgmpa_required_plugins( $list = array() ) {
		if ( equadio_storage_isset( 'required_plugins', 'contact-form-7' ) && equadio_storage_get_array( 'required_plugins', 'contact-form-7', 'install' ) !== false ) {
			// CF7 plugin
			$list[] = array(
				'name'     => equadio_storage_get_array( 'required_plugins', 'contact-form-7', 'title' ),
				'slug'     => 'contact-form-7',
				'required' => false,
			);
		}
		return $list;
	}
}

// Filter theme-supported plugins list
if ( ! function_exists( 'equadio_cf7_theme_plugins' ) ) {	
	function equadio_cf7_theme_plugins( $list = array() ) {
		if ( ! empty( $list['contact-form-7']['group'] ) ) {
			foreach ( $list as $k => $v ) {
				if ( substr( $k, 0, 15 ) == 'contact-form-7-' ) {
					if ( empty( $v['group'] ) ) {
						$list[ $k ]['group'] = $list['contact-form-7']['group'];
					}
					if ( empty( $v['logo'] ) ) {
						$logo = equadio_get_file_url( "plugins/contact-form-7/{$k}.png" );
						$list[ $k ]['logo'] = empty( $logo )
												? ( ! empty( $list['contact-form-7']['logo'] )
													? ( strpos( $list['contact-form-7']['logo'], '//' ) !== false
														? $list['contact-form-7']['logo']
														: equadio_get_file_url( "plugins/contact-form-7/{$list['contact-form-7']['logo']}" )
														)
													: ''
													)
												: $logo;
					}
				}
			}
		}
		return $list;
	}
}



// Check if cf7 installed and activated
if ( ! function_exists( 'equadio_exists_cf7' ) ) {
	function equadio_exists_cf7() {
		return class_exists( 'WPCF7' );
	}
}

// Enqueue custom scripts
if ( ! function_exists( 'equadio_cf7_frontend_scripts' ) ) {	
	function equadio_cf7_frontend_scripts() {
		if ( equadio_is_on( equadio_get_theme_option( 'debug_mode' ) ) ) {
			$equadio_url = equadio_get_file_url( 'plugins/contact-form-7/contact-form-7.js' );
			if ( '' != $equadio_url ) {
				wp_enqueue_script( 'equadio-contact-form-7', $equadio_url, array( 'jquery' ), null, true );
			}
		}
	}
}

// Merge custom scripts
if ( ! function_exists( 'equadio_cf7_merge_scripts' ) ) {	
	function equadio_cf7_merge_scripts( $list ) {
		$list[] = 'plugins/contact-form-7/contact-form-7.js';
		return $list;
	}
}
