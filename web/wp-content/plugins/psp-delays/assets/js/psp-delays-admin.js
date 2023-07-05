jQuery(document).ready(function($) {

    $('.psp-add-delay').click(function(e){

        e.preventDefault();

        $(this).hide();
        $('.psp-add-delay-form').slideDown('slow');
        $('.psp-add-delay-form .required').prop( 'required', true );


    });

    $('.modal-close').click(function(e) {

        e.preventDefault();

        $('.psp-add-delay').show();
        $('.psp-add-delay-form').slideUp('slow');
        $('.psp-add-delay-form .required').prop( 'required', false );


    });

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
        var project_id  = $('#psp-project-id').val();
        var nonce       = $(parent).data('nonce');
        var date        = $(parent).find('input.psp-delay-date').val();
        var days        = $(parent).find('input.psp-delay-days').val();
        var description = $(parent).find('textarea.psp-delay-description').val();

        $.ajax({
            url : ajaxurl + '?action=psp_update_delay_fe',
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
                psp_recaculate_days_delayed();

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

        $('.psp-delay-active-edit .psp-delay-date').datepicker({
            'dateFormat' : 'mm/dd/yy'
        });

        $(parent).addClass( 'psp-editing' );

    });

    $('.psp-delay-delete').click(function(e) {

        e.preventDefault();

        confirm( psp_delete_confirmation_message );

        var parent      = $(this).parents('tr');
        var delay_id    = $(this).parents('tr').data('postid');
        var nonce       = $(this).data('nonce');
        var project_id  = $('#psp-project-id').val();

        $.ajax({
            url : ajaxurl + '?action=psp_delete_delay_fe',
            type: 'post',
            data: {
                project_id      : project_id,
                delay_id        : delay_id,
                nonce           : nonce
            },
            success: function( response ) {

                $(parent).fadeOut( 'slow' ).remove();
                psp_recaculate_days_delayed();

            }
        });

    });

    $('.psp-delays-submit').click(function(e) {

        e.preventDefault();

        var date_occured    = $('#psp-date-occured').val();
        var days_delayed    = $('#psp-days-delayed').val();
        var description     = $('#psp-delays-description').val();
        var project_id      = $('#psp-project-id').val();

        $.ajax({
            url : ajaxurl + '?action=psp_add_delay_fe',
            type: 'post',
            data: {
                project_id      : project_id,
                description     : description,
                days_delayed    : days_delayed,
                date_occured    : date_occured
            },
            success: function( response ) {

                $('.psp-add-delay-form .no-delays-row').hide();

                $('.psp-add-delay-form .success-message').fadeIn( 'slow' );
                $('.psp-add-delay-form').find('.psp-modal-actions').slideUp( 'slow' );
                $('.psp-add-delay-form').find('table').slideUp( 'slow' );

                var row =   '<tr data-post-id="' + response.data.post_id + '" data-date="' + date_occured + '" data-days="' + days_delayed + '">';
                row     +=       '<td class="psp-delay-date-td"><small>' + date_occured + '</small></td>';
                row     +=       '<td class="psp-text-center psp-delay-days-td">' + days_delayed + '</td>';
                row     +=       '<td class="psp-delay-descr-td">' + description + '</td>';
                row     +=       '<td class="psp-delay-actions-td"><a href="#" class="psp-delay-edit"><i class="fa fa-pencil"></i> Edit</a> <a href="#" class="psp-delay-delete" data-nonce="a632034273"><i class="fa fa-trash"></i> Delete</a></td>';
                row     +=   '</tr>';

                $('.psp-delays-table tbody').prepend( row );

                psp_recaculate_days_delayed();

                setTimeout(function() {

                    $('.psp-add-delay-form').fadeOut( 'slow', function() {
                        psp_reset_delays_modal();
                    });

                }, 1500 );

            },
            error: function( data ) {

                $('.psp-add-delay-form .error-message').fadeIn( 'slow' );

            }
        });

    });

    function psp_reset_delays_modal() {

        $('.psp-add-delay-form .success-message').hide();
        $('.psp-add-delay-form .error-message').hide();
        $('.psp-add-delay-form').find('.psp-modal-actions').show();
        $('.psp-add-delay-form #psp-date-occured').val('');
        $('.psp-add-delay-form #psp-days-delayed').val('');
        $('.psp-add-delay-form #psp-delays-description').val('');
        $('.psp-add-delay-form table').show();

    }

    function psp_recaculate_days_delayed() {

        var days = 0;

        $('.psp-delay-days-td').each(function() {

            days = days + parseInt( $(this).text() );

        });

        $('.psp-date-table-total-delayed').text( days );

    }

});
