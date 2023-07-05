<?php
add_action( 'init', 'psp_setup_notifications' );
function psp_setup_notifications() {

	global $psp_notifications;

	$psp_notifications = apply_filters( 'psp_notifications', array() );

	if ( ! $psp_notifications ) {
		return;
	}

	foreach ( $psp_notifications as $notification_ID => $notification_args ) {

		// Init the post types
		psp_create_notification_post_type( $notification_ID, $notification_args );

		// Handle creating and updating feed post types
		if ( isset( $_POST['action'] ) && $_POST['action'] == 'update' ) {
			if ( isset( $_POST["psp_{$notification_ID}_notification_feeds"] ) ) {
				psp_notification_update_feeds( $notification_ID, $notification_args );
			}
		}
	}

	// Deleting feed post types
	if ( isset( $_POST["psp_notification_deleted_feeds"] ) ) {
		psp_notification_delete_feeds();
	}
}

add_action( 'psp_after_settings_table', 'psp_notifications_feeds_section' );
function psp_notifications_feeds_section( $args ) {

	global $psp_notifications;

	if ( ! $psp_notifications || ! isset( $_GET['tab'] ) || $_GET['tab'] != 'psp_settings_notifications' ) {
		return;
	}

	if ( ! isset( $_GET['section'] ) ) {

		$sections = psp_get_registered_settings_sections();
		$section  = key( $sections['psp_settings_notifications'] );

	} else {

		$section = $_GET['section'];
	}

	$notification_ID   = $section;
	$notification_args = $psp_notifications[ $notification_ID ];

	psp_notification_feeds_section( $notification_ID, $notification_args );
}

add_filter( 'psp_settings_sections_notifications', 'psp_notifications_feeds_sections' );
function psp_notifications_feeds_sections( $sections ) {

	global $psp_notifications;

	if ( ! $psp_notifications ) {
		return $sections;
	}

	foreach ( $psp_notifications as $notification_ID => $notification_args ) {
		$sections[ $notification_ID ] = $notification_args['name'];
	}

	return $sections;
}

add_action( 'psp_notify', 'psp_notify_handle', 10, 2 );
function psp_notify_handle( $type, $args, $email_parts = array() ) {

	global $psp_notifications;

	do_action( "psp_notify_$type", $args );

	if ( $psp_notifications ) {
		foreach ( $psp_notifications as $notification_ID => $notification_args ) {
			psp_notifications_do_notification( $notification_ID, $notification_args, $type, $args, $email_parts );
		}
	}

}

