<?php
/**
 * Skin Options
 *
 * @package EQUADIO
 * @since EQUADIO 1.76.0
 */


// Theme init priorities:
// Action 'after_setup_theme'
// 1 - register filters to add/remove lists items in the Theme Options
// 2 - create Theme Options
// 3 - add/remove Theme Options elements
// 5 - load Theme Options. Attention! After this step you can use only basic options (not overriden)
// 9 - register other filters (for installer, etc.)
//10 - standard Theme init procedures (not ordered)
// Action 'wp_loaded'
// 1 - detect override mode. Attention! Only after this step you can use overriden options (separate values for the shop, courses, etc.)

if ( ! function_exists( 'equadio_create_theme_options' ) ) {

	function equadio_create_theme_options() {

		// Message about options override.
		// Attention! Not need esc_html() here, because this message put in wp_kses_data() below
		$msg_override = esc_html__( 'Attention! Some of these options can be overridden in the following sections (Blog, Plugins settings, etc.) or in the settings of individual pages. If you changed such parameter and nothing happened on the page, this option may be overridden in the corresponding section or in the Page Options of this page. These options are marked with an asterisk (*) in the title.', 'equadio' );

		// Color schemes number: if < 2 - hide fields with selectors
		$hide_schemes = count( equadio_storage_get( 'schemes' ) ) < 2;

		equadio_storage_set(

			'options', array(

				// 'Logo & Site Identity'
				//---------------------------------------------
				'title_tagline'                 => array(
					'title'    => esc_html__( 'Logo & Site Identity', 'equadio' ),
					'desc'     => '',
					'priority' => 10,
					'icon'     => 'icon-home-2',
					'type'     => 'section',
				),
				'logo_info'                     => array(
					'title'    => esc_html__( 'Logo Settings', 'equadio' ),
					'desc'     => '',
					'priority' => 20,
					'qsetup'   => esc_html__( 'General', 'equadio' ),
					'type'     => 'info',
				),
				'logo_text'                     => array(
					'title'    => esc_html__( 'Use Site Name as Logo', 'equadio' ),
					'desc'     => wp_kses_data( __( 'Use the site title and tagline as a text logo if no image is selected', 'equadio' ) ),
					'priority' => 30,
					'std'      => 1,
					'qsetup'   => esc_html__( 'General', 'equadio' ),
					'type'     => EQUADIO_THEME_FREE ? 'hidden' : 'switch',
				),
				'logo_zoom'                     => array(
					'title'      => esc_html__( 'Logo zoom', 'equadio' ),
					'desc'       => wp_kses_data( __( 'Zoom the logo (set 1 to leave original size). For this parameter to affect images, their max-height should be specified in "em" instead of "px" during header creation. In this case, maximum logo size depends on the actual size of the picture.', 'equadio' ) ),
					'std'        => 1,
					'min'        => 0.2,
					'max'        => 2,
					'step'       => 0.1,
					'refresh'    => false,
					'show_value' => true,
					'type'       => EQUADIO_THEME_FREE ? 'hidden' : 'slider',
				),
				'logo_retina_enabled'           => array(
					'title'    => esc_html__( 'Allow retina display logo', 'equadio' ),
					'desc'     => wp_kses_data( __( 'Show fields to select logo images for Retina display', 'equadio' ) ),
					'priority' => 40,
					'refresh'  => false,
					'std'      => 0,
					'type'     => EQUADIO_THEME_FREE ? 'hidden' : 'switch',
				),
				// Parameter 'logo' was replaced with standard WordPress 'custom_logo'
				'logo_retina'                   => array(
					'title'      => esc_html__( 'Logo for Retina', 'equadio' ),
					'desc'       => wp_kses_data( __( 'Select or upload site logo used on Retina displays (if empty - use default logo from the field above)', 'equadio' ) ),
					'priority'   => 70,
					'dependency' => array(
						'logo_retina_enabled' => array( 1 ),
					),
					'std'        => '',
					'type'       => EQUADIO_THEME_FREE ? 'hidden' : 'image',
				),
				'logo_mobile_header'            => array(
					'title' => esc_html__( 'Logo for the mobile header', 'equadio' ),
					'desc'  => wp_kses_data( __( 'Select or upload site logo to display it in the mobile header (if enabled in the section "Header - Header mobile"', 'equadio' ) ),
					'std'   => '',
					'type'  => 'image',
				),
				'logo_mobile_header_retina'     => array(
					'title'      => esc_html__( 'Logo for the mobile header on Retina', 'equadio' ),
					'desc'       => wp_kses_data( __( 'Select or upload site logo used on Retina displays (if empty - use default logo from the field above)', 'equadio' ) ),
					'dependency' => array(
						'logo_retina_enabled' => array( 1 ),
					),
					'std'        => '',
					'type'       => EQUADIO_THEME_FREE ? 'hidden' : 'image',
				),
				'logo_mobile'                   => array(
					'title' => esc_html__( 'Logo for the mobile menu', 'equadio' ),
					'desc'  => wp_kses_data( __( 'Select or upload site logo to display it in the mobile menu', 'equadio' ) ),
					'std'   => '',
					'type'  => 'image',
				),
				'logo_mobile_retina'            => array(
					'title'      => esc_html__( 'Logo mobile on Retina', 'equadio' ),
					'desc'       => wp_kses_data( __( 'Select or upload site logo used on Retina displays (if empty - use default logo from the field above)', 'equadio' ) ),
					'dependency' => array(
						'logo_retina_enabled' => array( 1 ),
					),
					'std'        => '',
					'type'       => EQUADIO_THEME_FREE ? 'hidden' : 'image',
				),
				'logo_side'                     => array(
					'title' => esc_html__( 'Logo for the side menu', 'equadio' ),
					'desc'  => wp_kses_data( __( 'Select or upload site logo (with vertical orientation) to display it in the side menu', 'equadio' ) ),
					'std'   => '',
					'type'  => 'hidden',
				),
				'logo_side_retina'              => array(
					'title'      => esc_html__( 'Logo for the side menu on Retina', 'equadio' ),
					'desc'       => wp_kses_data( __( 'Select or upload site logo (with vertical orientation) to display it in the side menu on Retina displays (if empty - use default logo from the field above)', 'equadio' ) ),
					'dependency' => array(
						'logo_retina_enabled' => array( 1 ),
					),
					'std'        => '',
					'type'       => 'hidden',
				),



				// 'General settings'
				//---------------------------------------------
				'general'                       => array(
					'title'    => esc_html__( 'General', 'equadio' ),
					'desc'     => wp_kses_data( $msg_override ),
					'priority' => 20,
					'icon'     => 'icon-settings',
					'type'     => 'section',
				),

				'general_layout_info'           => array(
					'title'  => esc_html__( 'Layout', 'equadio' ),
					'desc'   => '',
					'qsetup' => esc_html__( 'General', 'equadio' ),
					'type'   => 'info',
				),
				'body_style'                    => array(
					'title'    => esc_html__( 'Body style', 'equadio' ),
					'desc'     => wp_kses_data( __( 'Select width of the body content', 'equadio' ) ),
					'override' => array(
						'mode'    => 'page,cpt_team,cpt_services,cpt_dishes,cpt_competitions,cpt_rounds,cpt_matches,cpt_cars,cpt_properties,cpt_courses,cpt_portfolio',
						'section' => esc_html__( 'Content', 'equadio' ),
					),
					'qsetup'   => esc_html__( 'General', 'equadio' ),
					'refresh'  => false,
					'std'      => 'wide',
					'options'  => equadio_get_list_body_styles( false ),
					'type'     => 'choice',
				),
				'page_width'                    => array(
					'title'      => esc_html__( 'Page width', 'equadio' ),
					'desc'       => wp_kses_data( __( 'Total width of the site content and sidebar (in pixels). If empty - use default width', 'equadio' ) ),
					'dependency' => array(
						'body_style' => array( 'boxed', 'wide' ),
					),
					'std'        => equadio_theme_defaults( 'page' ),
					'min'        => 1000,
					'max'        => 1600,
					'step'       => 10,
					'show_value' => true,
					'units'      => 'px',
					'refresh'    => false,
					'customizer' => 'page',               // SASS variable's name to preview changes 'on fly'
					'type'       => EQUADIO_THEME_FREE ? 'hidden' : 'slider',
				),
				'page_boxed_extra'             => array(
					'title'      => esc_html__( 'Boxed page extra spaces', 'equadio' ),
					'desc'       => wp_kses_data( __( 'Width of the extra side space on boxed pages', 'equadio' ) ),
					'dependency' => array(
						'body_style' => array( 'boxed' ),
					),
					'std'        => equadio_theme_defaults( 'page_boxed_extra' ),
					'min'        => 0,
					'max'        => 150,
					'step'       => 10,
					'show_value' => true,
					'units'      => 'px',
					'refresh'    => false,
					'customizer' => 'page_boxed_extra',   // SASS variable's name to preview changes 'on fly'
					'type'       => EQUADIO_THEME_FREE ? 'hidden' : 'slider',
				),
				'boxed_bg_image'                => array(
					'title'      => esc_html__( 'Boxed bg image', 'equadio' ),
					'desc'       => wp_kses_data( __( 'Select or upload image for the background of the boxed content.', 'equadio' ) ),
					'dependency' => array(
						'body_style' => array( 'boxed' ),
					),
					'override'   => array(
						'mode'    => 'page,cpt_team,cpt_services,cpt_dishes,cpt_competitions,cpt_rounds,cpt_matches,cpt_cars,cpt_properties,cpt_courses,cpt_portfolio',
						'section' => esc_html__( 'Content', 'equadio' ),
					),
					'std'        => '',
					'qsetup'     => esc_html__( 'General', 'equadio' ),
					'type'       => 'image',
				),
				'remove_margins'                => array(
					'title'    => esc_html__( 'Page margins', 'equadio' ),
					'desc'     => wp_kses_data( __( 'Add margins above and below the content area', 'equadio' ) ),
					'override' => array(
						'mode'    => 'page,cpt_team,cpt_services,cpt_dishes,cpt_competitions,cpt_rounds,cpt_matches,cpt_cars,cpt_properties,cpt_courses,cpt_portfolio',
						'section' => esc_html__( 'Content', 'equadio' ),
					),
					'refresh'  => false,
					'std'      => 0,
					'options'  => equadio_get_list_remove_margins(),
					'type'     => 'choice',
				),

				'general_menu_info'             => array(
					'title' => esc_html__( 'Navigation', 'equadio' ),
					'desc'  => '',
					'type'  => EQUADIO_THEME_FREE ? 'hidden' : 'info',
				),
				'menu_side'                     => array(
					'title'    => esc_html__( 'Sidemenu position', 'equadio' ),
					'desc'     => wp_kses_data( __( 'Select position of the side menu - panel with icons (ancors) for inner-page navigation. Use this menu if shortcodes "Ancor" are present on the page.', 'equadio' ) ),
					'override' => array(
						'mode'    => 'page,cpt_team,cpt_services,cpt_dishes,cpt_competitions,cpt_rounds,cpt_matches,cpt_cars,cpt_properties,cpt_courses,cpt_portfolio',
						'section' => esc_html__( 'Content', 'equadio' ),
					),
					'std'      => 'none',
					'options'  => array(
						'hide'  => array(
										'title' => esc_html__( 'No menu', 'equadio' ),
										'icon'  => 'images/theme-options/menu-side/hide.png',
									),
						'left'  => array(
										'title' => esc_html__( 'Left menu', 'equadio' ),
										'icon'  => 'images/theme-options/menu-side/left.png',
									),
						'right' => array(
										'title' => esc_html__( 'Right menu', 'equadio' ),
										'icon'  => 'images/theme-options/menu-side/right.png',
									),
					),
					'type'     => 'hidden',
				),
				'menu_side_icons'               => array(
					'title'      => esc_html__( 'Iconed sidemenu', 'equadio' ),
					'desc'       => wp_kses_data( __( 'Get icons from anchors and display them in the sidemenu, or mark sidemenu items with simple dots', 'equadio' ) ),
					'override'   => array(
						'mode'    => 'page,cpt_team,cpt_services,cpt_dishes,cpt_competitions,cpt_rounds,cpt_matches,cpt_cars,cpt_properties,cpt_courses,cpt_portfolio',
						'section' => esc_html__( 'Content', 'equadio' ),
					),
					'dependency' => array(
						'menu_side' => array( 'left', 'right' ),
					),
					'std'        => 1,
					'type'       => 'hidden',
				),
				'menu_side_stretch'             => array(
					'title'      => esc_html__( 'Stretch sidemenu', 'equadio' ),
					'desc'       => wp_kses_data( __( 'Stretch sidemenu to window height (if menu items number >= 5)', 'equadio' ) ),
					'override'   => array(
						'mode'    => 'page,cpt_team,cpt_services,cpt_dishes,cpt_competitions,cpt_rounds,cpt_matches,cpt_cars,cpt_properties,cpt_courses,cpt_portfolio',
						'section' => esc_html__( 'Content', 'equadio' ),
					),
					'dependency' => array(
						'menu_side' => array( 'left', 'right' ),
						'menu_side_icons' => array( 1 )
					),
					'std'        => 0,
					'type'       => 'hidden',
				),
				'menu_mobile_fullscreen'        => array(
					'title' => esc_html__( 'Mobile menu fullscreen', 'equadio' ),
					'desc'  => wp_kses_data( __( 'Display mobile menu on full screen', 'equadio' ) ),
					'std'   => 1,
					'type'  => EQUADIO_THEME_FREE ? 'hidden' : 'switch',
				),

				'general_sidebar_info'          => array(
					'title' => esc_html__( 'Sidebar', 'equadio' ),
					'desc'  => '',
					'type'  => 'info',
				),
				'sidebar_position'              => array(
					'title'    => esc_html__( 'Sidebar position', 'equadio' ),
					'desc'     => wp_kses_data( __( 'Select position to show sidebar', 'equadio' ) ),
					'override' => array(
						'mode'    => 'page',		// Override parameters for single posts moved to the 'sidebar_position_single'
						'section' => esc_html__( 'Content', 'equadio' ),
					),
					'std'      => 'right',
					'qsetup'   => esc_html__( 'General', 'equadio' ),
					'options'  => array(),
					'type'     => 'choice',
				),
				'sidebar_position_ss'       => array(
					'title'    => esc_html__( 'Sidebar position on the small screen', 'equadio' ),
					'desc'     => wp_kses_data( __( "Select position to move sidebar (if it's not hidden) on the small screen - above or below the content", 'equadio' ) ),
					'override' => array(
						'mode'    => 'page',		// Override parameters for single posts moved to the 'sidebar_position_ss_single'
						'section' => esc_html__( 'Content', 'equadio' ),
					),
					'dependency' => array(
						'sidebar_position' => array( '^hide' ),
					),
					'std'      => 'below',
					'qsetup'   => esc_html__( 'General', 'equadio' ),
					'options'  => array(),
					'type'     => 'radio',
				),
				'sidebar_type'              => array(
					'title'    => esc_html__( 'Sidebar style', 'equadio' ),
					'desc'     => wp_kses_data( __( 'Choose whether to use the default sidebar or sidebar Layouts (available only if the ThemeREX Addons is activated)', 'equadio' ) ),
					'override'   => array(
						'mode'    => 'page',		// Override parameters for single posts moved to the 'sidebar_position_single'
						'section' => esc_html__( 'Content', 'equadio' ),
					),
					'dependency' => array(
						'sidebar_position' => array( '^hide' ),
					),
					'std'      => 'default',
					'options'  => equadio_get_list_header_footer_types(),
					'type'     => EQUADIO_THEME_FREE || ! equadio_exists_trx_addons() ? 'hidden' : 'radio',
				),
				'sidebar_style'                 => array(
					'title'      => esc_html__( 'Select custom layout', 'equadio' ),
					'desc'       => wp_kses( __( 'Select custom sidebar from Layouts Builder', 'equadio' ), 'equadio_kses_content' ),
					'override'   => array(
						'mode'    => 'page',		// Override parameters for single posts moved to the 'sidebar_position_single'
						'section' => esc_html__( 'Content', 'equadio' ),
					),
					'dependency' => array(
						'sidebar_position' => array( '^hide' ),
						'sidebar_type' => array( 'custom' ),
					),
					'std'        => 'sidebar-custom-sidebar',
					'options'    => array(),
					'type'       => 'select',
				),
				'sidebar_widgets'               => array(
					'title'      => esc_html__( 'Sidebar widgets', 'equadio' ),
					'desc'       => wp_kses_data( __( 'Select default widgets to show in the sidebar', 'equadio' ) ),
					'override'   => array(
						'mode'    => 'page',		// Override parameters for single posts moved to the 'sidebar_widgets_single'
						'section' => esc_html__( 'Content', 'equadio' ),
					),
					'dependency' => array(
						'sidebar_position' => array( '^hide' ),
						'sidebar_type'     => array( 'default')
					),
					'std'        => 'sidebar_widgets',
					'options'    => array(),
					'qsetup'     => esc_html__( 'General', 'equadio' ),
					'type'       => 'select',
				),
				'sidebar_width'                 => array(
					'title'      => esc_html__( 'Sidebar width', 'equadio' ),
					'desc'       => wp_kses_data( __( 'Width of the sidebar (in pixels). If empty - use default width', 'equadio' ) ),
					'std'        => equadio_theme_defaults( 'sidebar' ),
					'min'        => 150,
					'max'        => 500,
					'step'       => 10,
					'show_value' => true,
					'units'      => 'px',
					'refresh'    => false,
					'customizer' => 'sidebar',      // SASS variable's name to preview changes 'on fly'
					'type'       => EQUADIO_THEME_FREE ? 'hidden' : 'slider',
				),
				'sidebar_gap'                   => array(
					'title'      => esc_html__( 'Sidebar gap', 'equadio' ),
					'desc'       => wp_kses_data( __( 'Gap between content and sidebar (in pixels). If empty - use default gap', 'equadio' ) ),
					'std'        => equadio_theme_defaults( 'sidebar_gap' ),
					'min'        => 0,
					'max'        => 100,
					'step'       => 1,
					'show_value' => true,
					'units'      => 'px',
					'refresh'    => false,
					'customizer' => 'sidebar_gap',  // SASS variable's name to preview changes 'on fly'
					'type'       => EQUADIO_THEME_FREE ? 'hidden' : 'slider',
				),
				'expand_content'                => array(
					'title'   => esc_html__( 'Content width', 'equadio' ),
					'desc'    => wp_kses_data( __( 'Content width if the sidebar is hidden', 'equadio' ) ),
					'refresh' => false,
					'override'   => array(
						'mode'    => 'page',		// Override parameters for single posts moved to the 'expand_content_single'
						'section' => esc_html__( 'Content', 'equadio' ),
					),
					'options' => equadio_get_list_expand_content(),
					'std'     => 'expand',
					'type'    => 'choice',
				),

				'general_widgets_info'          => array(
					'title' => esc_html__( 'Additional widgets', 'equadio' ),
					'desc'  => '',
					'type'  => EQUADIO_THEME_FREE ? 'hidden' : 'info',
				),
				'widgets_above_page'            => array(
					'title'    => esc_html__( 'Widgets at the top of the page', 'equadio' ),
					'desc'     => wp_kses_data( __( 'Select widgets to show at the top of the page (above content and sidebar)', 'equadio' ) ),
					'override' => array(
						'mode'    => 'page,cpt_team,cpt_services,cpt_dishes,cpt_competitions,cpt_rounds,cpt_matches,cpt_cars,cpt_properties,cpt_courses,cpt_portfolio',
						'section' => esc_html__( 'Widgets', 'equadio' ),
					),
					'std'      => 'hide',
					'options'  => array(),
					'type'     => EQUADIO_THEME_FREE ? 'hidden' : 'select',
				),
				'widgets_above_content'         => array(
					'title'    => esc_html__( 'Widgets above the content', 'equadio' ),
					'desc'     => wp_kses_data( __( 'Select widgets to show at the beginning of the content area', 'equadio' ) ),
					'override' => array(
						'mode'    => 'page,cpt_team,cpt_services,cpt_dishes,cpt_competitions,cpt_rounds,cpt_matches,cpt_cars,cpt_properties,cpt_courses,cpt_portfolio',
						'section' => esc_html__( 'Widgets', 'equadio' ),
					),
					'std'      => 'hide',
					'options'  => array(),
					'type'     => EQUADIO_THEME_FREE ? 'hidden' : 'select',
				),
				'widgets_below_content'         => array(
					'title'    => esc_html__( 'Widgets below the content', 'equadio' ),
					'desc'     => wp_kses_data( __( 'Select widgets to show at the ending of the content area', 'equadio' ) ),
					'override' => array(
						'mode'    => 'page,cpt_team,cpt_services,cpt_dishes,cpt_competitions,cpt_rounds,cpt_matches,cpt_cars,cpt_properties,cpt_courses,cpt_portfolio',
						'section' => esc_html__( 'Widgets', 'equadio' ),
					),
					'std'      => 'hide',
					'options'  => array(),
					'type'     => EQUADIO_THEME_FREE ? 'hidden' : 'select',
				),
				'widgets_below_page'            => array(
					'title'    => esc_html__( 'Widgets at the bottom of the page', 'equadio' ),
					'desc'     => wp_kses_data( __( 'Select widgets to show at the bottom of the page (below content and sidebar)', 'equadio' ) ),
					'override' => array(
						'mode'    => 'page,cpt_team,cpt_services,cpt_dishes,cpt_competitions,cpt_rounds,cpt_matches,cpt_cars,cpt_properties,cpt_courses,cpt_portfolio',
						'section' => esc_html__( 'Widgets', 'equadio' ),
					),
					'std'      => 'hide',
					'options'  => array(),
					'type'     => EQUADIO_THEME_FREE ? 'hidden' : 'select',
				),

				'general_effects_info'          => array(
					'title' => esc_html__( 'Design & Effects', 'equadio' ),
					'desc'  => '',
					'type'  => 'info',
				),
				'border_radius'                 => array(
					'title'      => esc_html__( 'Border radius', 'equadio' ),
					'desc'       => wp_kses_data( __( 'Specify the border radius of the form fields and buttons in pixels', 'equadio' ) ),
					'std'        => equadio_theme_defaults( 'rad' ),
					'min'        => 0,
					'max'        => 20,
					'step'       => 1,
					'show_value' => true,
					'units'      => 'px',
					'refresh'    => false,
					'customizer' => 'rad',      // SASS name to preview changes 'on fly'
					'type'       => 'hidden',
				),

				'general_misc_info'             => array(
					'title' => esc_html__( 'Miscellaneous', 'equadio' ),
					'desc'  => '',
					'type'  => EQUADIO_THEME_FREE ? 'hidden' : 'info',
				),
				'seo_snippets'                  => array(
					'title' => esc_html__( 'SEO snippets', 'equadio' ),
					'desc'  => wp_kses_data( __( 'Add structured data markup to the single posts and pages', 'equadio' ) ),
					'std'   => 0,
					'type'  => EQUADIO_THEME_FREE ? 'hidden' : 'switch',
				),
				'privacy_text' => array(
					"title" => esc_html__("Text with Privacy Policy link", 'equadio'),
					"desc"  => wp_kses_data( __("Specify text with Privacy Policy link for the checkbox 'I agree ...'", 'equadio') ),
					"std"   => wp_kses( __( 'I agree that my submitted data is being collected and stored.', 'equadio'), 'equadio_kses_content' ),
					"type"  => "textarea"
				),



				// 'Header'
				//---------------------------------------------
				'header'                        => array(
					'title'    => esc_html__( 'Header', 'equadio' ),
					'desc'     => wp_kses_data( $msg_override ),
					'priority' => 30,
					'icon'     => 'icon-header',
					'type'     => 'section',
				),

				'header_style_info'             => array(
					'title' => esc_html__( 'Header style', 'equadio' ),
					'desc'  => '',
					'type'  => 'info',
				),
				'header_type'                   => array(
					'title'    => esc_html__( 'Header style', 'equadio' ),
					'desc'     => wp_kses_data( __( 'Choose whether to use the default header or header Layouts (available only if the ThemeREX Addons is activated)', 'equadio' ) ),
					'override' => array(
						'mode'    => 'page,post,product,cpt_team,cpt_services,cpt_dishes,cpt_competitions,cpt_rounds,cpt_matches,cpt_cars,cpt_properties,cpt_courses,cpt_portfolio',
						'section' => esc_html__( 'Header', 'equadio' ),
					),
					'std'      => 'default',
					'options'  => equadio_get_list_header_footer_types(),
					'type'     => EQUADIO_THEME_FREE || ! equadio_exists_trx_addons() ? 'hidden' : 'radio',
				),
				'header_style'                  => array(
					'title'      => esc_html__( 'Select custom layout', 'equadio' ),
					'desc'       => wp_kses( __( 'Select custom header from Layouts Builder', 'equadio' ), 'equadio_kses_content' ),
					'override'   => array(
						'mode'    => 'page,post,product,cpt_team,cpt_services,cpt_dishes,cpt_competitions,cpt_rounds,cpt_matches,cpt_cars,cpt_properties,cpt_courses,cpt_portfolio',
						'section' => esc_html__( 'Header', 'equadio' ),
					),
					'dependency' => array(
						'header_type' => array( 'custom' ),
					),
					'std'        => 'header-custom-elementor-header-default',
					'options'    => array(),
					'type'       => 'select',
				),
				'header_position'               => array(
					'title'    => esc_html__( 'Header position', 'equadio' ),
					'desc'     => wp_kses_data( __( 'Select site header position', 'equadio' ) ),
					'override' => array(
						'mode'    => 'page,post,product,cpt_team,cpt_services,cpt_dishes,cpt_competitions,cpt_rounds,cpt_matches,cpt_cars,cpt_properties,cpt_courses,cpt_portfolio',
						'section' => esc_html__( 'Header', 'equadio' ),
					),
					'std'      => 'default',
					'options'  => array(),
					'type'     => EQUADIO_THEME_FREE ? 'hidden' : 'radio',
				),
				'header_fullheight'             => array(
					'title'    => esc_html__( 'Header fullheight', 'equadio' ),
					'desc'     => wp_kses_data( __( 'Stretch header area to fill the entire screen. Used only if the header has a background image', 'equadio' ) ),
					'override' => array(
						'mode'    => 'page,post,product,cpt_team,cpt_services,cpt_dishes,cpt_competitions,cpt_rounds,cpt_matches,cpt_cars,cpt_properties,cpt_courses,cpt_portfolio',
						'section' => esc_html__( 'Header', 'equadio' ),
					),
					'std'      => 0,
					'type'     => EQUADIO_THEME_FREE ? 'hidden' : 'switch',
				),
				'header_wide'                   => array(
					'title'      => esc_html__( 'Header fullwidth', 'equadio' ),
					'desc'       => wp_kses_data( __( 'Do you want to stretch the header widgets area to the entire window width?', 'equadio' ) ),
					'override'   => array(
						'mode'    => 'page,post,product,cpt_team,cpt_services,cpt_dishes,cpt_competitions,cpt_rounds,cpt_matches,cpt_cars,cpt_properties,cpt_courses,cpt_portfolio',
						'section' => esc_html__( 'Header', 'equadio' ),
					),
					'std'        => 1,
					'type'       => EQUADIO_THEME_FREE ? 'hidden' : 'switch',
				),
				'header_zoom'                   => array(
					'title'   => esc_html__( 'Header title zoom', 'equadio' ),
					'desc'    => wp_kses_data( __( 'Zoom the header title. 1 - original size', 'equadio' ) ),
					'std'     => 1,
					'min'     => 0.2,
					'max'     => 2,
					'step'    => 0.1,
					'show_value' => true,
					'refresh' => false,
					'type'    => EQUADIO_THEME_FREE ? 'hidden' : 'slider',
				),

				'header_widgets_info'           => array(
					'title' => esc_html__( 'Header widgets', 'equadio' ),
					'desc'  => wp_kses_data( __( 'Here you can place a widget slider, advertising banners, etc.', 'equadio' ) ),
					'type'  => 'info',
				),
				'header_widgets'                => array(
					'title'    => esc_html__( 'Header widgets', 'equadio' ),
					'desc'     => wp_kses_data( __( 'Select set of widgets to show in the header on each page', 'equadio' ) ),
					'override' => array(
						'mode'    => 'page,cpt_team,cpt_services,cpt_dishes,cpt_competitions,cpt_rounds,cpt_matches,cpt_cars,cpt_properties,cpt_courses,cpt_portfolio',
						'section' => esc_html__( 'Header', 'equadio' ),
						'desc'    => wp_kses_data( __( 'Select set of widgets to show in the header on this page', 'equadio' ) ),
					),
					'std'      => 'hide',
					'options'  => array(),
					'type'     => 'select',
				),
				'header_columns'                => array(
					'title'      => esc_html__( 'Header columns', 'equadio' ),
					'desc'       => wp_kses_data( __( 'Select number columns to show widgets in the Header. If 0 - autodetect by the widgets count', 'equadio' ) ),
					'override'   => array(
						'mode'    => 'page,cpt_team,cpt_services,cpt_dishes,cpt_competitions,cpt_rounds,cpt_matches,cpt_cars,cpt_properties,cpt_courses,cpt_portfolio',
						'section' => esc_html__( 'Header', 'equadio' ),
					),
					'dependency' => array(
						'header_widgets' => array( '^hide' ),
					),
					'std'        => 0,
					'options'    => equadio_get_list_range( 0, 6 ),
					'type'       => 'select',
				),

				'header_image_info'             => array(
					'title' => esc_html__( 'Header image', 'equadio' ),
					'desc'  => '',
					'type'  => EQUADIO_THEME_FREE ? 'hidden' : 'info',
				),
				'header_image_override'         => array(
					'title'    => esc_html__( 'Header image override', 'equadio' ),
					'desc'     => wp_kses_data( __( "Allow overriding the header image with a featured image of the page, post, product, etc.", 'equadio' ) ),
					'override' => array(
						'mode'    => 'page',
						'section' => esc_html__( 'Header', 'equadio' ),
					),
					'std'      => 0,
					'type'     => EQUADIO_THEME_FREE ? 'hidden' : 'switch',
				),

				'header_mobile_info'            => array(
					'title'      => esc_html__( 'Mobile header', 'equadio' ),
					'desc'       => wp_kses_data( __( 'Configure the mobile version of the header', 'equadio' ) ),
					'priority'   => 500,
					'dependency' => array(
						'header_type' => array( 'default' ),
					),
					'type'       => EQUADIO_THEME_FREE ? 'hidden' : 'info',
				),
				'header_mobile_enabled'         => array(
					'title'      => esc_html__( 'Enable the mobile header', 'equadio' ),
					'desc'       => wp_kses_data( __( 'Use the mobile version of the header (if checked) or relayout the current header on mobile devices', 'equadio' ) ),
					'dependency' => array(
						'header_type' => array( 'default' ),
					),
					'std'        => 0,
					'type'       => EQUADIO_THEME_FREE ? 'hidden' : 'switch',
				),
				'header_mobile_additional_info' => array(
					'title'      => esc_html__( 'Additional info', 'equadio' ),
					'desc'       => wp_kses_data( __( 'Additional info to show at the top of the mobile header', 'equadio' ) ),
					'std'        => '',
					'dependency' => array(
						'header_type'           => array( 'default' ),
						'header_mobile_enabled' => array( 1 ),
					),
					'refresh'    => false,
					'teeny'      => false,
					'rows'       => 20,
					'type'       => EQUADIO_THEME_FREE ? 'hidden' : 'text_editor',
				),
				'header_mobile_hide_info'       => array(
					'title'      => esc_html__( 'Hide additional info', 'equadio' ),
					'std'        => 0,
					'dependency' => array(
						'header_type'           => array( 'default' ),
						'header_mobile_enabled' => array( 1 ),
					),
					'type'       => EQUADIO_THEME_FREE ? 'hidden' : 'switch',
				),
				'header_mobile_hide_logo'       => array(
					'title'      => esc_html__( 'Hide logo', 'equadio' ),
					'std'        => 0,
					'dependency' => array(
						'header_type'           => array( 'default' ),
						'header_mobile_enabled' => array( 1 ),
					),
					'type'       => EQUADIO_THEME_FREE ? 'hidden' : 'switch',
				),
				'header_mobile_hide_login'      => array(
					'title'      => esc_html__( 'Hide login/logout', 'equadio' ),
					'std'        => 0,
					'dependency' => array(
						'header_type'           => array( 'default' ),
						'header_mobile_enabled' => array( 1 ),
					),
					'type'       => EQUADIO_THEME_FREE ? 'hidden' : 'switch',
				),
				'header_mobile_hide_search'     => array(
					'title'      => esc_html__( 'Hide search', 'equadio' ),
					'std'        => 0,
					'dependency' => array(
						'header_type'           => array( 'default' ),
						'header_mobile_enabled' => array( 1 ),
					),
					'type'       => EQUADIO_THEME_FREE ? 'hidden' : 'switch',
				),
				'header_mobile_hide_cart'       => array(
					'title'      => esc_html__( 'Hide cart', 'equadio' ),
					'std'        => 0,
					'dependency' => array(
						'header_type'           => array( 'default' ),
						'header_mobile_enabled' => array( 1 ),
					),
					'type'       => EQUADIO_THEME_FREE ? 'hidden' : 'switch',
				),



				// 'Footer'
				//---------------------------------------------
				'footer'                        => array(
					'title'    => esc_html__( 'Footer', 'equadio' ),
					'desc'     => wp_kses_data( $msg_override ),
					'priority' => 50,
					'icon'     => 'icon-footer',
					'type'     => 'section',
				),
				'footer_type'                   => array(
					'title'    => esc_html__( 'Footer style', 'equadio' ),
					'desc'     => wp_kses_data( __( 'Choose whether to use the default footer or footer Layouts (available only if the ThemeREX Addons is activated)', 'equadio' ) ),
					'override' => array(
						'mode'    => 'page,post,product,cpt_team,cpt_services,cpt_dishes,cpt_competitions,cpt_rounds,cpt_matches,cpt_cars,cpt_properties,cpt_courses,cpt_portfolio',
						'section' => esc_html__( 'Footer', 'equadio' ),
					),
					'std'      => 'default',
					'options'  => equadio_get_list_header_footer_types(),
					'type'     => EQUADIO_THEME_FREE || ! equadio_exists_trx_addons() ? 'hidden' : 'radio',
				),
				'footer_style'                  => array(
					'title'      => esc_html__( 'Select custom layout', 'equadio' ),
					'desc'       => wp_kses( __( 'Select custom footer from Layouts Builder', 'equadio' ), 'equadio_kses_content' ),
					'override'   => array(
						'mode'    => 'page,post,product,cpt_team,cpt_services,cpt_dishes,cpt_competitions,cpt_rounds,cpt_matches,cpt_cars,cpt_properties,cpt_courses,cpt_portfolio',
						'section' => esc_html__( 'Footer', 'equadio' ),
					),
					'dependency' => array(
						'footer_type' => array( 'custom' ),
					),
					'std'        => 'footer-custom-elementor-footer-default',
					'options'    => array(),
					'type'       => 'select',
				),
				'footer_widgets'                => array(
					'title'      => esc_html__( 'Footer widgets', 'equadio' ),
					'desc'       => wp_kses_data( __( 'Select set of widgets to show in the footer', 'equadio' ) ),
					'override'   => array(
						'mode'    => 'page,post,product,cpt_team,cpt_services,cpt_dishes,cpt_competitions,cpt_rounds,cpt_matches,cpt_cars,cpt_properties,cpt_courses,cpt_portfolio',
						'section' => esc_html__( 'Footer', 'equadio' ),
					),
					'dependency' => array(
						'footer_type' => array( 'default' ),
					),
					'std'        => 'footer_widgets',
					'options'    => array(),
					'type'       => 'select',
				),
				'footer_columns'                => array(
					'title'      => esc_html__( 'Footer columns', 'equadio' ),
					'desc'       => wp_kses_data( __( 'Select number columns to show widgets in the footer. If 0 - autodetect by the widgets count', 'equadio' ) ),
					'override'   => array(
						'mode'    => 'page,post,product,cpt_team,cpt_services,cpt_dishes,cpt_competitions,cpt_rounds,cpt_matches,cpt_cars,cpt_properties,cpt_courses,cpt_portfolio',
						'section' => esc_html__( 'Footer', 'equadio' ),
					),
					'dependency' => array(
						'footer_type'    => array( 'default' ),
						'footer_widgets' => array( '^hide' ),
					),
					'std'        => 0,
					'options'    => equadio_get_list_range( 0, 6 ),
					'type'       => 'select',
				),
				'footer_wide'                   => array(
					'title'      => esc_html__( 'Footer fullwidth', 'equadio' ),
					'desc'       => wp_kses_data( __( 'Do you want to stretch the footer to the entire window width?', 'equadio' ) ),
					'override'   => array(
						'mode'    => 'page,post,product,cpt_team,cpt_services,cpt_dishes,cpt_competitions,cpt_rounds,cpt_matches,cpt_cars,cpt_properties,cpt_courses,cpt_portfolio',
						'section' => esc_html__( 'Footer', 'equadio' ),
					),
					'dependency' => array(
						'footer_type' => array( 'default' ),
					),
					'std'        => 0,
					'type'       => 'switch',
				),
				'logo_in_footer'                => array(
					'title'      => esc_html__( 'Show logo', 'equadio' ),
					'desc'       => wp_kses_data( __( 'Show logo in the footer', 'equadio' ) ),
					'refresh'    => false,
					'dependency' => array(
						'footer_type' => array( 'default' ),
					),
					'std'        => 0,
					'type'       => 'switch',
				),
				'logo_footer'                   => array(
					'title'      => esc_html__( 'Logo for footer', 'equadio' ),
					'desc'       => wp_kses_data( __( 'Select or upload site logo to display it in the footer', 'equadio' ) ),
					'dependency' => array(
						'footer_type'    => array( 'default' ),
						'logo_in_footer' => array( 1 ),
					),
					'std'        => '',
					'type'       => 'image',
				),
				'logo_footer_retina'            => array(
					'title'      => esc_html__( 'Logo for footer (Retina)', 'equadio' ),
					'desc'       => wp_kses_data( __( 'Select or upload logo for the footer area used on Retina displays (if empty - use default logo from the field above)', 'equadio' ) ),
					'dependency' => array(
						'footer_type'         => array( 'default' ),
						'logo_in_footer'      => array( 1 ),
						'logo_retina_enabled' => array( 1 ),
					),
					'std'        => '',
					'type'       => EQUADIO_THEME_FREE ? 'hidden' : 'image',
				),
				'socials_in_footer'             => array(
					'title'      => esc_html__( 'Show social icons', 'equadio' ),
					'desc'       => wp_kses_data( __( 'Show social icons in the footer (under logo or footer widgets)', 'equadio' ) ),
					'dependency' => array(
						'footer_type' => array( 'default' ),
					),
					'std'        => 0,
					'type'       => ! equadio_exists_trx_addons() ? 'hidden' : 'switch',
				),
				'copyright'                     => array(
					'title'      => esc_html__( 'Copyright', 'equadio' ),
					'desc'       => wp_kses_data( __( 'Copyright text in the footer. Use {Y} to insert current year and press "Enter" to create a new line', 'equadio' ) ),
					'translate'  => true,
					'std'        => esc_html__( 'Copyright &copy; {Y} by ThemeREX. All rights reserved.', 'equadio' ),
					'dependency' => array(
						'footer_type' => array( 'default' ),
					),
					'refresh'    => false,
					'type'       => 'textarea',
				),



				// 'Mobile version'
				//---------------------------------------------
				'mobile'                        => array(
					'title'    => esc_html__( 'Mobile', 'equadio' ),
					'desc'     => wp_kses_data( $msg_override ),
					'priority' => 55,
					'icon'     => 'icon-smartphone',
					'type'     => 'section',
				),

				'mobile_header_info'            => array(
					'title' => esc_html__( 'Header on the mobile device', 'equadio' ),
					'desc'  => '',
					'type'  => 'info',
				),
				'header_type_mobile'            => array(
					'title'    => esc_html__( 'Header style', 'equadio' ),
					'desc'     => wp_kses_data( __( 'Choose the header to be used on mobile devices: the default header or header Layouts (available only if the ThemeREX Addons is activated)', 'equadio' ) ),
					'std'      => 'inherit',
					'options'  => equadio_get_list_header_footer_types( true ),
					'type'     => EQUADIO_THEME_FREE || ! equadio_exists_trx_addons() ? 'hidden' : 'radio',
				),
				'header_style_mobile'           => array(
					'title'      => esc_html__( 'Select custom layout', 'equadio' ),
					'desc'       => wp_kses( __( 'Select custom header from Layouts Builder', 'equadio' ), 'equadio_kses_content' ),
					'dependency' => array(
						'header_type_mobile' => array( 'custom' ),
					),
					'std'        => 'inherit',
					'options'    => array(),
					'type'       => 'select',
				),
				'header_position_mobile'        => array(
					'title'    => esc_html__( 'Header position', 'equadio' ),
					'desc'     => wp_kses_data( __( 'Select position to display the site header', 'equadio' ) ),
					'std'      => 'inherit',
					'options'  => array(),
					'type'     => EQUADIO_THEME_FREE ? 'hidden' : 'radio',
				),

				'mobile_sidebar_info'           => array(
					'title' => esc_html__( 'Sidebar on the mobile device', 'equadio' ),
					'desc'  => '',
					'type'  => 'info',
				),
				'sidebar_position_mobile'       => array(
					'title'    => esc_html__( 'Sidebar position on mobile', 'equadio' ),
					'desc'     => wp_kses_data( __( 'Select sidebar position on mobile devices', 'equadio' ) ),
					'std'      => 'inherit',
					'options'  => array(),
					'type'     => 'choice',
				),
				'sidebar_type_mobile'           => array(
					'title'    => esc_html__( 'Sidebar style', 'equadio' ),
					'desc'     => wp_kses_data( __( 'Choose whether to use the default sidebar or sidebar Layouts (available only if the ThemeREX Addons is activated)', 'equadio' ) ),
					'dependency' => array(
						'sidebar_position_mobile' => array( '^hide' ),
					),
					'std'      => 'inherit',
					'options'  => equadio_get_list_header_footer_types( true ),
					'type'     => EQUADIO_THEME_FREE || ! equadio_exists_trx_addons() ? 'hidden' : 'radio',
				),
				'sidebar_style_mobile'          => array(
					'title'      => esc_html__( 'Select custom layout', 'equadio' ),
					'desc'       => wp_kses( __( 'Select custom sidebar from Layouts Builder', 'equadio' ), 'equadio_kses_content' ),
					'dependency' => array(
						'sidebar_position_mobile' => array( '^hide' ),
						'sidebar_type_mobile' => array( 'custom' ),
					),
					'std'        => 'inherit',
					'options'    => array(),
					'type'       => 'select',
				),
				'sidebar_widgets_mobile'        => array(
					'title'      => esc_html__( 'Sidebar widgets', 'equadio' ),
					'desc'       => wp_kses_data( __( 'Select default widgets to show in the sidebar on mobile devices', 'equadio' ) ),
					'dependency' => array(
						'sidebar_position_mobile' => array( '^hide' ),
						'sidebar_type_mobile' => array( 'default' )
					),
					'std'        => 'sidebar_widgets',
					'options'    => array(),
					'type'       => 'select',
				),
				'expand_content_mobile'         => array(
					'title'   => esc_html__( 'Content width', 'equadio' ),
					'desc'    => wp_kses_data( __( 'Content width if the sidebar is hidden on mobile devices', 'equadio' ) ),
					'refresh' => false,
					'dependency' => array(
						'sidebar_position_mobile' => array( 'hide', 'inherit' ),
					),
					'std'     => 'inherit',
					'options' => equadio_get_list_expand_content( true ),
					'type'     => EQUADIO_THEME_FREE ? 'hidden' : 'choice',
				),

				'mobile_footer_info'           => array(
					'title' => esc_html__( 'Footer on the mobile device', 'equadio' ),
					'desc'  => '',
					'type'  => 'info',
				),
				'footer_type_mobile'           => array(
					'title'    => esc_html__( 'Footer style', 'equadio' ),
					'desc'     => wp_kses_data( __( 'Choose whether to use on mobile devices: the default footer or footer Layouts (available only if the ThemeREX Addons is activated)', 'equadio' ) ),
					'std'      => 'inherit',
					'options'  => equadio_get_list_header_footer_types( true ),
					'type'     => EQUADIO_THEME_FREE || ! equadio_exists_trx_addons() ? 'hidden' : 'radio',
				),
				'footer_style_mobile'          => array(
					'title'      => esc_html__( 'Select custom layout', 'equadio' ),
					'desc'       => wp_kses( __( 'Select custom footer from Layouts Builder', 'equadio' ), 'equadio_kses_content' ),
					'dependency' => array(
						'footer_type_mobile' => array( 'custom' ),
					),
					'std'        => 'inherit',
					'options'    => array(),
					'type'       => 'select',
				),
				'footer_widgets_mobile'        => array(
					'title'      => esc_html__( 'Footer widgets', 'equadio' ),
					'desc'       => wp_kses_data( __( 'Select set of widgets to show in the footer', 'equadio' ) ),
					'dependency' => array(
						'footer_type_mobile' => array( 'default' ),
					),
					'std'        => 'footer_widgets',
					'options'    => array(),
					'type'       => 'select',
				),
				'footer_columns_mobile'        => array(
					'title'      => esc_html__( 'Footer columns', 'equadio' ),
					'desc'       => wp_kses_data( __( 'Select number columns to show widgets in the footer. If 0 - autodetect by the widgets count', 'equadio' ) ),
					'dependency' => array(
						'footer_type_mobile'    => array( 'default' ),
						'footer_widgets_mobile' => array( '^hide' ),
					),
					'std'        => 0,
					'options'    => equadio_get_list_range( 0, 6 ),
					'type'       => 'select',
				),



				// 'Blog'
				//---------------------------------------------
				'blog'                          => array(
					'title'    => esc_html__( 'Blog', 'equadio' ),
					'desc'     => wp_kses_data( __( 'Options of the the blog archive', 'equadio' ) ),
					'priority' => 70,
					'icon'     => 'icon-page',
					'type'     => 'panel',
				),


				// Blog - Posts page
				//---------------------------------------------
				'blog_general'                  => array(
					'title' => esc_html__( 'Posts page', 'equadio' ),
					'desc'  => wp_kses_data( __( 'Style and components of the blog archive', 'equadio' ) ),
					'type'  => 'section',
				),
				'blog_general_info'             => array(
					'title'  => esc_html__( 'Posts page settings', 'equadio' ),
					'desc'   => wp_kses_data( __( 'Customize the blog archive: post layout, header and footer style, sidebar position, etc.', 'equadio' ) ),
					'qsetup' => esc_html__( 'General', 'equadio' ),
					'type'   => 'info',
				),
				'blog_style'                    => array(
					'title'      => esc_html__( 'Blog style', 'equadio' ),
					'desc'       => '',
					'override'   => array(
						'mode'    => 'page',
						'section' => esc_html__( 'Content', 'equadio' ),
					),
					'dependency' => array(
						'compare' => 'or',
						'#page_template' => array( 'blog.php' ),
						'.editor-page-attributes__template select' => array( 'blog.php' ),
						'.components-select-control:not(.post-author-selector) select' => array( 'blog.php' ),
					),
					'std'        => 'excerpt',
					'qsetup'     => esc_html__( 'General', 'equadio' ),
					'options'    => array(),
					'type'       => 'choice',
				),
				'first_post_large'              => array(
					'title'      => esc_html__( 'Large first post', 'equadio' ),
					'desc'       => wp_kses_data( __( 'Make your first post stand out by making it bigger', 'equadio' ) ),
					'override'   => array(
						'mode'    => 'page',
						'section' => esc_html__( 'Content', 'equadio' ),
					),
					'dependency' => array(
						'compare' => 'or',
						'#page_template' => array( 'blog.php' ),
						'.editor-page-attributes__template select' => array( 'blog.php' ),
						'.components-select-control:not(.post-author-selector) select' => array( 'blog.php' ),
						'blog_style' => array( 'classic', 'masonry' ),
					),
					'std'        => 0,
					'type'       => 'switch',
				),
				'blog_content'                  => array(
					'title'      => esc_html__( 'Posts content', 'equadio' ),
					'desc'       => wp_kses_data( __( 'Display either post excerpts or the full post content', 'equadio' ) ),
					'std'        => 'excerpt',
					'dependency' => array(
						'blog_style' => array( 'excerpt' ),
					),
					'options'    => equadio_get_list_blog_contents(),
					'type'       => 'radio',
				),
				'excerpt_length'                => array(
					'title'      => esc_html__( 'Excerpt length', 'equadio' ),
					'desc'       => wp_kses_data( __( 'Length (in words) to generate excerpt from the post content. Attention! If the post excerpt is explicitly specified - it appears unchanged', 'equadio' ) ),
					'dependency' => array(
						'blog_style'   => array( 'excerpt' ),
						'blog_content' => array( 'excerpt' ),
					),
					'std'        => 35,
					'type'       => 'text',
				),
				'blog_columns'                  => array(
					'title'   => esc_html__( 'Blog columns', 'equadio' ),
					'desc'    => wp_kses_data( __( 'How many columns should be used in the blog archive (from 2 to 4)?', 'equadio' ) ),
					'std'     => 2,
					'options' => equadio_get_list_range( 2, 4 ),
					'type'    => 'hidden',      // This options is available and must be overriden only for some modes (for example, 'shop')
				),
				'post_type'                     => array(
					'title'      => esc_html__( 'Post type', 'equadio' ),
					'desc'       => wp_kses_data( __( 'Select post type to show in the blog archive', 'equadio' ) ),
					'override'   => array(
						'mode'    => 'page',
						'section' => esc_html__( 'Content', 'equadio' ),
					),
					'dependency' => array(
						'compare' => 'or',
						'#page_template' => array( 'blog.php' ),
						'.editor-page-attributes__template select' => array( 'blog.php' ),
						'.components-select-control:not(.post-author-selector) select' => array( 'blog.php' ),
					),
					'linked'     => 'parent_cat',
					'refresh'    => false,
					'hidden'     => true,
					'std'        => 'post',
					'options'    => array(),
					'type'       => 'select',
				),
				'parent_cat'                    => array(
					'title'      => esc_html__( 'Category to show', 'equadio' ),
					'desc'       => wp_kses_data( __( 'Select category to show in the blog archive', 'equadio' ) ),
					'override'   => array(
						'mode'    => 'page',
						'section' => esc_html__( 'Content', 'equadio' ),
					),
					'dependency' => array(
						'compare' => 'or',
						'#page_template' => array( 'blog.php' ),
						'.editor-page-attributes__template select' => array( 'blog.php' ),
						'.components-select-control:not(.post-author-selector) select' => array( 'blog.php' ),
					),
					'refresh'    => false,
					'hidden'     => true,
					'std'        => '0',
					'options'    => array(),
					'type'       => 'select',
				),
				'posts_per_page'                => array(
					'title'      => esc_html__( 'Posts per page', 'equadio' ),
					'desc'       => wp_kses_data( __( 'How many posts will be displayed on this page', 'equadio' ) ),
					'override'   => array(
						'mode'    => 'page',
						'section' => esc_html__( 'Content', 'equadio' ),
					),
					'dependency' => array(
						'compare' => 'or',
						'#page_template' => array( 'blog.php' ),
						'.editor-page-attributes__template select' => array( 'blog.php' ),
						'.components-select-control:not(.post-author-selector) select' => array( 'blog.php' ),
					),
					'hidden'     => true,
					'std'        => '',
					'type'       => 'text',
				),
				'blog_pagination'               => array(
					'title'      => esc_html__( 'Pagination style', 'equadio' ),
					'desc'       => wp_kses_data( __( 'Show Older/Newest posts or Page numbers below the posts list', 'equadio' ) ),
					'override'   => array(
						'mode'    => 'page',
						'section' => esc_html__( 'Content', 'equadio' ),
					),
					'std'        => 'pages',
					'qsetup'     => esc_html__( 'General', 'equadio' ),
					'dependency' => array(
						'compare' => 'or',
						'#page_template' => array( 'blog.php' ),
						'.editor-page-attributes__template select' => array( 'blog.php' ),
						'.components-select-control:not(.post-author-selector) select' => array( 'blog.php' ),
					),
					'options'    => equadio_get_list_blog_paginations(),
					'type'       => 'choice',
				),
				'blog_animation'                => array(
					'title'      => esc_html__( 'Post animation', 'equadio' ),
					'desc'       => wp_kses_data( __( "Select post animation for the archive page. Attention! Do not use any animation on pages with the 'wheel to the anchor' behaviour!", 'equadio' ) ),
					'override'   => array(
						'mode'    => 'page',
						'section' => esc_html__( 'Content', 'equadio' ),
					),
					'dependency' => array(
						'compare'                                  => 'or',
						'#page_template'                           => array( 'blog.php' ),
						'.editor-page-attributes__template select' => array( 'blog.php' ),
						'.components-select-control:not(.post-author-selector) select' => array( 'blog.php' ),
					),
					'std'        => 'none',
					'options'    => array(),
					'type'       => EQUADIO_THEME_FREE ? 'hidden' : 'select',
				),
				'disable_animation_on_mobile'   => array(
					'title'      => esc_html__( 'Disable animation on mobile', 'equadio' ),
					'desc'       => wp_kses_data( __( 'Disable any posts animation on mobile devices', 'equadio' ) ),
					'std'        => 0,
					'type'       => EQUADIO_THEME_FREE ? 'hidden' : 'switch',
				),
				'show_filters'                  => array(
					'title'      => esc_html__( 'Show filters', 'equadio' ),
					'desc'       => wp_kses_data( __( 'Show categories as tabs to filter posts', 'equadio' ) ),
					'override'   => array(
						'mode'    => 'page',
						'section' => esc_html__( 'Content', 'equadio' ),
					),
					'dependency' => array(
						'compare'                                  => 'or',
						'#page_template'                           => array( 'blog.php' ),
						'.editor-page-attributes__template select' => array( 'blog.php' ),
						'.components-select-control:not(.post-author-selector) select' => array( 'blog.php' ),
					),
					'hidden'     => true,
					'std'        => 0,
					'type'       => EQUADIO_THEME_FREE ? 'hidden' : 'switch',
				),
				'video_in_popup'                => array(
					'title'      => esc_html__( 'Open video in the popup on a blog archive', 'equadio' ),
					'desc'       => wp_kses_data( __( 'Open the video from posts in the popup (if plugin "ThemeREX Addons" is installed) or play the video instead the cover image', 'equadio' ) ),
					'std'        => 0,
					'override'   => array(
						'mode'    => 'page',
						'section' => esc_html__( 'Content', 'equadio' ),
					),
					'dependency' => array(
						'compare'                                  => 'or',
						'#page_template'                           => array( 'blog.php' ),
						'.editor-page-attributes__template select' => array( 'blog.php' ),
						'.components-select-control:not(.post-author-selector) select' => array( 'blog.php' ),
					),
					'type'       => 'switch',
				),
				'open_full_post_in_blog'        => array(
					'title'      => esc_html__( 'Open full post in blog', 'equadio' ),
					'desc'       => wp_kses_data( __( 'Allow to open the full version of the post directly in the blog feed. Attention! Applies only to 1 column layouts!', 'equadio' ) ),
					'override'   => array(
						'mode'    => 'page',
						'section' => esc_html__( 'Content', 'equadio' ),
					),
					'std'        => 0,
					'type'       => 'switch',
				),
				'open_full_post_hide_author'    => array(
					'title'      => esc_html__( 'Hide author bio', 'equadio' ),
					'desc'       => wp_kses_data( __( "Hide author bio after post content when open the full version of the post directly in the blog feed.", 'equadio' ) ),
					'override'   => array(
						'mode'    => 'page',
						'section' => esc_html__( 'Content', 'equadio' ),
					),
					'dependency' => array(
						'open_full_post_in_blog' => array( 1 ),
					),
					'std'        => 1,
					'type'       => EQUADIO_THEME_FREE ? 'hidden' : 'switch',
				),
				'open_full_post_hide_related'   => array(
					'title'      => esc_html__( 'Hide related posts', 'equadio' ),
					'desc'       => wp_kses_data( __( "Hide related posts after post content when open the full version of the post directly in the blog feed.", 'equadio' ) ),
					'override'   => array(
						'mode'    => 'page',
						'section' => esc_html__( 'Content', 'equadio' ),
					),
					'dependency' => array(
						'open_full_post_in_blog' => array( 1 ),
					),
					'std'        => 1,
					'type'       => EQUADIO_THEME_FREE ? 'hidden' : 'switch',
				),

				'blog_header_info'              => array(
					'title' => esc_html__( 'Header', 'equadio' ),
					'desc'  => '',
					'type'  => 'info',
				),
				'header_type_blog'              => array(
					'title'    => esc_html__( 'Header style', 'equadio' ),
					'desc'     => wp_kses_data( __( 'Choose whether to use the default header or header Layouts (available only if the ThemeREX Addons is activated)', 'equadio' ) ),
					'std'      => 'inherit',
					'options'  => equadio_get_list_header_footer_types( true ),
					'type'     => EQUADIO_THEME_FREE || ! equadio_exists_trx_addons() ? 'hidden' : 'radio',
				),
				'header_style_blog'             => array(
					'title'      => esc_html__( 'Select custom layout', 'equadio' ),
					'desc'       => wp_kses( __( 'Select custom header from Layouts Builder', 'equadio' ), 'equadio_kses_content' ),
					'dependency' => array(
						'header_type_blog' => array( 'custom' ),
					),
					'std'        => 'inherit',
					'options'    => array(),
					'type'       => 'select',
				),
				'header_position_blog'          => array(
					'title'    => esc_html__( 'Header position', 'equadio' ),
					'desc'     => wp_kses_data( __( 'Select position to display the site header', 'equadio' ) ),
					'std'      => 'inherit',
					'options'  => array(),
					'type'     => EQUADIO_THEME_FREE ? 'hidden' : 'radio',
				),
				'header_fullheight_blog'        => array(
					'title'    => esc_html__( 'Header fullheight', 'equadio' ),
					'desc'     => wp_kses_data( __( 'Stretch header area to fill the entire screen. Used only if the header has a background image', 'equadio' ) ),
					'std'      => 'inherit',
					'options'  => equadio_get_list_checkbox_values( true ),
					'type'     => EQUADIO_THEME_FREE ? 'hidden' : 'radio',
				),
				'header_wide_blog'              => array(
					'title'      => esc_html__( 'Header fullwidth', 'equadio' ),
					'desc'       => wp_kses_data( __( 'Do you want to stretch the header widgets area to the entire window width?', 'equadio' ) ),
					'dependency' => array(
						'header_type_blog' => array( 'default' ),
					),
					'std'      => 'inherit',
					'options'  => equadio_get_list_checkbox_values( true ),
					'type'     => EQUADIO_THEME_FREE ? 'hidden' : 'radio',
				),

				'blog_sidebar_info'             => array(
					'title' => esc_html__( 'Sidebar', 'equadio' ),
					'desc'  => '',
					'type'  => 'info',
				),
				'sidebar_position_blog'         => array(
					'title'   => esc_html__( 'Sidebar position', 'equadio' ),
					'desc'    => wp_kses_data( __( 'Select position to show sidebar', 'equadio' ) ),
					'std'     => 'inherit',
					'options' => array(),
					'qsetup'     => esc_html__( 'General', 'equadio' ),
					'type'    => 'choice',
				),
				'sidebar_position_ss_blog'  => array(
					'title'    => esc_html__( 'Sidebar position on the small screen', 'equadio' ),
					'desc'     => wp_kses_data( __( 'Select position to move sidebar on the small screen - above or below the content', 'equadio' ) ),
					'dependency' => array(
						'sidebar_position_blog' => array( '^hide' ),
					),
					'std'      => 'inherit',
					'qsetup'   => esc_html__( 'General', 'equadio' ),
					'options'  => array(),
					'type'     => 'radio',
				),
				'sidebar_type_blog'           => array(
					'title'    => esc_html__( 'Sidebar style', 'equadio' ),
					'desc'     => wp_kses_data( __( 'Choose whether to use the default sidebar or sidebar Layouts (available only if the ThemeREX Addons is activated)', 'equadio' ) ),
					'dependency' => array(
						'sidebar_position_blog' => array( '^hide' ),
					),
					'std'      => 'default',
					'options'  => equadio_get_list_header_footer_types(),
					'type'     => EQUADIO_THEME_FREE || ! equadio_exists_trx_addons() ? 'hidden' : 'radio',
				),
				'sidebar_style_blog'            => array(
					'title'      => esc_html__( 'Select custom layout', 'equadio' ),
					'desc'       => wp_kses( __( 'Select custom sidebar from Layouts Builder', 'equadio' ), 'equadio_kses_content' ),
					'dependency' => array(
						'sidebar_position_blog' => array( '^hide' ),
						'sidebar_type_blog'     => array( 'custom' ),
					),
					'std'        => 'sidebar-custom-sidebar',
					'options'    => array(),
					'type'       => 'select',
				),
				'sidebar_widgets_blog'          => array(
					'title'      => esc_html__( 'Sidebar widgets', 'equadio' ),
					'desc'       => wp_kses_data( __( 'Select default widgets to show in the sidebar', 'equadio' ) ),
					'dependency' => array(
						'sidebar_position_blog' => array( '^hide' ),
						'sidebar_type_blog'     => array( 'default' ),
					),
					'std'        => 'sidebar_widgets',
					'options'    => array(),
					'qsetup'     => esc_html__( 'General', 'equadio' ),
					'type'       => 'select',
				),
				'expand_content_blog'           => array(
					'title'   => esc_html__( 'Content width', 'equadio' ),
					'desc'    => wp_kses_data( __( 'Content width if the sidebar is hidden', 'equadio' ) ),
					'refresh' => false,
					'std'     => 'inherit',
					'options' => equadio_get_list_expand_content( true ),
					'type'    => EQUADIO_THEME_FREE ? 'hidden' : 'choice',
				),

				'blog_widgets_info'             => array(
					'title' => esc_html__( 'Additional widgets', 'equadio' ),
					'desc'  => '',
					'type'  => EQUADIO_THEME_FREE ? 'hidden' : 'info',
				),
				'widgets_above_page_blog'       => array(
					'title'   => esc_html__( 'Widgets at the top of the page', 'equadio' ),
					'desc'    => wp_kses_data( __( 'Select widgets to show at the top of the page (above content and sidebar)', 'equadio' ) ),
					'std'     => 'hide',
					'options' => array(),
					'type'    => EQUADIO_THEME_FREE ? 'hidden' : 'select',
				),
				'widgets_above_content_blog'    => array(
					'title'   => esc_html__( 'Widgets above the content', 'equadio' ),
					'desc'    => wp_kses_data( __( 'Select widgets to show at the beginning of the content area', 'equadio' ) ),
					'std'     => 'hide',
					'options' => array(),
					'type'    => EQUADIO_THEME_FREE ? 'hidden' : 'select',
				),
				'widgets_below_content_blog'    => array(
					'title'   => esc_html__( 'Widgets below the content', 'equadio' ),
					'desc'    => wp_kses_data( __( 'Select widgets to show at the ending of the content area', 'equadio' ) ),
					'std'     => 'hide',
					'options' => array(),
					'type'    => EQUADIO_THEME_FREE ? 'hidden' : 'select',
				),
				'widgets_below_page_blog'       => array(
					'title'   => esc_html__( 'Widgets at the bottom of the page', 'equadio' ),
					'desc'    => wp_kses_data( __( 'Select widgets to show at the bottom of the page (below content and sidebar)', 'equadio' ) ),
					'std'     => 'hide',
					'options' => array(),
					'type'    => EQUADIO_THEME_FREE ? 'hidden' : 'select',
				),

				'blog_advanced_info'            => array(
					'title' => esc_html__( 'Advanced settings', 'equadio' ),
					'desc'  => '',
					'type'  => 'info',
				),
				'no_image'                      => array(
					'title' => esc_html__( 'Image placeholder', 'equadio' ),
					'desc'  => wp_kses_data( __( "Select or upload a placeholder image for posts without a featured image. Placeholder is used exclusively on the blog stream page (and not on single post pages), and only in those styles, where omitting a featured image would be inappropriate.", 'equadio' ) ),
					'std'   => '',
					'type'  => 'image',
				),
				'sticky_style'                  => array(
					'title'   => esc_html__( 'Sticky posts style', 'equadio' ),
					'desc'    => wp_kses_data( __( 'Select style of the sticky posts output', 'equadio' ) ),
					'std'     => 'inherit',
					'options' => array(
						'inherit' => esc_html__( 'Decorated posts', 'equadio' ),
						'columns' => esc_html__( 'Mini-cards', 'equadio' ),
					),
					'type'    => EQUADIO_THEME_FREE ? 'hidden' : 'select',
				),
				'meta_parts'                    => array(
					'title'      => esc_html__( 'Post meta', 'equadio' ),
					'desc'       => wp_kses_data( __( "If your blog page is created using the 'Blog archive' page template, set up the 'Post Meta' settings in the 'Theme Options' section of that page. Post counters and Share Links are available only if plugin ThemeREX Addons is active", 'equadio' ) )
								. '<br>'
								. wp_kses_data( __( '<b>Tip:</b> Drag items to change their order.', 'equadio' ) ),
					'override'   => array(
						'mode'    => 'page',
						'section' => esc_html__( 'Content', 'equadio' ),
					),
					'dependency' => array(
						'compare' => 'or',
						'#page_template' => array( 'blog.php' ),
						'.editor-page-attributes__template select' => array( 'blog.php' ),
						'.components-select-control:not(.post-author-selector) select' => array( 'blog.php' ),
					),
					'dir'        => 'vertical',
					'sortable'   => true,
					'std'        => 'categories=0|date=1|modified=0|views=0|likes=0|comments=1|author=0|share=0|edit=0',
					'options'    => equadio_get_list_meta_parts(),
					'type'       => EQUADIO_THEME_FREE ? 'hidden' : 'checklist',
				),
				'time_diff_before'              => array(
					'title' => esc_html__( 'Easy readable date format', 'equadio' ),
					'desc'  => wp_kses_data( __( "For how many days to show the easy-readable date format (e.g. '3 days ago') instead of the standard publication date", 'equadio' ) ),
					'std'   => 5,
					'type'  => 'text',
				),
				'use_blog_archive_pages'        => array(
					'title'      => esc_html__( 'Use "Blog Archive" page settings on the post list', 'equadio' ),
					'desc'       => wp_kses_data( __( 'Apply options and content of pages created with the template "Blog Archive" for some type of posts and / or taxonomy when viewing feeds of posts of this type and taxonomy.', 'equadio' ) ),
					'std'        => 0,
					'type'       => 'switch',
				),


				// Blog - Single posts
				//---------------------------------------------
				'blog_single'                   => array(
					'title' => esc_html__( 'Single posts', 'equadio' ),
					'desc'  => wp_kses_data( __( 'Settings of the single post', 'equadio' ) ),
					'type'  => 'section',
				),

				'blog_single_info'       => array(
					'title' => esc_html__( 'Single posts', 'equadio' ),
					'desc'   => wp_kses_data( __( 'Customize the single post: content  layout, header and footer styles, sidebar position, meta elements, etc.', 'equadio' ) ),
					'type'  => 'info',
				),
				'blog_single_header_info'       => array(
					'title' => esc_html__( 'Header', 'equadio' ),
					'desc'   => '',
					'type'  => 'info',
				),
				'header_type_single'            => array(
					'title'    => esc_html__( 'Header style', 'equadio' ),
					'desc'     => wp_kses_data( __( 'Choose whether to use the default header or header Layouts (available only if the ThemeREX Addons is activated)', 'equadio' ) ),
					'std'      => 'inherit',
					'options'  => equadio_get_list_header_footer_types( true ),
					'type'     => EQUADIO_THEME_FREE || ! equadio_exists_trx_addons() ? 'hidden' : 'radio',
				),
				'header_style_single'           => array(
					'title'      => esc_html__( 'Select custom layout', 'equadio' ),
					'desc'       => wp_kses( __( 'Select custom header from Layouts Builder', 'equadio' ), 'equadio_kses_content' ),
					'dependency' => array(
						'header_type_single' => array( 'custom' ),
					),
					'std'        => 'inherit',
					'options'    => array(),
					'type'       => 'select',
				),
				'header_position_single'        => array(
					'title'    => esc_html__( 'Header position', 'equadio' ),
					'desc'     => wp_kses_data( __( 'Select position to display the site header', 'equadio' ) ),
					'std'      => 'inherit',
					'options'  => array(),
					'type'     => EQUADIO_THEME_FREE ? 'hidden' : 'radio',
				),
				'header_fullheight_single'      => array(
					'title'    => esc_html__( 'Header fullheight', 'equadio' ),
					'desc'     => wp_kses_data( __( 'Stretch header area to fill the entire screen. Used only if the header has a background image', 'equadio' ) ),
					'std'      => 'inherit',
					'options'  => equadio_get_list_checkbox_values( true ),
					'type'     => EQUADIO_THEME_FREE ? 'hidden' : 'radio',
				),
				'header_wide_single'            => array(
					'title'      => esc_html__( 'Header fullwidth', 'equadio' ),
					'desc'       => wp_kses_data( __( 'Do you want to stretch the header widgets area to the entire window width?', 'equadio' ) ),
					'dependency' => array(
						'header_type_single' => array( 'default' ),
					),
					'std'      => 'inherit',
					'options'  => equadio_get_list_checkbox_values( true ),
					'type'     => EQUADIO_THEME_FREE ? 'hidden' : 'radio',
				),

				'blog_single_sidebar_info'      => array(
					'title' => esc_html__( 'Sidebar', 'equadio' ),
					'desc'  => '',
					'type'  => 'info',
				),
				'sidebar_position_single'       => array(
					'title'   => esc_html__( 'Sidebar position', 'equadio' ),
					'desc'    => wp_kses_data( __( 'Select position to show sidebar on the single posts', 'equadio' ) ),
					'std'     => 'hide',
					'override'   => array(
						'mode'    => 'post,product,cpt_team,cpt_services,cpt_dishes,cpt_competitions,cpt_rounds,cpt_matches,cpt_cars,cpt_properties,cpt_courses,cpt_portfolio',
						'section' => esc_html__( 'Content', 'equadio' ),
					),
					'options' => array(),
					'type'    => 'choice',
				),
				'sidebar_position_ss_single'    => array(
					'title'    => esc_html__( 'Sidebar position on the small screen', 'equadio' ),
					'desc'     => wp_kses_data( __( 'Select position to move sidebar on the single posts on the small screen - above or below the content', 'equadio' ) ),
					'override' => array(
						'mode'    => 'post,product,cpt_team,cpt_services,cpt_dishes,cpt_competitions,cpt_rounds,cpt_matches,cpt_cars,cpt_properties,cpt_courses,cpt_portfolio',
						'section' => esc_html__( 'Content', 'equadio' ),
					),
					'dependency' => array(
						'sidebar_position_single' => array( '^hide' ),
					),
					'std'      => 'below',
					'options'  => array(),
					'type'     => 'radio',
				),
				'sidebar_type_single'           => array(
					'title'    => esc_html__( 'Sidebar style', 'equadio' ),
					'desc'     => wp_kses_data( __( 'Choose whether to use the default sidebar or sidebar Layouts (available only if the ThemeREX Addons is activated)', 'equadio' ) ),
					'override'   => array(
						'mode'    => 'post,product,cpt_team,cpt_services,cpt_dishes,cpt_competitions,cpt_rounds,cpt_matches,cpt_cars,cpt_properties,cpt_courses,cpt_portfolio',
						'section' => esc_html__( 'Content', 'equadio' ),
					),
					'dependency' => array(
						'sidebar_position_single' => array( '^hide' ),
					),
					'std'      => 'default',
					'options'  => equadio_get_list_header_footer_types(),
					'type'     => EQUADIO_THEME_FREE || ! equadio_exists_trx_addons() ? 'hidden' : 'radio',
				),
				'sidebar_style_single'            => array(
					'title'      => esc_html__( 'Select custom layout', 'equadio' ),
					'desc'       => wp_kses( __( 'Select custom sidebar from Layouts Builder', 'equadio' ), 'equadio_kses_content' ),
					'override'   => array(
						'mode'    => 'post,product,cpt_team,cpt_services,cpt_dishes,cpt_competitions,cpt_rounds,cpt_matches,cpt_cars,cpt_properties,cpt_courses,cpt_portfolio',
						'section' => esc_html__( 'Content', 'equadio' ),
					),
					'dependency' => array(
						'sidebar_position_single' => array( '^hide' ),
						'sidebar_type_single'     => array( 'custom' ),
					),
					'std'        => 'sidebar-custom-sidebar',
					'options'    => array(),
					'type'       => 'select',
				),
				'sidebar_widgets_single'        => array(
					'title'      => esc_html__( 'Sidebar widgets', 'equadio' ),
					'desc'       => wp_kses_data( __( 'Select default widgets to show in the sidebar on the single posts', 'equadio' ) ),
					'override'   => array(
						'mode'    => 'post,product,cpt_team,cpt_services,cpt_dishes,cpt_competitions,cpt_rounds,cpt_matches,cpt_cars,cpt_properties,cpt_courses,cpt_portfolio',
						'section' => esc_html__( 'Content', 'equadio' ),
					),
					'dependency' => array(
						'sidebar_position_single' => array( '^hide' ),
						'sidebar_type_single'     => array( 'default' ),
					),
					'std'        => 'sidebar_widgets',
					'options'    => array(),
					'type'       => 'select',
				),
				'expand_content_single'         => array(
					'title'   => esc_html__( 'Content width', 'equadio' ),
					'desc'    => wp_kses_data( __( 'Content width on the single posts if the sidebar is hidden', 'equadio' ) ),
					'override'   => array(
						'mode'    => 'post,product,cpt_team,cpt_services,cpt_dishes,cpt_competitions,cpt_rounds,cpt_matches,cpt_cars,cpt_properties,cpt_courses,cpt_portfolio',
						'section' => esc_html__( 'Content', 'equadio' ),
					),
					'refresh' => false,
					'std'     => 'inherit',
					'options' => equadio_get_list_expand_content( true, true ),
					'type'    => EQUADIO_THEME_FREE ? 'hidden' : 'choice',
				),

				'blog_single_title_info'        => array(
					'title' => esc_html__( 'Featured image and title', 'equadio' ),
					'desc'  => '',
					'type'  => 'info',
				),
				'single_style'                  => array(
					'title'      => esc_html__( 'Single style', 'equadio' ),
					'desc'       => '',
					'override'   => array(
						'mode'    => 'post',
						'section' => esc_html__( 'Content', 'equadio' ),
					),
					'std'        => 'style-1',
					'qsetup'     => esc_html__( 'General', 'equadio' ),
					'options'    => array(),
					'type'       => 'choice',
				),
				'post_subtitle'                 => array(
					'title' => esc_html__( 'Post subtitle', 'equadio' ),
					'desc'  => wp_kses_data( __( "Specify post subtitle to display it under the post title.", 'equadio' ) ),
					'override' => array(
						'mode'    => 'post',
						'section' => esc_html__( 'Content', 'equadio' ),
					),
					'std'   => '',
					'hidden' => true,
					'type'  => 'text',
				),
				'show_post_meta'                => array(
					'title' => esc_html__( 'Show post meta', 'equadio' ),
					'desc'  => wp_kses_data( __( "Display block with post's meta: date, categories, counters, etc.", 'equadio' ) ),
					'std'   => 1,
					'type'  => 'switch',
				),
				'meta_parts_single'             => array(
					'title'      => esc_html__( 'Post meta', 'equadio' ),
					'desc'       => wp_kses_data( __( 'Meta parts for single posts. Post counters and Share Links are available only if plugin ThemeREX Addons is active', 'equadio' ) )
								. '<br>'
								. wp_kses_data( __( '<b>Tip:</b> Drag items to change their order.', 'equadio' ) ),
					'dependency' => array(
						'show_post_meta' => array( 1 ),
					),
					'dir'        => 'vertical',
					'sortable'   => true,
					'std'        => 'author=0|categories=0|date=1|modified=0|views=0|likes=1|share=0|comments=1|edit=0',
					'options'    => equadio_get_list_meta_parts(),
					'type'       => EQUADIO_THEME_FREE ? 'hidden' : 'checklist',
				),
				'share_position'                 => array(
					'title'      => esc_html__( 'Share links position', 'equadio' ),
					'desc'       => wp_kses_data( __( 'Select one or more positions to show Share links on single posts. Post counters and Share Links are available only if plugin ThemeREX Addons is active', 'equadio' ) ),
					'dependency' => array(
						'show_post_meta' => array( 1 ),
					),
					'dir'        => 'vertical',
					'std'        => 'top=0|left=0|bottom=1',
					'options'    => equadio_get_list_share_links_positions(),
					'type'       => EQUADIO_THEME_FREE ? 'hidden' : 'checklist',
				),
				'show_author_info'              => array(
					'title' => esc_html__( 'Show author info', 'equadio' ),
					'desc'  => wp_kses_data( __( "Display block with information about post's author", 'equadio' ) ),
					'std'   => 1,
					'type'  => 'switch',
				),
				'show_comments_button'          => array(
					'title' => esc_html__( 'Show comments button', 'equadio' ),
					'desc'  => wp_kses_data( __( "Display button to show/hide comments block", 'equadio' ) ),
					'override' => array(
						'mode'    => 'post',
						'section' => esc_html__( 'Content', 'equadio' ),
					),
					'std'   => 1,
					'type'  => 'switch',
				),
				'show_comments'                 => array(
					'title'   => esc_html__( 'Comments block', 'equadio' ),
					'desc'    => wp_kses_data( __( "Select initial state of the comments block", 'equadio' ) ),
					'override' => array(
						'mode'    => 'post',
						'section' => esc_html__( 'Content', 'equadio' ),
					),
					'options' => equadio_get_list_visiblehidden(),
					'dependency' => array(
						'show_comments_button' => array( 1 ),
					),
					'std'     => 'hidden',
					'type'    => 'radio',
				),

				'blog_single_related_info'      => array(
					'title' => esc_html__( 'Related posts', 'equadio' ),
					'desc'  => '',
					'type'  => 'info',
				),
				'show_related_posts'            => array(
					'title'    => esc_html__( 'Show related posts', 'equadio' ),
					'desc'     => wp_kses_data( __( "Show 'Related posts' section on single post pages", 'equadio' ) ),
					'override' => array(
						'mode'    => 'post',
						'section' => esc_html__( 'Content', 'equadio' ),
					),
					'std'      => 1,
					'type'     => 'switch',
				),
				'related_style'                 => array(
					'title'      => esc_html__( 'Related posts style', 'equadio' ),
					'desc'       => wp_kses_data( __( 'Select the style of the related posts output', 'equadio' ) ),
					'override' => array(
						'mode'    => 'post',
						'section' => esc_html__( 'Content', 'equadio' ),
					),
					'dependency' => array(
						'show_related_posts' => array( 1 ),
					),
					'std'        => 'classic',
					'options'    => array(
						'modern'  => esc_html__( 'Modern', 'equadio' ),
						'classic' => esc_html__( 'Classic', 'equadio' ),
						'wide'    => esc_html__( 'Wide', 'equadio' ),
						'list'    => esc_html__( 'List', 'equadio' ),
						'short'   => esc_html__( 'Short', 'equadio' ),
					),
					'type'       => EQUADIO_THEME_FREE ? 'hidden' : 'radio',
				),
				'related_position'              => array(
					'title'      => esc_html__( 'Related posts position', 'equadio' ),
					'desc'       => wp_kses_data( __( 'Select position to display the related posts', 'equadio' ) ),
					'override' => array(
						'mode'    => 'post',
						'section' => esc_html__( 'Content', 'equadio' ),
					),
					'dependency' => array(
						'show_related_posts' => array( 1 ),
					),
					'std'        => 'below_content',
					'options'    => array (
						'inside'        => esc_html__( 'Inside the content (fullwidth)', 'equadio' ),
						'inside_left'   => esc_html__( 'At left side of the content', 'equadio' ),
						'inside_right'  => esc_html__( 'At right side of the content', 'equadio' ),
						'below_content' => esc_html__( 'After the content', 'equadio' ),
						'below_page'    => esc_html__( 'After the content & sidebar', 'equadio' ),
					),
					'type'       => EQUADIO_THEME_FREE ? 'hidden' : 'select',
				),
				'related_position_inside'       => array(
					'title'      => esc_html__( 'Before # paragraph', 'equadio' ),
					'desc'       => wp_kses_data( __( 'Before what paragraph should related posts appear? If 0 - randomly.', 'equadio' ) ),
					'override' => array(
						'mode'    => 'post',
						'section' => esc_html__( 'Content', 'equadio' ),
					),
					'dependency' => array(
						'show_related_posts' => array( 1 ),
						'related_position' => array( 'inside', 'inside_left', 'inside_right' ),
					),
					'std'        => 2,
					'options'    => equadio_get_list_range( 0, 9 ),
					'type'       => EQUADIO_THEME_FREE ? 'hidden' : 'select',
				),
				'related_posts'                 => array(
					'title'      => esc_html__( 'Related posts', 'equadio' ),
					'desc'       => wp_kses_data( __( 'How many related posts should be displayed in the single post?', 'equadio' ) ),
					'override' => array(
						'mode'    => 'post',
						'section' => esc_html__( 'Content', 'equadio' ),
					),
					'dependency' => array(
						'show_related_posts' => array( 1 ),
					),
					'std'        => 2,
					'min'        => 1,
					'max'        => 9,
					'show_value' => true,
					'type'       => EQUADIO_THEME_FREE ? 'hidden' : 'slider',
				),
				'related_columns'               => array(
					'title'      => esc_html__( 'Related columns', 'equadio' ),
					'desc'       => wp_kses_data( __( 'How many columns should be used to output related posts on the single post page?', 'equadio' ) ),
					'override' => array(
						'mode'    => 'post',
						'section' => esc_html__( 'Content', 'equadio' ),
					),
					'dependency' => array(
						'show_related_posts' => array( 1 ),
						'related_position' => array( 'inside', 'below_content', 'below_page' ),
					),
					'std'        => 2,
					'options'    => equadio_get_list_range( 1, 6 ),
					'type'       => EQUADIO_THEME_FREE ? 'hidden' : 'radio',
				),
				'related_slider'                => array(
					'title'      => esc_html__( 'Use slider layout', 'equadio' ),
					'desc'       => wp_kses_data( __( 'Use slider layout in case related posts count is more than columns count', 'equadio' ) ),
					'override' => array(
						'mode'    => 'post',
						'section' => esc_html__( 'Content', 'equadio' ),
					),
					'dependency' => array(
						'show_related_posts' => array( 1 ),
					),
					'std'        => 0,
					'type'       => EQUADIO_THEME_FREE ? 'hidden' : 'switch',
				),
				'related_slider_controls'       => array(
					'title'      => esc_html__( 'Slider controls', 'equadio' ),
					'desc'       => wp_kses_data( __( 'Show arrows in the slider', 'equadio' ) ),
					'override' => array(
						'mode'    => 'post',
						'section' => esc_html__( 'Content', 'equadio' ),
					),
					'dependency' => array(
						'show_related_posts' => array( 1 ),
						'related_slider' => array( 1 ),
					),
					'std'        => 'none',
					'options'    => array(
						'none'    => esc_html__('None', 'equadio'),
						'side'    => esc_html__('Side', 'equadio'),
						'outside' => esc_html__('Outside', 'equadio'),
						'top'     => esc_html__('Top', 'equadio'),
						'bottom'  => esc_html__('Bottom', 'equadio')
					),
					'type'       => EQUADIO_THEME_FREE ? 'hidden' : 'select',
				),
				'related_slider_pagination'       => array(
					'title'      => esc_html__( 'Slider pagination', 'equadio' ),
					'desc'       => wp_kses_data( __( 'Show bullets after the slider', 'equadio' ) ),
					'override' => array(
						'mode'    => 'post',
						'section' => esc_html__( 'Content', 'equadio' ),
					),
					'dependency' => array(
						'show_related_posts' => array( 1 ),
						'related_slider' => array( 1 ),
					),
					'std'        => 'bottom',
					'options'    => array(
						'none'    => esc_html__('None', 'equadio'),
						'bottom'  => esc_html__('Bottom', 'equadio')
					),
					'type'       => EQUADIO_THEME_FREE ? 'hidden' : 'radio',
				),
				'related_slider_space'          => array(
					'title'      => esc_html__( 'Space between slides', 'equadio' ),
					'desc'       => wp_kses_data( __( 'Space between slides in the related posts slider (in pixels)', 'equadio' ) ),
					'override' => array(
						'mode'    => 'post',
						'section' => esc_html__( 'Content', 'equadio' ),
					),
					'dependency' => array(
						'show_related_posts' => array( 1 ),
						'related_slider' => array( 1 ),
					),
					'std'        => 30,
					'min'        => 0,
					'max'        => 100,
					'show_value' => true,
					'type'       => EQUADIO_THEME_FREE ? 'hidden' : 'slider',
				),
				'posts_navigation_info'      => array(
					'title' => esc_html__( 'Post navigation', 'equadio' ),
					'desc'  => '',
					'type'  => 'info',
				),
				'posts_navigation'           => array(
					'title'   => esc_html__( 'Show post navigation', 'equadio' ),
					'desc'    => wp_kses_data( __( "Display post navigation on single post pages or load the next post automatically after the content of the current article.", 'equadio' ) ),
					'std'     => 'links',
					'options' => array(
						'none'   => esc_html__('None', 'equadio'),
						'links'  => esc_html__('Prev/Next links', 'equadio'),
						'scroll' => esc_html__('Autoload next post', 'equadio')
					),
					'type'    => EQUADIO_THEME_FREE ? 'hidden' : 'radio',
				),
				'posts_navigation_fixed'     => array(
					'title'      => esc_html__( 'Fixed post navigation', 'equadio' ),
					'desc'       => wp_kses_data( __( "Fix the position of post navigation buttons on desktop. Display them on either side of post content.", 'equadio' ) ),
					'dependency' => array(
						'posts_navigation' => array( 'links' ),
					),
					'std'        => 0,
					'type'       => EQUADIO_THEME_FREE ? 'hidden' : 'switch',
				),
				'posts_navigation_scroll_which_block'  => array(
					'title'   => esc_html__( 'Which block to load?', 'equadio' ),
					'desc'    => wp_kses_data( __( "Load only the content of the next article or the article and sidebar together.", 'equadio' ) )
								. '<br>'
								. wp_kses_data( __( "Attention! If you override sidebar position or content width on single posts (e.g. the sidebar is displayed on some posts and hidden on others), please don’t use the 'Full post' option to prevent improper content positioning.", 'equadio' ) ),
					'dependency' => array(
						'posts_navigation' => array( 'scroll' ),
					),
					'std'     => 'article',
					'options' => array(
						'article' => array(
										'title' => esc_html__( 'Only content', 'equadio' ),
										'icon'  => 'images/theme-options/posts-navigation-scroll-which-block/article.png',
									),
						'wrapper' => array(
										'title' => esc_html__( 'Full post', 'equadio' ),
										'icon'  => 'images/theme-options/posts-navigation-scroll-which-block/wrapper.png',
									),
					),
					'type'    => EQUADIO_THEME_FREE ? 'hidden' : 'choice',
				),
				'posts_navigation_scroll_hide_author'  => array(
					'title'      => esc_html__( 'Hide author bio', 'equadio' ),
					'desc'       => wp_kses_data( __( "Hide author bio after post content when infinite scroll is used.", 'equadio' ) ),
					'dependency' => array(
						'posts_navigation' => array( 'scroll' ),
					),
					'std'        => 0,
					'type'       => EQUADIO_THEME_FREE ? 'hidden' : 'switch',
				),
				'posts_navigation_scroll_hide_related'  => array(
					'title'      => esc_html__( 'Hide related posts', 'equadio' ),
					'desc'       => wp_kses_data( __( "Hide related posts after post content when infinite scroll is used.", 'equadio' ) ),
					'dependency' => array(
						'posts_navigation' => array( 'scroll' ),
					),
					'std'        => 0,
					'type'       => EQUADIO_THEME_FREE ? 'hidden' : 'switch',
				),
				'posts_navigation_scroll_hide_comments' => array(
					'title'      => esc_html__( 'Hide comments', 'equadio' ),
					'desc'       => wp_kses_data( __( "Hide comments after post content when infinite scroll is used.", 'equadio' ) ),
					'dependency' => array(
						'posts_navigation' => array( 'scroll' ),
					),
					'std'        => 1,
					'type'       => EQUADIO_THEME_FREE ? 'hidden' : 'switch',
				),
				'blog_end'                      => array(
					'type' => 'panel_end',
				),



				// 'Colors'
				//---------------------------------------------
				'panel_colors'                  => array(
					'title'    => esc_html__( 'Colors', 'equadio' ),
					'desc'     => '',
					'priority' => 300,
					'icon'     => 'icon-customizer',
					'type'     => 'section',
				),

				'color_schemes_info'            => array(
					'title'  => esc_html__( 'Color schemes', 'equadio' ),
					'desc'   => wp_kses_data( __( 'Color schemes for various parts of the site. "Inherit" means that this block uses the main color scheme from the first parameter - Site Color Scheme', 'equadio' ) ),
					'hidden' => $hide_schemes,
					'type'   => 'info',
				),
				'color_scheme'                  => array(
					'title'    => esc_html__( 'Site Color Scheme', 'equadio' ),
					'desc'     => '',
					'override' => array(
						'mode'    => 'page,cpt_team,cpt_services,cpt_dishes,cpt_competitions,cpt_rounds,cpt_matches,cpt_cars,cpt_properties,cpt_courses,cpt_portfolio',
						'section' => esc_html__( 'Colors', 'equadio' ),
					),
					'std'      => 'default',
					'options'  => array(),
					'refresh'  => false,
					'type'     => $hide_schemes ? 'hidden' : 'radio',
				),
				'header_scheme'                 => array(
					'title'    => esc_html__( 'Header Color Scheme', 'equadio' ),
					'desc'     => '',
					'override' => array(
						'mode'    => 'page,cpt_team,cpt_services,cpt_dishes,cpt_competitions,cpt_rounds,cpt_matches,cpt_cars,cpt_properties,cpt_courses,cpt_portfolio',
						'section' => esc_html__( 'Colors', 'equadio' ),
					),
					'std'      => 'inherit',
					'options'  => array(),
					'refresh'  => false,
					'type'     => $hide_schemes ? 'hidden' : 'radio',
				),
				'menu_scheme'                   => array(
					'title'    => esc_html__( 'Sidemenu Color Scheme', 'equadio' ),
					'desc'     => '',
					'override' => array(
						'mode'    => 'page,cpt_team,cpt_services,cpt_dishes,cpt_competitions,cpt_rounds,cpt_matches,cpt_cars,cpt_properties,cpt_courses,cpt_portfolio',
						'section' => esc_html__( 'Colors', 'equadio' ),
					),
					'std'      => 'inherit',
					'options'  => array(),
					'refresh'  => false,
					'type'     => 'hidden',
				),
				'sidebar_scheme'                => array(
					'title'    => esc_html__( 'Sidebar Color Scheme', 'equadio' ),
					'desc'     => '',
					'override' => array(
						'mode'    => 'page,cpt_team,cpt_services,cpt_dishes,cpt_competitions,cpt_rounds,cpt_matches,cpt_cars,cpt_properties,cpt_courses,cpt_portfolio',
						'section' => esc_html__( 'Colors', 'equadio' ),
					),
					'std'      => 'default',
					'options'  => array(),
					'refresh'  => false,
					'type'     => $hide_schemes ? 'hidden' : 'radio',
				),
				'footer_scheme'                 => array(
					'title'    => esc_html__( 'Footer Color Scheme', 'equadio' ),
					'desc'     => '',
					'override' => array(
						'mode'    => 'page,cpt_team,cpt_services,cpt_dishes,cpt_competitions,cpt_rounds,cpt_matches,cpt_cars,cpt_properties,cpt_courses,cpt_portfolio',
						'section' => esc_html__( 'Colors', 'equadio' ),
					),
					'std'      => 'dark',
					'options'  => array(),
					'refresh'  => false,
					'type'     => $hide_schemes ? 'hidden' : 'radio',
				),

				'color_scheme_editor_info'      => array(
					'title' => esc_html__( 'Color scheme editor', 'equadio' ),
					'desc'  => wp_kses_data( __( 'Select a color scheme to modify. Attention! Only sections of the site with the selected color scheme will be affected by the changes', 'equadio' ) ),
					'type'  => 'info',
				),
				'scheme_storage'                => array(
					'title'       => esc_html__( 'Color scheme editor', 'equadio' ),
					'desc'        => '',
					'std'         => '$equadio_get_scheme_storage',
					'refresh'     => false,
					'colorpicker' => 'spectrum', //'tiny',
					'type'        => 'scheme_editor',
				),

				// Internal options.
				// Attention! Don't change any options in the section below!
				// Huge priority is used to call render this elements after all options!
				'reset_options'                 => array(
					'title'    => '',
					'desc'     => '',
					'std'      => '0',
					'priority' => 10000,
					'type'     => 'hidden',
				),

				'last_option'                   => array(     // Need to manually call action to include Tiny MCE scripts
					'title' => '',
					'desc'  => '',
					'std'   => 1,
					'type'  => 'hidden',
				),

			)
		);


		// Add parameters for "Caregory", "Tag", "Author", "Search" to Theme Options
		equadio_storage_set_array_before( 'options', 'blog_single', equadio_options_get_list_blog_options( 'category', esc_html__( 'Category', 'equadio' ) ) );
		equadio_storage_set_array_before( 'options', 'blog_single', equadio_options_get_list_blog_options( 'tag', esc_html__( 'Tag', 'equadio' ) ) );
		equadio_storage_set_array_before( 'options', 'blog_single', equadio_options_get_list_blog_options( 'author', esc_html__( 'Author', 'equadio' ) ) );
		equadio_storage_set_array_before( 'options', 'blog_single', equadio_options_get_list_blog_options( 'search', esc_html__( 'Search', 'equadio' ) ) );


		// Prepare panel 'Fonts'
		// -------------------------------------------------------------
		$fonts = array(

			// 'Fonts'
			//---------------------------------------------
			'fonts'             => array(
				'title'    => esc_html__( 'Typography', 'equadio' ),
				'desc'     => '',
				'priority' => 200,
				'icon'     => 'icon-text',
				'type'     => 'panel',
			),

			// Fonts - Load_fonts
			'load_fonts'        => array(
				'title' => esc_html__( 'Load fonts', 'equadio' ),
				'desc'  => wp_kses_data( __( 'Specify fonts to load when theme start. You can use them in the base theme elements: headers, text, menu, links, input fields, etc.', 'equadio' ) )
						. wp_kses_data( __( 'Press "Refresh" button to reload preview area after the all fonts are changed', 'equadio' ) ),
				'type'  => 'section',
			),
			'load_fonts_info'   => array(
				'title' => esc_html__( 'Load fonts', 'equadio' ),
				'desc'  => '',
				'type'  => 'info',
			),
			'load_fonts_subset' => array(
				'title'   => esc_html__( 'Google fonts subsets', 'equadio' ),
				'desc'    => wp_kses_data( __( 'Specify a comma separated list of subsets to be loaded from Google fonts.', 'equadio' ) )
						. wp_kses_data( __( 'Permitted subsets include: latin,latin-ext,cyrillic,cyrillic-ext,greek,greek-ext,vietnamese', 'equadio' ) ),
				'class'   => 'equadio_column-1_3 equadio_new_row',
				'refresh' => false,
				'std'     => '$equadio_get_load_fonts_subset',
				'type'    => 'text',
			),
		);

		for ( $i = 1; $i <= equadio_get_theme_setting( 'max_load_fonts' ); $i++ ) {
			if ( equadio_get_value_gp( 'page' ) != 'theme_options' ) {
				$fonts[ "load_fonts-{$i}-info" ] = array(
					// Translators: Add font's number - 'Font 1', 'Font 2', etc
					'title' => esc_html( sprintf( __( 'Font %s', 'equadio' ), $i ) ),
					'desc'  => '',
					'type'  => 'info',
				);
			}
			$fonts[ "load_fonts-{$i}-name" ]   = array(
				'title'   => esc_html__( 'Font name', 'equadio' ),
				'desc'    => '',
				'class'   => 'equadio_column-1_3 equadio_new_row',
				'refresh' => false,
				'std'     => '$equadio_get_load_fonts_option',
				'type'    => 'text',
			);
			$fonts[ "load_fonts-{$i}-family" ] = array(
				'title'   => esc_html__( 'Font family', 'equadio' ),
				'desc'    => 1 == $i
							? wp_kses_data( __( 'Select a font family to be used if the preferred font is not available', 'equadio' ) )
							: '',
				'class'   => 'equadio_column-1_3',
				'refresh' => false,
				'std'     => '$equadio_get_load_fonts_option',
				'options' => array(
					'inherit'    => esc_html__( 'Inherit', 'equadio' ),
					'serif'      => esc_html__( 'serif', 'equadio' ),
					'sans-serif' => esc_html__( 'sans-serif', 'equadio' ),
					'monospace'  => esc_html__( 'monospace', 'equadio' ),
					'cursive'    => esc_html__( 'cursive', 'equadio' ),
					'fantasy'    => esc_html__( 'fantasy', 'equadio' ),
				),
				'type'    => 'select',
			);
			$fonts[ "load_fonts-{$i}-styles" ] = array(
				'title'   => esc_html__( 'Font styles', 'equadio' ),
				'desc'    => 1 == $i
							? wp_kses_data( __( 'Font styles used only for Google fonts. This is a comma separated list of the font weight and style options. For example: 400,400italic,700', 'equadio' ) )
								. '<br>'
								. wp_kses_data( __( 'Attention! Each weight and style option increases download size! Specify only those weight and style options that you plan on using.', 'equadio' ) )
							: '',
				'class'   => 'equadio_column-1_3',
				'refresh' => false,
				'std'     => '$equadio_get_load_fonts_option',
				'type'    => 'text',
			);
		}
		$fonts['load_fonts_end'] = array(
			'type' => 'section_end',
		);

		// Fonts - H1..6, P, Info, Menu, etc.
		$theme_fonts = equadio_get_theme_fonts();
		foreach ( $theme_fonts as $tag => $v ) {
			$fonts[ "{$tag}_font_section" ] = array(
				'title' => ! empty( $v['title'] )
								? $v['title']
								// Translators: Add tag's name to make title 'H1 settings', 'P settings', etc.
								: esc_html( sprintf( __( '%s settings', 'equadio' ), $tag ) ),
				'desc'  => ! empty( $v['description'] )
								? $v['description']
								// Translators: Add tag's name to make description
								: wp_kses_data( sprintf( __( 'Font settings for the "%s" tag.', 'equadio' ), $tag ) ),
				'type'  => 'section',
			);
			$fonts[ "{$tag}_font_info" ] = array(
				'title' => ! empty( $v['title'] )
								? $v['title']
								// Translators: Add tag's name to make title 'H1 settings', 'P settings', etc.
								: esc_html( sprintf( __( '%s settings', 'equadio' ), $tag ) ),
				'desc'  => ! empty( $v['description'] )
								? $v['description']
								: '',
				'type'  => 'info',
			);
			foreach ( $v as $css_prop => $css_value ) {
				if ( in_array( $css_prop, array( 'title', 'description' ) ) ) {
					continue;
				}
				// Skip property 'text-decoration' for the main text
				if ( 'text-decoration' == $css_prop && 'p' == $tag ) {
					continue;
				}

				$options    = '';
				$type       = 'text';
				$load_order = 1;
				$title      = ucfirst( str_replace( '-', ' ', $css_prop ) );
				if ( 'font-family' == $css_prop ) {
					$type       = 'select';
					$options    = array();
					$load_order = 2;        // Load this option's value after all options are loaded (use option 'load_fonts' to build fonts list)
				} elseif ( 'font-weight' == $css_prop ) {
					$type    = 'select';
					$options = array(
						'inherit' => esc_html__( 'Inherit', 'equadio' ),
						'100'     => esc_html__( '100 (Light)', 'equadio' ),
						'200'     => esc_html__( '200 (Light)', 'equadio' ),
						'300'     => esc_html__( '300 (Thin)', 'equadio' ),
						'400'     => esc_html__( '400 (Normal)', 'equadio' ),
						'500'     => esc_html__( '500 (Semibold)', 'equadio' ),
						'600'     => esc_html__( '600 (Semibold)', 'equadio' ),
						'700'     => esc_html__( '700 (Bold)', 'equadio' ),
						'800'     => esc_html__( '800 (Black)', 'equadio' ),
						'900'     => esc_html__( '900 (Black)', 'equadio' ),
					);
				} elseif ( 'font-style' == $css_prop ) {
					$type    = 'select';
					$options = array(
						'inherit' => esc_html__( 'Inherit', 'equadio' ),
						'normal'  => esc_html__( 'Normal', 'equadio' ),
						'italic'  => esc_html__( 'Italic', 'equadio' ),
					);
				} elseif ( 'text-decoration' == $css_prop ) {
					$type    = 'select';
					$options = array(
						'inherit'      => esc_html__( 'Inherit', 'equadio' ),
						'none'         => esc_html__( 'None', 'equadio' ),
						'underline'    => esc_html__( 'Underline', 'equadio' ),
						'overline'     => esc_html__( 'Overline', 'equadio' ),
						'line-through' => esc_html__( 'Line-through', 'equadio' ),
					);
				} elseif ( 'text-transform' == $css_prop ) {
					$type    = 'select';
					$options = array(
						'inherit'    => esc_html__( 'Inherit', 'equadio' ),
						'none'       => esc_html__( 'None', 'equadio' ),
						'uppercase'  => esc_html__( 'Uppercase', 'equadio' ),
						'lowercase'  => esc_html__( 'Lowercase', 'equadio' ),
						'capitalize' => esc_html__( 'Capitalize', 'equadio' ),
					);
				}
				$fonts[ "{$tag}_{$css_prop}" ] = array(
					'title'      => $title,
					'desc'       => '',
					'refresh'    => false,
					'load_order' => $load_order,
					'std'        => '$equadio_get_theme_fonts_option',
					'options'    => $options,
					'type'       => $type,
				);
			}

			$fonts[ "{$tag}_section_end" ] = array(
				'type' => 'section_end',
			);
		}

		$fonts['fonts_end'] = array(
			'type' => 'panel_end',
		);

		// Add fonts parameters to Theme Options
		equadio_storage_set_array_before( 'options', 'panel_colors', $fonts );

		// Add Header Video if WP version < 4.7
		// -----------------------------------------------------
		if ( ! function_exists( 'get_header_video_url' ) ) {
			equadio_storage_set_array_after(
				'options', 'header_image_override', 'header_video', array(
					'title'    => esc_html__( 'Header video', 'equadio' ),
					'desc'     => wp_kses_data( __( 'Select video to use it as background for the header', 'equadio' ) ),
					'override' => array(
						'mode'    => 'page',
						'section' => esc_html__( 'Header', 'equadio' ),
					),
					'std'      => '',
					'type'     => 'video',
				)
			);
		}

		// Add option 'logo' if WP version < 4.5
		// or 'custom_logo' if current page is not 'Customize'
		// ------------------------------------------------------
		if ( ! function_exists( 'the_custom_logo' ) || ! equadio_check_url( 'customize.php' ) ) {
			equadio_storage_set_array_before(
				'options', 'logo_retina', function_exists( 'the_custom_logo' ) ? 'custom_logo' : 'logo', array(
					'title'    => esc_html__( 'Logo', 'equadio' ),
					'desc'     => wp_kses_data( __( 'Select or upload the site logo', 'equadio' ) ),
					'priority' => 60,
					'std'      => '',
					'qsetup'   => esc_html__( 'General', 'equadio' ),
					'type'     => 'image',
				)
			);
		}

	}
}


