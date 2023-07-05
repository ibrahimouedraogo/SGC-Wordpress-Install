<?php
$post_id 		= isset( $post_id ) ? $post_id : get_the_ID();
$completed 		= psp_compute_progress( $post_id );
$all_milestones	= psp_organize_milestones( get_field( 'milestones', $post_id ) );

if( !empty( $all_milestones ) && $all_milestones['milestones'] ): ?>

	<div class="<?php echo esc_attr($style); ?>">

		<hgroup class="psp-section-heading">

			<?php do_action( 'psp_before_milestone_title', $all_milestones, $completed, $post_id ); ?>
			<div class="psp-h2" class="psp-section-title"><?php esc_html_e( 'Milestones', 'psp_projects' ); ?></div>
			<?php do_action( 'psp_after_milestone_title', $all_milestones, $completed, $post_id ); ?>
			<div class="psp-section-data">
				<?php
				// Hook to add content
				do_action( 'psp_before_project_milestone_section_data', $all_milestones, $completed, $post_id );

				echo esc_html($all_milestones['completed']); ?> / <?php echo esc_html( count( get_field('milestones', $post_id )) ) . ' ' . __( 'Completed', 'psp_projects' );

				// Hook to add content
				do_action( 'psp_after_project_milestone_section_data', $all_milestones, $completed, $post_id ); ?>
			</div>
			<?php do_action( 'psp_after_milestone_data', $all_milestones, $completed, $post_id ); ?>

		</hgroup> <!--/.psp-section-heading-->

		<div class="psp-milestone-timeline">

			<div class="psp-progress"><span class="psp-<?php echo esc_attr($completed); ?>"><b><?php echo esc_html($completed); ?>%</b></span></div>

			<div class="psp-enhanced-milestones">
				<div class="psp-milestone-dots">
					<?php foreach( $all_milestones['milestones'] as $milestones ) include( psp_template_hierarchy( 'projects/milestones/single/marker' ) ); ?>
				</div> <!--/.psp-milestone-dots-->
			</div> <!--/.psp-enhanced-milestones-->

		</div> <!--/.psp-milestone-timeline-->

	</div>

<?php endif; ?>
