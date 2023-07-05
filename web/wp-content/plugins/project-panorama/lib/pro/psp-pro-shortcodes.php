<?php
/**
 * Description of psp-pro-shortcodes
 *
 * Shortcodes related to paid version of Project Panorama
 *
 * @package psp-projects
 *
 */

/**
 * psp_project_part()
 *
 * Embed a portion of a project via shortcode
 *
 *
 * @param $atts (array)
 * @param $atts['id'] (int) Post ID
 * @param $atts['display'] (string) Use the shortcode wrapper
 *
 * @return markup
 *
 */

add_shortcode( 'project_phases', 'psp_project_phase_shortcode' );
function psp_project_phase_shortcode( $atts ) {

     ob_start();

     // Don't allow embeds on projects
     if( get_post_type() == 'psp_projects' ) {
         return;
    }

    if( empty($atts['project']) || empty($atts['phases']) ) {
         return;
    }

    $post_id = $atts['project'];

    $phases = get_field( 'phases', $post_id );

    if( empty($phases) ) {
         return;
    }

    $phase_ids = explode( ',', $atts['phases'] );
    $phase_index = 0;

    psp_front_assets(true);

    wp_enqueue_script( 'psp-frontend-library' );
    wp_enqueue_script( 'psp-admin-lib' );
    wp_enqueue_script( 'psp-frontend-behavior' );

    if( function_exists('psp_fe_enqueue_scripts') ) {

         wp_enqueue_style( 'psp-fe-project-front' );
         wp_enqueue_style( 'psp-fe-front' );

         wp_enqueue_script('psp-fe-front');

		$jqueryui_assets = array (
			'jquery-ui-core' => array(
				'script'	=>	true,
				'style'	=>	true,
			),
			'jquery-ui-sortable' => array(
				'script'	=>	true,
				'style'	=>	false
			)
		);

		foreach( $jqueryui_assets as $handle => $settings ) {
			if( $settings['script'] ) {
				wp_enqueue_script($handle);
			}
			if( $settings['style'] ) {
				wp_enqueue_style($handle);
			}
		}

		wp_enqueue_script( 'tinymce_js', includes_url( 'js/tinymce/' ) . 'wp-tinymce.php', array( 'jquery' ), false, true );

		wp_enqueue_style('select2');
		wp_enqueue_script('select2');

     }

    if( function_exists('psp_st_frontend_scripts') ) {
         wp_enqueue_style('subtasks');
         wp_enqueue_script('subtasks');
    } ?>

          <div id="psp-projects" class="psp-part-project psp-theme-template psp-shortcode">
               <div id="psp-phases" class="psp-shortcode">

                    <?php do_action( 'psp_before_all_phases', $post_id, $phases ); ?>

                    <script>
                    	var chartOptions = {
                    		responsive: true,
                    		percentageInnerCutout : <?php echo esc_js( apply_filters( 'psp_graph_percent_inner_cutout', 92 ) ); ?>,
                    		maintainAspectRatio: true,
                    	}
                       var allCharts = [];
                    </script>

                    <div class="psp-phase-wrap">
                    	<?php
                    	while ( have_rows( 'phases', $post_id ) ) : $phase = the_row();

                              if( !in_array( get_sub_field('phase_id'), $phase_ids ) ) {
                                   $phase_index++;
                                   continue;
                              }

                    		if( get_sub_field('private_phase') && !psp_can_edit_project() ) {
                    		    $phase_index++;
                    		    continue;
                    		}

                    		include( psp_template_hierarchy( 'projects/phases/single.php') );

                    	endwhile;

                    	do_action( 'psp_after_all_phases', $post_id, $phases ); ?>
                    </div> <!--/.psp-phase-wrap-->

               </div> <!--/#psp-phases-->
          </div> <!--/#psp-projects -->

     <?php

     return ob_get_clean();

}

add_shortcode( 'project_status_part' , 'psp_project_part_shortcode' );
function psp_project_part_shortcode( $atts ) {

        // Don't allow embeds on projects
        if( get_post_type() == 'psp_projects' )
            return;

        psp_front_assets(true);

        wp_enqueue_script( 'psp-frontend-library' );
        wp_enqueue_script( 'psp-admin-lib' );
        wp_enqueue_script( 'psp-frontend-behavior' );

        extract( shortcode_atts(
			array(
				'id'	 	     => '',
				'display' 	=> '',
				'style'	 	=> '',
			), $atts )
		);


		$project = new WP_Query( array( 'p' => $id, 'post_type' => 'psp_projects' ) );

		// Check to see if a post is returned

		if( $project->have_posts() ) {

			while( $project->have_posts() ) { $project->the_post();

				$output = '<div id="psp-projects" class="psp-part-project psp-theme-template psp-shortcode">';

				if( $display == 'overview' ) {

					$output .= do_action( 'psp_before_essentials' ) . psp_essentials( $id, 'psp-shortcode', 'none' ) . do_action( 'psp_after_essentials' );

				} elseif ( $display == 'documents' ) {

					$output .= '<div id="psp-essentials" class="psp-shortcode"><div id="psp-documents"><div id="psp-documents-list">' . psp_documents( $id, 'shortcode' ) . '</div></div></div>';

                } elseif( $display == 'milestones' ) {

                    $output .= do_action( 'psp_before_progress' ) . psp_the_milestones( $id, 'psp-shortcode', $style ) . do_action( 'psp_after_progress' );

				} elseif ( $display == 'progress' ) {

					$output .= do_action( 'psp_before_progress' ) . psp_total_progress( $id, 'psp-shortcode', $style ) . do_action( 'psp_after_progress' );

				} elseif ( $display == 'phases' ) {

                    $output .= do_action('psp_head');

					$output .= do_action( 'psp_before_phases' ) . '<div id="psp-phases">' . psp_phases( $id, 'psp-shortcode', $style, null ) . '</div>' . do_action( 'psp_after_phases' );

                    psp_translation_variables( true );

                    ob_start(); ?>

                    <script><?php do_action('psp_js_variables'); ?></script>

                    <?php
                    include( psp_template_hierarchy( 'global/navigation-off.php' ) );

                    do_action('psp_footer');

                    $output .= ob_get_clean();

				} elseif ( $display == 'tasks' ) {

					$output .= psp_task_table( $id, 'psp-shortcode', $style );

				}

				$output .= '</div>';

			}

            wp_reset_query();

            return $output;

		} else {

			return '<p>'.__('No project with that ID','psp_projects').'</p>';

		}

}

