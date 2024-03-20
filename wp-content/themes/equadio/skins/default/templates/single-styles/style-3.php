<?php
/**
 * The "Style 3" template to display the post header of the single post or attachment:
 * featured image and title placed in the post header
 *
 * @package EQUADIO
 * @since EQUADIO 1.75.0
 */

if ( apply_filters( 'equadio_filter_single_post_header', is_singular( 'post' ) || is_singular( 'attachment' ) ) ) {
	$post_format = str_replace( 'post-format-', '', get_post_format() );
	ob_start();
	// Featured image
	equadio_show_post_featured_image( array(
		'thumb_bg'  => true,
		'popup'     => true,
		'class_avg' => in_array( $post_format, array( 'video' ) ) ? 'content_wrap' : '',
	) );
	$equadio_post_header = ob_get_contents();
	ob_end_clean();
	$equadio_with_featured_image = equadio_is_with_featured_image( $equadio_post_header );
	// Post title and meta
	ob_start();
	equadio_show_post_title_and_meta( array(
										'content_wrap'  => true,
										'share_type'    => 'list',
										'author_avatar' => false,
										'show_labels'   => true,
										'add_spaces'    => true,
										)
									);
	$equadio_post_header .= ob_get_contents();
	ob_end_clean();

	if ( strpos( $equadio_post_header, 'post_featured' ) !== false
		|| strpos( $equadio_post_header, 'post_title' ) !== false
		|| strpos( $equadio_post_header, 'post_meta' ) !== false
	) {
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
