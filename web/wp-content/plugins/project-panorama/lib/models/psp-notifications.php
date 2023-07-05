<?php
function psp_create_notification_post_type( $notification_ID, $notification_args ) {

	$labels = array(
		'name'               => "$notification_args[name] Feeds",
		'singular_name'      => "$notification_args[name] Feed",
		'menu_name'          => "$notification_args[name] Feeds",
		'name_admin_bar'     => "$notification_args[name] Feed",
		'add_new'            => "Add New",
		'add_new_item'       => "Add New $notification_args[name] Feed",
		'new_item'           => "New $notification_args[name] Feed",
		'edit_item'          => "Edit $notification_args[name] Feed",
		'view_item'          => "View $notification_args[name] Feed",
		'all_items'          => "All $notification_args[name] Feeds",
		'search_items'       => "Search $notification_args[name] Feeds",
		'parent_item_colon'  => "Parent $notification_args[name] Feeds:",
		'not_found'          => "No $notification_args[name] Feeds found.",
		'not_found_in_trash' => "No $notification_args[name] Feeds found in Trash.",
	);

	$args = array(
		'labels'             => $labels,
		'public'             => false,
		'publicly_queryable' => true,
		'show_ui'            => false,
	);

	register_post_type( "psp-{$notification_ID}-feed", $args );
}

function psp_notification_feeds_section( $notification_ID, $notification_args ) {

	$feeds = get_posts( array(
		'post_type'   => "psp-{$notification_ID}-feed",
		'numberposts' => - 1,
		'order'       => 'ASC',
	) );

	$values = array();

	if ( ! empty( $feeds ) && ! is_wp_error( $feeds ) ) {
		foreach ( $feeds as $feed ) {

			$value = array(
				'post_id'      => $feed->ID,
				'notification' => get_post_meta( $feed->ID, "psp_{$notification_ID}_feed_notification", true ),
				'admin_title'  => get_the_title( $feed ),
			);

			foreach ( $notification_args['fields'] as $field_ID => $field ) {
				$value[ $field_ID ] = get_post_meta( $feed->ID, "psp_{$notification_ID}_feed_$field_ID", true );
			}

			$values[] = $value;
		}
	}

	$repeater_args = apply_filters( 'psp_notification_feeds_repeater_args', array(
		'id'                        => "psp_{$notification_ID}_notification_feeds",
		'input_name'                => "psp_{$notification_ID}_notification_feeds",
		'values'                    => $values,
		'add_item_text'             => __( 'Add Notification', 'psp_projects' ),
		'delete_item_text'          => __( 'Delete Notification', 'psp_projects' ),
		'sortable'                  => false,
		'collapsable'               => true,
		'collapsable_title'         => 'admin_title',
		'collapsable_title_default' => $notification_args['default_feed_title'],
		'fields'                    => array(
			'post_id'      => array(
				'type'  => 'hidden',
				'label' => false,
			),
			'notification' => array(
				'type'  => 'select',
				'label' => __( 'Notify when...', 'psp_projects' ),
				'args'  => array(
					'options' => array(
						'task_complete'    => __( '...a task is completed on the frontend.', 'psp_projects' ),
						'task_due'         => __( '...a task is due today.', 'psp_projects' ),
						'task_overdue'     => __( '...a task is overdue.', 'psp_projects' ),
						'task_assigned'    => __( '...a task is assigned to a user', 'psp_projects' ),
						'project_complete' => __( '...a project is completed.', 'psp_projects' ),
						'document_status'  => __( '...document status is changed on the frontend.', 'psp_projects' ),
						'new_comment'      => __( '...a new comment is left on a project.', 'psp_projects' ),
						'users_assigned'   => __( '...users are assigned to a project (all users)', 'psp_projects' ),
						'user_assigned'    => __( '...a user is assigned to a project', 'psp_projects' ),
						'project_progress' => __( '...progress is made on a project', 'psp_projects' ),
						'project_progress_point' => __( '...project progress reaches a certain point', 'psp_projects' )
					),
				),
			),
		),
	) );

	$repeater_args['fields'] = array_merge( (array) $repeater_args['fields'], (array) $notification_args['fields'] );

	$repeater_args['fields']['admin_title'] = array(
		'type'  => 'text',
		'label' => __( 'Admin Title', 'psp_projects' ),
	);

	do_action( "psp_before_{$notification_ID}_notification_feed_repeater", $repeater_args );

	if ( ! apply_filters( "psp_remove_{$notification_ID}_notification_feed_repeater", false ) ) : ?>

		<h3>
			<?php
			echo sprintf(
				__( '%s Notifications', 'psp_projects' ),
				$notification_args['name']
			);
			?>
		</h3>

		<div class="psp-notification-feeds">
			<?php psp_repeater_callback( $repeater_args ); ?>
		</div>

		<input type="hidden" name="psp_notification_deleted_feeds"/>

	<?php endif;

	do_action( "psp_after_{$notification_ID}_notification_feed_repeater", $repeater_args );

}

