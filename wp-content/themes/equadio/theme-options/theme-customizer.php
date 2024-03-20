<?php
/**
 * Theme customizer
 *
 * @package EQUADIO
 * @since EQUADIO 1.0
 */


//--------------------------------------------------------------
//-- First run actions after switch theme
//--------------------------------------------------------------
if ( ! function_exists( 'equadio_customizer_action_switch_theme' ) ) {
	add_action( 'after_switch_theme', 'equadio_customizer_action_switch_theme' );
	function equadio_customizer_action_switch_theme() {
		// Duplicate theme options between parent and child themes
		$duplicate = equadio_get_theme_setting( 'duplicate_options' );
		if ( in_array( $duplicate, array( 'child', 'both' ) ) ) {
			$theme_slug      = get_option( 'template' );
			$theme_time      = (int) get_option( "equadio_options_timestamp_{$theme_slug}" );
			$stylesheet_slug = get_option( 'stylesheet' );

			// If child-theme is activated - duplicate options from template to the child-theme
			if ( $theme_slug != $stylesheet_slug ) {
				$stylesheet_time = (int) get_option( "equadio_options_timestamp_{$stylesheet_slug}" );
				if ( $theme_time > $stylesheet_time ) {
					equadio_customizer_duplicate_theme_options( $theme_slug, $stylesheet_slug, $theme_time );
				}

				// If main theme (template) is activated and 'duplicate_options' == 'child'
				// (duplicate options only from template to the child-theme) - regenerate CSS  with custom colors and fonts
			} elseif ( 'child' == $duplicate && $theme_time > 0 ) {
				equadio_customizer_save_css();
			}
		}
	}
}


// Duplicate theme options between template and child-theme
if ( ! function_exists( 'equadio_customizer_duplicate_theme_options' ) ) {
	function equadio_customizer_duplicate_theme_options( $from, $to, $timestamp = 0 ) {
		if ( 0 == $timestamp ) {
			$timestamp = get_option( "equadio_options_timestamp_{$from}" );
		}
		$from         = "theme_mods_{$from}";
		$from_options = get_option( $from );
		$to           = "theme_mods_{$to}";
		$to_options   = get_option( $to );
		if ( is_array( $from_options ) ) {
			if ( ! is_array( $to_options ) ) {
				$to_options = array();
			}
			$theme_options = equadio_storage_get( 'options' );
			foreach ( $from_options as $k => $v ) {
				if ( isset( $theme_options[ $k ] ) ) {
					$to_options[ $k ] = $v;
				}
			}
			update_option( $to, $to_options );
			update_option( "equadio_options_timestamp_{$to}", $timestamp );
		}
	}
}


//--------------------------------------------------------------
//-- New panel in the Customizer Controls
//--------------------------------------------------------------

// Theme init priorities:
// 3 - add/remove Theme Options elements
if ( ! function_exists( 'equadio_customizer_setup3' ) ) {
	add_action( 'after_setup_theme', 'equadio_customizer_setup3', 3 );
	function equadio_customizer_setup3() {
		equadio_storage_merge_array(
			'options', '', array(
				'cpt' => array(
					'title'    => esc_html__( 'Plugins settings', 'equadio' ),
					'desc'     => '',
					'priority' => 400,
					'icon'     => 'icon-plugins',
					'type'     => 'panel',
				),
			)
		);
	}
}
// 3 - add/remove Theme Options elements
if ( ! function_exists( 'equadio_customizer_setup4' ) ) {
	add_action( 'after_setup_theme', 'equadio_customizer_setup4', 4 );
	function equadio_customizer_setup4() {
		equadio_storage_merge_array(
			'options', '', array(
				'cpt_end' => array(
					'type' => 'panel_end',
				),
			)
		);
	}
}


//--------------------------------------------------------------
//-- Register Customizer Controls
//--------------------------------------------------------------

define( 'EQUADIO_CUSTOMIZE_PRIORITY', 200 );      // Start priority for the new controls

// Register custom controls for the customizer
if ( ! function_exists( 'equadio_customizer_custom_controls' ) ) {
	add_action( 'customize_register', 'equadio_customizer_custom_controls' );
	function equadio_customizer_custom_controls( $wp_customize ) {
		require_once EQUADIO_THEME_DIR . 'theme-options/theme-customizer-controls.php';
	}
}

