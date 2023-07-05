( function( $ ) {
	
	$( document ).ready( function() {
		
		$( '.sub_field.field_type-repeater[data-field_key="psp_st_sub_tasks"], .acf-field-repeater[data-key="psp_st_sub_tasks"]' ).each( function( index, repeater ) {
			
			toggleParentTaskCompletion( $( repeater ) );
			
			if ( $( repeater ).find( '.row, .acf-row:not( .acf-clone )' ).length ) {
			
				calculateParentTaskProgress( $( repeater ).closest( '.row, .acf-row:not( .acf-clone )' ), $( repeater ) );
				
			}
			
		} );
		
		$( '.sub_field.field_type-repeater[data-field_key="psp_st_sub_tasks"] .row .sub_field[data-field_name="status"], .acf-field-repeater[data-key="psp_st_sub_tasks"] .acf-row:not( .acf-clone ) .acf-field[data-name="status"]' ).on( 'change', function( event ) {
			
			var $subTasks = $( this ).closest( '.sub_field.field_type-repeater[data-field_key="psp_st_sub_tasks"], .acf-field-repeater[data-key="psp_st_sub_tasks"]' ),
				$parentTask = $subTasks.closest( '.row, .acf-row:not( .acf-clone )' );
			
			calculateParentTaskProgress( $parentTask, $subTasks );
			
		} );
		
		attachAddRemoveEvents();
		
		// ACF 5 only, since ACF 5 hides removal behind a confirm dialog
		// ACF 5 also gives us some nice events
		if ( typeof acf.add_action !== 'undefined' ) {
		
			acf.add_action( 'change', function( element ) {

				var $subTasks = $( element ).closest( '.acf-field' );
				
				if ( $subTasks.data( 'key' ) !== 'psp_st_sub_tasks' ) return;
				
				var $parentTask = $subTasks.closest( '.acf-row:not( .acf-clone )' );
				
				addRemoveSubTaskEvent( $parentTask, $subTasks );

			} );
			
		}
		
	} );
	
	/**
	 * Toggle visibility of the Parent Task Completion
	 * 
	 * @param		{object} $repeater jQuery DOM Object for the Sub Task Repeater
	 *                            
	 * @since		{{VERSION}}
	 * @return		void
	 */
	function toggleParentTaskCompletion( $repeater ) {
		
		// If there are rows, hide Parent Progress
		if ( $repeater.find( '.row, .acf-row:not( .acf-clone )' ).length ) {
			
			$repeater.parent().find( '.sub_field[data-field_name="status"], .acf-field[data-name="status"]' ).first().addClass( 'hidden' );
			
		}
		else {
			
			$repeater.parent().find( '.sub_field[data-field_name="status"], .acf-field[data-name="status"]' ).first().removeClass( 'hidden' );
			
		}
		
	}
	
	/**
	 * Update the hidden Completion value for the Parent Task
	 * 
	 * @param {object} $parentTask jQuery DOM Object for the Parent Task
	 * @param {object} $subTasks   jQuery DOM Object for the Sub Task Repeater
	 *                             
	 * @since		{{VERSION}}
	 * @return		void
	 */
	function calculateParentTaskProgress( $parentTask, $subTasks ) {
		
		var progress = 0,
			$subTaskCompletions = $subTasks.find( '.row .sub_field[data-field_name="status"], .acf-row:not( .acf-clone ) .acf-field[data-name="status"]' ),
			$parentTaskCompletion = $parentTask.find( '.sub_field[data-field_name="status"], .acf-field[data-name="status"]' ).first().find( 'select' );
		
		$subTaskCompletions.each( function( index, row ) {
			
			progress += parseInt( $( row ).find( 'select' ).val() );
			
		} );
		
		var overallProgress = Math.ceil( progress / $subTaskCompletions.length );
		
		if ( ( overallProgress % 5 ) < 3 ) {
			
			if ( ( overallProgress % 5 ) !== 0 ) {
				
				// Round down to nearest multiple of 5
				overallProgress = Math.floor( overallProgress / 5 ) * 5;
				
			}
			
		}
		else {
			
			// Round up to nearest multiple of 5
			overallProgress = Math.ceil( overallProgress / 5 ) * 5;
			
		}
		
		$parentTaskCompletion.val( overallProgress );
		
	}
	
	/**
	 * On Add/Remove Sub Task, recalculate Parent Task Completion and hide/show the Parent Task Completion
	 * 
	 * @param {object} $parentTask jQuery DOM Object for the Parent Task
	 * @param {object} $subTasks   jQuery DOM Object for the Sub Task Repeater
	 *                             
	 * @since		{{VERSION}}
	 * @return		void
	 */
	function addRemoveSubTaskEvent( $parentTask, $subTasks ) {
		
		toggleParentTaskCompletion( $subTasks );
				
		// If there are still subtasks, recalculate
		if ( $subTasks.find( '.row, .acf-row:not( .acf-clone )' ).length ) {

			calculateParentTaskProgress( $parentTask, $subTasks );

		}
		else {

			// Set Parent Completion value to 0
			$parentTask.find( '.sub_field[data-field_name="status"], .acf-field[data-name="status"]' ).first().find( 'select' ).val( 0 );

		}
		
	}
	
	/**
	 * Attaches the Add/Remove Events for Sub Tasks
	 * Remove Events are handled separately for ACF 5 and do not need to be re-attached when the Repeater changes
	 * 
	 * @since		{{VERSION}}
	 * @return		void
	 */
	function attachAddRemoveEvents() {
		
		$( 'tr[data-field_key="psp_st_sub_tasks"] .add-row-end.acf-button, tr[data-field_key="psp_st_sub_tasks"] .acf-button-add, tr[data-field_key="psp_st_sub_tasks"] .acf-button-remove, .acf-field-repeater[data-key="psp_st_sub_tasks"] a[data-event="add-row"]' ).on( 'click', function() {
			
			var $subTasks = $( this ).closest( '.sub_field.field_type-repeater[data-field_key="psp_st_sub_tasks"], .acf-field-repeater[data-key="psp_st_sub_tasks"]' ),
				$parentTask = $subTasks.closest( '.row, .acf-row:not( .acf-clone )' );
            
			// Wait for transitions
            setTimeout( function() {
				
				addRemoveSubTaskEvent( $parentTask, $subTasks );
				
				// Reattach events
				attachAddRemoveEvents();
				
			}, '500' );
            
        } );
		
	}
	
} )( jQuery );