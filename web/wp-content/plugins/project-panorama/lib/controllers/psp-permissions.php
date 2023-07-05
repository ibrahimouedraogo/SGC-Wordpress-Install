<?php
/**
 * psp-permissions.php
 *
 * Manage user levels and permissions to projects
 *
 * @category controller
 * @package psp-projects
 * @author Ross Johnson
 * @since 1.3.6
 */

function psp_get_project_users( $post_id = NULL ) {

	// Extra caution
	global $post;
	$post_id = ( $post_id == NULL ? $post->ID : $post_id );

	$project_teams	= get_field( 'teams' , $post_id );
	$users = array();

	// TODO: Could this be simplified with an array_merge?
	while( have_rows( 'allowed_users', $post_id ) ) { the_row();
		$users[]	= get_sub_field( 'user' );
	}

	if( !empty( $project_teams ) ) {
		foreach( $project_teams as $team ) {

			$team_users = psp_get_team_members( $team );

			// TODO: Could this be simplified with an array_merge?
			if( ( !empty( $team_users ) ) && ( is_array( $team_users ) ) ) {
				foreach( $team_users as $team_user ) $users[]	=	$team_user;
			}

		}
	}

	// Remove dupes
	$user_ids = array();
	$unique_users = array();

	foreach( $users as $user ) {

		if( !in_array( $user['ID'], $user_ids ) ) {
			$unique_users[] = $user;
		}

		$user_ids[] = $user['ID'];

	}

	return $unique_users;

}

/*
 * Adds two roles for users
 *
 */

add_action( 'admin_init', 'psp_add_project_roles' );
function psp_add_project_roles() {

    add_role( 'psp_project_owner',
        __( 'Project Owner', 'psp_projects' ),
        array(
            'read' 			=> true,
            'edit_posts' 	=> false,
            'delete_posts' 	=> false,
            'publish_posts' => false,
            'upload_files' 	=> true,
        )
    );

    add_role( 'psp_project_creator',
        __( 'Project Creator', 'psp_projects' ),
        array(
            'read' 			=> true,
            'edit_posts' 	=> false,
            'delete_posts' 	=> false,
            'publish_posts' => false,
            'upload_files' 	=> true,
        )
    );

    add_role('psp_project_manager',
        __( 'Project Manager', 'psp_projects' ),
        array(
            'read' 			=> true,
            'edit_posts' 	=> false,
            'delete_posts' 	=> false,
            'publish_posts' => false,
            'upload_files' 	=> true,
        )
    );

}

/*
* Assigns capabilities to the project roles
*/
function psp_make_role_project_owner( $role ) {

    $caps = array(
        'edit_psp_project',
        'edit_psp_projects',
        'edit_others_psp_projects',
        'read_psp_project',
        'read_private_psp_project',
		'see_priority_psp_projects',
		'read_psp_private_comments'
    );

    foreach( $caps as $cap ) {
        $role->add_cap( $cap );
    }

}

function psp_make_role_project_creator( $role ) {

    $caps = array(
        'edit_psp_project',
        'edit_psp_projects',
        'edit_psp_projects',
        'edit_published_posts',
        'read_psp_project',
        'read_private_psp_project',
        'edit_published_psp_projects',
        'publish_psp_projects',
        'delete_psp_projects',
		'see_priority_psp_projects',
		'read_psp_private_comments',
		'copy_posts'
    );

	$rems = array( 'edit_others_psp_projects' );

    foreach( $caps as $cap ) {
        $role->add_cap( $cap );
    }

	foreach( $rems as $rem ) {
		$role->remove_cap( $rem );
	}

}

function psp_make_role_project_manager($role) {

    $caps = array(
        'read',
        'read_psp_project',
        'read_private_psp_projects',
        'edit_psp_project',
        'edit_psp_projects',
        'edit_private_psp_projects',
        'edit_others_psp_projects',
        'edit_published_psp_projects',
	   'psp_register_users',
        'publish_psp_projects',
        'delete_psp_projects',
        'delete_others_psp_projects',
        'delete_private_psp_projects',
        'delete_published_psp_projects',
        'copy_posts',
		'see_priority_psp_projects',
		'read_psp_private_comments',
		// Teams
		'read_psp_team',
		'read_private_psp_teams',
		'edit_psp_team',
		'edit_psp_teams',
		'edit_others_psp_teams',
		'edit_published_psp_teams',
		'publish_psp_teams',
		'delete_others_psp_teams',
		'delete_private_psp_teams',
		'delete_published_psp_teams',
		'psp_see_all_users'
    );

    foreach( $caps as $cap ) {

        $role->add_cap( $cap );

    }

}

