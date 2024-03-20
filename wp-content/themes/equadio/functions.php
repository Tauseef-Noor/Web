<?php
/**
 * Theme functions: init, enqueue scripts and styles, include required files and widgets
 *
 * @package EQUADIO
 * @since EQUADIO 1.0
 */

if ( ! defined( 'EQUADIO_THEME_DIR' ) ) {
	define( 'EQUADIO_THEME_DIR', trailingslashit( get_template_directory() ) );
}
if ( ! defined( 'EQUADIO_THEME_URL' ) ) {
	define( 'EQUADIO_THEME_URL', trailingslashit( get_template_directory_uri() ) );
}
if ( ! defined( 'EQUADIO_CHILD_DIR' ) ) {
	define( 'EQUADIO_CHILD_DIR', trailingslashit( get_stylesheet_directory() ) );
}
if ( ! defined( 'EQUADIO_CHILD_URL' ) ) {
	define( 'EQUADIO_CHILD_URL', trailingslashit( get_stylesheet_directory_uri() ) );
}

//-------------------------------------------------------
//-- Theme init
//-------------------------------------------------------

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

if ( ! function_exists( 'equadio_theme_setup1' ) ) {
	add_action( 'after_setup_theme', 'equadio_theme_setup1', 1 );
	function equadio_theme_setup1() {
		// Make theme available for translation
		// Translations can be filed in the /languages directory
		// Attention! Translations must be loaded before first call any translation functions!
		load_theme_textdomain( 'equadio', equadio_get_folder_dir( 'languages' ) );
	}
}

if ( ! function_exists( 'equadio_theme_setup' ) ) {
	add_action( 'after_setup_theme', 'equadio_theme_setup' );
	function equadio_theme_setup() {

		// Set theme content width
		$GLOBALS['content_width'] = apply_filters( 'equadio_filter_content_width', equadio_get_theme_option( 'page_width' ) );

		// Allow external updtates
		if ( EQUADIO_THEME_ALLOW_UPDATE ) {
			add_theme_support( 'theme-updates-allowed' );
		}

		// Add default posts and comments RSS feed links to head
		add_theme_support( 'automatic-feed-links' );

		// Custom header setup
		add_theme_support( 'custom-header',
			array(
				'header-text' => false,
				'video'       => true,
			)
		);

		// Custom logo
		add_theme_support( 'custom-logo',
			array(
				'width'       => 250,
				'height'      => 60,
				'flex-width'  => true,
				'flex-height' => true,
			)
		);
		// Custom backgrounds setup
		add_theme_support( 'custom-background', array() );

		// Partial refresh support in the Customize
		add_theme_support( 'customize-selective-refresh-widgets' );

		// Supported posts formats
		add_theme_support( 'post-formats', array( 'gallery', 'video', 'audio', 'link', 'quote', 'image', 'status', 'aside', 'chat' ) );

		// Autogenerate title tag
		add_theme_support( 'title-tag' );

		// Add theme menus
		add_theme_support( 'nav-menus' );

		// Switch default markup for search form, comment form, and comments to output valid HTML5.
		add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption' ) );

		// Register navigation menu
		register_nav_menus(
			array(
				'menu_main'   => esc_html__( 'Main Menu', 'equadio' ),
				'menu_mobile' => esc_html__( 'Mobile Menu', 'equadio' ),
				'menu_footer' => esc_html__( 'Footer Menu', 'equadio' ),
			)
		);

		// Register theme-specific thumb sizes
		add_theme_support( 'post-thumbnails' );
		set_post_thumbnail_size( 370, 0, false );
		$thumb_sizes = equadio_storage_get( 'theme_thumbs' );
		$mult        = equadio_get_theme_option( 'retina_ready', 1 );
		if ( $mult > 1 ) {
			$GLOBALS['content_width'] = apply_filters( 'equadio_filter_content_width', 1170 * $mult );
		}
		foreach ( $thumb_sizes as $k => $v ) {
			add_image_size( $k, $v['size'][0], $v['size'][1], $v['size'][2] );
			if ( $mult > 1 ) {
				add_image_size( $k . '-@retina', $v['size'][0] * $mult, $v['size'][1] * $mult, $v['size'][2] );
			}
		}
		// Add new thumb names
		add_filter( 'image_size_names_choose', 'equadio_theme_thumbs_sizes' );

		// Excerpt filters
		add_filter( 'excerpt_length', 'equadio_excerpt_length' );
		add_filter( 'excerpt_more', 'equadio_excerpt_more' );

		// Comment form
		add_filter( 'comment_form_fields', 'equadio_comment_form_fields' );
		add_filter( 'comment_form_fields', 'equadio_comment_form_agree', 11 );

		// Add required meta tags in the head
		add_action( 'wp_head', 'equadio_wp_head', 0 );

		// Load current page/post customization (if present)
		add_action( 'wp_footer', 'equadio_wp_footer' );
		add_action( 'admin_footer', 'equadio_wp_footer' );

		// Enqueue scripts and styles for the frontend
		add_action( 'wp_enqueue_scripts', 'equadio_wp_styles', 1000 );              // priority 1000 - load main theme styles
		add_action( 'wp_enqueue_scripts', 'equadio_wp_styles_plugins', 1100 );      // priority 1100 - load styles of the supported plugins
		add_action( 'wp_enqueue_scripts', 'equadio_wp_styles_custom', 1200 );       // priority 1200 - load styles with custom fonts and colors
		add_action( 'wp_enqueue_scripts', 'equadio_wp_styles_child', 1500 );        // priority 1500 - load styles of the child theme
		add_action( 'wp_enqueue_scripts', 'equadio_wp_styles_responsive', 2000 );   // priority 2000 - load responsive styles after all other styles

		// Enqueue scripts for the frontend
		add_action( 'wp_enqueue_scripts', 'equadio_wp_scripts', 1000 );             // priority 1000 - load main theme scripts
		add_action( 'wp_footer', 'equadio_localize_scripts' );

		// Add body classes
		add_filter( 'body_class', 'equadio_add_body_classes' );

		// Register sidebars
		add_action( 'widgets_init', 'equadio_register_sidebars' );
	}
}


