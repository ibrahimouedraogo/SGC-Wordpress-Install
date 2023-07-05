<nav id="psp-offcanvas-task-details" class="psp-modal psp-hide">

	<div class="psp-modal-content content">

		<a class="modal-close psp-modal-x" href="#"><i class="fa fa-close"></i></a>

		<div class="psp-modal-header">
			 <div class="psp-h2"><?php esc_html_e( 'Task', 'psp_projects' ); ?></div>
		</div>

		<div class="meta">

			<?php do_action( 'psp_task_modal_before_title' ); ?>

			<div class="psp-task-title">
				<div class="psp-h3"></div>
			</div>

			<?php do_action( 'psp_task_modal_before_description' ); ?>

			<div class="psp-task-table-description"></div>

			<?php do_action( 'psp_task_modal_before_assigned' ); ?>

			<span class="assigned hidden">
				<i class="fa fa-fw fa-user"></i>
				<span class="text"></span>
			</span>

			<?php do_action( 'psp_task_modal_before_due' ); ?>

			<span class="due-date hidden">
				<i class="psp-fi-icon psp-fi-calendar"></i>
				<span class="text"></span>
			</span>

			<?php do_action( 'psp_task_modal_before_task_table' ); ?>

			<div class="psp-task-table">
				<div class="psp-task-table-status">
					<span class="psp-progress-bar"><em class="status"></em></span>
				</div>
			</div>
			<?php do_action( 'psp_task_modal_before_title' ); ?>

		</div>

		<?php

			do_action( 'psp_task_table_status_after' );

			// Key is the Ajax Action for population
			// Count determines whether or not a Count of items is shown in the Tab Title
			$task_panel_tabs = apply_filters( 'psp_task_panel_tabs', array(
				'psp_get_task_discussions' => array(
					'tab_id' => 'discussions',
					'tab_title' => __( 'Discussions', 'psp_projects' ),
					'tab_icon' => 'psp-fi-icon psp-fi-discussion',
					'count' => true,
					'default_content' => '',
				),
				'psp_get_task_documents' => array(
					'tab_id' => 'documents',
					'tab_title' => __( 'Documents', 'psp_projects' ),
					'tab_icon' => 'fa fa-files-o',
					'count' => true,
					'default_content' => '',
				),
			) );

		?>

		<div class="tabs" data-tabs id="task-panel-tabs">

			<?php

			$first = true;
			foreach ( $task_panel_tabs as $tab ) : ?>

			<div class="tabs-title<?php echo ( $first ) ? ' is-active' : ''; ?>">
				<a data-tabs-target="<?php echo $tab['tab_id']; ?>" href="#<?php echo $tab['tab_id']; ?>"<?php echo ( $first ) ? ' aria-selected="true"' : ''; ?>>
					<strong><?php echo $tab['tab_title']; ?></strong> <i class="<?php echo $tab['tab_icon']; ?>"></i><?php echo ( $tab['count'] ) ? ' <span id="' . $tab['tab_id'] . '-count"></span>' : ''; ?>
				</a>
			</div>

			<?php

				$first = false;

			endforeach; ?>

		</div>

		<div class="task-panel-tabs-content tabs-content" data-tabs-content="task-panel-tabs">

			<?php

			$first = true;

			foreach ( $task_panel_tabs as $ajax_action => $tab ) : ?>

				<div class="tabs-panel<?php echo ( $first ) ? ' is-active' : ''; ?>" id="<?php echo $tab['tab_id']; ?>" data-action="<?php echo $ajax_action; ?>">

					<i class="fa fa-fw fa-spin fa-spinner loading" aria-hidden="true"></i>

					<div class="content"><?php echo $tab['default_content']; ?></div>

				</div>

			<?php $first = false;

			endforeach; ?>

		</div>

	</div>

</nav>
