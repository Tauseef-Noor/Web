<?php
/**
 * The template 'Style 2' to displaying related posts
 *
 * @package EQUADIO
 * @since EQUADIO 1.0
 */

$equadio_link        = get_permalink();
$equadio_post_format = get_post_format();
$equadio_post_format = empty( $equadio_post_format ) ? 'standard' : str_replace( 'post-format-', '', $equadio_post_format );
?><div id="post-<?php the_ID(); ?>" <?php post_class( 'related_item post_format_' . esc_attr( $equadio_post_format ) ); ?> data-post-id="<?php the_ID(); ?>">
	<?php
	equadio_show_post_featured(
		array(
			'thumb_size'    => apply_filters( 'equadio_filter_related_thumb_size', equadio_get_thumb_size( (int) equadio_get_theme_option( 'related_posts' ) == 1 ? 'huge' : 'big' ) ),
		)
	);
	?>
	<div class="post_header entry-header">
		<h6 class="post_title entry-title"><a href="<?php echo esc_url( $equadio_link ); ?>"><?php
			if ( '' == get_the_title() ) {
				esc_html_e( '- No title -', 'equadio' );
			} else {
				the_title();
			}
		?></a></h6>
        <?php

        if ( in_array(get_post_type(), array( 'post', 'attachment' ) ) ) {
            equadio_show_post_meta( array(
                    'components' => 'date, comments',
                    'seo' => false,
                )
            );
        }
        ?>
        <div class="related_button_wrap">
            <a class="sc_button sc_button_size_small related_more" href="<?php the_permalink(); ?>"><?php echo esc_html__( 'Read more', 'equadio' ); ?></a>
        </div>
	</div>
</div>
