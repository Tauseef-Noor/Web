<?php
/**
 * Skins support
 *
 * @package EQUADIO
 * @since EQUADIO 1.0.46
 */

// Define constants for the current skin
if ( ! defined( 'EQUADIO_SKIN_NAME' ) ) {
	$equadio_current_skin = '';
	if ( ! is_admin() ) {
		$equadio_current_skin = equadio_get_value_gp( 'skin' );
		if ( EQUADIO_REMEMBER_SKIN ) {
			if ( empty( $equadio_current_skin ) ) {
				$equadio_current_skin = ! empty( $_COOKIE[ 'equadio_current_skin' ] ) ? wp_unslash( sanitize_text_field($_COOKIE[ 'equadio_current_skin' ]) ) : '';
			} else {
				setcookie( 'equadio_current_skin', $equadio_current_skin, 0, '/' );
			}
		}
	}
	if ( empty( $equadio_current_skin ) ) {
		$equadio_current_skin = get_option( sprintf( 'theme_skin_%s', get_option( 'stylesheet' ) ), EQUADIO_DEFAULT_SKIN );
	}
	define( 'EQUADIO_SKIN_NAME', $equadio_current_skin );
}

// Return name of the current skin (can be overriden on the page)
if ( ! function_exists( 'equadio_skins_get_current_skin_name' ) ) {
	function equadio_skins_get_current_skin_name() {
		return EQUADIO_SKIN_NAME;
	}
}

// Return dir of the current skin (can be overriden on the page)
if ( ! function_exists( 'equadio_skins_get_current_skin_dir' ) ) {
	function equadio_skins_get_current_skin_dir( $skin=false ) {
		return 'skins/' . trailingslashit( $skin ? $skin : equadio_skins_get_current_skin_name() );
	}
}

// Theme init priorities:
// Action 'after_setup_theme'
// 1 - register filters to add/remove lists items in the Theme Options
if ( ! function_exists( 'equadio_skins_theme_setup1' ) ) {
	add_action( 'after_setup_theme', 'equadio_skins_theme_setup1', 1 );
	function equadio_skins_theme_setup1() {
		equadio_storage_set( 'skins', apply_filters( 'equadio_filter_skins_list', array() ) );
	}
}


// Add class to the body with current skin name
if ( ! function_exists( 'equadio_skins_add_body_class' ) ) {
	add_filter( 'body_class', 'equadio_skins_add_body_class' );
	function equadio_skins_add_body_class( $classes ) {
		$classes[] = sprintf( 'skin_%s', equadio_skins_get_current_skin_name() );
		return $classes;
	}
}


// Retrieve available skins from the upgrade-server
if ( ! function_exists( 'equadio_skins_get_available_skins' ) ) {
	add_filter( 'equadio_filter_skins_list', 'equadio_skins_get_available_skins' );
	function equadio_skins_get_available_skins( $skins = array() ) {
		$skins_file      = equadio_get_file_dir( 'skins/skins.json' );
		$skins_installed = json_decode( equadio_fgc( $skins_file ), true );
		$skins           = get_transient( 'equadio_list_skins' );
		if ( ! is_array( $skins ) || count( $skins ) == 0 ) {
			$skins_available = equadio_fgc( trailingslashit( equadio_storage_get( 'theme_upgrade_url' ) ) . 'upgrade.php?action=info_skins&theme_slug=' . urlencode( get_option( 'template' ) ) );
			if ( is_serialized( $skins_available ) ) {
				$skins_available = equadio_unserialize( $skins_available );
				if ( empty( $skins_available['error'] ) && ! empty( $skins_available['data'] ) && $skins_available['data'][0] == '{' ) {
					$skins = json_decode( $skins_available['data'], true );
				}
			}
			if ( ! is_array( $skins ) || count( $skins ) == 0 ) {
				$skins = $skins_installed;
			}
			set_transient( 'equadio_list_skins', $skins, 8 * 60 * 60 );       // Store to the cache for 8 hours
		}
		// Check if new skins appears after the theme update
		// (included in the folder 'skins' inside the theme)
		if ( is_array( $skins_installed ) && count( $skins_installed ) > 0 ) {
			foreach( $skins_installed as $k => $v ) {
				if ( ! isset( $skins[ $k ] ) ) {
					$skins[ $k ] = $v;
				}
			}
		}
		// Check the state of each skin
		if ( is_array( $skins ) && count( $skins ) > 0 ) {
			foreach( $skins as $k => $v ) {
				if ( ! is_array( $v ) ) {
					unset( $skins[ $k ] );
				} else {
					$skins[ $k ][ 'installed' ] = equadio_skins_get_file_dir( "skin.php", $k ) != '' && ! empty( $skins_installed[ $k ][ 'version' ] )
													? $skins_installed[ $k ][ 'version' ]
													: '';
				}
			}
		}
		return $skins;
	}
}



// Notice with info about new skins or new versions of installed skins
//------------------------------------------------------------------------

