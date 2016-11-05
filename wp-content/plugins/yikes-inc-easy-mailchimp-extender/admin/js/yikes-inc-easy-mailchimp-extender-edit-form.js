window.Yikes_MailChimp_Edit_Form = window.Yikes_MailChimp_Edit_Form || {};
window.yikes_mailchimp_edit_form = window.yikes_mailchimp_edit_form || {};

(function( window, document, $, app, undefined ) {
	'use strict';

	app.l10n = window.yikes_mailchimp_edit_form || {};

	 $( document ).ready( function() {

		/* Initialize Sortable Container */
		/* Sortable Form Builder - re-arrange field order (edit-form.php) */
		$( 'body' ).find( '#form-builder-container' ).sortable({
			items: '.draggable:not(.non-draggable-yikes)',
			axis: 'y',
			placeholder: 'form-builder-placeholder',
			update: function( ) {
			  var i = 1;
			  jQuery( '#form-builder-container' ).find( '.draggable' ).each( function() {
					jQuery( this ).find( '.position-input' ).val( i );
					i++;
			  });
			}
		});

		/*
		* Remove a field from the form builder
		* re-enable it in the available fields list
		*/
		$( 'body' ).on( 'click' , '.remove-field' , function() {
			var merge_tag = jQuery( this ).attr( 'alt' );
			var clicked = jQuery( this );
			$( this ).parents( '.yikes-mc-settings-expansion-section' ).prev().find( '.dashicons' ).toggleClass( 'dashicons-minus' );
			$( this ).parents( '.yikes-mc-settings-expansion-section' ).slideToggle( 450 , function() {
				clicked.parents( '.draggable' ).find( '.expansion-section-title' ).css( 'background' , 'rgb(255, 134, 134)' );
				clicked.parents( '.draggable' ).fadeOut( 'slow' , function() {
					/* re-enable the field, to be added to the form */
					jQuery( '#available-fields' ).find( 'li[alt="'+merge_tag+'"]' ).removeClass( 'not-available' );
					jQuery( '#available-interest-groups' ).find( 'li[alt="'+merge_tag+'"]' ).removeClass( 'not-available' );
					/* remove the element from the DOM */
					jQuery( this ).remove();
					if( $( '#form-builder-container' ).find( '.draggable' ).length < 1 ) {
						$( '.clear-form-fields' ).hide();
						$( '.clear-form-fields' ).next().hide(); /* Update Form button next to clear form fields */
						$( '#form-builder-container' ).html( '<h4 class="no-fields-assigned-notice non-draggable-yikes"><em>'+app.l10n.no_fields_assigned+'</em></h4>' );
					}
				});
			});
			return false;
		});

		/*
		* Hide a field (click 'close')
		*/
		$( 'body' ).on( 'click' , '.hide-field' , function() {
			$( this ).parents( '.yikes-mc-settings-expansion-section' ).prev().find( '.dashicons' ).toggleClass( 'dashicons-minus' );
			$( this ).parents( '.yikes-mc-settings-expansion-section' ).slideToggle( 450 );
			return false;
		});

		/*
		* Send selected field to the form builder
		* and disable it from the available fields list
		*/
		$( 'body' ).on( 'click' , '.add-field-to-editor' , function() {

			$( '.field-to-add-to-form' ).each( function() {
				/* get the length, to decide if we should clear the html and append, or just append */
				var form_builder_length = $( '#form-builder-container' ).find( '.draggable' ).length;

				var field = $( this );
				var merge_tag = field.attr( 'alt' );

				/* temporarily disable all of the possible merge variables and interest groups (to prevent some weird stuff happening) */
				$( '#available-fields' ).children( 'li' ).removeClass( 'available-form-field' );
				var clicked_button = $( this );
				clicked_button.attr( 'disabled' , 'disabled' ).attr( 'onclick' , 'return false;' ).removeClass( 'add-field-to-editor' );

				/* build our data */
				var data = {
					'action' : 'add_field_to_form',
					'field_name' : field.attr( 'data-attr-field-name' ),
					'merge_tag' : merge_tag,
					'field_type' : field.attr( 'data-attr-field-type' ),
					'list_id' : field.attr( 'data-attr-form-id' ) /* grab the form ID to query the API for field data */
				};

				/* submit our ajax request */
				$.ajax({
					url: app.l10n.ajax_url,
					type:'POST',
					data: data,
					dataType: 'html',
					success : function( response, textStatus, jqXHR) {
						field.removeClass( 'field-to-add-to-form' ).addClass( 'not-available' );
						$( '.add-field-to-editor' ).hide();

						/* If the banner is visible, this means that there is no fields assigned to the form - clear it */
						if ( $( '.no-fields-assigned-notice' ).is( ':visible') ) {
							$( '#form-builder-container' ).html( '' );
						}

						/* Append our response, and display our buttons */
						$( '#form-builder-container' ).append( response );
						$( '.clear-form-fields' ).show(); /* Clear Form Fields */
						$( '.clear-form-fields' ).next().show(); /* Update Form button next to clear form fields */

						/* add a value to the position */
						$( '.field-'+merge_tag+'-position' ).val( parseInt( form_builder_length + 1 ) ); /* add one :) */
					},
					error : function( jqXHR, textStatus, errorThrown ) {
						alert( textStatus+jqXHR.status+jqXHR.responseText+"..." );
					},
					complete : function( jqXHR, textStatus ) {
						/* console.log( 'field successfully added to the form' ); */
						/* temporarily disable all of the possible merge variables and interest groups (to prevent some weird stuff happening) */
						$( '#available-fields' ).children( 'li' ).addClass( 'available-form-field' );
						clicked_button.removeAttr( 'disabled' ).removeAttr( 'onclick' );
						/* re-hide the add field to form builder button */
						$( '.add-field-to-editor' ).hide();
					}
				});
			});
			return false;
		}); /* end add field to form builder */

		/*
		* Send selected Interest group to our form
		* and disable it from the available interest groups list
		*/
		$( 'body' ).on( 'click' , '.add-interest-group-to-editor' , function() {
			/* get the length, to decide if we should clear the html and append, or just append */
			var form_builder_length = $( '#form-builder-container' ).find( '.draggable' ).length;

			var group_id = $( '.group-to-add-to-form' ).attr( 'alt' );

			/* temporarily disable all of the possible merge variables and interest groups (to prevent some weird stuff happening) */
			$( '#available-interest-groups' ).children( 'li' ).removeClass( 'available-interest-group' );

			var button = $( this );
			button.attr( 'disabled' , 'disabled' ).attr( 'onclick' , 'return false;' ).removeClass( 'add-interest-group-to-editor' );

			/* build our data */
			var data = {
				'action' : 'add_interest_group_to_form',
				'field_name' : $( '.group-to-add-to-form' ).attr( 'data-attr-field-name' ),
				'group_id' : group_id,
				'field_type' : $( '.group-to-add-to-form' ).attr( 'data-attr-field-type' ),
				'list_id' : $( '.group-to-add-to-form' ).attr( 'data-attr-form-id' ) /* grab the form ID to query the API for field data */
			};

			/* submit our ajax request */
			$.ajax({
				url: app.l10n.ajax_url,
				type:'POST',
				data: data,
				dataType: 'html',
				success : function( response, textStatus, jqXHR) {
					$( '.group-to-add-to-form' ).removeClass( 'group-to-add-to-form' ).addClass( 'not-available' );
					$( '.add-interest-group-to-editor' ).hide();
					if( form_builder_length < 1 ) {
						$( '#form-builder-container' ).html( '' ).append( response );
						$( '.clear-form-fields' ).show();
						$( '.clear-form-fields' ).next().show(); /* Update Form button next to clear form fields */
					} else {
						$( '#form-builder-container' ).append( response );
					}
				},
				error : function( jqXHR, textStatus, errorThrown ) {
					alert( textStatus+jqXHR.status+jqXHR.responseText+"..." );
				},
				complete : function( jqXHR, textStatus ) {
					/* console.log( 'interest group successfully added to the form..' ); */
					/* temporarily disable all of the possible merge variables and interest groups (to prevent some weird stuff happening) */
					$( '#available-interest-groups' ).children( 'li' ).addClass( 'available-interest-group' );
					button.removeAttr( 'disabled' ).removeAttr( 'onclick' ).addClass( 'add-interest-group-to-editor' );
					/* re-hide the add field to form builder button */
					$( '.add-interest-group-to-editor' ).hide();
				}
			});
			return false;
		}); /* end add field to form builder */


		/* initialize color pickers */
		$('.color-picker').each(function() {
			$( this ).wpColorPicker();
		}); /* end color picker initialization */

		/* Toggle settings hidden containers */
		$( 'body' ).on( 'click' , '.expansion-section-title' , function() {
			$( this ).next().stop().slideToggle();
			$( this ).find( '.dashicons' ).toggleClass( 'dashicons-minus' );
			return false;
		});

		/* Toggle Selected Class (Available Merge Vars) */
		$( 'body' ).on( 'click' , '.available-form-field' , function() {
			if( $( this ).hasClass( 'not-available' ) ) {
				return false;
			} else {
				if( $( this ).hasClass( 'field-to-add-to-form' ) ) {
					$( this ).removeClass( 'field-to-add-to-form' );
					$( '.add-field-to-editor' ).stop().fadeOut();
				} else {
					/* Remove the class that decides what icons will be added to our form */
					/* $( '.field-to-add-to-form' ).removeClass( 'field-to-add-to-form' ); */
					$( this ).toggleClass( 'field-to-add-to-form' );
					$( '.add-field-to-editor' ).stop().fadeIn();
				}
			}
		});

		/* Toggle Selected Class (Available Merge Vars) */
		$( 'body' ).on( 'click' , '.available-interest-group' , function() {
			if( $( this ).hasClass( 'not-available' ) ) {
				return false;
			} else {
				if( $( this ).hasClass( 'group-to-add-to-form' ) ) {
					$( this ).removeClass( 'group-to-add-to-form' );
					$( '.add-interest-group-to-editor' ).stop().fadeOut();
				} else {
					$( '.group-to-add-to-form' ).removeClass( 'group-to-add-to-form' );
					$( this ).toggleClass( 'group-to-add-to-form' );
					$( '.add-interest-group-to-editor' ).stop().fadeIn();
				}
			}
		});

		/* Toggle Additional Form Settings (customizer, builder, error messages) */
		$( 'body' ).on( 'click' , '.hidden_setting' , function() {
			$( '.hidden_setting' ).removeClass( 'selected_hidden_setting' );
			$( '.selected_setting_triangle' ).remove();
			$( this ).addClass( 'selected_hidden_setting' ).append( '<div class="selected_setting_triangle"></div>' );
			var container = $( this ).attr( 'data-attr-container' );
			$( '.hidden-setting-label' ).hide();
			$( '#'+container ).show();
		});

		/* Close the form when clickcing 'close' */
		$( 'body' ).on( 'click' , '.close-form-expansion' , function() {
			$( this ).parents( '.yikes-mc-settings-expansion-section' ).slideToggle().prev().find( '.dashicons' ).toggleClass( 'dashicons-minus' );
			return false;
		});

		/* Toggle between 'Merge Varialbe' & 'Interest Group' Tabs */
		$( 'body' ).on( 'click' , '.mv_ig_list .nav-tab' , function() {
			if( $( this ).hasClass( 'nav-tab-active' ) ) {
				return false;
			}
			if( $( this ).hasClass( 'nav-tab-disabled' ) ) {
				return false;
			}
			$( '.mv_ig_list .nav-tab' ).removeClass( 'nav-tab-active' );
			$( '.arrow-down' ).remove();
			$( this ).addClass( 'nav-tab-active' ).prepend( '<div class="arrow-down"></div>' );
			$( '.mv_ig_list .nav-tab' ).addClass( 'nav-tab-disabled' );
			var clicked_tab = $( this ).attr( 'alt' );
			if( clicked_tab == 'merge-variables' ) {
				$( '#merge-variables-container' ).stop().animate({
					left: '0px'
				}, function() {
					$( '.mv_ig_list .nav-tab' ).removeClass( 'nav-tab-disabled' );
				});
				$( '#interest-groups-container' ).stop().animate({
					left: '+=268px'
				}, function() {
					$( '.mv_ig_list .nav-tab' ).removeClass( 'nav-tab-disabled' );
				});
			} else {
				$( '#merge-variables-container' ).stop().animate({
					left: '-=278px'
				}, function() {
					$( '.mv_ig_list .nav-tab' ).removeClass( 'nav-tab-disabled' );
				});
				$( '#interest-groups-container' ).stop().animate({
					left: '-=268px'
				}, function() {
					$( '.mv_ig_list .nav-tab' ).removeClass( 'nav-tab-disabled' );
				});
			}
			return false;
		});

		/*
		*	Clear all fields assigned to a form in bulk
		*	@since 6.0.2.2
		*/
		$( 'body' ).on( 'click', '.clear-form-fields', function() {
			if ( confirm( app.l10n.bulk_delete_alert ) ) {
				/* hide/remove the fields */
				$( '#form-builder' ).find( '.draggable' ).find( '.expansion-section-title' ).each( function() {
					$( this ).css( 'background' , 'rgb(255, 134, 134)' );
					var merge_tag = $( this ).parents( '.draggable' ).find( '.remove-field' ).attr( 'alt' );
					$( this ).fadeOut( 'slow', function() {
						/* re-enable the field, to be added to the form */
						$( '#available-fields' ).find( 'li[alt="'+merge_tag+'"]' ).removeClass( 'not-available' );
						$( '#available-interest-groups' ).find( 'li[alt="'+merge_tag+'"]' ).removeClass( 'not-available' );
						/* hide the button */
						$( this ).remove();
						$( '.clear-form-fields' ).hide(); /* Clear form fields button */
						$( '.clear-form-fields' ).next().hide(); /* Update Form button next to clear form fields */
						$( '.available-form-field' ).each( function()  {
							$( this ).removeClass( 'not-available' );
						});
						$( '#form-builder-container' ).html( '<h4 class="no-fields-assigned-notice non-draggable-yikes"><em>'+app.l10n.no_fields_assigned+'</em></h4>' );
					});
				});
			}
			return false;
		});

		/**
		*	Initialize our date pickers on init
		*	@since 6.0.3.8
		*/
		initialize_form_schedule_time_pickers();

	});




})( window, document, jQuery, Yikes_MailChimp_Edit_Form );


