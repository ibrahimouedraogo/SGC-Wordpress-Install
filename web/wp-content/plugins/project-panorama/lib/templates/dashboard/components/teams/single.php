<?php
/**
 * Teams Listing Page
 *
 * Lists all the teams on the site that you have access to
 * @var post_type	psp_teams
 */
include( psp_template_hierarchy( 'dashboard/header.php' ) );
include( psp_template_hierarchy( 'global/header/navigation-sub' ) );
?>

<div id="psp-archive-container">

	<?php
    if( psp_current_user_can_access_team( $post->ID ) ): ?>

        	<div id="psp-archive-content">

              <div class="psp-archive-section psp-team-projects psp-archive-body">

			    <div class="psp-table-header">
				    <div class="psp-h2"><?php esc_html_e( 'Active Projects Assigned to', 'psp_projects' ); ?> <?php the_title(); ?></div>
			    </div>

			    <div class="psp-archive-list-wrapper">
	                  <?php
	                  $projects = psp_get_team_projects( $post->ID );

	                  echo psp_archive_project_listing( $projects );

	                  wp_reset_postdata(); ?>
  			    </div>

              </div>

		    <?php include( psp_template_hierarchy( 'dashboard/components/teams/sidebar.php' ) ); ?>

           </div> <!--/#psp-archive-content-->

    <?php
    else:

        wp_die( __( 'You don\'t have access to this team.', 'psp_projects' ) );

    endif;

include( psp_template_hierarchy( 'dashboard/footer.php' ) );
