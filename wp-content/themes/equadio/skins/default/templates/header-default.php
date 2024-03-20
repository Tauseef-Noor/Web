<?php
/**
 * The template to display default site header
 *
 * @package EQUADIO
 * @since EQUADIO 1.0
 */

$equadio_header_css   = '';
$equadio_header_image = get_header_image();
$equadio_header_video = equadio_get_header_video();
if ( ! empty( $equadio_header_image ) && equadio_trx_addons_featured_image_override( is_singular() || equadio_storage_isset( 'blog_archive' ) || is_category() ) ) {
	$equadio_header_image = equadio_get_current_mode_image( $equadio_header_image );
}

?><header class="top_panel top_panel_default
	<?php
	echo ! empty( $equadio_header_image ) || ! empty( $equadio_header_video ) ? ' with_bg_image' : ' without_bg_image';
	if ( '' != $equadio_header_video ) {
		echo ' with_bg_video';
	}
	if ( '' != $equadio_header_image ) {
		echo ' ' . esc_attr( equadio_add_inline_css_class( 'background-image: url(' . esc_url( $equadio_header_image ) . ');' ) );
	}
	if ( is_single() && has_post_thumbnail() ) {
		echo ' with_featured_image';
	}
	if ( equadio_is_on( equadio_get_theme_option( 'header_fullheight' ) ) ) {
		echo ' header_fullheight equadio-full-height';
	}
	$equadio_header_scheme = equadio_get_theme_option( 'header_scheme' );
	if ( ! empty( $equadio_header_scheme ) && ! equadio_is_inherit( $equadio_header_scheme  ) ) {
		echo ' scheme_' . esc_attr( $equadio_header_scheme );
	}
	?>
">
	<?php

	// Background video
	if ( ! empty( $equadio_header_video ) ) {
		get_template_part( apply_filters( 'equadio_filter_get_template_part', 'templates/header-video' ) );
	}

	// Main menu
	get_template_part( apply_filters( 'equadio_filter_get_template_part', 'templates/header-navi' ) );

	// Mobile header
	if ( equadio_is_on( equadio_get_theme_option( 'header_mobile_enabled' ) ) ) {
		get_template_part( apply_filters( 'equadio_filter_get_template_part', 'templates/header-mobile' ) );
	}

	// Page title and breadcrumbs area
	if ( ! is_single() ) {
		get_template_part( apply_filters( 'equadio_filter_get_template_part', 'templates/header-title' ) );
	}

	// Header widgets area
	get_template_part( apply_filters( 'equadio_filter_get_template_part', 'templates/header-widgets' ) );
	?>
</header>
