<?php
add_filter( 'psp_settings_sections_addons', 'psp_gallery_settings_section' );
function psp_gallery_settings_section( $sections ) {

    $sections[ 'psp_gallery_settings' ] = __( 'Image Gallery', 'psp-projects' );

    return $sections;

}

add_filter( 'psp_settings_addons', 'psp_gallery_settings' );
function psp_gallery_settings( $settings ) {

     $psp_gallery_settings['psp_gallery_settings'] = array(
          'psp_gallery_title' => array(
               'id'      => 'psp_gallery_title',
               'name'    => '<h2>' . __( 'Image Gallery', 'psp-projects' ) . '</h2>',
               'type'    => 'html',
          ),
          'psp_gallery_license'    =>   array(
               'id'    => 'psp_gallery_license',
               'name'  => __( 'License Key', 'psp-projects' ),
               'desc'  => __( 'Enter your license key, save and then activate.', 'psp-projects' ),
               'type'  => 'text',
          ),
          'psp_gallery_activate_license' => array(
              'id'    =>  'psp_gallery_activate_license',
              'name'  =>  __( 'Activate License', 'psp-projects' ),
              'type'  =>  'gallery_license_key'
          ),
          'psp_gallery_location' => array(
               'id'   => 'psp_gallery_location',
               'name'    => __( 'Gallery Location', 'psp-projects' ),
               'type'    =>  'select',
               'options'  => array(
                    'overview'	=>	__( 'After Overview', 'psp-projects' ),
                    'progress'	=>	__( 'After Progress', 'psp-projects' ),
                    'phases'	=>	__( 'After Phases', 'psp-projects' ),
                    'shortcode' => __( 'Using [psp-gallery] shortcode', 'psp-projects' )
               )
          ),
          'psp_gallery_show_description' => array(
               'id'      =>   'psp_gallery_show_description',
               'name'    =>   __( 'Show image description', 'psp-projects' ),
               'type'    =>   'select',
               'options'   =>   array(
                    'yes'     =>   __( 'Yes', 'psp-projects' ),
                    'no'      =>  __( 'No', 'psp-projects' )
               )
          )
     );

     return array_merge( $settings, $psp_gallery_settings );

}


function psp_gallery_license_key_callback( $args ) {

    $status = get_option('panorama_gallery_license_status');

    $html   = '';

    if( isset( $_GET['psp_gallery_activate_response'] ) ) :
        ob_start(); ?>
            <div class="psp-status-message">
                <?php var_dump( psp_gallery_check_activation_response() ); ?>
            </div>
        <?php
        $html .= ob_get_clean();
    endif;

    if ( $status !== false && $status == 'valid' ) {
        $html .= '<span style="color:green;" class="psp-activation-notice">' . __( 'Active', 'psp-front-edit' ) . '</span> ';
        $html .= wp_nonce_field( 'psp_gallery_nonce', 'psp_gallery_nonce', true, false );
        $html .= '<input type="submit" class="button-secondary" name="psp_gallery_license_deactivate" value="' . __('Deactivate License','psp-front-edit') . '"/>';
    }
    else {
        $html .= '<span style="color:red;" class="psp-activation-notice">' . __( 'Inactive', 'psp-front-edit' ) . '</span> ';
        $html .= wp_nonce_field( 'psp_gallery_nonce', 'psp_gallery_nonce', true, false );
        $html .= ' <input type="submit" class="button-secondary" name="psp_gallery_license_activate" value="'. __('Activate License','psp-front-edit') . '"/>';
        $html .= '<a class="button" href="' . admin_url() . 'options-general.php?page=panorama-license&tab=psp_settings_addons&psp_gallery_activate_response=true">' . __( 'Check Activation Message', 'psp_projects' ) . '</a>';

    }

    echo $html;

}

