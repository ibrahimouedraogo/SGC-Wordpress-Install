<?php
add_action( 'comment_post', 'psp_comment_notify', 11, 3 );

/**
 * Fires when a new comment is created and potentially pushes to Slack.
 *
 * @since {{VERSION}}
 * @access private
 *
 * @param $comment_ID string The comment ID.
 * @param $comment_approved int Whether or not the comment is approved.
 * @param $commentdata array Data about the comment.
 */
function psp_comment_notify( $comment_ID, $comment_approved, $commentdata ) {

	$comment_post = get_post( $commentdata['comment_post_ID'] );

	if ( get_post_type( $comment_post ) == 'psp_projects' ) {

		// Manual set triggers
		$phase_title = psp_get_phase_title_by_key( get_comment_meta( $comment_ID, 'phase-key', true ), $commentdata[ 'comment_post_ID' ] );
		$task_title  = psp_get_task_title_by_key( get_comment_meta( $comment_ID, 'task-key', true ), $commentdata['comment_post_ID'] );

		do_action( 'psp_notify', 'new_comment', array(
			'commentdata'      => $commentdata,
			'comment_ID'       => $comment_ID,
			'comment_approved' => $comment_approved,
			'comment_post'     => $comment_post,
			'post_id'		   => $commentdata[ 'comment_post_ID' ],
			'phase_title'	   => $phase_title,
			'task_title'	   => $task_title,
		) );

		// Automatic triggers

		$do_notify 	= false;
		$mentions 	= null;
		$notify_users  = ( isset( $_POST['psp_user'] ) ? $_POST['psp_user'] : array() );

		// Check for mentions
		preg_match_all('/data-notify="([^"]*)"/i', stripslashes($commentdata['comment_content']), $mentions );

		if( !empty($mentions) ) {
			foreach( $mentions[1] as $user_id ) {
				$notify_users[] = $user_id;
			}
		}

		$args = array(
			'commentdata'  => $commentdata,
			'comment_ID'	=> $comment_ID,
			'comment_post' => $comment_post,
			'type'		=> 'global',
			'post_id' 	=> $commentdata[ 'comment_post_ID' ],
			'project_id' 	=> $commentdata[ 'comment_post_ID' ]
		);

		if( $phase_title ) {
			$args['phase_title'] = $phase_title;
			$args['type'] = 'phase';
		}

		if( $task_title ) {

			$task_key = get_comment_meta( $comment_ID, 'task-key', true );
			$task 	= psp_get_task_by_key($task_key);
			$phase 	= psp_get_task_phase_by_id( $task_key, $commentdata[ 'comment_post_ID' ] );

			// Add the arguments
			$args['phase_title'] 	= $phase['phase']['title'];
			$args['task_title'] 	= $task_title;
			$args['type'] 			= 'task';

			// Check to see if anyone is assigned

			if( !empty($task['assigned']) ) {
				$notify_users = array_merge( $notify_users, $task['assigned'] );
			}

		}

		if( !empty($notify_users) ) {
			psp_comment_notify_users( $notify_users, $args );
		}

	}

}

/**
 * psp-discussions.php
 *
 * Controls custom comments and discussion modifications.
 *
 * @category controller
 * @package psp-projects
 * @author Ross Johnson & Kellen Mace
 * @since 1.3.6
 */

function psp_comment_template( $comment_template ) {

	global $post;

	if ( $post->post_type == 'psp_projects' ) {
		return PROJECT_PANORAMA_DIR . '/lib/templates/projects/discussion/discussion-loop.php';
	} else {
		return $comment_template;
	}

}
add_filter( 'comments_template', 'psp_comment_template', 999 );


function project_status_comment( $comment, $args, $depth ) {

	$GLOBALS['comment'] = $comment;

	if ( ! empty( $args ) ) {

		extract( $args, EXTR_SKIP );

		if ( 'div' == $args['style'] ) {

			$tag       = 'div';
			$add_below = 'comment';

		} else {

			$tag       = 'div';
			$add_below = 'div-comment';
		}

	}

	$private_comment = get_comment_meta( get_comment_ID(), '_psp_private_comment', true );

	// If the user can't read private comments and this is one, continue
	if( $private_comment && !current_user_can('read_psp_private_comments') ) {
		return;
	}

	$comment_body_class = ( $private_comment ? 'comment-body psp-private-comment' : 'psp-comment-body' );

	$post_id = get_the_ID();

	// Fix comment by post author coloring for Task Discussions
	if ( ! $post_id &&
		isset( $args['post_id'] ) ) {
		$post_id = $args['post_id'];
	}

	?>

	<div <?php comment_class( ( empty( $args['has_children'] ) ? 'psp-comment' : 'psp-comment psp-comment-parent' ), get_comment_ID(), $post_id ); ?> id="comment-<?php comment_ID() ?>">
		<div id="div-comment-<?php comment_ID() ?>" class="<?php echo esc_attr( $comment_body_class ); ?>">



			<?php if( $private_comment ): ?>
				<div class="psp-p psp-comment-notice"><?php esc_html_e( 'Private Message', 'psp_projects' ); ?></div>
			<?php endif; ?>

			<div class="psp-comment-text">

				<?php if ( $comment->comment_approved == '0' ) : ?>
					<div class="psp-p psp-comment-notice"><em class="comment-awaiting-moderation"><?php esc_html_e( 'Your comment is awaiting moderation.', 'psp_projects' ) ?></em></div>
				<?php endif; ?>

				<div class="psp-comment-text__content">
					<?php comment_text() ?>
				</div>

				<div class="psp-comment-reply">
					<a class="psp-comment-reply-link pano-btn" href="#" rel="nofollow" data-comment-parent="<?php comment_ID(); ?>"><?php esc_html_e( 'Reply', 'psp_projects' ); ?></a>
				</div>


			</div>

			<div class="psp-comment-author vcard">
				<?php echo get_avatar( $comment, 50 ); ?>
				<div class="psp-comment-meta">
					<?php printf( __( '<div class="psp-author__name">%s</div>' ), get_comment_author_link() ); ?>
					<div class="psp-comment-date">
						<?php
						/* translators: 1: date, 2: time */
						printf( __( '%1$s at %2$s' ), get_comment_date(), get_comment_time() );

						if ( current_user_can( 'edit_comment', $comment->comment_ID ) ): ?>
							<span class="psp-comment-meta__utils">
								 <a href="#" class="psp-js-edit-comment" data-comment_id="<?php echo esc_attr($comment->comment_ID); ?>"><i class="fa fa-pencil"></i> <?php esc_html_e( 'Edit', 'psp_projects' ); ?></a> <span class="psp-pipe">|</span> <a href="#" class="psp-js-delete-comment" data-comment_id="<?php echo esc_attr($comment->comment_ID); ?>"><i class="fa fa-trash"></i> <?php esc_html_e( 'Delete', 'psp_projects' ); ?></a>
							</span>
						<?php endif; ?>

					</div>
				</div> <!--/.psp-comment-meta-->
			</div> <!--/.psp-comment-author-->

		</div>
	</div>


<?php }