//-------------------------------------------------------
//-- Theme styles
//-------------------------------------------------------

// Load frontend styles
if ( ! function_exists( 'equadio_wp_styles' ) ) {	
	function equadio_wp_styles() {

		// Links to selected fonts
		$links = equadio_theme_fonts_links();
		if ( count( $links ) > 0 ) {
			foreach ( $links as $slug => $link ) {
				wp_enqueue_style( sprintf( 'equadio-font-%s', $slug ), $link, array(), null );
			}
		}

		// Font icons styles must be loaded before main stylesheet
		// This style NEED the theme prefix, because style 'fontello' in some plugin contain different set of characters
		// and can't be used instead this style!
		wp_enqueue_style( 'fontello-icons', equadio_get_file_url( 'css/font-icons/css/fontello.css' ), array(), null );

		// Load main stylesheet
		$main_stylesheet = EQUADIO_THEME_URL . 'style.css';
		wp_enqueue_style( 'equadio-style', $main_stylesheet, array(), null );

		// Add custom bg image
		$bg_image = equadio_remove_protocol_from_url( equadio_get_theme_option( 'front_page_bg_image' ), false );
		if ( is_front_page() && ! empty( $bg_image ) && equadio_is_on( equadio_get_theme_option( 'front_page_enabled' ) ) ) {
			// Add custom bg image for the Front page
			equadio_add_inline_css( 'body.frontpage, body.home-page { background-image:url(' . esc_url( $bg_image ) . ') !important }' );
		} else {
			// Add custom bg image for the body_style == 'boxed'
			$bg_image = equadio_get_theme_option( 'boxed_bg_image' );
			if ( equadio_get_theme_option( 'body_style' ) == 'boxed' && ! empty( $bg_image ) ) {
				equadio_add_inline_css( '.body_style_boxed { background-image:url(' . esc_url( $bg_image ) . ') !important }' );
			}
		}

		// Add post nav background
		equadio_add_bg_in_post_nav();
	}
}

// Load styles of the supported plugins
if ( ! function_exists( 'equadio_wp_styles_plugins' ) ) {	
	function equadio_wp_styles_plugins() {
		if ( equadio_is_off( equadio_get_theme_option( 'debug_mode' ) ) ) {
			wp_enqueue_style( 'equadio-plugins', equadio_get_file_url( 'css/__plugins.css' ), array(), null );
		}
	}
}

