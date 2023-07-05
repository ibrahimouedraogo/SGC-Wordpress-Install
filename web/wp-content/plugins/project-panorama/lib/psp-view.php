<?php

/**
 * Call the psp_essentials function and echo it to the screen. Adds it to the page using the psp_the_essentials hook
 *
 *
 * @param NULL
 * @return NULL
 **/

add_action( 'psp_the_essentials', 'psp_echo_essentials' );
function psp_echo_essentials() {

    global $post;
    echo psp_essentials( $post->ID );

}

/**
 * Outputs all the overview information to the page
 *
 *
 * @param $id, int post ID. $style string, $docs string
 * @return HTML output
 **/

function psp_essentials( $id = null, $style = null, $docs = null ) {

    ob_start();

	include( psp_template_hierarchy( '/projects/overview/index.php' ) );

    return ob_get_clean();

}


/**
 * Outputs a doughnut chart of all project progress
 *
 *
 * @param $id (current post ID)
 * @return HTML output
 **/

function psp_short_progress( $id ) {

    include( psp_template_hierarchy( '/parts/short-progress.php' ) );

}


function psp_echo_total_progress( $post_id = NULL ) {

    global $post;

    $post_id = ( isset( $post_id ) ? $post_id : $post->ID );

	echo psp_total_progress( $post_id );

}

/* Use an action to add the progress indicator to the template */
add_action( 'psp_the_progress', 'psp_echo_the_milestones' );
function psp_echo_the_milestones( $post_id = null ) {

    global $post;

    $post_id = ( isset( $post_id ) ? $post_id : $post->ID );

	echo psp_the_milestones( $post_id );

}

function psp_the_milestones( $id, $style = null, $options = null ) {

    ob_start();
if(is_user_logged_in()){

	if( get_option( 'psp_database_version' ) < 4) {

		include( psp_template_hierarchy( '/projects/milestones/dep/index.php' ) );

	} else {

		include( psp_template_hierarchy( '/projects/milestones/index.php' ) );

	}
}

    return ob_get_clean();

}

function psp_total_progress( $id, $style = null, $options = null ) {

    ob_start();

    $completed = psp_compute_progress( $id ); ?>

    <div id="psp-short-progress">
    	<p class="psp-progress">
    		<span class="psp-<?php echo esc_attr($completed); ?>" data-toggle="psp-tooltip" data-placement="top" title="<?php echo esc_attr($completed . '% ' . __( 'Complete', 'psp_projects' ) ); ?>">
    			<b><?php echo esc_html($completed); ?>%</b>
    		</span>
    		<i class="psp-progress-label"> <?php esc_html_e('Progress','psp_projects'); ?> </i>
    	</p>
    </div>

    <?php
    return ob_get_clean();

}

function psp_get_phase_completion( $tasks, $id ) {

    $completed 			= 0;
    $task_count 		= 0;
    $task_completion	= 0;

    if( get_field( 'phases_automatic_progress', $id ) ) {

        foreach( $tasks as $task ) {

            $task_count++;

			$task_completion += $task[ 'status' ];

		}

        if( $task_count >= 1 ) {

            $completed += ceil( $task_completion / $task_count );

		} elseif ( $task_count == 1 ) {

		    $completed = 0;

		} else {

		    $completed += $task_completion;

		}

        return $completed;

    }

}

function psp_get_phase_completed( $id, $post_id = null ) {

    $post_id = ( $post_id == null ? get_the_ID() : $post_id );

    $completed 			= 0;
    $tasks 				= 0;
    $task_completion 	= 0;
    $completed_tasks 	= 0;

    $phases             = get_field( 'phases', $post_id );
    $tasks_array        = $phases[$id]['tasks'];

    if( empty($tasks_array) &&  get_field( 'phases_automatic_progress', $post_id ) ) {
        return array(
            'completed'         =>  0,
            'tasks'             =>  0,
            'completed_tasks'   =>  0
        );
    }

    if( get_field( 'phases_automatic_progress', $post_id ) ) {

        if( is_array( $tasks_array ) ) {
            foreach( $tasks_array as $task ) {

                $tasks++;

                $task_completion += apply_filters( 'psp_task_status', intval($task['status']), $post_id, $id, $task['task_id'], $task );

                if( $task['status'] == '100' ) $completed_tasks++;

            }
        }

        if( $tasks >= 1 ) {
			$completed += ceil( $task_completion / $tasks );
		} elseif ( $tasks == 1 ) {
			$completed = 0;
		} else {
			$completed += $task_completion;
		}

    } else {

        if( is_array($tasks_array) ) {
            foreach( $tasks_array as $task ) {
               $tasks++;
    			$task_completion += intval($task['status']);
    			if( $task['status'] == '100' ) $completed_tasks++;
		    }
        }

        $completed = $phases[$id]['percent_complete'];

    }

    return array(
        'completed'         =>  intval($completed),
        'tasks'             =>  $tasks,
        'completed_tasks'   =>  $completed_tasks
    );

}