// Parse Theme Options and add controls to the customizer
if ( ! function_exists( 'equadio_customizer_register_controls' ) ) {
	add_action( 'customize_register', 'equadio_customizer_register_controls', 20 );
	function equadio_customizer_register_controls( $wp_customize ) {

		$refresh_auto = equadio_get_theme_setting( 'customize_refresh' ) != 'manual';

		$panels   = array( '' );
		$p        = 0;
		$sections = array( '' );
		$s        = 0;

		$expand = array();

		$i = EQUADIO_CUSTOMIZE_PRIORITY;

		// Reload Theme Options before create controls
		if ( is_admin() ) {
			equadio_storage_set( 'options_reloaded', true );
			equadio_load_theme_options();
		}
		$options = equadio_storage_get( 'options' );

		foreach ( $options as $id => $opt ) {
			$i = ! empty( $opt['priority'] )
					? $opt['priority']
					: ( in_array( $opt['type'], array( 'panel', 'section' ) )
							? EQUADIO_CUSTOMIZE_PRIORITY
							: $i++
						);

			if ( ! empty( $opt['hidden'] ) ) {
				continue;
			}

			if ( ! isset( $opt['title'] ) ) {
				$opt['title'] = '';
			}
			if ( ! isset( $opt['desc'] ) ) {
				$opt['desc'] = '';
			}

			$transport = $refresh_auto && ( ! isset( $opt['refresh'] ) || true === $opt['refresh'] ) ? 'refresh' : 'postMessage';

			if ( ! empty( $opt['override'] ) ) {
				$opt['title'] .= ' *';
			}

			// URL to redirect preview area and/or JS callback on expand panel
			if ( in_array( $opt['type'], array( 'panel', 'section' ) ) && ! empty( $opt['expand_url'] ) || ! empty( $opt['expand_callback'] ) ) {
				$expand[ $id ] = array( 'type' => $opt['type'] );
				if ( ! empty( $opt['expand_url'] ) ) {
					$expand[ $id ]['url'] = $opt['expand_url'];
				}
				if ( ! empty( $opt['expand_callback'] ) ) {
					$expand[ $id ]['callback'] = $opt['expand_callback'];
				}
			}

			if ( 'panel' == $opt['type'] ) {

				if ( $p > 0 ) {
					array_pop( $panels );
					$p--;
				}
				if ( $s > 0 ) {
					array_pop( $sections );
					$s--;
				}

				$sec = $wp_customize->get_panel( $id );
				if ( is_object( $sec ) && ! empty( $sec->title ) ) {
					$sec->title       = $opt['title'];
					$sec->description = $opt['desc'];
					if ( ! empty( $opt['priority'] ) ) {
						$sec->priority = $opt['priority'];
					}
					if ( ! empty( $opt['active_callback'] ) ) {
						$sec->active_callback = $opt['active_callback'];
					}
				} else {
					$wp_customize->add_panel(
						esc_attr( $id ), array(
							'title'           => $opt['title'],
							'description'     => $opt['desc'],
							'priority'        => $i,
							'active_callback' => ! empty( $opt['active_callback'] ) ? $opt['active_callback'] : '',
						)
					);
				}

				array_push( $panels, $id );
				$p++;

			} elseif ( 'panel_end' == $opt['type'] ) {

				array_pop( $panels );
				$p--;

			} elseif ( 'section' == $opt['type'] ) {

				if ( $s > 0 ) {
					array_pop( $sections );
					$s--;
				}

				$sec = $wp_customize->get_section( $id );
				if ( is_object( $sec ) && ! empty( $sec->title ) ) {
					$sec->title       = $opt['title'];
					$sec->description = $opt['desc'];
					$sec->panel       = esc_attr( $panels[ $p ] );
					if ( ! empty( $opt['priority'] ) ) {
						$sec->priority = $opt['priority'];
					}
					if ( ! empty( $opt['active_callback'] ) ) {
						$sec->active_callback = $opt['active_callback'];
					}
				} else {
					$wp_customize->add_section(
						esc_attr( $id ), array(
							'title'           => $opt['title'],
							'description'     => $opt['desc'],
							'panel'           => esc_attr( $panels[ $p ] ),
							'priority'        => $i,
							'active_callback' => ! empty( $opt['active_callback'] ) ? $opt['active_callback'] : '',
						)
					);
				}

				array_push( $sections, $id );
				$s++;

			} elseif ( 'section_end' == $opt['type'] ) {

				array_pop( $sections );
				$s--;

			} elseif ( 'select' == $opt['type'] ) {

				$wp_customize->add_setting(
					$id, array(
						'default'           => equadio_get_theme_option_std( $id, $opt['std'] ),	// From 1.0.59 used instead equadio_get_theme_option( $id )
						'sanitize_callback' => 'sanitize_text_field',
						'transport'         => $transport,
					)
				);

				$wp_customize->add_control(
					$id, array(
						'label'           => $opt['title'],
						'description'     => $opt['desc'],
						'section'         => esc_attr( $sections[ $s ] ),
						'priority'        => $i,
						'active_callback' => ! empty( $opt['active_callback'] ) ? $opt['active_callback'] : '',
						'type'            => 'select',
						'choices'         => apply_filters( 'equadio_filter_options_get_list_choises', $opt['options'], $id ),
						'input_attrs'     => array(
							'var_name' => ! empty( $opt['customizer'] ) ? $opt['customizer'] : '',
						),
					)
				);

			} elseif ( 'radio' == $opt['type'] ) {

				$wp_customize->add_setting(
					$id, array(
						'default'           => equadio_get_theme_option_std( $id, $opt['std'] ),	// From 1.0.59 used instead equadio_get_theme_option( $id ),
						'sanitize_callback' => 'sanitize_text_field',
						'transport'         => $transport,
					)
				);

				$wp_customize->add_control(
					$id, array(
						'label'           => $opt['title'],
						'description'     => $opt['desc'],
						'section'         => esc_attr( $sections[ $s ] ),
						'priority'        => $i,
						'active_callback' => ! empty( $opt['active_callback'] ) ? $opt['active_callback'] : '',
						'type'            => 'radio',
						'choices'         => apply_filters( 'equadio_filter_options_get_list_choises', $opt['options'], $id ),
						'input_attrs'     => array(
							'var_name' => ! empty( $opt['customizer'] ) ? $opt['customizer'] : '',
						),
					)
				);

			} elseif ( 'checkbox' == $opt['type'] || 'switch' == $opt['type'] ) {
				$wp_customize->add_setting(
					$id, array(
						'default'           => equadio_get_theme_option_std( $id, $opt['std'] ),	// From 1.0.59 used instead equadio_get_theme_option( $id ),
						'sanitize_callback' => 'sanitize_text_field',
						'transport'         => $transport,
					)
				);

				$wp_customize->add_control(
					$id, array(
						'label'           => $opt['title'],
						'description'     => $opt['desc'],
						'section'         => esc_attr( $sections[ $s ] ),
						'active_callback' => ! empty( $opt['active_callback'] ) ? $opt['active_callback'] : '',
						'priority'        => $i,
						'type'            => 'checkbox',
						'input_attrs'     => array(
							'var_name' => ! empty( $opt['customizer'] ) ? $opt['customizer'] : '',
						),
					)
				);

			} elseif ( 'switch' == $opt['type'] ) {

				$wp_customize->add_setting(
					$id, array(
						'default'           => equadio_get_theme_option_std( $id, $opt['std'] ),	// From 1.0.59 used instead equadio_get_theme_option( $id ),
						'sanitize_callback' => 'sanitize_text_field',
						'transport'         => $transport,
					)
				);

				$wp_customize->add_control(
					new Equadio_Customize_Switch_Control(
						$wp_customize, $id, array(
							'label'           => $opt['title'],
							'description'     => $opt['desc'],
							'section'         => esc_attr( $sections[ $s ] ),
							'priority'        => $i,
							'active_callback' => ! empty( $opt['active_callback'] ) ? $opt['active_callback'] : '',
							'input_attrs'     => array(
								'value'    => equadio_get_theme_option( $id ),
								'var_name' => ! empty( $opt['customizer'] ) ? $opt['customizer'] : '',
							),
						)
					)
				);

			} elseif ( 'color' == $opt['type'] ) {

				$wp_customize->add_setting(
					$id, array(
						'default'           => equadio_get_theme_option_std( $id, $opt['std'] ),	// From 1.0.59 used instead equadio_get_theme_option( $id ),
						'sanitize_callback' => 'sanitize_hex_color',
						'transport'         => $transport,
					)
				);

				$wp_customize->add_control(
					new WP_Customize_Color_Control(
						$wp_customize, $id, array(
							'label'           => $opt['title'],
							'description'     => $opt['desc'],
							'section'         => esc_attr( $sections[ $s ] ),
							'active_callback' => ! empty( $opt['active_callback'] ) ? $opt['active_callback'] : '',
							'priority'        => $i,
							'input_attrs'     => array(
								'var_name' => ! empty( $opt['customizer'] ) ? $opt['customizer'] : '',
							),
						)
					)
				);

			} elseif ( 'image' == $opt['type'] ) {
				$wp_customize->add_setting(
					$id, array(
						'default'           => equadio_remove_protocol_from_url( equadio_get_theme_option_std( $id, $opt['std'] ), false ),	// From 1.0.59 used instead equadio_remove_protocol_from_url( equadio_get_theme_option( $id ), false ),
						'sanitize_callback' => 'sanitize_text_field',
						'transport'         => $transport,
					)
				);

				$wp_customize->add_control(
					new WP_Customize_Image_Control(
						$wp_customize, $id, array(
							'label'           => $opt['title'],
							'description'     => $opt['desc'],
							'section'         => esc_attr( $sections[ $s ] ),
							'active_callback' => ! empty( $opt['active_callback'] ) ? $opt['active_callback'] : '',
							'priority'        => $i,
							'input_attrs'     => array(
								'var_name' => ! empty( $opt['customizer'] ) ? $opt['customizer'] : '',
							),
						)
					)
				);

			} elseif ( in_array( $opt['type'], array( 'media', 'audio', 'video' ) ) ) {
				$wp_customize->add_setting(
					$id, array(
						'default'           => equadio_remove_protocol_from_url( equadio_get_theme_option_std( $id, $opt['std'] ), false ),	// From 1.0.59 used instead equadio_remove_protocol_from_url( equadio_get_theme_option( $id ), false ),
						'sanitize_callback' => 'sanitize_text_field',
						'transport'         => $transport,
					)
				);

				$wp_customize->add_control(
					new WP_Customize_Media_Control(
						$wp_customize, $id, array(
							'label'           => $opt['title'],
							'description'     => $opt['desc'],
							'section'         => esc_attr( $sections[ $s ] ),
							'active_callback' => ! empty( $opt['active_callback'] ) ? $opt['active_callback'] : '',
							'priority'        => $i,
							'input_attrs'     => array(
								'var_name' => ! empty( $opt['customizer'] ) ? $opt['customizer'] : '',
							),
						)
					)
				);

			} elseif ( 'icon' == $opt['type'] ) {

				$wp_customize->add_setting(
					$id, array(
						'default'           => equadio_remove_protocol_from_url( equadio_get_theme_option_std( $id, $opt['std'] ), false ),	// From 1.0.59 used instead equadio_remove_protocol_from_url( equadio_get_theme_option( $id ), false ),
						'sanitize_callback' => 'sanitize_text_field',
						'transport'         => $transport,
					)
				);

				$wp_customize->add_control(
					new Equadio_Customize_Icon_Control(
						$wp_customize, $id, array(
							'label'           => $opt['title'],
							'description'     => $opt['desc'],
							'section'         => esc_attr( $sections[ $s ] ),
							'priority'        => $i,
							'active_callback' => ! empty( $opt['active_callback'] ) ? $opt['active_callback'] : '',
							'input_attrs'     => array(
								'value'    => equadio_get_theme_option( $id ),
								'var_name' => ! empty( $opt['customizer'] ) ? $opt['customizer'] : '',
							),
						)
					)
				);

			} elseif ( 'checklist' == $opt['type'] ) {

				$wp_customize->add_setting(
					$id, array(
						'default'           => equadio_get_theme_option_std( $id, $opt['std'] ),	// From 1.0.59 used instead equadio_get_theme_option( $id ),
						'sanitize_callback' => 'sanitize_text_field',
						'transport'         => $transport,
					)
				);

				$wp_customize->add_control(
					new Equadio_Customize_Checklist_Control(
						$wp_customize, $id, array(
							'label'           => $opt['title'],
							'description'     => $opt['desc'],
							'section'         => esc_attr( $sections[ $s ] ),
							'priority'        => $i,
							'active_callback' => ! empty( $opt['active_callback'] ) ? $opt['active_callback'] : '',
							'choices'         => apply_filters( 'equadio_filter_options_get_list_choises', $opt['options'], $id ),
							'input_attrs'     => array_merge(
								$opt, array(
									'value'    => equadio_get_theme_option( $id ),
									'var_name' => ! empty( $opt['customizer'] ) ? $opt['customizer'] : '',
								)
							),
						)
					)
				);

			} elseif ( 'choice' == $opt['type'] ) {

				$wp_customize->add_setting(
					$id, array(
						'default'           => equadio_get_theme_option_std( $id, $opt['std'] ),	// From 1.0.59 used instead equadio_get_theme_option( $id ),
						'sanitize_callback' => 'sanitize_text_field',
						'transport'         => $transport,
					)
				);

				$wp_customize->add_control(
					new Equadio_Customize_Choice_Control(
						$wp_customize, $id, array(
							'label'           => $opt['title'],
							'description'     => $opt['desc'],
							'section'         => esc_attr( $sections[ $s ] ),
							'priority'        => $i,
							'active_callback' => ! empty( $opt['active_callback'] ) ? $opt['active_callback'] : '',
							'choices'         => apply_filters( 'equadio_filter_options_get_list_choises', $opt['options'], $id ),
							'input_attrs'     => array_merge(
								$opt, array(
									'value'    => equadio_get_theme_option( $id ),
									'var_name' => ! empty( $opt['customizer'] ) ? $opt['customizer'] : '',
								)
							),
						)
					)
				);

			} elseif ( in_array( $opt['type'], array( 'slider', 'range' ) ) ) {

				$wp_customize->add_setting(
					$id, array(
						'default'           => equadio_get_theme_option_std( $id, $opt['std'] ),	// From 1.0.59 used instead equadio_get_theme_option( $id ),
						'sanitize_callback' => 'sanitize_text_field',
						'transport'         => $transport,
					)
				);

				$wp_customize->add_control(
					new Equadio_Customize_Range_Control(
						$wp_customize, $id, array(
							'label'           => $opt['title'],
							'description'     => $opt['desc'],
							'section'         => esc_attr( $sections[ $s ] ),
							'priority'        => $i,
							'active_callback' => ! empty( $opt['active_callback'] ) ? $opt['active_callback'] : '',
							'input_attrs'     => array_merge(
								$opt, array(
									'show_value' => ! isset( $opt['show_value'] ) || $opt['show_value'],
									'value'      => equadio_get_theme_option( $id ),
									'var_name'   => ! empty( $opt['customizer'] ) ? $opt['customizer'] : '',
								)
							),
						)
					)
				);

			} elseif ( 'scheme_editor' == $opt['type'] ) {

				$wp_customize->add_setting(
					$id, array(
						'default'           => equadio_get_theme_option_std( $id, $opt['std'] ),	// From 1.0.59 used instead equadio_get_theme_option( $id ),
						'sanitize_callback' => 'sanitize_text_field',
						'transport'         => $transport,
					)
				);

				$wp_customize->add_control(
					new Equadio_Customize_Scheme_Editor_Control(
						$wp_customize, $id, array(
							'label'           => $opt['title'],
							'description'     => $opt['desc'],
							'section'         => esc_attr( $sections[ $s ] ),
							'priority'        => $i,
							'active_callback' => ! empty( $opt['active_callback'] ) ? $opt['active_callback'] : '',
							'input_attrs'     => array_merge(
								$opt, array(
									'value'    => equadio_get_theme_option( $id ),
									'var_name' => ! empty( $opt['customizer'] ) ? $opt['customizer'] : '',
								)
							),
						)
					)
				);

			} elseif ( 'text_editor' == $opt['type'] ) {

				$wp_customize->add_setting(
					$id, array(
						'default'           => equadio_get_theme_option_std( $id, $opt['std'] ),	// From 1.0.59 used instead equadio_get_theme_option( $id ),
						'sanitize_callback' => 'wp_kses_post',
						'transport'         => $transport,
					)
				);

				$wp_customize->add_control(
					new Equadio_Customize_Text_Editor_Control(
						$wp_customize, $id, array(
							'label'           => $opt['title'],
							'description'     => $opt['desc'],
							'section'         => esc_attr( $sections[ $s ] ),
							'priority'        => $i,
							'active_callback' => ! empty( $opt['active_callback'] ) ? $opt['active_callback'] : '',
							'input_attrs'     => array_merge(
								$opt, array(
									'value'    => equadio_get_theme_option( $id ),
									'var_name' => ! empty( $opt['customizer'] ) ? $opt['customizer'] : '',
								)
							),
						)
					)
				);

			} elseif ( 'button' == $opt['type'] ) {

				$wp_customize->add_setting(
					$id, array(
						'default'           => equadio_get_theme_option_std( $id, $opt['std'] ),	// From 1.0.59 used instead equadio_get_theme_option( $id ),
						'sanitize_callback' => 'sanitize_text_field',
						'transport'         => $transport,
					)
				);

				$wp_customize->add_control(
					new Equadio_Customize_Button_Control(
						$wp_customize, $id, array(
							'label'           => $opt['title'],
							'description'     => $opt['desc'],
							'section'         => esc_attr( $sections[ $s ] ),
							'priority'        => $i,
							'active_callback' => ! empty( $opt['active_callback'] ) ? $opt['active_callback'] : '',
							'input_attrs'     => $opt,
						)
					)
				);

			} elseif ( 'info' == $opt['type'] ) {

				$wp_customize->add_setting(
					$id, array(
						'default'           => '',
						'sanitize_callback' => 'sanitize_text_field',
						'transport'         => 'postMessage',
					)
				);

				$wp_customize->add_control(
					new Equadio_Customize_Info_Control(
						$wp_customize, $id, array(
							'label'           => $opt['title'],
							'description'     => $opt['desc'],
							'section'         => esc_attr( $sections[ $s ] ),
							'priority'        => $i,
							'active_callback' => ! empty( $opt['active_callback'] ) ? $opt['active_callback'] : '',
						)
					)
				);

			} elseif ( 'hidden' == $opt['type'] ) {

				if ( isset( $opt['std']) ) {
					$wp_customize->add_setting(
						$id, array(
							'default'           => equadio_get_theme_option_std( $id, $opt['std'] ),	// From 1.0.59 used instead equadio_get_theme_option( $id ),
							'sanitize_callback' => 'equadio_sanitize_html',
							'transport'         => 'postMessage',
						)
					);

					$wp_customize->add_control(
						new Equadio_Customize_Hidden_Control(
							$wp_customize, $id, array(
								'label'           => $opt['title'],
								'description'     => $opt['desc'],
								'section'         => esc_attr( $sections[ $s ] ),
								'priority'        => $i,
								'active_callback' => ! empty( $opt['active_callback'] ) ? $opt['active_callback'] : '',
								'input_attrs'     => array(
									'var_name' => ! empty( $opt['customizer'] ) ? $opt['customizer'] : '',
								),
							)
						)
					);
				}

			} else {    // if in_array($opt['type'], array('text', 'textarea'))

				if ( ! apply_filters( 'equadio_filter_register_customizer_control', false, $wp_customize, $id, $sections[ $s ], $i, $transport, $opt ) ) {
					
					if ( 'text_editor' == $opt['type'] ) {
						$opt['type'] = 'textarea';
					}

					$wp_customize->add_setting(
						$id, array(
							'default'           => equadio_get_theme_option_std( $id, $opt['std'] ),	// From 1.0.59 used instead equadio_get_theme_option( $id ),
							'sanitize_callback' => ! empty( $opt['sanitize'] )
														? $opt['sanitize']
														: ( 'text' == $opt['type']
																? 'sanitize_text_field'
																: 'wp_kses_post'
															),
							'transport'         => $transport,
						)
					);

					$wp_customize->add_control(
						$id, array(
							'label'           => $opt['title'],
							'description'     => $opt['desc'],
							'section'         => esc_attr( $sections[ $s ] ),
							'priority'        => $i,
							'active_callback' => ! empty( $opt['active_callback'] ) ? $opt['active_callback'] : '',
							'type'            => $opt['type'],
							'input_attrs'     => array(
								'var_name' => ! empty( $opt['customizer'] ) ? $opt['customizer'] : '',
							),
						)
					);
				}
			}

			// Register Partial Refresh (if supported)
			if ( $refresh_auto && isset( $opt['refresh'] ) && is_string( $opt['refresh'] )
				&& function_exists( "equadio_customizer_partial_refresh_{$id}" )
				&& isset( $wp_customize->selective_refresh ) ) {
				$wp_customize->selective_refresh->add_partial(
					$id, array(
						'selector'            => $opt['refresh'],
						'settings'            => $id,
						'render_callback'     => "equadio_customizer_partial_refresh_{$id}",
						'container_inclusive' => ! empty( $opt['refresh_wrapper'] ),
					)
				);
			}
		}

		// Save expand callbacks to use it in the localize scripts
		equadio_storage_set( 'customizer_expand', $expand );

		// Setup standard WP Controls
		// ---------------------------------

		// Reorder standard WP sections
		$sec = $wp_customize->get_panel( 'nav_menus' );
		if ( is_object( $sec ) ) {
			$sec->priority = 60;
		}
		$sec = $wp_customize->get_panel( 'widgets' );
		if ( is_object( $sec ) ) {
			$sec->priority = 61;
		}
		$sec = $wp_customize->get_section( 'static_front_page' );
		if ( is_object( $sec ) ) {
			$sec->priority = 62;
		}
		$sec = $wp_customize->get_section( 'custom_css' );
		if ( is_object( $sec ) ) {
			$sec->priority = 2000;
		}

		// Modify standard WP controls
		$sec = $wp_customize->get_control( 'blogname' );
		if ( is_object( $sec ) ) {
			$sec->description = esc_html__( 'Use "((" and "))", "{{" and "}}" to modify style and color of parts of the text, "||" to break current line', 'equadio' );
		}
		$sec = $wp_customize->get_setting( 'blogname' );
		if ( is_object( $sec ) ) {
			$sec->transport = 'postMessage';
		}

		$sec = $wp_customize->get_setting( 'blogdescription' );
		if ( is_object( $sec ) ) {
			$sec->transport = 'postMessage';
		}

		$sec = $wp_customize->get_control( 'site_icon' );
		if ( is_object( $sec ) ) {
			$sec->priority = 15;
		}
		$sec = $wp_customize->get_control( 'custom_logo' );
		if ( is_object( $sec ) ) {
			$sec->priority    = 50;
			$sec->description = wp_kses_data( __( 'Select or upload the site logo', 'equadio' ) );
		}

		$sec  = $wp_customize->get_section( 'header_image' );
		$sec2 = $wp_customize->get_control( 'header_image_info' );
		if ( is_object( $sec2 ) ) {
			$sec2->description = ( ! empty( $sec2->description ) ? $sec2->description . '<br>' : '' ) . $sec->description;
		}

		$sec = $wp_customize->get_control( 'header_image' );
		if ( is_object( $sec ) ) {
			$sec->priority = 300;
			$sec->section  = 'header';
		}
		$sec = $wp_customize->get_control( 'header_video' );
		if ( is_object( $sec ) ) {
			$sec->priority = 310;
			$sec->section  = 'header';
		}
		$sec = $wp_customize->get_control( 'external_header_video' );
		if ( is_object( $sec ) ) {
			$sec->priority = 320;
			$sec->section  = 'header';
		}

		$sec = $wp_customize->get_section( 'background_image' );
		if ( is_object( $sec ) ) {
			$sec->title       = esc_html__( 'Background', 'equadio' );
			$sec->priority    = 310;
			$sec->description = esc_html__( 'Used only if "General settings - Body style" equal to "boxed"', 'equadio' );
		}

		$sec = $wp_customize->get_control( 'background_color' );
		if ( is_object( $sec ) ) {
			$sec->priority = 10;
			$sec->section  = 'background_image';
		}

		// Remove unused sections
		$wp_customize->remove_section( 'colors' );
		$wp_customize->remove_section( 'header_image' );
	}
}


