<?php
/**
 * Handles plugin upgrades.
 *
 * @since {{VERSION}}
 */

defined( 'ABSPATH' ) || die();

/**
 * Class PSP_DB_Upgrade
 *
 * Handles plugin upgrades.
 *
 * @since {{VERSION}}
 */
class PSP_DB_Upgrade {

	/**
	 * PSP_DB_Upgrade constructor.
	 *
	 * @since {{VERSION}}
	 *
	 * @return bool True if needs to upgrade, false if does not.
	 */
	function __construct() {

		// If we're rolling back, don't check anything else
		if ( isset( $_GET['psp_db_rollback'] ) ) {

			add_action( 'admin_init', array( $this, 'database_rollback' ) );

		}
		else {

			add_action( 'admin_init', array( $this, 'check_upgrades' ) );

			if ( isset( $_GET['psp_upgrade_db'] ) ) {

				add_action( 'admin_init', array( $this, 'do_upgrades' ) );

			}

			if ( isset( $_GET['psp_lite_upgrade'] ) ) {

				add_action( 'admin_init', array( $this, 'lite_upgrade' ) );

			}

			if ( isset( $_GET['psp_db_upgraded'] ) ) {

				add_action( 'admin_notices', array( $this, 'show_upgraded_message' ) );

			}

		}

	}

	/**
	 * This is a slight misnomer. It only rolls back the saved Database Version. It does NOT rollback changes made by the upgrade routines.
	 * This can be used to re-run database migration scripts or to skip them. Use at your own risk.
	 *
	 * @since {{VERSION}}
	 * @access public
	 */
	public function database_rollback() {

		$ver = preg_replace( '/\D/', '', $_GET[ 'psp_db_rollback' ] );

		update_option( 'psp_database_version', $ver );

		wp_safe_redirect( remove_query_arg( 'psp_db_rollback' ) );
		exit();

	}

	/**
	 * Checks for upgrades and migrations.
	 *
	 * @since {{VERSION}}
	 * @access public
	 */
	public function check_upgrades() {

		$current_db_version = get_option( 'psp_database_version', '1.0' );

		// Includes legacy upgrade scripts for those that need them
		if ( $current_db_version ) {

			foreach ( $this->get_upgrades() as $upgrade_version => $upgrade_callback ) {

				if ( version_compare( $current_db_version, $upgrade_version, '<' ) ) {

					$args = array(
						'post_type'			=>	'psp_projects',
						'posts_per_page'	=> 1
					);

					$projects = new WP_Query($args);

					if( !$projects->have_posts() ) {
						$this->do_upgrades();
					} else {
						add_action( 'admin_notices', array( $this, 'show_upgrade_nag' ) );
					}

					break;

				}

			}

		}
		else {

			if ( get_option( 'psp_lite_migration' ) != 1 ) { // Upgrading from Lite

				$lite_projects = new WP_Query( array( 'post_type' => 'psp_projects', 'meta_key' => '_psp_lite_project', 'meta_value' => '1' ) );

				if ( $lite_projects->found_posts > 0 ) {
					add_action( 'admin_notices', array( $this, 'show_lite_upgrade_nag' ) );
				} else {
					// Nothing to update
					update_option( 'psp_lite_migration', 1 );
					update_option( 'psp_database_version', PSP_DB_VER );
				}

			}
			else { // New Install

				update_option( 'psp_database_version', PSP_DB_VER );

			}

		}

	}

	/**
	 * Runs upgrades.
	 *
	 * @since {{VERSION}}
	 * @access public
	 */
	public function do_upgrades() {

		$current_db_version = get_option( 'psp_database_version', '1.0' );

		foreach ( $this->get_upgrades() as $upgrade_version => $upgrade_callback ) {

			if ( version_compare( $current_db_version, $upgrade_version, '<' ) ) {

				call_user_func( $upgrade_callback );
				update_option( 'psp_database_version', $upgrade_version );

			}

		}

		$url = remove_query_arg( 'psp_upgrade_db' );

		wp_safe_redirect( add_query_arg( 'psp_db_upgraded', '1', $url ) );
		exit();

	}

	/**
	 * Returns an array of all versions that require an upgrade.
	 *
	 * @since {{VERSION}}
	 * @access private
	 *
	 * @return array
	 */
	private function get_upgrades() {

		return array(
			4 => array( $this, 'upgrade_db_4' ),
			5 => array( $this, 'upgrade_db_5' ),
			6 => array( $this, 'upgrade_db_6' ),
			7 => array( $this, 'upgrade_db_7' ),
			8 => array( $this, 'upgrade_db_8' ),
		);

	}