/* Toggle Page Slection for form submission redirection */
function togglePageRedirection( e ) {
	if( e.value == 1 ) {
		jQuery( '#redirect-user-to-selection-label' ).fadeIn();
	} else {
		jQuery( '#redirect-user-to-selection-label' ).fadeOut();
	}
}
/* Pass the clicked element for proper populating */
function storeGlobalClicked( e ) {
	/* get the input field name */
	var parent_name = e.parents( 'td' ).find( 'input' ).attr( 'name' );
	/* pass it to hidden thickbox field */
	jQuery( '.clicked-input' ).val( parent_name );
}
/* Populate the input field with the selected tag */
function populateDefaultValue( tag ) {
	/* store the value */
	var field = jQuery( '.clicked-input' ).val();
	/* clear input */
	jQuery( '.clicked-input' ).val( '' );
	/* remove thickbox */
	tb_remove();
	/* populate the field */
	jQuery( 'input[name="'+field+'"]' ).val( tag );
}

/**
*	toggle_nested_section()
*	- toggle the visibility of some additional options
*	@since 1.0
*/
function toggle_nested_section( clicked_option ) {
	var clicked_value = jQuery( clicked_option ).val();
	switch( clicked_value ) {

		case 'image':
			jQuery( '.submit-button-type-text' ).fadeOut( 'fast', function() {
				jQuery( '.submit-button-type-image' ).fadeIn( 'fast' );
			});
			break;

		case 'text':
			jQuery( '.submit-button-type-image' ).fadeOut( 'fast', function() {
				jQuery( '.submit-button-type-text' ).fadeIn( 'fast' );
			});
			break;

		default:
		case '1':
			/* Schedule toggle */
			if( jQuery( clicked_option ).attr( 'name' ) == 'yikes-easy-mc-form-schedule' ) {
				jQuery( '.date-restirction-section' ).fadeToggle();
			} else {
				/* login required toggle */
				jQuery( '.login-restirction-section' ).fadeToggle();
			}
			break;
	}
	return false;
}