// Sanitize simple value - remove all tags and spaces
if ( ! function_exists( 'equadio_sanitize_value' ) ) {
	function equadio_sanitize_value( $value ) {
		return empty( $value ) ? $value : trim( strip_tags( $value ) );
	}
}


// Sanitize html value - keep only allowed tags
if ( ! function_exists( 'equadio_sanitize_html' ) ) {
	function equadio_sanitize_html( $value ) {
		return empty( $value ) ? $value : wp_kses_post( $value );
	}
}


// Return url to autofocus related field
if ( ! function_exists( 'equadio_customizer_get_focus_url' ) ) {
	function equadio_customizer_get_focus_url( $field ) {
		return admin_url( "customize.php?autofocus&#91;control&#93;={$field}" );
	}
}

// Return link to autofocus related field
if ( ! function_exists( 'equadio_customizer_get_focus_link' ) ) {
	function equadio_customizer_get_focus_link( $field, $text ) {
		return sprintf(
			'<a href="%1$s" class="equadio_customizer_link">%2$s</a>',
			esc_url( equadio_customizer_get_focus_url( $field ) ),
			$text
		);
	}
}

// Display message "Need to select widgets"
if ( ! function_exists( 'equadio_customizer_need_widgets_message' ) ) {
	function equadio_customizer_need_widgets_message( $field, $text ) {
		?><div class="equadio_customizer_message">
		<?php
			echo wp_kses_data(
				sprintf(
					// Translators: Add widget's name or link to focus specified section
					__( 'You have to choose widget "<b>%s</b>" in this section. You can also select any other widget, and change the purpose of this section', 'equadio' ),
					is_customize_preview()
						? $text
						: equadio_customizer_get_focus_link( $field, $text )
				)
			);
		?>
		</div>
		<?php
	}
}