function psp_notification_update_feeds( $notification_ID, $notification_args ) {

	$feeds = $_POST["psp_{$notification_ID}_notification_feeds"];

	if ( empty( $feeds ) ) {
		return;
	}

	$notification_args = wp_parse_args( $notification_args, array(
		'default_feed_title' => __( 'New feed', 'psp_projects' ),
		'fields'             => array(),
	) );

	$notification_args['fields']['notification'] = true;

	foreach ( $feeds as $feed ) {

		$post_args = array(
			'ID'          => (int) $feed['post_id'] > 0 ? (int) $feed['post_id'] : 0,
			'post_type'   => "psp-{$notification_ID}-feed",
			'post_title'  => '',
			'post_status' => 'publish',
		);

		$notification_meta = array();
		foreach ( $notification_args['fields'] as $field_name => $field ) {
			if ( isset( $feed[ $field_name ] ) ) {
				$notification_meta["psp_{$notification_ID}_feed_$field_name"] = $feed[ $field_name ];
			}
		}

		if ( $feed['admin_title'] ) {
			$post_args['post_title'] = $feed['admin_title'];
		} else {
			$post_args['post_title'] = $notification_args['default_feed_title'];
		}

		$post_ID = wp_insert_post( $post_args );

		if ( $post_ID !== 0 && ! is_wp_error( $post_ID ) ) {

			/**
			 * Update the post meta as needed
			 * @var [type]
			 */
			foreach ( $notification_meta as $field_name => $field_value ) {
				update_post_meta( $post_ID, $field_name, $field_value );
			}

		}
	}
}

function psp_notification_delete_feeds() {

	$feeds = $_POST["psp_notification_deleted_feeds"];

	if ( empty( $feeds ) ) {
		return;
	}

	$feeds = explode( ',', $feeds );

	foreach ( $feeds as $feed_post_ID ) {
		wp_delete_post( $feed_post_ID, true );
	}

}

add_action( 'init', 'psp_setup_cron_notification' );
function psp_setup_cron_notification( $post_id ) {

	if ( get_option( 'psp_cron_running' ) ) {
		return;
	}

	wp_schedule_event( strtotime( '12:01' ), 'daily', 'psp_cron_notifications' );
	update_option( 'psp_cron_running', 1 );

}

add_action( 'psp_cron_notifications', 'psp_send_date_notifications' );
function psp_send_date_notifications() {

	$notifications = apply_filters( 'psp_date_notification_types', array(
		array(
			'type' => 'task_due',
			'date' => date( 'Ymd' )
		),
		array(
			'type' => 'task_overdue',
			'date' => date( 'Ymd', strtotime( 'yesterday' ) )
		)
	) );

	foreach ( $notifications as $notification ) {

		$args = array(
			'post_type'                   => 'psp-email-feed',
			'posts_per_page'              => - 1,
			'psp_email_feed_notification' => $notification['type']
		);

		$feeds = get_posts( $args );

		if ( count( $feeds ) > 1 ) {

			psp_find_tasks_by_due_date( $notification['date'], $notification['type'] );
		}

	}

}

add_action( 'admin_init', 'psp_test_notification_dates' );
function psp_test_notification_dates() {

	if ( ! isset( $_GET['psp_test_notifications'] ) ) {
		return;
	}

	$notifications = apply_filters( 'psp_date_notification_types', array(
		array(
			'type' => 'task_due',
			'date' => date( 'Ymd' )
		),
		array(
			'type' => 'task_overdue',
			'date' => date( 'Ymd', strtotime( 'yesterday' ) )
		)
	) );

	foreach ( $notifications as $notification ) {

		$args = array(
			'post_type'                   => 'psp-email-feed',
			'posts_per_page'              => - 1,
			'psp_email_feed_notification' => $notification['type']
		);

		$feeds = get_posts( $args );

		if ( count( $feeds ) > 1 ) {
			psp_find_tasks_by_due_date( $notification['date'], $notification['type'] );
		}

	}

}

