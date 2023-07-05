<?php
// Increment counters
$c++; $i++;

// Set the colors
$c     = ( $c == count($colors) ? 0 : $c );
$color = $colors[$c];

/**
 * Important Phase Variables
 *
 * @var $phase_data [array] 'completed', 'completed-tasks', 'tasks'
 * @var $remaining [int]
 * @var $phase_comment_key [string]
 * @var $comment_count [int]
 */

$phase_data 		= psp_get_phase_completed( $phase_index, $post_id );
$remaining 		= 100 - $phase_data['completed'];

$phase_comment_key 	= psp_validate_or_generate_comment_key( get_sub_field('phase_id'), $phase_index, $i - 1 );
$phase_id           = $phase_comment_key; // This comes up later

$comment_count		= ( ! empty( $phase_comment_key ) ? psp_get_phase_comment_count( $phase_comment_key ) : '0' );
$phase_docs    	= psp_parse_phase_documents( get_field('documents'), get_sub_field('phase_id'), get_sub_field('tasks') );
$phase_docs_count	= ( !empty($phase_docs['all']) ? count($phase_docs['all']) : 0 );
$approved = 0;
if ( isset( $phase_docs['all'] ) && $phase_docs['all'] ) {
    $approved       	= psp_count_approved_documents( $phase_docs['all'] );
}

do_action( 'psp_before_individual_phase_wrapper', $post_id, $phase_index, $phases, $phase ); ?>

<div class="psp-phase color-<?php echo esc_attr($color['name']); ?> phase-<?php echo esc_attr($phase_index + 1); ?> <?php echo esc_attr( psp_get_phase_classes( $post_id, $phase_index ) ); ?>" data-phase-index="<?php esc_attr_e($phase_index); ?>" data-phase_id="<?php esc_attr_e( $phase_comment_key ); ?>" data-completed="<?php esc_attr_e( $phase_data['completed'] ); ?>" data-tasks="<?php esc_attr_e($phase_data['tasks']); ?>" id="phase-<?php esc_attr_e( $phase_index + 1 ); ?>">
     <div class="psp-phase-content">
          <?php do_action( 'psp_after_individual_phase_wrapper', $post_id, $phase_index, $phases, $phase ); ?>

          <div class="psp-h3 psp-phase-title-wrap">

             <?php do_action( 'psp_before_phase_title',  $post_id, $phase_index, $phases, $phase ); ?>

             <span class="psp-phase-title">
                 <?php
                 the_sub_field('title');
                 if( get_sub_field('private_phase') ):
                     esc_html_e( ' (Private)', 'psp_projects' );
                 endif; ?>
             </span>

             <span class="psp-top-complete psp-row cf">
                 <?php
                 $psp_phase_stats = apply_filters( 'psp_phase_stats', array(
                     array(
                         'wrapper_class'	=>	'psp-phase-stat',
                         'stat_class'	     =>	'psp-phase-document-count',
                         'stat'			=>	$phase_docs_count . ' <i class="fa fa-files-o"></i>',
                         'title'			=>	__( 'Approved Documents', 'psp_projects'),
                     ),
                     array(
                         'wrapper_class'	=>	'psp-phase-stat',
                         'stat_class'	=>	'count task-count',
                         'stat'			=>	'<span class="completed">' . $phase_data['completed_tasks'] . '</span> / <span class="total total-task-count">' . $phase_data['tasks'] . '</span> <i class="fa fa-tasks"></i>',
                         'title'			=>	__( 'Completed Tasks', 'psp_projects' ),
                     ),
                     array(
                         'condition'		=>	comments_open(),
                         'wrapper_class'	=>	'psp-phase-stat',
                         'stat_class'	     =>	'comment-count',
                         'stat'			=>	$comment_count . ' <i class="fa fa-comment"></i>',
                         'title'			=>	__( 'Messages', 'psp_projects' ),
                     )
                 ) );

                 foreach( $psp_phase_stats as $stat ):

                     if( isset($stat['condition']) && isset($stat['condition']) == false ) continue; ?>
                     <span class="<?php echo esc_attr( $stat['wrapper_class'] ); ?>" data-toggle="psp-tooltip" data-placement="top" title="<?php echo esc_attr($stat['title']); ?>">
                         <span class="<?php echo esc_attr( $stat['stat_class'] ); ?>">
                             <?php echo wp_kses_post($stat['stat']); ?>
                         </span>
                     </span>
                 <?php endforeach; ?>
             </span>
         </div> <!--/h3.psp-phase-title-wrap-->

         <?php do_action( 'psp_after_phase_title', $post_id, $phase_index ); ?>

         <div class="psp-phase-overview psp-phase-progress-<?php echo esc_attr($phase_data['completed']); ?>">

             <div class="psp-chart">
                 <span class="psp-chart-complete"><?php echo esc_html($phase_data['completed']); ?>%</span>
                 <canvas class="phase-chart" data-chart-id="<?php echo esc_attr($i); ?>" id="chart-<?php echo esc_attr($phase_index); ?>" width="100%"></canvas>

                 <script>
                     jQuery(document).ready(function() {

                         var data = [
                             {
                                 value: <?php echo $phase_data['completed']; ?>,
                                 color: "<?php echo $color[ 'hex' ]; ?>",
                                 label: "<?php esc_html_e( 'Completed', 'psp_projects' ); ?>"
                             },
                             {
                                 value: <?php echo $remaining; ?>,
                                 color: "#efefef",
                                 label: "<?php esc_html_e( 'Remaining', 'psp_projects' ); ?>"
                             }
                         ];

                         var chart_<?php echo $phase_index; ?> = document.getElementById("chart-<?php echo $phase_index; ?>").getContext("2d");
                         allCharts[<?php echo $phase_index; ?>] = new Chart(chart_<?php echo $phase_index; ?>).Doughnut(data,chartOptions);

                     });
                 </script>

             </div> <!--/.psp-chart-->

             <div class="psp-phase-info">
                 <?php
                 do_action( 'psp_before_phase_description', $post_id, $phase_index, $phases, $phase );
                 if( get_sub_field('description') ): ?>
                     <div class="psp-h5"><?php esc_html_e( 'Description', 'psp_projects' ); ?></div>
                     <?php echo do_shortcode( get_sub_field( 'description' ) ); ?>
                 <?php
                 endif;
                 do_action( 'psp_after_phase_description', $post_id, $phase_index, $phases, $phase ); ?>
             </div>

         </div> <!-- tasks is '.$task_style.'-->

         <?php
         do_action( 'psp_before_phase_lists', $post_id, $phase_index, $phases, $phase );

         include( psp_template_hierarchy( 'projects/phases/tasks/index.php' ) );

         include( psp_template_hierarchy( 'projects/phases/documents/index.php' ) );

         if( comments_open( $post_id ) ) include( psp_template_hierarchy( 'projects/phases/discussions/index.php' ) );

         do_action( 'psp_after_phase_lists', $post_id, $phase_index, $phases, $phase ); ?>

    </div> <!--/.psp-phase-wrap-->

</div> <!--/.psp-task-list-->
<?php $phase_index++;
