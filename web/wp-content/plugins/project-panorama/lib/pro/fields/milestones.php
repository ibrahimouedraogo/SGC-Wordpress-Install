<?php
$date_format_opt 	= get_option( 'date_format' );
$date_format 		= ( substr( $date_format_opt, 0, 1 ) == 'd' ? 'dd/mm/yy' : 'mm/dd/yy' );
	// Load the fields for milestones

	if( get_option( 'psp_database_version' ) < 4 ) {

	register_field_group(array (
		'id' => 'acf_milestones',
		'title' => __('Milestones','psp_projects'),
		'fields' => array (
			array (
				'key' => 'field_53138cd18335f',
				'label' => __('Milestones','psp_projects'),
				'name' => '',
				'type' => 'message',
				'message' => __('If desired, describe what will be completed at key points in the project.','psp_projects'),
			),
			array (
				'key' => 'field_528170f6552e1',
				'label' => __('Display Milestones','psp_projects'),
				'name' => 'display_milestones',
				'type' => 'checkbox',
				'choices' => array (
					'Yes' => __('Yes','psp_projects'),
				),
				'default_value' => '',
				'layout' => 'vertical',
			),
			array (
				'key' => 'field_52817036552d8',
				'label' => __('Milestone Frequency','psp_projects'),
				'name' => 'milestone_frequency',
				'type' => 'select',
				'conditional_logic' => array (
					'status' => 1,
					'rules' => array (
						array (
							'field' => 'field_528170f6552e1',
							'operator' => '==',
							'value' => 'Yes',
						),
					),
					'allorany' => 'all',
				),
				'choices' => array (
					'null' => '---',
					'quarters' => '25% / 50% / 75%',
					'fifths' => '20% / 40% / 60% / 80%',
				),
				'default_value' => '',
				'allow_null' => 0,
				'multiple' => 0,
			),
			array (
				'key' => 'field_52a9e020fba90',
				'label' => __('1st Milestone','psp_projects'),
				'name' => '',
				'type' => 'tab',
				'conditional_logic' => array (
					'status' => 1,
					'rules' => array (
						array (
							'field' => 'field_528170f6552e1',
							'operator' => '==',
							'value' => 'Yes',
						),
					),
					'allorany' => 'all',
				),
			),
			array (
				'key' => 'field_53138d853bbf8',
				'label' => __('Note:','psp_projects'),
				'name' => '',
				'type' => 'message',
				'conditional_logic' => array (
					'status' => 1,
					'rules' => array (
						array (
							'field' => 'field_528170f6552e1',
							'operator' => '!=',
							'value' => 'Yes',
						),
					),
					'allorany' => 'all',
				),
				'message' => __('If you\'d like to label key milestones please turn "Display Milestones" on (above).','psp_projects'),
			),
			array (
				'key' => 'field_5281706e552d9',
				'label' => __('Title','psp_projects'),
				'name' => '25%_title',
				'type' => 'text',
				'conditional_logic' => array (
					'status' => 1,
					'rules' => array (
						array (
							'field' => 'field_528170f6552e1',
							'operator' => '==',
							'value' => 'Yes',
						),
					),
					'allorany' => 'all',
				),
				'default_value' => '',
				'placeholder' => '',
				'prepend' => '',
				'append' => '',
				'formatting' => 'none',
				'maxlength' => '',
			),
			array (
				'key' => 'field_528170a1552da',
				'label' => __('Description','psp_projects'),
				'name' => '25%_description',
				'type' => 'textarea',
				'conditional_logic' => array (
					'status' => 1,
					'rules' => array (
						array (
							'field' => 'field_528170f6552e1',
							'operator' => '==',
							'value' => 'Yes',
						),
					),
					'allorany' => 'all',
				),
				'default_value' => '',
				'placeholder' => '',
				'maxlength' => '',
				'formatting' => 'br',
			),
			array (
				'key' => 'field_52a9e067fba92',
				'label' => __('2nd Milestone','psp_projects'),
				'name' => '',
				'type' => 'tab',
				'conditional_logic' => array (
					'status' => 1,
					'rules' => array (
						array (
							'field' => 'field_528170f6552e1',
							'operator' => '==',
							'value' => 'Yes',
						),
					),
					'allorany' => 'all',
				),
			),
			array (
				'key' => 'field_53138db13bbf9',
				'label' => __('Note:','psp_projects'),
				'name' => '',
				'type' => 'message',
				'conditional_logic' => array (
					'status' => 1,
					'rules' => array (
						array (
							'field' => 'field_528170f6552e1',
							'operator' => '!=',
							'value' => 'Yes',
						),
					),
					'allorany' => 'all',
				),
				'message' => '',
			),
			array (
				'key' => 'field_528170ac552db',
				'label' => __('Title','psp_projects'),
				'name' => '50%_title',
				'type' => 'text',
				'conditional_logic' => array (
					'status' => 1,
					'rules' => array (
						array (
							'field' => 'field_528170f6552e1',
							'operator' => '==',
							'value' => 'Yes',
						),
					),
					'allorany' => 'all',
				),
				'default_value' => '',
				'placeholder' => '',
				'prepend' => '',
				'append' => '',
				'formatting' => 'none',
				'maxlength' => '',
			),
			array (
				'key' => 'field_528170b6552dc',
				'label' => __('Description','psp_projects'),
				'name' => '50%_description',
				'type' => 'textarea',
				'conditional_logic' => array (
					'status' => 1,
					'rules' => array (
						array (
							'field' => 'field_528170f6552e1',
							'operator' => '==',
							'value' => 'Yes',
						),
					),
					'allorany' => 'all',
				),
				'default_value' => '',
				'placeholder' => '',
				'maxlength' => '',
				'formatting' => 'br',
			),
			array (
				'key' => 'field_52a9e084fba93',
				'label' => __('3rd Milestone','psp_projects'),
				'name' => '',
				'type' => 'tab',
				'conditional_logic' => array (
					'status' => 1,
					'rules' => array (
						array (
							'field' => 'field_528170f6552e1',
							'operator' => '==',
							'value' => 'Yes',
						),
					),
					'allorany' => 'all',
				),
			),
			array (
				'key' => 'field_53138dbe3bbfa',
				'label' => __('Note: ','psp_projects'),
				'name' => '',
				'type' => 'message',
				'conditional_logic' => array (
					'status' => 1,
					'rules' => array (
						array (
							'field' => 'field_528170f6552e1',
							'operator' => '!=',
							'value' => 'Yes',
						),
					),
					'allorany' => 'all',
				),
				'message' => '',
			),
			array (
				'key' => 'field_528170c2552dd',
				'label' => __('Title','psp_projects'),
				'name' => '75%_title',
				'type' => 'text',
				'conditional_logic' => array (
					'status' => 1,
					'rules' => array (
						array (
							'field' => 'field_528170f6552e1',
							'operator' => '==',
							'value' => 'Yes',
						),
					),
					'allorany' => 'all',
				),
				'default_value' => '',
				'placeholder' => '',
				'prepend' => '',
				'append' => '',
				'formatting' => 'html',
				'maxlength' => '',
			),
			array (
				'key' => 'field_528170cc552de',
				'label' => __('Description','psp_projects'),
				'name' => '75%_description',
				'type' => 'textarea',
				'conditional_logic' => array (
					'status' => 1,
					'rules' => array (
						array (
							'field' => 'field_528170f6552e1',
							'operator' => '==',
							'value' => 'Yes',
						),
					),
					'allorany' => 'all',
				),
				'default_value' => '',
				'placeholder' => '',
				'maxlength' => '',
				'formatting' => 'br',
			),
			array (
				'key' => 'field_52a9e03afba91',
				'label' => __('4th Milestone','psp_projects'),
				'name' => '',
				'type' => 'tab',
				'conditional_logic' => array (
					'status' => 1,
					'rules' => array (
						array (
							'field' => 'field_528170f6552e1',
							'operator' => '==',
							'value' => 'Yes',
						),
						array (
							'field' => 'field_52817036552d8',
							'operator' => '==',
							'value' => 'fifths',
						),
					),
					'allorany' => 'all',
				),
			),
			array (
				'key' => 'field_53138dcc3bbfb',
				'label' => __('Note:','psp_projects'),
				'name' => '',
				'type' => 'message',
				'conditional_logic' => array (
					'status' => 1,
					'rules' => array (
						array (
							'field' => 'field_528170f6552e1',
							'operator' => '!=',
							'value' => 'Yes',
						),
					),
					'allorany' => 'all',
				),
				'message' => '',
			),
			array (
				'key' => 'field_53138d1583360',
				'label' => __('Note:','psp_projects'),
				'name' => '',
				'type' => 'message',
				'conditional_logic' => array (
					'status' => 1,
					'rules' => array (
						array (
							'field' => 'field_52817036552d8',
							'operator' => '==',
							'value' => 'quarters',
						),
					),
					'allorany' => 'all',
				),
				'message' => __('These fields are only available if you select "Fifths" on the milestone frequency field.','psp_projects'),
			),
			array (
				'key' => 'field_528170db552df',
				'label' => __('Title','psp_projects'),
				'name' => '100%_title',
				'type' => 'text',
				'conditional_logic' => array (
					'status' => 1,
					'rules' => array (
						array (
							'field' => 'field_528170f6552e1',
							'operator' => '==',
							'value' => 'Yes',
						),
						array (
							'field' => 'field_52817036552d8',
							'operator' => '==',
							'value' => 'fifths',
						),
					),
					'allorany' => 'all',
				),
				'default_value' => '',
				'placeholder' => '',
				'prepend' => '',
				'append' => '',
				'formatting' => 'none',
				'maxlength' => '',
			),
			array (
				'key' => 'field_528170e6552e0',
				'label' => __('Description','psp_projects'),
				'name' => '100%_description',
				'type' => 'textarea',
				'conditional_logic' => array (
					'status' => 1,
					'rules' => array (
						array (
							'field' => 'field_528170f6552e1',
							'operator' => '==',
							'value' => 'Yes',
						),
						array (
							'field' => 'field_52817036552d8',
							'operator' => '==',
							'value' => 'fifths',
						),
					),
					'allorany' => 'all',
				),
				'default_value' => '',
				'placeholder' => '',
				'maxlength' => '',
				'formatting' => 'br',
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
			'layout' => 'default',
			'hide_on_screen' => array (
			),
		),
		'menu_order' => 1,
	));


	}

	if( ( get_option( 'psp_database_version' ) >= 4 ) || ( isset( $_GET[ 'psp_upgrade_db' ] ) ) ) {

		$milestone_fields = array (
			'id' => 'acf_psp_milestones',
			'title' => 'Milestones',
			'fields' => array (
				array (
					'key' => 'field_563d1e50786e6',
					'label' => 'Milestones',
					'name' => 'milestones',
					'type' => 'repeater',
					'sub_fields' => array (
						array (
							'key' => 'field_563d1e62786e7',
							'label' => 'Occurs',
							'name' => 'occurs',
							'type' => 'select',
							'column_width' => 10,
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
							'key' => 'field_563d1eab786e8',
							'label' => 'Title',
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
							'key' => 'field_563d1eb2786e9',
							'label' => 'Description',
							'name' => 'description',
							'type' => 'textarea',
							'column_width' => '',
							'default_value' => '',
							'placeholder' => '',
							'maxlength' => 250,
							'rows' => 3,
							'formatting' => 'html',
						),
						array (
							'key' => 'psp_milestone_date',
							'label' => __('Date','psp_projects'),
							'name' => 'date',
							'type' => 'date_picker',
							'date_format' => 'yymmdd',
							'display_format' => $date_format,
							'first_day' => 0,
						),
					),
					'row_min' => '',
					'row_limit' => '',
					'layout' => 'row',
					'button_label' => 'Add Milestone',
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
				'layout' => 'default',
				'hide_on_screen' => array (
				),
			),
			'menu_order' => 2,
		);

		$milestone_fields = apply_filters( 'psp_milestone_fields' , $milestone_fields );

		register_field_group( $milestone_fields );

	}
