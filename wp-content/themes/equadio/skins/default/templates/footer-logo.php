<?php
/**
 * The template to display the site logo in the footer
 *
 * @package EQUADIO
 * @since EQUADIO 1.0.10
 */

// Logo
if ( equadio_is_on( equadio_get_theme_option( 'logo_in_footer' ) ) ) {
	$equadio_logo_image = equadio_get_logo_image( 'footer' );
	$equadio_logo_text  = get_bloginfo( 'name' );
	if ( ! empty( $equadio_logo_image['logo'] ) || ! empty( $equadio_logo_text ) ) {
		?>
		<div class="footer_logo_wrap">
			<div class="footer_logo_inner">
				<?php
				if ( ! empty( $equadio_logo_image['logo'] ) ) {
					$equadio_attr = equadio_getimagesize( $equadio_logo_image['logo'] );
					echo '<a href="' . esc_url( home_url( '/' ) ) . '">'
							. '<img src="' . esc_url( $equadio_logo_image['logo'] ) . '"'
								. ( ! empty( $equadio_logo_image['logo_retina'] ) ? ' srcset="' . esc_url( $equadio_logo_image['logo_retina'] ) . ' 2x"' : '' )
								. ' class="logo_footer_image"'
								. ' alt="' . esc_attr__( 'Site logo', 'equadio' ) . '"'
								. ( ! empty( $equadio_attr[3] ) ? ' ' . wp_kses_data( $equadio_attr[3] ) : '' )
							. '>'
						. '</a>';
				} elseif ( ! empty( $equadio_logo_text ) ) {
					echo '<h1 class="logo_footer_text">'
							. '<a href="' . esc_url( home_url( '/' ) ) . '">'
								. esc_html( $equadio_logo_text )
							. '</a>'
						. '</h1>';
				}
				?>
			</div>
		</div>
		<?php
	}
}