// Show admin notice
if ( ! function_exists( 'equadio_skins_admin_notice' ) ) {
	add_action('admin_notices', 'equadio_skins_admin_notice');
	function equadio_skins_admin_notice() {
		// Check if new skins available
		if ( current_user_can( 'update_themes' ) ) {
			$skins  = equadio_storage_get( 'skins' );
			$update = 0;
			$free   = 0;
			$pay    = 0;
			foreach ( $skins as $skin => $data ) {
				if ( ! empty( $data['installed'] ) ) {
					if ( version_compare( $data['installed'], $data['version'], '<' ) ) {
						$update++;
					}
				} else if ( ! empty( $data['buy_url'] ) ) {
					$pay++;
				} else { 
					$free++;
				}
			}
			// Show notice
			$show = get_option( 'equadio_skins_notice' );
			if ( ( false !== $show && 0 == (int) $show ) || $update + $free + $pay == 0 || ! equadio_exists_trx_addons() ) {
				return;
			}
			set_query_var( 'equadio_skins_notice_args', compact( 'update', 'free', 'pay' ) );
			get_template_part( apply_filters( 'equadio_filter_get_template_part', 'skins/skins-notice' ) );
		}
	}
}

// Hide admin notice
if ( ! function_exists( 'equadio_callback_hide_skins_notice' ) ) {
	add_action('wp_ajax_equadio_hide_skins_notice', 'equadio_callback_hide_skins_notice');
	function equadio_callback_hide_skins_notice() {
		if ( wp_verify_nonce( equadio_get_value_gp( 'nonce' ), admin_url( 'admin-ajax.php' ) ) ) {
			update_option( 'equadio_skins_notice', '0' );
		}
		equadio_exit();
	}
}


// Add skins folder to the theme-specific file search
//------------------------------------------------------------

// Check if file exists in the skin folder and return its path or empty string if file is not found
if ( ! function_exists( 'equadio_skins_get_file_dir' ) ) {
	function equadio_skins_get_file_dir( $file, $skin = false, $return_url = false ) {
		if ( strpos( $file, '//' ) !== false ) {
			$dir = $file;
		} else {
			$dir = '';
			if ( EQUADIO_ALLOW_SKINS ) {
				$skin_dir = equadio_skins_get_current_skin_dir( $skin );
				if ( strpos( $file, $skin_dir ) === 0 ) {
					$skin_dir = '';
				}
				if ( EQUADIO_CHILD_DIR != EQUADIO_THEME_DIR && file_exists( EQUADIO_CHILD_DIR . ( $skin_dir ) . ( $file ) ) ) {
					$dir = ( $return_url ? EQUADIO_CHILD_URL : EQUADIO_CHILD_DIR ) . ( $skin_dir ) . equadio_check_min_file( $file, EQUADIO_CHILD_DIR . ( $skin_dir ) );
				} elseif ( file_exists( EQUADIO_THEME_DIR . ( $skin_dir ) . ( $file ) ) ) {
					$dir = ( $return_url ? EQUADIO_THEME_URL : EQUADIO_THEME_DIR ) . ( $skin_dir ) . equadio_check_min_file( $file, EQUADIO_THEME_DIR . ( $skin_dir ) );
				}
			}
		}
		return $dir;
	}
}

// Check if file exists in the skin folder and return its url or empty string if file is not found
if ( ! function_exists( 'equadio_skins_get_file_url' ) ) {
	function equadio_skins_get_file_url( $file, $skin = false ) {
		return equadio_skins_get_file_dir( $file, $skin, true );
	}
}


// Add skins folder to the theme-specific files search
if ( ! function_exists( 'equadio_skins_get_theme_file_dir' ) ) {
	add_filter( 'equadio_filter_get_theme_file_dir', 'equadio_skins_get_theme_file_dir', 10, 3 );
	function equadio_skins_get_theme_file_dir( $dir, $file, $return_url = false ) {
		return equadio_skins_get_file_dir( $file, equadio_skins_get_current_skin_name(), $return_url );
	}
}


// Check if folder exists in the current skin folder and return its path or empty string if the folder is not found
if ( ! function_exists( 'equadio_skins_get_folder_dir' ) ) {
	function equadio_skins_get_folder_dir( $folder, $skin = false, $return_url = false ) {
		$dir = '';
		if ( EQUADIO_ALLOW_SKINS ) {
			$skin_dir = equadio_skins_get_current_skin_dir( $skin );
			if ( EQUADIO_CHILD_DIR != EQUADIO_THEME_DIR && is_dir( EQUADIO_CHILD_DIR . ( $skin_dir ) . ( $folder ) ) ) {
				$dir = ( $return_url ? EQUADIO_CHILD_URL : EQUADIO_CHILD_DIR ) . ( $skin_dir ) . ( $folder );
			} elseif ( is_dir( EQUADIO_THEME_DIR . ( $skin_dir ) . ( $folder ) ) ) {
				$dir = ( $return_url ? EQUADIO_THEME_URL : EQUADIO_THEME_DIR ) . ( $skin_dir ) . ( $folder );
			}
		}
		return $dir;
	}
}

// Check if folder exists in the skin folder and return its url or empty string if folder is not found
if ( ! function_exists( 'equadio_skins_get_folder_url' ) ) {
	function equadio_skins_get_folder_url( $folder, $skin = false ) {
		return equadio_skins_get_folder_dir( $folder, $skin, true );
	}
}

// Add skins folder to the theme-specific folders search
if ( ! function_exists( 'equadio_skins_get_theme_folder_dir' ) ) {
	add_filter( 'equadio_filter_get_theme_folder_dir', 'equadio_skins_get_theme_folder_dir', 10, 3 );
	function equadio_skins_get_theme_folder_dir( $dir, $folder, $return_url = false ) {
		return equadio_skins_get_folder_dir( $folder, equadio_skins_get_current_skin_name(), $return_url );
	}
}


