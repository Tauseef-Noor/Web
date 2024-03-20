<?php
/**
 * Skin Setup
 *
 * @package EQUADIO
 * @since EQUADIO 1.76.0
 */


//--------------------------------------------
// SKIN DEFAULTS
//--------------------------------------------

// Return theme's (skin's) default value for the specified parameter
if ( ! function_exists( 'equadio_theme_defaults' ) ) {
	function equadio_theme_defaults( $name='', $value='' ) {
		$defaults = array(
			'page'              => 1278,
			'page_boxed_extra'  => 60,
			'page_fullwide_max' => 1920,
			'paddings_fullwide' => 130,
			'sidebar'           => 372,
			'sidebar_gap'       => 64,
			'grid_gap'          => 30,
			'rad'               => 0,
		);
		if ( empty( $name ) ) {
			return $defaults;
		} else {
			if ( empty( $value ) && isset( $defaults[ $name ] ) ) {
				$value = $defaults[ $name ];
			}
			return $value;
		}
	}
}


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


//--------------------------------------------
// SKIN SETTINGS
//--------------------------------------------
if ( ! function_exists( 'equadio_skin_setup' ) ) {
	add_action( 'after_setup_theme', 'equadio_skin_setup', 1 );
	function equadio_skin_setup() {
		$GLOBALS['EQUADIO_STORAGE'] = array_merge( $GLOBALS['EQUADIO_STORAGE'], array(

			// Key validator: market[env|loc]-vendor[axiom|ancora|themerex]
			'theme_pro_key'       => 'env-themerex',

			'theme_doc_url'       => '//equadio.themerex.net/doc',

			'theme_demofiles_url' => '//demofiles.themerex.net/equadio/',
			
			'theme_rate_url'      => '//themeforest.net/download',

			'theme_custom_url' => '//themerex.net/offers/?utm_source=offers&utm_medium=click&utm_campaign=themedash',

			'theme_support_url'   => '//themerex.net/support/',

			'theme_download_url'  => '//themeforest.net/item/equadio-nonprofit-and-environmental-wordpress-theme/26692522',            // ThemeREX

			'theme_video_url'     => '//www.youtube.com/channel/UCnFisBimrK2aIE-hnY70kCA',   // ThemeREX

			'theme_privacy_url'   => '//themerex.net/privacy-policy/',                       // ThemeREX

			'portfolio_url'       => '//themeforest.net/user/themerex/portfolio',            // ThemeREX

			// Comma separated slugs of theme-specific categories (for get relevant news in the dashboard widget)
			// (i.e. 'children,kindergarten')
			'theme_categories'    => '',
		) );
	}
}


// Add/remove/change Theme Settings
if ( ! function_exists( 'equadio_skin_setup_settings' ) ) {
	add_action( 'after_setup_theme', 'equadio_skin_setup_settings', 1 );
	function equadio_skin_setup_settings() {
		// Example: enable (true) / disable (false) thumbs in the prev/next navigation
		equadio_storage_set_array( 'settings', 'thumbs_in_navigation', true );
	}
}