// Display message "Need to install plugin ThemeREX Addons"
if ( ! function_exists( 'equadio_customizer_need_trx_addons_message' ) ) {
	function equadio_customizer_need_trx_addons_message() {
		?>
		<div class="equadio_customizer_message">
			<?php
			echo wp_kses_data(
				sprintf(
					// Translators: Add the link to install plugin and its name
					__( 'You need to install the <b>%s</b> plugin to be able to add Team members, Testimonials, Services and many other widgets', 'equadio' ),
					is_customize_preview()
						? __( 'ThemeREX Addons', 'equadio' )
						: sprintf(
							// Translators: Make the tag with link to install plugin
							'<a href="%1$s" class="equadio_customizer_link">%2$s</a>',
							esc_url(
								wp_nonce_url(
									self_admin_url( 'update.php?action=install-plugin&plugin=trx_addons' ),
									'install-plugin_trx_addons'
								)
							),
							__( 'ThemeREX Addons', 'equadio' )
						)
				)
			);
			echo '<br>' . wp_kses_data( __( 'Also you can insert in this section any other widgets and to modify its purpose', 'equadio' ) );
			?>
		</div>
		<?php
	}
}


//--------------------------------------------------------------
// Save custom settings in CSS file
//--------------------------------------------------------------

// Save CSS with custom colors and fonts after save custom options
if ( ! function_exists( 'equadio_customizer_action_save_after' ) ) {
	add_action( 'customize_save_after', 'equadio_customizer_action_save_after' );
	function equadio_customizer_action_save_after( $api = false ) {

		// Get saved settings
		$settings = $api->settings();

		// Store new schemes colors
		$scheme_storage = $settings['scheme_storage']->value();
		if ( $scheme_storage == serialize( equadio_storage_get( 'schemes_original' ) ) ) {
			remove_theme_mod( 'scheme_storage' );
		} else {
			$schemes = equadio_unserialize( $scheme_storage );
			if ( is_array( $schemes ) && count( $schemes ) > 0 ) {
				equadio_storage_set( 'schemes', $schemes );
			}
		}

		// Store new fonts parameters
		$fonts = equadio_get_theme_fonts();
		foreach ( $fonts as $tag => $v ) {
			foreach ( $v as $css_prop => $css_value ) {
				if ( in_array( $css_prop, array( 'title', 'description' ) ) ) {
					continue;
				}
				if ( isset( $settings[ "{$tag}_{$css_prop}" ] ) ) {
					$fonts[ $tag ][ $css_prop ] = $settings[ "{$tag}_{$css_prop}" ]->value();
				}
			}
		}
		equadio_storage_set( 'theme_fonts', $fonts );

		// Collect options from the external storages
		$theme_mods        = array();
		$options           = equadio_storage_get( 'options' );
		$external_storages = array();
		foreach ( $options as $k => $v ) {
			// Skip non-data options - sections, info, etc.
			if ( ! isset( $v['std'] ) ) {
				continue;
			}
			// Get option value from Customizer
			$value            = isset( $settings[ $k ] )
							? $settings[ $k ]->value()
							: ( in_array( $v['type'], array( 'checkbox', 'switch' ) )  ? 0 : '' );
			$theme_mods[ $k ] = $value;
			// Skip internal options
			if ( empty( $v['options_storage'] ) ) {
				continue;
			}
			// Save option to the external storage
			if ( ! isset( $external_storages[ $v['options_storage'] ] ) ) {
				$external_storages[ $v['options_storage'] ] = array();
			}
			$external_storages[ $v['options_storage'] ][ $k ] = $value;
		}

		// Update options in the external storages
		foreach ( $external_storages as $storage_name => $storage_values ) {
			$storage = get_option( $storage_name, false );
			if ( is_array( $storage ) ) {
				foreach ( $storage_values as $k => $v ) {
					$storage[ $k ] = $v;
				}
				update_option( $storage_name, apply_filters( 'equadio_filter_options_save', $storage, $storage_name ) );
			}
		}

		do_action( 'equadio_action_just_save_options', $theme_mods );

		// Update ThemeOptions save timestamp
		$stylesheet_slug = get_option( 'stylesheet' );
		$stylesheet_time = time();
		update_option( "equadio_options_timestamp_{$stylesheet_slug}", $stylesheet_time );

		// Synchronize theme options between child and parent themes
		if ( equadio_get_theme_setting( 'duplicate_options' ) == 'both' ) {
			$theme_slug = get_option( 'template' );
			if ( $theme_slug != $stylesheet_slug ) {
				equadio_customizer_duplicate_theme_options( $stylesheet_slug, $theme_slug, $stylesheet_time );
			}
		}

		// Apply action - moved to the delayed state (see below) to load all enabled modules and apply changes after
		// Attention! Don't remove comment the line below!
		// Not need here: do_action('equadio_action_save_options');
		update_option( 'equadio_action', 'equadio_action_save_options' );
	}
}

