<?php
// Add plugin-specific vars to the custom CSS
if ( ! function_exists( 'equadio_elm_add_theme_vars' ) ) {
	add_filter( 'equadio_filter_add_theme_vars', 'equadio_elm_add_theme_vars', 10, 2 );
	function equadio_elm_add_theme_vars( $rez, $vars ) {
		$gaps = array(
					'nogap'    =>  0,
					'narrow'   => 10,
					'default'  => 20,
					'extended' => 30,
					'wide'     => 40,
					'wider'    => 60
					);
		foreach ( $gaps as $n => $m ) {
			if ( substr( $vars['page'], 0, 2 ) != '{{' ) {
				$rez[ "page{$m}" ]     = ( $vars['page'] + $m ) . 'px';
				$rez[ "content{$m}" ]  = ( $vars['page'] - $vars['sidebar_gap'] - $vars['sidebar'] + $m ) . 'px';
				$rez[ "elm_gap_{$n}" ] = "{$m}px";
			} else {
				$rez[ "page{$m}" ]     = "{{ data.page{$m} }}";
				$rez[ "content{$m}" ]  = "{{ data.content{$m} }}";
				$rez[ "elm_gap_{$n}" ] = "{{ data.elm_gap_{$n} }}";
			}
		}
		$rez[ "elm_add_page_margins" ] = $rez[ "elm_gap_extended" ];
		return $rez;
	}
}

// Add plugin-specific fonts to the custom CSS
if ( ! function_exists( 'equadio_elm_get_css' ) ) {
    add_filter( 'equadio_filter_get_css', 'equadio_elm_get_css', 10, 2 );
    function equadio_elm_get_css( $css, $args ) {
        if ( isset( $css['fonts'] ) && isset( $args['fonts'] ) ) {
            $fonts         = $args['fonts'];
            $css['fonts'] .= <<<CSS
            
		/* Counter  */
		.elementor-counter .elementor-counter-number,
        .elementor-counter .elementor-counter-number-prefix,
        .elementor-counter .elementor-counter-number-suffix {
            {$fonts['h2_font-family']}
        }

CSS;
        }
        return $css;
    }
}


// Add theme-specific CSS-animations
if ( ! function_exists( 'equadio_elm_add_theme_animations' ) ) {
	add_filter( 'elementor/controls/animations/additional_animations', 'equadio_elm_add_theme_animations' );
	function equadio_elm_add_theme_animations( $animations ) {
		$animations = array_merge(
						$animations,
						array(
							esc_html__( 'Theme Specific', 'equadio' ) => array(
																			'ta_under_strips' => esc_html__( 'Under the strips', 'equadio' ),
																			'ta_marker' => esc_html__( 'Decoration Title Animation', 'equadio' ),
																			)
							)
						);
		return $animations;
	}
}