//--------------------------------------------
// SKIN FONTS
//--------------------------------------------
if ( ! function_exists( 'equadio_skin_setup_fonts' ) ) {
	add_action( 'after_setup_theme', 'equadio_skin_setup_fonts', 1 );
	function equadio_skin_setup_fonts() {
		// Fonts to load when theme start
		// It can be Google fonts or uploaded fonts, placed in the folder css/font-face/font-name inside the skin folder
		// Attention! Font's folder must have name equal to the font's name, with spaces replaced on the dash '-'
		// example: font name 'TeX Gyre Termes', folder 'TeX-Gyre-Termes'
		equadio_storage_set(
			'load_fonts', array(
				// Google font
				array(
					'name'   => 'Titillium Web',
					'family' => 'sans-serif',
					'styles' => '300,300italic,400,400italic,600,600italic,700,700italic',     // Parameter 'style' used only for the Google fonts
				),
                array(
                    'name'   => 'Permanent Marker',
                    'family' => 'cursive',
                    'styles' => '400',     // Parameter 'style' used only for the Google fonts
                ),
			)
		);

		// Characters subset for the Google fonts. Available values are: latin,latin-ext,cyrillic,cyrillic-ext,greek,greek-ext,vietnamese
		equadio_storage_set( 'load_fonts_subset', 'latin,latin-ext' );

		// Settings of the main tags
		// Attention! Font name in the parameter 'font-family' will be enclosed in the quotes and no spaces after comma!
		// example:
		// Correct:   'font-family' => '"Roboto",sans-serif'
		// Incorrect: 'font-family' => '"Roboto", sans-serif'
		// Incorrect: 'font-family' => 'Roboto,sans-serif'

		$font_description = esc_html__( 'Font settings for the %s of the site. To ensure that the elements scale properly on mobile devices, please use only the following units: "rem", "em" or "ex"', 'equadio' );

		equadio_storage_set(
			'theme_fonts', array(
				'p'       => array(
					'title'           => esc_html__( 'Main text', 'equadio' ),
					'description'     => sprintf( $font_description, esc_html__( 'main text', 'equadio' ) ),
					'font-family'     => '"Titillium Web",sans-serif',
					'font-size'       => '1rem',
					'font-weight'     => '400',
					'font-style'      => 'normal',
					'line-height'     => '1.5em',
					'text-decoration' => 'none',
					'text-transform'  => 'none',
					'letter-spacing'  => '0.15px',
					'margin-top'      => '0em',
					'margin-bottom'   => '1.4em',
				),
				'post'    => array(
					'title'           => esc_html__( 'Article text', 'equadio' ),
					'description'     => sprintf( $font_description, esc_html__( 'article text', 'equadio' ) ),
					'font-family'     => '',			// Example: '"PR Serif",serif',
					'font-size'       => '',			// Example: '1.286rem',
					'font-weight'     => '',			// Example: '400',
					'font-style'      => '',			// Example: 'normal',
					'line-height'     => '',			// Example: '1.75em',
					'text-decoration' => '',			// Example: 'none',
					'text-transform'  => '',			// Example: 'none',
					'letter-spacing'  => '',			// Example: '',
					'margin-top'      => '',			// Example: '0em',
					'margin-bottom'   => '',			// Example: '1.4em',
				),
				'h1'      => array(
					'title'           => esc_html__( 'Heading 1', 'equadio' ),
					'description'     => sprintf( $font_description, esc_html__( 'tag H1', 'equadio' ) ),
					'font-family'     => '"Permanent Marker",cursive',
					'font-size'       => '4.375rem',
					'font-weight'     => '400',
					'font-style'      => 'normal',
					'line-height'     => '0.905em',
					'text-decoration' => 'none',
					'text-transform'  => 'none',
					'letter-spacing'  => '',
					'margin-top'      => '1em',
					'margin-bottom'   => '0.5688em',
				),
				'h2'      => array(
					'title'           => esc_html__( 'Heading 2', 'equadio' ),
					'description'     => sprintf( $font_description, esc_html__( 'tag H2', 'equadio' ) ),
					'font-family'     => '"Permanent Marker",cursive',
					'font-size'       => '3.750rem',
					'font-weight'     => '400',
					'font-style'      => 'normal',
					'line-height'     => '1.025em',
					'text-decoration' => 'none',
					'text-transform'  => 'none',
					'letter-spacing'  => '',
					'margin-top'      => '0.9556em',
					'margin-bottom'   => '0.5522em',
				),
				'h3'      => array(
					'title'           => esc_html__( 'Heading 3', 'equadio' ),
					'description'     => sprintf( $font_description, esc_html__( 'tag H3', 'equadio' ) ),
					'font-family'     => '"Titillium Web",sans-serif',
					'font-size'       => '2.875rem',
					'font-weight'     => '700',
					'font-style'      => 'normal',
					'line-height'     => '1.1em',
					'text-decoration' => 'none',
					'text-transform'  => 'none',
					'letter-spacing'  => '0.9px',
					'margin-top'      => '1.2255em',
					'margin-bottom'   => '0.5755em',
				),
				'h4'      => array(
					'title'           => esc_html__( 'Heading 4', 'equadio' ),
					'description'     => sprintf( $font_description, esc_html__( 'tag H4', 'equadio' ) ),
					'font-family'     => '"Titillium Web",sans-serif',
					'font-size'       => '2.250rem',
					'font-weight'     => '700',
					'font-style'      => 'normal',
					'line-height'     => '1.152em',
					'text-decoration' => 'none',
					'text-transform'  => 'none',
					'letter-spacing'  => '0.8px',
					'margin-top'      => '1.4225em',
					'margin-bottom'   => '0.6455em',
				),
				'h5'      => array(
					'title'           => esc_html__( 'Heading 5', 'equadio' ),
					'description'     => sprintf( $font_description, esc_html__( 'tag H5', 'equadio' ) ),
					'font-family'     => '"Titillium Web",sans-serif',
					'font-size'       => '1.875rem',
					'font-weight'     => '700',
					'font-style'      => 'normal',
					'line-height'     => '1.1855em',
					'text-decoration' => 'none',
					'text-transform'  => 'none',
					'letter-spacing'  => '0.7px',
					'margin-top'      => '1.555em',
					'margin-bottom'   => '0.588em',
				),
				'h6'      => array(
					'title'           => esc_html__( 'Heading 6', 'equadio' ),
					'description'     => sprintf( $font_description, esc_html__( 'tag H6', 'equadio' ) ),
					'font-family'     => '"Titillium Web",sans-serif',
					'font-size'       => '1.250rem',
					'font-weight'     => '700',
					'font-style'      => 'normal',
					'line-height'     => '1.285em',
					'text-decoration' => 'none',
					'text-transform'  => 'none',
					'letter-spacing'  => '0.4px',
					'margin-top'      => '1.8355em',
					'margin-bottom'   => '0.7888em',
				),
				'logo'    => array(
					'title'           => esc_html__( 'Logo text', 'equadio' ),
					'description'     => sprintf( $font_description, esc_html__( 'text of the logo', 'equadio' ) ),
					'font-family'     => '"Titillium Web",sans-serif',
					'font-size'       => '1.875rem',
					'font-weight'     => '700',
					'font-style'      => 'normal',
					'line-height'     => '1.1855em',
					'text-decoration' => 'none',
					'text-transform'  => 'uppercase',
					'letter-spacing'  => '0.7px',
				),
				'button'  => array(
					'title'           => esc_html__( 'Buttons', 'equadio' ),
					'description'     => sprintf( $font_description, esc_html__( 'buttons', 'equadio' ) ),
					'font-family'     => '"Titillium Web",sans-serif',
					'font-size'       => '16px',
					'font-weight'     => '600',
					'font-style'      => 'normal',
					'line-height'     => '24px',
					'text-decoration' => 'none',
					'text-transform'  => 'uppercase',
					'letter-spacing'  => '1px',
				),
				'input'   => array(
					'title'           => esc_html__( 'Input fields', 'equadio' ),
					'description'     => sprintf( $font_description, esc_html__( 'input fields, dropdowns and textareas', 'equadio' ) ),
					'font-family'     => '"Titillium Web",sans-serif',
					'font-size'       => '18px',
					'font-weight'     => '400',
					'font-style'      => 'normal',
					'line-height'     => '1.5em',     // Attention! Firefox don't allow line-height less then 1.5em in the select
					'text-decoration' => 'none',
					'text-transform'  => 'none',
					'letter-spacing'  => '0.2px',
				),
				'info'    => array(
					'title'           => esc_html__( 'Post meta', 'equadio' ),
					'description'     => sprintf( $font_description, esc_html__( 'post meta (author, categories, publish date, counters, share, etc.)', 'equadio' ) ),
					'font-family'     => 'inherit',
					'font-size'       => '1em',  // Old value '13px' don't allow using 'font zoom' in the custom blog items
					'font-weight'     => '600',
					'font-style'      => 'normal',
					'line-height'     => '1.5em',
					'text-decoration' => 'none',
					'text-transform'  => 'none',
					'letter-spacing'  => '0.2px',
					'margin-top'      => '0.4em',
					'margin-bottom'   => '',
				),
				'menu'    => array(
					'title'           => esc_html__( 'Main menu', 'equadio' ),
					'description'     => sprintf( $font_description, esc_html__( 'main menu items', 'equadio' ) ),
					'font-family'     => '"Titillium Web",sans-serif',
					'font-size'       => '14px',
					'font-weight'     => '600',
					'font-style'      => 'normal',
					'line-height'     => '1.5em',
					'text-decoration' => 'none',
					'text-transform'  => 'uppercase',
					'letter-spacing'  => '0.8px',
				),
				'submenu' => array(
					'title'           => esc_html__( 'Dropdown menu', 'equadio' ),
					'description'     => sprintf( $font_description, esc_html__( 'dropdown menu items', 'equadio' ) ),
					'font-family'     => '"Titillium Web",sans-serif',
					'font-size'       => '14px',
					'font-weight'     => '600',
					'font-style'      => 'normal',
					'line-height'     => '1.5em',
					'text-decoration' => 'none',
					'text-transform'  => 'uppercase',
					'letter-spacing'  => '0.8px',
				),
			)
		);
	}
}