add_action( 'psp_users_added_to_project', 'psp_notify_users_of_project_assignment', 10, 2 );
function psp_notify_users_of_project_assignment( $post_id, $user_ids = null ) {

	// Requires database version 7
	if ( ( empty( $user_ids ) ) || ( get_option( 'psp_database_version' ) < 7 ) ) {
		return;
	}

	do_action( 'psp_notify', 'users_assigned', array(
		'post_id'  => $post_id,
		'user_ids' => $user_ids
	) );

	foreach ( $user_ids as $user_id ) {

		do_action( 'psp_notify', 'user_assigned', array(
			'post_id' => $post_id,
			'user_id' => $user_id,
		) );
	}

}
// $parts, $args, $notification_type, $fields
function psp_send_email( $email_parts = array(), $args = array(), $notification_type = null, $fields = array() ) {

	$psp_settings = get_option( 'psp_settings' );

	$from_name  = isset( $psp_settings['psp_from_name'] ) ? $psp_settings['psp_from_name'] : __( 'Project Panorama', 'psp_projects' );
	$from_email = isset( $psp_settings['psp_from_email'] ) ? $psp_settings['psp_from_email'] : get_option( 'admin_email' );
	$logo       = isset( $psp_settings['psp_logo'] ) ? $psp_settings['psp_logo'] : false;
	$cuser      = wp_get_current_user();

	if ( $from_name == '%current_user%' || $from_email == '%current_user%' ) {
		$from_email = ( $from_email == '%current_user%' ? $cuser->user_email : $from_email );
		$from_name  = ( $from_name == '%current_user%' ? psp_get_nice_username_by_id( $cuser->ID ) : $from_name );
	}

	/**
	 * Extracts variables for use in templates. There may also be more, for extended templates.
	 *
	 * @var string $from_email
	 * @var string $from_name
	 * @var string $from_logo
	 * @var string $recipient_email
	 * @var string $recipient_name
	 * @var string $subject
	 * @var string $message
	 */
	extract( $fields = wp_parse_args( $fields, array(
		'from_email'      => $from_email,
		'from_name'       => $from_name,
		'logo'            => $logo,
		'recipient_email' => '',
		'recipient_cc'	  => '',
		'recipient_bcc'   => '',
		'recipient_name'  => '',
		'subject'         => '',
		'progress'        => '',
		'message'         => '',
		'post_id'         => '',
		'user_ids'        => '',
	) ) );

	/**
	 * Dynamically insert current username if applicable
	 */

	$cuser = wp_get_current_user();

	if ( is_user_logged_in() ) {
		$from_email = ( $from_email == '%current_user%' ? $cuser->user_email : $from_email );
		$from_name  = ( $from_name == '%current_user%' ? psp_get_nice_username_by_id( $cuser->ID ) : $from_name );
	} else {
		$from_email = ( $from_email == '%current_user%' ? get_option( 'admin_email' ) : $from_email );
		$from_name  = ( $from_name == '%current_user%' ? __( 'Project Panorama', 'psp_projects' ) : $from_name );
	}

	$from_data = apply_filters( 'psp_notification_from_email', array(
		'from_email' => $from_email,
		'from_name' => $from_name,
	), $notification_type );

	$headers         = 'From: ' . $from_data['from_name'] . ' <' . $from_data['from_email'] . ">\r\n";

	$recipient_email = apply_filters( 'psp_notification_recipient_email', $recipient_email, $post_id, $user_ids, 'to', $notification_type );
	$recipient_cc 	 = apply_filters( 'psp_notification_recipient_cc_email', $recipient_cc, $post_id, $user_ids, 'cc', $notification_type );
	$recipient_bcc	 = apply_filters( 'psp_notification_recipient_bcc_email', $recipient_bcc, $post_id, $user_ids, 'bcc', $notification_type );

	if( !empty($recipient_cc) ) {
		$headers .= " Cc: " . $recipient_cc . "\r\n";
	}

	if( !empty($recipient_bcc) ) {
		$headers .= " Bcc: " . $recipient_bcc . "\r\n";
	}

	$headers = apply_filters( 'psp_notification_headers', $headers, $args, $notification_type );

	ob_start();

	include( psp_template_hierarchy( '/email/index.php' ) );

	$message = ob_get_clean();

	add_filter( 'wp_mail_content_type', 'psp_set_mail_content_type' );

	wp_mail( $recipient_email, $subject, $message, $headers );

	remove_filter( 'wp_mail_content_type', 'psp_set_mail_content_type' );

}

/**
 * Looks to see if notifications are turned on and if so, notify the users
 *
 *
 * @param integer $post_id post ID
 * @param integer $post Post Object
 *
 * @return NULL
 **/

add_action( 'save_post', 'psp_notify_users', 10 );
function psp_notify_users( $post_id ) {

	// Bail if we're doing an auto save
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	// if our current user can't edit this post, bail
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	if( get_post_type($post_id) != 'psp_projects' || !isset($_POST['psp-notify-users']) ) {
		return;
	}

	// Get an array of users
	$user_ids = psp_sanitize_integers( $_POST['psp-user'] );
	$progress = psp_compute_progress( $post_id );

	$subject = apply_filters( 'psp_notification_subject', $_POST['psp-notification-subject'] );
	$message = apply_filters( 'psp_notification_message', stripslashes( $_POST['psp-notify-message'] ) );

	if( $user_ids && !empty($user_ids) ) {

		foreach ( $user_ids as $user_id ) {

			$subject = apply_filters( 'psp_notification_subject_user', $subject, $user_id );
			$message = apply_filters( 'psp_notification_message_user', $message, $user_id );

			psp_send_progress_email( $user_id, $subject, $message, $post_id, $progress );

		}

	}

}

/*
 * Replace project template variables with the correct data
 *
 * @param string $text The text potentially containing template variables
 * @return string $text The text with all template variables replaced
 */
