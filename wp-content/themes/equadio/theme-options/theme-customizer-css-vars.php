<?php
// Add theme-specific fonts, vars and colors to the custom CSS
if ( ! function_exists( 'equadio_add_css_vars' ) ) {
	add_filter( 'equadio_filter_get_css', 'equadio_add_css_vars', 1, 2 );
	function equadio_add_css_vars( $css, $args ) {

		if ( isset( $css['fonts'] ) && isset( $args['fonts'] ) ) {
			$fonts = $args['fonts'];
			if ( is_array( $fonts ) && count( $fonts ) > 0 ) {
				$tmp = ":root {\n";
				foreach( $fonts as $tag => $font ) {
					if ( is_array( $font ) ) {
						$tmp .= "--theme-font-{$tag}_font-family: " . ( ! empty( $font['font-family'] ) && ! equadio_is_inherit( $font['font-family'] )
																	? equadio_prepare_css_value( $font['font-family'] )
																	: 'inherit'
																) . ";\n"
								. "--theme-font-{$tag}_font-size: " . ( ! empty( $font['font-size'] ) && ! equadio_is_inherit( $font['font-size'] )
																	? equadio_prepare_css_value( $font['font-size'] )
																	: 'inherit'
																) . ";\n"
								. "--theme-font-{$tag}_line-height: " . ( ! empty( $font['line-height'] ) && ! equadio_is_inherit( $font['line-height'] )
																	? equadio_prepare_css_value( $font['line-height'] )
																	: 'inherit'
																) . ";\n"
								. "--theme-font-{$tag}_font-weight: " . ( ! empty( $font['font-weight'] ) && ! equadio_is_inherit( $font['font-weight'] )
																	? equadio_prepare_css_value( $font['font-weight'] )
																	: 'inherit'
																) . ";\n"
								. "--theme-font-{$tag}_font-style: " . ( ! empty( $font['font-style'] ) && ! equadio_is_inherit( $font['font-style'] )
																	? equadio_prepare_css_value( $font['font-style'] )
																	: 'inherit'
																) . ";\n"
								. "--theme-font-{$tag}_text-decoration: " . ( ! empty( $font['text-decoration'] ) && ! equadio_is_inherit( $font['text-decoration'] )
																	? equadio_prepare_css_value( $font['text-decoration'] )
																	: 'inherit'
																) . ";\n"
								. "--theme-font-{$tag}_text-transform: " . ( ! empty( $font['text-transform'] ) && ! equadio_is_inherit( $font['text-transform'] )
																	? equadio_prepare_css_value( $font['text-transform'] )
																	: 'inherit'
																) . ";\n"
								. "--theme-font-{$tag}_letter-spacing: " . ( ! empty( $font['letter-spacing'] ) && ! equadio_is_inherit( $font['letter-spacing'] )
																	? equadio_prepare_css_value( $font['letter-spacing'] )
																	: 'inherit'
																) . ";\n"
								. "--theme-font-{$tag}_margin-top: " . ( ! empty( $font['margin-top'] ) && ! equadio_is_inherit( $font['margin-top'] )
																	? equadio_prepare_css_value( $font['margin-top'] )
																	: 'inherit'
																) . ";\n"
								. "--theme-font-{$tag}_margin-bottom: " . ( ! empty( $font['margin-bottom'] ) && ! equadio_is_inherit( $font['margin-bottom'] )
																	? equadio_prepare_css_value( $font['margin-bottom'] )
																	: 'inherit'
																) . ";\n";
					}
				}
				$css['fonts'] = $tmp . "\n}\n" . $css['fonts'];
			}
		}

		if ( isset( $css['vars'] ) && isset( $args['vars'] ) ) {
			$vars = $args['vars'];
			if ( is_array( $vars ) && count( $vars ) > 0 ) {
				$tmp = ":root {\n";
				foreach ( $vars as $var => $value ) {
					$tmp .= "--theme-var-{$var}: " . ( empty( $value ) ? 0 : $value ) . ";\n";
				}
				$css['vars'] = $tmp . "\n}\n" . $css['vars'];
			}
		}

		if ( isset( $css['colors'] ) && isset( $args['colors'] ) ) {
			$colors = $args['colors'];
			if ( is_array( $colors ) && count( $colors ) > 0 ) {
				$tmp = ".scheme_{$args['scheme']}, body.scheme_{$args['scheme']} {\n";
				foreach ( $colors as $color => $value ) {
					$tmp .= "--theme-color-{$color}: {$value};\n";
				}
				$css['colors'] = $tmp . "\n}\n" . $css['colors'];
			}
		}

		return $css;
	}
}

