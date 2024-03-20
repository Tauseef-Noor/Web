<?php
/**
 * The "Style 2" template to display the content of the single post or attachment:
 * featured image placed to the post header and title placed inside content
 *
 * @package EQUADIO
 * @since EQUADIO 1.75.0
 */
?>
<article id="post-<?php the_ID(); ?>"
	<?php
	post_class( 'post_item_single'
		. ' post_type_' . esc_attr( get_post_type() ) 
		. ' post_format_' . esc_attr( str_replace( 'post-format-', '', get_post_format() ) )
	);
	equadio_add_seo_itemprops();
	?>
>
<?php

	do_action( 'equadio_action_before_post_data' );

	equadio_add_seo_snippets();

	// Single post thumbnail and title
	if ( apply_filters( 'equadio_filter_single_post_header', is_singular( 'post' ) || is_singular( 'attachment' ) ) ) {
		ob_start();
		?>
		<div class="post_header_wrap post_header_wrap_in_content post_header_wrap_style_<?php
			echo esc_attr( equadio_get_theme_option( 'single_style' ) );
		?>">
			<?php
			// Post title and meta
			equadio_show_post_title_and_meta( array( 
				'author_avatar' => false,
				'show_labels'   => false,
				'share_type'    => 'list',	// block - icons with bg, list - small icons without background
				'split_meta_by' => 'share',
				'add_spaces'    => true,
			) );
			?>
		</div>
		<?php
		$equadio_post_header = ob_get_contents();
		ob_end_clean();
		if ( strpos( $equadio_post_header, 'post_title' ) !== false	|| strpos( $equadio_post_header, 'post_meta' ) !== false ) {
			do_action( 'equadio_action_before_post_header' );
			equadio_show_layout( $equadio_post_header );
			do_action( 'equadio_action_after_post_header' );
		}
	}

	do_action( 'equadio_action_before_post_content' );

	// Post content
	$equadio_share_position = equadio_array_get_keys_by_value( equadio_get_theme_option( 'share_position' ) );
	?>
	<div class="post_content post_content_single entry-content<?php
		if ( in_array( 'left', $equadio_share_position ) ) {
			echo ' post_info_vertical_present' . ( in_array( 'top', $equadio_share_position ) ? ' post_info_vertical_hide_on_mobile' : '' );
		}
	?>" itemprop="mainEntityOfPage">
		<?php
		if ( in_array( 'left', $equadio_share_position ) ) {
			?><div class="post_info_vertical"><?php
				equadio_show_post_meta(
					apply_filters(
						'equadio_filter_post_meta_args',
						array(
							'components'      => 'share',
							'class'           => 'post_share_vertical',
							'share_type'      => 'block',
							'share_direction' => 'vertical',
						),
						'single',
						1
					)
				);
			?></div><?php
		}
		the_content();
		?>
	</div><!-- .entry-content -->
	<?php
	do_action( 'equadio_action_after_post_content' );
	
	// Post footer: Tags, likes, share, author, prev/next links and comments
	do_action( 'equadio_action_before_post_footer' );
	?>
	<div class="post_footer post_footer_single entry-footer">
		<?php
		equadio_show_post_pagination();
		if ( is_single() && ! is_attachment() ) {
			equadio_show_post_footer();
		}
		?>
	</div>
	<?php
	do_action( 'equadio_action_after_post_footer' );
	?>
</article>
