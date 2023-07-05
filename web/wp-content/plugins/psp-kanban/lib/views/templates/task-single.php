<?php
$task = psp_get_task_by_id( $task_id, $post_id, $lane_phases );
$task_phase = psp_get_task_phase_by_id( $task_id, $post_id, $lane_phases );

if( $task ): ?>
     <div class="psp-lane__task psp-card task-item psp-task-id-<?php echo esc_attr($task['task_id']); ?>" data-task_id="<?php echo esc_attr($task['task_id']); ?>" data-phase_id="<?php echo esc_attr($task_phase['ID']); ?>">

          <?php
          $task_status = ( !empty($task['status']) ? $task['status'] : 0 );

          $task_panel_atts = apply_filters( 'psp_task_panel_dashboard_attributes', array(
     		'task_index'		=> $task['ID'],
     		'task_id'			=> $task['task_id'],
     		'task_description'	=> esc_attr($task['task_description']),
     		'phase_index'		=> $task_phase['index'],
     		'phase_id'		=> $task_phase['ID'],
     		'project'			=> $post_id,
     		'project_name'		=> get_the_title( $post_id ),
     		'project_permalink' => get_permalink( $post_id ),
          ), $post_id, $task_phase['index'], $task['ID'] ); ?>

          <a href="#psp-offcanvas-task-details" class="psp-task-title"
               <?php foreach( $task_panel_atts as $att => $val ): ?>
                    data-<?php echo $att; ?>="<?php echo esc_attr( $val ); ?>"
               <?php endforeach; ?>>
               <?php echo esc_html( $task['task'] ); ?>

               <span class="psp-task__phase"><?php echo esc_html( $lane_phases[$task_phase['index']]['title']); ?></span>

          </a>

          <?php do_action( 'psp_kb_after_task_name', $post_id, $task_phase['index'], $task['ID'], $lane_phases, $task_phase, $task ); ?>

          <div class="psp-lane__task-meta">
               <?php
     		if( !empty($task['assigned']) ):
                    foreach( $task['assigned'] as $user ):

                         if( $user == 'unassigned' || $user == 'Array' ) {
     						continue;
     				} ?>

                         <span class="psp-assigned-to">
     						<?php
     						echo get_avatar( $user ); ?>
     						<span class="text"><?php echo apply_filters( 'psp_task_assigned', psp_username_by_id( $user ), $post_id, $phase_index, $task[ 'ID' ] ); ?></span>
     				</span>
                    <?php
                    endforeach;
     		endif; ?>

     		<?php
     		if( isset($task['due_date']) && !empty($task['due_date']) ) :

     			$date   = strtotime( $task[ 'due_date' ] );
     			$format = get_option( 'date_format' );

     			$date_class = ( $date < strtotime( 'today' ) ? 'late' : '' ); ?>

     			<b class="psp-task-due-date <?php echo $date_class; ?>">
     				<i class="psp-fi-icon psp-fi-calendar"></i>
     				<span class="text"><?php echo date_i18n( $format, $date ); ?></span>
     			</b>

     			<?php $after_task_name_items_count++;

     		endif;

               // TODO: Should make this better, one query vs query per task
               $task_documents = psp_parse_task_documents( get_field( 'documents', $post_id ), $task['task_id'] );

     		if ( $task_documents && $document_count = count( $task_documents ) ): ?>

     			<b class="psp-task-documents js-open-task-panel" data-target="<?php echo esc_attr($target_id); ?>">
     				<i class="fa fa-files-o"></i>
     				<span class="text"><?php echo esc_html($document_count); ?></span>
     			</b>

     			<?php $after_task_name_items_count++;

     		endif;

     		// We always "show" comment count since this could be updated from 0 to 1
     		$task_comment_count = psp_get_task_comment_count( $task['task_id'], $post_id ); ?>

     		<span class="psp-task-discussions js-open-task-panel<?php echo ( ! $task_comment_count ) ? ' hidden' : ''; ?>" data-target="<?php echo esc_attr($target_id); ?>">
     			<i class="psp-fi-icon psp-fi-discussion"></i>
     			<span class="text"><?php echo esc_html($task_comment_count); ?></span>
     		</span>

     		<?php $after_task_name_items_count++;

     		// $after_task_name_items_count is passed by reference in the chance that none of the above were true, so that if one extension were to add an item, then it could let other extensions know that it did and they may want to add a separator
     		do_action_ref_array( 'psp_after_task_assigned', array( $post_id, $phase_index, $task['ID'], $lane_phases, $task_phase, &$after_task_name_items_count ) ); ?>
	     </div> <!--/.psp-lane__task-meta-->

          <?php include('task/edit.php'); ?>

          <span class="psp-progress-bar"><em class="status psp-<?php echo esc_attr($task_status); ?>"></em></span>

     </div> <!--/.psp-card-->
<?php endif; ?>