// Add skins folder to the get_template_part
if ( ! function_exists( 'equadio_skins_get_template_part' ) ) {
	add_filter( 'equadio_filter_get_template_part', 'equadio_skins_get_template_part', 10, 2 );
	function equadio_skins_get_template_part( $slug, $part = '' ) {
		if ( ! empty( $part ) ) {
			$part = "-{$part}";
		}
		$slug_in_skins = str_replace( '//', '/', sprintf( 'skins/%1$s/%2$s', equadio_skins_get_current_skin_name(), $slug ) );
		if ( equadio_skins_get_file_dir( "{$slug}{$part}.php" ) != '' ) {
			$slug = $slug_in_skins;
		} else {
			if ( equadio_get_file_dir( "{$slug}{$part}.php" ) == '' && equadio_skins_get_file_dir( "{$slug}.php" ) != '' ) {
				$slug = $slug_in_skins;
			}
		}
		return $slug;
	}
}



// Add skin-specific styles to the Gutenberg preview
//------------------------------------------------------

if ( ! function_exists( 'equadio_skins_gutenberg_get_styles' ) ) {
	add_filter( 'equadio_filter_gutenberg_get_styles', 'equadio_skins_gutenberg_get_styles' );
	function equadio_skins_gutenberg_get_styles( $css ) {
		$css .= equadio_fgc( equadio_get_file_dir( equadio_skins_get_current_skin_dir() . 'css/style.css' ) );
		return $css;
	}
}



// Add tab with skins to the 'Theme Panel'
//------------------------------------------------------

// Add step 'Skins'
if ( ! function_exists( 'equadio_skins_theme_panel_steps' ) ) {
	add_filter( 'trx_addons_filter_theme_panel_steps', 'equadio_skins_theme_panel_steps' );
	function equadio_skins_theme_panel_steps( $steps ) {
		if ( EQUADIO_ALLOW_SKINS ) {
			$steps = equadio_array_merge( array( 'skins' => wp_kses_data( __( 'Select a skin for your website.', 'equadio' ) ) ), $steps );
		}
		return $steps;
	}
}

// Add tab link 'Skins'
if ( ! function_exists( 'equadio_skins_theme_panel_tabs' ) ) {
	add_filter( 'trx_addons_filter_theme_panel_tabs', 'equadio_skins_theme_panel_tabs' );
	function equadio_skins_theme_panel_tabs( $tabs ) {
		if ( EQUADIO_ALLOW_SKINS ) {
			equadio_array_insert_after( $tabs, 'general', array( 'skins' => esc_html__( 'Skins', 'equadio' ) ) );
		}
		return $tabs;
	}
}

