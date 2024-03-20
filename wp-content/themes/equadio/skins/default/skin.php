<?php
/**
 * Skins support: Main skin file for the skin 'Default'
 *
 * Load scripts and styles,
 * and other operations that affect the appearance and behavior of the theme
 * when the skin is activated
 *
 * @package EQUADIO
 * @since EQUADIO 1.0.46
 */


// SKIN SETUP
//--------------------------------------------------------------------

// Setup fonts, colors, blog and single styles, etc.
$equadio_skin_path = equadio_get_file_dir( equadio_skins_get_current_skin_dir() . 'skin-setup.php' );
if ( ! empty( $equadio_skin_path ) ) {
	require_once $equadio_skin_path;
}

// Skin options
$equadio_skin_path = equadio_get_file_dir( equadio_skins_get_current_skin_dir() . 'skin-options.php' );
if ( ! empty( $equadio_skin_path ) ) {
	require_once $equadio_skin_path;
}

// Required plugins
$equadio_skin_path = equadio_get_file_dir( equadio_skins_get_current_skin_dir() . 'skin-plugins.php' );
if ( ! empty( $equadio_skin_path ) ) {
	require_once $equadio_skin_path;
}

// Demo import
$equadio_skin_path = equadio_get_file_dir( equadio_skins_get_current_skin_dir() . 'skin-demo-importer.php' );
if ( ! empty( $equadio_skin_path ) ) {
	require_once $equadio_skin_path;
}


// TRX_ADDONS SETUP
//--------------------------------------------------------------------

// Filter to add in the required plugins list
// Priority 11 to add new plugins to the end of the list
if ( ! function_exists( 'equadio_skin_tgmpa_required_plugins' ) ) {
	add_filter( 'equadio_filter_tgmpa_required_plugins', 'equadio_skin_tgmpa_required_plugins', 11 );
	function equadio_skin_tgmpa_required_plugins( $list = array() ) {
		// ToDo: Check if plugin is in the 'required_plugins' and add his parameters to the TGMPA-list
		//       Replace 'skin-specific-plugin-slug' to the real slug of the plugin
		if ( equadio_storage_isset( 'required_plugins', 'skin-specific-plugin-slug' ) ) {
			$list[] = array(
				'name'     => equadio_storage_get_array( 'required_plugins', 'skin-specific-plugin-slug', 'title' ),
				'slug'     => 'skin-specific-plugin-slug',
				'required' => false,
			);
		}
		return $list;
	}
}

// Filter to add/remove components of ThemeREX Addons when current skin is active
if ( ! function_exists( 'equadio_skin_trx_addons_default_components' ) ) {
	add_filter('trx_addons_filter_load_options', 'equadio_skin_trx_addons_default_components', 20);
	function equadio_skin_trx_addons_default_components($components) {
		// ToDo: Set key value in the array $components to 0 (disable component) or 1 (enable component)
		//---> For example (enable reviews for posts):
		//---> $components['components_components_reviews'] = 1;
		return $components;
	}
}

// Filter to add/remove CPT
if ( ! function_exists( 'equadio_skin_trx_addons_cpt_list' ) ) {
	add_filter('trx_addons_cpt_list', 'equadio_skin_trx_addons_cpt_list');
	function equadio_skin_trx_addons_cpt_list( $list = array() ) {
		// ToDo: Unset CPT slug from list to disable CPT when current skin is active
		//---> For example to disable CPT 'Portfolio':
		//---> unset( $list['portfolio'] );
		return $list;
	}
}

