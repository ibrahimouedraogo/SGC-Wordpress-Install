<?php
/**
 * psp-projects.php
 *
 * Register custom projects post type and any admin management around the CPT
 *
 * @category controller
 * @package psp-projects
 * @author Ross Johnson
 * @version 1.1
 * @since 1.3.6
 */

add_action( 'init', 'psp_create_psp_projects' );
function psp_create_psp_projects() {

    $psp_slug  = psp_get_option( 'psp_slug', 'panorama' );

    $labels = apply_filters( 'psp_project_post_type_labels', array(
        'name'                => _x( 'Projects', 'Post Type General Name', 'psp_projects' ),
        'singular_name'       => _x( 'Project', 'Post Type Singular Name', 'psp_projects' ),
        'menu_name'           => __( 'Projects', 'psp_projects' ),
        'parent_item_colon'   => __( 'Parent Project:', 'psp_projects' ),
        'all_items'           => __( 'All Projects', 'psp_projects' ),
        'view_item'           => __( 'View Project', 'psp_projects' ),
        'add_new_item'        => __( 'Add New Project', 'psp_projects' ),
        'add_new'             => __( 'New Project', 'psp_projects' ),
        'edit_item'           => __( 'Edit Project', 'psp_projects' ),
        'update_item'         => __( 'Update Project', 'psp_projects' ),
        'search_items'        => __( 'Search Projects', 'psp_projects' ),
        'not_found'           => __( 'No projects found', 'psp_projects' ),
        'not_found_in_trash'  => __( 'No projects found in Trash', 'psp_projects' ),
    ) );

    $rewrite = apply_filters( 'psp_project_post_type_rewrites', array(
        'slug'                => $psp_slug,
        'with_front'          => true,
        'pages'               => true,
        'feeds'               => true,
    ) );

    $args = apply_filters( 'psp_project_post_type_args', array(
        'label'               => __( 'psp_projects', 'psp_projects' ),
        'description'         => __( 'Projects', 'psp_projects' ),
        'labels'              => $labels,
        'supports'            => array( 'title', 'comments', 'revisions', 'author' ),
        'hierarchical'        => false,
        'public'              => true,
        'show_ui'             => true,
        'show_in_menu'        => true,
        'show_in_nav_menus'   => true,
        'show_in_admin_bar'   => true,
        'menu_position'       => 20,
        'menu_icon'           => '',
        'can_export'          => true,
        'has_archive'         => true,
        'exclude_from_search' => false,
        'publicly_queryable'  => true,
        'rewrite'             => $rewrite,
        'capability_type'     => array( 'psp_project', 'psp_projects' ),
//            'capability_type'     => array('psp_project','psp_projects'),
        'map_meta_cap'        => true,
    ) );

    register_post_type( 'psp_projects', $args );

    do_action( 'psp_loaded_post_type_project' );

}

add_filter( 'manage_psp_projects_posts_columns', 'psp_project_header' );
function psp_project_header( $defaults ) {

    $new = array();

    foreach( $defaults as $key => $title ) {

        if( $key == 'title' ) {

		    $new[$key] 			= $title;
            $new['client'] 		= __( 'Client', 'psp_projects' );
            $new['assigned'] 	= __( 'Users', 'psp_projects' );
            $new['td-progress'] = __( '% Complete', 'psp_projects' );
            $new['timing'] 		= __( 'Time Elapsed', 'psp_projects' );

		} else {
			$new[ $key ] = $title;
		}

    }

    return $new;

}

add_action( 'manage_psp_projects_posts_custom_column', 'psp_project_header_content', 10, 2);
function psp_project_header_content( $column_name, $post_ID ) {

    if( $column_name == 'client' ) {

	    echo get_field( 'client' );

	} elseif ( $column_name == 'td-progress' ) {

        $completed = psp_compute_progress( $post_ID );

		if($completed > 10) {
            echo '<p class="psp-progress"><span class="psp-' . esc_attr($completed) . '"><strong>%' . esc_html($completed) . '</strong></span></p>';
		} else {
		    echo '<p class="psp-progress"><span class="psp-' . esc_attr($completed) . '"></span></p>';
		}

	} elseif ( $column_name == 'assigned' ) {

        $users = psp_get_project_users( $post_ID );

        echo psp_get_user_icons( $users );

    } elseif ( $column_name == 'timing' ) {

       psp_the_timing_bar( $post_ID );

    }
}