// Returns a list of options that can be overridden for categories, tags, archives, author posts, search, etc.
if ( ! function_exists( 'equadio_options_get_list_blog_options' ) ) {
	function equadio_options_get_list_blog_options( $mode, $title = '' ) {
		if ( empty( $title ) ) {
			$title = ucfirst( $mode );
		}
		return apply_filters( 'equadio_filter_get_list_blog_options', array(
				"blog_general_{$mode}"           => array(
					'title' => $title,
					// Translators: Add mode name to the description
					'desc'  => wp_kses_data( sprintf( __( "Style and components of the %s posts page", 'equadio' ), $title ) ),
					'type'  => 'section',
				),
				"blog_general_info_{$mode}"      => array(
					// Translators: Add mode name to the title
					'title'  => wp_kses_data( sprintf( __( "%s posts page", 'equadio' ), $title ) ),
					// Translators: Add mode name to the description
					'desc'   => wp_kses_data( sprintf( __( 'Customize %s page: post layout, header and footer styles, sidebar position and widgets, etc.', 'equadio' ), $title ) ),
					'type'   => 'info',
				),
				"blog_style_{$mode}"             => array(
					'title'      => esc_html__( 'Blog style', 'equadio' ),
					'desc'       => '',
					'std'        => 'classic-masonry_3',
					'options'    => array(),
					'type'       => 'choice',
				),
				"first_post_large_{$mode}"       => array(
					'title'      => esc_html__( 'Large first post', 'equadio' ),
					'desc'       => wp_kses_data( __( 'Make your first post stand out by making it bigger', 'equadio' ) ),
					'std'        => 0,
					'options'    => equadio_get_list_yesno( true ),
					'dependency' => array(
						'blog_style_{$mode}' => array( 'classic', 'masonry' ),
					),
					'type'       => 'radio',
				),
				"blog_content_{$mode}"           => array(
					'title'      => esc_html__( 'Posts content', 'equadio' ),
					'desc'       => wp_kses_data( __( 'Display either post excerpts or the full post content', 'equadio' ) ),
					'std'        => 'excerpt',
					'dependency' => array(
						"blog_style_{$mode}" => array( 'excerpt' ),
					),
					'options'    => equadio_get_list_blog_contents( true ),
					'type'       => 'radio',
				),
				"excerpt_length_{$mode}"         => array(
					'title'      => esc_html__( 'Excerpt length', 'equadio' ),
					'desc'       => wp_kses_data( __( 'Length (in words) to generate excerpt from the post content. Attention! If the post excerpt is explicitly specified - it appears unchanged', 'equadio' ) ),
					'dependency' => array(
						"blog_style_{$mode}"   => array( 'excerpt' ),
						"blog_content_{$mode}" => array( 'excerpt' ),
					),
					'std'        => 35,
					'type'       => 'text',
				),
				"meta_parts_{$mode}"             => array(
					'title'      => esc_html__( 'Post meta', 'equadio' ),
					'desc'       => wp_kses_data( __( "Set up post meta parts to show in the blog archive. Post counters and Share Links are available only if plugin ThemeREX Addons is active", 'equadio' ) )
								. '<br>'
								. wp_kses_data( __( '<b>Tip:</b> Drag items to change their order.', 'equadio' ) ),
					'dir'        => 'vertical',
					'sortable'   => true,
					'std'        => 'categories=0|date=1|modified=0|views=0|likes=0|comments=1|author=0|share=0|edit=0',
					'options'    => equadio_get_list_meta_parts(),
					'type'       => EQUADIO_THEME_FREE ? 'hidden' : 'checklist',
				),
				"blog_pagination_{$mode}"        => array(
					'title'      => esc_html__( 'Pagination style', 'equadio' ),
					'desc'       => wp_kses_data( __( 'Show Older/Newest posts or Page numbers below the posts list', 'equadio' ) ),
					'std'        => 'pages',
					'options'    => equadio_get_list_blog_paginations( true ),
					'type'       => 'choice',
				),
				"blog_animation_{$mode}"         => array(
					'title'      => esc_html__( 'Post animation', 'equadio' ),
					'desc'       => wp_kses_data( __( "Select post animation for the archive page. Attention! Do not use any animation on pages with the 'wheel to the anchor' behaviour!", 'equadio' ) ),
					'std'        => 'none',
					'options'    => array(),
					'type'       => EQUADIO_THEME_FREE ? 'hidden' : 'select',
				),
				"open_full_post_in_blog_{$mode}" => array(
					'title'      => esc_html__( 'Open full post in blog', 'equadio' ),
					'desc'       => wp_kses_data( __( 'Allow to open the full version of the post directly in the posts feed. Attention! Applies only to 1 column layouts!', 'equadio' ) ),
					'std'        => 0,
					'options'    => equadio_get_list_checkbox_values( true ),
					'type'       => 'radio',
				),

				"blog_header_info_{$mode}"       => array(
					'title' => esc_html__( 'Header', 'equadio' ),
					'desc'  => '',
					'type'  => 'info',
				),
				"header_type_{$mode}"            => array(
					'title'    => esc_html__( 'Header style', 'equadio' ),
					'desc'     => wp_kses_data( __( 'Choose whether to use the default header or header Layouts (available only if the ThemeREX Addons is activated)', 'equadio' ) ),
					'std'      => 'inherit',
					'options'  => equadio_get_list_header_footer_types( true ),
					'type'     => EQUADIO_THEME_FREE || ! equadio_exists_trx_addons() ? 'hidden' : 'radio',
				),
				"header_style_{$mode}"           => array(
					'title'      => esc_html__( 'Select custom layout', 'equadio' ),
					'desc'       => wp_kses( __( 'Select custom header from Layouts Builder', 'equadio' ), 'equadio_kses_content' ),
					'dependency' => array(
						"header_type_{$mode}" => array( 'custom' ),
					),
					'std'        => 'inherit',
					'options'    => array(),
					'type'       => 'select',
				),
				"header_position_{$mode}"        => array(
					'title'    => esc_html__( 'Header position', 'equadio' ),
					'desc'     => wp_kses_data( __( 'Select position to display the site header', 'equadio' ) ),
					'std'      => 'inherit',
					'options'  => array(),
					'type'     => EQUADIO_THEME_FREE ? 'hidden' : 'radio',
				),
				"header_fullheight_{$mode}"      => array(
					'title'    => esc_html__( 'Header fullheight', 'equadio' ),
					'desc'     => wp_kses_data( __( 'Stretch header area to fill the entire screen. Used only if the header has a background image', 'equadio' ) ),
					'std'      => 'inherit',
					'options'  => equadio_get_list_checkbox_values( true ),
					'type'     => EQUADIO_THEME_FREE ? 'hidden' : 'radio',
				),
				"header_wide_{$mode}"            => array(
					'title'      => esc_html__( 'Header fullwidth', 'equadio' ),
					'desc'       => wp_kses_data( __( 'Do you want to stretch the header widgets area to the entire window width?', 'equadio' ) ),
					'dependency' => array(
						"header_type_{$mode}" => array( 'default' ),
					),
					'std'      => 'inherit',
					'options'  => equadio_get_list_checkbox_values( true ),
					'type'     => EQUADIO_THEME_FREE ? 'hidden' : 'radio',
				),

				"blog_sidebar_info_{$mode}"      => array(
					'title' => esc_html__( 'Sidebar', 'equadio' ),
					'desc'  => '',
					'type'  => 'info',
				),
				"sidebar_position_{$mode}"       => array(
					'title'   => esc_html__( 'Sidebar position', 'equadio' ),
					'desc'    => wp_kses_data( __( 'Select position to show sidebar', 'equadio' ) ),
					'std'     => 'hide',
					'options' => array(),
					'type'    => 'choice',
				),
				"sidebar_type_{$mode}"           => array(
					'title'    => esc_html__( 'Sidebar style', 'equadio' ),
					'desc'     => wp_kses_data( __( 'Choose whether to use the default sidebar or sidebar Layouts (available only if the ThemeREX Addons is activated)', 'equadio' ) ),
					'dependency' => array(
						"sidebar_position_{$mode}" => array( '^hide' ),
					),
					'std'      => 'default',
					'options'  => equadio_get_list_header_footer_types(),
					'type'     => EQUADIO_THEME_FREE || ! equadio_exists_trx_addons() ? 'hidden' : 'radio',
				),
				"sidebar_style_{$mode}"          => array(
					'title'      => esc_html__( 'Select custom layout', 'equadio' ),
					'desc'       => wp_kses( __( 'Select custom sidebar from Layouts Builder', 'equadio' ), 'equadio_kses_content' ),
					'dependency' => array(
						"sidebar_position_{$mode}" => array( '^hide' ),
						"sidebar_type_{$mode}"     => array( 'custom' ),
					),
					'std'        => 'sidebar-custom-sidebar',
					'options'    => array(),
					'type'       => 'select',
				),
				"sidebar_widgets_{$mode}"        => array(
					'title'      => esc_html__( 'Sidebar widgets', 'equadio' ),
					'desc'       => wp_kses_data( __( 'Select default widgets to show in the sidebar', 'equadio' ) ),
					'dependency' => array(
						"sidebar_position_{$mode}" => array( '^hide' ),
						"sidebar_type_{$mode}"     => array( 'default' ),
					),
					'std'        => 'sidebar_widgets',
					'options'    => array(),
					'type'       => 'select',
				),
				"expand_content_{$mode}"         => array(
					'title'   => esc_html__( 'Content width', 'equadio' ),
					'desc'    => wp_kses_data( __( 'Content width if the sidebar is hidden', 'equadio' ) ),
					'refresh' => false,
					'std'     => 'inherit',
					'options' => equadio_get_list_expand_content( true ),
					'type'    => EQUADIO_THEME_FREE ? 'hidden' : 'choice',
				),

				"blog_widgets_info_{$mode}"      => array(
					'title' => esc_html__( 'Additional widgets', 'equadio' ),
					'desc'  => '',
					'type'  => EQUADIO_THEME_FREE ? 'hidden' : 'info',
				),
				"widgets_above_page_{$mode}"     => array(
					'title'   => esc_html__( 'Widgets at the top of the page', 'equadio' ),
					'desc'    => wp_kses_data( __( 'Select widgets to show at the top of the page (above content and sidebar)', 'equadio' ) ),
					'std'     => 'hide',
					'options' => array(),
					'type'    => EQUADIO_THEME_FREE ? 'hidden' : 'select',
				),
				"widgets_above_content_{$mode}"  => array(
					'title'   => esc_html__( 'Widgets above the content', 'equadio' ),
					'desc'    => wp_kses_data( __( 'Select widgets to show at the beginning of the content area', 'equadio' ) ),
					'std'     => 'hide',
					'options' => array(),
					'type'    => EQUADIO_THEME_FREE ? 'hidden' : 'select',
				),
				"widgets_below_content_{$mode}"  => array(
					'title'   => esc_html__( 'Widgets below the content', 'equadio' ),
					'desc'    => wp_kses_data( __( 'Select widgets to show at the ending of the content area', 'equadio' ) ),
					'std'     => 'hide',
					'options' => array(),
					'type'    => EQUADIO_THEME_FREE ? 'hidden' : 'select',
				),
				"widgets_below_page_{$mode}"     => array(
					'title'   => esc_html__( 'Widgets at the bottom of the page', 'equadio' ),
					'desc'    => wp_kses_data( __( 'Select widgets to show at the bottom of the page (below content and sidebar)', 'equadio' ) ),
					'std'     => 'hide',
					'options' => array(),
					'type'    => EQUADIO_THEME_FREE ? 'hidden' : 'select',
				),
			), $mode, $title
		);
	}
}