	/**
	 * Displays upgrade nag.
	 *
	 * @since {{VERSION}}
	 * @access public
	 */
	public function show_upgrade_nag() {
		?>
        <div class="notice notice-warning">
			<p><img src="<?php echo plugins_url() . '/project-panorama/dist/assets/images/panorama-logo.png'; ?>" width="100" alt="Project Panorama"></p>
			<p><?php _e( 'Project Panorama needs to update your projects to support new features. <strong>IMPORTANT: Backup your site before continuing.</strong>', 'psp_projects' ); ?> <a href="<?php echo add_query_arg( 'psp_upgrade_db', '1' ); ?>"><?php _e( 'Click here to upgrade', 'psp_projects' ); ?></a>.</p>
        </div>
		<?php
	}

	/**
	 * Displays Lite Version upgrade nag.
	 *
	 * @since {{VERSION}}
	 * @access public
	 */
	public function show_lite_upgrade_nag() {
		?>
        <div class="notice notice-warning">
			<p><img src="<?php echo plugins_url() . '/project-panorama/dist/assets/images/panorama-logo.png'; ?>" width="100" alt="Project Panorama"></p>
			<p><?php _e( 'Project Panorama needs to migrate your Lite projects to premium.', 'psp_projects' ); ?> <a href="<?php echo add_query_arg( 'psp_lite_upgrade', '1' ); ?>"><?php _e( 'Click here to migrate', 'psp_projects' ); ?></a></p>
        </div>
		<?php
	}

	/**
	 * Displays the upgraded complete message.
	 *
	 * @since {{VERSION}}
	 * @access public
	 */
	public function show_upgraded_message() {
		?>
        <div class="notice notice-success">
			<p><img src="<?php echo plugins_url() . '/project-panorama/dist/assets/images/panorama-logo.png'; ?>" width="100" alt="Project Panorama"></p>
			<p><?php _e( 'Panorama has successfully updated your projects.', 'psp_projects' ); ?></p>
        </div>
		<?php
	}

	/**
	 * Upgrades from PSP Lite to PSP Pro
	 *
	 * @since {{VERSION}}
	 * @access public
	 */
	public function lite_upgrade() {

		$lite_projects = new WP_Query(array('post_type' => 'psp_projects', 'posts_per_page' => -1));

		while($lite_projects->have_posts()): $lite_projects->the_post();

			global $post;

			$users		= get_post_meta( $post->ID, '_pano_users', true );
			$phases 	= get_post_meta( $post->ID, '_pano_phases', true );
			$documents 	= get_post_meta( $post->ID, '_pano_documents', true );

			if( $users && !empty($users) ) {

				// Normalize

				$users = str_replace( "'", '', $users );
				$users = str_replace( ' ', '', $users );
				$users = array_map( 'intval', explode( ',', $users ) );

				$psp_users = array();

				foreach( $users as $user_id ) {

					$user = get_user_by( 'ID', $user_id );

					if( $user ) {
						$psp_users[] = array(
							'user'	=>	$user_id
						);
					}

				}

				if( !empty($psp_users) ) {
					update_field( 'field_532b8d8b9c46b', $psp_users, $post->ID );
				}

			}

			if(!empty($phases)) {

				$acf_phases = array();

				foreach($phases as $phase) {

					if(isset($phase['percentage_complete'])) {
						$complete = $phase['percentage_complete'];
					} else {
						$complete = 0;
					}

					$phase_fields = array(
						'title'				=>	$phase['title'],
						'description'		=>	$phase['description'],
						'percent_complete'	=>	$complete,
						'tasks'				=>  array()
					);

					if( isset($phase['tasks']) && !empty($phase['tasks']) ) {
						foreach( $phase['tasks'] as $task ) {

							$phase_fields['tasks'][] = array(
								'task'		=>	$task['title'],
								'status'	=>	$task['complete'],
								'task_id'	=>	psp_generate_task_id()
							);

						}
					}

					array_push( $acf_phases, $phase_fields );

				}

				update_field('field_527d5dc12fa29',$acf_phases,$post->ID);

			}


			if(!empty($documents)) {

				$acf_documents = array();

				foreach($documents as $doc) {

					// Set basics

					if(isset($doc['link'])) { $link = $doc['link']; } else { $link = ''; }
					if(isset($doc['file_id'])) { $file = $doc['file_id']; } else { $file = ''; }

					$doc_array = array(
						'title'			=>		$doc['title'],
						'status'		=>		$doc['status'],
						'description'	=>		$doc['description'],
						'url'			=>		$link,
						'file'			=>		$file
					);

					array_push($acf_documents,$doc_array);

				}

				update_field('field_52a9e4634b147',$acf_documents,$post->ID);

			}

			update_post_meta($post->ID,'_psp_lite_project','0');

		endwhile;

		update_option( 'psp_lite_migration', '1' );

		$url = remove_query_arg( 'psp_lite_upgrade' );

		wp_safe_redirect( add_query_arg( 'psp_db_upgraded', '1', $url ) );
		exit();

	}

