<?php
/* SpeakOut support functions
------------------------------------------------------------------------------- */

// Theme init priorities:
// 9 - register other filters (for installer, etc.)
if ( ! function_exists( 'equadio_speakout_theme_setup9' ) ) {
    add_action( 'after_setup_theme', 'equadio_speakout_theme_setup9', 9 );
    function equadio_speakout_theme_setup9() {
        if ( equadio_exists_speakout() ) {
            add_action( 'wp_enqueue_scripts', 'equadio_speakout_frontend_scripts', 1100 );
            add_action( 'wp_enqueue_scripts', 'equadio_speakout_responsive_styles', 2000 );
            add_filter( 'equadio_filter_merge_styles', 'equadio_speakout_merge_styles' );
            add_filter('equadio_filter_merge_styles_responsive', 'equadio_speakout_merge_styles_responsive');
            add_filter( 'equadio_filter_merge_scripts', 'equadio_speakout_merge_scripts' );
        }
        if ( is_admin() ) {
            add_filter( 'equadio_filter_tgmpa_required_plugins', 'equadio_speakout_tgmpa_required_plugins' );
        }
    }
}


// Filter to add in the required plugins list
if ( ! function_exists( 'equadio_speakout_tgmpa_required_plugins' ) ) {    
    function equadio_speakout_tgmpa_required_plugins( $list = array() ) {
        if ( equadio_storage_isset( 'required_plugins', 'speakout' ) && equadio_storage_get_array( 'required_plugins', 'speakout', 'install' ) !== false ) {
            $list[] = array(
                'name'     => equadio_storage_get_array( 'required_plugins', 'speakout', 'title' ),
                'slug'     => 'speakout',
                'required' => false,
            );
        }
        return $list;
    }
}

// Check if plugin installed and activated
if ( ! function_exists( 'equadio_exists_speakout' ) ) {
    function equadio_exists_speakout() {
        return function_exists( 'dk_speakout_translate' );
    }
}


// Enqueue styles for frontend
if ( ! function_exists( 'equadio_speakout_frontend_scripts' ) ) {   
    function equadio_speakout_frontend_scripts() {
        if ( equadio_is_on( equadio_get_theme_option( 'debug_mode' ) ) ) {
            $equadio_url = equadio_get_file_url( 'plugins/speakout/speakout.css' );
            if ( '' != $equadio_url ) {
                wp_enqueue_style( 'equadio-speakout', $equadio_url, array(), null );
            }
            $equadio_url = equadio_get_file_url( 'plugins/speakout/speakout.js' );
            if ( '' != $equadio_url ) {
                wp_enqueue_script( 'equadio-speakout', $equadio_url, array( 'jquery' ), null, true );
            }
        }
    }
}
// Enqueue  responsive styles
if ( ! function_exists( 'equadio_speakout_responsive_styles' ) ) {   
    function equadio_speakout_responsive_styles() {
        if ( equadio_is_on( equadio_get_theme_option( 'debug_mode' ) ) ) {
            $equadio_url = equadio_get_file_url( 'plugins/speakout/speakout-responsive.css' );
            if ( '' != $equadio_url ) {
                wp_enqueue_style( 'equadio-speakout-responsive', $equadio_url, array(), null );
            }
        }
    }
}

// Merge custom styles
if ( ! function_exists( 'equadio_speakout_merge_styles' ) ) {
    function equadio_speakout_merge_styles( $list ) {
        $list[] = 'plugins/speakout/speakout.css';
        return $list;
    }
}

// Merge responsive styles
if ( ! function_exists( 'equadio_speakout_merge_styles_responsive' ) ) {  
    function equadio_speakout_merge_styles_responsive( $list ) {
        $list[] = 'plugins/speakout/speakout-responsive.css';
        return $list;
    }
}

// Merge custom scripts
if ( ! function_exists( 'equadio_speakout_merge_scripts' ) ) { 
    function equadio_speakout_merge_scripts( $list ) {
        $list[] = 'plugins/speakout/speakout.js';
        return $list;
    }
}

// Add plugin-specific colors and fonts to the custom CSS
if ( equadio_exists_speakout() ) {
    require_once equadio_get_file_dir ( 'plugins/speakout/speakout-styles.php' );
}

// One-click import support
if ( equadio_exists_trx_addons() && is_admin() && equadio_exists_speakout() ) {
    require_once equadio_get_file_dir ( 'plugins/speakout/speakout-demo-importer.php');
}
