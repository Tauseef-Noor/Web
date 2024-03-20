<?php
/**
 * The default template to display the content
 *
 * Used for index/archive/search.
 *
 * @package EQUADIO
 * @since EQUADIO 1.0
 */

$equadio_template_args = get_query_var( 'equadio_template_args' );
$equadio_columns = 1;
if ( is_array( $equadio_template_args ) ) {
	$equadio_columns    = empty( $equadio_template_args['columns'] ) ? 1 : max( 1, $equadio_template_args['columns'] );
	$equadio_blog_style = array( $equadio_template_args['type'], $equadio_columns );
	if ( ! empty( $equadio_template_args['slider'] ) ) {
		?><div class="slider-slide swiper-slide">
		<?php
	} elseif ( $equadio_columns > 1 ) {
		?>
		<div class="column-1_<?php echo esc_attr( $equadio_columns ); ?>">
		<?php
	}
}
$equadio_expanded    = ! equadio_sidebar_present() && equadio_get_theme_option( 'expand_content' ) == 'expand';
$equadio_post_format = get_post_format();
$equadio_post_format = empty( $equadio_post_format ) ? 'standard' : str_replace( 'post-format-', '', $equadio_post_format );
?>
<article id="post-<?php the_ID(); ?>" data-post-id="<?php the_ID(); ?>"
	<?php
	post_class( 'post_item post_item_container post_layout_excerpt post_format_' . esc_attr( $equadio_post_format ) );
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
								: array_map( 'trim', explode( ',', $equadio_template_args['meta_parts'] ) )
								)
							: equadio_array_get_keys_by_value( equadio_get_theme_option( 'meta_parts' ) );
	equadio_show_post_featured(
		array(
			'no_links'   => ! empty( $equadio_template_args['no_links'] ),
			'hover'      => $equadio_hover,
			'meta_parts' => $equadio_components,
			'thumb_size' => equadio_get_thumb_size( strpos( equadio_get_theme_option( 'body_style' ), 'full' ) !== false
								? 'full'
								: ( $equadio_expanded 
									? 'huge' 
									: 'big' 
									)
								),
		)
	);

	?><div class="post_wrapper"><?php

	// Title and post meta
	$equadio_show_title = get_the_title() != '';
	$equadio_show_meta  = count( $equadio_components ) > 0 && ! in_array( $equadio_hover, array( 'border', 'pull', 'slide', 'fade', 'info' ) );

	if ( $equadio_show_title || $equadio_show_meta) {
		?><div class="post_header entry-header"><?php
                // Post title
                if ( apply_filters( 'equadio_filter_show_blog_title', true, 'excerpt' ) ) {
                    do_action( 'equadio_action_before_post_title' );
                    if ( empty( $equadio_template_args['no_links'] ) ) {
                        the_title( sprintf( '<h3 class="post_title entry-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h3>' );
                    } else {
                        the_title( '<h3 class="post_title entry-title">', '</h3>' );
                    }
                    do_action( 'equadio_action_after_post_title' );
                }

                // Post meta
                if ( apply_filters( 'equadio_filter_show_blog_meta', $equadio_show_meta, $equadio_components, 'excerpt' ) ) {
                    if ( count( $equadio_components ) > 0 ) {
                        do_action( 'equadio_action_before_post_meta' );
                        equadio_show_post_meta(
                            apply_filters(
                                'equadio_filter_post_meta_args', array(
                                    'components' => join( ',', $equadio_components ),
                                    'seo'        => false,
                                    'echo'       => true,
                                ), 'excerpt', 1
                            )
                        );
                        do_action( 'equadio_action_after_post_meta' );
                    }
                }

                ?>
            </div><!-- .post_header -->
		<?php
	}

	// Post content
	if ( apply_filters( 'equadio_filter_show_blog_excerpt', empty( $equadio_template_args['hide_excerpt'] ) && equadio_get_theme_option( 'excerpt_length' ) > 0, 'excerpt' ) ) {
		?>
		<div class="post_content entry-content">
			<?php
			if ( equadio_get_theme_option( 'blog_content' ) == 'fullpost' ) {
				// Post content area
				?>
				<div class="post_content_inner">
					<?php
					do_action( 'equadio_action_before_full_post_content' );
					the_content( '' );
					do_action( 'equadio_action_after_full_post_content' );
					?>
				</div>
				<?php
				// Inner pages
				wp_link_pages(
					array(
						'before'      => '<div class="page_links"><span class="page_links_title">' . esc_html__( 'Pages:', 'equadio' ) . '</span>',
						'after'       => '</div>',
						'link_before' => '<span>',
						'link_after'  => '</span>',
						'pagelink'    => '<span class="screen-reader-text">' . esc_html__( 'Page', 'equadio' ) . ' </span>%',
						'separator'   => '<span class="screen-reader-text">, </span>',
					)
				);
			} else {
				// Post content area
				equadio_show_post_content( $equadio_template_args, '<div class="post_content_inner">', '</div>' );
			}

			// More button
			if ( apply_filters( 'equadio_filter_show_blog_readmore', true, 'excerpt' ) ) {
				if ( empty( $equadio_template_args['no_links'] ) ) {
					do_action( 'equadio_action_before_post_readmore' );
					if ( equadio_get_theme_option( 'blog_content' ) != 'fullpost' ) {
						equadio_show_post_more_link( $equadio_template_args, '<p>', '</p>' );
					} else {
						equadio_show_post_comments_link( $equadio_template_args, '<p>', '</p>' );
					}
					do_action( 'equadio_action_after_post_readmore' );
				}
			}
			?>
		</div><!-- .entry-content -->
		<?php
	}
	?>
	</div><!-- .post_wrapper -->
</article>
<?php

if ( is_array( $equadio_template_args ) ) {
	if ( ! empty( $equadio_template_args['slider'] ) || $equadio_columns > 1 ) {
		?>
		</div>
		<?php
	}
}
