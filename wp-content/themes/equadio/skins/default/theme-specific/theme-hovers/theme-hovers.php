<?php
/**
 * Generate custom CSS for theme hovers
 *
 * @package EQUADIO
 * @since EQUADIO 1.0
 */

// Theme init priorities:
// 3 - add/remove Theme Options elements
if ( ! function_exists( 'equadio_hovers_theme_setup3' ) ) {
	add_action( 'after_setup_theme', 'equadio_hovers_theme_setup3', 3 );
	function equadio_hovers_theme_setup3() {

		// Add 'Buttons hover' option
		equadio_storage_set_array_after(
			'options', 'general_effects_info', array(
				'button_hover' => array(
					'title'   => esc_html__( "Button hover", 'equadio' ),
					'desc'    => wp_kses_data( __( 'Select a hover effect for theme buttons', 'equadio' ) ),
					'std'     => 'default',
					'options' => array(
						'default'      => esc_html__( 'Fade', 'equadio' ),
					),
					'type'    => 'select',
				),
				'image_hover'  => array(
					'title'    => esc_html__( "Image hover", 'equadio' ),
					'desc'     => wp_kses_data( __( 'Select a hover effect for theme images', 'equadio' ) ),
					'std'      => 'icon',
					'override' => array(
						'mode'    => 'page',
						'section' => esc_html__( 'Content', 'equadio' ),
					),
					'options'  => equadio_get_list_hovers(),
					'type'     => 'select',
				),
			)
		);
	}
}

// Theme init priorities:
// 9 - register other filters (for installer, etc.)
if ( ! function_exists( 'equadio_hovers_theme_setup9' ) ) {
	add_action( 'after_setup_theme', 'equadio_hovers_theme_setup9', 9 );
	function equadio_hovers_theme_setup9() {
		add_action( 'wp_enqueue_scripts', 'equadio_hovers_frontend_scripts', 1100 );      // Priority 1100 -  after theme scripts (1000)
		add_action( 'wp_enqueue_scripts', 'equadio_hovers_frontend_styles', 1100 );       // Priority 1100 -  after theme/skin styles (1050)
		add_action( 'wp_enqueue_scripts', 'equadio_hovers_responsive_styles', 2100 );     // Priority 2100 -  after theme/skin responsive (2000)
		add_filter( 'equadio_filter_localize_script', 'equadio_hovers_localize_script' );
		add_filter( 'equadio_filter_merge_scripts', 'equadio_hovers_merge_scripts' );
		add_filter( 'equadio_filter_merge_styles', 'equadio_hovers_merge_styles' );
		add_filter( 'equadio_filter_merge_styles_responsive', 'equadio_hovers_merge_styles_responsive' );
	}
}

// Enqueue hover styles and scripts
if ( ! function_exists( 'equadio_hovers_frontend_scripts' ) ) {	
	function equadio_hovers_frontend_scripts() {
		if ( equadio_is_on( equadio_get_theme_option( 'debug_mode' ) ) ) {
			$equadio_url = equadio_get_file_url( 'theme-specific/theme-hovers/theme-hovers.js' );
			if ( '' != $equadio_url ) {
				wp_enqueue_script( 'equadio-hovers', $equadio_url, array( 'jquery' ), null, true );
			}
		}
	}
}

// Enqueue styles for frontend
if ( ! function_exists( 'equadio_hovers_frontend_styles' ) ) {	
	function equadio_hovers_frontend_styles() {
		if ( equadio_is_on( equadio_get_theme_option( 'debug_mode' ) ) ) {
			$equadio_url = equadio_get_file_url( 'theme-specific/theme-hovers/theme-hovers.css' );
			if ( '' != $equadio_url ) {
				wp_enqueue_style( 'equadio-hovers', $equadio_url, array(), null );
			}
		}
	}
}

// Enqueue responsive styles for frontend
if ( ! function_exists( 'equadio_hovers_responsive_styles' ) ) {	
	function equadio_hovers_responsive_styles() {
		if ( equadio_is_on( equadio_get_theme_option( 'debug_mode' ) ) ) {
			$equadio_url = equadio_get_file_url( 'theme-specific/theme-hovers/theme-hovers-responsive.css' );
			if ( '' != $equadio_url ) {
				wp_enqueue_style( 'equadio-hovers-responsive', $equadio_url, array(), null );
			}
		}
	}
}

// Merge hover effects into single css
if ( ! function_exists( 'equadio_hovers_merge_styles' ) ) {	
	function equadio_hovers_merge_styles( $list ) {
		$list[] = 'theme-specific/theme-hovers/theme-hovers.css';
		return $list;
	}
}