// Filter to add/remove shortcodes
if ( ! function_exists( 'equadio_skin_trx_addons_sc_list' ) ) {
	add_filter('trx_addons_sc_list', 'equadio_skin_trx_addons_sc_list');
	function equadio_skin_trx_addons_sc_list( $list = array() ) {


        //Blogger Templates
        unset($list['blogger']['templates']['news']['magazine']);
        unset($list['blogger']['templates']['default']['over_centered']);
        unset($list['blogger']['templates']['default']['over_bottom']);


        $list['blogger']['templates']['default']['only_featured'] = array(
            'title' => esc_html__('Only Featured Image', 'equadio'),
            'layout' => array(
                'featured' => array(
                ),
            )
        );

        $list['blogger']['templates']['default']['classic_alter'] = array(
        		'title' => esc_html__('Classic Grid Alternative', 'equadio'),
        		'layout' => array(
        			'featured' => array(),
        			'content' => array( 'title', 'excerpt', 'meta', 'readmore')
        		)
        );

        //Announce
        $list['blogger']['templates']['news']['announce'] = array(
            'title' => esc_html__('Announce', 'equadio'),
            'grid'  => array(
                // One post
                array(
                    'grid-layout' => array(
                        array(
                            'template' => 'default/only_featured',
                            'args' => array( 'image_ratio' => '16:9', 'columns' => 1, 'thumb_size' => 'full' )
                        ),
                    )
                ),
                // Two posts
                array(
                    'grid-layout' => array(
                        array(
                            'template' => 'default/only_featured',
                            'args' => array( 'image_ratio' => '16:9', 'columns' => 1, 'thumb_size' => 'full'  )
                        ),
                        array(
                            'template' => 'default/only_featured',
                            'args' => array( 'image_ratio' => '16:9', 'columns' => 1, 'thumb_size' => 'full'  )
                        ),
                    )
                ),
                // Three posts
                array(
                    'grid-layout' => array(
                        array(
                            'template' => 'default/only_featured',
                            'args' => array( 'image_ratio' => '16:9', 'columns' => 1, 'thumb_size' => 'full'  )
                        ),
                        array(
                            'template' => 'default/only_featured',
                            'args' => array( 'image_ratio' => '16:9', 'columns' => 1, 'thumb_size' => 'full'  )
                        ),
                        array(
                            'template' => 'default/only_featured',
                            'args' => array( 'image_ratio' => '16:9', 'columns' => 1, 'thumb_size' => 'full'  )
                        ),
                    )
                ),
                // Four posts
                array(
                    'grid-layout' => array(
                        array(
                            'template' => 'default/only_featured',
                            'args' => array( 'image_ratio' => '16:9', 'columns' => 1, 'thumb_size' => 'full'  )
                        ),
                        array(
                            'template' => 'default/only_featured',
                            'args' => array( 'image_ratio' => '16:9', 'columns' => 1, 'thumb_size' => 'quadratic'  )
                        ),
                        array(
                            'template' => 'default/only_featured',
                            'args' => array( 'image_ratio' => '16:9', 'columns' => 1, 'thumb_size' => 'quadratic'  )
                        ),
                        array(
                            'template' => 'default/only_featured',
                            'args' => array( 'image_ratio' => '16:9', 'columns' => 1, 'thumb_size' => 'full'  )
                        ),
                    )
                ),
                // Five posts
                array(
                    'grid-layout' => array(
                        array(
                            'template' => 'default/only_featured',
                            'args' => array( 'image_ratio' => '16:9', 'columns' => 1, 'thumb_size' => 'quadratic'  )
                        ),
                        array(
                            'template' => 'default/only_featured',
                            'args' => array( 'image_ratio' => '16:9', 'columns' => 1, 'thumb_size' => 'quadratic'  )
                        ),
                        array(
                            'template' => 'default/only_featured',
                            'args' => array( 'image_ratio' => '16:9', 'columns' => 1, 'thumb_size' => 'full'  )
                        ),
                        array(
                            'template' => 'default/only_featured',
                            'args' => array( 'image_ratio' => '16:9', 'columns' => 1, 'thumb_size' => 'quadratic'  )
                        ),
                        array(
                            'template' => 'default/only_featured',
                            'args' => array( 'image_ratio' => '16:9', 'columns' => 1, 'thumb_size' => 'quadratic'  )
                        ),
                    )
                ),
                // Six posts
                array(
                    'grid-layout' => array(
                        array(
                            'template' => 'default/only_featured',
                            'args' => array( 'image_ratio' => '16:9', 'columns' => 1, 'thumb_size' => 'full' )
                        ),
                        array(
                            'template' => 'default/only_featured',
                            'args' => array( 'image_ratio' => '16:9', 'columns' => 1, 'thumb_size' => 'quadratic' )
                        ),
                        array(
                            'template' => 'default/only_featured',
                            'args' => array( 'image_ratio' => '16:9', 'columns' => 1, 'thumb_size' => 'quadratic' )
                        ),
                        array(
                            'template' => 'default/only_featured',
                            'args' => array( 'image_ratio' => '16:9', 'columns' => 1, 'thumb_size' => 'quadratic' )
                        ),
                        array(
                            'template' => 'default/only_featured',
                            'args' => array( 'image_ratio' => '16:9', 'columns' => 1, 'thumb_size' => 'quadratic' )
                        ),
                        array(
                            'template' => 'default/only_featured',
                            'args' => array( 'image_ratio' => '16:9', 'columns' => 1, 'thumb_size' => 'full' )
                        ),
                    )
                ),
                // Seven posts
                array(
                    'grid-layout' => array(
                        array(
                            'template' => 'default/only_featured',
                            'args' => array( 'image_ratio' => '16:9', 'columns' => 1, 'thumb_size' => 'full' )
                        ),
                        array(
                            'template' => 'default/only_featured',
                            'args' => array( 'image_ratio' => '16:9', 'columns' => 1, 'thumb_size' => 'quadratic' )
                        ),
                        array(
                            'template' => 'default/only_featured',
                            'args' => array( 'image_ratio' => '16:9', 'columns' => 1, 'thumb_size' => 'quadratic' )
                        ),
                        array(
                            'template' => 'default/only_featured',
                            'args' => array( 'image_ratio' => '16:9', 'columns' => 1, 'thumb_size' => 'quadratic' )
                        ),
                        array(
                            'template' => 'default/only_featured',
                            'args' => array( 'image_ratio' => '16:9', 'columns' => 1, 'thumb_size' => 'quadratic' )
                        ),
                        array(
                            'template' => 'default/only_featured',
                            'args' => array( 'image_ratio' => '16:9', 'columns' => 1, 'thumb_size' => 'quadratic' )
                        ),
                        array(
                            'template' => 'default/only_featured',
                            'args' => array( 'image_ratio' => '16:9', 'columns' => 1, 'thumb_size' => 'quadratic' )
                        ),
                    )
                ),
                // Eight posts
                array(
                    'grid-layout' => array(
                        array(
                            'template' => 'default/only_featured',
                            'args' => array( 'image_ratio' => '16:9', 'columns' => 1,  'thumb_size' => 'quadratic' )
                        ),
                        array(
                            'template' => 'default/only_featured',
                            'args' => array( 'image_ratio' => '16:9', 'columns' => 1, 'thumb_size' => 'quadratic')
                        ),
                        array(
                            'template' => 'default/only_featured',
                            'args' => array( 'image_ratio' => '16:9', 'columns' => 1, 'thumb_size' => 'quadratic' )
                        ),
                        array(
                            'template' => 'default/only_featured',
                            'args' => array( 'image_ratio' => '16:9', 'columns' => 1, 'thumb_size' => 'quadratic' )
                        ),
                        array(
                            'template' => 'default/only_featured',
                            'args' => array( 'image_ratio' => '16:9', 'columns' => 1, 'thumb_size' => 'quadratic' )
                        ),
                        array(
                            'template' => 'default/only_featured',
                            'args' => array( 'image_ratio' => '16:9', 'columns' => 1, 'thumb_size' => 'quadratic' )
                        ),
                        array(
                            'template' => 'default/only_featured',
                            'args' => array( 'image_ratio' => '16:9', 'columns' => 1, 'thumb_size' => 'quadratic' )
                        ),
                        array(
                            'template' => 'default/only_featured',
                            'args' => array( 'image_ratio' => '16:9', 'columns' => 1, 'thumb_size' => 'quadratic' )
                        ),
                    )
                ),
                // Nine posts
                array(
                    'grid-layout' => array(
                        array(
                            'template' => 'default/only_featured',
                            'args' => array( 'image_ratio' => '16:9', 'columns' => 1,  'thumb_size' => 'full' )
                        ),
                        array(
                            'template' => 'default/only_featured',
                            'args' => array( 'image_ratio' => '16:9', 'columns' => 1, 'thumb_size' => 'quadratic' )
                        ),
                        array(
                            'template' => 'default/only_featured',
                            'args' => array( 'image_ratio' => '16:9', 'columns' => 1, 'thumb_size' => 'quadratic' )
                        ),
                        array(
                            'template' => 'default/only_featured',
                            'args' => array( 'image_ratio' => '16:9', 'columns' => 1, 'thumb_size' => 'quadratic' )
                        ),
                        array(
                            'template' => 'default/only_featured',
                            'args' => array( 'image_ratio' => '16:9', 'columns' => 1, 'thumb_size' => 'quadratic' )
                        ),
                        array(
                            'template' => 'default/only_featured',
                            'args' => array( 'image_ratio' => '16:9', 'columns' => 1, 'thumb_size' => 'full' )
                        ),
                        array(
                            'template' => 'default/only_featured',
                            'args' => array( 'image_ratio' => '16:9', 'columns' => 1, 'thumb_size' => 'full' )
                        ),
                        array(
                            'template' => 'default/only_featured',
                            'args' => array( 'image_ratio' => '16:9', 'columns' => 1, 'thumb_size' => 'quadratic' )
                        ),
                        array(
                            'template' => 'default/only_featured',
                            'args' => array( 'image_ratio' => '16:9', 'columns' => 1, 'thumb_size' => 'quadratic' )
                        ),
                    )
                ),
                // Ten posts
                array(
                    'grid-layout' => array(
                        array(
                            'template' => 'default/only_featured',
                            'args' => array( 'image_ratio' => '16:9', 'columns' => 1, 'thumb_size' => 'full' )
                        ),
                        array(
                            'template' => 'default/only_featured',
                            'args' => array( 'image_ratio' => '16:9', 'columns' => 1, 'thumb_size' => 'quadratic' )
                        ),
                        array(
                            'template' => 'default/only_featured',
                            'args' => array( 'image_ratio' => '16:9', 'columns' => 1, 'thumb_size' => 'quadratic' )
                        ),
                        array(
                            'template' => 'default/only_featured',
                            'args' => array( 'image_ratio' => '16:9', 'columns' => 1, 'thumb_size' => 'quadratic' )
                        ),
                        array(
                            'template' => 'default/only_featured',
                            'args' => array( 'image_ratio' => '16:9', 'columns' => 1, 'thumb_size' => 'quadratic' )
                        ),
                        array(
                            'template' => 'default/only_featured',
                            'args' => array( 'image_ratio' => '16:9', 'columns' => 1, 'thumb_size' => 'quadratic' )
                        ),
                        array(
                            'template' => 'default/only_featured',
                            'args' => array( 'image_ratio' => '16:9', 'columns' => 1, 'thumb_size' => 'quadratic' )
                        ),
                        array(
                            'template' => 'default/only_featured',
                            'args' => array( 'image_ratio' => '16:9', 'columns' => 1, 'thumb_size' => 'quadratic' )
                        ),
                        array(
                            'template' => 'default/only_featured',
                            'args' => array( 'image_ratio' => '16:9', 'columns' => 1, 'thumb_size' => 'quadratic' )
                        ),
                        array(
                            'template' => 'default/only_featured',
                            'args' => array( 'image_ratio' => '16:9', 'columns' => 1, 'thumb_size' => 'full' )
                        ),
                    )
                ),
            )
        );


		return $list;
	}
}