// Returns a list of options that can be overridden for CPT
if ( ! function_exists( 'equadio_options_get_list_cpt_options' ) ) {
	function equadio_options_get_list_cpt_options( $cpt, $title = '' ) {
		if ( empty( $title ) ) {
			$title = ucfirst( $cpt );
		}
		return apply_filters( 'equadio_filter_get_list_cpt_options',
								array_merge(
									equadio_options_get_list_cpt_options_body( $cpt, $title ),
									equadio_options_get_list_cpt_options_header( $cpt, $title ),
									equadio_options_get_list_cpt_options_sidebar( $cpt, $title ),
									equadio_options_get_list_cpt_options_sidebar_single( $cpt, $title ),
									equadio_options_get_list_cpt_options_footer( $cpt, $title ),
									equadio_options_get_list_cpt_options_widgets( $cpt, $title )
								),
								$cpt,
								$title
							);
	}
}


// Returns a list of options that can be overridden for CPT. Section 'Content'
if ( ! function_exists( 'equadio_options_get_list_cpt_options_body' ) ) {
	function equadio_options_get_list_cpt_options_body( $cpt, $title = '' ) {
		if ( empty( $title ) ) {
			$title = ucfirst( $cpt );
		}
		return apply_filters( 'equadio_filter_get_list_cpt_options_body', array(
				"content_info_{$cpt}"           => array(
					'title' => esc_html__( 'Body style', 'equadio' ),
					// Translators: Add CPT name to the description
					'desc'  => wp_kses_data( sprintf( __( 'Select body style to display %s list and single posts', 'equadio' ), $title ) ),
					'type'  => 'info',
				),
				"body_style_{$cpt}"             => array(
					'title'    => esc_html__( 'Body style', 'equadio' ),
					'desc'     => wp_kses_data( __( 'Select width of the body content', 'equadio' ) ),
					'std'      => 'inherit',
					'options'  => equadio_get_list_body_styles( true ),
					'type'     => 'choice',
				),
				"boxed_bg_image_{$cpt}"         => array(
					'title'      => esc_html__( 'Boxed bg image', 'equadio' ),
					'desc'       => wp_kses_data( __( 'Select or upload image, used as background in the boxed body', 'equadio' ) ),
					'dependency' => array(
						"body_style_{$cpt}" => array( 'boxed' ),
					),
					'std'        => 'inherit',
					'type'       => 'image',
				),
			), $cpt, $title
		);
	}
}


