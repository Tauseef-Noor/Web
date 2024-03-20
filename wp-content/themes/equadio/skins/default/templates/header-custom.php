<?php
/**
 * The template to display custom header from the ThemeREX Addons Layouts
 *
 * @package EQUADIO
 * @since EQUADIO 1.0.06
 */

$equadio_header_css   = '';
$equadio_header_image = get_header_image();
$equadio_header_video = equadio_get_header_video();
if ( ! empty( $equadio_header_image ) && equadio_trx_addons_featured_image_override( is_singular() || equadio_storage_isset( 'blog_archive' ) || is_category() ) ) {
	$equadio_header_image = equadio_get_current_mode_image( $equadio_header_image );
}

$equadio_header_id = equadio_get_custom_header_id();
$equadio_header_meta = get_post_meta( $equadio_header_id, 'trx_addons_options', true );
if ( ! empty( $equadio_header_meta['margin'] ) ) {
	equadio_add_inline_css( sprintf( '.page_content_wrap{padding-top:%s}', esc_attr( equadio_prepare_css_value( $equadio_header_meta['margin'] ) ) ) );
}

?><header class="top_panel top_panel_custom top_panel_custom_<?php echo esc_attr( $equadio_header_id ); ?> top_panel_custom_<?php echo esc_attr( sanitize_title( get_the_title( $equadio_header_id ) ) ); ?>
				<?php
				echo ! empty( $equadio_header_image ) || ! empty( $equadio_header_video )
					? ' with_bg_image'
					: ' without_bg_image';
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

	// Custom header's layout
	do_action( 'equadio_action_show_layout', $equadio_header_id );

	// Header widgets area
	get_template_part( apply_filters( 'equadio_filter_get_template_part', 'templates/header-widgets' ) );

	?>
</header>