// Filter to add/remove widgets
if ( ! function_exists( 'equadio_skin_trx_addons_widgets_list' ) ) {
	add_filter('trx_addons_widgets_list', 'equadio_skin_trx_addons_widgets_list');
	function equadio_skin_trx_addons_widgets_list( $list = array() ) {
		// ToDo: Unset widget's slug from list to disable widget when current skin is active
		//---> For example to disable widget 'About Me':
		//---> unset( $list['aboutme'] );
		return $list;
	}
}



// SCRIPTS AND STYLES
//--------------------------------------------------

// Enqueue skin-specific scripts
// Priority 1050 -  before main theme plugins-specific (1100)
if ( ! function_exists( 'equadio_skin_frontend_scripts' ) ) {
	add_action( 'wp_enqueue_scripts', 'equadio_skin_frontend_scripts', 1050 );
	function equadio_skin_frontend_scripts() {
		$equadio_url = equadio_get_file_url( equadio_skins_get_current_skin_dir() . 'css/style.css' );
		if ( '' != $equadio_url ) {
			wp_enqueue_style( 'equadio-skin-' . esc_attr( equadio_skins_get_current_skin_name() ), $equadio_url, array(), null );
		}
		$equadio_url = equadio_get_file_url( equadio_skins_get_current_skin_dir() . 'skin.js' );
		if ( '' != $equadio_url ) {
			wp_enqueue_script( 'equadio-skin-' . esc_attr( equadio_skins_get_current_skin_name() ), $equadio_url, array( 'jquery' ), null, true );
		}
	}
}


