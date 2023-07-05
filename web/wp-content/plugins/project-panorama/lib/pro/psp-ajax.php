<?php
/**
 * Update the task status on the frontend
 * @return NULL
 */
function psp_update_task_fe( $project_id = null, $phase_id = null, $task_id = null, $progress = null ) {

     if( empty($project_id) ) {
          $project_id     = $_POST[ "project_id" ];
     }

     if( empty($phase_id) ) {
          $phase_id       = $_POST[ "phase_id" ];
     }

     if( empty($task_id) ) {
          $task_id        = $_POST[ "task_id" ];
     }

     if( empty($progress) ) {
          if( isset($_POST['progress'] ) ) {
               $progress = $_POST[ "progress" ];
          } else {
               $progress = intval(0);
          }
     }

    $phases         = get_field( 'phases', $project_id );
    $cuser          = wp_get_current_user();

    $i = 0;
    $phase_index = 0;
    foreach( $phases as $phase ) {
         if( $phase['phase_id'] == $phase_id ) {
              $phase_index = $i;
         }
         $i++;
    }

    $i = 0;
    $task_index = 0;
    foreach( $phases[$phase_index]['tasks'] as $task ) {
         if( $task['task_id'] == $task_id ) {
              $task_index = $i;
         }
         $i++;
    }

	$phases[ $phase_index ][ 'tasks' ][ $task_index ][ 'status' ] = $progress;

	update_field( 'phases', $phases, $project_id );

	$phase_info    = $phases[ $phase_index ];
	$task_info     = $phase_info[ 'tasks' ][ $task_index ];
	$task_title    = $task_info['task'];
     $user_id       = $cuser->ID;

	/**
	 * Fires when updating a task to a new progress.
	 *
	 * @since {{VERSION}}
	 */
	do_action( 'psp_update_task', $task_info, $progress, $task_id, $project_id, $phase_id, $user_id );

	if ( $progress == '100' ) {

		do_action( 'psp_notify', 'task_complete', array(
			'task_title' => $task_title,
			'task_info'  => $task_info,
			'project_id' => $project_id,
			'phase_info' => $phase_info,
			'phase_id'   => $phase_index,
            'post_id'    => $project_id,
            'user_id'    => $user_id,
            'description'  =>  $task_info['task_description'],
		) );
	}

    $current_progress 	= get_post_meta( $project_id, '_psp_current_progress', true );
    $new_progress		= psp_compute_progress( $project_id );

    if( $new_progress != $current_progress ) {
        // Progress has moved forward so we fire an acction for the current progress
        do_action( 'psp_project_progress_change', $project_id, $new_progress, $current_progress );
    }

    update_post_meta( $project_id, '_psp_current_progress', $new_progress );

}
add_action( 'wp_ajax_nopriv_psp_update_task_fe', 'psp_update_task_fe' );
add_action( 'wp_ajax_psp_update_task_fe', 'psp_update_task_fe' );

/**
 * Calculate and return the total to the frontend
 * @return [int] [0 - 100]
 */
function psp_update_total_fe() {

    $project_id = $_POST[ 'project_id' ];
    $status     = psp_compute_progress( $project_id );

    psp_set_project_completion( $project_id, $status );

	echo psp_compute_progress( $project_id );

	die();

}
add_action( 'wp_ajax_nopriv_psp_update_total_fe', 'psp_update_total_fe' );
add_action( 'wp_ajax_psp_update_total_fe','psp_update_total_fe' );

/**
 * Update the document status through the frontend
 *
 * @return NULL
 */
