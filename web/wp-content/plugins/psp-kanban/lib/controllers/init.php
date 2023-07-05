<?php
add_action( 'psp_between_progress_phases', 'psp_kb_display_lanes' );
function psp_kb_display_lanes( $post_id = null ) {

     if( !psp_kb_show_lanes() ) {
          return;
     }

     if( $post_id == null ) {
          $post_id = get_the_ID();
     }

     $lanes = psp_kb_get_lanes( $post_id );

     include( PSP_KB_PATH . '/lib/views/templates/lanes.php' );

}

function psp_kb_move_complete_tasks( $post_id = null , $lanes = null, $phases = null, $return = false ) {


     if( $post_id == null ) {
          $post_id = get_the_ID();
     }

     if( $lanes == null ) {
          $lanes = psp_kb_get_lanes( $post_id );
     }

     if( $phases == null ) {
          $phases = get_field( 'phases', $post_id );
     }

     if( empty($phases) || empty($lanes) ) {
          return $lanes;
     }


     foreach( $phases as $phase ) {

          if( empty($phase['tasks']) ) {
               continue;
          }

          foreach( $phase['tasks'] as $task ) {


               if( $task['status'] != '100' && $task['status'] != 100 ) {
                    continue;
               }

               if( !in_array( $task['task_id'], $lanes['new']['tasks'] ) ) {
                    continue;
               }

               $lanes = psp_kb_remove_task_from_lane( $post_id, $task['task_id'], 'new', $lanes );
               $lanes['completed']['tasks'][] = $task['task_id'];

          }

          if( $lanes ) {

               update_post_meta( $post_id, '_psp_kanban_lanes', $lanes );

               if( $return ) {
                    return $lanes;
               } else {
                    return false;
               }

          } else {
               return false;
          }

     }

}

function psp_kb_get_lanes( $post_id = null ) {

     if( $post_id == null ) {
          $post_id = get_the_ID();
     }

     $lanes = get_post_meta( $post_id, '_psp_kanban_lanes', true );

     if( !$lanes || isset( $_GET['psp_kb_reset_lanes'] ) ) {
          $lanes = psp_kb_init_project($post_id);
     }

     $phases = get_field( 'phases', $post_id );

     $lanes = psp_kb_add_new_tasks_to_lanes( $post_id, $lanes, $phases, true );
     $lanes = psp_kb_move_complete_tasks( $post_id, $lanes, $phases, true );

     return $lanes;

}

function psp_kb_add_new_tasks_to_lanes( $post_id = null, $lanes = null, $phases = null, $return = false ) {


     if( $post_id == null ) {
          $post_id = get_the_ID();
     }

     if( $lanes == null ) {
          $lanes = psp_kb_get_lanes( $post_id );
     }

     if( $phases == null ) {
          $phases = get_field( 'phases', $post_id );
     }

     if( empty($lanes) ) {
          return null;
     }

     if( empty($phases) ) {
          if( $return ) {
               return $lanes;
          } else {
               return false;
          }
     }

     $existing_tasks = array();

     foreach( $lanes as $slug => $lane ) {
          if( !empty($lane['tasks']) ) {
               $existing_tasks = array_merge( $existing_tasks, $lane['tasks'] );
          }
     }

     if( !empty($existing_tasks) ) {

          foreach( $phases as $phase ) {

               if( empty($phase['tasks']) ) {
                    continue;
               }

               foreach( $phase['tasks'] as $task ) {

                    if( !in_array( $task['task_id'], $existing_tasks ) ) {

                         $lane = ( $task['status'] == '100' || $task['status'] == 100 ? 'completed' : 'new' );

                         $lanes[ $lane ]['tasks'][] = $task['task_id'];

                    }

               }

          }

     }

     update_post_meta( $post_id, '_psp_kanban_lanes', $lanes );

     if( $return ) {
          return $lanes;
     } else {
          return true;
     }

}