add_action( 'psp_the_phases', 'psp_echo_phases', 10, 1 );
function psp_echo_phases( $post_id = null ) {

    $post_id = ( $post_id == null ? get_the_ID() : $post_id );

    echo psp_phases( $post_id );

}

function psp_phases( $post_id, $style = null, $task_style = null ) {

    ob_start();
    if(is_user_logged_in()){
    include( psp_template_hierarchy( 'projects/phases/index.php' ) );
    }
    return ob_get_clean();

}

add_action( 'psp_the_discussion', 'psp_echo_discussions' );
function psp_echo_discussions() {

    include( psp_template_hierarchy( 'projects/discussion/index.php' ) );

}

/**
 *
 * Function psp_documents
 *
 * Stores all of the psp_documents into an unordered list and returns them
 *
 * @param $post_id
 * @return $psp_docs
 *
 */

function psp_documents( $post_id, $style ) {

    ob_start();

    include( psp_template_hierarchy( 'projects/overview/documents/index.php' ) );

    return ob_get_clean();

}

function psp_get_nav_items( $post_id = NULL ) {

    global $post;

    $post_id = ( $post_id == NULL ? $post->ID : $post_id );

    $nav_items  = array();
    $back_opt   = psp_get_option('psp_back');

    if( is_single() && get_post_type() == 'psp_projects' ) {

        $back = ( $back_opt && !empty($back_opt) ? $back_opt : get_post_type_archive_link('psp_projects') );
        $single_nav_items = array();

        $single_nav_items['back'] = array(
            'title' =>  __( 'Dashboard', 'psp_projects' ),
            'id'    =>  'nav-dashboard',
            'link'  =>  $back,
            'icon'  =>  'psp-fi-back psp-fi-icon',
        );

        if( has_nav_menu( 'psp_project_menu' ) ) {
            $single_nav_items = array_merge( $single_nav_items, psp_get_custom_project_menu_items('psp_project_menu') );
        }

        $nav_items = ( empty($nav_items) ? $single_nav_items : array_merge( $nav_items, $single_nav_items ) );

    }

    if( ( is_post_type_archive() ) && (	has_nav_menu( 'psp_archive_menu' ) ) ) {
        $nav_items = ( empty($nav_items) ? psp_get_custom_project_menu_items('psp_archive_menu') : array_merge( $nav_items, psp_get_custom_project_menu_items('psp_archive_menu') ) );
    }

    return apply_filters( 'psp_get_nav_items', $nav_items, $post_id );

}

function psp_get_custom_project_menu_items( $theme_location ) {

	$menu_items = psp_get_menu_items_in_location( $theme_location );
	$menu       = array();
    $i          = 0;

	foreach ( (array) $menu_items as $key => $menu_item ) {

            $item = array(
                'psp_custom_menu_' . $theme_location . '_' . $i => array(
                    'title' =>  $menu_item->title,
                    'id'    =>  'psp_custom_menu_' . $theme_location . '_' . $i,
                    'link'  =>  $menu_item->url,
            ) );

            if( isset($menu_item->description) ) {
                $item[ 'psp_custom_menu_' . $theme_location . '_' . $i ][ 'icon' ] = $menu_item->description;
            }

            if( isset($menu_item->classes) ) {

                $classes = '';

                foreach( $menu_item->classes as $class ) {
                    $classes .= $class . ' ';
                }

                $item[ 'psp_custom_menu_' . $theme_location . '_' . $i ][ 'icon' ] = $classes;
            }

            $menu = array_merge( $menu, $item );

            $i++;

		}

	return apply_filters( 'psp_custom_project_menu_' . $theme_location, $menu );

}

