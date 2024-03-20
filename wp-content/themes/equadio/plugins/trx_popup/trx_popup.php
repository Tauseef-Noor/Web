<?php
/* ThemeREX Popup support functions
------------------------------------------------------------------------------- */


// Theme init priorities:
// 9 - register other filters (for installer, etc.)
if ( ! function_exists( 'equadio_trx_popup_theme_setup9' ) ) {
	add_action( 'after_setup_theme', 'equadio_trx_popup_theme_setup9', 9 );
	function equadio_trx_popup_theme_setup9() {
		if ( is_admin() ) {
			add_filter( 'equadio_filter_tgmpa_required_plugins', 'equadio_trx_popup_tgmpa_required_plugins' );
		}
	}
}

// Filter to add in the required plugins list
if ( ! function_exists( 'equadio_trx_popup_tgmpa_required_plugins' ) ) {	
	function equadio_trx_popup_tgmpa_required_plugins( $list = array() ) {
		if ( equadio_storage_isset( 'required_plugins', 'trx_popup' ) && equadio_storage_get_array( 'required_plugins', 'trx_popup', 'install' ) !== false && equadio_is_theme_activated() ) {
			$path = equadio_get_plugin_source_path( 'plugins/trx_popup/trx_popup.zip' );
			if ( ! empty( $path ) || equadio_get_theme_setting( 'tgmpa_upload' ) ) {
				$list[] = array(
					'name'     => equadio_storage_get_array( 'required_plugins', 'trx_popup', 'title' ),
					'slug'     => 'trx_popup',
					'source'   => ! empty( $path ) ? $path : 'upload://trx_popup.zip',
					'required' => false,
				);
			}
		}
		return $list;
	}
}

// Check if plugin installed and activated
if ( ! function_exists( 'equadio_exists_trx_popup' ) ) {
	function equadio_exists_trx_popup() {
		return defined( 'TRX_POPUP_URL' );
	}
}
