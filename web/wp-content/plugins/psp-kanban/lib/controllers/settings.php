<?php
add_filter( 'psp_settings_sections_addons', 'psp_kb_settings_section' );
add_filter( 'psp_settings_addons', 'psp_kb_settings' );
function psp_kb_license_key_callback( $args ) {

    $status = get_option( 'psp_kb_license_key_status' );
    $html   = '';

    if( isset( $_GET['psp_kb_activate_response'] ) ) :
        ob_start(); ?>
            <div class="psp-status-message">
                <?php var_dump( psp_kb_check_activation_response() ); ?>
            </div>
        <?php
        $html .= ob_get_clean();
    endif;

    if ( $status !== false && $status == 'valid' ) {
        $html .= '<span style="color:green;" class="psp-activation-notice">' . __( 'Active', 'psp_projects' ) . '</span>';
        $html .= wp_nonce_field( 'edd_panorama_nonce', 'edd_panorama_nonce', true, false );
        $html .= '<input type="submit" class="button-secondary" name="psp_kb_license_deactivate" value="' . __('Deactivate License','psp_projects') . '"/>';
    }
    else {
        $html .= '<span style="color:red;" class="psp-activation-notice">' . __( 'Inactive', 'psp_projects' ) . '</span>';
        $html .= wp_nonce_field( 'edd_panorama_nonce', 'edd_panorama_nonce', true, false );
        $html .= ' <input type="submit" class="button-secondary" name="psp_kb_license_activate" value="'. __('Activate License','psp_projects') . '"/>';
        $html .= '<a class="button" href="' . admin_url() . 'options-general.php?page=panorama-license&tab=psp_settings_addons&psp_kb_activate_response=true">' . __( 'Check Activation Message', 'psp_projects' ) . '</a>';

    }

    echo $html;

}

function psp_kb_settings_section( $sections ) {

    $sections[ 'psp_kanban_settings' ] = __( 'Kanban', 'psp_projects' );

    return $sections;

}

// TODO: Change this so it's a license key
function psp_kb_settings( $settings ) {

    $psp_kb_settings[ 'psp_kanban_settings' ] = array(
        'psp_invoices_nag'  => array(
            'id' => 'psp_fe_title',
            'name' => '<h2>' . __( 'Kanban', 'psp_projects' ) . '</h2>',
            'type' => 'html',
        ),
        'psp_kb_license_key' => array(
            'id'    => 'psp_kb_license',
            'name'  => __( 'License Key', 'psp_projects' ),
            'desc'  => __( 'Enter your license key, save and then activate.', 'psp_projects' ),
            'type'  => 'text',
        ),
        'psp_kb_activate_license' => array(
            'id'    =>  'psp_kb_activate_license',
            'name'  =>  __( 'Activate License', 'psp_projects' ),
            'type'  =>  'kb_license_key'
        ),
    );

    return array_merge( $settings, $psp_kb_settings );

}



add_action( 'admin_init', 'psp_kb_activate_license', 0 );
function psp_kb_activate_license() {

    if( isset( $_POST[ 'psp_kb_license_activate' ] ) ) {
        // run a quick security check
	 	if( ! check_admin_referer( 'edd_panorama_nonce', 'edd_panorama_nonce' ) )
			return; // get out if we didn't click the Activate button

		// retrieve the license from the database
          $license = get_option( 'psp_settings' );
		$license = trim( $license[ 'psp_kb_license' ] );


		// data to send in our API request
		$api_params = array(
			'edd_action'=> 'activate_license',
			'license' 	=> $license,
			'item_name' => urlencode( PSP_KB_ITEM_NAME ), // the name of our product in EDD
		    'url'   => home_url()
        );

		// Call the custom API.
		$response = wp_remote_get( add_query_arg( $api_params, PSP_KB_STORE_URL ), array( 'timeout' => 15, 'sslverify' => false ) );

		// make sure the response came back okay
		if ( is_wp_error( $response ) )
			return false;

		// decode the license data
		$license_data = json_decode( wp_remote_retrieve_body( $response ) );

		// $license_data->license will be either "active" or "inactive"

		update_option( 'psp_kb_license_key_status', $license_data->license );

    }

}

function psp_kb_deactivate_license() {

	// listen for our activate button to be clicked
	if( isset( $_POST[ 'psp_kb_license_deactivate' ] ) ) {

		// run a quick security check
	 	if( ! check_admin_referer( 'edd_panorama_nonce', 'edd_panorama_nonce' ) )
			return; // get out if we didn't click the Activate button

		// retrieve the license from the database
		$license = get_option( 'psp_settings' );
		$license = trim( $license[ 'psp_kb_license' ] );


		// data to send in our API request
		$api_params = array(
			'edd_action'=> 'deactivate_license',
			'license' 	=> $license,
			'item_name' => urlencode( PSP_KB_ITEM_NAME ) // the name of our product in EDD
		);

		// Call the custom API.
		$response = wp_remote_get( add_query_arg( $api_params, PSP_KB_STORE_URL ), array( 'timeout' => 15, 'sslverify' => false ) );

		// make sure the response came back okay
		if ( is_wp_error( $response ) )
			return false;

		// decode the license data
		$license_data = json_decode( wp_remote_retrieve_body( $response ) );

		// $license_data->license will be either "deactivated" or "failed"
		if( $license_data->license == 'deactivated' )
			delete_option( 'psp_kb_license_key_status' );

	}
}
add_action('admin_init', 'psp_kb_deactivate_license');

function psp_kb_check_activation_response() {
    $license = get_option( 'psp_settings' );
    $license = trim( $license[ 'psp_kb_license' ] );


    // data to send in our API request
    $api_params = array(
        'edd_action'=> 'activate_license',
        'license' 	=> $license,
        'item_name' => urlencode( PSP_KB_ITEM_NAME ), // the name of our product in EDD
        'url'   => home_url()
    );

    // Call the custom API.
    $response = wp_remote_get( add_query_arg( $api_params, PSP_KB_STORE_URL ), array( 'timeout' => 15, 'sslverify' => false ) );

    return $response;

}
