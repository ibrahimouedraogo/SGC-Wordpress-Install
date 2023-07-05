jQuery(document).ready(function($) {

    $('#psp-phases, #psp-archive-content').on( 'click', '.complete-sub-task-link', function(e) {

         	e.preventDefault();

          var formID 		= $(this).attr('data-target');
          var project_id 	= $(this).attr('data-project');
          var task_id 		= $(this).attr('data-task');
          var progress 		= 100;
          var phase_progress 	= $(this).attr('data-phase-auto');
          var total_progress 	= $(this).attr('data-overall-auto');

          if( $(this).parents('.task-item').length ) {
               var phase_id        = $(this).parents('.task-item').find('.psp-task-title').data('phase_id'); // This is the wrong one for some reason
               var parent_id       = $(this).parents('.task-item').find('.psp-task-title').data('task_id');
          } else if ( $(this).parents('.sub-task-row').length ) {
               var phase_id 		= $(this).parents('.sub-task-row').prev('.task-row').data('phase_id');
               var parent_id 		= $(this).parents('.sub-task-row').prev('.task-row').data('task_id');
          }

          // Single, Dashboard, and Your Tasks view
          var $parentTask = $( '.psp-single-' + project_id + ' .psp-phase[data-phase_id="' + phase_id + '"] .psp-task-id-' + parent_id + ', .psp-task-project-' + project_id + ' .psp-task-id-' + parent_id + ', .project-' + project_id + '[data-task_id="' + parent_id + '"]'  );

          // Update dynamically
          psp_update_sub_task_progress( project_id, phase_id, task_id, parent_id, $parentTask, progress, phase_progress, total_progress );

          var the_parent = $(this).parents('.sub-task-item'),
        	target 		= $(the_parent).find('.status');

          if( progress == '100' ) {
         		$(the_parent).addClass('complete');
         	} else {
         		$(the_parent).removeClass('complete');
         	}

          $(target).removeClass().addClass('status').addClass( 'psp-' + progress );
          $(the_parent).attr( 'data-progress', progress );

    });

    $('#psp-phases, #psp-archive-content').on( 'click', '.sub-task-save-button', function(e) {

        e.preventDefault();

		var task_element 	= $(this).parents('.sub-task-select').find( '#edit-sub-task-select-' + $(this).attr('data-phase') + '-' + $(this).attr('data-task') );
          var project_id 	= $(this).attr('data-project');
          var task_id 		= $(this).attr('data-task');

          if( $(this).parents('.task-item').length ) {
               var phase_id        = $(this).parents('.task-item').find('.psp-task-title').data('phase_id');
               var parent_id       = $(this).parents('.task-item').find('.psp-task-title').data('task_id');
          } else if ( $(this).parents('.sub-task-row').length ) {
               var phase_id 		= $(this).parents('.sub-task-row').prev('.task-row').data('phase_id');
               var parent_id 		= $(this).parents('.sub-task-row').prev('.task-row').data('task_id');
          }

		var progress 		= $(task_element).val();
		var parent_div 	= $(this).parents('.task-select');

		var phase_progress 	= $(this).attr('data-phase-auto');
		var total_progress 	= $(this).attr('data-overall-auto');

		// Single, Dashboard, and Your Tasks view
          var $parentTask = $( '.psp-single-' + project_id + ' .psp-phase[data-phase_id="' + phase_id + '"] .psp-task-id-' + parent_id + ', .psp-task-project-' + project_id + ' .psp-task-id-' + parent_id + ', .project-' + project_id + '[data-task_id="' + parent_id + '"]'  );

		psp_update_sub_task_progress( project_id, phase_id, task_id, parent_id, $parentTask, progress, phase_progress, total_progress );

          var the_parent = $(this).parents('.sub-task-item'),
        	target 		= $(the_parent).find('.status');

          $(target).removeClass().addClass('status').addClass( 'psp-' + progress );
          $(the_parent).attr('data-progress',progress);

          if(progress == '100') {
          	$(the_parent).addClass('complete');
          } else {
          	$(the_parent).removeClass('complete');
          }

          $(this).parents('.psp-sub-task-edit-links').removeClass('is-active');

    });

    $('#psp-phases, #psp-archive-content').on( 'click', '.sub-task-edit-link', function(e) {

		e.preventDefault();
          $(this).parents('.psp-sub-task-edit-links').addClass('is-active');

		// var the_parent = $(this).parents('.sub-task-item');
          // $(the_parent).children('.sub-task-select').fadeIn('slow');

	});

});

function psp_update_sub_task_progress( project_id, phase_id, task_id, parent_id, $parentTask, progress, phase_progress, total_progress ) {

    var ajaxurl 		= jQuery('#psp-ajax-url').val();

	jQuery.ajax({
		url: ajaxurl + "?action=psp_update_sub_task_fe",
		type: 'post',
		data: {
			project_id : project_id,
			phase_id	 : phase_id,
			task_id    : task_id,
               parent_id  : parent_id,
			progress	 : progress
		},
		success: function(response) {

            $parentTask.find( '.psp-progress-bar .status').removeClass().addClass('status').addClass('psp-' + response.data.parent_progress );
            $parentTask.attr( 'data-progress', response.data.parent_progress );

			psp_update_my_task_count( project_id, phase_id );

			if( typeof phase_progress !== 'undefined' && phase_progress == 'Yes' ) {
				psp_update_phase_completion( project_id, phase_id );
			}

			if( typeof total_progress !== 'undefined' && total_progress == 'Yes' ) {
				psp_update_total_progress(project_id);
			}

		},
		error: function(data) {
			console.log(data);
		}
	});

}