/**
 * psp_single_project()
 *
 * Embed an entire project via shortcode
 *
 *
 * @param $atts['id] (int) : Post ID
 * @param $atts['overview'] (string) : If 'yes' include the project overview
 * @param $atts['progress'] (string) : If 'yes' include the project overall progress
 * @param $atts['milestones'] (string) : If 'yes' include the project milestones
 * @param $atts['phases'] (string) : If 'yes' include the project phases
 * @param $atts['tasks'] (string) : If 'yes' include the project tasks
 *
 * @return markup
 *
 */

add_shortcode( 'project_status', 'psp_single_project_shortcode' );
function psp_single_project_shortcode( $atts ) {

		psp_front_assets(1);

		extract( shortcode_atts(
			array(
				'id' 			=> '',
				'overview' 		=> 'yes',
				'progress' 		=> 'yes',
				'milestones'	=> 'condensed',
				'phases' 		=> 'yes',
				'tasks' 		=> 'yes'
			), $atts )
		);

		$project 			= new WP_Query( array( 'p' => $id, 'post_type'	=> 'psp_projects' ) );
		$panorama_access 	= panorama_check_access( $id );

		if( $project->have_posts() ) {

			ob_start();

			// Make sure the user has access to this project

			if( $panorama_access ) { ?>

					<?php while( $project->have_posts() ) { $project->the_post(); ?>

					<div id="psp-projects" class="psp-single-project psp-reset psp-theme-template psp-shortcode">

						<?php

						global $post;

						// Is the overview to be displayed?

						if($overview == 'yes'): ?>

                            <section id="overview" class="wrapper psp-section">

                                <?php do_action( 'psp_before_essentials' ); ?>

                                <?php do_action( 'psp_the_essentials' ); ?>

                                <?php do_action( 'psp_after_essentials' ); ?>

                            </section> <!--/#overview-->

                        <?php endif;

						if($progress == 'yes'): ?>

                            <?php do_action( 'psp_between_overview_progress' ); ?>

                            <section id="psp-progress" class="wrapper cf psp-section">

                                <?php do_action( 'psp_before_progress' ); ?>
                                <?php do_action( 'psp_the_progress' ); ?>
                                <?php do_action( 'psp_after_progress' ); ?>

                            </section> <!--/#progress-->

                            <?php do_action( 'psp_between_progress_phases' ); ?>

                        <?php
						endif;

						if($phases == 'yes'): ?>

                            <?php $phase_auto = get_field( 'phases_automatic_progress', get_the_ID() ); ?>

                            <section id="psp-phases" class="wrapper psp-section" data-phase-auto="<?php echo ( isset($phase_auto[0]) && $phase_auto[0] !== NULL ? $phase_auto[0] : 'No' ); ?>">

                                <?php do_action( 'psp_before_phases' ); ?>
                                <?php do_action( 'psp_the_phases', $id ); ?>
                                <?php do_action( 'psp_after_phases' ); ?>

                            </section>

						<?php endif;  ?>

						</div>

					<?php }

                    psp_translation_variables( true ); ?>

                    <script> <?php do_action('psp_js_variables'); ?> </script>

                    <?php
                    include( psp_template_hierarchy( 'global/navigation-off.php' ) ); ?>

			<?php } else { ?>

			<?php // TODO: This should be a global element ?>
			<div id="psp-projects" class="psp-shortcode">
				<div id="psp-login">
					<?php if(( ! $panorama_access ) && (get_field('restrict_access_to_specific_users',$id))): ?>
						<h2><?php _e('This Project Requires a Login','psp_projects'); ?></h2>
						<?php if(!is_user_logged_in()) {
							echo panorama_login_form();
						} else {
							echo "<p>".__('You don\'t have permission to access this project','psp_projects')."</p>";
						}
						?>
					<?php endif; ?>
					<?php if((post_password_required()) && (!current_user_can('manage_options'))): ?>
						<h2><?php _e('This Project is Password Protected','psp_projects'); ?></h2>
						<?php echo get_the_password_form(); ?>
					<?php endif; ?>
				</div>
			</div>
			<?php }

			return ob_get_clean();

               // All the resets
			wp_reset_postdata(); wp_reset_query();

		} else {

			return '<p>'.__('No project with that ID','psp_projects').'</p>';

		}

	}

/**
 * psp_single_project_admin_dialog_modal()
 *
 * Output the markup and javascript for managing the single project embed WYSIWYG button
 *
 */

add_action( 'admin_footer-post.php', 'psp_single_project_admin_dialog_modal' ); // Fired on the page with the posts table
add_action( 'admin_footer-edit.php', 'psp_single_project_admin_dialog_modal' ); // Fired on the page with the posts table
add_action( 'admin_footer-post-new.php', 'psp_single_project_admin_dialog_modal' ); // Fired on the page with the posts table

