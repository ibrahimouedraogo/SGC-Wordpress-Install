<?php acf_form_head(); ?>
<!DOCTYPE html>
<html <?php language_attributes(); ?> class="no-js">
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
    <link rel="stylesheet" type="text/css" href="<?php echo esc_url( PANO_GALLERY_URL . '/assets/css/psp-gallery.css'); ?>">
</head>
<body id="psp-projects-gallery-template-file" <?php body_class(); ?>>
    <div id="psp-projects-form">

        <?php
        $post_id = ( isset($_GET['post_id']) ? $_GET['post_id'] : $post_id );

        $args = apply_filters( 'psp_edit_gallery_shortcode_form_atts', array(
            'id'		=>	'psp-acf-form',
            'post_id'	=>	$post_id,
            'new_post'	=>	false,
            'fields'	=>	array( 'field_5424a66e77cd4', 'field_5424a67777cd5', 'field_5424a67777cda', 'field_5424a358051b1' ),
        ));

        acf_form( $args ); ?>

    </div>

    <?php acf_enqueue_uploader(); ?>
    <?php wp_footer(); ?>

</body>
</html>