/*
 * Save unique comment key with each phase
 */
function psp_save_phase_comment_keys() {

	if ( isset($_POST['post_type']) && 'psp_projects' != $_POST['post_type'] ) {
		return;
	}

	if( isset($_POST['_acf_post_id']) && get_post_type($_POST['_acf_post_id']) != 'psp_projects' ) {
		return;
	}

	$post_index = ( isset($_POST['fields']) ? 'fields' : 'acf' );

	$phases = $_POST[$post_index]['field_527d5dc12fa29'];

	if ( is_array( $phases ) ) {
		foreach ( $phases as $key => $fields ) {

			if ( 'acfcloneindex' === $key ) {
				continue;
			}

			if ( empty( $fields['psp_phase_id'] ) ) {
				$_POST[$post_index]['field_527d5dc12fa29'][ $key ]['psp_phase_id'] = psp_generate_phase_id();
			}
		}
	}
}
add_action( 'acf/save_post', 'psp_save_phase_comment_keys', 9 );

/*
 * Save comment phase key meta when comment submitted
 */
function psp_save_comment_phase_key_meta( $comment_id ) {

	if ( ! empty( $_POST['phase-key'] ) ) {
		$phase_key = wp_filter_nohtml_kses( $_POST['phase-key'] );
		add_comment_meta( $comment_id, 'phase-key', $phase_key );
	}
}
add_action( 'comment_post', 'psp_save_comment_phase_key_meta' );

function psp_save_comment_task_key_meta( $comment_id ) {

	if ( ! empty( $_POST['task-key'] ) ) {
		$task_key = wp_filter_nohtml_kses( $_POST['task-key'] );
		add_comment_meta( $comment_id, 'task-key', $task_key );
	}
}
add_action( 'comment_post', 'psp_save_comment_task_key_meta' );


/*
 * Get the comments for a phase
 */
function psp_get_phase_comments( $phase_comment_key, $post_id = null ) {

	$post_id = ( $post_id == null ? get_the_ID() : $post_id );

	return get_comments( array(
		'meta_key'   => 'phase-key',
		'meta_value' => sanitize_text_field( $phase_comment_key ),
		'order'      => 'ASC',
		'post_id'	 => $post_id
	) );
}

/**
 * Get Comments for a Task
 * The Meta Key format of "-key" was kept to make it the same as Phase Comments
 *
 * @param		string  $task_comment_key Task ID
 * @param		integer $post_id          Project Post ID
 *
 * @since		{{VERSION}}
 * @return		array   Array of Comment Objects
 */
function psp_get_task_comments( $task_comment_key, $post_id = null ) {

	$post_id = ( $post_id == null ? get_the_ID() : $post_id );

	return get_comments( array(
		'meta_key'   => 'task-key',
		'meta_value' => sanitize_text_field( $task_comment_key ),
		'order'      => 'ASC',
		'post_id'	 => intval($post_id),
	) );

}

function psp_get_general_comments( $post_id = null ) {

	$post_id = ( $post_id == null ? get_the_ID() : $post_id );

	return get_comments( array(
		'post_id'    => $post_id,
		'meta_query' => array(
			'relation' => 'AND',
			array(
				'key'     => 'phase-key',
				'compare' => 'NOT EXISTS'
			),
			array(
				'key'     => 'task-key',
				'compare' => 'NOT EXISTS'
			)
		)
	) );

}

function psp_get_general_comment_count( $post_id = null ) {

	$post_id = ( $post_id == null ? get_the_ID() : $post_id );

	return count( psp_get_general_comments( $post_id ) );

}

function psp_get_phase_comment_count( $phase_comment_key, $post_id = null ) {

	$post_id = ( $post_id == null ? get_the_ID() : $post_id );

	return count( psp_get_phase_comments( $phase_comment_key, $post_id ) );

}

/**
 * Get Count of Comments on a Task
 *
 * @param		string  $task_comment_key Task ID
 * @param		integer $post_id          Project Post ID
 *
 * @since		{{VERSION}}
 * @return		integer Number of Task Comments
 */
function psp_get_task_comment_count( $task_comment_key, $post_id = null ) {

	$post_id = ( $post_id == null ? get_the_ID() : $post_id );

	return count( psp_get_task_comments( $task_comment_key, $post_id ) );

}

/**
 * Output a complete commenting form for use within each phase
 *
 * Based on the WordPress comment_form() function
 *
 * @param string $phase Which phase of the project.
 * @param array $args Optional. Default arguments and form fields to override.
 * @param int|WP_Post $post_id Post ID or WP_Post object to generate the form for. Default current post.
 */
