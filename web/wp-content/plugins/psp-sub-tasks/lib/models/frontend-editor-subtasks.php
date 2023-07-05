<?php
/**
 * Frontend Editor functionality
 *
 * @since		{{VERSION}}
 *
 * @package psp-sub-tasks
 * @subpackage psp-sub-tasks/lib/models
 */

defined( 'ABSPATH' ) || die();

final class PspST_Frontend_Editor {

	/**
	 * PspST_Frontend_Editor constructor.
	 *
	 * @since		{{VERSION}}
	 */
	function __construct() {

		add_action( 'psp_head', array( $this, 'frontend_editor_scripts' ) );

		// We want to do this before other integrations to prevent broken, accidental integrations
		// A filter is available for intentionally including sub-fields
		add_filter( 'psp_fe_add_task_fields', array( $this, 'add_task_fields' ), 1 );

		add_filter( 'psp_st_sub_task_fields', array( $this, 'add_hidden_status_field' ) );

		add_filter( 'psp_fe_edit_task_values', array( $this, 'prepopulate_task_fields' ), 10, 6 );

		add_filter( 'psp_fe_add_task_data', array( $this, 'save_task_fields' ), 10, 6 );
		add_filter( 'psp_fe_update_task_data', array( $this, 'save_task_fields' ), 10, 6 );

		add_action( 'psp_get_all_my_tasks_loop', array( $this, 'add_assigned_subtasks' ), 10, 3 );

		add_filter( 'psp_show_task_on_dashboard', array( $this, 'show_parent_task' ), 10, 3 );
		add_filter( 'psp_show_task_on_user_tasks', array( $this, 'show_parent_task' ), 10, 3 );

		add_action( 'psp_get_item_count_task_loop', array( $this, 'show_subtasks_in_item_count' ), 10, 8 );

	}

	/**
	 * Add necessary Styles and Scripts to the Frontend Editor
	 *
	 * @access		public
	 * @since		{{VERSION}}
	 * @return		void
	 */
	public function frontend_editor_scripts() {

		global $wp_query;


		// We have to fake a ton of stuff for this
		// Project Panorama fakes this kind of thing too
		if ( get_query_var( 'psp_manage_page' ) ) {

			psp_enqueue_script(
				'psp-st-admin',
				PSP_ST_URL . '/assets/js/admin.js',
				array( 'jquery' ),
				defined( 'WP_DEBUG' ) && WP_DEBUG ? time() : PSP_ST_VER
			);

		}
		else {

			psp_enqueue_script(
				'rbm-fh-admin',
				RBM_FIELD_HELPERS_URI . '/assets/dist/js/rbm-field-helpers-admin.min.js',
				array( 'jquery', 'rbm-fh-jquery-repeater' ),
				RBM_FIELD_HELPERS_VER
			);

			psp_enqueue_style(
				'rbm-fh-admin',
				RBM_FIELD_HELPERS_URI . '/assets/dist/css/rbm-field-helpers-admin.min.css',
				array(),
				RBM_FIELD_HELPERS_VER
			);

			psp_enqueue_script(
				'rbm-fh-jquery-repeater',
				RBM_FIELD_HELPERS_URI . "/vendor/jquery-repeater/jquery.repeater.min.js",
				array( 'jquery' ),
				'1.2.1'
			);

			psp_enqueue_script(
				'jquery-ui-core',
				"/wp-includes/js/jquery/ui/core.min.js",
				array( 'jquery' ),
				'1.11.4'
			);

			/*

			psp_enqueue_script(
				'jquery-ui-widget',
				"/wp-includes/js/jquery/ui/widget.min.js",
				array( 'jquery' ),
				'1.11.4'
			); */

			psp_enqueue_script(
				'jquery-ui-mouse',
				"/wp-includes/js/jquery/ui/mouse.min.js",
				array( 'jquery-ui-core', 'jquery-ui-widget' ),
				'1.11.4'
			);

			psp_enqueue_script(
				'jquery-ui-sortable',
				"/wp-includes/js/jquery/ui/sortable.min.js",
				array( 'jquery-ui-mouse' ),
				'1.11.4'
			);

			psp_enqueue_script(
				'jquery-ui-datepicker',
				"/wp-includes/js/jquery/ui/datepicker.min.js",
				array( 'jquery-ui-core' ),
				'1.11.4'
			);

			psp_enqueue_script(
				'rbm-fh-select2',
				"https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.4/js/select2.full.min.js",
				array( 'jquery' ),
				RBM_FIELD_HELPERS_VER,
				true
			);

			global $wp_scripts;

			// get registered script object for jquery-ui
			$ui = $wp_scripts->query( 'jquery-ui-core' );

			// tell WordPress to load the Smoothness theme from Google CDN
			$url = "//ajax.googleapis.com/ajax/libs/jqueryui/{$ui->ver}/themes/smoothness/jquery-ui.min.css";
			psp_enqueue_style(
				'jquery-ui-smoothness',
				$url,
				false,
				$ui->ver
			);

			psp_enqueue_style(
				'dashicons',
				"/wp-includes/css/dashicons.min.css",
				array()
			);

			psp_enqueue_style(
				'rbm-fh-jquery-ui-datetimepicker',
				RBM_FIELD_HELPERS_URI . '/vendor/jQuery-Timepicker-Addon/jquery-ui-timepicker-addon.css',
				array(),
				RBM_FIELD_HELPERS_VER
			);

			psp_enqueue_style(
				'rbm-fh-select2',
				'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.4/css/select2.min.css',
				array(),
				RBM_FIELD_HELPERS_VER
			);

			// Force the Repeater to work, since the way we are "enqueing" it doesn't populate this correctly
			add_filter( 'rbm_field_helpers_admin_data', function( $data ) {

				$data['psp_st']['repeater']['sub_tasks'] = array(
					'empty' => true,
				) + $this->get_repeater_localization();

				// Sub Field Localization
				$data['psp_st']['repeaterFields']['sub_tasks']['due_date'] = array(
				) + $this->get_datepicker_localization();

				$data['psp_st']['repeaterFields']['sub_tasks']['assigned'] = array(
				) + $this->get_select_localization();

				// Allow other integrations to localize data for RBM Field Helpers, if necessary
				$data = apply_filters( 'psp_st_rbm_field_helpers_admin_data', $data, $this );

				return $data;

			} );

			// Grab Localized Data including the above Filter
			$l10n = psp_st_field_helpers()->get_localized_data();

			psp_st_localize_js( 'RBM_FieldHelpers', $l10n );

		}

	}

