<?php
function psp_update_sub_task_fe() {

    $project_id     = $_POST['project_id'];
    $phase_id       = $_POST['phase_id'];
    $parent_id      = $_POST['parent_id'];
    $task_index     = intval($_POST['task_id']);
    $progress       = $_POST['progress'];
    $phases         = get_field( 'phases', $project_id );
    $cuser          = wp_get_current_user();

    $phase_index   = intval( psp_get_phase_index_by_id( $phase_id, $project_id ) );
    $parent_index  = intval( psp_get_task_index_by_id( $parent_id, $phase_id, $project_id ) );

    if( empty($phases[$phase_index]['tasks'][$parent_index]['sub_task'][$task_index]) ) {
         wp_send_json_error( array( 'success' => false, 'phase_id' => $phase_id, 'parent_id' => $parent_id, 'phase_index' => $phase_index, 'parent_index' => $parent_index, 'task_index' => $task_index ) );
    }

    $phases[$phase_index]['tasks'][$parent_index]['sub_task'][$task_index]['status'] = $progress;

    $parent_task_progress = psp_calculate_parent_task_progress( $phases[$phase_index]['tasks'][$parent_index] );
    $phases[$phase_index]['tasks'][$parent_index]['status'] = $parent_task_progress;

	update_field( 'phases', $phases, $project_id );

	$phase_info    = $phases[$phase_index];
	$task_info     = $phase_info['tasks'][$parent_index]['sub_tasks'][$task_index];
	$task_title    = $task_info['task'];
     $user_id       = $cuser->ID;

	/**
	 * Fires when updating a task to a new progress.
	 *
	 * @since {{VERSION}}
	 */
	do_action( 'psp_update_task', $task_info, $progress, $task_index, $project_id, $phase_index, $user_id );

	if ( $progress == '100' ) {

		do_action( 'psp_notify', 'task_complete', array(
			'task_title' => $task_title,
			'task_info'  => $task_info,
			'project_id' => $project_id,
			'phase_info' => $phase_info,
			'phase_id'   => $phase_index,
            'post_id'    => $project_id,
            'user_id'    => $user_id
		) );
	}

    $current_progress 	= get_post_meta( $project_id, '_psp_current_progress', true );
    $new_progress		= psp_compute_progress( $project_id );

    if( $new_progress != $current_progress ) {
        // Progress has moved forward so we fire an acction for the current progress
        do_action( 'psp_project_progress_change', $project_id, $new_progress, $current_progress );
    }

    update_post_meta( $project_id, '_psp_current_progress', $new_progress );

    wp_send_json_success( array( 'success' => true, 'parent_progress' => $parent_task_progress ) );

}
add_action( 'wp_ajax_nopriv_psp_update_sub_task_fe', 'psp_update_sub_task_fe' );
add_action( 'wp_ajax_psp_update_sub_task_fe', 'psp_update_sub_task_fe' );

function psp_calculate_parent_task_progress( $task ) {

    if( !isset( $task['sub_task'] ) || empty($task['sub_task']) ) {
        return $task['status'];
    }

    $sub_task_count     = count( $task['sub_task'] );
    $sub_task_progress  = 0;

    foreach( $task['sub_task'] as $sub_task ) {
        $sub_task_progress += $sub_task['status'];
    }

    // If there is no progress recorded, return 0
    if ( $sub_task_progress == 0 ) return 0;

	$sub_task_progress = ceil( $sub_task_progress / $sub_task_count );

	if ( ( $sub_task_progress % 5 ) < 3 ) {

		if ( ( $sub_task_progress % 5 ) !== 0 ) {

			// Round down to nearest multiple of 5
			return floor( $sub_task_progress / 5 ) * 5;

		}

	}

	// Round up to nearest multiple of 5
	return ceil( $sub_task_progress / 5 ) * 5;

}

add_filter( 'psp_fe_update_task_data', 'psp_add_subtask_to_front_end_task_data', 10, 6 );
function psp_add_subtask_to_front_end_task_data( $task, $post_id, $phase_index, $task_index, $phases, $post_vars ) {

    if( !isset($phases[$phase_index]['tasks'][$task_index]['sub_task']) || empty($phases[$phase_index]['tasks'][$task_index]['sub_task']) ) return $task;

    $task['sub_task'] = $phases[$phase_index]['tasks'][$task_index]['sub_task'];

    return $task;

}
