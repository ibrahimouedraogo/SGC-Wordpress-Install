<?php
add_filter( 'psp_settings_sections_addons', 'psp_delays_settings_section' );
add_filter( 'psp_settings_addons', 'psp_delays_settings' );

function psp_delays_license_key_callback( $args ) {

    $status = get_option( 'psp_delays_license_key_status' );

    if ( $status !== false && $status == 'valid' ) {
        $html = '<span style="color:green;" class="psp-activation-notice">' . __( 'Active', 'psp-projects' ) . '</span>';
        $html .= wp_nonce_field( 'edd_panorama_nonce', 'edd_panorama_nonce', true, false );
        $html .= '<input type="submit" class="button-secondary" name="psp_delays_license_deactivate" value="' . __('Deactivate License','psp-projects') . '"/>';
    }
    else {
        $html = '<span style="color:red;" class="psp-activation-notice">' . __( 'Inactive', 'psp-front-edit' ) . '</span>';
        $html .= wp_nonce_field( 'edd_panorama_nonce', 'edd_panorama_nonce', true, false );
        $html .= ' <input type="submit" class="button-secondary" name="psp_delays_license_activate" value="'. __('Activate License','psp-projects') . '"/>';

    }

    echo $html;

}

function psp_delays_settings_section( $sections ) {

    $sections[ 'psp_delays_settings' ] = __( 'Delays', 'psp-projects' );

    return $sections;

}

// TODO: Change this so it's a license key
function psp_delays_settings( $settings ) {

    $psp_delays_settings[ 'psp_delays_settings' ] = array(
        'psp_delays_title'  => array(
            'id' => 'psp_delays_title',
            'name' => '<h2>' . __( 'Delays', 'psp-projects' ) . '</h2>',
            'type' => 'html',
        ),
        'psp_delays_license_key' => array(
            'id'    => 'psp_delays_license_key',
            'name'  => __( 'License Key', 'psp-projects' ),
            'desc'  => __( 'Enter your license key, save and then activate.', 'psp-projects' ),
            'type'  => 'text',
        ),
        'psp_delays_activate_license' => array(
            'id'    =>  'psp_delays_activate_license',
            'name'  =>  __( 'Activate License', 'psp-projects' ),
            'type'  =>  'delays_license_key'
        ),
        'psp_delays_only_weekdays'    =>  array(
            'id'    =>  'psp_delays_only_weekdays',
            'name'  =>  __( 'Only count week days', 'psp-projects' ),
            'type'  =>  'checkbox'
        )
    );

    return array_merge( $settings, $psp_delays_settings );

}

add_action( 'admin_init', 'psp_delays_activate_license', 0 );
function psp_delays_activate_license() {

    if( isset( $_POST[ 'psp_delays_license_activate' ] ) ) {
        // run a quick security check
	 	if( ! check_admin_referer( 'edd_panorama_nonce', 'edd_panorama_nonce' ) )
			return; // get out if we didn't click the Activate button

		// retrieve the license from the database
        $license = get_option( 'psp_settings' );
		$license = trim( $license[ 'psp_delays_license_key' ] );


		// data to send in our API request
		$api_params = array(
			'edd_action'=> 'activate_license',
			'license' 	=> $license,
			'item_name' => urlencode( PSP_DELAYS_ITEM_NAME ), // the name of our product in EDD
		    'url'   => home_url()
        );

		// Call the custom API.
		$response = wp_remote_get( add_query_arg( $api_params, PSP_DELAYS_STORE_URL ), array( 'timeout' => 15, 'sslverify' => false ) );

		// make sure the response came back okay
		if ( is_wp_error( $response ) )
			return false;

		// decode the license data
		$license_data = json_decode( wp_remote_retrieve_body( $response ) );

		// $license_data->license will be either "active" or "inactive"

		update_option( 'psp_delays_license_key_status', $license_data->license );

    }

}

function psp_delays_deactivate_license() {

	// listen for our activate button to be clicked
	if( isset( $_POST[ 'psp_delays_license_deactivate' ] ) ) {

		// run a quick security check
	 	if( ! check_admin_referer( 'edd_panorama_nonce', 'edd_panorama_nonce' ) )
			return; // get out if we didn't click the Activate button

		// retrieve the license from the database
		$license = get_option( 'psp_settings' );
		$license = trim( $license[ 'psp_delays_license' ] );


		// data to send in our API request
		$api_params = array(
			'edd_action'=> 'deactivate_license',
			'license' 	=> $license,
			'item_name' => urlencode( PSP_DELAYS_ITEM_NAME ) // the name of our product in EDD
		);

		// Call the custom API.
		$response = wp_remote_get( add_query_arg( $api_params, PSP_DELAYS_STORE_URL ), array( 'timeout' => 15, 'sslverify' => false ) );

		// make sure the response came back okay
		if ( is_wp_error( $response ) )
			return false;

		// decode the license data
		$license_data = json_decode( wp_remote_retrieve_body( $response ) );

		// $license_data->license will be either "deactivated" or "failed"
		if( $license_data->license == 'deactivated' )
			delete_option( 'psp_delays_license_key_status' );

	}
}
add_action('admin_init', 'psp_delays_deactivate_license');