function psp_get_menu_items_in_location( $location_id ) {

	$locations 			= get_registered_nav_menus();
	$menus 				= wp_get_nav_menus();
	$menu_locations 	= get_nav_menu_locations();

	if ( isset( $menu_locations[ $location_id ] ) ) {

		foreach ( $menus as $menu ) {
			// If the ID of this menu is the ID associated with the location we're searching for
			if ( $menu->term_id == $menu_locations[ $location_id ] ) {
				// This is the correct menu

				// Get the items for this menu
				return wp_get_nav_menu_items( $menu );

			}
		}

	}

    return false;

}

/**
 *
 * Function psp_single_template_header
 *
 * Adds the header to the Project Panorama single.php template
 *
 * @param
 * @return
 *
 */
add_action( 'psp_the_header', 'psp_single_template_header' );
function psp_single_template_header() {

    global $post;

    $user_has_access = panorama_check_access( $post->ID );

	if( $user_has_access ): ?>

	<div id="psp-primary-header" class="psp-grid-row cf">

		<?php if( ( psp_get_option( 'psp_logo' ) != '' ) && ( psp_get_option( 'psp_logo' ) != 'http://' ) ) { ?>
			<div class="psp-masthead-logo">
				<a href="<?php echo home_url(); ?>" class="psp-single-project-logo"><img src="<?php echo psp_get_option('psp_logo'); ?>"></a>
			</div>
		<?php } ?>

         <?php if( $user_has_access ): ?>

			<?php do_action( 'psp_the_navigation' ); ?>

            	<?php if( is_user_logged_in() ) { ?>
					 <aside class="psp-masthead-user">

						<?php
						$cuser = wp_get_current_user(); ?>

						<p>
                            <?php esc_html_e( 'Hello', 'psp_projects' ); ?> <?php esc_html_e($cuser->display_name); ?>
                            <a href="<?php echo esc_url(wp_logout_url()); ?>">Log Out</a>
                        </p>

						<?php echo get_avatar( $cuser->ID ); ?>

					</aside>
				<?php } ?>

		 <?php endif; ?>

	</div>

<?php endif;

}

/**
 *
 * Function psp_add_dashboard_widgets
 *
 * Defines the dashboard widget slug, title and display function
 *
 * @param
 * @return
 *
 */

add_action( 'wp_dashboard_setup', 'psp_add_dashboard_widgets' );
function psp_add_dashboard_widgets() {

    // Make sure the user has the right permissions

    if(current_user_can('publish_psp_projects')) {

        wp_add_dashboard_widget(
            'psp_dashboard_overview',         // Widget slug.
            'Projects',         // Title.
            'psp_dashboard_overview_widget_function' // Display function.
        );

		wp_add_dashboard_widget(
			'psp_dashboard_timing',
			'Project Calendar',
			'psp_dashboard_calendar_widget'
		);

    }

}

/**
 *
 * Function psp_dashboard_overview_widget_function
 *
 * Echo's the output of psp_populate_dashboard_widget
 *
 * @param
 * @return contents of psp_populate_dashboard_widget
 *
 */


function psp_dashboard_overview_widget_function() {
    echo psp_populate_dashboard_widget();
}