// Returns a list of options that can be overridden for CPT. Section 'Header'
if ( ! function_exists( 'equadio_options_get_list_cpt_options_header' ) ) {
	function equadio_options_get_list_cpt_options_header( $cpt, $title = '' ) {
		if ( empty( $title ) ) {
			$title = ucfirst( $cpt );
		}
		return apply_filters( 'equadio_filter_get_list_cpt_options_header', array(
				"header_info_{$cpt}"            => array(
					'title' => esc_html__( 'Header', 'equadio' ),
					// Translators: Add CPT name to the description
					'desc'  => wp_kses_data( sprintf( __( 'Set up header parameters to display %s list and single posts', 'equadio' ), $title ) ),
					'type'  => 'info',
				),
				"header_type_{$cpt}"            => array(
					'title'   => esc_html__( 'Header style', 'equadio' ),
					'desc'    => wp_kses_data( __( 'Choose whether to use the default header or header Layouts (available only if the ThemeREX Addons is activated)', 'equadio' ) ),
					'std'     => 'inherit',
					'options' => equadio_get_list_header_footer_types( true ),
					'type'    => EQUADIO_THEME_FREE ? 'hidden' : 'radio',
				),
				"header_style_{$cpt}"           => array(
					'title'      => esc_html__( 'Select custom layout', 'equadio' ),
					// Translators: Add CPT name to the description
					'desc'       => wp_kses_data( sprintf( __( 'Select custom layout to display the site header on the %s pages', 'equadio' ), $title ) ),
					'dependency' => array(
						"header_type_{$cpt}" => array( 'custom' ),
					),
					'std'        => 'inherit',
					'options'    => array(),
					'type'       => EQUADIO_THEME_FREE ? 'hidden' : 'select',
				),
				"header_position_{$cpt}"        => array(
					'title'   => esc_html__( 'Header position', 'equadio' ),
					// Translators: Add CPT name to the description
					'desc'    => wp_kses_data( sprintf( __( 'Select position to display the site header on the %s pages', 'equadio' ), $title ) ),
					'std'     => 'inherit',
					'options' => array(),
					'type'    => EQUADIO_THEME_FREE ? 'hidden' : 'radio',
				),
				"header_image_override_{$cpt}"  => array(
					'title'   => esc_html__( 'Header image override', 'equadio' ),
					// Translators: Add CPT name to the description
					'desc'    => wp_kses_data( sprintf( __( "Allow overriding the header image with a featured image of %s.", 'equadio' ), $title ) ),
					'std'     => 'inherit',
					'options' => equadio_get_list_yesno( true ),
					'type'    => EQUADIO_THEME_FREE ? 'hidden' : 'radio',
				),
				"header_widgets_{$cpt}"         => array(
					'title'   => esc_html__( 'Header widgets', 'equadio' ),
					// Translators: Add CPT name to the description
					'desc'    => wp_kses_data( sprintf( __( 'Select set of widgets to show in the header on the %s pages', 'equadio' ), $title ) ),
					'std'     => 'hide',
					'options' => array(),
					'type'    => 'select',
				),
			), $cpt, $title
		);
	}
}


