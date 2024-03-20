<?php
/* Give (donation forms) support functions
------------------------------------------------------------------------------- */

if ( ! defined( 'EQUADIO_GIVE_FORMS_PT_FORMS' ) )			define( 'EQUADIO_GIVE_FORMS_PT_FORMS', 'give_forms' );
if ( ! defined( 'EQUADIO_GIVE_FORMS_PT_PAYMENT' ) )			define( 'EQUADIO_GIVE_FORMS_PT_PAYMENT', 'give_payment' );
if ( ! defined( 'EQUADIO_GIVE_FORMS_TAXONOMY_CATEGORY' ) )	define( 'EQUADIO_GIVE_FORMS_TAXONOMY_CATEGORY', 'give_forms_category' );
if ( ! defined( 'EQUADIO_GIVE_FORMS_TAXONOMY_TAG' ) )		define( 'EQUADIO_GIVE_FORMS_TAXONOMY_TAG', 'give_forms_tag' );


// Theme init priorities:
// 1 - register filters, that add/remove lists items for the Theme Options
if ( ! function_exists( 'equadio_give_theme_setup1' ) ) {
    add_action( 'after_setup_theme', 'equadio_give_theme_setup1', 1 );
    function equadio_give_theme_setup1() {
        add_filter( 'equadio_filter_list_sidebars', 'equadio_give_list_sidebars' );
        add_filter( 'equadio_filter_detect_blog_mode', 'equadio_give_detect_blog_mode' );
    }
}

// Theme init priorities:
// 3 - add/remove Theme Options elements
if ( ! function_exists( 'equadio_give_theme_setup3' ) ) {
    add_action( 'after_setup_theme', 'equadio_give_theme_setup3', 3 );
    function equadio_give_theme_setup3() {
        if ( equadio_exists_give() ) {

            // Section 'Give Donation'
            equadio_storage_merge_array(
                'options', '', array_merge(
                    array(
                        'give'     => array(
                            'title' => esc_html__( 'Give Donation', 'equadio' ),
                            'desc'  => wp_kses_data( __( 'Select parameters to display the community pages', 'equadio' ) ),
                            'type'  => 'section',
                        ),
                    ),
                    equadio_options_get_list_cpt_options( 'give' )
                )
            );
        }
    }
}

// Theme init priorities:
// 9 - register other filters (for installer, etc.)
if ( ! function_exists( 'equadio_give_theme_setup9' ) ) {
	add_action( 'after_setup_theme', 'equadio_give_theme_setup9', 9 );
	function equadio_give_theme_setup9() {
		if ( equadio_exists_give() ) {
			add_action( 'wp_enqueue_scripts', 'equadio_give_frontend_scripts', 1100 );
            add_action( 'wp_enqueue_scripts', 'equadio_give_responsive_styles', 2000 );
			add_filter( 'equadio_filter_merge_styles', 'equadio_give_merge_styles' );
            add_filter( 'equadio_filter_merge_styles_responsive', 'equadio_give_merge_styles_responsive' );
            add_filter( 'equadio_filter_merge_scripts', 'equadio_give_merge_scripts' );
            add_filter( 'equadio_filter_post_type_taxonomy', 'equadio_give_post_type_taxonomy', 10, 2 );
            add_filter( 'equadio_filter_get_post_categories', 'equadio_give_get_post_categories', 10, 2 );
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

// Return true, if current page is any give page
if ( ! function_exists( 'equadio_is_give_page' ) ) {
    function equadio_is_give_page() {
        $rez = false;
        if ( equadio_exists_give() ) {
            $rez =  is_post_type_archive( 'give_forms' ) || is_give_form() || is_singular( 'give_forms' ) || is_give_category() || is_give_tag() || is_give_taxonomy();
        }
        return $rez;
    }
}

// Detect current blog mode
if ( ! function_exists( 'equadio_give_detect_blog_mode' ) ) {   
    function equadio_give_detect_blog_mode( $mode = '' ) {
        if ( equadio_is_give_page() ) {
            $mode = 'give';
        }
        return $mode;
    }
}

// Return taxonomy for current post type
if ( ! function_exists( 'equadio_give_post_type_taxonomy' ) ) {    
    function equadio_give_post_type_taxonomy( $tax = '', $post_type = '' ) {
        if ( 'give_forms' == $post_type ) {
            $tax = 'give_forms_category';
        }
        return $tax;
    }
}

// Show categories
if ( ! function_exists( 'equadio_give_get_post_categories' ) ) {   
    function equadio_give_get_post_categories( $cats = '' ) {
        if ( get_post_type() == 'give_forms' ) {
            $cats = equadio_get_post_terms( ', ', get_the_ID(), 'give_forms_category' );
        }
        return $cats;
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
            $equadio_url = equadio_get_file_url( 'plugins/give/give.js' );
            if ( '' != $equadio_url ) {
                wp_enqueue_script( 'equadio-give', $equadio_url, array( 'jquery' ), null, true );
            }
		}
	}
}

// Enqueue responsive styles for frontend
if ( ! function_exists( 'equadio_give_responsive_styles' ) ) {
    function equadio_give_responsive_styles() {
        if ( equadio_is_on( equadio_get_theme_option( 'debug_mode' ) ) ) {
            $equadio_url = equadio_get_file_url( 'plugins/give/give-responsive.css' );
            if ( '' != $equadio_url ) {
                wp_enqueue_style( 'equadio-give-responsive', $equadio_url, array(), null );
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

// Merge responsive styles
if ( ! function_exists( 'equadio_give_merge_styles_responsive' ) ) {   
    function equadio_give_merge_styles_responsive( $list ) {
        $list[] = 'plugins/give/give-responsive.css';
        return $list;
    }
}

// Merge custom scripts
if ( ! function_exists( 'equadio_give_merge_scripts' ) ) {   
    function equadio_give_merge_scripts( $list ) {
        $list[] = 'plugins/give/give.js';
        return $list;
    }
}


// Add Give specific items into lists
//------------------------------------------------------------------------

// Add sidebar
if ( ! function_exists( 'equadio_give_list_sidebars' ) ) {
    function equadio_give_list_sidebars( $list = array() ) {
        $list['give_widgets'] = array(
            'name'        => esc_html__( 'Give Widgets', 'equadio' ),
            'description' => esc_html__( 'Widgets to be shown on the Give pages', 'equadio' ),
        );
        return $list;
    }
}

// Add plugin-specific colors and fonts to the custom CSS
if ( equadio_exists_give() ) {
	require_once equadio_get_file_dir( 'plugins/give/give-style.php' );
}
