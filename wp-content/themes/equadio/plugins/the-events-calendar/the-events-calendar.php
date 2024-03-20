<?php
/* Tribe Events Calendar support functions
------------------------------------------------------------------------------- */

// Theme init priorities:
// 1 - register filters, that add/remove lists items for the Theme Options
if ( ! function_exists( 'equadio_tribe_events_theme_setup1' ) ) {
	add_action( 'after_setup_theme', 'equadio_tribe_events_theme_setup1', 1 );
	function equadio_tribe_events_theme_setup1() {
		add_filter( 'equadio_filter_list_sidebars', 'equadio_tribe_events_list_sidebars' );
	}
}

// Theme init priorities:
// 3 - add/remove Theme Options elements
if ( ! function_exists( 'equadio_tribe_events_theme_setup3' ) ) {
	add_action( 'after_setup_theme', 'equadio_tribe_events_theme_setup3', 3 );
	function equadio_tribe_events_theme_setup3() {
		if ( equadio_exists_tribe_events() ) {
			// Section 'Tribe Events'
			equadio_storage_merge_array(
				'options', '', array_merge(
					array(
						'events' => array(
							'title' => esc_html__( 'Events', 'equadio' ),
							'desc'  => wp_kses_data( __( 'Select parameters to display the events pages', 'equadio' ) ),
							'type'  => 'section',
						),
					),
					equadio_options_get_list_cpt_options( 'events', esc_html__( 'Event', 'equadio' ) )
				)
			);
		}
	}
}

// Theme init priorities:
// 9 - register other filters (for installer, etc.)
if ( ! function_exists( 'equadio_tribe_events_theme_setup9' ) ) {
	add_action( 'after_setup_theme', 'equadio_tribe_events_theme_setup9', 9 );
	function equadio_tribe_events_theme_setup9() {
		if ( equadio_exists_tribe_events() ) {
			add_action( 'wp_enqueue_scripts', 'equadio_tribe_events_frontend_scripts', 1100 );
			add_action( 'wp_enqueue_scripts', 'equadio_tribe_events_responsive_styles', 2000 );
			add_filter( 'equadio_filter_merge_styles', 'equadio_tribe_events_merge_styles' );
			add_filter( 'equadio_filter_merge_styles_responsive', 'equadio_tribe_events_merge_styles_responsive' );
			add_filter( 'equadio_filter_post_type_taxonomy', 'equadio_tribe_events_post_type_taxonomy', 10, 2 );
			add_filter( 'equadio_filter_detect_blog_mode', 'equadio_tribe_events_detect_blog_mode' );
			add_filter( 'equadio_filter_get_post_categories', 'equadio_tribe_events_get_post_categories' );
			add_filter( 'equadio_filter_get_post_date', 'equadio_tribe_events_get_post_date' );
		}
		if ( is_admin() ) {
			add_filter( 'equadio_filter_tgmpa_required_plugins', 'equadio_tribe_events_tgmpa_required_plugins' );
		}

	}
}

// Filter to add in the required plugins list
if ( ! function_exists( 'equadio_tribe_events_tgmpa_required_plugins' ) ) {	
	function equadio_tribe_events_tgmpa_required_plugins( $list = array() ) {
		if ( equadio_storage_isset( 'required_plugins', 'the-events-calendar' ) && equadio_storage_get_array( 'required_plugins', 'the-events-calendar', 'install' ) !== false ) {
			$list[] = array(
				'name'     => equadio_storage_get_array( 'required_plugins', 'the-events-calendar', 'title' ),
				'slug'     => 'the-events-calendar',
				'required' => false,
			);
		}
		return $list;
	}
}


// Remove 'Tribe Events' section from Customizer
if ( ! function_exists( 'equadio_tribe_events_customizer_register_controls' ) ) {
	add_action( 'customize_register', 'equadio_tribe_events_customizer_register_controls', 100 );
	function equadio_tribe_events_customizer_register_controls( $wp_customize ) {
		$wp_customize->remove_panel( 'tribe_customizer' );
	}
}


// Check if Tribe Events is installed and activated
if ( ! function_exists( 'equadio_exists_tribe_events' ) ) {
	function equadio_exists_tribe_events() {
		return class_exists( 'Tribe__Events__Main' );
	}
}

// Return true, if current page is any tribe_events page
if ( ! function_exists( 'equadio_is_tribe_events_page' ) ) {
	function equadio_is_tribe_events_page() {
		$rez = false;
		if ( equadio_exists_tribe_events() ) {
			if ( ! is_search() ) {
				$rez = tribe_is_event() || tribe_is_event_query() || tribe_is_event_category() || tribe_is_event_venue() || tribe_is_event_organizer();
			}
		}
		return $rez;
	}
}

