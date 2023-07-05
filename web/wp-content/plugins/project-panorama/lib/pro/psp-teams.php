<?php
/**
 * Description of psp-teams
 *
 * Functions related to the core Panorama teams capabilties
 * @package psp-projects
 *
 *
 */

add_shortcode( 'project_team', 'psp_team_members_shortcode' );
function psp_team_members_shortcode( $atts ) {

	if( !isset( $atts['id'] ) ) {

		global $post;

		$atts['id'] = $post->ID;

	}

	ob_start();

	psp_front_assets(1);

	echo psp_the_team_members( $atts['id'] );

	return ob_get_clean();

}

add_shortcode( 'my_project_teams' , 'psp_project_teams_shortcode' );
function psp_project_teams_shortcode() {

	ob_start();

	$cuser 			= wp_get_current_user();
	$teams			= psp_get_user_teams( $cuser->ID );

	if( !empty( $teams ) ) { ?>

		<div class="psp-module psp-module-my-teams">

			<?php foreach( $teams as $team ) { ?>

				<div class="psp-module-indv-team">

					<h2><a href="<?php echo get_permalink( $team_id ); ?>"><?php echo get_the_title( $team->ID ); ?></a></h2>

				</div>

			<?php } ?>

		</div>

	<?php } else { ?>

		<p class="psp-notice"><?php _e( 'You\'re not currently assigned to any teams', 'psp_projects' ); ?></p>

	<?php }

	return ob_get_clean();

}

function psp_get_the_teams() {

	$args = array(
		'post_type'			=>	'psp_teams',
		'posts_per_page'	=>	-1
	);

	return new WP_Query( $args );

}

function psp_get_current_user_teams() {

	$cuser = wp_get_current_user();
	return psp_get_user_teams($cuser->ID);

}

function psp_get_user_teams_query( $user_id = NULL, $limit = -1 ) {

	if( $user_id == NULL ) {

		$cuser 		= wp_get_current_user();
		$user_id 	= $cuser->ID;

	}

	$team_objects	=	array();
	$args			=	array(
		'post_type'			=>	'psp_teams',
        'posts_per_page'    =>  $limit,
        'meta_query'        =>  array(
            array(
                'key' => 'team_members_%_team_member',
                'value' => $user_id
            )
        )
	);

	$teams 	=	new WP_Query( $args );

	return $teams;

}

function psp_get_user_teams( $user_id = NULL, $limit = -1 ) {

	if( $user_id == NULL ) {
		$cuser 		= wp_get_current_user();
		$user_id 	= $cuser->ID;
	}

	$team_objects	=	array();
	$args			=	array(
		'post_type'			=>	'psp_teams',
        'posts_per_page'    =>  $limit,
        'meta_query'        =>  array(
            array(
                'key' => 'team_members_%_team_member',
                'value' => $user_id
            )
        )
	);

	$teams = new WP_Query( $args );

	if( $teams->have_posts() ) {

		while( $teams->have_posts() ) { $teams->the_post();

			global $post;

			$team_objects[] = $post;

		}

		wp_reset_query();

		return $team_objects;

	} else {

		return FALSE;

	}

}

function psp_the_team_members( $post_id = NULL ) {

	if( $post_id == NULL ) {

		global $post;

		$post_id = $post->ID;

	}

	$team_members = psp_get_team_members( $post_id );

	if( $team_members ) { ?>

		<div class="psp-module psp-module-team">

			<h2><?php echo get_the_title( $post_id ); ?></h2>

		<?php foreach( $team_members as $team_member ) { ?>

			<div class="psp-team-member">

				<div class="psp-team-gravatar">
					<?php echo $team_member[ 'user_avatar' ]; ?>
				</div>

				<p class="psp-team-name"><?php echo $team_member[ 'user_nicename' ]; ?></p>

			</div>

		<?php } ?>

		</div>

	<?php } else { ?>

		<p class="psp-notice"><?php _e( 'No users assigned to this team', 'psp_projects' ); ?></p>

	<?php }

}

function psp_get_team_members( $post_id = NULL ) {

	global $post;

	$post_id = ( $post_id != NULL ? $post_id : $post->ID );

	if( get_field( 'team_members', $post_id ) ) {

		$members = array();

		while( have_rows( 'team_members', $post_id ) ) { the_row();

			$members[] = get_sub_field( 'team_member' );

		}

		return $members;

	} else {

		return FALSE;

	}

}