function psp_kb_get_all_tasks( $post_id = null ) {

     if( $post_id == null ) {
          $post_id = get_the_ID();
     }

     $phases = get_field( 'phases', $post_id );

     if( empty($phases) ) {
          return false;
     }

     $tasks = array(
          'new'     =>   array(),
          'completed' => array(),
     );

     foreach( $phases as $phase ) {

          if( empty($phase['tasks']) ) {
               continue;
          }

          foreach( $phase['tasks'] as $task ) {

               if( $task['status'] == '100' ) {
                    $tasks['completed'][] = $task['task_id'];
               } else {
                    $tasks['new'][] = $task['task_id'];
               }

          }

     }

     return apply_filters( 'psp_kb_get_all_tasks', $tasks, $post_id );

}

add_action( 'wp_ajax_psp_kanban_ajax_move_lane', 'psp_kanban_ajax_move_lane' );
function psp_kanban_ajax_move_lane() {

     $required_fields = array(
          'post_id',
          'slug',
          'index'
     );


     foreach( $required_fields as $field ) {
          if( !isset($_POST[$field]) ) {
               wp_send_json_error( array( 'success' => false, 'message' => __( 'No post id, slug, or index recieved', 'psp_projects' ) ) );
          }
     }

     // Get all the post variables
     $post_id = $_POST['post_id'];
     $slug    = $_POST['slug'];
     $index   = $_POST['index'];

     if( psp_kanban_move_lane( $post_id, $slug, $index ) ) {
          wp_send_json_success( array( 'success' => true ) );
     } else {
          wp_send_json_error( array( 'success' => false, 'message' => __( 'Something went wrong', 'psp_projects' ) ) );
     }

}

function psp_kanban_move_lane( $post_id = null, $slug, $index, $lanes = null ) {

     if( $post_id == null ) {
          $post_id = get_the_ID();
     }

     if( $lanes == null ) {
          $lanes = get_post_meta( $post_id, '_psp_kanban_lanes', true );
     }

     if( !$lanes || empty($lanes) || empty($slug) || empty($lanes[$slug]) ) {
          return false;
     }

     $movable_lane = $lanes[$slug];

     unset( $lanes[$slug] );

     $new_lanes = array();

     $i = 0;
     foreach( $lanes as $lane_slug => $lane ) {

          if( $i == $index ) {
               $new_lanes[$slug] = $movable_lane;
          }

          $new_lanes[ $lane_slug ] = $lane;
          $i++;

     }

     // array_splice( $lanes, $index, 0, array( $slug => $movable_lane ) );

     update_post_meta( $post_id, '_psp_kanban_lanes', $new_lanes );

     return true;

}

add_action( 'wp_ajax_psp_kanban_move_task', 'psp_kanban_move_task' );
function psp_kanban_move_task() {

     $required_fields = array(
          'post_id',
          'origin',
          'destination',
          'task_id'
     );

     foreach( $required_fields as $field ) {
          if( !isset($_POST[$field]) ) {
               wp_send_json_error( array( 'success' => false, 'message' => __( 'No post id, task id, or lane recieved', 'psp_projects' ) ) );
          }
     }

     // Confirm user can edit this project
     if( !psp_can_edit_project($_POST['post_id']) ) {
          wp_send_json_error( array( 'sucess' => false, 'message' => __( 'You do not have permission to edit this task', 'psp_projects' ) ) );
          exit();
     }

     // Get all the post variables
     $post_id       = $_POST['post_id'];
     $origin        = $_POST['origin'];
     $destination   = $_POST['destination'];
     $task_id       = $_POST['task_id'];
     $index         = intval($_POST['index']);

     $lanes    = get_post_meta( $post_id, '_psp_kanban_lanes', true );
     $progress = false;

     if( !$lanes ) {
          wp_send_json_error( array( 'sucess' => false, 'message' => __( 'No lanes found', 'psp_projects' ) ) );
          exit();
     }

     // just changing the order
     if( $origin == $destination ) {

          $lanes = psp_kb_reorder_task_in_lane( $post_id, $task_id, $origin, $index, $lanes );

     // Changing order and lane
     } else {

          $lanes = psp_kb_remove_task_from_lane( $post_id, $task_id, $origin, $lanes );
          $lanes = psp_kb_add_task_to_lane( $post_id, $task_id, $destination, $index, $lanes );

          // Pass in progress so we can update on front end
          $progress = $lanes[$destination]['progress'];

          if( !$progress && $origin == 'completed' ) {

               $progress = 0;

               psp_kb_update_task_progress( $post_id, $task_id, $progress );

          }

     }

     update_post_meta( $post_id, '_psp_kanban_lanes', $lanes );

     wp_send_json_success( array( 'success' => true, 'task_id' => $task_id, 'progress' => $progress ) );

}