add_action( 'admin_init', 'psp_add_role_caps', 999 );
function psp_add_role_caps() {

	$has_run = get_option( 'psp_has_set_permissions', false );

	if ( version_compare( $has_run, '2.0', '<' ) ) {

		// Need to run!

		$default_css_permissions = array(
			'update_doc_status'
		);

		$default_cap_permissions = array(
			'psp_upload_documents'
		);

		$admin_caps = array(
			'administrator' => array(
				'psp_register_users',
				'psp_see_all_users',
			),
			'editor' => array(
				'psp_see_all_users',
			),
			'psp_project_manager' => array(
				'psp_see_all_users',
			),
		);

		$permissions = psp_get_option( 'psp_role_permissions' );

		global $wp_roles;
		$roles = $wp_roles->roles;


		if( !empty($roles) ) {
			foreach( $roles as $role_slug => $args ) {

				// Default permissions - everyone should get these
				if( !empty($default_cap_permissions) ) {
					foreach( $default_cap_permissions as $cap ) {

						$role = get_role($role_slug);

						if( $role ) {
							$role->add_cap($cap);
						}

					}
				}

				if( !empty($default_css_permissions) ) {

					foreach( $default_css_permissions as $cap ) {
						$permissions[ $role_slug ][ $cap ] = 'true';
					}
				}

			}

			psp_update_option( 'psp_role_permissions', $permissions );

		}

		foreach( $admin_caps as $role => $caps ) {

			$role = get_role( $role_slug );

			if( $role ) {
				if( !empty( $caps ) ) {
					foreach( $caps as $cap ) {
						$role->add_cap($cap);
					}
				}
			}

		}

		update_option( 'psp_has_set_permissions', '2.0' );

	}

	if( $has_run ) {
		return;
	}

    $owners = get_role( 'psp_project_owner' );
	if( !empty( $owners ) ) {
		psp_make_role_project_owner( $owners );
	}

    $manager = get_role( 'psp_project_manager' );
    if( !empty( $manager ) ) {
		psp_make_role_project_manager( $manager );
	}

    $admin = get_role( 'administrator' );
    if( !empty( $admin ) ) {
		psp_make_role_project_manager( $admin );
	}

    $editor = get_role( 'editor' );
    if( !empty( $editor ) ) {
		psp_make_role_project_manager( $editor );
	}

    $creator = get_role( 'psp_project_creator' );
    if( !empty( $creator ) ) {
        psp_make_role_project_creator( $creator );
    }

	update_option( 'psp_has_set_permissions', true );

}

/*
    Remove the add button for project owners
*/
add_action( 'admin_menu', 'psp_remove_add_project' );
function psp_remove_add_project() {

    global $submenu;

    if( psp_get_current_user_role() == 'Project Owner' ) {
        $submenu['edit.php?post_type=psp_projects'][10][1] = '';
    }

}

/**
 * Returns the translated role of the current user. If that user has
 * no role for the current blog, it returns false.
 *
 * @return string The name of the current role
 **/
function psp_get_current_user_role() {

    global $wp_roles;

    $current_user 	= wp_get_current_user();
    $roles 			= $current_user->roles;
    $role 			= array_shift( $roles );

    return isset( $wp_roles->role_names[$role]) ? translate_user_role($wp_roles->role_names[$role] ) : false;
}

/**
 * Outputs a list of projects assigned to a particular user
 *
 * @return HTML table
 **/
add_action( 'admin_menu', 'register_psp_user_project_list' );
function register_psp_user_project_list() {
    add_submenu_page( NULL, 'Projects By User', 'Projects by User', 'manage_options', 'psp_user_list', 'psp_user_project_list' );
}

/**
 * Hook into the failed login and redirect to our login form and display an error
 * @param  string $user WordPress user login
 * @return NULL
 */