function psp_get_project_teams( $post_id = NULL ) {

	if( $post_id == NULL ) {

		global $post;

		$post_id = $post->ID;

	}

	if( get_field( 'teams', $post_id ) ) {

		$teams = array();

		while( have_rows( 'teams' ) ) { the_row();

			$teams[] = the_sub_field('team');

		}

		return $teams;

	} else {

		return FALSE;

	}

}


add_filter('posts_where', 'psp_posts_where_team_members');
function psp_posts_where_team_members( $where ) {

	global $wpdb;

	if( method_exists( $wpdb, 'remove_placeholder_escape' ) ) {
		$where = str_replace("meta_key = 'team_members_%_team_member'", "meta_key LIKE 'team_members_%_team_member'", $wpdb->remove_placeholder_escape($where));
	} else {
		$where = str_replace("meta_key = 'team_members_%_team_member'", "meta_key LIKE 'team_members_%_team_member'", $where );
	}

	return $where;

}

function psp_get_team_ids( $user_id = NULL ) {

	if( $user_id == NULL ) {

		$user 		= wp_get_current_user();
		$user_id	= $user->ID;

	}

	global $wpdb;

	$sql	=	"SELECT $wpdb->posts.* FROM $wpdb->posts, $wpdb->postmeta WHERE $wpdb->posts.ID = $wpdb->postmeta.post_id AND $wpdb->postmeta.meta_key LIKE 'team_members_%_team_member' AND $wpdb->postmeta.meta_value = " . $user_id . " AND $wpdb->posts.post_status = 'publish' AND $wpdb->posts.post_type = 'psp_teams'";

	$teams 		= $wpdb->get_results( $sql );

	if( !empty( $teams ) ) {

		$team_ids	= array();

		foreach( $teams as $team ) {

			$team_ids[] = $team->ID;

		}

		return $team_ids;

	} else {

		return FALSE;

	}

}

function psp_team_dynamic_meta_query( $team_ids = NULL ) {

	if( $team_ids == NULL ) {

		$team_ids = psp_get_team_ids();

	}

	$meta_query = array();

	if( !empty( $team_ids ) ) {

		foreach( $team_ids as $team_id ) {

			$meta_query[] = array(
				'key'		=>	'teams',
				'value'		=>	$team_id,
				'compare'	=>	'LIKE'
			);

		}

		return $meta_query;

	}

}

// This isn't ready yet
// add_action( 'admin_menu', 'psp_my_teams_admin_menu' );
function psp_my_teams_admin_menu() {

	$cuser = wp_get_current_user();

	if( psp_get_team_ids( $cuser->ID ) ) {

		add_submenu_page( 'edit.php?post_type=psp_projects', 'My Teams', 'My Teams', 'read', 'psp_my_teams', 'psp_my_teams_markup' );

	}

}