function psp_phase_comment_form( $phase = '', $args = array(), $post_id = null ) {

	$post_id = ( $post_id == null ? get_the_ID() : $post_id );

	$commenter     = wp_get_current_commenter();
	$user          = wp_get_current_user();
	$user_identity = $user->exists() ? $user->display_name : '';

	$args = wp_parse_args( $args );
	if ( ! isset( $args['format'] ) ) {
		$args['format'] = current_theme_supports( 'html5', 'comment-form' ) ? 'html5' : 'xhtml';
	}

	$req      = get_option( 'require_name_email' );
	$aria_req = ( $req ? " aria-required='true'" : '' );
	$html_req = ( $req ? " required='required'" : '' );
	$html5    = 'html5' === $args['format'];
	$fields   = array(
		'author' => '<p class="comment-form-author">' . '<label for="author">' . __( 'Name' ) . ( $req ? ' <span class="required">*</span>' : '' ) . '</label> ' .
		            '<input id="author" name="author" type="text" value="' . esc_attr( $commenter['comment_author'] ) . '" size="30"' . $aria_req . $html_req . ' /></p>',
		'email'  => '<p class="comment-form-email"><label for="email">' . __( 'Email' ) . ( $req ? ' <span class="required">*</span>' : '' ) . '</label> ' .
		            '<input id="email" name="email" ' . ( $html5 ? 'type="email"' : 'type="text"' ) . ' value="' . esc_attr( $commenter['comment_author_email'] ) . '" size="30" aria-describedby="email-notes"' . $aria_req . $html_req . ' /></p>',
		'url'    => '<p class="comment-form-url"><label for="url">' . __( 'Website' ) . '</label> ' .
		            '<input id="url" name="url" ' . ( $html5 ? 'type="url"' : 'type="text"' ) . ' value="' . esc_attr( $commenter['comment_author_url'] ) . '" size="30" /></p>',
	);

	$required_text = sprintf( ' ' . __( 'Required fields are marked %s' ), '<span class="required">*</span>' );

	$cid = 'comment-' . uniqid();

	/**
	 * Filter the default comment form fields.
	 *
	 * @since 3.0.0
	 *
	 * @param array $fields The default comment fields.
	 */
	$fields   = apply_filters( 'comment_form_default_fields', $fields );
	$defaults = array(
		'fields'               => $fields,
		'comment_field'        => '<div class="psp-comment-form-comment"><textarea id="' . $cid . '" name="comment" class="psp-phase-comment-field" cols="45" rows="8"  aria-required="true"></textarea></div>',
		/** This filter is documented in wp-includes/link-template.php */
		'must_log_in'          => '<div class="psp-notice psp-p must-log-in">' . sprintf( __( 'You must be <a href="%s">logged in</a> to post a comment.' ), wp_login_url( apply_filters( 'the_permalink', get_permalink( $post_id ) ) ) ) . '</div>',
		/** This filter is documented in wp-includes/link-template.php */
		'logged_in_as'         => '',
		'comment_notes_before' => '<div class="comment-notes"><span id="email-notes">' . __( 'Your email address will not be published.' ) . '</span>' . ( $req ? $required_text : '' ) . '</div>',
		'comment_notes_after'  => '',
		'id_form'              => 'psp-phase-comment-form-' . uniqid(),
		'class_form'           => 'psp-comment-form',
		'id_submit'            => 'submit-' . uniqid(),
		'class_submit'         => 'submit',
		'name_submit'          => 'submit',
		'title_reply'          => __( 'Leave a Reply' ),
		'title_reply_to'       => __( 'Leave a Reply to %s' ),
		'cancel_reply_link'    => __( 'Cancel reply' ),
		'label_submit'         => __( 'Reply', 'psp_projects' ),
		'submit_button'        => '<input name="%1$s" type="submit" id="%2$s" class="%3$s pano-btn" value="%4$s" />',
		'submit_field'         => '<div class="psp-form-submit">%1$s %2$s %3$s</div>',
		'format'               => 'xhtml',
	);

	/**
	 * Filter the comment form default arguments.
	 *
	 * Use 'comment_form_default_fields' to filter the comment fields.
	 *
	 * @since 3.0.0
	 *
	 * @param array $defaults The default comment form arguments.
	 */
	$args = wp_parse_args( $args, apply_filters( 'comment_form_defaults', $defaults ) );

	// Ensure that the filtered args contain all required default values.
	$args = array_merge( $defaults, $args );

	if ( comments_open( $post_id ) ) : ?>
		<?php
		/**
		 * Fires before the comment form.
		 *
		 * @since 3.0.0
		 */
		do_action( 'comment_form_before' );
		?>
		<div class="psp-discussion-form">
			<div class="psp-h3 psp-reply-title">
				<?php comment_form_title( $args['title_reply'], $args['title_reply_to'] ); ?>
				<small><?php cancel_comment_reply_link( $args['cancel_reply_link'] ); ?></small>
			</div>
			<?php if ( get_option( 'comment_registration' ) && ! is_user_logged_in() ) : ?>
				<?php echo $args['must_log_in']; ?>
				<?php
				/**
				 * Fires after the HTML-formatted 'must log in after' message in the comment form.
				 *
				 * @since 3.0.0
				 */
				do_action( 'comment_form_must_log_in_after' );
				?>
			<?php else : ?>
				<form action="<?php echo site_url( '/wp-comments-post.php' ); ?>" method="post"
				      id="<?php echo esc_attr( $args['id_form'] ); ?>"
				      class="comment-form psp-comment-form"<?php echo $html5 ? ' novalidate' : ''; ?>>
					<?php
					/**
					 * Fires at the top of the comment form, inside the form tag.
					 *
					 * @since 3.0.0
					 */
					do_action( 'comment_form_top' );
					?>
					<?php if ( is_user_logged_in() ) : ?>
						<?php
						/**
						 * Filter the 'logged in' message for the comment form for display.
						 *
						 * @since 3.0.0
						 *
						 * @param string $args_logged_in The logged-in-as HTML-formatted message.
						 * @param array $commenter An array containing the comment author's
						 *                               username, email, and URL.
						 * @param string $user_identity If the commenter is a registered user,
						 *                               the display name, blank otherwise.
						 */
						echo apply_filters( 'comment_form_logged_in', $args['logged_in_as'], $commenter, $user_identity );
						?>
						<?php
						/**
						 * Fires after the is_user_logged_in() check in the comment form.
						 *
						 * @since 3.0.0
						 *
						 * @param array $commenter An array containing the comment author's
						 *                              username, email, and URL.
						 * @param string $user_identity If the commenter is a registered user,
						 *                              the display name, blank otherwise.
						 */
						do_action( 'comment_form_logged_in_after', $commenter, $user_identity );
						?>
					<?php else : ?>
						<?php echo $args['comment_notes_before']; ?>
						<?php
						/**
						 * Fires before the comment fields in the comment form.
						 *
						 * @since 3.0.0
						 */
						do_action( 'comment_form_before_fields' );
						foreach ( (array) $args['fields'] as $name => $field ) {
							/**
							 * Filter a comment form field for display.
							 *
							 * The dynamic portion of the filter hook, `$name`, refers to the name
							 * of the comment form field. Such as 'author', 'email', or 'url'.
							 *
							 * @since 3.0.0
							 *
							 * @param string $field The HTML-formatted output of the comment form field.
							 */
							echo apply_filters( "comment_form_field_{$name}", $field ) . "\n";
						}
						/**
						 * Fires after the comment fields in the comment form.
						 *
						 * @since 3.0.0
						 */
						do_action( 'comment_form_after_fields' );
						?>
					<?php endif; ?>
					<?php
					/**
					 * Filter the content of the comment textarea field for display.
					 *
					 * @since 3.0.0
					 *
					 * @param string $args_comment_field The content of the comment textarea field.
					 */
					echo apply_filters( 'comment_form_field_comment', $args['comment_field'] );
					?>
					<?php echo $args['comment_notes_after']; ?>

					<?php
					$members = psp_get_project_users( $post_id );
					psp_comment_notify_fields( $members, $phase ); ?>

					<?php
					$submit_button = sprintf(
						$args['submit_button'],
						esc_attr( $args['name_submit'] ),
						esc_attr( $args['id_submit'] ),
						esc_attr( $args['class_submit'] ),
						esc_attr( $args['label_submit'] )
					);

					/**
					 * Filter the submit button for the comment form to display.
					 *
					 * @since 4.2.0
					 *
					 * @param string $submit_button HTML markup for the submit button.
					 * @param array $args Arguments passed to `comment_form()`.
					 */
					$submit_button = apply_filters( 'comment_form_submit_button', $submit_button, $args );

					$submit_field = sprintf(
						$args['submit_field'],
						$submit_button,
						get_comment_id_fields( $post_id ),
						'<input class="phase-key" type="hidden" name="phase-key" value="' . esc_attr( $phase ) . '">'
					);

					/**
					 * Filter the submit field for the comment form to display.
					 *
					 * The submit field includes the submit button, hidden fields for the
					 * comment form, and any wrapper markup.
					 *
					 * @since 4.2.0
					 *
					 * @param string $submit_field HTML markup for the submit field.
					 * @param array $args Arguments passed to comment_form().
					 */
					echo apply_filters( 'comment_form_submit_field', $submit_field, $args );

					/**
					 * Fires at the bottom of the comment form, inside the closing </form> tag.
					 *
					 * @since 1.5.0
					 *
					 * @param int $post_id The post ID.
					 */
					do_action( 'comment_form', $post_id );
					?>
				</form>
			<?php endif; ?>
		</div><!-- #respond -->
		<?php
		/**
		 * Fires after the comment form.
		 *
		 * @since 3.0.0
		 */
		do_action( 'comment_form_after' );
	else :
		/**
		 * Fires after the comment form if comments are closed.
		 *
		 * @since 3.0.0
		 */
		do_action( 'comment_form_comments_closed' );
	endif;
}