function psp_update_doc_fe() {

    $project_id     = $_POST[ 'project_id' ];
    $project_name   = get_the_title( $project_id );
    $doc_id         = $_POST[ 'doc_id' ];
    $status         = $_POST[ 'status' ];
    $filename       = $_POST[ 'filename' ];
    $editor         = psp_username_by_id( $_POST[ 'editor' ] );

    $users          = $_POST["users"];

	$message = "<h3 style='font-size: 18px; font-weight: normal; font-family: Arial, Helvetica, San-serif;'>" . $project_name . "</h3>";
	$message .= "<p><strong>" . $filename . __( " status has been changed to ", "psp_projects" ) . $status . __( " by ", "psp_projects" ) . $editor . "</p>";
    $message .= $_POST[ "message" ];

    $subject = $project_name . ": " . $filename . __( " status has been changed to ", "psp_projects" ) . $status . __(" by ", "psp_projects" ) . $editor;

    $docs = get_field( 'documents', $project_id );

    $docs[$doc_id]['status'] = $status;

    if( psp_get_option( 'psp_disable_file_obfuscation' ) ) {
    	$file_url 	= ( !empty( $docs[$doc_id]['file'] ) ? $docs[$doc_id]['file']['url'] : $docs[$doc_id]['url'] );
    } elseif( get_option('permalink_structure') ) {
    	$file_url 	= ( !empty( $docs[$doc_id]['file'] ) ? get_permalink( $post_id ) . '?psp_download=' . $docs[$doc_id] : $docs[$doc_id]['url'] );
    } else {
    	$file_url 	= ( !empty( $docs[$doc_id]['file'] ) ? get_permalink( $post_id ) . '&psp_download=' . $docs[$doc_id] : $docs[$doc_id]['url'] );
    }

    update_field( 'documents', $docs, $project_id );

    if( !empty($users) ) {
         foreach( $users as $user ) {
             psp_send_progress_email( $user, $subject, $message, $project_id );
        }
    }

    do_action( 'psp_document_status_changed', $project_id, $docs, $doc_id, $status );

    do_action( 'psp_notify', 'document_status', array(
        'name'       => $filename,
        'status'     => $status,
        'project_id' => $project_id,
        'message'    => $_GET['message'],
        'post_id'    => $project_id,
        'user_id'    => $user_id,
        'file_url'   => $file_url,
    ) );

    $document_stats = psp_count_documents( $project_id );

    wp_send_json_success( array( 'success' => true, 'approved' => $document_stats['approved'] ) );

}
add_action( 'wp_ajax_nopriv_psp_update_doc_fe', 'psp_update_doc_fe' );
add_action( 'wp_ajax_psp_update_doc_fe', 'psp_update_doc_fe' );

/**
 * Gets the total number of approved documents for a given phase and returns them
 * @return [type] [description]
 */
function psp_get_phase_approval_count() {

    $post_id    = $_POST['post_id'];
    $phase_id   = $_POST['phase_id'];

    if( !isset($post_id) || empty($post_id) || !isset($phase_id) || ( empty($phase_id) && $phase_id != 0 ) ) {
        wp_send_json_error( array( 'success' => false, 'message' => 'Missing post and phase ID', 'post_id' => $_POST['post_id'], 'phase_id' => $_POST['phase_id'] ) );
        die();
    }

    $approvals = 0;
    $documents = get_field( 'documents', $post_id );
    $phases    = get_field( 'phases', $post_id );

    foreach( $documents as $document ) {
        if( $document['document_phase'] == $phases[$phase_id]['phase_id'] && ( $document['status'] == 'Approved' || $document['status'] == 'none' ) ) $approvals++;
    }

    wp_send_json_success( array( 'success' => true, 'count' => $approvals ) );

}
add_action( 'wp_ajax_psp_get_phase_approval_count', 'psp_get_phase_approval_count' );
add_action( 'wp_ajax_nopriv_psp_get_phase_approval_count', 'psp_get_phase_approval_count' );

/**
 * Ajax callback for grabbing Task Discussions in the Task Panel
 *
 * @since		{{VERSION}}
 * return		void
 */
