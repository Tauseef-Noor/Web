<?php
/**
 * The template to display the socials in the footer
 *
 * @package EQUADIO
 * @since EQUADIO 1.0.10
 */


// Socials
if ( equadio_is_on( equadio_get_theme_option( 'socials_in_footer' ) ) ) {
	$equadio_output = equadio_get_socials_links();
	if ( '' != $equadio_output ) {
		?>
		<div class="footer_socials_wrap socials_wrap">
			<div class="footer_socials_inner">
				<?php equadio_show_layout( $equadio_output ); ?>
			</div>
		</div>
		<?php
	}
}
