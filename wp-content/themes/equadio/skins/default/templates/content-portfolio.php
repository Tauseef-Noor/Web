<?php
/**
 * The Portfolio template to display the content
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

$equadio_post_format = get_post_format();
$equadio_post_format = empty( $equadio_post_format ) ? 'standard' : str_replace( 'post-format-', '', $equadio_post_format );

?><div class="
<?php
if ( ! empty( $equadio_template_args['slider'] ) ) {
	echo ' slider-slide swiper-slide';
} else {
	echo ( equadio_is_blog_style_use_masonry( $equadio_blog_style[0] ) ? 'masonry_item masonry_item' : 'column' ) . '-1_' . esc_attr( $equadio_columns );
}
?>
"><article id="post-<?php the_ID(); ?>" 
	<?php
	post_class(
		'post_item post_item_container post_format_' . esc_attr( $equadio_post_format )
		. ' post_layout_portfolio'
		. ' post_layout_portfolio_' . esc_attr( $equadio_columns )
		. ( 'portfolio' != $equadio_blog_style[0] ? ' ' . esc_attr( $equadio_blog_style[0] )  . '_' . esc_attr( $equadio_columns ) : '' )
		. ( is_sticky() && ! is_paged() ? ' sticky' : '' )
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

	$equadio_hover   = ! empty( $equadio_template_args['hover'] ) && ! equadio_is_inherit( $equadio_template_args['hover'] )
								? $equadio_template_args['hover']
								: equadio_get_theme_option( 'image_hover' );

	if ( 'dots' == $equadio_hover ) {
		$equadio_post_link = empty( $equadio_template_args['no_links'] )
								? ( ! empty( $equadio_template_args['link'] )
									? $equadio_template_args['link']
									: get_permalink()
									)
								: '';
		$equadio_target    = ! empty( $equadio_post_link ) && false === strpos( $equadio_post_link, home_url() )
								? ' target="_blank" rel="nofollow"'
								: '';
	}
	
	// Meta parts
	$equadio_components = ! empty( $equadio_template_args['meta_parts'] )
							? ( is_array( $equadio_template_args['meta_parts'] )
								? $equadio_template_args['meta_parts']
								: explode( ',', $equadio_template_args['meta_parts'] )
								)
							: equadio_array_get_keys_by_value( equadio_get_theme_option( 'meta_parts' ) );

	// Featured image
	equadio_show_post_featured(
		array(
			'hover'         => $equadio_hover,
			'no_links'      => ! empty( $equadio_template_args['no_links'] ),
			'thumb_size'    => equadio_get_thumb_size(
									equadio_is_blog_style_use_masonry( $equadio_blog_style[0] )
										? (	strpos( equadio_get_theme_option( 'body_style' ), 'full' ) !== false || $equadio_columns < 3
											? 'masonry-big'
											: 'masonry'
											)
										: (	strpos( equadio_get_theme_option( 'body_style' ), 'full' ) !== false || $equadio_columns < 3
											? 'big'
											: 'med'
											)
								),
			'show_no_image' => true,
			'meta_parts'    => $equadio_components,
			'class'         => 'dots' == $equadio_hover ? 'hover_with_info' : '',
			'post_info'     => 'dots' == $equadio_hover
										? '<div class="post_info"><h5 class="post_title">'
											. ( ! empty( $equadio_post_link )
												? '<a href="' . esc_url( $equadio_post_link ) . '"' . ( ! empty( $target ) ? $target : '' ) . '>'
												: ''
												)
												. esc_html( get_the_title() ) 
											. ( ! empty( $equadio_post_link )
												? '</a>'
												: ''
												)
											. '</h5></div>'
										: '',
		)
	);
	?>
</article></div><?php
// Need opening PHP-tag above, because <article> is a inline-block element (used as column)!