function psp_user_name( $user ) {

    if ( empty($user) ) return;

    if( !empty( $user['user_firstname'] ) && !empty( $user['user_lastname'] ) ) {
        $name = $user['user_firstname']. ' '. $user['user_lastname'];
    } else {
        $name = $user[ 'user_nicename' ];
    }

    return '<p class="psp_user_assigned"><a href="edit.php?post_type=psp_projects&page=psp_user_list&user=' . $user[ 'ID' ] . '">' . $user[ 'user_avatar' ] . ' <span>' . $name . '</span></a></p>';

}

add_action( 'save_post', 'psp_project_save_routines', 100, 2);
function psp_project_save_routines( $post_id, $post ) {

	// Bail if this is an autosave
    if( wp_is_post_revision($post_id) || wp_is_post_autosave($post_id) ) return;

	// Bail if this is not a Panorama project
	if( get_post_type() != 'psp_projects' ) return;

	do_action( 'psp_save_post', $post_id, $post );

    $author_id = get_post_field( 'post_author', $post_id );

    update_post_meta( $post_id, '_psp_post_author', $author_id );

    if( isset($_POST['psp-project-status']) ) {
         psp_set_project_status( $post_id, $_POST['psp-project-status'] );
    }

}

function psp_set_project_status( $post_id = null , $status = 'incomplete' ) {

     if( $post_id == null ) {
          $post_id = get_the_ID();
     }

     switch( $status ) {
          case( 'completed' ):
          case( 'complete' ):
               update_post_meta( $post_id, '_psp_completed', '1' );
               wp_set_post_terms( $post_id, 'completed', 'psp_status' );
               break;
          case( 'hold'):
               update_post_meta( $post_id, '_psp_completed', '0' );
               wp_set_post_terms( $post_id, 'hold', 'psp_status' );
               break;
          case( 'canceled'):
               update_post_meta( $post_id, '_psp_completed', '0' );
               wp_set_post_terms( $post_id, 'canceled', 'psp_status' );
               break;
          case( 'incomplete' ):
               update_post_meta( $post_id, '_psp_completed', '0' );
               wp_set_post_terms( $post_id, 'incomplete', 'psp_status' );
               break;
     }

     do_action( 'psp_set_project_status', $post_id, $status );

}

add_action( 'psp_save_post', 'psp_process_project_progress', 11, 2 );
function psp_process_project_progress( $post_id, $post ) {

	$current_progress 	= get_post_meta( $post_id, '_psp_current_progress', true );
	$new_progress		= psp_compute_progress( $post_id );

    if( $new_progress != $current_progress ) {
        // Progress is different so we fire an action for progress change
        do_action( 'psp_project_progress_change', $post_id, $new_progress, $current_progress );
    }

	if( $new_progress > $current_progress ) {
		// Progress has moved forward so we fire an action for the current progress
		do_action( 'psp_project_completion', $post_id, $new_progress, $current_progress );
	}

    psp_set_project_completion( $post_id, $new_progress );

    update_post_meta( $post_id, '_psp_current_progress', $new_progress );

}

add_action( 'psp_save_post', 'psp_reorder_milestones', 10, 2 );
function psp_reorder_milestones( $post_id, $post ) {

	$milestones = get_field( 'milestones', $post_id );

	// Bail if there are no milestones
	if( empty( $milestones ) ) return;

	$order = array();

	foreach( $milestones as $i => $row ) {
		$order[ $i ] = $row[ 'occurs' ];
	}

	array_multisort( $order, SORT_ASC, $milestones );

	update_field( 'milestones', $milestones, $post_id );

}

add_action( 'psp_save_post', 'psp_assign_users_to_project', 10, 2);
function psp_assign_users_to_project( $post_id, $post ) {

    // Bail if this is an autosave
    if( wp_is_post_revision($post_id) || wp_is_post_autosave($post_id) ) return;

	// Bail if this is not a Panorama project
	if( get_post_type() != 'psp_projects' ) return;

    $current_users      = psp_get_project_users( $post_id );
    $existing_users     = (array) get_post_meta( $post_id, '_psp_assigned_users', true );
    $current_user_ids   = array();

    foreach( $current_users as $user ) {
        $current_user_ids[] = $user['ID'];
    }

    if( empty( $existing_users ) ) $existing_users = array();

    $new_users = array_diff( $current_user_ids, $existing_users );

    if( !empty( $new_users ) ) {
        do_action( 'psp_users_added_to_project', $post_id, $new_users );
    }

    update_post_meta( $post_id, '_psp_assigned_users', $current_user_ids );

}

