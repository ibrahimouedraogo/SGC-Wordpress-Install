<?php
add_action( 'psp_frontend_assets', 'psp_kanban_assets' );
function psp_kanban_assets() {

     if( !is_singular('psp_projects') ) {
          return;
     }

     wp_register_style( 'psp-kanban', PSP_KB_URL . 'assets/css/psp-kanban.css', array(), PSP_KB_VER );
     wp_enqueue_style( 'psp-kanban' );

     wp_register_script( 'psp-kanban', PSP_KB_URL . 'assets/js/psp-kanban.js', array(), PSP_KB_VER, true );
     wp_enqueue_script( 'psp-kanban' );

     // jQuery UI - just in case FEE isn't installed

     $jqueryui_assets = array (
		'jquery-ui-core' => array(
			'script'	=>	true,
			'style'	=>	true,
		),
		'jquery-ui-sortable' => array(
			'script'	=>	true,
			'style'	=>	false
		)
	);

	foreach( $jqueryui_assets as $handle => $settings ) {
		if( $settings['script'] ) {
			wp_enqueue_script($handle);
		}
		if( $settings['style'] ) {
			wp_enqueue_style($handle);
		}
	}

}

add_action( 'psp_after_lane_tasks_wrapper', 'psp_kb_add_task_button', 10, 2 );
function psp_kb_add_task_button( $lane = null, $post_id = null ) {

     if( $lane == null || !psp_kb_fee_is_active() ) {
          return;
     }

     if( (!psp_can_edit_project($post_id) && !current_user_can('edit_psp_tasks') ) || is_post_type_archive('psp_projects') ) {
          return;
     }

     if( $post_id == null ) {
          $post_id = get_the_ID();
     }

     $data = apply_filters( 'psp_kb_add_task_button_data', array(
          'task_id'      => 'new',
          'lane'         => $lane['slug'],
          'submit_label' => __( 'Add Task', 'psp_projects' ),
          'modal_title'  => __( 'Add Task to ', 'psp_projects' ) . $lane['label'],
          'phase_id'     => ''
     ) ); ?>

     <a href="#psp-add-task" class="pano-btn psp-modal-btn psp-fe-add-element psp-kb-add-task"
          <?php foreach( $data as $slug => $value ): ?>
               data-<?php echo $slug; ?>="<?php echo esc_attr($value); ?>"
          <?php endforeach; ?>>
          <?php esc_html_e('Add Task','psp_projects'); ?>
     </a>

     <?php

}

add_action( 'psp_kb_after_task_name' , 'psp_fe_manage_task_links', 10, 6 );
/*
add_filter( 'psp_fe_edit_task_values', 'psp_kb_edit_task_values', 10, 6 );
function psp_kb_edit_task_values( $edit_data, $post_id, $phase_index, $task_index, $phases, $phase ) {

     $edit_data['phase_id']

} */

add_filter( 'psp_fe_add_task_fields', 'psp_kb_add_task_fields', 999, 2 );
function psp_kb_add_task_fields( $fields, $post_id = null ) {

     if( $post_id == null ) {
          wp_reset_postdata();
          $post_id = get_the_ID();
     }

     $new_fields    = array();
     $phases        = array();

     $project_phases = get_field( 'phases', $post_id );

     if( empty($project_phases) ) {
          return $fields;
     }

     foreach( $project_phases as $phase ) {
          $phases[ $phase['phase_id'] ] = $phase['title'];
     }

     foreach( $fields as $field ) {

          $new_fields[] = $field;

          if( $field['name'] == 'task' ) {

               $new_fields[] = array(
                    'name'     =>   'phase_id',
                    'callback' =>   'psp_field_type_select',
                    'label'    => __( 'Phase', 'psp_projects' ) . ' <span class="psp-req">*</span>',
                    'options'  =>   $phases,
                    'required' => true,
               );

               $new_fields[] = array(
                    'name'         =>   'lane',
                    'callback'     =>   'psp_field_type_hidden',
                    'value'        =>   ''
               );
          }

     }

     return $new_fields;

}

// add_action( 'psp_the_phases', 'psp_kb_hide_phases' );
function psp_kb_hide_phases() {

     if( psp_kb_show_lanes() ) { ?>
          <style type="text/css">
               #psp-phases {
                    height: 0;
                    overflow: hidden;
               }
          </style>
          <?php
     }

}

add_action( 'psp_before_phases_section_title', 'psp_kb_toggle_phase_icon' );
function psp_kb_toggle_phase_icon() { ?>

     <span class="psp-kb-phase-icon psp-svg-color-primary">
          <?php echo file_get_contents( PSP_KB_PATH . 'assets/img/phases.svg' ); ?>


     <?php
}

add_action( 'psp_after_phases_section_title', 'psp_kb_board_toggle' );
function psp_kb_board_toggle() { ?>

          <a href="<?php echo esc_url( add_query_arg( 'psp_view', 'kanban', get_the_permalink() ) ); ?>#psp-kanban-<?php echo get_the_ID(); ?>" class="psp-kb-switch" data-view="board">
               <?php
               echo file_get_contents( PSP_KB_PATH . 'assets/img/board.svg' ); ?>
               <?php esc_html_e( 'Board', 'psp_projects' ); ?>
          </a>
     </span>

     <?php
}

add_action( 'psp_after_task_name', 'psp_kb_lane_on_task', 10, 6 );
function psp_kb_lane_on_task( $post_id, $phase_index, $task_id, $phases, $phase, $task ) {

     if( psp_kb_show_lanes() ) {
          // return;
     }

     $lane = psp_kb_get_lane_from_task_id( $task_id, $post_id );

     $skip_lanes = apply_filters( 'psp_kb_lane_on_task_skips', array(
          'new',
          'completed'
     ) );

     if( empty($lane) || in_array( $lane['slug'], $skip_lanes ) ) {
          return;
     }

     echo '<span class="psp-kb-task-lane">' . $lane['label'] . '</span>';

}



/* add_filter( 'psp_fe_edit_task_values', 'psp_fe_add_lane_to_fe_edit_task_values', 10, 6 );
function psp_fe_add_lane_to_fe_edit_task_values( $edit_data, $post_id, $phase_index, $task_index, $phases, $phase ) {

     $task = $phases[$phase_index]['tasks'][$task_index];

} */
