<?php
/**
 * The template to display the logo or the site name and the slogan in the Header
 *
 * @package EQUADIO
 * @since EQUADIO 1.0
 */

$equadio_args = get_query_var( 'equadio_logo_args' );

// Site logo
$equadio_logo_type   = isset( $equadio_args['type'] ) ? $equadio_args['type'] : '';
$equadio_logo_image  = equadio_get_logo_image( $equadio_logo_type );
$equadio_logo_text   = equadio_is_on( equadio_get_theme_option( 'logo_text' ) ) ? get_bloginfo( 'name' ) : '';
$equadio_logo_slogan = get_bloginfo( 'description', 'display' );
if ( ! empty( $equadio_logo_image['logo'] ) || ! empty( $equadio_logo_text ) ) {
	?><a class="sc_layouts_logo" href="<?php echo esc_url( home_url( '/' ) ); ?>">
		<?php
		if ( ! empty( $equadio_logo_image['logo'] ) ) {
			if ( empty( $equadio_logo_type ) && function_exists( 'the_custom_logo' ) && is_numeric( $equadio_logo_image['logo'] ) && $equadio_logo_image['logo'] > 0 ) {
				the_custom_logo();
			} else {
				$equadio_attr = equadio_getimagesize( $equadio_logo_image['logo'] );
				echo '<img src="' . esc_url( $equadio_logo_image['logo'] ) . '"'
						. ( ! empty( $equadio_logo_image['logo_retina'] ) ? ' srcset="' . esc_url( $equadio_logo_image['logo_retina'] ) . ' 2x"' : '' )
						. ' alt="' . esc_attr( $equadio_logo_text ) . '"'
						. ( ! empty( $equadio_attr[3] ) ? ' ' . wp_kses_data( $equadio_attr[3] ) : '' )
						. '>';
			}
		} else {
			equadio_show_layout( equadio_prepare_macros( $equadio_logo_text ), '<span class="logo_text">', '</span>' );
			equadio_show_layout( equadio_prepare_macros( $equadio_logo_slogan ), '<span class="logo_slogan">', '</span>' );
		}
		?>
	</a>
	<?php
}
