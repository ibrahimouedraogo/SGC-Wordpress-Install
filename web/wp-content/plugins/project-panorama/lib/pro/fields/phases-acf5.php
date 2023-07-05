<?php
$date_format_opt 	= get_option( 'date_format' );
$date_format 		= ( substr( $date_format_opt, 0, 1 ) == 'd' ? 'd/m/Y' : 'm/d/Y' );
$wysiwyg_format		= ( psp_get_option( 'psp_lazyload_wysiwyg' ) ? 'lite_wysiwyg' : 'wysiwyg' );

$phase_fields = array (
    'key'   => 'acf_phases',
    'id'    => 'acf_phases',
    'title' => __('Phases','psp_projects'),
    'fields' => array (
        array (
            'key' => 'field_527d5dc12fa29',
            'label' => __('Phases','psp_projects'),
            'name' => 'phases',
            'type' => 'repeater',
            'sub_fields' => array (
                array (
                    'key' => 'field_527d5dd02fa2a',
                    'label' => __('Title','psp_projects'),
                    'name' => 'title',
                    'type' => 'text',
                    'column_width' => '',
                    'default_value' => '',
                    'placeholder' => '',
                    'prepend' => '',
                    'append' => '',
                    'formatting' => 'html',
                    'maxlength' => '',
                ),
                array (
        			'key' => 'psp_private_phase',
        			'label' => __('Private Phase','psp_projects'),
        			'name' => 'private_phase',
        			'type' => 'checkbox',
        			'instructions' => __('Hide this phase from anyone who can\'t edit the project','psp_projects'),
        			'choices' => array (
        				'Yes' => __('Yes','psp_projects'),
        			),
        			'default_value' => '',
        			'layout' => 'vertical',
        			'wrapper'	=>	array(
        				'width'	=>	'50'
        			)
        		),
				array (
                    'key' => 'psp_phase_id',
                    'label' => __('Phase ID','psp_projects'),
                    'name' => 'phase_id',
                    'type' => 'hidden',
                    'column_width' => '',
                    'default_value' => '',
                    'placeholder' => '',
                    'prepend' => '',
                    'append' => '',
                    'formatting' => 'html',
                    'maxlength' => '',
                ),
                array (
                    'key' => 'field_53207efc069cb',
                    'label' => __('Weight','psp_projects'),
                    'name' => 'weight',
                    'type' => 'select',
                    'instructions' => __('Specify how much shorter this phase is compared to the rest, i.e. selecting 95% will reduce this phases contribution to the total completion by 5%','psp_projects'),
                    'required' => 1,
                    'conditional_logic' => array (
                        'status' => 1,
                        'rules' => array (
                            array (
                                'field' => 'field_5436e7f4e06b4',
                                'operator' => '==',
                                'value' => 'Yes',
                            ),
                            array (
                                'field' => 'field_5436e85ee06b5',
                                'operator' => '==',
                                'value' => 'Weighting',
                            ),
                        ),
                        'allorany' => 'all',
                    ),
                    'column_width' => '',
                    'choices' => array (
                        '1'   => '1',
                        '.95' => '.95',
                        '.90' => '.90',
                        '.85' => '.85',
                        '.80' => '.80',
                        '.75' => '.75',
                        '.70' => '.70',
                        '.65' => '.65',
                        '.60' => '.60',
                        '.55' => '.55',
                        '.50' => '.50',
                        '.45' => '.45',
                        '.40' => '.40',
                        '.35' => '.35',
                        '.30' => '.30',
                        '.25' => '.25',
                        '.20' => '.20',
                        '.15' => '.15',
                        '.10' => '.10',
                        '.05' => '.05',
                    ),
                    'default_value' => '',
                    'allow_null' => 0,
                    'multiple' => 0,
                ),
                array (
                    'key' => 'field_5436eab7a2238',
                    'label' => __('Hours','psp_projects'),
                    'name' => 'hours',
                    'type' => 'text',
                    'instructions' => __('Enter the total number of hours this phase will take','psp_projects'),
                    'required' => 0,
                    'conditional_logic' => array (
                        'status' => 1,
                        'rules' => array (
                            array (
                                'field' => 'field_5436e7f4e06b4',
                                'operator' => '==',
                                'value' => 'Yes',
                            ),
                            array (
                                'field' => 'field_5436e85ee06b5',
                                'operator' => '==',
                                'value' => 'Hours',
                            ),
                        ),
                        'allorany' => 'all',
                    ),
                    'column_width' => '',
                    'default_value' => '',
                    'placeholder' => '',
                    'prepend' => '',
                    'append' => '',
                    'formatting' => 'none',
                    'maxlength' => '',
                ),
                array (
                    'key' => 'field_phase_percentage',
                    'label' => __('Percentage','psp_projects'),
                    'name' => 'percentage',
                    'type' => 'text',
                    'instructions' => __('Enter the percentage of the project this phase represents.','psp_projects'),
                    'required' => 0,
                    'conditional_logic' => array (
                        'status' => 1,
                        'rules' => array (
                            array (
                                'field' => 'field_5436e7f4e06b4',
                                'operator' => '==',
                                'value' => 'Yes',
                            ),
                            array (
                                'field' => 'field_5436e85ee06b5',
                                'operator' => '==',
                                'value' => 'Percentage',
                            ),
                        ),
                        'allorany' => 'all',
                    ),
                    'column_width' => '',
                    'default_value' => '',
                    'placeholder' => '',
                    'prepend' => '',
                    'append' => '',
                    'formatting' => 'none',
                    'maxlength' => '',
                ),
                array (
                    'key' => 'field_527d5dd82fa2b',
                    'label' => __('Percent Complete','psp_projects'),
                    'name' => 'percent_complete',
                    'type' => 'select',
                    'conditional_logic' => array (
                        'status' => 1,
                        'rules' => array (
                            array (
                                'field' => 'field_5436e7f4e06b4',
                                'operator' => '!=',
                                'value' => 'Yes',
                            ),
                        ),
                        'allorany' => 'all',
                    ),
                    'column_width' => '',
                    'choices' => array (
                        0 => '0%',
                        5 => '5%',
                        10 => '10%',
                        15 => '15%',
                        20 => '20%',
                        25 => '25%',
                        30 => '30%',
                        35 => '35%',
                        40 => '40%',
                        45 => '45%',
                        50 => '50%',
                        55 => '55%',
                        60 => '60%',
                        65 => '65%',
                        70 => '70%',
                        75 => '75%',
                        80 => '80%',
                        85 => '85%',
                        90 => '90%',
                        95 => '95%',
                        100 => '100%',
                    ),
                    'default_value' => '',
                    'allow_null' => 0,
                    'multiple' => 0,
                ),
                array (
                    'key' => 'field_527d5dea2fa2c',
                    'label' => __('Description','psp_projects'),
                    'name' => 'description',
                    'type' => $wysiwyg_format,
                    'column_width' => '90',
                    'default_value' => '',
                    'toolbar' => 'full',
                    'media_upload' => 'no',
                ),
                array (
                    'key' => 'field_527d5dfd2fa2d',
                    'label' => __('Tasks','psp_projects'),
                    'name' => 'tasks',
                    'type' => 'repeater',
                    'column_width' => '',
                    'sub_fields' => array (
                        array (
                            'key' => 'field_527d5e072fa2e',
                            'label' => __('Task','psp_projects'),
                            'name' => 'task',
                            'type' => 'text',
                            'column_width' => '',
                            'default_value' => '',
                            'placeholder' => '',
                            'prepend' => '',
                            'append' => '',
                            'formatting' => 'html',
                            'maxlength' => '',
                        ),
						array (
                            'key' => 'psp_task_id',
                            'label' => __('Task ID','psp_projects'),
                            'name' => 'task_id',
                            'type' => 'hidden',
                            'column_width' => '',
                            'default_value' => '',
                            'placeholder' => '',
                            'prepend' => '',
                            'append' => '',
                            'formatting' => 'html',
                            'maxlength' => '',
                        ),
						array (
							'key' => 'field_532b8da69c46e',
							'label' => __('Assigned to','psp_projects'),
							'name' => 'assigned',
							'type' => 'select',
							'column_width' => '',
							'role' => array (
								0 => 'all',
							),
							'field_type' => 'select',
                                   'multiple'     => 1,
							'allow_null' => 1,
                                   'ui'           =>   1,
                                   'ajax'         =>   0,
                            'column_width' => '50',
						),
                        array (
                            'key' => 'psp_task_due_date',
                            'label' => __('Due Date','psp_projects'),
                            'name' => 'due_date',
                            'type' => 'date_picker',
                            'return_format' => 'Ymd',
                            'display_format' => $date_format,
                            'first_day' => 0,
                            'column_width' => '50',
                        ),
                        array (
                            'key' => 'field_527d5e0e2fa2f',
                            'label' => __('Completion','psp_projects'),
                            'name' => 'status',
                            'type' => 'select',
                            'column_width' => '100',
                            'choices' => array (
                                0 => '0%',
								5 => '5%',
                                10 => '10%',
                                15 => '15%',
                                20 => '20%',
                                25 => '25%',
                                30 => '30%',
								35 => '35%',
                                40 => '40%',
                                45 => '45%',
                                50 => '50%',
                                55 => '55%',
                                60 => '60%',
                                65 => '65%',
                                70 => '70%',
                                75 => '75%',
                                80 => '80%',
                                85 => '85%',
                                90 => '90%',
                                95 => '95%',
                                100 => '100%',
                            ),
                            'default_value' => '',
                            'allow_null' => 0,
                            'multiple' => 0,
                        ),
                        array(
                            'key'       => 'psp_task_description',
                            'label'     => __( 'Description','psp-subtasks'),
                            'name'      => 'task_description',
                            'type'      => 'wysiwyg',
                            'default_value' => '',
                            'toolbar'       => 'full',
                            'media_upload'  => 'yes',
                        ),
                    ),
                    'row_min' => '',
                    'row_limit' => '',
                    'layout' => 'block',
                    'button_label' => __('Add Task','psp_projects'),
                ),
            ),
            'row_min' => '',
            'row_limit' => '',
            'layout' => 'block',
            'button_label' => __('Add Phase','psp_projects'),
        ),
    ),
    'location' => array (
        array (
            array (
                'param' => 'post_type',
                'operator' => '==',
                'value' => 'psp_projects',
                'order_no' => 0,
                'group_no' => 0,
            ),
        ),
    ),
    'options' => array (
        'position' => 'normal',
        // 'layout' => 'no_box',
        'hide_on_screen' => array (
        ),
    ),
    'menu_order' => 3,
);