/**
 * Output a complete commenting form for use within each task
 *
 * Based on the WordPress comment_form() function
 *
 * At first I thought of putting a little conditional in the psp_phase_comment_form() function to support Tasks because all that gets changed is the hidden field's name attribute to make this work, but then I decided against it to make it easily possible to change the two of them independently of each other.
 *
 * @param		string      $task    Which Task of the project.
 * @param		array       $args    Optional. Default arguments and form fields to override.
 * @param 		int|WP_Post $post_id Post ID or WP_Post object to generate the form for. Default current post.
 *
 * @since		{{VERSION}}
 * @return		void
 */
function psp_task_comment_form( $task = '', $args = array(), $post_id = null ) {

	$post_id = ( $post_id == null ? get_the_ID() : $post_id );

	$commenter     = wp_get_current_commenter();
	$user          = wp_get_current_user();
	$user_identity = $user->exists() ? $user->display_name : '';

	$args = wp_parse_args( $args );
	if ( ! isset( $args['format'] ) ) {
		$args['format'] = current_theme_supports( 'html5', 'comment-form' ) ? 'html5' : 'xhtml';
	}

	$req      = get_option( 'require_name_email' );
	$aria_req = ( $req ? " aria-required='true'" : '' );
	$html_req = ( $req ? " required='required'" : '' );
	$html5    = 'html5' === $args['format'];
	$fields   = array(
		'author' => '<p class="comment-form-author">' . '<label for="author">' . __( 'Name' ) . ( $req ? ' <span class="required">*</span>' : '' ) . '</label> ' .
		            '<input id="author" name="author" type="text" value="' . esc_attr( $commenter['comment_author'] ) . '" size="30"' . $aria_req . $html_req . ' /></p>',
		'email'  => '<p class="comment-form-email"><label for="email">' . __( 'Email' ) . ( $req ? ' <span class="required">*</span>' : '' ) . '</label> ' .
		            '<input id="email" name="email" ' . ( $html5 ? 'type="email"' : 'type="text"' ) . ' value="' . esc_attr( $commenter['comment_author_email'] ) . '" size="30" aria-describedby="email-notes"' . $aria_req . $html_req . ' /></p>',
		'url'    => '<p class="comment-form-url"><label for="url">' . __( 'Website' ) . '</label> ' .
		            '<input id="url" name="url" ' . ( $html5 ? 'type="url"' : 'type="text"' ) . ' value="' . esc_attr( $commenter['comment_author_url'] ) . '" size="30" /></p>',
	);

	$required_text = sprintf( ' ' . __( 'Required fields are marked %s' ), '<span class="required">*</span>' );

	/**
	 * Filter the default comment form fields.
	 *
	 * @since 3.0.0
	 *
	 * @param array $fields The default comment fields.
	 */
	$fields   = apply_filters( 'comment_form_default_fields', $fields );
	$defaults = array(
		'fields'               => $fields,
		'comment_field'        => '<div class="psp-task-comment"><label for="comment" class="psp-h3">' . _x( 'Leave a Reply', 'noun' ) . '</label> <textarea id="comment" name="comment" cols="45" rows="8"  aria-required="true"></textarea></div>',
		/** This filter is documented in wp-includes/link-template.php */
		'must_log_in'          => '<p class="must-log-in">' . sprintf( __( 'You must be <a href="%s">logged in</a> to post a comment.' ), wp_login_url( apply_filters( 'the_permalink', get_permalink( $post_id ) ) ) ) . '</p>',
		/** This filter is documented in wp-includes/link-template.php */
		'logged_in_as'         => '',
		'comment_notes_before' => '<p class="comment-notes"><span id="email-notes">' . __( 'Your email address will not be published.' ) . '</span>' . ( $req ? $required_text : '' ) . '</p>',
		'comment_notes_after'  => '',
		'id_form'              => '',
		'class_form'           => 'psp-comment-form',
		'id_submit'            => 'submit',
		'class_submit'         => 'submit',
		'name_submit'          => 'submit',
		'title_reply'          => __( 'Leave a Reply' ),
		'title_reply_to'       => __( 'Leave a Reply to %s' ),
		'cancel_reply_link'    => __( 'Cancel reply' ),
		'label_submit'         => __( 'Reply' ),
		'submit_button'        => '<input name="%1$s" type="submit" id="%2$s" class="%3$s" value="%4$s" />',
		'submit_field'         => '<p class="form-submit">%1$s %2$s %3$s</p>',
		'format'               => 'xhtml',
	);

	/**
	 * Filter the comment form default arguments.
	 *
	 * Use 'comment_form_default_fields' to filter the comment fields.
	 *
	 * @since 3.0.0
	 *
	 * @param array $defaults The default comment form arguments.
	 */
	$args = wp_parse_args( $args, apply_filters( 'comment_form_defaults', $defaults ) );

	// Ensure that the filtered args contain all required default values.
	$args = array_merge( $defaults, $args );

	if ( comments_open( $post_id ) ) : ?>
		<?php
		/**
		 * Fires before the comment form.
		 *
		 * @since 3.0.0
		 */
		do_action( 'comment_form_before' );
		?>
		<div id="respond" class="psp-task-respond">

			<?php if ( get_option( 'comment_registration' ) && ! is_user_logged_in() ) : ?>
				<?php echo $args['must_log_in']; ?>
				<?php
				/**
				 * Fires after the HTML-formatted 'must log in after' message in the comment form.
				 *
				 * @since 3.0.0
				 */
				do_action( 'comment_form_must_log_in_after' );
				?>
			<?php else : ?>
				<form action="<?php echo site_url( '/wp-comments-post.php' ); ?>" method="post"
				      id="<?php echo esc_attr( $args['id_form'] ); ?>"
				      class="comment-form psp-comment-form"<?php echo $html5 ? ' novalidate' : ''; ?>>
					<?php
					/**
					 * Fires at the top of the comment form, inside the form tag.
					 *
					 * @since 3.0.0
					 */
					do_action( 'comment_form_top' );
					?>
					<?php if ( is_user_logged_in() ) : ?>
						<?php
						/**
						 * Filter the 'logged in' message for the comment form for display.
						 *
						 * @since 3.0.0
						 *
						 * @param string $args_logged_in The logged-in-as HTML-formatted message.
						 * @param array $commenter An array containing the comment author's
						 *                               username, email, and URL.
						 * @param string $user_identity If the commenter is a registered user,
						 *                               the display name, blank otherwise.
						 */
						echo apply_filters( 'comment_form_logged_in', $args['logged_in_as'], $commenter, $user_identity );
						?>
						<?php
						/**
						 * Fires after the is_user_logged_in() check in the comment form.
						 *
						 * @since 3.0.0
						 *
						 * @param array $commenter An array containing the comment author's
						 *                              username, email, and URL.
						 * @param string $user_identity If the commenter is a registered user,
						 *                              the display name, blank otherwise.
						 */
						do_action( 'comment_form_logged_in_after', $commenter, $user_identity );
						?>
					<?php else : ?>
						<?php echo $args['comment_notes_before']; ?>
						<?php
						/**
						 * Fires before the comment fields in the comment form.
						 *
						 * @since 3.0.0
						 */
						do_action( 'comment_form_before_fields' );
						foreach ( (array) $args['fields'] as $name => $field ) {
							/**
							 * Filter a comment form field for display.
							 *
							 * The dynamic portion of the filter hook, `$name`, refers to the name
							 * of the comment form field. Such as 'author', 'email', or 'url'.
							 *
							 * @since 3.0.0
							 *
							 * @param string $field The HTML-formatted output of the comment form field.
							 */
							echo apply_filters( "comment_form_field_{$name}", $field ) . "\n";
						}
						/**
						 * Fires after the comment fields in the comment form.
						 *
						 * @since 3.0.0
						 */
						do_action( 'comment_form_after_fields' );
						?>
					<?php endif; ?>
					<?php
					/**
					 * Filter the content of the comment textarea field for display.
					 *
					 * @since 3.0.0
					 *
					 * @param string $args_comment_field The content of the comment textarea field.
					 */
					echo apply_filters( 'comment_form_field_comment', $args['comment_field'] );
					?>
					<?php echo $args['comment_notes_after']; ?>

					<?php
					$members   = psp_get_project_users( $post_id );
					$task_data = psp_get_task_by_key( $task, $post_id );

					psp_comment_notify_fields( $members, $task, $task_data['assigned'] ); ?>

					<?php
					$submit_button = sprintf(
						$args['submit_button'],
						esc_attr( $args['name_submit'] ),
						esc_attr( $args['id_submit'] ),
						esc_attr( $args['class_submit'] ),
						esc_attr( $args['label_submit'] )
					);

					/**
					 * Filter the submit button for the comment form to display.
					 *
					 * @since 4.2.0
					 *
					 * @param string $submit_button HTML markup for the submit button.
					 * @param array $args Arguments passed to `comment_form()`.
					 */
					$submit_button = apply_filters( 'comment_form_submit_button', $submit_button, $args );

					$submit_field = sprintf(
						$args['submit_field'],
						$submit_button,
						get_comment_id_fields( $post_id ),
						'<input id="task-key" type="hidden" name="task-key" value="' . esc_attr( $task ) . '">'
					);

					/**
					 * Filter the submit field for the comment form to display.
					 *
					 * The submit field includes the submit button, hidden fields for the
					 * comment form, and any wrapper markup.
					 *
					 * @since 4.2.0
					 *
					 * @param string $submit_field HTML markup for the submit field.
					 * @param array $args Arguments passed to comment_form().
					 */
					echo apply_filters( 'comment_form_submit_field', $submit_field, $args );

					/**
					 * Fires at the bottom of the comment form, inside the closing </form> tag.
					 *
					 * @since 1.5.0
					 *
					 * @param int $post_id The post ID.
					 */
					do_action( 'comment_form', $post_id );
					?>
				</form>
			<?php endif; ?>
		</div><!-- #respond -->
		<?php
		/**
		 * Fires after the comment form.
		 *
		 * @since 3.0.0
		 */
		do_action( 'comment_form_after' );
	else :
		/**
		 * Fires after the comment form if comments are closed.
		 *
		 * @since 3.0.0
		 */
		do_action( 'comment_form_comments_closed' );
	endif;
}