add_action( 'wp_authenticate', 'psp_login_failed' );
add_action( 'wp_login_failed', 'psp_login_failed' );
function psp_login_failed( $user ) {

	if( isset( $_POST[ 'psp-login-form' ] ) ) {

		$referrer 	= $_SERVER[ 'HTTP_REFERER' ];
		$retry		= strpos( $referrer, '?login=failed' );

		if( !empty( $retry ) ) {
			wp_redirect( $_SERVER[ 'HTTP_REFERER' ] );
		} else {
			wp_redirect( $_SERVER[ 'HTTP_REFERER' ] . '?login=failed' );
		}

	}

}

/**
 * Add custom hidden field so we know this login field is coming form a PSP page
 */
add_filter( 'login_form_middle', 'psp_add_login_field' );
function psp_add_login_field( $content ) {

	if( ( get_post_type() == 'psp_projects' ) || ( is_post_type_archive( 'psp_projects' ) ) ) {
		$content .= '<input type="hidden" name="psp-login-form" value="true">';
	}

	return $content;

}

/**
 * Checks to see if the user has access to the project, returns 1 if access is granted, 0 if false
 *
 *
 * @param integer $post_id post ID
 * @return int 1 or 0
 **/
function panorama_check_access( $post_id = NULL ) {

     $access_level 	= get_field( 'restrict_access_to_specific_users' , $post_id );
	$project_teams	= get_field( 'teams' , $post_id );
     $current_user 	= wp_get_current_user();
	$result			= FALSE; // default to false unless
     $author         = get_post_field( 'post_author', $post_id );

	if( is_post_type_archive('psp_projects') && get_query_var('psp_view') == 'public' ) {

		$result = true;

	} elseif( is_post_type_archive() && !is_user_logged_in() ) {

		$result = FALSE;

	} elseif( is_post_type_archive() && is_user_logged_in() ) {

		$result = TRUE;

	} elseif( current_user_can( 'delete_others_psp_projects' ) ) {

		// Admin or PM, can view / edit anything
	    $result = TRUE;

    } elseif( post_password_required() ) {

		// Password required, require password entry
        $result = FALSE;

	} elseif( ( $access_level ) && ( !is_user_logged_in() ) ) {

		// Post is private and the user isn't logged in
        $result = FALSE;

    } elseif( ( current_user_can( 'publish_psp_projects' ) ) && ( $author == $current_user->ID ) ) {

        // The user can publish projects and they are the post author
        $result = TRUE;

    } elseif( ( !$access_level ) && ( current_user_can( 'edit_others_psp_projects' ) ) ) {

		// This project isn't private, user can view
        $result = TRUE;

	} elseif( ( $access_level ) && ( is_user_logged_in() ) ) {

		// Post is private, user is logged in and they are not an admin. Check to see if they have access.

		if( $project_teams ) {

			foreach( $project_teams as $team ) {

				// $team is the CPT ID

				$team_users = get_field( 'team_members' , $team );

				// TODO: Simplify this with in_array();

				if( ( !empty( $team_users ) ) && ( is_array( $team_users ) ) ) {

					foreach( $team_users as $user ) {

						if( $user[ 'team_member' ][ 'ID' ] == $current_user->ID ) {

							$result = TRUE;

						}

					}

				}

			}

		}

		// If we've gotten this far users are not part of a team, check if they are individually added

		$allowed_users 	= array(); // Array of all users assigned to this project

        while ( have_rows( 'allowed_users' , $post_id ) ) { the_row();

			$user = get_sub_field( 'user' );

			if( isset( $user[ 'ID' ] ) && $user[ 'ID' ] == $current_user->ID ) $result = TRUE;

        }


    } else {

		// Return true
        $result = TRUE;

	}

	return apply_filters( 'panorama_check_access', $result, $post_id );

}

add_action( 'add_meta_boxes' , 'psp_project_edit_restrictions' );
function psp_project_edit_restrictions() {

	global $post;

	if( ( get_post_type() == 'psp_projects' ) && ( !panorama_check_access( $post->ID ) ) ) {
		wp_die( 'Sneaky, you don\'t have access to this project!' );
	}

}

/**
 * psp_can_edit_task()
 * Checks to see if the user can edit a task
 *
 * @param integer $post_id 		Post ID
 * @param integer $phase_id 	Phase ID of Post ID
 * @param integer $task_id 		Task ID of Phase ID
 *
 * @return bool
 **/
