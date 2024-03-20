<?php
/* Give (donation forms) support functions
------------------------------------------------------------------------------- */

if ( ! defined( 'EQUADIO_GIVE_FORMS_PT_FORMS' ) )			define( 'EQUADIO_GIVE_FORMS_PT_FORMS', 'give_forms' );
if ( ! defined( 'EQUADIO_GIVE_FORMS_PT_PAYMENT' ) )			define( 'EQUADIO_GIVE_FORMS_PT_PAYMENT', 'give_payment' );
if ( ! defined( 'EQUADIO_GIVE_FORMS_TAXONOMY_CATEGORY' ) )	define( 'EQUADIO_GIVE_FORMS_TAXONOMY_CATEGORY', 'give_forms_category' );
if ( ! defined( 'EQUADIO_GIVE_FORMS_TAXONOMY_TAG' ) )		define( 'EQUADIO_GIVE_FORMS_TAXONOMY_TAG', 'give_forms_tag' );

// Theme init priorities:
// 9 - register other filters (for installer, etc.)
if ( ! function_exists( 'equadio_give_theme_setup9' ) ) {
	add_action( 'after_setup_theme', 'equadio_give_theme_setup9', 9 );
	function equadio_give_theme_setup9() {
		if ( equadio_exists_give() ) {
			add_action( 'wp_enqueue_scripts', 'equadio_give_frontend_scripts', 1100 );
			add_filter( 'equadio_filter_merge_styles', 'equadio_give_merge_styles' );
			add_filter( 'equadio_filter_get_post_categories', 'equadio_give_get_post_categories');
		}
		if ( is_admin() ) {
			add_filter( 'equadio_filter_tgmpa_required_plugins', 'equadio_give_tgmpa_required_plugins' );
		}
	}
}

// Filter to add in the required plugins list
if ( ! function_exists( 'equadio_give_tgmpa_required_plugins' ) ) {	
	function equadio_give_tgmpa_required_plugins( $list = array() ) {
		if ( equadio_storage_isset( 'required_plugins', 'give' ) && equadio_storage_get_array( 'required_plugins', 'give', 'install' ) !== false ) {
			$list[] = array(
				'name'     => equadio_storage_get_array( 'required_plugins', 'give', 'title' ),
				'slug'     => 'give',
				'required' => false,
			);
		}
		return $list;
	}
}

// Check if plugin installed and activated
if ( ! function_exists( 'equadio_exists_give' ) ) {
	function equadio_exists_give() {
		return class_exists( 'Give' );
	}
}

// Enqueue styles for frontend
if ( ! function_exists( 'equadio_give_frontend_scripts' ) ) {	
	function equadio_give_frontend_scripts() {
		if ( equadio_is_on( equadio_get_theme_option( 'debug_mode' ) ) ) {
			$equadio_url = equadio_get_file_url( 'plugins/give/give.css' );
			if ( '' != $equadio_url ) {
				wp_enqueue_style( 'equadio-give', $equadio_url, array(), null );
			}
		}
	}
}

// Merge custom styles
if ( ! function_exists( 'equadio_give_merge_styles' ) ) {	
	function equadio_give_merge_styles( $list ) {
		$list[] = 'plugins/give/give.css';
		return $list;
	}
}


// Show categories of the current product
if ( ! function_exists( 'equadio_give_get_post_categories' ) ) {	
	function equadio_give_get_post_categories( $cats = '' ) {
		if ( get_post_type() == EQUADIO_GIVE_FORMS_PT_FORMS ) {
			$cats = equadio_get_post_terms( ', ', get_the_ID(), EQUADIO_GIVE_FORMS_TAXONOMY_CATEGORY );
		}
		return $cats;
	}
}

// Add plugin-specific colors and fonts to the custom CSS
if ( equadio_exists_give() ) {
	require_once equadio_get_file_dir( 'plugins/give/give-style.php' );
}