// Custom styles
$equadio_style_path = equadio_get_file_dir( equadio_skins_get_current_skin_dir() . 'css/style.php' );
if ( ! empty( $equadio_style_path ) ) {
	require_once $equadio_style_path;
}

// Shortcodes support
//------------------------------------------------------------------------

// Add new output types (layouts) in the shortcodes
if ( ! function_exists( 'equadio_skin_trx_addons_sc_type' ) ) {
    add_filter( 'trx_addons_sc_type', 'equadio_skin_trx_addons_sc_type', 10, 2 );
    function equadio_skin_trx_addons_sc_type( $list, $sc ) {
        if ( 'trx_sc_title' == $sc ) {
            $list['decoration'] = esc_html__('Decoration', 'equadio');
        }
        if ( 'trx_sc_services' == $sc ) {
            $list['alter'] = esc_html__('Alter', 'equadio');
        }
        return $list;
    }
}

// Add new params to the default shortcode's atts
if ( ! function_exists( 'equadio_skin_trx_addons_sc_atts' ) ) {
    add_filter('trx_addons_sc_atts', 'equadio_skin_trx_addons_sc_atts', 10, 2);
    function equadio_skin_trx_addons_sc_atts($atts, $sc)  {
        if ( 'trx_sc_services' == $sc ) {
            $atts['show_subtitle'] = '';
        }
        if ( 'trx_sc_events' == $sc ) {
            $atts['show_excerpt'] = '';
        }
        return $atts;
    }
}
// Get thumb size for the items
if ( ! function_exists( 'equadio_skin_trx_addons_thumb_size' ) ) {
    add_filter( 'trx_addons_filter_thumb_size', 'equadio_skin_trx_addons_thumb_size', 10, 3 );
    function equadio_skin_trx_addons_thumb_size( $thumb_size = '', $type = '', $args=array() ) {

        if ( 'blogger-default' == $type && 'classic_alter' == $args['template_default']) {
            $thumb_size = 'quadratic';
        }
        if ($type == 'team-default') {
            $thumb_size = equadio_get_thumb_size('quadratic');
        }
        return $thumb_size;
    }
}

//Remove class sc_button_simple from blogger button
if ( ! function_exists( 'equadio_skin_filter_blogger_sc_item_link_classes' ) ) {
    add_filter( 'trx_addons_filter_sc_item_link_classes', 'equadio_skin_filter_blogger_sc_item_link_classes', 10, 3 );
    function equadio_skin_filter_blogger_sc_item_link_classes( $class, $sc, $args=array() ) {
        if ( 'sc_blogger' == $sc && 'default' == $args['type'] ) {
            $class = 'sc_button sc_button_size_small';
        }
        return $class;
    }
}

// Return tag SVG from specified file
if (!function_exists('equadio_get_svg_from_file')) {
    function equadio_get_svg_from_file($svg) {
        $content = equadio_fgc($svg);
        preg_match("#<\s*?svg\b[^>]*>(.*?)</svg\b[^>]*>#s", $content, $matches);
        return !empty($matches[0]) ? str_replace(array("\r", "\n"), array('', ' '), $matches[0]) : '';
    }
}

// Add item params to skills
if ( ! function_exists( 'equadio_filter_skills_add_param' ) ) {
    add_filter( 'trx_addons_sc_param_group_params', 'equadio_filter_skills_add_param', 10, 2 );
    function equadio_filter_skills_add_param( $params, $sc ) {

        if ( in_array( $sc, array( 'trx_sc_skills' ) ) ) {
            if ( isset( $params[0]['name'] ) && isset( $params[0]['label'] ) ) {
                array_splice($params, 1, 0, array( array(
                    'name'        => 'description',
                    'type' => \Elementor\Controls_Manager::TEXT,
                    'label'       => esc_html__( 'Description', 'equadio' ),
                    'label_block' => false,
                    'placeholder' => esc_html__( 'Items Description', 'equadio' ),
                    'default'     => '',
                ) ) );
            }
        }
        return $params;
    }
}

// Remove input hover effects
if ( !function_exists( 'equadio_skin_filter_get_list_input_hover' ) ) {
    add_filter( 'trx_addons_filter_get_list_input_hover', 'equadio_skin_filter_get_list_input_hover');
    function equadio_skin_filter_get_list_input_hover($list) {
        unset($list['accent']);
        unset($list['path']);
        unset($list['jump']);
        unset($list['underline']);
        unset($list['iconed']);
        return $list;
    }
}