function psp_can_edit_task( $post_id, $phase_id, $task_id ) {

	// Check to see if the user has permissions to edit the project, if so they can complete the task
	if( psp_can_edit_project( $post_id ) ) return true;

	if( !is_user_logged_in() ) return false;

	$current_user 	= wp_get_current_user();
	$phases 		= get_field( 'phases', $post_id );
	$assigned 	= $phases[$phase_id]['tasks'][$task_id]['assigned'];

	// If this task is assigned to the current user then they can edit it
	if( is_array($assigned) && in_array( $current_user->ID, $assigned ) ) {
		return apply_filters( 'psp_can_edit_task', true, $post_id, $phase_id, $task_id );
	} elseif( $asigned == $current_user->ID ) {
		return apply_filters( 'psp_can_edit_task', true, $post_id, $phase_id, $task_id );
	}

	return apply_filters( 'psp_can_edit_task', false, $post_id, $phase_id, $task_id );

}

/**
 * Checks to see if the user can edit the current project, returns true or false
 *
 *
 * @param integer $id post ID
 * @return bool
 **/
function psp_can_edit_project( $post_id = NULL ) {

	$post_id = ( $post_id == NULL ? get_the_ID() : $post_id );

    if( current_user_can( 'delete_others_psp_projects' ) ) {

		// User can publish projects, so they can edit all projects
        return true;

	} elseif( current_user_can( 'edit_psp_projects' ) ) {

		// User can edit projects but not publish, see if they are assigned to this project

		$current_user 	= wp_get_current_user();
		$allowed_users 	= psp_get_project_users( $post_id );

		if( get_post_meta( $post_id, '_psp_post_author', true ) == $current_user->ID ) return true;

		// Loop through all the allowed users and add them to an array
		if( !empty( $allowed_users ) ) {
			foreach ( $allowed_users as $allowed_user ) {

				if( !isset( $allowed_user[ 'ID' ] ) ) return false;

				// If the current user matches this user ID return true
            	if( $current_user->ID == $allowed_user[ 'ID' ] ) {
					return true;
				}

			} //end.foreach
		} //end.if

    }

	// User can't edit this project, return false
    return false;

}

/**
 * psp_allowed_posts_where()
 *
 * Alters the query string so we can query for projects assigned to a specific user
 *
 * @param $where
 * @return $where
 *
 **/

add_filter( 'posts_where', 'psp_allowed_posts_where' );
function psp_allowed_posts_where( $where ) {

	global $wpdb;

	if( method_exists( $wpdb, 'remove_placeholder_escape' ) ) {
    		$where = str_replace("meta_key = 'allowed_users_%_user'", "meta_key LIKE 'allowed_users_%_user'", $wpdb->remove_placeholder_escape($where) );
	} else {
		$where = str_replace("meta_key = 'allowed_users_%_user'", "meta_key LIKE 'allowed_users_%_user'", $where );
	}

    return $where;

}

add_filter( 'posts_where', 'psp_tasks_assigned_posts_where' );
function psp_tasks_assigned_posts_where( $where ) {

	global $wpdb;

	if( method_exists( $wpdb, 'remove_placeholder_escape' ) ) {
    	$where = str_replace( "meta_key = 'phases_*_tasks_%_assigned'", "meta_key LIKE 'phases_*_tasks_%_assigned'", $wpdb->remove_placeholder_escape($where) );
	} else {
		$where = str_replace( "meta_key = 'phases_*_tasks_%_assigned'", "meta_key LIKE 'phases_*_tasks_%_assigned'", $where );
	}

    return $where;

}

add_filter( 'pre_get_posts', 'psp_limit_teams_to_granted_users', 9999 );
function psp_limit_teams_to_granted_users( $query ) {

	global $pagenow;

	if( ( $query->is_admin ) && ( $pagenow == 'edit.php' ) && ( $query->query[ 'post_type' ] == 'psp_teams' ) && !current_user_can('edit_others_psp_teams') ) {

		$cuser = wp_get_current_user();

		$query->set( 'author', $cuser->ID );

     }

}

/*
 * limit_psp_to_granted_users()
 *
 * Limits what projects are available to the ones the user has access to
 * TODO: Rewrite this using SQL query
 */