// Display 'Skins' section in the Theme Panel
if ( ! function_exists( 'equadio_skins_theme_panel_section' ) ) {
	add_action( 'trx_addons_action_theme_panel_section', 'equadio_skins_theme_panel_section', 10, 2);
	function equadio_skins_theme_panel_section( $tab_id, $theme_info ) {
		if ( 'skins' !== $tab_id ) return;
		?>
		<div id="trx_addons_theme_panel_section_<?php echo esc_attr($tab_id); ?>" class="trx_addons_tabs_section">

			<?php
			do_action('trx_addons_action_theme_panel_section_start', $tab_id, $theme_info);

			if ( trx_addons_is_theme_activated() ) {
				?>
				<div class="trx_addons_theme_panel_skins_selector">

					<?php do_action('trx_addons_action_theme_panel_before_section_title', $tab_id, $theme_info); ?>
		
					<h1 class="trx_addons_theme_panel_section_title">
						<?php esc_html_e( 'Choose a Skin', 'equadio' ); ?>
					</h1>

					<?php do_action('trx_addons_action_theme_panel_after_section_title', $tab_id, $theme_info); ?>

					<div class="trx_addons_theme_panel_section_info trx_addons_info_box">
						<p><?php echo wp_kses_data( __( 'Select the desired style of your website. Some skins may require you to install additional plugins.', 'equadio' ) ); ?></p>
					</div>

					<?php do_action('trx_addons_action_theme_panel_before_list_items', $tab_id, $theme_info); ?>
					
					<div class="trx_addons_theme_panel_skins_list trx_addons_image_block_wrap">
						<?php
						$skins = equadio_storage_get( 'skins' );
						foreach ( $skins as $skin => $data ) {
							$skin_classes = array();
							if ( EQUADIO_SKIN_NAME == $skin ) {
								$skin_classes[] = 'skin_active';
							}
							if ( ! empty( $data['installed'] ) ) {
								$skin_classes[] = 'skin_installed';
							} else if ( ! empty( $data['buy_url'] ) ) {
								$skin_classes[] = 'skin_buy';
							} else {
								$skin_classes[] = 'skin_free';
							}
							// 'trx_addons_image_block' is a inline-block element and spaces around it are not allowed
							?><div class="trx_addons_image_block <?php echo esc_attr( join( ' ', $skin_classes ) ); ?>">
								<div class="trx_addons_image_block_inner" tabindex="0">
									<div class="trx_addons_image_block_image
									 	<?php 
										$theme_slug  = get_option( 'template' );
										// Skin image
										$img = ! empty( $data['installed'] )
												? equadio_skins_get_file_url( 'skin.jpg', $skin )
												: trailingslashit( equadio_storage_get( 'theme_upgrade_url' ) ) . 'skins/' . urlencode( $theme_slug ) . '/' . urlencode( $skin ) . '/skin.jpg';
										if ( ! empty( $img ) ) {
											echo equadio_add_inline_css_class( 'background-image: url(' . esc_url( $img ) . ');' );
										}				 	
									 	?>">
									 	<?php
										// Link to demo site
										if ( ! empty( $data['demo_url'] ) ) {
											?>
											<a href="<?php echo esc_url( $data['demo_url'] ); ?>" class="trx_addons_image_block_link trx_addons_image_block_link_view_demo" target="_blank" tabindex="-1">
												<?php
												esc_html_e( 'Live Preview', 'equadio' );
												?>
											</a>
											<?php
										}
										?>
								 	</div>
								 	<div class="trx_addons_image_block_footer">
										<?php
										// Link to choose skin
										if ( EQUADIO_SKIN_NAME == $skin ) {
											if ( ! equadio_skins_update_button( $skin, $data ) ) {
												?>
												<span class="trx_addons_image_block_link trx_addons_image_block_link_active">
													<?php
													esc_html_e( 'Activated', 'equadio' );
													?>
												</span>
												<?php
											}

										} else if ( ! empty( $data['installed'] ) ) {
											if ( ! equadio_skins_update_button( $skin, $data ) ) {
												?>
												<a href="#" tabindex="0"
													class="trx_addons_image_block_link trx_addons_image_block_link_choose_skin trx_addons_button trx_addons_button_small trx_addons_button_accent"
													data-skin="<?php echo esc_attr( $skin ); ?>">
														<?php
														esc_html_e( 'Activate', 'equadio' );
														?>
												</a>
												<?php
											}

										} else if ( ! empty( $data['buy_url'] ) ) {
											?>
											<a href="#" tabindex="0"
												class="trx_addons_image_block_link trx_addons_image_block_link_buy_skin trx_addons_button trx_addons_button_small trx_addons_button_accent"
												data-skin="<?php echo esc_attr( $skin ); ?>"
												data-buy="<?php echo esc_url( $data['buy_url'] ); ?>">
													<?php
													esc_html_e( 'Purchase', 'equadio' );
													?>
											</a>
											<?php

										} else {
											?>
											<a href="#" tabindex="0"
												class="trx_addons_image_block_link trx_addons_image_block_link_download_skin trx_addons_button trx_addons_button_small"
												data-skin="<?php echo esc_attr( $skin ); ?>">
													<?php
													esc_html_e( 'Download', 'equadio' );
													?>
											</a>
											<?php
										}
										// Skin title
										if ( ! empty( $data['title'] ) ) {
											?>
											<h5 class="trx_addons_image_block_title">
												<?php
												// Translators: Add version of the skin to the string
												echo esc_html( $data['title'] )
													. ( ! empty( $data['installed'] )
														? ' ' . esc_html( sprintf( __( 'v.%s', 'equadio' ), $data['installed'] ) )
														: ''
														);
												?>
											</h5>
											<?php
										}
										// Skin description
										if ( ! empty( $data['description'] ) ) {
											?>
											<div class="trx_addons_image_block_description">
												<?php
												echo wp_kses( $data['description'], 'equadio_kses_content' );
												?>
											</div>
											<?php
										}
										?>
									</div>
								</div>
							</div><?php // No spaces allowed after this <div>, because it is an inline-block element
						}
						?>
					</div>

					<?php do_action('trx_addons_action_theme_panel_after_list_items', $tab_id, $theme_info); ?>

				</div>
				<?php
				do_action('trx_addons_action_theme_panel_after_section_data', $tab_id, $theme_info);
			} else {
				?>
				<div class="<?php
					if ( equadio_exists_trx_addons() ) {
						echo 'trx_addons_info_box trx_addons_info_box_warning';
					} else {
						echo 'error';
					}
				?>"><p>
					<?php esc_html_e( 'Activate your theme in order to be able to change skins.', 'equadio' ); ?>
				</p></div>
				<?php
			}

			do_action('trx_addons_action_theme_panel_section_end', $tab_id, $theme_info);
			?>
		</div>
		<?php
	}
}


// Display button 'Update skin'
if ( ! function_exists( 'equadio_skins_update_button' ) ) {
	function equadio_skins_update_button( $skin, $data ) {
		$rez = version_compare( $data['installed'], $data['version'], '<' );
		if ( $rez ) {
			?>
			<a href="#"
				class="trx_addons_image_block_link trx_addons_image_block_link_update_skin trx_addons_button trx_addons_button_small trx_addons_button_accent"
				data-skin="<?php echo esc_attr( $skin ); ?>">
					<?php
					// Translators: Add new version of the skin to the string
					echo esc_html( sprintf( __( 'Update to v.%s', 'equadio' ), $data['version'] ) );
					?>
			</a>
			<?php
		}
		return $rez;
	}
}


// Load page-specific scripts and styles
if ( ! function_exists( 'equadio_skins_about_enqueue_scripts' ) ) {
	add_action( 'admin_enqueue_scripts', 'equadio_skins_about_enqueue_scripts' );
	function equadio_skins_about_enqueue_scripts() {
		$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : false;
		if ( ! empty( $screen->id ) && ( false !== strpos($screen->id, '_page_trx_addons_theme_panel') || 'update-core' == $screen->id ) ) {
			wp_enqueue_style( 'equadio-skins-admin', equadio_get_file_url( 'skins/skins-admin.css' ), array(), null );
			wp_enqueue_script( 'equadio-skins-admin', equadio_get_file_url( 'skins/skins-admin.js' ), array( 'jquery' ), null, true );
		}
	}
}