//--------------------------------------------
// COLOR SCHEMES
//--------------------------------------------
if ( ! function_exists( 'equadio_skin_setup_schemes' ) ) {
	add_action( 'after_setup_theme', 'equadio_skin_setup_schemes', 1 );
	function equadio_skin_setup_schemes() {

		// Theme colors for customizer
		// Attention! Inner scheme must be last in the array below
		equadio_storage_set(
			'scheme_color_groups', array(
				'main'    => array(
					'title'       => esc_html__( 'Main', 'equadio' ),
					'description' => esc_html__( 'Colors of the main content area', 'equadio' ),
				),
				'alter'   => array(
					'title'       => esc_html__( 'Alter', 'equadio' ),
					'description' => esc_html__( 'Colors of the alternative blocks (sidebars, etc.)', 'equadio' ),
				),
				'extra'   => array(
					'title'       => esc_html__( 'Extra', 'equadio' ),
					'description' => esc_html__( 'Colors of the extra blocks (dropdowns, price blocks, table headers, etc.)', 'equadio' ),
				),
				'inverse' => array(
					'title'       => esc_html__( 'Inverse', 'equadio' ),
					'description' => esc_html__( 'Colors of the inverse blocks - when link color used as background of the block (dropdowns, blockquotes, etc.)', 'equadio' ),
				),
				'input'   => array(
					'title'       => esc_html__( 'Input', 'equadio' ),
					'description' => esc_html__( 'Colors of the form fields (text field, textarea, select, etc.)', 'equadio' ),
				),
			)
		);

		equadio_storage_set(
			'scheme_color_names', array(
				'bg_color'    => array(
					'title'       => esc_html__( 'Background color', 'equadio' ),
					'description' => esc_html__( 'Background color of this block in the normal state', 'equadio' ),
				),
				'bg_hover'    => array(
					'title'       => esc_html__( 'Background hover', 'equadio' ),
					'description' => esc_html__( 'Background color of this block in the hovered state', 'equadio' ),
				),
				'bd_color'    => array(
					'title'       => esc_html__( 'Border color', 'equadio' ),
					'description' => esc_html__( 'Border color of this block in the normal state', 'equadio' ),
				),
				'bd_hover'    => array(
					'title'       => esc_html__( 'Border hover', 'equadio' ),
					'description' => esc_html__( 'Border color of this block in the hovered state', 'equadio' ),
				),
				'text'        => array(
					'title'       => esc_html__( 'Text', 'equadio' ),
					'description' => esc_html__( 'Color of the text inside this block', 'equadio' ),
				),
				'text_dark'   => array(
					'title'       => esc_html__( 'Text dark', 'equadio' ),
					'description' => esc_html__( 'Color of the dark text (bold, header, etc.) inside this block', 'equadio' ),
				),
				'text_light'  => array(
					'title'       => esc_html__( 'Text light', 'equadio' ),
					'description' => esc_html__( 'Color of the light text (post meta, etc.) inside this block', 'equadio' ),
				),
				'text_link'   => array(
					'title'       => esc_html__( 'Link', 'equadio' ),
					'description' => esc_html__( 'Color of the links inside this block', 'equadio' ),
				),
				'text_hover'  => array(
					'title'       => esc_html__( 'Link hover', 'equadio' ),
					'description' => esc_html__( 'Color of the hovered state of links inside this block', 'equadio' ),
				),
				'text_link2'  => array(
					'title'       => esc_html__( 'Link 2', 'equadio' ),
					'description' => esc_html__( 'Color of the accented texts (areas) inside this block', 'equadio' ),
				),
				'text_hover2' => array(
					'title'       => esc_html__( 'Link 2 hover', 'equadio' ),
					'description' => esc_html__( 'Color of the hovered state of accented texts (areas) inside this block', 'equadio' ),
				),
				'text_link3'  => array(
					'title'       => esc_html__( 'Link 3', 'equadio' ),
					'description' => esc_html__( 'Color of the other accented texts (buttons) inside this block', 'equadio' ),
				),
				'text_hover3' => array(
					'title'       => esc_html__( 'Link 3 hover', 'equadio' ),
					'description' => esc_html__( 'Color of the hovered state of other accented texts (buttons) inside this block', 'equadio' ),
				),
			)
		);

		// Default values for each color scheme
		$schemes = array(

			// Color scheme: 'default'
			'default' => array(
				'title'    => esc_html__( 'Default', 'equadio' ),
				'internal' => true,
				'colors'   => array(

					// Whole block border and background
					'bg_color'         => '#f2f1ec', //ok
					'bd_color'         => '#e0dfdb', //ok

					// Text and links colors
					'text'             => '#606060', //ok
					'text_light'       => '#909090', //ok
					'text_dark'        => '#2c2c2c', //ok
					'text_link'        => '#f42a2a', //ok red
					'text_hover'       => '#000000', //ok dark
					'text_link2'       => '#ffbb00', //ok yellow
					'text_hover2'      => '#000000', //ok dark
					'text_link3'       => '#000000', //ok dark
					'text_hover3'      => '#f42a2a', //ok red

					// Alternative blocks (sidebar, tabs, alternative blocks, etc.)
					'alter_bg_color'   => '#ffffff', //ok
					'alter_bg_hover'   => '#e8e6dc', //ok
					'alter_bd_color'   => '#e0dfdb', //ok
					'alter_bd_hover'   => '#c7c7c7', //ok
					'alter_text'       => '#606060', //ok
					'alter_light'      => '#909090', //ok
					'alter_dark'       => '#2c2c2c', //ok
					'alter_link'       => '#f42a2a', //ok red
					'alter_hover'      => '#000000', //ok dark
					'alter_link2'      => '#ffbb00', //ok yellow
					'alter_hover2'     => '#000000', //ok dark
					'alter_link3'      => '#000000', //ok dark
					'alter_hover3'     => '#f42a2a', //ok red

					// Extra blocks (submenu, tabs, color blocks, etc.)
					'extra_bg_color'   => '#000000', //ok
					'extra_bg_hover'   => '#171818', //ok
					'extra_bd_color'   => '#2c2c2c', //ok
					'extra_bd_hover'   => '#504a4a', //ok
					'extra_text'       => '#9d9d9d', //ok
					'extra_light'      => '#909090', //ok
					'extra_dark'       => '#ffffff', //ok
					'extra_link'       => '#9d9d9d', //ok
					'extra_hover'      => '#ffffff', //ok
					'extra_link2'      => '#ffffff', //ok
					'extra_hover2'     => '#ffbb00', //ok yellow
					'extra_link3'      => '#ddb837',
					'extra_hover3'     => '#eec432',

					// Input fields (form's fields and textarea)
					'input_bg_color'   => '#ffffff', //ok
					'input_bg_hover'   => '#ffffff', //ok
					'input_bd_color'   => '#e0dfdb', //ok
					'input_bd_hover'   => '#c7c7c7', //ok
					'input_text'       => '#606060', //ok
					'input_light'      => '#909090', //ok
					'input_dark'       => '#2c2c2c', //ok

					// Inverse blocks (text and links on the 'text_link' background)
					'inverse_bd_color' => '#eedddd', //ok
					'inverse_bd_hover' => '#ffffff', //ok
					'inverse_text'     => '#ffffff', //ok
					'inverse_light'    => '#d5d5d5', //ok
					'inverse_dark'     => '#ffffff', //ok
					'inverse_link'     => '#ffffff', //ok
					'inverse_hover'    => '#ffffff', //ok

					// Additional (skin-specific) colors.
					// Attention! Set of colors must be equal in all color schemes.
					//---> For example:
					//---> 'new_color1'         => '#rrggbb',
					//---> 'alter_new_color1'   => '#rrggbb',
					//---> 'inverse_new_color1' => '#rrggbb',
				),
			),

			// Color scheme: 'dark'
			'dark'    => array(
				'title'    => esc_html__( 'Dark', 'equadio' ),
				'internal' => true,
				'colors'   => array(

					// Whole block border and background
					'bg_color'         => '#181818', //ok
					'bd_color'         => '#333333', //ok

					// Text and links colors
					'text'             => '#9a9a9a', //ok
					'text_light'       => '#7b7b7b', //ok
					'text_dark'        => '#ffffff', //ok
					'text_link'        => '#f42a2a', //ok red
					'text_hover'       => '#ffffff', //ok light
					'text_link2'       => '#ffbb00', //ok yellow
					'text_hover2'      => '#ffffff', //ok light
					'text_link3'       => '#ffffff', //ok light
					'text_hover3'      => '#f42a2a', //ok red

					// Alternative blocks (sidebar, tabs, alternative blocks, etc.)
					'alter_bg_color'   => '#000000', //ok
					'alter_bg_hover'   => '#0e0e0e', //ok
					'alter_bd_color'   => '#333333', //ok
					'alter_bd_hover'   => '#444444', //ok
					'alter_text'       => '#9a9a9a', //ok
					'alter_light'      => '#7b7b7b', //ok
					'alter_dark'       => '#ffffff', //ok
					'alter_link'       => '#f42a2a', //ok red
					'alter_hover'      => '#ffffff', //ok light
					'alter_link2'      => '#ffbb00', //ok yellow
					'alter_hover2'     => '#ffffff', //ok light
					'alter_link3'      => '#ffffff', //ok light
					'alter_hover3'     => '#f42a2a', //ok red

					// Extra blocks (submenu, tabs, color blocks, etc.)
					'extra_bg_color'   => '#000000', //ok
					'extra_bg_hover'   => '#171818', //ok
					'extra_bd_color'   => '#2c2c2c', //ok
					'extra_bd_hover'   => '#504a4a', //ok
					'extra_text'       => '#9a9a9a', //ok
					'extra_light'      => '#909090', //ok
					'extra_dark'       => '#ffffff', //ok
					'extra_link'       => '#9a9a9a', //ok
					'extra_hover'      => '#ffffff', //ok
					'extra_link2'      => '#ffffff', //ok
					'extra_hover2'     => '#ffbb00', //ok yellow
					'extra_link3'      => '#ddb837',
					'extra_hover3'     => '#eec432',

					// Input fields (form's fields and textarea)
					'input_bg_color'   => '#333333', //ok
					'input_bg_hover'   => '#333333', //ok
					'input_bd_color'   => '#333333', //ok
					'input_bd_hover'   => '#e0dfdb', //ok
					'input_text'       => '#9a9a9a', //ok
					'input_light'      => '#7b7b7b', //ok
					'input_dark'       => '#ffffff', //ok

					// Inverse blocks (text and links on the 'text_link' background)
					'inverse_bd_color' => '#eedddd', //ok
					'inverse_bd_hover' => '#ffffff', //ok
					'inverse_text'     => '#ffffff', //ok
					'inverse_light'    => '#d5d5d5', //ok
					'inverse_dark'     => '#ffffff', //ok
					'inverse_link'     => '#ffffff', //ok
					'inverse_hover'    => '#000000', //ok

					// Additional (skin-specific) colors.
					// Attention! Set of colors must be equal in all color schemes.
					//---> For example:
					//---> 'new_color1'         => '#rrggbb',
					//---> 'alter_new_color1'   => '#rrggbb',
					//---> 'inverse_new_color1' => '#rrggbb',
				),
			),
		);
		equadio_storage_set( 'schemes', $schemes );
		equadio_storage_set( 'schemes_original', $schemes );

		// Add names of additional colors
		//---> For example:
		//---> equadio_storage_set_array( 'scheme_color_names', 'new_color1', array(
		//---> 	'title'       => __( 'New color 1', 'equadio' ),
		//---> 	'description' => __( 'Description of the new color 1', 'equadio' ),
		//---> ) );


		// Additional colors for each scheme
		// Parameters:	'color' - name of the color from the scheme that should be used as source for the transformation
		//				'alpha' - to make color transparent (0.0 - 1.0)
		//				'hue', 'saturation', 'brightness' - inc/dec value for each color's component
		equadio_storage_set(
			'scheme_colors_add', array(
				'bg_color_0'        => array(
					'color' => 'bg_color',
					'alpha' => 0,
				),
				'bg_color_02'       => array(
					'color' => 'bg_color',
					'alpha' => 0.2,
				),
				'bg_color_07'       => array(
					'color' => 'bg_color',
					'alpha' => 0.7,
				),
				'bg_color_08'       => array(
					'color' => 'bg_color',
					'alpha' => 0.8,
				),
				'bg_color_09'       => array(
					'color' => 'bg_color',
					'alpha' => 0.9,
				),
				'alter_bg_color_07' => array(
					'color' => 'alter_bg_color',
					'alpha' => 0.7,
				),
				'alter_bg_color_04' => array(
					'color' => 'alter_bg_color',
					'alpha' => 0.4,
				),
				'alter_bg_color_00' => array(
					'color' => 'alter_bg_color',
					'alpha' => 0,
				),
				'alter_bg_color_02' => array(
					'color' => 'alter_bg_color',
					'alpha' => 0.2,
				),
                'alter_bg_hover_06' => array(
                    'color' => 'alter_bg_hover',
                    'alpha' => 0.6,
                ),
                'alter_bg_hover_08' => array(
                    'color' => 'alter_bg_hover',
                    'alpha' => 0.8,
                ),
				'alter_bd_color_02' => array(
					'color' => 'alter_bd_color',
					'alpha' => 0.2,
				),
                'alter_dark_05'     => array(
                    'color' => 'alter_dark',
                    'alpha' => 0.5,
                ),
				'alter_link_02'     => array(
					'color' => 'alter_link',
					'alpha' => 0.2,
				),
				'alter_link_07'     => array(
					'color' => 'alter_link',
					'alpha' => 0.7,
				),
				'extra_bg_color_05' => array(
					'color' => 'extra_bg_color',
					'alpha' => 0.5,
				),
				'extra_bg_color_07' => array(
					'color' => 'extra_bg_color',
					'alpha' => 0.7,
				),
				'extra_link_02'     => array(
					'color' => 'extra_link',
					'alpha' => 0.2,
				),
				'extra_link_07'     => array(
					'color' => 'extra_link',
					'alpha' => 0.7,
				),
                'extra_link2_05'     => array(
                    'color' => 'extra_link2',
                    'alpha' => 0.5,
                ),
                'extra_link2_06'     => array(
                    'color' => 'extra_link2',
                    'alpha' => 0.6,
                ),
                'text_dark_02'      => array(
                    'color' => 'text_dark',
                    'alpha' => 0.2,
                ),
                'text_dark_03'      => array(
                    'color' => 'text_dark',
                    'alpha' => 0.3,
                ),
				'text_dark_07'      => array(
					'color' => 'text_dark',
					'alpha' => 0.7,
				),
                'text_dark_075'      => array(
                    'color' => 'text_dark',
                    'alpha' => 0.75,
                ),
                'text_dark_08'      => array(
                    'color' => 'text_dark',
                    'alpha' => 0.8,
                ),
				'text_link_02'      => array(
					'color' => 'text_link',
					'alpha' => 0.2,
				),
				'text_link_07'      => array(
					'color' => 'text_link',
					'alpha' => 0.7,
				),
                'text_link_08'      => array(
                    'color' => 'text_link',
                    'alpha' => 0.8,
                ),
                'text_link2_02'      => array(
                    'color' => 'text_link2',
                    'alpha' => 0.2,
                ),
                'text_link3_02'      => array(
                    'color' => 'text_link3',
                    'alpha' => 0.2,
                ),
                'inverse_link_03'      => array(
                    'color' => 'inverse_link',
                    'alpha' => 0.3,
                ),
                'inverse_link_05'      => array(
                    'color' => 'inverse_link',
                    'alpha' => 0.5,
                ),
                'inverse_link_08'      => array(
                    'color' => 'inverse_link',
                    'alpha' => 0.8,
                ),
				'text_link_blend'   => array(
					'color'      => 'text_link',
					'hue'        => 2,
					'saturation' => -5,
					'brightness' => 5,
				),
                'text_link2_blend'   => array(
                    'color'      => 'text_link2',
                    'hue'        => 2,
                    'saturation' => -5,
                    'brightness' => 5,
                ),
				'alter_link_blend'  => array(
					'color'      => 'alter_link',
					'hue'        => 2,
					'saturation' => -5,
					'brightness' => 5,
				),
			)
		);

		// Simple scheme editor: lists the colors to edit in the "Simple" mode.
		// For each color you can set the array of 'slave' colors and brightness factors that are used to generate new values,
		// when 'main' color is changed
		// Leave 'slave' arrays empty if your scheme does not have a color dependency
		equadio_storage_set(
			'schemes_simple', array(
				'text_link'        => array(),
				'text_hover'       => array(),
				'text_link2'       => array(),
				'text_hover2'      => array(),
				'text_link3'       => array(),
				'text_hover3'      => array(),
				'alter_link'       => array(),
				'alter_hover'      => array(),
				'alter_link2'      => array(),
				'alter_hover2'     => array(),
				'alter_link3'      => array(),
				'alter_hover3'     => array(),
				'extra_link'       => array(),
				'extra_hover'      => array(),
				'extra_link2'      => array(),
				'extra_hover2'     => array(),
				'extra_link3'      => array(),
				'extra_hover3'     => array(),
				'inverse_bd_color' => array(),
				'inverse_bd_hover' => array(),
			)
		);

		// Parameters to set order of schemes in the css
		equadio_storage_set(
			'schemes_sorted', array(
				'color_scheme',
				'header_scheme',
				'menu_scheme',
				'sidebar_scheme',
				'footer_scheme',
			)
		);
	}
}