// Returns a list of options that can be overridden for CPT. Section 'Sidebar'
if ( ! function_exists( 'equadio_options_get_list_cpt_options_sidebar' ) ) {
	function equadio_options_get_list_cpt_options_sidebar( $cpt, $title = '' ) {
		if ( empty( $title ) ) {
			$title = ucfirst( $cpt );
		}
		return apply_filters( 'equadio_filter_get_list_cpt_options_sidebar', array(
				"sidebar_info_{$cpt}"           => array(
					'title' => wp_kses_data( sprintf( __( 'Sidebar on the %s list', 'equadio' ), $title ) ),
					// Translators: Add CPT name to the description
					'desc'  => wp_kses_data( sprintf( __( 'Set up sidebar parameters to display %s list and single posts', 'equadio' ), $title ) ),
					'type'  => 'info',
				),
				"sidebar_position_{$cpt}"       => array(
					// Translators: Add CPT name to the title
					'title'   => sprintf( __( 'Sidebar position on the %s list', 'equadio' ), $title ),
					// Translators: Add CPT name to the description
					'desc'    => wp_kses_data( sprintf( __( 'Select sidebar position for the %s list', 'equadio' ), $title ) ),
					'std'     => 'right',
					'options' => array(),
					'type'    => 'choice',
				),
				"sidebar_position_ss_{$cpt}"    => array(
					// Translators: Add CPT name to the title
					'title'    => sprintf( __( 'Sidebar position on the %s list on the small screen', 'equadio' ), $title ),
					'desc'     => wp_kses_data( __( 'Select a position for the sidebar on the small screen: above the content, below or on a sliding side-panel.', 'equadio' ) ),
					'std'      => 'below',
					'dependency' => array(
						"sidebar_position_{$cpt}" => array( '^hide' ),
					),
					'options'  => array(),
					'type'     => 'radio',
				),
				"sidebar_type_{$cpt}"           => array(
					// Translators: Add CPT name to the title
					'title'    => sprintf( __( 'Sidebar style on the %s list', 'equadio' ), $title ),
					'desc'     => wp_kses_data( __( 'Choose whether to use the default sidebar or sidebar Layouts (available only if the ThemeREX Addons is activated)', 'equadio' ) ),
					'dependency' => array(
						"sidebar_position_{$cpt}" => array( '^hide' ),
					),
					'std'      => 'default',
					'options'  => equadio_get_list_header_footer_types(),
					'type'     => EQUADIO_THEME_FREE || ! equadio_exists_trx_addons() ? 'hidden' : 'radio',
				),
				"sidebar_style_{$cpt}"          => array(
					'title'      => esc_html__( 'Select custom layout', 'equadio' ),
					'desc'       => wp_kses( __( 'Select custom sidebar from Layouts Builder', 'equadio' ), 'equadio_kses_content' ),
					'dependency' => array(
						"sidebar_position_{$cpt}" => array( '^hide' ),
						"sidebar_type_{$cpt}"     => array( 'custom' ),
					),
					'std'        => 'sidebar-custom-sidebar',
					'options'    => array(),
					'type'       => 'select',
				),
				"sidebar_widgets_{$cpt}"        => array(
					// Translators: Add CPT name to the title
					'title'      => sprintf( __( 'Sidebar widgets on the %s list', 'equadio' ), $title ),
					// Translators: Add CPT name to the description
					'desc'       => wp_kses_data( sprintf( __( 'Select sidebar to show on the %s list', 'equadio' ), $title ) ),
					'dependency' => array(
						"sidebar_position_{$cpt}" => array( '^hide' ),
						"sidebar_type_{$cpt}"     => array( 'default' ),
					),
					'std'        => 'hide',
					'options'    => array(),
					'type'       => 'select',
				),
				"expand_content_{$cpt}"         => array(
					'title'   => esc_html__( 'Content width', 'equadio' ),
					'desc'    => wp_kses_data( __( 'Content width if the sidebar is hidden', 'equadio' ) ),
					'refresh' => false,
					'std'     => 'inherit',
					'options' => equadio_get_list_expand_content( true ),
					'type'    => EQUADIO_THEME_FREE ? 'hidden' : 'choice',
				),
			), $cpt, $title
		);
	}
}