// Add page-specific vars to the localize array
if ( ! function_exists( 'equadio_skins_localize_script' ) ) {
	add_filter( 'equadio_filter_localize_script_admin', 'equadio_skins_localize_script' );
	function equadio_skins_localize_script( $arr ) {

		// Switch an active skin
		$arr['msg_switch_skin_caption']           = esc_html__( "Attention!", 'equadio' );
		$arr['msg_switch_skin']                   = apply_filters( 'equadio_filter_msg_switch_skin',
			'<p>'
			. esc_html__( "Some skins require installation of additional plugins.", 'equadio' )
			. '</p><p>'
			. esc_html__( "After selecting a new skin, your theme settings will be changed.", 'equadio' )
			. '</p>'
		);
		$arr['msg_switch_skin_success']           = esc_html__( 'A new skin is selected. The page will be reloaded.', 'equadio' );
		$arr['msg_switch_skin_success_caption']   = esc_html__( 'Skin is changed!', 'equadio' );

		// Download a new skin
		$arr['msg_download_skin_caption']         = esc_html__( "Download skin", 'equadio' );
		$arr['msg_download_skin']                 = apply_filters( 'equadio_filter_msg_download_skin',
			'<p>'
			. esc_html__( "The new skin will be installed in the 'skins' folder inside your theme folder.", 'equadio' )
			. '</p><p>'
			. esc_html__( "Attention! Do not forget to activate the new skin after installation.", 'equadio' )
			. '</p>'
		);
		$arr['msg_download_skin_success']         = esc_html__( 'A new skin is installed. The page will be reloaded.', 'equadio' );
		$arr['msg_download_skin_success_caption'] = esc_html__( 'Skin is installed!', 'equadio' );
		$arr['msg_download_skin_error_caption']   = esc_html__( 'Skin download error!', 'equadio' );

		// Buy a new skin
		$arr['msg_buy_skin_caption']              = esc_html__( "Download purchased skin", 'equadio' );
		$arr['msg_buy_skin']                      = apply_filters( 'equadio_filter_msg_buy_skin',
			'<p>'
			. esc_html__( "1. Follow the link below and purchase the selected skin. After payment you will receive a purchase code.", 'equadio' )
			. '</p><p>'
			. '<a href="#" target="_blank">' . esc_html__( "Purchase the selected skin.", 'equadio' ) . '</a>'
			. '</p><p>'
			. esc_html__( "2. Enter the purchase code of the selected skin in the field below and press the button 'Apply'.", 'equadio' )
			. '</p><p>'
			. esc_html__( "3. The new skin will be installed to the folder 'skins' inside your theme folder.", 'equadio' )
			. '</p><p>'
			. esc_html__( "Attention! Do not forget to activate the new skin after installation.", 'equadio' )
			. '</p>'
		);
		$arr['msg_buy_skin_placeholder']          = esc_html__( 'Enter the purchase code of the skin.', 'equadio' );
		$arr['msg_buy_skin_success']              = esc_html__( 'A new skin is installed. The page will be reloaded.', 'equadio' );
		$arr['msg_buy_skin_success_caption']      = esc_html__( 'Skin is installed!', 'equadio' );
		$arr['msg_buy_skin_error_caption']        = esc_html__( 'Skin download error!', 'equadio' );

		// Update an installed skin
		$arr['msg_update_skin_caption']         = esc_html__( "Update skin", 'equadio' );
		$arr['msg_update_skin']                 = apply_filters( 'equadio_filter_msg_update_skin',
			'<p>'
			. esc_html__( "Attention! The new version of the skin will be installed in the same folder instead the current version!", 'equadio' )
			. '</p><p>'
			. esc_html__( "If you made any changes in the files from the folder of the selected skin - they will be lost.", 'equadio' )
			. '</p>'
		);
		$arr['msg_update_skin_success']         = esc_html__( 'The skin is updated. The page will be reloaded.', 'equadio' );
		$arr['msg_update_skin_success_caption'] = esc_html__( 'Skin is updated!', 'equadio' );
		$arr['msg_update_skin_error_caption']   = esc_html__( 'Skin update error!', 'equadio' );
		$arr['msg_update_skins_result']         = esc_html__( 'Selected skins are updated.', 'equadio' );

		return $arr;
	}
}


