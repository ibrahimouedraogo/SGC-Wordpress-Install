<?php
/**
 * Project Dashboard
 *
 * Location the users first login to and get an overview of their projects
 * @var post_type	psp_projects
 */

include( psp_template_hierarchy( 'dashboard/header' ) );

$projects_per_page  = intval(psp_get_option('psp_projects_per_page'));
if( !$projects_per_page ) {
	$projects_per_page = get_option('posts_per_page');
}

$count     = apply_filters( 'psp_archive_project_listing_count', ( isset($_GET['count']) ? $_GET['count'] : $projects_per_page ) );
$status    = apply_filters( 'psp_archive_project_listing_status', ( get_query_var('psp_status_page') ? get_query_var('psp_status_page') : 'active' ) );
$paged     = ( get_query_var('paged') ? get_query_var('paged') : 1 );
$args      = apply_filters( 'psp_archive_project_listing_args', psp_setup_all_project_args($_GET) );
$projects	 = psp_get_all_my_projects( $status, $count, $paged, $args );
$tasks 	 = psp_get_all_my_tasks();

?>

<?php include( psp_template_hierarchy( 'global/header/navigation-sub' ) ); ?>

<div id="psp-archive-container" >
		<div id="psp-archive-content">

				<div class="psp-archive-body">

					<?php
					do_action( 'psp_dashboard_before_my_projects' );

					include( psp_template_hierarchy( 'dashboard/components/projects/my-projects.php' ) );

					do_action( 'psp_dashboard_after_my_projects' );

					if( $tasks ) include( psp_template_hierarchy( 'dashboard/components/tasks/dashboard.php' ) );

					do_action( 'psp_dashboard_after_my_tasks' );

					?>

				</div> <!--/.psp-col-md-8-->

				<aside id="psp-archive-sidebar" class="psp-archive-aside">

					<?php do_action( 'psp_before_dashboard_widgets' ); ?>

                    	<?php do_action( 'psp_dashboard_widgets' ); ?>

					<div class="psp-archive-widget psp-projects-widget cf">

						<div class="psp-h4"><?php esc_html_e( 'Overview', 'psp_projects' ); ?></div>

						<?php echo psp_get_project_breakdown(); ?>

					</div>

					<?php
					$teams = psp_get_user_teams();
					if( $teams ): ?>
						<div class="psp-archive-widget psp-teams-archive-widget">

							<div class="psp-h4"><?php esc_html_e( 'My Teams', 'psp_projects' ); ?></div>

							<div class="psp-team-list">
								<?php foreach( $teams as $team ):
									$members = get_field( 'team_members', $team->ID ); ?>
									<div class="psp-team-list__item">
										<?php psp_team_user_icons( $team->ID ); ?>
										<a href="<?php echo esc_url( get_the_permalink( $team->ID ) ); ?>">
											<strong><?php echo esc_html( get_the_title( $team->ID ) ); ?></strong>
											<span class="psp-tmi-count"><?php echo count( $members ) . ' ' . __( 'Members', 'psp_projects' ); ?></span>
										</a>
									</div>
								<?php endforeach; ?>
							</div>

						</div>
					<?php endif; ?>

					<div class="psp-archive-widget psp-calendar-archive-widget">
						<div class="psp-h4">
							<?php esc_html_e( 'Calendar', 'psp_projects' ); ?>
							<a class="psp-ical-link psp-archive-ical-link" href="<?php echo psp_get_ical_link(); ?>" target="_new"><?php echo esc_html_e( 'iCal Feed', 'psp_projects' ); ?></a>
						</div>
						<?php echo psp_output_project_calendar(); ?>
					</div> <!--/.psp-archive-widget-->

                    <?php do_action( 'psp_after_dashboard_widgets' ); ?>

				</aside>

			</div> <!--/.psp-grid-row-->
		</div> <!--/.#psp-archive-content-->
	</div> <!--/.psp-grid-row-->
</div> <!--/#psp-archive-container-->

<?php
include( psp_template_hierarchy( 'dashboard/footer.php' ) );
