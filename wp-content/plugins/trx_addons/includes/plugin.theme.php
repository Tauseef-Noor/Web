<?php
/**
 * Get/Update theme options
 *
 * @package ThemeREX Addons
 * @since v2.28.0
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}

if ( ! function_exists( 'trx_addons_get_theme_data' ) ) {
	/**
	 * Return a theme data from the theme storage
	 * 
	 * @hooked trx_addons_filter_get_theme_data
	 * 
	 * @param string $name    Theme data name
	 * @param mixed  $default Default value if a data is not found
	 * 
	 * @return mixed          Theme data value
	 */
	function trx_addons_get_theme_data( $name, $default = '' ) {
		return apply_filters( 'trx_addons_filter_get_theme_data', $default, $name );
	}
}

if ( ! function_exists( 'trx_addons_get_theme_info' ) ) {
	/**
	 * Return theme info
	 *
	 * @param bool $cache If true (default) - get info from cache
	 * 
	 * @return array Theme info array. Keys: theme_slug, theme_name, theme_version, theme_activated, theme_categories, theme_plugins, etc.
	 */
	function trx_addons_get_theme_info( $cache = true ) {
		static $cached_info = false;
		if ( $cached_info !== false ) {
			$theme_info = $cached_info;
		} else {
			$theme_slug = get_template();
			$theme = wp_get_theme( $theme_slug );
			//Data below required for the 'Dashboard Widget' to display theme- and category-relevant news
			$theme_info = apply_filters('trx_addons_filter_get_theme_info', array(
				'theme_slug' => $theme_slug,
				'theme_name' => $theme->get( 'Name' ),
				'theme_version' => $theme->get( 'Version' ),
				'theme_activated' => '',
				'theme_pro_key' => '',
				'theme_page_url' => function_exists( 'menu_page_url' ) ? menu_page_url( 'trx_addons_theme_panel', false ) : '',
				'theme_categories' => '',
				'theme_plugins' => '',
				'theme_feed' => array(),
				'theme_actions' => array(),
				)
			);
			$theme_pro_key = get_option( sprintf( 'purchase_code_src_%s', $theme_slug ) );
			if ( $theme_pro_key ) {
				$theme_info['theme_pro_key'] = $theme_pro_key;
			}
			if ( $cache ) {
				$cached_info = $theme_info;
			}
		}
		return $theme_info;
	}
}

if ( ! function_exists( 'trx_addons_get_theme_options_name' ) ) {
	/**
	 * Return the option's name with the theme options
	 *
	 * @return string  Name of the option with the theme options
	 */
	function trx_addons_get_theme_options_name() {
		return sprintf( 'theme_mods_%s', get_template() );
	}
}

if ( ! function_exists( 'trx_addons_get_theme_options' ) ) {
	/**
	 * Return the theme options array
	 * 
	 * @return array  The theme options array
	 */
	function trx_addons_get_theme_options() {
		return apply_filters( 'trx_addons_get_theme_options', get_option( trx_addons_get_theme_options_name(), array() ) );
	}
}

if ( ! function_exists( 'trx_addons_update_theme_options' ) ) {
	/**
	 * Save/update the theme options
	 * 
	 * @param array $options  The theme options to save/update
	 * @param bool  $force_action  If true - force to regenerate styles and scripts on first run
	 *                             (via update option 'trx_addons_action' with value 'trx_addons_action_save_options')
	 */
	function trx_addons_update_theme_options( $options, $force_action = false ) {
		update_option( trx_addons_get_theme_options_name(), $options );
   		// Set this flag to regenerate styles and scripts on first run
		if ( $force_action ) {
			update_option('trx_addons_action', 'trx_addons_action_save_options');
		}
	   // Trigger the action to update the theme options
	   do_action( 'trx_addons_action_update_theme_options', $options );
	}
}

if ( ! function_exists( 'trx_addons_get_theme_option' ) ) {
	/**
	 * Return a theme option
	 * 
	 * @hooked trx_addons_filter_get_theme_option
	 * 
	 * @param string $name    Theme option name
	 * @param mixed  $default Default value if an option is not found
	 * 
	 * @return mixed          Theme option value
	 */
	function trx_addons_get_theme_option( $name, $default = '' ) {
		$not_exists_value = -9999999;
		$value = apply_filters( 'trx_addons_filter_get_theme_option', $not_exists_value, $name );
		if ( $value == $not_exists_value ) {
			$theme_slug = str_replace( '-', '_', get_template() );
			$func = "{$theme_slug}_get_theme_option";
			if ( function_exists( $func ) ) {
				$value = $func( $name, $not_exists_value );
				if ( $value == $not_exists_value ) {
					$value = $default;
				}
			} else {
				$value = $default;
			}
		}
		return $value;
	}
}

if ( ! function_exists( 'trx_addons_get_theme_color_schemes' ) ) {
	/**
	 * Return the list of the theme color schemes
	 * 
	 * @return array  The theme color schemes
	 */
	function trx_addons_get_theme_color_schemes() {
		$schemes = trx_addons_get_theme_data( 'schemes' );
		return apply_filters( 'trx_addons_filter_get_theme_color_schemes', $schemes );
	}
}
