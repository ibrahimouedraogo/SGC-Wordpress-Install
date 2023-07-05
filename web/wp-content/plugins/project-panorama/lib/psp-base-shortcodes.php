<?php
/**
 * Description of psp-base-shortcodes
 *
 * Shortcodes that are present in lite and paid versino of Panorama
 * @package psp-projects
 *
 */

function psp_current_projects( $atts ) {

    extract( shortcode_atts(
            array(
                'type'      => 'all',
                'status'    => 'all',
                'access'    => 'user',
                'count'     => '10',
				'sort'	    => 'default',
				'order'	    => 'ASC',
                'collapsed' => false,
                'target'    => '',
                'ids'       =>  '',
            ), $atts )
    );

    $paged 	= ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
	$cuser 	= wp_get_current_user();
	$cid 	= $cuser->ID;

	// Determine the sorting

	if($sort == 'start') {

		$meta_sort 	= 'start_date';
		$order_by 	= 'meta_value';

	} elseif ($sort == 'end') {

		$meta_sort 	= 'end_date';
		$order_by 	= 'meta_value';

	} elseif ($sort == 'title') {

		$meta_sort 	= NULL;
		$order_by 	= 'title';

	} else {

		$meta_sort 	= 'start_date';
		$order_by 	= 'menu_order';

	}

	// Set the initial arguments

    $args = array(
        'post_type' 		=> 	'psp_projects',
        'paged'				=> 	$paged,
        'posts_per_page'	=> 	$count,
		'meta_key' 			=>	$meta_sort,
		'orderby'			=>	$order_by,
		'order'				=>	$order
    );

    // If a type has been selected, add it to the argument

    if( ( !empty( $type ) ) && ( $type != 'all' ) ) {

        $tax_args 	= array( 'psp_tax' => $type );
        $args 		= array_merge( $args, $tax_args );

    }

	if($status == 'active') {
		$status_args = array('tax_query' => array(
			array(
				'taxonomy'	=>	'psp_status',
				'field'		=>	'slug',
				'terms'		=>	'completed',
				'operator'	=>	'NOT IN'
				)
			)
		);

		$args = array_merge($args,$status_args);

	}

	if($status == 'completed') {
		$status_args = array('tax_query' => array(
			array(
				'taxonomy'	=>	'psp_status',
				'field'		=>	'slug',
				'terms'		=>	'completed',
				)
			)
		);

		$args = array_merge($args,$status_args);

	}

    if( !empty($ids) ) {
        $args['post__in'] = explode( ',', $ids );
    }

    if( $access == 'user' ) {

		// Just restricting access, not worried about active or complete

        if( !current_user_can( 'manage_options' ) ) {

            $cuser 	    = wp_get_current_user();
            $meta_args  = array(
                'meta_query' 	=> psp_access_meta_query( $cuser->ID ),
				'has_password'	=> false
            );

		  $args = array_merge( $args, $meta_args );

		}

    } elseif( $access == 'assigned' ) {

         $cuser 	    = wp_get_current_user();
         $meta_args  = array(
             'meta_query' 	=> psp_access_meta_query( $cuser->ID ),
                 'has_password'	=> false
         );

         $args = array_merge( $args, $meta_args );

    } elseif( is_int($access) ) {

         $meta_args  = array(
            'meta_query' => psp_access_meta_query( $access ),
         );

    }

    $projects = new WP_Query($args);

	if( ( $access == 'user' ) && ( !is_user_logged_in() ) ) { ?>
	<div id="psp-projects" class="psp-shortcode">
		<div id="psp-overview">

        	<div id="psp-login" class="shortcode-login">

				<h2><?php _e( 'Please Login to View Projects', 'psp_projects' ); ?></h2>

				<?php echo panorama_login_form(); ?>

			</div> <!--/#psp-login-->

		</div>
	</div>
	<?php

		 psp_front_assets(1);

		return;

	}

    if( $projects->have_posts() ): ob_start(); ?>

		<div id="psp-projects">

			<?php
			$template = ( $collapsed ? '/shortcodes/project-list-collapsed.php' : '/shortcodes/project-list.php' );
			include( psp_template_hierarchy( $template ) );
			?>

		</div>

        <?php psp_front_assets( 1 );

		// Clear out this query
		wp_reset_query();

        return ob_get_clean();

    else:

        return '<p>' . __( 'No projects found', 'psp_projects' ) . '</p>';

    endif;

}
add_shortcode( 'project_list', 'psp_current_projects' );

function psp_archive_project_listing( $projects, $page = 1 ) {

    if( $projects->have_posts()):

        ob_start();

        include( psp_template_hierarchy( 'dashboard/components/projects/table' ) );

        psp_front_assets(1);

        return ob_get_clean();

    else:

        return '<div class="psp-notice"><p>' . __( 'No projects found' , 'psp_projects' ) . '</p></div>';

    endif;

}

