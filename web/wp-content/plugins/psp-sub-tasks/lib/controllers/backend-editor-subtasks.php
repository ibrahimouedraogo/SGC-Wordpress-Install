<?php
/**
 * The Post Edit Screen for PSP Sub Tasks
 *
 * @since		{{VERSION}}
 *
 * @package psp-sub-tasks
 * @subpackage psp-sub-tasks/core/admin
 */

defined( 'ABSPATH' ) || die();

final class PspST_Post_Edit {

	/**
	 * PspST_Post_Edit constructor.
	 *
	 * @since		{{VERSION}}
	 */
	function __construct() {

		if ( is_admin() ) {

			// Enqueue our Scripts on the Post Edit screen
			add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );

			add_action( 'save_post', array( $this, 'save_post' ), 900 );

		}

		// The Frontend Editor uses its own, ACF-specific, save routine
		add_filter( 'acf/save_post', array( $this, 'save_post' ), 900 );

	}

	/**
	 * Add JS to the Page Footer (Admin Only)
	 *
	 * @access		public
	 * @since		{{VERSION}}
	 * @return		void
	 */
	public function admin_enqueue_scripts() {

		$current_screen = get_current_screen();
		global $pagenow;

		if ( $current_screen->post_type == 'psp_projects' &&
			in_array( $pagenow, array( 'post-new.php', 'post.php' ) ) ) {
			// Only load for the Project Post Type

			wp_enqueue_script( 'psp-st-admin' );

		}

	}

	/**
	 * Server-side verification of our JS calculations
	 *
	 * @param		integer $post_id Post ID
	 *
	 * @access		public
	 * @since		{{VERSION}}
	 * @return		void
	 */
	public function save_post( $post_id ) {

		if ( get_post_type() !== 'psp_projects' )
			return;

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
			return;

		if ( defined( 'DOING_AJAX' ) && DOING_AJAX )
        	return;

		if ( false !== wp_is_post_revision( $post_id ) )
        	return;

		$this->validate_parent_task_completion( $_POST );

		return $post_id;

	}

	/**
	 * Validates the Parent Task Completion in $_POST passed by reference
	 *
	 * @param		array &$global_post $_POST
	 *
	 * @access		private
	 * @since		{{VERSION}}
	 * @return		void
	 */
	private function validate_parent_task_completion( &$global_post ) {

		$phases_key = 'field_527d5dc12fa29';
		$completion_key = 'field_527d5e0e2fa2f';
		$tasks_key = 'field_527d5dfd2fa2d';

		// For simplicity/readability
		$phases = &$global_post['fields'][ $phases_key ];

		if( !$phases || empty($phases) ) {
			return;
		}

		foreach ( $phases as &$phase ) {

			$tasks = &$phase[ $tasks_key ];

			foreach ( $tasks as &$task ) {

				// psp_calculate_parent_task_progress() expects the data a certian way
				$task['status'] = $task[ $completion_key ];
				$task['sub_task'] = $task['psp_st_sub_tasks'];

				// We need to ensure that we calculate for the correct number of subtasks
				foreach ( $task['sub_task'] as $key => $sub_task ) {

					if ( $key === 'acfcloneindex' ) {
						unset( $task['sub_task'][ $key ] );
						continue;
					}

					// Put the status/completion data where psp_calculate_parent_task_progress() expects it
					$task['sub_task'][ $key ]['status'] = $task['sub_task'][ $key ][ $completion_key ];

				}

				// Save as a String like it normally would
				$task[ $completion_key ] = (String) psp_calculate_parent_task_progress( $task );

				// Prevent possible saving oddities
				unset( $task['status'] );
				unset( $task['sub_task'] );

			}

		}

	}

}

$instance = new PspST_Post_Edit();
