<?php
add_action( 'psp_head', 'psp_fe_acf_assets' );
function psp_fe_acf_assets() {

	psp_register_style( 'psp-fe-project-front', PSP_FE_BASE_URI . 'assets/css/front.css', false, PSP_FE_VER );
	psp_register_style( 'psp-fe-project', PSP_FE_BASE_URI . 'assets/css/project.css', false, PSP_FE_VER );
	// psp_register_script( 'psp-fe-front', PSP_FE_BASE_URI . 'assets/js/front.js', false, PSP_FE_VER );

}

add_action( 'wp_enqueue_scripts', 'psp_custom_template_scripts', 99999 );
function psp_custom_template_scripts() {

	global $post;

	wp_register_style( 'psp-fe-project-front', PSP_FE_BASE_URI . 'assets/css/front.css', false );
	wp_register_style( 'psp-fe-project', PSP_FE_BASE_URI . 'assets/css/project.css', false );
	wp_register_script( 'psp-fe-front', PSP_FE_BASE_URI . 'assets/js/front.js', array('jquery'), PSP_FE_VER, true );

	if( ( get_post_type() == 'psp_projects' && psp_get_option('psp_use_custom_template') ) || ( isset($post->post_content) && has_shortcode($post->post_content, 'project_status') ) || ( isset($post->post_content) && has_shortcode($post->post_content, 'project_status_part') ) ) {

		wp_enqueue_style( 'psp-fe-project-front' );
		wp_enqueue_style( 'psp-fe-project' );
		wp_enqueue_script( 'psp-fe-front' );

		/*
		wp_enqueue_script( 'psp-tinymce' );
		wp_enqueue_style( 'psp-tinymce' );
		wp_enqueue_style( 'psp-tinymce-lg' );
		*/

		wp_enqueue_script( 'tinymce_js', includes_url( 'js/tinymce/' ) . 'wp-tinymce.php', array( 'jquery' ), false, true );

	}

}

add_action( 'wp_enqueue_scripts', 'psp_fe_enqueue_scripts', 9999 );
function psp_fe_enqueue_scripts() {

	wp_register_style( 'psp-fe-project-front', PSP_FE_BASE_URI . 'assets/css/front.css' );
	wp_register_style( 'psp-fe-project', PSP_FE_BASE_URI . 'assets/css/project.css' );
	wp_register_script( 'psp-fe-front', PSP_FE_BASE_URI . 'assets/js/front.js', array('jquery'), PSP_FE_VER, true );

	wp_register_style( 'select2', PSP_FE_BASE_URI . 'assets/vendor/select2/css/select2.min.css', array(), PSP_FE_VER );
	wp_register_script( 'select2', PSP_FE_BASE_URI . 'assets/vendor/select2/js/select2.full.min.js', array('jquery'), PSP_FE_VER, true );

	if( is_post_type_archive( 'psp_projects' ) && get_query_var( 'psp_manage_page' ) ) {

	   wp_enqueue_media();

        wp_enqueue_script( 'jquery-ui-core' );
        wp_enqueue_script( 'jquery-ui-slider' );
        wp_enqueue_style( 'jquery-ui-core' );
        wp_enqueue_style( 'jquery-ui-slider' );

		if( psp_get_option( 'psp_use_custom_template' ) ) {

			wp_enqueue_style( 'psp-fe-project-front' );
			wp_enqueue_style( 'psp-fe-front' );
			wp_enqueue_script( 'psp-fe-front' );

		}

	}

	if( is_singular('psp_projects') ) {

		wp_enqueue_script('psp-fe-front');

		$jqueryui_assets = array (
			'jquery-ui-core' => array(
				'script'	=>	true,
				'style'	=>	true,
			),
			'jquery-ui-sortable' => array(
				'script'	=>	true,
				'style'	=>	false
			)
		);

		foreach( $jqueryui_assets as $handle => $settings ) {
			if( $settings['script'] ) {
				wp_enqueue_script($handle);
			}
			if( $settings['style'] ) {
				wp_enqueue_style($handle);
			}
		}

		wp_enqueue_script( 'tinymce_js', includes_url( 'js/tinymce/' ) . 'wp-tinymce.php', array( 'jquery' ), false, true );

		wp_enqueue_style('select2');
		wp_enqueue_script('select2');


		/*
		wp_register_script( 'psp-tinymce', get_site_url() . '/wp-includes/js/tinymce/wp-tinymce.php?c=1', false );
		wp_register_style( 'psp-tinymce', get_site_url() . '/wp-includes/js/tinymce/skins/wordpress/wp-content.css', false );
		wp_register_style( 'psp-tinymce-lg', get_site_url() . '/wp-includes/js/tinymce/skins/lightgray/skin.min.css', false );

		wp_enqueue_script( 'psp-tinymce' );
		// wp_enqueue_style( 'psp-tinymce' );
		wp_enqueue_style( 'psp-tinymce-lg' );
		*/




	}

	if( get_query_var( 'psp_manage_page' ) ) {
		wp_enqueue_script('psp-fe-front');
	}

}

