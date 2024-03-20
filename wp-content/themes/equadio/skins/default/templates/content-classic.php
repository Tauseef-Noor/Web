<?php
/**
 * The Classic template to display the content
 *
 * Used for index/archive/search.
 *
 * @package EQUADIO
 * @since EQUADIO 1.0
 */

$equadio_template_args = get_query_var( 'equadio_template_args' );

if ( is_array( $equadio_template_args ) ) {
	$equadio_columns    = empty( $equadio_template_args['columns'] ) ? 2 : max( 1, $equadio_template_args['columns'] );
	$equadio_blog_style = array( $equadio_template_args['type'], $equadio_columns );
} else {
	$equadio_blog_style = explode( '_', equadio_get_theme_option( 'blog_style' ) );
	$equadio_columns    = empty( $equadio_blog_style[1] ) ? 2 : max( 1, $equadio_blog_style[1] );
}
$equadio_expanded   = ! equadio_sidebar_present() && equadio_get_theme_option( 'expand_content' ) == 'expand';

$equadio_post_format = get_post_format();
$equadio_post_format = empty( $equadio_post_format ) ? 'standard' : str_replace( 'post-format-', '', $equadio_post_format );

?><div class="<?php
	if ( ! empty( $equadio_template_args['slider'] ) ) {
		echo ' slider-slide swiper-slide';
	} else {
		echo ( equadio_is_blog_style_use_masonry( $equadio_blog_style[0] ) ? 'masonry_item masonry_item' : 'column' ) . '-1_' . esc_attr( $equadio_columns );
	}
?>"><article id="post-<?php the_ID(); ?>" data-post-id="<?php the_ID(); ?>"
	<?php
	post_class(
		'post_item post_item_container post_format_' . esc_attr( $equadio_post_format )
				. ' post_layout_classic post_layout_classic_' . esc_attr( $equadio_columns )
				. ' post_layout_' . esc_attr( $equadio_blog_style[0] )
				. ' post_layout_' . esc_attr( $equadio_blog_style[0] ) . '_' . esc_attr( $equadio_columns )
	);
	equadio_add_blog_animation( $equadio_template_args );
	?>
>
	<?php

	// Sticky label
	if ( is_sticky() && ! is_paged() ) {
		?>
		<span class="post_label label_sticky"></span>
		<?php
	}

	// Featured image
	$equadio_hover      = ! empty( $equadio_template_args['hover'] ) && ! equadio_is_inherit( $equadio_template_args['hover'] )
							? $equadio_template_args['hover']
							: equadio_get_theme_option( 'image_hover' );

	$equadio_components = ! empty( $equadio_template_args['meta_parts'] )
							? ( is_array( $equadio_template_args['meta_parts'] )
								? $equadio_template_args['meta_parts']
								: explode( ',', $equadio_template_args['meta_parts'] )
								)
							: equadio_array_get_keys_by_value( equadio_get_theme_option( 'meta_parts' ) );

	equadio_show_post_featured(
		array(
			'thumb_size' => equadio_get_thumb_size(
				'classic' == $equadio_blog_style[0]
						? ( strpos( equadio_get_theme_option( 'body_style' ), 'full' ) !== false
								? ( $equadio_columns > 2 ? 'big' : 'huge' )
								: ( $equadio_columns > 2
									? ( $equadio_expanded ? 'med' : 'small' )
									: ( $equadio_expanded ? 'big' : 'med' )
									)
							)
						: ( strpos( equadio_get_theme_option( 'body_style' ), 'full' ) !== false
								? ( $equadio_columns > 2 ? 'masonry-big' : 'full' )
								: ( $equadio_columns <= 2 && $equadio_expanded ? 'masonry-big' : 'masonry' )
							)
			),
			'hover'      => $equadio_hover,
			'meta_parts' => $equadio_components,
			'no_links'   => ! empty( $equadio_template_args['no_links'] ),
		)
	);

    ?><div class="post_wrapper"><?php

        // Title and post meta
        $equadio_show_title = get_the_title() != '';
        $equadio_show_meta  = count( $equadio_components ) > 0 && ! in_array( $equadio_hover, array( 'border', 'pull', 'slide', 'fade', 'info' ) );

        if ( $equadio_show_title || $equadio_show_meta ) {
            ?>
            <div class="post_header entry-header">
                <?php
                // Post title
                if ( apply_filters( 'equadio_filter_show_blog_title', true, 'classic' ) ) {
                    do_action( 'equadio_action_before_post_title' );
                    if ( empty( $equadio_template_args['no_links'] ) ) {
                        the_title( sprintf( '<h4 class="post_title entry-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h4>' );
                    } else {
                        the_title( '<h4 class="post_title entry-title">', '</h4>' );
                    }
                    do_action( 'equadio_action_after_post_title' );
                }
                // Post meta
                if ( apply_filters( 'equadio_filter_show_blog_meta', $equadio_show_meta, $equadio_components, 'classic' ) ) {
                    if ( count( $equadio_components ) > 0 ) {
                        do_action( 'equadio_action_before_post_meta' );
                        equadio_show_post_meta(
                            apply_filters(
                                'equadio_filter_post_meta_args', array(
                                'components' => join( ',', $equadio_components ),
                                'seo'        => false,
                                'echo'       => true,
                            ), $equadio_blog_style[0], $equadio_columns
                            )
                        );
                        do_action( 'equadio_action_after_post_meta' );
                    }
                }
                ?>
            </div><!-- .entry-header -->
            <?php
        }

        // Post content
        ob_start();
        if ( apply_filters( 'equadio_filter_show_blog_excerpt', empty( $equadio_template_args['hide_excerpt'] ) && equadio_get_theme_option( 'excerpt_length' ) > 0, 'classic' ) ) {
            equadio_show_post_content( $equadio_template_args, '<div class="post_content_inner">', '</div>' );
        }
        $equadio_content = ob_get_contents();
        ob_end_clean();

        equadio_show_layout( $equadio_content, '<div class="post_content entry-content">', '</div><!-- .entry-content -->' );

        // More button
        if ( apply_filters( 'equadio_filter_show_blog_readmore', ! $equadio_show_title, 'classic' ) ) {
            if ( empty( $equadio_template_args['no_links'] ) ) {
                do_action( 'equadio_action_before_post_readmore' );
                equadio_show_post_more_link( $equadio_template_args, '<p>', '</p>' );
                do_action( 'equadio_action_after_post_readmore' );
            }
        }
        ?>
    </div><!-- .post_wrapper -->
</article></div><?php
// Need opening PHP-tag above, because <div> is a inline-block element (used as column)!
