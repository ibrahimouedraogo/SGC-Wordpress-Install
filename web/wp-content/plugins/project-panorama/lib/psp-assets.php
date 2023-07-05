<?php
/**
 * psp-assets.php
 * Register and enqueue styles and scripts for Project Panorama
 *
 * @author Ross Johnson
 * @copyright 3.7 MEDIA
 * @license GNU GPL version 3 (or later) {@see license.txt}
 * @package panorama
 **/

function psp_custom_template_assets() {

    $post_types = array(
        'psp_projects',
        'psp_teams'
    );


    if( in_array( get_post_type(), $post_types ) && psp_get_option('psp_use_custom_template') ) psp_front_assets(true);

}
add_action( 'wp_enqueue_scripts', 'psp_custom_template_assets', 9999 );

function psp_enqueue_calendar_assets() {

    wp_register_script( 'psp-admin-lib' , plugins_url() . '/' . PSP_PLUGIN_DIR . '/dist/assets/js/psp-admin-lib.min.js' , array( 'jquery' ) , PSP_VER , false );
    wp_enqueue_script( 'psp-admin-lib' );
    wp_enqueue_script( 'psp-frontend' );
    wp_enqueue_script( 'psp-custom' );

}

// Frontend Style and Behavior
add_action( 'wp_enqueue_scripts', 'psp_front_assets');
function psp_front_assets( $add_psp_scripts = null ) {

     $post_types = apply_filters( 'psp_asset_templates', array(
          'psp_projects',
          'psp_teams'
     ) );

     if( !in_array( get_post_type(), $post_types ) && !$add_psp_scripts && !is_post_type_archive($post_types) ) {
          return;
     }

     // wp_enqueue_style( 'psp-custom' );
     wp_enqueue_script( 'jquery' );

     wp_register_style( 'psp-frontend', plugins_url() . '/' . PSP_PLUGIN_DIR . '/dist/assets/css/psp-frontend.css', false, PSP_VER );
     wp_register_style( 'psp-calendar', plugins_url() . '/' .  PSP_PLUGIN_DIR . '/dist/assets/css/psp-calendar.css', false, PSP_VER );
     wp_register_style( 'psp-custom', plugins_url() . '/' . PSP_PLUGIN_DIR . '/dist/assets/css/psp-custom.css.php', false, PSP_VER );
     wp_register_style( 'psp-divi', plugins_url() . '/' . PSP_PLUGIN_DIR . '/dist/assets/css/psp-divi.css', false, PSP_VER );
     wp_register_script( 'psp-admin-lib' , plugins_url() . '/' . PSP_PLUGIN_DIR . '/dist/assets/js/psp-admin-lib.min.js' , array( 'jquery' ) , PSP_VER , false );

     wp_enqueue_style( 'psp-frontend' );
     wp_enqueue_style( 'psp-calendar' );
     wp_enqueue_style( 'psp-custom' );

     // Frontend Scripts
     wp_register_script( 'psp-frontend-library', plugins_url() . '/' . PSP_PLUGIN_DIR . '/dist/assets/js/psp-frontend-lib.min.js', array( 'jquery' ), PSP_VER, true );
     wp_register_script( 'psp-frontend-behavior', plugins_url() . '/' . PSP_PLUGIN_DIR . '/dist/assets/js/psp-frontend-behavior.js', array( 'jquery' ), PSP_VER, true );

     if( !get_query_var('psp_manage_page') ) {

          // If no query var is set, this is a post type single
          do_action( 'psp_single_project_assets' );

          wp_enqueue_script( 'psp-frontend-library' );
          wp_enqueue_script( 'psp-admin-lib' );
          wp_enqueue_script( 'psp-frontend-behavior' );

          // Dup. from FEE, can remove after a few vers
          wp_enqueue_script( 'tinymce_js', includes_url( 'js/tinymce/' ) . 'wp-tinymce.php', array( 'jquery' ), false, true );
          wp_enqueue_script( 'at-mentions', plugins_url() . '/' . PSP_PLUGIN_DIR . '/dist/assets/js/vendor/at-mentions.js', array( 'jquery', 'tinymce_js' ), PSP_VER, true );

     } else {

          // If the query var is set, this is a management page
          do_action( 'psp_manage_page_assets' );

     }

     // Special considerations for DIVI
     if( function_exists('et_setup_theme') ) {
          // wp_enqueue_style( 'psp-divi' );
          add_action( 'wp_footer', 'psp_negate_divi_smooth_scroll', 9999 );
     }

     // If using a theme template we need to wrap off canvas elements in a #psp-projects ID
     if( psp_get_option('psp_use_custom_template') ) {
          add_action( 'wp_footer', 'psp_reclaim_psp_elements', 99999 );
     }

     $local_js = array(
          'slug' => psp_get_option( 'psp_slug' , 'panorama' ),
          'ajax'  => admin_url( 'admin-ajax.php' ),
     );

     wp_localize_script(
          'psp-frontend-behavior',
               'psp',
               $local_js
          );

     do_action( 'psp_frontend_assets' );

}