// Save CSS with custom colors and fonts to the custom.css
if ( ! function_exists( 'equadio_customizer_save_css' ) ) {
	add_action( 'equadio_action_save_options', 'equadio_customizer_save_css', 20 );
	add_action( 'trx_addons_action_save_options', 'equadio_customizer_save_css', 20 );
	function equadio_customizer_save_css() {

		$msg = '/* ' . esc_html__( "ATTENTION! This file was generated automatically! Don't change it!!!", 'equadio' )
				. "\n----------------------------------------------------------------------- */\n";

		// Save CSS with custom fonts and vars to the __custom.css
		$css = equadio_customizer_get_css();
		equadio_fpc( equadio_get_file_dir( 'css/__custom.css' ), $msg . $css );

		// Merge styles
		equadio_merge_css(
			'css/__plugins.css', apply_filters(
				'equadio_filter_merge_styles', array(
				)
			)
		);

		// Merge responsive styles
		equadio_merge_css(
			'css/__responsive.css', apply_filters(
				'equadio_filter_merge_styles_responsive', array(
					'css/responsive.css',
				)
			), true
		);

		// Merge scripts
		equadio_merge_js(
			'js/__scripts.js', apply_filters(
				'equadio_filter_merge_scripts', array(
					'js/skip-link-focus-fix/skip-link-focus-fix.js',
					'js/bideo/bideo.js',
					'js/tubular/jquery.tubular.js',
					'js/utils.js',
					'js/init.js',
				)
			)
		);
	}
}

// Add theme-specific blog styles and scripts to the list
//-------------------------------------------------------------------------------
if ( ! function_exists( 'equadio_customizer_add_blog_styles_and_scripts' ) ) {
	function equadio_customizer_add_blog_styles_and_scripts( $list = false, $type = 'styles', $responsive = false ) {
		$styles = equadio_storage_get( 'blog_styles' );
		if ( is_array( $styles ) ) {
			if ( equadio_exists_trx_addons() ) {
				$styles = array_merge(
					$styles,
					array(
						'custom' => array( 'styles' => 'custom' )
					)
				);
			}
			foreach ( $styles as $k => $v ) {
				if ( ! empty( $v[ $type ] ) ) {
					foreach ( (array) $v[ $type ] as $s ) {
						if ( apply_filters( "equadio_filter_enqueue_blog_{$type}", true, $k, $s, $list, $responsive ) ) {
							$path = sprintf(
								'templates/blog-styles/%1$s%2$s.%3$s',
								$s,
								$responsive ? '-responsive' : '',
								'styles' == $type ? 'css' : 'js'
							);
							if ( is_array( $list ) ) {
								if ( array_search( $path, $list ) === false ) {
									$list[] = $path;
								}
							} else {
								$path = equadio_get_file_url( $path );
								if ( '' != $path ) {
									if ( 'scripts' == $type ) {
										wp_enqueue_script( 'equadio-blog-script-' . esc_attr( $s ), $path, array( 'jquery' ), null, true );
									} else {
										wp_enqueue_style( 'equadio-blog-style-' . esc_attr( $s . ( $responsive ? '-responsive' : '' ) ),  $path, array(), null );
									}
								}
							}
						}
					}
				}
			}
		}
		return $list;
	}
}

// Merge theme-specific blog styles
if ( ! function_exists( 'equadio_customizer_merge_blog_styles' ) ) {
	add_filter( 'equadio_filter_merge_styles', 'equadio_customizer_merge_blog_styles', 8, 1 );
	function equadio_customizer_merge_blog_styles( $list ) {
		return equadio_customizer_add_blog_styles_and_scripts( $list, 'styles' );
	}
}

