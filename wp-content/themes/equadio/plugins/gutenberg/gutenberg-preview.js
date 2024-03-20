/* global jQuery:false */
/* global EQUADIO_STORAGE:false */

jQuery(window).on('load',function () {
	"use strict";
	equadio_gutenberg_first_init();
	// Create the observer to reinit visual editor after switch from code editor to visual editor
	var equadio_observers = {};
	if (typeof window.MutationObserver !== 'undefined') {
		equadio_create_observer('check_visual_editor', jQuery('.block-editor').eq(0), function(mutationsList) {
			var gutenberg_editor = jQuery('.edit-post-visual-editor:not(.equadio_inited)').eq(0);
			if (gutenberg_editor.length > 0) equadio_gutenberg_first_init();
		});
	}

	function equadio_gutenberg_first_init() {
		var gutenberg_editor = jQuery( '.edit-post-visual-editor:not(.equadio_inited)' ).eq( 0 );

		if ( 0 == gutenberg_editor.length ) {
			return;
		}
		
		// Add color scheme to the wrapper (instead '.editor-block-list__layout')
		jQuery( '.block-editor-writing-flow' ).addClass( 'scheme_' + EQUADIO_STORAGE['color_scheme'] );
		gutenberg_editor.addClass( 'scheme_' + EQUADIO_STORAGE['color_scheme'] );
		
		// Decorate sidebar placeholder
		gutenberg_editor.addClass( 'sidebar_position_' + EQUADIO_STORAGE['sidebar_position'] );
		gutenberg_editor.addClass( EQUADIO_STORAGE['expand_content'] + '_content' );
		if ( EQUADIO_STORAGE['sidebar_position'] == 'left' ) {
			gutenberg_editor.prepend( '<div class="editor-post-sidebar-holder"></div>' );
		} else if ( EQUADIO_STORAGE['sidebar_position'] == 'right' ) {
			gutenberg_editor.append( '<div class="editor-post-sidebar-holder"></div>' );
		}

		gutenberg_editor.addClass('equadio_inited');
	}

	// Create mutations observer
	function equadio_create_observer(id, obj, callback) {
		if (typeof window.MutationObserver !== 'undefined' && obj.length > 0) {
			if (typeof equadio_observers[id] == 'undefined') {
				equadio_observers[id] = new MutationObserver(callback);
				equadio_observers[id].observe(obj.get(0), { attributes: false, childList: true, subtree: true });
			}
			return true;
		}
		return false;
	}
} );