add_filter( 'pre_get_posts', 'limit_psp_projects_list_to_granted_users', 999 );
function limit_psp_projects_list_to_granted_users( $query ) {

    global $pagenow;

    // Check to see if were in the admin panel and project edit page
	// if( !isset( $_GET[ 'post_type'] ) ) return;

	$projects_per_page  = intval(psp_get_option('psp_projects_per_page'));

    if( ( $query->is_admin ) && ( $pagenow == 'edit.php' ) && ( $query->query[ 'post_type' ] == 'psp_projects' ) ) {

		$user_id 			= get_current_user_id();
		$psp_meta_query 	= psp_access_meta_query( $user_id );

		if( $psp_meta_query ) {
			$query->set( 'meta_query', $psp_meta_query );
		}

    }

	if( is_post_type_archive('psp_projects') && $projects_per_page && $query->is_main_query() && !is_admin() ) {
		$query->set( 'posts_per_page', $projects_per_page );
	}

}

function psp_access_meta_query( $user_id = NULL ) {

	if( $user_id == NULL ) {
		$user 	= wp_get_current_user();
		$user_id	= $user->ID;
	}

	if( ( !current_user_can( 'publish_pages' ) ) && ( psp_get_current_user_role() != 'Project Manager' ) ) {

		$team_meta_query	= psp_team_dynamic_meta_query( psp_get_team_ids( $user_id ) );

		$meta_query = array(
			'relation'	=>	'OR',
	       	array(
				'key' 	=> 'allowed_users_%_user',
				'value' => $user_id
			),
			array(
				'key' 	=> 'restrict_access_to_specific_users',
				'value' => ''
			),
			array(
				'key'	=>	'_psp_post_author',
				'value'	=>	$user_id
			)
		);

		if( $team_meta_query ) {
			$meta_query = array_merge( $meta_query, $team_meta_query );
		}

		return apply_filters( 'psp_project_access_meta_query', $meta_query );

	} else {

		return FALSE;

	}

}

add_filter( 'pre_get_posts', 'psp_limit_to_completed_projects', 999 );
function psp_limit_to_completed_projects( $query ) {

	global $pagenow;

	if(!isset($_GET['post_type'])) {
		return $query;
	}

	$status_ignore = array(
		'draft',
		'publish',
		'all',
		'trash',
		'private'
	);

	if( isset($_GET['post_status']) && $_GET['post_status'] ) {

		if(($pagenow == 'edit.php') && ($_GET['post_type'] == 'psp_projects') && ($query->is_main_query())) {

			if( !in_array( $_GET['post_status'], $status_ignore ) ) {

				if( isset($_GET['psp_project_types']) ) {

					$query->set('tax_query', array(
							'relation'	=>	'AND',
							array(
								'taxonomy'	=>	'psp_project_types',
								'field'		=>	'slug',
								'terms'		=>	$_GET['psp_project_types']
							),
							array(
								'taxonomy'	=>	'psp_status',
								'field'		=>	'slug',
								'terms'		=>	$_GET['post_status'],
							)
						)
					);

				} else {

					$query->set('tax_query', array(
							array(
								'taxonomy'	=>	'psp_status',
								'field'		=>	'slug',
								'terms'		=>	$_GET['post_status'],
							)
						)
					);

				}

			} else {

				$query->set( 'post_status', $_GET['post_status'] );

			}

		}

	} elseif (($pagenow == 'edit.php') && ($_GET['post_type'] == 'psp_projects') && ($query->is_main_query())) {

		$terms = apply_filters( 'psp_inactive_post_statuses', array( 'completed', 'hold', 'canceled' ) );

		$query->set('tax_query',array(
				array(
					'taxonomy'	=>	'psp_status',
					'field'		=>	'slug',
					'terms'		=>	$terms,
					'operator'	=>	'NOT IN'
				),
			)
		);

		$query->set('post_status','publish');

	}

	return $query;

}

function panorama_login_form( $password = NULL, $redirect = NULL ) {

	$redirect = ( $redirect == NULL ? psp_full_url( $_SERVER ) : $redirect );

	if( empty( $password ) ):

    	return wp_login_form( apply_filters( 'psp_login_form', array( 'redirect' => $redirect ) ) );

	else:

		ob_start(); ?>

		<form action="<?php echo esc_url( site_url( 'wp-login.php?action=postpass', 'login_post' ) ); ?>" method="post">

			<p><label for="post_password"><?php esc_html_e( 'Password', 'psp_projects' ); ?></label>

			<input name="post_password" type="password"></p>

			<p><input type="submit" name="Submit" value="<?php _e( 'Submit', 'psp_projects' ); ?>" class="pano-btn psp-btn"></p>

		</form>

		<?php

	endif;

}