// Load styles with custom fonts and colors
if ( ! function_exists( 'equadio_wp_styles_custom' ) ) {	
	function equadio_wp_styles_custom() {
		if ( ! is_customize_preview() && equadio_is_off( equadio_get_theme_option( 'debug_mode' ) ) ) {
			wp_enqueue_style( 'equadio-custom', equadio_get_file_url( 'css/__custom.css' ), array(), null );
		} else {
			wp_enqueue_style( 'equadio-custom', equadio_get_file_url( 'css/__custom-inline.css' ), array(), null );
			wp_add_inline_style( 'equadio-custom', equadio_customizer_get_css() );
		}
	}
}

// Load child-theme stylesheet (if different) after all theme styles
if ( ! function_exists( 'equadio_wp_styles_child' ) ) {	
	function equadio_wp_styles_child() {
		$main_stylesheet  = EQUADIO_THEME_URL . 'style.css';
		$child_stylesheet = EQUADIO_CHILD_URL . 'style.css';
		if ( $child_stylesheet != $main_stylesheet ) {
			wp_enqueue_style( 'equadio-child-style', $child_stylesheet, array( 'equadio-style' ), null );
		}
	}
}

// Load responsive styles (priority 2000 - load it after main styles and plugins custom styles)
if ( ! function_exists( 'equadio_wp_styles_responsive' ) ) {	
	function equadio_wp_styles_responsive() {
		if ( equadio_is_off( equadio_get_theme_option( 'debug_mode' ) ) ) {
			wp_enqueue_style( 'equadio-responsive', equadio_get_file_url( 'css/__responsive.css' ), array(), null );
		} else {
			wp_enqueue_style( 'equadio-responsive', equadio_get_file_url( 'css/responsive.css' ), array(), null );
		}
	}
}


//-------------------------------------------------------
//-- Theme scripts
//-------------------------------------------------------

// Load frontend scripts
if ( ! function_exists( 'equadio_wp_scripts' ) ) {	
	function equadio_wp_scripts() {
		$blog_archive = equadio_storage_get( 'blog_archive' ) === true || is_home();
		$blog_style   = equadio_get_theme_option( 'blog_style' );
		$use_masonry  = false;
		if ( strpos( $blog_style, 'blog-custom-' ) === 0 ) {
			$blog_id   = equadio_get_custom_blog_id( $blog_style );
			$blog_meta = equadio_get_custom_layout_meta( $blog_id );
			if ( ! empty( $blog_meta['scripts_required'] ) && ! equadio_is_off( $blog_meta['scripts_required'] ) ) {
				$blog_style  = $blog_meta['scripts_required'];
				$use_masonry = strpos( $blog_meta['scripts_required'], 'masonry' ) !== false;
			}
		} else {
			$blog_parts  = explode( '_', $blog_style );
			$blog_style  = $blog_parts[0];
			$use_masonry = equadio_is_blog_style_use_masonry( $blog_style );
		}

		// Superfish Menu
		// Attention! To prevent duplicate this script in the plugin and in the menu, don't merge it!
		wp_enqueue_script( 'superfish', equadio_get_file_url( 'js/superfish/superfish.min.js' ), array( 'jquery' ), null, true );

		// Merged scripts
		if ( equadio_is_off( equadio_get_theme_option( 'debug_mode' ) ) ) {
			wp_enqueue_script( 'equadio-init', equadio_get_file_url( 'js/__scripts.js' ), array( 'jquery' ), null, true );
		} else {
			// Skip link focus
			wp_enqueue_script( 'skip-link-focus-fix', equadio_get_file_url( 'js/skip-link-focus-fix/skip-link-focus-fix.js' ), null, true );
			// Background video
			$header_video = equadio_get_header_video();
			if ( ! empty( $header_video ) && ! equadio_is_inherit( $header_video ) ) {
				if ( equadio_is_youtube_url( $header_video ) ) {
					wp_enqueue_script( 'jquery-tubular', equadio_get_file_url( 'js/tubular/jquery.tubular.js' ), array( 'jquery' ), null, true );
				} else {
					wp_enqueue_script( 'bideo', equadio_get_file_url( 'js/bideo/bideo.js' ), array(), null, true );
				}
			}
			// Theme scripts
			wp_enqueue_script( 'equadio-utils', equadio_get_file_url( 'js/utils.js' ), array( 'jquery' ), null, true );
			wp_enqueue_script( 'equadio-init', equadio_get_file_url( 'js/init.js' ), array( 'jquery' ), null, true );
		}

		// Load masonry scripts
		if ( ( $blog_archive && $use_masonry ) || ( is_singular( 'post' ) && str_replace( 'post-format-', '', get_post_format() ) == 'gallery' ) ) {
			equadio_load_masonry_scripts();
		}

		// Load tabs to show filters
		if ( $blog_archive && ! is_customize_preview() && ! equadio_is_off( equadio_get_theme_option( 'show_filters' ) ) ) {
			wp_enqueue_script( 'jquery-ui-tabs', false, array( 'jquery', 'jquery-ui-core' ), null, true );
		}

		// Comments
		if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
			wp_enqueue_script( 'comment-reply' );
		}

		// Media elements library
		if ( equadio_get_theme_setting( 'use_mediaelements' ) ) {
			wp_enqueue_style( 'mediaelement' );
			wp_enqueue_style( 'wp-mediaelement' );
			wp_enqueue_script( 'mediaelement' );
			wp_enqueue_script( 'wp-mediaelement' );
		}
	}
}