function psp_my_teams_markup() {

	$cuser	=	wp_get_current_user();
	$teams	=	psp_get_team_ids( $cuser->ID ); ?>

	<div class="wrap">

		<h2><?php _e('My Teams', 'psp-projects' ); ?></h2>

		<table id="psp-my-teams-table" class="wp-list-table widefat fixed posts">
			<thead>
				<tr>
					<th><?php _e( 'Team', 'psp-projects' ); ?></th>
					<th><?php _e( 'Team Members', 'psp-projects' ); ?></th>
					<th><?php _e( 'Current Projects', 'psp-projects' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php if( !empty( $teams ) ) {

					foreach( $teams as $team ) {

						$team_members = psp_get_team_members( $team ); ?>

						<tr>
							<td>
								<?php if( current_user_can( 'publish_psp_projects' ) ) { ?>
									<strong><a href="<?php echo admin_url(); ?>/post.php?post=<?php echo $team; ?>?action=edit"><?php echo get_the_title( $team ); ?></a></strong>
								<?php } else { ?>
									<strong><?php echo get_the_title( $team ); ?></strong>
								<?php } ?>

								<?php the_field( 'description', $team );?>

							</td>
							<td class="column-assigned">

								<?php foreach( $team_members as $member ) { ?>
									<div class="psp_user_assigned">
										<?php echo $member[ 'user_avatar' ]; ?>
										<span><?php echo psp_username_by_id( $member[ 'ID' ] ); ?></span>
									</div>
								<?php } ?>

							</td>
							<td>

							</td>
						</tr>

					<?php }
				} ?>
			</tbody>
		</table>
	</div>

<?php
}

function psp_the_team_thumbnail( $post_id = null ) {

	global $post;

	$post_id = ( $post_id == null ? $post->ID : $post_id );

	echo wp_kses_post( psp_get_team_thumbnail( $post_id ) );

}

function psp_get_team_thumbnail( $post_id = null ) {

	global $post;

	$post_id = ( $post_id == null ? $post->ID : $post_id );

	if( has_post_thumbnail( $post_id ) ) {

		return get_the_post_thumbnail( $post_id, 'small' );

	} else {

		// TODO: Make a default here?
		return false;

	}

}

function psp_get_user_projects( $user_id = null, $paged = 1, $status = null, $posts_per_page = null ) {

	$cuser   = wp_get_current_user();
	$user_id = ( $user_id == null ? $cuser->ID : $user_id );

	$team_meta_query = psp_team_dynamic_meta_query( psp_get_team_ids( $user_id ) );

	$args = array(
		'post_type'	=>	'psp_projects',
		'paged'		=>	$paged
	);

	if( $posts_per_page ) {
		$args['posts_per_page'] = $posts_per_page;
	}

	if( !user_can( $user_id, 'delete_others_psp_projects' ) ) {

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

		$args['meta_query']	= $meta_query;

	}


	if( isset( $status ) ) {
		$args = array_merge( $args, array( 'psp_status' => $status ) );
	}

	return new WP_Query($args);

}

function psp_get_team_projects( $post_id = NULL, $status = null ) {

	global $post;

	$post_id = ( $post_id != NULL ? $post_id : $post->ID );

	$args = array(
		'post_type'			=>	'psp_projects',
		'posts_per_page'	=>	-1,
		'meta_query'		=>	array(
			array(
				'key'		=>	'teams',
				'value'		=>	$post_id,
				'compare'	=>	'LIKE'
			)
		)
	);

	if( isset( $status ) ) {
		$args['tax_query'] = array(
			array(
				'taxonomy' => 'psp_status',
				'field'	   => 'slug',
				'terms'    => $status
			)
		);
	}

	return new WP_Query( $args );

}

function psp_current_user_can_access_team( $post_id ) {

	global $post;

	$post_id = ( $post_id != NULL ? $post_id : $post->ID );

	if( current_user_can( 'delete_others_psp_projects' ) ) return true;

	$users = get_field( 'team_members', $post_id );
	$cuser = wp_get_current_user();

	if( empty( $users ) ) return false;

	foreach( $users as $user ) if( $user['team_member']['ID'] == $cuser->ID ) return true;

	return false;

}

function psp_team_user_icons( $post_id = null, $limit = 3 ) {

	global $post;

	$post_id 	= ( $post_id != NULL ? $post_id : $post->ID );
	$users 		= get_field( 'team_members', $post_id );
	$max		= $limit - 1;
	$c			= 1;

	if( empty( $users ) ) return; ?>

	<span class="psp-team-user-icons">

		<?php
		for( $i = 0; $i <= $max; $i++):

			if( isset($users[$i]) && !empty($users[$i]) && isset( $users[$i]['team_member']['ID'] ) ): ?>

				<span class="psp-team-member-icon psp-tooltip" data-tooltip="<?php echo psp_get_nice_username_by_id( $users[ $i ]['team_member']['ID'] ); ?>">
					<?php
					if( current_user_can('delete_others_psp_projects') ): ?>
						<a href="<?php echo esc_url( psp_get_user_permalink( $users[$i]['team_member']['ID'] ) ); ?>">
					<?php
					endif;
					echo get_avatar( $users[ $i ]['team_member']['ID'] );
					if( current_user_can('delete_others_psp_projects') ): ?>
						</a>
					<?php endif; ?>
				</span>

			<?php
			endif;

			$c++;

		endfor;

		if( count( $users ) > $c ): ?>

			<span class="psp-team-member-more-icon">
				+<?php echo count( $users ) - $c; ?>
			</span>

		<?php endif; ?>

	</span>

	<?php
}