add_filter( 'login_form_middle', 'psp_inject_login_form_hook' );
function psp_inject_login_form_hook( $content ) {

	if( get_post_type() == 'psp_projects' || is_post_type_archive( 'psp_projects' ) ) {

		ob_start();

			do_action( 'login_form' );

		$content = ob_get_clean();

	}

	return $content;

}

add_filter( 'ajax_query_attachments_args', 'psp_show_current_user_attachments' );
function psp_show_current_user_attachments( $query ) {

    $user_id = get_current_user_id();

    if ( $user_id && ( psp_user_has_role('psp_project_owner') || psp_user_has_role('psp_project_creator') ) ) {
        $query['author'] = $user_id;
    }

    return $query;
}

function psp_get_hashed_id( $cuser = NULL ) {

	$cuser = ( $cuser == NULL ? wp_get_current_user() : $cuser );

	return str_rot13($cuser->user_nicename);

}

// add_filter( 'acf/prepare_field/key=field_569707ee2c384', 'psp_author_remove_user_access_controls' );
// add_filter( 'acf/prepare_field/key=field_532b8d8b9c46b', 'psp_author_remove_user_access_controls' );
function psp_author_remove_user_access_controls( $field ) {

	$user_id = get_current_user_id();

	if ( $user_id && ( psp_user_has_role('psp_project_owner') || psp_user_has_role('psp_project_creator') ) ) {

		return false;

	}

	return $field;

}

// add_action( 'admin_head', 'psp_author_hide_user_access_controls' );
// add_action( 'psp_head', 'psp_author_hide_user_access_controls' );
function psp_author_hide_user_access_controls() {

	$user_id = get_current_user_id();

	if( is_admin() && get_post_type() !== 'psp_projects' ) {
		return;
	}

	if ( $user_id && ( psp_user_has_role('psp_project_owner') || psp_user_has_role('psp_project_creator') ) ) { ?>
		<style type="text/css">
			#acf-teams,
			#acf-allowed_users {
				display: none;
			}
		</style>
	<?php }

}

add_action( 'psp_head', 'psp_css_permission_controls' );
function psp_css_permission_controls() {

	if( 'psp_projects' !== get_post_type() ) {
		return;
	}

	$permissions = psp_get_option( 'psp_role_permissions' );

	$css = '';

	if( !empty($permissions) ) {

		foreach( $permissions as $role_slug => $caps ) {

			if( !empty($caps['update_doc_status']) && $caps['update_doc_status'] !== 'true' ) {
				$css .= '#psp-projects.role-' . $role_slug . ' a.doc-status,';
			}

		}

		if( !empty($css) ) {

			$css = rtrim( $css, ',' );

			$css .= '{
				pointer-events: none;
			}'; ?>

			<style type="text/css">
				<?php echo $css; ?>
			</style>

			<?php

		}

	}

}


function psp_get_edit_post_link( $post_id = null, $action = '', $context = '' ) {

	$post_id = ( $post_id == null ? get_the_ID() : $post_id );

	if ( ! $post = get_post( $post_id ) )
        return;

    if ( 'revision' === $post->post_type )
        $action = '';
    else
        $action = '&action=edit';

    $post_type_object = get_post_type_object( $post->post_type );
    if ( !$post_type_object )
        return;

    if ( $post_type_object->_edit_link ) {
        $link = admin_url( sprintf( $post_type_object->_edit_link . $action, $post->ID ) );
    } else {
        $link = '';
    }

	return $link;

}

add_action( 'init', 'psp_check_file_permissions' );
function psp_check_file_permissions() {

	/*
	RewriteCond %{REQUEST_URI} \.(pdf|zip)$ [NC]
	RewriteCond %{REQUEST_FILENAME} -s
	RewriteRule ^wp-content/uploads/(.*)$ dl-file.php?file=$1 [QSA,L]
	*/

	$uploads_dir = wp_upload_dir();

	// var_dump($uploads_dir);

	$condition = $uploads_dir['basedir'] . '/psp/(.*)$ ' . get_post_type_archive_link('psp_projects') . '/?psp_download_file=$1 [QSA,L]';

	// echo $condition;

	add_rewrite_rule('^wp-content/uploads/psp/(.*)?', 'index.php?psp_download_file=$1', 'top' );

	/*
	RewriteCond %{REQUEST_FILENAME} -s
	RewriteRule ^
	*/
}