// Add variables to the scripts in the frontend
if ( ! function_exists( 'equadio_localize_scripts' ) ) {	
	function equadio_localize_scripts() {

		$video = equadio_get_header_video();

		wp_localize_script(
			'equadio-init', 'EQUADIO_STORAGE', apply_filters(
				'equadio_filter_localize_script', array(
					// AJAX parameters
					'ajax_url'            => esc_url( admin_url( 'admin-ajax.php' ) ),
					'ajax_nonce'          => esc_attr( wp_create_nonce( admin_url( 'admin-ajax.php' ) ) ),

					// Site base url
					'site_url'            => get_home_url(),
					'theme_url'           => EQUADIO_THEME_URL,

					// Site color scheme
					'site_scheme'         => sprintf( 'scheme_%s', equadio_get_theme_option( 'color_scheme' ) ),

					// User logged in
					'user_logged_in'      => is_user_logged_in() ? true : false,

					// Window width to switch the site header to the mobile layout
					'mobile_layout_width' => 767,
					'mobile_device'       => wp_is_mobile(),

					// Sidemenu options
					'menu_side_stretch'   => equadio_get_theme_option( 'menu_side_stretch' ) > 0,
					'menu_side_icons'     => equadio_get_theme_option( 'menu_side_icons' ) > 0,

					// Video background
					'background_video'    => equadio_is_from_uploads( $video ) ? $video : '',

					// Video and Audio tag wrapper
					'use_mediaelements'   => equadio_get_theme_setting( 'use_mediaelements' ) ? true : false,

					// Allow open full post in the blog
					'open_full_post'      => equadio_get_theme_option( 'open_full_post_in_blog' ) > 0,

					// Which block to load in the single posts ?
					'which_block_load'    => equadio_get_theme_option( 'posts_navigation_scroll_which_block' ),

					// Current mode
					'admin_mode'          => false,

					// Strings for translation
					'msg_ajax_error'      => esc_html__( 'Invalid server answer!', 'equadio' ),
				)
			)
		);
	}
}

// Enqueue masonry, portfolio and gallery-specific scripts
if ( ! function_exists( 'equadio_load_masonry_scripts' ) ) {
	function equadio_load_masonry_scripts() {
		static $once = true;
		if ( $once ) {
			$once = false;
			wp_enqueue_script( 'imagesloaded' );
			wp_enqueue_script( 'masonry' );
		}
	}
}

// Disable lazy load for images
if ( ! function_exists( 'equadio_lazy_load_off' ) ) {
	function equadio_lazy_load_off() {
		static $once = true;
		if ( $once ) {
			$once = false;
			add_filter( 'wp_lazy_loading_enabled', '__return_false' );
		}
	}
}