// Returns a list of options that can be overridden for CPT. Section 'Sidebar Single'
if ( ! function_exists( 'equadio_options_get_list_cpt_options_sidebar_single' ) ) {
	function equadio_options_get_list_cpt_options_sidebar_single( $cpt, $title = '' ) {
		if ( empty( $title ) ) {
			$title = ucfirst( $cpt );
		}
		return apply_filters( 'equadio_filter_get_list_cpt_options_sidebar_single', array(
				"sidebar_single_info_{$cpt}"           => array(
					'title' => wp_kses_data( sprintf( __( 'Sidebar on the single %s', 'equadio' ), $title ) ),
					// Translators: Add CPT name to the description
					'desc'  => wp_kses_data( sprintf( __( 'Set up sidebar parameters to display single %s', 'equadio' ), $title ) ),
					'type'  => 'info',
				),
				"sidebar_position_single_{$cpt}"       => array(
					'title'   => esc_html__( 'Sidebar position', 'equadio' ),
					// Translators: Add CPT name to the description
					'desc'    => wp_kses_data( sprintf( __( 'Select sidebar position for the single %s', 'equadio' ), $title ) ),
					'std'     => 'left',
					'options' => array(),
					'type'    => 'choice',
				),
				"sidebar_position_ss_single_{$cpt}"    => array(
					'title'    => esc_html__( 'Sidebar position on the small screen', 'equadio' ),
					'desc'     => wp_kses_data( __( 'Select a position for the sidebar on the small screen: above the content, below or on a sliding side-panel.', 'equadio' ) ),
					'dependency' => array(
						"sidebar_position_single_{$cpt}" => array( '^hide' ),
					),
					'std'      => 'below',
					'options'  => array(),
					'type'     => 'radio',
				),
				"sidebar_type_single_{$cpt}"           => array(
					// Translators: Add CPT name to the title
					'title'    => esc_html__( 'Sidebar style', 'equadio' ),
					'desc'     => wp_kses_data( __( 'Choose whether to use the default sidebar or sidebar Layouts (available only if the ThemeREX Addons is activated)', 'equadio' ) ),
					'dependency' => array(
						"sidebar_position_single_{$cpt}" => array( '^hide' ),
					),
					'std'      => 'default',
					'options'  => equadio_get_list_header_footer_types(),
					'type'     => EQUADIO_THEME_FREE || ! equadio_exists_trx_addons() ? 'hidden' : 'radio',
				),
				"sidebar_style_single_{$cpt}"          => array(
					'title'      => esc_html__( 'Select custom layout', 'equadio' ),
					'desc'       => wp_kses( __( 'Select custom sidebar from Layouts Builder', 'equadio' ), 'equadio_kses_content' ),
					'dependency' => array(
						"sidebar_position_single_{$cpt}" => array( '^hide' ),
						"sidebar_type_single_{$cpt}"     => array( 'custom' ),
					),
					'std'        => 'sidebar-custom-sidebar',
					'options'    => array(),
					'type'       => 'select',
				),
				"sidebar_widgets_single_{$cpt}"        => array(
					'title'      => esc_html__( 'Sidebar widgets', 'equadio' ),
					// Translators: Add CPT name to the description
					'desc'       => wp_kses_data( sprintf( __( 'Select sidebar widgets for the single %s', 'equadio' ), $title ) ),
					'dependency' => array(
						"sidebar_position_single_{$cpt}" => array( '^hide' ),
						"sidebar_type_single_{$cpt}"     => array( 'default' ),
					),
					'std'        => 'hide',
					'options'    => array(),
					'type'       => 'select',
				),
				"expand_content_single_{$cpt}"         => array(
					'title'   => esc_html__( 'Content width', 'equadio' ),
					'desc'    => wp_kses_data( __( 'Content width on the single post if the sidebar is hidden', 'equadio' ) ),
					'refresh' => false,
					'std'     => 'inherit',
					'options' => equadio_get_list_expand_content( true ),
					'type'    => EQUADIO_THEME_FREE ? 'hidden' : 'choice',
				),
			), $cpt, $title
		);
	}
}