/**
 * Output a complete commenting form for use within each task
 *
 * Based on the WordPress comment_form() function
 *
 * At first I thought of putting a little conditional in the psp_phase_comment_form() function to support Tasks because all that gets changed is the hidden field's name attribute to make this work, but then I decided against it to make it easily possible to change the two of them independently of each other.
 *
 * @param		string      $task    Which Task of the project.
 * @param		array       $args    Optional. Default arguments and form fields to override.
 * @param 		int|WP_Post $post_id Post ID or WP_Post object to generate the form for. Default current post.
 *
 * @since		{{VERSION}}
 * @return		void
 */
function psp_project_comment_form( $task = '', $args = array(), $post_id = null ) {

	$post_id = ( $post_id == null ? get_the_ID() : $post_id );

	$commenter     = wp_get_current_commenter();
	$user          = wp_get_current_user();
	$user_identity = $user->exists() ? $user->display_name : '';

	$args = wp_parse_args( $args );
	if ( ! isset( $args['format'] ) ) {
		$args['format'] = current_theme_supports( 'html5', 'comment-form' ) ? 'html5' : 'xhtml';
	}

	$req      = get_option( 'require_name_email' );
	$aria_req = ( $req ? " aria-required='true'" : '' );
	$html_req = ( $req ? " required='required'" : '' );
	$html5    = 'html5' === $args['format'];
	$fields   = array(
		'author' => '<p class="comment-form-author">' . '<label for="author">' . __( 'Name' ) . ( $req ? ' <span class="required">*</span>' : '' ) . '</label> ' .
		            '<input id="author" name="author" type="text" value="' . esc_attr( $commenter['comment_author'] ) . '" size="30"' . $aria_req . $html_req . ' /></p>',
		'email'  => '<p class="comment-form-email"><label for="email">' . __( 'Email' ) . ( $req ? ' <span class="required">*</span>' : '' ) . '</label> ' .
		            '<input id="email" name="email" ' . ( $html5 ? 'type="email"' : 'type="text"' ) . ' value="' . esc_attr( $commenter['comment_author_email'] ) . '" size="30" aria-describedby="email-notes"' . $aria_req . $html_req . ' /></p>',
		'url'    => '<p class="comment-form-url"><label for="url">' . __( 'Website' ) . '</label> ' .
		            '<input id="url" name="url" ' . ( $html5 ? 'type="url"' : 'type="text"' ) . ' value="' . esc_attr( $commenter['comment_author_url'] ) . '" size="30" /></p>',
	);

	$required_text = sprintf( ' ' . __( 'Required fields are marked %s' ), '<span class="required">*</span>' );

	$cid = 'comment-' . uniqid();


	/**
	 * Filter the default comment form fields.
	 *
	 * @since 3.0.0
	 *
	 * @param array $fields The default comment fields.
	 */
	$fields   = apply_filters( 'comment_form_default_fields', $fields );

	$defaults = array(
		'fields'               => $fields,
		'comment_field'        => '<div class="psp-project-comment"><label for="' . $cid . '" class="psp-h3">' . _x( 'Post a Message', 'psp_projects' ) . '</label> <textarea id="' . $cid . '" name="comment" class="psp-project-comment-field" cols="45" rows="8"></textarea></div>',
		/** This filter is documented in wp-includes/link-template.php */
		'must_log_in'          => '<p class="must-log-in">' . sprintf( __( 'You must be <a href="%s">logged in</a> to post a comment.' ), wp_login_url( apply_filters( 'the_permalink', get_permalink( $post_id ) ) ) ) . '</p>',
		/** This filter is documented in wp-includes/link-template.php */
		'logged_in_as'         => '',
		'comment_notes_before' => '<p class="comment-notes"><span id="email-notes">' . __( 'Your email address will not be published.' ) . '</span>' . ( $req ? $required_text : '' ) . '</p>',
		'comment_notes_after'  => '',
		'id_form'              => 'psp-comment-form-' . uniqid(),
		'class_form'           => 'psp-project-comment-form',
		'id_submit'            => 'submit-' . uniqid(),
		'class_submit'         => 'submit',
		'name_submit'          => 'submit',
		'title_reply'          => __( 'Post a Message', 'psp_projects' ),
		'title_reply_to'       => __( 'Leave a Reply to %s' ),
		'cancel_reply_link'    => __( 'Cancel reply', 'psp_projects' ),
		'label_submit'         => __( 'Reply', 'psp_projects' ),
		'submit_button'        => '<input name="%1$s" type="submit" id="%2$s" class="%3$s" value="%4$s" />',
		'submit_field'         => '<p class="form-submit">%1$s %2$s %3$s</p>',
		'format'               => 'xhtml',
	);

	/**
	 * Filter the comment form default arguments.
	 *
	 * Use 'comment_form_default_fields' to filter the comment fields.
	 *
	 * @since 3.0.0
	 *
	 * @param array $defaults The default comment form arguments.
	 */
	$args = wp_parse_args( $args, apply_filters( 'comment_form_defaults', $defaults ) );

	// Ensure that the filtered args contain all required default values.
	$args = array_merge( $defaults, $args );

	if ( comments_open( $post_id ) ) : ?>
		<?php
		/**
		 * Fires before the comment form.
		 *
		 * @since 3.0.0
		 */
		do_action( 'comment_form_before' );
		?>
		<div id="psp-project-respond" class="psp-project-respond">

			<?php if ( get_option( 'comment_registration' ) && ! is_user_logged_in() ) : ?>
				<?php echo $args['must_log_in']; ?>
				<?php
				/**
				 * Fires after the HTML-formatted 'must log in after' message in the comment form.
				 *
				 * @since 3.0.0
				 */
				do_action( 'comment_form_must_log_in_after' );
				?>
			<?php else : ?>
				<form action="<?php echo site_url( '/wp-comments-post.php' ); ?>" method="post"
				      id="<?php echo esc_attr( $args['id_form'] ); ?>"
				      class="comment-form psp-project-comment-form"<?php echo $html5 ? ' novalidate' : ''; ?>>
					<?php
					/**
					 * Fires at the top of the comment form, inside the form tag.
					 *
					 * @since 3.0.0
					 */
					do_action( 'comment_form_top' );
					?>
					<?php if ( is_user_logged_in() ) : ?>
						<?php
						/**
						 * Filter the 'logged in' message for the comment form for display.
						 *
						 * @since 3.0.0
						 *
						 * @param string $args_logged_in The logged-in-as HTML-formatted message.
						 * @param array $commenter An array containing the comment author's
						 *                               username, email, and URL.
						 * @param string $user_identity If the commenter is a registered user,
						 *                               the display name, blank otherwise.
						 */
						echo apply_filters( 'comment_form_logged_in', $args['logged_in_as'], $commenter, $user_identity );
						?>



						<?php
						/**
						 * Fires after the is_user_logged_in() check in the comment form.
						 *
						 * @since 3.0.0
						 *
						 * @param array $commenter An array containing the comment author's
						 *                              username, email, and URL.
						 * @param string $user_identity If the commenter is a registered user,
						 *                              the display name, blank otherwise.
						 */
						do_action( 'comment_form_logged_in_after', $commenter, $user_identity );
						?>
					<?php else : ?>
						<?php echo $args['comment_notes_before']; ?>
						<?php
						/**
						 * Fires before the comment fields in the comment form.
						 *
						 * @since 3.0.0
						 */
						do_action( 'comment_form_before_fields' );

						foreach ( (array) $args['fields'] as $name => $field ) {
							/**
							 * Filter a comment form field for display.
							 *
							 * The dynamic portion of the filter hook, `$name`, refers to the name
							 * of the comment form field. Such as 'author', 'email', or 'url'.
							 *
							 * @since 3.0.0
							 *
							 * @param string $field The HTML-formatted output of the comment form field.
							 */
							echo apply_filters( "comment_form_field_{$name}", $field ) . "\n";
						}
						/**
						 * Fires after the comment fields in the comment form.
						 *
						 * @since 3.0.0
						 */
						do_action( 'comment_form_after_fields' );
						?>
					<?php endif; ?>
					<?php
					/**
					 * Filter the content of the comment textarea field for display.
					 *
					 * @since 3.0.0
					 *
					 * @param string $args_comment_field The content of the comment textarea field.
					 */
					echo apply_filters( 'comment_form_field_comment', $args['comment_field'] );

					/**
					  * Populate the fields with project users if they exists
					  */
					$members = psp_get_project_users($post_id);

					psp_comment_notify_fields( $members, $post_id ); ?>

					<?php echo $args['comment_notes_after']; ?>

					<?php
					$submit_button = sprintf(
						$args['submit_button'],
						esc_attr( $args['name_submit'] ),
						esc_attr( $args['id_submit'] ),
						esc_attr( $args['class_submit'] ),
						esc_attr( $args['label_submit'] )
					);

					/**
					 * Filter the submit button for the comment form to display.
					 *
					 * @since 4.2.0
					 *
					 * @param string $submit_button HTML markup for the submit button.
					 * @param array $args Arguments passed to `comment_form()`.
					 */
					$submit_button = apply_filters( 'comment_form_submit_button', $submit_button, $args );
					$reply_to_id = isset( $_GET['replytocom'] ) ? (int) $_GET['replytocom'] : 0;

					$submit_field = sprintf(
						$args['submit_field'],
						$submit_button,
						get_comment_id_fields( $post_id ),
						'<input type="hidden" name="comment_post_ID" value="' . $post_id . '">',
						'<input type="hidden" name="comment_parent" value="' . $reply_to_id . '">',
						'<input id="task-key" type="hidden" name="task-key" value="' . esc_attr( $task ) . '">'
					);

					/*
					$result      = "<input type='hidden' name='comment_post_ID' value='$post_id' id='comment_post_ID' />\n";
    $result     .= "<input type='hidden' name='comment_parent' id='comment_parent' value='$reply_to_id' />\n";
					*/


					/**
					 * Filter the submit field for the comment form to display.
					 *
					 * The submit field includes the submit button, hidden fields for the
					 * comment form, and any wrapper markup.
					 *
					 * @since 4.2.0
					 *
					 * @param string $submit_field HTML markup for the submit field.
					 * @param array $args Arguments passed to comment_form().
					 */
					echo apply_filters( 'comment_form_submit_field', $submit_field, $args );

					/**
					 * Fires at the bottom of the comment form, inside the closing </form> tag.
					 *
					 * @since 1.5.0
					 *
					 * @param int $post_id The post ID.
					 */
					do_action( 'comment_form', $post_id );
					?>
				</form>
			<?php endif; ?>
		</div><!-- #respond -->
		<?php
		/**
		 * Fires after the comment form.
		 *
		 * @since 3.0.0
		 */
		do_action( 'comment_form_after' );
	else :
		/**
		 * Fires after the comment form if comments are closed.
		 *
		 * @since 3.0.0
		 */
		do_action( 'comment_form_comments_closed' );
	endif;
}