// Add simple hover effect
if ( !function_exists( 'equadio_skin_filter_list_hovers' ) ) {
    add_filter( 'equadio_filter_list_hovers', 'equadio_skin_filter_list_hovers');
    function equadio_skin_filter_list_hovers($list) {
        $list['simple'] = esc_html__( 'Simple', 'equadio' );
        return $list;
    }
}
// Remove 'left' item position from the list
if ( !function_exists( 'equadio_skin_filter_list_share_links_positions' ) ) {
    add_filter( 'equadio_filter_list_share_links_positions', 'equadio_skin_filter_list_share_links_positions');
    function equadio_skin_filter_list_share_links_positions($list) {
        unset($list['left']);
        return $list;
    }
}
// Remove 'float' item position from the list
if ( !function_exists( 'equadio_skin_filter_list_sidebars_positions_ss' ) ) {
    add_filter( 'equadio_filter_list_sidebars_positions_ss', 'equadio_skin_filter_list_sidebars_positions_ss');
    function equadio_skin_filter_list_sidebars_positions_ss($list) {
        unset($list['float']);
        return $list;
    }
}

// Show post meta block: post date, author, categories, counters, etc.
if ( ! function_exists( 'equadio_show_post_meta' ) ) {
    function equadio_show_post_meta( $args = array() ) {
        if ( is_single() && equadio_is_off( equadio_get_theme_option( 'show_post_meta' ) ) ) {
            return ' ';  // Space is need!
        }
        $args = array_merge(
            array(
                'components'      => 'categories,date,author,comments,share,edit',
                'show_labels'     => true,
                'share_type'      => 'drop',
                'share_direction' => 'horizontal',
                'seo'             => false,
                'author_avatar'   => true,
                'date_format'     => '',
                'class'           => '',
                'add_spaces'      => false,
                'echo'            => true,
            ),
            $args
        );

        ob_start();

        $components = is_array( $args['components'] ) ? $args['components'] : explode( ',', $args['components'] );

        // Reorder meta_parts with last user's choise
        if ( equadio_storage_isset( 'options', 'meta_parts', 'val' ) ) {
            $parts = explode( '|', equadio_get_theme_option( 'meta_parts' ) );
            $list_new = array();
            foreach( $parts as $part ) {
                $part = explode( '=', $part );
                if ( in_array( $part[0], $components ) ) {
                    $list_new[] = $part[0];
                    $components = equadio_array_delete_by_value( $components, $part[0] );
                }
            }
            $components = count( $components ) > 0 ? array_merge( $list_new, $components ) : $list_new;
        }

        // Display components
        $dt_last = '';
        foreach ( $components as $comp ) {
            $comp = trim( $comp );
            if ( 'categories' == $comp ) {
                // Label 'Sponsored content' will be shown always before the categories list
                if ( equadio_exists_trx_addons() ) {
                    $meta = get_post_meta( get_the_ID(), 'trx_addons_options', true );
                    if ( ! empty( $meta['sponsored_post'] ) && 1 == (int) $meta['sponsored_post'] ) {
                        $cats = ( ! empty( $meta['sponsored_url'] )
                                ? '<a class="post_sponsored_label"'
                                . ' href="' . esc_url( $meta['sponsored_url'] ) . '"'
                                . ' target="_blank"'
                                . ( ! empty( $meta['sponsored_rel_nofollow'] ) || ! empty( $meta['sponsored_rel_sponsored'] )
                                    ? ' rel="'
                                    . trim( ( ! empty( $meta['sponsored_rel_nofollow'] ) ? 'nofollow ' : '' )
                                        . ( ! empty( $meta['sponsored_rel_sponsored'] ) ? 'sponsored' : '' )
                                    )
                                    . '"'
                                    : '' )
                                . '>'
                                : '<span class="post_sponsored_label">' )
                            . ( ! empty( $meta['sponsored_label'] )
                                ? esc_html( $meta['sponsored_label'] )
                                : esc_html__( 'Sponsored content', 'equadio' )
                            )
                            . ( ! empty( $meta['sponsored_url'] )
                                ? '</a>'
                                : '</span>'
                            );
                        equadio_show_layout( $cats, '<span class="post_meta_item post_sponsored">', '</span>');
                    }
                }
                // Post categories
                $cats = get_post_type() == 'post' ? get_the_category_list( ', ' ) : apply_filters( 'equadio_filter_get_post_categories', '' );
                if ( ! empty( $cats ) ) {
                    equadio_show_layout( $cats, '<span class="post_meta_item post_categories">', '</span>');
                }
            } elseif ( 'date' == $comp || ( 'modified' == $comp && get_post_type() != 'post' ) ) {
                // Published date
                $dt = apply_filters( 'equadio_filter_get_post_date', equadio_get_date( '', ! empty( $args['date_format'] ) ? $args['date_format'] : '' ) );
                if ( ! empty( $dt ) && ( empty( $dt_last ) || $dt_last != $dt ) ) {
                    equadio_show_layout(
                        $dt,
                        '<span class="post_meta_item post_date' . ( ! empty( $args['seo'] ) ? ' date published' : '' ) . '"'
                        . ( ! empty( $args['seo'] ) ? ' itemprop="datePublished"' : '' )
                        . '>'
                        . ( ! is_single() ? '<a href="' . esc_url( get_permalink() ) . '">' : '' )
                        . ( in_array( 'date', $components ) && in_array( 'modified', $components ) && get_post_type() == 'post' ? '<span class="post_meta_item_label">' . esc_html__( 'Published:', 'equadio' ) . '</span>' : '' ),
                        ( ! is_single() ? '</a>' : '' ) . '</span>'
                    );
                    $dt_last = $dt;
                }
            } elseif ( 'modified' == $comp && get_post_type() == 'post' ) {
                // Modified date
                $dt = apply_filters( 'equadio_filter_get_post_modified_date', equadio_get_date( get_post_modified_time( 'U' ), ! empty( $args['date_format'] ) ? $args['date_format'] : '' ) );
                if ( ! empty( $dt ) && ( empty( $dt_last ) || $dt_last != $dt ) ) {
                    equadio_show_layout(
                        $dt,
                        '<span class="post_meta_item post_date' . ( ! empty( $args['seo'] ) ? ' date updated modified' : '' ) . '"'
                        . ( ! empty( $args['seo'] ) ? ' itemprop="dateModified"' : '' )
                        . '>'
                        . ( ! is_single() ? '<a href="' . esc_url( get_permalink() ) . '">' : '' )
                        . '<span class="post_meta_item_label">' . esc_html__( 'Updated:', 'equadio' ) . '</span>',
                        ( ! is_single() ? '</a>' : '' ) . '</span>'
                    );
                    $dt_last = $dt;
                }
            } elseif ( 'author' == $comp ) {
                // Post author
                $author_id = get_the_author_meta( 'ID' );
                if ( empty( $author_id ) && ! empty( $GLOBALS['post']->post_author ) ) {
                    $author_id = $GLOBALS['post']->post_author;
                }
                if ( $author_id > 0 ) {
                    $author_link   = get_author_posts_url( $author_id );
                    $author_name   = get_the_author_meta( 'display_name', $author_id );
                    $author_avatar = ! empty( $args['author_avatar'] )
                        ? get_avatar( get_the_author_meta( 'user_email', $author_id ), apply_filters( 'equadio_filter_author_avatar_size', 56, 'post_meta' ) * equadio_get_retina_multiplier() )
                        : '';
                    echo '<a class="post_meta_item post_author" rel="author" href="' . esc_url( $author_link ) . '">'
                        . ( ! empty( $author_avatar )
                            ? sprintf( '<span class="post_author_avatar">%s</span>', $author_avatar )
                            : '<span class="post_author_by">' . esc_html__( 'By', 'equadio' ) . '</span>'
                        )
                        . '<span class="post_author_name">' . esc_html( $author_name ) . '</span>'
                        . '</a>';
                }

            } else if ( 'comments' == $comp ) {
                // Comments
                if ( !is_single() || have_comments() || comments_open() ) {
                    $post_comments = get_comments_number();
                    echo '<a href="' . esc_url( get_comments_link() ) . '" class="post_meta_item post_meta_comments icon-comment-light">'
                        . '<span class="post_meta_number">' . esc_html( equadio_num2size( $post_comments ) ) . '</span>'
                        . ( $args['show_labels'] ? '<span class="post_meta_label">' . esc_html( _n( 'Comment', 'Comments', $post_comments, 'equadio' ) ) . '</span>' : '' )
                        . '</a>';
                }

                // Views
            } else if ( 'views' == $comp ) {
                if ( function_exists( 'trx_addons_get_post_views' ) ) {
                    $post_views = trx_addons_get_post_views( get_the_ID() );
                    echo '<a href="' . esc_url( get_permalink() ) . '" class="post_meta_item post_meta_views trx_addons_icon-eye">'
                        . '<span class="post_meta_number">' . esc_html( equadio_num2size( $post_views ) ) . '</span>'
                        . ( $args['show_labels'] ? '<span class="post_meta_label">' . esc_html( _n( 'View', 'Views', $post_views, 'equadio' ) ) . '</span>' : '' )
                        . '</a>';
                }

                // Likes (Emotions)
            } else if ( 'likes' == $comp ) {
                if ( function_exists( 'trx_addons_get_post_likes' ) ) {
                    $emotions_allowed = trx_addons_is_on( trx_addons_get_option( 'emotions_allowed' ) );
                    if ( $emotions_allowed ) {
                        $post_emotions = trx_addons_get_post_emotions( get_the_ID() );
                        $post_likes = 0;
                        if ( is_array( $post_emotions ) ) {
                            foreach ( $post_emotions as $v ) {
                                $post_likes += (int) $v;
                            }
                        }
                    } else {
                        $post_likes = trx_addons_get_post_likes( get_the_ID() );
                    }
                    $liked = isset( $_COOKIE['trx_addons_likes'] ) ? sanitize_text_field($_COOKIE['trx_addons_likes']) : '';
                    $allow = strpos( sprintf( ',%s,', $liked ), sprintf( ',%d,', get_the_ID() ) ) === false;
                    echo ( true == $emotions_allowed
                            ? '<a href="' . esc_url( trx_addons_add_hash_to_url( get_permalink(), 'trx_addons_emotions' ) ) . '"'
                            . ' class="post_meta_item post_meta_emotions trx_addons_icon-angellist">'
                            : '<a href="#"'
                            . ' class="post_meta_item post_meta_likes trx_addons_icon-heart' . ( ! empty( $allow ) ? '-empty enabled' : ' disabled' ) . '"'
                            . ' title="' . ( ! empty( $allow ) ? esc_attr__( 'Like', 'equadio') : esc_attr__( 'Dislike', 'equadio' ) ) . '"'
                            . ' data-postid="' . esc_attr( get_the_ID() ) . '"'
                            . ' data-likes="' . esc_attr( $post_likes ) . '"'
                            . ' data-title-like="' . esc_attr__( 'Like', 'equadio') . '"'
                            . ' data-title-dislike="' . esc_attr__( 'Dislike', 'equadio' ) . '"'
                            . '>'
                        )
                        . '<span class="post_meta_number">' . esc_html( equadio_num2size( $post_likes ) )  . '</span>'
                        . ( $args['show_labels']
                            ? '<span class="post_meta_label">'
                            . ( true == $emotions_allowed
                                ? esc_html( _n( 'Reaction', 'Reactions', $post_likes, 'equadio' ) )
                                : esc_html( _n( 'Like', 'Likes', $post_likes, 'equadio' ) )
                            )
                            . '</span>'
                            : '' )
                        . '</a>';
                }

            } elseif ( 'share' == $comp ) {
                // Socials share
                equadio_show_share_links(
                    array(
                        'type'      => $args['share_type'],
                        'direction' => $args['share_direction'],
                        'caption'   => 'drop' == $args['share_type'] ? esc_html__( 'Share', 'equadio' ) : '',
                        'before'    => '<span class="post_meta_item post_share">',
                        'after'     => '</span>',
                    )
                );

            } elseif ( 'edit' == $comp ) {
                // Edit page link
                edit_post_link( esc_html__( 'Edit', 'equadio' ), '', '', 0, 'post_meta_item post_edit icon-pencil' );

            } else {
                // Custom counter
                do_action( 'equadio_action_show_post_meta', $comp, get_the_ID(), $args );
            }
            // Spaces between post_items
            if ( ! empty( $args['add_spaces'] ) ) {
                echo ' ';
            }
        }

        $rez = ob_get_contents();
        ob_end_clean();

        if ( ! empty( trim( $rez ) ) ) {
            $rez = '<div class="post_meta' . ( ! empty( $args['class'] ) ? ' ' . esc_attr( $args['class'] ) : '' ) . '">'
                . trim( $rez )
                . '</div>';
            if ( $args['echo'] ) {
                equadio_show_layout( $rez );
                $rez = '';
            }
        }

        return $rez;
    }
}

