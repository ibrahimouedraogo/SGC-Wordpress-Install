<?php
$post_id		= ( isset($post_id) && !empty($post_id ) ? $post_id : get_the_ID() );
$task_class 	=	'task-item task-item-' . $task['ID'] . ' psp-task-id-' . $task['task_id'];
$task_class 	.= 	( $task['status'] == 100 ? ' complete' : '' );
$assigned 		= 	$task['assigned'];
$task['status']	=	( empty( $task['status'] ) ? 0 : $task['status'] );
$task['status'] = 	apply_filters( 'psp_task_status', $task['status'], $post_id, $phase_index, $task['ID'], $task );
$task_documents = 	psp_parse_task_documents( get_field( 'documents', $post_id ), $task['task_id'] );

do_action( 'psp_before_task_entry', $post_id, $phase_index, $task['ID'], $phases, $phase ); ?>

<div class="<?php echo esc_attr( apply_filters( 'psp_task_classes', $task_class, $post_id, $phase_index, $task['ID'], $phases, $phase ) ); ?>" data-progress="<?php echo $task['status']; ?>">

	<?php do_action( 'psp_before_task_name', $post_id, $phase_index, $task['ID'], $phases, $phase ); ?>

	<?php

	$task_name = apply_filters( 'psp_task_name', $task['task'], $post_id, $phase_index, $task['ID'] );
	$target_id = 'phase-' . $phase_index . '-task-' . $task['ID'];

	// Logic the phase ID TODO: This is causing the issue here!
	if( empty($phase_id) ) {

		$phase_id = '';

		if( isset( $phase['psp_phase_id'] ) ) {
			$phase_id = $phase['psp_phase_id'];
		} elseif( isset( $phase['phase_id']) ) {
			$phase_id = $phase['phase_id'];
		}

	}

	$task_panel_atts = apply_filters( 'psp_task_panel_dashboard_attributes', array(
		'task_index'		=>	$task['ID'],
		'task_id'			=>  $task['task_id'],
		'task_description'	=>	esc_attr($task['task_description']),
		'phase_index'		=>	$phase_index,
		'phase_id'		=>  $phase_id,
		'project'			=>	$post_id,
		'project_name'		=>  get_the_title( $post_id ),
		'project_permalink' =>	get_permalink( $post_id ),
	), $post_id, $phase_index, $task['ID'] ); ?>

	<a id="<?php echo esc_attr($target_id); ?>" class="psp-task-title" href="<?php echo esc_url( '#psp-offcanvas-task-details' ); // ' . $task['task_id'] ); ?>"
		<?php foreach( $task_panel_atts as $att => $val ): ?>
			data-<?php echo $att; ?>="<?php echo esc_attr( $val ); ?>"
		<?php endforeach; ?>
			>
		<strong><?php echo esc_html($task_name); ?></strong>
		<span class="psp-view-link"><i class="fa fa-angle-right"></i></span>
	</a>

	<?php do_action( 'psp_after_task_name', $post_id, $phase_index, $task['ID'], $phases, $phase, $task ); ?>

	<b class="after-task-name">

		<?php $after_task_name_items_count = 0; ?>

		<?php
		if( !empty($task['assigned']) ):
			if( is_array( $task['assigned'] ) ): ?>
				<?php foreach( $task['assigned'] as $user ):
					if( $user == 'unassigned' || $user == 'Array' ) {
						continue;
					} ?>
					<b class="psp-assigned-to">
						<?php
						echo get_avatar( $user ); ?>
						<span class="text"><?php echo apply_filters( 'psp_task_assigned', psp_username_by_id( $user ), $post_id, $phase_index, $task[ 'ID' ] ); ?></span>
					</b>
				<?php endforeach; ?>
			<?php else: ?>
				<b class="psp-assigned-to">
					<?php
					echo get_avatar( $task['assigned'] ); ?>
					<span class="text"><?php echo apply_filters( 'psp_task_assigned', psp_username_by_id( $task['assigned'] ), $post_id, $phase_index, $task[ 'ID' ] ); ?></span>
				</b>
			<?php endif; ?>

			<?php $after_task_name_items_count++; ?>

		<?php endif; ?>

		<?php
		if( isset($task['due_date']) && !empty($task['due_date']) ) :

			$date 	= strtotime( $task[ 'due_date' ] );
			$format = get_option( 'date_format' );

			$date_class = ( $date < strtotime( 'today' ) ? 'late' : '' ); ?>

			<b class="psp-task-due-date <?php echo $date_class; ?>">
				<i class="psp-fi-icon psp-fi-calendar"></i>
				<span class="text"><?php echo date_i18n( $format, $date ); ?></span>
			</b>

			<?php $after_task_name_items_count++;

		endif;

		if ( $task_documents && $document_count = count( $task_documents ) ): ?>

			<b class="psp-task-documents js-open-task-panel" data-target="<?php echo esc_attr($target_id); ?>">
				<i class="fa fa-files-o"></i>
				<span class="text"><?php echo esc_html($document_count); ?></span>
			</b>

			<?php $after_task_name_items_count++;

		endif;

		// We always "show" comment count since this could be updated from 0 to 1
		$task_comment_count = psp_get_task_comment_count( $task['task_id'], $post_id ); ?>

		<b class="psp-task-discussions js-open-task-panel<?php echo ( ! $task_comment_count ) ? ' hidden' : ''; ?>" data-target="<?php echo esc_attr($target_id); ?>">
			<i class="psp-fi-icon psp-fi-discussion"></i>
			<span class="text"><?php echo esc_html($task_comment_count); ?></span>
		</b>

		<?php $after_task_name_items_count++;

		// $after_task_name_items_count is passed by reference in the chance that none of the above were true, so that if one extension were to add an item, then it could let other extensions know that it did and they may want to add a separator
		do_action_ref_array( 'psp_after_task_assigned', array( $post_id, $phase_index, $task['ID'], $phases, $phase, &$after_task_name_items_count ) ); ?>

	</b> <!--/.after-task-name-->

	<?php if(psp_can_edit_task( $post_id, $phase_index, $task['ID'] )) { ?>

		<span class="psp-task-edit-links">

			<span class="psp-task-edit-links__actions">
				<?php do_action( 'psp_task_edit_links_start', $post_id, $phase_index, $task['ID'] ); ?>

				<a href="#edit-task-<?php echo $task['ID']; ?>" class="task-edit-link"><b class="fa fa-adjust"></b> <?php _e('update','psp_projects'); ?></a>

				<?php $task_atts = apply_filters( 'psp_task_attributes', array(
					'target'			=>	$task['ID'],
					'task'				=>	$task['ID'],
					'phase'				=>	$phase_index,
					'project'			=>	$post_id,
					'phase-auto'		=>	( isset($phase_auto[0]) && $phase_auto[0] !== NULL ? $phase_auto[0] : 'No' ),
					'overall-auto'		=>	( isset($overall_auto[0]) && $overall_auto[0] !== NULL ? $overall_auto[0] : 'No' ),
				), $post_id, $phase_index, $task['ID'] ); ?>

				<a href="#" class="complete-task-link"
					<?php foreach( $task_atts as $att => $val ): ?>
						data-<?php echo $att; ?>="<?php echo esc_attr( $val ); ?>"
					<?php endforeach; ?>
					>
					<b class="fa fa-check"></b>
					<?php esc_html_e( 'complete', 'psp_projects' ); ?>
				</a>

				<?php do_action( 'psp_task_edit_links_before_select', $post_id, $phase_index, $task['ID'], $phases, $phase ); ?>
			</span>

			<span id="edit-task-<?php echo $task['ID']; ?>" class="task-select">

				<span class="psp-select-wrapper">
					<select id="edit-task-select-<?php echo $phase_index . '-' . $task['ID']; ?>" class="edit-task-select">
						<option value="<?php echo esc_attr( $task['status'] ); ?>"><?php echo $task['status']; ?>%</option>
						<?php $values = apply_filters( 'psp_task_values', array(
							'0'		=>	'0%',
							'5'		=>	'5%',
							'10'	=>	'10%',
							'15'	=>	'15%',
							'20'	=>	'20%',
							'25'	=>	'25%',
							'30'	=>	'30%',
							'35'	=>	'35%',
							'40'	=>	'40%',
							'45'	=>	'45%',
							'50'	=>	'50%',
							'55'	=>	'55%',
							'60'	=>	'60%',
							'65'	=>	'65%',
							'70'	=>	'70%',
							'75'	=>	'75%',
							'80'	=>	'80%',
							'85'	=>	'85%',
							'90'	=>	'90%',
							'95'	=>	'95%',
							'100'	=>	'100%'
						) );
						foreach( $values as $value => $label ): ?>
							<option value="<?php echo esc_attr( $value ); ?>"><?php echo esc_html( $label ); ?></option>
						<?php endforeach; ?>
					</select>
				</span>

				<?php
				$data_atts = apply_filters( 'psp_single_task_data_atts', array(
					'target'		=>	$task['ID'],
					'task'		=>	$task['ID'],
					'phase'		=>	$phase_index,
					'project'		=>	$post_id,
					'phase-auto'	=>	isset($phase_auto[0]) ? $phase_auto[0] : false,
					'overall-auto'	=>	isset($overall_auto[0]) ? $overall_auto[0] : false
				)); ?>

				<input type="submit" name="save" value="save" class="task-save-button" <?php foreach( $data_atts as $att => $value ) echo 'data-' . $att . '="' . esc_attr($value) . '" '; ?>>

		   </span>

		   <?php do_action( 'psp_task_edit_links_end', $post_id, $phase_index, $task['ID'], $phases, $phase ); ?>

		</span>


	<?php } ?>

	<?php do_action( 'psp_before_task_progress', $post_id, $phase_index, $task['ID'], $phases, $phase ); ?>

	<span class="psp-progress-bar"><em class="status psp-<?php echo $task[ 'status' ]; ?>"></em></span>

	<?php do_action( 'psp_after_task_progress', $post_id, $phase_index, $task['ID'], $phases, $phase ); ?>

</div>

<?php do_action( 'psp_after_task_entry', $post_id, $phase_index, $task['ID'], $phases, $phase ); ?>