function psp_get_project_breakdown() {

    $cuser = wp_get_current_user();

    /*
     * Cache this query
     */
    $projects = wp_cache_get( 'psp_project_breakdown_' . $cuser->ID );
    if ( false === $projects ) {

        $meta_query = psp_access_meta_query( $cuser->ID );

        $args = array(
            'post_type'		  =>	    'psp_projects',
            'posts_per_page'	  =>		-1,
            'meta_query'        =>      $meta_query,
            'fields'            =>      'ids',
        );

        $projects = new WP_Query($args);

        wp_cache_set(  'psp_project_breakdown_' . $cuser->ID, $projects, 7200 );
    }

    $total_projects = $projects->found_posts;
    $taxonomies 	= get_terms('psp_tax','fields=count');
    $colors         = apply_filters( 'psp_project_breakdown_colors', array(
            'complete'      =>  psp_get_option( 'psp_accent_color_1', '#2a3542' ),
            'incomplete'    =>  psp_get_option( 'psp_accent_color_2', '#3299bb' ),
            'unstarted'     =>  '#666666'
    ) );

    // Calculate the number of completed projects

    $completed_projects = 0;
    $not_started 		= 0;
    $active 			= 0;

    if( $projects->have_posts() ): while( $projects->have_posts() ): $projects->the_post();

         $progress = psp_compute_progress( get_the_ID() );

         switch( $progress ) {
              case('100'):
                   $completed_projects++;
                   break;
              case(0):
                   $not_started++;
                   break;
              default:
                   $active++;
                   break;
         }

    endwhile; endif;

    if( $completed_projects != 0 && $total_projects != 0 ) {
        $percent_complete = floor( $completed_projects / $total_projects * 100 );
    } else {
        $percent_complete = 0;
    }

    if( $not_started != 0 && $total_projects != 0 ) {
        $percent_not_started = floor( $not_started / $total_projects * 100 );
    } else {
        $percent_not_started = 0;
    }

    $percent_remaining = 100 - $percent_complete - $percent_not_started;

    $stats = apply_filters( 'psp_overview_stats', array(
         'projects' => array(
              'label'    =>   __( 'Projects', 'psp_projects' ),
              'value'    =>   $total_projects,
         ),
         'sub_stats' => array(
              'active'   =>   array(
                   'label'    =>   __( 'Active', 'psp_projects' ),
                   'value'    =>   $active,
                   'color'    =>   $colors[ 'incomplete' ],
              ),
              'complete' =>   array(
                   'label'    =>   __( 'Complete', 'psp_projects' ),
                   'value'    =>   $completed_projects,
                   'color'    =>   $colors[ 'complete' ],
              ),
              'notstarted'    =>   array(
                   'label'    =>   __( 'Unstarted', 'psp_projects' ),
                   'value'    =>   $not_started,
                   'color'    =>   $colors[ 'unstarted' ]
              )
         )
    ) );

    ?>


     <div class="psp-color-stats">
          <div class="psp-color-stats__overview">
               <div class="psp-overview-chart">
                   <canvas id="psp-overview-chart" width="150" height="150"></canvas>
               </div>
               <div class="psp-color-stats_number">
                    <span class="psp-stat-count"><?php echo esc_html( $stats['projects']['value'] ); ?></span>
                    <span class="psp-stat-label"><?php echo esc_html( $stats['projects']['label'] ); ?></span>
               </div>
               <div class="psp-color-stats_number">
                    <span class="psp-stat-count"><?php echo esc_html( $percent_complete . '%' ); ?></span>
                    <span class="psp-stat-label"><?php echo esc_html_e( 'Complete', 'psp_projects' ); ?></span>
               </div>

          </div>
          <div class="psp-color-stats__sub">
               <?php foreach( $stats['sub_stats'] as $slug => $stat ): ?>
                    <div class="psp-color-stat psp-color-stat__<?php echo esc_attr($slug); ?>">
                         <span class="psp-stat-count">
                              <?php echo esc_html($stat['value']); ?>
                         </span>
                         <span class="psp-stat-label">
                              <span class="psp-stat-legend" style="background-color: <?php echo esc_attr($stat['color']); ?>"></span>
                              <?php echo esc_html($stat['label']); ?>
                         </span>
                    </div> <!--/.psp-color-stat-->
               <?php endforeach; ?>
          </div>
     </div> <!--/.psp-color-stats-->

     <script>
     jQuery(document).ready(function() {

                var chartOptions = {
                    responsive: true,
                    percentageInnerCutout : <?php echo esc_js( apply_filters( 'psp_graph_percent_inner_cutout', 80 ) ); ?>
                }

                var data = [
                    {
                        value: <?php echo $percent_complete; ?>,
                        color: "<?php echo $colors[ 'complete' ]; ?>",
                        label: "Completed"
                    },
                    {
                        value: <?php echo $percent_remaining; ?>,
                        color: "<?php echo $colors[ 'incomplete' ]; ?>",
                        label: "In Progress"
                    },
                    {
                        value: <?php echo $percent_not_started; ?>,
                        color: "<?php echo $colors[ 'unstarted' ]; ?>",
                        label: "Not Started"
                    }
                ];


                var psp_dashboard_chart = document.getElementById("psp-overview-chart").getContext("2d");

                var has_error = false;

                try {
                    new Chart(psp_dashboard_chart).Doughnut(data,chartOptions);
                } catch(err) {
                    has_error = true;
                }

                if( has_error == false ) {

                    if( typeof Chart !== 'undefined' ) {
                        new Chart(psp_dashboard_chart).Doughnut(data,chartOptions);
                    }

                }

            });

        </script>

    <?php
}

