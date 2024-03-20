<?php
/**
 * The template to display default site footer
 *
 * @package EQUADIO
 * @since EQUADIO 1.0.10
 */

$equadio_footer_id = equadio_get_custom_footer_id();
$equadio_footer_meta = get_post_meta( $equadio_footer_id, 'trx_addons_options', true );
if ( ! empty( $equadio_footer_meta['margin'] ) ) {
	equadio_add_inline_css( sprintf( '.page_content_wrap{padding-bottom:%s}', esc_attr( equadio_prepare_css_value( $equadio_footer_meta['margin'] ) ) ) );
}
?>
<footer class="footer_wrap footer_custom footer_custom_<?php echo esc_attr( $equadio_footer_id ); ?> footer_custom_<?php echo esc_attr( sanitize_title( get_the_title( $equadio_footer_id ) ) ); ?>
						<?php
						$equadio_footer_scheme = equadio_get_theme_option( 'footer_scheme' );
						if ( ! empty( $equadio_footer_scheme ) && ! equadio_is_inherit( $equadio_footer_scheme  ) ) {
							echo ' scheme_' . esc_attr( $equadio_footer_scheme );
						}
						?>
						">
	<?php
	// Custom footer's layout
	do_action( 'equadio_action_show_layout', $equadio_footer_id );
	?>
</footer><!-- /.footer_wrap -->
