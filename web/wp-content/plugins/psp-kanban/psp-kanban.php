<?php
/**
 * Plugin Name: Project Panorama - Kanban
 * Plugin URI: http://www.projectpanorama.com
 * Description: Kanban Board for Project Panorama
 * Version: 1.1.2
 * Author: SnapOrbital
 * Author URI: https://www.projectpanorama.com
 * License: GPL2
 * Text Domain: psp_projects
 */

do_action( 'psp_kb_before_init' );

$defintions = array(
    'PSP_KB_VER'  =>  '1.1.2',
    'PSP_KB_PATH' =>  plugin_dir_path( __FILE__ ),
    'PSP_KB_URL'  =>  plugin_dir_url( __FILE__ ),
    'PSP_KB_STORE_URL'  =>  'https://www.projectpanorama.com',
    'PSP_KB_ITEM_NAME'  =>  'Kanban',
);

foreach( $defintions as $definition => $value ) {

    if( !defined($definition) ) define( $definition, $value );

}

if( !empty($_GET['psp_view']) ) {
     setcookie( 'psp_view', $_GET['psp_view'], time() +2592000, '/' );
}

include_once( 'lib/init.php' );

if( !class_exists( 'EDD_SL_Plugin_Updater' ) ) {
	// load our custom updater
	include( dirname( __FILE__ ) . '/EDD_SL_Plugin_Updater.php' );
}

add_action( 'admin_init', 'psp_kb_project_panorama_plugin_updater' );
function psp_kb_project_panorama_plugin_updater() {

    $settings = get_option('psp_settings');

    if( !isset( $settings['psp_kb_license'] ) ) {
        return;
    }

    // retrieve our license key from the DB
    $license_key = trim( $settings['psp_kb_license'] );

	// setup the updater
	$edd_updater = new EDD_SL_Plugin_Updater( PSP_KB_STORE_URL, __FILE__, array(
			'version' 	=> PSP_KB_VER, 				// current version number
			'license' 	=> $license_key, 		// license key (used get_option above to retrieve from DB)
			'item_name'    => PSP_KB_ITEM_NAME, 	// name of this plugin
			'author' 	     => 'SnapOrbital',  // author of this plugin
			'url'          => home_url()
		)
	);

}

global $psp_kb_lanes;

do_action( 'psp_kb_after_init' );
