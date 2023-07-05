<?php
$post_id		= ( isset($post_id) && !empty($post_id ) ? $post_id : get_the_ID() );
$task_class 	=	'sub-task-item sub-task-item-' . $task['ID'] . ( $task['status'] == 100 ? ' complete' : '' );
$assigned 		= 	$task['assigned'];
$user			=	FALSE;
$task['status']	=	( empty( $task['status'] ) ? 0 : $task['status'] );
$task['status'] = 	apply_filters( 'psp_sub_task_status', $task['status'], $post_id, $phase_id, $task['ID'] );

if ( ! psp_can_edit_sub_task( $post_id, $phase_id, $task['parent_ID'], $task['ID'] ) ) {
	$task_class .= ' sub-task-edit-disabled';
}

if( ( !empty($assigned) ) && ( $assigned != 'unassigned' ) && ( $assigned != 'null' ) ) {
	$user	=	get_userdata( $assigned );
}

if( empty($phase_index) ) {
	$i = 0; foreach( $phases as $phase ) {
		if( $phase['phase_id'] == $phase_id ) {
			$phase_index = $i;
		}
	}
}


do_action( 'psp_before_sub_task_entry', $post_id, $phase_id, $task['ID'], $phases, $phase ); ?>

<div class="<?php echo esc_attr( apply_filters( 'psp_sub_task_classes', $task_class, $post_id, $phase_id, $task['ID'], $phases, $phase ) ); ?>" data-progress="<?php echo esc_attr($task['status']); ?>">

	<?php do_action( 'psp_before_sub_task_name', $post_id, $phase_id, $task['ID'], $phases, $phase ); ?>

	<strong><?php echo apply_filters( 'psp_sub_task_name', $task['task'], $post_id, $phase_id, $task['ID'] ); ?></strong>

	<?php do_action( 'psp_after_sub_task_name', $post_id, $phase_id, $task['ID'], $phases, $phase ); ?>

	<b class="after-task-name">

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

			<?php endif; ?>

		<?php
		if( isset($task['due_date']) && !empty($task['due_date']) ) :

			$date 		= strtotime($task['due_date']);
			$format		= get_option('date_format');
			$date_class = ( $date < strtotime('today') ? 'late' : '' );

			echo '<b class="psp-task-due-date ' . $date_class . '"><i class="psp-fi-icon psp-fi-calendar"></i> ' . date_i18n( $format, $date ) . '</b>';

		endif; ?>

		<?php do_action( 'psp_after_sub_task_assigned', $post_id, $phase_id, $task['ID'], $phases, $phase ); ?>

	</b> <!--/.after-task-name-->

	<?php if ( psp_can_edit_sub_task( $post_id, $phase_id, $task['parent_ID'], $task['ID'] ) ) { ?>

		<span class="psp-sub-task-edit-links">
			<span class="psp-sub-task-edit-links__actions">

				<?php do_action( 'psp_sub_task_edit_links_start', $post_id, $phase_id, $task['ID'] ); ?>

				<a href="<?php echo esc_attr( '#edit-task-' . $task['ID'] ); ?>" class="sub-task-edit-link"><b class="fa fa-adjust"></b> <?php esc_html_e('update','psp-subtasks'); ?></a>

				<?php $task_atts = apply_filters( 'psp_sub_task_attributes', array(
					'target'			=>	$task['ID'],
					'parent_id'			=>	$task['parent_ID'],
					'task'				=>	$task['ID'],
					'phase'				=>	$phase_id,
					'project'			=>	$post_id,
					'phase-auto'		=>	( isset($phase_auto[0]) && $phase_auto[0] !== NULL ? $phase_auto[0] : 'No' ),
					'overall-auto'		=>	( isset($overall_auto[0]) && $overall_auto[0] !== NULL ? $overall_auto[0] : 'No' ),
				), $post_id, $phase_id, $task['ID'] ); ?>

				<a href="#" class="complete-sub-task-link"
					<?php foreach( $task_atts as $att => $val ): ?>
						data-<?php echo $att; ?>="<?php echo esc_attr( $val ); ?>"
					<?php endforeach; ?>
					>
					<b class="fa fa-check"></b>
					<?php esc_html_e( 'complete', 'psp-subtasks' ); ?>
				</a>

				<?php do_action( 'psp_sub_task_edit_links_end', $post_id, $phase_id, $task['ID'], $phases, $phase ); ?>

			</span>

			<span id="<?php echo esc_attr( 'edit-sub-task-' . $task['ID'] ); ?>" class="sub-task-select">

				<span class="psp-select-wrapper">
					<select id="<?php echo esc_attr( 'edit-sub-task-select-' . $phase_id . '-' . $task['ID'] ); ?>" class="edit-sub-task-select">
						<option value="<?php echo esc_attr( $task['status'] ); ?>"><?php echo esc_html( $task['status'] . '%' ); ?></option>
						<?php $values = apply_filters( 'psp_sub_task_values', array(
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
				$data_atts = apply_filters( 'psp_single_sub_task_data_atts', array(
					'target'		=>	$task['ID'],
					'task'			=>	$task['ID'],
					'parent_id'		=>	$task['parent_ID'],
					'phase'			=>	$phase_id,
					'project'		=>	$post_id,
					'phase-auto'	=>	isset($phase_auto[0]) ? $phase_auto[0] : '',
					'overall-auto'	=>	isset($overall_auto[0]) ? $overall_auto[0] : '',
				)); ?>

				<input type="submit" name="save" value="save" class="sub-task-save-button" <?php foreach( $data_atts as $att => $value ) echo 'data-' . $att . '="' . esc_attr($value) . '" '; ?>>

	        </span>
	   </span>
   <?php } ?>

   <?php do_action( 'psp_before_sub_task_description', $post_id, $phase_id, $task['ID'], $phases, $phase ); ?>

      <?php if( !empty($task['task_description']) ): ?>
		 <div class="psp-sub-task-description">
			 <?php echo wp_kses_post( wpautop($task['task_description']) ); ?>
		 </div>
	 <?php endif; ?>

	<?php do_action( 'psp_before_sub_task_progress', $post_id, $phase_id, $task['ID'], $phases, $phase ); ?>

	<span class="psp-sub-progress-bar"><em class="status psp-<?php echo $task[ 'status' ]; ?>"></em></span>

	<?php do_action( 'psp_after_sub_task_progress', $post_id, $phase_id, $task['ID'], $phases, $phase ); ?>


</div>

<?php do_action( 'psp_after_sub_task_entry', $post_id, $phase_id, $task['ID'], $phases, $phase ); ?>
