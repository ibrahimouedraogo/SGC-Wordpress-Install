<?php
global $post, $doctype;
$cuser  = wp_get_current_user(); ?>
<!DOCTYPE html>
<html <?php language_attributes( $doctype ); ?>>
<head>

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

	<meta name="robots" content="noindex, nofollow">

	<?php do_action( 'psp_enqueue_scripts' ); ?>

    <!--[if lte IE 9]>
    	<script src="<?php echo plugins_url() . '/' . PROJECT_PANORAMA_DIR; ?>/assets/js/html5shiv.min.js"></script>
    	<script src="<?php echo plugins_url() . '/' . PROJECT_PANORAMA_DIR; ?>/assets/js/css3-mediaqueries.js"></script>
    <![endif]-->

	<!--[if IE]>
    	<link rel="stylesheet" type="text/css" src="<?php echo plugins_url() . '/' . PROJECT_PANORAMA_DIR; ?>/assets/css/ie.css">
    <![endif]-->

    <?php
    wp_head();
    do_action( 'psp_head' ); ?>

    <script>
        <?php do_action( 'psp_localize' ); ?>
    </script>

</head>
<body id="psp-projects" class="<?php psp_the_body_classes(); ?>">

	<?php
    do_action( 'psp_dashboard_page' );
    do_action( 'psp_dashboard_page_' . __FILE__ );

    if( is_user_logged_in() || psp_view_is_public() ):
        include( psp_template_hierarchy( 'global/header/masthead' ) );
    endif; ?>