//--------------------------------------------
// THUMBS
//--------------------------------------------
if ( ! function_exists( 'equadio_skin_setup_thumbs' ) ) {
	add_action( 'after_setup_theme', 'equadio_skin_setup_thumbs', 1 );
	function equadio_skin_setup_thumbs() {
		equadio_storage_set(
			'theme_thumbs', apply_filters(
				'equadio_filter_add_thumb_sizes', array(
					// Width of the image is equal to the content area width (without sidebar)
					// Height is fixed
					'equadio-thumb-huge'        => array(
						'size'  => array( 1278, 719, true ),
						'title' => esc_html__( 'Huge image', 'equadio' ),
						'subst' => 'trx_addons-thumb-huge',
					),
					// Width of the image is equal to the content area width (with sidebar)
					// Height is fixed
					'equadio-thumb-big'         => array(
						'size'  => array( 842, 474, true ),
						'title' => esc_html__( 'Large image', 'equadio' ),
						'subst' => 'trx_addons-thumb-big',
					),

					// Width of the image is equal to the 1/3 of the content area width (without sidebar)
					// Height is fixed
					'equadio-thumb-med'         => array(
						'size'  => array( 406, 228, true ),
						'title' => esc_html__( 'Medium image', 'equadio' ),
						'subst' => 'trx_addons-thumb-medium',
					),

					// Small square image (for avatars in comments, etc.)
					'equadio-thumb-tiny'        => array(
						'size'  => array( 90, 90, true ),
						'title' => esc_html__( 'Small square avatar', 'equadio' ),
						'subst' => 'trx_addons-thumb-tiny',
					),

					// Width of the image is equal to the content area width (with sidebar)
					// Height is proportional (only downscale, not crop)
					'equadio-thumb-masonry-big' => array(
						'size'  => array( 842, 0, false ),     // Only downscale, not crop
						'title' => esc_html__( 'Masonry Large (scaled)', 'equadio' ),
						'subst' => 'trx_addons-thumb-masonry-big',
					),

					// Width of the image is equal to the 1/3 of the full content area width (without sidebar)
					// Height is proportional (only downscale, not crop)
					'equadio-thumb-masonry'     => array(
						'size'  => array( 406, 0, false ),     // Only downscale, not crop
						'title' => esc_html__( 'Masonry (scaled)', 'equadio' ),
						'subst' => 'trx_addons-thumb-masonry',
					),
                    // Medium square image (for posts list)
                    'equadio-thumb-square'        => array(
                        'size'  => array( 556, 276, true ),
                        'title' => esc_html__( 'Medium square image', 'equadio' ),
                        'subst' => 'trx_addons-thumb-square',
                    ),
                    // Medium wide image (for price)
                    'equadio-thumb-wide'        => array(
                        'size'  => array( 772, 284, true ),
                        'title' => esc_html__( 'Medium wide image', 'equadio' ),
                        'subst' => 'trx_addons-thumb-wide',
                    ),
                    // Medium square image (for blogger, team)
                    'equadio-thumb-quadratic'        => array(
                        'size'  => array( 594, 606, true ),
                        'title' => esc_html__( 'Medium quadratic image', 'equadio' ),
                        'subst' => 'trx_addons-thumb-quadratic',
                    ),
                    // Big rectangle image (for events)
                    'equadio-thumb-rectangle'        => array(
                        'size'  => array( 842, 533, true ),
                        'title' => esc_html__( 'Big rectangle image', 'equadio' ),
                        'subst' => 'trx_addons-thumb-rectangle',
                    ),

				)
			)
		);
	}
}