function psp_populate_project_template_variables( $text, $post_id = null ) {

	$post_id = ( isset( $post_id ) ? $post_id : get_the_ID() );

	$project_title = get_the_title( $post_id );
	$client        = sanitize_text_field( get_field( 'client', $post_id ) );
	$project_url   = get_the_permalink( $post_id );
	$dashboard     = psp_get_option( 'psp_slug', 'panorama' );
	$date          = current_time( get_option( 'date_format' ) );

	$template_var_replacements = array(
		'%project_title%' => ! empty( $project_title ) ? $project_title : '',
		'%client%'        => ! empty( $client ) ? $client : '',
		'%project_url%'   => ! empty( $project_url ) ? $project_url : '',
		'%dashboard%'     => ! empty( $dashboard ) ? $dashboard : '',
		'%date%'          => ! empty( $date ) ? $date : '',
	);

	foreach ( $template_var_replacements as $template_variable => $replacement ) {
		$text = str_replace( $template_variable, $replacement, $text );
	}

	return $text;
}

add_filter( 'psp_notification_subject', 'psp_populate_project_template_variables' );
add_filter( 'psp_notification_message', 'psp_populate_project_template_variables' );

/*
 * Replace template variables particular to each user with the correct data
 *
 * @param string $text The text potentially containing template variables
 * @param int $user_id The user ID
 * @return string $text The text with all template variables replaced
 */
function psp_populate_user_template_variables( $text, $user_id ) {

	$user_data    = get_userdata( $user_id );
	$display_name = ! empty( $user_data->display_name ) ? $user_data->display_name : '';

	return str_replace( '%name%', $display_name, $text );

}

add_filter( 'psp_notification_subject_user', 'psp_populate_user_template_variables', 10, 2 );
add_filter( 'psp_notification_message_user', 'psp_populate_user_template_variables', 10, 2 );

/*
 * Sanitize integers
 *
 * @param mixed $integers integers to be sanitized
 * @return mixed $integers sanitized integers
 */
function psp_sanitize_integers( $integers ) {

	if ( ! is_array( $integers ) ) {
		$integers = (int) sanitize_text_field( $integers );
	} else {
		foreach ( $integers as $index => $integer ) {
			$integers[ $index ] = (int) sanitize_text_field( $integer );
		}
	}

	return $integers;
}

function psp_set_mail_content_type() {
	return 'text/html';
}

add_filter( 'psp_notification_recipient_bcc_email', 'psp_replace_recipient_variables', 10, 5 );
add_filter( 'psp_notification_recipient_cc_email', 'psp_replace_recipient_variables', 10, 5 );
add_filter( 'psp_notification_recipient_email', 'psp_replace_recipient_variables', 10, 5 );
function psp_replace_recipient_variables( $recipient_email, $post_id, $user_ids = null, $email_header = 'to', $notification_type = null ) {

	$user_groups = array(
		'%users%'            => null,
		'%subscribers%'      => 'subscriber',
		'%project_owners%'   => 'psp_project_owner',
		'%project_managers%' => 'psp_project_manager',
		'%editiors%'		 => 'editor',
		'%administrators%'   => 'administrator'
	);

	if ( in_array( $recipient_email, array_keys( $user_groups ) ) ) {

		$key             = $recipient_email;
		$recipient_email = '';
		$users           = psp_get_project_users( $post_id );
		$cuser           = wp_get_current_user();

		if ( $users ) {
			foreach ( $users as $user ) {
				if ( ( $key == '%users%' || psp_user_has_role( $user_groups[ $key ], $user['ID'] ) ) && $user['ID'] != $cuser->ID ) {

					 $add_user = true;

					 // Is this a notification that could be on a private phase?
					if( isset($args['comment_ID']) && !user_can( $user['ID'], 'edit_psp_projects' ) ) {

						// Check for comment conditions
						if( isset($args['comment_ID']) ) {
							$task_key  = get_comment_meta( $args['comment_ID'], 'task-key', true );
							$phase_key = get_comment_meta( $args['comment_ID'], 'phase-key', true );
						}

						// Check for document conditions
						if( isset($args['file_name']) ) {
							$task_key = $args['task_key'];
							$phase	  = $args['phase'];
						}

						if( psp_task_is_private( $task_key, $post_id ) || psp_phase_is_private( $phase_key, $post_id ) ) {
							$add_user = false;
						}

					}

					if( $add_user ) {
						$recipient_email .= $user['user_email'] . ',';
					}

				}
			}
		}

	}

	if ( $user_ids != null ) {

		$recipient_email = '';

		foreach ( $user_ids as $user_id ) {
			$user            = get_user_by( 'id', $user_id );
			$recipient_email .= $user->user_email . ', ';
		}

	}

	return apply_filters( 'psp_replace_recipient_email', $recipient_email, $post_id, $user_ids, $email_header, $notification_type );

}

