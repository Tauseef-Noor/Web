<?php
/**
 * Skin Demo importer
 *
 * @package EQUADIO
 * @since EQUADIO 1.76.0
 */


// Theme storage
//-------------------------------------------------------------------------

equadio_storage_set( 'theme_demo_url', '//equadio.themerex.net' );


//------------------------------------------------------------------------
// One-click import support
//------------------------------------------------------------------------

// Set theme specific importer options
if ( ! function_exists( 'equadio_skin_importer_set_options' ) ) {
	add_filter( 'trx_addons_filter_importer_options', 'equadio_skin_importer_set_options', 9 );
	function equadio_skin_importer_set_options( $options = array() ) {
		if ( is_array( $options ) ) {
			$options['files']['default']['title']       = esc_html__( 'Equadio Demo', 'equadio' );
			$options['files']['default']['domain_dev']  = ''; // Developers domain
			$options['files']['default']['domain_demo'] = equadio_storage_get( 'theme_demo_url' ); // Demo-site domain
			if ( substr( $options['files']['default']['domain_demo'], 0, 2 ) === '//' ) {
				$options['files']['default']['domain_demo'] = equadio_get_protocol() . ':' . $options['files']['default']['domain_demo'];
			}
		}
		return $options;
	}
}


//------------------------------------------------------------------------
// OCDI support
//------------------------------------------------------------------------

// Set theme specific OCDI options
if ( ! function_exists( 'equadio_skin_ocdi_set_options' ) ) {
	add_filter( 'trx_addons_filter_ocdi_options', 'equadio_skin_ocdi_set_options', 9 );
	function equadio_skin_ocdi_set_options( $options = array() ) {
		if ( is_array( $options ) ) {
			// Demo-site domain
			$options['files']['ocdi']['title']       = esc_html__( 'Equadio OCDI Demo', 'equadio' );
			$options['files']['ocdi']['domain_demo'] = equadio_storage_get( 'theme_demo_url' );
			if ( substr( $options['files']['ocdi']['domain_demo'], 0, 2 ) === '//' ) {
				$options['files']['ocdi']['domain_demo'] = equadio_get_protocol() . ':' . $options['files']['ocdi']['domain_demo'];
			}
			// If theme need more demo - just copy 'default' and change required parameters
		}
		return $options;
	}
}
