<?php
/**
 * Reports Archive Page
 *
 * Frontend report
 */

// Set the current tab
$active_tab = ( !empty($_GET['tab']) ? $_GET['tab'] : 'projects' );

// Set the current page
if( get_query_var('paged') ) {
   $_REQUEST['paged'] = get_query_var('paged');
}

include( psp_template_hierarchy( 'dashboard/header.php' ) ); ?>

<?php include( psp_template_hierarchy( 'global/header/navigation-sub' ) ); ?>

<div id="psp-archive-container" class="psp-grid-container-fluid psp-reports-wrap">
        <div id="psp-archive-content">
               <?php
               if( !current_user_can('edit_others_projects') && !is_user_logged_in() ): ?>

                    <div class="psp-col-md-6 psp-col-md-offset-3">
                        <div class="psp-error">
                            <p><em><?php esc_html_e( 'You do not have access to reports', 'psp_projects' ); ?></em></p>
                        </div>
                    </div>

               <?php else: ?>

                   <div class="psp-archive-section">
                        <div class="psp-table-header">
                             <div class="psp-h2"><?php esc_html_e( 'Reports', 'psp_projects' ); ?></div>
                        </div>

                        <div class="psp-tabs">
                             <?php
                             $baseurl = trailingslashit(get_post_type_archive_link('psp_projects')) . ( get_option('permalink_structure') ?  'reports/?tab=' : '?psp_reports_page=home&tab=' );

                             $tabs = apply_filters( 'psp_frontend_report_tabs', array(
                                  array(
                                       'slug'     =>   'projects',
                                       'label'    =>   __( 'Projects', 'psp_projects' ),
                                  ),
                                  array(
                                       'slug'     =>   'users',
                                       'label'    =>   __( 'Users', 'psp_projects' )
                                  )
                             ) );

                             foreach( $tabs as $tab ):
                                  $url = trailingslashit( get_post_type_archive_link('psp_projects') ) . ( PSP_FE_PERMALINKS ? 'manage/edit/' . $post->ID .'/' : '&psp_manage_page=edit&psp_manage_option=' . $post->ID );
                                  $class = 'psp-tab' . ( $tab['slug'] == $active_tab ? ' is-active' : '' ); ?>
                                   <a href="<?php echo esc_attr( $baseurl . $tab['slug'] ); ?>" class="<?php echo esc_attr($class); ?>"><?php echo esc_html( $tab['label'] ); ?></a>
                              <?php endforeach; ?>
                        </div>

                        <?php

                        // Load assets
                         require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
                         require_once(ABSPATH . 'wp-admin/includes/screen.php');
                         require_once(ABSPATH . 'wp-admin/includes/class-wp-screen.php');
                         require_once(ABSPATH . 'wp-admin/includes/template.php');

                        if( $active_tab == 'projects' ):

                            $projectList = new PSP_Projects_List();
                            $projectList->prepare_items(); ?>

                            <div class="psp-report__projects psp-report">
                                 <?php $projectList->display(); ?>
                            </div>

                       <?php
                       endif;

                       if( $active_tab == 'users' ):

                          $userList = new PSP_User_List();
                          $userList->prepare_items(); ?>

                          <div class="psp-report__users psp-report">
                                <?php $userList->display(); ?>
                          </div>

                       <?php
                       endif; ?>
                   </div>

             <?php endif; ?>
        </div>
    </div>
<?php
include( psp_template_hierarchy( 'dashboard/footer.php' ) );
