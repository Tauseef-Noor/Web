<?php
/**
 * The main template file.
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists.
 * Learn more: //codex.wordpress.org/Template_Hierarchy
 *
 * @package EQUADIO
 * @since EQUADIO 1.0
 */

$equadio_template = apply_filters( 'equadio_filter_get_template_part', equadio_blog_archive_get_template() );

if ( ! empty( $equadio_template ) && 'index' != $equadio_template ) {

	get_template_part( $equadio_template );

} else {

	equadio_storage_set( 'blog_archive', true );

	get_header();

	if ( have_posts() ) {

		// Query params
		$equadio_stickies  = is_home() ? get_option( 'sticky_posts' ) : false;
		$equadio_post_type = equadio_get_theme_option( 'post_type' );
		$equadio_args      = array(
								'blog_style'     => equadio_get_theme_option( 'blog_style' ),
								'post_type'      => $equadio_post_type,
								'taxonomy'       => equadio_get_post_type_taxonomy( $equadio_post_type ),
								'parent_cat'     => equadio_get_theme_option( 'parent_cat' ),
								'posts_per_page' => equadio_get_theme_option( 'posts_per_page' ),
								'sticky'         => equadio_get_theme_option( 'sticky_style' ) == 'columns'
															&& is_array( $equadio_stickies )
															&& count( $equadio_stickies ) > 0
															&& get_query_var( 'paged' ) < 1
								);

		equadio_blog_archive_start();

		do_action( 'equadio_action_blog_archive_start' );

		if ( is_author() ) {
			do_action( 'equadio_action_before_page_author' );
			get_template_part( apply_filters( 'equadio_filter_get_template_part', 'templates/author-page' ) );
			do_action( 'equadio_action_after_page_author' );
		}

		if ( equadio_get_theme_option( 'show_filters' ) ) {
			do_action( 'equadio_action_before_page_filters' );
			equadio_show_filters( $equadio_args );
			do_action( 'equadio_action_after_page_filters' );
		} else {
			do_action( 'equadio_action_before_page_posts' );
			equadio_show_posts( array_merge( $equadio_args, array( 'cat' => $equadio_args['parent_cat'] ) ) );
			do_action( 'equadio_action_after_page_posts' );
		}

		do_action( 'equadio_action_blog_archive_end' );

		equadio_blog_archive_end();

	} else {

		if ( is_search() ) {
			get_template_part( apply_filters( 'equadio_filter_get_template_part', 'templates/content', 'none-search' ), 'none-search' );
		} else {
			get_template_part( apply_filters( 'equadio_filter_get_template_part', 'templates/content', 'none-archive' ), 'none-archive' );
		}
	}

	get_footer();
}