	/**
	 * Add Gantt Chart Fields to Frontend Add/Edit Task Dialog
	 *
	 * @param		array $fields Field Items
	 *
	 * @access		public
	 * @since		{{VERSION}}
	 * @return		array Field Items
	 */
	public function add_task_fields( $fields ) {

		$post_id = get_the_ID();

		foreach ( $fields as $index => $field ) {

			if ( $field['name'] == 'due_date' ) {
				// Used for inserting our Fields appropriately
				$due_date_index = $index;
				break;
			}

		}

		$sub_fields = array();

		// Convert PSP Frontend Fields from the task into subfields for the RBM FH Repeater
		foreach ( $fields as $field ) {

			// The Task ID is a hidden field which Frontend Editor handles separately, so we don't need to manually exclude it

			preg_match( '/[^_]+?$/', $field['callback'], $matches );

			$field_type = $matches[0];

			if ( $field_type == 'select' ) {

				// Don't include empty selects as visible items
				if ( empty( $field['options'] ) ) {
					$field_type = 'hidden';
				}

			}

			if ( $field['name'] == 'due_date' ) {

				$field_type = 'datepicker';

			}

			$sub_fields[ $field['name'] ]['type'] = $field_type;

			$sub_fields[ $field['name'] ]['args'] = $field;

			if ( isset( $sub_fields['due_date'] ) ) {

				$sub_fields['due_date']['args'] = array_merge( $sub_fields['due_date']['args'], $this->get_datepicker_localization() );

			}

			if ( isset( $sub_fields['assigned'] ) ) {

				$sub_fields['assigned']['args'] = array_merge( $sub_fields['assigned']['args'], $this->get_select_localization() );

			}

			if ( isset( $field['attributes']['class'] ) ) {

				$sub_fields[ $field['name'] ]['args']['input_class'] = $field['attributes']['class'];

			}

			if( isset( $sub_fields['task_description'] ) ) {
				$sub_fields[ $field['name'] ]['type'] = 'textarea';
				$sub_fields[ $field['name'] ]['args']['input_class'] = 'subtask-description';
			}

		}

		// This expects Fields in RBM Field Helpers format, not Project Panorama format
		$sub_fields = apply_filters( 'psp_st_sub_task_fields', $sub_fields );

		psp_st_array_insert( $fields, $due_date_index + 1, array( array(
			'name' => 'sub_tasks',
			'no_init' => true,
			'callback' => 'psp_st_do_field_repeater',
			'label' => __( 'Sub Task','psp-subtasks' ),
			'classes' => 'psp_projects-select2-multiple',
			'fields' => $sub_fields,
		) + $this->get_repeater_localization() ) );

		return $fields;

	}