/**
 *
 * Function psp_populate_dashboard_widget
 *
 * Gathers the dashboard content and returns it in a variable
 *
 * @param
 * @return (variable) ($output)
 *
 */

// TODO: This should be a template file
function psp_populate_dashboard_widget() {

    $args = apply_filters( 'psp_populate_dashboard_widget_args', array(
        'post_type'         =>  'psp_projects',
        'posts_per_page'    =>  '10',
        'orderby'           =>  'modified',
        'order'             =>  'DESC',
        'post_status'       =>  'publish'
    ) );

    $recent = new WP_Query( $args );

     ob_start();

	echo psp_get_project_breakdown(); ?>

			 <table class="psp-dashboard-widget-table">
				<tr>
					<th><?php esc_html_e( 'Project', 'psp_projects' ); ?></th>
					<th><?php esc_html_e( 'Progress', 'psp_projects' ); ?></th>
				</tr>

    			<?php while($recent->have_posts()): $recent->the_post(); global $post; ?>
        			<tr>
					   <td>
						   <a href="<?php echo get_edit_post_link(); ?>"><strong><?php the_title(); ?></strong></a>
						   <p class="psp-dashboard-widget-updated"><?php esc_html_e( 'Updated on', 'psp_projects' ); ?> <?php echo get_the_modified_date( 'm/d/Y' ); ?></p>

					   </td>
					   <td>
						   <?php
						   $completed = psp_compute_progress( $post->ID );

						   	if($completed > 10): ?>
          						<p class="psp-progress"><span class="psp-<?php echo $completed; ?>"><strong>%<?php echo $completed; ?></strong></span></p>
							<?php else: ?>
            					<p class="psp-progress"><span class="psp-<?php echo $completed; ?>"></span></p>
        					<?php endif; ?>
  					  </td>
				</tr>
    			<?php endwhile; ?>
		</table>

	<?php
    return ob_get_clean();

}

// Function to output the project calendar
function psp_dashboard_calendar_widget() {

	echo psp_output_project_calendar();

}

function psp_get_section_nav_items() {

    $slug   = psp_get_option( 'psp_slug' );
    $cuser  = wp_get_current_user();

    $defaults = apply_filters( 'psp_section_nav_items', array(
        array(
            'name'  =>  __( 'Dashboard', 'psp_projects' ),
            'url'   =>  get_post_type_archive_link( 'psp_projects' ),
            'slug'  =>  'dashboard',
            'icon'  =>  'psp-fi-back psp-fi-icon',
            'public' => true
        ),
        array(
            'name'  =>  __( 'Teams', 'psp_projects' ),
            'url'   =>  get_post_type_archive_link( 'psp_teams' ),
            'slug'  =>  'teams',
            'icon'  =>  'psp-fi-icon psp-fi-teams'
        ),
        array(
            'name'  =>  __( 'Calendar', 'psp_projects' ),
            'url'   =>  ( get_option('permalink_structure') ? trailingslashit( get_post_type_archive_link('psp_projects') ) . 'calendar/' : add_query_arg( 'psp_calendar_page', $cuser->ID, get_post_type_archive_link('psp_projects') ) ),
            'slug'  =>  'calendar',
            'icon'  =>  'psp-fi-icon psp-fi-calendar',
            'public' => true
        ),
        array(
            'name'  =>  __( 'Tasks', 'psp_projects' ),
            'url'   =>  ( get_option('permalink_structure') ? trailingslashit( get_post_type_archive_link('psp_projects') ) . 'tasks/' : add_query_arg( 'psp_tasks_page=', $cuser->ID, get_post_type_archive_link('psp_projects') ) ),
            'slug'  =>  'tasks',
            'icon'  =>  'psp-fi-icon psp-fi-tasks',
        )
    ) );

    $teams = psp_get_user_teams( $cuser->ID );

    if( empty( $teams ) ) unset( $defaults[1] );

    /**
      * Filter out items for public views
      */
    if( !is_user_logged_in() ) {

         foreach( $defaults as $key => $item ) {

              if( empty($item['public']) || $item['public'] !== true ) {
                   unset($defaults[$key]);
              }

         }
    }

    if( current_user_can('edit_others_psp_projects') ) {
         $defaults[] = array(
             'name'  =>  __( 'Reports', 'psp_projects' ),
             'url'   =>  ( get_option('permalink_structure') ? trailingslashit( get_post_type_archive_link('psp_projects') ) . 'reports/' : add_query_arg( 'psp_tasks_page=', $cuser->ID, get_post_type_archive_link('psp_projects') ) ),
             'slug'  =>  'reports',
             'icon'  =>  'psp-fi-icon psp-fi-reports',
        );
    }

    if( has_nav_menu('psp_section_menu') ) {
        $defaults = ( array_merge( $defaults, psp_get_custom_section_menu_items('psp_section_menu') ) );
    }

    return apply_filters( 'psp_section_nav_items', $defaults );

}

