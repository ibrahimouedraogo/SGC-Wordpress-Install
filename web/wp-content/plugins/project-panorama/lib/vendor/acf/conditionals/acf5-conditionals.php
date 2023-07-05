<?php
function psp_acf5_custom_conditionals() {

    /*
    if( get_post_type() !== 'psp_projects' ) {
        return;
    } */

	?>
	<style type="text/css">
        .acf-field-hidden,
		.acf-field-53207efc069cb,
		.acf-field-5436eab7a2238,
		.acf-field-phase-percentage {
			display: none;
		}
	</style>
	<script type="text/javascript">
	(function($){




		/*
		*  hide_fields
		*
		*  @description: a small function to hide all the conditional fields
		*  @created: 17/07/12
		*/

		function psp_hide_fields() {

            // Hide everything
            $('.acf-field-53207efc069cb, .acf-field-5436eab7a2238, .acf-field-phase-percentage, .acf-field-527d5dd82fa2b').hide();

		}



		/*
		*  acf/setup_fields
		*  - Similar to $(document).ready, but runs after ACF has instantiated itself
		*/

		acf.add_action('ready', function(e, postbox){

            // Trigger change on project automatic progress
            $('.acf-field-52c46fa974b08 input[type="checkbox"]').trigger('change');

			// trigger change on the automatic phase progress checkbox
			$('.acf-field-5436e7f4e06b4 input[type="checkbox"]').trigger('change');

            // Trigger change on progress calculation type change
            $('.acf-field-5436e85ee06b5 select').trigger('change');

            psp_reset_conditionals();

		});

        function psp_reset_conditionals() {

            psp_hide_fields();

            // Phase progress setting
            var phase_progress = $('.acf-field-5436e7f4e06b4 input[type="checkbox"]').prop('checked');
            var phase_progress_type = $('.acf-field-5436e85ee06b5 select').val();

            if( phase_progress ) {

                if( phase_progress_type == 'Weighting' ) {

                    $('.acf-field-53207efc069cb').show().data( 'required', '1' );

                } else if ( phase_progress_type == 'Percentage' ) {

                    $('.acf-field-phase-percentage').show().data( 'required', '1' );
                    $('.acf-field-5436eab7a2238').data( 'required', '0' );
                    $('.acf-field-53207efc069cb').data( 'required', '0' );


                } else if ( phase_progress_type == 'Hours' ) {

                    $('.acf-field-5436eab7a2238').show().data( 'required', '1' );
                    $('.acf-field-53207efc069cb').data( 'required', '0' );
                    $('.acf-field-phase-percentage').data( 'required', '0' );

                }

            } else {

                $('.acf-field-527d5dd82fa2b').show();

            }

        }


		/*
		*  Hero Type change
		*/

		$('.acf-field-5436e7f4e06b4 input[type="checkbox"]').on('change', function(){
			psp_reset_conditionals();
		});

        // Trigger change on progress calculation type change
        $('.acf-field-5436e85ee06b5 select').on('change', function() {
            psp_reset_conditionals();
        });


	})(jQuery);
	</script>
	<?php
}

add_action('acf/input/admin_footer', 'psp_acf5_custom_conditionals');