// Callback for output single comment layout
if ( ! function_exists( 'equadio_output_single_comment' ) ) {
    function equadio_output_single_comment( $comment, $args, $depth ) {
        switch ( $comment->comment_type ) {
            case 'pingback':
                ?>
                <li class="trackback"><?php esc_html_e( 'Trackback:', 'equadio' ); ?> <?php comment_author_link(); ?><?php edit_comment_link( esc_html__( 'Edit', 'equadio' ), '<span class="edit-link">', '<span>' ); ?>
                <?php
                break;
            case 'trackback':
                ?>
                <li class="pingback"><?php esc_html_e( 'Pingback:', 'equadio' ); ?> <?php comment_author_link(); ?><?php edit_comment_link( esc_html__( 'Edit', 'equadio' ), '<span class="edit-link">', '<span>' ); ?>
                <?php
                break;
            default:
                $author_id   = $comment->user_id;
                $author_link = ! empty( $author_id ) ? get_author_posts_url( $author_id ) : '';
                $author_post = get_the_author_meta( 'ID' ) == $author_id;
                $mult        = equadio_get_retina_multiplier();
                $comment_id  = get_comment_ID();
                ?>
            <li id="comment-<?php echo esc_attr( $comment_id ); ?>" <?php comment_class( 'comment_item' ); ?>>
                <div id="comment_body-<?php echo esc_attr( $comment_id ); ?>" class="comment_body">
                    <div class="comment_author_avatar"><?php echo get_avatar( $comment, 180 * $mult ); ?></div>
                    <div class="comment_content">
                        <div class="comment_info_wrap">
                            <div class="comment_info">
                                <?php if ( $author_post ) {	?>
                                    <div class="comment_bypostauthor">
                                        <?php
                                        esc_html_e( 'Post Author', 'equadio' );
                                        ?>
                                    </div>
                                <?php } ?>
                                <div class="comment_posted">
                                    <span class="comment_posted_label"><?php esc_html_e( 'Posted', 'equadio' ); ?></span>
                                    <span class="comment_date">
                                        <?php
                                        echo esc_html( get_comment_date( get_option( 'date_format' ) ) );
                                        ?>
                                        </span>
                                    <span class="comment_time_label"><?php esc_html_e( 'at', 'equadio' ); ?></span>
                                    <span class="comment_time">
                                        <?php
                                        echo esc_html( get_comment_date( get_option( 'time_format' ) ) );
                                        ?>
                                    </span>
                                </div>
                                <div class="comment_author_posted">
                                    <span class="comment_author"><span class="comment_author_by"><?php esc_html_e( 'by', 'equadio' ); ?></span>
                                        <?php
                                        echo ( ! empty( $author_link ) ? '<a href="' . esc_url( $author_link ) . '">' : '' )
                                            . esc_html( get_comment_author() )
                                            . ( ! empty( $author_link ) ? '</a>' : '' );
                                        ?>
                                    </span>
                                </div>
                                <?php
                                // Show rating in the comment
                                do_action( 'trx_addons_action_post_rating', 'c' . esc_attr( $comment_id ) );
                                ?>
                            </div>
                            <div class="comment_reply_wrap">
                                <?php
                                if ( 1 == $comment->comment_approved && equadio_exists_trx_addons() ) {
                                    ?>
                                    <span class="comment_counters"><?php equadio_show_comment_counters('likes,rating'); ?></span>
                                    <?php
                                }
                                if ( $depth < $args['max_depth'] ) {
                                    ?>
                                    <span class="reply comment_reply">
                                            <?php
                                            comment_reply_link(
                                                array_merge(
                                                    $args, array(
                                                        'add_below' => 'comment_body',
                                                        'depth' => $depth,
                                                        'max_depth' => $args['max_depth'],
                                                    )
                                                )
                                            );
                                            ?>
                                        </span>
                                    <?php
                                }
                                ?>
                            </div>
                        </div>
                        <div class="comment_text_wrap">
                            <?php if ( 0 == $comment->comment_approved ) { ?>
                                <div class="comment_not_approved"><?php esc_html_e( 'Your comment is awaiting moderation.', 'equadio' ); ?></div>
                            <?php } ?>
                            <div class="comment_text"><?php comment_text(); ?></div>
                        </div>
                    </div>
                </div>
                <?php
                break;
        }
    }
}

