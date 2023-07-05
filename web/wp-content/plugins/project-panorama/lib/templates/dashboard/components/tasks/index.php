<?php
/**
 * Teams Listing Page
 *
 * Lists all the teams on the site that you have access to
 * @var post_type	psp_teams
 */
include( psp_template_hierarchy( 'dashboard/header.php' ) );

$tasks_id   = get_query_var( 'psp_tasks_page' );
$cuser 	    = wp_get_current_user();
$tasks_id   = ( $tasks_id == 'home' ? $cuser->ID : $tasks_id ); ?>

<?php include( psp_template_hierarchy( 'global/header/navigation-sub' ) ); ?>

<div id="psp-archive-container" class="psp-grid-container-fluid">

        <div id="psp-archive-content">

			<?php
            	if( ( $cuser->ID == $tasks_id || current_user_can( 'edit_others_psp_projects' ) ) && get_user_by( 'id', $tasks_id ) ): ?>

				<div class="psp-archive-header">

					<div class="psp-h2"><?php esc_html_e( 'Upcoming Tasks', 'psp_projects' ); ?></div>

					<div class="psp-tasks-project-search">
                              <div class="psp-task-project-search__wrap">
	                             <span class="fa fa-search"></span>
	                             <input id="psp-tasks-project-live-search" type="text" placeholder="<?php esc_attr_e( 'Filter...', 'psp_projects' ); ?>">
                             </div>
					</div>

				</div>

                <div class="psp-archive-section">
					<table class="psp-my-tasks not-shortcode">
						<thead>
							<tr>
								<?php
								$cols = apply_filters( 'psp_task_table_headers', array(
									''			=>	array(
										'label'	=>	'',
									),
									'task'		=>	array(
										'label'	=> __( 'Task', 'psp_projects' ),
									),
									'due'		=>	array(
										'label'	=> __( 'Due', 'psp_projects' ),
										'link'	=> ( isset($_GET['sort']) && $_GET['sort']) == 'DESC' ? add_query_arg( 'sort', 'ASC') : add_query_arg( 'sort', 'DESC' ),
										'class' => 'psp-sort sort-' . ( isset($_GET['sort']) ? $_GET['sort'] : 'ASC' )
									)
								) );
								foreach( $cols as $slug => $heading ): ?>
									<th class="<?php echo esc_attr( 'psp-my-tasks-' . $slug . '-head'); ?>">
										<?php
										if( isset( $heading['link']) ): ?>
											<a href="<?php echo esc_attr($heading['link']); ?>" class="<?php echo esc_attr($heading['class']); ?>">
										<?php
										endif;
										echo esc_html($heading['label']);
										if( isset( $heading['link']) ): ?>
											</a>
										<?php endif; ?>
									</th>
								<?php
								endforeach; ?>
							</tr>
						</thead>
						<tbody>
                    		<?php echo psp_all_my_tasks_shortcode( array( 'id' => $tasks_id ), false ); ?>
						</tbody>
					</table> <!--/.psp-archive-table-->
                </div>

            <?php else: ?>

                <div class="psp-col-md-6 psp-col-md-offset-3">
                    <div class="psp-error">
                        <p><em><?php esc_html_e( 'You do not have access to these tasks', 'psp_projects' ); ?></em></p>
                    </div>
                </div>

        <?php endif; ?>

    </div>
<?php
include( psp_template_hierarchy( 'dashboard/footer.php' ) );
