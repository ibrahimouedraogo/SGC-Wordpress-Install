<?php
add_action( 'psp_head', 'psp_st_add_subtask_styles' );
function psp_st_add_subtask_styles() {

    psp_register_style( 'subtasks', PSP_ST_URL . 'assets/css/style.css' );
    psp_register_script( 'subtasks', PSP_ST_URL . 'assets/js/script.js' );

}

add_action( 'init', 'psp_st_register_scripts' );
function psp_st_register_scripts() {

	wp_register_script(
		'psp-st-admin',
		plugins_url( '/assets/js/admin.js', PSP_ST_FILE ),
		array( 'jquery' ),
		defined( 'WP_DEBUG' ) && WP_DEBUG ? time() : PSP_ST_VER,
		true
	);

}

add_action( 'wp_enqueue_scripts', 'psp_st_frontend_scripts' );
function psp_st_frontend_scripts() {

    global $post;

    wp_register_style( 'subtasks', PSP_ST_URL . 'assets/css/style.css', null, PSP_ST_VER );
    wp_register_script( 'subtasks', PSP_ST_URL . 'assets/js/script.js', array('jquery'), true, PSP_ST_VER );

    $post_types = apply_filters( 'psp_subtasks_assets_post_types', array(
        'psp_projects',
        'psp_teams'
    ) );

    if( ( in_array( get_post_type(), $post_types ) && psp_get_option('psp_use_custom_template') ) || has_shortcode( $post->post_content, 'project_status' ) || has_shortcode( $post->post_content, 'project_status_part' ) ) {
        wp_enqueue_style('subtasks');
        wp_enqueue_script('subtasks');
    }

}

add_action( 'wp_footer', 'psp_uncollapse_subtasks' );
function psp_uncollapse_subtasks() {

    if( get_post_type() == 'psp_projects' && get_query_var('psp_manage_page') ): ?>

    <script>
        jQuery('document').ready(function($) {

            acf.add_action( 'load', function() {

                $('.acf-field-psp-st-sub-tasks .acf-repeater').each(function() {
                    $(this).find('.acf-row').removeClass('-collapsed');
                });

            });

        });
    </script>

    <?php
    endif;

}