// Enqueue specific styles and scripts for blog style
if ( ! function_exists( 'equadio_load_specific_scripts' ) ) {
	add_filter( 'equadio_filter_enqueue_blog_scripts', 'equadio_load_specific_scripts', 10, 5 );
	function equadio_load_specific_scripts( $load, $blog_style, $script_slug, $list, $responsive ) {
		if ( 'masonry' == $script_slug && false === $list ) { // if list === false - called from enqueue_scripts, true - called from merge_script
			equadio_load_masonry_scripts();
			$load = false;
		}
		return $load;
	}
}


//-------------------------------------------------------
//-- Head, body and footer
//-------------------------------------------------------

//  Add meta tags in the header for frontend
if ( ! function_exists( 'equadio_wp_head' ) ) {	
	function equadio_wp_head() {
		?>
		<meta charset="<?php bloginfo( 'charset' ); ?>">
		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
		<meta name="format-detection" content="telephone=no">
		<link rel="profile" href="//gmpg.org/xfn/11">
		<?php
		if ( is_singular() && pings_open() ) {
			?>
			<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
			<?php
		}	
	}
}

// Add theme specified classes to the body
if ( ! function_exists( 'equadio_add_body_classes' ) ) {	
	function equadio_add_body_classes( $classes ) {
		$classes[] = 'scheme_' . esc_attr( equadio_get_theme_option( 'color_scheme' ) );

		$blog_mode = equadio_storage_get( 'blog_mode' );
		$classes[] = 'blog_mode_' . esc_attr( $blog_mode );
		$classes[] = 'body_style_' . esc_attr( equadio_get_theme_option( 'body_style' ) );

		if ( in_array( $blog_mode, array( 'post', 'page' ) ) || apply_filters( 'equadio_filter_single_post_header', is_singular( 'post' ) ) ) {
			$classes[] = 'is_single';
		} else {
			$classes[] = ' is_stream';
			$classes[] = 'blog_style_' . esc_attr( equadio_get_theme_option( 'blog_style' ) );
			if ( equadio_storage_get( 'blog_template' ) > 0 ) {
				$classes[] = 'blog_template';
			}
		}

		if ( apply_filters( 'equadio_filter_single_post_header', is_singular( 'post' ) || is_singular( 'attachment' ) ) ) {
			$classes[] = 'single_style_' . esc_attr( equadio_get_theme_option( 'single_style' ) );
		}

		if ( equadio_sidebar_present() ) {
			$classes[] = 'sidebar_show sidebar_' . esc_attr( equadio_get_theme_option( 'sidebar_position' ) );
			$classes[] = 'sidebar_small_screen_' . esc_attr( equadio_get_theme_option( 'sidebar_position_ss' ) );
		} else {
			$expand = equadio_get_theme_option( 'expand_content' );
			// Compatibility with old versions
			if ( "={$expand}" == '=0' ) {
				$expand = 'normal';
			} else if ( "={$expand}" == '=1' ) {
				$expand = 'expand';
			}
			if ( 'narrow' == $expand && ! is_singular( 'post' ) ) {
				$expand = 'normal';
			}
			$classes[] = 'sidebar_hide';
			$classes[] = "{$expand}_content";
		}

		if ( equadio_is_on( equadio_get_theme_option( 'remove_margins' ) ) ) {
			$classes[] = 'remove_margins';
		}

		$bg_image = equadio_get_theme_option( 'front_page_bg_image' );
		if ( is_front_page() && equadio_is_on( equadio_get_theme_option( 'front_page_enabled' ) ) && ! empty( $bg_image ) ) {
			$classes[] = 'with_bg_image';
		}

		$classes[] = 'trx_addons_' . esc_attr( equadio_exists_trx_addons() ? 'present' : 'absent' );

		$classes[] = 'header_type_' . esc_attr( equadio_get_theme_option( 'header_type' ) );
		$classes[] = 'header_style_' . esc_attr( 'default' == equadio_get_theme_option( 'header_type' ) ? 'header-default' : equadio_get_theme_option( 'header_style' ) );
		$header_position = equadio_get_theme_option( 'header_position' );
		if ( 'over' == $header_position && is_single() && ! has_post_thumbnail() ) {
			$header_position = 'default';
		}
		$classes[] = 'header_position_' . esc_attr( $header_position );

		$menu_side = equadio_get_theme_option( 'menu_side' );
		$classes[] = 'menu_side_' . esc_attr( $menu_side ) . ( in_array( $menu_side, array( 'left', 'right' ) ) ? ' menu_side_present' : '' );
		$classes[] = 'no_layout';

		if ( equadio_get_theme_setting( 'fixed_blocks_sticky' ) ) {
			$classes[] = 'fixed_blocks_sticky';
		}

		return $classes;
	}
}

