/* global jQuery:false */
/* global EQUADIO_STORAGE:false */
/* global TRX_ADDONS_STORAGE:false */

jQuery( document ).on(
    'action.ready_equadio', function() {
        "use strict";
    // Checkbox with "I agree..."
    if (jQuery('input[type="checkbox"][name="dk-speakout-privacypolicy"]:not(.inited)').length > 0) {
        jQuery('input[type="checkbox"][name="dk-speakout-privacypolicy"]:not(.inited)')
            .addClass('inited')
            .on('change', function(e) {
                if (jQuery(this).get(0).checked)
                    jQuery(this).parents('form').find('button,input[type="submit"]').removeAttr('disabled');
                else
                    jQuery(this).parents('form').find('button,input[type="submit"]').attr('disabled', 'disabled');
            }).trigger('change');
    }

    // Fix attribute 'for' empty space
    if (jQuery('label.dk-speakout-options').length > 0) {
        jQuery('label.dk-speakout-options').each(function () {
            var $self = jQuery(this),
                attr = $self.attr('for');
            if (attr !== undefined && attr.indexOf(' ') > 0) {
                $self.attr('for', attr.replace(' ', ''));
            }
        });
    }
    //Fix Remove empty tag
    if (jQuery('.dk-speakout-greeting').length > 0 && jQuery( '.dk-speakout-greeting' ).html() === '' ) {
         jQuery( '.dk-speakout-greeting' ).remove();
    }
    if ( jQuery('.dk-speakout-signaturelist caption').length > 0 && jQuery( '.dk-speakout-signaturelist caption' ).html() === '' ) {
        jQuery( '.dk-speakout-signaturelist caption' ).remove();
    }

});
