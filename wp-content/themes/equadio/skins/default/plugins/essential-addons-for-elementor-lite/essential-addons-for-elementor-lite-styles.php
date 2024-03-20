<?php
// Add plugin-specific colors and fonts to the custom CSS
if ( ! function_exists( 'equadio_essential_addons_for_elementor_get_css' ) ) {
	add_filter( 'equadio_filter_get_css', 'equadio_essential_addons_for_elementor_get_css', 10, 2 );
	function equadio_essential_addons_for_elementor_get_css( $css, $args ) {

		if ( isset( $css['fonts'] ) && isset( $args['fonts'] ) ) {
			$fonts         = $args['fonts'];
			$css['fonts'] .= <<<CSS
			
		.eael-advance-tabs .eael-tabs-nav > ul li {
            {$fonts['h5_font-family']}
        }

CSS;
		}

		return $css;
	}
}

