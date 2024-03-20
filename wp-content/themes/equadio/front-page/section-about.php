<div class="front_page_section front_page_section_about<?php
	$equadio_scheme = equadio_get_theme_option( 'front_page_about_scheme' );
	if ( ! empty( $equadio_scheme ) && ! equadio_is_inherit( $equadio_scheme ) ) {
		echo ' scheme_' . esc_attr( $equadio_scheme );
	}
	echo ' front_page_section_paddings_' . esc_attr( equadio_get_theme_option( 'front_page_about_paddings' ) );
	if ( equadio_get_theme_option( 'front_page_about_stack' ) ) {
		echo ' sc_stack_section_on';
	}
?>"
		<?php
		$equadio_css      = '';
		$equadio_bg_image = equadio_get_theme_option( 'front_page_about_bg_image' );
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
	$equadio_anchor_icon = equadio_get_theme_option( 'front_page_about_anchor_icon' );
	$equadio_anchor_text = equadio_get_theme_option( 'front_page_about_anchor_text' );
if ( ( ! empty( $equadio_anchor_icon ) || ! empty( $equadio_anchor_text ) ) && shortcode_exists( 'trx_sc_anchor' ) ) {
	echo do_shortcode(
		'[trx_sc_anchor id="front_page_section_about"'
									. ( ! empty( $equadio_anchor_icon ) ? ' icon="' . esc_attr( $equadio_anchor_icon ) . '"' : '' )
									. ( ! empty( $equadio_anchor_text ) ? ' title="' . esc_attr( $equadio_anchor_text ) . '"' : '' )
									. ']'
	);
}
?>
	<div class="front_page_section_inner front_page_section_about_inner
	<?php
	if ( equadio_get_theme_option( 'front_page_about_fullheight' ) ) {
		echo ' equadio-full-height sc_layouts_flex sc_layouts_columns_middle';
	}
	?>
			"
			<?php
			$equadio_css           = '';
			$equadio_bg_mask       = equadio_get_theme_option( 'front_page_about_bg_mask' );
			$equadio_bg_color_type = equadio_get_theme_option( 'front_page_about_bg_color_type' );
			if ( 'custom' == $equadio_bg_color_type ) {
				$equadio_bg_color = equadio_get_theme_option( 'front_page_about_bg_color' );
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
		<div class="front_page_section_content_wrap front_page_section_about_content_wrap content_wrap">
			<?php
			// Caption
			$equadio_caption = equadio_get_theme_option( 'front_page_about_caption' );
			if ( ! empty( $equadio_caption ) || ( current_user_can( 'edit_theme_options' ) && is_customize_preview() ) ) {
				?>
				<h2 class="front_page_section_caption front_page_section_about_caption front_page_block_<?php echo ! empty( $equadio_caption ) ? 'filled' : 'empty'; ?>"><?php echo wp_kses( $equadio_caption, 'equadio_kses_content' ); ?></h2>
				<?php
			}

			// Description (text)
			$equadio_description = equadio_get_theme_option( 'front_page_about_description' );
			if ( ! empty( $equadio_description ) || ( current_user_can( 'edit_theme_options' ) && is_customize_preview() ) ) {
				?>
				<div class="front_page_section_description front_page_section_about_description front_page_block_<?php echo ! empty( $equadio_description ) ? 'filled' : 'empty'; ?>"><?php echo wp_kses( wpautop( $equadio_description ), 'equadio_kses_content' ); ?></div>
				<?php
			}

			// Content
			$equadio_content = equadio_get_theme_option( 'front_page_about_content' );
			if ( ! empty( $equadio_content ) || ( current_user_can( 'edit_theme_options' ) && is_customize_preview() ) ) {
				?>
				<div class="front_page_section_content front_page_section_about_content front_page_block_<?php echo ! empty( $equadio_content ) ? 'filled' : 'empty'; ?>">
					<?php
					$equadio_page_content_mask = '%%CONTENT%%';
					if ( strpos( $equadio_content, $equadio_page_content_mask ) !== false ) {
						$equadio_content = preg_replace(
							'/(\<p\>\s*)?' . $equadio_page_content_mask . '(\s*\<\/p\>)/i',
							sprintf(
								'<div class="front_page_section_about_source">%s</div>',
								apply_filters( 'the_content', get_the_content() )
							),
							$equadio_content
						);
					}
					equadio_show_layout( $equadio_content );
					?>
				</div>
				<?php
			}
			?>
		</div>
	</div>
</div>