function psp_gallery_activate_license() {

	// listen for our activate button to be clicked
	if( isset( $_POST['psp_gallery_license_activate'] ) ) {
		// run a quick security check
	 	if( ! check_admin_referer( 'psp_gallery_nonce', 'psp_gallery_nonce' ) )
			return; // get out if we didn't click the Activate button
		// retrieve the license from the database
        $psp_settings = get_option('psp_settings');

		$license = trim( $psp_settings['psp_gallery_license'] );
		// data to send in our API request
		$api_params = array(
			'edd_action' => 'activate_license',
			'license'    => $license,
			'item_name'    => PANORAMA_GALLERY, // The ID of the item in EDD
			'url'        => home_url()
		);
		// Call the custom API.
		$response = wp_remote_post( PROJECT_PANORAMA_GALLERY_STORE_URL, array( 'timeout' => 15, 'sslverify' => false, 'body' => $api_params ) );
		// make sure the response came back okay
		if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {
			$response_error_message = $response->get_error_message();
			$message =  ( is_wp_error( $response ) && ! empty( $response_error_message ) ) ? $response->get_error_message() : __( 'An error occurred, please try again.' );
		} else {
			$license_data = json_decode( wp_remote_retrieve_body( $response ) );
			if ( false === $license_data->success ) {
				switch( $license_data->error ) {
					case 'expired' :
						$message = sprintf(
							__( 'Your license key expired on %s.' ),
							date_i18n( get_option( 'date_format' ), strtotime( $license_data->expires, current_time( 'timestamp' ) ) )
						);
						break;
					case 'revoked' :
						$message = __( 'Your license key has been disabled.' );
						break;
					case 'missing' :
						$message = __( 'Invalid license.' );
						break;
					case 'invalid' :
					case 'site_inactive' :
						$message = __( 'Your license is not active for this URL.' );
						break;
					case 'item_name_mismatch' :
						$message = sprintf( __( 'This appears to be an invalid license key for %s.' ), PANORAMA_GALLERY );
						break;
					case 'no_activations_left':
						$message = __( 'Your license key has reached its activation limit.' );
						break;
					default :
						$message = __( 'An error occurred, please try again.' );
						break;
				}
			}
		}
		// Check if anything passed on a message constituting a failure
		if ( ! empty( $message ) ) {
			$base_url = admin_url( 'options-general.php?page=panorama-license&tab=psp_settings_addons&section=psp_gallery_settings' );
			$redirect = add_query_arg( array( 'sl_activation' => 'false', 'message' => urlencode( $message ) ), $base_url );
			wp_redirect( $redirect );
			exit();
		}
		// $license_data->license will be either "valid" or "invalid"
		update_option( 'panorama_gallery_license_status', $license_data->license );
		wp_redirect( admin_url( 'options-general.php?page=panorama-license&tab=psp_settings_addons&section=psp_gallery_settings' ) );
		exit();
	}
}
add_action('admin_init', 'psp_gallery_activate_license');


function psp_gallery_deactivate_license() {

	// listen for our activate button to be clicked
	if( isset( $_POST[ 'psp_gallery_license_deactivate' ] ) ) {

		// run a quick security check
	 	if( ! check_admin_referer( 'psp_gallery_nonce', 'psp_gallery_nonce' ) )
			return; // get out if we didn't click the Activate button

		// retrieve the license from the database
		$license = get_option( 'psp_settings' );
		$license = trim( $license[ 'psp_gallery_license' ] );


		// data to send in our API request
		$api_params = array(
			'edd_action'=> 'deactivate_license',
			'license' 	=> $license,
			'item_name' => urlencode( PANORAMA_GALLERY ) // the name of our product in EDD
		);

		// Call the custom API.
		$response = wp_remote_get( add_query_arg( $api_params, PROJECT_PANORAMA_GALLERY_STORE_URL ), array( 'timeout' => 15, 'sslverify' => false ) );

		// make sure the response came back okay
		if ( is_wp_error( $response ) )
			return false;

		// decode the license data
		$license_data = json_decode( wp_remote_retrieve_body( $response ) );

		// $license_data->license will be either "deactivated" or "failed"
		if( $license_data->license == 'deactivated' )
			delete_option( 'panorama_gallery_license_status' );

	}
}
add_action('admin_init', 'psp_gallery_deactivate_license');

function psp_gallery_check_activation_response() {

    $license = get_option( 'psp_settings' );
    $license = trim( $license[ 'psp_gallery_license' ] );

    // data to send in our API request
    $api_params = array(
        'edd_action'=> 'activate_license',
        'license' 	=> $license,
        'item_name' => urlencode( PANORAMA_GALLERY ), // the name of our product in EDD
        'url'   => home_url()
    );

    // Call the custom API.
    $response = wp_remote_get( add_query_arg( $api_params, PROJECT_PANORAMA_GALLERY_STORE_URL ), array( 'timeout' => 15, 'sslverify' => false ) );

    return $response;

}
