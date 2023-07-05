jQuery(document).ready(function($) {

    $('#psp-projects').on( 'click', '.js-edit-gallery-close', function(e) {

        e.preventDefault();

        var ajaxurl  = $('#psp-ajax-url').val();
        var post_id  = $(this).data('post_id');

        $('.psp-gallery-loading').show();

        $.ajax({
            url: ajaxurl + '?action=psp_update_gallery_fe',
            type: 'post',
            data: {
                post_id : post_id
            },
            success: function( response ) {
                $('#psp-gallery-container .psp-gallery-section').replaceWith( response.data.markup );
                $('.psp-gallery-frontend-wrapper').fadeOut('slow');
                $('.psp-gallery-loading').hide();
            }

        });

    });

    $('#psp-projects').on( 'click', '.js-edit-psp-gallery', function(e) {

        e.preventDefault();

        if( !$('#psp-gallery-iframe').length ) {
            var iframe_url = $('#psp-gallery-iframe-src').val();
            $('<iframe src="' + iframe_url + '"></iframe').insertAfter('#psp-gallery-iframe-src');
        }
        /*
        setTimeout(function() {
            $('#psp-gallery-iframe').load(function () {
                $(this).height($(this).contents().height());
                $(this).width($(this).contents().width());
                $('.psp-gallery-loading').hide();
            });
        }, 500 ); */

        $('.psp-gallery-frontend-wrapper').fadeIn('slow');

    });

});