// Returns a list of options that can be overridden for CPT. Section 'Footer'
if ( ! function_exists( 'equadio_options_get_list_cpt_options_footer' ) ) {
	function equadio_options_get_list_cpt_options_footer( $cpt, $title = '' ) {
		if ( empty( $title ) ) {
			$title = ucfirst( $cpt );
		}
		return apply_filters( 'equadio_filter_get_list_cpt_options_footer', array(
				"footer_info_{$cpt}"            => array(
					'title' => esc_html__( 'Footer', 'equadio' ),
					// Translators: Add CPT name to the description
					'desc'  => wp_kses_data( sprintf( __( 'Set up footer parameters to display %s list and single posts', 'equadio' ), $title ) ),
					'type'  => 'info',
				),
				"footer_type_{$cpt}"            => array(
					'title'   => esc_html__( 'Footer style', 'equadio' ),
					'desc'    => wp_kses_data( __( 'Choose whether to use the default footer or footer Layouts (available only if the ThemeREX Addons is activated)', 'equadio' ) ),
					'std'     => 'inherit',
					'options' => equadio_get_list_header_footer_types( true ),
					'type'    => EQUADIO_THEME_FREE ? 'hidden' : 'radio',
				),
				"footer_style_{$cpt}"           => array(
					'title'      => esc_html__( 'Select custom layout', 'equadio' ),
					'desc'       => wp_kses_data( __( 'Select custom layout to display the site footer', 'equadio' ) ),
					'std'        => 'inherit',
					'dependency' => array(
						"footer_type_{$cpt}" => array( 'custom' ),
					),
					'options'    => array(),
					'type'       => EQUADIO_THEME_FREE ? 'hidden' : 'select',
				),
				"footer_widgets_{$cpt}"         => array(
					'title'      => esc_html__( 'Footer widgets', 'equadio' ),
					'desc'       => wp_kses_data( __( 'Select set of widgets to show in the footer', 'equadio' ) ),
					'dependency' => array(
						"footer_type_{$cpt}" => array( 'default' ),
					),
					'std'        => 'footer_widgets',
					'options'    => array(),
					'type'       => 'select',
				),
				"footer_columns_{$cpt}"         => array(
					'title'      => esc_html__( 'Footer columns', 'equadio' ),
					'desc'       => wp_kses_data( __( 'Select number columns to show widgets in the footer. If 0 - autodetect by the widgets count', 'equadio' ) ),
					'dependency' => array(
						"footer_type_{$cpt}"    => array( 'default' ),
						"footer_widgets_{$cpt}" => array( '^hide' ),
					),
					'std'        => 0,
					'options'    => equadio_get_list_range( 0, 6 ),
					'type'       => 'select',
				),
				"footer_wide_{$cpt}"            => array(
					'title'      => esc_html__( 'Footer fullwidth', 'equadio' ),
					'desc'       => wp_kses_data( __( 'Do you want to stretch the footer to the entire window width?', 'equadio' ) ),
					'dependency' => array(
						"footer_type_{$cpt}" => array( 'default' ),
					),
					'std'        => 0,
					'type'       => 'switch',
				),
			), $cpt, $title
		);
	}
}


