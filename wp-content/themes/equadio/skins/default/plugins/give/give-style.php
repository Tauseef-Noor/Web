<?php
// Add plugin-specific colors and fonts to the custom CSS
if ( ! function_exists( 'equadio_give_get_css' ) ) {
	add_filter( 'equadio_filter_get_css', 'equadio_give_get_css', 10, 2 );
	function equadio_give_get_css( $css, $args ) {

		if ( isset( $css['fonts'] ) && isset( $args['fonts'] ) ) {
			$fonts         = $args['fonts'];
			$css['fonts'] .= <<<CSS
			
[id*="give-form"] .give-form-title {
	{$fonts['h4_font-family']}	
	{$fonts['h4_font-size']}
	{$fonts['h4_font-weight']}
	{$fonts['h4_font-style']}
	{$fonts['h4_line-height']}
	{$fonts['h4_text-decoration']}
	{$fonts['h4_text-transform']}
	{$fonts['h4_letter-spacing']}
	{$fonts['h4_margin-top']}
	{$fonts['h4_margin-bottom']}
}
			
form[id*="give-form"] .give-donation-amount .give-currency-symbol,
form[id*="give-form"] .give-donation-amount .give-currency-symbol.give-currency-position-before,
form[id*="give-form"] .give-donation-amount .give-currency-symbol.give-currency-position-after,
form[id*="give-form"] #give-final-total-wrap .give-donation-total-label,
#give-recurring-form .form-row input[type="email"],
#give-recurring-form .form-row input[type="password"],
#give-recurring-form .form-row input[type="tel"],
#give-recurring-form .form-row input[type="text"],
#give-recurring-form .form-row input[type="url"],
#give-recurring-form .form-row select,
#give-recurring-form .form-row textarea,
form.give-form .form-row input[type="email"],
form.give-form .form-row input[type="password"],
form.give-form .form-row input[type="tel"],
form.give-form .form-row input[type="text"],
form.give-form .form-row input[type="url"],
form.give-form .form-row select,
form.give-form .form-row textarea,
form[id*="give-form"] .form-row input[type="email"],
form[id*="give-form"] .form-row input[type="password"],
form[id*="give-form"] .form-row input[type="tel"],
form[id*="give-form"] .form-row input[type="text"],
form[id*="give-form"] .form-row input[type="url"],
form[id*="give-form"] .form-row select,
form[id*="give-form"] .form-row textarea,
form[id*="give-form"] .give-donation-amount #give-amount,
form[id*="give-form"] .give-donation-amount #give-amount-text,
form[id*="give-form"] #give-final-total-wrap .give-final-total-amount {
	{$fonts['input_font-family']}
	{$fonts['input_font-size']}
	{$fonts['input_font-weight']}
	{$fonts['input_font-style']}
	{$fonts['input_line-height']}
	{$fonts['input_text-decoration']}
	{$fonts['input_text-transform']}
	{$fonts['input_letter-spacing']}
}
.give-tooltip[class*="hint--"]:after {
	{$fonts['p_font-family']}
}


CSS;
		}

		return $css;
	}
}