	/**
	 * DB v4 upgrade script.
	 *
	 * @since {{VERSION}}
	 * @access public
	 */
	public function upgrade_db_4() {

		$args = array(
			'post_type'			=>	'psp_projects',
			'posts_per_page'	=>	-1,
		);

		if( isset($_GET['paged']) ) {
			$args['posts_per_page']	= 20;
			$args['paged']	= $_GET['paged'];
		}

		$projects = new WP_Query($args);

		while ($projects->have_posts()): $projects->the_post();

			global $post;

			$auto_progress = 0;

			// Update the phases progress

			while (have_rows('phases')): the_row();

				if (get_sub_field('auto_progress')) {
					$auto_progress = 1;
				}

			endwhile;

			// Update auto progress

			if ($auto_progress == 1) {
				update_field('field_5436e7f4e06b4', 'Yes',$post->ID);
			}

			// Mark the project as complete or not

			if(psp_compute_progress($post->ID) == 100) {

				wp_set_post_terms($post->ID,'completed','psp_status');

			}

			// Check for old milestones

			$milestones = array();

			if((get_field('milestone_frequency') == 'quarters') && (get_field('display_milestones'))) {

				$milestones[0] = array(
					'occurs'		=>		'25',
					'title'			=>		get_field('25%_title'),
					'description'	=>		get_field('25%_description')
				);

				$milestones[1] = array(
					'occurs'		=>		'50',
					'title'			=>		get_field('50%_title'),
					'description'	=>		get_field('50%_description')
				);

				$milestones[2] = array(
					'occurs'		=>		'75',
					'title'			=>		get_field('75%_title'),
					'description'	=>		get_field('75%_description')
				);

			} elseif((get_field('milestone_frequency') == 'fifths') && (get_field('display_milestones'))) {

				$milestones[0] = array(
					'occurs'		=>		'20',
					'title'			=>		get_field('25%_title'),
					'description'	=>		get_field('25%_description')
				);

				$milestones[1] = array(
					'occurs'		=>		'40',
					'title'			=>		get_field('50%_title'),
					'description'	=>		get_field('50%_description')
				);

				$milestones[2] = array(
					'occurs'		=>		'60',
					'title'			=>		get_field('75%_title'),
					'description'	=>		get_field('75%_description')
				);

				$milestones[3] = array(
					'occurs'		=>		'80',
					'title'			=>		get_field('100%_title'),
					'description'	=>		get_field('100%_description')
				);

			}

			if(count($milestones) > 0) {

				update_field('field_563d1e50786e6',$milestones,$post->ID);

			}

		endwhile;

	}

	/**
	 * DB v5 upgrade script.
	 *
	 * @since {{VERSION}}
	 * @access public
	 */
	public function upgrade_db_5() {

		$args = array(
			'post_type'			=>	'psp_projects',
			'posts_per_page'	=>	-1,
		);

		if( isset($_GET['paged']) ) {
			$args['posts_per_page']	= 20;
			$args['paged']	= $_GET['paged'];
		}


		$projects = new WP_Query($args);

		while( $projects->have_posts() ) { $projects->the_post();

			$phases         = get_field( 'phases' );
			$new_phases     = array();

			global $post;

			if( $phases ) {

				foreach( $phases as $phase ) {

					if( empty( $phase['phase-comment-key'] ) ) {

						$phase['phase-comment-key'] = uniqid();

					}

					$new_phases[] = $phase;

				}

			}

			update_field( 'field_527d5dc12fa29', $new_phases, $post->ID );

		}

	}

