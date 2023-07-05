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
    // Figure out what user we're going to display

    $cuser    = wp_get_current_user();
    $user_id  = ( get_query_var('psp_user') ? get_query_var('psp_user') : $cuser->ID );

    if( current_user_can('delete_others_psp_projects') || $user_id == $cuser->ID ): ?>

        	<div id="psp-archive-content">

			<div class="psp-archive-body">
				<div class="psp-archive-section ">

					<div class="psp-table-header">
						<div class="psp-h2"><?php echo esc_html_e( 'Active Projects Assigned to', 'psp_projects' ) . ' ' . psp_get_nice_username_by_id( $user_id ); ?></div>
					</div> <!--/.psp-table-header-->

					<div class="psp-archive-list-wrapper">

						<?php
						$paged 	  = ( get_query_var('paged') ? get_query_var('paged') : 1 );
						$projects   = psp_get_user_projects( $user_id, $paged );

						echo psp_archive_project_listing( $projects );

						wp_reset_postdata(); ?>

					</div>

				</div> <!--/.psp-archive-section-->

				<div class="psp-archive-section psp-user-tasks-section">

					<div class="psp-table-header">
						<div class="psp-h2"><?php esc_html_e( 'Assigned Tasks', 'psp_projects' ); ?></div>
					</div>

					<table class="psp-my-tasks not-shortcode psp-user-tasks">
						<tbody>
							<?php echo psp_all_my_tasks_shortcode( array( 'id' => $user_id ), false ); ?>
						</tbody>
					</table> <!--/.psp-archive-table-->

				</div>

			</div> <!--/.psp-archive-body-->

			<?php include( psp_template_hierarchy( 'dashboard/components/users/sidebar.php' ) ); ?>

		</div> <!--/#psp-archive-content-->

		<?php
		else:

		wp_die( __( 'You don\'t have permission to view this user.', 'psp_projects' ) );

		endif;

include( psp_template_hierarchy( 'dashboard/footer.php' ) );