	/**
	 * The Status/Completion Field does not exist in the normal Task Creation Dialog, but if it isn't there then the data gets deleted on Save
	 *
	 * @param		array $sub_fields Sub Fields
	 *
	 * @access		public
	 * @since		{{VERSION}}
	 * @return		array Sub Fields
	 */
	public function add_hidden_status_field( $sub_fields ) {

		$sub_fields['status'] = array(
			'type' => 'hidden',
		);

		return $sub_fields;

	}

	/**
	 * Prepopulates our custom Fields in the Edit Task modal
	 *
	 * @param		array   $values   Task Values
	 * @param		integer $post_id  Post ID
	 * @param		integer $phase_index Phase Index
	 * @param		integer $task_index  Task Index
	 * @param		array   $phases   All Phases
	 * @param		array   $phase    Current Phase
	 *
	 * @access		public
	 * @since		{{VERSION}}
	 * @return		array   Task Values
	 */
	public function prepopulate_task_fields( $values, $post_id, $phase_index, $task_index, $phases, $phase ) {

		$sub_tasks = array();
		if ( isset( $phases[ $phase_index ]['tasks'][ $task_index ]['sub_task'] ) &&
			! empty( $phases[ $phase_index ]['tasks'][ $task_index ]['sub_task'] ) ) {
			$sub_tasks = $phases[ $phase_index ]['tasks'][ $task_index ]['sub_task'];
		}

		// Copy how Frontend Editor handles it for Due Date
		$date_format_option = get_option( 'date_format', 'F j, Y' );
		$date_format = ( substr( $date_format_option, 0, 1 ) == 'd' ? 'd/m/Y' : 'm/d/Y' );

		// Correctly format any fields with "Date" in the name
		foreach ( $sub_tasks as &$sub_task ) {

			foreach ( $sub_task as $key => $value ) {

				if ( strpos( $key, 'date' ) !== false ) {

					// Make sure there is a value, otherwise this defaults to todays date
					if( $value && !empty($value) ) {
						$sub_task[ $key ] = date( 'Ymd', strtotime( $value ) );
					}

				}

			}

		}

		$values['sub_task'] = json_encode( $sub_tasks );

		return $values;

	}

	/**
	 * Sets up the fields for Saving
	 *
	 * @param		array   $task        Task Values
	 * @param		integer $post_id     Post ID
	 * @param		integer $phase_index    Phase Index
	 * @param		integer $task_index     Task Index
	 * @param		array   $phases      All Phases in Project
	 * @param		array   $global_post $_POST Data
	 *
	 * @access		public
	 * @since		{{VERSION}}
	 * @return		array   Task Values
	 */
	public function save_task_fields( $task, $post_id, $phase_id, $task_id, $phases, $global_post ) {

		$task['sub_task'] = $global_post['sub_tasks'];

		return $task;

	}

	/**
	 * Adds the Parent Task of an Assigned Sub Task to the output of psp_get_all_my_tasks()
	 * This is important if the Parent Task is not assigned to you, but Sub Tasks are
	 *
	 * @param		array $phase_tasks The Parent Tasks within a Phase assigned to you
	 * @param		array $phase       Phase Data
	 * @param		array $task        Parent Task Data
	 *
	 * @access		public
	 * @since		{{VERSION}}
	 * @return		void
	 */
	public function add_assigned_subtasks( &$phase_tasks, $phase, $task ) {

		if ( have_rows( 'sub_task' ) ) {

			$current_user = wp_get_current_user();

			while ( have_rows( 'sub_task' ) ) {

				$sub_task = the_row();

				$assigned = get_sub_field('assigned');

				if( ( is_array($assigned) && in_array($current_user->ID,$assigned) ) || $assigned == $current_user->ID ) {

					if( !isset($task['task']) ) {
						continue;
					}

					$phase_tasks[] = array(
						'task' => $task['task'],
						'status' => $task['status'],
						'added_via_assigned_subtask' => true,
					);

				}

			}

		}

	}

	/**
	 * Even if the Data exists in psp_get_all_my_tasks(), the Template still has some checks we need to overcome
	 *
	 * @param		boolean $show_task Whether or not to show the Parent Task
	 * @param		array   $phase     Phase Data
	 * @param		array   $task      Task Data
	 *
	 * @access		public
	 * @since		{{VERSION}}
	 * @return		boolean Whether to show the Parent Task or not
	 */
	public function show_parent_task( $show_task, $phase, $task ) {

		if ( ! $show_task &&
		   isset( $task['sub_task'] ) ) {

			$current_user = wp_get_current_user();

			if( !isset($task['sub_task']) || empty($task['sub_task']) ) {
				return;
			}

			foreach ( $task['sub_task'] as $sub_task ) {

				if( is_array($sub_task['assigned']) && in_array( $current_user->ID, $sub_task['assigned'] ) ) {
					return true;
				}

				if ( $sub_task['assigned'] == $current_user->ID ) {
					return true;
				}

			}

		}

		return $show_task;

	}