add_action( 'comment_post', 'psp_ajax_comments', 25, 2 );
function psp_ajax_comments( $comment_id, $comment_status ) {

	$comment              = get_comment( $comment_id );

	if ( ( ! empty( $_SERVER['HTTP_X_REQUESTED_WITH'] ) ) && ( strtolower( $_SERVER['HTTP_X_REQUESTED_WITH'] ) == 'xmlhttprequest' ) && ( get_post_type( $comment->comment_post_ID ) == 'psp_projects' ) ) {

		//If AJAX Request Then
		switch ( $comment_status ) {

			case '0':

				//notify moderator of unapproved comment
				wp_notify_moderator( $comment_id );

			case '1': //Approved comment

				// echo "success";

				$commentdata 	= get_comment( $comment_id, ARRAY_A );
				$permaurl 	= get_permalink( $comment->comment_post_ID );
				$url      	= str_replace( 'http://', '/', $permaurl );


				if ( $commentdata['comment_parent'] == 0 ) {

					ob_start();

					echo project_status_comment( $comment, array(), 1 );

					// echo ob_get_clean();

				} else {

					echo '<div class="children">' . project_status_comment( $commentdata, array( 'style' => 'div' ), 2 ) . '</div>';

				}

				$post = get_post( $commentdata['comment_post_ID'] );

				wp_notify_postauthor( $comment_id );

				break;

			default:

				echo "error";

		}

		exit;

	}

}