// Merge theme-specific blog styles
if ( ! function_exists( 'equadio_customizer_merge_blog_styles_responsive' ) ) {
	add_filter( 'equadio_filter_merge_styles_responsive', 'equadio_customizer_merge_blog_styles_responsive', 8, 1 );
	function equadio_customizer_merge_blog_styles_responsive( $list ) {
		return equadio_customizer_add_blog_styles_and_scripts( $list, 'styles', true );
	}
}

// Merge theme-specific blog scripts
if ( ! function_exists( 'equadio_customizer_merge_blog_scripts' ) ) {
	add_filter( 'equadio_filter_merge_scripts', 'equadio_customizer_merge_blog_scripts' );
	function equadio_customizer_merge_blog_scripts( $list ) {
		return equadio_customizer_add_blog_styles_and_scripts( $list, 'scripts' );
	}
}

// Enqueue theme-specific blog scripts
if ( ! function_exists( 'equadio_customizer_blog_styles' ) ) {
	add_action( 'wp_enqueue_scripts', 'equadio_customizer_blog_styles', 1100 );
	function equadio_customizer_blog_styles() {
		if ( equadio_is_on( equadio_get_theme_option( 'debug_mode' ) ) ) {
			equadio_customizer_add_blog_styles_and_scripts( false, 'styles' );
			equadio_customizer_add_blog_styles_and_scripts( false, 'scripts' );
		}
	}
}

// Enqueue theme-specific blog scripts for responsive
if ( ! function_exists( 'equadio_customizer_blog_styles_responsive' ) ) {
	add_action( 'wp_enqueue_scripts', 'equadio_customizer_blog_styles_responsive', 2000 );
	function equadio_customizer_blog_styles_responsive() {
		if ( equadio_is_on( equadio_get_theme_option( 'debug_mode' ) ) ) {
			equadio_customizer_add_blog_styles_and_scripts( false, 'styles', true );
		}
	}
}


// Add theme-specific single styles and scripts to the list
//-------------------------------------------------------------------------------
if ( ! function_exists( 'equadio_customizer_add_single_styles_and_scripts' ) ) {
	function equadio_customizer_add_single_styles_and_scripts( $list = false, $type = 'styles', $responsive = false ) {
		$styles = equadio_storage_get( 'single_styles' );
		if ( is_array( $styles ) && ( is_array( $list ) || apply_filters( 'equadio_filter_single_post_header', is_singular( 'post' ) || is_singular( 'attachment' ) ) ) ) {
			$single_style = equadio_get_theme_option( 'single_style' );
			foreach ( $styles as $k => $v ) {
				if ( ( is_array( $list ) || $k == $single_style ) && ! empty( $v[ $type ] ) ) {
					foreach ( (array) $v[ $type ] as $s ) {
						$path = sprintf(
							'templates/single-styles/%1$s%2$s.%3$s',
							$s,
							$responsive ? '-responsive' : '',
							'styles' == $type ? 'css' : 'js'
						);
						if ( is_array( $list ) ) {
							if ( array_search( $path, $list ) === false ) {
								$list[] = $path;
							}
						} else {
							$path = equadio_get_file_url( $path );
							if ( '' != $path ) {
								if ( 'scripts' == $type ) {
									wp_enqueue_script( 'equadio-single-script-' . esc_attr( $s ), $path, array( 'jquery' ), null, true );
								} else {
									wp_enqueue_style( 'equadio-single-style-' . esc_attr( $s . ( $responsive ? '-responsive' : '' ) ),  $path, array(), null );
								}
							}
						}
					}
				}
			}
		}
		return $list;
	}
}

// Merge theme-specific single styles
if ( ! function_exists( 'equadio_customizer_merge_single_styles' ) ) {
	add_filter( 'equadio_filter_merge_styles', 'equadio_customizer_merge_single_styles', 8, 1 );
	function equadio_customizer_merge_single_styles( $list ) {
		return equadio_customizer_add_single_styles_and_scripts( $list, 'styles' );
	}
}

// Merge theme-specific single styles
if ( ! function_exists( 'equadio_customizer_merge_single_styles_responsive' ) ) {
	add_filter( 'equadio_filter_merge_styles_responsive', 'equadio_customizer_merge_single_styles_responsive', 8, 1 );
	function equadio_customizer_merge_single_styles_responsive( $list ) {
		return equadio_customizer_add_single_styles_and_scripts( $list, 'styles', true );
	}
}

// Merge theme-specific single scripts
if ( ! function_exists( 'equadio_customizer_merge_single_scripts' ) ) {
	add_filter( 'equadio_filter_merge_scripts', 'equadio_customizer_merge_single_scripts' );
	function equadio_customizer_merge_single_scripts( $list ) {
		return equadio_customizer_add_single_styles_and_scripts( $list, 'scripts' );
	}
}

// Enqueue theme-specific single scripts
if ( ! function_exists( 'equadio_customizer_single_styles' ) ) {
	add_action( 'wp_enqueue_scripts', 'equadio_customizer_single_styles', 1100 );
	function equadio_customizer_single_styles() {
		if ( equadio_is_on( equadio_get_theme_option( 'debug_mode' ) ) ) {
			equadio_customizer_add_single_styles_and_scripts( false, 'styles' );
			equadio_customizer_add_single_styles_and_scripts( false, 'scripts' );
		}
	}
}

// Enqueue theme-specific single scripts for responsive
if ( ! function_exists( 'equadio_customizer_single_styles_responsive' ) ) {
	add_action( 'wp_enqueue_scripts', 'equadio_customizer_single_styles_responsive', 2000 );
	function equadio_customizer_single_styles_responsive() {
		if ( equadio_is_on( equadio_get_theme_option( 'debug_mode' ) ) ) {
			equadio_customizer_add_single_styles_and_scripts( false, 'styles', true );
		}
	}
}


//--------------------------------------------------------------
// Customizer JS and CSS
//--------------------------------------------------------------

// Binds JS listener to Customizer controls.
if ( ! function_exists( 'equadio_customizer_control_js' ) ) {
	add_action( 'customize_controls_enqueue_scripts', 'equadio_customizer_control_js' );
	function equadio_customizer_control_js() {
		wp_enqueue_style( 'equadio-customizer', equadio_get_file_url( 'theme-options/theme-customizer.css' ), array(), null );
		wp_enqueue_script(
			'equadio-customizer',
			equadio_get_file_url( 'theme-options/theme-customizer.js' ),
			array( 'customize-controls', 'iris', 'underscore', 'wp-util' ), null, true
		);
		wp_enqueue_style(  'spectrum', equadio_get_file_url( 'js/colorpicker/spectrum/spectrum.css' ), array(), null );
		wp_enqueue_script( 'spectrum', equadio_get_file_url( 'js/colorpicker/spectrum/spectrum.js' ), array( 'jquery' ), null, true );
		wp_localize_script( 'equadio-customizer', 'equadio_color_schemes', equadio_storage_get( 'schemes' ) );
		wp_localize_script( 'equadio-customizer', 'equadio_simple_schemes', equadio_storage_get( 'schemes_simple' ) );
		wp_localize_script( 'equadio-customizer', 'equadio_sorted_schemes', equadio_storage_get( 'schemes_sorted' ) );
		wp_localize_script( 'equadio-customizer', 'equadio_additional_colors', equadio_storage_get( 'scheme_colors_add' ) );
		wp_localize_script( 'equadio-customizer', 'equadio_theme_fonts', equadio_storage_get( 'theme_fonts' ) );
		wp_localize_script( 'equadio-customizer', 'equadio_theme_vars', equadio_get_theme_vars() );
		wp_localize_script(
			'equadio-customizer', 'equadio_customizer_vars', apply_filters(
				'equadio_filter_customizer_vars', array(
					'max_load_fonts'    => equadio_get_theme_setting( 'max_load_fonts' ),
					'msg_refresh'       => esc_html__( 'Refresh', 'equadio' ),
					'msg_reset'         => esc_html__( 'Reset', 'equadio' ),
					'msg_reset_confirm' => esc_html__( 'Are you sure you want to reset all Theme Options?', 'equadio' ),
					'actions'           => array(
						'expand' => equadio_storage_get( 'customizer_expand', array() ),
					),
				)
			)
		);
		wp_localize_script( 'equadio-customizer', 'equadio_dependencies', equadio_get_theme_dependencies() );
		equadio_admin_localize_scripts();
	}
}