// Load current page/post customization (if present)
if ( ! function_exists( 'equadio_wp_footer' ) ) {	
	function equadio_wp_footer() {
		// Add header zoom
		$header_zoom = max( 0.2, min( 2, (float) equadio_get_theme_option( 'header_zoom' ) ) );
		if ( 1 != $header_zoom ) {
			equadio_add_inline_css( ".sc_layouts_title_title{font-size:{$header_zoom}em}" );
		}
		// Add logo zoom
		$logo_zoom = max( 0.2, min( 2, (float) equadio_get_theme_option( 'logo_zoom' ) ) );
		if ( 1 != $logo_zoom ) {
			equadio_add_inline_css( ".custom-logo-link,.sc_layouts_logo{font-size:{$logo_zoom}em}" );
		}
		// Put inline styles to the output
		$css = equadio_get_inline_css();
		if ( ! empty( $css ) ) {
			wp_enqueue_style( 'equadio-inline-styles', equadio_get_file_url( 'css/__inline.css' ), array(), null );
			wp_add_inline_style( 'equadio-inline-styles', $css );
		}
	}
}


//-------------------------------------------------------
//-- Sidebars and widgets
//-------------------------------------------------------

// Register widgetized areas
if ( ! function_exists( 'equadio_register_sidebars' ) ) {	
	function equadio_register_sidebars() {
		$sidebars = equadio_get_sidebars();
		if ( is_array( $sidebars ) && count( $sidebars ) > 0 ) {
			$cnt = 0;
			foreach ( $sidebars as $id => $sb ) {
				$cnt++;
				register_sidebar(
					apply_filters( 'equadio_filter_register_sidebar',
						array(
							'name'          => $sb['name'],
							'description'   => $sb['description'],
							// Translators: Add the sidebar number to the id
							'id'            => ! empty( $id ) ? $id : sprintf( 'theme_sidebar_%d', $cnt),
							'before_widget' => '<aside id="%1$s" class="widget %2$s">',
							'after_widget'  => '</aside>',
							'before_title'  => '<h5 class="widget_title">',
							'after_title'   => '</h5>',
						)
					)
				);
			}
		}
	}
}

// Return theme specific widgetized areas
if ( ! function_exists( 'equadio_get_sidebars' ) ) {
	function equadio_get_sidebars() {
		$list = apply_filters(
			'equadio_filter_list_sidebars', array(
				'sidebar_widgets'       => array(
					'name'        => esc_html__( 'Sidebar Widgets', 'equadio' ),
					'description' => esc_html__( 'Widgets to be shown on the main sidebar', 'equadio' ),
				),
				'header_widgets'        => array(
					'name'        => esc_html__( 'Header Widgets', 'equadio' ),
					'description' => esc_html__( 'Widgets to be shown at the top of the page (in the page header area)', 'equadio' ),
				),
				'above_page_widgets'    => array(
					'name'        => esc_html__( 'Top Page Widgets', 'equadio' ),
					'description' => esc_html__( 'Widgets to be shown below the header, but above the content and sidebar', 'equadio' ),
				),
				'above_content_widgets' => array(
					'name'        => esc_html__( 'Above Content Widgets', 'equadio' ),
					'description' => esc_html__( 'Widgets to be shown above the content, near the sidebar', 'equadio' ),
				),
				'below_content_widgets' => array(
					'name'        => esc_html__( 'Below Content Widgets', 'equadio' ),
					'description' => esc_html__( 'Widgets to be shown below the content, near the sidebar', 'equadio' ),
				),
				'below_page_widgets'    => array(
					'name'        => esc_html__( 'Bottom Page Widgets', 'equadio' ),
					'description' => esc_html__( 'Widgets to be shown below the content and sidebar, but above the footer', 'equadio' ),
				),
				'footer_widgets'        => array(
					'name'        => esc_html__( 'Footer Widgets', 'equadio' ),
					'description' => esc_html__( 'Widgets to be shown at the bottom of the page (in the page footer area)', 'equadio' ),
				),
			)
		);
		return $list;
	}
}


