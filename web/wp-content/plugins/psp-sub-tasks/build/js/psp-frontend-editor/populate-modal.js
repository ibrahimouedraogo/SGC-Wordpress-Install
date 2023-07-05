( function( $ ) {

	$( document ).ready( function() {

		$(document).on('click', '.fieldhelpers-field-repeater-add-button', function(event) {

			if( $('select[data-fieldhelpers-field-select="assigned"]').length ) {
				 $('select[data-fieldhelpers-field-select="assigned"]').select2();
			}

			if( $('.subtask-description') && typeof(tinyMCE) !== 'undefined' ) {
				tinymce.init({
				    selector: 'textarea.subtask-description',
				    theme : 'modern',
				    skin  : 'lightgray',
				    menubar   : false,
				    statusbar : true,
				    plugins   : 'lists',
				    elements  : 'pre-details',
				    mode      : 'exact',
					 toolbar: "bold italic underline justifyleft justifycenter justifyright outdent indent code formatselect numlist bullist"
				});

			}

		});

		$( '#psp-phases' ).on( 'click', '.fe-edit-task-link', function( event ) {

			var target = $( this ).attr( 'href' );
			var $modal = $( target );
			var values = $( this ).data();

			var subTasks = values['sub_task'];

			clearRepeater( $modal );

			addRows( subTasks, $modal );

			$modal.find( 'input[data-fieldhelpers-field-datepicker]' ).each( function( index, preview ) {

				var date = $( preview ).next( 'input[type="hidden"]' ).val(),
					month = date.substring( 4, 6 ),
					day = date.substring( 6, 8 ),
					year = date.substring( 0, 4 ),
					dateObject = new Date( year + '-' + month + '-' + day );

					// Don't populate a value if there is no due date
					if( $( preview ).next( 'input[type="hidden"]' ).val() != '' ) {
						// https://stackoverflow.com/a/16048201
						dateObject.setTime( dateObject.getTime() + dateObject.getTimezoneOffset() * 60 * 1000 );
					} else {
						dateObject = null;
					}

					$( preview ).datepicker( 'setDate', dateObject ).on('changeDate', function(e) {

						console.log('is see change!');

						$(this).siblings('.psp-datepicker').val( $(this).val() );

					});


			} );

		} );

		$( '#psp-phases' ).on( 'click', '.psp-fe-add-element', function( event ) {

			var target = $( this ).attr( 'href' );
			var $modal = $( target );

			clearRepeater( $modal );

		} );

	} );

	/*
	$( document ).on( 'psp-fe-task-edit-modal-populated', function( event, $modal, values ) {

		// With our Included Datepicker, we don't want to do it based on the field value. No tomfoolery necessary
		$modal.find( 'input.psp-datepicker' ).each( function( index, field ) {

			$( field ).datepicker();

		} );

	} ); */

	$( document ).on( 'psp-fe-deleted-task', function( event, phase_index, task_index ) {

		if ( typeof rbmFHinitField === 'function' ) {

			// Re-init the Repeater field
			rbmFHinitField( $( '.psp_st-fieldhelpers-field[data-fieldhelpers-name="sub_tasks"]' ).closest( '.psp-frontend-field' ) );

		}
		else {
			// Oh well, Repeaters will be broken on Task Delete if an older RBM FH is running
		}

	} );

	/**
	 * Clears the Sub Task Repeater of all Values
	 *
	 * @param		{object} $modal Task Edit Modal jQuery Object
	 *
	 * @since		{{VERSION}}
	 * @return		void
	 */
	function clearRepeater( $modal = null ) {

		$modal.find( '.psp_st-fieldhelpers-field[data-fieldhelpers-name="sub_tasks"] .fieldhelpers-field-repeater-row' ).remove();

	}

	/**
	 * Clears the Sub Task Repeater of all Values
	 *
	 * @param		{Array} subTasks      Array of Objects representing the values for each Row
	 * @param		{object} $modal       Task Edit Modal jQuery Object
	 *
	 * @since		{{VERSION}}
	 * @return		void
	 */
	function addRows( subTasks, $modal = null ) {

		// Add Rows
		for ( var subTask in subTasks ) {

			$modal.find( '.psp_st-fieldhelpers-field[data-fieldhelpers-name="sub_tasks"] .fieldhelpers-field-repeater-add-button' ).click();

		}

		// Populate Rows
		$modal.find( '.psp_st-fieldhelpers-field[data-fieldhelpers-name="sub_tasks"] .fieldhelpers-field-repeater-row' ).each( function( index, row ) {

			var subTask = subTasks[ index ];

			for ( var key in subTask ) {

				if( key == 'assigned' ) {

					var strAssigned = String(subTask.assigned);

					if( strAssigned.indexOf(',') > -1 ) {
						var assigned = strAssigned.split(',');
					} else if( strAssigned === "" ) {
						var assigned = 0;
					} else {
						var assigned = strAssigned;
					}

					$( row ).find( 'select[data-fieldhelpers-field-select="assigned"]' ).val( assigned ).trigger('change');

				} else {

					var value = subTask[ key ];

					$( row ).find( '*[name$="' + key + ']"]' ).val( value );

				}

			}

		} );

		if( $('.subtask-description') && typeof(tinyMCE) !== 'undefined' ) {
			tinymce.init({
			    selector: 'textarea.subtask-description',
			    theme : 'modern',
			    skin  : 'lightgray',
			    menubar   : false,
			    statusbar : true,
			    plugins   : 'lists',
			    elements  : 'pre-details',
			    mode      : 'exact',
				 toolbar: "bold italic underline justifyleft justifycenter justifyright outdent indent code formatselect numlist bullist"
			});

		}

	}

} )( jQuery );
