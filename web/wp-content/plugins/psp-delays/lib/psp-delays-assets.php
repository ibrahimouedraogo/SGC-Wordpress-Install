<?php
add_action( 'admin_enqueue_scripts', 'psp_delay_admin_scripts' );
function psp_delay_admin_scripts() {

    if( get_post_type() == 'psp_delays' || get_post_type() == 'psp_projects' ) {

        	wp_enqueue_script(
        			'psp-delays-admin',
        			PSP_DELAYS_BASE_URI . 'assets/js/psp-delays-admin.js',
        			array('jquery', 'jquery-ui-core', 'jquery-ui-datepicker'),
        			time(),
        			true
        		);
                // TODO: Localize to plugin

            wp_enqueue_style('jquery-style', '//ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/smoothness/jquery-ui.css');

            wp_register_style( 'psp-delays-admin', PSP_DELAYS_BASE_URI . 'assets/css/psp-delays-admin.css' );
            wp_enqueue_style( 'psp-delays-admin' );

    }

}

add_action( 'psp_head', 'psp_delays_frontend_assets' );
function psp_delays_frontend_assets() {

    psp_register_style( 'psp-delays', PSP_DELAYS_BASE_URI . 'assets/css/psp-delays.css', null, PSP_DELAYS_VER );
    psp_register_script( 'psp-delays-front', PSP_DELAYS_BASE_URI . 'assets/js/psp-delays-frontend.js', array( 'jquery' ), PSP_DELAYS_VER );

}

add_action( 'psp_head', 'psp_delays_javascript_messages' );
add_action( 'admin_head', 'psp_delays_javascript_messages' );
function psp_delays_javascript_messages() {

    if( ( is_admin() && get_post_type() == 'psp_projects' ) || !is_admin() ) { ?>

        <script>
            var psp_delete_confirmation_message = '<?php echo esc_js( __( 'Are you sure you want to delete this?', 'psp-delays' ) ); ?>';
        </script>

    <?php }

}
