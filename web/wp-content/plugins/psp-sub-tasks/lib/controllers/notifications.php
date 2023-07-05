<?php
// 	add_action( 'psp_fe_before_update_task_complete', $post_id, $task, $old_task, $phases, $phase_index, $task_id );
add_action( 'psp_fe_before_update_task_complete', 'psp_st_user_assigned_notification', 10, 6 );
function psp_st_user_assigned_notification( $post_id, $task, $old_task = null, $phases, $phase_index, $task_id ) {


     if( empty($task['sub_task']) ) {
          return;
     }

     foreach( $task['sub_task'] as $task ) {

          if( empty($task['assigned']) ) {
               continue;
          }

          if( empty($old_task['assigned']) ) {
               $users = $task['assigned'];
          } else {
               $users = array_diff( $old_task['assigned'], $task['assigned'] );
          }

          if( !empty($users) ) {

               foreach( $users as $user_id ) {

                    $notification = array(
                        'post_id'	=>	$post_id,
                        'project_id'	=>	$post_id,
                        'user_id'	=>	$user_id,
                        'user_ids'	=>	array( $user_id ),
                        'phases'	=>	array(
                             $phase_index => array(
                                  'phase_title'	=>	stripslashes($phases[$phase_index]['title']),
                                  'tasks'	=>	array(
                                       array(
                                            'name'		=>	$task['task'],
                                            'task_id'	=>	$task['ID'], // Legacy
                                            'due_date'	=>	$task['due_date'],
                                            'status'	=>	$task['status']
                                       )
                                  )
                             )
                        )
                    );

                    // wp_send_json_error( array( $notification ) );

                    // Do the action
                    do_action( 'psp_notify', 'task_assigned', $notification );

               }

          }

     }


}
