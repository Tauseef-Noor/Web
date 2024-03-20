/**
 * Shortcode AI Chat
 *
 * @package ThemeREX Addons
 * @since v2.22.0
 */

/* global jQuery, TRX_ADDONS_STORAGE */


jQuery( document ).ready( function() {

	"use strict";

	var $window             = jQuery( window ),
		$document           = jQuery( document ),
		$body               = jQuery( 'body' );

	$document.on( 'action.init_hidden_elements', function(e, container) {

		if ( container === undefined ) {
			container = $body;
		}

		// Init AI Chat
		container.find( '.sc_chat:not(.sc_chat_inited)' ).each( function() {

			var $sc = jQuery( this ).addClass( 'sc_chat_inited' ),
				sc_id = $sc.attr('id') || '',
				$form = $sc.find( '.sc_chat_form' ),
				$prompt = $sc.find( '.sc_chat_form_field_prompt_text' ),
				$button = $sc.find( '.sc_chat_form_field_prompt_button' ),
				$result = $sc.find( '.sc_chat_list' ),
				$start_new = $sc.find( '.sc_chat_form_start_new' ),
				chat = [],
				chat_position = 0;

			// Open/Close chat in the popup
			if ( $sc.hasClass( 'sc_chat_popup' ) ) {
				var $popup_content = $sc.find( '.sc_chat_content' ),
					$popup_button = $sc.find( '.sc_chat_popup_button' );
				$popup_button.on( 'click', function(e) {
					e.preventDefault();
					$sc.addClass( 'sc_chat_opening' );
					$popup_content.slideToggle( function() {
						$sc.removeClass( 'sc_chat_opening' );
						$sc.toggleClass( 'sc_chat_opened' );
						if ( $sc.hasClass( 'sc_chat_opened' ) ) {
							if ( $popup_button.data( 'chat-opened-svg' ) ) {
								$popup_button.find( '.sc_chat_popup_button_svg' ).html( $popup_button.data( 'chat-opened-svg' ) );
							} else if ( $popup_button.data( 'chat-opened-image' ) ) {
								$popup_button.find( '.sc_chat_popup_button_image' ).attr( 'src', $popup_button.data( 'chat-opened-image' ) );
							} else if ( $popup_button.data( 'chat-opened-icon' ) ) {
								$popup_button.find( '.sc_chat_popup_button_icon' ).removeClass( $popup_button.data( 'chat-icon' ) ).addClass( $popup_button.data( 'chat-opened-icon' ) );
							}
							$document.trigger( 'action.sc_chat_popup_opened', [ $popup_content ] );
						} else {
							if ( $popup_button.data( 'chat-svg' ) ) {
								$popup_button.find( '.sc_chat_popup_button_svg' ).html( $popup_button.data( 'chat-svg' ) );
							} else if ( $popup_button.data( 'chat-image' ) ) {
								$popup_button.find( '.sc_chat_popup_button_image' ).attr( 'src', $popup_button.data( 'chat-image' ) );
							} else if ( $popup_button.data( 'chat-icon' ) ) {
								$popup_button.find( '.sc_chat_popup_button_icon' ).removeClass( $popup_button.data( 'chat-opened-icon' ) ).addClass( $popup_button.data( 'chat-icon' ) );
							}
							$document.trigger( 'action.sc_chat_popup_closed', [ $popup_content ] );
						}
					} );
					return false;
				} );
				// Open on load
				if ( $sc.hasClass( 'sc_chat_open_on_load' ) ) {
					$popup_button.trigger( 'click' );
				}
			}

			// Enable/disable button "Generate"
			$prompt.on( 'change keyup', function(e) {
				$button.toggleClass( 'sc_chat_form_field_prompt_button_disabled', $prompt.val() == '' );
			} )
			.trigger( 'change' );

			// Set padding for the prompt field to avoid overlapping the button
			if ( $button.css( 'position' ) == 'absolute' ) {
				var set_prompt_padding = function() {
					var button_size = Math.ceil( $button.outerWidth() ) + 10;
					if ( button_size > 0 ) {
						$prompt.css( 'padding-right', button_size + 'px' );
					}
				};
				set_prompt_padding();
				$window.on( 'resize', set_prompt_padding );
				$document.on( 'action.sc_chat_popup_opened', set_prompt_padding );
			}
				
			// Close a message popup on click on the close button
			$sc.on( 'click', '.sc_chat_message_close', function(e) {
				e.preventDefault();
				$form.find( '.sc_chat_message' ).slideUp();
				return false;
			} );

			// Set a new list of messages
			$sc.on( 'trx_addons_action_sc_chat_update', function(e, messages) {
				if ( ! messages || ! messages.length ) {
					return;
				}
				// Clear previous chat
				chat = [];
				chat_position = 0;
				$result.empty();
				$form.data( 'chat-thread-id', '' );
				// Hide the link "Start new chat"
				$start_new.removeClass( 'trx_addons_hidden' );
				// Hide the message popup
				$form.find( '.sc_chat_message' ).slideUp();
				// Add new messages to the chat
				for ( var i = 0; i < messages.length; i++ ) {
					add_to_chat( messages[ i ].content, messages[ i ].role );
				}
				// Clear the prompt field and set focus to it
				$prompt.val( '' ).trigger( 'change' );
				$prompt.get(0).focus();
			} );

			// Start a new chat
			$start_new.on( 'click', function(e) {
				e.preventDefault();
				// Clear previous chat
				chat = [];
				chat_position = 0;
				$result.empty();
				$form.data( 'chat-thread-id', '' );
				trx_addons_do_action( 'trx_addons_action_sc_chat_updated', chat, $sc );
				// Hide the link "Start new chat"
				$start_new.addClass( 'trx_addons_hidden' );
				// Hide the message popup
				$form.find( '.sc_chat_message' ).slideUp();
				// Clear the prompt field and set focus to it
				$prompt.val( '' ).trigger( 'change' );
				$prompt.get(0).focus();
				return false;
			} );

			// Change the prompt text on click on the tag
			$sc.on( 'click', '.sc_chat_form_field_tags_item', function(e) {
				e.preventDefault();
				var $self = jQuery( this );
				if ( ! $prompt.attr( 'disabled' ) ) {
					$prompt.val( $self.data( 'tag-prompt' ) ).trigger( 'change' ).get(0).focus();
				}
				return false;
			} );

			// Show previous/next message
			$prompt.on( 'keydown', function(e) {
				var i;
				if ( e.keyCode == 38 ) {
					e.preventDefault();
					if ( chat_position > 0 ) {
						for ( i = chat_position - 1; i >= 0; i-- ) {
							if ( chat[i].role == 'user' ) {
								$prompt.val( chat[i].content ).trigger( 'change' );
								chat_position = i;
								break;
							}
						}
					}
				} else if ( e.keyCode == 40 ) {
					e.preventDefault();
					if ( chat_position < chat.length - 1 ) {
						for ( i = chat_position + 1; i <= chat.length; i++ ) {
							if ( i == chat.length ) {
								$prompt.val( '' ).trigger( 'change' );
								chat_position = i;
								break;
							} else if ( chat[i].role == 'user' ) {
								$prompt.val( chat[i].content ).trigger( 'change' );
								chat_position = i;
								break;
							}
						}
					}
				}
			} );

			// Generate answer
			$prompt.on( 'keypress', function(e) {
				if ( e.keyCode == 13 ) {
					e.preventDefault();
					$button.trigger( 'click' );
				}
			} );

			$button.on( 'click', function(e) {
				e.preventDefault();
				var prompt = $prompt.val(),
					settings = $form.data( 'chat-settings' );

				if ( ! prompt || ! checkLimits() ) {
					return;
				}

				// Add prompt to the chat
				add_to_chat( prompt, 'user' );

				// Display loading animation
				show_loading();

				// Display the link "Start new chat"
				$start_new.removeClass( 'trx_addons_hidden' );

				// Send request via AJAX
				jQuery.post( TRX_ADDONS_STORAGE['ajax_url'], {
					nonce: TRX_ADDONS_STORAGE['ajax_nonce'],
					action: 'trx_addons_ai_helper_chat',
					count: ( trx_addons_get_cookie( 'trx_addons_ai_helper_chat_count' ) || 0 ) * 1 + 1,
					chat: JSON.stringify( chat ),
					settings: settings,
					thread_id: $form.data( 'chat-thread-id' ) || '',
				}, show_answer );
			} );

			// Fetch answer
			function fetch_answer( data ) {
				jQuery.post( TRX_ADDONS_STORAGE['ajax_url'], {
					nonce: TRX_ADDONS_STORAGE['ajax_nonce'],
					action: 'trx_addons_ai_helper_chat_fetch',
					thread_id: data.thread_id,
					run_id: data.run_id,
					settings: $form.data( 'chat-settings' )
				}, show_answer );
			}

			// Show answer
			function show_answer( response ) {
				// Prepare response
				var rez = {};
				if ( response == '' || response == 0 ) {
					rez = { error: TRX_ADDONS_STORAGE['msg_ai_helper_error'] };
				} else if ( typeof response == 'string' ) {
					try {
						rez = JSON.parse( response );
					} catch (e) {
						rez = { error: TRX_ADDONS_STORAGE['msg_ai_helper_error'] };
						console.log( response );
					}
				} else {
					rez = response;
				}

				// Save thread ID
				if ( rez.thread_id ) {
					$form.data( 'chat-thread-id', rez.thread_id );
				}
				// If queued - fetch answer again
				if ( rez.finish_reason == 'queued' ) {
					var time = rez.fetch_time ? rez.fetch_time : 2000;
					setTimeout( function() {
						fetch_answer( rez );
					}, time );
				} else {
					// Hide loading animation
					hide_loading();
					// Set focus to the prompt field
					$prompt.get(0).focus();
					// Show result
					if ( ! rez.error && rez.data.text ) {
						add_to_chat( rez.data.text, 'assistant' );
						$prompt.val( '' ).trigger( 'change' );
						updateLimitsCounter();
						updateRequestsCounter();
						trx_addons_do_action( 'trx_addons_action_sc_chat_updated', chat, $sc );
					}
					if ( rez.error ) {
						showMessage( rez.error, 'error' );
					} else if ( rez.data.message ) {
						showMessage( rez.data.message, 'info' );
					}
				}
			}

			// Return a layout of the chat list item
			function get_chat_list_item( message, role ) {
				var dt = new Date(),
					hours = dt.getHours(),
					minutes = dt.getMinutes() < 10 ? '0' + dt.getMinutes() : dt.getMinutes(),
					am = hours < 12 ? 'AM' : 'PM',
					use_am = trx_addons_apply_filters( 'trx_addons_filter_sc_chat_time_use_am', true ),
					hours = use_am && hours > 12 ? hours - 12 : hours;
				var style = $form.data( 'chat-style' );
				var id = 'sc_chat_list_item_' + ( '' + Math.random() ).replace( '.', '' ),
					name = [ 'assistant', 'loading' ].indexOf( role ) >= 0 && style.assistant_name
								? style.assistant_name
								: ( role == 'user' && style.user_name
									? style.user_name
									: ''
									),
					icon = [ 'assistant', 'loading' ].indexOf( role ) >= 0 && style.assistant_icon
								? style.assistant_icon
								: ( role == 'user' && style.user_icon
									? style.user_icon
									: ''
									),
					image = [ 'assistant', 'loading' ].indexOf( role ) >= 0 && style.assistant_image
								? style.assistant_image
								: ( role == 'user' && style.user_image
									? style.user_image
									: ''
									),
					image_type = image ? trx_addons_get_file_ext( image ) : '',
					image_svg  = image_type == 'svg'
									? ( [ 'assistant', 'loading' ].indexOf( role ) >= 0 && style.assistant_svg
										? style.assistant_svg
										: ( role == 'user' && style.user_svg
											? style.user_svg
											: trx_addons_get_inline_svg( image, {
													render: function( html ) {
														if ( html ) {
															jQuery( '#' + id + ' .sc_chat_list_item_svg' ).html( html );
														}
													}
												} )
											)
										)
									: '';
				return trx_addons_apply_filters(
					'trx_addons_filter_sc_chat_list_item',
					'<li id="' + id + '" class="sc_chat_list_item sc_chat_list_item_' + role
						+ ( role == 'loading' ? ' sc_chat_list_item_assistant' : '' )
						+ ( image || icon ? ' sc_chat_list_item_with_avatar' : ' sc_chat_list_item_without_avatar' )
					+ '">'
						+ ( image || icon
							? '<span class="sc_chat_list_item_avatar">'
								+ ( image
									? ( image_type == 'svg'
										? '<span class="sc_chat_list_item_svg">' + image_svg + '</span>'
										: '<img src="' + image + '" alt="' + name + '" class="sc_chat_list_item_image">'
										)
									: '<span class="sc_chat_list_item_icon ' + icon + '"></span>'
									)
								+ '</span>'
							: ''
							)
						+ '<span class="sc_chat_list_item_wrap">'
							+ '<span class="sc_chat_list_item_content">' + message + '</span>'
							+ ( role != 'loading' ? '<span class="sc_chat_list_item_time">' + hours + ':' + minutes + ( use_am ? ' ' + am : '' ) + '</span>' : '' )
						+ '</span>'
					+ '</li>',
					message, role
				);
			}

			// Add a new message to the chat and display it
			function add_to_chat( message, role ) {
				// Add to the list of messages
				if ( chat.length === 0 || chat[chat.length-1].role != role || chat[chat.length-1].content != message ) {
					chat.push( {
						'role': role,
						'content': message
					} );
					chat_position = chat.length;
					// Display message
					$result.append( get_chat_list_item( message, role ) );
					// Display chat if it's hidden
					if ( chat.length == 1 ) {
						$result.parent().slideDown( function() {
							scroll_to_bottom();
						});
					} else {
						scroll_to_bottom();
					}
				}
			}

			// Show loading animation
			function show_loading() {
				$form.addClass( 'sc_chat_form_loading' );
				// Add loading animation to the chat
				$result.append( get_chat_list_item( '<span class="sc_chat_list_item_loading_dot"></span><span class="sc_chat_list_item_loading_dot"></span><span class="sc_chat_list_item_loading_dot"></span>', 'loading' ) );
				// Scroll chat to the bottom
				scroll_to_bottom();
			}

			// Hide loading animation
			function hide_loading() {
				$form.removeClass( 'sc_chat_form_loading' );
				$result.find( '.sc_chat_list_item_loading' ).remove();
			}

			// Scroll the chat to the bottom
			function scroll_to_bottom() {
				$result.parent().animate( { scrollTop: $result.parent().prop( 'scrollHeight' ) }, 500 );
			}

			// Show message
			function showMessage( msg, type ) {
				$form
					.find( '.sc_chat_message_inner' )
						.html( msg )
						.parent()
							.toggleClass( 'sc_chat_message_type_error', type == 'error' )
							.toggleClass( 'sc_chat_message_type_info', type == 'info' )
							.toggleClass( 'sc_chat_message_type_success', type == 'success' )
							.addClass( 'sc_chat_message_show' )
							.slideDown();
			}

			// Check limits for generation images
			function checkLimits() {
				// Block the button if the limits are exceeded
				var total, used;
				// Check limits for the generation requests from all users
				var $limit_total = $form.find( '.sc_chat_limits_total_value' ),
					$limit_used  = $form.find( '.sc_chat_limits_used_value' );
				if ( $limit_total.length && $limit_used.length ) {
					total = parseInt( $limit_total.text(), 10 );
					used  = parseInt( $limit_used.text(), 10 );
					if ( ! isNaN( total ) && ! isNaN( used ) ) {
						if ( used >= total ) {
							$button.toggleClass( 'sc_chat_form_field_prompt_button_disabled', true );
							$prompt.attr( 'disabled', 'disabled' );
							showMessage( $form.data( 'chat-limit-exceed' ), 'error' );
							return false;
						}
					}
				}
				// Check limits for the generation requests from the current user
				var $requests_total = $form.find( '.sc_chat_limits_total_requests' ),
					$requests_used  = $form.find( '.sc_chat_limits_used_requests' );
				if ( $requests_total.length && $requests_used.length ) {
					total = parseInt( $requests_total.text(), 10 );
					//used  = parseInt( $requests_used.text(), 10 );
					used = ( trx_addons_get_cookie( 'trx_addons_ai_helper_chat_count' ) || 0 ) * 1;
					if ( ! isNaN( total ) && ! isNaN( used ) ) {
						if ( used >= total ) {
							$button.toggleClass( 'sc_chat_form_field_prompt_button_disabled', true );
							$prompt.attr( 'disabled', 'disabled' );
							showMessage( $form.data( 'chat-limit-exceed' ), 'error' );
							return false;
						}
					}
				}
				return true;
			}
			
			// Update a counter of requests inside a limits text
			function updateLimitsCounter() {
				var total, used;
				// Update a counter of the total requests
				var $limit_total = $form.find( '.sc_chat_limits_total_value' ),
					$limit_used  = $form.find( '.sc_chat_limits_used_value' );
				if ( $limit_total.length && $limit_used.length ) {
					total = parseInt( $limit_total.text(), 10 );
					used  = parseInt( $limit_used.text(), 10 );
					if ( ! isNaN( total ) && ! isNaN( used ) ) {
						if ( used < total ) {
							used = Math.min( used + 1, total );
							$limit_used.text( used );
						}
					}
				}
				// Update a counter of the user requests
				var $requests_total = $form.find( '.sc_chat_limits_total_requests' ),
					$requests_used  = $form.find( '.sc_chat_limits_used_requests' );
				if ( $requests_total.length && $requests_used.length ) {
					total = parseInt( $requests_total.text(), 10 );
					// used  = parseInt( $requests_used.text(), 10 );
					used = ( trx_addons_get_cookie( 'trx_addons_ai_helper_chat_count' ) || 0 ) * 1;
					if ( ! isNaN( total ) && ! isNaN( used ) ) {
						if ( used < total ) {
							used = Math.min( used + 1, total );
							$requests_used.text( used );
						}
					}
				}
			}

			// Update a counter of the generation requests
			function updateRequestsCounter() {
				// Save a number of requests to the client storage
				var count = trx_addons_get_cookie( 'trx_addons_ai_helper_chat_count' ) || 0,
					limit = 60 * 60 * 1000 * 1,	// 1 hour
					expired = limit - ( new Date().getTime() % limit );

				trx_addons_set_cookie( 'trx_addons_ai_helper_chat_count', ++count, expired );
			}
			
			// Load/Save a chat messages to the client storage
			if ( $form.data( 'chat-save-history' ) ) {

				// Load a chat messages from the client storage
				var loadChatStorage = function() {
					// Load saved chats from the client storage
					var storage = trx_addons_get_storage( 'trx_addons_sc_chats' );
					if ( storage && storage.charAt(0) == '{' ) {
						storage = JSON.parse( storage );
					} else {
						storage = {};
					}
					// Remove old chats (more than 1 day)
					for ( var id in storage ) {
						if ( storage[id].date < ( new Date().getTime() - 24 * 60 * 60 * 1000 ) ) {
							delete storage[id];
						}
					}
					return storage;
				};

				// Save a chat messages to the client storage
				var saveChatStorage = function( storage ) {
					trx_addons_set_storage( 'trx_addons_sc_chats', JSON.stringify( storage ) );
				};

				// Update a chat messages in the client storage
				var updateChatStorage = function( chat, $sc ) {
					if ( ! trx_addons_is_local_storage_exists() ) {
						return;
					}
					// Get saved chats from the client storage
					var storage = loadChatStorage();
					// Add new chat to the storage
					if ( sc_id ) {
						if ( ! chat || chat.length == 0 ) {
							delete storage[sc_id];
						} else {
							storage[sc_id] = {
								date: new Date().getTime(),
								chat: chat,
								thread_id: $form.data( 'chat-thread-id' ) || ''
							};
						}
					}
					// Save storage to the client storage
					saveChatStorage( storage );
				};

				// Restore a chat messages from the client storage on page load
				var storage = loadChatStorage();
				if ( sc_id && storage[ sc_id ] ) {
					$sc.trigger( 'trx_addons_action_sc_chat_update', [ storage[ sc_id ].chat ] );
					// Thread ID should be restored after the chat messages are updated because the thread ID is set to empty value while this action
					$form.data( 'chat-thread-id', storage[sc_id].thread_id );
				}

				// Save a chat messages to the client storage after the chat messages are updated
				$sc.on( 'trx_addons_action_sc_chat_update', function( e, messages ) {
					updateChatStorage( messages );
				 } );
				trx_addons_add_action( 'trx_addons_action_sc_chat_updated', updateChatStorage );
			}

		} );

	} );

} );