function psp_kb_remove_task_from_lane( $post_id = null, $task_id, $lane_slug, $lanes = null ) {

     if( $post_id == null ) {
          $post_id = get_the_ID();
     }

     if( $lanes == null ) {
          $lanes = get_post_meta( $post_id, '_psp_kanban_lanes', true );
     }

     if( empty($lanes) || empty( $lanes[$lane_slug] ) ) {
          return false;
     }

     $new_tasks = array();

     foreach( $lanes[$lane_slug]['tasks'] as $task ) {

          if( $task == $task_id ) {
               continue;
          }

          $new_tasks[] = $task;

     }

     $lanes[$lane_slug]['tasks'] = $new_tasks;

     return $lanes;


}

function psp_kb_add_task_to_lane( $post_id = null, $task_id, $lane_slug, $index = null, $lanes = null ) {

     if( $post_id == null ) {
          $post_id = get_the_ID();
     }

     if( $lanes == null ) {
          $lanes = get_post_meta( $post_id, '_psp_kanban_lanes', true );
     }

     if( empty($lanes) || empty($lanes[$lane_slug]) ) {
          return false;
     }

     $new_tasks = $lanes[$lane_slug]['tasks'];

     if( !$index ) {
          $index = count( $lanes[$lane_slug]['tasks'] );
     }

     array_splice( $new_tasks, $index, 0, $task_id );

     $lanes[$lane_slug]['tasks'] = $new_tasks;

     if( $lanes[$lane_slug]['progress'] ) {

          psp_kb_update_task_progress( $post_id, $task_id, intval($lanes[$lane_slug]['progress']) );

     }

     return $lanes;

}

function psp_kb_reorder_task_in_lane( $post_id = null, $task_id, $lane_slug, $index, $lanes = null ) {

     if( $post_id == null ) {
          $post_id = get_the_ID();
     }

     if( $lanes == null ) {
          $lanes = get_post_meta( $post_id, '_psp_kanban_lanes', true );
     }

     if( empty($lanes) || empty( $lanes[$lane_slug]) ) {
          return false;
     }

     $new_tasks = array();

     foreach( $lanes[$lane_slug]['tasks'] as $task ) {

          if( $task == $task_id ) {
               continue;
          }

          $new_tasks[] = $task;

     }

     array_splice( $new_tasks, $index, 0, $task_id );

     $lanes[$lane_slug]['tasks'] = $new_tasks;

     return $lanes;

}

add_action( 'wp_ajax_psp_kb_ajax_update_task_progress', 'psp_kb_ajax_update_task_progress' );
function psp_kb_ajax_update_task_progress() {

     $requires = array(
          'post_id',
          'task_id',
     );

     foreach( $requires as $required ) {
          if( empty($_POST[$required]) ) {
               wp_send_json_error( array( 'success' => false, 'message' => __( 'Missing required field', 'psp_projects' ) ) );
          }
     }

     $progress = intval( ( empty($_POST['progress']) ? 0 : $_POST['progress']) );
     $post_id  = $_POST['post_id'];
     $task_id  = $_POST['task_id'];

     $result = psp_kb_update_task_progress( $post_id, $task_id, $progress );

     if( $result ) {
          wp_send_json_success( array( 'success' => true ) );
     } else {
          wp_send_json_error( array( 'success' => false, 'message' => __( 'Something went wrong, please reload the page and try again', 'psp_projects' ) ) );
     }

}