// AJAX handler for the 'equadio_switch_skin' action
if ( ! function_exists( 'equadio_skins_ajax_switch_skin' ) ) {
	add_action( 'wp_ajax_equadio_switch_skin', 'equadio_skins_ajax_switch_skin' );
	function equadio_skins_ajax_switch_skin() {

		equadio_verify_nonce();

		if ( ! current_user_can( 'edit_theme_options' ) ) {
			equadio_forbidden( esc_html__( 'Sorry, you are not allowed to switch skins.', 'equadio' ) );
		}

		$response = array( 'error' => '' );

		$skin  = equadio_get_value_gp( 'skin' );
		$skins = equadio_storage_get( 'skins' );

		if ( empty( $skin ) || ! isset( $skins[ $skin ] ) || empty( $skins[ $skin ]['installed'] ) ) {
			// Translators: Add the skin's name to the message
			$response['error'] = sprintf( esc_html__( 'Can not switch to the skin %s', 'equadio' ), $skin );

		} elseif ( EQUADIO_SKIN_NAME == $skin ) {
			// Translators: Add the skin's name to the message
			$response['error'] = sprintf( esc_html__( 'Skin %s is already active', 'equadio' ), $skin );

		} else {
			// Get current theme slug
			$theme_slug = get_option( 'stylesheet' );
			// Get previously saved options for new skin
			$skin_mods = get_option( sprintf( 'theme_mods_%1$s_skin_%2$s', $theme_slug, $skin ), false );
			if ( ! $skin_mods ) {
				// First activation of the skin - get options from the file
				if ( file_exists( EQUADIO_THEME_DIR . 'skins/skins-options.php' ) ) {
					require_once EQUADIO_THEME_DIR . 'skins/skins-options.php';
					if ( isset( $skins_options[ $skin ]['options'] ) ) {
						$skin_mods = apply_filters(
										'equadio_filter_skin_options_restore_from_file',
										equadio_unserialize( $skins_options[ $skin ]['options'] )
										);
					}
				}
			}
			if ( false !== $skin_mods ) {
				// Save current options
				update_option( sprintf( 'theme_mods_%1$s_skin_%2$s', $theme_slug, EQUADIO_SKIN_NAME ), apply_filters( 'equadio_filter_skin_options_store', get_theme_mods() ) );
				// Replace theme mods with options from new skin
				if ( ! empty( $skin_mods ) ) {
					equadio_options_update( apply_filters( 'equadio_filter_skin_options_restore', $skin_mods ) );
				}
				// Replace current skin
				update_option( sprintf( 'theme_skin_%s', $theme_slug ), $skin );
				// Clear current skin from visitor's storage
				if ( EQUADIO_REMEMBER_SKIN ) {
					setcookie( 'skin_current', '', 0, '/' );
				}
				// Set flag to regenerate styles and scripts on first run
				update_option( 'equadio_action', '' );
				update_option( 'trx_addons_action', 'trx_addons_action_save_options' );
			} else {
				$response['error'] = esc_html__( 'Options of the new skin are not found!', 'equadio' );
			}
		}

		equadio_ajax_response( $response );
	}
}


// Remove all entries with media from options restored from file
if ( ! function_exists( 'equadio_skins_options_restore_from_file' ) ) {
	add_filter( 'equadio_filter_skin_options_restore_from_file', 'equadio_skins_options_restore_from_file' );
	function equadio_skins_options_restore_from_file( $mods ) {
		$options = equadio_storage_get( 'options' );
		if ( is_array( $options ) ) {
			foreach( $options as $k => $v ) {
				if ( ! empty( $v['type'] ) && in_array( $v['type'], array( 'image', 'media', 'video', 'audio' ) ) && isset( $mods[ $k ] ) ) {
					unset( $mods[ $k ] );
				}
			}
		}
		return $mods;
	}
}


// AJAX handler for the 'equadio_download_skin' action
if ( ! function_exists( 'equadio_skins_ajax_download_skin' ) ) {
	add_action( 'wp_ajax_equadio_download_skin', 'equadio_skins_ajax_download_skin' );
	add_action( 'wp_ajax_equadio_buy_skin', 'equadio_skins_ajax_download_skin' );
	add_action( 'wp_ajax_equadio_update_skin', 'equadio_skins_ajax_download_skin' );
	function equadio_skins_ajax_download_skin() {

		equadio_verify_nonce();

		$response = array( 'error' => '' );

		$action   = current_action() == 'wp_ajax_equadio_download_skin'
						? 'download'
						: (current_action() == 'wp_ajax_equadio_buy_skin'
							? 'buy'
							: 'update' );

		$key      = equadio_get_theme_activation_code();

		$skin     = equadio_get_value_gp( 'skin' );
		$code     = 'update' == $action
						? get_option( sprintf( 'purchase_code_%s_%s', get_option( 'template' ), $skin ), '' )
						: equadio_get_value_gp( 'code' );

		$skins    = equadio_storage_get( 'skins' );

		if ( empty( $key ) ) {
			// Translators: Add the skin's name to the message
			$response['error'] = esc_html__( 'Theme is not activated!', 'equadio' );

		} else if ( empty( $skin ) || ! isset( $skins[ $skin ] ) ) {
			// Translators: Add the skin's name to the message
			$response['error'] = sprintf( esc_html__( 'Can not download the skin %s', 'equadio' ), $skin );

		} else if ( ! empty( $skins[ $skin ]['installed'] ) && 'update' != $action ) {
			// Translators: Add the skin's name to the message
			$response['error'] = sprintf( esc_html__( 'Skin %s is already installed', 'equadio' ), $skin );

		} else {

			$theme_slug  = get_option( 'template' );
			$theme_name  = wp_get_theme( $theme_slug )->get( 'Name' );
			// Add the key, theme slug and name, skin name and purchase code to the link
			$upgrade_url = sprintf(
				trailingslashit( equadio_storage_get( 'theme_upgrade_url' ) ) . 'upgrade.php?key=%1$s&src=%2$s&theme_slug=%3$s&theme_name=%4$s&skin=%5$s&action=download_skin&skin_key=%6$s&rnd=%7$s',
				urlencode( $key ),
				urlencode( equadio_storage_get( 'theme_pro_key' ) ),
				urlencode( $theme_slug ),
				urlencode( $theme_name ),
				urlencode( $skin ),
				urlencode( $code ),
				mt_rand()
			);
			$result      = function_exists( 'trx_addons_fgc' ) ? trx_addons_fgc( $upgrade_url ) : equadio_fgc( $upgrade_url );
			if ( is_serialized( $result ) ) {
				try {
					// JSON is bad working with big data:
					// Use serialization instead:
					$result = equadio_unserialize( $result );
				} catch ( Exception $e ) {
					$result = array(
						'error' => esc_html__( 'Unrecognized server answer!', 'equadio' ),
						'data'  => '',
						'info'  => ''
					);
				}
				if ( isset( $result['error'] ) && isset( $result['data'] ) ) {
					if ( substr( $result['data'], 0, 2 ) == 'PK' ) {
						$tmp_name = 'tmp-' . rand() . '.zip';
						$tmp      = wp_upload_bits( $tmp_name, null, $result['data'] );
						if ( $tmp['error'] ) {
							$response['error'] = esc_html__( 'Problem with save upgrade file to the folder with uploads', 'equadio' );
						} else {
							$response['error'] .= equadio_skins_install_skin( $skin, $tmp['file'], $result['info'] );
							// Store purchase code to update skins in the future
							if ( ! empty( $code ) && empty( $response['error'] ) ) {
								update_option( sprintf( 'purchase_code_%s_%s', get_option( 'template' ), $skin ), $code );
							}
						}
					} else {
						$response['error'] = ! empty( $result['error'] )
														? $result['error']
														: esc_html__( 'Package with upgrade is corrupt', 'equadio' );
					}
				} else {
					$response['error'] = esc_html__( 'Incorrect server answer', 'equadio' );
				}
			} else {
				$response['error'] = esc_html__( 'Unrecognized server answer format:', 'equadio' ) . strlen( $result ) . ' "' . substr( $result, 0, 100 ) . '...' . substr( $result, -100 ) . '"';
			}
		}

		equadio_ajax_response( $response );
	}
}