	/**
	 * Have instances where the Parent Task is not assigned to you, but a Sub Task is, be counted on the Dashboard under Assigned/In Progress/Completed
	 *
	 * @param		integer $phase_count     Phase Count
	 * @param		integer $tasks_count     Assigned Tasks Count
	 * @param		integer $completed_count Completed Tasks Count
	 * @param		integer $started_count   Started Tasks Count
	 * @param		array   $phase           Current Phase in Loop
	 * @param		array   $task            Current Task in Loop
	 * @param		integer $user_id         Current User ID
	 * @param		integer $post_id         Current Project ID
	 *
	 * @access		public
	 * @since		{{VERSION}}
	 * @return		void
	 */
	public function show_subtasks_in_item_count( &$phase_count, &$tasks_count, &$completed_count, &$started_count, $phase, $task, $user_id, $post_id ) {

		if( !empty( $task['psp_st_sub_tasks'] ) ) {

			foreach ( $task['psp_st_sub_tasks'] as $sub_task ) {

				if ( ( is_array($sub_task['field_532b8da69c46e']) && in_array($user_id, $sub_task['field_532b8da69c46e']) ) || $sub_task['field_532b8da69c46e'] == $user_id ) {

					$tasks_count++;

					if ( $sub_task['field_527d5e0e2fa2f'] > 0 &&
					   $task['field_527d5e0e2fa2f'] < 100 ) {
						$started_count++;
					}

					// We found one. We are counting Parent Tasks rather than the individual Sub Tasks, so we bail
					break;

				}

			}

		}

	}

	/**
	 * DRY copy of the Localization for our Repeater
	 * Since our implementation here requires the JS Global to be made manually, at least try to make it clean
	 *
	 * @access		public
	 * @since		{{VERSION}}
	 * @return		array Repeater L10N
	 */
	public function get_repeater_localization() {

		return apply_filters( 'psp_st_sub_task_repeater_l10n', array(
			'collapsable'            => false,
			'sortable'               => true,
			'isFirstItemUndeletable' => false,
			'l10n'                   => array(
				'collapsable_title' => __( 'New Sub Task', 'psp-subtasks' ),
				'confirm_delete_text'    => __( 'Are you sure you want to delete this Sub Task?', 'psp-subtasks' ),
				'delete_item'       => __( 'Delete Sub Task', 'psp-subtasks' ),
				'add_item'          => __( 'Add Sub Task', 'psp-subtasks' ),
			),
		) );

	}

	/**
	 * DRY copy of the Localization for our Datepickers in the Repeater
	 * Since our implementation here requires the JS Global to be made manually, at least try to make it clean
	 *
	 * @access		public
	 * @since		{{VERSION}}
	 * @return		array Datepicker L10N
	 */
	public function get_datepicker_localization() {

		// In this case, PHP and JavaScript actually use different values
		return apply_filters( 'psp_st_sub_task_due_date_l10n', array(
			'datepickerOptions' => array(
				'altFormat' => 'yymmdd',
			),
			'datepicker_args' => array(
				'altFormat' => 'Ymd',
			),
		) );

	}

	/**
	 * DRY copy of the Localization for our Select Fields in the Repeater
	 * Since our implementation here requires the JS Global to be made manually, at least try to make it clean
	 *
	 * @access		public
	 * @since		{{VERSION}}
	 * @return		array Select L10N
	 */
	public function get_select_localization() {

		$defaults = array(
			'opt_groups' => false,
			'opt_group_selection_prefix' => true,
			'select2_disable' => true,
			'select2_options' => array(
				'placeholder' => '',
				'containerCssClass' => 'fieldhelpers-select2',
				'dropdownCssClass'  => 'fieldhelpers-select2',
				'language' => array(),
			),
		);

		// Filtering using PHP Version
		$options = apply_filters( 'psp_st_sub_task_assigned_l10n', $defaults );

		// Assign JavaScript equivalents
		$options['optGroups'] = $options['opt_groups'];
		$options['optGroupSelectionPrefix'] = $options['opt_group_selection_prefix'];
		$options['select2Disabled'] = $options['select2_disable'];
		$options['select2Options'] = $options['select2_options'];

		return $options;

	}

}

$instance = new PspST_Frontend_Editor();
