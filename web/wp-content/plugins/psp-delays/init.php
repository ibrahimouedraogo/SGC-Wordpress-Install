<?php
$definitions = array(
    'PSP_DELAYS_BASE_DIR'   =>  dirname( __FILE__ ),
    'PSP_DELAYS_BASE_URI'   =>  plugin_dir_url( __FILE__ ),
    'PSP_DELAYS_STORE_URL'  =>  'https://www.projectpanorama.com',
    'PSP_DELAYS_ITEM_NAME'  =>  'Project Delays',
    'PSP_DELAYS_VER'        =>  '1.5',
);

foreach( $definitions as $definition => $value ) {
    if( !defined( $definition ) ) define( $definition, $value );
}

add_action( 'psp_after_panorama_loaded', 'psp_delays_init' );
function psp_delays_init() {

    do_action( 'psp_before_delays_loaded' );

    $deps = apply_filters( 'psp_delay_depedencies', array(
        'psp-delays-settings',
    	'lib/psp-delays-models',
    	'lib/psp-delays-assets',
    	'lib/psp-delays-controllers',
        'lib/psp-delays-views',
        'lib/psp-delays-permissions'
    ) );

    if( defined( 'PSP_VER' ) ) {
    	foreach( $deps as $dep ) require_once( $dep . '.php' );
    } else {
        add_action( 'admin_notices', 'psp_delays_install_panorama' );
    }

    do_action( 'psp_after_delays_loaded' );

    if( !class_exists( 'EDD_SL_Plugin_Updater' ) ) {
	// load our custom updater
	   include( dirname( __FILE__ ) . '/EDD_SL_Plugin_Updater.php' );
    }

}

function psp_delays_install_panorama() { ?>

    <div class="notice notice-warning is-dismissible">
        <p><?php esc_html_e( 'Project Delays requires Project Panorama 1.4.2.4 or higher to be active.', 'psp_delays' ); ?></p>
    </div>

    <?php
}

add_action( 'admin_init', 'psp_delays_project_panorama_plugin_updater' );
function psp_delays_project_panorama_plugin_updater() {

    if( !class_exists( 'EDD_SL_Plugin_Updater' ) ) {
        return;
    }

	// retrieve our license key from the DB
	$license_key = trim( get_option( 'psp_delays_license' ) );

	// setup the updater
	$edd_updater = new EDD_SL_Plugin_Updater( PSP_DELAYS_STORE_URL, __FILE__, array(
			'version' 	=> PSP_DELAYS_VER, 				// current version number
			'license' 	=> $license_key, 		// license key (used get_option above to retrieve from DB)
			'item_name' => PSP_DELAYS_ITEM_NAME, 	// name of this plugin
			'author' 	=> 'SnapOrbital',  // author of this plugin
			'url'       => home_url()
		)
	);

}


 add_action( 'plugins_loaded', 'psp_delays_localize_init' );
 function psp_delays_localize_init() {
     load_plugin_textdomain( 'psp-delays', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
 }
