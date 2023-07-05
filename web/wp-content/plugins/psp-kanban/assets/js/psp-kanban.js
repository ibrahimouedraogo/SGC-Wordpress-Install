var psp_lane_delete_confirmation = 'Are you sure you want to delete this list?';

jQuery(document).ready(function($) {

     psp_kb_init();

     var phaseIdField = $('.psp-frontend-form').find('.psp-frontend-field-phase_id');
     $(phaseIdField).hide().find('select').prop( 'required', false );

    $('.psp-section').on( 'click', '.psp-kb-switch', function(e) {

         e.preventDefault();

         var view = $(this).data('view');
         var parent = $(this).parents('#psp-projects');

         if( view == 'board' ) {

              $(parent).find('#psp-phases').addClass('psp-kb-hidden').removeClass('psp-kb-active');
              $(parent).find('.psp-kanban').show();

              psp_kb_set_kanban_width();

              $(phaseIdField).show().find('select').prop( 'required', true );

         } else {

              $(parent).find('#psp-phases').removeClass('psp-kb-hidden').addClass('psp-kb-active');
              $(parent).find('.psp-kanban').hide();

         }

    });

     /**
       * #02 Section: Task progress
       */

     $('#psp-projects .psp-kanban').on( 'click', '.task-edit-link', function(e) {
          e.preventDefault();
          $(this).parents('.psp-task-edit-links').addClass('is-active');
     });

     $('#psp-projects .psp-kanban').on( 'click', '.js-kb-complete-task-link', function(e) {

          e.preventDefault();

          var card = $(this).parents('.psp-card.task-item');

          psp_kb_move_card_to_complete( card );

     });

     $('#psp-phases').on( 'click', '.complete-task-link', function(e) {

          var task_id = $(this).parents('.task-item').find('.psp-task-title').data('task_id');

          var card = $('.psp-kanban').find('.task-item[data-task_id="' + task_id + '"]');

          psp_kb_move_card_to_complete( card );

     });

     $('#psp-projects .psp-kanban').on( 'click', '.js-kb-task-save-button', function(e) {

          e.preventDefault();

          var card = $(this).parents('.psp-card.task-item');
          var progress = Number( $(this).parents('.js-kb-task-select').find('.edit-task-select').val() );

          $(this).parents('.psp-task-edit-links').removeClass('is-active');

          if( progress == '100' ) {
               psp_kb_move_card_to_complete( card );
          } else {
               psp_kb_update_task_progress( card, progress );
          }

     });

     /**
       * #03 Section: Lane Menu
       */

     $('#psp-projects').on( 'click', '.psp-lane__menu', function(e) {

          e.preventDefault();
          e.stopPropagation();

          $(this).addClass('is-open');

     });

     $('#psp-projects').click(function(e) {
          $('.psp-lane__menu').removeClass('is-open');
     });

     /**
       * #04 Section: Add Lanes
       */

     $('.psp-add-lane form').submit(function(e) {

       e.preventDefault();

       var data = $(this).serialize();
       var form = $(this);
       var post_id = $(this).data('post_id');

       $(form).addClass('psp-is-loading');

       $.ajax({
           url  :  pano_ajax + "?action=psp_kanban_ajax_add_lane",
           type :  'post',
           data : data,
           success: function( response, success ) {

               $('.psp-kanban[data-post_id=' + post_id + ']').find('.psp-lanes__cards').append( response.data.markup );

               psp_kb_make_lists_sortable( $('.psp-kanban[data-post_id=' + post_id + ']').find('.psp-lane__container') );


               $('.psp-modal.psp-add-lane').find('.modal-close').click();

               psp_kb_reset_form(form);


           },
           error: function( response ) {

               alert( response.message );

           }
       });


   });

   $('.psp-edit-lane form').submit(function(e) {

    e.preventDefault();

    var data = $(this).serialize();
    var form = $(this);
    var post_id = $(this).data('post_id');

    $(form).addClass('psp-is-loading');

    $.ajax({
         url  :  pano_ajax + "?action=psp_kanban_ajax_update_lane",
         type :  'post',
         data : data,
         success: function( response, success ) {

             var lane = $('.psp-lane[data-slug="' + response.data.slug + '"]');
             var container = $(lane).find('.psp-lane__container');

             if( response.data.updatable ) {
                  updatable = 'yes';
             } else {
                  updatable = 'no';
             }

             if( response.data.progress ) {
                  progress = response.data.progress;
             } else {
                  progress = 'no';
             }

             $(lane).data( 'updatable', updatable );
             $(container).data( 'updatable', updatable );

             $(lane).data( 'progress', progress );
             $(container).data( 'progress', progress );

             $(lane).find('.psp-lane-handle').text( response.data.label );

             $('.psp-modal.psp-add-lane').find('.modal-close').click();

             psp_kb_reset_form(form);

         },
         error: function( response ) {

             alert( response.message );

         }
    });


});

$('#psp-projects').on( 'click', '.psp-phase .psp-fe-add-element', function(e) {

     $('.psp-frontend-form input[name="lane"]').val('new');

     // TODO: Update phase field to current phase


});



   $('#psp-projects').on( 'click', '.psp-js-del-lane', function(e) {

        e.preventDefault();

        var r = confirm( psp_lane_delete_confirmation );
        if( r == false ) {
             return;
        }

        var slug = $(this).parents('.psp-lane').data('slug');
        var post_id = $(this).parents('.psp-kanban').data('post_id');

        var data = {
             slug : slug,
             post_id : post_id
        }

        $.ajax({
            url  :  pano_ajax + "?action=psp_kanban_ajax_delete_lane",
            type :  'post',
            data : data,
            success: function( response, success ) {

                 // TODO, gotta check for and refresh new to-do's lane
                $('.psp-kanban[data-post_id="' + post_id + '"]').find('.psp-lane[data-slug="' + slug + '"]').fadeOut('slow', function() {
                     $('.psp-kanban[data-post_id="' + post_id + '"]').find('.psp-lane[data-slug="' + slug + '"]').remove();
                });

                if( response.data.reloadTasks == true ) {
                     psp_kb_reload_new_tasks( post_id );
                }

            },
            error: function( response ) {

                alert( response.data.message );

            }
        });

   });

   $('#psp-projects').on( 'click', '.psp-js-edit-lane', function(e) {

        e.preventDefault();

        var parent    = $(this).parents('.psp-lane');
        var form      = $('.psp-edit-lane').find('.psp-lane-form');
        var progress  = $(parent).find('.psp-lane__container').data('progress');
        var updatable = $(parent).find('.psp-lane__container').data('updatable');
        var slug      = $(parent).find('.psp-lane__container').data('slug');

        $(form).find('input[name="label"]').val( $(parent).find('.psp-lane-handle').text() );
        $(form).find('input[name="slug"]').val( slug );
        $(form).find('select[name="progress"]').val( progress );
        $(form).find('select[name="updatable"]').val( updatable );


   });

   $(document).on( 'psp-fe-deleted-task', function( event, phase_id, task_id ) {

        $('.psp-lane__task[data-task_id="' + task_id + '"]').slideUp();

   });


   $('#psp-phases').on( 'click', '.psp-fe-add-element', function(e) {

        var phaseId = $(this).parents('.psp-phase').data('phase_id');
        var phaseTitle = $(this).parents('.psp-phase').find('.psp-phase-title').text();
        var frontendField = $('.psp-frontend-form').find('.psp-frontend-field-phase_id select');

        if( !$(frontendField).find('option[value="' + phaseId + '"]').length ) {
             $(frontendField).append( $('<option>', { value: phaseId, text: phaseTitle }) );
        }

        $('.psp-frontend-form').find('.psp-frontend-field-phase_id').val( phaseId );

   });

   $('.psp-kanban').on( 'click', '.fe-edit-task-link', function(e) {

       if( $('select[data-fieldhelpers-field-select="assigned"]').length ) {
           $('select[data-fieldhelpers-field-select="assigned"]').select2();
       }

      e.preventDefault();

      var target = $(this).attr('href');
      var modal  = $(target);
      var values = $(this).data();

      $(modal).find('input[name="lane"]').val( $(this).parents('.psp-lane').data('slug') );

      $(modal).removeClass('is-add-task').addClass('is-edit-task');

      for ( var name in values ) {

          if( name == 'task_description' && values.task_description.length > 0 ) {
              $( modal ).find( '[name="' + name + '"]' ).val(unescape(values[name]) );
          } else if( name == 'assigned' ) {

               var strAssigned = String(values.assigned);

               if( strAssigned.indexOf(',') > -1 ) {
                    var assigned = strAssigned.split(',');
               } else if( strAssigned === "" ) {
                    var assigned = 0;
               } else {
                    var assigned = strAssigned;
               }

               $('select[data-name="field-assigned"]').val( assigned ).trigger('change');


          } else {
              $( modal ).find( '[name="' + name + '"]' ).val( values[name] );
          }
      }

      $(modal).find('input[type="submit"]').val( values['submit_label'] );
      $(modal).find('.psp-h2').text( values['modal_title'] );

      // TODO: This might not be necissary
        // Re-init datepickers. Important after Deleting a Task, since the Datepicker needs to be re-inited (as the DOM was removed and re-added) and ensure the datepicker knows the right value as it is created
        if ( jQuery( '.psp-datepicker' ).length ) {
          psp_init_datepickers();
        }

      if( typeof(tinyMCE) !== 'undefined' ) {

          if( tinymce.get('field-task_description') ) {

              tinymce.get('field-task_description').setContent( values.task_description );

          } else {

              tinymce.init({
                  selector: 'textarea[name="task_description"]',
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

      $(modal).find('.fe-del-task-link').removeClass('psp-hide');

        $( document ).trigger( 'psp-fe-task-edit-modal-populated', [ $( modal ), values ] );

  });


});

function psp_kb_reset_form( form ) {
     jQuery(form).trigger('reset');
}

function psp_kanban_reorder_lane( lane, event ) {

     var data = {
          post_id : jQuery(lane).parents('.psp-kanban').data('post_id'),
          index   : jQuery(lane).index(),
          slug    : jQuery(lane).data('slug'),
     }

     jQuery.ajax({
         url: pano_ajax + "?action=psp_kanban_ajax_move_lane",
         type: 'post',
         data: data,
         success: function( response ) {

            if( response.data.success == false ) {
                 alert( response.data.message );
                 return;
            }

         }

     });

}

function psp_kanban_move_task( task, origin, destination, type ) {

     var data = {
          post_id : jQuery(task).parents('.psp-kanban').data('post_id'),
          origin  : origin,
          destination : destination,
          index   : jQuery(task).index(),
          type    : type,
          task_id : jQuery(task).data('task_id')
     }

     var original_task = jQuery(task);

     jQuery.ajax({
         url: pano_ajax + "?action=psp_kanban_move_task",
         type: 'post',
         data: data,
         success: function( response ) {

            if( response.data.success == false ) {
                 alert( response.data.message );
                 return;
            }

            progress = response.data.progress.toString();

            if( progress != 'false' ) {
                 jQuery(original_task).find('.status').removeClass().addClass('status psp-' + progress );
            }

      }

     });

}

function psp_kb_make_lists_sortable( container ) {

     jQuery(container).sortable({
          // containment: '.psp-lane__container',
          handle: '.psp-task-title',
          connectWith: '.psp-lane__container',
          receive: function( event, ui ) {

               item_recieved = true;

               var destination = jQuery(this).data('slug');
               var origin = jQuery(ui.sender[0]).data('slug');
               var type = 'switch';

               psp_kanban_move_task( ui.item, origin, destination, type );

          },
          stop: function( event, ui ) {

               if( typeof item_recieved !== 'undefined' && item_recieved ) {

                    // Reset
                    item_recieved = false;

               } else {

                    var destination = jQuery(this).data('slug');
                    var origin = jQuery(this).data('slug');
                    var type = 'sort';

                    psp_kanban_move_task( ui.item, origin, destination, type );

               }

          },

     });

     psp_kb_make_cards_sortable( jQuery(container).find('.psp-lanes__cards') );

}

function psp_kb_reload_new_tasks( post_id ) {

     var data = {
          post_id : post_id,
     }

     jQuery.ajax({
         url: pano_ajax + "?action=psp_kanban_ajax_get_new_tasks",
         type: 'post',
         data: data,
         success: function( response ) {

            if( response.data.success == false ) {
                 alert( response.data.message );
                 return;
            }

            jQuery('.psp-kanban[data-post_id="' + post_id + '"]').find('.psp-lane[data-slug="new"]').replaceWith( response.data.markup );

            psp_kb_make_lists_sortable( jQuery('.psp-kanban[data-post_id=' + post_id + ']').find('.psp-lane__container') );

       }

     });

}

function psp_kb_make_cards_sortable( container ) {

     jQuery(container).sortable({
          handle: '.psp-lane-handle',
          update: function( event, ui ) {

               psp_kanban_reorder_lane( ui.item, event )

          }
     });

}

function psp_kb_update_task_progress( task, progress ) {

     var data = {
          post_id : jQuery(task).parents('.psp-kanban').data('post_id'),
          progress : Number(progress),
          task_id : jQuery(task).data('task_id'),
          phase_id : jQuery(task).data('phase_id')
     }

     var original_task = jQuery('.task-item.psp-task-id-' + data.task_id ); //jQuery(task);

     jQuery.ajax({
         url: pano_ajax + "?action=psp_kb_ajax_update_task_progress",
         type: 'post',
         data: data,
         success: function( response ) {

                 if( response.data.success == false ) {
                      alert( response.data.message );
                      return;
                 }

                 jQuery(original_task).attr( 'data-progress', data.progress ).find('.status').removeClass().addClass('status psp-' + data.progress );

                 // Update project

                 psp_update_phase_completion( data.post_id, data.phase_id );

                 psp_update_total_progress( data.post_id );

          }

     });

}

function psp_kb_move_card_to_complete( task ) {

     var origin = jQuery(task).parents('.psp-lane').data('slug');
     var destination = 'completed';
     var type = 'switch';
     var board = jQuery(task).parents('.psp-kanban');

     jQuery(task).fadeOut( 'fast', function() {
          jQuery(task).remove();

          target = jQuery(board).find('.psp-lane[data-slug="completed"] .psp-lane__container');
          jQuery(task).appendTo(target);
          jQuery(task).fadeIn('fast');
          jQuery(task).find('.status').removeClass().addClass('status psp-100');

          psp_kanban_move_task( task, origin, destination, type );

     });

}

function psp_kb_set_kanban_width() {

     var width = 0;
     var offset = 0;
     var count = 0;
     var elmWidth = 0;

     jQuery('.psp-kanban').find('.psp-lanes__cards > .psp-lane').each(function(e) {
          elmWidth = jQuery(this).width();
          width += jQuery(this).width() + 16;
     });

     width += elmWidth;


     jQuery('.psp-lanes-wrap').width( width );

     var containerWidth = jQuery(window).width();
     var contentWidth = jQuery('.psp-kanban .psp-wrapper').width();

     var offset = Math.floor( (containerWidth - contentWidth) / 2);

     jQuery('.psp-lanes-wrap').css( 'paddingLeft', offset + 'px' );


}

function psp_kb_init() {

     /**
       * #01 Section: Sorting
       */

     jQuery('.psp-lanes__cards').sortable({
          handle: '.psp-lane-handle',
          update: function( event, ui ) {

               psp_kanban_reorder_lane( ui.item, event )

          }
     });

     psp_kb_make_lists_sortable( jQuery('.psp-lane__container') );

}