add_action( 'psp_head', 'psp_translation_variables' );
add_action( 'wp_head', 'psp_translation_variables' );
function psp_translation_variables( $override = false ) {

	if( get_post_type() == 'psp_projects' || $override == true ): ?>

		<script>
			var psp_translate_processesing = '<?php _e( 'Adding your comment...', 'psp_projects' ); ?>';
			var psp_translate_comment_failed = '<?php _e( 'Your comment failed, you either are posting a duplicate comment or have tried to comment again too quickly.', 'psp_projects' ); ?>';
			var psp_translate_comment_success = '<?php _e( 'Comment added...', 'psp_projects' ); ?>';
			var psp_translate_comment_error = '<?php _e( 'Comment failed, you might be having connectivity issues', 'psp_projects' ); ?>';
		</script>

	<?php
	endif;

}

function psp_validate_or_generate_comment_key( $comment_key = NULL, $phase_id, $phase_offset_id, $post_id = NULL ) {

	// If the comment key exists, just return it
	if( $comment_key != NULL ) return $comment_key;

	$post_id = ( $post_id == NULL ? get_the_ID() : $post_id );

	// Otherwise we need to generate and save it.

	$comment_key 	= psp_generate_phase_id();
	$phases 		= get_field( 'phases', $post_id );
	$phases[$phase_id]['phase_id'] = $comment_key;

	update_field( 'phases', $phases, $post_id );

	return $comment_key;

}

