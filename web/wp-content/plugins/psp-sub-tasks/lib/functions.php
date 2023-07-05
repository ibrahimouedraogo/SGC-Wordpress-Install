<?php
/**
 * Provides helper functions.
 *
 * @since		{{VERSION}}
 *
 * @package	psp-sub-tasks
 * @subpackage psp-sub-tasks/lib
 */
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

/**
 * Utility Function to insert one Array into another at a specified Index. Useful for the Notification Repeater Field's Filter
 * 
 * @param		array   &$array       Array being modified. This passes by reference.
 * @param		integer $index        Insertion Index. Even if it is an associative array, give a numeric index. Determine it by doing a foreach() until you hit your desired placement and then break out of the loop.
 * @param		array   $insert_array Array being Inserted at the Index
 *                                                           
 * @since		{{VERSION}}
 * @return		void
 */
function psp_st_array_insert( &$array, $index, $insert_array ) { 

	// First half before the cut-off for the splice
	$first_array = array_splice( $array, 0, $index ); 

	// Merge this with the inserted array and the last half of the splice
	$array = array_merge( $first_array, $insert_array, $array ); 

}

/**
 * Localizes Script in the same way WP does, but whenever we could need it
 * 
 * @since		{{VERSION}}
 * @return		void
 */
function psp_st_localize_js( $object_name, $l10n ) {

	foreach ( $l10n as $key => $value ) {

		if ( ! is_scalar( $value ) )
			continue;

		$l10n[$key] = html_entity_decode( (string) $value, ENT_QUOTES, 'UTF-8' );

	}

	$script = "var $object_name = " . wp_json_encode( $l10n ) . ';';

	$script = "/* <![CDATA[ */\n" . $script . "\n/* ]]> */";

	?>

	<script type="text/javascript"><?php echo $script; ?></script>

	<?php

}