// Detect current blog mode
if ( ! function_exists( 'equadio_tribe_events_detect_blog_mode' ) ) {	
	function equadio_tribe_events_detect_blog_mode( $mode = '' ) {
		if ( equadio_is_tribe_events_page() ) {
			$mode = 'events';
		}
		return $mode;
	}
}

// Return taxonomy for current post type
if ( ! function_exists( 'equadio_tribe_events_post_type_taxonomy' ) ) {	
	function equadio_tribe_events_post_type_taxonomy( $tax = '', $post_type = '' ) {
		if ( equadio_exists_tribe_events() && Tribe__Events__Main::POSTTYPE == $post_type ) {
			$tax = Tribe__Events__Main::TAXONOMY;
		}
		return $tax;
	}
}

// Show categories of the current event
if ( ! function_exists( 'equadio_tribe_events_get_post_categories' ) ) {	
	function equadio_tribe_events_get_post_categories( $cats = '' ) {
		if ( get_post_type() == Tribe__Events__Main::POSTTYPE ) {
			$cats = equadio_get_post_terms( ', ', get_the_ID(), Tribe__Events__Main::TAXONOMY );
		}
		return $cats;
	}
}

// Return date of the current event
if ( ! function_exists( 'equadio_tribe_events_get_post_date' ) ) {	
	function equadio_tribe_events_get_post_date( $dt = '' ) {
		if ( get_post_type() == Tribe__Events__Main::POSTTYPE ) {
			if ( is_int( $dt ) ) {
				// Return start date and time in the Unix format
				$dt = tribe_get_start_date( get_the_ID(), true, 'U' );
			} else {
				// Return start date and time - end date and time
				// Example: $dt = tribe_events_event_schedule_details( get_the_ID(), '', '' )
				
				// Return start date and time as string
				// If second parameter is true - time is showed
				$dt = tribe_get_start_date( get_the_ID(), true );
			}
		}
		return $dt;
	}
}

// Enqueue styles for frontend
if ( ! function_exists( 'equadio_tribe_events_frontend_scripts' ) ) {	
	function equadio_tribe_events_frontend_scripts() {
		if ( equadio_is_tribe_events_page() ) {
			if ( equadio_is_on( equadio_get_theme_option( 'debug_mode' ) ) ) {
				$equadio_url = equadio_get_file_url( 'plugins/the-events-calendar/the-events-calendar.css' );
				if ( '' != $equadio_url ) {
					wp_enqueue_style( 'equadio-the-events-calendar', $equadio_url, array(), null );
				}
			}
		}
	}
}

// Enqueue responsive styles for frontend
if ( ! function_exists( 'equadio_tribe_events_responsive_styles' ) ) {	
	function equadio_tribe_events_responsive_styles() {
		if ( equadio_is_tribe_events_page() ) {
			if ( equadio_is_on( equadio_get_theme_option( 'debug_mode' ) ) ) {
				$equadio_url = equadio_get_file_url( 'plugins/the-events-calendar/the-events-calendar-responsive.css' );
				if ( '' != $equadio_url ) {
					wp_enqueue_style( 'equadio-the-events-calendar-responsive', $equadio_url, array(), null );
				}
			}
		}
	}
}

// Merge custom styles
if ( ! function_exists( 'equadio_tribe_events_merge_styles' ) ) {	
	function equadio_tribe_events_merge_styles( $list ) {
		$list[] = 'plugins/the-events-calendar/the-events-calendar.css';
		return $list;
	}
}


// Merge responsive styles
if ( ! function_exists( 'equadio_tribe_events_merge_styles_responsive' ) ) {	
	function equadio_tribe_events_merge_styles_responsive( $list ) {
		$list[] = 'plugins/the-events-calendar/the-events-calendar-responsive.css';
		return $list;
	}
}



// Add Tribe Events specific items into lists
//------------------------------------------------------------------------

// Add sidebar
if ( ! function_exists( 'equadio_tribe_events_list_sidebars' ) ) {	
	function equadio_tribe_events_list_sidebars( $list = array() ) {
		$list['tribe_events_widgets'] = array(
			'name'        => esc_html__( 'Tribe Events Widgets', 'equadio' ),
			'description' => esc_html__( 'Widgets to be shown on the Tribe Events pages', 'equadio' ),
		);
		return $list;
	}
}


// Add plugin-specific colors and fonts to the custom CSS
if ( equadio_exists_tribe_events() ) {
	require_once equadio_get_file_dir( 'plugins/the-events-calendar/the-events-calendar-style.php' );
}
