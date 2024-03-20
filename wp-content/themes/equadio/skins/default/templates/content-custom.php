<?php
/**
 * The custom template to display the content
 *
 * Used for index/archive/search.
 *
 * @package EQUADIO
 * @since EQUADIO 1.0.50
 */

$equadio_template_args = get_query_var( 'equadio_template_args' );
if ( is_array( $equadio_template_args ) ) {
	$equadio_columns    = empty( $equadio_template_args['columns'] ) ? 2 : max( 1, $equadio_template_args['columns'] );
	$equadio_blog_style = array( $equadio_template_args['type'], $equadio_columns );
} else {
	$equadio_blog_style = explode( '_', equadio_get_theme_option( 'blog_style' ) );
	$equadio_columns    = empty( $equadio_blog_style[1] ) ? 2 : max( 1, $equadio_blog_style[1] );
}
$equadio_blog_id       = equadio_get_custom_blog_id( join( '_', $equadio_blog_style ) );
$equadio_blog_style[0] = str_replace( 'blog-custom-', '', $equadio_blog_style[0] );
$equadio_expanded      = ! equadio_sidebar_present() && equadio_get_theme_option( 'expand_content' ) == 'expand';
$equadio_components    = ! empty( $equadio_template_args['meta_parts'] )
							? ( is_array( $equadio_template_args['meta_parts'] )
								? join( ',', $equadio_template_args['meta_parts'] )
								: $equadio_template_args['meta_parts']
								)
							: equadio_array_get_keys_by_value( equadio_get_theme_option( 'meta_parts' ) );
$equadio_post_format   = get_post_format();
$equadio_post_format   = empty( $equadio_post_format ) ? 'standard' : str_replace( 'post-format-', '', $equadio_post_format );

$equadio_blog_meta     = equadio_get_custom_layout_meta( $equadio_blog_id );
$equadio_custom_style  = ! empty( $equadio_blog_meta['scripts_required'] ) ? $equadio_blog_meta['scripts_required'] : 'none';

if ( ! empty( $equadio_template_args['slider'] ) || $equadio_columns > 1 || ! equadio_is_off( $equadio_custom_style ) ) {
	?><div class="
		<?php
		if ( ! empty( $equadio_template_args['slider'] ) ) {
			echo 'slider-slide swiper-slide';
		} else {
			echo esc_attr( ( equadio_is_off( $equadio_custom_style ) ? 'column' : sprintf( '%1$s_item %1$s_item', $equadio_custom_style ) ) . "-1_{$equadio_columns}" );
		}
		?>
	">
	<?php
}
?>
<article id="post-<?php the_ID(); ?>" data-post-id="<?php the_ID(); ?>"
	<?php
	post_class(
			'post_item post_item_container post_format_' . esc_attr( $equadio_post_format )
					. ' post_layout_custom post_layout_custom_' . esc_attr( $equadio_columns )
					. ' post_layout_' . esc_attr( $equadio_blog_style[0] )
					. ' post_layout_' . esc_attr( $equadio_blog_style[0] ) . '_' . esc_attr( $equadio_columns )
					. ( ! equadio_is_off( $equadio_custom_style )
						? ' post_layout_' . esc_attr( $equadio_custom_style )
							. ' post_layout_' . esc_attr( $equadio_custom_style ) . '_' . esc_attr( $equadio_columns )
						: ''
						)
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
	// Custom layout
	do_action( 'equadio_action_show_layout', $equadio_blog_id, get_the_ID() );
	?>
</article><?php
if ( ! empty( $equadio_template_args['slider'] ) || $equadio_columns > 1 || ! equadio_is_off( $equadio_custom_style ) ) {
	?></div><?php
	// Need opening PHP-tag above just after </div>, because <div> is a inline-block element (used as column)!
}
