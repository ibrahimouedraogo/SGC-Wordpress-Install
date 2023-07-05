<?php
/* Custom Single.php for project only view */
global $post, $doctype; ?>
<!DOCTYPE html>
<html <?php language_attributes( $doctype ); ?>>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title><?php psp_the_title( $post ); ?></title>

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <?php if( get_field( 'hide_from_search_engines' , $post->ID ) ): ?>
        <meta name="robots" content="noindex, nofollow">
	<?php endif; ?>

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
    do_action( 'psp_head' );  ?>

</head>

<?php
$data = apply_filters( 'psp_project_wrapper_data_atts', array(
	'post_id' => get_the_ID(),
	'project_progress' => get_field( 'automatic_progress', get_the_ID() )[0],
	'phase_progress' => get_field( 'phases_automatic_progress', get_the_ID() )[0],
) ); ?>

<body id="psp-projects" class="<?php psp_the_body_classes(); ?>"
     <?php
     foreach( $data as $att => $value ): ?>
          data-<?php echo esc_attr($att); ?>="<?php echo esc_attr($value); ?>"
     <?php endforeach; ?>
     >

<?php $panorama_access = panorama_check_access( $post->ID ); ?>

<div class="psp-standard-template <?php psp_the_project_wrapper_classes(); ?>">

    <?php
    while( have_posts() ): the_post(); ?>

    <?php include( psp_template_hierarchy( 'global/header/masthead' ) ); ?>

        <?php if ( $panorama_access ) : ?>

            <?php
            if( is_user_logged_in() ) {
                include( psp_template_hierarchy( 'global/header/navigation-sub' ) );
            } ?>


            <?php do_action( 'psp_before_overview', $post->ID ); ?>

            <section id="overview" class="psp-wrapper psp-section">

                <?php do_action( 'psp_before_essentials', $post->ID ); ?>
                <?php do_action( 'psp_the_essentials', $post->ID ); ?>
				<?php do_action( 'psp_after_essentials', $post->ID ); ?>

            </section> <!--/#overview-->

            <?php do_action( 'psp_between_overview_progress' ); ?>
            <?php if( get_field( 'milestones', $post->ID ) ): ?>
                <section id="psp-progress" class="psp-wrapper cf psp-section">

                    <?php do_action( 'psp_before_progress', $post->ID ); ?>
                    <?php do_action( 'psp_the_progress', $post->ID ); ?>
                    <?php do_action( 'psp_after_progress', $post->ID ); ?>

                </section> <!--/#progress-->
            <?php endif; ?>


            <?php do_action( 'psp_between_progress_phases', $post->ID ); ?>

			<?php $phase_auto = get_field( 'phases_automatic_progress', $post->ID ); ?>

            <section id="psp-phases" class="psp-wrapper psp-section" data-phase-auto="<?php echo ( isset($phase_auto[0]) && $phase_auto[0] !== NULL ? $phase_auto[0] : 'No' ); ?>">

                <?php do_action( 'psp_before_phases',$post->ID ); ?>
                <?php do_action( 'psp_the_phases', $post->ID ); ?>
                <?php do_action( 'psp_after_phases', $post->ID ); ?>

            </section>

            <?php do_action( 'psp_between_phases_discussion', $post->ID ); ?>

            <!-- Discussion -->
            <?php if ( comments_open() ) : ?>
                <section id="psp-discussion" class="psp-section cf">

                    <?php
                    do_action( 'psp_before_discussion', $post->ID );
                    do_action( 'psp_the_discussion', $post->ID );
                    do_action( 'psp_after_discussion', $post->ID );
                    ?>

                </section>
            <?php endif; ?>

        <?php endif; ?>

        <?php if( ! $panorama_access ) { ?>

			<?php include( psp_template_hierarchy( 'global/login.php' ) ); ?>

		<?php } ?>

    <?php endwhile; // ends the loop ?>

</div> <!--/#psp-project-->

<?php
include( psp_template_hierarchy( 'global/navigation-off.php' ) );
wp_footer();
get_template_part('template', 'sharing-box');
do_action( 'psp_footer', $post->ID ); ?>

</body>
</html>