	/**
	 * DB v6 upgrade script.
	 *
	 * @since {{VERSION}}
	 * @access public
	 */
	public function upgrade_db_6() {

		$old_settings = array(
			'edd_panorama_license_key',
			'edd_panorama_license_status',
			'psp_slug',
			'psp_logo',
			'psp_flush_rewrites',
			'psp_rewrites_flushed',
			'psp_back',
			'psp_from_name',
			'psp_from_email',
			'psp_default_subject',
			'psp_default_message',
			'psp_include_logo',
			'psp_header_background',
			'psp_header_text',
			'psp_menu_background',
			'psp_menu_text',
			'psp_header_accent',
			'psp_body_background',
			'psp_body_text',
			'psp_body_link',
			'psp_body_heading',
			'psp_footer_background',
			'psp_accent_color_1',
			'psp_accent_color_1_txt',
			'psp_accent_color_2',
			'psp_accent_color_2_txt',
			'psp_accent_color_3',
			'psp_accent_color_3_txt',
			'psp_accent_color_4',
			'psp_accent_color_4_txt',
			'psp_accent_color_5',
			'psp_accent_color_5_txt',
			'psp_timeline_color',
			'psp_use_custom_template',
			'psp_custom_template',
			'psp_open_css',
			'psp_disable_js',
			'psp_disable_clone_post',
			'psp_calendar_language'
		);

		$psp_settings = array();

		foreach( $old_settings as $setting ) {

			$value = get_option( $setting );

			if( !empty( $value ) ) {

				// Convert old checkboxes to new checkboxes
				if( $value == 'on' ) { $value = 1; }

				$psp_settings = array_merge( $psp_settings, array( $setting => $value ) );

			}

		}

		update_option( 'psp_settings', $psp_settings );

	}

	/**
	 * DB v7 upgrade script.
	 *
	 * @since {{VERSION}}
	 * @access public
	 */
	public function upgrade_db_7() {

		$args = array(
			'post_type'			=>	'psp_projects',
			'posts_per_page'	=>	-1,
			'fields'			=>	'ids',
			'cache_results'		=>	false,
			'no_found_rows'		=>	true
		);

		$projects = get_posts( $args );

		foreach( $projects as $post_id ) {

			if( !get_field( 'restrict_access_to_specific_users', $post_id ) ) continue;

			$current_users      = psp_get_project_users( $post_id );

			if( !empty($current_users) ) {

				foreach( $current_users as $user ) {

					$current_user_ids[] = $user[ 'ID' ];

				}

			}

			update_post_meta( $post_id, '_psp_assigned_users', $current_user_ids );

		}

	}

	/**
	 * DB v8 upgrade script.
	 *
	 * @since {{VERSION}}
	 * @access public
	 */
	public function upgrade_db_8() {

		global $wpdb;

		$args = array(
			'post_type'			=>	'psp_projects',
			'posts_per_page'	=>	-1,
			'fields'			=>	'ids',
			'cache_results'		=>	false,
			'no_found_rows'		=>	true
		);

		$projects = get_posts( $args );

		foreach ( $projects as $project_id ) {

			$phases = get_field( 'phases', $project_id );
			$documents = get_field( 'documents', $project_id );

			foreach ( $phases as $phase_index => $phase ) {

				$phases[ $phase_index ]['phase_id'] = psp_generate_phase_id();

				// Phase Comment Key was basically identical in function to Phase ID, but less clear in naming
				// The new Phase ID is more unique
				$phase_comment_key = get_post_meta( $project_id, 'phases_' . $phase_index . '_phase-comment-key', true );
				$phase_comments = psp_get_phase_comments( $phase_comment_key, $project_id );

				if ( $phase_comments ) {

					foreach ( $phase_comments as $phase_comment ) {

						// Update the Phase Comment Key to equal the new Phase ID
						update_comment_meta( $phase_comment->comment_ID, 'phase-key', $phases[ $phase_index ]['phase_id'] );

					}

				}

				if ( $documents ) {

					foreach ( $documents as $document_index => $document ) {

						// Same here with converting the Phase Comment Key into the Phase ID
						if ( $document['document_phase'] == $phase_comment_key ) {

							$documents[ $document_index ]['document_phase'] = $phases[ $phase_index ]['phase_id'];

						}

					}

				}

				foreach ( $phases[ $phase_index ]['tasks'] as $task_index => $task ) {

					$phases[ $phase_index ]['tasks'][ $task_index ]['task_id'] = psp_generate_task_id();

				}

			}

			update_field( 'phases', $phases, $project_id );
			update_field( 'documents', $documents, $project_id );

		}

		// Phase Comment Key now removed from DB
		$sql = $wpdb->delete(
			$wpdb->postmeta,
			array(
				'meta_key' => '_phases_%s_phase-comment-key',
				'meta_key' => 'phases_%s_phase-comment-key',
			)
		);

	}

}

