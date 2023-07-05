<?php
/**
 * Plugin Name: Project Panorama Gallery
 * Plugin URI: http://www.projectpanorama.com
 * Description: Add a photogallery to your project page
 * Version: 2.0.4
 * Author: SnapOrbital
 * Author URI: http://www.projectpanorama.com
 * License: GPL2
 * Text Domain: psp_projects
 */


// Constants

define( 'PANORAMA_GALLERY', 'Panorama Photo Gallery' );
define( 'PROJECT_PANORAMA_GALLERY_STORE_URL', 'https://www.projectpanorama.com' );
define( 'PANORAMA_GALLERY_VER', '2.0.4' );
define( 'PANO_GALLERY_PATH', plugin_dir_path( __FILE__ ) );
define( 'PANO_GALLERY_URL', plugin_dir_url( __FILE__ ) );


if( !class_exists( 'EDD_SL_Plugin_Updater' ) ) {
	// load our custom updater
	include( dirname( __FILE__ ) . '/EDD_SL_Plugin_Updater.php' );
}

/**
* Check to see if the gallery module is installed, if not activate it
*
* @return null
*
*/

add_action( 'psp_after_panorama_loaded', 'psp_init_gallery' );
function psp_init_gallery() {

	// Only works with pro versions
	if( !function_exists('psp_get_option') ) {
    		return false;
	}

	if( !class_exists( 'acf_field_gallery' ) ) {
		if( !function_exists( 'register_field_group' ) ) include_once( ABSPATH . 'wp-content/plugins/project-panorama/lib/vendor/acf/master/acf.php' );
		if( class_exists( 'acf_field' ) ) include_once( 'psp-gallery-module/acf-gallery.php' );
	}

	$reqs = array(
		'settings',
		'assets',
		'views'
	);

	foreach( $reqs as $req ) require_once( 'psp-gallery-' . $req . '.php' );

	// Load the custom fields
	include_once( 'panorama-gallery-fields.php' );

}

/**
 * Load any localization files after the plugins have loaded
 *
 * @return null
 *
 */

add_action( 'plugins_loaded', 'psp_gallery_localize_init' );
function psp_gallery_localize_init() {
	load_plugin_textdomain( 'psp_projects', false, dirname( plugin_basename( __FILE__ ) ) . '/lang' );
}


/**
 * Add a custom image size for nicer styling
 *
 * @return null
 *
 */

add_image_size( 'psp-gallery-thumbnail', 300, 300, true ); // (cropped)
add_image_size( 'psp-gallery-slideshow', 900, 350, false );

/**
 * Custom hook to inject the gallery into the page based on user selection
 *
 * @return null
 *
 */

add_action( 'init', 'psp_gallery_location' );
function psp_gallery_location( $query ) {

	// Only works with pro versions
	if( !function_exists('psp_get_option') ) {
		return false;
	}

	$location = psp_get_option( 'psp_gallery_location', 'overview' );

	if( empty( $location ) ) {
		$location = 'overview';
	}

	if( $location == 'overview' ) {
		add_action( 'psp_before_progress', 'psp_gallery_include' );
	}

	if( $location == 'progress' ) {
		add_action( 'psp_between_progress_phases', 'psp_gallery_include' );
	}

	if( $location == 'phases' ) {
		add_action( 'psp_between_phases_discussion', 'psp_gallery_include' );
	}

}


/**
 * Create our addon settings menu if it doesn't exist
 *
 * @return null
 *
 **/

if( !function_exists( 'panorama_addon_license_menu' ) ) {
	include( 'addon-license-menu.php' );
	add_action( 'admin_menu', 'panorama_addon_license_menu' );
}


/**
 * Add the license options to the settings page
 *
 * @return null
 *
 **/