add_filter( 'psp_team_fields', 'psp_add_team_register_user_fields', 1000, 1 );
function psp_add_team_register_user_fields( $fields ) {
	// If this role can't register users, pass
	if( !current_user_can('psp_register_users') ) {
		return $fields;
	}

     $cuser = wp_get_current_user();
     $teams = psp_get_user_teams_query($cuser->ID);

     $team_options = array(
          ''   =>   '',
     );

     while( $teams->have_posts() ): $teams->the_post();

          $team_options[ get_the_ID() ] = get_the_title();

     endwhile;

     $new_field = array(
          'key' => 'psp_register_users',
		'label' => __('Register Users','psp_projects'),
		'name' => 'psp_register_users',
		'type' => 'repeater',
		'role' => array (
			0 => 'all',
		),
		'field_type' => 'select',
		'allow_null' => 1,
		'sub_fields' => array (
			array (
				'key' => 'register_users_email',
				'label' => __('Email Address','psp_projects'),
				'name' => 'email_address',
				'prefix' => '',
				'type' => 'text',
				'instructions' => __('Enter the email address of the user you\'d like to add', 'psp_projects' ),
				'required' => 1,
				'conditional_logic' => 0,
				'wrapper' => array (
					'width' => '',
					'class' => '',
					'id' => '',
				),
				'role' => '',
				'allow_null' => 0,
				'multiple' => 0,
                    'wrapper'	=>	array(
                         'width'	=>	'50'
                    )
			),
		),
		'row_min' => 0,
		'row_limit' => '',
		'layout' => 'block',
		'button_label' => __('Add Email','psp_projects')
     );

	$new_fields     = array();
	$new_sub_fields = array();

	foreach( $fields['fields'] as $parent_field ) {

          $new_fields[] = $parent_field;

		if( $parent_field['name'] == 'team_members' ) {
			$new_fields[] = $new_field;
		}

	}

	$fields['fields'] = $new_fields;

	return $fields;

}

/*
 * Add fields for user registration to projects TODO: Add teams
 */
add_filter( 'psp_overview_fields', 'psp_add_register_user_fields', 1000, 1 );
function psp_add_register_user_fields( $fields ) {

	// If this role can't register users, pass
	if( !current_user_can('psp_register_users') ) {
		return $fields;
	}

     $cuser = wp_get_current_user();
     $teams = psp_get_user_teams_query($cuser->ID);

     $team_options = array(
          ''   =>   '',
     );

     while( $teams->have_posts() ): $teams->the_post();

          $team_options[ get_the_ID() ] = get_the_title();

     endwhile;

     $new_field = array(
          'key' => 'psp_register_users',
		'label' => __('Register Users','psp_projects'),
		'name' => 'psp_register_users',
		'type' => 'repeater',
		'role' => array (
			0 => 'all',
		),
		'field_type' => 'select',
		'allow_null' => 1,
		'sub_fields' => array (
			array (
				'key' => 'register_users_email',
				'label' => __('Email Address','psp_projects'),
				'name' => 'email_address',
				'prefix' => '',
				'type' => 'text',
				'instructions' => __('Enter the email address of the user you\'d like to add', 'psp_projects' ),
				'required' => 1,
				'conditional_logic' => 0,
				'wrapper' => array (
					'width' => '',
					'class' => '',
					'id' => '',
				),
				'role' => '',
				'allow_null' => 0,
				'multiple' => 0,
                    'wrapper'	=>	array(
                         'width'	=>	'50'
                    )
			),
               array(
                    'key' => 'assign_to_team',
                    'label' => __('Team','psp_projects'),
                    'instructions' => __('Select any teams you\'d like to assign them to', 'psp_projects' ),
                    'name' => 'assign_to_team',
                    'type' => 'select',
                    'field_type'   =>   'select',
                    'choices' => $team_options,
                    'allow_null' => 1,
                    'multiple' => 1,
                    'ui'      => 1,
                    'ajax'    =>   0,
        			'wrapper'	=>	array(
        				'width'	=>	'50'
        			)
               )
		),
		'row_min' => 0,
		'row_limit' => '',
		'layout' => 'block',
		'button_label' => __('Add Email','psp_projects')
     );

	$new_fields     = array();
	$new_sub_fields = array();

	foreach( $fields['fields'] as $parent_field ) {

          $new_fields[] = $parent_field;

		if( $parent_field['name'] == 'allowed_users' ) {
			$new_fields[] = $new_field;
		}

	}

	$fields['fields'] = $new_fields;

	return $fields;

}

