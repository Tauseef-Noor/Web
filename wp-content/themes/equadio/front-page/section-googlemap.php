<div class="front_page_section front_page_section_googlemap<?php
	$equadio_scheme = equadio_get_theme_option( 'front_page_googlemap_scheme' );
	if ( ! empty( $equadio_scheme ) && ! equadio_is_inherit( $equadio_scheme ) ) {
		echo ' scheme_' . esc_attr( $equadio_scheme );
	}
	echo ' front_page_section_paddings_' . esc_attr( equadio_get_theme_option( 'front_page_googlemap_paddings' ) );
	if ( equadio_get_theme_option( 'front_page_googlemap_stack' ) ) {
		echo ' sc_stack_section_on';
	}
?>"
		<?php
		$equadio_css      = '';
		$equadio_bg_image = equadio_get_theme_option( 'front_page_googlemap_bg_image' );
		if ( ! empty( $equadio_bg_image ) ) {
			$equadio_css .= 'background-image: url(' . esc_url( equadio_get_attachment_url( $equadio_bg_image ) ) . ');';
		}
		if ( ! empty( $equadio_css ) ) {
			echo ' style="' . esc_attr( $equadio_css ) . '"';
		}
		?>
>
<?php
	// Add anchor
	$equadio_anchor_icon = equadio_get_theme_option( 'front_page_googlemap_anchor_icon' );
	$equadio_anchor_text = equadio_get_theme_option( 'front_page_googlemap_anchor_text' );
if ( ( ! empty( $equadio_anchor_icon ) || ! empty( $equadio_anchor_text ) ) && shortcode_exists( 'trx_sc_anchor' ) ) {
	echo do_shortcode(
		'[trx_sc_anchor id="front_page_section_googlemap"'
									. ( ! empty( $equadio_anchor_icon ) ? ' icon="' . esc_attr( $equadio_anchor_icon ) . '"' : '' )
									. ( ! empty( $equadio_anchor_text ) ? ' title="' . esc_attr( $equadio_anchor_text ) . '"' : '' )
									. ']'
	);
}
?>
	<div class="front_page_section_inner front_page_section_googlemap_inner
		<?php
		$equadio_layout = equadio_get_theme_option( 'front_page_googlemap_layout' );
		echo ' front_page_section_layout_' . esc_attr( $equadio_layout );
		if ( equadio_get_theme_option( 'front_page_googlemap_fullheight' ) ) {
			echo ' equadio-full-height sc_layouts_flex sc_layouts_columns_middle';
		}
		?>
		"
			<?php
			$equadio_css      = '';
			$equadio_bg_mask  = equadio_get_theme_option( 'front_page_googlemap_bg_mask' );
			$equadio_bg_color_type = equadio_get_theme_option( 'front_page_googlemap_bg_color_type' );
			if ( 'custom' == $equadio_bg_color_type ) {
				$equadio_bg_color = equadio_get_theme_option( 'front_page_googlemap_bg_color' );
			} elseif ( 'scheme_bg_color' == $equadio_bg_color_type ) {
				$equadio_bg_color = equadio_get_scheme_color( 'bg_color', $equadio_scheme );
			} else {
				$equadio_bg_color = '';
			}
			if ( ! empty( $equadio_bg_color ) && $equadio_bg_mask > 0 ) {
				$equadio_css .= 'background-color: ' . esc_attr(
					1 == $equadio_bg_mask ? $equadio_bg_color : equadio_hex2rgba( $equadio_bg_color, $equadio_bg_mask )
				) . ';';
			}
			if ( ! empty( $equadio_css ) ) {
				echo ' style="' . esc_attr( $equadio_css ) . '"';
			}
			?>
	>
		<div class="front_page_section_content_wrap front_page_section_googlemap_content_wrap
		<?php
		if ( 'fullwidth' != $equadio_layout ) {
			echo ' content_wrap';
		}
		?>
		">
			<?php
			// Content wrap with title and description
			$equadio_caption     = equadio_get_theme_option( 'front_page_googlemap_caption' );
			$equadio_description = equadio_get_theme_option( 'front_page_googlemap_description' );
			if ( ! empty( $equadio_caption ) || ! empty( $equadio_description ) || ( current_user_can( 'edit_theme_options' ) && is_customize_preview() ) ) {
				if ( 'fullwidth' == $equadio_layout ) {
					?>
					<div class="content_wrap">
					<?php
				}
					// Caption
				if ( ! empty( $equadio_caption ) || ( current_user_can( 'edit_theme_options' ) && is_customize_preview() ) ) {
					?>
					<h2 class="front_page_section_caption front_page_section_googlemap_caption front_page_block_<?php echo ! empty( $equadio_caption ) ? 'filled' : 'empty'; ?>">
					<?php
					echo wp_kses( $equadio_caption, 'equadio_kses_content' );
					?>
					</h2>
					<?php
				}

					// Description (text)
				if ( ! empty( $equadio_description ) || ( current_user_can( 'edit_theme_options' ) && is_customize_preview() ) ) {
					?>
					<div class="front_page_section_description front_page_section_googlemap_description front_page_block_<?php echo ! empty( $equadio_description ) ? 'filled' : 'empty'; ?>">
					<?php
					echo wp_kses( wpautop( $equadio_description ), 'equadio_kses_content' );
					?>
					</div>
					<?php
				}
				if ( 'fullwidth' == $equadio_layout ) {
					?>
					</div>
					<?php
				}
			}

			// Content (text)
			$equadio_content = equadio_get_theme_option( 'front_page_googlemap_content' );
			if ( ! empty( $equadio_content ) || ( current_user_can( 'edit_theme_options' ) && is_customize_preview() ) ) {
				if ( 'columns' == $equadio_layout ) {
					?>
					<div class="front_page_section_columns front_page_section_googlemap_columns columns_wrap">
						<div class="column-1_3">
					<?php
				} elseif ( 'fullwidth' == $equadio_layout ) {
					?>
					<div class="content_wrap">
					<?php
				}

				?>
				<div class="front_page_section_content front_page_section_googlemap_content front_page_block_<?php echo ! empty( $equadio_content ) ? 'filled' : 'empty'; ?>">
				<?php
					echo wp_kses( $equadio_content, 'equadio_kses_content' );
				?>
				</div>
				<?php

				if ( 'columns' == $equadio_layout ) {
					?>
					</div><div class="column-2_3">
					<?php
				} elseif ( 'fullwidth' == $equadio_layout ) {
					?>
					</div>
					<?php
				}
			}

			// Widgets output
			?>
			<div class="front_page_section_output front_page_section_googlemap_output">
				<?php
				if ( is_active_sidebar( 'front_page_googlemap_widgets' ) ) {
					dynamic_sidebar( 'front_page_googlemap_widgets' );
				} elseif ( current_user_can( 'edit_theme_options' ) ) {
					if ( ! equadio_exists_trx_addons() ) {
						equadio_customizer_need_trx_addons_message();
					} else {
						equadio_customizer_need_widgets_message( 'front_page_googlemap_caption', 'ThemeREX Addons - Google map' );
					}
				}
				?>
			</div>
			<?php

			if ( 'columns' == $equadio_layout && ( ! empty( $equadio_content ) || ( current_user_can( 'edit_theme_options' ) && is_customize_preview() ) ) ) {
				?>
				</div></div>
				<?php
			}
			?>
		</div>
	</div>
</div>
