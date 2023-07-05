<?php
$tooltip  = count( $milestones ) . ' ' . _n( __('Milestone', 'psp_projects'), __('Milestones','psp_projects'), count($milestones) );
$i		= 0;
$post_id 	= ( isset( $post_id ) ? $post_id : get_the_ID() );
$date 	= get_sub_field( 'date' ); ?>

<a href="<?php echo esc_attr( '#milestones-' . $milestones[0]['occurs'] ); ?>" class="<?php echo esc_attr( psp_milestone_marker_classes( $milestones, $completed ) ); ?> psp-modal-btn" data-milestone="<?php echo esc_attr($milestones[0]['occurs']); ?>">
	<?php echo esc_html_e( $milestones[0]['occurs'] . '%' ); ?>
	<span class="psp-milestone-dot-count" data-toggle="psp-tooltip" data-placement="top" title="<?php echo esc_attr($tooltip); ?>"><?php echo esc_html( count($milestones) ); ?></span>
</a>

<div class="psp-hide psp-modal psp-milestones-display" id="<?php echo esc_attr( 'milestones-' . $milestones[0]['occurs'] ); ?>">
	<div class="psp-modal-content">
		<?php do_action( 'psp_before_milestone_marker_text', $milestones, $post_id ); ?>

			<a class="modal-close psp-modal-x" href="#"><i class="fa fa-close"></i></a>

			<div class="psp-modal-header">
				<div class="psp-h2"><?php esc_html_e( 'Project Milestones', 'psp_projects' ); ?></div>
			</div>

			<div class="psp-milestone-dot__single <?php echo esc_attr( psp_milestone_marker_classes( $milestones, $completed ) ); ?> ">
				<?php echo esc_html( $milestones[0]['occurs'] . '%' ); ?>
			</div>

			<div class="psp-milestones-list">
				<?php
				foreach( $milestones as $milestone ): ?>
					<div class="psp-milestone-list__item">
						<span class="psp-marker-title">
							<span class="psp-marker-title__title">
								<?php echo esc_html($milestone['title']); ?>
							</span>
							<?php
							if( !empty($milestone['date']) && $milestone['date'] ) psp_the_milestone_due_date($milestone['date']); ?>
						</span>
						<?php if( !empty($milestone['description']) ): ?>
							<span class="psp-milestone-description" id="<?php echo esc_attr($id); ?>">
								<?php echo wp_kses_post( do_shortcode($milestone['description']) ); ?>
							</span>
						<?php endif; ?>
					</div>
				<?php endforeach; ?>
			</div>

		<?php do_action( 'psp_after_milestone_marker_text', $milestones, $post_id ); ?>
	</div>
</div> <!--/.psp-milestones-display-->