// add_action( 'wp_enqueue_scripts', 'psp_fe_manage_psp_assets', 9999 );
function psp_fe_manage_psp_assets( $scripts ) {

	$psp_options = get_option('psp_settings');

	if( psp_get_option('psp_use_custom_template') ) return;

	if( get_query_var( 'psp_manage_page' ) ) {

		// Drop the extra jquery

		// Strip out any theme styles
		global $wp_styles;

		foreach( $wp_styles->registered as $style ) {
			if( strpos( $style->src, 'wp-content/themes' ) ) {
				wp_deregister_style( $style->handle );
				wp_dequeue_style( $style->handle );
			}
		}

	}

}

// add_filter( 'psp_global_scripts', 'psp_fe_unset_jquery' );
function psp_fe_unset_jquery( $scripts ) {

	if( psp_get_option('psp_custom_template') ) return;

	if( get_query_var( 'psp_manage_page' ) ) {

		$i = 0;

		foreach( $scripts as $script ) {
			if( $script == 'jquery.js' ) unset( $scripts[ $i ] );
		}

	}

	return $scripts;

}


add_action( 'wp_enqueue_scripts', 'psp_fe_process_enqueue_scripts', 9999 );
function psp_fe_process_enqueue_scripts() {

	if( !is_post_type_archive( 'psp_projects' ) && psp_get_option( 'psp_fe_dequeue' ) ) {

		$scripts = psp_get_option( 'psp_fe_dequeue' );
		$scripts = explode( ',', $scripts );

		if( is_array( $scripts ) ) {
			foreach( $scripts as $script ) {

				wp_deregister_style( trim($script) );
				wp_dequeue_style( trim($script) );
				wp_deregister_script( trim($script) );
				wp_dequeue_script( trim($script) );

			}
		} else {

			wp_deregister_style( trim($scripts) );
			wp_dequeue_style( trim($scripts) );
			wp_deregister_script( trim($scripts) );
			wp_dequeue_script( trim($scripts) );

		}

	}

}

add_action( 'psp_head', 'psp_fe_localize_strings' );
function psp_fe_localize_strings() {
	echo '<script>var psp_delete_confirmation_message = "' . __( 'Are you sure you want to delete this project?', 'psp_projects' ) . '";</script>';
}

add_action( 'wp_head', 'psp_fe_localize_strings_ct' );
function psp_fe_localize_strings_ct() {

	if( is_singular() && get_post_type() == 'psp_projects' ) psp_fe_localize_strings();

}

add_action( 'get_header', 'psp_fe_acf_custom_template' );
function psp_fe_acf_custom_template() {

	if( is_post_type_archive('psp_projects') && psp_get_option('psp_use_custom_template') && get_query_var('psp_manage_page')  ) {
		acf_form_head();
	}

}

add_action( 'psp_head', 'psp_fe_global_js_vars' );
function psp_fe_global_js_vars() { ?>

	<script>
		var pano_ajax = "<?php echo admin_url( 'admin-ajax.php' ); ?>";
		var pano_post_id = "<?php echo get_the_ID(); ?>";
	</script>

	<?php
}