// Add theme-specific controls
if ( ! function_exists( 'equadio_skin_elm_add_param_control_before' ) ) {
    add_action( 'elementor/element/before_section_start', 'equadio_skin_elm_add_param_control_before', 10, 3 );
    function equadio_skin_elm_add_param_control_before($element, $section_id, $args)  {
        if (is_object($element)) {
            $el_name = $element->get_name();

            if ( 'trx_sc_events' == $el_name && $section_id == 'section_slider_params' ) {

                $element->start_controls_section(
                    'section_sc_courses_details', array(
                        'label' => esc_html__('Details', 'equadio'),
                        'tab' => \Elementor\Controls_Manager::TAB_LAYOUT
                    )
                );

                $element->add_control(
                    'show_excerpt', array(
                        'type' => \Elementor\Controls_Manager::SWITCHER,
                        'label' => esc_html__('Show Excerpt', 'equadio'),
                        'label_off' => esc_html__('Hide', 'equadio'),
                        'label_on' => esc_html__('Show', 'equadio'),
                        'return_value' => '1',
                        'condition' => [
                            'type' => ['default']
                        ],
                    )
                );

                $element->end_controls_section();
            }

        }
    }
}

// Add theme-specific controls after section start
if ( ! function_exists( 'equadio_skin_elm_add_param_control_after' ) ) {
    add_action('elementor/element/after_section_start', 'equadio_skin_elm_add_param_control_after', 10, 3);
    function equadio_skin_elm_add_param_control_after($element, $section_id, $args)
    {
        if (is_object($element)) {
            $el_name = $element->get_name();

            if ('trx_sc_services' == $el_name && $section_id == 'section_sc_services_details') {
                $element->add_control(
                    'show_subtitle', array(
                        'type' => \Elementor\Controls_Manager::SWITCHER,
                        'label' => esc_html__('Subtitle', 'equadio'),
                        'label_off' => esc_html__('Hide', 'equadio'),
                        'label_on' => esc_html__('Show', 'equadio'),
                        'return_value' => '1',
                        'condition' => [
                            'type' => ['default', 'light']
                        ],
                    )
                );
            }
        }
    }
}

