<?php

/* Custom Single.php for project only view */
$sections   = psp_fe_get_edit_sections();
$cuser      = wp_get_current_user();
$section    = 'overview';
acf_form_head();

include( psp_template_hierarchy('dashboard/header') ); ?>

    <input id="psp-ajax-url" type="hidden" value="<?php echo esc_url(admin_url()); ?>admin-ajax.php">

	<?php
    do_action('psp_dashboard_page');
    do_action( 'psp_dashboard_page_' . __FILE__ ); ?>

	<?php if( is_user_logged_in() ): ?>

        <style type="text/css">
            #psp-primary-header {
                display: none;
            }
        </style>

        <div id="psp-archive-container" class="psp-grid-container-fluid psp-fe-page">

             <?php include( psp_template_hierarchy( 'global/header/masthead' ) ); ?>

             <div class="psp-fe-wizard">

                  <div class="psp-fe-wizard__nav">
                       <div class="psp-h2"><?php echo wp_kses_post($title); ?></div>
                       <?php include( PSP_FE_PATH . 'lib/view/partials/timeline.php' ); ?>
                  </div>

                  <div class="psp-fe-wizard__content">

                       <!-- Section title -->
                       <div class="psp-h4 psp-wizard-section">
                          <?php if( isset($_GET['status']) && $_GET['status'] == 'new' ): ?>
                              <strong><?php esc_html_e( 'Step', 'psp_projects' );?> <span class="step">5</span> <?php esc_html_e( 'of', 'psp_projects' ); ?> 6</strong>
                          <?php endif; ?>
                          <span class="timeline-title"><?php esc_html_e( 'Loading...', 'psp_projects' ); ?></span>
                       </div>

                       <!-- Loading -->
                      <div id="cssload-pgloading">
                          <div class="cssload-loadingwrap">
                               <ul class="cssload-bokeh">
                                    <li></li>
                                    <li></li>
                                    <li></li>
                                    <li></li>
                               </ul>
                          </div>
                      </div>
                      <!--/loading-->

                       <?php
                       $templates  = psp_get_global_templates();
                       $action    = ( PSP_FE_PERMALINKS ? 'manage/duplicate/' : '&psp_manage_page=duplicate' );

                           if( $templates->have_posts() ): ?>

                               <div class="psp-form psp-fe-template-form">
                                   <form method="post" action="<?php echo esc_attr( get_post_type_archive_link('psp_projects') . $action ); ?>">
                                        <?php
                                        $cuser = wp_get_current_user();
                                        wp_nonce_field( 'duplicate_project_' . $cuser->ID ); ?>
                                        <input type="hidden" name="user_id" value="<?php echo esc_attr($cuser->ID); ?>">
                                        <div class="psp-fe-template-select">
                                             <div class="psp-select-wrapper">
                                                <select name="psp-fe-use-template" id="psp-fe-use-template">
                                                    <option value="---"><?php esc_html_e( 'Use Template', 'psp_projects' ); ?></option>
                                                    <?php while( $templates->have_posts() ): $templates->the_post(); global $post; ?>
                                                        <option value="<?php echo esc_attr( $post->ID ); ?>"><?php the_title(); ?></option>
                                                    <?php endwhile; ?>
                                                </select>
                                             </div>
                                           <input type="submit" class="psp-fe-use-template-submit pano-btn" value="Select" name="Select">
                                       </div>
                                   </form>
                               </div>
                           <?php
                           endif; ?>

                           <?php
                           psp_fe_acf_form();  ?>


                            <div class="psp-wizard-actions">
                                <input type="button" class="psp-wizard-prev-button psp-btn is-inactive" value="Back"> <input type="button" class="psp-wizard-next-button psp-btn" value="Next">
                            </div>

                       </div>

          </div>

          <div class="psp-p psp-wizard-cancel"><a href="<?php echo esc_url(get_post_type_archive_link('psp_projects')); ?>"><?php esc_html_e( 'Cancel', 'psp_projects' ); ?></a></div>

     </div>


	<?php else: ?>

        <div id="overview" class="psp-comments-wrapper">

			<?php include( psp_template_hierarchy( 'global/login.php' ) ); ?>

        </div>
	<?php endif; ?>

    <?php
    if( PSP_VER == 5 ) {
        acf_enqueue_uploader();
    } ?>

    <?php
    include( psp_template_hierarchy( 'dashboard/footer.php' ) );
    wp_footer(); ?>

</body>
</html>
