<?php

function psp_get_project_milestone_stats( $post_id = null ) {

     if( $post_id == null ) {
          $post_id = get_the_ID();
     }

     $milestones = get_field( 'milestones', $post_id );
     $project_progress = psp_compute_progress( $post_id );

     $stats = array(
          'total'        => 0,
          'complete'     => 0,
          'late'         => 0,
     );

     if( $milestones ) {
          foreach( $milestones as $milestone ) {

               $stats['total']++;

               if( intval($milestone['occurs']) <= $project_progress ) {
                    $stats['complete']++;
               } elseif( !empty($milestone['date']) && strtotime($milestone['date']) < strtotime('today') ) {
                    $stats['late']++;
               }

          }

     }

     return apply_filters( 'psp_get_project_milestone_stats', $stats, $post_id, $milestones );

}