$instance = new PSP_DB_Upgrade();

add_action( 'admin_init', 'psp_gen_task_ids' );
function psp_gen_task_ids() {

	if( !current_user_can('manage_options') || !isset($_GET['psp_gen_task_ids']) ) {
		return;
	}

	$args = array(
		'post_type'			=>	'psp_projects',
		'posts_per_page'	=>	-1,
		'fields'			=>	'ids',
		'cache_results'		=>	false,
		'no_found_rows'		=>	true
	);

	$projects = get_posts( $args );

	foreach ( $projects as $project_id ) {

		$phases 	= get_field( 'phases', $project_id );
		$documents  = get_field( 'documents', $project_id );

		if( $phases && !empty($phases) ) {

			foreach ( $phases as $phase_index => $phase ) {

				if( !isset($phases[ $phase_index ]['phase_id']) || empty($phases[ $phase_index ]['phase_id']) ) {

					$phases[ $phase_index ]['phase_id'] = psp_generate_phase_id();

					echo '<p>' . esc_html_e( 'Missing key on ' . $phases[ $phase_index ]['title'] . ' on ' . get_the_title($project_id) . '</p>' );

					// Phase Comment Key was basically identical in function to Phase ID, but less clear in naming
					// The new Phase ID is more unique
					$phase_comment_key = get_post_meta( $project_id, 'phases_' . $phase_index . '_phase-comment-key', true );
					$phase_comments = psp_get_phase_comments( $phase_comment_key, $project_id );

					if ( $phase_comments ) {

						foreach ( $phase_comments as $phase_comment ) {

							// Update the Phase Comment Key to equal the new Phase ID
							update_comment_meta( $phase_comment->comment_ID, 'phase-key', $phases[ $phase_index ]['phase_id'] );

						}

					}

					if ( $documents ) {

						foreach ( $documents as $document_index => $document ) {

							// Same here with converting the Phase Comment Key into the Phase ID
							if ( $document['document_phase'] == $phase_comment_key ) {

								$documents[ $document_index ]['document_phase'] = $phases[ $phase_index ]['phase_id'];

							}

						}

					}

				}

				if( isset($phases[ $phase_index ]['tasks']) && !empty($phases[ $phase_index ]['tasks']) ) {

					foreach ( $phases[ $phase_index ]['tasks'] as $task_index => $task ) {

						if( !isset($phases[ $phase_index ]['tasks'][ $task_index ]['task_id']) || empty($phases[ $phase_index ]['tasks'][ $task_index ]['task_id']) ) {

							$phases[ $phase_index ]['tasks'][ $task_index ]['task_id'] = psp_generate_task_id();

							echo '<p>' . esc_html_e( 'Missing key on ' . $phases[ $phase_index ]['tasks'][ $task_index ]['task'] . ' on ' . get_the_title($project_id) . '</p>' );

						}

					}

				}

			}

			update_field( 'phases', $phases, $project_id );

		}

	}

}

add_action( 'wp', 'psp_regen_project_id_tasks', 999999999 );
function psp_regen_project_id_tasks() {

	if( !current_user_can('manage_options') || !isset($_GET['psp_regen_project_id_tasks']) ) {
		return;
	}

	$phases = get_field( 'phases', get_the_ID() );

	if( empty($phases) ) {
		return false;
	}

	$new_phases = array();

	foreach( $phases as $phase ) {

		if( !empty($phase['tasks']) ) {

			$new_tasks = array();

			foreach( $phase['tasks'] as $task ) {

				$task['task_id'] = psp_generate_task_id();
				$new_tasks[] = $task;

			}

			$phase['tasks'] = $new_tasks;

		}

		$new_phases[] = $phase;

	}

	update_field( 'phases', $new_phases, get_the_ID() );

}