//--------------------------------------------
// BLOG STYLES
//--------------------------------------------
if ( ! function_exists( 'equadio_skin_setup_blog_styles' ) ) {
	add_action( 'after_setup_theme', 'equadio_skin_setup_blog_styles', 1 );
	function equadio_skin_setup_blog_styles() {

		$blog_styles = array(
			'excerpt' => array(
				'title'   => esc_html__( 'Standard', 'equadio' ),
				'archive' => 'index',
				'item'    => 'templates/content-excerpt',
				'styles'  => 'excerpt',
				'icon'    => "images/theme-options/blog-style/excerpt.png",
			),
			'band'    => array(
				'title'   => esc_html__( 'Band', 'equadio' ),
				'archive' => 'index',
				'item'    => 'templates/content-band',
				'styles'  => 'band',
				'icon'    => "images/theme-options/blog-style/band.png",
			),
			'classic' => array(
				'title'   => esc_html__( 'Classic', 'equadio' ),
				'archive' => 'index',
				'item'    => 'templates/content-classic',
				'columns' => array( 2, 3, 4 ),
				'styles'  => 'classic',
				'icon'    => "images/theme-options/blog-style/classic-%d.png",
				'new_row' => true,
			),
		);
		if ( ! EQUADIO_THEME_FREE ) {
			$blog_styles['classic-masonry']   = array(
				'title'   => esc_html__( 'Classic Masonry', 'equadio' ),
				'archive' => 'index',
				'item'    => 'templates/content-classic',
				'columns' => array( 2, 3, 4 ),
				'styles'  => array( 'classic', 'masonry' ),
				'scripts' => 'masonry',
				'icon'    => "images/theme-options/blog-style/classic-masonry-%d.png",
				'new_row' => true,
			);
			$blog_styles['portfolio'] = array(
				'title'   => esc_html__( 'Portfolio', 'equadio' ),
				'archive' => 'index',
				'item'    => 'templates/content-portfolio',
				'columns' => array( 2, 3, 4 ),
				'styles'  => 'portfolio',
				'icon'    => "images/theme-options/blog-style/portfolio-%d.png",
				'new_row' => true,
			);
			$blog_styles['portfolio-masonry'] = array(
				'title'   => esc_html__( 'Portfolio Masonry', 'equadio' ),
				'archive' => 'index',
				'item'    => 'templates/content-portfolio',
				'columns' => array( 2, 3, 4 ),
				'styles'  => array( 'portfolio', 'masonry' ),
				'scripts' => 'masonry',
				'icon'    => "images/theme-options/blog-style/portfolio-masonry-%d.png",
				'new_row' => true,
			);
		}
		equadio_storage_set( 'blog_styles', apply_filters( 'equadio_filter_add_blog_styles', $blog_styles ) );
	}
}


