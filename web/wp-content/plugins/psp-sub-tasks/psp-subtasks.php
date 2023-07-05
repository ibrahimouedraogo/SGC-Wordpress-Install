<?php
/**
 * Plugin Name: Project Panorama Sub Tasks
 * Plugin URI: http://www.projectpanorama.com
 * Description: Ability to create sub tasks in Project Panorama
 * Version: 1.6.5.1
 * Author: SnapOrbital
 * Author URI: http://www.projectpanorama.com
 * License: GPL2
 * Text Domain: psp-subtask
 * Contributors: 3pointross, d4mation
 */

do_action( 'pspst_before_init' );

$defintions = array(
    'PSP_ST_VER'  =>  '1.6.5.1',
    'PSP_ST_PATH' =>  plugin_dir_path( __FILE__ ),
    'PSP_ST_URL'  =>  plugin_dir_url( __FILE__ ),
    'PSP_ST_FILE' =>  __FILE__,
    'PSP_ST_STORE_URL' => 'https://www.projectpanorama.com',
    'PSP_ST_ITEM_ID'    => 59219
);

foreach( $defintions as $definition => $value ) {
    if( !defined($definition) ) define( $definition, $value );
}

if( !class_exists( 'EDD_SL_Plugin_Updater' ) ) {
	// load our custom updater if it doesn't already exist
	include( dirname( __FILE__ ) . '/lib/vendor/EDD_SL_Plugin_Updater.php' );
}

if( function_exists('psp_get_option') ) {
    include_once( 'lib/init.php' );
} else {
    add_action( 'admin_notices', 'psp_st_needs_panorama' );
}

function psp_st_needs_panorama() { ?>

    <div class="notice notice-error is-dismissible">
        <p><?php esc_html_e( 'Project Panorama Sub Tasks requires Project Panorama premium to run', 'psp_projects' ); ?></p>
    </div>

    <?php
}

do_action( 'pspst__after_init' );

psp_st_run_updater_api();

function psp_st_run_updater_api() {

    $psp_settings = get_option('psp_settings');

    if( !isset($psp_settings['psp_st_license']) ) return;

    // retrieve our license key from the DB
    $license_key = trim( $psp_settings['psp_st_license'] );

    // setup the updater
    $edd_updater = new EDD_SL_Plugin_Updater( PSP_ST_STORE_URL, __FILE__, array(
    	'version' 	=> PSP_ST_VER,		// current version number
    	'license' 	=> $license_key,	// license key (used get_option above to retrieve from DB)
    	'item_id'   => PSP_ST_ITEM_ID,	// id of this plugin
    	'author' 	=> 'SnapOrbital',	// author of this plugin
    	'url'       => home_url(),
        'beta'          => false // set to true if you wish customers to receive update notifications of beta releases
    ) );

}