// Add the option to the publish metabox
add_action( 'post_submitbox_misc_actions', 'psp_notify_metabox' );
function psp_notify_metabox() {

	global $post;

	if ( 'psp_projects' != get_post_type( $post ) ) {
		return;
	}

	$notify_all = psp_get_option( 'psp_notify_all_by_default', false );

	if ( get_field( 'allowed_users', $post->ID ) ): ?>

        <div class="misc-pub-section misc-pub-section-last" style="border-top: 1px solid #eee;">
			<?php wp_nonce_field( plugin_basename( __FILE__ ), 'article_or_box_nonce' ); ?>
            <input type="checkbox" name="psp-notify-users" id="psp-notify-users" value="yes"/>
            <label for="psp-notify-users" class="select-it">
				<?php esc_html_e( 'Notify users of update', 'psp_projects' ); ?>
                <a href="#psp-notification-modal"
                   class="psp-notification-edit"><?php esc_html_e( 'Edit', 'psp_projects' ); ?></a>
            </label>
        </div>

        <div class="psp-notification-modal" id="psp-notification-modal">

            <div class="psp-notify-warning">
                <p><?php esc_html_e( "Save this project to notify recently added users.", 'psp_projects' ); ?></p>
            </div>

            <p>
                <strong><?php esc_html_e( "Select which users you'd like to notify upon save.", "psp_projects" ); ?></strong>
            </p>

            <ul class="psp-notify-list">
                <li class="all-line">

				 <input type="checkbox" class="all-checkbox" name="psp-notify-all" value="all" <?php echo checked($notify_all); ?>> <?php esc_html_e( 'All Users', 'psp_projects' ); ?></li>
				<?php
				// Get the project ID
				$project_id = $post->ID;

				$users = psp_get_project_users( $post->ID );
				if ( $users ): foreach ( $users as $user ): ?>
                    <li><input type="checkbox" name="psp-user[]" value="<?php echo esc_attr( $user['ID'] ); ?>"
                               class="psp-notify-user-box" <?php echo checked($notify_all); ?>><?php echo esc_html( psp_get_nice_username_by_id( $user['ID'] ) ); ?>
                    </li>
				<?php endforeach; endif; ?>
            </ul>

            <p><label for="psp-notification-subject"><?php esc_html_e( 'Subject', 'psp_projects' ); ?></label></p>
            <p><input type="text" id="psp-notification-subject" name="psp-notification-subject"
                      value="<?php echo psp_get_option( 'psp_default_subject' ); ?>"></p>
            <p><label for="psp-notify-message"><?php esc_html_e( 'Message', 'psp_projects' ); ?></label></p>
            <p><textarea name="psp-notify-message"
                         class="psp-notify-message"><?php echo psp_get_option( 'psp_default_message' ); ?></textarea>
            <p><label for="psp-notify-message"
                      class="psp-label-description"><?php esc_html_e( 'Available dynamic variables: %project_title% %client% %project_url% %dashboard% %date%', 'psp_projects' ); ?></label>
            </p>
            <p><?php esc_html_e( 'Selected users will be sent this notice next time you save this project.', 'psp_projects' ); ?></p>
            <p><a class="button-primary psp-notify-ok" href="#"><?php esc_html_e( 'OK', 'psp_projects' ); ?></a></p>

        </div>

	<?php else: ?>

        <div class="misc-pub-section misc-pub-section-last" style="border-top: 1px solid #eee;">
			<?php wp_nonce_field( plugin_basename( __FILE__ ), 'article_or_box_nonce' ); ?>
            <input type="checkbox" name="psp-notify-users" id="psp-notify-users" value="yes" disabled/>
            <label for="psp-notify-users"
                   class="select-it psp-disabled"><?php _e( 'Notify users of update', 'psp_projects' ); ?>
                <a href="#psp-no-users" class="psp-notification-help"><?php esc_html_e( 'Help', 'psp-projects' ); ?></a>
            </label>
        </div>

        <div class="psp-notification-modal" id="psp-notification-modal">
            <p>
                <strong><?php esc_html_e( 'Users must be assigned to this project before you can notify them.', 'psp-projects' ); ?></strong>
            </p>
            <p><?php esc_html_e( 'Assign users to this project by through the access tab under project overview. Once you save the project you can notify users on future updates.' ); ?></p>
            <p><?php esc_html_e( 'Example:', 'psp-projects' ); ?></p>
            <p>
                <img src="<?php echo site_url(); ?>/wp-content/plugins/project-panorama/assets/images/help/notification-help.png">
            </p>
        </div>

	<?php
	endif;

}