add_filter( 'views_edit-psp_projects', 'psp_update_project_quicklinks' , 1 );
function psp_update_project_quicklinks( $views ) {

	$post_counts		= psp_get_post_counts();

	// Reset defaults
	$completed_class 	= '';
	$publish_class 		= '';
	$draft_class 		= '';
	$trash_class 		= '';
	$active_class 		= '';
     $hold_class         = '';
     $canceled_class     = '';

	if( isset( $_GET[ 'post_status' ] ) ) {

		$post_status = $_GET['post_status'];

		switch($post_status) {

			case 'completed':
				$completed_class 	= 'current';
				break;
               case 'hold':
                    $hold_class         = 'current';
                    break;
               case 'canceled':
                    $canceled_class     = 'current';
                    break;
			case 'publish':
				$publish_class 	= 'current';
				break;
			case 'draft':
				$draft_class 		= 'current';
				break;
			case 'trash':
				$trash_class 		= 'current';
				break;
			default:
				$active_class 		= 'current';
				break;
		}

	} else {
		$active_class 				= 'current';
	}

	$views['all'] = '<a class="' . $active_class . '" href="edit.php?post_type=psp_projects">Active <span class="count">(' . $post_counts[ 'active' ] . ')</span></a>';

     $newlinks = "<a class='" . $completed_class . "' href='edit.php?post_type=psp_projects&post_status=completed'>".__('Completed','psp_projects')." <span class='count'>  (" . $post_counts[ 'complete' ] . ")</span></a>";
     $newlinks .= " | <a class='" . $hold_class . "' href='edit.php?post_type=psp_projects&post_status=hold'>".__('On Hold','psp_projects')." <span class='count'>  (" . $post_counts[ 'hold' ] . ")</span></a>";
     $newlinks .= " | <a class='" . $canceled_class . "' href='edit.php?post_type=psp_projects&post_status=canceled'>".__('Canceled','psp_projects')." <span class='count'>  (" . $post_counts[ 'canceled' ] . ")</span></a>";

     unset( $views['publish'] );

	array_splice($views, 1, 0, $newlinks );

	return $views;

}

add_action( 'restrict_manage_posts', 'psp_add_types_post_filter_to_admin' );
function psp_add_types_post_filter_to_admin(){

    //execute only on the 'post' content type
    global $post_type;
    if( $post_type == 'psp_projects' ){

        //get a listing of all users that are 'author' or above
        $tax_args = array(
            'show_option_all'   => __( 'All Types', 'psp_projects' ),
            'orderby'           => 'name',
            'order'             => 'ASC',
            'name'              => 'psp_project_types',
            'taxonomy'          => 'psp_tax',
            'include_selected'  => true
        );

        //determine if we have selected a user to be filtered by already
        if( isset( $_GET[ 'psp_project_types' ] ) ) {
            //set the selected value to the value of the author
            $tax_args[ 'selected' ] = (int)sanitize_text_field( $_GET[ 'psp_project_types' ] );
        }

        //display the users as a drop down
        wp_dropdown_categories( $tax_args );
    }

}

add_filter( 'parse_query', 'psp_do_types_post_filter_in_admin' );
function psp_do_types_post_filter_in_admin( $query ) {

    global $post_type, $pagenow;

    //if we are currently on the edit screen of the post type listings
    if( $pagenow == 'edit.php' && $post_type == 'psp_projects' ){

        if( isset( $_GET[ 'psp_project_types' ] ) ) {

            //get the desired post format
            $post_format = sanitize_text_field( $_GET[ 'psp_project_types' ] );
            //if the post format is not 0 (which means all)
            if( $post_format != 0 ) {

                $query->query_vars[ 'tax_query' ] = array(
                    array(
                        'taxonomy'  => 'psp_tax',
                        'field'     => 'ID',
                        'terms'     => array( $post_format )
                    )
                );

            }
        }
    }

}

function psp_get_user_icons( $users = null, $limit = 5 ) {

    if( empty( $users )  ) return false;

    ob_start();
    $i = 0;

    foreach( $users as $user ) {

        if( $i < $limit ) {
            echo psp_user_name( $user );
        }

        $i++;

    }

    if( count($users) > $limit ) {
        echo '<div class="psp_user_assigned overage"><strong>+' . ( count( $users ) - $limit ) . '</strong></div>';
    }

    return ob_get_clean();

}