function psp_single_project_admin_dialog_modal() {

		$output = '

			<style type="text/css">
				#TB_Window { z-index: 9000 !important; }
			</style>

			<script>

				function psp_full_project() {

					jQuery("#psp-full-project-table").show();
					jQuery("#psp-part-project-table").hide();

				}

				function psp_part_project() {

					jQuery("#psp-full-project-table").hide();
					jQuery("#psp-part-project-table").show();


				}

				function psp_part_change() {

					target = jQuery("#psp-part-display").val();
					jQuery("tr.psp-part-option-row").hide();
					jQuery("#psp-part-" + target + "-option").show();

				}

				function psp_single_phases_select() {

					target = jQuery("#psp-single-phases").val();

					if(target == "yes") {
						jQuery(".psp-single-tasks-row").show();
					} else {
						jQuery(".psp-single-tasks-row").hide();
					}


				}

				jQuery(document).ready(function() {

					jQuery("#psp-full-project").attr("checked",false);
					jQuery("#psp-part-project").attr("checked",false);

					psp_single_phases_select();
					psp_part_change();

				});

			</script>
		';

		$output .= '<div class="psp-dialog" style="display:none">';
		$output .= '<div id="psp-single-project-diaglog">';
		$output .= '<h3>'.__('Insert a Project Overview','psp_projects').'</h3>';
		$output .= '<p>'.__('Select a project below to add it to your post or page.','psp_projects').'</p>';
		$output .= '<table class="form-table">';
		$output .= '<tr><th>Project</th><td>';
		$output .= '<div class="psp-loading"></div>';
		$output .= '<div id="psp-single-project-list"></div>';
		$output .= '</td></tr>';
		$output .= '<tr><th><label for="psp-display-style">Style</label></th><td><label for="psp-display-style"><input unchecked type="radio" name="psp-display-style" onClick="psp_full_project();" id="psp-full-project" value="full"> ' . __( 'Full Project', 'psp_projects' ) . '</label>&nbsp;&nbsp;&nbsp;<label for="psp-display-style"><input type="radio" unchecked name="psp-display-style" onClick="psp_part_project()" id="psp-part-project" value="part"> '.__('Portion of Project','psp_projects').'</label></td></tr>';
		$output .= '</table>';

		$output .= '<table class="form-table psp-hide-table" id="psp-full-project-table">';

		$output .= '<tr>
						<th><label for="psp-single-overview">'.__('Overview','psp_projects').'</label></th>
						<td>
							<select id="psp-single-overview">
								<option value="yes">'.__('Show Overview','psp_projects').'</option>
								<option value="no">'.__('No Overview','psp_projects').'</option>
							</select>
						</td>
					</tr>';

		$output .= '<tr>
						<th><label for="psp-single-progress">'.__('Progress Bar','psp_projects').'</label></th>
						<td>
							<select id="psp-single-progress">
								<option value="yes">'.__('Show Progress Bar','psp_projects').'</option>
								<option value="no">'.__('No Progress Bar','psp_projects').'</option>
							</select>
						</td>
					</tr>';

		$output .= '<tr>
						<th><label for="psp-single-milestones">'.__('Milestones','psp_projects').'</label></th>
						<td>
							<select id="psp-single-milestones">
								<option value="condensed">'.__('Condensed','psp_projects').'</option>
								<option value="full">'.__('Full Width','psp_projects').'</option>
								<option value="no">'.__('No Milestones','psp_projects').'</option>
							</select>
						</td>
					</tr>';

		$output .= '<tr>
						<th><label for="psp-single-phases">Phases:</label></td>
						<td>
							<select id="psp-single-phases" onChange="psp_single_phases_select();">
								<option value="yes">'.__('Show Phases','psp_projects').'</option>
								<option value="no">'.__('No Phases','psp_projects').'</option>
							</select>
						</td>
					</tr>';

		$output .= '<tr class="psp-single-tasks-row">
						<th><label for="psp-single-tasks">'.__('Tasks','psp_projects').'</label></th>
						<td>
							<select id="psp-single-tasks">
								<option value="yes">'.__('All Tasks','psp_projects').'</option>
								<option value="incomplete">'.__('Incomplete Only','psp_projects').'</option>
								<option value="complete">'.__('Completed Only','psp_projects').'</option>
								<option value="no">'.__('No Tasks','psp_projects').'</option>
							</select>
						</td>
						<td colspan="2">&nbsp;</td>
					</tr>
				</table>';

		$output .= '<table class="form-table psp-hide-table" id="psp-part-project-table">
						<tr>
							<th><label for="psp-part-display">'.__('Display','psp_projects').'</label></th>
							<td>
								<select id="psp-part-display" onChange="psp_part_change();">
									<option value="overview">' . __( 'Overview','psp_projects').'</option>
									<option value="documents">' . __( 'Documents','psp_projects').'</option>
									<option value="progress">' . __( 'Overall Progress','psp_projects').'</option>
									<option value="phases">' . __( 'Phases','psp_projects').'</option>
									<option value="tasks">' . __( 'Tasks','psp_projects').'</option>
								</select>
							</td>
						</tr>
						<tr id="psp-part-progress-option" class="psp-part-option-row">
							<th><label for="psp-part-overview-progress-select">'.__('Milestones','psp_projects').'</label></th>
							<td><select id="psp-part-overview-progress-select">
									<option value="full">'.__('Full Width','psp_projects').'</option>
									<option value="condensed">'.__('Condensed','psp_projects').'</option>
									<option value="no">'.__('None','psp_projects').'</option>
								</select>
							</td>
						</tr>
						<tr id="psp-part-phases-option" class="psp-part-option-row">
							<th><label for="psp-part-phases-select">'.__('Tasks','psp_projects').'</label></th>
							<td><select id="psp-part-phases-select">
									<option value="all">'.__('All Tasks','psp_projects').'</option>
									<option value="complete">'.__('Completed Tasks','psp_projects').'</option>
									<option value="incomplete">'.__('Incomplete Tasks','psp_projects').'</option>
									<option value="no">'.__('None','psp_projects').'</option>
								</select>
							</td>
						</tr>
						<tr id="psp-part-tasks-option" class="psp-part-option-row">
							<th><label for="psp-part-tasks-select">'.__('Show','psp_projects').'</label></th>
							<td><select id="psp-part-tasks-select">
									<option value="tasks">'.__('All Tasks','psp_projects').'</option>
									<option value="completed">'.__('Completed','psp_projects').'</option>
									<option value="open">'.__('Incomplete','psp_projects').'</option>
								</select>
							</td>
						</tr>
					</table>
			';


		$output .= '<p><input class="button-primary" type="button" onclick="InsertPspProject();" value="'.__('Insert Project','psp_projects').'"> <a class="button" onclick="tb_remove(); return false;" href="#">'.__('Cancel','psp_projects').'</a></p>';
		$output .= '</div></div>';

		echo $output;

	}