// Unpack and install skin
if ( ! function_exists( 'equadio_skins_install_skin' ) ) {
	function equadio_skins_install_skin( $skin, $file, $info ) {
		if ( file_exists( $file ) ) {
			ob_start();
			// Unpack skin
			$dest = equadio_get_folder_dir( '/skins' );
			if ( ! empty( $dest ) ) {
				unzip_file( $file, $dest );
			}
			// Remove uploaded archive
			unlink( $file );
			$log = ob_get_contents();
			ob_end_clean();
			// Save skin options
			if ( ! empty( $info['skin_options'] ) ) {
				if ( is_string( $info['skin_options'] ) && is_serialized( $info['skin_options'] ) ) {
					$info['skin_options'] = equadio_unserialize( stripslashes( $info['skin_options'] ) );
				}
				if ( is_array( $info['skin_options'] ) ) {
					$theme_slug  = get_option( 'stylesheet' );
					update_option( sprintf( 'theme_mods_%1$s_skin_%2$s', $theme_slug, $skin ), $info['skin_options'] );
				}
			}
			// Update skins list
			$skins_file      = equadio_get_file_dir( 'skins/skins.json' );
			$skins_installed = json_decode( equadio_fgc( $skins_file ), true );
			$skins_available = equadio_storage_get( 'skins' );
			if ( isset( $skins_available[ $skin ][ 'installed' ] ) ) {
				unset( $skins_available[ $skin ][ 'installed' ] );
			}
			$skins_installed[ $skin ] = $skins_available[ $skin ];
			equadio_fpc( $skins_file, json_encode( $skins_installed, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_FORCE_OBJECT ) );
			// Remove stored list to reload it while next site visit occurs
			delete_transient( 'equadio_list_skins' );
		} else {
			return esc_html__( 'Uploaded file with skin package is not available', 'equadio' );
		}
	}
}



//-------------------------------------------------------
//-- Update skins via WordPress update screen
//-------------------------------------------------------

