<?php
/* EssentialAddonsForElementorLite support functions
------------------------------------------------------------------------------- */

// Theme init priorities:
// 9 - register other filters (for installer, etc.)
if ( ! function_exists( 'equadio_essential_addons_for_elementor_theme_setup9' ) ) {
	add_action( 'after_setup_theme', 'equadio_essential_addons_for_elementor_theme_setup9', 9 );
	function equadio_essential_addons_for_elementor_theme_setup9() {
		if ( equadio_exists_essential_addons_for_elementor() ) {
			add_action( 'wp_enqueue_scripts', 'equadio_essential_addons_for_elementor_frontend_scripts', 1100 );
            add_action( 'wp_enqueue_scripts', 'equadio_essential_addons_for_elementor_responsive_styles', 2000 );
			add_filter( 'equadio_filter_merge_styles', 'equadio_essential_addons_for_elementor_merge_styles' );
            add_filter( 'equadio_filter_merge_styles_responsive', 'equadio_essential_addons_for_elementor_merge_styles_responsive' );
		}
		if ( is_admin() ) {
			add_filter( 'equadio_filter_tgmpa_required_plugins', 'equadio_essential_addons_for_elementor_tgmpa_required_plugins' );
		}
	}
}

// Filter to add in the required plugins list
if ( ! function_exists( 'equadio_essential_addons_for_elementor_tgmpa_required_plugins' ) ) {	
	function equadio_essential_addons_for_elementor_tgmpa_required_plugins( $list = array() ) {
		if ( equadio_storage_isset( 'required_plugins', 'essential-addons-for-elementor-lite' ) && equadio_storage_get_array( 'required_plugins', 'essential-addons-for-elementor-lite', 'install' ) !== false ) {
			$list[] = array(
				'name'     => equadio_storage_get_array( 'required_plugins', 'essential-addons-for-elementor-lite', 'title' ),
				'slug'     => 'essential-addons-for-elementor-lite',
				'required' => false,
			);
		}
		return $list;
	}
}

// Check if plugin installed and activated
if ( ! function_exists( 'equadio_exists_essential_addons_for_elementor' ) ) {
	function equadio_exists_essential_addons_for_elementor() {
        return class_exists( 'Elementor\Plugin' ) || defined( 'EAEL_PLUGIN_VERSION' );
	}
}

// Custom styles and scripts
//------------------------------------------------------------------------

// Enqueue styles for frontend
if ( ! function_exists( 'equadio_essential_addons_for_elementor_frontend_scripts' ) ) {	
	function equadio_essential_addons_for_elementor_frontend_scripts() {
		if ( equadio_is_on( equadio_get_theme_option( 'debug_mode' ) ) ) {
			$equadio_url = equadio_get_file_url( 'plugins/essential-addons-for-elementor-lite/essential-addons-for-elementor-lite.css' );
			if ( '' != $equadio_url ) {
				wp_enqueue_style( 'equadio-essential-addons-for-elementor-lite', $equadio_url, array(), null );
			}
		}
	}
}
// Enqueue responsive styles for frontend
if ( ! function_exists( 'equadio_essential_addons_for_elementor_responsive_styles' ) ) { 
    function equadio_essential_addons_for_elementor_responsive_styles() {
        if ( equadio_is_on( equadio_get_theme_option( 'debug_mode' ) ) ) {
            $equadio_url = equadio_get_file_url( 'plugins/essential-addons-for-elementor-lite/essential-addons-for-elementor-lite-responsive.css' );
            if ( '' != $equadio_url ) {
                wp_enqueue_style( 'equadio-essential-addons-for-elementor-lite-responsive', $equadio_url, array(), null );
            }
        }
    }
}

// Merge custom styles
if ( ! function_exists( 'equadio_essential_addons_for_elementor_merge_styles' ) ) {	
	function equadio_essential_addons_for_elementor_merge_styles( $list ) {
		$list[] = 'plugins/essential-addons-for-elementor-lite/essential-addons-for-elementor-lite.css';
		return $list;
	}
}
// Merge responsive styles
if ( ! function_exists( 'equadio_essential_addons_for_elementor_merge_styles_responsive' ) ) { 
    function equadio_essential_addons_for_elementor_merge_styles_responsive( $list ) {
        $list[] = 'plugins/essential-addons-for-elementor-lite/essential-addons-for-elementor-lite-responsive.css';
        return $list;
    }
}


// Add plugin-specific colors and fonts to the custom CSS
if ( equadio_exists_essential_addons_for_elementor() ) {
    require_once equadio_get_file_dir( 'plugins/essential-addons-for-elementor-lite/essential-addons-for-elementor-lite-styles.php' );
}

