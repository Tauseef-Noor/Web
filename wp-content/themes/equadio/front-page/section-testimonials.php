<div class="front_page_section front_page_section_testimonials<?php
	$equadio_scheme = equadio_get_theme_option( 'front_page_testimonials_scheme' );
	if ( ! empty( $equadio_scheme ) && ! equadio_is_inherit( $equadio_scheme ) ) {
		echo ' scheme_' . esc_attr( $equadio_scheme );
	}
	echo ' front_page_section_paddings_' . esc_attr( equadio_get_theme_option( 'front_page_testimonials_paddings' ) );
	if ( equadio_get_theme_option( 'front_page_testimonials_stack' ) ) {
		echo ' sc_stack_section_on';
	}
?>"
		<?php
		$equadio_css      = '';
		$equadio_bg_image = equadio_get_theme_option( 'front_page_testimonials_bg_image' );
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
	$equadio_anchor_icon = equadio_get_theme_option( 'front_page_testimonials_anchor_icon' );
	$equadio_anchor_text = equadio_get_theme_option( 'front_page_testimonials_anchor_text' );
if ( ( ! empty( $equadio_anchor_icon ) || ! empty( $equadio_anchor_text ) ) && shortcode_exists( 'trx_sc_anchor' ) ) {
	echo do_shortcode(
		'[trx_sc_anchor id="front_page_section_testimonials"'
									. ( ! empty( $equadio_anchor_icon ) ? ' icon="' . esc_attr( $equadio_anchor_icon ) . '"' : '' )
									. ( ! empty( $equadio_anchor_text ) ? ' title="' . esc_attr( $equadio_anchor_text ) . '"' : '' )
									. ']'
	);
}
?>
	<div class="front_page_section_inner front_page_section_testimonials_inner
	<?php
	if ( equadio_get_theme_option( 'front_page_testimonials_fullheight' ) ) {
		echo ' equadio-full-height sc_layouts_flex sc_layouts_columns_middle';
	}
	?>
			"
			<?php
			$equadio_css      = '';
			$equadio_bg_mask  = equadio_get_theme_option( 'front_page_testimonials_bg_mask' );
			$equadio_bg_color_type = equadio_get_theme_option( 'front_page_testimonials_bg_color_type' );
			if ( 'custom' == $equadio_bg_color_type ) {
				$equadio_bg_color = equadio_get_theme_option( 'front_page_testimonials_bg_color' );
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
		<div class="front_page_section_content_wrap front_page_section_testimonials_content_wrap content_wrap">
			<?php
			// Caption
			$equadio_caption = equadio_get_theme_option( 'front_page_testimonials_caption' );
			if ( ! empty( $equadio_caption ) || ( current_user_can( 'edit_theme_options' ) && is_customize_preview() ) ) {
				?>
				<h2 class="front_page_section_caption front_page_section_testimonials_caption front_page_block_<?php echo ! empty( $equadio_caption ) ? 'filled' : 'empty'; ?>"><?php echo wp_kses( $equadio_caption, 'equadio_kses_content' ); ?></h2>
				<?php
			}

			// Description (text)
			$equadio_description = equadio_get_theme_option( 'front_page_testimonials_description' );
			if ( ! empty( $equadio_description ) || ( current_user_can( 'edit_theme_options' ) && is_customize_preview() ) ) {
				?>
				<div class="front_page_section_description front_page_section_testimonials_description front_page_block_<?php echo ! empty( $equadio_description ) ? 'filled' : 'empty'; ?>"><?php echo wp_kses( wpautop( $equadio_description ), 'equadio_kses_content' ); ?></div>
				<?php
			}

			// Content (widgets)
			?>
			<div class="front_page_section_output front_page_section_testimonials_output">
				<?php
				if ( is_active_sidebar( 'front_page_testimonials_widgets' ) ) {
					dynamic_sidebar( 'front_page_testimonials_widgets' );
				} elseif ( current_user_can( 'edit_theme_options' ) ) {
					if ( ! equadio_exists_trx_addons() ) {
						equadio_customizer_need_trx_addons_message();
					} else {
						equadio_customizer_need_widgets_message( 'front_page_testimonials_caption', 'ThemeREX Addons - Testimonials' );
					}
				}
				?>
			</div>
		</div>
	</div>
</div>
