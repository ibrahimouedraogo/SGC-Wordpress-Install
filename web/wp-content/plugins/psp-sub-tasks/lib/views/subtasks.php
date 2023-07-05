<?php

add_action( 'psp_after_task_progress', 'psp_sub_task_list_view_project', 10, 5 );
function psp_sub_task_list_view_project( $post_id = null, $phase_index = null, $task_index = null, $phases, $phase ) {

    psp_sub_task_list( $post_id, $phase_index, $task_index, $phases );

}

/**
 * Outputs the Sub Tasks on the User Tasks View
 *
 * @param		integer $post_id  Post ID
 * @param		integer $phase_index Phase Index
 * @param		integer $task_index  Task Index
 * @param		array   $phases   All Phases
 * @param 		array   $phase    Current Phase in Loop
 *
 * @since		{{VERSION}}
 * @return		void
 */
add_action( 'psp_all_my_tasks_shortcode_after_task', 'psp_sub_task_list_my_tasks', 10, 5 );
function psp_sub_task_list_my_tasks( $post_id = null, $phase_id = null, $task_id = null, $phases, $phase ) {

    ob_start();

    psp_sub_task_list( $post_id, $phase_id, $task_id, $phases, $phase );

    $sub_tasks = ob_get_clean();

    if( !empty($sub_tasks) ): ?>
        <tr class="sub-task-row">
    		<td colspan="100"> <!-- Don't prove me wrong by making more than 100 columns, please -->
    			<?php echo $sub_tasks; ?>
    		</td>
    	</tr>
	<?php
    endif;

}

/**
 * Generates HTML for the Sub Tasks List
 *
 * @param		integer $post_id  Post ID
 * @param		integer $phase_index Phase Index
 * @param		integer $task_index  Task Index
 * @param		array   $phases   All Phases
 *
 * @since		{{VERSION}}
 * @return		void
 */

function psp_sub_task_list( $post_id = null, $phase_id = null, $task_id = null, $phases, $phase = null, $show_desc = false ) {

	if ( ! isset( $phases[$phase_id]['tasks'][$task_id]['sub_task'] ) || empty( $phases[$phase_id]['tasks'][$task_id]['sub_task'] ) ) {
        return false;
     }

	$s = 0;
     $overall_auto	= get_field( 'automatic_progress', $post_id );
	$phase_auto		= get_field( 'phases_automatic_progress', $post_id );

	$current_user = wp_get_current_user();

	// We don't need to show the bubble if no sub-tasks are assigned

     if( is_post_type_archive('psp_projects') && !empty($phases[$phase_id]['tasks'][$task_id]['sub_task']) ) {

          $assigned_tasks = 0;

		foreach( $phases[$phase_id]['tasks'][$task_id]['sub_task'] as $task ) {

               if( ( is_array($task['assigned']) && in_array( $current_user->ID, $task['assigned']) ) || $task['assigned'] == $current_user->ID ) {
                    $assigned_tasks++;
               }

		}

		if( $assigned_tasks == 0 ) {
			return;
		}

     }

     /*

     if( is_array($phases[$phase_id]['tasks'][$task_id]['assigned']) ) {

          if( is_post_type_archive('psp_projects') && !in_array( $current_user->ID, $phases[$phase_id]['tasks'][$task_id]['assigned'] ) ) {

     		$assigned_tasks = 0;

     		foreach( $phases[$phase_id]['tasks'][$task_id]['sub_task'] as $task ) {

     			if( $task['assigned'] == $current_user->ID ) {
     				$assigned_tasks++;
     			}

     		}

     		if( $assigned_tasks == 0 ) {
     			return;
     		}

          }

     } else {

     	if( is_post_type_archive('psp_projects') && $phases[$phase_id]['tasks'][$task_id]['assigned'] != $current_user->ID ) {

     		$assigned_tasks = 0;

     		foreach( $phases[$phase_id]['tasks'][$task_id]['sub_task'] as $task ) {

     			if( $task['assigned'] == $current_user->ID ) {
     				$assigned_tasks++;
     			}

     		}

     		if( $assigned_tasks == 0 ) {
     			return;
     		}

     	}

     } */ ?>

	<div class="psp-sub-tasks">
        <?php foreach( $phases[$phase_id]['tasks'][$task_id]['sub_task'] as $task ) :

            // Assign the parent task ID
            $task['parent_ID'] = $task_id;

            // Assign the task ID and increment
            $task['ID'] = $s; $s++;

            // Legacy: Support older versions of Panorama where you can only assign one
            if( is_array($task['assigned']) ) {

                 if( is_post_type_archive('psp_projects') && !in_array( $current_user->ID, $task['assigned'] ) && !in_array( $current_user->ID, $phases[$phase_id]['tasks'][$task_id]['assigned'] ) ) {
                      continue;
                 }

            } else {

			if( is_post_type_archive('psp_projects') && $task['assigned'] != $current_user->ID && $phases[$phase_id]['tasks'][$task_id]['assigned'] != $current_user->ID ) {
				continue;
			}

            }

            add_filter( 'psp_task_classes', 'psp_add_is_subtask_class' );

            include( PSP_ST_PATH . '/lib/views/subtask-template.php' );

            remove_filter( 'psp_task_classes', 'psp_add_is_subtask_class' );

        endforeach; ?>
   </div>

	<?php

}

add_filter( 'psp_task_classes', 'psp_add_has_subtask_class', 10, 6 );
function psp_add_has_subtask_class( $classes, $post_id, $phase_index = 0, $task_index = 0, $phases, $phase ) {

    if( !isset( $phases[$phase_index]['tasks'][$task_index]['sub_task'] ) || empty($phases[$phase_index]['tasks'][$task_index]['sub_task']) ) return $classes;

    $classes .= ' psp-has-subtask';

    return $classes;

}

function psp_add_is_subtask_class( $classes ) {

    return $classes .= ' psp-is-subtask';

}


/**
 * Determine whether or not the Current User can edit a Sub Task
 *
 * @param		integer $post_id        Post ID
 * @param		integer $phase_index       Phase Index
 * @param		integer $parent_task_id Parent Task Index
 * @param		integer $sub_task_id    Sub Task Index
 *
 * @since		{{VERSION}}
 * @return		boolean Can/Cannot Edit Sub Task
 */
function psp_can_edit_sub_task( $post_id, $phase_index, $parent_task_id, $sub_task_id ) {

	// If they have the Ability to edit the Parent Task, they should be able to edit all Sub Tasks
	if ( psp_can_edit_task( $post_id, $phase_index, $parent_task_id ) ) {
		return true;
	}

	$current_user = wp_get_current_user();

	$phases = get_field( 'phases', $post_id );

	if ( isset( $phases[ $phase_index ]['tasks'][ $parent_task_id ]['sub_task'][ $sub_task_id ]['assigned'] ) &&
	   $phases[ $phase_index ]['tasks'][ $parent_task_id ]['sub_task'][ $sub_task_id ]['assigned'] == $current_user->ID ) {
		return apply_filters( 'psp_can_edit_sub_task', true, $post_id, $phase_index, $parent_task_id, $sub_task_id );
	}

	return apply_filters( 'psp_can_edit_sub_task', false, $post_id, $phase_index, $parent_task_id, $sub_task_id );

}