add_action( 'psp_project_progress_change', 'psp_project_change_notification', 10, 2 );
function psp_project_change_notification( $post_id, $new_progress, $current_progress = null ) {

	do_action( 'psp_notify', 'project_progress', array(
		'post_id'       => $post_id,
		'project_title' => get_the_title( $post_id ),
		'new_progress'  => $new_progress
	) );

	// Check to see if a notification is setup for a particular point

	$args = array(
		'post_type'         => 'psp-email-feed',
		'posts_per_page'    => -1,
		'meta_key'			=>	'psp_email_feed_notification',
		'meta_value'		=>	'project_progress_point',
	);
	$feeds = get_posts($args);

	// If none exist, peace out!
	if( !$feeds || empty($feeds) ) {
		return;
	}

	foreach( $feeds as $feed ) {

		if( !$current_progress || empty($current_progress) ) {
			$current_progress 	= get_post_meta( $post_id, '_psp_current_progress', true );
		}

		$progress_notification = intval( get_post_meta( $feed->ID, 'psp_email_feed_percent_complete', true ) );

		if( $current_progress < $progress_notification && $new_progress >= $progress_notification ) {

			do_action( 'psp_notify', 'project_progress_point', array(
				'post_id'       		=> $post_id,
				'project_title' 		=> get_the_title( $post_id ),
				'new_progress'  		=> intval($new_progress),
				'current_progress'		=> intval($current_progress),
				'progress_notification'	=> intval($progress_notification)
			) );

		}

	}

}

add_action( 'psp_js_variables', 'psp_set_notification_defaults' );
function psp_set_notification_defaults() {

	$notify_all = psp_get_option( 'psp_notify_all_by_default', 0 ); ?>

	var psp_notify_all = <?php echo esc_js( ( $notify_all ? 'true' : 'false' ) ); ?>;

	<?php

}

function psp_comment_notify_users( $user_ids = false, $args = array() ) {

	if( $user_ids == false ) {
		return false;
	}

	$subject = psp_get_option( 'psp_default_comment_subject', 'New message on %project_title% by %comment_author%' );
	$message = psp_get_option( 'psp_default_comment_message',
		'<strong>Project:</strong> %project_title%<br>
		<strong>Phase:</strong> %phase_title%<br>
		<strong>Task:</strong> %task_title%<br>
		<strong>Author:</strong> %comment_author%<br>
		<hr>

		%comment_content%

		%comment_link%' );

	$fields = apply_filters( 'psp_comment_notify_users_fields', array(
		'subject'         	=> $subject,
		'message'         	=> $message,
		'recipient_email' 	=> '',
		'recipient_cc'		=> '',
		'recipient_bcc'	=> '',
		'post_id'			=> $args['post_id'],
		'user_ids'		=> $user_ids,
	), $user_ids, $comment_data, $post_id );

	$replacements = psp_notifications_replacements(
		array( 'subject' => $fields['subject'], 'message' => $fields['message'] ),
		'new_comment',
		$args,
		array(),
		uniqid(),
	);

	$parts = apply_filters( 'psp_notify_email_parts', $parts, $post, $fields, $args, 'new_comment' );

	$fields['subject'] = $replacements['subject'];
	$fields['message'] = $replacements['message'];


	if( is_array($user_ids) ) {
		foreach( $user_ids as $user_id ) {
			$recipient = get_user_by( 'id', $user_id );
			$fields['recipient_email'] .= $recipient->user_email . ',';
		}
	} else {
		$recipient = get_user_by( 'id', $user_ids );
	}

	psp_send_email( $parts, $args, $notification_type, $fields );

}