/**
*	Initialize the date/time pickers on the scheduled section of the edit form page (form settings section)
*	@since 6.0.3.8
*/
function initialize_form_schedule_time_pickers() {
	/* Initialize the date pickers */
	jQuery( '.date-picker' ).datepicker({
		numberOfMonths: 1,
		showButtonPanel: true,
		closeText: window.yikes_mailchimp_edit_form.closeText,
		currentText: window.yikes_mailchimp_edit_form.currentText,
		monthNames: window.yikes_mailchimp_edit_form.monthNames,
		monthNamesShort: window.yikes_mailchimp_edit_form.monthNamesShort,
		dayNames: window.yikes_mailchimp_edit_form.dayNames,
		dayNamesShort: window.yikes_mailchimp_edit_form.dayNamesShort,
		dayNamesMin: window.yikes_mailchimp_edit_form.dayNamesMin,
		dateFormat: window.yikes_mailchimp_edit_form.dateFormat,
		firstDay: window.yikes_mailchimp_edit_form.firstDay,
		isRTL: window.yikes_mailchimp_edit_form.isRTL,
		onSelect: function( newDate, instance ) {
			var prevDate = instance.lastVal;
			var changed_object_id = instance.id;
			yikes_check_valid_date( newDate, prevDate, changed_object_id );
		},
	});
	/* initialize the time pickers */
	jQuery( '.time-picker' ).timepicker({
		scrollDefault: 'now',
		timeFormat: 'h:i A'
	});
	jQuery( '.time-picker' ).on( 'changeTime', function() {
		var changed_object_id = jQuery( this ).attr( 'id' );
		var newDate = jQuery( '#yikes-easy-mc-form-restriction-start-date' ).val();
		var prevDate = jQuery( '#yikes-easy-mc-form-restriction-end-date' ).val();
		yikes_check_valid_date( newDate, prevDate, changed_object_id );
	});
}

