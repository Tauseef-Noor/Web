<?php
/**
 * The template to display the page title and breadcrumbs
 *
 * @package EQUADIO
 * @since EQUADIO 1.0
 */

// Page (category, tag, archive, author) title

if ( equadio_need_page_title() ) {
	equadio_sc_layouts_showed( 'title', true );
	equadio_sc_layouts_showed( 'postmeta', true );
	?>
	<div class="top_panel_title sc_layouts_row sc_layouts_row_type_normal">
		<div class="content_wrap">
			<div class="sc_layouts_column sc_layouts_column_align_center">
				<div class="sc_layouts_item">
					<div class="sc_layouts_title sc_align_center">
						<?php
						// Post meta on the single post
						if ( is_single() ) {
							?>
							<div class="sc_layouts_title_meta">
							<?php
								equadio_show_post_meta(
									apply_filters(
										'equadio_filter_post_meta_args', array(
											'components' => join( ',', equadio_array_get_keys_by_value( equadio_get_theme_option( 'meta_parts' ) ) ),
											'counters'   => join( ',', equadio_array_get_keys_by_value( equadio_get_theme_option( 'counters' ) ) ),
											'seo'        => equadio_is_on( equadio_get_theme_option( 'seo_snippets' ) ),
										), 'header', 1
									)
								);
							?>
							</div>
							<?php
						}

						// Blog/Post title
						?>
						<div class="sc_layouts_title_title">
							<?php
							$equadio_blog_title           = equadio_get_blog_title();
							$equadio_blog_title_text      = '';
							$equadio_blog_title_class     = '';
							$equadio_blog_title_link      = '';
							$equadio_blog_title_link_text = '';
							if ( is_array( $equadio_blog_title ) ) {
								$equadio_blog_title_text      = $equadio_blog_title['text'];
								$equadio_blog_title_class     = ! empty( $equadio_blog_title['class'] ) ? ' ' . $equadio_blog_title['class'] : '';
								$equadio_blog_title_link      = ! empty( $equadio_blog_title['link'] ) ? $equadio_blog_title['link'] : '';
								$equadio_blog_title_link_text = ! empty( $equadio_blog_title['link_text'] ) ? $equadio_blog_title['link_text'] : '';
							} else {
								$equadio_blog_title_text = $equadio_blog_title;
							}
							?>
							<h1 itemprop="headline" class="sc_layouts_title_caption<?php echo esc_attr( $equadio_blog_title_class ); ?>">
								<?php
								$equadio_top_icon = equadio_get_term_image_small();
								if ( ! empty( $equadio_top_icon ) ) {
									$equadio_attr = equadio_getimagesize( $equadio_top_icon );
									?>
									<img src="<?php echo esc_url( $equadio_top_icon ); ?>" alt="<?php esc_attr_e( 'Site icon', 'equadio' ); ?>"
										<?php
										if ( ! empty( $equadio_attr[3] ) ) {
											equadio_show_layout( $equadio_attr[3] );
										}
										?>
									>
									<?php
								}
								echo wp_kses_post( $equadio_blog_title_text );
								?>
							</h1>
							<?php
							if ( ! empty( $equadio_blog_title_link ) && ! empty( $equadio_blog_title_link_text ) ) {
								?>
								<a href="<?php echo esc_url( $equadio_blog_title_link ); ?>" class="theme_button theme_button_small sc_layouts_title_link"><?php echo esc_html( $equadio_blog_title_link_text ); ?></a>
								<?php
							}

							// Category/Tag description
							if ( ! is_paged() && ( is_category() || is_tag() || is_tax() ) ) {
								the_archive_description( '<div class="sc_layouts_title_description">', '</div>' );
							}

							?>
						</div>
						<?php

						// Breadcrumbs
						ob_start();
						do_action( 'equadio_action_breadcrumbs' );
						$equadio_breadcrumbs = ob_get_contents();
						ob_end_clean();
						equadio_show_layout( $equadio_breadcrumbs, '<div class="sc_layouts_title_breadcrumbs">', '</div>' );
						?>
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php
}
