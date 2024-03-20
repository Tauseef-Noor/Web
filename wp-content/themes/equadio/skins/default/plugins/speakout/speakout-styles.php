<?php

// Add plugin-specific colors and fonts to the custom CSS
if ( ! function_exists( 'equadio_speakout_get_css' ) ) {
    add_filter( 'equadio_filter_get_css', 'equadio_speakout_get_css', 10, 2 );
    function equadio_speakout_get_css( $css, $args ) {


        if ( isset( $css['fonts'] ) && isset( $args['fonts'] ) ) {
            $fonts         = $args['fonts'];
            $fonts['p_font-family!'] = str_replace(';', ' !important;', $fonts['p_font-family']);
            $fonts['p_font-size!'] = str_replace(';', ' !important;', $fonts['p_font-size']);
            $fonts['p_line-height!'] = str_replace(';', ' !important;', $fonts['p_line-height']);
            $fonts['p_font-weight!'] = str_replace(';', ' !important;', $fonts['p_font-weight']);
            $fonts['input_font-family!'] = str_replace(';', ' !important;', $fonts['input_font-family']);
            $fonts['input_font-size!'] = str_replace(';', ' !important;', $fonts['input_font-size']);
            $fonts['input_font-weight!'] = str_replace(';', ' !important;', $fonts['input_font-weight']);
            $fonts['input_font-style!'] = str_replace(';', ' !important;', $fonts['input_font-style']);
            $fonts['input_line-height!'] = str_replace(';', ' !important;', $fonts['input_line-height']);
            $css['fonts'] .= <<<CSS
   
   
#dk-speakout-reader,
.dk-speakout-petition-wrap,
#dk-speakout-reader-content {
    {$fonts['p_font-family!']}
    {$fonts['p_font-size!']}
    {$fonts['p_line-height!']}
    {$fonts['p_font-weight!']}
}
.dk-speakout-petition-wrap h3{
    {$fonts['h5_font-family']}
	{$fonts['h5_font-size']}
	{$fonts['h5_font-weight']}
	{$fonts['h5_font-style']}
	{$fonts['h5_line-height']}
	{$fonts['h5_text-decoration']}
	{$fonts['h5_text-transform']}
	{$fonts['h5_letter-spacing']}
}

.dk-speakout-petition-wrap .dk-speakout-readme,
.dk-speakout-petition-wrap .dk-speakout-petition .dk-speakout-submit,
.dk-speakout-petition-wrap .dk-speakout-share a.dk-speakout-twitter, 
.dk-speakout-petition-wrap .dk-speakout-share a.dk-speakout-facebook {
	{$fonts['button_font-family']}
	{$fonts['button_font-size']}
	{$fonts['button_font-weight']}
	{$fonts['button_font-style']}
	{$fonts['button_line-height']}
	{$fonts['button_text-decoration']}
	{$fonts['button_text-transform']}
	{$fonts['button_letter-spacing']}
}

.dk-speakout-petition-wrap .dk-speakout-petition input[type="text"],
.dk-speakout-petition-wrap .dk-speakout-petition input[type="email"],
.dk-speakout-petition-wrap .dk-speakout-petition select {
	{$fonts['input_font-family!']}
	{$fonts['input_font-size!']}
	{$fonts['input_font-weight!']}
	{$fonts['input_font-style!']}
	{$fonts['input_line-height!']}
	{$fonts['input_text-decoration']}
	{$fonts['input_text-transform']}
	{$fonts['input_letter-spacing']}
}  

.dk-speakout-petition-wrap .dk-speakout-share div > p:first-child  {
    {$fonts['h6_font-family']}
    {$fonts['h6_font-size']}
	{$fonts['h6_font-weight']}
	{$fonts['h6_font-style']}
	{$fonts['h6_line-height']}
	{$fonts['h6_text-decoration']}
	{$fonts['h6_text-transform']}
	{$fonts['h6_letter-spacing']}
}
  
.dk-speakout-signaturelist caption{
	{$fonts['h1_font-family']}
	{$fonts['h1_font-size']}
	{$fonts['h1_font-weight']}
	{$fonts['h1_font-style']}
	{$fonts['h1_line-height']}
	{$fonts['h1_text-decoration']}
	{$fonts['h1_text-transform']}
	{$fonts['h1_letter-spacing']}
	{$fonts['h1_margin-top']}
	{$fonts['h1_margin-bottom']}
}

.dk-speakout-signaturelist-date{
    {$fonts['info_font-family']}
}
#dk-speakout-reader-content .dk-speakout-greeting,
.dk-speakout-share div p {
    {$fonts['h5_font-family']}
}


CSS;
        }

        return $css;
    }
}