function psp_kb_update_task_progress( $post_id = null, $task_id, $progress = false ) {

     if( $post_id == null ) {
          $post_id = get_the_ID();
     }

     settype( $progress, 'integer' );

     if( empty($progress) ) {
          $progress = intval(0);
     }

     $phases = get_field( 'phases', $post_id );

     if( !$phases ) {
          return false;
     }

     foreach( $phases as $phase ) {

          if( empty($phase['tasks']) ) {
               continue;
          }

          foreach( $phase['tasks'] as $task ) {

               if( $task_id == $task['task_id'] ) {
                    $phase_id = $phase['phase_id'];
               }

          }

     }

     if( !isset($phase_id) ) {
          return false;
     }

     psp_update_task_fe( $post_id, $phase_id, $task_id, $progress );

     return true;

}

function psp_kb_fee_is_active() {

     if( defined('PSP_FE_VER') ) {
          return true;
     }

     return false;

}

function psp_kanban_add_lane( $post_id = null, $label, $progress = false, $updatable = false, $lanes = null, $return_lanes = false ) {

     if( $post_id == null ) {
          $post_id = get_the_ID();
     }

     if( $lanes == null ) {
          $lanes = get_post_meta( $post_id, '_psp_kanban_lanes', true );
     }

     $slug = psp_kanban_unique_title( $label, $lanes );

     $lanes[$slug] = array(
          'slug'         =>  $slug,
          'label'        =>  $label,
          'progress'     =>  $progress,
          'updatable'    =>  $updatable,
          'tasks'        =>  array()
     );

     update_post_meta( $post_id, '_psp_kanban_lanes', $lanes );

     if( $return_lanes ) {
          return $lanes;
     }

     return true;

}

function psp_kanban_update_lane( $post_id = null, $slug, $args, $lanes = null ) {

     if( $post_id == null ) {
          $post_id = get_the_ID();
     }

     if( $lanes == null ) {
          $lanes = psp_kb_get_lanes( $post_id );
     }

     if( empty($lanes) || empty($lanes[$slug]) ) {
          return false;
     }

     $updatable = array(
          'progress',
          'label',
          'updatable'
     );

     foreach( $updatable as $update ) {
          if( isset($args[$update]) ) {
               $lanes[$slug][$update] = $args[$update];
          }
     }

     update_post_meta( $post_id, '_psp_kanban_lanes', $lanes );

     return true;

}

add_action( 'wp_ajax_psp_kanban_ajax_update_lane', 'psp_kanban_ajax_update_lane' );
function psp_kanban_ajax_update_lane() {

     $required = array(
          'slug',
          'label',
          'updatable',
          'progress'
     );

     foreach( $required as $required ) {
          if( empty($_POST[ $required ] ) ) {
               wp_send_json_error( array( 'success' => false, 'message' => __( 'Missing field ', 'psp_projects' ) . ' ' . $required ) );
          }
     }

     $post_id  = ( empty($_POST['post_id']) ? get_the_ID() : $_POST['post_id'] );
     $slug     = $_POST['slug'];
     $lanes    = get_post_meta( $post_id, '_psp_kanban_lanes', true );

     if( empty($lanes) || empty($lanes[$slug]) ) {
          wp_send_json_error( array( 'success' => false, 'message' => __( 'Lane doesn\'t exist', 'psp_projects' ),  'lanes' => $lanes ) );
     }

     $args = array(
          'label' => $_POST['label'],
          'updatable' => ( $_POST['updatable'] == 'yes' ? true : false ),
          'progress' => ( $_POST['progress'] == 'no' ? false : $_POST['progress'] )
     );

     $result = psp_kanban_update_lane( $post_id, $slug, $args );

     if( $result ) {
          wp_send_json_success( array( 'success' => true, 'slug' => $slug, 'updatable' => $args['updatable'], 'label' => $args['label'], 'progress' => $args['progress'] ) );
     } else {
          wp_send_json_error( array( 'success' => false, 'message' => __( 'Couldn\'t find lanes or lane', 'psp_projects' ) ) );
     }

}

