<?php
/**
 * The Front Page template file.
 *
 * @package EQUADIO
 * @since EQUADIO 1.0.31
 */

get_header();

// If front-page is a static page
if ( get_option( 'show_on_front' ) == 'page' ) {

	// If Front Page Builder is enabled - display sections
	if ( equadio_is_on( equadio_get_theme_option( 'front_page_enabled' ) ) ) {

		if ( have_posts() ) {
			the_post();
		}

		$equadio_sections = equadio_array_get_keys_by_value( equadio_get_theme_option( 'front_page_sections' ) );
		if ( is_array( $equadio_sections ) ) {
			foreach ( $equadio_sections as $equadio_section ) {
				get_template_part( apply_filters( 'equadio_filter_get_template_part', 'front-page/section', $equadio_section ), $equadio_section );
			}
		}

		// Else if this page is blog archive
	} elseif ( is_page_template( 'blog.php' ) ) {
		get_template_part( apply_filters( 'equadio_filter_get_template_part', 'blog' ) );

		// Else - display native page content
	} else {
		get_template_part( apply_filters( 'equadio_filter_get_template_part', 'page' ) );
	}

	// Else get index template to show posts
} else {
	get_template_part( apply_filters( 'equadio_filter_get_template_part', 'index' ) );
}

get_footer();