$phase_fields = apply_filters( 'psp_phase_fields' , $phase_fields );

register_field_group( $phase_fields );

add_filter('acf/load_field/name=phases', 'psp_phases_collapse');
function psp_phases_collapse($field) {
	$field['collapsed'] = 'field_527d5dd02fa2a';
	return $field;
}

add_filter('acf/load_field/name=tasks', 'psp_tasks_collapse');
function psp_tasks_collapse($field) {
	$field['collapsed'] = 'field_527d5e072fa2e';
	return $field;
}

add_filter( 'acf/validate_value/key=field_5436eab7a2238', 'psp_custom_hours_validation', 10, 4 );
function psp_custom_hours_validation( $valid, $value, $field, $input ) {

    $progress_type = ( isset($_POST['acf']['field_5436e85ee06b5']) ? $_POST['acf']['field_5436e85ee06b5'] : 'none' );

    if( $progress_type == 'Hours' && empty($value) ) {
        return __( 'Please enter the number of hours this phase will take', 'psp_projects' );
    }

    return $valid;

}

add_filter( 'acf/validate_value/key=field_phase_percentage', 'psp_custom_percentage_validation', 10, 4 );
function psp_custom_percentage_validation( $valid, $value, $field, $input ) {

    $progress_type = ( isset($_POST['acf']['field_5436e85ee06b5']) ? $_POST['acf']['field_5436e85ee06b5'] : 'none' );

    if( $progress_type == 'Percentage' && empty($value) ) {
        return __( 'Please enter the percentage of the project this phase represents', 'psp_projects' );
    }

    return $valid;

}