add_filter( 'post_password_required', 'psp_bypass_password_protection_for_logged_in_users', 10, 2 );
function psp_bypass_password_protection_for_logged_in_users( $required, $post ) {

	if( is_user_logged_in() && get_post_type($post) == 'psp_projects' ) return false;

	return $required;

}

// add_action( 'comment_form_after_fields', 'psp_private_comment_field' );
function psp_private_comment_field() {

	if( get_post_type() !== 'psp_projects' || !current_user_can('read_psp_private_comments') ) return;

 	echo '<p class="psp-private-comment-field"><label for="psp-private-comment"><input type="checkbox" value="true" name="psp-private-comment"> ' . __( 'Private Message', 'psp_projects' ) . '</label></p>';

}

add_action( 'psp_js_variables', 'psp_discussion_vars' );
function psp_discussion_vars() {

	$discussion_order = psp_get_option( 'psp_comment_reverse_order', false );

	if( !$discussion_order || empty($discussion_order) ) {
		return;
	}

	echo 'var psp_reverse_discussion_order = ' . $discussion_order . ';';

}

add_filter( 'duplicate_comment_id', 'psp_allow_duplicate_comments', 10, 2 );
function psp_allow_duplicate_comments( $dup_id = false, $commentdata ) {

	if( get_post_type( $commentdata['comment_post_ID'] ) == 'psp_projects' ) {
		return false;
	}

	return $dup_id;

}

add_action( 'pre_get_comments', 'psp_remove_project_comments_from_global', 9, 1 );
function psp_remove_project_comments_from_global( $query ) {

	if( get_post_type() == 'psp_projects' || isset($_POST['task_id']) ) {
		return;
	}

	if( is_admin() || current_user_can('moderate_comments') ) {
		return;
	}

	$post_types = get_post_types();
	$post_types_list = array();

	foreach( $post_types as $type ) {
		if( $type == 'psp_projects' ) {
			continue;
		}
		$post_types_list[] = $type;
	}

	$query->query_vars['post_type'] = $post_types_list;

	return $query;

}

add_action( 'init', 'psp_allow_html_in_comments' );
function psp_allow_html_in_comments() {

	global $allowedtags;

	if( is_user_logged_in() ) {

		$new_tags = array(
			'h1',
			'h2',
			'h3',
			'h4',
			'h5',
			'h6',
			'ul',
			'ol',
			'li',
			'strong',
			'em',
			'u',
			'a',
			'span'
		);

		foreach( $new_tags as $tag ) {

			$atts = array();

			if( $tag == 'span' ) {
				$atts = array(
					'data-notify' => array(),
					'class' => array()
				);
			}

			$allowedtags[$tag] = $atts;
		}

	}

}

function psp_comment_notify_fields( $members = false, $cid = false, $pre_selected = array() ) {

	if( $members == false ) {
		return false;
	}

	if( $cid == false ) {
		$cid = uniqid();
	}

	if( !empty($members) ): ?>

		<script>

			var projectUsers = [
				<?php
				foreach( $members as $user ): ?>
				{
					name: '<?php echo esc_js($user['display_name']); ?>',
					id: '<?php echo esc_js($user['ID']); ?>',
				},
				<?php endforeach; ?>
			];

		</script>

		<div class="psp-notify-users">

			<div class="psp-p"><label for="<?php echo esc_attr( 'notify-' . $cid ); ?>"><?php _e( 'Notify', 'psp_projects' ); ?></label></div>

			<?php
			$notify_all = psp_get_option( 'psp_notify_all_by_default', 0 );

			foreach( $members as $member ):

				if( $notify_all || in_array( $member['ID'], $pre_selected ) ) {
					$checked 	= 'checked';
					$class 	= 'is-selected';
				} else {
					$checked 	= '';
					$class 	= '';
				}

				$notify_uid = 'psp-notify-users-' . $cid . '-' . $member['ID']; ?>

				<div class="psp-notify-users__user">
					<label for="<?php echo esc_attr($notify_uid); ?>" class="<?php echo esc_attr($class); ?>">
						<?php echo get_avatar( $member['ID'], 50 ); ?>
						<input name="psp_user[]" type="checkbox" <?php echo $checked; ?>  value="<?php echo esc_attr($member['ID']); ?>" id="<?php echo esc_attr($notify_uid); ?>"> <span class="psp-username"><?php echo esc_html($member['display_name']); ?></span>
					</label>
				</div>
			<?php
			endforeach; ?>
		</div>
	<?php
	endif;
}

add_action( 'psp_js_variables', 'psp_project_users_source_var' );
function psp_project_users_source_var() {

	$users = psp_get_project_users(); ?>

	var at_mention_css = '<?php echo esc_url( PROJECT_PANARAMA_URI . '/dist/assets/css/at-mentions.css' ); ?>';

	var projectUsers = [
		<?php
		if( $users ): foreach( $users as $user ): ?>
		{
			name: '<?php echo esc_js($user['display_name']); ?>',
			id: '<?php echo esc_js($user['ID']); ?>',
		},
		<?php endforeach; endif; ?>
	];
	<?php

}