//-------------------------------------------------------
//-- Theme fonts
//-------------------------------------------------------

// Return links for all theme fonts
if ( ! function_exists( 'equadio_theme_fonts_links' ) ) {
	function equadio_theme_fonts_links() {
		$links = array();

		/*
		Translators: If there are characters in your language that are not supported
		by chosen font(s), translate this to 'off'. Do not translate into your own language.
		*/
		$google_fonts_enabled = ( 'off' !== esc_html_x( 'on', 'Google fonts: on or off', 'equadio' ) );
		$custom_fonts_enabled = ( 'off' !== esc_html_x( 'on', 'Custom fonts (included in the theme): on or off', 'equadio' ) );

		if ( ( $google_fonts_enabled || $custom_fonts_enabled ) && ! equadio_storage_empty( 'load_fonts' ) ) {
			$load_fonts = equadio_storage_get( 'load_fonts' );
			if ( count( $load_fonts ) > 0 ) {
				$google_fonts = '';
				foreach ( $load_fonts as $font ) {
					$url = '';
					if ( $custom_fonts_enabled && empty( $font['styles'] ) ) {
						$slug = equadio_get_load_fonts_slug( $font['name'] );
						$url  = equadio_get_file_url( "css/font-face/{$slug}/stylesheet.css" );
						if ( ! empty( $url ) ) {
							$links[ $slug ] = $url;
						}
					}
					if ( $google_fonts_enabled && empty( $url ) ) {
						// Attention! Using '%7C' instead '|' damage loading second+ fonts
						$google_fonts .= ( $google_fonts ? '%7C' : '' )
										. str_replace( ' ', '+', $font['name'] )
										. ':'
										. ( empty( $font['styles'] ) ? '400,400italic,700,700italic' : $font['styles'] );
					}
				}
				if ( $google_fonts_enabled && ! empty( $google_fonts ) ) {
					$google_fonts_subset = equadio_get_theme_option( 'load_fonts_subset' );
					$links['google_fonts'] = esc_url( "//fonts.googleapis.com/css?family={$google_fonts}&subset={$google_fonts_subset}" );
				}
			}
		}
		return $links;
	}
}

// Return links for WP Editor
if ( ! function_exists( 'equadio_theme_fonts_for_editor' ) ) {
	function equadio_theme_fonts_for_editor() {
		$links = array_values( equadio_theme_fonts_links() );
		if ( is_array( $links ) && count( $links ) > 0 ) {
			for ( $i = 0; $i < count( $links ); $i++ ) {
				$links[ $i ] = str_replace( ',', '%2C', $links[ $i ] );
			}
		}
		return $links;
	}
}


//-------------------------------------------------------
//-- The Excerpt
//-------------------------------------------------------
if ( ! function_exists( 'equadio_excerpt_length' ) ) {
	function equadio_excerpt_length( $length ) {
		$blog_style = explode( '_', equadio_get_theme_option( 'blog_style' ) );
		return max( 0, round( equadio_get_theme_option( 'excerpt_length' ) / ( in_array( $blog_style[0], array( 'classic', 'masonry', 'portfolio' ) ) ? 2 : 1 ) ) );
	}
}

if ( ! function_exists( 'equadio_excerpt_more' ) ) {	
	function equadio_excerpt_more( $more ) {
		return '&hellip;';
	}
}


//-------------------------------------------------------
//-- Comments
//-------------------------------------------------------

// Comment form fields order
if ( ! function_exists( 'equadio_comment_form_fields' ) ) {	
	function equadio_comment_form_fields( $comment_fields ) {
		if ( equadio_get_theme_setting( 'comment_after_name' ) ) {
			$keys = array_keys( $comment_fields );
			if ( 'comment' == $keys[0] ) {
				$comment_fields['comment'] = array_shift( $comment_fields );
			}
		}
		return $comment_fields;
	}
}