// Hide Params in Elementor
if ( !function_exists('equadio_skin_elm_hide_params_dependencies_in_section' ) ) {
    add_action('elementor/element/before_section_end', 'equadio_skin_elm_hide_params_dependencies_in_section', 10, 3);
    function equadio_skin_elm_hide_params_dependencies_in_section($element, $section_id, $args)    {

        //Services params dependencies
        if  ( 'trx_sc_services' == $element->get_name() &&  'section_sc_services_details' == $section_id ) {
            $element->update_control(
                'hide_bg_image',
                [
                    'condition' => [
                        'type' => ['hover'],
                    ],
                ]
            );
        }
        if  ( 'trx_sc_services' == $element->get_name() &&  'section_sc_services' == $section_id ) {
            $element->update_control(
                'featured_position',
                [
                    'description' => '',
                ]
            );
        }
        if ('trx_sc_services' == $element->get_name() && 'section_sc_services' ==  $section_id ) {
            $element->update_control(
                'featured', array(
                    'condition' => [
                        'type' => ['default', 'alter', 'callouts', 'hover', 'light', 'list', 'iconed', 'tabs', 'tabs_simple', 'timeline']
                    ],
                )
            );
        }
        if ('trx_sc_services' == $element->get_name() && 'section_sc_services' ==  $section_id ) {
            $element->update_control(
                'columns', array(
                    'condition' => [
                        'type' => ['default', 'alter', 'callouts','light','list','iconed','hover','chess','timeline']
                    ],
                )
            );
        }
        if ('trx_sc_services' == $element->get_name() && 'section_slider_params' == $section_id ) {
            $element->update_control(
                'slider', array(
                    'condition' => [
                        'type' => ['default', 'alter','callouts','light','list','iconed','hover','chess','timeline']
                    ],
                )
            );
        }
    }
}

// Remove bottom featured position (not use callouts or timeline service layouts)
if ( !function_exists( 'equadio_skin_filter_get_list_sc_services_featured_positions' ) ) {
    add_filter('trx_addons_filter_get_list_sc_services_featured_positions', 'equadio_skin_filter_get_list_sc_services_featured_positions');
    function equadio_skin_filter_get_list_sc_services_featured_positions($list)  {
        unset($list['bottom']);
        return $list;
    }
}

//Add class to Popup
if ( ! function_exists( 'equadio_skin_trx_popup_classes' ) ) {
    add_filter( 'trx_popup_filter_classes', 'equadio_skin_trx_popup_classes' );
    function equadio_skin_trx_popup_classes() {
        return 'scheme_dark';
    }
}

/* System info */
if ( ! function_exists( 'equadio_get_sys_info' ) ) {
    add_filter( 'trx_addons_filter_get_sys_info', 'equadio_get_sys_info', 9 );
    function equadio_get_sys_info( $options = array() ) {
		unset($options['php_max_input_vars']);
        return $options;
    }
}