/**
 * psp_ajax_project_list()
 *
 * Dynamically populate the project list for embed modals to prevent slow admin experience
 *
 */

add_action( 'wp_ajax_psp_get_projects', 'psp_ajax_get_project_list' );
function psp_ajax_get_project_list() { ?>

	<?php
	$args = array(
		'post_type'			=>		'psp_projects',
		'posts_per_page'	=>		-1,
	);

	$projects = new WP_Query( $args ); ?>

	<p>
		<select id="psp-single-project-id">

			<?php while( $projects->have_posts() ) { $projects->the_post(); global $post; ?>

				<option value="<?php echo $post->ID; ?>"><?php the_title(); ?></option>

			<?php } ?>

		</select>
	</p>

<?php }


add_action( 'admin_footer-post.php', 'psp_project_listing_dialog' ); // Fired on the page with the posts table
add_action( 'admin_footer-edit.php', 'psp_project_listing_dialog' ); // Fired on the page with the posts table
add_action( 'admin_footer-post-new.php', 'psp_project_listing_dialog' ); // Fired on the page with the posts table


/**
 * psp_populate_user_dashboard_widget()
 *
 * Create the dashboard widget with project completion breakdown and recently updated projects
 * Also usable with shortcode [user-dashboard-widget]
 *
 */

add_shortcode( 'user-dashboard-widget', 'psp_populate_user_dashboard_widget' );
function psp_populate_user_dashboard_widget() {

	psp_front_assets(1);

	$cuser 			= wp_get_current_user();
	$cid 			= $cuser->ID;
	$projects 		= get_posts( array(
								'post_type' 		=> 'psp_projects',
								'posts_per_page' 	=> '-1',
								'meta_query' 		=> psp_access_meta_query( $cid ),
	                			)
	            			);

	$total_projects 	= count( $projects );
	$taxonomies 		= get_terms( 'psp_tax' , 'fields=count' );
	$completed_projects = 0;
	$not_started 		= 0;
	$active 			= 0;

	foreach( $projects as $project ) {

		if( get_post_meta( $project->ID, '_psp_completed', true ) == '1' ) {
			$completed_projects++;
		}

		if( psp_compute_progress( $project->ID ) == 0) {
			$not_started++;
		} else {
			$active++;
		}

	}

	// Calculate percentage of complete projects
	if( $completed_projects > 0 && $total_projects > 0 ) {
		$percent_complete = floor( $completed_projects / $total_projects * 100 );
	} else {
		$percent_complete = 0;
	}

	// Calculate percentage of projects that haven't been started yet
    if( $not_started > 0 && $total_projects > 0 ) {
        $percent_not_started = floor( $not_started / $total_projects * 100 );
    } else {
        $percent_not_started = 0;
    }

	 // Calculate percent remaining
	 if( $percent_complete > 0 && $total_projects > 0 ) {
		 $percent_remaining = 100 - $percent_complete - $percent_not_started;
	} else {
		$percent_remaining = 100;
	}

	ob_start(); ?>

		<div class="psp-chart">
			<canvas id="psp-dashboard-chart" width="100%" height="150"></canvas>
		</div>

		<script>

	        jQuery(document).ready(function() {

				var chartOptions = {
					responsive: true
				}

	            var data = [
	                {
	                    value: <?php echo $percent_complete; ?>,
	                    color: "#2a3542",
	                    label: "Completed"
	                },
	                {
	                    value: <?php echo $percent_remaining; ?>,
	                    color: "#3299bb",
	                    label: "In Progress"
	                },
	                {
	                    value: <?php echo $percent_not_started; ?>,
	                    color: "#666666",
	                    label: "Not Started"
	                }
	            ];


	            var psp_dashboard_chart = document.getElementById("psp-dashboard-chart").getContext("2d");

	            new Chart(psp_dashboard_chart).Doughnut(data,chartOptions);

	        });

		</script>

    	 <ul class="psp-projects-overview">
			<li><span class="psp-dw-projects"><?php esc_html_e($total_projects); ?></span> <strong><?php _e( 'Projects', 'psp_projects' ); ?></strong> </li>
			<li><span class="psp-dw-completed"><?php esc_html_e($completed_projects); ?></span> <strong><?php _e( 'Completed', 'psp_projects' ); ?></strong></li>
			<li><span class="psp-dw-active"><?php esc_html_e($active); ?></span> <strong><?php _e( 'Active', 'psp_projects' ); ?></strong></li>
			<li><span class="psp-dw-types"><?php esc_html_e($taxonomies); ?></span> <strong><?php _e( 'Types', 'psp_projects' ); ?></strong></li>
    	  </ul>

		<?php
	    return ob_get_clean();

}


add_shortcode('task-list','psp_output_my_tasks_shortcode');
function psp_output_my_tasks_shortcode( $atts ) {

    extract( shortcode_atts(
		array(
			'columns' => 'no'
		), $atts )
	);

	return '<div id="psp-projects" class="psp-theme-template">' . psp_output_my_tasks( $columns ) . '</div>';

}


