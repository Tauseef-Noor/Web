/* global jQuery:false */
/* global EQUADIO_STORAGE:false */

(function() {
	"use strict";

	jQuery( document ).on(

		'action.ready_equadio', function() {

            // Give Close
            var give_busy = false;
            jQuery('body').on('click', '.give-modal.mfp-wrap', function(e) {
                if ( ! give_busy && jQuery(e.target).parents('.mfp-content').length == 0 ) {
                    give_busy = true;
                    jQuery('.give-modal.mfp-wrap .mfp-close').trigger('click');
                    give_busy = false;
                    e.preventDefault();
                    return false;
                }
            });
		}
	);
})();
