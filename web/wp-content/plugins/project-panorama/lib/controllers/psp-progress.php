<?php
/**
 * psp-progress.php
 *
 * Controls progress and the caluclation of progress
 *
 * @category controller
 * @package psp-projects
 * @author Ross Johnson
 * @since 1.3.6
 */

 /**
  * Computes the total project progress and outputs it with a visual indication by phase ( DEPRICATED )
  *
  * DEPRICATED
  * @param integer $id post ID
  * @return HTML markup of a progress bar
  **/

 function psp_phased_complete( $id ) {

     if( get_field( 'automatic_progress', $id ) ) {

         $completed 		= 0;
         $phases 			= 0;
         $phase_completion 	= 0;
         $phase_total 		= 0;
         $task_index          = 0;

         $total_phases 		= count( get_field( 'phases', $id ) );

         $phase_breakdown 	= array();

         while(has_sub_field( 'phases', $id ) ) {

             $phase_info = array();

             // Add the row number into the array
             array_push( $phase_info, $phases );

             if( get_sub_field( 'weighting' ) ) {
                 $phases++;
                 $phase_total += 100 * get_sub_field( 'weight' );
             } else {
                 $phases++;
                 $phase_total += 100;
             }

             if(get_sub_field('auto_progress')) {

                 $tasks = 0;
                 $task_completion = 0;

                 while(has_sub_field('tasks')) {
                     $tasks++;
                     $task = get_row();
                     $task_completion += apply_filters( 'psp_task_status', intval( get_sub_field('status') ), $id, $phase_index, $task['psp_task_id'], $task );
                 }

                 if($tasks > 1) {

                     if(get_sub_field('weighting')) {

                         $relative_status = ceil($task_completion / $tasks / $total_phases * get_sub_field('weight'));
                         $actual_status = ceil($task_completion / $tasks);

                         array_push($phase_info,$relative_status);
                         array_push($phase_info,$actual_status);

                     } else {

                         $relative_status = ceil($task_completion / $tasks / $total_phases);
                         $actual_status = ceil($task_completion / $tasks);

                         array_push($phase_info,$relative_status);
                         array_push($phase_info,$actual_status);

                     }

                 } else {
                     $phase_completion += $task_completion;
                     array_push($phase_info,$task_completion);
                     array_push($phase_info,$task_completion);
                 }

             } else {
                 $phase_completion += get_sub_field('percent_complete');

                 $phase_total = ceil(get_sub_field('percent_complete') / $total_phases);

                 array_push($phase_info,$phase_total);
                 array_push($phase_info,get_sub_field('percent_complete'));

             }

             array_push($phase_breakdown,$phase_info);

             $phase_index++;

         }

         if($phase_total != 0) {

             echo '<p class="psp-progress">';
             $c = 1;
             $phases = get_field('phases');

             foreach($phase_breakdown as $phase) {

                 $pid = $phase[0];

                 if($c == 1) {
                     $color = 'blue';
                     $chex = '#3299BB';
                 } elseif ($c == 2) {
                     $color = 'teal';
                     $chex = '#4ECDC4';
                 } elseif ($c == 3) {
                     $color = 'green';
                     $chex = '#CBE86B';
                 } elseif ($c == 4) {
                     $color = 'pink';
                     $chex = '#FF6B6B';
                 } elseif ($c == 5) {
                     $color = 'maroon';
                     $chex = '#C44D58';
                     $c = 0;
                 }

                 echo '<span class="psp-'.$phase[1].' color-'.$color.'" title="'.$phases[$pid]['title'].' - '.$phase[2].'% complete"></span>';
                 $c++;
             }
             // return ceil($phase_completion / $phase_total * 100);
             echo '</p>';
         } else {
             echo '<p class="psp-progress"><span class="psp-0"><b>0%</b></span></p>';

         }


     } else {

         echo '<p class="psp-progress"><span class="psp-'.get_field('percent_complete',$id).'"><b>'.get_field('percent_complete',$id).'%</b></span></p>';

     }

 }

 /**
  * Computes the total project progress based on total number of hours
  *
  *
  * @param integer $id post ID
  * @return a string containing a number from 1 - 100 (percentage of completion)
  **/

 function psp_hourly_progress( $id, $type ) {

     // Count the number of hours

     $total_hours 			= 0;
     $completed_hours 		= 0;
     $phase_index             = 0;

     while( has_sub_field( 'phases' , $id ) ) {

         $tasks 			= 0;
         $task_completion 	= 0;
         $phase_total 		= 0;

         if( $type == 'Hours' ) {
             $total_hours 		+= intval(get_sub_field('hours'));
             $phase_hours 		= intval(get_sub_field('hours'));
         }

         if( $type == 'Percentage' ) {
             $total_hours 		+= intval(get_sub_field('percentage'));
             $phase_hours 		= intval(get_sub_field('percentage'));
         }

         while( has_sub_field( 'tasks' ) ) {

            $tasks++;
            $task = get_row();
 		  $task_completion += apply_filters( 'psp_task_status', intval( get_sub_field('status') ), $id, $phase_index, $task['psp_task_id'], $task );

 		}

          $phase_index++;

         // If a phase doesn't have any tasks, skip
         if( 0 === $tasks ) continue;

         $phase_total 		= $tasks * 100;
         $completed_hours 	+= ( ( $task_completion / $phase_total ) * $phase_hours );

     }

     if( $completed_hours != 0 ) {

         return apply_filters( 'psp_hourly_progress', ceil( $completed_hours / $total_hours * 100 ), $id );

     } else {

         return apply_filters( 'psp_hourly_progress', '0', $id );

     }

 }

 /**
  * Computes the total project progress by weighting the phases
  *
  *
  * @param integer $id post ID
  * @return a string containing a number from 1 - 100 (percentage of completion)
  **/

 function psp_weighted_progress( $id ) {

     $phase_completion 	= 0;
     $phase_total 		= 0;
     $phase_index        = 0;

     while( has_sub_field( 'phases' , $id ) ) {

        $weight 			= get_sub_field( 'weight' );
 		$tasks 				= 0;
 		$task_completion 	= 0;

 		// Calculate the weighted value of this phase

         if( empty( $weight ) ) {

             $phase_total += 100;

         } else {

             $phase_total += 100 * get_sub_field('weight');

 		}

 		// Compute the percentage complete of the phase based on task completion

         while( has_sub_field( 'tasks' ) ) {

             $tasks++;
             $task = get_row();

             $task_completion += apply_filters( 'psp_task_status', intval( get_sub_field('status') ), $id, $phase_index, $task['psp_task_id'], $task );

         }

         if( $tasks > 1 ) {

             $weight_calc = get_sub_field('weight');

             if( empty( $weight_calc ) ) { $weight_calc = 1; }

             $phase_completion += ceil( $task_completion / $tasks * $weight_calc );

         } else {

             $phase_completion += $task_completion;

 		}

          $phase_index++;

     }

    if($phase_total != 0) {
        return apply_filters( 'psp_hourly_progress', ceil($phase_completion / $phase_total * 100), $id );
 	} else {
 	    return apply_filters( 'psp_hourly_progress', '0', $id );
 	}

 }

 /**
  * Computes the total project progress with no weighting or hours
  *
  *
  * @param integer $id post ID
  * @return a string containing a number from 1 - 100 (percentage of completion)
  **/

 function psp_standard_progress( $id ) {

     $phase_completion 	= 0;
     $phase_total 		= 0;
     $tasks 			= 0;
     $task_completion 	= 0;
     $phase_index        = 0;

     while( has_sub_field( 'phases' , $id ) ) {

         $phase_total += 100;

         while( has_sub_field( 'tasks' ) ) {
             $tasks++;
             $task = get_row();
             $task_completion += apply_filters( 'psp_task_status', intval( get_sub_field('status') ), $id, $phase_index, $task['psp_task_id'], $task );
         }

         if($tasks > 0) {
             $phase_completion += ceil( $task_completion / $tasks );
 		}

          $phase_index++;

     }

     if( $phase_total != 0 ) {
         return apply_filters( 'psp_standard_progress', ceil( $phase_completion / $phase_total * 100 ), $id );
     } else {
         return apply_filters( 'psp_standard_progress', '0', $id );
 	}

 }

 function psp_phase_progress( $id ) {

    $total      = 0;
    $completion = 0;

    while( have_rows( 'phases', $id ) ) { the_row();
        $total      += 100;
        $completion += get_sub_field( 'percent_complete' );
    }

    if( $total == 0 ) {
        return apply_filters( 'psp_phase_progress', 0, $id );
    }

    return apply_filters( 'psp_phase_progress', ceil( $completion / $total * 100 ), $id );

 }

 function psp_get_phase_progress( $post_id = null, $phase_id ) {

     $post_id = $post_id == null ? get_the_ID() : $post_id;

     $phases = get_field( 'phases', $post_id );

     $tasks             = 0;
     $task_completion   = 0;

     foreach( $phases[$phase_id]['tasks'] as $task ) {
         $tasks++;
         $task_completion += apply_filters( 'psp_task_status', intval( $task['status'] ), $id, $phase_id, $task['psp_task_id'], $task );

     }

     if( $tasks == 0 || $task_completion == 0 ) return apply_filters( 'psp_get_phase_progress', 0, $post_id, $phase_id );

     return apply_filters( 'psp_get_phase_progress', floor( 100 * ( $task_completion / ( $tasks * 100 ) ) ) );

 }

 /**
  * Computes the total project progress
  *
  *
  * @param integer $id post ID
  * @return a string containing a number from 1 - 100 (percentage of completion)
  **/

 function psp_compute_progress( $id ) {

 	// Check to see if we're doing a calculation or returning a field value

     if( get_field( 'automatic_progress' , $id ) ) {

         $completed 		= 0;
         $phases 			= 0;
         $phase_completion 	= 0;
         $phase_total 		= 0;

         if ( get_field( 'phases_automatic_progress' , $id ) ) {

 			// Phases are auto calculated as well, see if there is any weighting

             if ( get_field( 'progress_type' , $id ) == 'Hours' || get_field( 'progress_type', $id ) == 'Percentage' ) {

 				// Hourly calculation
                 return psp_hourly_progress( $id, get_field( 'progress_type', $id ) );

             } elseif ( get_field( 'progress_type' , $id ) == 'Weighting' ) {

 				// Weighted calculation
                 return psp_weighted_progress( $id );

             } else {

                 // Just add up the total phases and progress
                 return psp_standard_progress( $id );

 			}

         } else {

             // Just add up the total phases and progress
             return psp_phase_progress( $id );

         }

     } else {

         // Not automatically computing progress, just return the slider value
         return ( get_field( 'percent_complete', $id ) ? intval(get_field('percent_complete')) : 0 );

     }

 }

 function psp_get_project_quick_stats( $post_id = NULL ) {

     $post_id = ( $post_id == NULL ? get_the_ID() : $post_id );

     $summary   = psp_get_project_summary( $post_id );
     $colors    = psp_get_phase_color( $post_id);

     return apply_filters( 'psp_project_summary_stats', array(
         'documents' => array(
             'value' =>  $summary['documents']['approved'],
             'color' =>  $colors[0]['hex'],
             'total' =>  $summary['documents']['total'],
             'remaining' =>  $summary['documents']['total'] - $summary['documents']['approved'],
             'label' =>  __( 'Approved Documents', 'psp_projects' ),
         ),
         'milestones' => array(
             'value' =>  $summary['milestones']['complete'],
             'color' =>  $colors[1]['hex'],
             'total' =>  $summary['milestones']['total'],
             'remaining' =>  $summary['milestones']['total'] - $summary['milestones']['complete'],
             'label' =>  __( 'Complete Milestones', 'psp_projects' ),
         ),
         'phases' => array(
             'value' =>  $summary['phases']['complete'],
             'color' =>  $colors[2]['hex'],
             'total' =>  $summary['phases']['total'],
             'remaining' =>  $summary['phases']['total'] - $summary['phases']['complete'],
             'label' =>  __( 'Complete Phases', 'psp_projects' ),
         ),
         'tasks' => array(
             'value' =>  $summary['tasks']['complete'],
             'color' =>  $colors[3]['hex'],
             'total' =>  $summary['tasks']['total'],
             'remaining' =>  $summary['tasks']['total'] - $summary['tasks']['complete'],
             'label' =>  __( 'Complete Tasks', 'psp_projects' ),
         ),
     ), $post_id, $summary );

 }

 function psp_set_project_completion( $post_id = null, $progress = 0 ) {

     $post_id  = ( $post_id == null ? get_the_ID() : $post_id );

     $progress = apply_filters( 'psp_set_project_completion_progress', strval($progress) );

     if( $progress === '100' ) {

         update_post_meta( $post_id, '_psp_completed', '1' );
         update_post_meta( $post_id, '_psp_status', 'complete' );
         wp_set_post_terms( $post_id, 'completed', 'psp_status' );

         $project_title = get_the_title( $post_id );

         do_action( 'psp_notify', 'project_complete', array(
             'project_title'  => $project_title,
             'post_id'        => $post_id,
         ) );

     } else {

         update_post_meta( $post_id, '_psp_completed', '0' );
         update_post_meta( $post_id, '_psp_status', 'incomplete' );
         wp_set_post_terms( $post_id, 'incomplete', 'psp_status' );

     }

}

function psp_get_current_phase( $post_id = null ) {

    if( $post_id == null ) {
        $post_id = get_the_ID();
    }

    $phases = get_field( 'phases', $post_id );

    if( !$phases ) {
        return false;
    }

    $i = 0;
    foreach( $phases as $phase ) {

        $completion = psp_get_phase_completed( $i, $post_id );

        $i++;

        if( $completion['completed'] != '100' ) {
            return $phase;
        }

    }

    return false;

}

function psp_get_task_completion( $task = null, $phase_id = null, $post_id = null ) {

     if( $task == null ) {
          return apply_filters( 'psp_get_task_completion', 0, $task );
     }

     return apply_filters( 'psp_get_task_completion', intval( $task['status'] ), $task );

}
