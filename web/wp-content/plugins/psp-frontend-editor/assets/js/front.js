
acf_set_initial = false;

/**
 * 'acf/setup_fields' only fires for ACF4
 */
jQuery(document).on('acf/setup_fields', function(e, el){

    setTimeout(function() {
        /**
         * Still need to do something here to look up the section and set the appropriate place
         * @type {String}
         */

        if( acf_set_initial == true ) return;

         var initial_marker   = jQuery('.psp-timeline .initial');

         var section   = jQuery(initial_marker).data('target');
         var title     = jQuery(initial_marker).data('title');
         var type      = jQuery(initial_marker).data('type');
         var target    = jQuery( '#' + section );

         acf_set_initial = true;

         psp_fe_update_wizard_section( initial_marker, target, title, type );

         jQuery('#cssload-pgloading').hide();

    }, 500 );

});

/**
 * 'acf/ready' only fires for ACF5
 */
jQuery(document).ready(function() {

    if( jQuery('#psp-projects').hasClass('psp-acf-ver-5') && ( jQuery('#psp-projects').hasClass('psp-fe-edit-project') || (jQuery('#psp-projects').hasClass('psp-fe-manage-page-new') ) ) )  {

        var initial_marker   = jQuery('.psp-timeline .initial');

        var section   = jQuery(initial_marker).data('target');
        var title     = jQuery(initial_marker).data('title');
        var type      = jQuery(initial_marker).data('type');

        jQuery('.timeline-title').text(title);
        jQuery( '#' + section ).show();

        if( type == 'tab' ) {
            var index  = jQuery(initial_marker).index();
            var tab    = jQuery('.acf-tab-group li').get(index);
            jQuery(tab).find('a').click();
        }

        if( jQuery(initial_marker).is(':first-child') ) {
            jQuery('.psp_fe_title_fields').show();
            jQuery('.psp-wizard-edit-prev-button').addClass('is-inactive');

        }
        if( jQuery(initial_marker).is(':last-child') ) {
            jQuery('.psp-wizard-edit-next-button').addClass('is-inactive');
        }

        jQuery('.acf-repeater .acf-row').each(function() {

            if( !jQuery(this).hasClass('acf-clone') ) {
                jQuery(this).addClass('-collapsed');
            }

        });

        jQuery('#cssload-pgloading').hide();

   }

});

jQuery( document ).on( 'psp-tasks-auto-expanded', function( event ) {

	 jQuery( '.psp-task-list-wrapper' ).each( function( index, wrapper ) {

		 if ( jQuery( wrapper ).find( '.psp-task-list' ).hasClass( 'active' ) ) {
			 jQuery( wrapper ).find('.psp-fe-add-task').slideToggle();
		 }

	 } );

 } );

function psp_fe_update_wizard_section( marker, target, title, type ) {

    jQuery('.timeline-title').text(title);
    jQuery(target).show().removeClass('hidden-by-tab');

    if( type == 'tab' ) {
        var index  = jQuery(marker).index();
        var tab    = jQuery('.acf-tab-group li').get(index);
        jQuery(tab).find('a').click();
    }

    if( jQuery(marker).is(':first-child') ) {

        jQuery('.psp_fe_title_fields').show();
        jQuery('.acf-field--post-title').show();
        jQuery('.psp_fe_title_field').show();
        jQuery('.psp-wizard-edit-prev-button').addClass('is-inactive');
        if( !$('#acf-field-automatic_progress').prop('checked') ) {
            $('#acf-percent_complete').show();
        }

    } else {

        $('#acf-percent_complete').hide();

    }

    if( jQuery(marker).is(':last-child') ) {
        jQuery('.psp-wizard-edit-next-button').addClass('is-inactive');
    }

}