function psp_output_my_tasks($columns = 'true') {

	// Make sure the user is logged in first
	if( is_user_logged_in() ):

		// Get the current logged in WordPress user object
		$cuser = wp_get_current_user();

		// Query all the projects where this user has been assigned a task
		$args = array(
			'post_type'		=>		'psp_projects',
			'limit'			=>		-1,
			'tax_query' 	=> array(
					array(
						'taxonomy'	=>	'psp_status',
						'field'		=>	'slug',
						'terms'		=>	'completed',
						'operator'	=>	'NOT IN'
					)
			),
            'meta_query' 	=> array(
                'key' 		=> 'tasks_%_assigned',
                'value' 	=> $cuser->ID,
            )
		);

		// Query with the above arguments
		$projects = new WP_Query($args);

			if($projects->have_posts()):

		        psp_front_assets(1);

    			ob_start(); ?>

    			<div class="psp-my-tasks">

    				<input id="psp-ajax-url" type="hidden" value="<?php echo admin_url(); ?>admin-ajax.php">

    				<?php
    				while($projects->have_posts()): $projects->the_post();

    					global $post;
    					$phases 		= NULL;
    					$task_id 		= 0;
    					$phase_count 	= 0;

    					// Loop through the phases
    					while(have_rows('phases')): the_row();

    						$phase_name = get_sub_field('title');
    						$tasks 		= NULL;

    						// Loop through the tasks
    						while(have_rows('tasks')): the_row();

    							$assigned = get_sub_field('assigned');

    							if($assigned == $cuser->ID) {

    								$overall_auto = get_field('automatic_progress',$post->ID);

    $link = '<a href="#edit-task-'.$task_id.'" class="task-edit-link"><b class="fa fa-pencil"></b> '.__('update','psp_projects').'</a> <a href="#" class="complete-task-link" data-target="'.$task_id.'" data-task="'.$task_id.'" data-task="'.$task_id.'" data-phase="'.$phase_count.'" data-project="'.$post->ID.'" data-phase-auto="null" data-overall-auto="'.$overall_auto[0].'"><b class="fa fa-check"></b> '.__('complete','psp_projects').'</a></strong>';

    								$tasks .= '<li class="task-item task-item-'.$task_id.'">'.get_sub_field('task').' '.$link.' <span class="psp-task-bar" data-progress="'.get_sub_field('status').'"><em class="status psp-'.get_sub_field('status').'"></em></span>';

    								$tasks .= '<div id="edit-task-'.$task_id.'" class="task-select">

                                    <select id="edit-task-select-'.$phase_count.'-'.$task_id.'" class="edit-task-select">
                                        <option value="'.get_sub_field('status',$post->ID).'">'.get_sub_field('status',$post->ID).'%</option>
                                        <option value="0">0%</option>
                                        <option value="10">10%</option>
                                        <option value="20">20%</option>
                                        <option value="30">30%</option>
                                        <option value="40">40%</option>
                                        <option value="50">50%</option>
                                        <option value="60">60%</option>
                                        <option value="70">70%</option>
                                        <option value="80">80%</option>
                                        <option value="90">90%</option>
                                        <option value="100">100%</option>
                                    </select>

    								<input type="submit" name="save" value="save" class="task-save-button" data-task="'.$task_id.'" data-phase="'.$phase_count.'" data-project="'.$post->ID.'" data-phase-auto="null" data-overall-auto="'.$overall_auto[0].'" data-userid="'.$cuser->ID.'">

                                	</div>
    							</li>';

    							}

    							$task_id++;

    						endwhile;

    						if(!empty($tasks)):
    							$phases .= '<div class="psp-tasks-phase">
    											<h3>'.$phase_name.'</h3>
    										</div>
    										<ul>
    											'.$tasks.'
    										</ul>';
						    endif;

					endwhile; // End phases loop

					if(!empty($phases)): ?>

						<?php $phases_and_tasks = psp_get_item_count($post,$cuser->ID);
						global $post; ?>

						<div class="psp-task-project <?php if($columns != 'no') { echo 'psp-col-lg-4 psp-col-md-6'; } ?> psp-task-project-<?php echo $post->ID; ?>">
							<div class="psp-task-project-wrapper">

								<h2 class="psp-task-project-title">
									<a href="<?php the_permalink(); ?>"><b><?php the_title(); ?></b> <strong><?php the_field('client'); ?></strong></a>
								</h2>

								<div class="psp-task-project-details">

									<h4><?php _e('Project Details','psp_projects'); ?></h4>

									<div class="psp-task-section">

										<p class="psp-progress"><span class="psp-<?php echo psp_compute_progress($post->ID); ?>"><b><?php echo psp_compute_progress($post->ID); ?>%</b></span></p>

										<?php echo psp_the_timebar($post->ID); ?>

									</div> <!--/.psp-tpd-content-->

								</div> <!--/.psp-task-project-details-->

								<div class="psp-task-project-tasks">

									<div class="psp-task-section">

										<ul class="psp-grid-row cf">
											<li class="psp-col-xs-4 psp-element-tally psp-element-tally-all" data-count="<?php echo $phases_and_tasks['tasks']; ?>">
												<strong><?php echo $phases_and_tasks['tasks']; ?></strong>
												<span><?php _e('Tasks<br>Assigned','psp_projects'); ?></span>
											</li>
											<li class="psp-col-xs-4 psp-element-tally psp-element-tally-started" data-count="<?php echo $phases_and_tasks['started']; ?>">
												<strong><?php echo $phases_and_tasks['started']; ?></strong>
												<span><?php _e('Tasks<br>Started','psp_projects'); ?></span>
											</li>
											<li class="psp-col-xs-4 psp-element-tally psp-element-tally-completed" data-count="<?php echo $phases_and_tasks['completed']; ?>">
												<strong><?php echo $phases_and_tasks['completed']; ?></strong>
												<span><?php _e('Tasks<br>Complete','psp_projects'); ?></span>
											</li>
										</ul>


										<?php echo $phases; ?>

									</div>

								</div> <!--/.psp-task-project-tasks-->

							</div>
						</div>

					<?php endif;

				// End query loop
				endwhile; ?>

			</div>

		<?php endif; ?>

	<?php endif;


	return ob_get_clean();

}

add_shortcode( 'before-phase', 'psp_before_phase' );
function psp_before_phase( $atts, $content = NULL ) {

	return '<div class="psp-before-phase">' . $content . '</div>';

}