// Add new skins versions to the WordPress update screen
if ( ! function_exists( 'equadio_skins_update_list' ) ) {
	add_action('core_upgrade_preamble', 'equadio_skins_update_list');
	function equadio_skins_update_list() {
		if ( current_user_can( 'update_themes' ) && equadio_is_theme_activated() ) {
			$skins  = equadio_storage_get( 'skins' );
			$update = 0;
			foreach ( $skins as $skin => $data ) {
				if ( ! empty( $data['installed'] ) && version_compare( $data['installed'], $data['version'], '<' ) ) {
					$update++;
				}
			}
			?>
			<h2>
				<?php esc_html_e( 'Active theme components: Skins', 'equadio' ); ?>
			</h2>
			<?php
			if ( $update == 0 ) {
				?>
				<p><?php esc_html_e( 'Skins of the current theme are all up to date.', 'equadio' ); ?></p>
				<?php
				return;
			}
			?>
			<p>
				<?php esc_html_e( 'The following skins have new versions available. Check the ones you want to update and then click &#8220;Update Skins&#8221;.', 'equadio' ); ?>
			</p>
			<p>
				<?php echo wp_kses_data( __( '<strong>Please Note:</strong> Any customizations you have made to skin files will be lost.', 'equadio' ) ); ?>
			</p>
			<div class="upgrade equadio_upgrade_skins">
				<p><input id="upgrade-skins" class="button equadio_upgrade_skins_button" type="button" value="<?php esc_attr_e( 'Update Skins', 'equadio' ); ?>" /></p>
				<table class="widefat updates-table" id="update-skins-table">
					<thead>
					<tr>
						<td class="manage-column check-column"><input type="checkbox" id="skins-select-all" /></td>
						<td class="manage-column"><label for="skins-select-all"><?php esc_html_e( 'Select All', 'equadio' ); ?></label></td>
					</tr>
					</thead>
					<tbody class="plugins">
						<?php
						foreach ( $skins as $skin => $data ) {
							if ( empty( $data['installed'] ) || ! version_compare( $data['installed'], $data['version'], '<' ) ) {
								continue;
							}
							$checkbox_id = 'checkbox_' . md5( $skin );
							?>
							<tr>
								<td class="check-column">
									<input type="checkbox" name="checked[]" id="<?php echo esc_attr( $checkbox_id ); ?>" value="<?php echo esc_attr( $skin ); ?>" />
									<label for="<?php echo esc_attr( $checkbox_id ); ?>" class="screen-reader-text">
										<?php
										// Translators: %s: Skin name
										printf( esc_html__( 'Select %s', 'equadio' ), $data['title'] );
										?>
									</label>
								</td>
								<td class="plugin-title"><p>
									<img src="<?php echo esc_url( equadio_skins_get_file_url( 'skin.jpg', $skin ) ); ?>" width="85" class="updates-table-screenshot" alt="<?php echo esc_attr( $data['title'] ); ?>" />
									<strong><?php echo esc_html( $data['title'] ); ?></strong>
									<?php
									// Translators: 1: skin version, 2: new version
									printf(
										esc_html__( 'You have version %1$s installed. Update to %2$s.', 'equadio' ),
										$data['installed'],
										$data['version']
									);
									?>
								</p></td>
							</tr>
							<?php
						}
						?>
					</tbody>
					<tfoot>
						<tr>
							<td class="manage-column check-column"><input type="checkbox" id="skins-select-all-2" /></td>
							<td class="manage-column"><label for="skins-select-all-2"><?php esc_html_e( 'Select All', 'equadio' ); ?></label></td>
						</tr>
					</tfoot>
				</table>
				<p><input id="upgrade-skins-2" class="button equadio_upgrade_skins_button" type="button" value="<?php esc_attr_e( 'Update Skins', 'equadio' ); ?>" /></p>
			</div>
			<?php
		}
	}
}


// Add new skins count to the WordPress updates count
if ( ! function_exists( 'equadio_skins_update_counts' ) ) {
	add_filter('wp_get_update_data', 'equadio_skins_update_counts', 10, 2);
	function equadio_skins_update_counts($update_data, $titles) {
		if ( current_user_can( 'update_themes' ) ) {
			$skins  = equadio_storage_get( 'skins' );
			$update = 0;
			foreach ( $skins as $skin => $data ) {
				if ( ! empty( $data['installed'] ) && version_compare( $data['installed'], $data['version'], '<' ) ) {
					$update++;
				}
			}
			if ( $update > 0 ) {
				$update_data[ 'counts' ][ 'skins' ]  = $update;
				$update_data[ 'counts' ][ 'total' ] += $update;
				// Translators: %d: number of updates available to installed skins
				$titles['skins']                     = sprintf( _n( '%d Skin Update', '%d Skin Updates', $update, 'equadio' ), $update );
				$update_data[ 'title' ]              = esc_attr( implode( ', ', $titles ) );
			}
		}
		return $update_data;
	}
}


// One-click import support
//------------------------------------------------------------------------

// Export custom layouts
if ( ! function_exists( 'equadio_skins_importer_export' ) ) {
	if ( is_admin() ) {
		add_action( 'trx_addons_action_importer_export', 'equadio_skins_importer_export', 10, 1 );
	}
	function equadio_skins_importer_export( $importer ) {
		$skins  = equadio_storage_get( 'skins' );
		$output = '';
		if ( is_array( $skins ) && count( $skins ) > 0 ) {
			$output     = '<?php'
						. "\n//" . esc_html__( 'Skins', 'equadio' )
						. "\n\$skins_options = array(";
			$counter    = 0;
			$theme_mods = get_theme_mods();
			$theme_slug = get_option( 'stylesheet' );
			foreach ( $skins as $skin => $skin_data ) {
				$options = get_option( sprintf( 'theme_mods_%1$s_skin_%2$s', $theme_slug, $skin ), false );
				if ( false === $options ) {
					$options = $theme_mods;
				}
				$output .= ( $counter++ ? ',' : '' )
						. "\n\t\t'{$skin}' => array("
						. "\n\t\t\t\t'options' => " . '"' . str_replace( array( "\r", "\n" ), array( '\r', '\n' ), addslashes( serialize( apply_filters( 'equadio_filter_export_skin_options', $options, $skin ) ) ) ) . '"'
						. "\n\t\t\t\t)";
			}
			$output .= "\n\t\t);"
					. "\n?>";
		}
		equadio_fpc( $importer->export_file_dir( 'skins.txt' ), $output );
	}
}

// Display exported data in the fields
if ( ! function_exists( 'equadio_skins_importer_export_fields' ) ) {
	if ( is_admin() ) {
		add_action( 'trx_addons_action_importer_export_fields', 'equadio_skins_importer_export_fields', 12, 1 );
	}
	function equadio_skins_importer_export_fields( $importer ) {
		$importer->show_exporter_fields(
			array(
				'slug'     => 'skins',
				'title'    => esc_html__( 'Skins', 'equadio' ),
				'download' => 'skins-options.php',
			)
		);
	}
}

// Load file with current skin
//----------------------------------------------------------
$equadio_skin_file = equadio_skins_get_file_dir( 'skin.php' );
if ( '' != $equadio_skin_file ) {
	require_once $equadio_skin_file;
}