// Binds JS handlers to make the Customizer preview reload changes asynchronously.
if ( ! function_exists( 'equadio_customizer_preview_js' ) ) {
	add_action( 'customize_preview_init', 'equadio_customizer_preview_js' );
	function equadio_customizer_preview_js() {
		wp_enqueue_script(
			'equadio-customizer-preview',
			equadio_get_file_url( 'theme-options/theme-customizer-preview.js' ),
			array( 'customize-preview' ), null, true
		);
		wp_localize_script( 'equadio-customizer-preview', 'equadio_color_schemes', equadio_storage_get( 'schemes' ) );
	}
}

// Output an Underscore template for generating CSS for the color scheme.
// The template generates the css dynamically for instant display in the Customizer preview.
if ( ! function_exists( 'equadio_customizer_css_template' ) ) {
	add_action( 'customize_controls_print_footer_scripts', 'equadio_customizer_css_template' );
	function equadio_customizer_css_template() {
		$colors = array();
		foreach ( equadio_get_scheme_colors() as $k => $v ) {
			$colors[ $k ] = '{{ data.' . esc_attr( $k ) . ' }}';
		}

		$tmpl_holder = 'script';

		$schemes = array_keys( equadio_get_list_schemes() );
		if ( count( $schemes ) > 0 ) {
			foreach ( $schemes as $scheme ) {
				equadio_show_layout(
					equadio_customizer_get_css(
						array(
							'colors'        => $colors,
							'scheme'        => $scheme,
							'fonts'         => false,
							'vars'          => false,
							'remove_spaces' => false,
						)
					),
					'<' . esc_attr( $tmpl_holder ) . ' type="text/html" id="tmpl-equadio-color-scheme-' . esc_attr( $scheme ) . '">',
					'</' . esc_attr( $tmpl_holder ) . '>'
				);
			}
		}

		// Fonts
		$fonts = equadio_get_theme_fonts();
		if ( is_array( $fonts ) && count( $fonts ) > 0 ) {
			foreach ( $fonts as $tag => $font ) {
				$fonts[ $tag ]['font-family']     = '{{ data["' . $tag . '"]["font-family"] }}';
				$fonts[ $tag ]['font-size']       = '{{ data["' . $tag . '"]["font-size"] }}';
				$fonts[ $tag ]['line-height']     = '{{ data["' . $tag . '"]["line-height"] }}';
				$fonts[ $tag ]['font-weight']     = '{{ data["' . $tag . '"]["font-weight"] }}';
				$fonts[ $tag ]['font-style']      = '{{ data["' . $tag . '"]["font-style"] }}';
				$fonts[ $tag ]['text-decoration'] = '{{ data["' . $tag . '"]["text-decoration"] }}';
				$fonts[ $tag ]['text-transform']  = '{{ data["' . $tag . '"]["text-transform"] }}';
				$fonts[ $tag ]['letter-spacing']  = '{{ data["' . $tag . '"]["letter-spacing"] }}';
				$fonts[ $tag ]['margin-top']      = '{{ data["' . $tag . '"]["margin-top"] }}';
				$fonts[ $tag ]['margin-bottom']   = '{{ data["' . $tag . '"]["margin-bottom"] }}';
			}
			equadio_show_layout(
				equadio_customizer_get_css(
					array(
						'colors'        => false,
						'scheme'        => '',
						'fonts'         => $fonts,
						'vars'          => false,
						'remove_spaces' => false,
					)
				),
				'<' . esc_attr( $tmpl_holder ) . ' type="text/html" id="tmpl-equadio-fonts">',
				'</' . esc_attr( $tmpl_holder ) . '>'
			);
		}

		// Theme vars
		$vars = equadio_get_theme_vars();
		if ( is_array( $vars ) && count( $vars ) > 0 ) {
			foreach ( $vars as $k => $v ) {
				$vars[ $k ] = '{{ data.' . esc_attr( $k ) . ' }}';
			}
			equadio_show_layout(
				equadio_customizer_get_css(
					array(
						'colors'        => false,
						'scheme'        => '',
						'fonts'         => false,
						'vars'          => $vars,
						'remove_spaces' => false,
					)
				),
				'<' . esc_attr( $tmpl_holder ) . ' type="text/html" id="tmpl-equadio-vars">',
				'</' . esc_attr( $tmpl_holder ) . '>'
			);
		}

	}
}


// Additional (calculated) theme-specific colors
// Attention! Don't forget setup additional colors also in the theme-customizer.js
if ( ! function_exists( 'equadio_customizer_add_theme_colors' ) ) {
	function equadio_customizer_add_theme_colors( $colors ) {
		$add = equadio_storage_get( 'scheme_colors_add' );
		if ( is_array( $add ) ) {
			foreach ( $add as $k => $v ) {
				if ( substr( $colors['text'], 0, 1 ) == '#' ) {
					$clr = $colors[ $v['color'] ];
					if ( isset( $v['hue'] ) || isset( $v['saturation'] ) || isset( $v['brightness'] ) ) {
						$clr = equadio_hsb2hex(
							equadio_hex2hsb(
								$clr,
								isset( $v['hue'] ) ? $v['hue'] : 0,
								isset( $v['saturation'] ) ? $v['saturation'] : 0,
								isset( $v['brightness'] ) ? $v['brightness'] : 0
							)
						);
					}
					if ( isset( $v['alpha'] ) ) {
						$clr = equadio_hex2rgba( $clr, $v['alpha'] );
					}
					$colors[ $k ] = $clr;
				} else {
					$colors[ $k ] = sprintf( '{{ data.%s }}', $k );
				}
			}
		}
		return $colors;
	}
}



