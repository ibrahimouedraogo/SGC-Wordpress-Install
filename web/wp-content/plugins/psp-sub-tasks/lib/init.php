<?php
$deps = array(
	'rbm-field-helpers/rbm-field-helpers',
	'rbm-field-helpers-functions',
	'functions',
    'controllers/progress',
	'controllers/backend-editor-subtasks',
    'models/subtasks',
	'models/frontend-editor-subtasks',
    'views/subtasks',
	'assets',
	'pspst-settings',
);

foreach( $deps as $dep ) include_once( $dep . '.php' );

global $psp_st_field_helpers;

$psp_st_field_helpers = new RBM_FieldHelpers( array(
	'ID'   => 'psp_st', // Your Theme/Plugin uses this to differentiate its instance of RBM FH from others when saving/grabbing data
	'l10n' => array(
		'field_table'    => array(
			'delete_row'    => __( 'Delete Row', 'psp-subtasks' ),
			'delete_column' => __( 'Delete Column', 'psp-subtasks' ),
		),
		'field_select'   => array(
			'no_options'       => __( 'No select options.', 'psp-subtasks' ),
			'error_loading'    => __( 'The results could not be loaded', 'psp-subtasks' ),
			/* translators: %d is number of characters over input limit */
			'input_too_long'   => __( 'Please delete %d character(s)', 'psp-subtasks' ),
			/* translators: %d is number of characters under input limit */
			'input_too_short'  => __( 'Please enter %d or more characters', 'psp-subtasks' ),
			'loading_more'     => __( 'Loading more results...', 'psp-subtasks' ),
			/* translators: %d is maximum number items selectable */
			'maximum_selected' => __( 'You can only select %d item(s)', 'psp-subtasks' ),
			'no_results'       => __( 'No results found', 'psp-subtasks' ),
			'searching'        => __( 'Searching...', 'psp-subtasks' ),
		),
		'field_repeater' => array(
			'collapsable_title' => __( 'New Row', 'psp-subtasks' ),
			'confirm_delete'    => __( 'Are you sure you want to delete this element?', 'psp-subtasks' ),
			'delete_item'       => __( 'Delete', 'psp-subtasks' ),
			'add_item'          => __( 'Add', 'psp-subtasks' ),
		),
		'field_media'    => array(
			'button_text'        => __( 'Upload / Choose Media', 'psp-subtasks' ),
			'button_remove_text' => __( 'Remove Media', 'psp-subtasks' ),
			'window_title'       => __( 'Choose Media', 'psp-subtasks' ),
		),
		'field_checkbox' => array(
			'no_options_text' => __( 'No options available.', 'psp-subtasks' ),
		),
	)
) );