jQuery(document).ready(function($) {

    $('.psp-datepicker').datepicker();

    $('.new-project-type-group input').click(function(e) {

        if( $(this).val() == 'new' ) {
            $('.psp-fe-template-form').hide();
            $('.acf-form').show();
        } else {
            $('.psp-fe-template-form').show();
            $('.acf-form').hide();
        }

    });

    psp_init_datepickers();

    $('.all-fe-checkbox').click(function() {

        if( $(this).is(':checked') ) {
            $(this).parents('#psp-notify-users').find('input.specific-fe-checkbox').prop('checked',false);
            $(this).parents('#psp-notify-users').find('.psp-notify-list input').prop('checked',true);
            $(this).parents('#psp-notify-users').find('.psp-notify-list').slideUp('fast');
        } else {
            $(this).parents('#psp-notify-users').find('.psp-notify-list input').prop('checked',false);
        }

    });

    $('.specific-fe-checkbox').click(function() {

        if( $(this).is(':checked') ) {
            $(this).parents('#psp-notify-users').find('.psp-notify-list').slideDown('fast');
            $(this).parents('#psp-notify-users').find('input.all-fe-checkbox').prop('checked',false);
        } else {
            $(this).parents('#psp-notify-users').find('.psp-notify-list').slideUp('fast');
            $(this).parents('#psp-notify-users').find('.psp-notify-list input').prop('checked',false);
        }

    });

    psp_fe_init_modals();

    $('.psp-fe-template-form form').submit(function(e) {

        e.preventDefault();

        var template    = $('#psp-fe-use-template').val();
        var ajaxurl     = $('#psp-ajax-url').val();

        if( template == '---' ) {
            return;
        }

        $('.psp-fe-template-form input[type="submit"]').attr( 'disabled', 'true' );
        $('.psp-fe-template-form').addClass('disabled');


        $.ajax({
            url  :  ajaxurl + "?action=psp_duplicate_template",
            type :  'post',
            data : {
                template : template
            },
            success: function( response, success ) {

                window.location = response.data.redirect;

            },
            error: function( response ) {

                $('.psp-fe-template-form .message').html( response.data.message ).show();

            }
        });


    });

    $('.psp-fe-notify-send').click(function(e) {

        e.preventDefault();

        var post_id     = $('#psp-fe-post-id').val();
        var subject     = $('#psp-fe-subject').val();
        var message     = $('#psp-fe-message').val();
        var users       = [];
        var ajaxurl     = $('#psp-ajax-url').val();

        $('.psp-fe-user:checked').each(function() {
            users.push( $(this).val() );
        });

        $.ajax({
            url     :   ajaxurl + "?action=psp_fe_notify",
            type    :   'post',
            data:   {
                post_id : post_id,
                subject : subject,
                message : message,
                users   : users
            },
            success: function( success ) {

                $('.psp-notify-list-message').fadeIn( 'fast' );

                setTimeout( function() {

                    $( '#psp-notify-users' ).fadeOut( 'slow', function() {

                        psp_fe_reset_modal();

                    });

                }, 500);

            }
        });

    });

    function psp_fe_reset_modal() {

        var subject = $('#psp-fe-subject').data( 'default' );
        var message = $('#psp-fe-message').data( 'default' );

        $('#psp-fe-subject').val( $('#psp-fe-subject').data('default') );
        $('#psp-fe-message').val( $('#psp-fe-message').data('default') );
        $('.psp-notify-list-message').hide();
        $('.psp-fe-user').prop( 'checked', false );
        $('.psp-fe-notify-all').prop( 'checked', false );

        $('#psp-projects').removeClass('psp-has-modal');
        $('.psp-modal-wrap').fadeOut();

    }

    $(document).on( 'click', '.js-psp-delete-document', function(e) {

        e.preventDefault();

        var parent = $(this).parents('.psp-document');
        var data   = {
            post_id : $(this).data('project'),
            doc_id  : $(parent).data('id'),
            title   : $(parent).find('.doc-title').text(),
        };

        var ajaxurl = $('#psp-ajax-url').val() + "?action=" + "psp_delete_document";
        var r = confirm( $(this).data('confirm') );

        if( r == false ) {
            return;
        }

        $.ajax({
            url     : ajaxurl,
            type    : 'post',
            data    : data,
            success: function( response, success ) {

                if( response.data.success == false ) {
                    alert( response.data.message );
                } else {
                    $(parent).slideUp('slow');
                }

            },

        });

    });

    /*
     *
     * Front end editing tasks
     */

    $(document).on( 'click', '#psp-phases .task-list-toggle', function(e) {
        $(this).parents('.psp-task-list-wrapper').find('.psp-fe-add-task').slideToggle();
    });

    $( document ).on( 'click', '.fe-del-task-link', function(e) {

        e.preventDefault();

        var result = confirm( $(this).data('confirmation') );

        if( result == true ) {

            var form = $(this).parents('form');
            var task_id = $(form).find('input[name="task_id"]');

            var data = {
				'task_id'   : $(form).find('input[name="task_id"]').val(),
                'phase_id'  : $(form).find('input[name="phase_id"]').val(),
                'post_id'   : $(form).find('input[name="post_id"]').val()
            };

            psp_fe_delete_task( data.post_id, data.phase_id, data.task_id );

            $(form).find('.modal-close').click();
            $(form).trigger('reset');

        }

    });

    $(document).on( 'click', '.psp-fe-add-element', function(e) {

        var target = $(this).attr('href');
        var modal  = $(target);
        var values = $(this).data();

        // Contextualize
        $(modal).addClass('is-add-task').removeClass('is-edit-task');

        for ( var name in values ) {

            if( name == 'task_description' && values.task_description.length > 0 ) {
                $( modal ).find( '[name="' + name + '"]' ).val(unescape(values[name]) );
            } else {
                $( modal ).find( '[name="' + name + '"]' ).val( values[name] );
            }

        }

        if( typeof(tinyMCE) !== 'undefined' ) {

            if( tinymce.get('field-task_description') ) {

                tinymce.get('field-task_description').setContent( ' ' );

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

     $('#psp-add-task').find('.psp-datepicker').datepicker();

          $(modal).find('input[type="submit"]').val( values.submit_label );
          $(modal).find('.psp-h2').text( values.modal_title );

          $(target).find('input[name="task"]').val('');
          $(target).find('input[name="due_date"]').val('');
          $(target).find('.fe-del-task-link').addClass('psp-hide');

          psp_init_datepickers();

     });

    $(document).on( 'submit', '.psp-frontend-form', function(e) {

        e.preventDefault();

        var data        = $(this).serialize();
        var form        = $(this);
        var ajaxurl     = $('#psp-ajax-url').val() + "?action=" + $(form).find('input[name="action"]').val();

        var post_id     = $(form).find('input[name="post_id"]').val();

        $(form).addClass('psp-is-loading');

        $.ajax({
            url     : ajaxurl,
            type    : 'post',
            data    : data,
            success: function( response, success ) {

                // Loop through everything that needs to be modified
                response.data.modify.forEach( psp_fe_modify_frontend );

                $('.fe-edit-task-link').leanModal({ closeButton: ".modal-close" });

                $(form).removeClass('psp-is-loading');

                if( $(form).parents('.psp-modal').hasClass('is-add-task') ) {

                     // Do nothing!

                } else {

                     $(form).parents('.psp-modal').find('.modal-close').click();

                }

                $(form).trigger('reset');

                // Trigger reset doesn't work
                psp_fe_reset_form(form);

                // TODO: Do a check
                psp_fe_update_phase_progress( response.data.phase_progress, response.data.phase_id );
                psp_fe_update_total_progress( response.data.project_progress, post_id );

				psp_update_my_task_count( post_id, $( form ).find( 'input[name="phase_index"]' ).val() );

				jQuery( document ).trigger( 'psp-fe-updated-task', [ $( form ).find( 'input[name="phase_index"]' ).val(), $( form ).find( '[name="task_index"]' ).val() ] );

               $('a.psp-task-title').leanModal({ closeButton: '.modal-close' });


            },
			error: function( request, status, error ) {

                $(form).removeClass('psp-is-loading');

				// Return a 500 error or something similar in PHP via the wp_ajax_psp_fe_update_task Hook to hit the Error block, then use this Event to display whatever error message you need to
				// This allows other plugins to validate Server-side and show their own Errors Client-side
				jQuery( document ).trigger( 'psp-fe-task-update-failed', [ $( form ), $( form ).find( 'input[name="phase_index"]' ).val(), $( form ).find( '[name="task_index"]' ).val() ] );

			}
        });

    });

    $('.psp-add-task-modal .modal-close').click(function(e) {

        var form = $(this).parents('.psp-add-task-modal').find('form');
        $(form).trigger('reset');
        psp_fe_reset_form(form);

    });

    $('#psp-phases').on( 'click', '.fe-edit-task-link', function(e) {

         if( $('select[data-fieldhelpers-field-select="assigned"]').length ) {
             $('select[data-fieldhelpers-field-select="assigned"]').select2();
         }

        e.preventDefault();

        var target = $(this).attr('href');
        var modal  = $(target);
        var values = $(this).data();

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

    $('#nav-delete a').click(function(e) {

        e.preventDefault();

        var post_id     = $(this).data('postid');
        var redirect    = $(this).data('redirect');
        var ajaxurl     = $('#psp-ajax-url').val();


        confirmation = confirm( psp_delete_confirmation_message );
        if( !confirmation ) return false;

        $.ajax({
            url     :   ajaxurl + "?action=psp_fe_delete_project",
            type    :   'post',
            data:   {
                post_id  : post_id,
                redirect : redirect,
            },
            success: function( success, data ) {
                window.location = redirect;
            },
            error: function() {
                alert( 'There was a problem deleting this project, you might not have permission' );
            }
        });


    });


    $('input:radio[name="psp-fe-project-type"]').change(function() {

        if( $(this).val() == 'template' ) {
            $('.psp-wizard-actions').hide();
        } else {
            $('.psp-wizard-actions').show();
        }

    });

    /*
     *
     * Project creation wizard
     */

    $('.psp-wizard-next-button').click(function() {

        var current_tab = $('.acf-tab-group').find('li.active');
        var next_tab    = $(current_tab).next('li');
        var index       = $(next_tab).index();

        $(next_tab).find('a').click();

        // Toggle fields
        $('.acf-field--post-title').hide();
        $('.new-project-type-group').hide();
        $('.psp-fe-template-form').hide();

        if( $(current_tab).is(':first-child') ) {
            $('.psp-wizard-prev-button').removeClass('is-inactive');
            $('.psp_fe_title_fields').hide();
        }

        $('#acf-percent_complete').hide();

        if( $(next_tab).is(':last-child') ) {
            $(this).addClass('is-inactive');
            $('.psp-wizard-actions input[type=submit]').show();
       } else {
            $(this).removeClass('is-inactive');
       }

        $('.psp-wizard-section span.timeline-title').html( $(next_tab).find('a').html() );

        var timeline_spot   = $('.psp-timeline .psp-timeline--marker').get(index);
        var progress        = $(timeline_spot).data('complete');
        var step            = $(timeline_spot).data('step');

        $('.psp-timeline .psp-timeline--marker').removeClass('active');
        $(timeline_spot).addClass('active');

        $('.psp-timeline--bar span').width( progress + '%' );
        $('.psp-wizard-section strong span.step').text(step);

    });

    $('.psp-wizard-prev-button').click(function() {

        var current_tab = $('.acf-tab-group').find('.active');
        var prev_tab    = $(current_tab).prev('li');
        var index       = $(prev_tab).index();
        var old_index   = $(current_tab).index();

        $(prev_tab).find('a').click();

        if( $(current_tab).is(':last-child') ) {
            $('.psp-wizard-next-button').removeClass('is-inactive');
            $('#psp-projects.psp-fe-manage-page-new .psp-fe-wizard input[type=submit]').hide();
        }

        if( $(prev_tab).is(':first-child') ) {
            $('.acf-field--post-title').hide();
            $('.new-project-type-group').show();
            $('.psp-fe-template-form').show();
            $('.psp_fe_title_fields').show();
            $(this).addClass('is-inactive');
            if( $('#acf-field-automatic_progress').prop('checked') ) {
                $('#acf-percent_complete').show();
            }
        }

        $('.psp-wizard-section span.timeline-title').html( $(prev_tab).find('a').html() );

        var timeline_spot       = $('.psp-timeline .psp-timeline--marker').get(index);
        var old_timeline_spot   = $('.psp-timeline .psp-timeline--marker').get(old_index);

        var progress = $(timeline_spot).data('complete');
        var step     = $(timeline_spot).data('step');
        $('.psp-timeline .psp-timeline--marker').removeClass('active');
        $(timeline_spot).addClass('active');

        $('.psp-timeline--bar span').width( progress + '%' );
        $('.psp-wizard-section strong span.step').text(step);

    });

    $('.psp-wizard-edit-next-button').click(function(e) {

        e.preventDefault();

        var current_marker  = $('.psp-timeline .psp-timeline--marker.active:last');
        var next_marker     = $(current_marker).next('.psp-timeline--marker');

        hide_target = $(current_marker).data('target');
        show_target = $(next_marker).data('target');

        $( '#' + hide_target ).hide();
        $( '#' + show_target ).show().removeClass('hidden-by-tab');

        $('.psp_fe_title_fields').hide();
        $('.acf-field--post-title').hide();
        $('#acf-percent_complete').hide();

        $('.psp-wizard-edit-prev-button').removeClass('is-inactive');

        var progress        = $(next_marker).data('complete');
        var step            = $(next_marker).data('step');
        var title           = $(next_marker).data('title');

        if( $(next_marker).data('type') == 'tab' ) {

            var index = $(next_marker).index();
            var tab = $('.acf-tab-group li').get(index);

            $(tab).find('a').click();

        }

        if( $(next_marker).is(':last-child') ) {
            $('.psp-wizard-actions input[type="submit"]').show();
            $('.psp-wizard-edit-next-button').addClass('is-inactive');
        }

        $('.timeline-title').text(title);
        $('.psp-wizard-section span.step').text(step);

        $('.psp-timeline .psp-timeline--marker').removeClass('active');
        $(next_marker).addClass('active');

        $('.psp-timeline--bar span').width( progress + '%' );

    });

    $('.psp-wizard-edit-prev-button').click(function(e) {

        e.preventDefault();

        var current_marker  = $('.psp-timeline .psp-timeline--marker.active:last');
        var next_marker     = $(current_marker).prev('.psp-timeline--marker');

        // Hide and Show

        $('.psp-wizard-edit-next-button').removeClass('is-inactive');

        hide_target = $(current_marker).data('target');
        show_target = $(next_marker).data('target');

        $('#' + hide_target).hide();
        $('#' + show_target).show();

        $(current_marker).removeClass('active');

        var progress        = $(next_marker).data('complete');
        var step            = $(next_marker).data('step');
        var title           = $(next_marker).data('title');

        if( $(next_marker).data('type') == 'tab' ) {

            var index = $(next_marker).index();
            var tab = $('.acf-tab-group li').get(index);

            $(tab).find('a').click();

            /*
            $('.acf-tab-wrap').show();
            $('.acf-tab-group').show();
            */

        }

        if( $(next_marker).is(':first-child') ) {
            $('.psp-wizard-edit-prev-button').addClass('is-inactive');
            $('.psp_fe_title_fields').show();
            if( !$('#acf-field-automatic_progress').prop('checked') ) {
                $('#acf-percent_complete').show();
            }
        }

        $('.timeline-title').text(title);
        $('.psp-wizard-section span.step').text(step);

        $('.psp-timeline .psp-timeline--marker').removeClass('active');
        $(next_marker).addClass('active');

        $('.psp-timeline--bar span').width( progress + '%' );

    });

    if( $('#psp-projects').hasClass('psp-fe-manage-page-new') && !$('body').hasClass('psp-fe-edit-project') ) {

        var button = $('#psp-projects.psp-fe-manage-page-new .psp-fe-wizard form.acf-form input[type=submit]').detach();
        $(button).insertBefore('.psp-wizard-next-button');
        $(button).click(function(e) {
            $('.psp-fe-wizard form.acf-form').submit();
        });

        $('.psp-fe-wizard .acf-tab-wrap').find('li:first-child').find('a').click();

    }

    if( $('#psp-projects').hasClass('psp-fe-edit-project') ) {

        /**
         * This is an edit page, do your magic!
         * @type {String}
         */

         if( $('#psp-projects').hasClass('psp-fe-edit-project-status-new') ) {
             /**
              * This is the step two, defaulting to milestones
              * @type {String}
              */
             $('#acf-milestones').show();
         }

         $('.field-repeater-toggle-all').click();

         var button = $('.psp-fe-wizard input[type="submit"]').detach();

         $(button).insertAfter('.psp-wizard-edit-prev-button');
         $(button).click(function(e) {
             $('.psp-fe-wizard form.acf-form').submit();

         });

    }

    $('.psp-timeline .psp-timeline--marker a').click(function(e) {

        e.preventDefault();

        psp_fe_hide_all_sections();

        var marker    = $(this).parent();
        var section   = $(marker).data('target');
        var title     = $(marker).data('title');
        var type      = $(marker).data('type');
        var progress  = $(marker).data('complete');

        if( $(marker).is(':first-child') ) {
            $('.psp-wizard-edit-prev-button').addClass('is-inactive');
        } else {
             $('.psp-wizard-edit-prev-button').removeClass('is-inactive');
        }
        if( $(marker).is(':last-child') ) {
            $('.psp-wizard-edit-next-button').addClass('is-inactive');
       } else {
            $('.psp-wizard-edit-next-button').removeClass('is-inactive');
       }

        $('.timeline-title').text(title);
        $( '#' + section ).show();

        if( type == 'tab' ) {
            var index = $(marker).index();
            var tab = $('.acf-tab-group li').get(index);
            $(tab).find('a').click();
        }

        $('.psp-timeline--bar span').width( progress + '%' );
        $('.psp-timeline--marker').removeClass('active');

        $('.psp-timeline .psp-timeline--marker').removeClass('active');
        // For context marking
        $(marker).addClass('active').prevAll('.psp-timeline--marker').addClass('completed');

        if( $(marker).is(':first-child') ) {
            $('.psp_fe_title_fields').show();
        } else {
            $('.psp_fe_title_fields').hide();
        }

    });

    function psp_fe_hide_all_sections() {

        $('.psp-timeline .psp-timeline--marker').each(function() {
            var target = $(this).data('target');
            $(this).removeClass('active');
            $('#'+target).hide();
        });

    }

    /*
    if( $('body').hasClass('psp-fe-manage-template') && !$('body').hasClass('psp-acf-ver-5') ) {

        $('.psp-fe-wizard select').each(function() {
            $(this).wrap('<div class="psp-select-wrapper"></div>');
        });

   } */

    $('body').on( 'click', '.js-psp-fe-set-dates', function(e) {

        e.preventDefault();

        $('.psp-fe-edit-date-input').parent().addClass('psp-is-editing');
        $('.psp-fe-edit-date-input').show();
        $(this).hide();

    });

    if( $('select[data-name="field-assigned"]').length ) {
         $('select[data-name="field-assigned"]').select2();
    }

    $('body').on( 'click', '.js-psp-edit-date', function(e) {

        e.preventDefault();

        $('.psp-fe-edit-date-input').parent().addClass('psp-is-editing');
        $('.psp-fe-edit-date-input').show();

        $('.psp-the-start-date, .psp-the-end-date').hide();

    });

    $('body').on( 'submit', '.psp-js-date-update', function(e) {

        e.preventDefault();

        var input_element = $(this).find('.psp-fe-project-date-field');

        var start_date = $(this).find('[name="psp-start-date"]').val();
        var end_date   = $(this).find('[name="psp-end-date"]').val();
        var post_id    = $(this).find('[name="psp-post-id"]').val();

        psp_fe_update_date( start_date, end_date, post_id );

        $('.psp-fe-edit-date-input').hide();

        $('.psp-overview__dates').removeClass('psp-fe-hide');

    });



    $('body').on( 'click', '.js-psp-edit-description', function(e) {

        e.preventDefault();

        if( typeof(tinyMCE) !== 'undefined' ) {

            if( tinymce.get('field-description') ) {

                 // tinymce.get('field-description').setContent('');

            } else {

                tinymce.init({
                    selector: 'textarea[name="description"]',
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

    });

    $('body').on( 'click', '.js-psp-edit-phase', function(e) {

        e.preventDefault();

        var ajaxurl     = jQuery('#psp-ajax-url').val();
        var phase_index = $(this).data('phase_index');
        var phase_id    = $(this).parents('.psp-phase').data('phase_id');

        var post_id     = $(this).data('post_id');
        var modal = $('#psp-edit-phases-modal');

        $(modal).find('input[name="callback"]').val('edit');
        $(modal).find('.add-phase-heading').hide();
        $(modal).find('.edit-phase-heading').show();

        $(modal).addClass('psp-is-loading');

        $.ajax({
            url  :  ajaxurl + "?action=psp_fe_populate_edit_phase_modal",
            type :  'post',
            data : {
                phase_index : phase_index,
                phase_id    : phase_id,
                post_id     : post_id
            },
            success: function( response, success ) {

                $(modal).find('input[name="phase-title"]').val( response.data.title );
                $(modal).find('input[name="' + response.data.progressfield + '"]').val( response.data.progress );
                $(modal).find('input[name="phase_index"]').val( phase_index );
                $(modal).find('input[name="phase_id"]').val( phase_id );

                $(modal).find('textarea[name="phase-description"]').val( response.data.description );

                if( typeof(tinyMCE) !== 'undefined' ) {

                    if( tinymce.get('field-phase-description') ) {

                        tinymce.get('field-phase-description').setContent( response.data.description );

                    } else {

                        tinymce.init({
                            selector: 'textarea[name="phase-description"]',
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

                $(modal).removeClass('psp-is-loading');

            },
            error: function( response ) {

                $('.psp-fe-template-form .message').html( response.data.message ).show();

            }
        });

    });

    $('body').on( 'submit', '.js-psp-edit-description-form', function(e) {

        e.preventDefault();

        var ajaxurl = jQuery('#psp-ajax-url').val();

        if( typeof(tinyMCE) !== 'undefined' ) {
            var description = tinyMCE.activeEditor.getContent();
            $(this).find('textarea[name="description"]').val(description);
        }

        var data = $(this).serialize();
        var form = $(this);
        var modal = $(form).parents('.psp-modal');
        var post_id = $(this).find('input[name="post_id"]').val();

        $(modal).addClass('psp-is-loading');

        $.ajax({
            url  :  ajaxurl + "?action=psp_fe_update_description",
            type :  'post',
            data :  data,
            success: function( response, success ) {

                response.data.modify.forEach( psp_fe_modify_frontend );


                $(modal).removeClass('psp-is-loading');
                $(modal).find('.modal-close').click();

            },
            error: function( response ) {

                $('.psp-fe-template-form .message').html( response.data.message ).show();

            }
        });


    });

    $('body').on( 'submit', '.js-psp-edit-phases-form', function(e) {

        e.preventDefault();

        var ajaxurl = jQuery('#psp-ajax-url').val();

        if( typeof(tinyMCE) !== 'undefined' ) {
            var description = tinyMCE.activeEditor.getContent();
            $(this).find('textarea[name="phase-description"]').val(description);
        }

        var data    = $(this).serialize();
        var form    = $(this);
        var modal   = $(this).parents('.psp-modal');
        var type    = $(this).find('input[name="callback"]').val();
        var callback  = 'psp_fe_update_phase';

        if( type == 'add' ) {
            callback = 'psp_fe_add_phase';
        }

        var post_id = $(this).find('input[name="post_id"]').val();

        $(modal).addClass('psp-is-loading');

        $.ajax({
            url  :  ajaxurl + "?action=" + callback,
            type :  'post',
            data :  data,
            success: function( response, success ) {

                response.data.modify.forEach( psp_fe_modify_frontend );

                if( ( jQuery(window).width() > 768 ) && ( ! jQuery('#psp-projects').hasClass('psp-width-single') ) ) {
                    jQuery('.psp-phase-info').css('height','auto');
                    pspEqualHeight( jQuery('.psp-phase-info') );
                }

                /**
                 * Reset the stats!
                 * @param  {[type]} psp [description]
                 * @return {[type]}     [description]
                 */

                $('#psp-projects').removeClass('psp-has-modal');

                psp_fe_reinit_phases();

                psp_fe_update_total_progress( response.data.progress, post_id );

                $(modal).removeClass('psp-is-loading');
                $(modal).find('.modal-close').click();

            },
            error: function( response ) {

                $('.psp-fe-template-form .message').html( response.data.message ).show();

            }
        });

    });

    /**
     * Add New Phase
     * @param  {[type]} e [description]
     * @return {[type]}   [description]
     */

    $(document).on( 'click', '.js-psp-fe-add-phase', function(e) {

        var phase_index = $(this).data('phase_index');
        var phase_id    = $(this).parents('.psp-phase').data('phase_id');
        // Prepare modal

        $('#psp-edit-phases-modal').find('input[name="callback"]').val('add');
        $('#psp-edit-phases-modal').find('input[name="phase_index"]').val( phase_index );
        $('#psp-edit-phases-modal').find('input[name="phase_id"]').val(phase_id);

        $('#psp-edit-phases-modal').find('.edit-phase-heading').hide();
        $('#psp-edit-phases-modal').find('.add-phase-heading').show();

        $('#psp-edit-phases-modal').find('input[type="text"]').val('');
        $('#psp-edit-phases-modal').find('input[type="number"]').val('');

        if( typeof(tinyMCE) !== 'undefined' ) {

            if( tinymce.get('field-phase-description') ) {

                tinymce.get('field-phase-description').setContent('');

            } else {

                tinymce.init({
                    selector: 'textarea[name="phase-description"]',
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

    });

    $( document ).on('click', '.psp-del-phase-link', function(e) {

        e.preventDefault();

        var result = confirm( $(this).data('confirmation') );

        if( result == true ) {

            var form = $(this).parents('form');
            var task_id = $(form).find('input[name="phase_id"]');

            var data = {
                'phase_id'  : $(form).find('input[name="phase_id"]').val(),
                'post_id'   : $(form).find('input[name="post_id"]').val()
            };

            psp_fe_delete_phase( data.post_id, data.phase_id, form );

        }

    });

});

function psp_fe_delete_phase( post_id, phase_id, form ) {

    var ajaxurl = jQuery('#psp-ajax-url').val();

    jQuery.ajax({
        url     :   ajaxurl + "?action=psp_fe_delete_phase",
        type    :   'post',
        data:   {
            post_id  : post_id,
            phase_id : phase_id,
        },
        success: function( response ) {

            jQuery('.psp-phase[data-phase_id="' + phase_id + '"]').slideUp('fast');

            jQuery('#psp-projects').removeClass('psp-has-modal');

            jQuery(form).find('.modal-close').click();

        },
        error: function() {
            alert( 'There was a problem deleting this phase.' );
        }
    });

}

function psp_fe_reinit_phases() {


    // Init the modals
    psp_fe_init_modals();

    // Datepickers
    psp_init_datepickers();

    // Document status
    jQuery('.js-pano-upload-file').leanModal({ closeButton: ".modal_close" });
    jQuery('#psp-phases').find('.doc-status').leanModal({ closeButton: "." });

}

function psp_fe_modify_frontend( item, index ) {

    if( item.method == 'replace' ) {
        jQuery( item.target ).replaceWith( item.markup );
        // Reset modals if needed
        jQuery( item.target ).find('.psp-modal-btn').leanModal({ closeButton: '.modal-close' });
    }

    if( item.method == 'prepend' ) {
        jQuery( item.target ).prepend( item.markup );
    }

    if( item.method == 'append' ) {
        jQuery( item.target ).append( item.markup );
    }

    if( item.method == 'next' ) {
        jQuery( item.markup ).insertAfter( item.target );
    }

    if( item.method == 'prev' ) {
        jQuery( item.markup ).insertBefore( item.target );
    }

    if( item.method == 'replace_attribute' ) {
        jQuery( item.target ).attr( item.attribute, item.value );
    }

    if( item.method == 'html' ) {
        jQuery( item.target ).html( item.markup );
    }

}

function psp_fe_update_phase_progress( progress, phase_id ) {

     var phase_elm = jQuery( '.psp-phase[data-phase_id="' + phase_id + '"]' );
     var phase_index = jQuery(phase_elm).data('phase-index');

    if( typeof allCharts !== 'undefined' ) {
        allCharts[eval(phase_index)].segments[0].value = progress.completed;
        allCharts[eval(phase_index)].segments[1].value = progress.remaining;
        allCharts[eval(phase_index)].update();
    }

    var prev_remaining = 100 - progress.previous;

    // Update the details
    jQuery(phase_elm).find('.psp-chart-complete').html( progress.completed + '%');
    jQuery(phase_elm).attr( 'data-completed', progress.completed );
    jQuery(phase_elm).removeClass( 'psp-phase-complete-' + progress.previous ).addClass( 'psp-phase-complete-' + progress.completed )
                     .removeClass( 'psp-phase-remaining-' + prev_remaining ).addClass( 'psp-phase-remaining-' + progress.remaining );

}

function psp_fe_get_phase_completion( post_id, phase_id ) {

    var ajaxurl     = jQuery('#psp-ajax-url').val();

    jQuery.ajax({
        url     :   ajaxurl + "?action=psp_fe_get_phase_data",
        type    :   'post',
        data:   {
            post_id  : post_id,
            phase_id : phase_id,
        },
        success: function( success, response ) {

            // Update the circle graph
            if( typeof allCharts !== 'undefined' ) {
                allCharts[phase_id].segments[0].value = returned.data.completion;
                allCharts[phase_id].segments[1].value = returned.data.remaining;
                allCharts[phase_id].update();
            }

            // Update the details
            jQuery('#phase-' + phase_id + ' .psp-chart-complete').html(returned.data.completion + '%');
            jQuery('#phase-' + phase_id + ' .task-list-toggle span').html(returned.data.tasks_list_string);
            jQuery('#phase-' + phase_id + ' .psp-top-complete span.percentage').html(returned.data.completion + '%');
            jQuery('#phase-' + phase_id + ' .psp-top-complete span.count').html(returned.data.count_string);
            jQuery('#phase-' + phase_id + ' .psp-phase-overview').removeClass().addClass('psp-phase-overview cf psp-phase-progress-' + returned.data.completion);

        },
        error: function() {
            alert( 'There was a problem updating this phase.' );
        }
    });

    /*
    // Update all indications of completion in the phase (function)
    psp_fe_update_phase_completion_indicators( phaseID, completion, tasks_completed );

    if( ( jQuery(window).width() > 768 ) && ( ! jQuery('#psp-projects').hasClass('psp-width-single') ) ) {
		jQuery('.psp-phase-info').css('height','auto');
		pspEqualHeight( jQuery('.psp-phase-info') );
    }
    */

}

function psp_fe_update_phase_completion_indicators( phaseID, completion, tasks_completed ) {

    jQuery('#phase-'+phaseID+' .psp-chart-complete').html(completion + '%');
    jQuery('#phase-'+phaseID+' .task-list-toggle span b').html(tasks_completed);
    jQuery('#phase-'+phaseID+' .psp-top-complete span.percentage').html(completion + '%');
    jQuery('#phase-'+phaseID+' .psp-top-complete span.count span.completed').html(tasks_completed);
    jQuery('#phase-'+phaseID+' .psp-phase-overview').removeClass().addClass('psp-phase-overview cf psp-phase-progress-' + completion);

}

function psp_fe_update_progress_bar( progress ) {

    jQuery('.psp-progress span').removeClass()
                                .addClass( 'psp-' + progress )
                                .html('<b>' + progress + '%</b>')
                                .attr( 'data-original-title', progress + '%' );

}

function psp_fe_update_milestones( progress ) {

    /*
     * Reset milestones
     */
    jQuery('.psp-enhanced-milestone').removeClass('completed');
    jQuery('.psp-milestone-dot').removeClass('completed');

    jQuery('.psp-enhanced-milestone').each(function() {

        if( jQuery(this).data('milestone') <= progress ) {
            jQuery(this).addClass('completed');
        }

    });

    /*
     * Reset milestone dots
     */
    jQuery('.psp-milestone-dot').each(function() {

        if( jQuery(this).data('milestone') <= progress ) {
            jQuery(this).addClass('completed');

        }
    });

}

function psp_fe_update_total_progress( progress, post_id ) {

    // If this is where there is a task progress
    if( jQuery('.psp-task-project-' + post_id ).length) {

        jQuery('.psp-task-project-' + post_id + ' .psp-progress span').removeClass().addClass( 'psp-' + progress ).html( '<b>' + progress + '%</b>' );
        return;

    }

    psp_fe_update_progress_bar( progress );
    psp_fe_update_milestones( progress );

}

function psp_fe_get_total_progress( post_id ) {

	var ajaxurl = jQuery('#psp-ajax-url').val();

    jQuery.ajax({
        url: ajaxurl + "?action=psp_update_total_fe",
        type: 'post',
        data: {
            post_id : post_id,
        },
        success: function( progress ) {
            psp_fe_update_total_progress( progress, post_id )
        },
        error: function(data) {
            console.log(data);
        }
    });

}

function psp_fe_delete_task( post_id, phase_id, task_id ) {

    var ajaxurl = jQuery('#psp-ajax-url').val();

    jQuery.ajax({
        url: ajaxurl + "?action=psp_fe_delete_task",
        type: 'post',
        data: {
            post_id     : post_id,
		  phase_id: phase_id,
		  task_id: task_id,
        },
        success: function( response ) {

            // Update phase and total progress
            psp_fe_update_phase_progress( response.data.phase_progress, response.data.phase_id );
            psp_fe_update_total_progress( response.data.project_progress, post_id );

            var phase = jQuery('.psp-phase[data-phase_id="' + phase_id + '"]');

            // Update and replace items on screen
            jQuery(phase).find( '.psp-task-id-' + task_id ).slideUp('slow', function() {

                response.data.modify.forEach( psp_fe_modify_frontend );

                /*
                jQuery(phase).find('.psp-task-list').addClass('active').show();
                jQuery(phase).find('.task-list-toggle').addClass('active');
                jQuery(phase).find('.psp-fe-add-task').show();
                jQuery(phase).find('.psp-modal-btn').leanModal({ closeButton: ".modal-close" });
                */

               var phase = jQuery('.psp-phase[data-phase_id="' + phase_id + '"]');
               var phase_index = null;

               if( jQuery(phase).length ) {

                    phase_index = jQuery(phase).data('phase-index');

                    psp_update_my_task_count( post_id, phase_index );

               }

               var form = jQuery('.psp-frontend-form');

               jQuery(form).trigger('reset');
               // Trigger reset doesn't work
               psp_fe_reset_form(form);

               jQuery('.psp-add-task-modal').find('.modal-close').click();

			// Placed here so that it happens once the animation has completed
			jQuery( document ).trigger( 'psp-fe-deleted-task', [ phase_id, task_id ] );


            });

        },
        error: function( response ) {
            alert( response.message );
        }
    });


}

function psp_fe_update_date( start_date, end_date, post_id ) {

    var ajaxurl = jQuery('#psp-ajax-url').val();

    jQuery.ajax({
        url: ajaxurl + "?action=psp_fe_update_date",
        type: 'post',
        data: {
            post_id       : post_id,
            start_date    : start_date,
            end_date      : end_date,
        },
        success: function( response ) {

            console.log(response);

            if( response.data.success == false ) {
                alert( response.data.message );
                return;
            }

            jQuery('#psp-short-progress .psp-tb-progress span')
                .removeClass()
                .addClass( 'psp-' + response.data.dates.ellapsed )
                .attr( 'data-original-title', response.data.dates.title );

            jQuery('#psp-short-progress .psp-tb-progress b').html( response.data.dates.ellapsed + '%' );

            jQuery('#psp-short-progress .psp-tb-progress')
                .removeClass('psp-behind')
                .addClass('psp-tb-progress')
                .addClass(response.data.dates.class);

            response.data.modify.forEach( psp_fe_modify_frontend );

            jQuery('.psp-the-start-date,.psp-the-end-date').show();

            jQuery( document ).trigger( 'psp-fe-updated-date', [ start_date, end_date, response.data.dates.ellapsed ] );

        },
        error: function( response ) {
            alert( response.message );
        }
    });


}

function psp_fe_reset_form( form ) {
    jQuery(form).find('input[name="task_id"]').val('');
    jQuery(form).find('select[data-name="field-assigned"]').val( 0 ).trigger('change');

}

function psp_fe_init_modals() {

    if( jQuery('#psp-projects').hasClass('psp-fe-manage-template') || jQuery('#psp-projects').hasClass('psp-single-psp_pages') ) {
        return;
    }

    jQuery('.psp-modal-btn').leanModal({ closeButton: ".modal-close" });

    if( jQuery( '.psp-fe-notify-modal' ).length ) {
        jQuery('.psp-fe-notify-modal').leanModal({ closeButton: ".modal-close" });
    }

    if( jQuery('.js-psp-edit-phase').length ) {
        jQuery('.js-psp-edit-phase').leanModal({ closeButton: ".modal-close" });
    }

    if( jQuery('.js-psp-fe-add-phase').length ) {
        jQuery('.js-psp-fe-add-phase').leanModal({ closeButton: ".modal-close" });
    }

    if( jQuery('.js-psp-edit-description').length ) {
        jQuery('.js-psp-edit-description').leanModal({ closeButton: ".modal-close" });
    }
}

function psp_init_datepickers() {

    jQuery( '.psp-datepicker' ).each( function( index, field ) {

            if( jQuery( field ).val() != '' ) {

            var date = jQuery( field ).val().replace( /\D/g, '' ),
                month = date.substring( 0, 2 ),
                day = date.substring( 2, 4 ),
                year = date.substring( 4, 8 ),
                dateObject = new Date( year + '-' + month + '-' + day );

            // https://stackoverflow.com/a/16048201
            dateObject.setTime( dateObject.getTime() + dateObject.getTimezoneOffset() * 60 * 1000 );

            jQuery( field ).datepicker( 'setDate', dateObject );

        }

    } );
}

function psp_fe_hide_fields() {
   // Hide everything
   jQuery('.acf-field-53207efc069cb, .acf-field-5436eab7a2238, .acf-field-phase-percentage, .acf-field-527d5dd82fa2b').hide();
}

if( typeof(acf) != 'undefined' ) {

     acf.add_action('ready', function(e, postbox){

          // Trigger change on project automatic progress
          jQuery('#acf-field_52c46fa974b08-Yes').trigger('change');

          // trigger change on the automatic phase progress checkbox
          jQuery('#acf-field_5436e7f4e06b4-Yes').trigger('change');

          // Trigger change on progress calculation type change
          jQuery('#acf-field_5436e85ee06b5').trigger('change');

          jQuery('#acf-field_5436e7f4e06b4-Yes').on('change', function(){
               psp_fe_reset_conditionals();
          });

          // Trigger change on progress calculation type change
          jQuery('#acf-field_5436e85ee06b5').on('change', function() {
               psp_fe_reset_conditionals();
          });

          psp_fe_reset_conditionals();

     });

}

function psp_fe_reset_conditionals() {

     psp_fe_hide_fields();

     // Phase progress setting
     var phase_progress = jQuery('.acf-field-5436e7f4e06b4 input[type="checkbox"]').prop('checked');
     var phase_progress_type = jQuery('.acf-field-5436e85ee06b5 select').val();

     if( phase_progress ) {

          if( phase_progress_type == 'Weighting' ) {

               jQuery('.acf-field-53207efc069cb').show().data( 'required', '1' );

          } else if ( phase_progress_type == 'Percentage' ) {

               jQuery('.acf-field-phase-percentage').show().data( 'required', '1' );
               jQuery('.acf-field-5436eab7a2238').data( 'required', '0' );
               jQuery('.acf-field-53207efc069cb').data( 'required', '0' );


          } else if ( phase_progress_type == 'Hours' ) {

               jQuery('.acf-field-5436eab7a2238').show().data( 'required', '1' );
               jQuery('.acf-field-53207efc069cb').data( 'required', '0' );
               jQuery('.acf-field-phase-percentage').data( 'required', '0' );

          }

     } else {

          jQuery('.acf-field-527d5dd82fa2b').show();

     }

}

function psp_fe_reorder_task( task ) {

     var ajaxurl = jQuery('#psp-ajax-url').val();

     var data = {
          post_id : jQuery(task).find('.psp-task-title').data('project'),
          task_id : jQuery(task).find('.psp-task-title').data('task_id'),
          phase_id : jQuery(task).find('.psp-task-title').data('phase_id'),
          index : task.index()
     }

     jQuery.ajax({
         url: ajaxurl + "?action=psp_fe_reorder_task",
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

function psp_fe_reorder_phase( phase ) {

     var data = {
          post_id : pano_post_id,
          phase_id : jQuery(phase).data('phase_id'),
          index: phase.index(),
     }

     jQuery.ajax({
         url: pano_ajax + "?action=psp_fe_reorder_phase",
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