function psp_get_task_discussions() {

	$task_comment_key = $_POST['task_id'];
	$post_id = $_POST['project'];
	$comment_count = psp_get_task_comment_count( $task_comment_key, $post_id );

	ob_start();
	include psp_template_hierarchy( '/projects/phases/tasks/discussions/index.php' );
	$content = ob_get_clean();

    wp_send_json_success( array(
		'success'     => true,
        'post_id'     => $post_id,
		'content'     => $content,
		'count'       => $comment_count,
        'key'         =>  $task_comment_key
	) );

}
add_action( 'wp_ajax_psp_get_task_discussions', 'psp_get_task_discussions' );
add_action( 'wp_ajax_nopriv_psp_get_task_discussions', 'psp_get_task_discussions' );

/**
 * Ajax callback for grabbing Task Documents in the Task Panel
 *
 * @since		{{VERSION}}
 * return		void
 */
function psp_get_task_documents() {

	$task_id = $_POST['task_id'];
	$post_id = $_POST['project'];

	$task_docs = psp_parse_task_documents( get_field( 'documents', $post_id ), $task_id );

	ob_start();
	include psp_template_hierarchy( '/projects/phases/tasks/documents/index.php' );
	$content = ob_get_clean();

    wp_send_json_success( array(
		'success' => true,
		'content' => $content,
		'count' => ( $task_docs ) ? count( $task_docs ) : 0,
        'key'   => $task_id
	) );

}
add_action( 'wp_ajax_psp_get_task_documents', 'psp_get_task_documents' );
add_action( 'wp_ajax_nopriv_psp_get_task_documents', 'psp_get_task_documents' );

/**
 * Ajax callback for generating a Task ID
 *
 * @since		{{VERSION}}
 * return		void
 */
function psp_ajax_generate_task_id() {

    wp_send_json_success( psp_generate_task_id() );

}
add_action( 'wp_ajax_psp_generate_task_id', 'psp_ajax_generate_task_id' );

/**
 * Ajax callback for generating a Phase ID
 *
 * @since		{{VERSION}}
 * return		void
 */
function psp_ajax_generate_phase_id() {

    wp_send_json_success( psp_generate_phase_id() );

}
add_action( 'wp_ajax_psp_generate_phase_id', 'psp_ajax_generate_phase_id' );

add_action( 'wp_ajax_psp_delete_comment', 'psp_delete_comment' );
function psp_delete_comment() {

     $comment_id = $_POST['comment_id'];
     $post_id    = $_POST['post_id'];
     $cuser      = wp_get_current_user();

     if( !isset($comment_id) || !isset($post_id) || empty($comment_id) || empty($post_id) ) {
          wp_send_json_error( array( 'success' => false, 'message' => __( 'Missing data, please try again', 'psp_projects' ) ) );
     }

     if ( !current_user_can( 'edit_comment', $comment_id ) ) {
          wp_send_json_error( array( 'success' => false, 'message' => __( 'You don\'t have permission to edit this message', 'psp_projects' ) ) );
     }

     wp_delete_comment( $comment_id );

     wp_send_json_success( array( 'success' => true ) );

}

add_action( 'wp_ajax_psp_edit_comment', 'psp_edit_comment' );
function psp_edit_comment() {

     $comment_id  = $_POST['comment_id'];
     $post_id     = $_POST['post_id'];
     $new_content = wpautop( stripslashes($_POST['new_content']) );
     $cuser       = wp_get_current_user();

     if( !isset($comment_id) || !isset($post_id) || empty($comment_id) || empty($post_id) ) {
          wp_send_json_error( array( 'success' => false, 'message' => __( 'Missing data, please try again', 'psp_projects' ) ) );
     }

     if ( !current_user_can( 'edit_comment', $comment_id ) ) {
          wp_send_json_error( array( 'success' => false, 'message' => __( 'You don\'t have permission to edit this message', 'psp_projects' ) ) );
     }

     $comment = array(
          'comment_ID' => $comment_id,
          'comment_content' => $new_content,
     );

     wp_update_comment( $comment );

     wp_send_json_success( array( 'success' => true, 'content' => $new_content ) );

}