function psp_get_project_post_statuses() {

     return apply_filters( 'psp_project_statuses', array(
          array(
               'slug' => 'active',
               'label' => __( 'Active', 'psp_projects' ),
          ),
          array(
              'slug'  =>  'completed',
              'label' =>  __( 'Completed', 'psp_projects' ),
          ),
          array(
              'slug'  =>  'hold',
              'label' =>  __( 'On Hold', 'psp_projects' ),
          ),
          array(
              'slug'  =>  'canceled',
              'label' =>  __( 'Canceled', 'psp_projects' ),
          ),
     ) );

}

add_action( 'post_submitbox_misc_actions', 'psp_custom_project_status', 9 );
function psp_custom_project_status() {

     global $post;

     if( 'psp_projects' != get_post_type($post) ) {
          return;
     }

     $statuses = psp_get_project_post_statuses();

     if( $statuses ): ?>

          <div class="misc-pub-section misc-pub-section-last">
               <?php wp_nonce_field( plugin_basename( __FILE__ ), 'psp-project-status' ); ?>
               <label for="psp-project-status"><?php esc_html_e( 'Project Status', 'psp-projects' ); ?></label>
               <select name="psp-project-status" id="psp-project-status">
                    <?php
                    foreach( $statuses as $option ):
                         $selected = ( has_term( $option, 'psp_status', $post ) ? 'selected' : '' ); ?>
                         <option value="<?php echo esc_attr($option['slug']); ?>" <?php echo $selected; ?>><?php echo esc_html( $option['label'] ); ?></option>
                    <?php endforeach; ?>
               </select>
          </div>

     <?php
     endif;

}


function psp_custom_project_post_statuses() {

    $statuses = array(
        array(
            'slug'  =>  'psp-completed',
            'label' =>  __( 'Completed', 'psp_projects' ),
        ),
        array(
            'slug'  =>  'psp-hold',
            'label' =>  __( 'On Hold', 'psp_projects' ),
        ),
        array(
            'slug'  =>  'psp-canceled',
            'label' =>  __( 'Canceled', 'psp_projects' ),
        ),
    );

    $args = array(
        'public'                    =>  true,
        'exclude_from_search'       =>  false,
        'show_in_admin_all_list'    => true,
	   'show_in_admin_status_list' => true,
    );

    foreach( $statuses as $status ) {

        $settings = array_merge( $status['label'], $args );

        register_post_status( $status['slug'], $settings );

    }

}

function psp_get_project_status( $post_id = null ) {

     if( $post_id == null ) {
          $post_id = get_the_ID();
     }

     $statuses = psp_get_project_post_statuses();

     foreach( $statuses as $status ) {
          if( has_term( $status, 'psp_status', $post_id ) ) {
               return $status;
          }
     }

}

/*
function psp_get_project_breakdown( $post_id = NULL ) {

    // Get the global post ID if it wasn't passed
    $post_id = $post_id == NULL ? get_the_ID() : $post_id;

    // Get the phases so we can count them later
    $phases = get_field( 'phases', $post_id );

    // The project array, pre-populated as best we can
    $project = array(
        'post_id'   =>  $post_id,
        'title'     =>  get_the_title($post_id),
        'client'    =>  get_field( 'client', $post_id ),
        'progress'  =>  psp_compute_progress($post_id),
        'timing'    =>  array(
            'elappsed'  =>  psp_calculate_timing($post_id),
            'start_date'    =>  psp_get_the_start_date($post_id),
            'end_date'      =>  psp_get_the_end_date($post_id),
        ),
        'milestones'    =>  array(
            'total'     =>  count( get_field( 'milestones', $post_id ) ),
            'complete'  =>  0,
            'overdue'   =>  0
        ),
        'phases'  =>  array(
            'total'     =>  count( $phases ),
            'complete'  =>  0,
        ),
        'tasks' =>  array(
            'total'     =>  0,
            'assigned'  =>  0,
            'complete'  =>  0,
            'overdue'   =>  0
        ),
        'discussions'   =>  array(
            'total'     =>  get_comment_count( $post_id ),
        )
    );

    foreach( $phases as $phase ) {



    }






}
*/