function psp_kanban_delete_lane( $post_id = null, $slug, $return_tasks = false ) {

     if( $post_id == null ) {
          $post_id = get_the_ID();
     }

     $lanes = psp_kb_get_lanes( $post_id );

     if( empty( $lanes[$slug] ) ) {
          return false;
     }

     $tasks = $lanes[$slug]['tasks'];

     if( !empty($tasks) ) {
          $lanes['new']['tasks'] = array_merge( $lanes['new']['tasks'], $tasks );
     }

     unset( $lanes[$slug] );

     update_post_meta( $post_id, '_psp_kanban_lanes', $lanes );

     if( $return_tasks && !empty($tasks) ) {
          return $tasks;
     }

     return true;

}

add_action( 'wp_ajax_psp_kanban_ajax_delete_lane', 'psp_kanban_ajax_delete_lane' );
function psp_kanban_ajax_delete_lane() {

     $post_id   = ( empty($_POST['post_id']) ? get_the_ID() : $_POST['post_id'] );
     $slug      = $_POST['slug'];

     if( empty($slug) ) {
          wp_send_json_error( array( 'success' => false, 'message' => __( 'No list seleted', 'psp_projects' ) ) );
     }

     $lanes = psp_kb_get_lanes( $post_id );

     if( empty($lanes) || empty($lanes[$slug]) ) {
          wp_send_json_error( array( 'success' => false, 'message' => __( 'That list no longer exists', 'psp_projects' ) ) );
     }

     $result = psp_kanban_delete_lane( $post_id, $slug, true );

     if( $result ) {

          if( !empty( $result ) ) {

          }

          wp_send_json_success( array( 'success' => true, 'reloadTasks' => true ) );

     } else {
          wp_send_json_error( array( 'success' => false, 'message' => __( 'Something went wrong, please try again', 'psp_projects' ) ) );
     }

}

function psp_kanban_unique_title( $label, $lanes, $i = 0 ) {

     $slug = sanitize_title( $label );
     $existing_slugs = array_keys($lanes);

     if( in_array( $slug, $existing_slugs ) ) {

          // Reset the counter
          if( strpos( '-', $slug ) ) {
               $slug = substr( $slug, 0, -2 );
          }

          $i++;

          $slug = $slug . '-' . $i;

          psp_kanban_unique_title( $slug, $lanes, $i );

     }

     return $slug;

}

add_action( 'wp_ajax_psp_kanban_ajax_add_lane', 'psp_kanban_ajax_add_lane' );
function psp_kanban_ajax_add_lane() {

     $post_id   = $_POST['post_id'];
     $label     = $_POST['label'];
     $progress  = ( $_POST['progress'] == 'no' ? false : $_POST['progress'] );
     $updatable = $_POST['updatable'];

     if( $post_id == null ) {
          $post_id = get_the_ID();
     }

     $lanes = psp_kanban_add_lane( $post_id, $label, $progress, $updatable, null, true );

     $lane = end($lanes);

     ob_start();

     include( PSP_KB_PATH . '/lib/views/templates/single-lane.php');

     $markup = ob_get_clean();

     wp_send_json_success( array( 'success' => true, 'markup' => $markup ) );

}

