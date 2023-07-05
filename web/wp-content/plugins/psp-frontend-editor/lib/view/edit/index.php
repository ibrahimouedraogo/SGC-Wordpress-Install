<?php
/*
 * File: edit/index.php
 *
 * Edit page view, slightly different than creating a new project
 */

// Setup necissary variables to see user, place and markers.
$cuser      = wp_get_current_user();
$sections   = psp_fe_get_edit_sections();
$markers    = psp_fe_get_milestone_markers();
$post_id    = get_query_var( 'psp_manage_option' );
$editable   = psp_fe_is_editable_post( $post_id );

// Identify what section we're on
$section    = ( isset($_GET['section']) && in_array( $_GET['section'], $sections ) ? $_GET['section'] : 'overview' );
$section    = ( isset($_GET['status']) && $_GET['status'] == 'new' ? 'milestones' : $section );
$title      = ( isset($_GET['status']) && $_GET['status'] == 'new' ? __( 'New Project', 'psp_projects' ) : '<span> ' . __( 'Editing' , 'psp_projects' ) . '</span>' . get_the_title($post_id) );

// Figure out completion
$completion = $markers[$section]['complete'];

acf_form_head();

include( psp_template_hierarchy('dashboard/header') ); ?>

    <input id="psp-ajax-url" type="hidden" value="<?php echo esc_url( admin_url() . 'admin-ajax.php' ); ?>">

	<?php
    do_action( 'psp_dashboard_page' );
    do_action( 'psp_dashboard_page_' . __FILE__ ); ?>

	<?php if( is_user_logged_in() ): ?>

		<div id="psp-archive-container" class="psp-grid-container-fluid psp-fe-page">

			<?php include( psp_template_hierarchy( 'global/header/masthead' ) ); ?>

                   <div class="psp-fe-wizard">
                       <?php
                       if( $editable && psp_can_edit_project($post_id) ): ?>

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
                                    // Setup the form arguments based on the post
                                    psp_fe_acf_form( $post_id ); ?>

                                    <div class="psp-wizard-actions">
                                        <input type="button" class="psp-wizard-edit-prev-button psp-btn" value="Back"> <input type="button" class="psp-wizard-edit-next-button psp-btn" value="Next">
                                    </div>

                               </div>

                         <?php
                         else:

                               if( !$editable ): ?>
                                   <div class="psp-fe-error-message psp-notice">
                                        <div class="psp-h2"><?php esc_html_e( 'No project found', 'psp_projects' ); ?></div>
                                        <div class="psp-p"><?php esc_html_e( 'Sorry, but there doesn\'t appear to be a project with that ID.', 'psp_projects' ); ?></div>
                                   </div>
                               <?php
                               else: ?>
                                    <div class="psp-fe-error-message psp-notice">
                                        <div class="psp-h2"><?php esc_html_e( 'Permission Denied', 'psp_projects' ); ?></div>
                                        <div class="psp-p"><?php esc_html_e( 'You don\'t have permission to edit this project.', 'psp-projects' ); ?></div>
                                   </div>
                               <?php
                              endif;

                         endif; ?>

                  </div> <!--__wrap-->

                  <div class="psp-p psp-wizard-cancel"><a href="<?php echo esc_url(get_permalink($post_id)); ?>"><?php esc_html_e( 'Cancel', 'psp_projects' ); ?></a></div>

     		</div> <!--/.psp-container-->
     	</div>

          <?php
          // Check to see if someone else is editing
          if( $editor_id = psp_check_post_lock($post_id) ): $editor = psp_get_nice_username_by_id($editor_id); ?>
               <div class="psp-post-lock-bg"></div>
               <div class="psp-post-lock-alert psp-alert">
                    <div class="psp-p"><?php echo sprintf( esc_html_x( 'This project is currently being edited by %s', 'Post lock message', 'psp_projects' ), $editor ); ?></div>
               </div>
          <?php
          else:
            psp_set_post_lock($post_id);
          endif;

     else: ?>
          <div id="overview" class="psp-comments-wrapper">
     	         <?php include( psp_template_hierarchy( 'global/login.php' ) ); ?>
          </div>
     <?php
     endif;

include( psp_template_hierarchy( 'global/navigation-off.php' ) );

wp_footer(); ?>

</body>
</html>