// Returns a list of options that can be overridden for CPT. Section 'Additional Widget Areas'
if ( ! function_exists( 'equadio_options_get_list_cpt_options_widgets' ) ) {
	function equadio_options_get_list_cpt_options_widgets( $cpt, $title = '' ) {
		if ( empty( $title ) ) {
			$title = ucfirst( $cpt );
		}
		return apply_filters( 'equadio_filter_get_list_cpt_options_widgets', array(
				"widgets_info_{$cpt}"           => array(
					'title' => esc_html__( 'Additional panels', 'equadio' ),
					// Translators: Add CPT name to the description
					'desc'  => wp_kses_data( sprintf( __( 'Set up additional panels to display %s list and single posts', 'equadio' ), $title ) ),
					'type'  => EQUADIO_THEME_FREE ? 'hidden' : 'info',
				),
				"widgets_above_page_{$cpt}"     => array(
					'title'   => esc_html__( 'Widgets at the top of the page', 'equadio' ),
					'desc'    => wp_kses_data( __( 'Select widgets to show at the top of the page (above content and sidebar)', 'equadio' ) ),
					'std'     => 'hide',
					'options' => array(),
					'type'    => EQUADIO_THEME_FREE ? 'hidden' : 'select',
				),
				"widgets_above_content_{$cpt}"  => array(
					'title'   => esc_html__( 'Widgets above the content', 'equadio' ),
					'desc'    => wp_kses_data( __( 'Select widgets to show at the beginning of the content area', 'equadio' ) ),
					'std'     => 'hide',
					'options' => array(),
					'type'    => EQUADIO_THEME_FREE ? 'hidden' : 'select',
				),
				"widgets_below_content_{$cpt}"  => array(
					'title'   => esc_html__( 'Widgets below the content', 'equadio' ),
					'desc'    => wp_kses_data( __( 'Select widgets to show at the ending of the content area', 'equadio' ) ),
					'std'     => 'hide',
					'options' => array(),
					'type'    => EQUADIO_THEME_FREE ? 'hidden' : 'select',
				),
				"widgets_below_page_{$cpt}"     => array(
					'title'   => esc_html__( 'Widgets at the bottom of the page', 'equadio' ),
					'desc'    => wp_kses_data( __( 'Select widgets to show at the bottom of the page (below content and sidebar)', 'equadio' ) ),
					'std'     => 'hide',
					'options' => array(),
					'type'    => EQUADIO_THEME_FREE ? 'hidden' : 'select',
				),
			), $cpt, $title
		);
	}
}


// Return lists with choises when its need in the admin mode
if ( ! function_exists( 'equadio_options_get_list_choises' ) ) {
	add_filter( 'equadio_filter_options_get_list_choises', 'equadio_options_get_list_choises', 10, 2 );
	function equadio_options_get_list_choises( $list, $id ) {
		if ( is_array( $list ) && count( $list ) == 0 ) {
			if ( strpos( $id, 'header_style' ) === 0 ) {
				$list = equadio_get_list_header_styles( strpos( $id, 'header_style_' ) === 0 );
			} elseif ( strpos( $id, 'header_position' ) === 0 ) {
				$list = equadio_get_list_header_positions( strpos( $id, 'header_position_' ) === 0 );
			} elseif ( strpos( $id, 'header_widgets' ) === 0 ) {
				$list = equadio_get_list_sidebars( strpos( $id, 'header_widgets_' ) === 0, true );
			} elseif ( strpos( $id, '_scheme' ) > 0 ) {
				$list = equadio_get_list_schemes( 'color_scheme' != $id );
			} else if ( strpos( $id, 'sidebar_style' ) === 0 ) {
				$list = equadio_get_list_sidebar_styles( strpos( $id, 'sidebar_style_' ) === 0 );
			} elseif ( strpos( $id, 'sidebar_widgets' ) === 0 ) {
				$list = equadio_get_list_sidebars( 'sidebar_widgets_single' != $id && ( strpos( $id, 'sidebar_widgets_' ) === 0 || strpos( $id, 'sidebar_widgets_single_' ) === 0 ), true );
			} elseif ( strpos( $id, 'sidebar_position_ss' ) === 0 ) {
				$list = equadio_get_list_sidebars_positions_ss( strpos( $id, 'sidebar_position_ss_' ) === 0 );
			} elseif ( strpos( $id, 'sidebar_position' ) === 0 ) {
				$list = equadio_get_list_sidebars_positions( strpos( $id, 'sidebar_position_' ) === 0 );
			} elseif ( strpos( $id, 'widgets_above_page' ) === 0 ) {
				$list = equadio_get_list_sidebars( strpos( $id, 'widgets_above_page_' ) === 0, true );
			} elseif ( strpos( $id, 'widgets_above_content' ) === 0 ) {
				$list = equadio_get_list_sidebars( strpos( $id, 'widgets_above_content_' ) === 0, true );
			} elseif ( strpos( $id, 'widgets_below_page' ) === 0 ) {
				$list = equadio_get_list_sidebars( strpos( $id, 'widgets_below_page_' ) === 0, true );
			} elseif ( strpos( $id, 'widgets_below_content' ) === 0 ) {
				$list = equadio_get_list_sidebars( strpos( $id, 'widgets_below_content_' ) === 0, true );
			} elseif ( strpos( $id, 'footer_style' ) === 0 ) {
				$list = equadio_get_list_footer_styles( strpos( $id, 'footer_style_' ) === 0 );
			} elseif ( strpos( $id, 'footer_widgets' ) === 0 ) {
				$list = equadio_get_list_sidebars( strpos( $id, 'footer_widgets_' ) === 0, true );
			} elseif ( strpos( $id, 'blog_style' ) === 0 ) {
				$list = equadio_get_list_blog_styles( strpos( $id, 'blog_style_' ) === 0 );
			} elseif ( strpos( $id, 'single_style' ) === 0 ) {
				$list = equadio_get_list_single_styles( strpos( $id, 'single_style_' ) === 0 );
			} elseif ( strpos( $id, 'post_type' ) === 0 ) {
				$list = equadio_get_list_posts_types();
			} elseif ( strpos( $id, 'parent_cat' ) === 0 ) {
				$list = equadio_array_merge( array( 0 => esc_html__( '- Select category -', 'equadio' ) ), equadio_get_list_categories() );
			} elseif ( strpos( $id, 'blog_animation' ) === 0 ) {
				$list = equadio_get_list_animations_in( strpos( $id, 'blog_animation_' ) === 0 );
			} elseif ( 'color_scheme_editor' == $id ) {
				$list = equadio_get_list_schemes();
			} elseif ( strpos( $id, '_font-family' ) > 0 ) {
				$list = equadio_get_list_load_fonts( true );
			}
		}
		return $list;
	}
}