function psp_project_listing_dialog() {

    $psp_taxes      = get_terms('psp_tax');
    $psp_tax_list   = '';

    foreach($psp_taxes as $tax) {
        $psp_tax_list .= '<option value="'.$tax->slug.'">'.$tax->name.'</option>';
    }

    $output = '

			<style type="text/css">
				#TB_Window { z-index: 9000 !important; }
			</style>

			<div class="psp-dialog" style="display:none">
					<div id="psp-project-listing-dialog">
						<h3>'.__('Project Listing','psp_projects').'</h3>
						<p>'.__('Select from the options below to output a list of projects.','psp_projects').'</p>
						<table class="form-table">
							<tr>
								<th><label for="psp-project-taxonomy">'.__('Project Type','psp_projects').'</label></th>
								<td>
									<select id="psp-project-taxonomy" name="psp-project-taxonomy">
										<option value="all">Any</option>
										'.$psp_tax_list.'
									</select>
								</td>
							</tr>
							<tr>
								<th><label for="psp-project-status">'.__('Project Status','psp_projects').'</label></th>
								<td>
									<select id="psp-project-status" name="psp-project-status">
										<option value="all">'.__('All','psp_projects').'</option>
										<option value="active">'.__('Active','psp_projects').'</option>
										<option value="completed">'.__('Completed','psp_projects').'</option>
									</select>
								</td>
							</tr>
							<tr>
							    <th colspan="2">
							        <input type="checkbox" name="psp-user-access" id="psp-user-access" checked>
							        <label for="psp-user-access">'.__('Only display projects current user has permission to access','psp_projects').'</label>
							    </th>
							</tr>
							<tr>
								<th><label for="psp-project-sort">'.__('Order By','psp_projects').'</label></th>
								<td>
									<select id="psp-project-sort" name="psp-project-sort">
										<option value="none">'.__('Creation Date','psp_projects').'</option>
										<option value="start">'.__('Start Date','psp_projects').'</option>
										<option value="end">'.__('End Date','psp_projects').'</option>
										<option value="title">'.__('Title','psp_projects').'</option>
									</select>
								</td>
							</tr>
							<tr>
							    <th><label for="psp-project-count">'.__('Projects to show','psp_projects').'</label></th>
                                <td>
                                    <select id="psp-project-count" name="psp-project-count">
                                        <option value="10">10</option>
                                        <option value="25">25</option>
                                        <option value="50">50</option>
                                        <option value="100">100</option>
                                        <option value="-1">All</option>
                                    </select>
                                </td>
							</tr>
						</table>';

    $output .= '<p><input class="button-primary" type="button" onclick="InsertPspProjectList();" value="'.__('Insert Project List','psp_projects').'"> <a class="button" onclick="tb_remove(); return false;" href="#">'.__('Cancel','psp_projects').'</a></p>';

    $output .= '</div></div>';

    echo $output;

}

function psp_buttons() {

	// Make sure the buttons are enabled

	if( ( psp_get_option( 'psp_disable_js' ) === '0') || ( psp_get_option( 'psp_disable_js' ) == NULL ) ) {

		add_filter( 'mce_external_plugins', 'psp_add_buttons' );
    	add_filter( 'mce_buttons', 'psp_register_buttons' );
	}

}

function psp_add_buttons( $plugin_array ) {
    $plugin_array[ 'pspbuttons' ] = plugins_url(). '/' . PSP_PLUGIN_DIR . '/dist/assets/js/psp-buttons.js';
    return $plugin_array;
}

function psp_register_buttons( $buttons ) {

    array_push( $buttons, 'currentprojects', 'singleproject' );

    return $buttons;
}

function psp_refresh_mce( $ver ) {
    $ver += 3;
    return $ver;
}

add_filter( 'tiny_mce_version', 'psp_refresh_mce');
add_action( 'init', 'psp_buttons' );


/**
 *
 * Function psp_dashboard_shortcode
 *
 * Outputs the Dashboard Widget in Shortcode Format
 *
 * @param 	(variable) ($atts) 	Attributes from the shortcode - currently none
 * @return 	($output) 			(Content from psp_populate_dashboard_widget() )
 *
 */

add_shortcode( 'panorama_dashboard', 'psp_dashboard_shortcode' );
function psp_dashboard_shortcode( $atts ) {

    $output = '<div class="psp-dashboard-widget">' . psp_populate_dashboard_widget() . '</div>';
    return $output;

}

add_shortcode( 'panorama_login', 'psp_login_form' );
function psp_login_form( $atts, $content ) {

    if( is_user_logged_in() ) return;

    $redirect = ( isset( $atts['redirect'] ) ? $atts['redirect'] : get_post_type_archive_link('psp_projects') );

    ob_start();

        panorama_login_form( null, $redirect );

    return ob_get_clean();

}

add_shortcode( 'psp_private', 'psp_private_content_shortcode' );
function psp_private_content_shortcode( $atts, $content = null ) {

    $cuser = wp_get_current_user();

    if( !is_user_logged_in() || !psp_can_edit_project() ) {
        return;
    }

    return '<div class="psp-private-content">' . do_shortcode($content) . '</div>';

}