function psp_negate_divi_smooth_scroll() { ?>

    <script>

        jQuery(document).ready(function($) {

            $( 'a[href*="#"]:not([href="#"])' ).each(function() {
                $(this).addClass('et_smooth_scroll_disabled');
            });

        });

    </script>

    <?php
}

function psp_reclaim_psp_elements() { ?>

    <style type="text/css">
        body.psp_projects-template-default {
            margin: 0;
            padding: 0;
        }
    </style>

    <?php
}


// Admin Style and Behavior

add_action( 'admin_enqueue_scripts', 'psp_admin_assets' );
function psp_admin_assets( $hook ) {

	global $post_type;

    if( function_exists('get_current_screen') ) {
        $screen = get_current_screen();
    } else {
        $screen = false;
    }

    // Admin Styling

    wp_register_style( 'psp-admin' , plugins_url() . '/' . PSP_PLUGIN_DIR . '/dist/assets/css/psp-admin.css', false, PSP_VER );
    wp_register_style( 'jquery-ui-psp' , plugins_url() . '/' . PSP_PLUGIN_DIR . '/dist/assets/css/jquery-ui-custom.css');

    wp_enqueue_media();
    wp_enqueue_style('psp-admin');
    wp_enqueue_style('wp-color-picker');

	// Determine if we need wp-color-picker or not

	if( $hook == 'settings_page_panorama-license') {
		wp_register_script( 'pspadmin' , plugins_url() . '/' . PSP_PLUGIN_DIR . '/dist/assets/js/psp-admin-behavior.js' , array( 'jquery' , 'wp-color-picker' ) , PSP_VER , true );
	} else {
		wp_register_script( 'pspadmin' , plugins_url() . '/' . PSP_PLUGIN_DIR . '/dist/assets/js/psp-admin-behavior.js' , array( 'jquery' ) , PSP_VER , true );
	}

	// Standard Needs
	wp_register_script( 'psp-admin-lib' , plugins_url() . '/' . PSP_PLUGIN_DIR . '/dist/assets/js/psp-admin-lib.min.js' , array( 'jquery' ) , PSP_VER , false );

	// PSP determines whether we load this or not. Keeping as a separate file just simplifies things for now, but localizing the value into JS may be better for lowering requests
	wp_register_script( 'psp-wysiwyg' , plugins_url() . '/' . PSP_PLUGIN_DIR . '/dist/assets/js/psp-wysiwyg.js' , array( 'jquery' ) , PSP_VER , false );

	// If this is the dashboard load dependencies
    if( $screen->id == 'dashboard' || $screen->id == 'psp_projects_page_panorama-calendar' ) {

        $assets = array(
            'scripts'   =>  array(
                'psp-frontend-library',
                'psp-admin-lib',
            ),
            'styles'    =>  array(
                'psp-frontend',
            )
        );

        foreach( $assets['scripts'] as $script ) wp_enqueue_script($script);
        foreach( $assets['styles'] as $style ) wp_enqueue_style($style);

    }

 	// If this is a Panorama project load dependencies
	if( $post_type == 'psp_projects' ) {

	    wp_enqueue_script('jquery-ui-datepicker');
	    wp_enqueue_script('jquery-ui-slider');

         wp_enqueue_style( 'jquery-ui-psp' );

	}

	// If this is a project page or settings page load the admin scripts
 	if( ( $post_type == 'psp_projects' ) || ( $hook == 'settings_page_panorama-license' ) ) {
	    wp_enqueue_script( 'pspadmin' );
	}

	if ( $hook == 'settings_page_panorama-license' ) {
		wp_enqueue_script( 'psp-admin-lib' );
	}

	// If the shortcode helpers are not disabled load the WYSIWYG buttons
	if((psp_get_option('psp_disable_js') === '0') || (psp_get_option('psp_disable_js') == NULL)) {
		wp_enqueue_script( 'psp-wysiwyg' );
	}

}

// Enqeue All
function psp_add_script( $script ) {
	echo '<script src="' . plugins_url() . '/' . PSP_PLUGIN_DIR . '/dist/assets/js/' . $script . '?ver=' . PSP_VER . '"></script> ';
}

function psp_add_style( $style, $ver = PSP_VER ) {
	echo '<link rel="stylesheet" type="text/css" href="' . plugins_url() . '/' . PSP_PLUGIN_DIR . '/dist/assets/css/' .$style .'?ver=' . $ver .'"> ';
}


