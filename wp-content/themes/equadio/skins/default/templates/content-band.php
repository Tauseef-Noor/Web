<?php
/**
 * 'Band' template to display the content
 *
 * Used for index/archive/search.
 *
 * @package EQUADIO
 * @since EQUADIO 1.71.0
 */

$equadio_template_args = get_query_var( 'equadio_template_args' );

$equadio_columns       = 1;

$equadio_expanded      = ! equadio_sidebar_present() && equadio_get_theme_option( 'expand_content' ) == 'expand';

$equadio_post_format   = get_post_format();
$equadio_post_format   = empty( $equadio_post_format ) ? 'standard' : str_replace( 'post-format-', '', $equadio_post_format );

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
?>
<article id="post-<?php the_ID(); ?>" data-post-id="<?php the_ID(); ?>"
	<?php
	post_class( 'post_item post_item_container post_layout_band post_format_' . esc_attr( $equadio_post_format ) );
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
			'thumb_bg'   => true,
			'thumb_size' => equadio_get_thumb_size( 
								in_array( $equadio_post_format, array( 'gallery', 'audio', 'video' ) )
									? ( strpos( equadio_get_theme_option( 'body_style' ), 'full' ) !== false
										? 'full'
										: ( $equadio_expanded 
											? 'big' 
											: 'med'
											)
										)
									: 'masonry-big'
								)
		)
	);

	?><div class="post_content_wrap"><?php

		// Title and post meta
		$equadio_show_title = get_the_title() != '';
		$equadio_show_meta  = count( $equadio_components ) > 0 && ! in_array( $equadio_hover, array( 'border', 'pull', 'slide', 'fade', 'info' ) );
		if ( $equadio_show_title ) {
			?>
			<div class="post_header entry-header">
				<?php
				// Categories
				if ( apply_filters( 'equadio_filter_show_blog_categories', $equadio_show_meta && in_array( 'categories', $equadio_components ), array( 'categories' ), 'band' ) ) {
					do_action( 'equadio_action_before_post_category' );
					?>
					<div class="post_category">
						<?php
						equadio_show_post_meta( apply_filters(
															'equadio_filter_post_meta_args',
															array(
																'components' => 'categories',
																'seo'        => false,
																'echo'       => true,
																),
															'hover_' . $equadio_hover, 1
															)
											);
						?>
					</div>
					<?php
					$equadio_components = equadio_array_delete_by_value( $equadio_components, 'categories' );
					do_action( 'equadio_action_after_post_category' );
				}
				// Post title
				if ( apply_filters( 'equadio_filter_show_blog_title', true, 'band' ) ) {
					do_action( 'equadio_action_before_post_title' );
					if ( empty( $equadio_template_args['no_links'] ) ) {
						the_title( sprintf( '<h4 class="post_title entry-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h4>' );
					} else {
						the_title( '<h4 class="post_title entry-title">', '</h4>' );
					}
					do_action( 'equadio_action_after_post_title' );
				}
				?>
			</div><!-- .post_header -->
			<?php
		}

		// Post content
		if ( ! isset( $equadio_template_args['excerpt_length'] ) && ! in_array( $equadio_post_format, array( 'gallery', 'audio', 'video' ) ) ) {
			$equadio_template_args['excerpt_length'] = 30;
		}
		if ( apply_filters( 'equadio_filter_show_blog_excerpt', empty( $equadio_template_args['hide_excerpt'] ) && equadio_get_theme_option( 'excerpt_length' ) > 0, 'band' ) ) {
			?>
			<div class="post_content entry-content">
				<?php
				// Post content area
				equadio_show_post_content( $equadio_template_args, '<div class="post_content_inner">', '</div>' );
				?>
			</div><!-- .entry-content -->
			<?php
		}
		// Post meta
		if ( apply_filters( 'equadio_filter_show_blog_meta', $equadio_show_meta, $equadio_components, 'band' ) ) {
			if ( count( $equadio_components ) > 0 ) {
				do_action( 'equadio_action_before_post_meta' );
				equadio_show_post_meta(
					apply_filters(
						'equadio_filter_post_meta_args', array(
							'components' => join( ',', $equadio_components ),
							'seo'        => false,
							'echo'       => true,
						), 'band', 1
					)
				);
				do_action( 'equadio_action_after_post_meta' );
			}
		}
		// More button
		if ( apply_filters( 'equadio_filter_show_blog_readmore', ! $equadio_show_title, 'band' ) ) {
			if ( empty( $equadio_template_args['no_links'] ) ) {
				do_action( 'equadio_action_before_post_readmore' );
				equadio_show_post_more_link( $equadio_template_args, '<p>', '</p>' );
				do_action( 'equadio_action_after_post_readmore' );
			}
		}
		?>
	</div>
</article>
<?php

if ( is_array( $equadio_template_args ) ) {
	if ( ! empty( $equadio_template_args['slider'] ) || $equadio_columns > 1 ) {
		?>
		</div>
		<?php
	}
}
