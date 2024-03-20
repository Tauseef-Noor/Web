<?php
/**
 * The template to display single post
 *
 * @package EQUADIO
 * @since EQUADIO 1.0
 */

// Full post loading
$full_post_loading        = equadio_get_value_gp( 'action' ) == 'full_post_loading';

// Prev post loading
$prev_post_loading        = equadio_get_value_gp( 'action' ) == 'prev_post_loading';
$prev_post_loading_type   = equadio_get_theme_option( 'posts_navigation_scroll_which_block' );

// Position of the related posts
$equadio_related_position = equadio_get_theme_option( 'related_position' );

// Type of the prev/next post navigation
$equadio_posts_navigation = equadio_get_theme_option( 'posts_navigation' );
$equadio_prev_post        = false;

// Rewrite style of the single post if current post loading via AJAX and featured image and title is not in the content
if ( ( $full_post_loading 
		|| 
		( $prev_post_loading && 'article' == $prev_post_loading_type )
	) 
	&& 
	! in_array( equadio_get_theme_option( 'single_style' ), array( 'style-6' ) )
) {
	equadio_storage_set_array( 'options_meta', 'single_style', 'style-6' );
}

get_header();

while ( have_posts() ) {

	the_post();

	// Type of the prev/next post navigation
	if ( 'scroll' == $equadio_posts_navigation ) {
		$equadio_prev_post = get_previous_post( true );         // Get post from same category
		if ( ! $equadio_prev_post ) {
			$equadio_prev_post = get_previous_post( false );    // Get post from any category
			if ( ! $equadio_prev_post ) {
				$equadio_posts_navigation = 'links';
			}
		}
	}

	// Override some theme options to display featured image, title and post meta in the dynamic loaded posts
	if ( $full_post_loading || ( $prev_post_loading && $equadio_prev_post ) ) {
		equadio_sc_layouts_showed( 'featured', false );
		equadio_sc_layouts_showed( 'title', false );
		equadio_sc_layouts_showed( 'postmeta', false );
	}

	// If related posts should be inside the content
	if ( strpos( $equadio_related_position, 'inside' ) === 0 ) {
		ob_start();
	}

	// Display post's content
	get_template_part( apply_filters( 'equadio_filter_get_template_part', 'templates/content', 'single-' . equadio_get_theme_option( 'single_style' ) ), 'single-' . equadio_get_theme_option( 'single_style' ) );

	// If related posts should be inside the content
	if ( strpos( $equadio_related_position, 'inside' ) === 0 ) {
		$equadio_content = ob_get_contents();
		ob_end_clean();

		ob_start();
		do_action( 'equadio_action_related_posts' );
		$equadio_related_content = ob_get_contents();
		ob_end_clean();

		$equadio_related_position_inside = max( 0, min( 9, equadio_get_theme_option( 'related_position_inside' ) ) );
		if ( 0 == $equadio_related_position_inside ) {
			$equadio_related_position_inside = mt_rand( 1, 9 );
		}

		$equadio_p_number = 0;
		$equadio_related_inserted = false;
		$equadio_in_block = false;
		for ( $i = 0; $i < strlen( $equadio_content ) - 3; $i++ ) {
			if ( $equadio_content[ $i ] != '<' ) {
				continue;
			}
			if ( $equadio_in_block ) {
				if ( strtolower( substr( $equadio_content, $i + 1, 12 ) ) == '/blockquote>' ) {
					$equadio_in_block = false;
					$i += 12;
				}
				continue;
			} else if ( strtolower( substr( $equadio_content, $i + 1, 10 ) ) == 'blockquote' && in_array( $equadio_content[ $i + 11 ], array( '>', ' ' ) ) ) {
				$equadio_in_block = true;
				$i += 11;
				continue;
			} else if ( 'p' == $equadio_content[ $i + 1 ] && in_array( $equadio_content[ $i + 2 ], array( '>', ' ' ) ) ) {
				$equadio_p_number++;
				if ( $equadio_related_position_inside == $equadio_p_number ) {
					$equadio_related_inserted = true;
					$equadio_content = ( $i > 0 ? substr( $equadio_content, 0, $i ) : '' )
										. $equadio_related_content
										. substr( $equadio_content, $i );
				}
			}
		}
		if ( ! $equadio_related_inserted ) {
			$equadio_content .= $equadio_related_content;
		}

		equadio_show_layout( $equadio_content );
	}

	// Comments
	do_action( 'equadio_action_before_comments' );
	comments_template();
	do_action( 'equadio_action_after_comments' );

	// Related posts
	if ( 'below_content' == $equadio_related_position
		&& ( 'scroll' != $equadio_posts_navigation || equadio_get_theme_option( 'posts_navigation_scroll_hide_related' ) == 0 )
		&& ( ! $full_post_loading || equadio_get_theme_option( 'open_full_post_hide_related' ) == 0 )
	) {
		do_action( 'equadio_action_related_posts' );
	}

	// Post navigation: type 'scroll'
	if ( 'scroll' == $equadio_posts_navigation && ! $full_post_loading ) {
		?>
		<div class="nav-links-single-scroll"
			data-post-id="<?php echo esc_attr( get_the_ID( $equadio_prev_post ) ); ?>"
			data-post-link="<?php echo esc_attr( get_permalink( $equadio_prev_post ) ); ?>"
			data-post-title="<?php the_title_attribute( array( 'post' => $equadio_prev_post ) ); ?>">
		</div>
		<?php
	}
}

get_footer();