// add_action( 'psp_addon_settings', 'panorama_gallery_license_page' );
function panorama_gallery_license_page() {

     $license 		= get_option( 'panorama_gallery_license_key' );
     $status 		= get_option( 'panorama_gallery_license_status' );
	$psp_gal_loc 	= get_option( 'psp_gallery_location' );
	$description	= get_option( 'psp_gallery_show_description' );
	?>

    <br>

    <hr>

    <br>

    <h3>Panorama Gallery</h3>

        <table class="form-table">
            <tbody>
            <tr valign="top">
                <th scope="row" valign="top">
                    <?php _e('Gallery License Key','psp_projects'); ?>
                </th>
                <td>
                    <input id="panorama_gallery_license_key" name="panorama_gallery_license_key" type="text" class="regular-text" value="<?php esc_attr_e( $license ); ?>" />
                    <label class="description" for="panorama_gallery_license_key"><?php _e('Enter your license key','psp_projects'); ?></label>
                </td>
            </tr>
            <?php if( false !== $license ) { ?>
                <tr valign="top">
                    <th scope="row" valign="top">
                        <?php _e('Activate License','psp_projects'); ?>
                    </th>
                    <td>
                        <?php if( $status !== false && $status == 'valid' ) { ?>
                            <span style="color:green;" class="psp-activation-notice"><?php _e('Active','psp_projects'); ?></span>
                            <?php wp_nonce_field( 'psp_panorama_nonce', 'psp_panorama_nonce' ); ?>
                            <input type="submit" class="button-secondary" name="psp_gallery_license_deactivate" value="<?php _e('Deactivate License','psp_projects'); ?>"/>
                        <?php } else { ?>
                            <span style="color:red;" class="psp-activation-notice"><?php _e('Inactive','psp_projects'); ?></span>
                            <?php wp_nonce_field( 'psp_panorama_nonce', 'psp_panorama_nonce' ); ?>
                            <input type="submit" class="button-secondary" name="psp_gallery_license_activate" value="<?php _e('Activate License','psp_projects'); ?>"/>
                        <?php } ?>
                    </td>
                </tr>
            <?php } ?>
			<tr valign="top">
				<th scope="row" valign="top">
					<label for="psp_gallery_location"><?php _e('Gallery Location','psp_projects'); ?></label>
				</th>
				<td>
					<select id="psp_gallery_location" name="psp_gallery_location">
						<?php
						$options = array(
							'overview'	=>	__( 'After Overview', 'psp_projects' ),
							'progress'	=>	__( 'After Progress', 'psp_projects' ),
							'phases'	=>	__( 'After Phases', 'psp_projects' ),
							'shortcode' => __( 'Using [psp-gallery] shortcode', 'psp_projects' )
						);
						foreach( $options as $key => $title ): ?>
							<option value="<?php echo esc_attr($key); ?>" <?php if( $psp_gal_loc == $key ) echo 'selected'; ?>><?php echo esc_html($title); ?></option>
						<?php
						endforeach; ?>
					</select>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row" valign="top">
					<label for="psp_gallery_show_description"><?php _e('Show Image Descriptions','psp_projects'); ?></label>
				</th>
				<td>
					<select id="psp_gallery_show_description" name="psp_gallery_show_description">
						<option value="yes" <?php if($description == 'yes') { echo 'selected'; } ?>><?php _e('Yes','psp-projects'); ?></option>
						<option value="no" <?php if($description == 'no') { echo 'selected'; } ?>><?php _e('No','psp-projects'); ?></option>
					</select>
					<label for="psp_gallery_show_description"><?php esc_html_e( 'Hiding the description will make photos larger', 'psp-projects' ); ?></label>
				</td>
			</tr>
        </table>

<?php }

add_action('admin_init', 'panorama_gallery_options');
function panorama_gallery_options() {

    // creates our settings in the options table
    register_setting('psp_addon_settings','panorama_gallery_license_key', 'panorama_gallery_sanitize_license');
	register_setting('psp_addon_settings','psp_gallery_location');
	register_setting('psp_addon_settings','psp_gallery_show_description');


    add_option('panorama_gallery_license_status');

}

function panorama_gallery_sanitize_license( $new ) {

    $old = get_option( 'panorama_gallery_license_key' );

    if( $old && $old != $new ) {
        delete_option( 'panorama_gallery_license_status' ); // new license has been entered, so must reactivate
    }

    return $new;
}

add_action( 'admin_init', 'panorama_gallery_plugin_updater' );
function panorama_gallery_plugin_updater() {

    // retrieve our license key from the DB
    $license_key = trim( get_option( 'panorama_gallery_license_key' ) );

    // setup the updater
    $pano_gallery_updater = new EDD_SL_Plugin_Updater( PROJECT_PANORAMA_GALLERY_STORE_URL, __FILE__, array(
            'version' 	=> PANORAMA_GALLERY_VER, 				// current version number
            'license' 	=> $license_key, 		// license key (used get_option above to retrieve from DB)
            'item_name' => PANORAMA_GALLERY, 	// name of this plugin
            'author' 	=> '37 Media',  // author of this plugin
            'url'       => home_url()
        )
    );

}

// add_action('admin_init', 'panorama_gallery_activate_license',0);
function panorama_gallery_activate_license() {

    // listen for our activate button to be clicked
    if( isset( $_POST['psp_gallery_license_activate'] ) ) {

        // run a quick security check
        if( ! check_admin_referer( 'psp_panorama_nonce', 'psp_panorama_nonce' ) )
            return; // get out if we didn't click the Activate button

        // retrieve the license from the database
        $license = trim( get_option( 'panorama_gallery_license_key' ) );


        // data to send in our API request
        $api_params = array(
            'edd_action'=> 'activate_license',
            'license' 	=> $license,
            'item_name' => urlencode( PANORAMA_GALLERY ), // the name of our product in EDD
            'url'   => home_url()
        );

        // Call the custom API.
        $response = wp_remote_get( add_query_arg( $api_params, PROJECT_PANORAMA_GALLERY_STORE_URL ), array( 'timeout' => 15, 'sslverify' => false ) );

        // make sure the response came back okay
        if ( is_wp_error( $response ) )
            return false;

        // decode the license data
        $license_data = json_decode( wp_remote_retrieve_body( $response ) );

        // $license_data->license will be either "active" or "inactive"

        update_option( 'panorama_gallery_license_status', $license_data->license );

    }

}

// add_action('admin_init', 'panorama_gallery_deactivate_license');
function panorama_gallery_deactivate_license() {

    // listen for our activate button to be clicked
    if( isset( $_POST['psp_gallery_license_deactivate'] ) ) {

        // run a quick security check
        if( ! check_admin_referer( 'psp_panorama_nonce', 'psp_panorama_nonce' ) )
            return; // get out if we didn't click the Activate button

        // retrieve the license from the database
        $license = trim( get_option( 'panorama_gallery_license_key' ) );


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

add_filter( 'psp_fe_acf4_field_groups', 'panorama_gallery_front_end_editor_groups' );
add_filter( 'psp_fe_acf5_field_groups', 'panorama_gallery_front_end_editor_groups' );
function panorama_gallery_front_end_editor_groups( $field_groups ) {

	if( get_query_var( 'psp_manage_page' ) == 'edit' ) $field_groups[] = 'acf_project-gallery';

	return $field_groups;

}
