<?php
/**
 * Teams Listing Page
 *
 * Lists all the teams on the site that you have access to
 * @var post_type	psp_teams
 */
include( psp_template_hierarchy( 'dashboard/header.php' ) );
include( psp_template_hierarchy( 'global/header/navigation-sub' ) ); ?>

<div id="psp-archive-container">

	<div id="psp-archive-content">
        <div class="psp-teams-section">

			<?php
			$i		= 0;
			$teams 	= ( current_user_can( 'delete_others_psp_projects' ) ? psp_get_the_teams() : psp_get_user_teams_query( $cuser->ID ) );

			if( $teams->have_posts() ): ?>
				<div class="psp-teams">
					<?php while( $teams->have_posts() ): $teams->the_post(); ?>

						<div class="psp-team-card">
							<aside class="psp-team-thumbnail">
								<?php
								if( has_post_thumbnail() ):
									the_post_thumbnail( 'thumbnail' );
								else: ?>
									<img src="<?php echo PROJECT_PANARAMA_URI; ?>/dist/assets/images/default-team.png" alt="<?php the_title(); ?>">
								<?php endif; ?>
							</aside>
							<article>

								<div class="psp-h3"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></div>

								<?php
								$projects = array(
									'incomplete'	=>  psp_get_team_projects( $post->ID, 'incomplete' ),
									'complete'		=>	psp_get_team_projects( $post->ID, 'completed' ),
								);

								$team_counts = array(
									'team-members'	=>	( get_field('team_members', $post->ID) ? count(get_field( 'team_members', $post->ID )) : 0 ),
									'active'		=>	$projects['incomplete']->post_count,
									'complete'		=>	$projects['complete']->post_count,
								);

								/*
								$team_counts = array(
									'team-members'	=>	( get_field('team_members', $post->ID) ? count(get_field( 'team_members', $post->ID )) : 0 ),
									'active'		=>	( $projects['incomplete']->have_posts() ? $projects['incomplete']->post_count : 0 ),
									'complete'		=>	( $projects['complete']->have_posts() ? $projects['complete']->post_count : 0 ),
								); */ ?>

								<div class="psp-team-meta">
									<div class="psp-team-meta_stat"><strong><?php echo esc_html($team_counts['team-members']); ?></strong> <span><?php esc_html_e( 'Members', 'psp_projects' ); ?></span></div>
									<div class="psp-team-meta_stat"><strong><?php echo esc_html($team_counts['active']); ?></strong> <span><?php esc_html_e( 'Active', 'psp_projects' ); ?></span></div>
									<div class="psp-team-meta_stat"><strong><?php echo esc_html($team_counts['complete']); ?></strong> <span><?php esc_html_e( 'Completed', 'psp_projects' ); ?></span> </div>
								</div>

								<div class="team-members">
									<?php psp_team_user_icons( $post->ID, 10 ); ?>
								</div>

							</article>
						</div>

					<?php endwhile; ?>
				</div>

			<?php else: ?>

				<div class="psp-notice psp-notice-alert"><?php esc_html_e( 'You are not assigned to any teams.', 'psp_projects' ); ?></div>

			<?php endif; ?>
		</div>
    </div>

<?php include( psp_template_hierarchy( 'dashboard/footer.php' ) );