// Additional theme-specific fonts rules
// Attention! Don't forget setup fonts rules also in the theme-customizer.js
if ( ! function_exists( 'equadio_customizer_add_theme_fonts' ) ) {
	function equadio_customizer_add_theme_fonts( $fonts ) {
		$rez = array();
		foreach ( $fonts as $tag => $font ) {
			
			$rez[ $tag ] = $font;
			
			if ( substr( $font['font-family'], 0, 2 ) != '{{' ) {
				$rez[ $tag . '_font-family' ]     = ! empty( $font['font-family'] ) && ! equadio_is_inherit( $font['font-family'] )
														? 'font-family:' . trim( $font['font-family'] ) . ';'
														: '';
				$rez[ $tag . '_font-size' ]       = ! empty( $font['font-size'] ) && ! equadio_is_inherit( $font['font-size'] )
														? 'font-size:' . equadio_prepare_css_value( $font['font-size'] ) . ';'
														: '';
				$rez[ $tag . '_line-height' ]     = ! empty( $font['line-height'] ) && ! equadio_is_inherit( $font['line-height'] )
														? 'line-height:' . trim( $font['line-height'] ) . ';'
														: '';
				$rez[ $tag . '_font-weight' ]     = ! empty( $font['font-weight'] ) && ! equadio_is_inherit( $font['font-weight'] )
														? 'font-weight:' . trim( $font['font-weight'] ) . ';'
														: '';
				$rez[ $tag . '_font-style' ]      = ! empty( $font['font-style'] ) && ! equadio_is_inherit( $font['font-style'] )
														? 'font-style:' . trim( $font['font-style'] ) . ';'
														: '';
				$rez[ $tag . '_text-decoration' ] = ! empty( $font['text-decoration'] ) && ! equadio_is_inherit( $font['text-decoration'] )
														? 'text-decoration:' . trim( $font['text-decoration'] ) . ';'
														: '';
				$rez[ $tag . '_text-transform' ]  = ! empty( $font['text-transform'] ) && ! equadio_is_inherit( $font['text-transform'] )
														? 'text-transform:' . trim( $font['text-transform'] ) . ';'
														: '';
				$rez[ $tag . '_letter-spacing' ]  = ! empty( $font['letter-spacing'] ) && ! equadio_is_inherit( $font['letter-spacing'] )
														? 'letter-spacing:' . trim( $font['letter-spacing'] ) . ';'
														: '';
				$rez[ $tag . '_margin-top' ]      = ! empty( $font['margin-top'] ) && ! equadio_is_inherit( $font['margin-top'] )
														? 'margin-top:' . equadio_prepare_css_value( $font['margin-top'] ) . ';'
														: '';
				$rez[ $tag . '_margin-bottom' ]   = ! empty( $font['margin-bottom'] ) && ! equadio_is_inherit( $font['margin-bottom'] )
														? 'margin-bottom:' . equadio_prepare_css_value( $font['margin-bottom'] ) . ';'
														: '';
			} else {
				$rez[ $tag . '_font-family' ]     = '{{ data["' . $tag . '_font-family"] }}';
				$rez[ $tag . '_font-size' ]       = '{{ data["' . $tag . '_font-size"] }}';
				$rez[ $tag . '_line-height' ]     = '{{ data["' . $tag . '_line-height"] }}';
				$rez[ $tag . '_font-weight' ]     = '{{ data["' . $tag . '_font-weight"] }}';
				$rez[ $tag . '_font-style' ]      = '{{ data["' . $tag . '_font-style"] }}';
				$rez[ $tag . '_text-decoration' ] = '{{ data["' . $tag . '_text-decoration"] }}';
				$rez[ $tag . '_text-transform' ]  = '{{ data["' . $tag . '_text-transform"] }}';
				$rez[ $tag . '_letter-spacing' ]  = '{{ data["' . $tag . '_letter-spacing"] }}';
				$rez[ $tag . '_margin-top' ]      = '{{ data["' . $tag . '_margin-top"] }}';
				$rez[ $tag . '_margin-bottom' ]   = '{{ data["' . $tag . '_margin-bottom"] }}';
			}
		}
		return $rez;
	}
}



// Additional theme-specific vars rules
// Attention! Don't forget setup vars rules also in the theme-customizer.js
if ( ! function_exists( 'equadio_customizer_add_theme_vars' ) ) {
	function equadio_customizer_add_theme_vars( $vars ) {
		$rez = $vars;
		// Add border radius
		if ( isset( $vars['rad'] ) ) {
			if ( substr( $vars['rad'], 0, 2 ) != '{{' ) {
				$rez['rad']    = equadio_prepare_css_value( ! empty( $vars['rad'] ) ? $vars['rad'] : 0 );	// Old way: equadio_get_border_radius() instead $vars['rad']
				$rez['rad50']  = ! empty( $vars['rad'] ) ? '50%' : 0;
				$rez['rad1em'] = ! empty( $vars['rad'] ) ? '1em' : 0;
				$rez['rad4']   = ! empty( $vars['rad'] ) ? '4px' : 0;
				$rez['rad3']   = ! empty( $vars['rad'] ) ? '3px' : 0;
				$rez['rad2']   = ! empty( $vars['rad'] ) ? '2px' : 0;
			} else {
				$rez['rad50']  = '{{ data.rad50 }}';
				$rez['rad1em'] = '{{ data.rad1em }}';
				$rez['rad4']   = '{{ data.rad4 }}';
				$rez['rad3']   = '{{ data.rad3 }}';
				$rez['rad2']   = '{{ data.rad2 }}';
			}
		}
		// Add page components
		if ( isset( $vars['page'] ) ) {
			if ( substr( $vars['page'], 0, 2 ) != '{{' ) {
				if ( empty( $vars['page'] ) ) {
					$vars['page'] = apply_filters( 'equadio_filter_content_width', equadio_get_theme_option( 'page_width' ) );
				}
				$rez['page']              = equadio_prepare_css_value( $vars['page'] );
				$rez['page_boxed_extra']  = equadio_prepare_css_value( $vars['page_boxed_extra'] );
				$rez['page_boxed']        = equadio_prepare_css_value( $vars['page'] + 2 * $vars['page_boxed_extra'] );
				$rez['page_fullwide_max'] = equadio_prepare_css_value( $vars['page_fullwide_max'] );
				$rez['paddings_fullwide'] = equadio_prepare_css_value( $vars['paddings_fullwide'] );
				$rez['content']           = equadio_prepare_css_value( $vars['page'] - $vars['sidebar'] - $vars['sidebar_gap'] );
				$rez['koef_narrow']       = 3 / 4;
				$rez['content_narrow']    = equadio_prepare_css_value( ( $vars['page'] - $vars['sidebar'] - $vars['sidebar_gap'] ) * $rez['koef_narrow'] );
				$rez['padding_narrow']    = equadio_prepare_css_value( ( $vars['page'] - $vars['sidebar'] - $vars['sidebar_gap'] ) * ( 1 - $rez['koef_narrow'] ) );
				$rez['sidebar']           = equadio_prepare_css_value( $vars['sidebar'] );
				$rez['sidebar_gap']       = equadio_prepare_css_value( $vars['sidebar_gap'] );
				$rez['sidebar_and_gap']   = equadio_prepare_css_value( $vars['sidebar'] + $vars['sidebar_gap'] );
				$rez['sidebar_prc']       = $vars['sidebar'] / $vars['page'];
				$rez['sidebar_gap_prc']   = $vars['sidebar_gap'] / $vars['page'];
				$rez['grid_gap']          = equadio_prepare_css_value( $vars['grid_gap'] );
			} else {
				$rez['page_boxed']      = '{{ data.page_boxed }}';
				$rez['content']         = '{{ data.content }}';
				$rez['koef_narrow']     = '{{ data.koef_narrow }}';
				$rez['content_narrow']  = '{{ data.content_narrow }}';
				$rez['padding_narrow']  = '{{ data.padding_narrow }}';
				$rez['sidebar_and_gap'] = '{{ data.sidebar_gap }}';
				$rez['sidebar_prc']     = '{{ data.sidebar_prc }}';
				$rez['sidebar_gap_prc'] = '{{ data.sidebar_gap_prc }}';
			}
		}
		return apply_filters( 'equadio_filter_add_theme_vars', $rez, $vars );
	}
}


//----------------------------------------------------------------------------------------------
// Add fix to allow theme-specific sidebars in Customizer (if is_customize_preview() mode)
//----------------------------------------------------------------------------------------------
if ( ! function_exists( 'equadio_customizer_fix_sidebars' ) && is_customize_preview() && is_front_page() ) {
	add_action( 'wp_footer', 'equadio_customizer_fix_sidebars' );
	function equadio_customizer_fix_sidebars() {
		$sidebars = equadio_get_sidebars();
		if ( is_array( $sidebars ) ) {
			foreach ( $sidebars as $sb => $params ) {
				if ( ! empty( $params['front_page_section'] ) && is_active_sidebar( $sb ) ) {
					?>
					<div class="hidden"><?php dynamic_sidebar( $sb ); ?></div><?php
				}
			}
		}
	}
}


// Load theme options and styles
require_once EQUADIO_THEME_DIR . 'theme-specific/theme-setup.php';
require_once EQUADIO_THEME_DIR . 'theme-options/theme-customizer-css-vars.php';
require_once EQUADIO_THEME_DIR . 'theme-options/theme-options.php';
require_once EQUADIO_THEME_DIR . 'theme-options/theme-options-override.php';
if ( ! EQUADIO_THEME_FREE ) {
	require_once EQUADIO_THEME_DIR . 'theme-options/theme-options-qsetup.php';
}