add_action( 'wp_ajax_psp_kanban_ajax_get_new_tasks', 'psp_kanban_ajax_get_new_tasks' );
function psp_kanban_ajax_get_new_tasks() {

     $post_id = ( empty($_POST['post_id']) ? get_the_ID() : $_POST['post_id'] );

     $lanes = psp_kb_get_lanes( $post_id );

     if( empty($lanes) ) {
          wp_send_json_error( array( 'message' => __( 'No lists found', 'psp_projects' ) ) );
     }

     $lane = $lanes['new'];

     ob_start();

     include( PSP_KB_PATH . '/lib/views/templates/single-lane.php');

     $markup = ob_get_clean();

     wp_send_json_success( array( 'success' => true, 'markup' => $markup ) );

}

add_action( 'psp_fe_after_task_update', 'psp_kb_add_task_to_lane_fe', 10, 4 );
function psp_kb_add_task_to_lane_fe( $task, $phases, $post_id, $post_vars ) {

     if( empty($post_vars['lane']) ) {
          return;
     }

     $lane_slug = $post_vars['lane'];
     $lanes = psp_kb_get_lanes( $post_id );

     if( empty($lanes[$lane_slug]) ) {
          return false;
     }

     if( in_array( $task['task_id'], $lanes[ $lane_slug ]['tasks'] ) ) {
          return;
     }

     $lanes = psp_kb_add_task_to_lane( $post_id, $task['task_id'], $lane_slug, null, $lanes );

     if( $lanes ) {
          update_post_meta( $post_id, '_psp_kanban_lanes', $lanes );
     }

}

add_action( 'psp_fe_after_init', 'psp_kb_fe_hooks' );
function psp_kb_fe_hooks() {
     add_filter( 'psp_fe_update_task_return', 'psp_kb_update_task_return', 10, 8 );
}

function psp_kb_update_task_return( $return, $post_id, $lane_phases, $phase_index, $task_id, $target, $task, $post_vars ) {

     if( empty($post_vars['lane']) ) {
          return $return;
     }

     $lane = $post_vars['lane'];

     if( empty($post_vars['task_id']) || $post_vars['task_id'] == 'new' ) {
          $method = 'append';
          $target = '.psp-lane-' . $lane . ' .psp-lane__container';
     } else {
          $method = 'replace';
          $target = '.psp-card[data-task_id="' . $task['task_id'] . '"]';
     }

     $task_id = $task['task_id']; // Need to reset if it's a new task

     ob_start();

     include( PSP_KB_PATH . '/lib/views/templates/task-single.php');

     $markup = ob_get_clean();

     $return['modify'][] = array(
          'method'  =>   $method,
          'target'  =>   $target,
          'markup'  =>   $markup,
          'kanban'  =>  'true',
          'debug'   =>   array(
               'task_id' =>   $task_id,
               'post_id' =>   $post_id,
               'lane_phases' => $lane_phases,
          )
     );

     return $return;

}

function psp_kb_show_lanes() {

     return true;

     /*

     if( !empty($_GET['psp_view']) && $_GET['psp_view'] !== 'kanban' ) {
          return false;
     }

     if( ( !empty($_GET['psp_view']) && $_GET['psp_view'] == 'kanban' ) || !(empty($_COOKIE['psp_view'])) && $_COOKIE['psp_view'] == 'kanban' ) {
          return true;
     }

     return false; */

}


function psp_kb_get_lane_from_task_id( $task_id, $post_id = null ) {

     if( $post_id == null ) {
          $post_id = get_the_ID();
     }

     $lanes = psp_kb_get_lanes( $post_id );

     if( empty($lanes) ) {
          return false;
     }

     $task_lane = false;

     foreach( $lanes as $lane ) {

          foreach( $lane['tasks'] as $task ) {

               if( $task != $task_id ) {
                    continue;
               }

               // If we're here we have a match!
               $task_lane = $lane;

          }

     }

     return $task_lane;

}
