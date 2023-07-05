<?php $panorama_access = panorama_check_access($post->ID);

$data = apply_filters( 'psp_project_wrapper_data_atts', array(
	'post_id' => $post->ID,
	'project_progress' => get_field( 'automatic_progress', $post->ID )[0],
	'phase_progress' => get_field( 'phases_automatic_progress', $post->ID )[0],
) ); ?>

<div id="psp-projects" class="psp-theme-template psp-reset <?php psp_the_body_classes(); ?>"
	<?php
	foreach( $data as $att => $value ): ?>
		data-<?php echo esc_attr($att); ?>="<?php echo esc_attr($value); ?>"
	<?php endforeach; ?>
	>

		<input type="hidden" id="psp-task-style" value="<?php the_field('expand_tasks_by_default',$post->ID); ?>">

        <?php // do_action('psp_the_header'); ?>

        <?php if ( $panorama_access ) :

			if( is_user_logged_in() ): ?>
				<div class="psp-wrapper">
					<?php include( psp_template_hierarchy( 'global/header/navigation-sub' ) ); ?>
				</div>
			<?php
			endif; ?>
 <?php if( is_user_logged_in() ): ?>


            <?php do_action('psp_before_overview'); ?>

            <section id="overview" class="psp-wrapper psp-section">

                <?php do_action('psp_before_essentials'); ?>
                <?php do_action('psp_the_essentials'); ?>
                <?php do_action('psp_after_essentials'); ?>

            </section> <!--/#overview-->

            <?php do_action('psp_between_overview_progress'); ?>

            <section id="psp-progress" class="psp-wrapper psp-section">

                <?php do_action('psp_before_progress'); ?>
                <?php do_action('psp_the_progress'); ?>
                <?php do_action('psp_after_progress'); ?>

            </section> <!--/#progress-->

            <?php do_action('psp_between_progress_phases'); ?>

            <section id="psp-phases" class="psp-wrapper psp-section">

                <?php do_action('psp_before_phases'); ?>
                <?php do_action('psp_the_phases'); ?>
                <?php do_action('psp_after_phases'); ?>

            </section>
<?php
                        endif; ?>

            <?php do_action('psp_between_phases_discussion'); ?>

			<!-- Discussion -->
            <?php if ( comments_open() ) : ?>
                <section id="psp-discussion" class="psp-wrapper psp-section cf">

                    <?php
                    do_action( 'psp_before_discussion' );
                    do_action( 'psp_the_discussion' );
                    do_action( 'psp_after_discussion' );
                    ?>

                </section>
            <?php
			add_filter( 'comments_template', 'psp_disable_custom_template_comments' );
			endif; ?>


        <?php endif; ?>

        <?php if( ! $panorama_access ): ?>
            <div id="overview" class="wrapper">
                <div id="psp-login">
                    <?php if( ( ! $panorama_access ) && (get_field('restrict_access_to_specific_users'))): ?>
                        <h2><?php _e('This Project Requires a Login','psp_projects'); ?></h2>
                        <?php if(!is_user_logged_in()) {
                            echo panorama_login_form();
                        } else {
                            echo "<p>".__('You don\'t have permission to access this project','psp_projects')."</p>";
                        }
                        ?>
                    <?php endif; ?>
                    <?php if((post_password_required()) && (!current_user_can('manage_options'))): ?>
                        <h2><?php _e('This Project is Password Protected','psp_projects'); ?></h2>
                        <?php echo get_the_password_form(); ?>
                    <?php endif; ?>
                </div>
            </div>

        <?php endif; ?>

		<?php
		include( psp_template_hierarchy( 'global/document-status-modal' ) );
	 	include( psp_template_hierarchy( 'global/navigation-off.php' ) );
		get_template_part('template', 'sharing-box');
		do_action( 'psp_footer' );
		do_action( 'psp_footer_custom_template' ); ?>


</div> <!--/#psp-project-->
