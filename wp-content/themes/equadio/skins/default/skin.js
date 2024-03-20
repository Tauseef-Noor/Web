/* global jQuery:false */
/* global EQUADIO_STORAGE:false */

(function() {
	"use strict";

	// Init skin-specific actions on first run
	// Attention! Don't forget to add the class "inited" and check it to prevent re-initialize the elements
	jQuery( document ).on(
		'action.ready_equadio', function() {

            // MailChimp - ID
            //------------------
            if (jQuery('.mc4wp-form-fields').length > 0) {
                var count_mc4wp = 1;
                jQuery('.mc4wp-form-fields').each(
                    function () {
                        jQuery(this).find('input[type="checkbox"]').each(
                            function () {
                                var id = jQuery(this).attr('id');
                                if (id) {
                                }
                                else {
                                    id = 'mc4wp';
                                }
                                jQuery(this).attr('id', id + '-' + count_mc4wp);
                                jQuery(this).next('label').attr('for', id + '-' + count_mc4wp);
                                count_mc4wp++;
                            });
                    });
            }

            // GDPR on click add class checked to label
            if (jQuery('#wpgdprc').length > 0) {
                var label = jQuery('.wpgdprc-checkbox label');
                if (jQuery('#wpgdprc').attr("checked") == 'checked') {
                    label.toggleClass('checked');
                }
                jQuery('#wpgdprc').on('click', function () {
                    if (jQuery('#wpgdprc').attr("checked") == 'checked') {
                        label.addClass('checked');
                    } else {
                        label.removeClass('checked');
                    }
                });
            }

            //Fix Events Calendar (Remove empty tag)
            if ( jQuery( '.tribe-events-view.tribe-events-view--day,.tribe-events-view.tribe-events-view--list,.tribe-events-view.tribe-events-view--month' ).length > 0 ) {
                var check_empty_day = function() {
                    setTimeout( function() {
                        if (jQuery('.tribe-events-calendar-day').length > 0 && jQuery('.tribe-events-calendar-day').html().trim() === '') {
                            jQuery('.tribe-events-calendar-day').remove();
                        }
                        jQuery('.tribe-events-view.tribe-events-view--day,.tribe-events-view.tribe-events-view--list,.tribe-events-view.tribe-events-view--month').on('beforeAjaxSuccess.tribeEvents', check_empty_day );
                    }, 1 );
                };
                check_empty_day();
            }
		}
	);

})();
