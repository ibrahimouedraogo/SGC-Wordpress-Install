<?php
/**
 * psp-init.php
 *
 * Master file, builds everything.
 * @package psp-projects
 *
 *  NOTE: Premium "Repeater Field" Add-on is NOT to be used or distributed outside of this plugin per original copyright information from ACF
 *	http://www.advancedcustomfields.com/resources/getting-started/including-lite-mode-in-a-plugin-theme/
 *
 */

add_action( 'plugins_loaded', 'psp_core_init', 900 );
function psp_core_init() {

	$php_version = phpversion();

	// If we dont' have the minimum version abort!
	if( !version_compare( $php_version, '5.6' ) ) {
		add_action( 'admin_notices', 'psp_low_php_version' );
		return;
	}

	do_action( 'psp_before_panorama_loaded' );

	// Add menus locations for the dashboard and single project
	$menus = apply_filters( 'psp_menu_locations', array(
		'psp_project_menu'	=>	__('Add to the Panorama single project settings menu' ),
		'psp_section_menu'	=>	__( 'Add to the Panorama section menu' ),
		'psp_archive_menu'	=>	__('Add to the Panorama dashboard settings menu' ),
		'psp_footer_menu'	=>	__( 'Links in the Panorama footer' ),
	) );

	foreach( $menus as $id => $description ) register_nav_menu( $id, $description );

	$library = array(
		'psp-migrations',								// Migraiton scripts to upgrade from lite or lower versions
		'vendor/psp-vendor-init',						// All the outside vendor libraries
		'controllers/psp-controller-init',				// Controllers
		'models/psp-data-model-init',					// Builds all the data models
		'psp-templates',								// Template management
		'psp-view',										// Hooks to add templates to specific places
		'psp-assets',									// Asset management, style sheets and JS
		'psp-helpers',									// Utility and helper functions
		'psp-base-shortcodes',							// Standard shortcodes for LITE and PRO
		'psp-widgets',									// Custom widgets
		'psp-hooks',									// Slow consildation of all hooks
		'psp-admin',									// Admin management
	);

	// Check to see if advanced custom fields is already installed, if not add it
	global $acf;

	if( !$acf ) {


		$library[] = 'vendor/acf/master/acf';

		add_filter( 'acf/settings/path', 'psp_acf_settings_path' );
		add_filter( 'acf/settings/dir', 'psp_acf_settings_dir' );

		if( psp_get_option('psp_acf_compatibility_mode') != 1 ) {

			if( !defined( 'ACF_LITE' ) ) define( 'ACF_LITE' , true );

			add_filter( 'acf/settings/show_admin', '__return_false' );

		}

		define( 'PSP_ACF_EXT', false );
		define( 'PSP_ACF_VER', 5 );

	} else {

		define( 'PSP_ACF_EXT', true );
		define( 'PSP_ACF_VER', 5 );

	}

	$library[] = 'vendor/acf/conditionals/acf5-conditionals';

	if( !psp_has_duplicate_post() && psp_get_option( 'psp_disable_clone_post' ) != '1' ) {
		include_once( 'vendor/clone/duplicate-post.php' );
	}

	// Check to see if this is a paid version of panorama
	if( file_exists( dirname( __FILE__ ) . '/pro/psp-pro-init.php' ) ) {

		// This is a professional version, define constants and load libraries
	    define( 'PSP_PLUGIN_TYPE', 'professional' );
	    define( 'PSP_PLUGIN_DIR', 'project-panorama' );

		$library[] = 'pro/psp-pro-init';

	    include_once( 'pro/psp-pro-init.php' );

		/*
	    if( !function_exists( 'acf_repeater_collapser_assets' ) ) {
			$library[] = 'vendor/acf/collapse/acf_repeater_collapser';
		} */

	} else {

		// This is a free version, load the stripped down libraries
	    define( 'PSP_PLUGIN_TYPE' , 'lite' );
	    define( 'PSP_PLUGIN_DIR' , 'project-panorama-lite' );

		$library[] = 'lite/psp-lite-init';

	}

	// Loop through the library of resources and load them
	foreach ( $library as $book ) include_once( $book . '.php' );

	do_action( 'psp_after_panorama_loaded' );

	if( !get_option( 'psp_data_models' ) || get_option( 'psp_data_models' ) < PSP_VER ) {
		add_action( 'init', 'flush_rewrite_rules' );
		update_option( 'psp_data_models', PSP_VER );
	}

}

add_action( 'admin_init', 'psp_optimize_acf_performance' );
function psp_optimize_acf_performance() {

	if( 'psp_projects' == get_post_type() && function_exists('update_sub_field') ) {
		add_filter('acf/setting/remove_wp_meta_box', '__return_true');
	}

}

function psp_acf_settings_path( $path ) {

	$path = PROJECT_PANORAMA_DIR . '/lib/vendor/acf/master/';

	return $path;

}

function psp_acf_settings_dir( $path ) {

	$path = PROJECT_PANARAMA_URI . '/lib/vendor/acf/master/';

	return $path;

}

function psp_low_php_version() { ?>

	<div class="update-nag notice is-dismissable">
		<p><img src="<?php echo PROJECT_PANARAMA_URI; ?>/dist/assets/images/panorama-logo.png" alt="Project Panorama" style="max-height:50px"></p>
		<p><?php esc_html_e( 'Your website host is running an extremely old and insecure version of PHP. To use Panorama please ask your host to upgrade to PHP 5.6 or higher', 'psp_projects' ); ?></p>
	</div>

	<?php
}

function psp_has_duplicate_post() {

	$has_duplicate_post = false;

	$duplicate_post_functions = array(
		'duplicate_post_is_current_user_allowed_to_copy',
		'duplicate_post_plugin_actions',
		'__duplicate_post_main'
	);

	foreach( $duplicate_post_functions as $function ) {
		if( function_exists($function) ) {
			$has_duplicate_post = true;
		}
	}

	return $has_duplicate_post;

}