add_shortcode( 'during-phase', 'psp_during_phase' );
function psp_during_phase( $atts, $content = NULL ) {

	return '<div class="psp-during-phase">' . $content . '</div>';

}

add_shortcode( 'after-phase', 'psp_after_phase' );
function psp_after_phase( $atts, $content = NULL ) {

	return '<div class="psp-after-phase">' . $content . '</div>';

}

add_shortcode( 'before-milestone', 'psp_before_milestone_shortcode' );
function psp_before_milestone_shortcode( $atts, $content = NULL ) {

    return '<div class="psp-before-milestone">' . wpautop( $content ) . '</div>';

}

add_shortcode( 'after-milestone', 'psp_after_milestone_shortcode' );
function psp_after_milestone_shortcode( $atts, $content = NULL ) {

    return '<div class="psp-after-milestone">' . wpautop( $content ) . '</div>';

}

add_shortcode( 'psp-upcoming-tasks', 'psp_all_my_tasks_shortcode' );
function psp_all_my_tasks_shortcode( $atts, $shortcode = TRUE ) {

    /*
     * Setup defaults
     */

    $cuser      = wp_get_current_user();
    $user_id    = ( !empty( $atts[ 'id' ] ) ? $atts[ 'id' ] : $cuser->ID );
    $amount     = ( !empty( $atts[ 'count' ] ) ? $atts[ 'count' ] : 10 );
    $tasks      = array();

    global $post;

    $post_id = $post->ID;

    if( isset($post->post_content ) ) {
         $shortcode = ( has_shortcode( $post->post_content, 'psp-upcoming-tasks' ) ? true : false );
    } else {
         $shortcode = false;
    }

    $class = 'psp-table psp-task-table ' . ( $shortcode ? 'is-shortcode' : 'not-shortcode' );

    psp_front_assets( 1 );

    ob_start();

	// Gets all Assigned Tasks. Each index is a new Project
	// Similarly to the Dashboard though, this is only used for determining which Projects we have Assigned Tasks in.
	// We have to use psp_get_tasks() in order to get some more data on them
    $tasks_query = psp_get_all_my_tasks( $user_id );

    if ( $tasks_query ) {

        foreach ( $tasks_query as $task_group ) {

			$phases = array();
			$phase_id = 0;

            // Skip any unpublished projects
            if ( get_post_status( $task_group['project_id'] ) != 'publish' ) continue;

			$phases	= get_field( 'phases', $task_group['project_id'] );

			while ( have_rows( 'phases', $task_group['project_id'] ) ) {

				$phase = the_row();

				$get_tasks = psp_get_tasks( $task_group['project_id'], $phase_id, 'incomplete' );

				if ( ! empty( $get_tasks ) ):

					foreach ( $get_tasks as $task ) {

                              $show_task = false;

                              if( is_array($task[ 'assigned' ]) && in_array( $cuser->ID, $task[ 'assigned' ] ) ) {
                                   $show_task = true;
                              } elseif( $task[ 'assigned' ] == $cuser->ID ) {
                                   $show_task = true;
                              }

						$show_task = apply_filters( 'psp_show_task_on_user_tasks', $show_task, $phase, $task );

						if ( $show_task ) {

                                   $status = apply_filters( 'psp_task_status', intval( $task['status'] ), $post_id, $phase_id, $task['task_id'], $task );

							$tasks[] = array(
								'name'      =>  $task['task'],
								'ID'        =>  $task['ID'], // Legacy, is actually an Index
								'task_id'	=>	$task['task_id'],
								'phase_id'  =>  $phase_id,
								'post_id'   =>  $task_group['project_id'],
								'phases'    =>  $phases,
								'phase'     =>  $phase['field_527d5dd02fa2a'],
								'due_date'  =>  $task[ 'due_date' ],
                                        'task_description' => $task['task_description'],
								'status'    =>  $status,
								'assigned'	=>	$task['assigned'],
							);

						}

					}

				endif;

				$phase_id++;

			}

        }

        $tasks      = array_intersect_key( $tasks, array_unique( array_map( 'serialize', $tasks ) ) );
        $sort_type  = apply_filters( 'psp_upcoming_tasks_sort_function', 'psp_sort_tasks_by_due_date' );

        usort( $tasks, $sort_type );

        $tasks = array_reverse($tasks);

        if( isset($_GET['sort']) && $_GET['sort'] == 'DESC' ) {
            $tasks = array_reverse($tasks);
        }

        // $tasks = array_reverse( $tasks ); ?>
        <?php if( $shortcode == TRUE ): ?>
            <div id="psp-projects" class="psp-shortcode">
        <?php endif; ?>

            <input type="hidden" id="psp-ajax-url" value="<?php echo admin_url(); ?>admin-ajax.php">

            <?php
            $table_headings = apply_filters( 'psp_all_my_tasks_table_headings', array(
                __( '', 'psp_projects' ),
                __( 'Task', 'psp_projects' ),
                __( 'Due Date', 'psp_projects' ),
            ) ); ?>

            <?php if( $shortcode ): ?>
                <table class="<?php echo esc_attr($class); ?>">
                    <thead>
                        <tr>
                            <?php foreach( $table_headings as $heading ): ?>
                                <th><?php echo esc_html( $heading ); ?></th>
                            <?php endforeach; ?>
                        </tr>
                    </thead>
                <?php endif; ?>
                <tbody>
                    <?php
                    if( empty( $tasks ) ): ?>
                        <tr>
                            <td colspan="4">
                                <div class="psp-notice"><?php esc_html_e( 'There are no active tasks assigned at this time.','psp_projects' ); ?></div>
                                <br>
                            </td>
                        </tr>
                    <?php else:
                        foreach( $tasks as $task ):

                            $link_attributes = array(
                                'target'    =>  $task['ID'],
                                'task'      =>  $task['ID'],
                                'phase'     =>  $task['phase_id'],
                                'project'   =>  $task['post_id'],
                            );

							$task_class = 'task-row project-' . esc_attr( $task['post_id'] ) . ' psp-task-id-' . $task['task_id'] . ' task-row-' . esc_attr( $task[ 'ID' ] );

							$phases = get_field( 'phases', $task['post_id'] );

							$date 	= strtotime( $task[ 'due_date' ] );
							$format = get_option( 'date_format' );
                            $phase  = $phases[$task['phase_id']];

							$task_panel_atts = apply_filters( 'psp_task_panel_your_tasks_attributes', array(
								'task_index'		=> $task['ID'],
								'task_id'			=> $task['task_id'],
                                        'task_description'  => $task['task_description'],
								'phase_index'		=> $task['phase_id'],
								'phase_id'		=> $phase['phase_id'],
								'project'			=> $task['post_id'],
								'project_name'		=> get_the_title( $task['post_id'] ),
								'due_date'			=>	date_i18n( $format, $date ),
								'assigned_name'		=>	psp_username_by_id( $task['assigned'] ),
								'project_permalink' =>	get_permalink( $task['post_id'] ),
							), $task['post_id'], $task['phase_id'], $task['ID'] );

							$task_documents = psp_parse_task_documents( get_field( 'documents', $task['post_id'] ), $task['task_id'] );
							$task_document_count = ( $task_documents ) ? count( $task_documents ) : 0;

							$task_comment_count = psp_get_task_comment_count( $task['task_id'], $task['post_id'] ); ?>

                            <tr class="<?php echo esc_attr( apply_filters( 'psp_task_classes', $task_class, $task['post_id'], $task['phase_id'], $task['ID'], $phases, $phases[ $task['phase_id'] ] ) ); ?>" data-task_index="<?php echo esc_attr( $task['ID'] ); ?>" data-task_id="<?php echo esc_attr( $task['task_id'] ); ?>" data-phase_id="<?php echo esc_attr( $phase['phase_id'] ); ?>" data-progress="<?php echo esc_attr( $task['status'] ); ?>">
                                <td class="psp-task-table-complete-link">
                                    <?php if( psp_can_edit_task( $task['post_id'], $task['phase_id'], $task['ID'] ) ): ?>
                                        <a href="#" class="complete-task-link task-table"
                                            <?php foreach( $link_attributes as $attribute => $value ): ?>
                                                data-<?php echo $attribute; ?>="<?php echo esc_attr( $value ); ?>"
                                            <?php endforeach; ?> >
                                            <b class="fa fa-check"></b>
                                        </a>
                                    <?php endif; ?>
                                </td>
                                <td class="psp-task-table-body">
                                     <span class="psp-task-breadcrumbs">

                                          <?php if( get_field( 'client', $task['post_id'] ) ): ?>
                                           <span class="psp-task-meta-item psp-tmi-client"><?php the_field( 'client', $task['post_id'] ); ?></span>
                                         <?php endif; ?>

                                         <a href="<?php echo esc_url( get_the_permalink($task['post_id']) ); ?>"><?php echo esc_html( get_the_title($task['post_id']) ); ?></a>
                                         &rangle;
                                         <a href="<?php echo esc_url( get_the_permalink($task['post_id']) . '#phase-' . intval($task_panel_atts['phase_index'] + 1 ) ); ?>"><?php echo esc_html( $phases[$task['phase_id']]['title']); ?></a>

                                     </span>

                                    <a class="psp-task-table-link psp-task-title" href="#psp-offcanvas-task-details"
									<?php foreach( $task_panel_atts as $att => $val ): ?>
										data-<?php echo $att; ?>="<?php echo esc_attr( $val ); ?>"
									<?php endforeach; ?>
										>

                                        <span class="psp-task-title__name">
                                             <strong><?php echo wp_kses_post( $task['name'] ); ?></strong>

                                             <span class="psp-task-meta after-task-name">
                                                 <span class="psp-task-documents">
                                                     <i class="fa fa-files-o"></i>&nbsp;
                                                     <span class="text"><?php echo esc_html( $task_document_count ); ?></span>
                                                 </span>
                                                 <?php
                                                 // We always "show" comment count since this could be updated from 0 to 1 ?>
                                                 <span class="psp-task-discussions <?php echo ( $task_comment_count <= 0 ) ? ' hidden' : ''; ?>">
                                                     <i class="psp-fi-icon psp-fi-discussion"></i>
                                                     <span class="text"><?php echo esc_html( $task_comment_count ); ?></span>
                                                 </span>
                                             </span>
                                        </span>

							</a>

                                   <?php

                                   do_action( 'psp_my_tasks_before_assigned', $task, $phase, $phases, $post_id  );

                                   if( !empty($task['assigned']) ):
                                        ?>
                                        <span class="psp-task-assigned">
                                             <?php
                                             do_action( 'psp_my_tasks_after_assigned_open', $task, $phase, $phases, $post_id );

                                             if( is_array( $task['assigned'] ) ): ?>
                                                  <?php foreach( $task['assigned'] as $user ): ?>
                                                       <b class="psp-assigned-to">
                                                            <?php
                                                            echo get_avatar( $user ); ?>
                                                            <span class="text"><?php echo apply_filters( 'psp_task_assigned', psp_username_by_id( $user ), $post_id, $phase_index, $task[ 'ID' ] ); ?></span>
                                                       </b>
                                                  <?php endforeach; ?>
                                             <?php else: ?>
                                                  <b class="psp-assigned-to">
                                                       <?php
                                                       echo get_avatar( $task['assigned'] ); ?>
                                                       <span class="text"><?php echo apply_filters( 'psp_task_assigned', psp_username_by_id( $task['assigned'] ), $post_id, $phase_index, $task[ 'ID' ] ); ?></span>
                                                  </b>
                                             <?php endif;
                                             do_action( 'psp_my_tasks_before_assigned_close', $task, $phase, $phases, $post_id );?>
                                        </span>
                                        <?php
                                   endif;

                                   do_action( 'psp_my_tasks_after_assigned', $task, $phase, $phases, $post_id ); ?>

                                   <div class="psp-task-table-status task-item-<?php echo esc_attr( $task['ID'] ); ?>">
                                       <?php if( psp_can_edit_task( $task[ 'post_id' ], $task[ 'phase_id' ], $task[ 'ID' ] ) ): ?>
                                           <span class="psp-task-edit-links">
                                                <span class="psp-task-edit-links__actions">
                                                   <a href="#edit-task-<?php echo $task[ 'ID' ]; ?>" class="task-table-edit-link"><b class="fa fa-adjust"></b> <?php esc_html_e( 'update', 'psp_projects' ); ?></a>
                                                   <a href="#" class="complete-task-link"
                                                        <?php foreach( $link_attributes as $attribute => $value ): ?>
                                                            data-<?php echo $attribute; ?>="<?php echo esc_attr( $value ); ?>"
                                                        <?php endforeach; ?> >
                                                        <b class="fa fa-check"></b>
                                                        <?php esc_html_e( 'complete', 'psp_projects' ); ?>
                                                   </a>
                                              </span>
                                              <span id="<?php echo esc_attr('#edit-task-' . $task['ID'] ); ?>" class="task-select">
                                                <?php $select_options = psp_get_status_percentages(); ?>
                                                <span class="psp-select-wrapper">
                                                     <select id="edit-task-select-<?php echo $task[ 'phase_id' ] . '-' . $task[ 'ID' ]; ?>" class="edit-task-select">
                                                          <option value="<?php echo esc_attr( $task['status'] ); ?>"><?php echo $task['status']; ?>%</option>
                                                          <?php foreach( $select_options as $value => $label ): ?>
                                                             <option value="<?php echo esc_attr( $value ); ?>"><?php echo esc_html( $label ); ?></option>
                                                          <?php endforeach; ?>
                                                     </select>
                                                </span>
                                                <input type="submit" name="save" value="save" class="task-save-button"
                                                     <?php foreach( $link_attributes as $attribute => $value ): ?>
                                                        data-<?php echo $attribute; ?>="<?php echo esc_attr( $value ); ?>"
                                                     <?php endforeach; ?>>
                                            </span> <!--/.task-select-->
                                          </span> <!--/.psp-task-edit-links-->
                                       <?php endif; ?>
                                       <span class="psp-progress-bar"><em class="status psp-<?php echo esc_attr( $task[ 'status' ] ); ?>"></em></span>
                                   </div>

                                </td>
                                <td class="psp-my-class-due">
                                    <?php
                                    if( !empty($task['due_date']) ):
                                        $date_class = 'psp-task-due-date ' . ( strtotime( $task['due_date'] ) < strtotime( 'today' ) ? 'late' : '' ); ?>
                                        <span class="<?php echo esc_attr($date_class); ?>">
                                            <i class="psp-fi-icon psp-fi-calendar"></i>
                                            <?php echo date_i18n( get_option('date_format'), strtotime($task['due_date']) ); ?>
                                        </span>
                                    <?php endif; ?>
                                </td>
                            </tr>
							<?php
                            $task_phase = ( isset($phase[$task['phase_id']]) ? $phase[$task['phase_id']] : null );
                            do_action( 'psp_all_my_tasks_shortcode_after_task', $task['post_id'], $task['phase_id'], $task['ID'], $task['phases'], $task_phase ); ?>
                        <?php endforeach;
                    endif; ?>
                </tbody>

        <?php if( $shortcode == TRUE ): ?>
                </table>
            </div>
        <?php endif; ?>

   <?php } else { ?>
        <tr>
          <td colspan="4">
               <div class="psp-notice"><?php esc_html_e( 'There are no active tasks assigned at this time.','psp_projects' ); ?></div>
               <br>
          </td>
       </tr>
    <?php }

    return ob_get_clean();

}