// add_action( 'psp_enqueue_scripts' , 'psp_add_assets_to_templates');
function psp_add_assets_to_templates() {

	$global_scripts = apply_filters( 'psp_global_scripts', array(
		'psp-frontend-lib.min.js',
		'psp-frontend-behavior.js'
	) );

	$pdf_scripts = apply_filters( 'psp_pdf_scripts', array(
		'jspdf.min.js',
		'vendor/html2canvas.js',
		'vendor/html2canvas.svg.js'
	) );

    $timestamp = get_option( 'psp_timestamp', PSP_VER );

	$global_styles = apply_filters( 'psp_global_styles', array(
		'psp-frontend.css'    => PSP_VER,
		'psp-custom.css.php'  => $timestamp,
	) );

    $psp_settings = get_option('psp_settings');

    if( isset($psp_settings['psp_use_rtl']) && $psp_settings['psp_use_rtl'] ) {
        $global_styles[] = 'psp-rtl.css';
    }

	$pdf_styles = apply_filters( 'psp_pdf_styles', array(
		'psp-print.css'
	) );

	/* If this is a PDF view, load the necissary assets */

	if( isset( $_GET['pdfview'] ) ) {

		add_action( 'psp_body_classes', 'psp_add_pdf_view_body_class' );

		$global_scripts   = array_merge( $global_scripts, $pdf_scripts);
		$global_styles    = array_merge( $global_styles, $pdf_styles );

	}

	/* If this is the dashboard page, load the necissary assets */

	if( is_archive() ) {

		$global_styles['psp-calendar.css'] = PSP_VER;

		$global_scripts[] .= 'psp-admin-lib.min.js';

	}

    $global_scripts = apply_filters( 'psp_global_scripts', $global_scripts );
    $global_styles  = apply_filters( 'psp_global_styles', $global_styles );

	foreach( $global_scripts as $script ) {
		psp_add_script( $script );
	}

	foreach( $global_styles as $style => $ver ) {
		psp_add_style( $style, $ver );
	}

     $local_js = array(
          'psp_slug' => psp_get_option( 'psp_slug' , 'panorama' ),
          'ajaxUrl'  => admin_url( 'admin-ajax.php' ),
     );

     if( is_singular('psp-project') ) {
          $local_js['phaseProgress'] = get_field('automatic_progress');
          $local_js['projectProgress'] = get_field('phases_automatic_progress');
     }

	psp_localize_js(
		'projectPanorama',
		$local_js,
	);

}

add_filter( 'psp_body_classes' , 'psp_add_pdf_view_body_class' );
function psp_add_pdf_view_body_class( $classes ) {

	if( isset ( $_GET['pdfview'] ) ) {
		$classes .= 'psp-pdf-view ';
	}

	return $classes;

}

add_filter( 'psp_project_wrapper_classes' , 'psp_add_pdf_view_single_row_class' );
function psp_add_pdf_view_single_row_class( $classes ) {

	if( isset ( $_GET['pdfview'] ) ) {
		$classes .= 'psp-width-single ';
	}

	return $classes;

}

add_action( 'psp_js_variables', 'psp_js_translation_strings' );
function psp_js_translation_strings() {

    echo 'var psp_js_label_more = "' . __( 'more', 'psp_projects' ) . '";';
    echo 'var psp_delete_comment_alert = "' . __( 'Are you sure you want to delete this message?', 'psp_projects' ) . '";';
    echo 'var psp_comment_save_btn = "' . __( 'Save', 'psp_projects' ) . '";';
    echo 'var psp_comment_cancel_txt = "' . __( 'Cancel', 'psp_projects' ) . '";';

}

add_action( 'admin_footer', 'psp_hide_add_button_from_owners' );
function psp_hide_add_button_from_owners() {

    $screen = get_current_screen();

    if( $screen->parent_file != 'edit.php?post_type=psp_projects' ) return;

    $user = wp_get_current_user();
    if ( in_array( 'psp_project_owner', (array) $user->roles ) ) : ?>

        <style type="text/css">
            .page-title-action {
                display: none;
            }
        </style>

    <?php endif; ?>

    <script>
        jQuery(document).ready(function($) {

            $('.acf-repeater .acf-row').each(function() {

                if( !$(this).hasClass('acf-clone') && !$(this).find('.acf-field-user').length ) {
                    $(this).addClass('-collapsed');
                }
                if( $(this).find('.acf-field-user').length ) {
                    $(this).removeClass('-collapsed');
                }

            });

            $('.acf-field-psp-st-sub-tasks').find('.acf-row').removeClass('-collapsed');

        });
    </script>

    <?php
}