// Merge hover effects to the single css (responsive)
if ( ! function_exists( 'equadio_hovers_merge_styles_responsive' ) ) {	
	function equadio_hovers_merge_styles_responsive( $list ) {
		$list[] = 'theme-specific/theme-hovers/theme-hovers-responsive.css';
		return $list;
	}
}

// Add hover effect's vars to the localize array
if ( ! function_exists( 'equadio_hovers_localize_script' ) ) {	
	function equadio_hovers_localize_script( $arr ) {
		$arr['button_hover'] = equadio_get_theme_option( 'button_hover' );
		return $arr;
	}
}

// Merge hover effects to the single js
if ( ! function_exists( 'equadio_hovers_merge_scripts' ) ) {	
	function equadio_hovers_merge_scripts( $list ) {
		$list[] = 'theme-specific/theme-hovers/theme-hovers.js';
		return $list;
	}
}

// Add hover icons on the featured image
if ( ! function_exists( 'equadio_hovers_add_icons' ) ) {
	function equadio_hovers_add_icons( $hover, $args = array() ) {

		// Additional parameters
		$args = array_merge(
			array(
				'cat'        => '',
				'image'      => null,
				'no_links'   => false,
				'link'       => '',
				'post_info'  => '',
				'meta_parts' => ''
			), $args
		);

		$post_link = empty( $args['no_links'] )
						? ( ! empty( $args['link'] )
							? $args['link']
							: get_permalink()
							)
						: '';
		$no_link   = 'javascript:void(0)';
		$target    = ! empty( $post_link ) && false === strpos( $post_link, home_url() )
						? ' target="_blank" rel="nofollow"'
						: '';

		if ( in_array( $hover, array( 'icons', 'zoom' ) ) ) {
			// Hover style 'Icons and 'Zoom'
			if ( $args['image'] ) {
				$large_image = $args['image'];
			} else {
				$attachment = wp_get_attachment_image_src( get_post_thumbnail_id(), 'masonry-big' );
				if ( ! empty( $attachment[0] ) ) {
					$large_image = $attachment[0];
				}
			}
			?>
			<div class="icons">
				<a href="<?php echo ! empty( $post_link ) ? esc_url( $post_link ) : esc_attr($no_link); ?>" <?php equadio_show_layout($target); ?> aria-hidden="true" class="icon-link
									<?php
									if ( empty( $large_image ) ) {
										echo ' single_icon';
									}
									?>
				"></a>
				<?php if ( ! empty( $large_image ) ) { ?>
				<a href="<?php echo esc_url( $large_image ); ?>" aria-hidden="true" class="icon-search" title="<?php the_title_attribute( '' ); ?>"></a>
				<?php } ?>
			</div>
			<?php

		} elseif ( 'icon' == $hover ) {
			// Hover style 'Icon'
			?>
            <a href="<?php echo ! empty( $post_link ) ? esc_url( $post_link ) : esc_attr($no_link); ?>" <?php equadio_show_layout($target); ?> aria-hidden="true" class="icons"><span aria-hidden="true" class="icon-add"></span></a>
			<?php

		} elseif ( 'dots' == $hover ) {
			// Hover style 'Dots'
			?>
			<a href="<?php echo ! empty( $post_link ) ? esc_url( $post_link ) : esc_attr($no_link); ?>" <?php equadio_show_layout($target); ?> aria-hidden="true" class="icons"><span></span><span></span><span></span></a>
			<?php

        } elseif ( 'simple' == $hover ) {
            // Hover style 'Simple'
            ?>
            <a href="<?php echo ! empty( $post_link ) ? esc_url( $post_link ) : esc_attr($no_link); ?>" <?php equadio_show_layout($target); ?> aria-hidden="true" class="simple"></a>
            <?php

		} elseif ( 'info' == $hover ) {
			// Hover style 'Info'
			if ( ! empty( $args['post_info'] ) ) {
				equadio_show_layout( $args['post_info'] );
			} else {
				$equadio_components = empty( $args['meta_parts'] )
										? equadio_array_get_keys_by_value( equadio_get_theme_option( 'meta_parts' ) )
										: ( is_array( $args['meta_parts'] )
											? $args['meta_parts']
											: explode( ',', $args['meta_parts'] )
											);
				?>
				<div class="post_info">
					<?php
					if ( in_array( 'categories', $equadio_components ) ) {
						if ( apply_filters( 'equadio_filter_show_blog_categories', true, array( 'categories' ) ) ) {
							?>
							<div class="post_category">
								<?php
								$categories = equadio_show_post_meta( apply_filters(
																	'equadio_filter_post_meta_args',
																	array(
																		'components' => 'categories',
																		'seo'        => false,
																		'echo'       => false,
																		),
																	'hover_' . $hover, 1
																	)
													);
								equadio_show_layout( str_replace( ', ', '', $categories ) );
								?>
							</div>
							<?php
						}
						$equadio_components = equadio_array_delete_by_value( $equadio_components, 'categories' );
					}
					if ( apply_filters( 'equadio_filter_show_blog_title', true ) ) {
						?>
						<h4 class="post_title">
							<?php
							if ( ! empty( $post_link ) ) {
								?>
								<a href="<?php echo esc_url( $post_link ); ?>" <?php equadio_show_layout($target); ?>>
								<?php
							}
							the_title();
							if ( ! empty( $post_link ) ) {
								?>
								</a>
								<?php
							}
							?>
						</h4>
						<?php
					}
					?>
					<div class="post_descr">
						<?php
						if ( ! empty( $equadio_components ) && count( $equadio_components ) > 0 ) {
							if ( apply_filters( 'equadio_filter_show_blog_meta', true, $equadio_components ) ) {
								equadio_show_post_meta(
									apply_filters(
										'equadio_filter_post_meta_args', array(
											'components' => join( ',', $equadio_components ),
											'seo'        => false,
											'echo'       => true,
										), 'hover_' . $hover, 1
									)
								);
							}
						}
						?>
					</div>
					<?php
					if ( ! empty( $post_link ) ) {
						?>
						<a class="post_link" href="<?php echo esc_url( $post_link ); ?>" <?php equadio_show_layout($target); ?>></a>
						<?php
					}
					?>
				</div>
				<?php
			}

		} elseif ( in_array( $hover, array( 'fade', 'pull', 'slide', 'border', 'excerpt' ) ) ) {
			// Hover style 'Fade', 'Slide', 'Pull', 'Border', 'Excerpt'
			if ( ! empty( $args['post_info'] ) ) {
				equadio_show_layout( $args['post_info'] );
			} else {
				?>
				<div class="post_info">
					<div class="post_info_back">
						<?php
						if ( apply_filters( 'equadio_filter_show_blog_title', true ) ) {
							?>
							<h4 class="post_title">
								<?php
								if ( ! empty( $post_link ) ) {
									?>
									<a href="<?php echo esc_url( $post_link ); ?>" <?php equadio_show_layout($target); ?>>
									<?php
								}
								the_title();
								if ( ! empty( $post_link ) ) {
									?>
									</a>
									<?php
								}
								?>
							</h4>
							<?php
						}
						?>
						<div class="post_descr">
							<?php
							if ( 'excerpt' != $hover ) {
								$equadio_components = empty( $args['meta_parts'] )
														? equadio_array_get_keys_by_value( equadio_get_theme_option( 'meta_parts' ) )
														: ( is_array( $args['meta_parts'] )
															? $args['meta_parts']
															: explode( ',', $args['meta_parts'] )
															);
								if ( ! empty( $equadio_components ) ) {
									if ( apply_filters( 'equadio_filter_show_blog_meta', true, $equadio_components ) ) {
										equadio_show_post_meta(
											apply_filters(
												'equadio_filter_post_meta_args', array(
													'components' => $equadio_components,
													'seo'        => false,
													'echo'       => true,
												), 'hover_' . $hover, 1
											)
										);
									}
								}
							}
							// Remove the condition below if you want display excerpt
							if ( 'excerpt' == $hover ) {
								if ( apply_filters( 'equadio_filter_show_blog_excerpt', true ) ) {
									?>
									<div class="post_excerpt"><?php
										equadio_show_layout( get_the_excerpt() );
									?></div>
									<?php
								}
							}
							?>
						</div>
						<?php
						if ( ! empty( $post_link ) ) {
							?>
							<a class="post_link" href="<?php echo esc_url( $post_link ); ?>" <?php equadio_show_layout($target); ?>></a>
							<?php
						}
						?>
					</div>
					<?php
					if ( ! empty( $post_link ) ) {
						?>
						<a class="post_link" href="<?php echo esc_url( $post_link ); ?>" <?php equadio_show_layout($target); ?>></a>
						<?php
					}
					?>
				</div>
				<?php
			}

		} elseif ( ! empty( $post_link ) ) {
			// Hover style empty
			?>
			<a href="<?php echo esc_url( $post_link ); ?>" <?php equadio_show_layout($target); ?> aria-hidden="true" class="icons"></a>
			<?php
		}
	}
}