function psp_sort_tasks_by_due_date($a, $b) {
    if( !is_int( $a['due_date'] ) || !is_int( $b['due_date'] ) ) {
        return 0;
    }
    return $a['due_date'] - $b[ 'due_date' ];
}



add_shortcode( 'psp-dashboard', 'psp_archive_dashboard_shortcode' );
function psp_archive_dashboard_shortcode() {

    psp_front_assets(1);

    $count     = apply_filters( 'psp_archive_project_listing_count', ( isset($_GET['count']) ? $_GET['count'] : get_option('posts_per_page') ) );
    $status    = apply_filters( 'psp_archive_project_listing_status', ( get_query_var('psp_status_page') ? get_query_var('psp_status_page') : 'active' ) );
    $paged     = ( get_query_var('paged') ? get_query_var('paged') : 1 );
    $args      = apply_filters( 'psp_archive_project_listing_args', psp_setup_all_project_args($_GET) );
    $projects	= psp_get_all_my_projects( $status, $count, $paged, $args );
    $tasks 		= psp_get_all_my_tasks();

    include( psp_template_hierarchy('dashboard/shortcode') );

}

add_shortcode( 'psp-calendar', 'psp_calendar_shortcode' );
function psp_calendar_shortcode( $atts ) {

     psp_front_assets(true);

     psp_enqueue_calendar_assets();

     wp_register_style( 'psp-calendar-public', plugins_url() . '/' . PSP_PLUGIN_DIR . '/dist/assets/css/psp-calendar-public.css', array(), PSP_VER );
     wp_enqueue_style( 'psp-calendar-public' );

     $user_id = ( isset($atts['user_id']) ? $atts['user_id'] : null );
     $project_id = ( isset($atts['project_id']) ? $atts['project_id'] : null );

     return '<div id="psp-projects" class="psp-shortcode">' . psp_output_project_calendar( $user_id, $project_id ) . '</div>';

     // echo psp_output_project_calendar( $user_id, $project_id );

}
