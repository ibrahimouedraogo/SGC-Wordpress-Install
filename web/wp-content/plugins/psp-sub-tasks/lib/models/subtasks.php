<?php
add_filter( 'psp_phase_fields', 'psp_st_sub_task_fields' );
function psp_st_sub_task_fields( $fields ) {

    $new_field = array(
        'key'       => 'psp_st_sub_task',
        'label'     => __('Sub Task','psp-subtasks'),
        'name'      => 'sub_task',
        // 'instructions'	=>	__( 'Task will become a sub task of the first non-subtask above it.', 'psp-subtasks' ),
        'type' => 'checkbox',
        'choices' => array (
            'Yes' => 'Yes',
        ),
        'default_value' => '',
        'layout' => 'horizontal',
    );

    $new_fields     = array();
    $new_sub_fields = array();

    foreach( $fields['fields'] as $parent_field ) {

        if( $parent_field['name'] == 'phases' ) {

            $new_parent_field = array();

            foreach( $parent_field['sub_fields'] as $sub_field ) {

                if( $sub_field['name'] == 'tasks' ) {

                    $sub_tasks = $sub_field;

                    $sub_tasks['key']   =  'psp_st_sub_tasks';
                    $sub_tasks['label'] =  __( 'Sub Tasks', 'psp-subtask' );
                    $sub_tasks['name']  =  'sub_task';
                    $sub_tasks['button_label'] = __( 'Add Sub Task', 'psp-subtasks' );

					foreach ( $sub_tasks['sub_fields'] as $field_index => $sub_task_field ) {

						// Sub Tasks are a part of the Parent Task, so they don't need their own Unique ID
						if ( $sub_task_field['name'] == 'task_id' ) {
							unset( $sub_tasks['sub_fields'][ $field_index ] );
						}

					}

                    $sub_field['sub_fields'][] = $sub_tasks;

                }

                $new_parent_field[] = $sub_field;

            }

            $parent_field['sub_fields'] = $new_parent_field;

        }

        $new_fields[] = $parent_field;

    }

    $fields['fields'] = $new_fields;

    return $fields;

}

/**
 * Add Sub Tasks Tab to Task Panel
 *
 * @param		array $task_panel_tabs Task Panel Tabs
 *
 * @since		{{VERSION}}
 * @return		array Task Panel Tabs
 */
add_filter( 'psp_task_panel_tabs', 'psp_st_add_to_task_panel' );
function psp_st_add_to_task_panel( $task_panel_tabs ) {

	$task_panel_tabs['psp_st_get_task_panel_subtasks'] = array(
		'tab_id' => 'subtasks',
		'tab_title' => __( 'Sub Tasks', 'psp-subtask' ),
		'tab_icon' => 'psp-fi-icon psp-fi-discussion',
		'count' => true,
		'default_content' => '',
	);

	return $task_panel_tabs;

}

/**
 * Ajax Callback for adding Sub Tasks to the Task Panel
 *
 * @since		{{VERSION}}
 * @return		void
 */
add_action( 'wp_ajax_psp_st_get_task_panel_subtasks', 'psp_st_get_task_panel_subtasks' );
add_action( 'wp_ajax_nopriv_psp_st_get_task_panel_subtasks', 'psp_st_get_task_panel_subtasks' );
function psp_st_get_task_panel_subtasks() {

	$phase_index   = $_POST['phase_index'];
	$task_index    = $_POST['task_index'];
	$post_id       = $_POST['project'];
	$phases        = get_field( 'phases', $post_id );
     
     $task_id       = $phases[$phase_index]['tasks'][$task_index]['task_id'];
     $sub_tasks     = $phases[$phase_index]['tasks'][$task_index]['sub_task'];

	$task_docs = psp_parse_task_documents( get_field( 'documents', $post_id ), $task_id );

	ob_start();
	psp_sub_task_list( $post_id, $phase_index, $task_index, $phases, $phases[$phase_index], true );
	$content = ob_get_clean();

    wp_send_json_success( array(
		'success' => true,
		'content' => $content,
		'count' => ( !empty($sub_tasks) ? count($sub_tasks) : 0 ),
	) );

}
