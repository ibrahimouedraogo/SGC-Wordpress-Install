<?php
/**
 * THIS IS DEPRICATED
 * @var [type]
 */


/* Setup Variables */
if( !isset( $post_id ) ) { global $post; $post_id = $post->ID; }

$tasks			= array();
$count 			= 0;
$task_id 		= 0;
$overall_auto	= get_field( 'automatic_progress', $post_id );
$phase_auto		= get_field( 'phases_automatic_progress', $post_id );

while( has_sub_field( 'tasks', $post_id ) ) {

	$completion = get_sub_field( 'status' );

}


	/* Get Settings */
	$overall_auto = get_field('automatic_progress',$post_id); $phase_auto = get_field('phases_automatic_progress',$post_id);

	$output = '';

    // Loop through all the tasks.
    while( has_sub_field( 'tasks', $post_id ) ) {

        $taskCompleted = get_sub_field( 'status' );

        // Continue if you want to show incomplete tasks only and this task is complete
        if( ( $task_style == 'incomplete' ) && ( $taskCompleted == '100' ) ) { continue; }

        // Continue if you want to show completed tasks and this task is not complete
        if( ( $task_style == 'complete' ) && ( $taskCompleted != '100' ) ) { continue; }

        $count++;

        // If the user can edit, display a link
        if( ( psp_can_edit_task( $post->ID, $phase_id, $task_id ) ) && ( get_post_type() == 'psp_projects' ) ) {
            $link = '<a href="#edit-task-' . $task_id . '" class="task-edit-link"><b class="fa fa-pencil"></b> '.__('update','psp_projects').'</a> <a href="#" class="complete-task-link" data-target="'.$task_id.'" data-task="'.$task_id.'" data-task="'.$task_id.'" data-phase="'.$phase_id.'" data-project="'.$post_id.'" data-phase-auto="'.$phase_auto[0].'" data-overall-auto="'.$overall_auto[0].'"><b class="fa fa-check"></b> '.__('complete','psp_projects').'</a>';
        } else {
            $link = null;
        }

		/** FILTER: psp_task_class **/
		$task_class = apply_filters( 'psp_task_class', ( $taskCompleted == '100' ? 'complete' : 'null' ) );
		$assigned 	= get_sub_field( 'assigned' );
		$date		= get_sub_field( 'due_date' );

		if( ( !empty( $assigned ) ) && ( $assigned != 'unassigned' ) && ( $assigned != 'null' ) ) {

			$user = get_userdata($assigned);

			/** FILTER: psp_task_assigned **/
			$assigned = '<b class="psp-assigned-to">' . apply_filters( 'psp_task_assigned' , psp_username_by_id( $user->ID ) ).'</b> ';

		} else {

			$assigned = NULL;

		}

		if( !empty( $date ) ) {

			$date_class = ( strtotime( $date ) < strtotime( 'today' ) ? 'late' : '' );

			$date_marker = '<b class="psp-task-due-date ' . $date_class . '">' . $date . '</b>';

		} else {

			$date_maker = NULL;

		}

		/** HOOK: psp_before_task **/
		$output .= do_action( 'psp_before_task', $post_id, $phase_id, $task_id );

		/** FILTER: psp_task_name **/
		$task_name 		= apply_filters( 'psp_task_name' , get_sub_field( 'task' ), $post_id, $phase_id, $task_id );
		$task_progress 	= apply_filters( 'psp_task_progress', get_sub_field( 'status', $post_id ) );
		$task_raw_id 	= apply_filters( 'psp_task_raw_id', get_sub_field('task_id'), $post_id, $phase_id, $task_d );

		if( empty( trim( $task_progress ) ) ) $task_progress = 0;

        $output .= '<li class="' . $task_class . ' task-item task-id-' . $task_raw_id . ' task-item-' . $task_id . '" data-progress="' . $task_progress . '">';

        $output .= do_action( 'psp_before_task_name', $post_id, $phase_id, $task_id ) . '<strong>' . $task_name . ' ' . $assigned . ' ' . $date_marker . ' '. $link . '</strong> <span><em class="status psp-' . get_sub_field( 'status', $post_id ) . '"></em></span>';

	    if( ( psp_can_edit_task( $post->ID, $phase_id, $task_id ) ) && ( get_post_type() == 'psp_projects' ) ) {
            $output .= '<div id="edit-task-'.$task_id.'" class="task-select">

                                <select id="edit-task-select-' . $phase_id . '-' . $task_id . '" class="edit-task-select">
                                    <option value="' . get_sub_field( 'status', $post_id ).'">' . get_sub_field( 'status', $post_id ) . '%</option>
                                    <option value="0">0%</option>
                                    <option value="10">10%</option>
                                    <option value="20">20%</option>
                                    <option value="30">30%</option>
                                    <option value="40">40%</option>
                                    <option value="50">50%</option>
                                    <option value="60">60%</option>
                                    <option value="70">70%</option>
                                    <option value="80">80%</option>
                                    <option value="90">90%</option>
                                    <option value="100">100%</option>
                                </select>

								<input type="submit" name="save" value="save" class="task-save-button" data-task="' . $task_id . '" data-task="' . $task_id . '" data-phase="' . $phase_id . '" data-project="' . $post_id . '" data-phase-auto="' . $phase_auto[ 0 ] . '" data-overall-auto="' . $overall_auto[ 0 ] . '">

                            </div>';
        }
        $output .= '</li>';

		/** HOOK: psp_after_task **/
		$output .= do_action( 'psp_after_task', $post_id, $phase_id, $task_id );

        $task_id++;

    }

    array_push( $taskList , $output );
    array_push( $taskList , $count );
