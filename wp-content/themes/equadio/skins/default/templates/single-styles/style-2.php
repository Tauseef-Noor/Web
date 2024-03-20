<?php
/**
 * The "Style 2" template to display the post header of the single post or attachment:
 * featured image placed in the post header and title placed inside content
 *
 * @package EQUADIO
 * @since EQUADIO 1.75.0
 */

if ( apply_filters( 'equadio_filter_single_post_header', is_singular( 'post' ) || is_singular( 'attachment' ) ) ) {
	$equadio_post_format = str_replace( 'post-format-', '', get_post_format() );
	// Featured image
	ob_start();
	equadio_show_post_featured_image( array(
		'thumb_bg'  => true,
		'popup'     => true,
		'class_avg' => in_array( $equadio_post_format, array( 'video' ) ) ? 'content_wrap' : '',
	) );
	$equadio_post_header = ob_get_contents();
	ob_end_clean();
	$equadio_with_featured_image = equadio_is_with_featured_image( $equadio_post_header );

	if ( strpos( $equadio_post_header, 'post_featured' ) !== false ) {
		?>
		<div class="post_header_wrap post_header_wrap_in_header post_header_wrap_style_<?php
			echo esc_attr( equadio_get_theme_option( 'single_style' ) );
			if ( $equadio_with_featured_image ) {
				echo ' with_featured_image';
			}
		?>">
			<?php
			do_action( 'equadio_action_before_post_header' );
			equadio_show_layout( $equadio_post_header );
			do_action( 'equadio_action_after_post_header' );
			?>
		</div>
		<?php
	}
}
