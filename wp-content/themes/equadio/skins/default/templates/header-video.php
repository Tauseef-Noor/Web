<?php
/**
 * The template to display the background video in the header
 *
 * @package EQUADIO
 * @since EQUADIO 1.0.14
 */
$equadio_header_video = equadio_get_header_video();
$equadio_embed_video  = '';
if ( ! empty( $equadio_header_video ) && ! equadio_is_from_uploads( $equadio_header_video ) ) {
	if ( equadio_is_youtube_url( $equadio_header_video ) && preg_match( '/[=\/]([^=\/]*)$/', $equadio_header_video, $matches ) && ! empty( $matches[1] ) ) {
		?><div id="background_video" data-youtube-code="<?php echo esc_attr( $matches[1] ); ?>"></div>
		<?php
	} else {
		?>
		<div id="background_video"><?php equadio_show_layout( equadio_get_embed_video( $equadio_header_video ) ); ?></div>
		<?php
	}
}
