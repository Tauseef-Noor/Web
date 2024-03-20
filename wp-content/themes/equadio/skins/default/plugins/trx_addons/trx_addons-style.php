<?php
// Add plugin-specific vars to the custom CSS
if ( ! function_exists( 'equadio_trx_addons_add_theme_vars' ) ) {
	add_filter( 'equadio_filter_add_theme_vars', 'equadio_trx_addons_add_theme_vars', 10, 2 );
	function equadio_trx_addons_add_theme_vars( $rez, $vars ) {
		if ( substr( $vars['page'], 0, 2 ) != '{{' ) {
			$rez['page_1_1'] = $vars['page'] . 'px';
			$rez['page_1_2'] = ( $vars['page'] / 2 ) . 'px';
			$rez['page_1_3'] = ( $vars['page'] / 3 ) . 'px';
			$rez['page_2_3'] = ( $vars['page'] / 3 * 2 ) . 'px';
			$rez['page_1_4'] = ( $vars['page'] / 4 ) . 'px';
			$rez['page_3_4'] = ( $vars['page'] / 4 * 3 ) . 'px';
		} else {
			$rez['page_1_1'] = '{{ data.page_1_1 }}';
			$rez['page_1_2'] = '{{ data.page_1_2 }}';
			$rez['page_1_3'] = '{{ data.page_1_3 }}';
			$rez['page_2_3'] = '{{ data.page_2_3 }}';
			$rez['page_1_4'] = '{{ data.page_1_4 }}';
			$rez['page_3_4'] = '{{ data.page_3_4 }}';
		}
		return $rez;
	}
}


// Add plugin-specific colors and fonts to the custom CSS
if ( ! function_exists( 'equadio_trx_addons_get_css' ) ) {
	add_filter( 'equadio_filter_get_css', 'equadio_trx_addons_get_css', 10, 2 );
	function equadio_trx_addons_get_css( $css, $args ) {

		if ( isset( $css['fonts'] ) && isset( $args['fonts'] ) ) {
			$fonts         = $args['fonts'];
			$css['fonts'] .= <<<CSS


.sc_services_alter .sc_services_item_number,
.sc_testimonials_item_content:before,
.sc_price_item_price,
.sc_skills_counter .sc_skills_total,
.sc_skills_pie.sc_skills_compact_off .sc_skills_total,
.trx_addons_dropcap_style_1 {
	{$fonts['h2_font-family']}
}
.sc_skills_pie.sc_skills_compact_off .sc_skills_item_title,
.sc_dishes_compact .sc_services_item_title,
.sc_services_iconed .sc_services_item_title {
	{$fonts['p_font-family']}
}

.sc_testimonials_item_author_title,
.sc_testimonials_item_author_title,
.sc_tgenerator_form_field_select .sc_tgenerator_form_field_select_options .sc_tgenerator_form_field_select_option,
.sc_igenerator .sc_igenerator_message p,
.sc_item_subtitle,
.toc_menu_item .toc_menu_description,
.sc_recent_news .post_item .post_footer .post_meta .post_meta_item,
.sc_icons_item_title,
.sc_price_item_title,
.sc_courses_default .sc_courses_item_price,
.sc_courses_default .trx_addons_hover_content .trx_addons_hover_links a,
.sc_events_classic .sc_events_item_price,
.sc_events_classic .trx_addons_hover_content .trx_addons_hover_links a,
.sc_promo_modern .sc_promo_link2 span+span,
.slider_container .slide_info.slide_info_large .slide_title,
.slider_style_modern .slider_controls_label span + span,
.slider_pagination_wrap,
.sc_slider_controller_info {
	{$fonts['h5_font-family']}
}
.sc_recent_news .post_item .post_meta,
.sc_action_item_description,
.sc_price_item_description,
.sc_price_item_details,
.sc_courses_default .sc_courses_item_date,
.courses_single .courses_page_meta,
.sc_events_classic .sc_events_item_date,
.sc_promo_modern .sc_promo_link2 span,
.sc_skills_counter .sc_skills_item_title,
.slider_style_modern .slider_controls_label span,
.slider_titles_outside_wrap .slide_cats,
.slider_titles_outside_wrap .slide_subtitle,
.sc_slider_controller_item_info_date,
.sc_team .sc_team_item_subtitle,
.sc_dishes .sc_dishes_item_subtitle,
.sc_services .sc_services_item_subtitle,
.team_member_page .team_member_brief_info_text,
.sc_testimonials_item_author_title,
.sc_testimonials_item_content:before {
	{$fonts['info_font-family']}
}
.slider_outer_wrap .sc_slider_controller .sc_slider_controller_item_info_date {
	{$fonts['info_font-size']}
	{$fonts['info_font-weight']}
	{$fonts['info_font-style']}
	{$fonts['info_line-height']}
	{$fonts['info_text-decoration']}
	{$fonts['info_text-transform']}
	{$fonts['info_letter-spacing']}	
}
.sc_button,
.sc_button.sc_button_simple,
.sc_form button {
	{$fonts['button_font-family']}
	{$fonts['button_font-size']}
	{$fonts['button_font-weight']}
	{$fonts['button_font-style']}
	{$fonts['button_line-height']}
	{$fonts['button_text-decoration']}
	{$fonts['button_text-transform']}
	{$fonts['button_letter-spacing']}
}
.sc_promo_modern .sc_promo_link2 {
	{$fonts['button_font-family']}
}

CSS;
		}

		return $css;
	}
}
