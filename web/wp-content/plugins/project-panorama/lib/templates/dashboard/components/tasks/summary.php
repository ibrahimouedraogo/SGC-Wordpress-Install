<?php
/**
 * Project tiles on the dashboard
 * @var [type]
 */
$post_id 			= $task['project_id'];
$cuser				= wp_get_current_user();
$phases_and_tasks 	= psp_get_item_count( $post_id, $cuser->ID );
$project_logo		= get_field( 'client_project_logo', $post_id );
$start_date 		= psp_text_date(get_field( 'start_date', $post_id ));
$end_date   		= psp_text_date(get_field( 'end_date', $post_id ));
?>

<div class="psp-task-project psp-masonry-item <?php echo esc_attr( 'psp-task-project-' . $post_id ); ?> psp-masonry-item" id="<?php echo esc_attr( 'psp-task-project-' . $post_id ); ?>">
	<div class="psp-task-project-wrapper">

		<hgroup class="psp-task-project-header">

			<div class="psp-h3 psp-task-project-title">
				<a href="<?php echo esc_url(get_the_permalink( $task['project_id'] )); ?>">
					<?php if( !empty($project_logo) ): ?>
						<img src="<?php echo esc_url( $project_logo['sizes']['medium'] ); ?>" alt="<?php the_field( 'client', $post_id ); ?>" class="psp-client-summary-logo">
					<?php endif; ?>
					<b><?php echo get_the_title($post_id); ?></b>
					<?php if( get_field('client') ) echo '<span class="psp-client">' . get_field( 'client', $post_id ) . '</span>'; ?>
				</a>
			</div>

			<?php
			$completed = psp_compute_progress($post_id);
			if( !$completed ) $completed = 0; ?>

			<div class="psp-progress">
				<span class="psp-<?php echo esc_attr($completed); ?>" data-toggle="psp-tooltip" data-placement="top" title="<?php echo esc_attr($completed . '% ' . __( 'Complete', 'psp_projects' ) ); ?>">
					<b><?php echo esc_html($completed); ?>%</b>
				</span>
				<i class="psp-progress-label"> <?php esc_html_e('Progress','psp_projects'); ?> </i>
			</div>

			<?php if( $start_date && $end_date ) psp_the_simplified_timebar($post_id); ?>

			<div class="psp-task-breakdown">
				<?php
				$breakdown = apply_filters( 'psp_task_stats_breakdown', array(
					array(
						'class'	=>	'psp-element-tally-all',
						'count'	=>	$phases_and_tasks['tasks'],
						'label'	=>	__( 'Assigned', 'psp_projects' )
					),
					array(
						'class'	=>	'psp-element-tally-started',
						'count'	=>	$phases_and_tasks['started'],
						'label'	=>	__( 'Started', 'psp_projects' )
					),
					array(
						'class'	=>	'psp-element-tally-completed',
						'count'	=>	$phases_and_tasks['completed'],
						'label'	=>	__( 'Complete', 'psp_projects' )
					),
				) );
				if( !empty($breakdown) ):
					foreach( $breakdown as $stat ): ?>
						<div class="psp-element-tally <?php echo esc_attr($stat['class']); ?>" data-count="<?php echo esc_attr($stat['count']); ?>">
							<strong><?php echo esc_html( $stat['count'] ); ?></strong>
							<span><?php echo esc_html( $stat['label'] ); ?></span>
						</div>
					<?php endforeach;
				endif; ?>
			</div>

		</hgroup> <!--/.psp-task-project-header-->

		<div class="psp-my-tasks psp-task-section">

			<input id="psp-ajax-url" type="hidden" value="<?php echo admin_url(); ?>admin-ajax.php">

			<?php
			$phase_index 		= 0;
			$overall_auto	= get_field( 'automatic_progress', $post_id );
			$phase_auto		= get_field( 'phases_automatic_progress', $post_id );
			$phases			= get_field( 'phases', $post_id );

			while( have_rows( 'phases', $post_id ) ) { the_row();

				$tasks = psp_get_tasks( $post_id, $phase_index );
				$phase = $phases[$phase_index];

				if( !empty($tasks) ):

					$t = 0;
					foreach ( $tasks as $task ) {

						$show_task = false; // reset

						if( ( is_int($task['assigned']) || is_string($task['assigned']) ) && $task['assigned'] == $cuser->ID ) {
							$show_task = true;
						} elseif( is_array($task['assigned']) && in_array( $cuser->ID, $task['assigned']) ) {
							$show_task = true;
						}

						$show_task = apply_filters( 'psp_show_task_on_dashboard', $show_task, $phase, $task );

						if ( $show_task ) {
							$t++;
						}

					}

					if( $t > 0 ): ?>

						<div class="psp-tasks-phase" data-phase_id=<?php echo $phase['phase_id']; ?>>

							<div class="psp-h3"><?php the_sub_field( 'title' ); ?></div>

							<div class="psp-task-list">
								<?php
								foreach( $tasks as $task ):

									$show_task = false; // reset

									if( ( is_int($task['assigned']) || is_string($task['assigned']) ) && $task['assigned'] == $cuser->ID ) {
										$show_task = true;
									} elseif( is_array($task['assigned']) && in_array( $cuser->ID, $task['assigned']) ) {
										$show_task = true;
									}

									$show_task = apply_filters( 'psp_show_task_on_dashboard', $show_task, $phase, $task );

									if ( $show_task ) {
										include( psp_template_hierarchy( 'projects/phases/tasks/single/entry.php' ) );
									}

								endforeach; ?>
							</div>

							<?php do_action( 'psp_after_dashboard_phase_tasks', $phase_index, $post_id ); ?>

						</div>

					<?php endif;

				endif;

				$phase_index++;

			} ?>

		</div> <!-- /.psp-my-tasks -->

	</div>
</div>