/**
*	Check if selected date is valid, and start date is before end date
*	@since 6.0.3.8
*/
function yikes_check_valid_date( new_date, previous_date, changed_object_id ) {
	var start_date = jQuery( '#yikes-easy-mc-form-restriction-start-date' ).val();
	var end_date = jQuery( '#yikes-easy-mc-form-restriction-end-date' ).val();
	var start_time = yikes_12_to_24_hour_time_conversion( jQuery( '#yikes-easy-mc-form-restriction-start-time' ).val() );
	var end_time = yikes_12_to_24_hour_time_conversion( jQuery( '#yikes-easy-mc-form-restriction-end-time' ).val() );

	var start_date_time = new Date( start_date + ' ' + start_time );
	var end_date_time = new Date( end_date + ' ' + end_time );

	/*
	*	if the start date & time are later than the end date time,
	* 	display an error and repopulate with previous value
	*/
	if( start_date_time > end_date_time ) {
		if( changed_object_id == 'yikes-easy-mc-form-restriction-start-date' || changed_object_id == 'yikes-easy-mc-form-restriction-end-date' ) {
			/* return to previous date */
			jQuery( '#' + changed_object_id ).val( previous_date );
		}
		/* if error is present, abort */
		if( jQuery( '.date-restirction-section' ).find( 'p.description.error' ).length ) {
			return;
		}
		/* display an error message */
		jQuery( '.date-restirction-section' ).first().find( 'p.description' ).after( '<p class="description error">' + window.yikes_mailchimp_edit_form.start_date_exceeds_end_date_error + '</p>' );
	} else {
		jQuery( '.date-restirction-section' ).find( 'p.description.error' ).remove();
	}
}

function yikes_12_to_24_hour_time_conversion( time ) {
    var hours = Number(time.match(/^(\d+)/)[1]);
    var minutes = Number(time.match(/:(\d+)/)[1]);
    var AMPM = time.match(/\s(.*)$/)[1];
    if (AMPM == "PM" && hours < 12) hours = hours + 12;
    if (AMPM == "AM" && hours == 12) hours = hours - 12;
    var sHours = hours.toString();
    var sMinutes = minutes.toString();
    if (hours < 10) sHours = "0" + sHours;
    if (minutes < 10) sMinutes = "0" + sMinutes;
    return (sHours + ":" + sMinutes);
}

/**
 * Toggle the visibility of the send update email container, based on the user selection
 * @param  mixed The radio button that was clicked, to read the value from
 */
function toggleUpdateEmailContainer( clicked_button ) {
	jQuery( '.send-update-email' ).stop().fadeToggle();
}