// Add checkbox with "I agree ..."
if ( ! function_exists( 'equadio_comment_form_agree' ) ) {	
	function equadio_comment_form_agree( $comment_fields ) {
		$privacy_text = equadio_get_privacy_text();
		if ( ! empty( $privacy_text )
			&& ( ! function_exists( 'equadio_exists_gdpr_framework' ) || ! equadio_exists_gdpr_framework() )
			&& ( ! function_exists( 'equadio_exists_wp_gdpr_compliance' ) || ! equadio_exists_wp_gdpr_compliance() )
		) {
			$comment_fields['i_agree_privacy_policy'] = equadio_single_comments_field(
				array(
					'form_style'        => 'default',
					'field_type'        => 'checkbox',
					'field_req'         => '',
					'field_icon'        => '',
					'field_value'       => '1',
					'field_name'        => 'i_agree_privacy_policy',
					'field_title'       => $privacy_text,
				)
			);
		}
		return $comment_fields;
	}
}



//-------------------------------------------------------
//-- Thumb sizes
//-------------------------------------------------------
if ( ! function_exists( 'equadio_theme_thumbs_sizes' ) ) {	
	function equadio_theme_thumbs_sizes( $sizes ) {
		$thumb_sizes = equadio_storage_get( 'theme_thumbs' );
		$mult        = equadio_get_theme_option( 'retina_ready', 1 );
		foreach ( $thumb_sizes as $k => $v ) {
			$sizes[ $k ] = $v['title'];
			if ( $mult > 1 ) {
				$sizes[ $k . '-@retina' ] = $v['title'] . ' ' . esc_html__( '@2x', 'equadio' );
			}
		}
		return $sizes;
	}
}



//-------------------------------------------------------
//-- Include theme (or child) PHP-files
//-------------------------------------------------------

require_once EQUADIO_THEME_DIR . 'includes/utils.php';
require_once EQUADIO_THEME_DIR . 'includes/storage.php';

require_once EQUADIO_THEME_DIR . 'includes/lists.php';
require_once EQUADIO_THEME_DIR . 'includes/wp.php';

if ( is_admin() ) {
	require_once EQUADIO_THEME_DIR . 'includes/tgmpa/class-tgm-plugin-activation.php';
	require_once EQUADIO_THEME_DIR . 'includes/admin.php';
}

require_once EQUADIO_THEME_DIR . 'theme-options/theme-customizer.php';

require_once EQUADIO_THEME_DIR . 'front-page/front-page-options.php';

// Theme skins support
if ( defined( 'EQUADIO_ALLOW_SKINS' ) && EQUADIO_ALLOW_SKINS && file_exists( EQUADIO_THEME_DIR . 'skins/skins.php' ) ) {
	require_once EQUADIO_THEME_DIR . 'skins/skins.php';
}

// Load the following files after the skins to allow substitution of files from the skins folder
require_once equadio_get_file_dir( 'theme-specific/theme-tags.php' );                     // Substitution from skin is disallowed
require_once equadio_get_file_dir( 'theme-specific/theme-about/theme-about.php' );        // Substitution from skin is disallowed

// Free themes support
if ( EQUADIO_THEME_FREE ) {
	require_once equadio_get_file_dir( 'theme-specific/theme-about/theme-upgrade.php' );
}

require_once equadio_get_file_dir( 'theme-specific/theme-hovers/theme-hovers.php' );      // Substitution from skin is allowed

// Plugins support
$equadio_required_plugins = equadio_storage_get( 'required_plugins' );
if ( is_array( $equadio_required_plugins ) ) {
	foreach ( $equadio_required_plugins as $equadio_plugin_slug => $equadio_plugin_data ) {
		$equadio_plugin_slug = equadio_esc( $equadio_plugin_slug );
		$equadio_plugin_path = equadio_get_file_dir( sprintf( 'plugins/%1$s/%1$s.php', $equadio_plugin_slug ) );
		if ( ! empty( $equadio_plugin_path ) ) {
			require_once $equadio_plugin_path;
		}
	}
}
