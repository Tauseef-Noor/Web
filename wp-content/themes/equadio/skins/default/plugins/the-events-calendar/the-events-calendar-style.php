<?php
// Add plugin-specific colors and fonts to the custom CSS
if ( ! function_exists( 'equadio_tribe_events_get_css' ) ) {
	add_filter( 'equadio_filter_get_css', 'equadio_tribe_events_get_css', 10, 2 );
	function equadio_tribe_events_get_css( $css, $args ) {
		if ( isset( $css['fonts'] ) && isset( $args['fonts'] ) ) {
			$fonts         = $args['fonts'];
			$css['fonts'] .= <<<CSS
	
.tribe-common .tribe-common-h5,
.tribe-common .tribe-common-h6,
.tribe-common .tribe-common-h4,
.tribe-common .tribe-common-b3,
.tribe-common .tribe-common-h3,
.tribe-events .tribe-events-c-view-selector__list-item-text,
.single-tribe_events .tribe-events-meta-group .tribe-events-single-section-title,		
.tribe-events-list .tribe-events-list-event-title {
	{$fonts['h3_font-family']}
}


.tribe-common .tribe-common-form-control-text__input,
.tribe-common--breakpoint-medium.tribe-common .tribe-common-form-control-text__input {
	{$fonts['input_font-family']}
	{$fonts['input_font-size']}
	{$fonts['input_font-weight']}
	{$fonts['input_font-style']}
	{$fonts['input_line-height']}
	{$fonts['input_text-decoration']}
	{$fonts['input_text-transform']}
	{$fonts['input_letter-spacing']}
}

#tribe-events .tribe-events-button,
.tribe-events-button,
.tribe-events-cal-links a,
.tribe-events-sub-nav li a,
/* Tribe Events 5.0+ */
.tribe-events .tribe-events-c-nav__list .tribe-common-b2,
.tribe-common--breakpoint-medium.tribe-events .tribe-events-c-nav__next,
.tribe-common--breakpoint-medium.tribe-events .tribe-events-c-nav__prev,
.tribe-events .tribe-events-c-nav__list button,
.tribe-events .tribe-events-c-ical__link,
.tribe-common .tribe-common-c-btn-border,
.tribe-common a.tribe-common-c-btn-border,
.single-tribe_events a.tribe-events-ical,
.single-tribe_events a.tribe-events-ical:hover,
.single-tribe_events a.tribe-events-gcal,
.single-tribe_events a.tribe-events-gcal:hover, 
.tribe-common .tribe-common-c-btn,
.tribe-common a.tribe-common-c-btn {
	{$fonts['button_font-family']}
	{$fonts['button_font-size']}
	{$fonts['button_font-weight']}
	{$fonts['button_font-style']}
	{$fonts['button_line-height']}
	{$fonts['button_text-decoration']}
	{$fonts['button_text-transform']}
	{$fonts['button_letter-spacing']}
}
#tribe-bar-form button, #tribe-bar-form a,
.tribe-events-read-more {
	{$fonts['button_font-family']}
	{$fonts['button_letter-spacing']}
}
.tribe-events-list .tribe-events-list-separator-month,
.tribe-events-calendar thead th,
.tribe-events-schedule, .tribe-events-schedule h2 {
	{$fonts['h5_font-family']}
}
.tribe-events .tribe-events-calendar-list__event-date-tag-weekday,
.tribe-events .datepicker .day, .tribe-events .datepicker .dow,
.tribe-events .datepicker .month, .tribe-events .datepicker .year,
.tribe-events .datepicker .datepicker-switch,
.tribe-events .tribe-events-calendar-month__calendar-event-tooltip-datetime,
.tribe-common.tribe-common .tribe-common-b2,
.tribe-events .tribe-events-calendar-month__calendar-event-datetime,
.tribe-common .tribe-common-h7,
.tribe-common .tribe-common-h8,
#tribe-bar-form input, #tribe-events-content.tribe-events-month,
#tribe-events-content .tribe-events-calendar div[id*="tribe-events-event-"] h3.tribe-events-month-event-title,
#tribe-mobile-container .type-tribe_events,
.tribe-events-list-widget ol li .tribe-event-title {
	{$fonts['p_font-family']}
}
.tribe-events-loop .tribe-event-schedule-details,
.single-tribe_events #tribe-events-content .tribe-events-event-meta dt,
#tribe-mobile-container .type-tribe_events .tribe-event-date-start {
	{$fonts['info_font-family']};
}

CSS;
		}

		return $css;
	}
}