function psp_get_custom_section_menu_items( $menu_slug ) {

     $menu_items = psp_get_menu_items_in_location( $menu_slug );
     $menu       = array();

     foreach ( (array) $menu_items as $key => $menu_item ) {

         $classes = '';

         if( isset($menu_item->classes) && !empty($menu_item->classes) ) {
             foreach( $menu_item->classes as $class ) {
                 $classes .= $class . ' ';
             }
         }

          $item = array(
              'name'    =>  $menu_item->title,
              'url'     =>  $menu_item->url,
              'slug'    =>  urlencode( $menu_item->title ),
              'icon'    =>  $classes,
          );

          $menu[] = $item;

     }

     return apply_filters( 'psp_custom_section_menu_' . $menu_slug, $menu );

}

add_filter( 'psp_section_nav_link_class', 'psp_section_nav_active_states', 10, 2 );
function psp_section_nav_active_states( $class, $slug ) {

    $custom_templates = array(
        'psp_calendar_page',
        'psp_tasks_page'
    );

    $conditions = apply_filters( 'psp_section_nav_active_states', array(
        array(
            'condition' => get_query_var('psp_calendar_page'),
            'slug'      =>  'calendar'
        ),
        array(
            'condition' =>  is_post_type_archive('psp_teams'),
            'slug'      =>  'teams'
        ),
        array(
            'condition' =>  is_post_type_archive('psp_projects') && get_query_var('psp_tasks_page'),
            'slug'      =>  'tasks'
        ),
        array(
             'condition' => is_post_type_archive('psp_projects') && get_query_var('psp_reports_page'),
             'slug'      => 'reports',
        ),
        array(
            'condition' =>  is_post_type_archive('psp_projects') && !get_query_var('psp_tasks_page') && !get_query_var('psp_calendar_page') && !get_query_var('psp_reports_page'),
            'slug'      =>  'dashboard'
       ),
    ) );

    foreach( $conditions as $condition ) {
        if( $condition['condition'] && $slug == $condition['slug'] ) return 'active';
    }

    if( get_post_type() == 'psp_' . $slug ) return 'active';

    return $class;

}

add_action( 'psp_head', 'psp_favicon_markup' );
function psp_favicon_markup() {
    if( psp_get_option('psp_favicon') ) {

        $favicon = psp_get_option('psp_favicon');
        $ext     = explode( '.', $favicon );
        $ext     = array_pop($ext);
        $media   = '';

        if( $ext == 'png' ) {
            $media = 'png';
        } elseif( $ext == 'gif' ) {
            $media = 'gif';
        } elseif( $ext == 'ico' ) {
            $media = 'x-icon';
        } else {
            $media = $ext;
        }

        echo '<link rel="icon" type="' . esc_attr( $media . '/image' ) . '" href="' . esc_url( psp_get_option('psp_favicon') ) .'">';

    }
}

add_filter('acf/format_value/type=textarea', 'psp_acf_do_shortcode', 10, 3 );
add_filter('acf/format_value/type=wysiwyg', 'psp_acf_do_shortcode', 10, 3 );
function psp_acf_do_shortcode( $value, $post_id, $field ) {

     if( get_post_type($post_id) == 'psp_projects' ) {
          return do_shortcode( $value );
     }

     return $value;

}

add_action( 'psp_footer', 'psp_add_mobile_menu_to_page' );
function psp_add_mobile_menu_to_page() {

     include( psp_template_hierarchy('global/mobile-menu.php') );

}
