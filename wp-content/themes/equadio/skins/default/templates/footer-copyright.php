<?php
/**
 * The template to display the copyright info in the footer
 *
 * @package EQUADIO
 * @since EQUADIO 1.0.10
 */

// Copyright area
?> 
<div class="footer_copyright_wrap
<?php
$equadio_copyright_scheme = equadio_get_theme_option( 'copyright_scheme' );
if ( ! empty( $equadio_copyright_scheme ) && ! equadio_is_inherit( $equadio_copyright_scheme  ) ) {
	echo ' scheme_' . esc_attr( $equadio_copyright_scheme );
}
?>
				">
	<div class="footer_copyright_inner">
		<div class="content_wrap">
			<div class="copyright_text">
			<?php
				$equadio_copyright = equadio_get_theme_option( 'copyright' );
			if ( ! empty( $equadio_copyright ) ) {
				// Replace {{Y}} or {Y} with the current year
				$equadio_copyright = str_replace( array( '{{Y}}', '{Y}' ), date( 'Y' ), $equadio_copyright );
				// Replace {{...}} and ((...)) on the <i>...</i> and <b>...</b>
				$equadio_copyright = equadio_prepare_macros( $equadio_copyright );
				// Display copyright
				echo wp_kses( nl2br( $equadio_copyright ), 'equadio_kses_content' );
			}
			?>
			</div>
		</div>
	</div>
</div>
