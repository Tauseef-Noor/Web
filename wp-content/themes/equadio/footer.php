<?php
/**
 * The Footer: widgets area, logo, footer menu and socials
 *
 * @package EQUADIO
 * @since EQUADIO 1.0
 */

							// Widgets area inside page content
							equadio_create_widgets_area( 'widgets_below_content' );
						
							?>
						</div><!-- /.content -->
						<?php

						// Show main sidebar
						get_sidebar();
						?>
					</div><!-- /.content_wrap -->
					<?php

					// Widgets area below page content and related posts below page content
					$equadio_body_style = equadio_get_theme_option( 'body_style' );
					$equadio_widgets_name = equadio_get_theme_option( 'widgets_below_page' );
					$equadio_show_widgets = ! equadio_is_off( $equadio_widgets_name ) && is_active_sidebar( $equadio_widgets_name );
					$equadio_show_related = is_single() && equadio_get_theme_option( 'related_position' ) == 'below_page';
					if ( $equadio_show_widgets || $equadio_show_related ) {
						if ( 'fullscreen' != $equadio_body_style ) {
							?>
							<div class="content_wrap">
							<?php
						}
						// Show related posts before footer
						if ( $equadio_show_related ) {
							do_action( 'equadio_action_related_posts' );
						}

						// Widgets area below page content
						if ( $equadio_show_widgets ) {
							equadio_create_widgets_area( 'widgets_below_page' );
						}
						if ( 'fullscreen' != $equadio_body_style ) {
							?>
							</div><!-- /.content_wrap -->
							<?php
						}
					}
					?>
			</div><!-- /.page_content_wrap -->
			<?php

			// Don't display the footer elements while actions 'full_post_loading' and 'prev_post_loading'
			if ( ( ! is_singular( 'post' ) && ! is_singular( 'attachment' ) ) || ! in_array ( equadio_get_value_gp( 'action' ), array( 'full_post_loading', 'prev_post_loading' ) ) ) {
				
				// Skip link anchor to fast access to the footer from keyboard
				?>
				<a id="footer_skip_link_anchor" class="equadio_skip_link_anchor" href="#"></a>
				<?php

				do_action( 'equadio_action_before_footer' );

				// Footer
				$equadio_footer_type = equadio_get_theme_option( 'footer_type' );
				if ( 'custom' == $equadio_footer_type && ! equadio_is_layouts_available() ) {
					$equadio_footer_type = 'default';
				}
				get_template_part( apply_filters( 'equadio_filter_get_template_part', "templates/footer-" . sanitize_file_name( $equadio_footer_type ) ) );

				do_action( 'equadio_action_after_footer' );

			}
			?>

		</div><!-- /.page_wrap -->

	</div><!-- /.body_wrap -->

	<?php wp_footer(); ?>

</body>
</html>