function psp_notifications_replacements( $strings, $notification_type, $args, $replacements = array(), $notification_ID = null ) {

	$replacements = wp_parse_args( $replacements, array(
		'%task_title%'      => '',
		'%phase_title%'     => '',
		'%username%'        => '',
		'%recipient_name%'  => '',
		'%project_title%'   => '',
		'%comment_link%'    => '',
		'%comment_author%'  => '',
		'%comment_content%' => '',
		'%project_url%'     => '',
		'%dashboard%'       => '',
		'%date%'            => '',
		'%client%'          => '',
		'%due_date%'        => '',
		'%task_completion%' => '',
		'%tasks_assigned%'  => '',
		'%doc_status%' 		=> '',
		'%doc_message%'		=> '',
		'%doc_name%'		=> '',
		'%file_name%'    	=> '',
		'%file_url%'     	=> '',
		'%project_completion%' => '',
	) );

	// Discrepency between project and post id
	if ( isset( $args['post_id'] ) && ! isset( $args['project_id']) ) {
		$args['project_id'] = $args['post_id'];
	}

	/**
	 * Global Replacements
	 */

	$replacements['%project_url%'] = '<a href="' . get_the_permalink( $args['project_id'] ) . '">' . __( 'View Project', 'psp_projects' ) . '</a>';
	$replacements['%dashboard%']   = get_post_type_archive_link( 'psp_projects' );
	$replacements['%date%']        = date( get_option( 'date_format' ) );
	$replacements['%client%']      = get_field( 'client', $args['project_id'] );
	$replacements['%project_completion%'] = psp_compute_progress( $args['project_id'] );

	$replacements = apply_filters( 'psp_notifications_replacements_array', $replacements, $notification_type, $args );

	switch ( $notification_type ) {

		case 'document_status':

			$replacements['%doc_status%'] 	 = $args['status'];
			$replacements['%doc_name%']		 = $args['filename'];
			$replacements['%file_name%']	 = $args['filename'];
			$replacements['%project_title%'] = get_the_title( $args['project_id'] );
			$replacements['%username%']      = psp_get_nice_username_by_id( $args['user_id'] );
			$replacements['%doc_message%']   = $args['message'];
			$replacements['%file_url%']		 = $args['file_url'];
			break;

		case 'task_complete':

			$replacements['%task_title%']    = $args['task_title'];
			$replacements['%phase_title%']   = $args['phase_info']['title'];
			$replacements['%project_title%'] = get_the_title( $args['project_id'] );
			$replacements['%username%']      = psp_get_nice_username_by_id( $args['user_id'] );
			$replacements['%task_description%'] = $args['description'];

			break;

		case 'project_progress_point':
		case 'project_progress':

			$replacements['%project_title%'] = $args['project_title'];
			$replacements['%progress%']      = $args['new_progress'];
			break;

		case 'project_complete':

			$replacements['%project_title%'] = $args['project_title'];
			break;

		case 'new_comment':

			if( !empty($args['task_title']) ) {
				$comment_url = get_permalink($args['comment_post']->ID) . '#psp-offcanvas-task-details-' . get_comment_meta( $args['comment_ID'], 'task-key', true );
			} else {
				$comment_url = get_permalink( $args['comment_post']->ID ) . '#comment-' . $args['comment_ID'];
			}

			$replacements['%comment_author%']  = $args['commentdata']['comment_author'];
			$replacements['%comment_content%'] = stripslashes( $args['commentdata']['comment_content'] );
			$replacements['%comment_link%']    = '<a href="' . $comment_url . '">' . __( 'View comment here', 'psp_projects' ) . '</a>';
			$replacements['%project_title%']   = get_the_title( $args['comment_post']->ID );
			$replacements['%project_url%']     = '<a href="' . get_the_permalink( $args['comment_post']->ID ) . '">' . __( 'View Project', 'psp_projects' ) . '</a>';
			$replacements['%client%']          = get_field( 'client', $args['comment_post']->ID );
			$replacements['%phase_title%']     = $args['phase_title'];
			$replacements['%task_title%']	   = $args['task_title'];

			break;

		case 'users_assigned':

			$replacements['%project_title%'] = get_the_title( $args['project_id'] );

			$users = array();

			foreach ( $args['user_ids'] as $user_id ) {

				$users[] = psp_get_nice_username_by_id( $user_id );
			}

			$replacements['%usernames%'] = implode( ', ', $users );
			break;

		case 'user_assigned':

			$replacements['%project_title%'] = get_the_title( $args['project_id'] );
			$replacements['%username%']      = psp_get_nice_username_by_id( $args['user_id'] );

			break;

		case 'task_overdue':
		case 'task_due':

			$phases = get_field( 'phases', $args['project_id'] );
			$user   = get_user_by( 'id', $args['user_id'] );

			$replacements['%project_title%']   = $args['project_title'];
			$replacements['%project_url%']     = get_the_permalink( $args['project_id'] );
			$replacements['%username%']        = psp_get_nice_username_by_id( $args['assigned'] );
			$replacements['%task_title%']      = $args['name'];
			$replacements['%due_date%']        = date( get_option( 'date_format' ), strtotime( $args['due_date'] ) );
			$replacements['%phase_title%']     = $phases[ $args['phase_id'] ]['title'];
			$replacements['%task_completion%'] = $args['status'] . '%';
			$replacements['%target%']          = $user->user_email;
			$replacements['%client%']          = get_field( 'client', $args['project_id'] );
			$replacements['%task_description%'] = $args['description'];

		case 'task_assigned':

			$user = get_user_by( 'id', $args['user_id'] );

			$replacements['%project_title%'] = get_the_title( $args['project_id'] );
			$replacements['%project_url%']   = get_the_permalink( $args['project_id'] );
			$replacements['%username%']      = psp_get_nice_username_by_id( $args['user_id'] );
			$replacements['%client%']        = get_field( 'client', $args['project_id'] );
			$replacements['%task_description%'] = isset( $args['description'] ) ? $args['description'] : '';

			$tasks = '';

			if ( ! empty( $args['phases'] ) ) {
				foreach ( $args['phases'] as $phase ) {

					$tasks .= '<div style="padding-bottom: 30px;">';
					$tasks .= '<h2 style="font-size: 18px; font-weight: bold">' . $phase['phase_title'] . '</h2>';
					$tasks .= '<ul>';
					foreach ( $phase['tasks'] as $task ) {
						$tasks .= '<li><strong>' . $task['name'] . '</strong><br><span style="font-size: 12px;color:#666;">' . $task['status'] . __( '% Complete', 'psp_projects' );
						if ( ! empty( $task['due_date'] ) ) {
							$tasks .= ' <b style="padding-left: 5px; padding-right: 5px; font-weight: normal">|</b> ' . __( 'Due: ', 'psp_projects' ) . date( get_option( 'date_format' ), strtotime( $task['due_date'] ) );
						}
						if( !empty($task['description']) ) {
							$tasks .= wpautop($task['description']);
						}
						$tasks .= '</span></li>';
					}
					$tasks .= '</ul>';

				}
			}

			$replacements['%tasks_assigned%'] = $tasks;

			break;

	}

	/**
	 * Allows additional replacements to be made.
	 *
	 * @since {{VERSION}}
	 */
	$replacements = apply_filters( 'psp_notifications_replacements', $replacements, $notification_type, $args, $notification_ID );

	foreach ( $strings as $i => $string ) {
		$strings[ $i ] = str_replace( array_keys( $replacements ), array_values( $replacements ), $string );
	}

	return $strings;
}

function psp_notifications_do_notification( $notification_ID, $notification_args, $notification_type, $args ) {

	$notifications = get_posts( array(
		'post_type'   => "psp-{$notification_ID}-feed",
		'numberposts' => - 1,
		'meta_key'    => "psp_{$notification_ID}_feed_notification",
		'meta_value'  => $notification_type,
	) );

	if ( ! $notifications ) {
		return;
	}

	foreach ( $notifications as $notification_post ) {

		// Get notification args
		$notification_post_fields = array();
		foreach ( $notification_args['fields'] as $field_name => $field ) {
			$notification_post_fields[ $field_name ] = get_post_meta(
				$notification_post->ID,
				"psp_{$notification_ID}_feed_{$field_name}",
				true
			);
		}

		/**
		 * Fires when a notification happens. Other plugins can hook here to do stuff on their custom notifications.
		 *
		 * @since {{VERSION}}
		 * @hooked psp_do_notification_email 10
		 */
		do_action(
			"psp_do_notification_$notification_ID",
			$notification_post,
			$notification_post_fields,
			$args,
			$notification_ID,
			$notification_args,
			$notification_type
		);
	}

}