add_action( 'save_post', 'psp_check_for_new_user_registration', 999, 2 );
function psp_check_for_new_user_registration( $post_id, $post ) {

	$post_types = apply_filters( 'psp_register_users_post_types', array(
		'psp_projects',
		'psp_teams'
	) );

     if( !in_array( get_post_type(), $post_types ) ) {
          return;
     }

     $register_users = get_field('psp_register_users');

     if( !$register_users ) {
          return;
     }

     $new_users = array();

     foreach( $register_users as $register ) {

          if( is_email($register['email_address']) ) {

               if( email_exists($register['email_address']) ) {
                    $user_id = email_exists($register['email_address']);
               } else {
                    $user_id = register_new_user( $register['email_address'], $register['email_address'] );
               }

               $user = get_user_by( 'id', $user_id );

               $new_users[] = $user_id;

			if( get_post_type() == 'psp_projects' ) {
               	add_row( 'allowed_users', array( 'user' => $user ), $post_id );
			}

			if( get_post_type() == 'psp_teams' ) {

				$members = get_field( 'team_members', $post_id );

				$members[] = array(
					'team_member' => $user
				);

				update_field( 'team_members', $members, $team_id );

			}

               // Add them to a team
               if( isset($register['assign_to_team']) && $register['assign_to_team'] ) {
                    foreach( $register['assign_to_team'] as $team_id ) {

                         $members = get_field( 'team_members', $team_id );

                         $members[] = array(
                              'team_member' => $user
                         );

                         update_field( 'team_members', $members, $team_id );

                    }
               }

          }

     }

     // Notify
     do_action( 'psp_users_added_to_project', $post_id, $new_users );

     // Clear
     delete_field( 'psp_register_users', $post_id );
     delete_post_meta( $post_id, 'psp_register_users' );

}



add_filter( 'acf/fields/relationship/query/key=field_569707ee2c384', 'psp_limit_team_assign_choices', 1000, 3 );
function psp_limit_team_assign_choices( $args, $field, $post_id ) {

	if( current_user_can( 'psp_see_all_users' ) ) {
		return $args;
	}

	$cuser = wp_get_current_user();
	$teams = psp_get_user_teams($cuser->ID);
	$ids = array();

	if( !empty($teams) ) {
		foreach( $teams as $team ) {
			$ids[] = $team->ID;
		}
	}

	$args['post__in'] = $ids;

	return apply_filters( 'psp_limit_team_assign_choices_args', $args );

}

add_filter( 'acf/fields/user/query/key=field_569707c883245', 'psp_limit_user_assign_choices', 1000, 3 );
add_filter( 'acf/fields/user/query/key=field_532b8da69c46c', 'psp_limit_user_assign_choices', 1000, 3 );
function psp_limit_user_assign_choices( $args, $field, $post_id ) {

	if( current_user_can( 'psp_see_all_users' ) ) {
		return $args;
	}

	$cuser = wp_get_current_user();
	$teams = psp_get_user_teams($cuser->ID);
	$users = array();

	// https://www.advancedcustomfields.com/resources/acf-fields-relationship-query/
	// https://codex.wordpress.org/Function_Reference/get_users

	if( !empty( $teams ) ) {
		foreach( $teams as $team ) {

			$team_members = get_field( 'team_members', $team );

			if( !empty( $team_members ) ) {
				foreach( $team_members as $member ) {
					if( in_array( $member['team_member']['ID'], $users ) == false ) $users[] = $member['team_member']['ID'];
				}
			}

		}
	}

	$args['include'] = apply_filters( 'psp_restrict_users_admin_list', $users, $teams, $cuser );

	return $args;

}

function psp_view_is_public() {

	if( get_query_var('psp_view') == 'public' ) {
		return true;
	}

	return false;

}
