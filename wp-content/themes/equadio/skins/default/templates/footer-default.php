<?php
/**
 * The template to display default site footer
 *
 * @package EQUADIO
 * @since EQUADIO 1.0.10
 */

?>
<footer class="footer_wrap footer_default
<?php
$equadio_footer_scheme = equadio_get_theme_option( 'footer_scheme' );
if ( ! empty( $equadio_footer_scheme ) && ! equadio_is_inherit( $equadio_footer_scheme  ) ) {
	echo ' scheme_' . esc_attr( $equadio_footer_scheme );
}
?>
				">
	<?php

	// Footer widgets area
	get_template_part( apply_filters( 'equadio_filter_get_template_part', 'templates/footer-widgets' ) );

	// Logo
	get_template_part( apply_filters( 'equadio_filter_get_template_part', 'templates/footer-logo' ) );

	// Socials
	get_template_part( apply_filters( 'equadio_filter_get_template_part', 'templates/footer-socials' ) );

	// Menu
	get_template_part( apply_filters( 'equadio_filter_get_template_part', 'templates/footer-menu' ) );

	// Copyright area
	get_template_part( apply_filters( 'equadio_filter_get_template_part', 'templates/footer-copyright' ) );

	?>
</footer><!-- /.footer_wrap -->
