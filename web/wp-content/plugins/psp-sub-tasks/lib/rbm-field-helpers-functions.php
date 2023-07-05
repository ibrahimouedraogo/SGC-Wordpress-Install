<?php
/**
 * Some heleprs functions for RBM FH
 *
 * @since {{VERSION}}
 */

defined( 'ABSPATH' ) || die();

/**
 * Quick access to plugin field helpers.
 *
 * @since {{VERSION}}
 *
 * @return RBM_FieldHelpers
 */
function psp_st_field_helpers() {
	
	global $psp_st_field_helpers;
	
	return $psp_st_field_helpers;
	
}

/**
 * Initializes a field group for automatic saving.
 *
 * @since {{VERSION}}
 *
 * @param $group
 */
function psp_st_init_field_group( $group ) {
	psp_st_field_helpers()->fields->save->initialize_fields( $group );
}

/**
 * Gets a meta field helpers field.
 *
 * @since {{VERSION}}
 *
 * @param string $name Field name.
 * @param string|int $post_ID Optional post ID.
 * @param mixed $default Default value if none is retrieved.
 * @param array $args
 *
 * @return mixed Field value
 */
function psp_st_get_field( $name, $post_ID = false, $default = '', $args = array() ) {
    $value = psp_st_field_helpers()->fields->get_meta_field( $name, $post_ID, $args );
    return $value !== false ? $value : $default;
}

/**
 * Gets a option field helpers field.
 *
 * @since {{VERSION}}
 *
 * @param string $name Field name.
 * @param mixed $default Default value if none is retrieved.
 * @param array $args
 *
 * @return mixed Field value
 */
function psp_st_get_option_field( $name, $default = '', $args = array() ) {
	$value = psp_st_field_helpers()->fields->get_option_field( $name, $args );
	return $value !== false ? $value : $default;
}

/**
 * Outputs a text field.
 *
 * @since {{VERSION}}
 *
 * @param array $args
 */
function psp_st_do_field_text( $args = array() ) {
	psp_st_field_helpers()->fields->do_field_text( $args['name'], $args );
}

/**
 * Outputs a textarea field.
 *
 * @since {{VERSION}}
 *
 * @param array $args
 */
function psp_st_do_field_textarea( $args = array() ) {
	psp_st_field_helpers()->fields->do_field_textarea( $args['name'], $args );
}

/**
 * Outputs a checkbox field.
 *
 * @since {{VERSION}}
 *
 * @param array $args
 */
function psp_st_do_field_checkbox( $args = array() ) {
	psp_st_field_helpers()->fields->do_field_checkbox( $args['name'], $args );
}

/**
 * Outputs a toggle field.
 *
 * @since {{VERSION}}
 *
 * @param array $args
 */
function psp_st_do_field_toggle( $args = array() ) {
	psp_st_field_helpers()->fields->do_field_toggle( $args['name'], $args );
}

/**
 * Outputs a radio field.
 *
 * @since {{VERSION}}
 *
 * @param array $args
 */
function psp_st_do_field_radio( $args = array() ) {
	psp_st_field_helpers()->fields->do_field_radio( $args['name'], $args );
}

/**
 * Outputs a select field.
 *
 * @since {{VERSION}}
 *
 * @param array $args
 */
function psp_st_do_field_select( $args = array() ) {
	psp_st_field_helpers()->fields->do_field_select( $args['name'], $args );
}

/**
 * Outputs a number field.
 *
 * @since {{VERSION}}
 *
 * @param array $args
 */
function psp_st_do_field_number( $args = array() ) {
	psp_st_field_helpers()->fields->do_field_number( $args['name'], $args );
}

/**
 * Outputs an image field.
 *
 * @since {{VERSION}}
 *
 * @param array $args
 */
function psp_st_do_field_media( $args = array() ) {
	psp_st_field_helpers()->fields->do_field_media( $args['name'], $args );
}

/**
 * Outputs a datepicker field.
 *
 * @since {{VERSION}}
 *
 * @param array $args
 */
function psp_st_do_field_datepicker( $args = array() ) {
	psp_st_field_helpers()->fields->do_field_datepicker( $args['name'], $args );
}

/**
 * Outputs a timepicker field.
 *
 * @since {{VERSION}}
 *
 * @param array $args
 */
function psp_st_do_field_timepicker( $args = array() ) {
	psp_st_field_helpers()->fields->do_field_timepicker( $args['name'], $args );
}

/**
 * Outputs a datetimepicker field.
 *
 * @since {{VERSION}}
 *
 * @param array $args
 */
function psp_st_do_field_datetimepicker( $args = array() ) {
	psp_st_field_helpers()->fields->do_field_datetimepicker( $args['name'], $args );
}

/**
 * Outputs a colorpicker field.
 *
 * @since {{VERSION}}
 *
 * @param array $args
 */
function psp_st_do_field_colorpicker( $args = array() ) {
	psp_st_field_helpers()->fields->do_field_colorpicker( $args['name'], $args );
}

/**
 * Outputs a list field.
 *
 * @since {{VERSION}}
 *
 * @param array $args
 */
function psp_st_do_field_list( $args = array() ) {
	psp_st_field_helpers()->fields->do_field_list( $args['name'], $args );
}

/**
 * Outputs a hidden field.
 *
 * @since {{VERSION}}
 *
 * @param array $args
 */
function psp_st_do_field_hidden( $args = array() ) {
	psp_st_field_helpers()->fields->do_field_hidden( $args['name'], $args );
}

/**
 * Outputs a table field.
 *
 * @since {{VERSION}}
 *
 * @param array $args
 */
function psp_st_do_field_table( $args = array() ) {
	psp_st_field_helpers()->fields->do_field_table( $args['name'], $args );
}

/**
 * Outputs a HTML field.
 *
 * @since {{VERSION}}
 *
 * @param array $args
 */
function psp_st_do_field_html( $args = array() ) {
	psp_st_field_helpers()->fields->do_field_html( $args['name'], $args );
}

/**
 * Outputs a repeater field.
 *
 * @since {{VERSION}}
 *
 * @param mixed $values
 */
function psp_st_do_field_repeater( $args = array() ) {
	psp_st_field_helpers()->fields->do_field_repeater( $args['name'], $args );
}

/**
 * Outputs a String if a Callback Function does not exist for an Options Page Field
 *
 * @since {{VERSION}}
 *
 * @param array $args
 */
function psp_st_missing_callback( $args ) {
	
	printf( 
		_x( 'A callback function called "psp_st_do_field_%s" does not exist.', '%s is the Field Type', 'psp-subtasks' ),
		$args['type']
	);
		
}