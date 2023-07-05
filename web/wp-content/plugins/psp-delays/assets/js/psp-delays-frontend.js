jQuery(function($) {

    $('.psp-delays-table').on( 'click', '.psp-delay-cancel', function(e) {
        e.preventDefault();
        psp_delays_table_reset();
    });

    function psp_delays_table_reset() {
        $('.psp-delay-active-edit').remove();
        $('.psp-delays-table tr.psp-editing').removeClass( 'psp-editing' );
    }

    $('.psp-delays-table').on( 'click', '.psp-delay-update', function(e) {

        e.preventDefault();

        var parent      = $(this).parents('tr');
        var delay_id    = $(parent).data('postid');
        var ajax_url    = $('#psp-ajax-url').val();
        var project_id  = $('#psp-project-id').val();
        var nonce       = $(parent).data('nonce');

        var date        = $(parent).find('input.psp-delay-date').val();
        var days        = $(parent).find('input.psp-delay-days').val();
        var description = $(parent).find('textarea.psp-delay-description').val();

        $.ajax({
            url : ajax_url + '?action=psp_update_delay_fe',
            type: 'post',
            data: {
                project_id      : project_id,
                delay_id        : delay_id,
                nonce           : nonce,
                date            : date,
                days            : days,
                description     : description,
            },
            success: function( response ) {

                var edit_row = $('.psp-delays-table tr.psp-editing' );
                $(edit_row).find( '.psp-delay-date-td small' ).html( date );
                $(edit_row).find( '.psp-delay-days-td' ).html( days );
                $(edit_row).find( '.psp-delay-descr-td' ).html( description );

                $(parent).remove();
                $(edit_row).removeClass( 'psp-editing' );

                $('#psp-time-overview .psp-archive-list-dates:last-child p').html( response.data );

            }
        });

    });

    $('.psp-delay-edit').click(function(e) {

        e.preventDefault();

        if( $('.psp-delays-table tr.psp-editing').length ) {
            psp_delays_table_reset();
        }

        // Grab the parent and populate
        var parent      = $(this).parents('tr');
        var postid      = $(parent).data('postid');
        var date        = $(parent).data('date');
        var days        = $(parent).data('days');
        var description = $(parent).data('description');

        var clone = $(this).parents('tbody').find('.psp-delay-clone-row').clone();
        $( clone ).removeClass( 'psp-hide' ).removeClass('psp-delay-clone-row').addClass('psp-delay-active-edit');

        $(clone).data('postid', postid );
        $(clone).find('input.psp-delay-date').val( date );
        $(clone).find('input.psp-delay-days').val( days );
        $(clone).find('textarea.psp-delay-description').val( description );

        $(parent).after( clone );

        $('.psp-delay-clone-row .psp-datepicker').datepicker();

        $(parent).addClass( 'psp-editing' );

    });

    $('.psp-delay-delete').click(function(e) {

        e.preventDefault();

        confirm( psp_delete_confirmation_message );

        var parent      = $(this).parents('tr');
        var delay_id    = $(this).parents('tr').data('postid');
        var nonce       = $(this).data('nonce');
        var ajax_url    = $('#psp-ajax-url').val();
        var project_id  = $('#psp-project-id').val();

        $.ajax({
            url : ajax_url + '?action=psp_delete_delay_fe',
            type: 'post',
            data: {
                project_id      : project_id,
                delay_id        : delay_id,
                nonce           : nonce
            },
            success: function( response ) {

                $('#psp-time-overview .psp-archive-list-dates:last-child p').html( response.data );
                $(parent).fadeOut( 'slow' );

            }
        });

    });

    $('.psp-delays-submit').click(function() {

        var date_occured    = $('#psp-date-occured').val();
        var days_delayed    = $('#psp-days-delayed').val();
        var description     = $('#psp-delays-description').val();
        var project_id      = $('#psp-project-id').val();
        var ajax_url        = $('#psp-ajax-url').val();

        $.ajax({
            url : ajax_url + '?action=psp_add_delay_fe',
            type: 'post',
            data: {
                project_id      : project_id,
                description     : description,
                days_delayed    : days_delayed,
                date_occured    : date_occured
            },
            success: function( response ) {

                /**
                 * Deal with the open modal
                 */
                $('#psp-delays-modal-form .success-message').fadeIn( 'slow' );
                $('#psp-delays-modal-form').parents('.psp-modal').find('.psp-modal-actions').slideUp( 'slow' );
                $('#psp-delays-modal-form').find('ol').slideUp( 'slow' );

                /**
                 * Redraw the end date
                 *
                 */
                $('#psp-time-overview .psp-archive-list-dates:last-child p').html( response.data.markup );

                /**
                 * Adjust the timing bar
                 */
                 $('.psp-simplified-timebar span').removeClass().addClass('psp-'+response.data.timing).html('<b>'+response.data.timing+'%</b>');

                /**
                 * Update the delays information
                 */
                $('.psp-delays-link').show();

                var count = parseInt( $('.psp-delays-link span.delays-value').data('count') );
                count++;
                $('.psp-delays-link span.delays-value').data( 'count', count ).text( count );

                setTimeout(function() {

                    $('#psp-delays-modal').fadeOut( 'slow', function() {

                         $('#psp-delays-modal').find('.modal-close').click();

                        psp_reset_delays_modal();

                    });

                }, 1500 );

            },
            error: function( data ) {

                $('#psp-delays-modal-form .error-message').fadeIn( 'slow' );

            }
        })

    });

    function psp_reset_delays_modal() {

        $('#psp-delays-modal-form .success-message').hide();
        $('#psp-delays-modal-form .error-message').hide();
        $('#psp-delays-modal-form').parents('.psp-modal').find('.psp-modal-actions').show();
        $('#psp-delays-modal-form #psp-date-occured').val('');
        $('#psp-delays-modal-form #psp-days-delayed').val('');
        $('#psp-delays-modal-form #psp-delays-description').val('');
        $('#psp-delays-modal-form .psp-form-fields').show();

    }

});