//--------------------------------------------
// SINGLE STYLES
//--------------------------------------------
if ( ! function_exists( 'equadio_skin_setup_single_styles' ) ) {
	add_action( 'after_setup_theme', 'equadio_skin_setup_single_styles', 1 );
	function equadio_skin_setup_single_styles() {

		equadio_storage_set( 'single_styles', apply_filters( 'equadio_filter_add_single_styles', array(
			'style-1'   => array(
				'title'       => esc_html__( 'Style 1', 'equadio' ),
				'description' => esc_html__( 'Fullwidth image is above the content area, the title and meta are over the image', 'equadio' ),
				'styles'      => 'style-1',
				'icon'        => "images/theme-options/single-style/style-1.png",
			),
			'style-2'   => array(
				'title'       => esc_html__( 'Style 2', 'equadio' ),
				'description' => esc_html__( 'Fullwidth image is above the content area, the title and meta are inside the content area', 'equadio' ),
				'styles'      => 'style-2',
				'icon'        => "images/theme-options/single-style/style-2.png",
			),
			'style-3'   => array(
				'title'       => esc_html__( 'Style 3', 'equadio' ),
				'description' => esc_html__( 'Fullwidth image is above the content area, the title and meta are below the image', 'equadio' ),
				'styles'      => 'style-3',
				'icon'        => "images/theme-options/single-style/style-3.png",
			),
			'style-4'   => array(
				'title'       => esc_html__( 'Style 4', 'equadio' ),
				'description' => esc_html__( 'Boxed image is above the content area, the title and meta are above the image', 'equadio' ),
				'styles'      => 'style-4',
				'icon'        => "images/theme-options/single-style/style-4.png",
			),
			'style-5'   => array(
				'title'       => esc_html__( 'Style 5', 'equadio' ),
				'description' => esc_html__( 'Boxed image is inside the content area, the title and meta are above the content area', 'equadio' ),
				'styles'      => 'style-5',
				'icon'        => "images/theme-options/single-style/style-5.png",
			),
			'style-6'   => array(
				'title'       => esc_html__( 'Style 6', 'equadio' ),
				'description' => esc_html__( 'Boxed image, the title and meta are inside the content area, the title and meta are above the image', 'equadio' ),
				'styles'      => 'style-6',
				'icon'        => "images/theme-options/single-style/style-6.png",
			),

		) ) );
	}
}
