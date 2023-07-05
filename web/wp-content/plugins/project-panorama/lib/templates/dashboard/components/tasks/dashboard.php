<div class="psp-my-tasks-section">

	<input id="psp-ajax-url" type="hidden" value="<?php echo admin_url(); ?>admin-ajax.php">
	<input id="psp-task-style" type="hidden" value="Yes">

	<div class="psp-h2 psp-box-title"><?php esc_html_e( 'Your Tasks', 'psp_projects' ); ?></div>

	<div class="psp-grid-row psp-masonry">
		<?php
		foreach( $tasks as $task ) {
			include( psp_template_hierarchy( 'dashboard/components/tasks/summary.php' ) );
		} ?>
	</div> <!--/.psp-grid-row-->

</div> <!--/.psp-archive-section.psp-my-tasks-->
